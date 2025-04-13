{{-- resources/views/about.blade.php --}}
@extends('layouts.app')

@section('title', 'About Us')

@section('content')
<section class="bg-white">
    <!-- Banner Section -->
    <div class="relative text-white">
        <img src="{{ asset('assets/BG_GYM.jpg') }}" alt="Fitness Training" class="w-full h-[320px] object-cover brightness-75">
        <div class="absolute inset-0 flex flex-col justify-center items-center px-4 md:px-36 text-center">
            <p class="uppercase tracking-widest text-sm md:text-base text-gray-300 animate-fade-in">Manila Total Fitness Center</p>
            <h1 class="text-4xl md:text-5xl font-extrabold mt-2 animate-slide-in">OUR MISSION IS TO</h1>
            <h1 class="text-4xl md:text-5xl font-extrabold animate-slide-in">HELP YOU SUCCEED.</h1>
        </div>
    </div>

    <!-- About and Community Section -->
    <div class="px-6 md:px-36 py-16 flex flex-col md:flex-row gap-12">
        <!-- Text Content -->
        <div class="md:w-2/3 space-y-12 text-justify animate-fade-in">
            <!-- About Us -->
            <div>
                <h2 class="text-2xl md:text-3xl font-bold text-black border-b-4 border-blue-600 pb-2 inline-block">About Us</h2>
                <p class="text-gray-800 mt-4 leading-relaxed">
                    Manila Total Fitness Center is the perfect place for all your fitness needs. We offer a wide range of programs suitable for everyone, regardless of fitness level or interest. Our modern gym is fully equipped with the latest strength and cardio equipment.
                </p>
                <p class="text-gray-800 mt-4 leading-relaxed">
                    Passionate about martial arts? We have expert instructors in boxing, taekwondo, BJJ, and more. With personalized support from our trainers, you'll enhance your skills, build confidence, and achieve your fitness goals—whether it's muscle gain, weight loss, or self-defense.
                </p>
            </div>

            <!-- Community -->
            <div>
                <h2 class="text-2xl md:text-3xl font-bold text-black border-b-4 border-blue-600 pb-2 inline-block">Community</h2>
                <p class="text-gray-800 mt-4 leading-relaxed">
                    Manila Total Fitness isn't just a gym—it's a strong community dedicated to holistic health and personal growth. We unite individuals pursuing physical, mental, and emotional wellness.
                </p>
                <p class="text-gray-800 mt-4 leading-relaxed">
                    Our foundation is built on support, empowerment, and a shared journey toward better living. Whether you're just starting out or a seasoned athlete, you'll find a place to belong and thrive here.
                </p>
            </div>
        </div>

        <!-- Image -->
        <div class="md:w-1/3 flex justify-center items-center mt-6 md:mt-24 animate-slide-in">
            <img src="{{ asset('assets/about_3.jpg') }}" alt="Gym" class="w-full h-auto rounded-2xl shadow-2xl transform hover:scale-105 transition-transform duration-300">
        </div>
    </div>

    <!-- Visit Us Section -->
    <div class="bg-gradient-to-br from-blue-50 to-white py-16 pb-24 px-6 md:px-36 animate-fade-in">
        <div class="flex flex-col md:flex-row gap-12">
            <!-- Google Map -->
            <div class="w-full md:w-1/2 rounded-2xl overflow-hidden shadow-lg">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3861.1790663571073!2d120.97751048432549!3d14.588870141015025!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397ca21f18a1789%3A0x5e2b67a947ccb95e!2sYMCA%20of%20Manila!5e0!3m2!1sen!2sph!4v1743879656062!5m2!1sen!2sph"
                    width="100%"
                    height="450"
                    style="border:0;"
                    allowfullscreen=""
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                    class="w-full h-full">
                </iframe>
            </div>

            <!-- Address Info -->
            <div class="w-full md:w-1/2 flex flex-col justify-center">
                <h2 class="text-3xl font-bold text-black mb-6">Come Visit Us</h2>
                <div class="space-y-2 text-lg text-gray-800 font-medium">
                    <p><i class="fas fa-map-marker-alt mr-2 text-red-600"></i>3rd Floor YMCA Bldg. 350</p>
                    <p><i class="fas fa-map-pin mr-2 text-red-600"></i>Villegas St. Ermita</p>
                    <p><i class="fas fa-location-arrow mr-2 text-red-600"></i>Manila, Philippines</p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
