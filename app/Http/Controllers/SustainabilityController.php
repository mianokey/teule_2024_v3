<?php

namespace App\Http\Controllers;

use App\Models\Sustainability;
use App\Models\Sustainability_project;
use Illuminate\Http\Request;

class SustainabilityController extends Controller
{
    public function index($id, $title)
    {
        // Use $id and $title as needed in your controller logic

        // Fetch the specific sustainability item by its id
        $sustainabilityItem = Sustainability_project::find($id);

        // Check if the item exists
        if ($sustainabilityItem) {
            // If the item exists, you can use it in your view or pass it to the view
            $reports = $sustainabilityItem->reports;
            return view('sustainability',['reports'=>$reports],['sustainabilityItem' => $sustainabilityItem]);
        } else {
            // Handle the case when the item with the provided id does not exist
            return redirect()->route('home')->with('error', 'Sustainability item not found.');
        }
    }
    public function create()
    {
        return view('sustainability.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'brief' => 'required|string',
            'description' => 'nullable|string',
            'image_links' => 'nullable|array',
        ]);

        $sustainability = Sustainability_project::create($validatedData);

        // Additional logic if needed

        return redirect()->route('sustainability.index')->with('success', 'Sustainability item created successfully.');
    }
}
