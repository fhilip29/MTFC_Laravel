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
    .card-animation:nth-child(3) { animation-delay: 0.7s; }
    .card-animation:nth-child(4) { animation-delay: 0.9s; }
    
    /* Promo badge */
    .promo-badge {
        position: absolute;
        top: -10px;
        right: -10px;
        background: linear-gradient(45deg, #f87171, #ef4444);
        color: white;
        font-weight: 600;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        transform: rotate(10deg);
        z-index: 10;
    }
    
    /* Featured plan highlighting */
    .featured-plan {
        border: 2px solid #3b82f6;
    }
</style>

<div class="page-transition">
    @include('components.waiver-modal')
    <section class="relative w-full min-h-screen text-gray-800">
        <!-- Background image -->
        @if(isset($activeSport) && $activeSport && $activeSport->background_image && $activeSport->background_image != '/assets/gym-bg.jpg')
            <div class="absolute inset-0 bg-cover bg-center bg-no-repeat blur-sm" style="background-image: url('{{ asset($activeSport->background_image) }}');"></div>
        @else
            <div class="absolute inset-0 bg-cover bg-center bg-no-repeat blur-sm" style="background-image: url('{{ asset('/assets/gym-bg.jpg') }}');"></div>
        @endif

        <!-- Overlay to darken -->
        <div class="absolute inset-0 bg-black opacity-50"></div>

        <!-- Main content -->
        <div class="relative z-10 max-w-7xl mx-auto px-3 sm:px-6 py-8 sm:py-16">
            <!-- Title -->
            <h1 class="text-2xl sm:text-3xl md:text-5xl font-bold text-white text-center mb-4 sm:mb-6">All-In-One Membership Plans</h1>
            <p class="text-base sm:text-lg text-white text-center mb-6 sm:mb-10">Choose the plan that fits your fitness journey at MTFC</p>

            <!-- Sport Tabs -->
            <div class="flex justify-center mb-10 w-full overflow-x-auto pb-2 px-2">
                <div class="flex space-x-2 md:space-x-4 min-w-max mx-auto">
                    @foreach($sports as $index => $sportTab)
                        <button type="button"
                                onclick="switchTab('{{ $sportTab->slug }}')"
                                class="px-4 py-2 rounded-md font-semibold whitespace-nowrap {{ $index === 0 ? 'bg-gray-300 text-black' : 'text-white bg-gray-700 hover:bg-gray-600' }}"
                                data-tab-id="{{ $sportTab->slug }}">
                            {{ $sportTab->name }}
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Sport content sections -->
            @foreach($sports as $index => $sportTab)
                <div id="sport-content-{{ $sportTab->id }}" class="sport-content {{ $activeSport && $sportTab->id === $activeSport->id ? 'block' : 'hidden' }}">
                    @if(count($sportTab->activePlans) > 0)
                        <div class="flex flex-wrap justify-center gap-4 md:gap-8">
                            @foreach($sportTab->activePlans as $plan)
                                <div class="w-full sm:w-80 bg-white rounded-2xl shadow-lg p-6 flex flex-col relative {{ $plan->is_featured ? 'featured-plan' : '' }} card-animation">
                                    @if($plan->isOnPromo() && $plan->original_price)
                                        <div class="promo-badge">
                                            {{ round((1 - $plan->price / $plan->original_price) * 100) }}% OFF
                                        </div>
                                    @endif
                                    
                                    @if($plan->is_featured)
                                        <div class="absolute top-0 left-0 right-0 bg-blue-600 text-white text-xs text-center font-semibold py-1 rounded-t-xl">
                                            RECOMMENDED
                                        </div>
                                    @endif
                                    
                                    <span class="inline-block text-xs font-semibold text-gray-500 mb-2">Membership</span>
                                    <h3 class="text-xl font-bold mb-3">{{ $plan->name }}</h3>
                                    
                                    <ul class="text-sm text-gray-700 space-y-2 mb-6">
                                        @foreach($plan->features as $feature)
                                            <li class="flex items-start gap-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                </svg>
                                                {{ $feature }}
                                            </li>
                                        @endforeach
                                    </ul>
                                    
                                    <div class="flex justify-between items-center mt-auto">
                                        <span class="text-lg font-semibold text-gray-800">
                                            @if($plan->isOnPromo() && $plan->original_price)
                                                <span class="line-through text-sm text-gray-500">₱{{ number_format($plan->original_price, 2) }}</span><br>
                                            @endif
                                            ₱{{ number_format($plan->price, 2) }}
                                            <span class="text-sm text-gray-500">
                                                / {{ $plan->plan === 'per-session' ? 'session' : ($plan->plan === 'daily' ? 'day' : 'month') }}
                                            </span>
                                        </span>
                                        
                                        @if(Auth::check())
                                            @if($userHasActive)
                                                @if($activeType == $sportTab->slug && $activePlan == $plan->plan)
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
                                                <button onclick="openWaiverModal('{{ $sportTab->slug }}', '{{ $plan->plan }}', '{{ $plan->price }}')" class="text-sm px-3 py-1 bg-blue-600 text-white rounded-full hover:bg-blue-700 transition">
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
                    @else
                        <div class="bg-white bg-opacity-10 rounded-lg p-8 text-center">
                            <p class="text-white">No pricing plans available for {{ $sportTab->name }} at this time.</p>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </section>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tab switching function
        window.switchTab = function(slug) {
            // Redirect to the sport's pricing page
            window.location.href = `/pricing/${slug}`;
        };
        
        // Initialize tab styling
        const sportTabs = document.querySelectorAll('[data-tab-id]');
        sportTabs.forEach(tab => {
            tab.addEventListener('click', () => {
                const slug = tab.dataset.tabId;
                switchTab(slug);
            });
        });
    });
</script>
@endsection 