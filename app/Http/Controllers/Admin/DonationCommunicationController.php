<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DonationCommunication;
use Illuminate\Http\Request;

class DonationCommunicationController extends Controller
{

public function index(Request $request)
{
    $query = DonationCommunication::with([
        'donor',
        'donation',
        'sentBy',
    ]);

    /*
    |--------------------------------------------------------------------------
    | Search
    |--------------------------------------------------------------------------
    */
    if ($request->filled('search')) {
        $search = trim($request->input('search'));

        $query->where(function ($q) use ($search) {
            $q->where('recipient', 'like', "%{$search}%")
                ->orWhere('subject', 'like', "%{$search}%")
                ->orWhere('message', 'like', "%{$search}%")
                ->orWhere('type', 'like', "%{$search}%")
                ->orWhereHas('donor', function ($donorQuery) use ($search) {
                    $donorQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Filter by communication channel
    |--------------------------------------------------------------------------
    */
    if ($request->filled('channel')) {
        $query->where('channel', $request->input('channel'));
    }

    /*
    |--------------------------------------------------------------------------
    | Filter by communication status
    |--------------------------------------------------------------------------
    */
    if ($request->filled('status')) {
        $query->where('status', $request->input('status'));
    }

    /*
    |--------------------------------------------------------------------------
    | Results and pagination
    |--------------------------------------------------------------------------
    */
    $communications = $query
        ->orderByDesc('created_at')
        ->paginate(15)
        ->withQueryString();

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