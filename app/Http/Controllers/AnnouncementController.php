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
        $query = Announcement::where('is_active', true);
    
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
            ]);

            $data = [
                'title' => $validated['title'],
                'message' => $validated['message'],
                'created_by' => Auth::id(),
            ];

            if ($request->filled('schedule_date') && $request->filled('schedule_time')) {
                $scheduledAt = Carbon::createFromFormat('Y-m-d H:i', $request->schedule_date . ' ' . $request->schedule_time);

                if ($scheduledAt->isPast()) {
                    return back()->withErrors(['schedule_date' => 'Scheduled time must be in the future.'])->withInput();
                }

                $data['scheduled_at'] = $scheduledAt;
                $data['is_active'] = 'pending';
            } else {
                $data['scheduled_at'] = null;
                $data['is_active'] = $request->has('is_active') ? 'active' : 'inactive';
            }

            DB::beginTransaction();
            Announcement::create($data);
            DB::commit();

            return redirect()->route('admin.promotion.admin_promo')->with('success', 'Announcement created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating announcement', ['error' => $e->getMessage()]);
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
            ]);

            $data = [
                'title' => $validated['title'],
                'message' => $validated['message'],
            ];

            if ($request->filled('schedule_date') && $request->filled('schedule_time')) {
                $scheduledAt = Carbon::createFromFormat('Y-m-d H:i', $request->schedule_date . ' ' . $request->schedule_time);

                if ($scheduledAt->isPast()) {
                    return back()->withErrors(['schedule_date' => 'Scheduled time must be in the future.'])->withInput();
                }

                $data['scheduled_at'] = $scheduledAt;
                $data['is_active'] = 'pending';
            } else {
                $data['scheduled_at'] = null;
                $data['is_active'] = $request->has('is_active') ? 'active' : 'inactive';
            }

            $announcement->update($data);

            return redirect()->route('admin.promotion.admin_promo')->with('success', 'Announcement updated successfully.');

        } catch (\Exception $e) {
            Log::error('Error updating announcement', ['error' => $e->getMessage()]);
            return back()->withErrors(['error' => 'An error occurred while updating the announcement.'])->withInput();
        }
    }

    // 🔍 Show details
    public function show($id)
    {
        $announcement = Announcement::findOrFail($id);
        return view('admin.announcements.show', compact('announcement'));
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
