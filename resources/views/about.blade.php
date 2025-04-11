{{-- resources/views/about.blade.php --}}
@extends('layouts.app')

@section('title', 'About Us')

@section('content')
<section class="bg-gray-100">
    <div class="w-full mx-auto">
        <!-- Banner -->
        <div class="relative text-white text-center">
            <img src="{{ asset('assets/about1.jpg') }}" alt="Fitness Training" class="w-full h-[300px] object-cover">
            <div class="absolute inset-0 flex flex-col justify-center items-center bg-black bg-opacity-50 p-4 md:px-36">
                <p class="uppercase tracking-wide text-sm">Manila Total Fitness Center</p>
                <h2 class="text-3xl font-bold">OUR MISSION IS TO</h2>
                <h2 class="text-3xl font-bold">HELP YOU SUCCEED.</h2>
            </div>
        </div>

        <!-- About Us & Community -->
        <div class="flex flex-col md:flex-row px-6 md:px-36 py-12 gap-12">
            <div class="w-full md:w-2/3 space-y-10 text-justify">
                <!-- About Us -->
                <div>
                    <h3 class="text-xl font-bold text-gray-800">About Us:</h3>
                    <p class="text-gray-700 mt-4">
                        Manila Total Fitness Center is the perfect place for all your fitness needs. We provide a wide variety of programs suitable for everyone, no matter their fitness level or interests. Our modern gym is filled with the latest equipment for strength and cardio workouts.
                    </p>
                    <p class="text-gray-700 mt-2">
                        If you’re interested in martial arts, we have skilled instructors teaching boxing, taekwondo, BJJ, and much more. Our knowledgeable trainers offer personalized support to help you enhance your skills, get fit, and gain confidence. Whether you want to build muscle, lose weight, or learn self-defense, Manila Total Fitness Center has what you need to reach your goals.
                    </p>
                </div>

                <!-- Community -->
                <div>
                    <h3 class="text-xl font-bold text-gray-800">Community:</h3>
                    <p class="text-gray-700 mt-4">
                        Manila Total Fitness is more than just a gym—it’s a thriving community in Metro Manila committed to holistic health and well-being. We bring together people looking to improve their fitness, wellness, and mindset.
                    </p>
                    <p class="text-gray-700 mt-2">
                        Our community is built on connection, empowerment, and personal growth, ensuring an environment where every individual can thrive. Whether you’re an experienced athlete or new to fitness, our community creates a welcoming space for all.
                    </p>
                </div>
            </div>

            <!-- Image Aside -->
            <div class="w-full md:w-1/3 mt-8 md:mt-24">
                <img src="{{ asset('assets/about2.jpg') }}" alt="Gym" class="w-full h-auto rounded-xl shadow-lg">
            </div>
        </div>

        <!-- Map Section -->
        <div class="flex flex-col md:flex-row px-6 md:px-36 py-12 mt-14 bg-gray-200 gap-12">
            <div class="w-full md:w-1/2">
                <!-- Google Map Embed -->
                <div class="w-full h-[450px] rounded-xl shadow-lg overflow-hidden">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3861.1790663571073!2d120.97751048432549!3d14.588870141015025!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397ca21f18a1789%3A0x5e2b67a947ccb95e!2sYMCA%20of%20Manila!5e0!3m2!1sen!2sph!4v1743879656062!5m2!1sen!2sph"
                        width="100%"
                        height="100%"
                        style="border:0;"
                        allowfullscreen=""
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>

            <div class="w-full md:w-1/2">
                <h2 class="text-2xl font-bold text-gray-800">Come Visit Us</h2>
                <div class="mt-6 space-y-2 text-lg font-semibold text-gray-700">
                    <p>3rd Floor YMCA Bldg. 350</p>
                    <p>Villegas St. Ermita</p>
                    <p>Manila, Philippines</p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
