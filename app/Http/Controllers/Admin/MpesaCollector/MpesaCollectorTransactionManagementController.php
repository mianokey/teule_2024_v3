<?php

namespace App\Http\Controllers\Admin\MpesaCollector;

use App\Http\Controllers\Controller;
use App\Models\MpesaCollectorTransaction;
use App\Models\MpesaCollectorDevice;
use Illuminate\Http\Request;

class MpesaCollectorTransactionManagementController extends Controller
{
    private function filteredQuery(Request $request)
    {
        $query = MpesaCollectorTransaction::with([
            'message.device',
        ]);

        if ($request->filled('q')) {
            $search = trim($request->q);

            $query->where(function ($q) use ($search) {
                $q->where('receipt_number', 'LIKE', "%{$search}%")
                    ->orWhere('counterparty_name', 'LIKE', "%{$search}%")
                    ->orWhere('counterparty_phone', 'LIKE', "%{$search}%")
                    ->orWhere('account_reference', 'LIKE', "%{$search}%")
                    ->orWhere('shortcode', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('transaction_type', $request->type);
        }

        if ($request->filled('direction')) {
            $query->where('direction', $request->direction);
        }

        if ($request->filled('status')) {
            $query->where('processing_status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('transaction_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('transaction_at', '<=', $request->date_to);
        }

        if ($request->filled('device_id')) {
            $query->whereHas('message', function ($message) use ($request) {
                $message->where(
                    'mpesa_collector_device_id',
                    $request->device_id
                );
            });
        }

        return $query;
    }

    public function index(Request $request)
    {
       $transactions = $this->filteredQuery($request)
    ->latest('transaction_at')
    ->paginate(20);

$transactions->appends($request->query());


        $counts = [
            'all' => MpesaCollectorTransaction::count(),
            'incoming' => MpesaCollectorTransaction::where('direction', 'INCOMING')->count(),
            'outgoing' => MpesaCollectorTransaction::where('direction', 'OUTGOING')->count(),
            'processed' => MpesaCollectorTransaction::where('processing_status', 'processed')->count(),
            'failed' => MpesaCollectorTransaction::where('processing_status', 'failed')->count(),
        ];

        $devices = MpesaCollectorDevice::orderBy('device_name')
            ->get(['id', 'device_id', 'device_name']);

        $transactionTypes = MpesaCollectorTransaction::query()
            ->whereNotNull('transaction_type')
            ->distinct()
            ->orderBy('transaction_type')
            ->pluck('transaction_type');

        return view(
            'admin.mpesa-collector.transactions',
            compact('transactions', 'counts', 'devices', 'transactionTypes')
        );
    }

    public function exportCsv(Request $request)
    {
        $transactions = $this->filteredQuery($request)
            ->latest('transaction_at')
            ->get();

        $filename = 'mpesa-transactions-' . now()->format('Y-m-d-His') . '.csv';

        return response()->streamDownload(function () use ($transactions) {

            $handle = fopen('php://output', 'w');

            // Excel-friendly UTF-8 BOM
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'Transaction Date',
                'Receipt Number',
                'Transaction Type',
                'Direction',
                'Amount (KES)',
                'Transaction Cost (KES)',
                'M-Pesa Balance (KES)',
                'Daily Limit Remaining (KES)',
                'Counterparty Name',
                'Counterparty Phone',
                'Account Reference',
                'Shortcode',
                'Device Name',
                'Device ID',
                'Processing Status',
            ]);

            foreach ($transactions as $transaction) {
                fputcsv($handle, [
                    $transaction->transaction_at,
                    $transaction->receipt_number,
                    $transaction->transaction_type,
                    $transaction->direction,
                    $transaction->amount,
                    $transaction->transaction_cost,
                    $transaction->balance,
                    $transaction->daily_limit_remaining,
                    $transaction->counterparty_name,
                    $transaction->counterparty_phone,
                    $transaction->account_reference,
                    $transaction->shortcode,
                    $transaction->message?->device?->device_name,
                    $transaction->message?->device?->device_id,
                    $transaction->processing_status,
                ]);
            }

            fclose($handle);

        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
