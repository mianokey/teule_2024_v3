<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReportAuditLog;
use App\Models\Store;
use App\Models\StoreItem;
use App\Models\StoreStock;
use App\Models\StoreStockMovement;
use App\Services\Reports\ReportExportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;

class StoreReportController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'report' => ['nullable', Rule::in(['current-stock', 'stock-movements'])],
            'store_id' => ['nullable', 'integer', 'exists:stores,id'],
            'item_id' => ['nullable', 'integer', 'exists:store_items,id'],
            'variant_id' => ['nullable', 'integer', 'exists:store_item_variants,id'],
            'movement_type' => [
                'nullable',
                Rule::in([
                    'RECEIPT', 'ISSUE', 'RETURN_IN', 'RETURN_OUT',
                    'TRANSFER_IN', 'TRANSFER_OUT', 'ADJUSTMENT',
                ]),
            ],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ]);

        $report = $request->input('report', 'current-stock');

        $stores = Store::where('is_active', true)->orderBy('name')->get();
        $items = StoreItem::with('unit')->where('is_active', true)->orderBy('name')->get();

        $variants = collect();

        if ($request->filled('item_id')) {
            $variants = \App\Models\StoreItemVariant::where('store_item_id', $request->item_id)
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
        }

        $rows = $this->getRows($request, $report);

        return view('admin.stores.reports.index', compact(
            'report',
            'stores',
            'items',
            'variants',
            'rows'
        ));
    }

    public function export(
        Request $request,
        string $format,
        ReportExportService $exporter
    ) {
        abort_unless(in_array($format, ['pdf', 'xlsx', 'csv'], true), 404);

        $request->validate([
            'report' => ['required', Rule::in(['current-stock', 'stock-movements'])],
            'store_id' => ['nullable', 'integer', 'exists:stores,id'],
            'item_id' => ['nullable', 'integer', 'exists:store_items,id'],
            'variant_id' => ['nullable', 'integer', 'exists:store_item_variants,id'],
            'movement_type' => [
                'nullable',
                Rule::in([
                    'RECEIPT', 'ISSUE', 'RETURN_IN', 'RETURN_OUT',
                    'TRANSFER_IN', 'TRANSFER_OUT', 'ADJUSTMENT',
                ]),
            ],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ]);

        $report = $request->input('report');
        $title = $report === 'current-stock'
            ? 'Current Stock Report'
            : 'Stock Movement Report';

        $rows = $this->getRows($request, $report);
        $headings = $this->headings($report);

        ReportAuditLog::create([
            'user_id' => $request->user()?->id,
            'report_key' => 'stores.' . $report,
            'report_title' => $title,
            'format' => $format,
            'filters' => $request->only([
                'store_id', 'item_id', 'variant_id',
                'movement_type', 'date_from', 'date_to',
            ]),
            'date_from' => $request->input('date_from'),
            'date_to' => $request->input('date_to'),
            'row_count' => $rows->count(),
        ]);

        $filename = 'teule-' . $report . '-' . now()->format('Ymd-His');

        if ($format === 'pdf') {
            return Pdf::loadView('admin.stores.reports.pdf', [
                'title' => $title,
                'headings' => $headings,
                'rows' => $rows,
                'generatedAt' => now(),
                'filters' => $request->only([
                    'date_from', 'date_to', 'store_id',
                    'item_id', 'variant_id', 'movement_type',
                ]),
            ])->setPaper('a4', 'landscape')->download($filename . '.pdf');
        }

        return $exporter->download(
            $headings,
            $rows->map(fn ($row) => array_values($row))->all(),
            $filename,
            $format
        );
    }

    private function getRows(Request $request, string $report): Collection
    {
        if ($report === 'current-stock') {
            return StoreStock::with(['store', 'item.unit', 'variant'])
                ->whereHas('store', fn ($q) => $q->where('is_active', true))
                ->whereHas('item', fn ($q) => $q->where('is_active', true))
                ->when($request->filled('store_id'),
                    fn ($q) => $q->where('store_id', $request->store_id))
                ->when($request->filled('item_id'),
                    fn ($q) => $q->where('store_item_id', $request->item_id))
                ->when($request->filled('variant_id'),
                    fn ($q) => $q->where('variant_id', $request->variant_id))
                ->orderBy('store_id')
                ->orderBy('store_item_id')
                ->get()
                ->map(fn ($stock) => [
                    'Store' => $stock->store?->name,
                    'Item' => $stock->item?->name,
                    'Variant' => $stock->variant?->name ?? '—',
                    'Unit' => $stock->item?->unit?->code,
                    'Current Stock' => $stock->quantity,
                    'Last Movement' => $stock->last_movement_at?->format('Y-m-d H:i:s') ?? '—',
                ]);
        }

        return StoreStockMovement::with(['store', 'item.unit', 'variant', 'creator'])
            ->whereHas('store', fn ($q) => $q->where('is_active', true))
            ->whereHas('item', fn ($q) => $q->where('is_active', true))
            ->when($request->filled('store_id'),
                fn ($q) => $q->where('store_id', $request->store_id))
            ->when($request->filled('item_id'),
                fn ($q) => $q->where('store_item_id', $request->item_id))
            ->when($request->filled('variant_id'),
                fn ($q) => $q->where('variant_id', $request->variant_id))
            ->when($request->filled('movement_type'),
                fn ($q) => $q->where('movement_type', $request->movement_type))
            ->when($request->filled('date_from'),
                fn ($q) => $q->where('created_at', '>=', $request->date_from . ' 00:00:00'))
            ->when($request->filled('date_to'),
                fn ($q) => $q->where('created_at', '<=', $request->date_to . ' 23:59:59'))
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($movement) => [
                'Date' => $movement->created_at?->format('Y-m-d H:i:s'),
                'Store' => $movement->store?->name,
                'Item' => $movement->item?->name,
                'Variant' => $movement->variant?->name ?? '—',
                'Movement Type' => $movement->movement_type,
                'Quantity' => $movement->quantity,
                'Balance After' => $movement->balance_after,
                'Reference' => $movement->reference_type
                    ? class_basename($movement->reference_type) . ' #' . $movement->reference_id
                    : '—',
                'Recorded By' => $movement->creator?->name ?? '—',
                'Notes' => $movement->notes ?? '—',
            ]);
    }

    private function headings(string $report): array
    {
        return $report === 'current-stock'
            ? ['Store', 'Item', 'Variant', 'Unit', 'Current Stock', 'Last Movement']
            : [
                'Date', 'Store', 'Item', 'Variant', 'Movement Type',
                'Quantity', 'Balance After', 'Reference', 'Recorded By', 'Notes',
            ];
    }
}
