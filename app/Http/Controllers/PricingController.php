<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Subscription;
use App\Models\Sport;
use App\Models\PricingPlan;

class PricingController extends Controller
{
    /**
     * Display a consolidated pricing page with all sports
     */
    public function index()
    {
        // Get all active sports with their active plans
        $sports = Sport::where('is_active', true)
                      ->orderBy('display_order')
                      ->get();
                      
        // Load active plans for each sport
        foreach ($sports as $sport) {
            $sport->activePlans = PricingPlan::where('type', $sport->slug)
                                  ->where('is_active', true)
                                  ->orderBy('display_order')
                                  ->get();
        }
        
        // Set first sport as active by default
        $activeSport = $sports->first();
        
        // Check if user has active subscription
        $userHasActive = false;
        $userRole = null;
        $activeType = null;
        $activePlan = null;
        
        if (Auth::check()) {
            $user = Auth::user();
            $userRole = $user->role;
            
            $activeSubscription = $user->subscriptions()
                ->where('is_active', true)
                ->latest()
                ->first();
                
            if ($activeSubscription) {
                $userHasActive = true;
                $activeType = $activeSubscription->type;
                $activePlan = $activeSubscription->plan;
            }
        }
        
        return view('pricing.pricing', compact(
            'sports', 
            'activeSport',
            'userHasActive', 
            'userRole', 
            'activeType', 
            'activePlan'
        ));
    }

    /**
     * Display a pricing page for any sport based on slug
     * This single method handles all sports dynamically without needing individual methods
     */
    public function show($slug = 'gym')
    {
        $sport = Sport::where('slug', $slug)
                     ->where('is_active', true)
                     ->first();
                     
        // If sport not found, default to gym or first available sport
        if (!$sport) {
            $sport = Sport::where('slug', 'gym')
                         ->where('is_active', true)
                         ->first();
                         
            if (!$sport) {
                $sport = Sport::where('is_active', true)
                             ->orderBy('display_order')
                             ->first();
                             
                if (!$sport) {
                    abort(404, 'No active sports found');
                }
            }
        }
                     
        $plans = PricingPlan::where('type', $sport->slug)
                          ->where('is_active', true)
                          ->orderBy('display_order')
                          ->get();
                          
        // Get all active sports for tabs
        $sports = Sport::where('is_active', true)
                      ->orderBy('display_order')
                      ->get();
                      
        // Check if user has active subscription
        $userHasActive = false;
        $userRole = null;
        $activeType = null;
        $activePlan = null;
        
        if (Auth::check()) {
            $user = Auth::user();
            $userRole = $user->role;
            
            $activeSubscription = $user->subscriptions()
                ->where('is_active', true)
                ->latest()
                ->first();
                
            if ($activeSubscription) {
                $userHasActive = true;
                $activeType = $activeSubscription->type;
                $activePlan = $activeSubscription->plan;
            }
        }
        
        return view('pricing.dynamic', compact(
            'sport', 
            'plans', 
            'sports', 
            'userHasActive', 
            'userRole', 
            'activeType', 
            'activePlan'
        ));
    }
} 