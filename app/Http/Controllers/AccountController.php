<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AccountController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the account settings form
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
        return view('account-settings', compact('user'));
    }

    /**
     * Update the user's profile information.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'mobile_number' => ['nullable', 'string', 'max:20', Rule::unique('users')->ignore($user->id)],
            'gender' => ['nullable', 'string', 'in:male,female,other'],
            'other_gender' => ['nullable', 'string', 'required_if:gender,other'],
            'fitness_goal' => ['nullable', 'string', 'in:weight-loss,muscle-gain,endurance,flexibility,general-fitness'],
            'profile_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:4096'], // 4MB
            'cropped_image' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Update basic information
        $user->fill([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'mobile_number' => $request->mobile_number,
            'gender' => $request->gender,
            'fitness_goal' => $request->fitness_goal,
        ]);
        
        // Handle other gender if selected
        if ($request->gender === 'other' && $request->has('other_gender')) {
            $user->other_gender = $request->other_gender;
        }

        // Handle profile image upload
        if ($request->hasFile('profile_image') || $request->filled('cropped_image')) {
            // Delete old image if it exists
            if ($user->profile_image && Storage::exists('public/' . $user->profile_image)) {
                Storage::delete('public/' . $user->profile_image);
            }
            
            if ($request->filled('cropped_image')) {
                // Process base64 image data
                $imageData = $request->cropped_image;
                
                // Extract the actual base64 string
                if (strpos($imageData, ';base64,') !== false) {
                    list(, $imageData) = explode(';base64,', $imageData);
                }
                
                // Generate a unique filename
                $filename = 'profile_' . $user->id . '_' . time() . '.jpg';
                $path = 'profile_images/' . $filename;
                
                // Store the file
                Storage::disk('public')->put($path, base64_decode($imageData));
                $user->profile_image = $path;
            }
            elseif ($request->hasFile('profile_image')) {
                // Store new image the traditional way
                $path = $request->file('profile_image')->store('profile_images', 'public');
                $user->profile_image = $path;
            }
        }

        $user->save();

        return redirect()->route('account.settings')->with('success', 'Profile updated successfully!');
    }

    /**
     * Update the user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator, 'password')
                ->withInput();
        }

        $user = Auth::user();

        // Check if current password is correct
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()
                ->withErrors(['current_password' => 'The current password is incorrect.'], 'password');
        }

        // Update password
        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('account.settings')->with('success', 'Password updated successfully!');
    }
} 