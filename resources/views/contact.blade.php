@extends('layouts.app')

@section('title', 'Contact Us')

@section('content')
<style>
    /* Page transition fade-in animation */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .page-transition {
        animation: fadeIn 0.8s ease forwards;
    }
    
    /* Element animations */
    .animate-item {
        opacity: 0;
        transform: translateY(20px);
        animation: fadeIn 0.8s ease forwards;
    }
    
    .animate-item:nth-child(1) { animation-delay: 0.2s; }
    .animate-item:nth-child(2) { animation-delay: 0.4s; }
    .animate-item:nth-child(3) { animation-delay: 0.6s; }
    .animate-item:nth-child(4) { animation-delay: 0.8s; }
    
    /* Map animation */
    .map-animation {
        opacity: 0;
        transform: translateY(30px);
        animation: fadeIn 0.8s ease 0.6s forwards;
    }
</style>

<section class="bg-white min-h-screen py-12 px-4 md:px-10 lg:px-20 page-transition">
    <div class="max-w-5xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl md:text-5xl font-bold text-black mb-4">Get in Touch</h1>
            <p class="text-black text-lg max-w-2xl mx-auto">Have questions about our gym or services? Our team is ready to help you with any inquiries you might have.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Contact Information -->
            <div class="md:col-span-1">
                <div class="bg-black rounded-xl shadow-2xl p-6 border border-gray-800 text-white h-full">
                    <h2 class="text-xl font-bold mb-6 text-white border-b border-gray-700 pb-3">Contact Information</h2>
                    
                    <div class="space-y-6">
                        <div class="flex items-start animate-item">
                            <div class="bg-gray-800 p-3 rounded-full mr-4">
                                <i class="fas fa-map-marker-alt text-white"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-white">Address</h3>
                                <p class="text-gray-300 mt-1">Manila Total Fitness Center</p>
                                <p class="text-gray-300 mt-1">3rd Floor YMCA Bldg. 350 Villegas St. Ermita, Manila, Philippines</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start animate-item">
                            <div class="bg-gray-800 p-3 rounded-full mr-4">
                                <i class="fas fa-phone text-white"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-white">Phone</h3>
                                <p class="text-gray-300 mt-1">0998 558 5911</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start animate-item">
                            <div class="bg-gray-800 p-3 rounded-full mr-4">
                                <i class="fas fa-envelope text-white"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-white">Email</h3>
                                <p class="text-gray-300 mt-1">mtfc987@gmail.com</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start animate-item">
                            <div class="bg-gray-800 p-3 rounded-full mr-4">
                                <i class="fas fa-clock text-white"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-white">Working Hours</h3>
                                <p class="text-gray-300 mt-1">8:00 am - 8:00 pm</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Social Media Links -->
                    <div class="mt-8 pt-6 border-t border-gray-700">
                        <h3 class="font-semibold mb-4">Follow Us</h3>
                        <div class="flex space-x-4">
                            <a href="https://www.facebook.com/people/Manila-Total-Fitness-Center/100064094075912/" target="_blank" class="bg-gray-800 hover:bg-gray-700 p-3 rounded-full text-white transition-all duration-300">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="https://www.instagram.com/manilatotalfc/?igsh=ZWhoeWphanZtdDFw#" target="_blank" class="bg-gray-800 hover:bg-gray-700 p-3 rounded-full text-white transition-all duration-300">
                                <i class="fab fa-instagram"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Map and Contact Admin Button -->
            <div class="md:col-span-2">
                <div class="bg-white border border-gray-200 rounded-xl shadow-lg p-6 mb-8 animate-item">
                    <h2 class="text-2xl font-bold text-black mb-6">Message Us</h2>
                    <p class="text-gray-700 mb-6">Have questions or need assistance? Our admin support team is here to help you. Click the button below to send a direct message to our admin team.</p>
                    
                    <div class="flex justify-center">
                        <a href="{{ route('user.messages.compose', ['admin' => true]) }}" class="bg-black text-white py-3 px-6 rounded-lg hover:bg-gray-800 transition duration-200 inline-flex items-center justify-center text-base">
                            <i class="fas fa-envelope mr-2"></i> Contact Admin Support
                        </a>
                    </div>
                </div>
                
                <!-- Google Map -->
                <div class="bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden map-animation">
                    <h2 class="text-2xl font-bold text-black p-6">Our Location</h2>
                    <div class="w-full h-96 rounded-lg overflow-hidden shadow-lg">
                        <iframe 
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3861.6504900122!2d120.9798166!3d14.5886964!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397cbfd5dc3aa3f%3A0xd241d9c495788763!2sManila%20Total%20Fitness%20Center!5e0!3m2!1sen!2sph!4v1710000000000!5m2!1sen!2sph"
                            width="100%" 
                            height="100%" 
                            style="border:0;" 
                            allowfullscreen="" 
                            loading="lazy">
                        </iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
