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
        
        if (Auth::check()) {
            $userHasActive = Auth::user()->subscriptions()
                ->where('type', 'gym')
                ->where('is_active', true)
                ->where('end_date', '>', now())
                ->exists();
        }
        
        return view('pricing.gym', compact('userHasActive'));
    }
    
    /**
     * Show boxing pricing page
     */
    public function boxing()
    {
        $userHasActive = false;
        
        if (Auth::check()) {
            $userHasActive = Auth::user()->subscriptions()
                ->where('type', 'boxing')
                ->where('is_active', true)
                ->where('end_date', '>', now())
                ->exists();
        }
        
        return view('pricing.boxing', compact('userHasActive'));
    }
    
    /**
     * Show muay thai pricing page
     */
    public function muay()
    {
        $userHasActive = false;
        
        if (Auth::check()) {
            $userHasActive = Auth::user()->subscriptions()
                ->where('type', 'muay')
                ->where('is_active', true)
                ->where('end_date', '>', now())
                ->exists();
        }
        
        return view('pricing.muay', compact('userHasActive'));
    }
    
    /**
     * Show jiu jitsu pricing page
     */
    public function jiu()
    {
        $userHasActive = false;
        
        if (Auth::check()) {
            $userHasActive = Auth::user()->subscriptions()
                ->where('type', 'jiu')
                ->where('is_active', true)
                ->where('end_date', '>', now())
                ->exists();
        }
        
        return view('pricing.jiu', compact('userHasActive'));
    }
} 