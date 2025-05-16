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
<body class="bg-[#1D1B20] text-white">
<!-- Page Loader -->
@include('components.loader')

<div class="flex min-h-screen pt-10">

    <!-- LEFT: Sign-up Form -->
    <div class="flex-1 flex justify-center items-center px-6 py-8">
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
                    <option value="other" class="text-black">Other</option>
                </select>
                
                <div id="otherGenderField" style="display: none;">
                    <input type="text" name="other_gender" placeholder="Please specify your gender" 
                           class="p-3 mb-3 w-full border border-gray-600 bg-transparent rounded"/>
                </div>

                <select name="fitness_goal" 
                        class="p-3 mb-3 w-full border border-gray-600 bg-transparent rounded">
                    <option value="" selected>Fitness Goal (Optional)</option>
                    <option value="lose-weight" class="text-black">Lose Weight</option>
                    <option value="build-muscle" class="text-black">Build Muscle</option>
                    <option value="maintain" class="text-black">Maintain</option>
                    <option value="boxing" class="text-black">Boxing</option>
                    <option value="muay-thai" class="text-black">Muay Thai</option>
                    <option value="jiu-jitsu" class="text-black">Jiu-Jitsu</option>
                </select>

                <input type="email" name="email" placeholder="Email Address" required
                       class="p-3 mb-3 w-full border border-gray-600 bg-transparent rounded"/>
                
                <input type="tel" name="mobile_number" placeholder="Philippine Phone Number (e.g., +63 917 123 4567)" required
                       class="p-3 mb-3 w-full border border-gray-600 bg-transparent rounded"/>

                <input type="password" name="password" placeholder="Password" required
                       class="p-3 mb-3 w-full border border-gray-600 bg-transparent rounded"/>

                <input type="password" name="password_confirmation" placeholder="Confirm Password" required
                       class="p-3 mb-3 w-full border border-gray-600 bg-transparent rounded"/>

                <div class="flex items-start mb-4 mt-2">
                    <input type="checkbox" id="terms_agreement" name="is_agreed_to_terms" required
                           class="mt-1 mr-2"/>
                    <label for="terms_agreement" class="text-sm text-gray-300">
                        I agree to the <a href="{{ route('terms') }}" class="text-white underline hover:text-purple-300" target="_blank">Terms of Use</a> and 
                        <a href="{{ route('privacy') }}" class="text-white underline hover:text-purple-300" target="_blank">Privacy Policy</a>
                    </label>
                </div>

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

<script>
    document.querySelector('form[action="{{ route('signup') }}"]').addEventListener('submit', function(event) {
        const termsCheckbox = document.getElementById('terms_agreement');
        if (!termsCheckbox.checked) {
            event.preventDefault(); // Stop form submission
            Swal.fire({
                icon: 'warning',
                title: 'Agreement Required',
                text: 'Please read the terms and privacy before signing up'
            });
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        const genderSelect = document.querySelector('select[name="gender"]');
        const otherGenderField = document.getElementById('otherGenderField');
        
        genderSelect.addEventListener('change', function() {
            if (this.value === 'other') {
                otherGenderField.style.display = 'block';
            } else {
                otherGenderField.style.display = 'none';
            }
        });
    });
</script>

</body>
</html>
