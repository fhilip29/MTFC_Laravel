@extends('layouts.admin')

@section('title', 'Site Settings')

@section('content')
<div class="container px-4 py-6 mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-white">Site Settings</h1>
    </div>

    <div class="bg-gray-800 shadow-md rounded-lg mb-8">
        <div class="border-b border-gray-700">
            <nav class="flex flex-wrap">
                <button id="about-tab" type="button" class="text-white py-4 px-6 block hover:bg-gray-700 focus:outline-none text-white-700 border-b-2 font-medium border-blue-500 active-tab" 
                        onclick="openTab('about')">
                    About Page
                </button>
                <button id="contact-tab" type="button" class="text-white py-4 px-6 block hover:bg-gray-700 focus:outline-none"
                        onclick="openTab('contact')">
                    Contact Page
                </button>
            </nav>
        </div>

        <!-- About Page Tab Content -->
        <div id="about-content" class="tab-content p-6">
            <form id="aboutForm" class="space-y-6">
                @csrf

                <div class="mb-6">
                    <label for="about_us_content" class="block text-[#9CA3AF] text-sm font-medium mb-2">About Us Content <span class="text-red-500">*</span></label>
                    <textarea id="about_us_content" name="about_us_content" rows="5" placeholder="Enter content for the About Us section" class="w-full bg-[#374151] border border-[#4B5563] text-white rounded-lg p-3 focus:outline-none focus:border-[#9CA3AF]" required>{{ $settings->about_us_content ?? "Manila Total Fitness Center is the perfect place for all your fitness needs. We offer a wide range of programs suitable for everyone, regardless of fitness level or interest. Our modern gym is fully equipped with the latest strength and cardio equipment.

Passionate about martial arts? We have expert instructors in boxing, taekwondo, BJJ, and more. With personalized support from our trainers, you'll enhance your skills, build confidence, and achieve your fitness goals—whether it's muscle gain, weight loss, or self-defense." }}</textarea>
                    <p class="text-xs text-[#9CA3AF] mt-1">Required. This content appears in the "About Us" section on the About page.</p>
                </div>

                <div class="mb-6">
                    <label for="community_content" class="block text-[#9CA3AF] text-sm font-medium mb-2">Community Content <span class="text-red-500">*</span></label>
                    <textarea id="community_content" name="community_content" rows="5" placeholder="Enter content for the Community section" class="w-full bg-[#374151] border border-[#4B5563] text-white rounded-lg p-3 focus:outline-none focus:border-[#9CA3AF]" required>{{ $settings->community_content ?? "Manila Total Fitness isn't just a gym—it's a strong community dedicated to holistic health and personal growth. We unite individuals pursuing physical, mental, and emotional wellness.

Our foundation is built on support, empowerment, and a shared journey toward better living. Whether you're just starting out or a seasoned athlete, you'll find a place to belong and thrive here." }}</textarea>
                    <p class="text-xs text-[#9CA3AF] mt-1">Required. This content appears in the "Community" section on the About page.</p>
                </div>

                <div class="mb-6">
                    <label class="block text-[#9CA3AF] text-sm font-medium mb-2">Our Values <span class="text-red-500">*</span></label>
                    <p class="text-xs text-[#9CA3AF] mb-3">Add the core values that define your organization. These will appear in the "Our Values" section on the About page.</p>
                    <div id="values-container" class="space-y-3">
                        @php
                            $valuesArray = [];
                            if (isset($settings->our_values)) {
                                if (is_string($settings->our_values)) {
                                    // Try to decode JSON string
                                    $decodedValues = json_decode($settings->our_values, true);
                                    if (is_array($decodedValues)) {
                                        $valuesArray = $decodedValues;
                                    }
                                } elseif (is_array($settings->our_values)) {
                                    $valuesArray = $settings->our_values;
                                }
                            }
                        @endphp
                        
                        @if(count($valuesArray) > 0)
                            @foreach($valuesArray as $index => $value)
                                <div class="flex items-center space-x-2">
                                    <input type="text" name="our_values[]" value="{{ $value }}" class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                    @if($index > 0)
                                        <button type="button" class="remove-value-btn text-red-500 hover:text-red-400">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            @endforeach
                        @else
                            <div class="flex items-center space-x-2">
                                <input type="text" name="our_values[]" value="Integrity in all we do" class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            </div>
                            <div class="flex items-center space-x-2">
                                <input type="text" name="our_values[]" value="Excellence in service" class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                <button type="button" class="remove-value-btn text-red-500 hover:text-red-400">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            <div class="flex items-center space-x-2">
                                <input type="text" name="our_values[]" value="Community support" class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                <button type="button" class="remove-value-btn text-red-500 hover:text-red-400">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            <div class="flex items-center space-x-2">
                                <input type="text" name="our_values[]" value="Results-driven approach" class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                <button type="button" class="remove-value-btn text-red-500 hover:text-red-400">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        @endif
                    </div>
                    <button type="button" id="add-value-btn" class="mt-3 text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2.5">
                        <i class="fas fa-plus mr-2"></i> Add Value
                    </button>
                </div>

                <h2 class="text-xl font-medium text-white border-b border-gray-700 pb-3 mt-8 mb-6">Location Section</h2>
                <p class="text-sm text-gray-400 mt-2 mb-4">These settings control the "Come Visit Us" section on the About page.</p>
                
                <div class="mb-6">
                    <label for="location_section_title" class="block text-[#9CA3AF] text-sm font-medium mb-2">Location Section Title</label>
                    <input type="text" id="location_section_title" name="location_section_title" value="{{ $settings->location_section_title ?? 'Come Visit Us' }}" placeholder="e.g. Come Visit Us" class="w-full bg-[#374151] border border-[#4B5563] text-white rounded-lg p-3 focus:outline-none focus:border-[#9CA3AF]">
                    <p class="text-xs text-[#9CA3AF] mt-1">The title for the location section on the About page.</p>
                </div>
                
                <div class="mb-6">
                    <label for="location_section_description" class="block text-[#9CA3AF] text-sm font-medium mb-2">Location Section Description</label>
                    <textarea id="location_section_description" name="location_section_description" rows="3" placeholder="Enter a brief description for the location section" class="w-full bg-[#374151] border border-[#4B5563] text-white rounded-lg p-3 focus:outline-none focus:border-[#9CA3AF]">{{ $settings->location_section_description ?? "We're conveniently located in the heart of Manila, ready to welcome you to our fitness community." }}</textarea>
                    <p class="text-xs text-[#9CA3AF] mt-1">A brief description for the location section on the About page.</p>
                </div>
                
                <h3 class="text-lg font-medium text-white mt-6 mb-4">Address & Map Information</h3>
                <p class="text-sm text-gray-400 mb-4">These settings control the address and map displayed in the About page location section.</p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="about_address_line1" class="block text-[#9CA3AF] text-sm font-medium mb-2">Address Line 1 <span class="text-red-500">*</span></label>
                        <input type="text" id="about_address_line1" name="about_address_line1" value="{{ $settings->about_address_line1 ?? '3rd Floor YMCA Bldg. 350' }}" placeholder="Building name, street number" class="w-full bg-[#374151] border border-[#4B5563] text-white rounded-lg p-3 focus:outline-none focus:border-[#9CA3AF]" required>
                        <p class="text-xs text-[#9CA3AF] mt-1">Required. First line of your business address on the About page.</p>
                    </div>
                    <div>
                        <label for="about_address_line2" class="block text-[#9CA3AF] text-sm font-medium mb-2">Address Line 2</label>
                        <input type="text" id="about_address_line2" name="about_address_line2" value="{{ $settings->about_address_line2 ?? 'Villegas St. Ermita, Manila, Philippines' }}" placeholder="City, state, country" class="w-full bg-[#374151] border border-[#4B5563] text-white rounded-lg p-3 focus:outline-none focus:border-[#9CA3AF]">
                        <p class="text-xs text-[#9CA3AF] mt-1">Optional. Second line of your business address on the About page.</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="about_phone_number" class="block text-[#9CA3AF] text-sm font-medium mb-2">Phone Number <span class="text-red-500">*</span></label>
                        <input type="text" id="about_phone_number" name="about_phone_number" value="{{ $settings->about_phone_number ?? '0998 558 5911' }}" placeholder="e.g. 0998 558 5911" class="w-full bg-[#374151] border border-[#4B5563] text-white rounded-lg p-3 focus:outline-none focus:border-[#9CA3AF]" required>
                        <p class="text-xs text-[#9CA3AF] mt-1">Required. Your business contact number shown on the About page.</p>
                    </div>
                    <div>
                        <label for="about_email" class="block text-[#9CA3AF] text-sm font-medium mb-2">Email <span class="text-red-500">*</span></label>
                        <input type="email" id="about_email" name="about_email" value="{{ $settings->about_email ?? 'mtfc987@gmail.com' }}" 
                               placeholder="e.g. mtfc987@gmail.com" 
                               oninput="validateEmail(this)"
                               class="w-full bg-[#374151] border border-[#4B5563] text-white rounded-lg p-3 focus:outline-none focus:border-[#9CA3AF]" required>
                        <p class="text-xs text-[#9CA3AF] mt-1">Required. Your business email address shown on the About page.</p>
                        <p class="text-xs text-red-500 mt-1 email-error-message hidden"></p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="about_working_hours_weekday" class="block text-[#9CA3AF] text-sm font-medium mb-2">Weekday Hours <span class="text-red-500">*</span></label>
                        <input type="text" id="about_working_hours_weekday" name="about_working_hours_weekday" value="{{ $settings->about_working_hours_weekday ?? 'Monday-Friday: 6am - 10pm' }}" placeholder="e.g. Monday-Friday: 6am - 10pm" class="w-full bg-[#374151] border border-[#4B5563] text-white rounded-lg p-3 focus:outline-none focus:border-[#9CA3AF]" required>
                        <p class="text-xs text-[#9CA3AF] mt-1">Required. Your business hours during weekdays shown on the About page.</p>
                    </div>
                    <div>
                        <label for="about_working_hours_weekend" class="block text-[#9CA3AF] text-sm font-medium mb-2">Weekend Hours <span class="text-red-500">*</span></label>
                        <input type="text" id="about_working_hours_weekend" name="about_working_hours_weekend" value="{{ $settings->about_working_hours_weekend ?? 'Weekends: 8am - 8pm' }}" placeholder="e.g. Weekends: 8am - 8pm" class="w-full bg-[#374151] border border-[#4B5563] text-white rounded-lg p-3 focus:outline-none focus:border-[#9CA3AF]" required>
                        <p class="text-xs text-[#9CA3AF] mt-1">Required. Your business hours during weekends shown on the About page.</p>
                    </div>
                </div>
                
                <div class="mb-6">
                    <label for="about_google_maps_embed_url" class="block text-[#9CA3AF] text-sm font-medium mb-2">Google Maps Embed URL <span class="text-red-500">*</span></label>
                    <input type="text" id="about_google_maps_embed_url" name="about_google_maps_embed_url" value="{{ $settings->about_google_maps_embed_url ?? 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3861.6504900122!2d120.9798166!3d14.5886964!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397cbfd5dc3aa3f%3A0xd241d9c495788763!2sManila%20Total%20Fitness%20Center!5e0!3m2!1sen!2sph!4v1710000000000!5m2!1sen!2sph' }}" placeholder="https://www.google.com/maps/embed?..." class="w-full bg-[#374151] border border-[#4B5563] text-white rounded-lg p-3 focus:outline-none focus:border-[#9CA3AF]" required>
                    <p class="text-xs text-[#9CA3AF] mt-1">Required. The URL from the Google Maps embed iframe code for the About page. To get this URL, go to Google Maps, search for your location, click 'Share', select 'Embed a map', and copy the URL from the iframe src attribute.</p>
                </div>
                
                <div class="flex justify-end">
                    <button type="submit" class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5">
                        <i class="fas fa-save mr-2"></i> Save About Page Settings
                    </button>
                </div>
            </form>
        </div>

        <!-- Contact Page Tab Content -->
        <div id="contact-content" class="tab-content p-6 hidden">
            <form id="contactForm" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="address_line1" class="block text-[#9CA3AF] text-sm font-medium mb-2">Address Line 1 <span class="text-red-500">*</span></label>
                        <input type="text" id="address_line1" name="address_line1" value="{{ $settings->address_line1 ?? '3rd Floor YMCA Bldg. 350' }}" placeholder="Building name, street number" class="w-full bg-[#374151] border border-[#4B5563] text-white rounded-lg p-3 focus:outline-none focus:border-[#9CA3AF]" required>
                        <p class="text-xs text-[#9CA3AF] mt-1">Required. First line of your business address.</p>
                    </div>
                    <div>
                        <label for="address_line2" class="block text-[#9CA3AF] text-sm font-medium mb-2">Address Line 2</label>
                        <input type="text" id="address_line2" name="address_line2" value="{{ $settings->address_line2 ?? 'Villegas St. Ermita, Manila, Philippines' }}" placeholder="City, state, country" class="w-full bg-[#374151] border border-[#4B5563] text-white rounded-lg p-3 focus:outline-none focus:border-[#9CA3AF]">
                        <p class="text-xs text-[#9CA3AF] mt-1">Optional. Second line of your business address.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="phone_number" class="block text-[#9CA3AF] text-sm font-medium mb-2">Phone Number <span class="text-red-500">*</span></label>
                        <input type="text" id="phone_number" name="phone_number" value="{{ $settings->phone_number ?? '0998 558 5911' }}" placeholder="e.g. 0998 558 5911" class="w-full bg-[#374151] border border-[#4B5563] text-white rounded-lg p-3 focus:outline-none focus:border-[#9CA3AF]" required>
                        <p class="text-xs text-[#9CA3AF] mt-1">Required. Your business contact number.</p>
                    </div>
                    <div>
                        <label for="email" class="block text-[#9CA3AF] text-sm font-medium mb-2">Email <span class="text-red-500">*</span></label>
                        <input type="email" id="email" name="email" value="{{ $settings->email ?? 'mtfc987@gmail.com' }}" placeholder="e.g. mtfc987@gmail.com" class="w-full bg-[#374151] border border-[#4B5563] text-white rounded-lg p-3 focus:outline-none focus:border-[#9CA3AF]" required>
                        <p class="text-xs text-[#9CA3AF] mt-1">Required. Your business email address.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="working_hours_weekday" class="block text-[#9CA3AF] text-sm font-medium mb-2">Weekday Hours <span class="text-red-500">*</span></label>
                        <input type="text" id="working_hours_weekday" name="working_hours_weekday" value="{{ $settings->working_hours_weekday ?? 'Monday-Friday: 6am - 10pm' }}" placeholder="e.g. Monday-Friday: 6am - 10pm" class="w-full bg-[#374151] border border-[#4B5563] text-white rounded-lg p-3 focus:outline-none focus:border-[#9CA3AF]" required>
                        <p class="text-xs text-[#9CA3AF] mt-1">Required. Your business hours during weekdays.</p>
                    </div>
                    <div>
                        <label for="working_hours_weekend" class="block text-[#9CA3AF] text-sm font-medium mb-2">Weekend Hours <span class="text-red-500">*</span></label>
                        <input type="text" id="working_hours_weekend" name="working_hours_weekend" value="{{ $settings->working_hours_weekend ?? 'Weekends: 8am - 8pm' }}" placeholder="e.g. Weekends: 8am - 8pm" class="w-full bg-[#374151] border border-[#4B5563] text-white rounded-lg p-3 focus:outline-none focus:border-[#9CA3AF]" required>
                        <p class="text-xs text-[#9CA3AF] mt-1">Required. Your business hours during weekends.</p>
                    </div>
                </div>

                <h2 class="text-xl font-medium text-white border-b border-gray-700 pb-3 mt-8">Social Media Links</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="facebook_url" class="block text-[#9CA3AF] text-sm font-medium mb-2">Facebook URL</label>
                        <input type="url" id="facebook_url" name="facebook_url" value="{{ $settings->facebook_url ?? 'https://www.facebook.com/people/Manila-Total-Fitness-Center/100064094075912/' }}" placeholder="https://www.facebook.com/your-page" class="w-full bg-[#374151] border border-[#4B5563] text-white rounded-lg p-3 focus:outline-none focus:border-[#9CA3AF]">
                        <p class="text-xs text-[#9CA3AF] mt-1">Optional. Your Facebook page URL.</p>
                    </div>
                    <div>
                        <label for="instagram_url" class="block text-[#9CA3AF] text-sm font-medium mb-2">Instagram URL</label>
                        <input type="url" id="instagram_url" name="instagram_url" value="{{ $settings->instagram_url ?? 'https://www.instagram.com/manilatotalfc/?igsh=ZWhoeWphanZtdDFw#' }}" placeholder="https://www.instagram.com/your-account" class="w-full bg-[#374151] border border-[#4B5563] text-white rounded-lg p-3 focus:outline-none focus:border-[#9CA3AF]">
                        <p class="text-xs text-[#9CA3AF] mt-1">Optional. Your Instagram profile URL.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="twitter_url" class="block text-[#9CA3AF] text-sm font-medium mb-2">Twitter URL</label>
                        <input type="url" id="twitter_url" name="twitter_url" value="{{ $settings->twitter_url }}" placeholder="https://twitter.com/your-account" class="w-full bg-[#374151] border border-[#4B5563] text-white rounded-lg p-3 focus:outline-none focus:border-[#9CA3AF]">
                        <p class="text-xs text-[#9CA3AF] mt-1">Optional. Your Twitter profile URL.</p>
                    </div>
                    <div>
                        <label for="youtube_url" class="block text-[#9CA3AF] text-sm font-medium mb-2">YouTube URL</label>
                        <input type="url" id="youtube_url" name="youtube_url" value="{{ $settings->youtube_url }}" placeholder="https://www.youtube.com/your-channel" class="w-full bg-[#374151] border border-[#4B5563] text-white rounded-lg p-3 focus:outline-none focus:border-[#9CA3AF]">
                        <p class="text-xs text-[#9CA3AF] mt-1">Optional. Your YouTube channel URL.</p>
                    </div>
                </div>


                
                <div class="mb-6">
                    <label for="google_maps_embed_url" class="block text-[#9CA3AF] text-sm font-medium mb-2">Google Maps Embed URL <span class="text-red-500">*</span></label>
                    <input type="text" id="google_maps_embed_url" name="google_maps_embed_url" value="{{ $settings->google_maps_embed_url ?? 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3861.7948173582903!2d120.98023537590552!3d14.57964228012682!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397cbf4fa6a0427%3A0xe3092a043ee7a16a!2s3%2FF%20350%20A.%20Villegas%20St%2C%20Ermita%2C%20Manila%2C%201000%20Metro%20Manila!5e0!3m2!1sen!2sph!4v1693306422214!5m2!1sen!2sph' }}" placeholder="https://www.google.com/maps/embed?..." class="w-full bg-[#374151] border border-[#4B5563] text-white rounded-lg p-3 focus:outline-none focus:border-[#9CA3AF]" required>
                    <p class="text-xs text-[#9CA3AF] mt-1">Required. The URL from the Google Maps embed iframe code. To get this URL, go to Google Maps, search for your location, click 'Share', select 'Embed a map', and copy the URL from the iframe src attribute.</p>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5">
                        <i class="fas fa-save mr-2"></i> Save Contact Page Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Function to validate email with common domains
function validateEmail(input) {
    const validDomains = [
        'gmail.com', 'yahoo.com', 'outlook.com', 'hotmail.com', 'icloud.com', 
        'msn.com', 'aol.com', 'ymail.com', 'me.com', 'live.com', 
        'protonmail.com', 'zoho.com'
    ];
    
    const email = input.value.toLowerCase().trim();
    let isValid = false;
    
    // Find the error element
    let errorElement = input.nextElementSibling;
    while (errorElement && !errorElement.classList.contains('email-error-message')) {
        errorElement = errorElement.nextElementSibling;
    }
    
    // Reset validation state
    input.classList.remove('border-red-500', 'border-green-500');
    if (errorElement) {
        errorElement.classList.add('hidden');
    }
    
    if (!email) {
        return false; // Empty input, let HTML5 validation handle it
    }
    
    if (email && email.includes('@')) {
        const parts = email.split('@');
        if (parts.length === 2 && parts[0].length > 0) {
            const domain = parts[1].trim();
            
            // Check if domain is in our list of valid domains
            if (!validDomains.includes(domain)) {
                input.classList.add('border-red-500');
                if (errorElement) {
                    errorElement.textContent = 'Please use a common email domain like gmail.com, yahoo.com, outlook.com, etc.';
                    errorElement.classList.remove('hidden');
                }
                isValid = false;
            } else if (!/^[a-z0-9._%+-]+$/.test(parts[0])) {
                // Check if the username part contains valid characters
                input.classList.add('border-red-500');
                if (errorElement) {
                    errorElement.textContent = 'Email contains invalid characters';
                    errorElement.classList.remove('hidden');
                }
                isValid = false;
            } else {
                // Valid email
                input.classList.add('border-green-500');
                isValid = true;
            }
        } else {
            input.classList.add('border-red-500');
            if (errorElement) {
                errorElement.textContent = 'Please enter a valid email format';
                errorElement.classList.remove('hidden');
            }
            isValid = false;
        }
    } else {
        input.classList.add('border-red-500');
        if (errorElement) {
            errorElement.textContent = 'Email must contain an @ symbol';
            errorElement.classList.remove('hidden');
        }
        isValid = false;
    }
    
    return isValid;
}
document.addEventListener('DOMContentLoaded', function() {
    // Tab functionality
    function openTab(tabName) {
        // Hide all tab contents
        document.querySelectorAll('.tab-content').forEach(tab => {
            tab.classList.add('hidden');
        });
        
        // Remove active class from all tabs
        document.querySelectorAll('button[id$="-tab"]').forEach(button => {
            button.classList.remove('border-blue-500', 'active-tab');
            button.classList.add('border-transparent');
        });
        
        // Show the selected tab content
        document.getElementById(tabName + '-content').classList.remove('hidden');
        
        // Set the active tab
        document.getElementById(tabName + '-tab').classList.add('border-blue-500', 'active-tab');
        document.getElementById(tabName + '-tab').classList.remove('border-transparent');
    }
    
    // Make the openTab function globally available
    window.openTab = openTab;
    
    // Add value button functionality
    document.getElementById('add-value-btn').addEventListener('click', function() {
        const container = document.getElementById('values-container');
        const newValueField = document.createElement('div');
        newValueField.className = 'flex items-center space-x-2';
        newValueField.innerHTML = `
            <input type="text" name="our_values[]" class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
            <button type="button" class="remove-value-btn text-red-500 hover:text-red-400">
                <i class="fas fa-trash"></i>
            </button>
        `;
        container.appendChild(newValueField);
        
        // Add event listener to the new remove button
        newValueField.querySelector('.remove-value-btn').addEventListener('click', function() {
            container.removeChild(newValueField);
        });
    });
    
    // Remove value button functionality
    document.querySelectorAll('.remove-value-btn').forEach(button => {
        button.addEventListener('click', function() {
            const valueField = this.parentNode;
            valueField.parentNode.removeChild(valueField);
        });
    });
    
    // About form submission
    document.getElementById('aboutForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Clear any existing error messages
        document.querySelectorAll('.error-message').forEach(el => el.remove());
        document.querySelectorAll('.border-red-500').forEach(el => el.classList.remove('border-red-500'));
        
        // Show loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalBtnText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Saving...';
        submitBtn.disabled = true;
        
        // Manually construct FormData to ensure all fields are included
        const formData = new FormData();
        
        // Add CSRF token
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        
        // Add text fields
        formData.append('about_us_content', document.getElementById('about_us_content').value);
        formData.append('community_content', document.getElementById('community_content').value);
        formData.append('location_section_title', document.getElementById('location_section_title').value);
        formData.append('location_section_description', document.getElementById('location_section_description').value);
        
        // Add address and contact fields for About page
        formData.append('about_address_line1', document.getElementById('about_address_line1').value);
        formData.append('about_address_line2', document.getElementById('about_address_line2').value);
        formData.append('about_phone_number', document.getElementById('about_phone_number').value);
        formData.append('about_email', document.getElementById('about_email').value);
        formData.append('about_working_hours_weekday', document.getElementById('about_working_hours_weekday').value);
        formData.append('about_working_hours_weekend', document.getElementById('about_working_hours_weekend').value);
        formData.append('about_google_maps_embed_url', document.getElementById('about_google_maps_embed_url').value);
        
        // Add our_values as array
        const valueInputs = document.querySelectorAll('input[name="our_values[]"]');
        valueInputs.forEach((input, index) => {
            formData.append(`our_values[${index}]`, input.value);
        });
        
        fetch('{{ route("admin.site-settings.about") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    title: 'Success!',
                    text: data.message,
                    icon: 'success',
                    confirmButtonColor: '#3085d6'
                });
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: data.message || 'An error occurred while saving settings.',
                    icon: 'error',
                    confirmButtonColor: '#3085d6'
                });
                
                // Show validation errors if any
                if (data.errors) {
                    Object.keys(data.errors).forEach(key => {
                        const field = document.querySelector(`[name="${key}"]`);
                        if (field) {
                            field.classList.add('border-red-500');
                            const errorSpan = document.createElement('span');
                            errorSpan.className = 'text-red-500 text-xs mt-1';
                            errorSpan.textContent = data.errors[key][0];
                            field.parentNode.appendChild(errorSpan);
                        }
                    });
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                title: 'Error!',
                text: 'An unexpected error occurred.',
                icon: 'error',
                confirmButtonColor: '#3085d6'
            });
        })
        .finally(() => {
            // Restore button state
            submitBtn.innerHTML = originalBtnText;
            submitBtn.disabled = false;
        });
    });
    
    // Contact form submission
    document.getElementById('contactForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Clear any existing error messages
        document.querySelectorAll('.error-message').forEach(el => el.remove());
        document.querySelectorAll('.border-red-500').forEach(el => el.classList.remove('border-red-500'));
        
        // Show loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalBtnText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Saving...';
        submitBtn.disabled = true;
        
        // Manually construct FormData to ensure all fields are included
        const formData = new FormData();
        
        // Add CSRF token
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        
        // Add all form fields manually
        formData.append('address_line1', document.getElementById('address_line1').value);
        formData.append('address_line2', document.getElementById('address_line2').value);
        formData.append('phone_number', document.getElementById('phone_number').value);
        formData.append('email', document.getElementById('email').value);
        formData.append('working_hours_weekday', document.getElementById('working_hours_weekday').value);
        formData.append('working_hours_weekend', document.getElementById('working_hours_weekend').value);
        formData.append('facebook_url', document.getElementById('facebook_url').value);
        formData.append('instagram_url', document.getElementById('instagram_url').value);
        formData.append('twitter_url', document.getElementById('twitter_url').value);
        formData.append('youtube_url', document.getElementById('youtube_url').value);
        formData.append('google_maps_embed_url', document.getElementById('google_maps_embed_url').value);
        
        fetch('{{ route("admin.site-settings.contact") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    title: 'Success!',
                    text: data.message,
                    icon: 'success',
                    confirmButtonColor: '#3085d6'
                });
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: data.message || 'An error occurred while saving settings.',
                    icon: 'error',
                    confirmButtonColor: '#3085d6'
                });
                
                // Show validation errors if any
                if (data.errors) {
                    Object.keys(data.errors).forEach(key => {
                        const field = document.querySelector(`[name="${key}"]`);
                        if (field) {
                            field.classList.add('border-red-500');
                            const errorSpan = document.createElement('span');
                            errorSpan.className = 'text-red-500 text-xs mt-1';
                            errorSpan.textContent = data.errors[key][0];
                            field.parentNode.appendChild(errorSpan);
                        }
                    });
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                title: 'Error!',
                text: 'An unexpected error occurred.',
                icon: 'error',
                confirmButtonColor: '#3085d6'
            });
        })
        .finally(() => {
            // Restore button state
            submitBtn.innerHTML = originalBtnText;
            submitBtn.disabled = false;
        });
    });
});
</script>
@endpush
@endsection
