<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\User;
use App\Http\Controllers\InvoiceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    protected $invoiceController;

    public function __construct(InvoiceController $invoiceController)
    {
        $this->invoiceController = $invoiceController;
        $this->middleware('auth');
    }

    /**
     * Create a new subscription
     */
    public function store(Request $request)
    {
        try {
            // Prevent admins and trainers from subscribing
            $user = Auth::user();
            if ($user->role === 'admin' || $user->role === 'trainer') {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Admins and trainers cannot subscribe to plans.'
                    ], 403);
                }
                return redirect()->back()->with('error', 'Admins and trainers cannot subscribe to plans.');
            }
            
            // Check if user already has an active subscription
            $hasActiveSubscription = $user->subscriptions()
                ->where('is_active', true)
                ->where('end_date', '>', now())
                ->exists();
                
            if ($hasActiveSubscription) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You already have an active subscription. Please cancel your current subscription before subscribing to a new plan.'
                    ], 400);
                }
                return redirect()->back()->with('error', 'You already have an active subscription. Please cancel your current subscription before subscribing to a new plan.');
            }
            
            $validated = $request->validate([
                'type' => 'required|string|in:gym,boxing,muay,jiu',
                'plan' => 'required|string|in:daily,monthly,quarterly,annual',
                'amount' => 'required|numeric|min:0',
                'payment_method' => 'nullable|string',
                'payment_status' => 'nullable|string',
                'waiver_accepted' => 'nullable|boolean'
            ]);

            // Get plan details to determine subscription duration
            $duration = null;
            $endDate = null;
            switch ($validated['plan']) {
                case 'daily':
                    $endDate = now()->addDay();
                    break;
                case 'monthly':
                    $endDate = now()->addMonth();
                    break;
                case 'quarterly':
                    $endDate = now()->addMonths(3);
                    break;
                case 'annual':
                    $endDate = now()->addYear();
                    break;
            }
            
            // Determine if the subscription should be active based on payment status
            $isActive = true;
            if (isset($validated['payment_status']) && $validated['payment_status'] === 'pending') {
                $isActive = false; // For cash payments, subscription is not active until payment is confirmed
            }

            // Create subscription
            $subscription = Subscription::create([
                'user_id' => Auth::id(),
                'type' => $validated['type'],
                'plan' => $validated['plan'],
                'price' => $validated['amount'],
                'start_date' => now(),
                'end_date' => $endDate,
                'is_active' => $isActive,
                'payment_method' => $request->payment_method ?? 'unknown',
                'payment_status' => $request->payment_status ?? 'completed',
                'waiver_accepted' => $request->waiver_accepted ?? false
            ]);

            // Generate invoice
            $subscriptionDetails = ucfirst($validated['type']) . ' - ' . ucfirst($validated['plan']) . ' Plan';
            $this->invoiceController->storeSubscriptionInvoice(
                Auth::id(),
                $subscriptionDetails,
                $validated['amount'],
                $request->payment_status ?? 'completed'
            );

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $isActive ? 
                        'Subscription activated successfully!' : 
                        'Subscription created successfully! Please complete payment at the counter to activate.'
                ]);
            }
            
            $message = $isActive ? 
                'Subscription activated successfully!' : 
                'Subscription created successfully! Please complete payment at the counter to activate.';
                
            return redirect()->route('profile')->with('success', $message);
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage()
                ]);
            }
            
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Show subscription history
     */
    public function history()
    {
        $subscriptions = Subscription::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('subscription.history', compact('subscriptions'));
    }

    /**
     * Cancel a subscription
     */
    public function cancel($id)
    {
        $subscription = Subscription::where('user_id', Auth::id())
            ->where('id', $id)
            ->where('is_active', true)
            ->firstOrFail();

        $subscription->update([
            'is_active' => false,
            'cancelled_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Subscription cancelled successfully!');
    }
} 