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
        
        // Priority 1: QR Code Scan (creates a new session for a user)
        if ($request->has('qr_code')) {
            $request->validate([
                'qr_code' => 'required|string',
                'status' => 'required|in:IN,OUT',
            ]);

            $user = User::where('qr_code', $request->qr_code)->first();

            if (!$user) {
                return response()->json(['success' => false, 'error' => 'Invalid QR Code'], 404);
            }

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
        // Priority 2: Guest Checkout (updates an existing guest session to 'OUT')
        elseif ($request->has('session_id') && $request->input('status') === 'OUT') {
            $request->validate([
                'session_id' => 'required|exists:sessions,id',
                'status' => 'required|in:OUT',
            ]);

            try {
                $session = Sessions::find($request->session_id);
                
                if (!$session) { // Should be caught by exists validation, but good practice
                    return response()->json(['success' => false, 'error' => 'Session not found.'], 404);
                }
                if ($session->user_id !== null) {
                    return response()->json(['success' => false, 'error' => 'This session is not a guest session.'], 400);
                }
                if ($session->status !== 'IN') {
                    return response()->json(['success' => false, 'error' => 'This guest is not currently checked IN or has already been checked OUT.'], 400);
                }

                $session->status = 'OUT';
                $session->time = Carbon::now(); // Update time to checkout time
                $session->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Guest checked out successfully.',
                    'data' => [ 
                        'id' => $session->id,
                        'full_name' => $session->guest_name,
                        'mobile_number' => $session->mobile_number,
                        'role' => 'guest',
                        'time' => $session->time,
                        'status' => $session->status,
                    ]
                ]);
            } catch (\Exception $e) {
                \Log::error('Guest Check-out Error', [
                    'error' => $e->getMessage(), 
                    'request' => $request->all()
                ]);
                return response()->json(['success' => false, 'error' => 'Error processing guest checkout: ' . $e->getMessage()], 500);
            }
        }
        // Priority 3: Guest Check-Out (creates a new guest session with status 'OUT')
        elseif ($request->has('guest_name') && $request->input('status') === 'OUT') {
            try {
                $request->validate([
                    'guest_name' => 'required|string|max:255',
                    'mobile_number' => 'required|string|max:20', 
                    'status' => 'required|in:OUT',
                ]);
    
                $session = Sessions::create([
                    'user_id' => null, 
                    'guest_name' => $request->guest_name,
                    'mobile_number' => $request->mobile_number,
                    'time' => Carbon::now(),
                    'status' => 'OUT', // Explicitly OUT
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
            } catch (\Illuminate\Validation\ValidationException $e) {
                \Log::error('Guest Check-out Validation Error', [
                    'errors' => $e->errors(), 
                    'request' => $request->all()
                ]);
                return response()->json(['success' => false, 'error' => 'Validation failed.', 'errors' => $e->errors()], 422);
            } catch (\Exception $e) {
                \Log::error('Guest Check-out Error', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'request' => $request->all()
                ]);
                
                return response()->json([
                    'success' => false,
                    'error' => 'Error processing guest check-out: ' . $e->getMessage()
                ], 500);
            }
        }
        // Priority 4: Guest Check-In (creates a new guest session with status 'IN')
        elseif ($request->has('guest_name') && $request->input('status') === 'IN') {
            try {
                $request->validate([
                    'guest_name' => 'required|string|max:255',
                    'mobile_number' => 'required|string|max:20', 
                    'status' => 'required|in:IN',
                ]);
    
                $session = Sessions::create([
                    'user_id' => null, 
                    'guest_name' => $request->guest_name,
                    'mobile_number' => $request->mobile_number,
                    'time' => Carbon::now(),
                    'status' => 'IN', // Explicitly IN
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
            } catch (\Illuminate\Validation\ValidationException $e) {
                \Log::error('Guest Check-in Validation Error', [
                    'errors' => $e->errors(), 
                    'request' => $request->all()
                ]);
                return response()->json(['success' => false, 'error' => 'Validation failed.', 'errors' => $e->errors()], 422);
            } catch (\Exception $e) {
                \Log::error('Guest Check-in Error', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'request' => $request->all()
                ]);
                
                return response()->json([
                    'success' => false,
                    'error' => 'Error processing guest check-in: ' . $e->getMessage()
                ], 500);
            }
        }
        // Fallback: Invalid request
        else {
            \Log::warning('Invalid Session Store Request', ['request_data' => $request->all()]);
            return response()->json(['success' => false, 'error' => 'Invalid request. Ensure all required parameters for the intended operation are provided correctly.'], 400);
        }
    }
}
