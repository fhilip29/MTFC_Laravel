<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SiteSettingsController extends Controller
{
    /**
     * Show the site settings form.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $settings = SiteSettings::getSettings();
        return view('admin.settings.site_settings', compact('settings'));
    }

    /**
     * Update the about page settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateAboutPage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'about_us_content' => 'required|string',
            'community_content' => 'required|string',
            'our_values' => 'required|array',
            'our_values.*' => 'required|string|max:100',
            'location_section_title' => 'nullable|string|max:100',
            'location_section_description' => 'nullable|string',
            'about_address_line1' => 'required|string|max:255',
            'about_address_line2' => 'nullable|string|max:255',
            'about_phone_number' => 'required|string|max:20',
            'about_email' => 'required|email|max:100',
            'about_working_hours_weekday' => 'required|string|max:100',
            'about_working_hours_weekend' => 'required|string|max:100',
            'about_google_maps_embed_url' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $settings = SiteSettings::getSettings();
        
        $settings->update([
            'about_us_content' => $request->about_us_content,
            'community_content' => $request->community_content,
            'our_values' => $request->our_values,
            'location_section_title' => $request->location_section_title,
            'location_section_description' => $request->location_section_description,
            'about_address_line1' => $request->about_address_line1,
            'about_address_line2' => $request->about_address_line2,
            'about_phone_number' => $request->about_phone_number,
            'about_email' => $request->about_email,
            'about_working_hours_weekday' => $request->about_working_hours_weekday,
            'about_working_hours_weekend' => $request->about_working_hours_weekend,
            'about_google_maps_embed_url' => $request->about_google_maps_embed_url,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'About page settings updated successfully'
        ]);
    }

    /**
     * Update the contact page settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateContactPage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'phone_number' => 'required|string|max:20',
            'email' => 'required|email|max:100',
            'working_hours_weekday' => 'required|string|max:100',
            'working_hours_weekend' => 'required|string|max:100',
            'facebook_url' => 'nullable|url|max:255',
            'instagram_url' => 'nullable|url|max:255',
            'twitter_url' => 'nullable|url|max:255',
            'youtube_url' => 'nullable|url|max:255',
            'google_maps_embed_url' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $settings = SiteSettings::getSettings();
        
        $settings->update([
            'address_line1' => $request->address_line1,
            'address_line2' => $request->address_line2,
            'phone_number' => $request->phone_number,
            'email' => $request->email,
            'working_hours_weekday' => $request->working_hours_weekday,
            'working_hours_weekend' => $request->working_hours_weekend,
            'facebook_url' => $request->facebook_url,
            'instagram_url' => $request->instagram_url,
            'twitter_url' => $request->twitter_url,
            'youtube_url' => $request->youtube_url,
            'google_maps_embed_url' => $request->google_maps_embed_url,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Contact page settings updated successfully'
        ]);
    }
}
