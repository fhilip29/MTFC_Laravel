<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Subscription;
use Illuminate\Http\Request;
use App\Models\Sessions;


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
        ]);
        
        // Set predefined values based on plan type
        $price = $this->getPlanPrice($validated['type'], $validated['plan']);
        $dates = $this->getPlanDates($validated['plan']);
        
        // Create subscription with predefined values
        $subscription = $user->subscriptions()->create([
            'type' => $validated['type'],
            'plan' => $validated['plan'],
            'price' => $price,
            'start_date' => $dates['start_date'],
            'end_date' => $dates['end_date'],
            'is_active' => true
        ]);
        
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
        ]);
        
        // Set predefined values based on plan type
        $price = $this->getPlanPrice($validated['type'], $validated['plan']);
        $dates = $this->getPlanDates($validated['plan']);
        
        // Update subscription with predefined values
        $subscription->update([
            'type' => $validated['type'],
            'plan' => $validated['plan'],
            'price' => $price,
            'start_date' => $dates['start_date'],
            'end_date' => $dates['end_date'],
            'is_active' => true
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Subscription updated successfully',
            'subscription' => $subscription
        ]);
    }
    
    /**
     * Get predefined price based on plan type and duration
     */
    private function getPlanPrice($type, $plan)
    {
        $prices = [
            'gym' => [
                'daily' => 10.00,
                'monthly' => 80.00,
                'per-session' => 15.00
            ],
            'boxing' => [
                'daily' => 15.00,
                'monthly' => 100.00,
                'per-session' => 20.00
            ],
            'muay' => [
                'daily' => 15.00,
                'monthly' => 100.00,
                'per-session' => 20.00
            ],
            'jiu-jitsu' => [
                'daily' => 20.00,
                'monthly' => 120.00,
                'per-session' => 25.00
            ]
        ];
        
        return $prices[$type][$plan] ?? 0.00;
    }
    
    /**
     * Get start/end dates based on plan
     */
    private function getPlanDates($plan)
    {
        $startDate = now();
        $endDate = null;
        
        switch ($plan) {
            case 'daily':
                $endDate = now()->addDay();
                break;
            case 'monthly':
                $endDate = now()->addMonth();
                break;
            case 'per-session':
                // No end date for per-session plans
                $endDate = null;
                break;
        }
        
        return [
            'start_date' => $startDate,
            'end_date' => $endDate
        ];
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
