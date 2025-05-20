<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PricingPlan;
use App\Models\Sport;
use Illuminate\Support\Facades\Validator;

class PricingController extends Controller
{
    /**
     * Display a listing of pricing plans.
     */
    public function index()
    {
        $sports = Sport::orderBy('display_order')->get();
        
        // Get plans for each sport
        foreach ($sports as $sport) {
            $sport->plans = PricingPlan::where('type', $sport->slug)
                ->orderBy('display_order')
                ->get();
        }
        
        return view('admin.pricing.index', compact('sports'));
    }

    /**
     * Store a newly created pricing plan.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|string|exists:sports,slug',
            'plan' => 'required|string|in:monthly,daily,per-session',
            'name' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'features' => 'nullable|array',
            'features.*' => 'string|max:255',
            'is_featured' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'is_promo' => 'nullable|boolean',
            'original_price' => 'nullable|numeric|min:0',
            'promo_ends_at' => 'nullable|date',
            'description' => 'nullable|string',
            'display_order' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Check if a plan with this type and plan already exists
        $existingPlan = PricingPlan::where('type', $request->type)
            ->where('plan', $request->plan)
            ->first();
            
        if ($existingPlan) {
            return response()->json([
                'success' => false,
                'errors' => ['plan' => ['A plan with this type and duration already exists.']]
            ], 422);
        }
        
        $features = $request->input('features', []);
        if (empty($features)) {
            $features = []; // Ensure features is an array if empty
        }
        
        // Create pricing plan
        $plan = PricingPlan::create([
            'type' => $request->type,
            'plan' => $request->plan,
            'name' => $request->name,
            'price' => $request->price,
            'features' => $features,
            'is_featured' => $request->boolean('is_featured', false),
            'is_active' => $request->boolean('is_active', true),
            'is_promo' => $request->boolean('is_promo', false),
            'original_price' => $request->original_price,
            'promo_ends_at' => $request->promo_ends_at,
            'description' => $request->description,
            'display_order' => $request->input('display_order', 0),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pricing plan created successfully',
            'plan' => $plan
        ]);
    }

    /**
     * Display the specified pricing plan.
     */
    public function show($id)
    {
        $plan = PricingPlan::findOrFail($id);
        
        return response()->json([
            'success' => true,
            'plan' => $plan
        ]);
    }

    /**
     * Update the specified pricing plan.
     */
    public function update(Request $request, $id)
    {
        $plan = PricingPlan::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'features' => 'nullable|array',
            'features.*' => 'string|max:255',
            'is_featured' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'is_promo' => 'nullable|boolean',
            'original_price' => 'nullable|numeric|min:0',
            'promo_ends_at' => 'nullable|date',
            'description' => 'nullable|string',
            'display_order' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        $features = $request->input('features', []);
        if (empty($features)) {
            $features = []; // Ensure features is an array if empty
        }
        
        // Update pricing plan
        $plan->update([
            'name' => $request->name,
            'price' => $request->price,
            'features' => $features,
            'is_featured' => $request->boolean('is_featured', false),
            'is_active' => $request->boolean('is_active', $plan->is_active),
            'is_promo' => $request->boolean('is_promo', false),
            'original_price' => $request->original_price,
            'promo_ends_at' => $request->promo_ends_at,
            'description' => $request->description,
            'display_order' => $request->input('display_order', $plan->display_order),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pricing plan updated successfully',
            'plan' => $plan
        ]);
    }

    /**
     * Remove the specified pricing plan.
     */
    public function destroy($id)
    {
        $plan = PricingPlan::findOrFail($id);
        
        // Check if subscriptions exist with this plan
        $hasSubscriptions = \App\Models\Subscription::where('type', $plan->type)
            ->where('plan', $plan->plan)
            ->exists();
            
        if ($hasSubscriptions) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete plan because there are active or inactive subscriptions using it. Consider deactivating it instead.'
            ], 422);
        }
        
        $plan->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Pricing plan deleted successfully'
        ]);
    }
} 