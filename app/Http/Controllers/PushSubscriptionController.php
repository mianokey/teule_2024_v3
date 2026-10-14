<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use NotificationChannels\WebPush\PushSubscription;

class PushSubscriptionController extends Controller
{
    /**
     * Save or update the logged-in user's browser subscription.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'endpoint' => ['required', 'string', 'url', 'max:500'],
            'keys.p256dh' => ['required', 'string', 'max:255'],
            'keys.auth' => ['required', 'string', 'max:255'],
            'contentEncoding' => ['nullable', 'string', 'max:50'],
        ]);

        $user = $request->user();

        $subscription = PushSubscription::findByEndpoint(
            $validated['endpoint']
        );

        if ($subscription) {
            // Do not allow a different user to take over an existing
            // subscription without explicitly transferring it.
            if (
                $subscription->subscribable_type !== get_class($user) ||
                (string) $subscription->subscribable_id !== (string) $user->getKey()
            ) {
                return response()->json([
                    'message' => 'This browser subscription is already registered to another account.',
                ], 409);
            }

            $subscription->update([
                'public_key' => $validated['keys']['p256dh'],
                'auth_token' => $validated['keys']['auth'],
                'content_encoding' => $validated['contentEncoding'] ?? 'aes128gcm',
            ]);
        } else {
            $user->updatePushSubscription(
                $validated['endpoint'],
                $validated['keys']['p256dh'],
                $validated['keys']['auth'],
                $validated['contentEncoding'] ?? 'aes128gcm'
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Push notifications enabled for this device.',
        ]);
    }

    /**
     * Remove a subscription belonging to the logged-in user.
     */
    public function destroy(Request $request)
    {
        $validated = $request->validate([
            'endpoint' => ['required', 'string', 'url', 'max:500'],
        ]);

        $subscription = PushSubscription::findByEndpoint(
            $validated['endpoint']
        );

        if (
            $subscription &&
            $subscription->subscribable_type === get_class($request->user()) &&
            (string) $subscription->subscribable_id === (string) $request->user()->getKey()
        ) {
            $subscription->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Push subscription removed.',
        ]);
    }
}
