<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MpesaCollectorDevice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MpesaCollectorDeviceController extends Controller
{
    /**
     * Register an Android M-Pesa collector device.
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'device_id' => [
                'required',
                'string',
                'max:50',
                'regex:/^TL-MP-[A-F0-9]{8}$/i',
            ],
            'device_name' => [
                'required',
                'string',
                'max:255',
            ],
            'location' => [
                'nullable',
                'string',
                'max:255',
            ],
            'department' => [
                'nullable',
                'string',
                'max:255',
            ],
            'platform' => [
                'nullable',
                'in:android',
            ],
            'app_version' => [
                'nullable',
                'string',
                'max:30',
            ],
        ]);

        $device = MpesaCollectorDevice::where(
            'device_id',
            $validated['device_id']
        )->first();

        // Prevent rejected, suspended or revoked devices
        // from reactivating themselves.
        if ($device && in_array($device->status, [
            'rejected',
            'suspended',
            'revoked',
        ], true)) {
            return response()->json([
                'success' => false,
                'message' => 'This device is not authorized to register. Please contact the system administrator.',
                'status' => $device->status,
            ], 403);
        }

        $isNewDevice = !$device;

        if (!$device) {
            $device = new MpesaCollectorDevice();
            $device->device_id = $validated['device_id'];
            $device->status = 'pending';
        }

        // Update device details without changing its approval
        // status or existing authentication credentials.
        $device->device_name = $validated['device_name'];
        $device->location = $validated['location'] ?? null;
        $device->department = $validated['department'] ?? null;
        $device->platform = $validated['platform'] ?? 'android';
        $device->app_version = $validated['app_version'] ?? null;

        $device->save();

        return response()->json([
            'success' => true,
            'message' => $device->status === 'approved'
                ? 'This device is already approved.'
                : 'Device registered successfully. Awaiting administrator approval.',
            'data' => [
                'device_id' => $device->device_id,
                'device_name' => $device->device_name,
                'location' => $device->location,
                'department' => $device->department,
                'status' => $device->status,
            ],
        ], $isNewDevice ? 201 : 200);
    }

    /**
     * Return the authenticated device's registration status.
     */
    public function status(Request $request): JsonResponse
    {
        $device = $request->attributes->get(
            'mpesa_collector_device'
        );

        return response()->json([
            'success' => true,
            'data' => [
                'device_id' => $device->device_id,
                'device_name' => $device->device_name,
                'status' => $device->status,
                'location' => $device->location,
                'department' => $device->department,
                'approved_at' => $device->approved_at,
                'last_sync_at' => $device->last_sync_at,
            ],
        ]);
    }

    /**
     * Receive a heartbeat from the Android application.
     */
    public function heartbeat(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'app_version' => [
                'nullable',
                'string',
                'max:30',
            ],
        ]);

        $device = $request->attributes->get(
            'mpesa_collector_device'
        );

        $device->forceFill([
            'last_seen_at' => now(),
            'app_version' => $validated['app_version']
                ?? $device->app_version,
        ])->save();

        return response()->json([
            'success' => true,
            'message' => 'Device heartbeat received.',
            'data' => [
                'device_id' => $device->device_id,
                'status' => $device->status,
                'server_time' => now()->toIso8601String(),
            ],
        ]);
    }


/**
 * Return dashboard statistics for the authenticated Android device.
 */
public function dashboard(Request $request): JsonResponse
{
    $device = $request->attributes->get(
        'mpesa_collector_device'
    );

    $messages = DB::table('mpesa_collector_messages')
        ->where('mpesa_collector_device_id', $device->id);

    $messageStats = [
        'total' => (clone $messages)->count(),

        'processed' => (clone $messages)
            ->where('processing_status', 'processed')
            ->count(),

        'unrecognized' => (clone $messages)
            ->where('processing_status', 'unrecognized')
            ->count(),

        'pending_processing' => (clone $messages)
            ->where('processing_status', 'pending')
            ->count(),

        'processing_failed' => (clone $messages)
            ->where('processing_status', 'failed')
            ->count(),
    ];

    $transactionsTotal = DB::table('mpesa_collector_transactions as t')
        ->join(
            'mpesa_collector_messages as m',
            'm.id',
            '=',
            't.mpesa_collector_message_id'
        )
        ->where('m.mpesa_collector_device_id', $device->id)
        ->count();

    return response()->json([
        'success' => true,
        'message' => 'Dashboard statistics retrieved successfully.',
        'data' => [
            'device' => [
                'device_id' => $device->device_id,
                'device_name' => $device->device_name,
                'status' => $device->status,
                'location' => $device->location,
                'department' => $device->department,
                'last_seen_at' => $device->last_seen_at,
                'last_sync_at' => $device->last_sync_at,
            ],

            'messages' => $messageStats,

            'transactions' => [
                'total' => $transactionsTotal,
            ],

            'server_time' => now()->toIso8601String(),
        ],
    ]);
}


}
