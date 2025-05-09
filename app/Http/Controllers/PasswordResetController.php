<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PasswordResetController extends Controller
{
    /**
     * Send password reset code to the user's email
     */
    public function sendResetCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.exists' => 'No account found with this email address.'
        ]);

        // Get the user
        $user = User::where('email', $request->email)->first();

        // Generate a 6-digit code
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Store the code with the user's email
        DB::table('password_resets')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => $code,
                'created_at' => Carbon::now()
            ]
        );

        // Send email with the code
        try {
            // Always try to send the actual email regardless of environment
            if (class_exists('App\Mail\ResetPasswordMail')) {
                Mail::to($request->email)->send(new \App\Mail\ResetPasswordMail($code, $user));
            } else {
                // Fall back to a simple text email if the custom mail class is not available
                Mail::raw("Your password reset code is: {$code}. This code will expire in 1 hour.", function($message) use ($request) {
                    $message->to($request->email)
                            ->subject('Reset Your Password - MTFC');
                });
            }
            
            return response()->json([
                'message' => 'Reset code sent successfully to your email'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to send reset code.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify the reset code
     */
    public function verifyCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string',
        ]);

        $reset = DB::table('password_resets')
            ->where('email', $request->email)
            ->where('token', $request->code)
            ->first();

        if (!$reset) {
            return response()->json([
                'message' => 'Invalid code.',
                'valid' => false
            ], 400);
        }

        // Check if code is expired (1 hour)
        $createdAt = Carbon::parse($reset->created_at);
        if ($createdAt->diffInHours(Carbon::now()) >= 1) {
            return response()->json([
                'message' => 'Code has expired.',
                'valid' => false
            ], 400);
        }

        return response()->json([
            'message' => 'Code verified successfully.',
            'valid' => true
        ]);
    }

    /**
     * Reset the user's password
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string',
            'password' => [
                'required',
                'min:8',
                'confirmed',
                'regex:/[a-z]/',      // must contain at least one lowercase letter
                'regex:/[A-Z]/',      // must contain at least one uppercase letter
                'regex:/[0-9]/',      // must contain at least one digit
            ],
        ], [
            'password.min' => 'Password must be at least 8 characters long.',
            'password.regex' => 'Password must include at least one uppercase letter, one lowercase letter, and one number.'
        ]);

        // Verify code is valid
        $reset = DB::table('password_resets')
            ->where('email', $request->email)
            ->where('token', $request->code)
            ->first();

        if (!$reset) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Invalid code.',
                    'success' => false
                ], 400);
            }
            return redirect()->back()->with('error', 'Invalid reset code.');
        }

        // Check if code is expired (1 hour)
        $createdAt = Carbon::parse($reset->created_at);
        if ($createdAt->diffInHours(Carbon::now()) >= 1) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Code has expired.',
                    'success' => false
                ], 400);
            }
            return redirect()->back()->with('error', 'Reset code has expired.');
        }

        // Update user's password
        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // Delete the used reset code
        DB::table('password_resets')
            ->where('email', $request->email)
            ->delete();

        // Send a confirmation email
        try {
            Mail::raw("Your MTFC account password has been successfully reset. If you did not make this change, please contact support immediately.", function($message) use ($user) {
                $message->to($user->email)
                        ->subject('Password Reset Successful - MTFC');
            });
        } catch (\Exception $e) {
            // Just log the error but continue the process
            \Log::error('Failed to send password reset confirmation: ' . $e->getMessage());
        }

        // Return appropriate response
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Password reset successfully.',
                'success' => true
            ]);
        }
        
        return redirect()->route('login')->with('success', 'Password has been reset successfully. Please login with your new password.');
    }

    /**
     * Handle Google password reset
     */
    public function handleGoogleReset(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        // For Google accounts, we'll use a different approach
        // We'll send an email with a secure reset link instead of a code
        
        $token = Str::random(60);
        
        DB::table('password_resets')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => $token,
                'created_at' => Carbon::now()
            ]
        );

        // Generate reset link
        $resetLink = url("/reset-password/{$token}");
        
        // Here we would send an email with the reset link
        // Mail::to($request->email)->send(new GoogleResetPasswordMail($resetLink));
        
        // For demo purposes, return the link
        return response()->json([
            'message' => 'Password reset link sent successfully.',
            'reset_link' => $resetLink // Remove in production
        ]);
    }
    
    /**
     * Show the form to reset password with a token (for direct link resets)
     */
    public function showResetForm(Request $request, $token)
    {
        // Find the token in the database
        $reset = DB::table('password_resets')
            ->where('token', $token)
            ->first();
            
        if (!$reset) {
            return redirect('/forgot-password')->with('error', 'Invalid or expired password reset link.');
        }
        
        // Check if token is expired (1 hour)
        $createdAt = Carbon::parse($reset->created_at);
        if ($createdAt->diffInHours(Carbon::now()) >= 1) {
            return redirect('/forgot-password')->with('error', 'Your password reset link has expired.');
        }
        
        // Pass the token and email to the view
        return view('auth.reset_password', [
            'token' => $token,
            'email' => $reset->email
        ]);
    }
} 