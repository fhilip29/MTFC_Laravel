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
    public function getUserAttendance(Request $request)
    {
        $user = Auth::user();
        $period = $request->period ?? 'month';
        
        // Different queries based on period
        $query = $user->sessions()->where('status', 'IN');
        
        switch ($period) {
            case 'week':
                $startDate = Carbon::now()->subDays(7);
                $query->where('time', '>=', $startDate);
                $data = $this->getWeeklyAttendanceData($query);
                break;
            case 'month':
                $startDate = Carbon::now()->startOfMonth()->subMonths(1);
                $query->where('time', '>=', $startDate);
                $data = $this->getMonthlyAttendanceData($query);
                break;
            case 'year':
                $startDate = Carbon::now()->startOfYear();
                $query->where('time', '>=', $startDate);
                $data = $this->getYearlyAttendanceData($query);
                break;
            case 'all':
                $data = $this->getAllTimeAttendanceData($query);
                break;
            default:
                $startDate = Carbon::now()->startOfMonth()->subMonths(1);
                $query->where('time', '>=', $startDate);
                $data = $this->getMonthlyAttendanceData($query);
        }
        
        // Add stats
        $data['stats'] = $this->getAttendanceStats($user);
        
        return response()->json($data);
    }
    
    /**
     * Get weekly attendance data
     */
    private function getWeeklyAttendanceData($query)
    {
        $labels = [];
        $checkIns = array_fill(0, 7, 0);
        
        // Create labels for the last 7 days
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $labels[] = $date->format('D'); // Day name
        }
        
        // Get attendance data
        $attendanceData = $query->get()->groupBy(function ($session) {
            return Carbon::parse($session->time)->format('Y-m-d');
        });
        
        // Fill in the data
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            if ($attendanceData->has($date)) {
                $checkIns[6-$i] = $attendanceData[$date]->count();
            }
        }
        
        return [
            'labels' => $labels,
            'checkIns' => $checkIns
        ];
    }
    
    /**
     * Get monthly attendance data
     */
    private function getMonthlyAttendanceData($query)
    {
        // Calculate the number of days in the current month
        $daysInMonth = Carbon::now()->daysInMonth;
        
        $labels = [];
        $checkIns = array_fill(0, $daysInMonth, 0);
        
        // Create labels for each day of the month
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $date = Carbon::now()->startOfMonth()->addDays($i - 1);
            $labels[] = $date->format('d'); // Day of month
        }
        
        // Get attendance data
        $attendanceData = $query->whereMonth('time', Carbon::now()->month)
            ->whereYear('time', Carbon::now()->year)
            ->get()
            ->groupBy(function ($session) {
                return Carbon::parse($session->time)->format('d');
            });
        
        // Fill in the data
        foreach ($attendanceData as $day => $sessions) {
            $dayIndex = (int)$day - 1; // Convert to 0-based index
            if ($dayIndex >= 0 && $dayIndex < $daysInMonth) {
                $checkIns[$dayIndex] = $sessions->count();
            }
        }
        
        return [
            'labels' => $labels,
            'checkIns' => $checkIns
        ];
    }
    
    /**
     * Get yearly attendance data
     */
    private function getYearlyAttendanceData($query)
    {
        $labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $checkIns = array_fill(0, 12, 0);
        
        // Get attendance data for the current year
        $attendanceData = $query->whereYear('time', Carbon::now()->year)
            ->get()
            ->groupBy(function ($session) {
                return Carbon::parse($session->time)->format('m');
            });
        
        // Fill in the data
        foreach ($attendanceData as $month => $sessions) {
            $monthIndex = (int)$month - 1; // Convert to 0-based index
            $checkIns[$monthIndex] = $sessions->count();
        }
        
        return [
            'labels' => $labels,
            'checkIns' => $checkIns
        ];
    }
    
    /**
     * Get all-time attendance data, grouped by month
     */
    private function getAllTimeAttendanceData($query)
    {
        // Get the earliest check-in date
        $earliestSession = $query->orderBy('time', 'asc')->first();
        
        if (!$earliestSession) {
            // No check-ins, return empty data
            return [
                'labels' => [],
                'checkIns' => []
            ];
        }
        
        $startDate = Carbon::parse($earliestSession->time)->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();
        $monthDiff = $startDate->diffInMonths($endDate) + 1;
        
        $labels = [];
        $checkIns = array_fill(0, $monthDiff, 0);
        
        // Generate labels for each month
        for ($i = 0; $i < $monthDiff; $i++) {
            $currentDate = (clone $startDate)->addMonths($i);
            $labels[] = $currentDate->format('M y');
        }
        
        // Group sessions by month
        $attendanceData = $query->get()
            ->groupBy(function ($session) {
                return Carbon::parse($session->time)->format('Y-m');
            });
        
        // Fill in the data
        foreach ($attendanceData as $yearMonth => $sessions) {
            $date = Carbon::createFromFormat('Y-m', $yearMonth)->startOfMonth();
            $index = $startDate->diffInMonths($date);
            if ($index >= 0 && $index < $monthDiff) {
                $checkIns[$index] = $sessions->count();
            }
        }
        
        return [
            'labels' => $labels,
            'checkIns' => $checkIns
        ];
    }
    
    /**
     * Get attendance statistics
     */
    private function getAttendanceStats($user)
    {
        // Calculate this month's attendance
        $thisMonth = $user->sessions()
            ->where('status', 'IN')
            ->whereMonth('time', Carbon::now()->month)
            ->whereYear('time', Carbon::now()->year)
            ->count();
        
        // Calculate last month's attendance
        $lastMonth = $user->sessions()
            ->where('status', 'IN')
            ->whereMonth('time', Carbon::now()->subMonth()->month)
            ->whereYear('time', Carbon::now()->subMonth()->year)
            ->count();
        
        // Calculate total unique days with check-ins
        $totalDays = $user->sessions()
            ->where('status', 'IN')
            ->selectRaw('DATE(time) as date')
            ->distinct()
            ->count();
        
        // Calculate first month of attendance
        $firstSession = $user->sessions()
            ->where('status', 'IN')
            ->orderBy('time', 'asc')
            ->first();
        
        $avgDaysPerMonth = 0;
        if ($firstSession) {
            $firstMonth = Carbon::parse($firstSession->time)->startOfMonth();
            $currentMonth = Carbon::now()->startOfMonth();
            $monthCount = $firstMonth->diffInMonths($currentMonth) + 1;
            
            if ($monthCount > 0) {
                $avgDaysPerMonth = round($totalDays / $monthCount, 1);
            }
        }
        
        return [
            'thisMonth' => $thisMonth,
            'lastMonth' => $lastMonth,
            'totalDays' => $totalDays,
            'avgDaysPerMonth' => $avgDaysPerMonth
        ];
    }

    /**
     * Show detailed attendance records page
     *
     * @return \Illuminate\View\View
     */
    public function showAttendanceDetails(Request $request)
    {
        $user = Auth::user();
        $filter = [
            'date' => $request->input('date'),
            'status' => $request->input('status'),
            'month' => $request->input('month'),
            'year' => $request->input('year'),
        ];
        
        // Build query with filters
        $query = $user->sessions();
        
        // Apply date filter if provided
        if (!empty($filter['date'])) {
            $query->whereDate('time', $filter['date']);
        }
        
        // Apply status filter if provided
        if (!empty($filter['status'])) {
            $query->where('status', $filter['status']);
        }
        
        // Apply month filter if provided
        if (!empty($filter['month'])) {
            $query->whereMonth('time', $filter['month']);
        }
        
        // Apply year filter if provided
        if (!empty($filter['year'])) {
            $query->whereYear('time', $filter['year']);
        }
        
        // Get paginated sessions
        $sessions = $query->orderBy('time', 'desc')->paginate(10);
        
        // Get stats for the view
        $stats = $this->getAttendanceStats($user);
        
        // Get year options for the filter
        $yearOptions = $user->sessions()
            ->selectRaw('YEAR(time) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();
            
        // If empty, add current year
        if (empty($yearOptions)) {
            $yearOptions[] = Carbon::now()->year;
        }
        
        return view('profile.attendance_details', compact('sessions', 'stats', 'filter', 'yearOptions'));
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
