<?php

namespace App\Http\Controllers\Admin\MpesaCollector;

use App\Http\Controllers\Controller;
use App\Models\MpesaCollectorDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MpesaCollectorDeviceManagementController extends Controller
{
    /**
     * Display registered collector devices.
     */
    public function index(Request $request)
    {
        $query = MpesaCollectorDevice::with('approver')
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $devices = $query->paginate(20)->withQueryString();

        $counts = [
            'all' => MpesaCollectorDevice::count(),
            'pending' => MpesaCollectorDevice::where('status', 'pending')->count(),
            'approved' => MpesaCollectorDevice::where('status', 'approved')->count(),
            'suspended' => MpesaCollectorDevice::where('status', 'suspended')->count(),
            'rejected' => MpesaCollectorDevice::where('status', 'rejected')->count(),
            'revoked' => MpesaCollectorDevice::where('status', 'revoked')->count(),
        ];

        return view(
            'admin.mpesa-collector.devices',
            compact('devices', 'counts')
        );
    }

    /**
     * Approve a pending device and generate its API token.
     */
    public function approve($id)
    {
        $device = MpesaCollectorDevice::findOrFail($id);

        if ($device->status !== 'pending') {
            return back()->with(
                'error',
                'Only pending devices can be approved.'
            );
        }

        // Generate a cryptographically secure, unique token.
        $plainToken = bin2hex(random_bytes(32));

        DB::transaction(function () use ($device, $plainToken) {
            $device->status = 'approved';
            $device->approved_by = auth()->id();
            $device->approved_at = now();
            $device->rejected_at = null;
            $device->revoked_at = null;
            $device->api_token_hash = hash('sha256', $plainToken);
            $device->save();
        });

        // Show the raw token once. It is never stored in the database.
        return back()
            ->with('success', 'Device approved successfully.')
            ->with('new_device_token', $plainToken)
            ->with('new_device_name', $device->device_name)
            ->with('new_device_id', $device->device_id);
    }

    /**
     * Reject a pending device.
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'admin_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $device = MpesaCollectorDevice::findOrFail($id);

        if ($device->status !== 'pending') {
            return back()->with(
                'error',
                'Only pending devices can be rejected.'
            );
        }

        $device->status = 'rejected';
        $device->rejected_at = now();
        $device->api_token_hash = null;
        $device->admin_notes = $request->admin_notes;
        $device->save();

        return back()->with('success', 'Device registration rejected.');
    }

    /**
     * Suspend an approved device.
     */
    public function suspend($id)
    {
        $device = MpesaCollectorDevice::findOrFail($id);

        if ($device->status !== 'approved') {
            return back()->with(
                'error',
                'Only approved devices can be suspended.'
            );
        }

        $device->status = 'suspended';
        $device->save();

        return back()->with('success', 'Device suspended successfully.');
    }

    /**
     * Permanently revoke a device's access.
     */
    public function revoke($id)
    {
        $device = MpesaCollectorDevice::findOrFail($id);

        if (!in_array($device->status, ['approved', 'suspended'], true)) {
            return back()->with(
                'error',
                'This device cannot be revoked in its current status.'
            );
        }

        $device->status = 'revoked';
        $device->revoked_at = now();
        $device->api_token_hash = null;
        $device->save();

        return back()->with('success', 'Device access revoked successfully.');
    }
}
