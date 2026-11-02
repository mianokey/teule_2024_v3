<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Http\Controllers\Controller;
use App\Models\CostCentre;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CostCentreController extends Controller
{
    public function index(Request $request)
    {
        $query = CostCentre::with('department');

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhereHas('department', function ($department) use ($search) {
                        $department->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $costCentres = $query
            ->orderBy('department_id')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        $departments = Department::orderBy('name')->get();

        return view('admin.finance.cost_centres.index', compact(
            'costCentres',
            'departments'
        ));
    }

public function create()
{
    $departments = Department::where('is_active', true)
        ->orderBy('name')
        ->get();

    return view('admin.finance.cost_centres.create', compact('departments'));
}

public function store(Request $request)
{
    $validated = $request->validate([
        'department_id' => [
            'required',
            'exists:departments,id',
        ],
        'name' => [
            'required',
            'string',
            'max:255',
        ],
        'code' => [
            'required',
            'string',
            'max:50',
        ],
        'description' => [
            'nullable',
            'string',
            'max:2000',
        ],
        'is_active' => [
            'nullable',
            'boolean',
        ],
    ]);

    $duplicateName = CostCentre::where('department_id', $validated['department_id'])
        ->where('name', $validated['name'])
        ->exists();

    $duplicateCode = CostCentre::where('department_id', $validated['department_id'])
        ->where('code', $validated['code'])
        ->exists();

    $errors = [];

    if ($duplicateName) {
        $errors['name'] = 'This cost centre name already exists in the selected department.';
    }

    if ($duplicateCode) {
        $errors['code'] = 'This cost centre code already exists in the selected department.';
    }

    if (!empty($errors)) {
        return back()
            ->withInput()
            ->withErrors($errors);
    }

    $validated['is_active'] = $request->boolean('is_active');

    CostCentre::create($validated);

    return redirect()
        ->route('admin.finance.cost-centres.index')
        ->with('success', 'Cost centre created successfully.');
}

public function edit(CostCentre $costCentre)
{
    $departments = Department::orderBy('name')->get();

    return view('admin.finance.cost_centres.edit', compact(
        'costCentre',
        'departments'
    ));
}

public function update(Request $request, CostCentre $costCentre)
{
    $validated = $request->validate([
        'department_id' => [
            'required',
            'exists:departments,id',
        ],
        'name' => [
            'required',
            'string',
            'max:255',
        ],
        'code' => [
            'required',
            'string',
            'max:50',
        ],
        'description' => [
            'nullable',
            'string',
            'max:2000',
        ],
        'is_active' => [
            'nullable',
            'boolean',
        ],
    ]);

    $duplicateName = CostCentre::where('department_id', $validated['department_id'])
        ->where('name', $validated['name'])
        ->where('id', '!=', $costCentre->id)
        ->exists();

    $duplicateCode = CostCentre::where('department_id', $validated['department_id'])
        ->where('code', $validated['code'])
        ->where('id', '!=', $costCentre->id)
        ->exists();

    $errors = [];

    if ($duplicateName) {
        $errors['name'] = 'This cost centre name already exists in the selected department.';
    }

    if ($duplicateCode) {
        $errors['code'] = 'This cost centre code already exists in the selected department.';
    }

    if (!empty($errors)) {
        return back()
            ->withInput()
            ->withErrors($errors);
    }

    $validated['is_active'] = $request->boolean('is_active');

    $costCentre->update($validated);

    return redirect()
        ->route('admin.finance.cost-centres.index')
        ->with('success', 'Cost centre updated successfully.');
}

public function toggleStatus(CostCentre $costCentre)
{
    $costCentre->update([
        'is_active' => !$costCentre->is_active,
    ]);

    return back()->with(
        'success',
        $costCentre->is_active
            ? 'Cost centre activated successfully.'
            : 'Cost centre deactivated successfully.'
    );
}

}
