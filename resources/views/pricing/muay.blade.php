@extends('layouts.app')

@section('content')
<style>
    /* Page transition fade-in animation */
    @keyframes pageTransition {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .page-transition {
        animation: pageTransition 0.8s ease forwards;
    }
    
    /* Card animation */
    @keyframes cardAppear {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .card-animation {
        opacity: 0;
        animation: cardAppear 0.6s ease forwards;
    }
    
    .card-animation:nth-child(1) { animation-delay: 0.3s; }
    .card-animation:nth-child(2) { animation-delay: 0.5s; }
</style>

<div class="page-transition">
    @include('components.waiver-modal')
    <section class="relative w-full min-h-screen text-gray-800">
        <!-- Background image -->
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat blur-sm" style="background-image: url('/assets/gym-bg.jpg');"></div>

        <!-- Overlay to darken -->
        <div class="absolute inset-0 bg-black opacity-50"></div>

        <!-- Main content -->
        <div class="relative z-10 max-w-7xl mx-auto px-6 py-16">
            <!-- Title -->
            <h1 class="text-3xl md:text-5xl font-bold text-white text-center mb-6">Muay Thai Membership Plans</h1>
            <p class="text-lg text-white text-center mb-10">Train in the art of eight limbs with experienced Muay Thai instructors</p>

            <!-- Tabs -->
            <div class="flex justify-center space-x-4 mb-10 overflow-x-auto">
                <a href="{{ route('pricing.gym') }}" class="px-5 py-2 rounded-md text-white font-semibold bg-gray-700 hover:bg-gray-600">
                    GYM
                </a>
                <a href="{{ route('pricing.boxing') }}" class="px-5 py-2 rounded-md text-white font-semibold bg-gray-700 hover:bg-gray-600">
                    Boxing
                </a>
                <a href="{{ route('pricing.muay') }}" class="px-5 py-2 rounded-md font-semibold bg-gray-300 text-black">
                    Muay Thai
                </a>
                <a href="{{ route('pricing.jiu') }}" class="px-5 py-2 rounded-md text-white font-semibold bg-gray-700 hover:bg-gray-600">
                    Jiu-Jitsu
                </a>
            </div>

            <!-- Pricing Cards -->
            <div class="flex flex-wrap justify-center gap-8">
                <!-- Monthly Membership -->
                <div class="w-80 bg-white rounded-2xl shadow-lg p-6 flex flex-col border-2 border-blue-600 card-animation">
                    <span class="inline-block text-xs font-semibold text-gray-500 mb-2">Membership</span>
                    <h2 class="text-xl font-bold mb-3">Monthly Membership</h2>
                    <ul class="text-sm text-gray-700 space-y-2 mb-6">
                    <li class="flex items-start gap-2">
                            <x-check-icon /> Free Use of Gym
                        </li>
                        <li class="flex items-start gap-2">
                            <x-check-icon /> Shower Given
                        </li>
                        <li class="flex items-start gap-2">
                            <x-check-icon /> Free Use of Boxing Equipment
                        </li>
                    </ul>
                    <div class="flex justify-between items-center mt-auto">
                        <span class="text-lg font-semibold text-gray-800">₱2,600 / month</span>
                        @if(Auth::check())
                            @if($userHasActive)
                                @if($activeType == 'muay' && $activePlan == 'monthly')
                                    <div class="flex flex-col items-end">
                                        <span class="text-sm px-3 py-1 bg-green-100 text-green-600 rounded-full">Current Plan</span>
                                    </div>
                                @else
                                    <span class="text-sm px-3 py-1 bg-gray-100 text-gray-600 rounded-full">Active</span>
                                @endif
                            @elseif($userRole === 'admin')
                                <span class="text-sm px-3 py-1 bg-gray-100 text-gray-600 rounded-full">Admin</span>
                            @elseif($userRole === 'trainer')
                                <span class="text-sm px-3 py-1 bg-gray-100 text-gray-600 rounded-full">Trainer</span>
                            @else
                                <button onclick="openWaiverModal('muay', 'monthly', '2600.00')" class="text-sm px-3 py-1 bg-blue-600 text-white rounded-full hover:bg-blue-700 transition">
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
                <div class="w-80 bg-white rounded-2xl shadow-lg p-6 flex flex-col card-animation">
                    <span class="inline-block text-xs font-semibold text-gray-500 mb-2">Membership</span>
                    <h2 class="text-xl font-bold mb-3">Per Session</h2>
                    <ul class="text-sm text-gray-700 space-y-2 mb-6">
                        <li class="flex items-start gap-2">
                            <x-check-icon /> One Muay Thai class
                        </li>
                        <li class="flex items-start gap-2">
                            <x-check-icon /> Sessions tracked in your account
                        </li>
                        <li class="flex items-start gap-2">
                            <x-check-icon /> Free use of gym for the day
                        </li>
                        <li class="flex items-start gap-2">
                            <x-check-icon /> Shower access
                        </li>
                        <li class="flex items-start gap-2">
                            <x-check-icon /> Muay Thai equipment provided
                        </li>
                    </ul>
                    <div class="flex justify-between items-center mt-auto">
                        <span class="text-lg font-semibold text-gray-800">₱350 / session</span>
                        @if(Auth::check())
                            @if($userHasActive)
                                @if($activeType == 'muay' && $activePlan == 'per-session')
                                    <div class="flex flex-col items-end">
                                        <span class="text-sm px-3 py-1 bg-green-100 text-green-600 rounded-full">Current Plan</span>
                                    </div>
                                @else
                                    <span class="text-sm px-3 py-1 bg-gray-100 text-gray-600 rounded-full">Active</span>
                                @endif
                            @elseif($userRole === 'admin')
                                <span class="text-sm px-3 py-1 bg-gray-100 text-gray-600 rounded-full">Admin</span>
                            @elseif($userRole === 'trainer')
                                <span class="text-sm px-3 py-1 bg-gray-100 text-gray-600 rounded-full">Trainer</span>
                            @else
                                <button onclick="openWaiverModal('muay', 'per-session', '350.00')" class="text-sm px-3 py-1 bg-blue-600 text-white rounded-full hover:bg-blue-700 transition">
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
</div>
@endsection
