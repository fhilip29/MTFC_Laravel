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
        <h1 class="text-3xl md:text-5xl font-bold text-white text-center mb-6">Jiu Jitsu Membership Plans</h1>
        <p class="text-lg text-white text-center mb-10">Learn Brazilian Jiu Jitsu from certified black belt instructors</p>

        <!-- Tabs -->
        <div class="flex justify-center space-x-4 mb-10 overflow-x-auto">
            <a href="{{ route('pricing.gym') }}" class="px-5 py-2 rounded-md text-white font-semibold bg-gray-700 hover:bg-gray-600">
                GYM
            </a>
            <a href="{{ route('pricing.boxing') }}" class="px-5 py-2 rounded-md text-white font-semibold bg-gray-700 hover:bg-gray-600">
                Boxing
            </a>
            <a href="{{ route('pricing.muay') }}" class="px-5 py-2 rounded-md text-white font-semibold bg-gray-700 hover:bg-gray-600">
                Muay Thai
            </a>
            <a href="{{ route('pricing.jiu') }}" class="px-5 py-2 rounded-md font-semibold bg-gray-300 text-black">
                Jiu-Jitsu
            </a>
        </div>

        <!-- Pricing Cards -->
        <div class="flex flex-wrap justify-center gap-8">
            <!-- Monthly Membership -->
            <div class="w-80 bg-white rounded-2xl shadow-lg p-6 flex flex-col border-2 border-blue-600">
                <span class="inline-block text-xs font-semibold text-gray-500 mb-2">Membership</span>
                <h2 class="text-xl font-bold mb-3">Monthly Membership</h2>
                <ul class="text-sm text-gray-700 space-y-2 mb-6">
                    <li class="flex items-start gap-2">
                        <x-check-icon /> Unlimited Jiu Jitsu classes
                    </li>
                    <li class="flex items-start gap-2">
                        <x-check-icon /> Belt promotion eligibility
                    </li>
                    <li class="flex items-start gap-2">
                        <x-check-icon /> Open mat access
                    </li>
                    <li class="flex items-start gap-2">
                        <x-check-icon /> Full gym access included
                    </li>
                </ul>
                <div class="flex justify-between items-center mt-auto">
                    <span class="text-lg font-semibold text-gray-800">₱3,500 / month</span>
                    @if(Auth::check())
                        @if($userHasActive)
                            <span class="text-sm px-3 py-1 bg-gray-100 text-gray-600 rounded-full">Active</span>
                        @elseif($userRole === 'admin')
                            <span class="text-sm px-3 py-1 bg-gray-100 text-gray-600 rounded-full">Admin</span>
                        @elseif($userRole === 'trainer')
                            <span class="text-sm px-3 py-1 bg-gray-100 text-gray-600 rounded-full">Trainer</span>
                        @else
                            <button onclick="openWaiverModal('jiu', 'monthly', '3500.00')" class="text-sm px-3 py-1 bg-blue-600 text-white rounded-full hover:bg-blue-700 transition">
                                Join Now
                            </button>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="text-sm px-3 py-1 bg-gray-300 text-gray-700 rounded-full hover:bg-gray-400 transition">
                            Login
                        </a>
                    @endif
                </div>
            </div>

            <!-- Per-Session Pass -->
            <div class="w-80 bg-white rounded-2xl shadow-lg p-6 flex flex-col">
                <span class="inline-block text-xs font-semibold text-gray-500 mb-2">Membership</span>
                <h2 class="text-xl font-bold mb-3">Per-Session Pass</h2>
                <ul class="text-sm text-gray-700 space-y-2 mb-6">
                    <li class="flex items-start gap-2">
                        <x-check-icon /> Single Jiu Jitsu session
                    </li>
                    <li class="flex items-start gap-2">
                        <x-check-icon /> Gi rental available
                    </li>
                    <li class="flex items-start gap-2">
                        <x-check-icon /> Perfect for trying it out
                    </li>
                </ul>
                <div class="flex justify-between items-center mt-auto">
                    <span class="text-lg font-semibold text-gray-800">₱400 / session</span>
                    @if(Auth::check())
                        @if($userHasActive)
                            <span class="text-sm px-3 py-1 bg-gray-100 text-gray-600 rounded-full">Active</span>
                        @elseif($userRole === 'admin')
                            <span class="text-sm px-3 py-1 bg-gray-100 text-gray-600 rounded-full">Admin</span>
                        @elseif($userRole === 'trainer')
                            <span class="text-sm px-3 py-1 bg-gray-100 text-gray-600 rounded-full">Trainer</span>
                        @else
                            <button onclick="openWaiverModal('jiu', 'per-session', '400.00')" class="text-sm px-3 py-1 bg-blue-600 text-white rounded-full hover:bg-blue-700 transition">
                                Join Now
                            </button>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="text-sm px-3 py-1 bg-gray-300 text-gray-700 rounded-full hover:bg-gray-400 transition">
                            Login
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@endsection