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

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

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

        /* Fix for mobile scrolling */
        body, html {
            height: auto;
            min-height: 100%;
            overflow-x: hidden;
        }

        @media (max-width: 768px) {
            .signup-container {
                height: auto;
                min-height: 100%;
                padding-bottom: 2rem;
                overflow-y: auto;
            }
        }
    </style>
</head>
<body class="bg-[#1D1B20] text-white">
<!-- Page Loader -->
@include('components.loader')

<div class="flex min-h-screen signup-container overflow-auto">

    <!-- LEFT: Sign-up Form -->
    <div class="flex-1 flex justify-center items-start px-6 py-8">
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
                
                <div id="otherGenderField" style="display: none;">
                    <input type="text" name="other_gender" placeholder="Please specify your gender" 
                           class="p-3 mb-3 w-full border border-gray-600 bg-transparent rounded"/>
                </div>

                <select name="fitness_goal" 
                        class="p-3 mb-3 w-full border border-gray-600 bg-transparent rounded">
                    <option value="" selected>Fitness Goal (Optional)</option>
                    <option value="weight-loss" class="text-black">Weight Loss</option>
                    <option value="muscle-gain" class="text-black">Build Muscle</option>
                    <option value="endurance" class="text-black">Improve Endurance</option>
                    <option value="flexibility" class="text-black">Increase Flexibility</option>
                    <option value="general-fitness" class="text-black">General Fitness</option>
                </select>

                <input type="email" name="email" placeholder="Email Address" required
                       class="p-3 mb-3 w-full border border-gray-600 bg-transparent rounded"/>
                
                <div class="mb-3">
                    <input type="tel" id="mobileNumberInput" name="mobile_number" placeholder="+63 917 123 4567" required
                           class="p-3 w-full border border-gray-600 bg-transparent rounded" 
                           onfocus="if(this.value === '+63 ') { this.setSelectionRange(4, 4); }" 
                           onkeydown="if(event.key === 'Backspace' && this.value.length <= 4) { event.preventDefault(); }" 
                           onkeyup="if(!this.value.startsWith('+63 ')) { this.value = '+63 ' + this.value.substring(4); }" />
                </div>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        // Initialize mobile number input with +63 prefix
                        const mobileInput = document.getElementById('mobileNumberInput');
                        if (mobileInput && !mobileInput.value) {
                            mobileInput.value = '+63 ';
                        }
                    });
                </script>

                <div class="relative mb-3">
                    <input type="password" name="password" id="password" placeholder="Password" required
                           class="p-3 w-full border border-gray-600 bg-transparent rounded pr-10"/>
                    <button type="button" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-white" 
                            onclick="togglePasswordVisibility('password', 'passwordEye')">
                        <i id="passwordEye" class="fas fa-eye"></i>
                    </button>
                </div>

                <div class="relative mb-3">
                    <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Confirm Password" required
                           class="p-3 w-full border border-gray-600 bg-transparent rounded pr-10"/>
                    <button type="button" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-white"
                            onclick="togglePasswordVisibility('password_confirmation', 'confirmEye')">
                        <i id="confirmEye" class="fas fa-eye"></i>
                    </button>
                </div>
                
                <!-- Password Requirements -->
                <div class="mb-4 text-xs text-gray-400">
                    <p class="mb-1">Password must meet the following requirements:</p>
                    <ul class="pl-5 list-disc space-y-1">
                        <li id="length" class="text-gray-500">At least 8 characters long</li>
                        <li id="uppercase" class="text-gray-500">At least one uppercase letter</li>
                        <li id="lowercase" class="text-gray-500">At least one lowercase letter</li>
                        <li id="number" class="text-gray-500">At least one number</li>
                        <li id="special" class="text-gray-500">At least one special character</li>
                    </ul>
                </div>

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

                <p class="text-center text-sm mt-6 mb-8">
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
        
        // Password toggle visibility
        window.togglePasswordVisibility = function(inputId, eyeId) {
            const passwordInput = document.getElementById(inputId);
            const eyeIcon = document.getElementById(eyeId);
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        };
        
        // Password requirements validator
        const passwordInput = document.getElementById('password');
        const requirements = {
            length: document.getElementById('length'),
            uppercase: document.getElementById('uppercase'),
            lowercase: document.getElementById('lowercase'),
            number: document.getElementById('number'),
            special: document.getElementById('special')
        };
        
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            
            // Check length
            if (password.length >= 8) {
                requirements.length.classList.add('text-green-500');
                requirements.length.classList.remove('text-gray-500');
            } else {
                requirements.length.classList.remove('text-green-500');
                requirements.length.classList.add('text-gray-500');
            }
            
            // Check uppercase
            if (/[A-Z]/.test(password)) {
                requirements.uppercase.classList.add('text-green-500');
                requirements.uppercase.classList.remove('text-gray-500');
            } else {
                requirements.uppercase.classList.remove('text-green-500');
                requirements.uppercase.classList.add('text-gray-500');
            }
            
            // Check lowercase
            if (/[a-z]/.test(password)) {
                requirements.lowercase.classList.add('text-green-500');
                requirements.lowercase.classList.remove('text-gray-500');
            } else {
                requirements.lowercase.classList.remove('text-green-500');
                requirements.lowercase.classList.add('text-gray-500');
            }
            
            // Check number
            if (/[0-9]/.test(password)) {
                requirements.number.classList.add('text-green-500');
                requirements.number.classList.remove('text-gray-500');
            } else {
                requirements.number.classList.remove('text-green-500');
                requirements.number.classList.add('text-gray-500');
            }
            
            // Check special character
            if (/[^A-Za-z0-9]/.test(password)) {
                requirements.special.classList.add('text-green-500');
                requirements.special.classList.remove('text-gray-500');
            } else {
                requirements.special.classList.remove('text-green-500');
                requirements.special.classList.add('text-gray-500');
            }
        });
    });
</script>

</body>
</html>
