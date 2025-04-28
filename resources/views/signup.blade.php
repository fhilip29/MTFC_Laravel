<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up | ActiveGym</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        .clip-path-custom {
            clip-path: polygon(0 0, 100% 0, 100% 100%, 25% 100%);
        }
    </style>
</head>
<body class="bg-[#1D1B20] text-white overflow-hidden">

<div class="flex h-screen">

    <!-- LEFT: Sign-up Form -->
    <div class="flex-1 flex justify-center items-center px-6">
        <div class="w-full max-w-md">

            <h3 class="text-3xl font-bold text-center mb-2">Sign Up now!</h3>
            <p class="text-center text-gray-300 mb-6">Unlock Your Potential: Join Us Today!</p>

            @if ($errors->any())
                <script>
                    document.addEventListener('DOMContentLoaded', () => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error',
                            html: `{!! implode('<br>', $errors->all()) !!}`
                        });
                    });
                </script>
            @endif

            @if (session('success'))
                <script>
                    document.addEventListener('DOMContentLoaded', () => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Signed Up!',
                            text: '{{ session('success') }}'
                        }).then(() => {
                            window.location.href = '/login';
                        });
                    });
                </script>
            @endif
            
            <!-- Google Sign Up Button -->
            <a href="{{ route('auth.google') }}" 
               class="flex items-center justify-center w-full py-3 mb-6 bg-white text-gray-700 rounded hover:bg-gray-200 transition">
                <img src="https://developers.google.com/identity/images/g-logo.png" alt="Google Logo" class="h-5 mr-2">
                <span class="font-medium">Continue with Google</span>
            </a>
            
            <!-- Divider -->
            <div class="relative mb-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-600"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-[#1D1B20] text-gray-400">OR</span>
                </div>
            </div>

            <form method="POST" action="{{ route('signup') }}">
                @csrf

                <input type="text" name="first_name" placeholder="First Name" required
                       class="p-3 mb-3 w-full border border-gray-600 bg-transparent rounded"/>

                <input type="text" name="last_name" placeholder="Last Name" required
                       class="p-3 mb-3 w-full border border-gray-600 bg-transparent rounded"/>

                <select name="gender" required
                        class="p-3 mb-3 w-full border border-gray-600 bg-transparent rounded">
                    <option value="" disabled selected>Gender</option>
                    <option value="male" class="text-black">Male</option>
                    <option value="female" class="text-black">Female</option>
                </select>

                <select name="fitness_goal" required
                        class="p-3 mb-3 w-full border border-gray-600 bg-transparent rounded">
                    <option value="" disabled selected>Fitness Goal</option>
                    <option value="lose-weight" class="text-black">Lose Weight</option>
                    <option value="build-muscle" class="text-black">Build Muscle</option>
                    <option value="maintain" class="text-black">Maintain</option>
                </select>

                <input type="email" name="email" placeholder="Email Address" required
                       class="p-3 mb-3 w-full border border-gray-600 bg-transparent rounded"/>

                <input type="password" name="password" placeholder="Password" required
                       class="p-3 mb-3 w-full border border-gray-600 bg-transparent rounded"/>

                <input type="password" name="password_confirmation" placeholder="Confirm Password" required
                       class="p-3 mb-3 w-full border border-gray-600 bg-transparent rounded"/>

                <button type="submit"
                        class="w-full py-3 bg-white text-black rounded hover:bg-gray-200 transition mt-4">
                    Sign Up
                </button>

                <p class="text-center text-sm mt-6">
                    Already have an account?
                    <a href="{{ route('login') }}" class="text-white font-semibold underline hover:text-purple-300">Log in</a>
                </p>
            </form>
        </div>
    </div>

    <!-- RIGHT: Clipped Image -->
    <div class="hidden md:block flex-1 relative clip-path-custom">
        <img src="{{ asset('assets/signup_background.png') }}"
             alt="Sign Up Image"
             class="absolute inset-0 w-full h-full object-cover z-[-1]" />
    </div>

</div>
</body>
</html>
