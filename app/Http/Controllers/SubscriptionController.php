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
            $validated = $request->validate([
                'type' => 'required|string|in:gym,boxing,muay,jiu',
                'plan' => 'required|string|in:daily,monthly,quarterly,annual',
                'amount' => 'required|numeric|min:0',
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

            // Create subscription
            $subscription = Subscription::create([
                'user_id' => Auth::id(),
                'type' => $validated['type'],
                'plan' => $validated['plan'],
                'price' => $validated['amount'],
                'start_date' => now(),
                'end_date' => $endDate,
                'is_active' => true,
            ]);

            // Generate invoice
            $subscriptionDetails = ucfirst($validated['type']) . ' - ' . ucfirst($validated['plan']) . ' Plan';
            $this->invoiceController->storeSubscriptionInvoice(
                Auth::id(),
                $subscriptionDetails,
                $validated['amount']
            );

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Subscription activated successfully!'
                ]);
            }

            return redirect()->back()->with('success', 'Subscription activated successfully!');
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