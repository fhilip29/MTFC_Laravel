<header class="bg-[#ABA9A9] text-white shadow-md">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
        <!-- Left: Logo -->
        <div class="flex items-center w-1/3 min-w-[180px]">
            <a href="/" class="flex items-center space-x-2">
                <img src="{{ asset('assets/MTFC_LOGO.PNG') }}" alt="Logo" class=" h-12 object-contain">
            </a>
        </div>

        <!-- Middle: Navigation -->
        <nav class="hidden md:flex justify-center flex-1 space-x-6 font-medium">
            <a href="{{ route('home') }}" class="text-black font-bold hover:text-red-500 transition">Home</a>
            <a href="{{ route('about') }}" class="text-black font-bold hover:text-red-500 transition">About</a>
            <a href="{{ route('classes') }}" class="text-black font-bold hover:text-red-500 transition">Classes</a>
            <a href="{{ route('trainer') }}" class="text-black font-bold hover:text-red-500 transition">Trainer</a>
            <a href="{{ route('pricing') }}" class="text-black font-bold hover:text-red-500 transition">Pricing</a>
        </nav>

        <!-- Right: User/Profile Area -->
        <div class="flex justify-end items-center space-x-4 w-1/3 min-w-[180px]">
            @guest
                <a href="{{ route('login') }}" class="bg-red-500 hover:bg-red-600 transition px-4 py-2 rounded text-white text-sm font-semibold">
                    <i class="fa-solid fa-lock mr-1"></i> Login
                </a>
            @else
                <a href="#" class="hover:text-red-400"><i class="fa-solid fa-cart-shopping text-lg"></i></a>
                <a href="{{ route('notifications') }}" class="hover:text-red-400"><i class="fa-solid fa-bell text-lg"></i></a>

                <!-- Profile Dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center focus:outline-none">
                        <img src="{{ Auth::user()->profile_url ?? asset('assets/default-profile.png') }}"
                             class="w-8 h-8 rounded-full border-2 border-red-500 object-cover">
                    </button>
                    <div x-show="open" @click.outside="open = false"
                         class="absolute right-0 mt-2 w-48 bg-white text-black rounded shadow-lg z-50 p-3">
                        <p class="text-sm font-semibold mb-2">Hello, {{ Auth::user()->full_name ?? 'User' }}</p>
                        <hr>
                        <div class="mt-2 space-y-1 text-sm">
                            @if (Auth::user()->user_type === 'admin')
                                <a href="{{ route('admin.dashboard') }}" class="block hover:bg-gray-100 px-2 py-1 rounded">Admin Panel</a>
                            @endif
                            <a href="{{ route('profile.settings') }}" class="block hover:bg-gray-100 px-2 py-1 rounded">Account Settings</a>
                            @if (Auth::user()->user_type !== 'admin')
                                <a href="{{ Auth::user()->user_type === 'trainer' ? route('trainer.profile') : route('profile') }}" class="block hover:bg-gray-100 px-2 py-1 rounded">My Profile</a>
                                <a href="{{ route('community') }}" class="block hover:bg-gray-100 px-2 py-1 rounded">Community</a>
                                <a href="{{ route('orders') }}" class="block hover:bg-gray-100 px-2 py-1 rounded">My Orders</a>
                            @endif
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="w-full text-left hover:bg-gray-100 px-2 py-1 rounded">Logout</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endguest
        </div>
    </div>
</header>
