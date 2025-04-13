@extends('layouts.app')

@section('title', 'Privacy Policy - Manila Total Fitness Center')

@section('content')
<div class="py-12 bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header with Logo -->
        <div class="text-center mb-8">
            <img src="{{ asset('assets/MTFC_LOGO.PNG') }}" alt="MTFC Logo" class="h-20 mx-auto mb-4">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Privacy Policy</h1>
            <p class="text-gray-600">Effective Date: April 5, 2025</p>
        </div>
        
        <!-- Privacy Content Card -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-10">
            <!-- Introduction -->
            <div class="border-b border-gray-200 bg-gray-50 p-6">
                <p class="text-gray-700">
                    Welcome to Manila Total Fitness Center. This Privacy Policy explains how we collect, use, and protect your information.
                    Please read this policy carefully to understand our practices regarding your personal data.
                </p>
            </div>
            
            <!-- Privacy Sections -->
            <div class="p-6 space-y-8">
                <!-- Section 1 -->
                <div class="terms-section">
                    <h2 class="flex items-center text-xl font-semibold text-gray-800 mb-3">
                        <span class="flex items-center justify-center bg-red-600 text-white rounded-full w-8 h-8 mr-3">1</span>
                        Information We Collect
                    </h2>
                    <div class="pl-11 text-gray-700 space-y-2">
                        <h3 class="font-medium mb-2">Personal Information:</h3>
                        <ul class="list-disc ml-5 space-y-1">
                            <li>Name and contact details</li>
                            <li>Email address</li>
                            <li>Billing information</li>
                            <li>Fitness goals and preferences</li>
                        </ul>
                        <h3 class="font-medium mb-2 mt-4">Usage Data:</h3>
                        <ul class="list-disc ml-5 space-y-1">
                            <li>Login activity and session information</li>
                            <li>Training preferences and workout history</li>
                            <li>Interaction with our services</li>
                        </ul>
                    </div>
                </div>
                
                <!-- Section 2 -->
                <div class="terms-section">
                    <h2 class="flex items-center text-xl font-semibold text-gray-800 mb-3">
                        <span class="flex items-center justify-center bg-red-600 text-white rounded-full w-8 h-8 mr-3">2</span>
                        How We Use Your Information
                    </h2>
                    <div class="pl-11 text-gray-700 space-y-2">
                        <ul class="list-disc ml-5 space-y-1">
                            <li>To provide and improve our fitness services</li>
                            <li>To personalize your workout experience</li>
                            <li>To process payments and maintain your account</li>
                            <li>To send important updates and communications</li>
                            <li>To ensure the security of our services</li>
                        </ul>
                    </div>
                </div>
                
                <!-- Section 3 -->
                <div class="terms-section">
                    <h2 class="flex items-center text-xl font-semibold text-gray-800 mb-3">
                        <span class="flex items-center justify-center bg-red-600 text-white rounded-full w-8 h-8 mr-3">3</span>
                        Data Security
                    </h2>
                    <div class="pl-11 text-gray-700 space-y-2">
                        <p>We implement industry-standard security measures to protect your data, including:</p>
                        <ul class="list-disc ml-5 space-y-1">
                            <li>Encryption of sensitive information</li>
                            <li>Regular security audits</li>
                            <li>Secure data storage systems</li>
                            <li>Limited access to personal information</li>
                        </ul>
                    </div>
                </div>
                
                <!-- Section 4 -->
                <div class="terms-section">
                    <h2 class="flex items-center text-xl font-semibold text-gray-800 mb-3">
                        <span class="flex items-center justify-center bg-red-600 text-white rounded-full w-8 h-8 mr-3">4</span>
                        Your Rights & Choices
                    </h2>
                    <div class="pl-11 text-gray-700 space-y-2">
                        <p>You have the right to:</p>
                        <ul class="list-disc ml-5 space-y-1">
                            <li>Access your personal data</li>
                            <li>Update or correct your information</li>
                            <li>Request deletion of your data</li>
                            <li>Opt-out of marketing communications</li>
                            <li>Export your data</li>
                        </ul>
                    </div>
                </div>
                
                <!-- Section 5 -->
                <div class="terms-section">
                    <h2 class="flex items-center text-xl font-semibold text-gray-800 mb-3">
                        <span class="flex items-center justify-center bg-red-600 text-white rounded-full w-8 h-8 mr-3">5</span>
                        Cookies & Tracking
                    </h2>
                    <div class="pl-11 text-gray-700 space-y-2">
                        <p>We use cookies and similar technologies to:</p>
                        <ul class="list-disc ml-5 space-y-1">
                            <li>Improve site functionality</li>
                            <li>Analyze usage patterns</li>
                            <li>Enhance user experience</li>
                            <li>Remember your preferences</li>
                        </ul>
                    </div>
                </div>
                
                <!-- Section 6 -->
                <div class="terms-section">
                    <h2 class="flex items-center text-xl font-semibold text-gray-800 mb-3">
                        <span class="flex items-center justify-center bg-red-600 text-white rounded-full w-8 h-8 mr-3">6</span>
                        Contact Us
                    </h2>
                    <div class="pl-11 text-gray-700 space-y-2">
                        <p>For questions about this Privacy Policy, please contact us at:</p>
                        <p class="font-medium">Email: privacy@mtfc.com</p>
                        <p class="font-medium">Phone: (555) 123-4567</p>
                        <p class="font-medium">Address: 123 Fitness Street, Gym City, GC 12345</p>
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