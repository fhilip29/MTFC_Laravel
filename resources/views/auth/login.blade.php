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

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        /* Page Loader Styles */
        #page-loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.9);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
    </style>
</head>
<body class="bg-gray-100">
<!-- Page Loader -->
@include('components.loader')

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

                <div class="relative mb-3">
                    <input 
                        type="password" 
                        name="password"
                        id="password"
                        placeholder="Password"
                        class="p-3 border border-gray-300 rounded w-full focus:outline-none focus:ring-2 focus:ring-blue-400 pr-10"
                        required
                    >
                    <button type="button" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700" 
                            onclick="togglePasswordVisibility()">
                        <i id="passwordEye" class="fas fa-eye"></i>
                    </button>
                </div>

                <div class="text-right text-sm mb-4">
                    <a href="{{ route('forgot_password') }}" class="text-stone-900 hover:underline">Forgot password?</a>
                </div>

                <button 
                    type="submit"
                    class="w-full py-3 mb-4 bg-slate-50 text-stone-900 rounded hover:bg-slate-100 transition"
                >
                    Log In
                </button>
            </form>
            
            <!-- Google Sign In Button -->
            <div class="relative my-4">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-400"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-zinc-200 text-stone-700">OR</span>
                </div>
            </div>
            
            <a href="{{ route('auth.google') }}" 
               class="flex items-center justify-center w-full py-3 mb-4 bg-white text-gray-700 rounded border border-gray-300 shadow hover:shadow-md transition">
                <img src="https://developers.google.com/identity/images/g-logo.png" alt="Google Logo" class="h-5 mr-2">
                <span class="font-medium">Continue with Google</span>
            </a>

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
<script>
    // Password toggle visibility
    function togglePasswordVisibility() {
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('passwordEye');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        }
    }
</script>
</html>
