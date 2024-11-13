<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Models\SystemDetail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

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

        // Check if file upload was successful
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            // Upload the image file
            $imagePath = $request->file('image')->store('uploads', 'public');

            // Check if the file exists in the storage
            if (Storage::disk('public')->exists($imagePath)) {
                try {

                    
                    // Create a new child record
                    $child = Child::create([
                        'name' => $request->input('name'),
                        'dob' => $request->input('dob'),
                        'status' => $request->input('status'),
                        'img_url' => $imagePath,
                    ]);

                    // Create a new child_details record with key-value pairs
                    $child->details()->createMany([
                        ['key' => 'hobbies', 'value' => $request->input('hobbies')],
                        ['key' => 'current_grade', 'value' => $request->input('current_grade')],
                        ['key' => 'aspirations', 'value' => $request->input('aspirations')],
                        ['key' => 'sponsors', 'value' => $request->input('sponsors')],
                    ]);

                    // Redirect back to a success page or somewhere else
                    return redirect()->back()->with('success', 'Child record created successfully!');
                } catch (\Exception $e) {
                    // If an error occurs, delete the uploaded image
                    Storage::disk('public')->delete($imagePath);
                    // Log the processed members data
                    \Log::info('Failed storing image'. $e->getMessage() );
                    // Redirect back with error message
                    return redirect()->back()->withInput($request->except('image'))->with('error', 'Failed to create child record: ' . $e->getMessage());

                }
            } else {
                // If the file doesn't exist in storage, redirect back with error message
                // Redirect back with error message
                  return redirect()->back()->withInput($request->except('image'))
                ->with('error', 'Failed to upload image. Please try again.');

            }
        } else {
            // If file upload failed, redirect back with error message
            // Redirect back with error message
            return redirect()->back()->withInput($request->except('image'))
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
        // Validate the incoming request data
        $request->validate([
            'name' => 'required|string|max:255',
            'dob' => 'required|date',
            'status' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
            'hobbies' => 'required|string|max:255',
            'current_grade' => 'required|string|max:255',
            'aspirations' => 'required|string|max:255',
            'sponsors' => 'required|int|max:255',
        ]);

        // Find the child record to update
        $child = Child::findOrFail($id);

        // Check if a new image was uploaded
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            // Delete the previous image if it exists
            if ($child->img_url && Storage::disk('public')->exists($child->img_url)) {
                Storage::disk('public')->delete($child->img_url);
            }

            // Upload the new image
            $imagePath = $request->file('image')->store('uploads', 'public');
            $child->img_url = $imagePath;
        }

        // Update the child record
        $child->name = $request->input('name');
        $child->dob = $request->input('dob');
        $child->status = $request->input('status');
        $child->save();

        // Update the child details dynamically
        $detailsToUpdate = [
            'hobbies' => $request->input('hobbies'),
            'current_grade' => $request->input('current_grade'),
            'aspirations' => $request->input('aspirations'),
            'sponsors' => $request->input('sponsors'),
            // Add more details here as needed
        ];

        foreach ($detailsToUpdate as $key => $value) {
            $child->details()->updateOrCreate(['key' => $key], ['value' => $value]);
        }

        // Redirect back to a success page or somewhere else
        return redirect()->back()->with('success', 'Child record updated successfully!');
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
        // Get users with their details
        $users = User::with('details')->get();

        return view('admin.user.index', compact('users'));
    }
    public function user_edit($id)
    {
        $user = User::findOrFail($id);
        $details = $user->details->pluck('value', 'key')->toArray();
        return view('admin.user.edit', compact('user', 'details'));
    }



    public function user_create()
    {
        return view('admin.user.create');
    }


    public function user_store(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'position' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        try {
            // Create a new user record
            $user = User::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'password' => Hash::make($request->input('password')),
            ]);

            // Check if a new image was uploaded
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                // Upload the new image
                $imagePath = $request->file('image')->store('uploads', 'public');

                // Create or update the user details with the image URL
                $user->details()->updateOrCreate(['key' => 'img_url'], ['value' => $imagePath]);
            }

            // Create or update other user details (e.g., position)
            $user->details()->updateOrCreate(['key' => 'position'], ['value' => $request->input('position')]);

            // Redirect to an appropriate page after successful registration
            return redirect()->back()->with('success', 'User registered successfully!');
        } catch (\Exception $e) {
            // If an error occurs during user creation, redirect back with error message
            return redirect()->back()->withInput()->with('error', 'Failed to register user: ' . $e->getMessage());
        }
    }


    public function user_update(Request $request, $id)
    {
        // Find the user by ID
        $user = User::findOrFail($id);
    
        // Determine the unique rule for the email field
        $emailRule = ['required', 'string', 'email', 'max:255'];
    
        // Check if the email has changed and if it's unique
        if ($request->input('email') !== $user->email) {
            // Add a rule to ensure uniqueness, except for the current user's email
            $emailRule[] = Rule::unique('users')->ignore($user->id);
        }
    
        // Validate the incoming request data
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => $emailRule,
            'current_password' => [
                'nullable',
                function ($attribute, $value, $fail) use ($request, $user) {
                    if (!Hash::check($value, $user->password)) {
                        $fail('The current password is incorrect.');
                    }
                },
            ],
            'password' => 'nullable|string|min:8|confirmed',
            'position' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ]);
    
        try {
            // Update the user record with the provided data
            $user->update([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'password' => $request->filled('password') ? Hash::make($request->input('password')) : $user->password,
            ]);
    
            // Check if a new image was uploaded
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                // Upload the new image
                $imagePath = $request->file('image')->store('uploads', 'public');
    
                // Create or update the user details with the image URL
                $user->details()->updateOrCreate(['key' => 'img_url'], ['value' => $imagePath]);
            }
    
            // Create or update other user details (e.g., position)
            $user->details()->updateOrCreate(['key' => 'position'], ['value' => $request->input('position')]);
    
            // Redirect to an appropriate page after successful update
            return redirect()->back()->with('success', 'User updated successfully!');
        } catch (\Exception $e) {
            // If an error occurs during user update, redirect back with error message
            return redirect()->back()->withInput()->with('error', 'Failed to update user: ' . $e->getMessage());
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

}
