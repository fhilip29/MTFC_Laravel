<header class="p-5 flex justify-between w-full relative z-10">
    {{-- Left side: Logo and Nav Links --}}
    <div class="flex">
        <a href="/">
            <img src="{{ asset('assets/logo.png') }}" alt="Logo" class="object-cover" height="60" width="100">
        </a>

        <nav class="ml-10">
            <ul class="flex space-x-6 text-zinc-50 font-semibold">
                <li><a href="{{ route('home') }}" class="relative group hover:scale-105 transition-transform">Home
                    <span class="absolute left-0 bottom-0 w-0 h-[2px] bg-red-500 group-hover:w-full transition-all duration-200 mt-5"></span>
                </a></li>
                <li><a href="{{ route('about') }}" class="relative group hover:scale-105 transition-transform">About
                    <span class="absolute left-0 bottom-0 w-0 h-[2px] bg-red-500 group-hover:w-full transition-all duration-200 mt-5"></span>
                </a></li>
                <li><a href="{{ route('classes') }}" class="relative group hover:scale-105 transition-transform">Classes
                    <span class="absolute left-0 bottom-0 w-0 h-[2px] bg-red-500 group-hover:w-full transition-all duration-200 mt-5"></span>
                </a></li>
                <li><a href="{{ route('trainer') }}" class="relative group hover:scale-105 transition-transform">Trainer
                    <span class="absolute left-0 bottom-0 w-0 h-[2px] bg-red-500 group-hover:w-full transition-all duration-200 mt-5"></span>
                </a></li>
                <li><a href="{{ route('pricing') }}" class="relative group hover:scale-105 transition-transform">Pricing
                    <span class="absolute left-0 bottom-0 w-0 h-[2px] bg-red-500 group-hover:w-full transition-all duration-200 mt-5"></span>
                </a></li>
            </ul>
        </nav>
    </div>

    {{-- Right side: Auth Buttons & User Dropdown --}}
    <nav>
        <ul class="flex items-center space-x-4 text-zinc-50">
            @guest
                <li>
                    <a href="{{ route('login') }}" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 flex items-center space-x-2">
                        <i class="fa-solid fa-lock"></i>
                        <span>Login</span>
                    </a>
                </li>
            @else
                {{-- Cart & Notification --}}
                <li>
                    <button @click="$dispatch('toggle-cart')" class="text-white hover:text-gray-300">
                        <i class="fa-solid fa-cart-shopping"></i>
                    </button>
                </li>
                <li>
                    <a href="{{ route('notifications') }}" class="text-white hover:text-gray-300">
                        <i class="fa-solid fa-bell"></i>
                    </a>
                </li>

                {{-- Profile Dropdown --}}
                <li x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="text-white hover:text-gray-300">
                        <i class="fa-solid fa-user"></i>
                    </button>

                    {{-- Dropdown Menu --}}
                    <div x-show="open" @click.outside="open = false"
                         class="absolute right-0 mt-2 w-48 bg-white rounded shadow-lg text-black text-xs z-50 p-2">
                        <div class="flex flex-col items-center pb-2 border-b">
                            <img src="{{ Auth::user()->profile_url ?? asset('assets/default-profile.png') }}"
                                 alt="Profile"
                                 class="rounded-full w-12 h-12 object-cover mb-2">
                            <span>Hello, <b>{{ Auth::user()->full_name ?? 'User' }}</b></span>
                        </div>

                        @if (Auth::user()->user_type === 'admin')
                            <a href="{{ route('admin.dashboard') }}"
                               class="flex items-center p-2 hover:bg-gray-100 mt-2 space-x-2">
                                <i class="fa-solid fa-cog text-gray-500"></i>
                                <span>Admin Panel</span>
                            </a>
                        @endif

                        <a href="{{ route('profile.settings') }}"
                           class="flex items-center p-2 hover:bg-gray-100 space-x-2">
                            <i class="fa-solid fa-user-cog text-gray-500"></i>
                            <span>Account Settings</span>
                        </a>

                        @if (Auth::user()->user_type !== 'admin')
                            <a href="{{ Auth::user()->user_type === 'trainer' ? route('trainer.profile') : route('profile') }}"
                               class="flex items-center p-2 hover:bg-gray-100 space-x-2">
                                <i class="fa-solid fa-user-shield text-gray-500"></i>
                                <span>My Profile</span>
                            </a>
                            <a href="{{ route('community') }}"
                               class="flex items-center p-2 hover:bg-gray-100 space-x-2">
                                <i class="fa-solid fa-dashcube text-gray-500"></i>
                                <span>Community Dashboard</span>
                            </a>
                            <a href="{{ route('orders') }}"
                               class="flex items-center p-2 hover:bg-gray-100 space-x-2">
                                <i class="fa-solid fa-cart-shopping text-gray-500"></i>
                                <span>My Orders</span>
                            </a>
                        @endif

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                    class="w-full text-left flex items-center p-2 hover:bg-gray-100 space-x-2 mt-2">
                                <i class="fa-solid fa-sign-out-alt text-gray-500"></i>
                                <span>Log out</span>
                            </button>
                        </form>
                    </div>
                </li>
            @endguest
        </ul>
    </nav>
</header>
