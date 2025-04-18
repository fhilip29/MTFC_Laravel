<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Subscription;
use Illuminate\Http\Request;

class AdminMemberController extends Controller
{
    public function index(Request $request)
    {
        // Check if we should show archived members
        $showArchived = $request->has('show_archived');
        
        $query = User::whereIn('role', ['member']);
        
        if ($showArchived) {
            $query->archived();
        } else {
            $query->notArchived();
        }
        
        $members = $query->get();
        
        return view('admin.members.admin_members', compact('members', 'showArchived'));
    }
    
    public function show($id)
    {
        $member = User::findOrFail($id);
        
        return view('admin.members.member_details', compact('member'));
    }

    public function manageMemberSubscriptions(User $user)
    {
        $subscriptions = $user->subscriptions()->latest()->get();
        return response()->json($subscriptions);
    }
    
    public function storeSubscription(Request $request, User $user)
    {
        $validated = $request->validate([
            'type' => 'required|string|in:gym,boxing,muay,jiu-jitsu',
            'plan' => 'required|string|in:daily,monthly,per-session',
            'price' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'boolean',
        ]);
        
        $subscription = $user->subscriptions()->create($validated);
        
        return response()->json([
            'success' => true,
            'message' => 'Subscription created successfully',
            'subscription' => $subscription
        ]);
    }
    
    public function updateSubscription(Request $request, User $user, Subscription $subscription)
    {
        // Check if the subscription belongs to the user
        if ($subscription->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Subscription does not belong to this user'
            ], 403);
        }
        
        $validated = $request->validate([
            'type' => 'required|string|in:gym,boxing,muay,jiu-jitsu',
            'plan' => 'required|string|in:daily,monthly,per-session',
            'price' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'boolean',
        ]);
        
        $subscription->update($validated);
        
        return response()->json([
            'success' => true,
            'message' => 'Subscription updated successfully',
            'subscription' => $subscription
        ]);
    }
    
    public function cancelSubscription(User $user, Subscription $subscription)
    {
        // Check if the subscription belongs to the user
        if ($subscription->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Subscription does not belong to this user'
            ], 403);
        }
        
        // Cancel the subscription by setting is_active to false
        $subscription->update(['is_active' => false]);
        
        return response()->json([
            'success' => true,
            'message' => 'Subscription cancelled successfully',
            'subscription' => $subscription
        ]);
    }

    public function archiveMember(User $user)
    {
        // Check if user is already archived and toggle the status
        $newStatus = !$user->is_archived;
        $message = $newStatus ? 'archived' : 'unarchived';
        
        $user->update(['is_archived' => $newStatus]);
        
        return response()->json([
            'success' => true,
            'message' => "Member has been {$message} successfully",
            'is_archived' => $newStatus
        ]);
    }
}
