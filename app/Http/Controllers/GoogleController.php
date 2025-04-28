<?php

namespace App\Http\Controllers;

use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            // Check if user already exists
            $user = User::where('email', $googleUser->getEmail())->first();

            if (!$user) {
                // Create new user if not exists
                $fullName = $googleUser->getName() ?? $googleUser->user['given_name'] . ' ' . $googleUser->user['family_name'] ?? $googleUser->getEmail();
                
                $user = User::create([
                    'full_name' => $fullName,
                    'email' => $googleUser->getEmail(),
                    'password' => bcrypt(Str::random(16)), // Random secure password
                    'role' => 'member', // Default role as member
                    'is_agreed_to_terms' => true, // Assume agreement to terms when using Google auth
                ]);
            }

            Auth::login($user);

            return redirect('/')->with('success', 'Welcome to MTFC!');
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }
}