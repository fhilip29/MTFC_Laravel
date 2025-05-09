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
        
        if (Auth::check()) {
            $user = Auth::user();
            $userRole = $user->role;
            
            $userHasActive = $user->subscriptions()
                ->where('type', 'gym')
                ->where('is_active', true)
                ->where('end_date', '>', now())
                ->exists();
        }
        
        return view('pricing.gym', compact('userHasActive', 'userRole'));
    }
    
    /**
     * Show boxing pricing page
     */
    public function boxing()
    {
        $userHasActive = false;
        $userRole = null;
        
        if (Auth::check()) {
            $user = Auth::user();
            $userRole = $user->role;
            
            $userHasActive = $user->subscriptions()
                ->where('type', 'boxing')
                ->where('is_active', true)
                ->where('end_date', '>', now())
                ->exists();
        }
        
        return view('pricing.boxing', compact('userHasActive', 'userRole'));
    }
    
    /**
     * Show muay thai pricing page
     */
    public function muay()
    {
        $userHasActive = false;
        $userRole = null;
        
        if (Auth::check()) {
            $user = Auth::user();
            $userRole = $user->role;
            
            $userHasActive = $user->subscriptions()
                ->where('type', 'muay')
                ->where('is_active', true)
                ->where('end_date', '>', now())
                ->exists();
        }
        
        return view('pricing.muay', compact('userHasActive', 'userRole'));
    }
    
    /**
     * Show jiu jitsu pricing page
     */
    public function jiu()
    {
        $userHasActive = false;
        $userRole = null;
        
        if (Auth::check()) {
            $user = Auth::user();
            $userRole = $user->role;
            
            $userHasActive = $user->subscriptions()
                ->where('type', 'jiu')
                ->where('is_active', true)
                ->where('end_date', '>', now())
                ->exists();
        }
        
        return view('pricing.jiu', compact('userHasActive', 'userRole'));
    }
} 