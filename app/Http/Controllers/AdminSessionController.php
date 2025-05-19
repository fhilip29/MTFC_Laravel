<?php

namespace App\Http\Controllers;

use App\Models\Sessions;
use Illuminate\Http\Request;

class AdminSessionController extends Controller
{
    /**
     * Check if a guest is already checked in or not checked in
     * Used to prevent duplicate check-ins and invalid check-outs
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkGuestStatus(Request $request)
    {
        try {
            $request->validate([
                'guest_name' => 'required|string',
                'mobile_number' => 'required|string',
                'status' => 'required|in:IN,OUT',
            ]);
            
            // Check if the guest is already checked in
            $lastGuestSession = Sessions::whereNull('user_id')
                ->where('guest_name', $request->guest_name)
                ->where('mobile_number', $request->mobile_number)
                ->latest('time')
                ->first();
            
            // If checking in (status = IN)
            if ($request->status === 'IN') {
                // If no previous session or last session was OUT, they can check in
                if (!$lastGuestSession || $lastGuestSession->status === 'OUT') {
                    return response()->json([
                        'success' => true,
                        'canProceed' => true,
                        'message' => 'Guest can check in'
                    ]);
                } else {
                    // Guest is already checked in
                    return response()->json([
                        'success' => true,
                        'canProceed' => false,
                        'alreadyCheckedIn' => true,
                        'message' => $request->guest_name . ' is already checked in.'
                    ]);
                }
            }
            // If checking out (status = OUT)
            else {
                // If no previous session or last session was already OUT, they can't check out
                if (!$lastGuestSession || $lastGuestSession->status === 'OUT') {
                    return response()->json([
                        'success' => true,
                        'canProceed' => false,
                        'notCheckedIn' => true,
                        'message' => $request->guest_name . ' is not currently checked in. Cannot check out.'
                    ]);
                } else {
                    // Guest is checked in and can check out
                    return response()->json([
                        'success' => true,
                        'canProceed' => true,
                        'message' => 'Guest can check out'
                    ]);
                }
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to check guest status: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Return a list of all currently checked-in guests
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCheckedInGuests()
    {
        try {
            // Get all guest sessions with status 'IN'
            $guests = Sessions::where('status', 'IN')
                ->whereNull('user_id') // Only get guests (non-users)
                ->whereNotNull('guest_name') // Must have a guest name
                ->orderBy('created_at', 'desc')
                ->get(['id', 'guest_name', 'mobile_number', 'created_at as time']);
            
            return response()->json([
                'success' => true,
                'guests' => $guests
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch checked-in guests: ' . $e->getMessage()
            ]);
        }
    }
} 