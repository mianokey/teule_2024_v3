<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::all(); // Retrieve all roles
        return view('admin.roles.index', compact('roles')); // Corrected path to the index view
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // return view('admin.roles_permissions.role.create'); // Corrected path to the create view
        return view('admin.roles.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles|max:255',
        ]);
        Role::create($request->only('name')); // Create a new role
        return redirect()->route('admin.roles.index')->with('success', 'Role created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $role = Role::findOrFail($id); // Retrieve the role by ID
        return view('admin.roles.show', compact('role')); // Corrected path to the show view
    }

    /**
     * Show the form for editing the specified resource.
     */

     public function edit($id)
     {
         $role = Role::findOrFail($id); // Find the role by ID or fail
         $permissions = Permission::all(); // Get all permissions (or filter as needed)
     
         return view('admin.roles.edit', compact('role', 'permissions')); // Pass both role and permissions to the view
     }
     

    /**
     * Update the specified resource in storage.
     */
    
    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);
    
        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id', // Ensure all IDs exist in the permissions table
        ]);
    
        // Update the role name
        $role->name = $request->name;
        $role->save();
    
        // If no permissions are selected, set it to an empty array
        $permissions = $request->permissions ?? [];
    
        if (!empty($permissions)) {
            // Fetch permission names using the IDs
            $permissionNames = Permission::whereIn('id', $permissions)->pluck('name')->toArray();
            $role->syncPermissions($permissionNames);
        } else {
            // If no permissions are selected, remove all permissions from the role
            $role->syncPermissions([]);
        }
    
        return redirect()->route('admin.roles.index')->with('success', 'Role updated successfully.');
    }
    
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $role = Role::findOrFail($id);
        $role->delete(); // Delete the role
        return redirect()->route('admin.roles.index')->with('success', 'Role deleted successfully.');
    }

    /**
     * Update Role Permissions.
     */
    public function updatePermissions(Request $request, Role $role)
    {
        $role->syncPermissions($request->permissions); // Sync permissions with the role
        return redirect()->back()->with('success', 'Permissions updated successfully.');
    }
}
