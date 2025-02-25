<?php

namespace App\Http\Controllers;


use App\Models\Child;
use App\Models\Event;
use App\Models\Latest;
use App\Models\NeedItem;
use App\Models\SystemDetail;
use App\Models\User;
use App\Models\UserDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class HomeController extends Controller
{

    public function index()
    {
      $systemDetails = SystemDetail::all();
      $needItems = NeedItem::all();
      $events = Event::all()->sortBy('date_from');
      return view('index', compact('systemDetails', 'needItems', 'events'));
    }



    public function donate()
    {
        return view('donate');
    }
    public function history()
    {
        return view('history');
    }
    public function founders()
    {
        return view('founders');
    }

    public function program()
    {
        return view('program');
    }

    public function showErrorPage($errorMessage = '')
    {
        return view('error', ['message' => $errorMessage]);
    }

    public function readmore($documentName)
    {
        // Get the path to the PDF file
        $filePath = 'uploads/' . $documentName;

        // Check if the file exists in storage
        if (Storage::disk('public')->exists($filePath)) {
            // Construct the URL to the PDF file
            $pdfUrl = asset($filePath);

            // Render the view with the PDF URL and document name
            return view('readmore', compact('pdfUrl', 'documentName'));
        } else {
            // If the file doesn't exist, return a 404 error
            $pdfUrl = '';
            return view('readmore', compact('pdfUrl', 'documentName'));
        }
    }

    public function board()
    {
        // Fetch users based on position from user_details table
        $members = User::whereHas('details', function ($query) {
            $query->where('key', 'position')
                ->where(function ($subQuery) {
                    $subQuery->where('value', 'like', '%board%')
                        ->orWhere('value', 'like', '%director%')
                        ->orWhere('value', 'like', '%founder%');
                });
        })
            ->whereNull('email_verified_at') // Filter users with unverified email
            ->get();

        // Check if there are members
        if ($members->isEmpty()) {
            // No members found, return with a message or redirect
            $errorMessage = 'Unable to fetch board member details';
            return view('error', compact('errorMessage'));
        }

        // Process the retrieved data
        $processedMembers = [];
        foreach ($members as $member) {
            $userData = $member->toArray();

            // Extract user details into a key-value array
            $userDetails = $member->details->pluck('value', 'key')->toArray();

            // Append user details to user data
            foreach ($userDetails as $key => $value) {
                $userData[$key] = $value;
            }

            // Add user data to processed members array
            $processedMembers[] = $userData;
        }

        return view('board', compact('processedMembers'));
    }

    public function team()
    {
        // Fetch users who do not have positions containing the specified strings
        $members = User::whereDoesntHave('details', function ($query) {
            $query->where('key', 'position')
                ->where(function ($subQuery) {
                    $subQuery->where('value', 'like', '%board%')
                        ->orWhere('value', 'like', '%director%')
                        ->orWhere('value', 'like', '%founder%');
                });
        })
            ->whereNull('email_verified_at') // Filter users with unverified email
            ->get();

        // Check if there are members
        if ($members->isEmpty()) {
            // No members found, return with a message or redirect
            $errorMessage = 'Unable to fetch team member details';
            return view('error', compact('errorMessage'));
        }

        // Process the retrieved data
        $processedMembers = [];
        foreach ($members as $member) {
            $userData = $member->toArray();

            // Extract user details into a key-value array
            $userDetails = $member->details->pluck('value', 'key')->toArray();

            // Append user details to user data
            foreach ($userDetails as $key => $value) {
                $userData[$key] = $value;
            }

            // Add user data to processed members array
            $processedMembers[] = $userData;
        }

        return view('team', compact('processedMembers'));
    }

    public function sustainability()
    {

        return view('sustainability');
    }


    public function contact()
    {
        return view('contact');
    }

    public function careers()
    {
        return view('careers');
    }

    public function contact_message(Request $request)
    {
        // Validate the form inputs
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone_number' => 'required|string|max:20',
            'msg_subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $subject = $request->input('msg_subject');
        $toRecipients = ['rmo@teulekenya.org'];
        $ccRecipients = ['rmo@teulekenya.org'];
        $notificationContent = '<h4>
            Greetings,</br>
            We have received a new notification(via website) from:</br>
            Name: ' . $request->input('name') . ' || </br>
            Contact details: ' . $request->input('phone_number') . ' || ' . $request->input('email') . '</br> with the following message:
            <blockquote>' . $request->input('message') . '</blockquote>
        </h4>';

        $emailController = new EmailController();

        // Call the non-static method on the instance and pass the $request object
        $response = $emailController->sendCustomNotification($request, $notificationContent, $subject, $toRecipients, $ccRecipients);

        $res =  $response = ['type' => $response['type'], 'message' => $response['message'], 'redirect_url' => route('contact_message')];

        if ($response['type'] === 'success') {
            session()->flash('success', $response['message']);
        } elseif ($response['type'] === 'error') {
            session()->flash('error', $response['message']);
        }

        // Return the response as JSON
        return response()->json($res);
    }

    public function sponsorship()
    {
        try {
            // Load children with their sponsor count and all details, ordered by sponsor count
            $children = Child::whereNotIn('status', ['Inactive'])
                ->withCount(['details as sponsor_count' => function ($query) {
                    $query->where('key', 'sponsor');
                }])
                ->orderByDesc('sponsor_count')
                ->paginate(8);

            // Calculate age for each child
            foreach ($children as $child) {
                $dob = Carbon::parse($child->dob);
                $now = Carbon::now();
                // Calculate difference
                $ageDiff = $now->diff($dob);
                // Format age based on the difference
                if ($ageDiff->y > 1) {
                    $age = $ageDiff->format('%y years old');
                } elseif ($ageDiff->y == 1) {
                    $age = $ageDiff->format('%y year old');
                } elseif ($ageDiff->m > 0) {
                    $age = $ageDiff->format('%m months old');
                } else {
                    $age = $ageDiff->format('%d days old');
                }
                $child->age = $age;
            }

            return view('sponsorship', compact('children'));
        } catch (\Exception $e) {
            // Handle the exception here
            $errorMessage = 'Error retrieving data.';
            return view('error', compact('errorMessage'));
        }
    }


    public function sponsorship_card($encodedId)
    {
        try {
            // Decode the encoded ID
            $decodedString = base64_decode($encodedId);

            // Define the delimiter used during encoding
            $delimiter = '|';

            // Extract the child ID from the decoded string
            $idParts = explode($delimiter, $decodedString);
            $childId = $idParts[0]; // The first part before the delimiter is the child ID

            // Retrieve the child using the decoded ID
            $child = Child::find($childId);

            if ($child) {
                // Calculate age
                $dob = Carbon::parse($child->dob);
                $now = Carbon::now();
                $ageDiff = $now->diff($dob);

                if ($ageDiff->y > 1) {
                    $age = $ageDiff->format('%y years old');
                } elseif ($ageDiff->y == 1) {
                    $age = $ageDiff->format('%y year old');
                } elseif ($ageDiff->m > 0) {
                    $age = $ageDiff->format('%m months old');
                } else {
                    $age = $ageDiff->format('%d days old');
                }
                $child->age = $age;

                // Display the sponsorship card view
                return view('child_profile', compact('child'));
            } else {
                // If the child does not exist, redirect with an error message
                $errorMessage = 'Child not found.';
                return view('error', compact('errorMessage'));
            }
        } catch (\Exception $e) {
            // Handle any exceptions
            $errorMessage = 'Error retrieving child.';
            return view('error', compact('errorMessage'));
        }
    }




    public function apply_intern()
    {
        return view('apply_intern');
    }
    public function volunteer()
    {
        return view('volunteer');
    }
    public function edu_fund()
    {
        return view('edu_fund');
    }

    public function tla()
    {
        return view('tla');
    }


    public function gallery($selectedYear = null)
    {
        // Folder path with mixed slashes
        $folderPath = public_path('assets/img/gallery');

        // Replace any backward slashes with forward slashes
        $folderPath = str_replace('\\', '/', $folderPath);

        // Get all year folders in the gallery directory
        $yearFolders = File::directories($folderPath);

        // Sort the year folders in descending order (latest year first)
        rsort($yearFolders);

        // If no specific year is selected, use the latest year folder
        if ($selectedYear === null) {
            $selectedYearFolder = reset($yearFolders);
            $selectedYear = basename($selectedYearFolder);
        } else {
            // Check if the selected year exists in the year folders
            $selectedYearFolder = null;
            foreach ($yearFolders as $yearFolder) {
                if (basename($yearFolder) == $selectedYear) {
                    $selectedYearFolder = $yearFolder;
                    break;
                }
            }

            // If the selected year doesn't exist, you can handle it as per your requirements (e.g., show an error, redirect, etc.)
            if ($selectedYearFolder === null) {
                // Handle the case when the selected year doesn't exist
                // For example, you can redirect back to the default page or show an error message
                return redirect()->route('gallery');
            }
        }

        // Define the number of images per page
        $imagesPerPage = 6; // Change this to 2 for 2 images per page

        // Get images from the selected year folder or the latest year folder if none is selected
        $imageFiles = glob($selectedYearFolder . '/*.*');

        // Manually paginate the images from the selected year folder using LengthAwarePaginator
        $page = request()->query('page', 1);
        $offset = ($page - 1) * $imagesPerPage;
        $currentPageItems = array_slice($imageFiles, $offset, $imagesPerPage);

        // Extract the selected year and image name for each image
        $imagePaths = [];
        foreach ($currentPageItems as $image) {
            $imageName = basename($image);
            $imagePath = 'assets/img/gallery/' . $selectedYear . '/' . $imageName;
            $imagePaths[] = $imagePath;
        }

        $paginatedImageFiles = new LengthAwarePaginator(
            $imagePaths,
            count($imageFiles),
            $imagesPerPage,
            $page,
            ['path' => url('gallery/' . basename($selectedYearFolder))]
        );

        // Prepare links to all available year folders
        $yearLinks = [];
        foreach ($yearFolders as $yearFolder) {
            $year = basename($yearFolder);
            $yearLinks[$year] = url('gallery/' . $year);
        }

        // Pass the correct variables to the view
        return view('gallery', compact('paginatedImageFiles', 'yearLinks', 'selectedYear'));
    }



    public function latest()
    {
        $latests = Latest::paginate(2);
        return view('latest', compact('latests'));
    }
    public function scholarship()
    {
        return view('scholarship');
    }
    public function apply_intern_post(Request $request)
    {
        // Validate the form inputs
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone_number' => 'required|string|max:20',
            'attachments' => 'array', // Make sure 'attachments' is an array
        ]);

        $subject = 'INTERNSHIP/ATTACHMENT APPLICATION';
        $message = 'Kindly find the attached documents.';
        $toRecipients = ['rmo@teulekenya.org'];
        $ccRecipients = ['rmo@teulekenya.org'];
        $notificationContent = '<h4>
        Greetings,</br>
        We have received a new application(via website) from:</br>
        Name: ' . $request->input('name') . ' || </br>
        Contact details: ' . $request->input('phone_number') . ' || ' . $request->input('email') . '</br> with the following message:
        <blockquote>' . $message . '</blockquote>
        </h4>';

        // Convert the 'attachments' input to an array (if it exists)
        $attachments = $request->has('attachments') ? $request->file('attachments') : [];

        $attachmentPaths = [];
        if ($attachments) {
            foreach ($attachments as $attachment) {
                $fileName = uniqid('attachment_', true) . '.' . $attachment->getClientOriginalExtension();

                // Move the uploaded file to the permanent location (e.g., public/uploads)
                $attachment->move(public_path('uploads'), $fileName);

                // Get the real path of the uploaded file
                $attachmentPaths[] = public_path('uploads/' . $fileName);
            }
        }



        $emailController = new EmailController();

        // Call the non-static method on the instance and pass the $request object and attachments array
        $response = $emailController->sendCustomNotification($request, $notificationContent, $subject, $toRecipients, $ccRecipients, $attachmentPaths);

        $res = ['type' => $response['type'], 'message' => $response['message'], 'redirect_url' => route('apply_intern')];

        if ($response['type'] === 'success') {
            session()->flash('success', $response['message']);
        } elseif ($response['type'] === 'error') {
            session()->flash('error', $response['message']);
        }

        // Return the response as JSON
        return response()->json($res);
    }

    public function aboutus_home()
    {
        return view('aboutus_home');
    }
}
