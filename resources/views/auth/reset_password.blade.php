<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-[#1D1B20] min-h-screen flex items-center justify-center">

    <div class="flex w-full h-screen">
        <!-- Left Section -->
        <div class="flex flex-1 items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-lg w-[320px] md:w-[400px] text-center p-6">
                <img src="{{ asset('assets/MTFC_LOGO.PNG') }}" class="mx-auto mb-4 h-12" />
                <h2 class="text-xl font-bold text-gray-800 mb-2">Reset Your Password</h2>
                <p class="text-sm text-gray-600 mb-4">Please enter a new secure password for your account.</p>

                @if(session('error'))
                    <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
                        {{ session('error') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.reset') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    <input type="hidden" name="email" value="{{ $email }}">

                    <input type="password" name="password" placeholder="New Password"
                        class="w-full p-3 border border-gray-300 rounded text-center mb-3"
                        required minlength="8">

                    <input type="password" name="password_confirmation" placeholder="Confirm Password"
                        class="w-full p-3 border border-gray-300 rounded text-center mb-4"
                        required minlength="8">

                    <button type="submit"
                        class="w-full bg-gray-800 text-white py-3 rounded hover:bg-gray-700 transition">
                        Reset Password
                    </button>
                </form>

                <p class="text-sm text-gray-600 mt-4">
                    <a href="{{ route('login') }}" class="text-blue-600 hover:underline">
                        Return to login
                    </a>
                </p>
            </div>
        </div>

        <!-- Right Section -->
        <div class="flex-1 hidden md:block relative overflow-hidden"
            style="clip-path: polygon(0 0, 100% 0, 100% 100%, 25% 100%)">
            <img src="{{ asset('assets/signup_background.png') }}"
                class="w-full h-full object-cover absolute inset-0 z-0" />
        </div>
    </div>
</body>
</html> 