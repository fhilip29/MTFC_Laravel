@extends('layouts.app')

@section('content')
@include('components.waiver-modal')
<section class="relative w-full min-h-screen text-gray-800">
    <!-- Background image -->
    <div class="absolute inset-0 bg-cover bg-center bg-no-repeat blur-sm" style="background-image: url('/assets/gym-bg.jpg');"></div>

    <!-- Overlay to darken -->
    <div class="absolute inset-0 bg-black opacity-50"></div>

    <!-- Main content -->
    <div class="relative z-10 max-w-7xl mx-auto px-6 py-16">
        <!-- Title -->
        <h1 class="text-3xl md:text-5xl font-bold text-white text-center mb-10">Fitness Pricing Table</h1>

        <!-- Tabs -->
<div class="flex justify-center space-x-4 mb-10 overflow-x-auto">
    <a href="{{ route('pricing.gym') }}" class="px-5 py-2 rounded-md text-white font-semibold bg-gray-300 text-black">
        GYM
    </a>
    <a href="{{ route('pricing.boxing') }}" class="px-5 py-2 rounded-md text-white font-semibold bg-gray-700 hover:bg-gray-600">
        Boxing
    </a>
    <a href="{{ route('pricing.muay') }}" class="px-5 py-2 rounded-md text-white font-semibold bg-gray-700 hover:bg-gray-600">
        Muay Thai
    </a>
    <a href="{{ route('pricing.jiu') }}" class="px-5 py-2 rounded-md text-white font-semibold bg-gray-700 hover:bg-gray-600">
        Jiu-Jitsu
    </a>
        </div>

        <!-- Pricing Cards -->
        <div class="flex flex-wrap justify-center gap-8">
            <!-- Card 1: Daily -->
            <div class="w-80 bg-white rounded-2xl shadow-lg p-6">
                <span class="inline-block text-xs font-semibold text-gray-500 mb-2">Membership</span>
                <h2 class="text-xl font-bold mb-3">Daily</h2>
                <ul class="text-sm text-gray-700 space-y-2 mb-6">
                    <li class="flex items-start gap-2">
                        <x-check-icon /> Access to gym equipment
                    </li>
                    <li class="flex items-start gap-2">
                        <x-check-icon /> Use of locker room facilities
                    </li>
                    <li class="flex items-start gap-2">
                        <x-check-icon /> Use of swimming pool
                    </li>
                </ul>
                <div class="flex justify-between items-center mt-auto">
                    <span class="text-lg font-semibold text-gray-800">₱1000 / month</span>
                    <span class="text-sm px-3 py-1 bg-gray-100 text-gray-600 rounded-full">Subscribed</span>
                </div>
            </div>

            <!-- Card 2: Monthly -->
            <div class="w-80 bg-white rounded-2xl shadow-lg p-6">
                <span class="inline-block text-xs font-semibold text-gray-500 mb-2">Membership</span>
                <h2 class="text-xl font-bold mb-3">Monthly</h2>
                <ul class="text-sm text-gray-700 space-y-2 mb-6">
                    <li class="flex items-start gap-2">
                        <x-check-icon /> Access to gym equipment
                    </li>
                    <li class="flex items-start gap-2">
                        <x-check-icon /> Use of locker room facilities
                    </li>
                    <li class="flex items-start gap-2">
                        <x-check-icon /> Use of swimming pool
                    </li>
                </ul>
                <div class="flex justify-between items-center mt-auto">
                    <span class="text-lg font-semibold text-gray-800">₱2000 / month</span>
                    <button onclick="openWaiverModal()" class="text-sm px-3 py-1 bg-blue-600 text-white rounded-full hover:bg-blue-700 transition">
                        Join Now
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
