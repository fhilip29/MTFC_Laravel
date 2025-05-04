<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Subscription;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the user profile.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return redirect()->route('login');
            }
            
            // Simple and direct approach to get active subscription
            $activeSubscription = DB::table('subscriptions')
                ->where('user_id', $user->id)
                ->where('is_active', true)
                ->where(function($query) {
                    $query->whereNull('end_date')
                          ->orWhereDate('end_date', '>=', now());
                })
                ->orderBy('created_at', 'desc')
                ->first();
            
            // Get all invoices for this user
            $invoices = DB::table('invoices')
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
            
            // Transform invoices to expected format with their items
            $formattedInvoices = collect();
            foreach ($invoices as $invoice) {
                // Get items for this invoice
                $items = DB::table('invoice_items')
                    ->where('invoice_id', $invoice->id)
                    ->get();
                
                // Format items
                $formattedItems = collect();
                foreach ($items as $item) {
                    $formattedItems->push([
                        'description' => $item->description,
                        'amount' => $item->amount
                    ]);
                }
                
                // Create formatted invoice object
                $formattedInvoice = (object)[
                    'id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'user_id' => $invoice->user_id,
                    'type' => $invoice->type,
                    'total_amount' => $invoice->total_amount,
                    'invoice_date' => $invoice->invoice_date,
                    'created_at' => $invoice->created_at,
                    'updated_at' => $invoice->updated_at,
                    'items' => $formattedItems
                ];
                
                $formattedInvoices->push($formattedInvoice);
            }
            
            // Log data
            \Log::info('User ID: ' . $user->id);
            \Log::info('Active subscription: ' . ($activeSubscription ? 'Yes' : 'No'));
            \Log::info('Invoices count: ' . count($formattedInvoices));
            
            return view('profile', [
                'activeSubscription' => $activeSubscription,
                'invoices' => $formattedInvoices
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in profile page: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            return view('profile', [
                'activeSubscription' => null,
                'invoices' => collect(),
                'error' => 'There was an error loading your profile data.'
            ]);
        }
    }

    /**
     * Show the user's QR code in fullscreen.
     *
     * @return \Illuminate\View\View
     */
    public function showQrCode()
    {
        $user = Auth::user();
        $role = $user->role; // Add role to pass to the view
        return view('profile.show_qr', compact('user', 'role'));
    }
    
    /**
     * Get user attendance data for charts
     */
    public function getUserAttendance()
    {
        $user = Auth::user();
        $currentYear = Carbon::now()->year;
        
        // Get all sessions for the current year
        $sessions = $user->sessions()
            ->whereYear('time', $currentYear)
            ->get();
            
        // Initialize arrays for all months
        $checkIns = array_fill(0, 12, 0);
        $checkOuts = array_fill(0, 12, 0);
        
        // Process sessions data
        foreach ($sessions as $session) {
            $date = Carbon::parse($session->time);
            $monthIndex = $date->month - 1; // Zero-based index (Jan = 0)
            
            if ($session->status === 'IN') {
                $checkIns[$monthIndex]++;
            } elseif ($session->status === 'OUT') {
                $checkOuts[$monthIndex]++;
            }
        }
        
        return response()->json([
            'checkIns' => $checkIns,
            'checkOuts' => $checkOuts,
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
        ]);
    }

    /**
     * Get user attendance dates for the calendar
     */
    public function getAttendanceDates()
    {
        $user = Auth::user();
        
        // Get distinct dates where the user had a session (IN or OUT)
        $dates = $user->sessions()
            ->selectRaw('DATE(time) as attendance_date') // Select only the date part
            ->distinct() // Get unique dates
            ->orderBy('attendance_date', 'asc') // Order by date
            ->pluck('attendance_date') // Get only the date strings
            ->map(function ($date) {
                // Ensure the date is formatted as YYYY-MM-DD
                return Carbon::parse($date)->format('Y-m-d');
            })
            ->toArray(); // Convert to array
            
        return response()->json($dates);
    }
}
