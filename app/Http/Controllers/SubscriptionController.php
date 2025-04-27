<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    /**
     * Store a new subscription
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string',
            'plan' => 'required|string',
            'price' => 'required|numeric',
        ]);

        // Create new subscription
        $subscription = new Subscription();
        $subscription->user_id = Auth::id();
        $subscription->type = $validated['type'];
        $subscription->plan = $validated['plan'];
        $subscription->price = $validated['price'];
        $subscription->start_date = Carbon::today();
        
        // Set end date based on plan
        if ($validated['plan'] == 'daily') {
            $subscription->end_date = Carbon::today()->addDay();
        } elseif ($validated['plan'] == 'monthly') {
            $subscription->end_date = Carbon::today()->addMonth();
        } elseif ($validated['plan'] == 'per-session') {
            // For per-session, we don't set an end date
            $subscription->end_date = null;
        }
        
        $subscription->is_active = true;
        $subscription->save();

        return redirect()->back()->with('success', 'You have successfully subscribed to ' . ucfirst($validated['type']) . '!');
    }

    /**
     * Show user's subscription history
     */
    public function history()
    {
        $subscriptions = Auth::user()->subscriptions()->orderBy('created_at', 'desc')->get();
        return view('subscription.history', compact('subscriptions'));
    }

    /**
     * Cancel a subscription
     */
    public function cancel($id)
    {
        $subscription = Subscription::findOrFail($id);
        
        // Ensure subscription belongs to the authenticated user
        if ($subscription->user_id != Auth::id()) {
            return redirect()->back()->with('error', 'You are not authorized to cancel this subscription.');
        }
        
        $subscription->is_active = false;
        $subscription->save();
        
        return redirect()->back()->with('success', 'Your subscription has been cancelled successfully.');
    }
} 