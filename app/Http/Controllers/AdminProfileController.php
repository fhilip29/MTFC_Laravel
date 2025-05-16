<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AdminProfileController extends Controller
{
    /**
     * Display the admin profile page
     */
    public function index()
    {
        return view('admin.profile');
    }

    /**
     * Update the admin's profile information
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        // Validate the request
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'mobile_number' => [
                'nullable',
                'string',
                'regex:/^(\+63|0)9\d{9}$/',
                Rule::unique('users')->ignore($user->id),
            ],
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'cropped_image' => 'nullable|string',
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable|min:8|confirmed',
        ], [
            'mobile_number.regex' => 'The mobile number must be a valid Philippine phone number (e.g., +639123456789 or 09123456789).'
        ]);

        // Update basic info
        $user->full_name = $request->full_name;
        $user->email = $request->email;
        $user->mobile_number = $request->mobile_number;

        // Handle profile image upload
        if ($request->hasFile('profile_image') || $request->filled('cropped_image')) {
            // Define the upload path - use images/admin directory
            $uploadPath = 'images/admin';
            $fullUploadPath = public_path($uploadPath);
            
            // Create directory if it doesn't exist
            if (!file_exists($fullUploadPath)) {
                mkdir($fullUploadPath, 0755, true);
            }
            
            // Delete old image if exists and not a default image
            if ($user->profile_image && file_exists(public_path($user->profile_image)) && !str_contains($user->profile_image, 'default_profile')) {
                unlink(public_path($user->profile_image));
            }

            if ($request->filled('cropped_image')) {
                // Process base64 image data
                $imageData = $request->cropped_image;
                
                // Extract the actual base64 string
                if (strpos($imageData, ';base64,') !== false) {
                    list(, $imageData) = explode(';base64,', $imageData);
                }
                
                // Generate unique filename
                $fileName = 'profile_' . $user->id . '_' . time() . '.jpg';
                $relativePath = $uploadPath . '/' . $fileName;
                $fullPath = public_path($relativePath);
                
                // Store the file
                file_put_contents($fullPath, base64_decode($imageData));
                $user->profile_image = $relativePath;
            } 
            elseif ($request->hasFile('profile_image')) {
                // Generate unique filename
                $file = $request->file('profile_image');
                $fileName = 'profile_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
                
                // Move file to public directory
                $file->move($fullUploadPath, $fileName);
                
                // Save path to database
                $user->profile_image = $uploadPath . '/' . $fileName;
            }
        }

        // Handle password change if provided
        if ($request->filled('current_password') && $request->filled('new_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect.']);
            }

            $user->password = Hash::make($request->new_password);
        }

        $user->save();

        return back()->with('success', 'Profile updated successfully.');
    }
} 