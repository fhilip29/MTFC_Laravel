<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sessions;
use App\Models\User;
use Carbon\Carbon;

class SessionController extends Controller
{
    public function index()
    {
        $sessions = Sessions::with('user')->latest()->get();
        return view('admin.session.admin_session', compact('sessions'));
    }

    public function store(Request $request)
    {
        \Log::info('Session Store Request', $request->all());
        
        // Validate based on the type of request (QR code scan or guest check-in)
        if ($request->has('qr_code')) {
            $request->validate([
                'qr_code' => 'required|string',
                'status' => 'required|in:IN,OUT',
            ]);

            // Find user by QR code
            $user = User::where('qr_code', $request->qr_code)->first();

            if (!$user) {
                return response()->json(['error' => 'Invalid QR Code'], 404);
            }

            // Create new session entry
            $session = Sessions::create([
                'user_id' => $user->id,
                'time' => Carbon::now(),
                'status' => $request->status,
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $session->id,
                    'user_id' => $user->id,
                    'full_name' => $user->full_name,
                    'role' => $user->role,
                    'time' => $session->time,
                    'status' => $session->status,
                ]
            ]);
        } 
        // Handle guest check-in
        elseif ($request->has('guest_name')) {
            try {
                $request->validate([
                    'guest_name' => 'required|string',
                    'mobile_number' => 'required|string', 
                    'status' => 'required|in:IN,OUT',
                ]);
    
                // Create guest session without user_id
                $session = Sessions::create([
                    'user_id' => null, // No user ID for guests
                    'guest_name' => $request->guest_name,
                    'mobile_number' => $request->mobile_number,
                    'time' => Carbon::now(),
                    'status' => $request->status,
                ]);
    
                return response()->json([
                    'success' => true,
                    'data' => [
                        'id' => $session->id,
                        'user_id' => null,
                        'full_name' => $request->guest_name,
                        'mobile_number' => $request->mobile_number,
                        'role' => 'guest',
                        'time' => $session->time,
                        'status' => $session->status,
                    ]
                ]);
            } catch (\Exception $e) {
                \Log::error('Guest Check-in Error', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'request' => $request->all()
                ]);
                
                return response()->json([
                    'success' => false,
                    'error' => 'Error processing guest: ' . $e->getMessage()
                ], 500);
            }
        }
        else {
            return response()->json(['error' => 'Invalid request. Missing qr_code or guest_name parameter.'], 400);
        }
    }
}
