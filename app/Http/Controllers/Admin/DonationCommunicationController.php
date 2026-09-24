<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DonationCommunication;
use Illuminate\Http\Request;

class DonationCommunicationController extends Controller
{
    public function index(Request $request)
    {
        $communications = DonationCommunication::with([
            'donor',
            'donation',
            'sentBy',
        ])
            ->latest()
            ->get();

        return view(
            'admin.donation-communications.index',
            compact('communications')
        );
    }

    public function show(DonationCommunication $communication)
    {
        $communication->load([
            'donor',
            'donation',
            'sentBy',
        ]);

        return view(
            'admin.donation-communications.show',
            compact('communication')
        );
    }

    public function cancel(DonationCommunication $communication)
    {
        if ($communication->status !== 'pending') {
            return back()->withErrors([
                'communication' =>
                    'This message can no longer be cancelled.',
            ]);
        }

        $communication->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        return back()->with(
            'success',
            'Message sending cancelled successfully.'
        );
    }
}