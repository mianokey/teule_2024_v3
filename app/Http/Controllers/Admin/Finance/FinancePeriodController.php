<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Http\Controllers\Controller;
use App\Models\FinancePeriod;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class FinancePeriodController extends Controller
{
    public function index(Request $request)
    {
        $query = FinancePeriod::with('creator')
            ->withCount('budgets');

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('year', 'like', "%{$search}%")
                    ->orWhere('month', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $financePeriods = $query
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->paginate(15)
            ->withQueryString();

        return view('admin.finance.periods.index', compact(
            'financePeriods'
        ));
    }

    public function create()
    {
        return view('admin.finance.periods.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'year' => [
                'required',
                'integer',
                'min:2020',
                'max:2100',
            ],
            'month' => [
                'required',
                'integer',
                'between:1,12',
                Rule::unique('finance_periods')->where(function ($query) use ($request) {
                    return $query
                        ->where('year', $request->year);
                }),
            ],
            'notes' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ], [
            'month.unique' => 'A finance period for this month and year already exists.',
        ]);

        $startDate = Carbon::create(
            $validated['year'],
            $validated['month'],
            1
        )->startOfMonth();

        $endDate = $startDate->copy()->endOfMonth();

        FinancePeriod::create([
            'year' => $validated['year'],
            'month' => $validated['month'],
            'starts_on' => $startDate->toDateString(),
            'ends_on' => $endDate->toDateString(),
            'status' => 'open',
            'notes' => $validated['notes'] ?? null,
            'created_by' => auth()->id(),
        ]);

        return redirect()
            ->route('admin.finance.periods.index')
            ->with('success', 'Finance period created successfully.');
    }

    public function close(FinancePeriod $financePeriod)
    {
        if ($financePeriod->status === 'closed') {
            return back()->with(
                'error',
                'This finance period is already closed.'
            );
        }

        $financePeriod->update([
            'status' => 'closed',
            'closed_by' => auth()->id(),
            'closed_at' => now(),
        ]);

        return back()->with(
            'success',
            'Finance period closed successfully.'
        );
    }

    public function reopen(FinancePeriod $financePeriod)
    {
        if ($financePeriod->status === 'open') {
            return back()->with(
                'error',
                'This finance period is already open.'
            );
        }

        $financePeriod->update([
            'status' => 'open',
            'closed_by' => null,
            'closed_at' => null,
        ]);

        return back()->with(
            'success',
            'Finance period reopened successfully.'
        );
    }
}