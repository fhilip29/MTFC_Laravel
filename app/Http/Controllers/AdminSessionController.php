<?php

namespace App\Http\Controllers;

use App\Models\Sessions;
use Illuminate\Http\Request;

class AdminSessionController extends Controller
{
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