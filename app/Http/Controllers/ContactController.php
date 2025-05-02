<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    /**
     * Show the contact form
     */
    public function index()
    {
        $user = Auth::check() ? Auth::user() : null;
        return view('contact', compact('user'));
    }

    /**
     * Handle the contact form submission
     */
    public function store(Request $request)
    {
        // Validate form input
        $validator = Validator::make($request->all(), [
            'fullName' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'phoneNumber' => 'required|string|max:20',
            'message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Create new contact message
        $contactMessage = new ContactMessage();
        $contactMessage->full_name = $request->fullName;
        $contactMessage->email = $request->email;
        $contactMessage->subject = $request->subject;
        $contactMessage->phone_number = $request->phoneNumber;
        $contactMessage->message = $request->message;
        
        // Associate with user if logged in
        if (Auth::check()) {
            $contactMessage->user_id = Auth::id();
        }

        $contactMessage->save();

        // Notify admin users about new message
        $admins = User::where('role', 'admin')->get();
        
        // TODO: Send email to admins (implement in a real application)
        // This is a placeholder for actual email sending logic
        // foreach ($admins as $admin) {
        //     Mail::to($admin->email)->send(new \App\Mail\NewContactMessage($contactMessage));
        // }

        return response()->json([
            'success' => true,
            'message' => 'Your message has been sent successfully!'
        ]);
    }
}
