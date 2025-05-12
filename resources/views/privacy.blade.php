@extends('layouts.app')

@section('title', 'Privacy Policy - Manila Total Fitness Center')

@section('content')
<div class="py-12 bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header with Logo -->
        <div class="text-center mb-8">
            <img src="{{ asset('assets/MTFC_LOGO.PNG') }}" alt="MTFC Logo" class="h-20 mx-auto mb-4">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Privacy Policy</h1>
            <p class="text-gray-600">Last Updated: {{ date('F d, Y') }}</p>
        </div>
        
        <!-- Privacy Content Card -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-10">
            <!-- Introduction -->
            <div class="border-b border-gray-200 bg-gray-50 p-6">
                <p class="text-gray-700">
                    At Manila Total Fitness Center (MTFC), we value your privacy and are committed to protecting your personal information. 
                    This Privacy Policy explains how we collect, use, share, and safeguard your data when you use our website, 
                    in-person services, and other digital platforms.
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
                        <h3 class="font-medium mb-2">Account Information:</h3>
                        <ul class="list-disc ml-5 space-y-1">
                            <li>Personal details (name, email, phone number)</li>
                            <li>Profile information and photos</li>
                            <li>Login credentials (password is encrypted)</li>
                            <li>Gender and fitness goals</li>
                            <li>Account preferences and settings</li>
                        </ul>
                        
                        <h3 class="font-medium mb-2 mt-4">Membership & Billing Information:</h3>
                        <ul class="list-disc ml-5 space-y-1">
                            <li>Subscription plan details</li>
                            <li>Payment information and transaction history</li>
                            <li>Billing address and payment methods</li>
                            <li>Subscription renewal dates and history</li>
                        </ul>
                        
                        <h3 class="font-medium mb-2 mt-4">Activity Data:</h3>
                        <ul class="list-disc ml-5 space-y-1">
                            <li>Session and attendance records</li>
                            <li>Trainer interactions</li>
                            <li>Product purchases and shopping cart information</li>
                            <li>Communication preferences and message history</li>
                            <li>Community interactions and content you post</li>
                        </ul>
                        
                        <h3 class="font-medium mb-2 mt-4">Technical Information:</h3>
                        <ul class="list-disc ml-5 space-y-1">
                            <li>Browser type and operating system</li>
                            <li>Login activity and session data</li>
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
                        <p>We use your information for the following purposes:</p>
                        <ul class="list-disc ml-5 space-y-1">
                            <li>Creating and managing your MTFC account</li>
                            <li>Processing payments and managing subscriptions</li>                           
                            <li>Processing product orders and managing your shopping cart</li>
                            <li>Enabling community interactions and social features</li>
                            <li>Communicating updates, promotions, and announcements</li>
                            <li>Improving our products, services, and website functionality</li>
                            <li>Analyzing usage patterns and optimizing user experience</li>
                            <li>Ensuring the security of your account and our platforms</li>
                            <li>Complying with legal obligations and resolving disputes</li>
                        </ul>
                    </div>
                </div>
                
                <!-- Section 3 -->
                <div class="terms-section">
                    <h2 class="flex items-center text-xl font-semibold text-gray-800 mb-3">
                        <span class="flex items-center justify-center bg-red-600 text-white rounded-full w-8 h-8 mr-3">3</span>
                        How We Share Information
                    </h2>
                    <div class="pl-11 text-gray-700 space-y-2">
                        <p>We may share your information with:</p>
                        <ul class="list-disc ml-5 space-y-1">
                            <li><strong>Service Providers:</strong> Payment processors, cloud hosting services, and other vendors who help us deliver our services</li>
                            <li><strong>MTFC Staff and Trainers:</strong> To facilitate your fitness journey and gym experience</li>
                            <li><strong>Other Members:</strong> When you participate in community features, message boards, or social interactions (based on your privacy settings)</li>
                            <li><strong>Legal Requirements:</strong> When required by law, legal process, or to protect our rights and safety</li>
                        </ul>
                        <p class="mt-3">We do not sell your personal information to third parties for advertising purposes.</p>
                    </div>
                </div>
                
                <!-- Section 4 -->
                <div class="terms-section">
                    <h2 class="flex items-center text-xl font-semibold text-gray-800 mb-3">
                        <span class="flex items-center justify-center bg-red-600 text-white rounded-full w-8 h-8 mr-3">4</span>
                        Data Security
                    </h2>
                    <div class="pl-11 text-gray-700 space-y-2">
                        <p>We implement industry-standard security measures to protect your data, including:</p>
                        <ul class="list-disc ml-5 space-y-1">
                            <li>Secure HTTPS connections for all website interactions</li>
                            <li>Encryption of sensitive information, including passwords</li>
                            <li>Secure payment processing through trusted providers</li>
                            <li>Regular security assessments and monitoring</li>
                            <li>Limited employee access to personal information</li>
                            <li>Secure data storage and backup procedures</li>
                        </ul>
                        <p class="mt-3">While we strive to protect your information, no method of transmission over the internet is 100% secure. We encourage you to use strong passwords and keep your login credentials confidential.</p>
                    </div>
                </div>
                
                <!-- Section 5 -->
                <div class="terms-section">
                    <h2 class="flex items-center text-xl font-semibold text-gray-800 mb-3">
                        <span class="flex items-center justify-center bg-red-600 text-white rounded-full w-8 h-8 mr-3">5</span>
                        Your Rights & Choices
                    </h2>
                    <div class="pl-11 text-gray-700 space-y-2">
                        <p>Depending on your location, you may have the following rights:</p>
                        <ul class="list-disc ml-5 space-y-1">
                            <li>Access and review your personal information</li>
                            <li>Update or correct inaccurate information</li>
                            <li>Delete certain personal information</li>
                            <li>Restrict or object to certain processing of your data</li>
                            <li>Export your data in a structured format</li>
                            <li>Opt-out of marketing communications</li>
                            <li>Manage cookie preferences and tracking technologies</li>
                        </ul>
                        <p class="mt-3">You can exercise many of these rights through your account settings page. For additional assistance, please contact us using the information in the "Contact Us" section.</p>
                    </div>
                </div>
                
              
                
              
                <div class="terms-section">
                    <h2 class="flex items-center text-xl font-semibold text-gray-800 mb-3">
                        <span class="flex items-center justify-center bg-red-600 text-white rounded-full w-8 h-8 mr-3">6</span>
                        Changes to This Policy
                    </h2>
                    <div class="pl-11 text-gray-700 space-y-2">
                        <p>We may update this Privacy Policy periodically to reflect changes in our practices, services, or applicable laws. We will post the revised policy on our website with an updated "Last Updated" date.</p>
                        <p class="mt-2">For significant changes, we may notify you via email or through an in-app notification. We encourage you to review this policy regularly.</p>
                    </div>
                </div>
                
                <div class="terms-section">
                    <h2 class="flex items-center text-xl font-semibold text-gray-800 mb-3">
                        <span class="flex items-center justify-center bg-red-600 text-white rounded-full w-8 h-8 mr-3">7</span>
                        Contact Us
                    </h2>
                    <div class="pl-11 text-gray-700 space-y-2">
                        <p>If you have questions, concerns, or requests regarding this Privacy Policy or your personal information, please contact us at:</p>
                        <p class="font-medium">Email: privacy@manilatotalfitness.com</p>
                        <p class="font-medium">Phone: 0998 558 5911</p>
                        <p class="font-medium">Address: 123 Fitness Street, Manila, Philippines</p>
                    </div>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="border-t border-gray-200 bg-gray-50 p-6 text-center">
                <p class="text-gray-600 text-sm">
                    &copy; {{ date('Y') }} Manila Total Fitness Center. All rights reserved.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection