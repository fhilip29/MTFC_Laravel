<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AnnouncementController extends Controller
{
    // 👥 Public view
    public function userIndex(Request $request)
    {
        $query = Announcement::where('is_active', 'active');
    
        // Search functionality
        if ($search = $request->query('search')) {
            $query->where('title', 'like', "%{$search}%");
        }
    
        // Filter: Recent
        if ($request->query('filter') === 'recent') {
            $query->where('created_at', '>=', now()->subDays(30));
        }
    
        $announcements = $query->orderBy('created_at', 'desc')->get();
    
        return view('announcements', compact('announcements'));
    }


    // 🛠 Admin index view (full list)
    public function adminIndex()
{
    $announcements = Announcement::orderBy('created_at', 'desc')->get();
    
    $totalAnnouncements = $announcements->count();
    $activeAnnouncements = $announcements->where('is_active', 'active')->count();
    $scheduledAnnouncements = $announcements->whereNotNull('scheduled_at')->where('scheduled_at', '>', now())->count();
    
    return view('admin.promotion.admin_promo', compact('announcements', 'totalAnnouncements', 'activeAnnouncements', 'scheduledAnnouncements'));
}


    // ➕ Create form
    public function create()
    {
        return view('admin.announcements.create');
    }

    // ✅ Store new announcement
    public function store(Request $request)
    {
        try {
            if (!Auth::check()) {
                return redirect()->route('login')->with('error', 'Please login first.');
            }

            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'message' => 'required|string',
                'schedule_date' => 'nullable|date_format:Y-m-d',
                'schedule_time' => 'nullable|date_format:H:i',
                'is_scheduled' => 'nullable|boolean',
                'is_pending' => 'nullable|boolean',
            ]);

            $data = [
                'title' => $validated['title'],
                'message' => $validated['message'],
                'created_by' => Auth::id(),
            ];

            if ($request->input('is_scheduled') == 1 && $request->filled('schedule_date') && $request->filled('schedule_time')) {
                $scheduledAt = Carbon::createFromFormat('Y-m-d H:i', $request->schedule_date . ' ' . $request->schedule_time);

                if ($scheduledAt->isPast()) {
                    return back()->withErrors(['schedule_date' => 'Scheduled time must be in the future.'])->withInput();
                }

                $data['scheduled_at'] = $scheduledAt;
                $data['is_active'] = 'pending'; // Set to pending for scheduled announcements
            } else {
                $data['scheduled_at'] = null;
                $data['is_active'] = $request->has('is_active') && $request->input('is_active') == 1 ? 'active' : 'inactive';
            }

            DB::beginTransaction();
            Announcement::create($data);
            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Announcement created successfully.'
                ]);
            }

            return redirect()->route('admin.promotion.admin_promo')->with('success', 'Announcement created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating announcement', ['error' => $e->getMessage()]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while creating the announcement: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->withErrors(['error' => 'An error occurred while creating the announcement.'])->withInput();
        }
    }

    // ✏️ Edit form
    public function edit(Announcement $announcement)
    {
        return view('admin.announcements.edit', compact('announcement'));
    }

    // 🔁 Update existing
    public function update(Request $request, $id)
    {
        try {
            $announcement = Announcement::findOrFail($id);

            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'message' => 'required|string',
                'schedule_date' => 'nullable|date_format:Y-m-d',
                'schedule_time' => 'nullable|date_format:H:i',
                'is_scheduled' => 'nullable|boolean',
                'is_pending' => 'nullable|boolean',
            ]);

            $data = [
                'title' => $validated['title'],
                'message' => $validated['message'],
            ];

            // If is_scheduled is true and both date and time are provided
            if ($request->input('is_scheduled') == 1 && $request->filled('schedule_date') && $request->filled('schedule_time')) {
                $scheduledAt = Carbon::createFromFormat('Y-m-d H:i', $request->schedule_date . ' ' . $request->schedule_time);

                if ($scheduledAt->isPast()) {
                    return back()->withErrors(['schedule_date' => 'Scheduled time must be in the future.'])->withInput();
                }

                $data['scheduled_at'] = $scheduledAt;
                $data['is_active'] = 'pending'; // Always set to pending for scheduled
            } else {
                // Explicitly set scheduled_at to null when unchecking the schedule_later checkbox
                $data['scheduled_at'] = null;
                $data['is_active'] = $request->has('is_active') && $request->input('is_active') == 1 ? 'active' : 'inactive';
            }

            $announcement->update($data);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Announcement updated successfully.'
                ]);
            }

            return redirect()->route('admin.promotion.admin_promo')->with('success', 'Announcement updated successfully.');

        } catch (\Exception $e) {
            Log::error('Error updating announcement', ['error' => $e->getMessage(), 'request' => $request->all()]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while updating the announcement: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->withErrors(['error' => 'An error occurred while updating the announcement.'])->withInput();
        }
    }

    // 🔍 Show details (admin view)
    public function show($id)
    {
        $announcement = Announcement::findOrFail($id);
        
        // Check if the announcement is active for non-admin users
        if (Auth::check() && Auth::user()->role === 'admin') {
            // Admin can view all announcements regardless of status
            return view('announcement-detail', compact('announcement'));
        } else {
            // Regular users can only view active announcements
            if ($announcement->is_active === 'active') {
                return view('announcement-detail', compact('announcement'));
            } else {
                return redirect()->route('announcements')
                    ->with('error', 'The requested announcement is not available.');
            }
        }
    }

    // ❌ Delete announcement
    public function destroy($id)
    {
        try {
            $announcement = Announcement::findOrFail($id);
            $announcement->delete();

            return redirect()->route('admin.promotion.admin_promo')->with('success', 'Announcement deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting announcement', ['error' => $e->getMessage()]);
            return back()->withErrors(['error' => 'An error occurred while deleting the announcement.']);
        }
    }

    // 🔄 Toggle active/inactive
    public function toggleActive($id)
    {
        try {
            $announcement = Announcement::findOrFail($id);
            $announcement->is_active = $announcement->is_active === 'active' ? 'inactive' : 'active';
            $announcement->save();

            return response()->json([
                'success' => true,
                'status' => $announcement->is_active,
            ]);
        } catch (\Exception $e) {
            Log::error('Toggle error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to toggle status.'
            ], 500);
        }
    }

    public function toggleStatus($id)
{
    try {
        $announcement = Announcement::findOrFail($id);

        // If the announcement is scheduled in the future, prevent toggling
        if ($announcement->scheduled_at && $announcement->scheduled_at > now()) {
            return response()->json(['error' => 'Cannot toggle a scheduled announcement before its time.'], 403);
        }

        // Toggle between 'active' and 'inactive'
        $announcement->is_active = $announcement->is_active === 'active' ? 'inactive' : 'active';
        $announcement->save();

        return response()->json([
            'success' => true,
            'status' => $announcement->is_active,
        ]);
    } catch (\Exception $e) {
        \Log::error('Toggle Status Error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'An error occurred while updating the announcement status.'
        ], 500);
    }
}



    // 📱 API Show for external apps
    public function apiShow($id)
    {
        $announcement = Announcement::findOrFail($id);
        return response()->json($announcement);
    }
}
