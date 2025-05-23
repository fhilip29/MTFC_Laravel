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
    
    /* Individual animation delays */
    .card-animation:nth-child(1) { animation-delay: 0.3s; }
    .card-animation:nth-child(2) { animation-delay: 0.4s; }
    .card-animation:nth-child(3) { animation-delay: 0.5s; }
    .card-animation:nth-child(4) { animation-delay: 0.6s; }
    .card-animation:nth-child(5) { animation-delay: 0.7s; }
    .card-animation:nth-child(6) { animation-delay: 0.8s; }
    .card-animation:nth-child(7) { animation-delay: 0.9s; }
    .card-animation:nth-child(8) { animation-delay: 1.0s; }
    .card-animation:nth-child(9) { animation-delay: 1.1s; }
    .card-animation:nth-child(10) { animation-delay: 1.2s; }
</style>

<div class="page-transition">
    @include('components.waiver-modal')
    <section class="relative w-full min-h-screen text-gray-800">
        <!-- Background image -->
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat blur-sm" style="background-image: url('{{ $sport->background_image ? asset($sport->background_image) : '/assets/gym-bg.jpg' }}');"></div>

        <!-- Overlay to darken -->
        <div class="absolute inset-0 bg-black opacity-50"></div>

        <!-- Main content -->
        <div class="relative z-10 max-w-7xl mx-auto px-3 sm:px-6 py-8 sm:py-16">
            <!-- Title -->
            <h1 class="text-2xl sm:text-3xl md:text-5xl font-bold text-white text-center mb-4 sm:mb-6">{{ $sport->name }} Membership Plans</h1>
            <p class="text-base sm:text-lg text-white text-center mb-6 sm:mb-10">{{ $sport->description }}</p>

            <!-- Tabs -->
            <div class="flex justify-center mb-10 w-full overflow-x-auto pb-2 px-2 snap-x scrollbar-hide">
                <div class="flex space-x-2 md:space-x-4 min-w-max mx-auto">
                    @foreach($sports as $sportTab)
                        <a href="{{ route('pricing.show', $sportTab->slug) }}" 
                           class="px-4 py-2 rounded-md font-semibold {{ $sportTab->slug === $sport->slug ? 'bg-gray-300 text-black' : 'text-white bg-gray-700 hover:bg-gray-600' }} whitespace-nowrap snap-start">
                            {{ $sportTab->name }}
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Pricing Cards -->
            <div class="flex flex-wrap justify-center gap-4 md:gap-8">
                @foreach($plans as $index => $plan)
                    <div class="w-full sm:w-80 bg-white rounded-2xl shadow-lg p-6 flex flex-col {{ $plan->is_featured ? 'border-2 border-blue-600' : '' }} card-animation">
                        <span class="inline-block text-xs font-semibold text-gray-500 mb-2">Membership</span>
                        <h2 class="text-xl font-bold mb-3">{{ $plan->name }}</h2>
                        
                        @if($plan->isOnPromo() && $plan->original_price)
                            <div class="mb-3">
                                <span class="line-through text-gray-500">₱{{ number_format($plan->original_price, 2) }}</span>
                                <span class="ml-2 bg-red-100 text-red-800 text-xs font-semibold px-2.5 py-0.5 rounded">PROMO</span>
                            </div>
                        @endif
                        
                        <ul class="text-sm text-gray-700 space-y-2 mb-6">
                            @foreach($plan->features as $feature)
                                <li class="flex items-start gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    {{ $feature }}
                                </li>
                            @endforeach
                        </ul>
                        
                        <div class="flex justify-between items-center mt-auto">
                            <span class="text-lg font-semibold text-gray-800">₱{{ number_format($plan->price, 2) }} / {{ $plan->plan === 'per-session' ? 'session' : ($plan->plan === 'daily' ? 'day' : 'month') }}</span>
                            
                            @if(Auth::check())
                                @if($userHasActive)
                                    @if($activeType == $sport->slug && $activePlan == $plan->plan)
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
                                    <button onclick="openWaiverModal('{{ $sport->slug }}', '{{ $plan->plan }}', '{{ $plan->price }}')" class="text-sm px-3 py-1 bg-blue-600 text-white rounded-full hover:bg-blue-700 transition">
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
                @endforeach
            </div>
        </div>
    </section>
</div>
@endsection 