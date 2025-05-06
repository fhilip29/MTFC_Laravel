<header x-data="{ mobileMenuOpen: false, adminProfileModal: false }" class="bg-white text-white shadow-md">
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
            <a href="{{ route('pricing.gym') }}" class="text-black font-bold relative transform hover:scale-110 transition-transform duration-300 after:absolute after:bottom-0 after:left-0 after:w-full after:h-0.5 after:bg-red-500 after:scale-x-0 hover:after:scale-x-100 after:transition-transform after:duration-300 hover:text-red-500">Pricing</a>
            <a href="{{ route('shop') }}" class="text-black font-bold relative transform hover:scale-110 transition-transform duration-300 after:absolute after:bottom-0 after:left-0 after:w-full after:h-0.5 after:bg-red-500 after:scale-x-0 hover:after:scale-x-100 after:transition-transform after:duration-300 hover:text-red-500">Shop</a>
            <a href="{{ route('contact') }}" class="text-black font-bold relative transform hover:scale-110 transition-transform duration-300 after:absolute after:bottom-0 after:left-0 after:w-full after:h-0.5 after:bg-red-500 after:scale-x-0 hover:after:scale-x-100 after:transition-transform after:duration-300 hover:text-red-500">Contact Us</a>
        </nav>

        <!-- Mobile Burger (only shown on small screens) -->
        <div class="md:hidden absolute right-20 top-5 z-50">
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
</div>


        <!-- Right: User/Profile Area -->
        <div class="absolute right-5 flex justify-end items-center space-x-4 w-1/3 min-w-[200px]">
            @auth
            <a href="{{ route('announcements') }}" class="right-1 relative text-gray-600 hover:text-black focus:outline-none">
                <i class="fas fa-bell text-xl"></i>
                <span class="absolute -top-1 -right-1 w-2 h-2 bg-red-500 rounded-full"></span>
            </a>

            <button id="cartButton" class="right- text-gray-600 hover:text-black focus:outline-none">
                <i class="fas fa-shopping-cart text-xl"></i>
            </button>
            @endauth

            @guest
                <a href="{{ route('login') }}" class="bg-red-500 hover:bg-red-600 transition px-4 py-2 rounded text-white text-sm font-semibold">
                    <i class="fa-solid fa-lock mr-1"></i> Login
                </a>
            @else
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center focus:outline-none">
                        <img src="{{ Auth::user()->profile_image ? asset('storage/'.Auth::user()->profile_image) : asset('assets/default-profile.jpg') }}"
                             class="w-8 h-8 rounded-full border-2 border-red-500 object-cover">
                    </button>
                    <div x-show="open" @click.outside="open = false"
                         class="absolute right-0 mt-2 w-48 bg-white rounded shadow-lg z-50 p-3">
                        <p class="text-sm font-semibold mb-2 text-gray-800">Hello, {{ Auth::user()->full_name ?? 'User' }}</p>
                        <hr>
                        <div class="mt-2 space-y-1 text-sm">
                            @if (Auth::user()->role === 'admin')
                                <a href="{{ route('admin.dashboard') }}" class="block text-gray-700 hover:bg-gray-100 hover:text-gray-900 px-2 py-1 rounded">Admin Panel</a>
                                <button @click="adminProfileModal = true; open = false" type="button" class="w-full text-left text-gray-700 hover:bg-gray-100 hover:text-gray-900 px-2 py-1 rounded">
                                    Profile
                                </button>
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
            @endguest
        </div>
    </div>

    <!-- Mobile Dropdown Navigation -->
<div x-show="mobileMenuOpen" x-cloak
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0 -translate-y-2"
     x-transition:enter-end="opacity-100 translate-y-0"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100 translate-y-0"
     x-transition:leave-end="opacity-0 -translate-y-2"
     class="md:hidden bg-white px-5 pt-4 pb-6 space-y-2 font-medium">
    <a href="{{ route('home') }}" class="block text-black hover:text-red-500 hover:underline transition duration-300">Home</a>
    <a href="{{ route('about') }}" class="block text-black hover:text-red-500 hover:underline transition duration-300">About</a>
    <a href="{{ route('trainers') }}" class="block text-black hover:text-red-500 hover:underline transition duration-300">Trainers</a>
    <a href="{{ route('pricing.gym') }}" class="block text-black hover:text-red-500 hover:underline transition duration-300">Pricing</a>
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
</script>
@endpush


                </div>
            </div>
        </div>
    </div>
</div>
