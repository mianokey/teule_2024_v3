<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{

    // public function __construct()
    // {
    //     $this->middleware('can:manage permissions'); // This checks if the user has the 'manage permissions' permission.
    // }


    // Display a listing of the permissions
    public function index()
    {
        $permissions = Permission::all();
        return view('admin.permissions.index', compact('permissions'));
    }

    // Show the form for creating a new permission
    public function create()
    {
        return view('admin.permissions.create');
    }

    // Store a newly created permission
    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|unique:permissions']);
        Permission::create($request->only('name'));

        return redirect()->route('admin.permissions.index')->with('success', 'Permission created successfully.');
    }

    // Show the form for editing the specified permission
    public function edit(Permission $permission)
    {
        return view('admin.permissions.edit', compact('permission'));
    }

    // Update the specified permission
    public function update(Request $request, Permission $permission)
    {
        $request->validate(['name' => 'required|string|unique:permissions,name,' . $permission->id]);
        $permission->update($request->only('name'));

        return redirect()->route('admin.permissions.index')->with('success', 'Permission updated successfully.');
    }

    // Remove the specified permission
    public function destroy(Permission $permission)
    {
        $permission->delete();

        return redirect()->route('admin.permissions.index')->with('success', 'Permission deleted successfully.');
    }
}
