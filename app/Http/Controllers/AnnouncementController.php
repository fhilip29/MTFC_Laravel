<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    public function userIndex()
    {
        $announcements = Announcement::query()
            ->where('is_active', true)
            ->where(function($query) {
                $query->where('target_audience', 'all')
                    ->orWhere('target_audience', Auth::user() ? Auth::user()->role : 'all');
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('announcements', compact('announcements'));
    }

    public function adminIndex()
    {
        $announcements = Announcement::query()
            ->with('creator')
            ->select([
                'id',
                'title',
                'message',
                'target_audience',
                'send_email',
                'send_in_app',
                'scheduled_at',
                'sent_at',
                'is_active',
                'created_by',
                'created_at'
            ])
            ->latest()
            ->get()
            ->map(function ($announcement) {
                return [
                    'id' => $announcement->id,
                    'title' => $announcement->title,
                    'date' => $announcement->created_at->format('Y-m-d'),
                    'sent_to' => ucfirst($announcement->target_audience),
                    'method' => $this->getDeliveryMethod($announcement),
                    'status' => $announcement->status,
                    'status_class' => $announcement->getStatusClassAttribute()
                ];
            });

        return view('admin.promotion.admin_promo', compact('announcements'));
    }

    public function index()
    {
        $announcements = Announcement::query()
            ->with('creator')
            ->select([
                'id',
                'title',
                'message',
                'target_audience',
                'send_email',
                'send_in_app',
                'scheduled_at',
                'sent_at',
                'is_active',
                'created_by',
                'created_at'
            ])
            ->latest()
            ->get()
            ->map(function ($announcement) {
                return [
                    'id' => $announcement->id,
                    'title' => $announcement->title,
                    'date' => $announcement->created_at->format('Y-m-d'),
                    'sent_to' => ucfirst($announcement->target_audience),
                    'method' => $this->getDeliveryMethod($announcement),
                    'status' => $announcement->status,
                    'status_class' => $announcement->getStatusClassAttribute()
                ];
            });

        return view('admin.announcement.admin_announcement', ['announcements' => $announcements]);
    }

    private function getDeliveryMethod($announcement)
    {
        $methods = [];
        if ($announcement->send_in_app) $methods[] = 'In-App';
        if ($announcement->send_email) $methods[] = 'Email';
        return implode(', ', $methods);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'target_audience' => 'required|in:all,active,trainers,staff',
            'send_email' => 'boolean',
            'send_in_app' => 'boolean',
            'schedule_later' => 'boolean',
            'schedule_date' => 'required_if:schedule_later,true|nullable|date',
            'schedule_time' => 'required_if:schedule_later,true|nullable|date_format:H:i',
        ]);

        $announcement = new Announcement();
        $announcement->title = $validated['title'];
        $announcement->message = $validated['message'];
        $announcement->target_audience = $validated['target_audience'];
        $announcement->send_email = $request->boolean('send_email', false);
        $announcement->send_in_app = $request->boolean('send_in_app', true);
        $announcement->created_by = Auth::id();

        if ($request->boolean('schedule_later') && $request->schedule_date && $request->schedule_time) {
            $announcement->scheduled_at = $request->schedule_date . ' ' . $request->schedule_time;
        }

        $announcement->save();

        // If not scheduled, mark as sent immediately
        if (!$announcement->scheduled_at) {
            $announcement->markAsSent();
        }

        return response()->json([
            'success' => true,
            'message' => 'Announcement created successfully',
            'announcement' => $announcement
        ]);
    }

    public function show(Announcement $announcement)
    {
        $announcement->load('creator');
        
        return response()->json([
            'success' => true,
            'announcement' => $announcement
        ]);
    }

    public function edit(Announcement $announcement)
    {
        return response()->json([
            'success' => true,
            'announcement' => $announcement
        ]);
    }

    public function update(Request $request, Announcement $announcement)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'target_audience' => 'required|in:all,active,trainers,staff',
            'send_email' => 'boolean',
            'send_in_app' => 'boolean',
            'schedule_later' => 'boolean',
            'schedule_date' => 'required_if:schedule_later,true|nullable|date',
            'schedule_time' => 'required_if:schedule_later,true|nullable|date_format:H:i',
        ]);

        $announcement->title = $validated['title'];
        $announcement->message = $validated['message'];
        $announcement->target_audience = $validated['target_audience'];
        $announcement->send_email = $request->boolean('send_email', false);
        $announcement->send_in_app = $request->boolean('send_in_app', true);

        if ($request->boolean('schedule_later') && $request->schedule_date && $request->schedule_time) {
            $announcement->scheduled_at = $request->schedule_date . ' ' . $request->schedule_time;
        } else {
            $announcement->scheduled_at = null;
        }

        $announcement->save();

        return response()->json([
            'success' => true,
            'message' => 'Announcement updated successfully',
            'announcement' => $announcement
        ]);
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();

        return response()->json([
            'success' => true,
            'message' => 'Announcement deleted successfully'
        ]);
    }

    public function toggleActive(Announcement $announcement)
    {
        $announcement->is_active = !$announcement->is_active;
        $announcement->save();

        return response()->json([
            'success' => true,
            'message' => 'Announcement status updated successfully',
            'is_active' => $announcement->is_active
        ]);
    }

    public function apiShow(Announcement $announcement)
    {
        return response()->json([
            'success' => true,
            'announcement' => [
                'id' => $announcement->id,
                'title' => $announcement->title,
                'message' => $announcement->message,
                'target_audience' => ucfirst($announcement->target_audience),
                'created_at' => $announcement->created_at
            ]
        ]);
    }
}
