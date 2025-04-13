@extends('layouts.app')

@section('title', 'Terms of Use - Manila Total Fitness Center')

@section('content')
<div class="py-12 bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header with Logo -->
        <div class="text-center mb-8">
            <img src="{{ asset('assets/MTFC_LOGO.PNG') }}" alt="MTFC Logo" class="h-20 mx-auto mb-4">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Terms of Use</h1>
            <p class="text-gray-600">Effective Date: April 5, 2023</p>
        </div>
        
        <!-- Terms Content Card -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-10">
            <!-- Introduction -->
            <div class="border-b border-gray-200 bg-gray-50 p-6">
                <p class="text-gray-700">
                    Welcome to Manila Total Fitness Center. These Terms of Use govern your use of our facilities, services, and website. 
                    Please read these terms carefully before using our services.
                </p>
            </div>
            
            <!-- Terms Sections -->
            <div class="p-6 space-y-8">
                <!-- Section 1 -->
                <div class="terms-section">
                    <h2 class="flex items-center text-xl font-semibold text-gray-800 mb-3">
                        <span class="flex items-center justify-center bg-red-600 text-white rounded-full w-8 h-8 mr-3">1</span>
                        Acceptance of Terms
                    </h2>
                    <div class="pl-11 text-gray-700 space-y-2">
                        <p>By accessing or using Manila Total Fitness Center's facilities, services, or website, you agree to comply with and be bound by these Terms of Use.</p>
                        <p>If you do not agree with any part of these terms, you may not use our services.</p>
                    </div>
                </div>
                
                <!-- Section 2 -->
                <div class="terms-section">
                    <h2 class="flex items-center text-xl font-semibold text-gray-800 mb-3">
                        <span class="flex items-center justify-center bg-red-600 text-white rounded-full w-8 h-8 mr-3">2</span>
                        Use of Services
                    </h2>
                    <div class="pl-11 text-gray-700 space-y-2">
                        <p>Our services are intended for users who are at least 13 years old.</p>
                        <p>You are responsible for ensuring that your use of our services complies with applicable laws and regulations.</p>
                        <p>Membership and access to specific facilities may require additional agreements or waivers.</p>
                    </div>
                </div>
                
                <!-- Section 3 -->
                <div class="terms-section">
                    <h2 class="flex items-center text-xl font-semibold text-gray-800 mb-3">
                        <span class="flex items-center justify-center bg-red-600 text-white rounded-full w-8 h-8 mr-3">3</span>
                        User Conduct
                    </h2>
                    <div class="pl-11 text-gray-700 space-y-2">
                        <p>You agree not to misuse our facilities or services, including but not limited to:</p>
                        <ul class="list-disc ml-5 space-y-1">
                            <li>Engaging in any form of harassment or disruptive behavior</li>
                            <li>Damaging equipment or facilities</li>
                            <li>Using services in a way that interferes with others' enjoyment</li>
                            <li>Violating gym etiquette or safety protocols</li>
                        </ul>
                    </div>
                </div>
                
                <!-- Section 4 -->
                <div class="terms-section">
                    <h2 class="flex items-center text-xl font-semibold text-gray-800 mb-3">
                        <span class="flex items-center justify-center bg-red-600 text-white rounded-full w-8 h-8 mr-3">4</span>
                        Intellectual Property
                    </h2>
                    <div class="pl-11 text-gray-700 space-y-2">
                        <p>All content, logos, trademarks, and materials on our website and in our facilities are the property of Manila Total Fitness Center or its licensors.</p>
                        <p>You may not use, reproduce, or distribute our intellectual property without prior written permission.</p>
                    </div>
                </div>
                
                <!-- Section 5 -->
                <div class="terms-section">
                    <h2 class="flex items-center text-xl font-semibold text-gray-800 mb-3">
                        <span class="flex items-center justify-center bg-red-600 text-white rounded-full w-8 h-8 mr-3">5</span>
                        Termination
                    </h2>
                    <div class="pl-11 text-gray-700 space-y-2">
                        <p>We reserve the right to suspend or terminate your access to our services if you violate these terms.</p>
                        <p>Membership cancellation policies are outlined in your membership agreement.</p>
                    </div>
                </div>
                
                <!-- Section 6 -->
                <div class="terms-section">
                    <h2 class="flex items-center text-xl font-semibold text-gray-800 mb-3">
                        <span class="flex items-center justify-center bg-red-600 text-white rounded-full w-8 h-8 mr-3">6</span>
                        Modifications
                    </h2>
                    <div class="pl-11 text-gray-700 space-y-2">
                        <p>We may update these terms from time to time. Continued use of our services after changes constitutes acceptance of the modified terms.</p>
                        <p>Significant changes will be communicated through our website or email.</p>
                    </div>
                </div>
                
                <!-- Section 7 -->
                <div class="terms-section">
                    <h2 class="flex items-center text-xl font-semibold text-gray-800 mb-3">
                        <span class="flex items-center justify-center bg-red-600 text-white rounded-full w-8 h-8 mr-3">7</span>
                        Contact Us
                    </h2>
                    <div class="pl-11 text-gray-700 space-y-2">
                        <p>For questions about these Terms of Use, please contact us at:</p>
                        <p class="font-medium">Email: info@manilatotalfitness.com</p>
                        <p class="font-medium">Phone: (02) 8123-4567</p>
                        <p class="font-medium">Address: 123 Fitness Street, Manila, Philippines</p>
                    </div>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="border-t border-gray-200 bg-gray-50 p-6 text-center">
                <p class="text-gray-600 text-sm">
                    &copy; 2023 Manila Total Fitness Center. All rights reserved.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection