<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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
            'gender'                => 'required|in:male,female',
            'fitness_goal'          => 'required|in:lose-weight,build-muscle,maintain',
            'email'                 => 'required|email|unique:users,email',
            'password'              => 'required|min:6|confirmed',
        ]);

        // Generate unique QR code for the user
        $qrCode = Str::random(24);

        $user = User::create([
            'full_name' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'gender' => $request->gender,
            'fitness_goal' => $request->fitness_goal,
            'is_agreed_to_terms' => $request->has('terms'),
            'qr_code' => $qrCode,
        ]);

        Auth::login($user);

        return redirect('/')->with('success', 'Welcome, your account has been created!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login')->with('success', 'You have been logged out.');
    }
}

