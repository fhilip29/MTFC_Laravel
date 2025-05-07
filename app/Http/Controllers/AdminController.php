<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Subscription;
use App\Models\Product;
use App\Models\ContactMessage;
use App\Models\User; // Assuming stats relate to users
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Sessions; // Import the Sessions model
use App\Services\PayMongoService;

class AdminController extends Controller
{
    public function index()
    {
        // Current month and year
        $currentMonth = Carbon::now()->format('F');
        $currentMonthYear = Carbon::now()->format('Y-m');

        // --- Membership Stats ---
        // Active Members
        $activeMembers = User::whereHas('subscriptions', function ($query) {
            $query->where('is_active', true)
                ->where(function($q) {
                    $q->whereNull('end_date')
                      ->orWhere('end_date', '>', Carbon::now());
                });
        })->count();

        // New Signups This Month
        $newSignups = User::whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)
            ->count();

        // Subscription Revenue This Month
        $subscriptionRevenue = Subscription::whereYear('start_date', Carbon::now()->year)
            ->whereMonth('start_date', Carbon::now()->month)
            ->sum('price');

        // Expired Memberships
        $expiredMemberships = Subscription::where('is_active', true)
            ->whereNotNull('end_date')
            ->whereDate('end_date', '<', Carbon::today())
            ->count();

        // Cancelled Memberships
        $cancelledMemberships = Subscription::where('is_active', false)
            ->count();

        // Membership Stats
        $membershipStats = [
            ['label' => 'Active Members', 'count' => $activeMembers, 'icon' => 'fas fa-user-check', 'color' => 'bg-blue-500'],
            ['label' => "New Signups ($currentMonth)", 'count' => $newSignups, 'icon' => 'fas fa-user-plus', 'color' => 'bg-green-500'],
            ['label' => "Subscription Revenue", 'count' => '₱' . number_format($subscriptionRevenue), 'icon' => 'fas fa-credit-card', 'color' => 'bg-indigo-500'],
            ['label' => 'Cancelled Membership', 'count' => $cancelledMemberships, 'icon' => 'fas fa-times-circle', 'color' => 'bg-yellow-500'],
        ];

        // --- Product Stats ---
        // Total Products Sold This Month - Only count from completed orders
        $productsSold = OrderItem::whereHas('order', function($q) {
                $q->where('status', 'Completed')
                  ->whereYear('created_at', Carbon::now()->year)
                  ->whereMonth('created_at', Carbon::now()->month);
            })
            ->sum('quantity');

        // Low Stock Items
        $lowStockItems = Product::where('stock', '<', 10)->count();

        // Top Product
        $topProduct = OrderItem::select('product_id', DB::raw('SUM(quantity) as total_sold'))
            ->whereHas('order', function($q) {
                $q->where('status', 'Completed')
                  ->whereYear('created_at', Carbon::now()->year)
                  ->whereMonth('created_at', Carbon::now()->month);
            })
            ->groupBy('product_id')
            ->orderBy('total_sold', 'desc')
            ->with('product:id,name')
            ->first();
        
        $topProductName = $topProduct && $topProduct->product ? $topProduct->product->name : 'No sales yet';

        // Product Sales This Month - Only from completed orders
        $productSalesAmount = OrderItem::select(DB::raw('SUM(price * quantity) as total_sales'))
            ->whereHas('order', function($q) {
                $q->where('status', 'Completed')
                  ->whereYear('created_at', Carbon::now()->year)
                  ->whereMonth('created_at', Carbon::now()->month);
            })
            ->first();

        // If no sales this month, display 0
        $productSalesAmount = $productSalesAmount ? $productSalesAmount->total_sales : 0;

        // Product Stats
        $productStats = [
            ['label' => "Products Sold ($currentMonth)", 'count' => $productsSold ?: '0', 'icon' => 'fas fa-shopping-cart', 'color' => 'bg-purple-500'],
            ['label' => 'Low Stock Items', 'count' => $lowStockItems, 'icon' => 'fas fa-exclamation-triangle', 'color' => 'bg-red-500'],
            ['label' => 'Top Product', 'count' => $topProductName, 'icon' => 'fas fa-trophy', 'color' => 'bg-amber-500'],
            ['label' => "Product Sales ($currentMonth)", 'count' => '₱' . number_format($productSalesAmount), 'icon' => 'fas fa-shopping-bag', 'color' => 'bg-pink-500'],
        ];

        // --- Fetch Latest Orders ---
        $latestOrders = Order::orderBy('created_at', 'desc')->take(5)->get();

        // --- Fetch Contact Messages ---
        $contactMessages = ContactMessage::orderBy('created_at', 'desc')->take(5)->get();

        // --- Fetch Sales Data (Monthly) ---
        $productSalesData = OrderItem::select(
                DB::raw('SUM(price * quantity) as total_sales'),
                DB::raw('DATE_FORMAT(orders.created_at, "%Y-%m") as month')
            )
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', 'Completed')
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->pluck('total_sales', 'month')
            ->all();

        $subscriptionSalesData = Subscription::select(
                DB::raw('SUM(price) as total_sales'),
                DB::raw('DATE_FORMAT(start_date, "%Y-%m") as month')
            )
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->pluck('total_sales', 'month')
            ->all();

        // Prepare chart data (last 6 months example)
        $chartLabels = [];
        $productSalesChartData = [];
        $subscriptionSalesChartData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i)->format('Y-m');
            $chartLabels[] = Carbon::now()->subMonths($i)->format('M');
            $productSalesChartData[] = isset($productSalesData[$month]) ? $productSalesData[$month] : 0;
            $subscriptionSalesChartData[] = isset($subscriptionSalesData[$month]) ? $subscriptionSalesData[$month] : 0;
        }

        // --- Fetch Attendance Data (Last 7 Days) ---
        $attendanceDataRaw = Sessions::select(
                DB::raw('DATE(time) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->where('status', 'IN')
            ->where('time', '>=', Carbon::now()->subDays(6)->startOfDay()) // Last 7 days including today
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->keyBy('date'); // Key by date for easy lookup

        $attendanceLabels = [];
        $attendanceData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $attendanceLabels[] = Carbon::now()->subDays($i)->format('M d'); // Format like 'Jul 25'
            $attendanceData[] = $attendanceDataRaw->has($date) ? $attendanceDataRaw[$date]->count : 0;
        }


        return view('admin.admin_dashboard', compact(
            'membershipStats',
            'productStats',
            'latestOrders',
            'contactMessages',
            'chartLabels',
            'productSalesChartData',
            'subscriptionSalesChartData',
            'attendanceLabels',
            'attendanceData'
        ));
    }

    public function verifyPayment(Request $request)
    {
        \Log::info('Verifying payment data:', $request->all());
        
        try {
            // For cash payments from QR codes
            if ($request->has('user_id') && $request->has('reference')) {
                // This is a cash payment QR code
                $userId = $request->input('user_id');
                $reference = $request->input('reference');
                $amount = $request->input('amount');
                $paymentType = $request->input('type', 'subscription'); // Default to subscription if not specified
                
                // Find the user
                $user = \App\Models\User::find($userId);
                if (!$user) {
                    return response()->json([
                        'success' => false,
                        'message' => 'User not found'
                    ]);
                }
                
                if ($paymentType === 'subscription') {
                    // Get subscription data from QR
                    $subscriptionType = $request->input('type', 'gym');
                    $subscriptionPlan = $request->input('plan', 'monthly');
                    
                    // Calculate end date based on plan
                    $endDate = now();
                    switch ($subscriptionPlan) {
                        case 'daily':
                            $endDate = $endDate->addDay();
                            break;
                        case 'weekly':
                            $endDate = $endDate->addWeek();
                            break;
                        case 'monthly':
                            $endDate = $endDate->addMonth();
                            break;
                        case 'quarterly':
                            $endDate = $endDate->addMonths(3);
                            break;
                        case 'annual':
                            $endDate = $endDate->addYear();
                            break;
                        default:
                            $endDate = $endDate->addMonth(); // Default to monthly
                    }
                    
                    // Create or update subscription
                    $subscription = \App\Models\Subscription::updateOrCreate(
                        ['payment_reference' => $reference],
                        [
                            'user_id' => $userId,
                            'type' => $subscriptionType,
                            'plan' => $subscriptionPlan,
                            'price' => $amount,
                            'start_date' => now(),
                            'end_date' => $endDate,
                            'is_active' => true,
                            'payment_method' => 'cash',
                            'payment_status' => 'paid',
                            'waiver_accepted' => true
                        ]
                    );
                    
                    // Create invoice
                    $invoiceController = app(\App\Http\Controllers\InvoiceController::class);
                    $subscriptionDetails = ucfirst($subscriptionType) . ' - ' . ucfirst($subscriptionPlan) . ' Plan';
                    $invoiceController->storeSubscriptionInvoice(
                        $userId,
                        $subscriptionDetails,
                        $amount,
                        'completed'
                    );
                    
                    return response()->json([
                        'success' => true,
                        'message' => 'Payment verified successfully for ' . $user->full_name . '\'s ' . ucfirst($subscriptionType) . ' subscription'
                    ]);
                } else if ($paymentType === 'product') {
                    // Product order
                    $items = $request->input('items', []);
                    
                    // Create order
                    $order = new \App\Models\Order([
                        'user_id' => $userId,
                        'order_date' => now(),
                        'status' => 'Completed',
                        'total_amount' => $amount,
                        'payment_method' => 'Cash',
                        'payment_status' => 'Paid',
                        'reference_no' => $reference
                    ]);
                    
                    $order->save();
                    
                    // Create invoice
                    $invoice = new \App\Models\Invoice([
                        'user_id' => $userId,
                        'type' => 'product',
                        'amount' => $amount,
                        'description' => 'Order #' . $order->reference_no,
                        'invoice_number' => 'INV-' . strtoupper(\Illuminate\Support\Str::random(8)),
                        'payment_status' => 'paid',
                        'payment_method' => 'Cash',
                        'payment_reference' => $reference,
                        'paid_at' => now(),
                        'invoice_date' => now()
                    ]);
                    
                    $invoice->save();
                    
                    return response()->json([
                        'success' => true,
                        'message' => 'Payment verified successfully for ' . $user->full_name . '\'s product order'
                    ]);
                }
            }
            
            // For regular payment verifications (PayMongo, etc.)
            $paymentData = $request->validate([
                'reference' => 'required|string',
                'amount' => 'required|numeric',
                'type' => 'required|in:product,subscription',
                'plan' => 'required_if:type,subscription|string',
                'order_id' => 'required_if:type,product|string'
            ]);

            // Verify payment with PayMongo
            $paymongoService = new PayMongoService();
            $payment = $paymongoService->verifyPayment($paymentData['reference']);

            if (!$payment || $payment['status'] !== 'paid') {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment verification failed'
                ]);
            }

            // Handle based on payment type
            if ($paymentData['type'] === 'subscription') {
                // Update subscription status
                $subscription = Subscription::where('payment_reference', $paymentData['reference'])->first();
                if ($subscription) {
                    $subscription->update([
                        'status' => 'active',
                        'payment_status' => 'paid'
                    ]);
                }
            } else {
                // Update order status
                $order = Order::where('id', $paymentData['order_id'])->first();
                if ($order) {
                    $order->update([
                        'status' => 'completed',
                        'payment_status' => 'paid'
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Payment verified successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error('Payment verification error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error verifying payment: ' . $e->getMessage()
            ], 500);
        }
    }
}