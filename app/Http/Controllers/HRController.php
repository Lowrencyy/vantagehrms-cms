<?php

namespace App\Http\Controllers;

use App\Models\Objective;
use Illuminate\Http\Request;

class ObjectiveController extends Controller
{
    // Display the list of objectives
    public function index()
    {
        $objectives = Objective::all(); // Retrieve all objectives from the database
        return view('CMS.objective', compact('objectives')); // Pass data to the view
    }

    // Display the form to create a new objective
    public function create()
    {
        return view('CMS.objective_create'); // Return the view for the add new objective form
    }

    // Store a new objective in the database
    public function store(Request $request)
    {
        // Validate incoming data
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Handle file upload for the image
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('public/objectives');
        }

        // Create and save the new objective
        Objective::create([
            'title' => $request->title,
            'description' => $request->description,
            'image' => $imagePath,
        ]);

        // Redirect back to the objectives list page with a success message
        return redirect()->route('admin.objectives')->with('success', 'Objective added successfully!');
    }

    // Show the form to edit an existing objective
    public function edit($id)
    {
        $objective = Objective::findOrFail($id); // Find the objective by ID
        return view('CMS.objective_edit', compact('objective')); // Return the edit view with the data
    }

    // Update an existing objective in the database
    public function update(Request $request, $id)
    {
        // Validate incoming data
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Find the objective by ID
        $objective = Objective::findOrFail($id);

        // Handle file upload for the image
        if ($request->hasFile('image')) {
            // Delete the old image if exists
            if ($objective->image) {
                \Storage::delete($objective->image);
            }
            $imagePath = $request->file('image')->store('public/objectives');
        } else {
            // Keep the existing image if no new image is uploaded
            $imagePath = $objective->image;
        }

        // Update the objective
        $objective->update([
            'title' => $request->title,
            'description' => $request->description,
            'image' => $imagePath,
        ]);

        // Redirect back to the objectives list page with a success message
        return redirect()->route('admin.objectives')->with('success', 'Objective updated successfully!');
    }

    // Delete an existing objective
    public function destroy($id)
    {
        $objective = Objective::findOrFail($id); // Find the objective by ID

        // Delete the image from storage if it exists
        if ($objective->image) {
            \Storage::delete($objective->image);
        }

        // Delete the objective from the database
        $objective->delete();

        // Redirect back to the objectives list page with a success message
        return redirect()->route('admin.objectives')->with('success', 'Objective deleted successfully!');
    }
}
