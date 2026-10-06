<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Models\ChildDetail;
use App\Models\SystemDetail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;



class AdminController extends Controller
{
    public function index()
    {
        return view('admin.index');
    }
    public function newchild_index()
    {
        return view('admin.newchild.index');
    }

 public function newchild_store(Request $request)
{
    // Validate the incoming request data
    $request->validate([
        'name' => 'required|string|max:255',
        'dob' => 'required|date',
        'status' => 'required|string|max:255',
        'image' => 'required|image|mimes:jpeg,jpg,png|max:2048',
        'hobbies' => 'required|string|max:255',
        'current_grade' => 'required|string|max:255',
        'aspirations' => 'required|string|max:255',
        'sponsors' => 'required|int|max:11',
    ]);

    // Handle file upload directly to public/uploads
    if ($request->hasFile('image') && $request->file('image')->isValid()) {
        $file = $request->file('image');
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('uploads'), $filename);
        $imagePath = 'uploads/' . $filename; // Store relative path for DB

        try {
            // Create a new child record
            $child = Child::create([
                'name' => $request->input('name'),
                'dob' => $request->input('dob'),
                'status' => $request->input('status'),
                'img_url' => $imagePath,
            ]);

            // Create child details
            $child->details()->createMany([
                ['key' => 'hobbies', 'value' => $request->input('hobbies')],
                ['key' => 'current_grade', 'value' => $request->input('current_grade')],
                ['key' => 'aspirations', 'value' => $request->input('aspirations')],
                ['key' => 'sponsors', 'value' => $request->input('sponsors')],
            ]);

            return redirect()->back()->with('success', 'Child record created successfully!');
        } catch (\Exception $e) {
            // Delete uploaded file if DB fails
            if (file_exists(public_path($imagePath))) {
                unlink(public_path($imagePath));
            }

            \Log::error('Failed storing child record: ' . $e->getMessage());

            return redirect()->back()
                ->withInput($request->except('image'))
                ->with('error', 'Failed to create child record: ' . $e->getMessage());
        }
    } else {
        return redirect()->back()
            ->withInput($request->except('image'))
            ->with('error', 'Failed to upload image. Please try again.');
    }
}


    public function children_list()
    {
        // Retrieve a list of children with their details
        $children = Child::with('details')->get();


        // Generate a unique token for each child
        foreach ($children as $child) {
            $delimiter = '|';
            $randomString = Str::random(10); // This part is optional if you want additional security
            $encodedId = base64_encode($child->id . $delimiter . $randomString);
            $child->encoded_id = $encodedId; // Assign dynamically
        }

        // Pass the data to a view
        return view('admin.children.index', compact('children'));
    }
public function spons_card()
{
    // Retrieve children with their details
    $children = Child::with('details')->get();

    // Generate a unique token for each child
    foreach ($children as $child) {
        $delimiter = '|';
        $randomString = Str::random(10);
        $encodedId = base64_encode($child->id . $delimiter . $randomString);

        $child->encoded_id = $encodedId;
    }

    /*
    |--------------------------------------------------------------------------
    | Automatically discover card templates
    |--------------------------------------------------------------------------
    */

    $templatePath = resource_path(
        'views/admin/children/sponscard/templates'
    );

    $templateFiles = File::files($templatePath);

    $templates = collect($templateFiles)
        ->filter(function ($file) {
            return str_ends_with($file->getFilename(), '.blade.php');
        })
        ->mapWithKeys(function ($file) {

            // general.blade.php → general
            $key = preg_replace(
                '/\.blade\.php$/',
                '',
                $file->getFilename()
            );

            // general → General
            $name = ucwords(
                str_replace(
                    ['_', '-'],
                    ' ',
                    $key
                )
            );

            return [
                $key => $name
            ];
        })
        ->toArray();

    return view(
        'admin.children.sponscard.index',
        compact('children', 'templates')
    );
}

    public function child_edit($id)
    {
        $child = Child::findOrFail($id);
        return view('admin.children.edit', compact('child'));
    }

    public function child_delete($id)
    {
        try {
            $child = Child::findOrFail($id);

            // Delete the child's image from storage if it exists
            if (Storage::disk('public')->exists($child->img_url)) {
                Storage::disk('public')->delete($child->img_url);
            }

            // Delete the child and associated details
            $child->details()->delete();
            $child->delete();

            return redirect()->route('children.index')->with('success', 'Child deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->route('children.index')->with('error', 'Failed to delete child.');
        }
    }

public function child_update(Request $request, $id)
{
    // Validate request
    $request->validate([
        'name' => 'required|string|max:255',
        'dob' => 'required|date',
        'status' => 'required|string|max:255',
        'image' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        'hobbies' => 'required|string|max:255',
        'current_grade' => 'required|string|max:255',
        'aspirations' => 'required|string|max:255',
        'sponsors' => 'required|integer|min:0',
        'detail_keys.*' => 'nullable|string|max:255',
        'detail_values.*' => 'nullable|string|max:1000',
    ]);

    $child = Child::findOrFail($id);

    /*
    |--------------------------------------------------------------------------
    | IMAGE UPDATE
    |--------------------------------------------------------------------------
    */
    if ($request->hasFile('image') && $request->file('image')->isValid()) {

        // Delete old image if exists
        if ($child->img_url && file_exists(public_path($child->img_url))) {
            unlink(public_path($child->img_url));
        }

        // Move new image to public/uploads
        $file = $request->file('image');
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('uploads'), $filename);

        $child->img_url = 'uploads/' . $filename;
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE BASIC INFO
    |--------------------------------------------------------------------------
    */
    $child->update([
        'name' => $request->name,
        'dob' => $request->dob,
        'status' => $request->status,
    ]);

    /*
    |--------------------------------------------------------------------------
    | PROTECTED DETAILS (Fixed Fields)
    |--------------------------------------------------------------------------
    */
    $protectedDetails = [
        'hobbies' => $request->hobbies,
        'current_grade' => $request->current_grade,
        'aspirations' => $request->aspirations,
        'sponsors' => $request->sponsors,
    ];

    foreach ($protectedDetails as $key => $value) {
        $child->details()->updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | DYNAMIC ADDITIONAL DETAILS
    |--------------------------------------------------------------------------
    */
    $protectedKeys = array_keys($protectedDetails);

    // Remove old dynamic details
    $child->details()
        ->whereNotIn('key', $protectedKeys)
        ->delete();

    // Save new dynamic details
    if ($request->detail_keys) {
        foreach ($request->detail_keys as $index => $key) {
            $value = $request->detail_values[$index] ?? null;
            if (!empty($key) && !empty($value) && !in_array($key, $protectedKeys)) {
                $child->details()->create([
                    'key' => $key,
                    'value' => $value,
                ]);
            }
        }
    }

    return redirect()->back()->with('success', 'Child record updated successfully!');
}
public function saveBio(Request $request)
{
    $request->validate([
        'child_id' => 'required',
        'value' => 'required'
    ]);

    ChildDetail::updateOrCreate(
        [
            'child_id' => $request->child_id,
            'key' => 'child_bio'
        ],
        [
            'value' => $request->value
        ]
    );

    return response()->json(['success' => true]);
}

public function child_card_select()
{
    $children = Child::orderBy('name')->get();
    return view('children.cards-select', compact('children'));
}



    public function showSystemDetails()
    {
        // Fetch all system details
        $systemDetails = SystemDetail::all();

        // Pass the system details to the view
        return view('admin.system_details.index', compact('systemDetails'));
    }

    public function addSystemDetails()
    {
        return view('admin.system_details.add');
    }
    public function storeSystemDetails(Request $request)
    {  // Validate the incoming request data
        $request->validate([
            'key' => 'required|string|max:255',
            'value' => 'required|string',
        ]);

        // Create a new system detail record
        SystemDetail::create([
            'key' => $request->input('key'),
            'value' => $request->input('value'),
        ]);

        // Redirect back or return a response as needed
        return redirect()->back()->with('success', 'System detail created successfully!');
    }

    public function editSystemDetails($id)
    {
        // Find the system detail by id
        $systemDetail = SystemDetail::find($id);

        // Check if the system detail exists
        if (!$systemDetail) {
            return redirect()->back()->with('error', 'System detail not found.');
        }

        // Pass the $systemDetail variable to the view
        return view('admin.system_details.edit', compact('systemDetail'));
    }
    public function deleteSystemDetails($id)
    {
        // Find the system detail by id
        $systemDetail = SystemDetail::find($id);

        // If the system detail exists, delete it
        if ($systemDetail) {
            $systemDetail->delete();
            return redirect()->back()->with('success', 'System detail deleted successfully.');
        }

        // If the system detail does not exist, return an error message
        return redirect()->back()->with('error', 'System detail not found.');
    }

    public function updateSystemDetails(Request $request, $id)
    {
        // Validate the incoming request data
        $request->validate([
            'key' => 'required|string|max:255',
            'value' => 'required|string',
        ]);

        // Find the system detail by id
        $systemDetail = SystemDetail::find($id);

        // Check if the system detail exists
        if (!$systemDetail) {
            return redirect()->back()->with('error', 'System detail not found.');
        }

        // Update the system detail with the new data
        $systemDetail->update([
            'key' => $request->input('key'),
            'value' => $request->input('value'),
        ]);

        // Redirect back or return a response as needed
        return redirect()->back()->with('success', 'System detail updated successfully!');
    }



public function user_index()
{
    $users = User::with('details')
        ->orderBy('name')
        ->get();

    return view('admin.user.index', compact('users'));
}


    public function user_edit(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $details = $user->details ? $user->details->pluck('value', 'key')->toArray() : [];
        $roles = Role::all();
        $userRole = $user->roles->first(); // Get the user's current role

        if ($request->isMethod('post')) {
            // Validate form input
            $request->validate([
                'role' => 'required|exists:roles,name',
            ]);

            $newRole = $request->input('role');

            // Check if the role has changed
            if (!$userRole || $userRole->name !== $newRole) {
                $user->syncRoles([$newRole]); // Remove previous roles and assign the new one
                return redirect()->back()->with('success', 'User role updated successfully.');
            }

            return redirect()->back()->with('info', 'No changes made to the user role.');
        }

        return view('admin.user.edit', compact('user', 'details', 'roles', 'userRole'));
    }



    public function user_create()
    {
        return view('admin.user.create');
    }


public function user_update(Request $request, $id)
{
    $user = User::findOrFail($id);

    $emailRule = [
        'required',
        'string',
        'email',
        'max:255',
    ];

    if ($request->input('email') !== $user->email) {
        $emailRule[] = Rule::unique('users')->ignore($user->id);
    }

    $request->validate([
        'name' => 'required|string|max:255',
        'email' => $emailRule,

        'current_password' => [
            'nullable',
            function ($attribute, $value, $fail) use ($user) {
                if (!Hash::check($value, $user->password)) {
                    $fail('The current password is incorrect.');
                }
            },
        ],

        'password' => 'nullable|string|min:8|confirmed',

        'position' => 'required|string|max:255',

        'role' => 'nullable|exists:roles,name',

        'image' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
    ]);

    try {

        /*
        |--------------------------------------------------------------------------
        | 1. Update basic user information
        |--------------------------------------------------------------------------
        */

        $user->update([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => $request->filled('password')
                ? Hash::make($request->input('password'))
                : $user->password,
        ]);


        /*
        |--------------------------------------------------------------------------
        | 2. Update role
        |--------------------------------------------------------------------------
        */

        if ($request->filled('role')) {
            $user->syncRoles([
                $request->input('role')
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | 3. Handle image
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('image')) {

            $file = $request->file('image');

            $allowedExtensions = [
                'jpg',
                'jpeg',
                'png'
            ];

            $ext = strtolower(
                $file->getClientOriginalExtension()
            );

            if (!in_array($ext, $allowedExtensions)) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with(
                        'error',
                        'Invalid image type. Allowed: jpg, jpeg, png.'
                    );
            }

            $maxSize = 2 * 1024 * 1024;

            if ($file->getSize() > $maxSize) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with(
                        'error',
                        'Image size exceeds 2 MB.'
                    );
            }

            $filename = uniqid() . '.' . $ext;

            $file->move(
                public_path('uploads'),
                $filename
            );

            $user->details()->updateOrCreate(
                ['key' => 'img_url'],
                ['value' => 'uploads/' . $filename]
            );
        }


        /*
        |--------------------------------------------------------------------------
        | 4. Update position
        |--------------------------------------------------------------------------
        */

        $user->details()->updateOrCreate(
            ['key' => 'position'],
            ['value' => $request->input('position')]
        );


        /*
        |--------------------------------------------------------------------------
        | 5. Finish
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->back()
            ->with(
                'success',
                'User updated successfully!'
            );

    } catch (\Exception $e) {

        Log::error(
            'User update failed: ' . $e->getMessage(),
            [
                'user_id' => $id,
                'trace' => $e->getTraceAsString(),
            ]
        );

        return redirect()
            ->back()
            ->withInput()
            ->with(
                'error',
                'Failed to update user: ' . $e->getMessage()
            );
    }
}

    public function user_delete($id)
    {
        try {
            // Find the user by ID
            $user = User::findOrFail($id);

            // Delete the associated details
            $user->details()->delete();

            // Delete the user
            $user->delete();

            // Redirect to an appropriate page after successful deletion
            return redirect()->back()->with('success', 'User and associated details deleted successfully!');
        } catch (\Exception $e) {
            // If an error occurs during deletion, redirect back with error message
            return redirect()->back()->with('error', 'Failed to delete user: ' . $e->getMessage());
        }
    }


public function updateRoles(Request $request, $userId)
{
    $request->validate([
        'role' => 'required|exists:roles,name',
        'permissions' => 'nullable|array',
        'permissions.*' => 'exists:permissions,id',
    ]);

    try {
        $user = User::findOrFail($userId);

        /*
        |--------------------------------------------------------------------------
        | Update user's role
        |--------------------------------------------------------------------------
        */
        $user->syncRoles([
            $request->input('role')
        ]);

        /*
        |--------------------------------------------------------------------------
        | Update user's DIRECT permissions
        |--------------------------------------------------------------------------
        |
        | This does NOT modify the permissions belonging to the role.
        | It only modifies permissions directly assigned to this user.
        |
        */
        $permissionIds = $request->input('permissions', []);

        $permissions = Permission::whereIn('id', $permissionIds)->get();

        $user->syncPermissions($permissions);

        /*
        |--------------------------------------------------------------------------
        | Clear permission cache
        |--------------------------------------------------------------------------
        */
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()
            ->back()
            ->with(
                'success',
                'User role and direct permissions updated successfully.'
            );

    } catch (\Exception $e) {

        \Log::error(
            'User role and permission update failed: ' . $e->getMessage(),
            [
                'user_id' => $userId,
                'trace' => $e->getTraceAsString(),
            ]
        );

        return redirect()
            ->back()
            ->withInput()
            ->with(
                'error',
                'Failed to update role and permissions: ' . $e->getMessage()
            );
    }
}

}
