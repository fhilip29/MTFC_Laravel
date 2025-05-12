<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Subscription;

class PricingController extends Controller
{
    /**
     * Show gym pricing page
     */
    public function gym()
    {
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
        
        return view('pricing.gym', compact('userHasActive', 'userRole', 'activeType', 'activePlan'));
    }
    
    /**
     * Show boxing pricing page
     */
    public function boxing()
    {
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
        
        return view('pricing.boxing', compact('userHasActive', 'userRole', 'activeType', 'activePlan'));
    }
    
    /**
     * Show muay thai pricing page
     */
    public function muay()
    {
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
        
        return view('pricing.muay', compact('userHasActive', 'userRole', 'activeType', 'activePlan'));
    }
    
    /**
     * Show jiu jitsu pricing page
     */
    public function jiu()
    {
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
        
        return view('pricing.jiu', compact('userHasActive', 'userRole', 'activeType', 'activePlan'));
    }
} 