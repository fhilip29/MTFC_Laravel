<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sport;
use App\Models\PricingPlan;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class SportController extends Controller
{
    /**
     * Display a listing of the sports.
     */
    public function index()
    {
        $sports = Sport::orderBy('display_order')->get();
        
        // Count trainers and plans for each sport
        foreach ($sports as $sport) {
            $sport->trainers_count = $sport->trainers()->count();
            $sport->plans_count = PricingPlan::where('type', $sport->slug)->count();
        }
        
        return view('admin.pricing.sports', compact('sports'));
    }

    /**
     * Store a newly created sport.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'description' => 'required|string',
            'background_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'short_description' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
            'display_order' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Create slug
        $slug = Str::slug($request->name);
        
        // Check if slug already exists
        $existingCount = Sport::where('slug', 'like', $slug . '%')->count();
        if ($existingCount > 0) {
            $slug = $slug . '-' . ($existingCount + 1);
        }
        
        // Handle image upload
        $backgroundImage = null;
        if ($request->hasFile('background_image')) {
            try {
                $file = $request->file('background_image');
                $filename = 'sport-bg-' . time() . '.' . $file->getClientOriginalExtension();
                
                // Create directory if it doesn't exist
                $directory = public_path('images/sports');
                if (!file_exists($directory)) {
                    mkdir($directory, 0755, true);
                }
                
                // Move the file to public/images/sports
                $file->move($directory, $filename);
                $backgroundImage = 'images/sports/' . $filename;
                
                \Log::info('Saved sport background image at: ' . $backgroundImage);
            } catch (\Exception $e) {
                \Log::error('Error handling sport background image: ' . $e->getMessage());
                \Log::error($e->getTraceAsString());
                $backgroundImage = '/assets/gym-bg.jpg'; // Default image on error
            }
        } else {
            $backgroundImage = '/assets/gym-bg.jpg'; // Default image
        }
        
        // Create sport
        $sport = Sport::create([
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description,
            'background_image' => $backgroundImage,
            'short_description' => $request->short_description ?? $request->description,
            'is_active' => $request->boolean('is_active', true),
            'display_order' => $request->input('display_order', 0),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Sport created successfully',
            'sport' => $sport
        ]);
    }

    /**
     * Display the specified sport.
     */
    public function show($id)
    {
        $sport = Sport::findOrFail($id);
        
        return response()->json([
            'success' => true,
            'sport' => $sport
        ]);
    }

    /**
     * Update the specified sport.
     */
    public function update(Request $request, $id)
    {
        $sport = Sport::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'description' => 'required|string',
            'background_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'short_description' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
            'display_order' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Keep existing slug to maintain routes/relationships
        // The system shouldn't allow changing slugs to avoid breaking existing data
        
        // Handle image upload or removal
        if ($request->hasFile('background_image')) {
            try {
                $file = $request->file('background_image');
                $filename = 'sport-bg-' . time() . '.' . $file->getClientOriginalExtension();
                
                // Create directory if it doesn't exist
                $directory = public_path('images/sports');
                if (!file_exists($directory)) {
                    mkdir($directory, 0755, true);
                }
                
                // Remove old image if it's not default and exists in the filesystem
                if ($sport->background_image && $sport->background_image != '/assets/gym-bg.jpg') {
                    $oldImagePath = public_path($sport->background_image);
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }
                
                // Move the file to public/images/sports
                $file->move($directory, $filename);
                $sport->background_image = 'images/sports/' . $filename;
                
                \Log::info('Updated sport background image at: ' . $sport->background_image);
            } catch (\Exception $e) {
                \Log::error('Error handling sport background image update: ' . $e->getMessage());
                \Log::error($e->getTraceAsString());
            }
        } elseif ($request->boolean('remove_background_image')) {
            // Handle image removal request
            try {
                // Remove old image if it's not default and exists in the filesystem
                if ($sport->background_image && $sport->background_image != '/assets/gym-bg.jpg') {
                    $oldImagePath = public_path($sport->background_image);
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                        \Log::info('Removed sport background image: ' . $sport->background_image);
                    }
                }
                
                // Set to default image
                $sport->background_image = '/assets/gym-bg.jpg';
            } catch (\Exception $e) {
                \Log::error('Error removing sport background image: ' . $e->getMessage());
                \Log::error($e->getTraceAsString());
            }
        }
        
        // Update sport
        $sport->update([
            'name' => $request->name,
            'description' => $request->description,
            'short_description' => $request->short_description ?? $request->description,
            'is_active' => $request->boolean('is_active', $sport->is_active),
            'display_order' => $request->input('display_order', $sport->display_order),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Sport updated successfully',
            'sport' => $sport
        ]);
    }

    /**
     * Remove the specified sport.
     */
    public function destroy($id)
    {
        $sport = Sport::findOrFail($id);
        
        // Check if subscriptions exist with this sport
        $hasSubscriptions = \App\Models\Subscription::where('type', $sport->slug)->exists();
            
        if ($hasSubscriptions) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete sport because there are active or inactive subscriptions associated with it. Consider deactivating it instead.'
            ], 422);
        }
        
        // Check if pricing plans exist
        $hasPlans = PricingPlan::where('type', $sport->slug)->exists();
        
        if ($hasPlans) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete sport because there are pricing plans associated with it. Delete the pricing plans first.'
            ], 422);
        }
        
        // Remove background image if it's not default
        if ($sport->background_image && $sport->background_image != '/assets/gym-bg.jpg') {
            try {
                $oldImagePath = public_path($sport->background_image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                    \Log::info('Deleted sport background image: ' . $sport->background_image);
                }
            } catch (\Exception $e) {
                \Log::error('Error deleting sport background image: ' . $e->getMessage());
                \Log::error($e->getTraceAsString());
            }
        }
        
        $sport->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Sport deleted successfully'
        ]);
    }
    
    /**
     * Update trainer-sport relationships
     */
    public function updateTrainers(Request $request, $id)
    {
        $sport = Sport::findOrFail($id);
        
        // Check if trainers array is empty - we'll allow it if all trainers have other sports
        if (empty($request->trainers) || count($request->trainers) === 0) {
            // This is fine - we'll just remove all trainers from this sport
            // as long as they have other sports assigned
            
            // Get trainers currently assigned to this sport
            $currentTrainers = $sport->trainers;
            
            // Check if any of them would be left without any sports
            foreach ($currentTrainers as $trainer) {
                $trainerSports = explode(',', $trainer->trainer->instructor_for);
                $trainerSports = array_map('trim', $trainerSports);
                
                // If this is their only sport, we can't remove them
                if (count($trainerSports) <= 1 && in_array($sport->slug, $trainerSports)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Trainer "' . $trainer->full_name . '" must be assigned to at least one sport. Please assign them to another sport before removing them from this one.'
                    ], 422);
                }
            }
        }
        
        $validator = Validator::make($request->all(), [
            'trainers' => 'required|array',
            'trainers.*' => 'exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Get all trainers
        $trainers = User::whereIn('id', $request->trainers)
            ->where('role', 'trainer')
            ->get();
        
        // Get the trainer IDs    
        $trainerIds = $trainers->pluck('id')->toArray();
            
        // Sync relationships in the trainer_specialties table
        $sport->trainers()->sync($trainerIds);
        
        // For each trainer, update their instructor_for field to include this sport
        foreach ($trainers as $user) {
            if ($user->trainer) {
                $currentSports = explode(',', $user->trainer->instructor_for);
                $currentSports = array_map('trim', $currentSports);
                
                // Add this sport's slug if not already in the list
                if (!in_array($sport->slug, $currentSports)) {
                    $currentSports[] = $sport->slug;
                    $user->trainer->instructor_for = implode(',', $currentSports);
                    $user->trainer->save();
                }
            }
        }
        
        // For trainers who were removed, update their instructor_for field
        $removedTrainers = \App\Models\Trainer::whereHas('user', function($query) use ($trainerIds, $sport) {
            $query->where('role', 'trainer')
                ->whereNotIn('id', $trainerIds)
                ->where('instructor_for', 'like', '%' . $sport->slug . '%');
        })->get();
        
        foreach ($removedTrainers as $trainer) {
            $currentSports = explode(',', $trainer->instructor_for);
            $currentSports = array_map('trim', $currentSports);
            
            // Remove this sport's slug from the list
            if (($key = array_search($sport->slug, $currentSports)) !== false) {
                unset($currentSports[$key]);
                $trainer->instructor_for = implode(',', $currentSports);
                $trainer->save();
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Sport trainers updated successfully'
        ]);
    }
    
    /**
     * API method to get trainers assigned to a specific sport
     */
    public function getTrainers($id)
    {
        $sport = Sport::findOrFail($id);
        
        $trainers = $sport->trainers()->get()->map(function($user) {
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