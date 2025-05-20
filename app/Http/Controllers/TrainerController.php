<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Trainer;
use App\Models\User;
use App\Models\TrainerSchedule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\FileUploadController;

class TrainerController extends Controller
{
    // Admin Trainer Page
    public function indexAdmin()
    {
        // Get filter parameter, default to 'all'
        $filter = request('filter', 'all');
        
        // Start with a base query
        $query = Trainer::with(['user', 'schedules']);
        
        // Apply filters based on request
        if ($filter === 'archived') {
            $query->whereHas('user', function($q) {
                $q->where('is_archived', true);
            });
        } elseif ($filter === 'active') {
            $query->whereHas('user', function($q) {
                $q->where('is_archived', false);
            });
        } elseif ($filter !== 'all') {
            // Sport-specific filter (check if it's a valid sport)
            $sport = \App\Models\Sport::where('slug', $filter)->first();
            if ($sport) {
                // Filter trainers by the selected sport
                $query->where('instructor_for', 'like', "%{$filter}%");
                // Also only show active trainers
                $query->whereHas('user', function($q) {
                    $q->where('is_archived', false);
                });
            } else {
                // Default behavior - show active trainers only
                $query->whereHas('user', function($q) {
                    $q->where('is_archived', false);
                });
            }
        } else {
            // Default behavior - show active trainers only
            $query->whereHas('user', function($q) {
                $q->where('is_archived', false);
            });
        }
        
        // Execute the query
        $trainers = $query->get();
        
        // Get all active sports for the filter dropdown
        $sports = \App\Models\Sport::where('is_active', true)
            ->orderBy('display_order')
            ->get();
        
        // Format the trainer data for display
        $trainers->each(function ($trainer) {
            // Get weekly schedule
            $weeklySchedule = [];
            foreach ($trainer->schedules as $schedule) {
                $day = substr($schedule->day_of_week, 0, 3); // Mon, Tue, etc.
                $startTime = date('ga', strtotime($schedule->start_time)); // 9AM
                $endTime = date('ga', strtotime($schedule->end_time)); // 5PM
                $weeklySchedule[$day] = "$startTime - $endTime";
            }
            
            // Add formatted schedule to trainer
            $trainer->formatted_schedule = $weeklySchedule;
            
            // Count active clients whose subscription type matches the trainer's instruction types
            $instructedTypes = explode(',', $trainer->instructor_for); // Assuming instructor_for is comma-separated
            $instructedTypes = array_map('trim', $instructedTypes);

            // Map instructor_for values to subscription type values
            $mappedTypes = [];
            foreach ($instructedTypes as $type) {
                $mappedTypes[] = $type; // Use as-is since now we're using the same slug format
            }

            $trainer->instructed_clients_count = User::whereHas('activeSubscriptions', function($query) use ($mappedTypes) {
                $query->whereIn('type', $mappedTypes);
            })->where('role', 'member') // Count only members
              ->where('is_archived', false) // Only non-archived users
              ->count();

            // Count active subscriptions for the trainer's own user account
            if ($trainer->user) {
                $trainer->trainer_active_subscriptions_count = $trainer->user->activeSubscriptions()->count();
            } else {
                $trainer->trainer_active_subscriptions_count = 0;
            }
        });
        
        return view('admin.trainer.admin_trainer', compact('trainers', 'filter', 'sports'));
    }

    // Store a new trainer
    public function store(Request $request)
    {
        // Log the incoming request for debugging
        \Log::info('Trainer store request received', [
            'has_file' => $request->hasFile('profile_image'),
            'has_base64' => $request->has('profile_image_base64'),
            'all_inputs' => $request->except(['password', 'profile_image_base64']), // Don't log sensitive data
        ]);

        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'mobile_number' => 'nullable|string|max:15|unique:users,mobile_number',
            'specialization' => 'required|string|max:255',
            'instructor_for' => 'required|string',
            'short_intro' => 'nullable|string',
            'profile_image' => 'nullable|file|image|max:5120', // 5MB max size
            'profile_image_base64' => 'nullable|string',
            'other_gender' => 'nullable|string|required_if:gender,other',
            'hired_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            \Log::warning('Trainer validation failed', [
                'errors' => $validator->errors()->toArray()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Create a new user
            $user = new User();
            $user->full_name = $request->full_name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->mobile_number = $request->mobile_number;
            
            // Handle gender - store additional info if 'other' is selected
            $user->gender = $request->has('gender') ? $request->gender : null;
            if ($request->gender === 'other' && $request->has('other_gender')) {
                $user->other_gender = $request->other_gender;
            }
            
            $user->role = 'trainer';
            $user->is_agreed_to_terms = true; // Setting as true since admin is creating
            
            // Generate QR code for the trainer
            try {
                // Generate a unique identifier for the QR code
                $uniqueId = uniqid('trainer_', true);
                
                // Create QR code data (e.g., user ID, name, and unique identifier)
                $qrData = json_encode([
                    'id' => $uniqueId,
                    'email' => $request->email,
                    'created_at' => now()->format('Y-m-d H:i:s')
                ]);
                
                // Encrypt QR data for security
                $encryptedData = base64_encode($qrData);
                
                // Set the QR code value
                $user->qr_code = $encryptedData;
            } catch (\Exception $e) {
                \Log::error('Error generating QR code: ' . $e->getMessage());
                // Continue even if QR code generation fails
            }
            
            $user->save();

            // Create trainer profile
            $trainer = new Trainer();
            $trainer->user_id = $user->id;
            $trainer->specialization = $request->specialization;
            $trainer->instructor_for = $request->instructor_for;
            $trainer->short_intro = $request->short_intro;
            
            // Handle hired_date - default to today if not provided
            $trainer->hired_date = $request->filled('hired_date') ? $request->hired_date : now()->format('Y-m-d');
            
            // Handle profile image upload
            if ($request->hasFile('profile_image')) {
                try {
                    $file = $request->file('profile_image');
                    $filename = time() . '_' . $file->getClientOriginalName();
                    
                    // Create directory if it doesn't exist
                    $directory = public_path('images/trainers');
                    if (!file_exists($directory)) {
                        mkdir($directory, 0755, true);
                    }
                    
                    // Move the file to public/images/trainers
                    $file->move($directory, $filename);
                    $trainer->profile_url = 'images/trainers/' . $filename;
                    
                    \Log::info('Saved profile image at: ' . $trainer->profile_url);
                } catch (\Exception $e) {
                    \Log::error('Error handling profile image: ' . $e->getMessage());
                    \Log::error($e->getTraceAsString());
                }
            } 
            // Handle cropped base64 image
            elseif ($request->has('profile_image_base64')) {
                try {
                    $base64Image = $request->profile_image_base64;
                    
                    // Extract the actual base64 string
                    if (strpos($base64Image, ';base64,') !== false) {
                        list(, $base64Image) = explode(';base64,', $base64Image);
                    }
                    
                    // Create directory if it doesn't exist
                    $directory = public_path('images/trainers');
                    if (!file_exists($directory)) {
                        mkdir($directory, 0755, true);
                    }
                    
                    // Generate a unique filename
                    $filename = 'cropped_' . time() . '.jpg';
                    $path = $directory . '/' . $filename;
                    
                    // Store the file
                    file_put_contents($path, base64_decode($base64Image));
                    $trainer->profile_url = 'images/trainers/' . $filename;
                    
                    \Log::info('Saved cropped profile image at: ' . $trainer->profile_url);
                } catch (\Exception $e) {
                    \Log::error('Error handling cropped profile image: ' . $e->getMessage());
                    \Log::error($e->getTraceAsString());
                }
            }
            
            $trainer->save();

            // Create trainer schedules if provided
            if ($request->has('schedule')) {
                foreach ($request->schedule as $day => $hours) {
                    if (isset($hours['start']) && isset($hours['end'])) {
                        TrainerSchedule::create([
                            'trainer_id' => $trainer->id,
                            'day_of_week' => $day,
                            'start_time' => $hours['start'],
                            'end_time' => $hours['end']
                        ]);
                    }
                }
            }
            
            // Sync with sports table - add this trainer to each sport in instructor_for
            $sportSlugs = explode(',', $request->instructor_for);
            $sportSlugs = array_map('trim', $sportSlugs);
            
            $sportsToSync = \App\Models\Sport::whereIn('slug', $sportSlugs)->pluck('id')->toArray();
            if (!empty($sportsToSync)) {
                $user->specialtySports()->sync($sportsToSync);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Trainer created successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating trainer: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error creating trainer: ' . $e->getMessage()
            ], 500);
        }
    }

    // Update an existing trainer
    public function update(Request $request, $id)
    {
        // Log the incoming request for debugging
        \Log::info('Trainer update request received', [
            'id' => $id,
            'has_file' => $request->hasFile('profile_image'),
            'has_base64' => $request->has('profile_image_base64'),
            'all_inputs' => $request->except(['password', 'profile_image_base64']), // Don't log sensitive data
        ]);

        $trainer = Trainer::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $trainer->user_id,
            'mobile_number' => 'nullable|string|max:15|unique:users,mobile_number,' . $trainer->user_id,
            'specialization' => 'required|string|max:255',
            'instructor_for' => 'required|string',
            'short_intro' => 'nullable|string',
            'profile_image' => 'nullable|file|image|max:5120', // 5MB max size
            'profile_image_base64' => 'nullable|string',
            'other_gender' => 'nullable|string|required_if:gender,other',
            'hired_date' => 'nullable|date',
            'resigned_date' => 'nullable|date|after_or_equal:hired_date',
        ]);

        if ($validator->fails()) {
            \Log::warning('Trainer update validation failed', [
                'errors' => $validator->errors()->toArray()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Update user
            $user = User::findOrFail($trainer->user_id);
            $user->full_name = $request->full_name;
            $user->email = $request->email;
            $user->mobile_number = $request->mobile_number;
            
            // Handle gender - store additional info if 'other' is selected
            if ($request->has('gender')) {
                $user->gender = $request->gender;
                
                if ($request->gender === 'other' && $request->has('other_gender')) {
                    $user->other_gender = $request->other_gender;
                }
            }
            
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }
            
            // Check if user has a QR code, if not, generate one
            if (empty($user->qr_code)) {
                try {
                    // Generate a unique identifier for the QR code
                    $uniqueId = uniqid('trainer_', true);
                    
                    // Create QR code data
                    $qrData = json_encode([
                        'id' => $uniqueId,
                        'email' => $user->email,
                        'created_at' => now()->format('Y-m-d H:i:s')
                    ]);
                    
                    // Encrypt QR data for security
                    $encryptedData = base64_encode($qrData);
                    
                    // Set the QR code value
                    $user->qr_code = $encryptedData;
                } catch (\Exception $e) {
                    \Log::error('Error generating QR code during update: ' . $e->getMessage());
                    // Continue even if QR code generation fails
                }
            }
            
            $user->save();

            // Update trainer profile
            $trainer->specialization = $request->specialization;
            
            // Store the old instructor_for value to check for changes
            $oldInstructorFor = $trainer->instructor_for;
            
            // Update instructor_for field
            $trainer->instructor_for = $request->instructor_for;
            $trainer->short_intro = $request->short_intro;
            
            // Handle hired_date and resigned_date
            if ($request->filled('hired_date')) {
                $trainer->hired_date = $request->hired_date;
            } elseif (!$trainer->hired_date) {
                $trainer->hired_date = now()->format('Y-m-d');
            }
            
            if ($request->filled('resigned_date')) {
                $trainer->resigned_date = $request->resigned_date;
            }
            
            // Handle profile image upload if provided
            if ($request->hasFile('profile_image')) {
                try {
                    $file = $request->file('profile_image');
                    $filename = time() . '_' . $file->getClientOriginalName();
                    
                    // Create directory if it doesn't exist
                    $directory = public_path('images/trainers');
                    if (!file_exists($directory)) {
                        mkdir($directory, 0755, true);
                    }
                    
                    // Delete old image if it exists and is not a default image
                    if ($trainer->profile_url && !str_contains($trainer->profile_url, 'default_profile')) {
                        $oldImagePath = public_path($trainer->profile_url);
                        if (file_exists($oldImagePath)) {
                            unlink($oldImagePath);
                        }
                    }
                    
                    // Move the file to public/images/trainers
                    $file->move($directory, $filename);
                    $trainer->profile_url = 'images/trainers/' . $filename;
                    
                    \Log::info('Saved profile image at: ' . $trainer->profile_url);
                } catch (\Exception $e) {
                    \Log::error('Error handling profile image: ' . $e->getMessage());
                    \Log::error($e->getTraceAsString());
                }
            }
            // Handle cropped base64 image
            elseif ($request->has('profile_image_base64')) {
                try {
                    $base64Image = $request->profile_image_base64;
                    
                    // Extract the actual base64 string
                    if (strpos($base64Image, ';base64,') !== false) {
                        list(, $base64Image) = explode(';base64,', $base64Image);
                    }
                    
                    // Create directory if it doesn't exist
                    $directory = public_path('images/trainers');
                    if (!file_exists($directory)) {
                        mkdir($directory, 0755, true);
                    }
                    
                    // Delete old image if it exists and is not a default image
                    if ($trainer->profile_url && !str_contains($trainer->profile_url, 'default_profile')) {
                        $oldImagePath = public_path($trainer->profile_url);
                        if (file_exists($oldImagePath)) {
                            unlink($oldImagePath);
                        }
                    }
                    
                    // Generate a unique filename
                    $filename = 'cropped_' . time() . '.jpg';
                    $path = $directory . '/' . $filename;
                    
                    // Store the file
                    file_put_contents($path, base64_decode($base64Image));
                    $trainer->profile_url = 'images/trainers/' . $filename;
                    
                    \Log::info('Saved cropped profile image at: ' . $trainer->profile_url);
                } catch (\Exception $e) {
                    \Log::error('Error handling cropped profile image: ' . $e->getMessage());
                    \Log::error($e->getTraceAsString());
                }
            }
            
            $trainer->save();

            // Update schedules - first delete all existing schedules
            TrainerSchedule::where('trainer_id', $trainer->id)->delete();
            
            // Then create new schedules
            if ($request->has('schedule')) {
                foreach ($request->schedule as $day => $hours) {
                    if (isset($hours['start']) && isset($hours['end'])) {
                        TrainerSchedule::create([
                            'trainer_id' => $trainer->id,
                            'day_of_week' => $day,
                            'start_time' => $hours['start'],
                            'end_time' => $hours['end']
                        ]);
                    }
                }
            }
            
            // If instructor_for has changed, sync with sports table
            if ($oldInstructorFor !== $request->instructor_for) {
                $sportSlugs = explode(',', $request->instructor_for);
                $sportSlugs = array_map('trim', $sportSlugs);
                
                $sportsToSync = \App\Models\Sport::whereIn('slug', $sportSlugs)->pluck('id')->toArray();
                $user->specialtySports()->sync($sportsToSync);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Trainer updated successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating trainer: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error updating trainer: ' . $e->getMessage()
            ], 500);
        }
    }

    // Get trainer details for editing
    public function edit($id)
    {
        $trainer = Trainer::with(['user', 'schedules'])->findOrFail($id);
        return response()->json($trainer);
    }

    // Archive/Unarchive a trainer
    public function archive($id)
    {
        try {
            $trainer = Trainer::findOrFail($id);
            $user = User::findOrFail($trainer->user_id);
            
            // Toggle the archived status
            $user->is_archived = !$user->is_archived;
            
            // If archiving, set the resigned date to today
            if ($user->is_archived) {
                $trainer->resigned_date = now()->format('Y-m-d');
                $trainer->save();
            } else {
                // If unarchiving, clear the resigned date
                $trainer->resigned_date = null;
                $trainer->save();
            }
            
            $user->save();
            
            return response()->json([
                'success' => true,
                'message' => $user->is_archived ? 'Trainer archived successfully' : 'Trainer unarchived successfully',
                'is_archived' => $user->is_archived
            ]);
        } catch (\Exception $e) {
            \Log::error('Error toggling trainer archive status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error toggling trainer archive status: ' . $e->getMessage()
            ], 500);
        }
    }

    // User View Trainers Page
    public function indexUser()
    {
        // Only get active (non-archived) trainers
        $trainers = Trainer::with(['user', 'schedules'])
            ->whereHas('user', function($query) {
                $query->where('is_archived', false);
            })->get();
        
        // Format the trainer data for display
        $trainers->each(function ($trainer) {
            // Get weekly schedule
            $weeklySchedule = [];
            foreach ($trainer->schedules as $schedule) {
                $day = substr($schedule->day_of_week, 0, 3); // Mon, Tue, etc.
                $startTime = date('ga', strtotime($schedule->start_time)); // 9AM
                $endTime = date('ga', strtotime($schedule->end_time)); // 5PM
                $weeklySchedule[$day] = "$startTime - $endTime";
            }
            
            // Add formatted schedule to trainer
            $trainer->formatted_schedule = $weeklySchedule;
        });
        
        return view('trainers', compact('trainers'));
    }
    
    /**
     * Show the trainer's profile dashboard
     */
    public function showProfile()
    {
        // Get the current trainer
        $user = auth()->user();
        $trainer = $user->trainer;
        
        if (!$trainer) {
            return redirect()->route('home')->with('error', 'Trainer profile not found');
        }
        
        // Get the trainer's schedule
        $schedules = $trainer->schedules;
        
        // Format weekly schedule
        $weeklySchedule = [];
        foreach ($schedules as $schedule) {
            $day = $schedule->day_of_week; // Full day name
            $startTime = date('g:i A', strtotime($schedule->start_time));
            $endTime = date('g:i A', strtotime($schedule->end_time));
            $weeklySchedule[$day] = [
                'start' => $startTime,
                'end' => $endTime
            ];
        }
        
        // Get next 5 sessions based on schedule (upcoming sessions)
        $upcomingSessions = [];
        $today = now();
        $daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $todayIndex = $today->dayOfWeek;
        
        // Convert to 1-7 (Monday-Sunday) instead of 0-6 (Sunday-Saturday)
        $todayIndex = $todayIndex == 0 ? 7 : $todayIndex;
        
        // Restructure schedules by day of week
        $schedulesByDay = [];
        foreach ($schedules as $schedule) {
            $dayIndex = array_search($schedule->day_of_week, $daysOfWeek) + 1;
            $schedulesByDay[$dayIndex] = [
                'start_time' => $schedule->start_time,
                'end_time' => $schedule->end_time,
                'day_name' => $schedule->day_of_week
            ];
        }
        
        // Get next 5 days that have sessions
        $count = 0;
        $currentDay = $today->copy();
        
        while ($count < 5) {
            $dayOfWeekIndex = $currentDay->dayOfWeek;
            $dayOfWeekIndex = $dayOfWeekIndex == 0 ? 7 : $dayOfWeekIndex;
            
            if (isset($schedulesByDay[$dayOfWeekIndex])) {
                $schedule = $schedulesByDay[$dayOfWeekIndex];
                $startTime = new \DateTime($schedule['start_time']);
                $endTime = new \DateTime($schedule['end_time']);
                
                // Only include future sessions for today
                if ($currentDay->format('Y-m-d') != $today->format('Y-m-d') || 
                    $today->format('H:i:s') < $schedule['start_time']) {
                    
                    $upcomingSessions[] = [
                        'date' => $currentDay->format('F j, Y'),
                        'day' => $schedule['day_name'],
                        'start_time' => $startTime->format('g:i A'),
                        'end_time' => $endTime->format('g:i A'),
                    ];
                    $count++;
                }
            }
            
            $currentDay->addDay();
        }
        
        // Get members subscribed to the trainer's specialties
        $instructorTypes = explode(',', $trainer->instructor_for);
        $instructorTypes = array_map('trim', $instructorTypes);
        
        // Map instructor_for values to subscription type values
        $mappedTypes = [];
        foreach ($instructorTypes as $type) {
            $mappedTypes[] = $type; // Use as-is since now we're using the same slug format
        }
        
        $members = User::where('role', 'member')
            ->where('is_archived', false)
            ->whereHas('subscriptions', function($query) use ($mappedTypes) {
                $query->where('is_active', true)
                      ->where(function($q) {
                           // Either end_date is null or end_date is in the future
                           $q->whereNull('end_date')
                             ->orWhere('end_date', '>', now());
                      })
                      ->whereIn('type', $mappedTypes);
            })
            ->with(['subscriptions' => function($query) use ($mappedTypes) {
                $query->where('is_active', true)
                      ->where(function($q) {
                           $q->whereNull('end_date')
                             ->orWhere('end_date', '>', now());
                      })
                      ->whereIn('type', $mappedTypes);
            }])
            ->get();
            
        // Count active members (instructed clients)
        $activeMembers = $members->count();
        $trainer->instructed_clients_count = $activeMembers;
        
        // Calculate the number of new students (joined in the last 30 days)
        $newStudents = User::where('role', 'member')
            ->where('is_archived', false)
            ->whereHas('subscriptions', function($query) use ($mappedTypes) {
                $query->where('is_active', true)
                      ->whereIn('type', $mappedTypes)
                      ->where('created_at', '>=', now()->subDays(30));
            })
            ->count();
        
        return view('trainer.profile', compact('trainer', 'weeklySchedule', 'upcomingSessions', 'members', 'activeMembers', 'newStudents'));
    }

    /**
     * Show the trainer's attendance history
     */
    public function showAttendanceDetails(Request $request)
    {
        // Get the current trainer's user account
        $user = auth()->user();
        
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
            $yearOptions[] = now()->year;
        }
        
        return view('trainer.attendance_details', compact('sessions', 'stats', 'filter', 'yearOptions'));
    }
    
    /**
     * Calculate attendance statistics for a user
     * 
     * @param User $user
     * @return array
     */
    private function getAttendanceStats($user)
    {
        // Get the current month's check-ins
        $thisMonth = $user->sessions()
            ->where('status', 'IN')
            ->whereMonth('time', now()->month)
            ->whereYear('time', now()->year)
            ->count();
            
        // Get last month's check-ins
        $lastMonth = $user->sessions()
            ->where('status', 'IN')
            ->whereMonth('time', now()->subMonth()->month)
            ->whereYear('time', now()->subMonth()->year)
            ->count();
            
        // Get total unique days with check-ins (total attendance days)
        $totalDays = $user->sessions()
            ->where('status', 'IN')
            ->selectRaw('DATE(time) as date')
            ->groupBy('date')
            ->get()
            ->count();
            
        // Calculate average days per month
        // First, get the user's first check-in date
        $firstCheckIn = $user->sessions()
            ->where('status', 'IN')
            ->orderBy('time', 'asc')
            ->first();
            
        if ($firstCheckIn) {
            $firstDate = \Carbon\Carbon::parse($firstCheckIn->time);
            $monthsSinceFirstCheckIn = $firstDate->diffInMonths(now()) + 1; // +1 to include current month
            
            if ($monthsSinceFirstCheckIn > 0) {
                $avgDaysPerMonth = round($totalDays / $monthsSinceFirstCheckIn, 1);
            } else {
                // If less than a month, calculate for current month only
                $avgDaysPerMonth = $totalDays;
            }
        } else {
            // No check-ins yet
            $avgDaysPerMonth = 0;
        }
        
        return [
            'thisMonth' => $thisMonth,
            'lastMonth' => $lastMonth,
            'totalDays' => $totalDays,
            'avgDaysPerMonth' => $avgDaysPerMonth
        ];
    }
    
    /**
     * Test method to return sample attendance data
     */
    public function testAttendanceData(Request $request)
    {
        // Sample data that matches the expected format
        $data = [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            'checkIns' => [5, 10, 15, 20, 8, 12, 7, 9, 14, 11, 6, 8],
            'stats' => [
                'thisMonth' => 10,
                'lastMonth' => 8,
                'totalDays' => 125,
                'avgDaysPerMonth' => 10.4
            ]
        ];
        
        return response()->json($data);
    }
    
    /**
     * API method to get trainer attendance data for chart
     */
    public function getTrainerAttendance(Request $request)
    {
        $user = auth()->user();
        $period = $request->query('period', 'month');
        
        \Log::info('Trainer attendance request', [
            'user_id' => $user->id, 
            'period' => $period
        ]);
        
        // Fetch actual attendance data based on the period
        if ($period === 'year') {
            // Yearly data - get monthly counts for current year
            $currentYear = now()->year;
            $monthlyData = [];
            
            // Initialize with all months having 0 check-ins
            for ($month = 1; $month <= 12; $month++) {
                $monthlyData[$month] = 0;
            }
            
            // Get actual check-in counts for each month in current year
            $checkIns = $user->sessions()
                ->where('status', 'IN')
                ->whereYear('time', $currentYear)
                ->selectRaw('MONTH(time) as month, COUNT(*) as count')
                ->groupBy('month')
                ->get();
                
            // Update monthly data with actual counts
            foreach ($checkIns as $checkIn) {
                $monthlyData[$checkIn->month] = $checkIn->count;
            }
            
            $labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            $data = [
                'labels' => $labels,
                'checkIns' => array_values($monthlyData),
                'stats' => $this->getAttendanceStats($user)
            ];
            
        } elseif ($period === 'week') {
            // Weekly data - last 7 days
            $dayLabels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
            $dailyData = array_fill(0, 7, 0);
            
            // Get the date 7 days ago
            $startDate = now()->subDays(6)->startOfDay();
            
            // Get check-ins for each day
            $checkIns = $user->sessions()
                ->where('status', 'IN')
                ->where('time', '>=', $startDate)
                ->get();
                
            foreach ($checkIns as $checkIn) {
                $checkInDate = \Carbon\Carbon::parse($checkIn->time);
                $dayIndex = ($checkInDate->dayOfWeek + 6) % 7; // Convert 0-6 (Sun-Sat) to 0-6 (Mon-Sun)
                $dailyData[$dayIndex]++;
            }
            
            $data = [
                'labels' => $dayLabels,
                'checkIns' => $dailyData,
                'stats' => $this->getAttendanceStats($user)
            ];
            
        } elseif ($period === 'all') {
            // All-time data - group by month for the past year
            $startDate = now()->subMonths(11)->startOfMonth();
            $monthlyData = [];
            
            // Initialize with past 12 months
            for ($i = 0; $i < 12; $i++) {
                $date = now()->subMonths(11 - $i)->startOfMonth();
                $key = $date->format('M y');
                $monthlyData[$key] = 0;
            }
            
            // Get check-ins grouped by month
            $checkIns = $user->sessions()
                ->where('status', 'IN')
                ->where('time', '>=', $startDate)
                ->selectRaw("DATE_FORMAT(time, '%b %y') as month_year, COUNT(*) as count")
                ->groupBy('month_year')
                ->get();
                
            foreach ($checkIns as $checkIn) {
                if (isset($monthlyData[$checkIn->month_year])) {
                    $monthlyData[$checkIn->month_year] = $checkIn->count;
                }
            }
            
            $data = [
                'labels' => array_keys($monthlyData),
                'checkIns' => array_values($monthlyData),
                'stats' => $this->getAttendanceStats($user)
            ];
            
        } else {
            // Default: Monthly data (days of current month)
            $currentMonth = now()->month;
            $currentYear = now()->year;
            $daysInMonth = now()->daysInMonth;
            $dailyData = array_fill(1, $daysInMonth, 0);
            
            // Get check-ins for current month grouped by day
            $checkIns = $user->sessions()
                ->where('status', 'IN')
                ->whereMonth('time', $currentMonth)
                ->whereYear('time', $currentYear)
                ->selectRaw('DAY(time) as day, COUNT(*) as count')
                ->groupBy('day')
                ->get();
                
            foreach ($checkIns as $checkIn) {
                $dailyData[$checkIn->day] = $checkIn->count;
            }
            
            $data = [
                'labels' => array_map('strval', range(1, $daysInMonth)),
                'checkIns' => array_values($dailyData),
                'stats' => $this->getAttendanceStats($user)
            ];
        }
        
        \Log::info('Returning attendance data for chart', [
            'period' => $period, 
            'labels_count' => count($data['labels']),
            'data_count' => count($data['checkIns'])
        ]);
        
        return response()->json($data);
    }

    /**
     * A test method to verify the attendance chart is working
     */
    public function testChartDisplay()
    {
        return view('trainer.test-chart');
    }
    
    /**
     * API method to get all trainers for dropdown or selection
     */
    public function getTrainers()
    {
        $trainers = User::where('role', 'trainer')
            ->where('is_archived', false)
            ->with('trainer')
            ->get()
            ->map(function($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->full_name,
                    'profile_image_url' => $user->trainer ? $user->trainer->profile_image_url : asset('assets/default_profile.png'),
                    'specialization' => $user->trainer ? $user->trainer->specialization : null
                ];
            });
            
        return response()->json($trainers);
    }
}