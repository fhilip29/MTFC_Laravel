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
                'time' => $session->time,
                'status' => $session->status,
            ]
        ]);
    }
}
