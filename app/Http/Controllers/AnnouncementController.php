<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    // Display all active announcements for the user (public view)
public function userIndex()
{
    // Fetch active announcements from the database
    $announcements = Announcement::where('is_active', true)
        ->orderBy('created_at', 'desc')
        ->get();

    // Return the notifications view with the announcements
    return view('notifications', compact('announcements'));
}

    // Admin dashboard: View all announcements (Admin view)
public function adminIndex()
{
    // Fetch all announcements for admin
    $announcements = Announcement::orderBy('created_at', 'desc')->get();

    // Return the admin announcement management view
    return view('admin.promotion.admin_promo', compact('announcements'));
}

    // Show the details of a specific announcement (Admin view)
    public function show($id)
    {
        // Find the specific announcement by its ID
        $announcement = Announcement::findOrFail($id);

        // Return the announcement details view
        return view('admin.announcements.show', compact('announcement'));
    }

    // Show the form to create a new announcement (Admin view)
    public function create()
    {
        // Return the form to create an announcement
        return view('admin.announcements.create');
    }

    // Store a new announcement in the database (Admin)
    public function store(Request $request)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'is_active' => 'required|boolean',
        ]);

        // Create and save the new announcement
        $announcement = new Announcement();
        $announcement->title = $validated['title'];
        $announcement->message = $validated['message'];
        $announcement->is_active = $validated['is_active'];
        $announcement->save();

        // Redirect to the announcement list with a success message
        return redirect()->route('admin.announcements')->with('success', 'Announcement created successfully!');
    }

    // Show the form to edit an existing announcement (Admin view)
    public function edit($id)
    {
        // Find the specific announcement by its ID
        $announcement = Announcement::findOrFail($id);

        // Return the edit form with the announcement data
        return view('admin.announcements.edit', compact('announcement'));
    }

    // Update an existing announcement in the database (Admin)
    public function update(Request $request, $id)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'is_active' => 'required|boolean',
        ]);

        // Find the announcement and update its data
        $announcement = Announcement::findOrFail($id);
        $announcement->title = $validated['title'];
        $announcement->message = $validated['message'];
        $announcement->is_active = $validated['is_active'];
        $announcement->save();

        // Redirect to the announcement list with a success message
        return redirect()->route('admin.announcements')->with('success', 'Announcement updated successfully!');
    }

    // Delete an announcement from the database (Admin)
    public function destroy($id)
    {
        // Find the announcement and delete it
        $announcement = Announcement::findOrFail($id);
        $announcement->delete();

        // Redirect to the announcement list with a success message
        return redirect()->route('admin.announcements')->with('success', 'Announcement deleted successfully!');
    }

    // Toggle the active status of an announcement (Admin)
    public function toggleActive($id)
    {
        // Find the announcement by its ID
        $announcement = Announcement::findOrFail($id);

        // Toggle the active status of the announcement
        $announcement->is_active = !$announcement->is_active;
        $announcement->save();

        // Return a success response or redirect
        return redirect()->route('admin.announcements')->with('success', 'Announcement status updated!');
    }

    // API route to show a specific announcement (Public API)
    public function apiShow($id)
    {
        // Fetch the specific announcement by its ID
        $announcement = Announcement::findOrFail($id);

        // Return the announcement data as JSON
        return response()->json($announcement);
    }
}
