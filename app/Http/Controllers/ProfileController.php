<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Subscription;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;

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
            
            // Ensure user has a QR code
            if (!$user->qr_code) {
                $user->qr_code = Str::uuid();
                $user->save();
            }
            
            // Log user information for debugging
            \Log::info('User profile data:', [
                'id' => $user->id,
                'profile_image' => $user->profile_image,
                'image_exists' => $user->profile_image ? file_exists(public_path($user->profile_image)) : false,
                'qr_code' => $user->qr_code
            ]);
            
            // Simple and direct approach to get active subscription
            $activeSubscription = Subscription::where('user_id', $user->id)
                ->where('is_active', true)
                ->where(function($query) {
                    $query->whereNull('end_date')
                          ->orWhereDate('end_date', '>=', now());
                })
                ->orderBy('created_at', 'desc')
                ->first();
            
            // Get all invoices for this user
            $invoices = Invoice::where('user_id', $user->id)
                ->with('items')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
            
            // Log data
            \Log::info('User ID: ' . $user->id);
            \Log::info('Active subscription: ' . ($activeSubscription ? 'Yes' : 'No'));
            \Log::info('Invoices count: ' . $invoices->count());
            
            return view('profile', [
                'user' => $user,
                'activeSubscription' => $activeSubscription,
                'invoices' => $invoices
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in profile page: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            return view('profile', [
                'user' => Auth::user(),
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
     * Generate QR code image for user identification
     * 
     * @return \Illuminate\Http\Response
     */
    public function generateQrImage()
    {
        $user = Auth::user();
        $userId = $user->id;
        $userRole = $user->role;
        
        // Create QR code with user ID and role
        $qrCode = QrCode::format('png')
            ->size(300)
            ->errorCorrection('H')
            ->generate(json_encode([
                'id' => $userId,
                'role' => $userRole,
                'timestamp' => time()
            ]));
            
        return response($qrCode)
            ->header('Content-Type', 'image/png');
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

    /**
     * Debug QR code generation
     * 
     * @return \Illuminate\View\View
     */
    public function debugQrCode()
    {
        $user = Auth::user();
        
        if (!$user->qr_code) {
            // Generate a new QR code if one doesn't exist
            $user->qr_code = Str::uuid();
            $user->save();
        }
        
        $qrCodeData = [
            'qr_code' => $user->qr_code,
            'id' => $user->id,
            'role' => $user->role
        ];
        
        return response()->json($qrCodeData);
    }
}
