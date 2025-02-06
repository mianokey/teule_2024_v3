<?php

namespace App\Http\Controllers;

use App\Models\Payee;
use App\Models\PettyCashApproval;
use App\Models\PettyCashRequest;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Permission\Models\Permission;

class PettycashController extends Controller
{
    use HasFactory, Notifiable, HasRoles; 
    public function create()
{
    $payees = Payee::select('id', 'name','ref_one', 'ref_two')->get(); // Adjust model and columns as necessary
    return view('admin.pettycash.create', compact('payees'));
}

public function addPayee(Request $request)
{
    $request->validate([
        'payee_name' => 'required|string|max:255',
        'payee_phone' => 'required|string|max:255',
        'payee_store' => 'required|string|max:255',
    ]);

    $payee = new Payee();
    $payee->name = $request->payee_name;
    $payee->ref_one = $request->payee_phone;
    $payee->ref_two = $request->payee_store;
    $payee->save();

    return redirect()->back()->with('success', 'Payee added successfully');
}



public function store(Request $request)
{
    // Validate inputs
    $validated = $request->validate([
        'payee' => 'nullable|exists:payees,id|required_without:new_payee',
        'new_payee' => 'nullable|string|required_without:payee',
        'payment_medium' => 'required|string',
        'store_account' => 'required|string',
        'amount' => 'required|numeric|min:1',
        'purpose' => 'required|string',
        'details' => 'nullable|string',
        'receipt' => 'nullable|file|mimes:jpeg,png,pdf',
    ]);

    $payee = null;

    // Handle new payee creation or existing payee selection
    if ($request->filled('new_payee')) {
        // Create or retrieve the payee
        $payee = Payee::firstOrCreate([
            'name' => $request->new_payee,
            'ref_one' => $request->payment_medium,
            'ref_two' => $request->store_account,
        ]);
    } elseif ($request->filled('payee')) {
        // Retrieve existing payee
        $payee = Payee::findOrFail($request->payee);

        // Validate that the selected payee matches the provided details
        if (
            $payee->ref_one !== $request->payment_medium ||
            $payee->ref_two !== $request->store_account
        ) {
            return back()->withErrors([
                'payee' => 'The selected payee details do not match the provided payment medium and store account.',
            ]);
        }
    }

    // Store receipt if provided
    $receiptPath = $request->file('receipt')?->store('receipts');

    // Create a new petty cash request
    $pettyCashRequest = PettyCashRequest::create([
        'payee_id' => $payee->id,
        'payment_medium' => $request->payment_medium,
        'store_account' => $request->store_account,
        'amount' => $request->amount,
        'purpose' => $request->purpose,
        'details' => $request->details,
        'receipt' => $receiptPath,
    ]);

    return redirect()
        ->route('admin.pettycash.index')
        ->with('success', 'Petty cash request submitted successfully.');
}


public function approve(Request $request, $id)
{
    $pettyCashRequest = PettyCashRequest::findOrFail($id);

    $user = auth()->user();

    if (!$user->can('approve pettycash')) {
        return back()->withErrors(['error' => 'You do not have permission to approve this request.']);
    }

    PettyCashApproval::create([
        'petty_cash_request_id' => $pettyCashRequest->id,
        'user_id' => $user->id,
        'approved' => true,
    ]);

    $approvalCount = $pettyCashRequest->approvals()->where('approved', true)->count();

    if ($approvalCount >= 2) {
        $pettyCashRequest->update(['status' => 'Approved']);
    } else {
        $pettyCashRequest->update(['status' => "Awaiting " . (2 - $approvalCount) . " Approvals"]);
    }

    return back()->with('success', 'Approval submitted successfully.');
}
}

