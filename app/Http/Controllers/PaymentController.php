<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\PayMongoService;
use App\Models\Subscription;
use App\Models\Invoice;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\User;

class PaymentController extends Controller
{
    protected $payMongoService;

    public function __construct(PayMongoService $payMongoService)
    {
        $this->payMongoService = $payMongoService;
        $this->middleware('auth')->except(['webhook']);
    }

    /**
     * Show payment method selection page
     */
    public function showPaymentMethods(Request $request)
    {
        // Validate query parameters
        $validated = $request->validate([
            'type' => 'required|string',
            'plan' => 'required|string',
            'amount' => 'required|numeric',
            'waiver_accepted' => 'required|boolean'
        ]);

        return view('payment-method', [
            'type' => $request->type,
            'plan' => $request->plan,
            'amount' => $request->amount,
            'waiver_accepted' => $request->waiver_accepted
        ]);
    }

    /**
     * Process payment
     */
    public function process(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'type' => 'required|string',
            'plan' => 'required|string',
            'amount' => 'required|numeric',
            'waiver_accepted' => 'required',
            'payment_method' => 'required|in:card,gcash,paymaya,paymongo',
            'billing_name' => 'required|string',
            'billing_email' => 'required|email',
            'billing_phone' => 'required|string'
        ]);

        try {
            // Generate a unique reference number for this transaction
            $referenceNumber = 'MTFC-' . strtoupper(Str::random(8));
            
            // Store payment intent reference in session
            session(['payment_reference' => $referenceNumber]);
            session(['subscription_data' => [
                'type' => $request->type,
                'plan' => $request->plan,
                'amount' => $request->amount,
                'waiver_accepted' => $request->waiver_accepted,
            ]]);
            
            // If payment method is PayMongo, create a checkout session
            if ($request->payment_method === 'paymongo') {
                // Create checkout session/link
                $description = $request->type . ' - ' . $request->plan . ' Plan';
                $billingDetails = [
                    'name' => $request->billing_name,
                    'email' => $request->billing_email,
                    'phone' => $request->billing_phone
                ];
                
                $successUrl = route('payment.success');
                $failureUrl = route('payment.failed');
                
                $checkoutSession = $this->payMongoService->createCheckoutSession(
                    $request->amount,
                    $description,
                    $billingDetails,
                    $successUrl,
                    $failureUrl,
                    $referenceNumber
                );
                
                if (!isset($checkoutSession['data']['attributes']['checkout_url'])) {
                    Log::error('PayMongo checkout session creation failed', $checkoutSession);
                    return redirect()->back()->with('error', 'Failed to create checkout session');
                }
                
                // Show the checkout URL instead of redirecting
                $checkoutUrl = $checkoutSession['data']['attributes']['checkout_url'];
                return view('payment.paymongo-link', [
                    'checkout_url' => $checkoutUrl,
                    'type' => $request->type,
                    'plan' => $request->plan,
                    'amount' => $request->amount,
                    'reference' => $referenceNumber
                ]);
            }
            
            // Legacy payment flow for other payment methods
            // 1. Create payment method
            $paymentMethod = $this->payMongoService->createPaymentMethod($request->payment_method, [
                'billing' => [
                    'name' => $request->billing_name,
                    'email' => $request->billing_email,
                    'phone' => $request->billing_phone
                ]
            ]);

            if (!isset($paymentMethod['data']['id'])) {
                Log::error('PayMongo payment method creation failed', $paymentMethod);
                return redirect()->back()->with('error', 'Failed to create payment method');
            }

            $paymentMethodId = $paymentMethod['data']['id'];

            // 2. Create intent
            $intent = $this->payMongoService->createPaymentIntent(
                $request->amount,
                $referenceNumber
            );

            if (!isset($intent['data']['id'])) {
                Log::error('PayMongo payment intent creation failed', $intent);
                return redirect()->back()->with('error', 'Failed to create payment intent');
            }

            $intentId = $intent['data']['id'];

            // 3. Attach method
            $payment = $this->payMongoService->attachPaymentMethod($intentId, $paymentMethodId);

            if (!isset($payment['data']['attributes']['next_action']['redirect']['url'])) {
                Log::error('PayMongo payment attach method failed', $payment);
                return redirect()->back()->with('error', 'Failed to process payment');
            }

            // Show the checkout URL instead of redirecting
            $checkoutUrl = $payment['data']['attributes']['next_action']['redirect']['url'];
            return view('payment.paymongo-link', [
                'checkout_url' => $checkoutUrl,
                'type' => $request->type,
                'plan' => $request->plan,
                'amount' => $request->amount,
                'reference' => $referenceNumber
            ]);

        } catch (\Exception $e) {
            Log::error('Payment processing error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Handle payment success
     */
    public function success(Request $request)
    {
        // Get stored reference number
        $referenceNumber = session('payment_reference');
        $subscriptionData = session('subscription_data', []);
        
        if (empty($referenceNumber) || empty($subscriptionData)) {
            return redirect()->route('pricing.gym')->with('error', 'Payment reference not found');
        }
        
        try {
            // Clear session data
            session()->forget(['payment_reference', 'subscription_data']);
            
            // Create subscription record
            $subscription = Subscription::create([
                'user_id' => auth()->id(),
                'type' => $subscriptionData['type'],
                'plan' => $subscriptionData['plan'],
                'is_active' => true,
                'start_date' => now(),
                'end_date' => $this->calculateEndDate($subscriptionData['plan']),
                'amount' => $subscriptionData['amount'],
                'payment_reference' => $referenceNumber,
                'payment_status' => 'paid',
                'waiver_accepted' => $subscriptionData['waiver_accepted']
            ]);
            
            // Create invoice record
            Invoice::create([
                'user_id' => auth()->id(),
                'subscription_id' => $subscription->id,
                'amount' => $subscriptionData['amount'],
                'description' => "{$subscriptionData['type']} - {$subscriptionData['plan']} Plan",
                'invoice_number' => 'INV-' . strtoupper(Str::random(8)),
                'payment_status' => 'paid',
                'payment_method' => 'PayMongo',
                'payment_reference' => $referenceNumber,
                'paid_at' => now()
            ]);
            
            return redirect()->route('profile')->with('success', 'Your subscription has been activated successfully!');
            
        } catch (\Exception $e) {
            Log::error('Subscription creation error: ' . $e->getMessage());
            $type = $subscriptionData['type'] ?? 'gym';
            $route = 'pricing.' . $type;
            
            // Default to gym if the route doesn't exist
            if (!in_array($type, ['gym', 'boxing', 'muay', 'jiu'])) {
                $route = 'pricing.gym';
            }
            
            return redirect()->route($route)->with('error', 'Your payment was successful, but we encountered an error while activating your subscription. Please contact support.');
        }
    }

    /**
     * Handle payment failure
     */
    public function failed(Request $request)
    {
        // Get subscription type from session
        $subscriptionData = session('subscription_data', []);
        $type = $subscriptionData['type'] ?? 'gym';
        $route = 'pricing.' . $type;
        
        // Default to gym if the route doesn't exist
        if (!in_array($type, ['gym', 'boxing', 'muay', 'jiu'])) {
            $route = 'pricing.gym';
        }
        
        // Clear payment reference from session
        session()->forget(['payment_reference', 'subscription_data']);
        
        return redirect()->route($route)->with('error', 'Payment was not successful. Please try again.');
    }

    /**
     * Calculate end date based on plan
     */
    private function calculateEndDate($plan)
    {
        switch (strtolower($plan)) {
            case 'daily':
                return now()->addDay();
            case 'weekly':
                return now()->addWeek();
            case 'monthly':
                return now()->addMonth();
            case 'quarterly':
                return now()->addMonths(3);
            case 'annual':
                return now()->addYear();
            default:
                return now()->addMonth(); // Default to monthly
        }
    }

    /**
     * Webhook handler for PayMongo events
     */
    public function webhook(Request $request)
    {
        // Verify webhook signature (in production)
        $payload = $request->getContent();
        $event = json_decode($payload, true);
        
        // Log the event
        Log::info('PayMongo webhook received', ['event' => $event['type'] ?? 'unknown']);
        
        // Handle different event types
        switch ($event['type'] ?? '') {
            case 'payment.paid':
                // Payment successful - handled by success route
                break;
                
            case 'payment.failed':
                // Payment failed - handle any necessary cleanup
                // Find subscription by reference number and mark as failed
                $referenceNumber = $event['data']['attributes']['reference_number'] ?? null;
                if ($referenceNumber) {
                    Subscription::where('payment_reference', $referenceNumber)
                        ->update(['payment_status' => 'failed']);
                }
                break;
                
            // Handle other event types as needed
        }
        
        return response()->json(['success' => true]);
    }

    /**
     * Generate Cash QR Code
     */
    public function generateQRCode(Request $request)
    {
        // Validate request
        $validated = $request->validate([
            'type' => 'required|string',
            'plan' => 'required|string',
            'amount' => 'required|numeric',
            'waiver_accepted' => 'required',
            'payment_method' => 'required|in:cash',
            'order_data' => 'nullable'
        ]);
        
        try {
            // Generate a unique reference number
            $referenceNumber = 'MTFC-' . strtoupper(Str::random(8));
            
            // Store the reference in the session
            session(['payment_reference' => $referenceNumber]);
            session(['order_data' => $request->order_data ?? null]);
            session(['subscription_data' => [
                'type' => $request->type,
                'plan' => $request->plan,
                'amount' => $request->amount,
                'waiver_accepted' => $request->waiver_accepted,
            ]]);
            
            return redirect()->route('payment.cash.qr.show', $referenceNumber);
            
        } catch (\Exception $e) {
            Log::error('Cash QR generation error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to generate payment QR code');
        }
    }
    
    /**
     * Show QR code for cash payment
     */
    public function showCashQr($reference)
    {
        // Get payment data from session
        $paymentReference = session('payment_reference');
        $subscriptionData = session('subscription_data');
        $orderData = session('order_data');
        
        // Check if payment reference exists and matches
        if (!$paymentReference || $paymentReference !== $reference) {
            return redirect()->route('pricing.gym')->with('error', 'Invalid payment reference');
        }
        
        // Determine the type of payment (subscription or product)
        $paymentType = isset($orderData['items']) ? 'product' : 'subscription';
        $userId = auth()->id();
        $userName = auth()->user()->name;
        
        // Create payment data
        $paymentData = [
            'reference' => $reference,
            'user_id' => $userId,
            'user_name' => $userName,
            'created_at' => now()->timestamp
        ];
        
        if ($paymentType === 'subscription') {
            $paymentData = array_merge($paymentData, [
                'type' => $subscriptionData['type'] ?? 'gym',
                'plan' => $subscriptionData['plan'] ?? 'monthly',
                'amount' => $subscriptionData['amount'] ?? 0,
                'waiver_accepted' => $subscriptionData['waiver_accepted'] ?? true,
            ]);
        } else {
            // Product purchase data
            $paymentData = array_merge($paymentData, [
                'type' => 'product',
                'amount' => $orderData['amount'] ?? 0,
                'items' => $orderData['items'] ?? []
            ]);
        }
        
        // Pass data to view
        return view('payment.cash-qr', [
            'paymentData' => $paymentData,
            'qrContent' => json_encode($paymentData)
        ]);
    }
    
    /**
     * Verify cash payment from QR scan
     */
    public function verifyCashPayment(Request $request)
    {
        $request->validate([
            'qr_data' => 'required|string'
        ]);
        
        try {
            // Decode QR data
            $qrData = json_decode($request->qr_data, true);
            
            if (!$qrData || !isset($qrData['reference']) || !isset($qrData['user_id'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid QR code data'
                ]);
            }
            
            // Find the user
            $user = User::find($qrData['user_id']);
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ]);
            }
            
            // Create subscription with active status
            $endDate = null;
            switch ($qrData['plan']) {
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
                default:
                    $endDate = now()->addMonth(); // Default to monthly
            }
            
            $subscription = Subscription::create([
                'user_id' => $qrData['user_id'],
                'type' => $qrData['type'],
                'plan' => $qrData['plan'],
                'price' => $qrData['amount'],
                'start_date' => now(),
                'end_date' => $endDate,
                'is_active' => true, // Activate the subscription
                'payment_method' => 'cash',
                'payment_status' => 'completed',
                'payment_reference' => $qrData['reference'],
                'waiver_accepted' => true
            ]);
            
            // Create invoice for the subscription
            $this->invoiceController = app(InvoiceController::class);
            $subscriptionDetails = ucfirst($qrData['type']) . ' - ' . ucfirst($qrData['plan']) . ' Plan';
            $this->invoiceController->storeSubscriptionInvoice(
                $qrData['user_id'],
                $subscriptionDetails,
                $qrData['amount'],
                'completed'
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Payment verified successfully',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email
                ],
                'subscription' => [
                    'id' => $subscription->id,
                    'type' => $subscription->type,
                    'plan' => $subscription->plan,
                    'amount' => $subscription->price,
                    'start_date' => $subscription->start_date,
                    'end_date' => $subscription->end_date
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('QR payment verification error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error verifying payment: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Process cash payment from client-side QR scanning
     */
    public function processCashPayment(Request $request)
    {
        try {
            // Validate request data
            $validated = $request->validate([
                'qr_code' => 'required|string',
                'reference' => 'required|string',
                'amount' => 'required|numeric',
                'order_data' => 'required',
                'payment_method' => 'required|string'
            ]);
            
            $orderData = $request->order_data;
            
            // For product purchases
            if (isset($orderData['items']) && is_array($orderData['items']) && count($orderData['items']) > 0) {
                // Create a new order
                $order = new \App\Models\Order([
                    'user_id' => auth()->id(),
                    'order_date' => now(),
                    'status' => 'Pending',
                    'total_amount' => $request->amount,
                    'payment_method' => 'Cash',
                    'payment_status' => 'Paid',
                    'reference_no' => 'ORD-' . strtoupper(Str::random(8)),
                    'street' => $orderData['shipping']['street'] ?? '',
                    'barangay' => $orderData['shipping']['barangay'] ?? '',
                    'city' => $orderData['shipping']['city'] ?? '',
                    'postal_code' => $orderData['shipping']['postal_code'] ?? '',
                    'phone_number' => $orderData['shipping']['phone_number'] ?? '',
                    'notes' => $orderData['shipping']['notes'] ?? '',
                ]);
                
                $order->save();
                
                // Add order items
                foreach ($orderData['items'] as $item) {
                    $orderItem = new \App\Models\OrderItem([
                        'order_id' => $order->id,
                        'product_id' => $item['id'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                        'discount' => $item['discount'] ?? 0
                    ]);
                    
                    $orderItem->save();
                }
                
                // Create invoice
                $invoice = new Invoice([
                    'user_id' => auth()->id(),
                    'type' => 'product',
                    'amount' => $request->amount,
                    'description' => 'Order #' . $order->reference_no,
                    'invoice_number' => 'INV-' . strtoupper(Str::random(8)),
                    'payment_status' => 'paid',
                    'payment_method' => 'Cash',
                    'payment_reference' => $request->reference,
                    'paid_at' => now(),
                    'invoice_date' => now()
                ]);
                
                $invoice->save();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Payment successful. Your order has been placed!',
                    'order_id' => $order->id,
                    'reference' => $order->reference_no
                ]);
            }
            // For subscriptions
            else if (isset($orderData['type']) && isset($orderData['plan'])) {
                // Create subscription
                $subscription = new Subscription([
                    'user_id' => auth()->id(),
                    'type' => $orderData['type'],
                    'plan' => $orderData['plan'],
                    'is_active' => true,
                    'start_date' => now(),
                    'end_date' => $this->calculateEndDate($orderData['plan']),
                    'amount' => $request->amount,
                    'payment_reference' => $request->reference,
                    'payment_status' => 'paid',
                    'waiver_accepted' => $orderData['waiver_accepted'] ?? false
                ]);
                
                $subscription->save();
                
                // Create invoice
                $invoice = new Invoice([
                    'user_id' => auth()->id(),
                    'subscription_id' => $subscription->id,
                    'type' => 'subscription',
                    'amount' => $request->amount,
                    'description' => "{$orderData['type']} - {$orderData['plan']} Plan",
                    'invoice_number' => 'INV-' . strtoupper(Str::random(8)),
                    'payment_status' => 'paid',
                    'payment_method' => 'Cash',
                    'payment_reference' => $request->reference,
                    'paid_at' => now(),
                    'invoice_date' => now()
                ]);
                
                $invoice->save();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Payment successful. Your subscription has been activated!',
                    'subscription_id' => $subscription->id
                ]);
            }
            else {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid order data format'
                ], 400);
            }
            
        } catch (\Exception $e) {
            Log::error('Cash payment processing error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to process payment: ' . $e->getMessage()
            ], 500);
        }
    }
}
