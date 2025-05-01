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
        $productSalesAmount = OrderItem::whereHas('order', function($q) {
                $q->where('status', 'Completed')
                  ->whereYear('created_at', Carbon::now()->year)
                  ->whereMonth('created_at', Carbon::now()->month);
            })
            ->sum(DB::raw('price * quantity'));

        // If no sales this month, display 0
        if ($productSalesAmount === null) {
            $productSalesAmount = 0;
        }

        // Product Stats
        $productStats = [
            ['label' => "Products Sold ($currentMonth)", 'count' => $productsSold ? $productsSold : '0', 'icon' => 'fas fa-shopping-cart', 'color' => 'bg-purple-500'],
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
            $productSalesChartData[] = $productSalesData[$month] ?? 0;
            $subscriptionSalesChartData[] = $subscriptionSalesData[$month] ?? 0;
        }

        return view('admin.admin_dashboard', compact(
            'membershipStats',
            'productStats',
            'latestOrders',
            'contactMessages',
            'chartLabels',
            'productSalesChartData',
            'subscriptionSalesChartData'
        ));
    }
}