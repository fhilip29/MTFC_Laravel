{{-- resources/views/about.blade.php --}}
@extends('layouts.app')

@section('title', 'About Us')

@section('content')
<style>
    /* Fade-in animation keyframes */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .animate-on-scroll {
        opacity: 0;
        transform: translateY(20px);
        transition: opacity 0.6s ease, transform 0.6s ease;
    }
    
    .animate-on-scroll.active {
        opacity: 1;
        transform: translateY(0);
    }
    
    /* Banner animations */
    .animate-fade-in {
        animation: fadeIn 0.8s ease forwards;
    }
    
    .animate-slide-in {
        animation: fadeIn 0.8s ease 0.2s forwards;
        opacity: 0;
    }
</style>

<!-- Banner Section -->
<section class="relative text-white h-[400px] md:h-[450px] overflow-hidden">
    <div class="absolute inset-0 bg-black">
        <img src="{{ asset('assets/BG_GYM.jpg') }}" alt="Fitness Training" class="w-full h-full object-cover opacity-70">
        <div class="absolute inset-0 bg-gradient-to-r from-black via-transparent to-black opacity-60"></div>
    </div>
    <div class="absolute inset-0 flex flex-col justify-center items-center px-6 md:px-16 text-center z-10">
        <span class="inline-block px-3 py-1 bg-[#FA5455] text-white text-xs font-bold rounded-full mb-4 animate-fade-in">ABOUT US</span>
        <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold tracking-tight mb-2 md:mb-4 animate-slide-in">OUR MISSION IS TO</h1>
        <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold tracking-tight animate-slide-in"><span class="text-[#FA5455]">HELP YOU SUCCEED</span></h1>
    </div>
</section>

<!-- About and Community Section -->
<section class="bg-[#1e1e1e] py-16 md:py-20 relative">
    <!-- Background pattern -->
    <div class="absolute inset-0 opacity-5">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%23ffffff\" fill-opacity=\"1\"%3E%3Cpath d=\"M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')"></div>
    </div>
    
    <div class="container mx-auto px-6 md:px-8 relative z-10">
        <div class="flex flex-col lg:flex-row gap-12 items-start">
            <!-- Text Content Column -->
            <div class="lg:w-2/3 space-y-12 animate-on-scroll">
                <!-- About Us -->
                <div class="bg-[#222] p-8 rounded-xl shadow-lg">
                    <h2 class="text-2xl md:text-3xl font-bold text-white mb-6 flex items-center">
                        <span class="inline-block w-12 h-1 bg-[#FA5455] mr-4"></span>
                        About Us
                    </h2>
                    <div class="text-gray-300 leading-relaxed">
                        {!! $settings->about_us_content ?? 'Manila Total Fitness Center is the perfect place for all your fitness needs. We offer a wide range of programs suitable for everyone, regardless of fitness level or interest. Our modern gym is fully equipped with the latest strength and cardio equipment.' !!}
                    </div>
                </div>

                <!-- Community -->
                <div class="bg-[#222] p-8 rounded-xl shadow-lg animate-on-scroll">
                    <h2 class="text-2xl md:text-3xl font-bold text-white mb-6 flex items-center">
                        <span class="inline-block w-12 h-1 bg-[#FA5455] mr-4"></span>
                        Community
                    </h2>
                    <div class="text-gray-300 leading-relaxed">
                        {!! $settings->community_content ?? 'Manila Total Fitness isn\'t just a gym—it\'s a strong community dedicated to holistic health and personal growth. We unite individuals pursuing physical, mental, and emotional wellness. Our foundation is built on support, empowerment, and a shared journey toward better living.' !!}
                    </div>
                </div>
            </div>

            <!-- Image Column -->
            <div class="lg:w-1/3 sticky top-24 animate-on-scroll">
                <div class="bg-[#222] p-4 rounded-xl shadow-xl">
                    <img src="{{ asset('assets/about_3.jpg') }}" alt="Gym" class="w-full h-auto rounded-lg shadow-lg transform hover:scale-105 transition-transform duration-500">
                    
                    <div class="mt-8 p-4 bg-[#1a1a1a] rounded-lg">
                        <h3 class="text-xl font-bold text-white mb-4">Our Values</h3>
                        <ul class="space-y-3">
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
                                @foreach($valuesArray as $value)
                                <li class="flex items-center text-gray-300">
                                    <span class="inline-block bg-[#FA5455] p-1 rounded-full mr-3"><i class="fas fa-check text-white text-xs"></i></span>
                                    {{ $value }}
                                </li>
                                @endforeach
                            @else
                                <li class="flex items-center text-gray-300">
                                    <span class="inline-block bg-[#FA5455] p-1 rounded-full mr-3"><i class="fas fa-check text-white text-xs"></i></span>
                                    Integrity in all we do
                                </li>
                                <li class="flex items-center text-gray-300">
                                    <span class="inline-block bg-[#FA5455] p-1 rounded-full mr-3"><i class="fas fa-check text-white text-xs"></i></span>
                                    Excellence in service
                                </li>
                                <li class="flex items-center text-gray-300">
                                    <span class="inline-block bg-[#FA5455] p-1 rounded-full mr-3"><i class="fas fa-check text-white text-xs"></i></span>
                                    Community support
                                </li>
                                <li class="flex items-center text-gray-300">
                                    <span class="inline-block bg-[#FA5455] p-1 rounded-full mr-3"><i class="fas fa-check text-white text-xs"></i></span>
                                    Results-driven approach
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Visit Us Section -->
<section class="bg-[#1e1e1e] py-16 md:py-20 mb-0">
    <div class="container mx-auto px-6 md:px-8">
        <div class="text-center mb-12 animate-on-scroll">
            <span class="inline-block px-3 py-1 bg-[#FA5455] text-white text-xs font-bold rounded-full mb-3">LOCATION</span>
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">{{ $settings->location_section_title ?? 'Come Visit Us' }}</h2>
            <p class="text-gray-400 max-w-xl mx-auto">{{ $settings->location_section_description ?? "We're conveniently located in the heart of Manila, ready to welcome you to our fitness community." }}</p>
        </div>
        
        <div class="flex flex-col lg:flex-row gap-8 items-start">
            <!-- Google Map -->
            <div class="w-full lg:w-2/3 rounded-xl overflow-hidden shadow-2xl relative animate-on-scroll">
                <div class="w-full h-96 rounded-lg overflow-hidden shadow-lg">
                    <iframe 
                        src="{{ $settings->about_google_maps_embed_url ?? $settings->google_maps_embed_url ?? 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3861.6504900122!2d120.9798166!3d14.5886964!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397cbfd5dc3aa3f%3A0xd241d9c495788763!2sManila%20Total%20Fitness%20Center!5e0!3m2!1sen!2sph!4v1710000000000!5m2!1sen!2sph' }}"
                        width="100%" 
                        height="100%" 
                        style="border:0;" 
                        allowfullscreen="" 
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>

            <!-- Address Info -->
            <div class="w-full lg:w-1/3 animate-on-scroll">
                <div class="bg-[#222] p-8 rounded-xl shadow-lg">
                    <h3 class="text-2xl font-bold text-white mb-6">Our Address</h3>
                    
                    <ul class="space-y-5">
                        <li class="flex items-start space-x-4">
                            <div class="bg-[#FA5455] p-3 rounded-full mt-1 flex-shrink-0">
                                <i class="fas fa-map-marker-alt text-white"></i>
                            </div>
                            <div>
                                <h4 class="text-white font-semibold mb-1">Location</h4>
                                <p class="text-gray-300">{{ $settings->about_address_line1 ?? $settings->address_line1 ?? '3rd Floor YMCA Bldg. 350' }}</p>
                                <p class="text-gray-300">{{ $settings->about_address_line2 ?? $settings->address_line2 ?? 'Villegas St. Ermita' }}</p>
                            </div>
                        </li>
                        
                        <li class="flex items-start space-x-4">
                            <div class="bg-[#FA5455] p-3 rounded-full mt-1 flex-shrink-0">
                                <i class="fas fa-phone-alt text-white"></i>
                            </div>
                            <div>
                                <h4 class="text-white font-semibold mb-1">Contact</h4>
                                <p class="text-gray-300">{{ $settings->about_phone_number ?? $settings->phone_number ?? '0998 558 5911' }}</p>
                                <p class="text-gray-300">{{ $settings->about_email ?? $settings->email ?? 'mtfc987@gmail.com' }}</p>
                            </div>
                        </li>
                        
                        <li class="flex items-start space-x-4">
                            <div class="bg-[#FA5455] p-3 rounded-full mt-1 flex-shrink-0">
                                <i class="fas fa-clock text-white"></i>
                            </div>
                            <div>
                                <h4 class="text-white font-semibold mb-1">Hours</h4>
                                <p class="text-gray-300">{{ $settings->about_working_hours_weekday ?? $settings->working_hours_weekday ?? 'Monday-Friday: 6am - 10pm' }}</p>
                                <p class="text-gray-300">{{ $settings->about_working_hours_weekend ?? $settings->working_hours_weekend ?? 'Weekends: 8am - 8pm' }}</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Add dark spacer div instead of margin -->

    </div>
</section>

<!-- Dark spacer div to match the background -->
<div class="bg-[#1e1e1e] h-24"></div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Get all elements that need to animate on scroll
        const animatedElements = document.querySelectorAll('.animate-on-scroll');
        
        // Check if an element is in viewport
        function isInViewport(element) {
            const rect = element.getBoundingClientRect();
            return (
                rect.top <= (window.innerHeight || document.documentElement.clientHeight) * 0.85
            );
        }
        
        // Function to check and apply animations
        function checkScroll() {
            animatedElements.forEach(element => {
                if (isInViewport(element)) {
                    element.classList.add('active');
                }
            });
        }
        
        // Initialize - check elements immediately on page load
        checkScroll();
        
        // Add scroll event listener
        window.addEventListener('scroll', checkScroll);
    });
</script>

@endsection
