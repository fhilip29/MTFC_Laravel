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
        } elseif (in_array($filter, ['gym', 'boxing', 'muay-thai', 'jiu-jitsu'])) {
            $query->where('instructor_for', 'like', "%$filter%");
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
        
        // Execute the query
        $trainers = $query->get();
        
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
            
            // Count active clients (placeholder - to be implemented with real data)
            $trainer->active_clients_count = rand(5, 15); // Placeholder
        });
        
        return view('admin.trainer.admin_trainer', compact('trainers', 'filter'));
    }

    // Store a new trainer
    public function store(Request $request)
    {
        // Log the incoming request for debugging
        \Log::info('Trainer store request received', [
            'has_file' => $request->hasFile('profile_image'),
            'all_files' => $request->allFiles(),
            'all_inputs' => $request->except(['password']), // Don't log password
        ]);

        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'mobile_number' => 'nullable|string|max:15',
            'specialization' => 'required|string|max:255',
            'hourly_rate' => 'required|numeric|min:0',
            'instructor_for' => 'required|string',
            'short_intro' => 'nullable|string',
            'profile_image' => 'nullable|file|image|max:5120', // 5MB max size
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
            $user->gender = $request->has('gender') ? $request->gender : null;
            $user->role = 'trainer';
            $user->is_agreed_to_terms = true; // Setting as true since admin is creating
            $user->save();

            // Create trainer profile
            $trainer = new Trainer();
            $trainer->user_id = $user->id;
            $trainer->specialization = $request->specialization;
            $trainer->hourly_rate = $request->hourly_rate;
            $trainer->instructor_for = $request->instructor_for;
            $trainer->short_intro = $request->short_intro;
            
            // Handle profile image upload if provided
            if ($request->hasFile('profile_image')) {
                try {
                    $file = $request->file('profile_image');
                    $filename = time() . '_' . $file->getClientOriginalName();
                    
                    // Store the file in public/uploads/profiles
                    $path = $file->storeAs('uploads/profiles', $filename, 'public');
                    $trainer->profile_url = 'storage/' . $path;
                    
                    \Log::info('Saved profile image at: ' . $trainer->profile_url);
                } catch (\Exception $e) {
                    \Log::error('Error handling profile image: ' . $e->getMessage());
                    \Log::error($e->getTraceAsString());
                    
                    // Continue with the save, just log the error
                    // We don't want to block the trainer creation because of an image issue
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
            'all_files' => $request->allFiles(),
            'all_inputs' => $request->except(['password']), // Don't log password
        ]);

        $trainer = Trainer::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $trainer->user_id,
            'mobile_number' => 'nullable|string|max:15',
            'specialization' => 'required|string|max:255',
            'hourly_rate' => 'required|numeric|min:0',
            'instructor_for' => 'required|string',
            'short_intro' => 'nullable|string',
            'profile_image' => 'nullable|file|image|max:5120', // 5MB max size
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
            if ($request->has('gender')) {
                $user->gender = $request->gender;
            }
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }
            $user->save();

            // Update trainer profile
            $trainer->specialization = $request->specialization;
            $trainer->hourly_rate = $request->hourly_rate;
            $trainer->instructor_for = $request->instructor_for;
            $trainer->short_intro = $request->short_intro;
            
            // Handle profile image upload if provided
            if ($request->hasFile('profile_image')) {
                try {
                    $file = $request->file('profile_image');
                    $filename = time() . '_' . $file->getClientOriginalName();
                    
                    // Store the file in public/uploads/profiles
                    $path = $file->storeAs('uploads/profiles', $filename, 'public');
                    $trainer->profile_url = 'storage/' . $path;
                    
                    \Log::info('Saved profile image at: ' . $trainer->profile_url);
                } catch (\Exception $e) {
                    \Log::error('Error handling profile image: ' . $e->getMessage());
                    \Log::error($e->getTraceAsString());
                    
                    // Continue with the save, just log the error
                    // We don't want to block the trainer creation because of an image issue
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
}