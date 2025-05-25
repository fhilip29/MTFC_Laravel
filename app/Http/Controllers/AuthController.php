<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Propaganistas\LaravelPhone\PhoneNumber;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function showSignupForm()
    {
        return view('signup');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        // First check if the user exists and is archived
        $user = \App\Models\User::where('email', $request->email)->first();
        
        if ($user && $user->is_archived) {
            return back()->with('error', 'Your account has been archived. Please contact the administrator for assistance.');
        }

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect('/')->with('success', 'Logged in successfully.');
        }

        return back()->with('error', 'Invalid email or password.');
    }

    public function signup(Request $request)
    {
        $request->validate([
            'first_name'            => 'required|string|max:255',
            'last_name'             => 'required|string|max:255',
            'gender'                => 'required|in:male,female,other',
            'other_gender'          => 'nullable|string|max:255',
            'fitness_goal'          => 'nullable|in:lose-weight,build-muscle,maintain,boxing,muay-thai,jiu-jitsu',
            'email'                 => 'required|email|unique:users,email',
            'mobile_number'         => 'required|phone:PH|unique:users,mobile_number',
            'password'              => [
                'required',
                'min:8',
                'confirmed',
                'regex:/[a-z]/',      // must contain at least one lowercase letter
                'regex:/[A-Z]/',      // must contain at least one uppercase letter
                'regex:/[0-9]/',      // must contain at least one digit
            ],
        ], [
            'mobile_number.phone' => 'The phone number format is invalid. Please enter a valid Philippine phone number.',
            'password.min' => 'Password must be at least 8 characters long.',
            'password.regex' => 'Password must include at least one uppercase letter, one lowercase letter, and one number.'
        ]);

        // Create full name from first and last name
        $fullName = $request->first_name . ' ' . $request->last_name;

        // Handle other gender option
        $gender = $request->gender;
        if ($gender === 'other' && $request->filled('other_gender')) {
            $gender = $request->other_gender;
        }

        // Generate unique QR code for the user
        $qrCode = Str::random(24);
        
        // Generate email verification token
        $verificationToken = Str::random(60);

        $user = User::create([
            'full_name' => $fullName,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'mobile_number' => $request->mobile_number,
            'gender' => $gender,
            'fitness_goal' => $request->fitness_goal,
            'is_agreed_to_terms' => $request->has('terms'),
            'qr_code' => $qrCode,
            'email_verified_at' => null,
        ]);
        
        // Store the verification token
        DB::table('email_verifications')->insert([
            'email' => $user->email,
            'token' => $verificationToken,
            'created_at' => now()
        ]);
        
        // Send verification email
        try {
            Mail::send('emails.verify-email', ['user' => $user, 'token' => $verificationToken], function ($message) use ($user) {
                $message->to($user->email);
                $message->subject('Verify Your Email Address - MTFC');
            });
        } catch (\Exception $e) {
            // Log the error but continue with the registration process
            \Log::error('Failed to send verification email: ' . $e->getMessage());
        }

        Auth::login($user);

        return redirect()->route('home')->with('success', 'Account created successfully! Please check your email to verify your account.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login')->with('success', 'You have been logged out.');
    }

    /**
     * Show the email verification notice view
     */
    public function showVerificationNotice()
    {
        return view('auth.verify-email');
    }
    
    /**
     * Handle email verification request
     */
    public function verifyEmail(Request $request, $token)
    {
        $verification = DB::table('email_verifications')
            ->where('token', $token)
            ->first();
            
        if (!$verification) {
            return redirect()->route('login')->with('error', 'Invalid verification link.');
        }
        
        // Check if token is expired (24 hours)
        $createdAt = Carbon::parse($verification->created_at);
        if ($createdAt->diffInHours(now()) >= 24) {
            return redirect()->route('login')->with('error', 'Your verification link has expired.');
        }
        
        // Mark the user as verified
        User::where('email', $verification->email)
            ->update(['email_verified_at' => now()]);
            
        // Delete the verification record
        DB::table('email_verifications')->where('token', $token)->delete();
        
        return redirect()->route('login')->with('success', 'Your email has been verified successfully! You can now log in.');
    }
    
    /**
     * Resend verification email
     */
    public function resendVerification(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);
        
        $user = User::where('email', $request->email)->first();
        
        if ($user->email_verified_at) {
            return back()->with('info', 'Your email is already verified.');
        }
        
        // Generate new token
        $verificationToken = Str::random(60);
        
        // Update or create verification record
        DB::table('email_verifications')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => $verificationToken,
                'created_at' => now()
            ]
        );
        
        // Send verification email
        try {
            Mail::send('emails.verify-email', ['user' => $user, 'token' => $verificationToken], function ($message) use ($user) {
                $message->to($user->email);
                $message->subject('Verify Your Email Address - MTFC');
            });
            
            return back()->with('success', 'Verification link has been sent to your email address.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send verification email. Please try again.');
        }
    }
}

