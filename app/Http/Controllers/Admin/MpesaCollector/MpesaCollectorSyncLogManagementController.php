<?php

namespace App\Http\Controllers\Admin\MpesaCollector;

use App\Http\Controllers\Controller;
use App\Models\MpesaCollectorSyncLog;
use App\Models\MpesaCollectorDevice;
use Illuminate\Http\Request;

class MpesaCollectorSyncLogManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = MpesaCollectorSyncLog::with('device');

        // Search
        if ($request->filled('q')) {
            $search = trim($request->q);

            $query->where(function ($q) use ($search) {
                $q->where('sync_reference', 'LIKE', "%{$search}%")
                    ->orWhereHas('device', function ($device) use ($search) {
                        $device->where('device_name', 'LIKE', "%{$search}%")
                            ->orWhere('device_id', 'LIKE', "%{$search}%");
                    });
            });
        }

        // Sync status
        if ($request->filled('status')) {
            $query->where('sync_status', $request->status);
        }

        // Device
        if ($request->filled('device_id')) {
            $query->where(
                'mpesa_collector_device_id',
                $request->device_id
            );
        }

        // Date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $syncLogs = $query
    ->latest('created_at')
    ->paginate(20);

$syncLogs->appends($request->query());
        // Overall statistics
        $counts = [
            'all' => MpesaCollectorSyncLog::count(),

            'completed' => MpesaCollectorSyncLog::where(
                'sync_status',
                'completed'
            )->count(),

            'partial' => MpesaCollectorSyncLog::where(
                'sync_status',
                'partial'
            )->count(),

            'failed' => MpesaCollectorSyncLog::where(
                'sync_status',
                'failed'
            )->count(),

            'messages_received' => MpesaCollectorSyncLog::sum(
                'messages_received'
            ),

            'messages_saved' => MpesaCollectorSyncLog::sum(
                'messages_saved'
            ),
        ];

        $devices = MpesaCollectorDevice::orderBy('device_name')
            ->get([
                'id',
                'device_id',
                'device_name',
            ]);

        return view(
            'admin.mpesa-collector.sync-logs',
            compact('syncLogs', 'counts', 'devices')
        );
    }
}
