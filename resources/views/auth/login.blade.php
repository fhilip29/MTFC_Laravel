<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-100">

<div 
    class="relative w-screen h-screen bg-cover bg-center"
    style="background-image: url('/assets/login_background.png')"
>
    <div class="h-full w-full flex">
        
        <!-- Left side (login box) -->
        <div class="w-full md:w-1/3 px-10 pt-20 bg-zinc-200 bg-opacity-70 flex flex-col justify-start">

            <div class="flex justify-center mb-6">
                <img src="{{ asset('assets/MTFC_LOGO.png') }}" alt="ActiveGym Logo" class="h-16">
            </div>
            
            <h3 class="text-3xl font-bold text-center text-stone-800 mb-6">Log in</h3>

            @if (session('error'))
                <script>
                    document.addEventListener('DOMContentLoaded', () => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Login Failed',
                            text: '{{ session('error') }}'
                        });
                    });
                </script>
            @endif

            @if (session('success'))
                <script>
                    document.addEventListener('DOMContentLoaded', () => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Welcome back!',
                            text: '{{ session('success') }}'
                        });
                    });
                </script>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <input 
                    type="email" 
                    name="email"
                    placeholder="Email"
                    class="p-3 mb-3 border border-gray-300 rounded w-full focus:outline-none focus:ring-2 focus:ring-blue-400"
                    required
                >

                <input 
                    type="password" 
                    name="password"
                    placeholder="Password"
                    class="p-3 mb-3 border border-gray-300 rounded w-full focus:outline-none focus:ring-2 focus:ring-blue-400"
                    required
                >

                <div class="text-right text-sm mb-4">
                    <a href="#" class="text-stone-900 hover:underline">Forgot password?</a>
                </div>

                <button 
                    type="submit"
                    class="w-full py-3 mb-4 bg-slate-50 text-stone-900 rounded hover:bg-slate-100 transition"
                >
                    Log In
                </button>
            </form>

            <p class="text-center text-sm text-stone-900">
                New to ActiveGym?
                <a href="{{ route('signup') }}" class="font-semibold text-stone-900 hover:underline">Sign up now</a>
            </p>
        </div>

        <!-- Right side -->
        <div class="hidden md:block w-2/3 bg-opacity-70">
            <!-- Add any image/design -->
        </div>
    </div>
</div>
</body>
</html>
