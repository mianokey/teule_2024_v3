<?php

namespace App\Http\Middleware;

use App\Models\MpesaCollectorDevice;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateMpesaCollectorDevice
{
    public function handle(Request $request, Closure $next): Response
    {
        $plainToken = $request->bearerToken();

        if (!$plainToken) {
            return response()->json([
                'success' => false,
                'message' => 'Device authentication token is required.',
            ], 401);
        }

        $tokenHash = hash('sha256', $plainToken);

        $device = MpesaCollectorDevice::where(
            'api_token_hash',
            $tokenHash
        )
            ->where('status', 'approved')
            ->first();

        if (
            !$device ||
            !hash_equals($device->api_token_hash, $tokenHash)
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or inactive device credentials.',
            ], 401);
        }

        $device->forceFill([
            'last_seen_at' => now(),
        ])->save();

        // Make the authenticated device available to API controllers.
        $request->attributes->set(
            'mpesa_collector_device',
            $device
        );

        return $next($request);
    }
}
