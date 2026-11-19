<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Donor;
use App\Models\Donation;
use App\Models\DonationItem;
use App\Models\DonationCommunication;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;

class DonorIntelligenceController extends Controller
{
    /**
     * Display the donor intelligence dashboard.
     */
    public function index(Request $request)
    {
        $data = $this->buildDashboardData($request);

        return view('admin.donor-intelligence.index', $data);
    }

    /**
     * Build all dashboard metrics.
     */
    private function buildDashboardData(Request $request): array
    {
        $filters = $this->getFilters($request);

        /*
         * Base donation query.
         * Only actual donations are included in donor-giving analytics.
         */
        $donations = Donation::query()
            ->with('donor')
            ->where('classification', 'donation');

        $this->applyDonationFilters($donations, $filters);

        $totalDonations = (clone $donations)->count();

        $donationsByType = (clone $donations)
            ->select('type', DB::raw('COUNT(*) as total'))
            ->groupBy('type')
            ->pluck('total', 'type');

        $cashByCurrency = (clone $donations)
            ->where('type', 'cash')
            ->select(
                'currency',
                DB::raw('COUNT(*) as donation_count'),
                DB::raw('SUM(COALESCE(amount, 0)) as total_amount')
            )
            ->groupBy('currency')
            ->orderBy('currency')
            ->get();

        /*
         * In-kind estimated value.
         * The value is taken from donation_items, not donations.amount.
         */
        $inKindQuery = DonationItem::query()
            ->join('donations', 'donation_items.donation_id', '=', 'donations.id')
            ->where('donations.classification', 'donation')
            ->where('donations.type', 'in_kind');

        $this->applyJoinedDonationFilters($inKindQuery, $filters);

        $inKindByCurrency = (clone $inKindQuery)
            ->select(
                'donations.currency',
                DB::raw('COUNT(DISTINCT donations.id) as donation_count'),
                DB::raw('SUM(COALESCE(donation_items.estimated_value, 0)) as estimated_value')
            )
            ->groupBy('donations.currency')
            ->orderBy('donations.currency')
            ->get();

        $inKindItems = (clone $inKindQuery)
            ->select(
                'donation_items.item',
                'donation_items.unit',
                DB::raw('SUM(COALESCE(donation_items.quantity, 0)) as total_quantity'),
                DB::raw('SUM(COALESCE(donation_items.estimated_value, 0)) as total_value')
            )
            ->groupBy('donation_items.item', 'donation_items.unit')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->get();

        /*
         * Donation sources.
         */
        $donationSources = (clone $donations)
            ->select(
                'source',
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN type = "cash" THEN COALESCE(amount, 0) ELSE 0 END) as cash_amount')
            )
            ->groupBy('source')
            ->orderByDesc('total')
            ->get();

        /*
         * Monthly donation trends.
         */
        $monthlyDonations = (clone $donations)
            ->select(
                DB::raw("DATE_FORMAT(donation_date, '%Y-%m') as month"),
                'currency',
                'type',
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('month', 'currency', 'type')
            ->orderBy('month')
            ->get();

        $monthlyCash = (clone $donations)
            ->where('type', 'cash')
            ->select(
                DB::raw("DATE_FORMAT(donation_date, '%Y-%m') as month"),
                'currency',
                DB::raw('SUM(COALESCE(amount, 0)) as total_amount')
            )
            ->groupBy('month', 'currency')
            ->orderBy('month')
            ->get();

        /*
         * Donor behaviour.
         * These lifetime figures are independent of the selected donation filters.
         */
        $donors = Donor::query()
            ->withCount([
                'donations as qualifying_donations_count' => function ($query) {
                    $query->where('classification', 'donation');
                }
            ])
            ->withMax([
                'donations as last_donation_date' => function ($query) {
                    $query->where('classification', 'donation');
                }
            ], 'donation_date');

        $allDonors = (clone $donors)->get();

        $donorsWithGiving = $allDonors->filter(
            fn ($donor) => $donor->qualifying_donations_count > 0
        );

        $repeatDonors = $donorsWithGiving->filter(
            fn ($donor) => $donor->qualifying_donations_count >= 2
        );

        $activeCutoff = now()->subDays(90)->toDateString();

        $activeDonors = $donorsWithGiving->filter(
            fn ($donor) => $donor->last_donation_date
                && Carbon::parse($donor->last_donation_date)->gte($activeCutoff)
        );

        $lapsedDonors = $donorsWithGiving->filter(
            fn ($donor) => $donor->last_donation_date
                && Carbon::parse($donor->last_donation_date)->lt($activeCutoff)
        );

        $retentionRate = $donorsWithGiving->count() > 0
            ? round(($repeatDonors->count() / $donorsWithGiving->count()) * 100, 1)
            : 0;

        /*
         * New donor registrations in the selected period.
         */
        $newDonorsQuery = Donor::query();

        if ($filters['from']) {
            $newDonorsQuery->whereDate('created_at', '>=', $filters['from']);
        }

        if ($filters['to']) {
            $newDonorsQuery->whereDate('created_at', '<=', $filters['to']);
        }

        $newDonors = (clone $newDonorsQuery)->count();

        $monthlyAcquisition = (clone $newDonorsQuery)
            ->select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        /*
         * Top cash donors.
         * Keep different currencies separate.
         */
        $topDonors = (clone $donations)
            ->join('donors', 'donations.donor_id', '=', 'donors.id')
            ->where('donations.type', 'cash')
            ->select(
                'donors.id',
                'donors.name',
                'donations.currency',
                DB::raw('COUNT(donations.id) as donation_count'),
                DB::raw('SUM(COALESCE(donations.amount, 0)) as total_amount')
            )
            ->groupBy('donors.id', 'donors.name', 'donations.currency')
            ->orderByDesc('total_amount')
            ->limit(10)
            ->get();

        /*
         * Donor contact coverage.
         */
        $contactCoverage = [
            'both' => Donor::whereNotNull('email')
                ->where('email', '!=', '')
                ->whereNotNull('phone')
                ->where('phone', '!=', '')
                ->count(),

            'email_only' => Donor::whereNotNull('email')
                ->where('email', '!=', '')
                ->where(function ($query) {
                    $query->whereNull('phone')->orWhere('phone', '');
                })
                ->count(),

            'phone_only' => Donor::whereNotNull('phone')
                ->where('phone', '!=', '')
                ->where(function ($query) {
                    $query->whereNull('email')->orWhere('email', '');
                })
                ->count(),

            'missing_both' => Donor::where(function ($query) {
                $query->whereNull('email')->orWhere('email', '');
            })->where(function ($query) {
                $query->whereNull('phone')->orWhere('phone', '');
            })->count(),
        ];

        /*
         * Communications performance.
         * Statuses are read from the actual communication records.
         */
        $communicationsQuery = DonationCommunication::query();

        if ($filters['from']) {
            $communicationsQuery->whereDate('created_at', '>=', $filters['from']);
        }

        if ($filters['to']) {
            $communicationsQuery->whereDate('created_at', '<=', $filters['to']);
        }

        $communications = (clone $communicationsQuery)
            ->select(
                'channel',
                'status',
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('channel', 'status')
            ->orderBy('channel')
            ->get();

        $communicationTotals = (clone $communicationsQuery)
            ->select('channel', 'status', DB::raw('COUNT(*) as total'))
            ->groupBy('channel', 'status')
            ->get()
            ->groupBy('channel');

        /*
         * Follow-up opportunities:
         * - donors without an email address
         * - donors without a phone number
         * - donors whose last donation was more than 90 days ago
         */
        $followUpDonors = $allDonors
            ->filter(function ($donor) use ($activeCutoff) {
                $missingContact = empty($donor->email) || empty($donor->phone);

                $lapsed = $donor->last_donation_date
                    && Carbon::parse($donor->last_donation_date)->lt($activeCutoff);

                return $missingContact || $lapsed;
            })
            ->sortBy('last_donation_date')
            ->take(15)
            ->values();

        /*
         * Donation records for clickable detail links and report exports.
         */
        $recentDonations = (clone $donations)
            ->latest('donation_date')
            ->limit(10)
            ->get();

        return [
            'filters' => $filters,

            'totalDonors' => $allDonors->count(),
            'donorsWithGiving' => $donorsWithGiving->count(),
            'newDonors' => $newDonors,
            'repeatDonors' => $repeatDonors->count(),
            'activeDonors' => $activeDonors->count(),
            'lapsedDonors' => $lapsedDonors->count(),
            'retentionRate' => $retentionRate,

            'totalDonations' => $totalDonations,
            'donationsByType' => $donationsByType,
            'cashByCurrency' => $cashByCurrency,
            'inKindByCurrency' => $inKindByCurrency,
            'inKindItems' => $inKindItems,
            'donationSources' => $donationSources,

            'monthlyDonations' => $monthlyDonations,
            'monthlyCash' => $monthlyCash,
            'monthlyAcquisition' => $monthlyAcquisition,

            'topDonors' => $topDonors,
            'contactCoverage' => $contactCoverage,

            'communications' => $communications,
            'communicationTotals' => $communicationTotals,

            'followUpDonors' => $followUpDonors,
            'recentDonations' => $recentDonations,

            'activeCutoff' => $activeCutoff,
        ];
    }

    /**
     * Get and validate dashboard filters.
     */
    private function getFilters(Request $request): array
    {
        $validated = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'type' => ['nullable', 'in:cash,in_kind'],
            'source' => ['nullable', 'in:manual,mpesa,bank,paypal,other'],
            'currency' => ['nullable', 'string', 'size:3'],
        ]);

        return [
            'from' => $validated['from'] ?? null,
            'to' => $validated['to'] ?? null,
            'type' => $validated['type'] ?? null,
            'source' => $validated['source'] ?? null,
            'currency' => isset($validated['currency'])
                ? strtoupper($validated['currency'])
                : null,
        ];
    }

    /**
     * Apply filters to a normal Donation query.
     */
    private function applyDonationFilters($query, array $filters): void
    {
        if ($filters['from']) {
            $query->whereDate('donation_date', '>=', $filters['from']);
        }

        if ($filters['to']) {
            $query->whereDate('donation_date', '<=', $filters['to']);
        }

        if ($filters['type']) {
            $query->where('type', $filters['type']);
        }

        if ($filters['source']) {
            $query->where('source', $filters['source']);
        }

        if ($filters['currency']) {
            $query->where('currency', $filters['currency']);
        }
    }

    /**
     * Apply filters to queries joined to donations.
     */
    private function applyJoinedDonationFilters($query, array $filters): void
    {
        if ($filters['from']) {
            $query->whereDate('donations.donation_date', '>=', $filters['from']);
        }

        if ($filters['to']) {
            $query->whereDate('donations.donation_date', '<=', $filters['to']);
        }

        if ($filters['type']) {
            $query->where('donations.type', $filters['type']);
        }

        if ($filters['source']) {
            $query->where('donations.source', $filters['source']);
        }

        if ($filters['currency']) {
            $query->where('donations.currency', $filters['currency']);
        }
    }

    /**
     * Export dashboard data.
     */
    public function export(Request $request, string $format)
    {
        abort_unless(in_array($format, ['pdf', 'csv'], true), 404);

        $data = $this->buildDashboardData($request);

        if ($format === 'pdf') {
            $pdf = Pdf::loadView(
                'admin.donor-intelligence.export-pdf',
                $data
            )->setPaper('a4', 'landscape');

            return $pdf->download(
                'Teule-Donor-Intelligence-' . now()->format('Ymd_His') . '.pdf'
            );
        }

        $filename = 'Teule-Donor-Intelligence-' . now()->format('Ymd_His') . '.csv';

        return Response::streamDownload(function () use ($data) {
            $output = fopen('php://output', 'w');

            // Excel-friendly UTF-8 BOM.
            fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($output, ['TEULE KENYA - DONOR INTELLIGENCE REPORT']);
            fputcsv($output, ['Generated', now()->format('d M Y H:i')]);
            fputcsv($output, []);

            fputcsv($output, ['DONOR OVERVIEW']);
            fputcsv($output, ['Total registered donors', $data['totalDonors']]);
            fputcsv($output, ['Donors with donations', $data['donorsWithGiving']]);
            fputcsv($output, ['New donors in selected period', $data['newDonors']]);
            fputcsv($output, ['Repeat donors', $data['repeatDonors']]);
            fputcsv($output, ['Active donors', $data['activeDonors']]);
            fputcsv($output, ['Lapsed donors', $data['lapsedDonors']]);
            fputcsv($output, ['Repeat donor rate (%)', $data['retentionRate']]);
            fputcsv($output, []);

            fputcsv($output, ['CASH DONATIONS']);
            fputcsv($output, ['Currency', 'Donation count', 'Total amount']);

            foreach ($data['cashByCurrency'] as $row) {
                fputcsv($output, [
                    $row->currency,
                    $row->donation_count,
                    $row->total_amount,
                ]);
            }

            fputcsv($output, []);
            fputcsv($output, ['IN-KIND DONATIONS']);
            fputcsv($output, ['Currency', 'Donation count', 'Estimated value']);

            foreach ($data['inKindByCurrency'] as $row) {
                fputcsv($output, [
                    $row->currency,
                    $row->donation_count,
                    $row->estimated_value,
                ]);
            }

            fputcsv($output, []);
            fputcsv($output, ['TOP CASH DONORS']);
            fputcsv($output, ['Donor', 'Currency', 'Donation count', 'Total amount']);

            foreach ($data['topDonors'] as $row) {
                fputcsv($output, [
                    $row->name,
                    $row->currency,
                    $row->donation_count,
                    $row->total_amount,
                ]);
            }

            fputcsv($output, []);
            fputcsv($output, ['COMMUNICATIONS']);
            fputcsv($output, ['Channel', 'Status', 'Total']);

            foreach ($data['communications'] as $row) {
                fputcsv($output, [
                    $row->channel,
                    $row->status,
                    $row->total,
                ]);
            }

            fclose($output);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
