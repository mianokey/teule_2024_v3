<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Http\Controllers\Controller;
use App\Models\CostCentre;
use App\Models\CostCentreBudget;
use App\Models\FinancePeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CostCentreBudgetController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Budget Listing
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $query = CostCentreBudget::with([
            'period',
            'costCentre.department',
            'creator',
        ]);

        if ($request->filled('finance_period_id')) {
            $query->where('finance_period_id', $request->finance_period_id);
        }

        if ($request->filled('cost_centre_id')) {
            $query->where('cost_centre_id', $request->cost_centre_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $budgets = $query
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $financePeriods = FinancePeriod::orderByDesc('year')
            ->orderByDesc('month')
            ->get();

        $costCentres = CostCentre::with('department')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.finance.budgets.index', compact(
            'budgets',
            'financePeriods',
            'costCentres'
        ));
    }


    /*
    |--------------------------------------------------------------------------
    | Create Budget
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        $financePeriods = FinancePeriod::where('status', 'open')
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->get();

        $costCentres = CostCentre::with('department')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.finance.budgets.create', compact(
            'financePeriods',
            'costCentres'
        ));
    }


    /*
    |--------------------------------------------------------------------------
    | Store Budget
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $validated = $request->validate([
            'finance_period_id' => [
                'required',
                'exists:finance_periods,id',
            ],
            'cost_centre_id' => [
                'required',
                'exists:cost_centres,id',
            ],
            'requested_amount' => [
                'required',
                'numeric',
                'min:0.01',
                'max:9999999999999.99',
            ],
            'notes' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        $period = FinancePeriod::findOrFail(
            $validated['finance_period_id']
        );

        if ($period->status !== 'open') {
            return back()
                ->withInput()
                ->with('error', 'Budgets cannot be created for a closed financial period.');
        }

        $costCentre = CostCentre::where('is_active', true)
            ->findOrFail($validated['cost_centre_id']);

        $exists = CostCentreBudget::where(
            'finance_period_id',
            $period->id
        )->where(
            'cost_centre_id',
            $costCentre->id
        )->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->with('error', 'A budget already exists for this cost centre and financial period.');
        }

        CostCentreBudget::create([
            'finance_period_id' => $period->id,
            'cost_centre_id' => $costCentre->id,
            'requested_amount' => $validated['requested_amount'],
            'approved_amount' => null,
            'carried_forward_amount' => 0,
            'status' => 'draft',
            'approval_stage' => null,
            'rejection_reason' => null,
            'return_reason' => null,
            'notes' => $validated['notes'] ?? null,
            'created_by' => auth()->id(),
        ]);

        return redirect()
            ->route('admin.finance.budgets.index', [
                'finance_period_id' => $period->id,
            ])
            ->with('success', 'Monthly budget created successfully.');
    }


    /*
    |--------------------------------------------------------------------------
    | Edit Budget
    |--------------------------------------------------------------------------
    */


public function edit(CostCentreBudget $budget)
{
    abort_unless(
        (int) $budget->created_by === (int) auth()->id(),
        403,
        'You are not authorised to edit this budget.'
    );

    if (!in_array($budget->status, ['draft', 'returned'], true)) {
        return redirect()
            ->route('admin.finance.budgets.index')
            ->with('error', 'Only draft or returned budgets can be edited.');
    }

    $periodStatus = FinancePeriod::whereKey($budget->finance_period_id)
        ->value('status');

    if ($periodStatus !== 'open') {
        return redirect()
            ->route('admin.finance.budgets.index')
            ->with('error', 'Budgets in closed financial periods cannot be edited.');
    }

    $financePeriods = FinancePeriod::where('status', 'open')
        ->orderByDesc('year')
        ->orderByDesc('month')
        ->get();

    $costCentres = CostCentre::with('department')
        ->where(function ($query) use ($budget) {
            $query->where('is_active', true)
                ->orWhere('id', $budget->cost_centre_id);
        })
        ->orderBy('name')
        ->get();

    return view('admin.finance.budgets.edit', compact(
        'budget',
        'financePeriods',
        'costCentres'
    ));
}



    /*
    |--------------------------------------------------------------------------
    | Update Budget
    |--------------------------------------------------------------------------
    */


public function update(Request $request, CostCentreBudget $budget)
{
    $validated = $request->validate([
        'finance_period_id' => [
            'required',
            'exists:finance_periods,id',
        ],
        'cost_centre_id' => [
            'required',
            'exists:cost_centres,id',
        ],
        'requested_amount' => [
            'required',
            'numeric',
            'min:0.01',
            'max:9999999999999.99',
        ],
        'notes' => [
            'nullable',
            'string',
            'max:2000',
        ],
    ]);

    $error = DB::transaction(function () use ($budget, $validated) {

        $lockedBudget = CostCentreBudget::whereKey($budget->id)
            ->lockForUpdate()
            ->firstOrFail();

        abort_unless(
            (int) $lockedBudget->created_by === (int) auth()->id(),
            403,
            'You are not authorised to edit this budget.'
        );

        if (!in_array($lockedBudget->status, ['draft', 'returned'], true)) {
            return 'Only draft or returned budgets can be edited.';
        }

        $period = FinancePeriod::whereKey($validated['finance_period_id'])
            ->lockForUpdate()
            ->firstOrFail();

        if ($period->status !== 'open') {
            return 'You cannot assign a budget to a closed financial period.';
        }

        $costCentreQuery = CostCentre::where(
            'id',
            $validated['cost_centre_id']
        );

        if (
            (int) $validated['cost_centre_id']
            !== (int) $lockedBudget->cost_centre_id
        ) {
            $costCentreQuery->where('is_active', true);
        }

        $costCentre = $costCentreQuery->firstOrFail();

        $duplicate = CostCentreBudget::where(
            'finance_period_id',
            $period->id
        )
            ->where('cost_centre_id', $costCentre->id)
            ->where('id', '!=', $lockedBudget->id)
            ->exists();

        if ($duplicate) {
            return 'A budget already exists for this cost centre and financial period.';
        }

        $lockedBudget->update([
            'finance_period_id' => $period->id,
            'cost_centre_id' => $costCentre->id,
            'requested_amount' => $validated['requested_amount'],
            'notes' => $validated['notes'] ?? null,

            'status' => 'draft',
            'approval_stage' => null,

            'rejection_reason' => null,
            'return_reason' => null,

            'submitted_by' => null,
            'submitted_at' => null,

            'approved_by' => null,
            'approved_at' => null,
            'approved_amount' => null,
        ]);

        return null;
    });

    if ($error) {
        return back()
            ->withInput()
            ->with('error', $error);
    }

    return redirect()
        ->route('admin.finance.budgets.index', [
            'finance_period_id' => $validated['finance_period_id'],
        ])
        ->with('success', 'Monthly budget updated successfully.');
}



    /*
    |--------------------------------------------------------------------------
    | Submit Budget for Approval
    |--------------------------------------------------------------------------
    */


public function submit(CostCentreBudget $budget)
{
    $error = DB::transaction(function () use ($budget) {

        $lockedBudget = CostCentreBudget::whereKey($budget->id)
            ->lockForUpdate()
            ->firstOrFail();

        abort_unless(
            (int) $lockedBudget->created_by === (int) auth()->id(),
            403,
            'You are not authorised to submit this budget.'
        );

        if (!in_array($lockedBudget->status, ['draft', 'returned'], true)) {
            return 'Only draft or returned budgets can be submitted.';
        }

        $period = FinancePeriod::whereKey($lockedBudget->finance_period_id)
            ->lockForUpdate()
            ->firstOrFail();

        if ($period->status !== 'open') {
            return 'This financial period is closed.';
        }

        if ((float) $lockedBudget->requested_amount <= 0) {
            return 'The requested amount must be greater than zero.';
        }

        $lockedBudget->update([
            'status' => 'submitted',
            'approval_stage' => 'finance',

            'submitted_by' => auth()->id(),
            'submitted_at' => now(),

            'rejection_reason' => null,
            'return_reason' => null,

            'approved_amount' => null,
            'approved_by' => null,
            'approved_at' => null,
        ]);

        return null;
    });

    if ($error) {
        return back()->with('error', $error);
    }

    return redirect()
        ->route('admin.finance.budgets.index')
        ->with('success', 'Monthly budget submitted for Finance approval.');
}



    /*
    |--------------------------------------------------------------------------
    | Approve Budget
    |--------------------------------------------------------------------------
    */

    public function approve(Request $request, CostCentreBudget $budget)
    {
        $validated = $request->validate([
            'comments' => [
                'nullable',
                'string',
                'max:2000',
            ],
            'approved_amount' => [
                'nullable',
                'numeric',
                'min:0.01',
            ],
        ]);

        DB::transaction(function () use ($budget, $validated) {

            $lockedBudget = CostCentreBudget::whereKey($budget->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedBudget->status !== 'submitted') {
                abort(422, 'This budget is not awaiting approval.');
            }

            $stage = $lockedBudget->approval_stage;

            $permission = $this->getApprovalPermissionForStage($stage);

            abort_unless(
                $permission && auth()->user()->can($permission),
                403,
                'You are not authorised to approve this budget at this stage.'
            );

            // Only CM can provide the final approved amount.
if ($stage === 'cm') {
    $approvedAmount = $lockedBudget->requested_amount;
} else {
    $approvedAmount = null;
}

            $lockedBudget->approvals()->create([
                'approval_stage' => $stage,
                'decision' => 'approved',
                'approved_by' => auth()->id(),
                'comments' => $validated['comments'] ?? null,
                'acted_at' => now(),
            ]);

            if ($stage === 'finance') {

                $lockedBudget->update([
                    'approval_stage' => 'dcm',
                ]);

            } elseif ($stage === 'dcm') {

                $lockedBudget->update([
                    'approval_stage' => 'cm',
                ]);

            } elseif ($stage === 'cm') {

                $lockedBudget->update([
                    'status' => 'approved',
                    'approval_stage' => 'approved',
                    'approved_amount' => $approvedAmount,
                    'approved_by' => auth()->id(),
                    'approved_at' => now(),
                    'return_reason' => null,
                ]);

            } else {

                abort(422, 'Invalid budget approval stage.');
            }
        });

        return back()->with(
            'success',
            'Budget approval recorded successfully.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Send Budget Back for Adjustment
    |--------------------------------------------------------------------------
    */


public function sendBack(Request $request, CostCentreBudget $budget)
{
    $validated = $request->validate([
        'return_reason' => [
            'required',
            'string',
            'max:2000',
        ],
    ]);

    DB::transaction(function () use ($budget, $validated) {

        $lockedBudget = CostCentreBudget::whereKey($budget->id)
            ->lockForUpdate()
            ->firstOrFail();

        if ($lockedBudget->status !== 'submitted') {
            abort(422, 'This budget is not awaiting approval.');
        }

        $stage = $lockedBudget->approval_stage;

        $permission = $this->getApprovalPermissionForStage($stage);

        abort_unless(
            $permission && auth()->user()->can($permission),
            403,
            'You are not authorised to send back this budget.'
        );

        $lockedBudget->approvals()->create([
            'approval_stage' => $stage,
            'decision' => 'sent_back',
            'approved_by' => auth()->id(),
            'comments' => $validated['return_reason'],
            'acted_at' => now(),
        ]);

        $lockedBudget->update([
            'status' => 'returned',
            'approval_stage' => null,
            'return_reason' => $validated['return_reason'],
        ]);
    });

    return back()->with(
        'success',
        'Budget sent back to the requester for adjustment.'
    );
}


public function reject(Request $request, CostCentreBudget $budget)
{
    $validated = $request->validate([
        'rejection_reason' => [
            'required',
            'string',
            'max:2000',
        ],
    ]);

    DB::transaction(function () use ($budget, $validated) {

        $lockedBudget = CostCentreBudget::whereKey($budget->id)
            ->lockForUpdate()
            ->firstOrFail();

        if ($lockedBudget->status !== 'submitted') {
            abort(422, 'This budget is not awaiting approval.');
        }

        $stage = $lockedBudget->approval_stage;

        $permission = $this->getApprovalPermissionForStage($stage);

        abort_unless(
            $permission && auth()->user()->can($permission),
            403,
            'You are not authorised to reject this budget.'
        );

        $lockedBudget->approvals()->create([
            'approval_stage' => $stage,
            'decision' => 'rejected',
            'approved_by' => auth()->id(),
            'comments' => $validated['rejection_reason'],
            'rejection_reason' => $validated['rejection_reason'],
            'acted_at' => now(),
        ]);

        $lockedBudget->update([
            'status' => 'rejected',
            'approval_stage' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
        ]);
    });

    return back()->with(
        'success',
        'Monthly budget has been rejected successfully.'
    );
}



    /*
    |--------------------------------------------------------------------------
    | Approval Permission by Stage
    |--------------------------------------------------------------------------
    */

    protected function getApprovalPermissionForStage(?string $stage): ?string
    {
        return match ($stage) {
            'finance' => 'APPROVE MONTHLY BUDGETS - FINANCE',
            'dcm' => 'APPROVE MONTHLY BUDGETS - DCM',
            'cm' => 'APPROVE MONTHLY BUDGETS - CM',
            default => null,
        };
    }


public function show(CostCentreBudget $budget)
{
    $budget->load([
        'period',
        'costCentre.department',
        'creator',
        'submitter',
        'approver',
        'approvals.approver',
    ]);

    $stageLabels = [
        'finance' => 'Finance',
        'dcm' => 'Deputy Centre Manager (DCM)',
        'cm' => 'Centre Manager (CM)',
    ];

    $approvalStage = $budget->approval_stage;

    $stageLabel = $stageLabels[$approvalStage] ?? null;

    $isDraft = $budget->status === 'draft';
    $isReturned = $budget->status === 'returned';
    $isPending = $budget->status === 'submitted';
    $isApproved = $budget->status === 'approved';

    $isRequester = (int) $budget->created_by === (int) auth()->id();

    $approvalPermissions = [
        'finance' => 'APPROVE MONTHLY BUDGETS - FINANCE',
        'dcm' => 'APPROVE MONTHLY BUDGETS - DCM',
        'cm' => 'APPROVE MONTHLY BUDGETS - CM',
    ];

    $requiredPermission = $approvalPermissions[$approvalStage] ?? null;

    $canApproveCurrentStage = $isPending
        && $requiredPermission
        && auth()->user()->can($requiredPermission);

    $isPeriodClosed = $budget->period->status === 'closed';

    $lastReturn = $budget->approvals
        ->where('decision', 'sent_back')
        ->sortByDesc('acted_at')
        ->first();

    return view('admin.finance.budgets.show', compact(
        'budget',
        'stageLabels',
        'approvalStage',
        'stageLabel',
        'isDraft',
        'isReturned',
        'isPending',
        'isApproved',
        'isRequester',
        'canApproveCurrentStage',
        'isPeriodClosed',
        'lastReturn'
    ));
}


}
