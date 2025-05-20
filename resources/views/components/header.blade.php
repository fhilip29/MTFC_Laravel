<header x-data="{ mobileMenuOpen: false, adminProfileModal: false }" class="bg-white text-white shadow-[0_4px_6px_-1px_rgba(0,0,0,0.1),0_2px_4px_-1px_rgba(0,0,0,0.06)] sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
        <!-- Left: Logo -->
        <div class="absolute left-5 flex w-1/3 min-w-[200px]">
            <a href="/" class="flex items-center space-x-2">
                <img src="{{ asset('assets/MTFC_LOGO.PNG') }}" alt="Logo" class="h-12 object-contain">
            </a>
        </div>

        <!-- Middle: Navigation -->
        <nav class="hidden md:flex justify-center flex-1 space-x-6 font-medium z-10 relative">
            <a href="{{ route('home') }}" class="text-black font-bold relative transform hover:scale-110 transition-transform duration-300 after:absolute after:bottom-0 after:left-0 after:w-full after:h-0.5 after:bg-red-500 after:scale-x-0 hover:after:scale-x-100 after:transition-transform after:duration-300 hover:text-red-500">Home</a>
            <a href="{{ route('about') }}" class="text-black font-bold relative transform hover:scale-110 transition-transform duration-300 after:absolute after:bottom-0 after:left-0 after:w-full after:h-0.5 after:bg-red-500 after:scale-x-0 hover:after:scale-x-100 after:transition-transform after:duration-300 hover:text-red-500">About</a>
            <a href="{{ route('trainers') }}" class="text-black font-bold relative transform hover:scale-110 transition-transform duration-300 after:absolute after:bottom-0 after:left-0 after:w-full after:h-0.5 after:bg-red-500 after:scale-x-0 hover:after:scale-x-100 after:transition-transform after:duration-300 hover:text-red-500">Trainers</a>
            <a href="{{ route('pricing') }}" class="text-black font-bold relative transform hover:scale-110 transition-transform duration-300 after:absolute after:bottom-0 after:left-0 after:w-full after:h-0.5 after:bg-red-500 after:scale-x-0 hover:after:scale-x-100 after:transition-transform after:duration-300 hover:text-red-500">Pricing</a>
            <a href="{{ route('shop') }}" class="text-black font-bold relative transform hover:scale-110 transition-transform duration-300 after:absolute after:bottom-0 after:left-0 after:w-full after:h-0.5 after:bg-red-500 after:scale-x-0 hover:after:scale-x-100 after:transition-transform after:duration-300 hover:text-red-500">Shop</a>
            <a href="{{ route('contact') }}" class="text-black font-bold relative transform hover:scale-110 transition-transform duration-300 after:absolute after:bottom-0 after:left-0 after:w-full after:h-0.5 after:bg-red-500 after:scale-x-0 hover:after:scale-x-100 after:transition-transform after:duration-300 hover:text-red-500">Contact Us</a>
        </nav>

        <!-- Burger/Profile (absolute, top right) -->
        <div class="md:hidden absolute right-5 top-1/2 -translate-y-1/2 z-50 flex items-center space-x-4">
            <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-black focus:outline-none p-2 rounded-md hover:bg-gray-100">
                <svg x-show="!mobileMenuOpen" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                <svg x-show="mobileMenuOpen" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            @auth
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="flex items-center focus:outline-none">
                    @if(Auth::user()->role === 'trainer')
                        @php
                            $trainer = App\Models\Trainer::where('user_id', Auth::id())->first();
                            $profileImage = $trainer && $trainer->profile_image_url ? $trainer->profile_image_url : asset('assets/default_profile.png');
                        @endphp
                        <img src="{{ $profileImage }}"
                             class="w-8 h-8 rounded-full object-cover border-2 border-gray-300 hover:border-red-500 transition-colors">
                    @elseif(Auth::user()->role === 'admin')
                        <img src="{{ Auth::user()->profile_image ? asset(Auth::user()->profile_image) : asset('assets/default_profile.png') }}"
                             class="w-8 h-8 rounded-full object-cover border-2 border-gray-300 hover:border-red-500 transition-colors">
                    @else
                        <img src="{{ Auth::user()->profile_image ? asset(Auth::user()->profile_image) : asset('assets/default_profile.png') }}"
                             class="w-8 h-8 rounded-full object-cover border-2 border-gray-300 hover:border-red-500 transition-colors">
                    @endif
                </button>
                <div x-show="open" @click.outside="open = false"
                     class="absolute right-0 mt-2 w-48 bg-white rounded shadow-lg z-50 p-3">
                    <p class="text-sm font-semibold mb-2 text-gray-800">Hello, {{ Auth::user()->full_name ?? 'User' }}</p>
                    <hr>
                    <div class="mt-2 space-y-1 text-sm">
                        @if (Auth::user()->role === 'admin')
                            <a href="{{ route('admin.dashboard') }}" class="block text-gray-700 hover:bg-gray-100 hover:text-gray-900 px-2 py-1 rounded">Admin Panel</a>
                            <a href="{{ route('admin.profile') }}" class="block text-gray-700 hover:bg-gray-100 hover:text-gray-900 px-2 py-1 rounded">Profile</a>
                            <a href="{{ route('community') }}" class="block text-gray-700 hover:bg-gray-100 hover:text-gray-900 px-2 py-1 rounded">Community</a>
                        @elseif (Auth::user()->role === 'trainer')
                            <a href="{{ route('trainer.profile') }}" class="block text-gray-700 hover:bg-gray-100 hover:text-gray-900 px-2 py-1 rounded">My Profile</a>
                            <a href="{{ route('community') }}" class="block text-gray-700 hover:bg-gray-100 hover:text-gray-900 px-2 py-1 rounded">Community</a>
                            <a href="{{ route('orders') }}" class="block text-gray-700 hover:bg-gray-100 hover:text-gray-900 px-2 py-1 rounded">My Orders</a>
                        @else
                            <a href="{{ route('account.settings') }}" class="block text-gray-700 hover:bg-gray-100 hover:text-gray-900 px-2 py-1 rounded">Account Settings</a>
                            <a href="{{ route('profile') }}" class="block text-gray-700 hover:bg-gray-100 hover:text-gray-900 px-2 py-1 rounded">My Profile</a>
                            <a href="{{ route('community') }}" class="block text-gray-700 hover:bg-gray-100 hover:text-gray-900 px-2 py-1 rounded">Community</a>
                            <a href="{{ route('orders') }}" class="block text-gray-700 hover:bg-gray-100 hover:text-gray-900 px-2 py-1 rounded">My Orders</a>
                            <a href="{{ route('subscription.history') }}" class="block text-gray-700 hover:bg-gray-100 hover:text-gray-900 px-2 py-1 rounded">My Subscriptions</a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="w-full text-left text-gray-700 hover:bg-gray-100 hover:text-gray-900 px-2 py-1 rounded">Logout</button>
                        </form>
                    </div>
                </div>
            </div>
            @endauth
            
            @guest
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="flex items-center focus:outline-none">
                    <img src="{{ asset('assets/default_profile.png') }}"
                         class="w-8 h-8 rounded-full object-cover border-2 border-gray-300 hover:border-red-500 transition-colors">
                </button>
                <div x-show="open" @click.outside="open = false"
                     class="absolute right-0 mt-2 w-48 bg-white rounded shadow-lg z-50 p-3">
                    <p class="text-sm font-semibold mb-2 text-gray-800">Hello, Guest</p>
                    <hr>
                    <div class="mt-2 space-y-1 text-sm">
                        <a href="{{ route('login') }}" class="block text-gray-700 hover:bg-gray-100 hover:text-gray-900 px-2 py-1 rounded">
                            <i class="fas fa-sign-in-alt mr-2"></i>Login
                        </a>
                        <a href="{{ route('signup.form') }}" class="block text-gray-700 hover:bg-gray-100 hover:text-gray-900 px-2 py-1 rounded">
                            <i class="fas fa-user-plus mr-2"></i>Sign Up
                        </a>
                    </div>
                </div>
            </div>
            @endguest
        </div>

        <!-- Right: User/Profile Area -->
        <div class="absolute right-5 flex justify-end items-center space-x-4 w-1/3 min-w-[200px]">
            @auth
            <div class="hidden md:flex items-center space-x-4">
                <a href="{{ route('announcements') }}" class="relative text-gray-600 hover:text-black focus:outline-none">
                    <i class="fas fa-bullhorn text-xl"></i>
                </a>

                <button id="cartButton" class="text-gray-600 hover:text-black focus:outline-none">
                    <i class="fas fa-shopping-cart text-xl"></i>
                </button>

                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center focus:outline-none">
                        @if(Auth::user()->role === 'trainer')
                            @php
                                $trainer = App\Models\Trainer::where('user_id', Auth::id())->first();
                                $profileImage = $trainer && $trainer->profile_image_url ? $trainer->profile_image_url : asset('assets/default_profile.png');
                            @endphp
                            <img src="{{ $profileImage }}"
                                 class="w-8 h-8 rounded-full object-cover border-2 border-gray-300 hover:border-red-500 transition-colors">
                        @elseif(Auth::user()->role === 'admin')
                            <img src="{{ Auth::user()->profile_image ? asset(Auth::user()->profile_image) : asset('assets/default_profile.png') }}"
                                 class="w-8 h-8 rounded-full object-cover border-2 border-gray-300 hover:border-red-500 transition-colors">
                        @else
                            <img src="{{ Auth::user()->profile_image ? asset(Auth::user()->profile_image) : asset('assets/default_profile.png') }}"
                                 class="w-8 h-8 rounded-full object-cover border-2 border-gray-300 hover:border-red-500 transition-colors">
                        @endif
                    </button>
                    <div x-show="open" @click.outside="open = false"
                         class="absolute right-0 mt-2 w-48 bg-white rounded shadow-lg z-50 p-3">
                        <p class="text-sm font-semibold mb-2 text-gray-800">Hello, {{ Auth::user()->full_name ?? 'User' }}</p>
                        <hr>
                        <div class="mt-2 space-y-1 text-sm">
                            @if (Auth::user()->role === 'admin')
                                <a href="{{ route('admin.dashboard') }}" class="block text-gray-700 hover:bg-gray-100 hover:text-gray-900 px-2 py-1 rounded">Admin Panel</a>
                                <a href="{{ route('admin.profile') }}" class="block text-gray-700 hover:bg-gray-100 hover:text-gray-900 px-2 py-1 rounded">Profile</a>
                                <a href="{{ route('community') }}" class="block text-gray-700 hover:bg-gray-100 hover:text-gray-900 px-2 py-1 rounded">Community</a>
                            @elseif (Auth::user()->role === 'trainer')
                                <a href="{{ route('trainer.profile') }}" class="block text-gray-700 hover:bg-gray-100 hover:text-gray-900 px-2 py-1 rounded">My Profile</a>
                                <a href="{{ route('community') }}" class="block text-gray-700 hover:bg-gray-100 hover:text-gray-900 px-2 py-1 rounded">Community</a>
                                <a href="{{ route('orders') }}" class="block text-gray-700 hover:bg-gray-100 hover:text-gray-900 px-2 py-1 rounded">My Orders</a>
                            @else
                                <a href="{{ route('account.settings') }}" class="block text-gray-700 hover:bg-gray-100 hover:text-gray-900 px-2 py-1 rounded">Account Settings</a>
                                <a href="{{ route('profile') }}" class="block text-gray-700 hover:bg-gray-100 hover:text-gray-900 px-2 py-1 rounded">My Profile</a>
                                <a href="{{ route('community') }}" class="block text-gray-700 hover:bg-gray-100 hover:text-gray-900 px-2 py-1 rounded">Community</a>
                                <a href="{{ route('orders') }}" class="block text-gray-700 hover:bg-gray-100 hover:text-gray-900 px-2 py-1 rounded">My Orders</a>
                                <a href="{{ route('subscription.history') }}" class="block text-gray-700 hover:bg-gray-100 hover:text-gray-900 px-2 py-1 rounded">My Subscriptions</a>
                            @endif
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="w-full text-left text-gray-700 hover:bg-gray-100 hover:text-gray-900 px-2 py-1 rounded">Logout</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endauth

            @guest
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center focus:outline-none">
                        <img src="{{ asset('assets/default_profile.png') }}"
                             class="w-8 h-8 rounded-full object-cover border-2 border-gray-300 hover:border-red-500 transition-colors">
                    </button>
                    <div x-show="open" @click.outside="open = false"
                         class="absolute right-0 mt-2 w-48 bg-white rounded shadow-lg z-50 p-3">
                        <p class="text-sm font-semibold mb-2 text-gray-800">Hello, Guest</p>
                        <hr>
                        <div class="mt-2 space-y-1 text-sm">
                            <a href="{{ route('login') }}" class="block text-gray-700 hover:bg-gray-100 hover:text-gray-900 px-2 py-1 rounded">
                                <i class="fas fa-sign-in-alt mr-2"></i>Login
                            </a>
                            <a href="{{ route('signup.form') }}" class="block text-gray-700 hover:bg-gray-100 hover:text-gray-900 px-2 py-1 rounded">
                                <i class="fas fa-user-plus mr-2"></i>Sign Up
                            </a>
                        </div>
                    </div>
                </div>
            @endguest
        </div>
    </div>

    <!-- Mobile Dropdown Navigation (should NOT be inside the above div) -->
    <div x-show="mobileMenuOpen" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="md:hidden absolute top-full left-0 right-0 bg-white px-5 pt-4 pb-6 space-y-2 font-medium shadow-lg z-40">
        @auth
        <div class="flex justify-end items-center space-x-4 mb-4">
            <a href="{{ route('announcements') }}" class="text-gray-600 hover:text-black focus:outline-none">
                <i class="fas fa-bullhorn text-xl"></i>
            </a>

            <button id="mobileCartButton" class="text-gray-600 hover:text-black focus:outline-none relative z-50 p-2">
                <i class="fas fa-shopping-cart text-xl"></i>
            </button>
        </div>
        @endauth
        <a href="{{ route('home') }}" class="block text-black hover:text-red-500 hover:underline transition duration-300">Home</a>
        <a href="{{ route('about') }}" class="block text-black hover:text-red-500 hover:underline transition duration-300">About</a>
        <a href="{{ route('trainers') }}" class="block text-black hover:text-red-500 hover:underline transition duration-300">Trainers</a>
        <a href="{{ route('pricing') }}" class="block text-black hover:text-red-500 hover:underline transition duration-300">Pricing</a>
        <a href="{{ route('shop') }}" class="block text-black hover:text-red-500 hover:underline transition duration-300">Shop</a>
        <a href="{{ route('contact') }}" class="block text-black hover:text-red-500 hover:underline transition duration-300">Contact Us</a>
    </div>

</header>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('headerData', () => ({
            mobileMenuOpen: false,
            adminProfileModal: false,
            toggleModal() {
                this.adminProfileModal = !this.adminProfileModal;
            }
        }))
    });
    
    // Add event listener for mobile cart button
    document.addEventListener('DOMContentLoaded', function() {
        // Mobile cart button handling
        const mobileCartButton = document.getElementById('mobileCartButton');
        
        if (mobileCartButton) {
            console.log('Mobile cart button found and initialized');
            
            mobileCartButton.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('Mobile cart button clicked');
                
                // Get cart drawer element
                const cartDrawer = document.getElementById('cartDrawer');
                
                if (cartDrawer) {
                    console.log('Cart drawer found, opening...');
                    // Remove translation class to make drawer visible
                    cartDrawer.classList.remove('translate-x-full');
                    
                    // Render the cart contents if function is available
                    if (typeof window.renderCart === 'function') {
                        console.log('Calling renderCart function');
                        window.renderCart();
                    } else {
                        console.warn('renderCart function not found');
                    }
                    
                    // Ensure drawer has proper z-index to appear above everything
                    cartDrawer.style.zIndex = '9999'; 
                } else {
                    console.error('Cart drawer element not found');
                }
                
                // Close mobile menu if open
                try {
                    const mobileMenuToggle = document.querySelector('[x-data]');
                    
                    if (mobileMenuToggle && typeof mobileMenuToggle.__x !== 'undefined') {
                        mobileMenuToggle.__x.updateElements(mobileMenuToggle, () => {
                            mobileMenuToggle.__x.$data.mobileMenuOpen = false;
                        });
                    }
                } catch (err) {
                    console.error('Error closing mobile menu:', err);
                }
            });
        } else {
            console.warn('Mobile cart button not found in DOM');
        }
    });
</script>
@endpush
