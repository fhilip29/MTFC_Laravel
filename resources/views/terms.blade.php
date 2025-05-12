@extends('layouts.app')

@section('title', 'Terms of Use - Manila Total Fitness Center')

@section('content')
<div class="py-12 bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header with Logo -->
        <div class="text-center mb-8">
            <img src="{{ asset('assets/MTFC_LOGO.PNG') }}" alt="MTFC Logo" class="h-20 mx-auto mb-4">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Terms of Use</h1>
            <p class="text-gray-600">Last Updated: {{ date('F d, Y') }}</p>
        </div>
        
        <!-- Terms Content Card -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-10">
            <!-- Introduction -->
            <div class="border-b border-gray-200 bg-gray-50 p-6">
                <p class="text-gray-700">
                    Welcome to Manila Total Fitness Center. These Terms of Use govern your use of our website, 
                    in-person services, online shopping, membership plans, and community features. By accessing or using our services, 
                    you agree to be bound by these terms. Please read them carefully.
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
                        <p>By creating an account, accessing our website, purchasing products, joining our online community, or using any of our services, you acknowledge that you have read, understood, and agree to be bound by these Terms of Use.</p>
                        
                        <p>If you do not agree with any part of these terms, you should not use our services.</p>
                    </div>
                </div>
                
                <!-- Section 2 -->
                <div class="terms-section">
                    <h2 class="flex items-center text-xl font-semibold text-gray-800 mb-3">
                        <span class="flex items-center justify-center bg-red-600 text-white rounded-full w-8 h-8 mr-3">2</span>
                        Account Registration & Security
                    </h2>
                    <div class="pl-11 text-gray-700 space-y-2">
                        <p>When creating an account with MTFC, you agree to:</p>
                        <ul class="list-disc ml-5 space-y-1">
                            <li>Provide accurate, current, and complete information</li>
                            <li>Maintain and promptly update your account information</li>
                            <li>Keep your password secure and confidential</li>
                            <li>Accept responsibility for all activities that occur under your account</li>
                            <li>Notify us immediately of any unauthorized use of your account</li>
                        </ul>
                        <p class="mt-3">We reserve the right to suspend or terminate accounts that contain false or outdated information, or that we believe have been compromised.</p>
                    </div>
                </div>
                
                <!-- Section 3 -->
                <div class="terms-section">
                    <h2 class="flex items-center text-xl font-semibold text-gray-800 mb-3">
                        <span class="flex items-center justify-center bg-red-600 text-white rounded-full w-8 h-8 mr-3">3</span>
                        Membership & Subscription Terms
                    </h2>
                    <div class="pl-11 text-gray-700 space-y-2">
                        <p>By purchasing a membership plan:</p>
                        <ul class="list-disc ml-5 space-y-1">
                            <li>You agree to pay all fees associated with your selected plan</li>
                         
                            <li>Membership benefits and access are specific to the plan you purchase</li>
                            <li>Subscription cancellations must be submitted through your account settings or by contacting customer service</li>
                            <li>Strictly no refunds</li>
                            <li>We reserve the right to modify our pricing and membership benefits with notice</li>
                        </ul>
                    </div>
                </div>
                
                <!-- Section 4 -->
                <div class="terms-section">
                    <h2 class="flex items-center text-xl font-semibold text-gray-800 mb-3">
                        <span class="flex items-center justify-center bg-red-600 text-white rounded-full w-8 h-8 mr-3">4</span>
                        Online Shop & Purchases
                    </h2>
                    <div class="pl-11 text-gray-700 space-y-2">
                        <p>When making purchases through our online shop:</p>
                        <ul class="list-disc ml-5 space-y-1">
                            <li>Product descriptions, images, and pricing are as accurate as possible, but we do not guarantee they are error-free</li>
                            <li>We reserve the right to limit order quantities or refuse orders</li>
                            <li>Payment for products must be made at the time of purchase</li>
                            <li>Shipping, delivery, and return policies are as specified on our website</li>
                            <li>Product availability and stock levels are subject to change without notice</li>
                        </ul>
                    </div>
                </div>
                
                <!-- Section 5 -->
                <div class="terms-section">
                    <h2 class="flex items-center text-xl font-semibold text-gray-800 mb-3">
                        <span class="flex items-center justify-center bg-red-600 text-white rounded-full w-8 h-8 mr-3">5</span>
                        Subscriptions & Cancellations
                    </h2>
                    <div class="pl-11 text-gray-700 space-y-2">
                        <p>For fitness sessions</p>
                        <ul class="list-disc ml-5 space-y-1">
                            
                            <li>Cancellations are non refundable</li>
                            
                            <li>MTFC reserves the right to cancel or reschedule sessions with reasonable notice</li>
                            <li>Session rescheduling is subject to availability</li>
                        </ul>
                    </div>
                </div>
                
                <!-- Section 6 -->
                <div class="terms-section">
                    <h2 class="flex items-center text-xl font-semibold text-gray-800 mb-3">
                        <span class="flex items-center justify-center bg-red-600 text-white rounded-full w-8 h-8 mr-3">6</span>
                        Community Guidelines & User Content
                    </h2>
                    <div class="pl-11 text-gray-700 space-y-2">
                        <p>When using our community features or posting content:</p>
                        <ul class="list-disc ml-5 space-y-1">
                            <li>You are responsible for all content you post or share</li>
                            <li>Content must not violate any applicable laws or infringe on others' rights</li>
                            <li>Prohibited content includes: harassment, discrimination, explicit material, spam, and commercial solicitations</li>
                            <li>MTFC reserves the right to remove any content at our discretion</li>
                            <li>Repeated violations may result in account suspension or termination</li>
                        </ul>
                        <p class="mt-3">By posting content, you grant MTFC a non-exclusive, royalty-free license to use, display, and distribute your content in connection with our services.</p>
                    </div>
                </div>
                
                <!-- Section 7 -->
                <div class="terms-section">
                    <h2 class="flex items-center text-xl font-semibold text-gray-800 mb-3">
                        <span class="flex items-center justify-center bg-red-600 text-white rounded-full w-8 h-8 mr-3">7</span>
                        Intellectual Property
                    </h2>
                    <div class="pl-11 text-gray-700 space-y-2">
                        <p>All content, logos, trademarks, software, designs, and materials on our platforms are the property of Manila Total Fitness Center or its licensors and are protected by intellectual property laws.</p>
                        <p class="mt-2">You may not use, reproduce, distribute, modify, or create derivative works of our content without explicit permission from MTFC.</p>
                    </div>
                </div>
                
                <!-- Section 8 -->
                <div class="terms-section">
                    <h2 class="flex items-center text-xl font-semibold text-gray-800 mb-3">
                        <span class="flex items-center justify-center bg-red-600 text-white rounded-full w-8 h-8 mr-3">8</span>
                        Limitation of Liability
                    </h2>
                    <div class="pl-11 text-gray-700 space-y-2">
                        <p>To the fullest extent permitted by law:</p>
                        <ul class="list-disc ml-5 space-y-1">
                            <li>MTFC provides services "as is" without any warranties, express or implied</li>
                            <li>We do not guarantee that our services will be error-free, uninterrupted, or secure</li>
                            <li>MTFC is not liable for any indirect, incidental, special, or consequential damages</li>
                            <li>Our liability for any claim arising from these terms or our services is limited to the amount you paid us in the 12 months preceding the claim</li>
                        </ul>
                    </div>
                </div>
                
                <!-- Section 9 -->
                <div class="terms-section">
                    <h2 class="flex items-center text-xl font-semibold text-gray-800 mb-3">
                        <span class="flex items-center justify-center bg-red-600 text-white rounded-full w-8 h-8 mr-3">9</span>
                        Termination
                    </h2>
                    <div class="pl-11 text-gray-700 space-y-2">
                        <p>MTFC reserves the right to:</p>
                        <ul class="list-disc ml-5 space-y-1">
                            <li>Suspend or terminate your access to our services at any time for violations of these terms</li>
                            <li>Discontinue any feature or service at our discretion</li>
                            <li>Restrict access to certain features for non-compliance with these terms</li>
                        </ul>
                        <p class="mt-2">You may terminate your account at any time through your account settings or by contacting customer service, subject to any active subscription terms.</p>
                    </div>
                </div>
                
                <!-- Section 10 -->
                <div class="terms-section">
                    <h2 class="flex items-center text-xl font-semibold text-gray-800 mb-3">
                        <span class="flex items-center justify-center bg-red-600 text-white rounded-full w-8 h-8 mr-3">10</span>
                        Changes to Terms
                    </h2>
                    <div class="pl-11 text-gray-700 space-y-2">
                        <p>We may update these Terms of Use from time to time. We will notify you of significant changes through:</p>
                        <ul class="list-disc ml-5 space-y-1">
                            <li>Email notifications to your registered email address</li>
                            <li>Notices on our website</li>
                            <li>Updated "Last Updated" date at the top of these terms</li>
                        </ul>
                        <p class="mt-2">Your continued use of our services after such changes constitutes your acceptance of the revised terms.</p>
                    </div>
                </div>
                
                <!-- Section 11 -->
                <div class="terms-section">
                    <h2 class="flex items-center text-xl font-semibold text-gray-800 mb-3">
                        <span class="flex items-center justify-center bg-red-600 text-white rounded-full w-8 h-8 mr-3">11</span>
                        Contact Us
                    </h2>
                    <div class="pl-11 text-gray-700 space-y-2"  >
                        <p>If you have questions or concerns about these Terms of Use, please contact us at:</p>
                        <p class="font-medium">Email: mtfc987@gmail.com</p>
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