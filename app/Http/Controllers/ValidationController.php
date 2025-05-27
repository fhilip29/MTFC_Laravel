<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Vendor;

class ValidationController extends Controller
{
    /**
     * Check if a mobile number already exists in the system
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkMobileNumberUnique(Request $request)
    {
        $request->validate([
            'mobile_number' => 'required|string',
            'current_id' => 'nullable|string', // Optional current user/vendor ID to exclude from check
        ]);

        $mobileNumber = $request->input('mobile_number');
        $currentId = $request->input('current_id');
        
        // Format the mobile number to a standard format
        $mobileNumber = preg_replace('/\s+/', '', $mobileNumber); // Remove spaces
        
        // Check if this mobile number exists in users table
        $userQuery = User::where('mobile_number', 'LIKE', '%' . $mobileNumber . '%');
        if ($currentId) {
            $userQuery->where('id', '!=', $currentId);
        }
        $userExists = $userQuery->exists();
        
        // Check if this mobile number exists in vendors table
        $vendorQuery = Vendor::where('contact_info', 'LIKE', '%' . $mobileNumber . '%');
        if ($currentId) {
            $vendorQuery->where('id', '!=', $currentId);
        }
        $vendorExists = $vendorQuery->exists();
        
        $exists = $userExists || $vendorExists;
        
        return response()->json([
            'unique' => !$exists,
            'message' => $exists ? 'This mobile number is already registered in the system.' : 'Mobile number is available.'
        ]);
    }
}
