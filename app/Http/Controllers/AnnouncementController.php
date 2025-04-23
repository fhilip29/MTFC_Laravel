<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AnnouncementController extends Controller
{
    /**
     * Display announcements for users
     */
    public function userIndex()
    {
        try {
            // Get active announcements
            $announcements = Announcement::active()
                ->where(function($query) {
                    $query->whereNull('scheduled_at')
                        ->orWhere('scheduled_at', '<=', now());
                })
                ->orderBy('created_at', 'desc')
                ->get();

            return view('announcements.index', compact('announcements'));
        } catch (\Exception $e) {
            Log::error('Error fetching announcements: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load announcements.');
        }
    }

    /**
     * Display announcements for admin management
     */
    public function adminIndex()
    {
        try {
            $announcements = Announcement::with('creator')
                ->orderBy('created_at', 'desc')
                ->get();

            return view('admin.promotion.admin_promo', compact('announcements'));
        } catch (\Exception $e) {
            Log::error('Error fetching announcements: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load announcements.');
        }
    }

    /**
     * Display a listing of announcements (used for admin.announcements route)
     */
    public function index()
    {
        try {
            $announcements = Announcement::with('creator')
                ->orderBy('created_at', 'desc')
                ->get();

            return view('admin.promotion.admin_promo', compact('announcements'));
        } catch (\Exception $e) {
            Log::error('Error fetching announcements: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load announcements.');
        }
    }

    /**
     * Store a new announcement
     */
    public function store(Request $request)
    {
        try {
            // Added additional debugging
            Log::info('Starting announcement creation with request data', [
                'request_data' => $request->all(),
                'user_id' => Auth::id(),
                'is_authenticated' => Auth::check()
            ]);

            // Check if user is authenticated
            if (!Auth::check()) {
                Log::error('User not authenticated when creating announcement');
                return redirect()->route('login')->with('error', 'Please login to create announcements.');
            }

            // Validate the request
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'message' => 'required|string',
                'is_active' => 'boolean',
                'schedule_date' => 'nullable|date_format:Y-m-d',
                'schedule_time' => 'nullable|date_format:H:i',
            ]);

            Log::info('Announcement validation passed', ['validated' => $validated]);

            // Prepare announcement data
            $data = [
                'title' => $validated['title'],
                'message' => $validated['message'],
                'is_active' => $request->has('is_active') ? 1 : 0,
                'created_by' => Auth::id(),
            ];

            // Handle scheduling
            if ($request->filled('schedule_date') && $request->filled('schedule_time')) {
                $scheduledAt = Carbon::createFromFormat(
                    'Y-m-d H:i',
                    $request->schedule_date . ' ' . $request->schedule_time
                );

                if ($scheduledAt->isPast()) {
                    if ($request->expectsJson()) {
                        return response()->json([
                            'success' => false,
                            'errors' => ['schedule_date' => ['Scheduled time cannot be in the past.']]
                        ], 422);
                    }
                    return back()->withErrors(['schedule_date' => 'Scheduled time cannot be in the past.'])
                                ->withInput();
                }

                $data['scheduled_at'] = $scheduledAt;
            }

            // Create the announcement with detailed logging
            DB::beginTransaction();
            try {
                $announcement = Announcement::create($data);
                Log::info('Announcement created successfully', [
                    'id' => $announcement->id,
                    'title' => $announcement->title,
                    'created_by' => $announcement->created_by
                ]);
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Database error creating announcement', [
                    'error' => $e->getMessage(),
                    'data' => $data
                ]);
                throw $e;
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Announcement created successfully.',
                    'announcement' => $announcement
                ]);
            }
            
            return redirect()->route('admin.promotion.admin_promo')
                            ->with('success', 'Announcement created successfully.');
        } catch (\Exception $e) {
            Log::error('Error creating announcement: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'request_data' => $request->except(['_token']),
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while creating the announcement: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->withErrors(['error' => 'An error occurred while creating the announcement: ' . $e->getMessage()])
                        ->withInput();
        }
    }

    /**
     * Get a specific announcement
     */
    public function show($id)
    {
        try {
            $announcement = Announcement::with('creator')->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'announcement' => $announcement
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching announcement: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Announcement not found.'
            ], 404);
        }
    }

    /**
     * Update an announcement
     */
    public function update(Request $request, $id)
    {
        try {
            Log::info('Starting announcement update', ['id' => $id]);
            
            $announcement = Announcement::findOrFail($id);
            
            // Validate the request
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'message' => 'required|string',
                'is_active' => 'boolean',
                'schedule_date' => 'nullable|date_format:Y-m-d',
                'schedule_time' => 'nullable|date_format:H:i',
            ]);

            // Prepare update data
            $data = [
                'title' => $validated['title'],
                'message' => $validated['message'],
                'is_active' => $request->has('is_active') ? 1 : 0,
            ];

            // Handle scheduling
            if ($request->filled('schedule_date') && $request->filled('schedule_time')) {
                $scheduledAt = Carbon::createFromFormat(
                    'Y-m-d H:i',
                    $request->schedule_date . ' ' . $request->schedule_time
                );

                if ($scheduledAt->isPast()) {
                    if ($request->expectsJson()) {
                        return response()->json([
                            'success' => false,
                            'errors' => ['schedule_date' => ['Scheduled time cannot be in the past.']]
                        ], 422);
                    }
                    return back()->withErrors(['schedule_date' => 'Scheduled time cannot be in the past.'])
                                ->withInput();
                }

                $data['scheduled_at'] = $scheduledAt;
            } else {
                $data['scheduled_at'] = null;
            }

            // Update the announcement
            $announcement->update($data);
            
            Log::info('Announcement updated successfully', ['id' => $id]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Announcement updated successfully.'
                ]);
            }
            
            return redirect()->route('admin.promotion.admin_promo')
                            ->with('success', 'Announcement updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating announcement: ' . $e->getMessage(), [
                'id' => $id,
                'user_id' => Auth::id(),
                'request_data' => $request->except(['_token'])
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while updating the announcement.'
                ], 500);
            }
            
            return back()->withErrors(['error' => 'An error occurred while updating the announcement.'])
                        ->withInput();
        }
    }

    /**
     * Delete an announcement
     */
    public function destroy($id)
    {
        try {
            Log::info('Starting announcement deletion', ['id' => $id]);
            $announcement = Announcement::findOrFail($id);
            $announcement->delete();
            
            Log::info('Announcement deleted successfully', ['id' => $id]);

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Announcement deleted successfully.'
                ]);
            }
            
            return redirect()->route('admin.promotion.admin_promo')
                            ->with('success', 'Announcement deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting announcement: ' . $e->getMessage(), ['id' => $id]);
            
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while deleting the announcement.'
                ], 500);
            }
            
            return redirect()->route('admin.promotion.admin_promo')
                            ->with('error', 'An error occurred while deleting the announcement.');
        }
    }

    /**
     * Toggle announcement active status
     */
    public function toggleActive($id)
    {
        try {
            Log::info('Starting announcement toggle status', ['id' => $id]);
            $announcement = Announcement::findOrFail($id);
            $announcement->update([
                'is_active' => !$announcement->is_active
            ]);

            $status = $announcement->is_active ? 'activated' : 'deactivated';
            Log::info('Announcement status updated', ['id' => $id, 'status' => $status]);

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Announcement $status successfully."
                ]);
            }
            
            return redirect()->route('admin.promotion.admin_promo')
                            ->with('success', "Announcement $status successfully.");
        } catch (\Exception $e) {
            Log::error('Error toggling announcement status: ' . $e->getMessage(), ['id' => $id]);
            
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while updating the announcement status.'
                ], 500);
            }
            
            return redirect()->route('admin.promotion.admin_promo')
                            ->with('error', 'An error occurred while updating the announcement status.');
        }
    }

    /**
     * Get a specific announcement for API
     */
    public function apiShow($id)
    {
        try {
            $announcement = Announcement::with('creator')->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'announcement' => $announcement
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching announcement: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Announcement not found.'
            ], 404);
        }
    }
}
