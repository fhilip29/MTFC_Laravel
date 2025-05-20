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
            $file = $request->file('background_image');
            $filename = 'sport-bg-' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/sports', $filename);
            $backgroundImage = '/storage/sports/' . $filename;
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
        
        // Handle image upload
        if ($request->hasFile('background_image')) {
            $file = $request->file('background_image');
            $filename = 'sport-bg-' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/sports', $filename);
            
            // Remove old image if it's not default
            if ($sport->background_image && $sport->background_image != '/assets/gym-bg.jpg') {
                $oldPath = str_replace('/storage/', 'public/', $sport->background_image);
                Storage::delete($oldPath);
            }
            
            $sport->background_image = '/storage/sports/' . $filename;
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
            $oldPath = str_replace('/storage/', 'public/', $sport->background_image);
            Storage::delete($oldPath);
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
        
        // Check if trainers array is empty
        if (empty($request->trainers) || count($request->trainers) === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Each trainer must be assigned to at least one sport. Please select at least one trainer.'
            ], 422);
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