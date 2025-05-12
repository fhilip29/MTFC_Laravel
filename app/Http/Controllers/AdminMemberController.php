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
        
        // Validate plan type combinations
        if ($validated['type'] !== 'gym' && $validated['plan'] === 'daily') {
            return response()->json([
                'success' => false,
                'message' => 'Daily plan is only available for gym subscriptions'
            ], 400);
        }
        
        if ($validated['type'] === 'gym' && $validated['plan'] === 'per-session') {
            return response()->json([
                'success' => false,
                'message' => 'Per-session plan is not available for gym subscriptions'
            ], 400);
        }
        
        // Set predefined values based on plan type
        $price = $this->getPlanPrice($validated['type'], $validated['plan']);
        $dates = $this->getPlanDates($validated['plan']);
        
        // Set default values for subscription creation
        $subscriptionData = [
            'user_id' => $user->id,
            'type' => $validated['type'],
            'plan' => $validated['plan'],
            'price' => $price,
            'start_date' => $dates['start_date'],
            'end_date' => $dates['end_date'],
            'is_active' => true,
            'sessions_used' => 0
        ];
        
        // For per-session plans, set initial session count and null end date
        if ($validated['plan'] === 'per-session') {
            $subscriptionData['sessions_remaining'] = 1;
        } else {
            $subscriptionData['sessions_remaining'] = null;
        }
        
        // Create subscription with predefined values
        $subscription = Subscription::create($subscriptionData);
        
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
        
        // Validate plan type combinations
        if ($validated['type'] !== 'gym' && $validated['plan'] === 'daily') {
            return response()->json([
                'success' => false,
                'message' => 'Daily plan is only available for gym subscriptions'
            ], 400);
        }
        
        if ($validated['type'] === 'gym' && $validated['plan'] === 'per-session') {
            return response()->json([
                'success' => false,
                'message' => 'Per-session plan is not available for gym subscriptions'
            ], 400);
        }
        
        // Set predefined values based on plan type
        $price = $this->getPlanPrice($validated['type'], $validated['plan']);
        $dates = $this->getPlanDates($validated['plan']);
        
        // Set default values for subscription update
        $subscriptionData = [
            'type' => $validated['type'],
            'plan' => $validated['plan'],
            'price' => $price,
            'start_date' => $dates['start_date'],
            'end_date' => $dates['end_date'],
            'is_active' => true
        ];
        
        // Set sessions for per-session plans
        if ($validated['plan'] === 'per-session') {
            // For existing per-session plans being updated, add 1 session
            if ($subscription->plan === 'per-session') {
                $sessionsRemaining = $subscription->sessions_remaining;
                if ($sessionsRemaining === null) {
                    $sessionsRemaining = 0; // convert from legacy/unlimited to counted
                }
                $subscriptionData['sessions_remaining'] = $sessionsRemaining + 1;
                $subscriptionData['sessions_used'] = $subscription->sessions_used;
            } else {
                // For new per-session conversions, start with 1 session
                $subscriptionData['sessions_remaining'] = 1;
                $subscriptionData['sessions_used'] = 0;
            }
        } else {
            // For non-per-session plans, set to null (time-based plans don't use sessions)
            $subscriptionData['sessions_remaining'] = null;
        }
        
        // Update subscription with predefined values
        $subscription->update($subscriptionData);
        
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
                'daily' => 100.00,
                'monthly' => 1000.00,
            ],
            'boxing' => [
                'monthly' => 3000.00,
                'per-session' => 260.00
            ],
            'muay' => [
                'monthly' => 2600.00,
                'per-session' => 350.00
            ],
            'jiu-jitsu' => [
                'monthly' => 3500.00,
                'per-session' => 400.00
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

    public function addSessions(Request $request, User $user, Subscription $subscription)
    {
        // Check if the subscription belongs to the user
        if ($subscription->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Subscription does not belong to this user'
            ], 403);
        }
        
        // Validate request
        $validated = $request->validate([
            'sessions' => 'required|integer|min:1|max:100',
        ]);
        
        // Check if the subscription is a per-session plan
        if ($subscription->plan !== 'per-session') {
            return response()->json([
                'success' => false,
                'message' => 'Can only add sessions to per-session subscriptions'
            ], 400);
        }
        
        // Initialize sessions_remaining if it's null (legacy data)
        if ($subscription->sessions_remaining === null) {
            $subscription->sessions_remaining = 0;
        }
        
        // Add sessions
        $subscription->sessions_remaining += $validated['sessions'];
        
        // Ensure the subscription is active
        $subscription->is_active = true;
        
        $subscription->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Sessions added successfully',
            'subscription' => $subscription
        ]);
    }
}
