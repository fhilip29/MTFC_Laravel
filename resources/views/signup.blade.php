<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up | ActiveGym</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

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

                <div class="mb-3">
                    <input type="text" name="first_name" placeholder="First Name" required
                           class="p-3 w-full border border-gray-600 bg-transparent rounded"
                           value="{{ old('first_name') }}" 
                           onkeypress="return /^[a-zA-Z\s]*$/.test(event.key)" 
                           oninput="this.value = this.value.replace(/[0-9]/g, '')"/>
                    <p class="text-xs text-gray-400 mt-1">Required (letters only, no numbers)</p>
                </div>

                <div class="mb-3">
                    <input type="text" name="last_name" placeholder="Last Name" required
                           class="p-3 w-full border border-gray-600 bg-transparent rounded"
                           value="{{ old('last_name') }}" 
                           onkeypress="return /^[a-zA-Z\s]*$/.test(event.key)" 
                           oninput="this.value = this.value.replace(/[0-9]/g, '')"/>
                    <p class="text-xs text-gray-400 mt-1">Required (letters only, no numbers)</p>
                </div>

                <div class="mb-3">
                    <select name="gender" required
                            class="p-3 w-full border border-gray-600 bg-transparent rounded">
                        <option value="" disabled {{ old('gender') ? '' : 'selected' }}>Gender</option>
                        <option value="male" class="text-black" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" class="text-black" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                    </select>
                    <p class="text-xs text-gray-400 mt-1">Required</p>
                </div>
                
                

                <div class="mb-3">
                    <select name="fitness_goal" 
                            class="p-3 w-full border border-gray-600 bg-transparent rounded">
                        <option value="" selected>Fitness Goal (Optional)</option>
                        <option value="lose-weight" class="text-black" {{ old('fitness_goal') == 'lose-weight' ? 'selected' : '' }}>Weight Loss</option>
                        <option value="build-muscle" class="text-black" {{ old('fitness_goal') == 'build-muscle' ? 'selected' : '' }}>Build Muscle</option>
                        <option value="maintain" class="text-black" {{ old('fitness_goal') == 'maintain' ? 'selected' : '' }}>Maintain Fitness</option>
                        <option value="boxing" class="text-black" {{ old('fitness_goal') == 'boxing' ? 'selected' : '' }}>Boxing</option>
                        <option value="muay-thai" class="text-black" {{ old('fitness_goal') == 'muay-thai' ? 'selected' : '' }}>Muay Thai</option>
                        <option value="jiu-jitsu" class="text-black" {{ old('fitness_goal') == 'jiu-jitsu' ? 'selected' : '' }}>Jiu-Jitsu</option>
                    </select>
                    <p class="text-xs text-gray-400 mt-1">Optional</p>
                </div>

                <div class="mb-3">
                    <input type="email" name="email" id="emailInput" placeholder="Email Address" required
                           class="p-3 w-full border border-gray-600 bg-transparent rounded"
                           value="{{ old('email') }}"
                           oninput="validateEmail(this)"/>
                    <p class="text-xs text-gray-400 mt-1">Required, must be a valid email address</p>
                    <p class="text-xs text-red-500 mt-1 email-error-message hidden"></p>
                </div>
                
                <div class="mb-3">
                    <input type="tel" id="mobileNumberInput" name="mobile_number" placeholder="+63 9XX XXX XXXX" required
                           class="p-3 w-full border border-gray-600 bg-transparent rounded" 
                           value="{{ old('mobile_number', '+63 ') }}"
                           onfocus="if(this.value === '+63 ') { this.setSelectionRange(4, 4); }" 
                           onkeydown="if(event.key === 'Backspace' && this.value.length <= 4) { event.preventDefault(); }" 
                           onkeyup="validateMobileNumber(this)" 
                           onblur="validateMobileNumber(this, true)" />
                    <p class="text-xs text-gray-400 mt-1">Required, must be a valid Philippine mobile number (+63 format)</p>
                    <p class="text-xs text-red-500 mt-1 error-message hidden"></p>
                </div>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        // Initialize mobile number input with +63 prefix
                        const mobileInput = document.getElementById('mobileNumberInput');
                        if (mobileInput && (!mobileInput.value || mobileInput.value === '')) {
                            mobileInput.value = '+63 ';
                        }
                        
                        // Form validation for mobile number
                        document.querySelector('form').addEventListener('submit', function(event) {
                            if (!validateMobileNumber(mobileInput)) {
                                event.preventDefault();
                            }
                        });
                    });
                    
                    // Function to validate mobile number (exactly 11 digits)
                    function validateMobileNumber(input, checkUnique = false) {
                        // Remove the +63 prefix and any spaces
                        const number = input.value.replace(/^\+63\s*/, '').replace(/\s+/g, '');
                        
                        // Check if the resulting number has exactly 10 digits (for a total of 11 with the leading 9)
                        if (number.length > 10) {
                            input.value = input.value.substring(0, input.value.length - 1);
                        }
                        
                        // Ensure it starts with 9 after the +63 prefix
                        if (number.length > 0 && number[0] !== '9') {
                            input.value = '+63 9' + number.substring(1);
                        }
                        
                        // Remove any non-numeric characters except spaces
                        input.value = input.value.replace(/[^\d\s\+]/g, '');
                        
                        // Always show validation message unless it's a valid number
                        const isValid = number.length === 10 && number[0] === '9' && /^\d+$/.test(number);
                        
                        if (isValid) {
                            clearInlineError(input);
                            
                            // Check uniqueness if requested and valid format
                            if (checkUnique && isValid) {
                                checkMobileNumberUnique(input);
                            }
                        } else if (input.value.length > 4) { // Only show error if user has started typing (after +63)
                            displayInlineError(input, 'Please enter a valid 11-digit Philippine mobile number starting with 9.');
                        }
                        
                        return isValid;
                    }
                    
                    // Function to check if mobile number is unique in the system
                    function checkMobileNumberUnique(input) {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                        const mobileNumber = input.value.trim();
                        
                        // Find error message element (we created a static one)
                        let errorElement = input.nextElementSibling.nextElementSibling;
                        
                        // Display 'checking' status
                        errorElement.textContent = 'Checking mobile number availability...';
                        errorElement.classList.remove('hidden');
                        errorElement.classList.add('text-blue-500');
                        errorElement.classList.remove('text-red-500');
                        
                        // Send AJAX request to check uniqueness
                        fetch('/api/validate/mobile-number-unique', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify({
                                mobile_number: mobileNumber
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (!data.unique) {
                                // Mobile number already exists
                                input.classList.remove('border-gray-600');
                                input.classList.add('border-red-500');
                                errorElement.textContent = 'This mobile number is already registered. Please use a different number.';
                                errorElement.classList.remove('text-blue-500');
                                errorElement.classList.add('text-red-500');
                                errorElement.classList.remove('hidden');
                                return false;
                            } else {
                                // Mobile number is unique
                                input.classList.remove('border-red-500');
                                input.classList.add('border-gray-600');
                                errorElement.textContent = 'Mobile number is available';
                                errorElement.classList.remove('text-red-500');
                                errorElement.classList.add('text-green-500');
                                errorElement.classList.remove('hidden');
                                
                                // Hide the success message after 2 seconds
                                setTimeout(() => {
                                    errorElement.classList.add('hidden');
                                }, 2000);
                                return true;
                            }
                        })
                        .catch(error => {
                            console.error('Error checking mobile number uniqueness:', error);
                            errorElement.classList.add('hidden');
                            return true; // Continue with validation to avoid blocking user on error
                        });
                    }
                    
                    // Function to validate email with common domains
                    function validateEmail(input) {
                        const validDomains = [
                            'gmail.com', 'yahoo.com', 'outlook.com', 'hotmail.com', 'icloud.com', 
                            'msn.com', 'aol.com', 'ymail.com', 'me.com', 'live.com', 
                            'protonmail.com', 'zoho.com'
                        ];
                        
                        const email = input.value.toLowerCase().trim();
                        let isValid = false;
                        const errorElement = input.nextElementSibling.nextElementSibling;
                        
                        // Reset validation state
                        input.classList.remove('border-red-500', 'border-green-500');
                        if (errorElement) {
                            errorElement.classList.add('hidden');
                        }
                        
                        if (!email) {
                            return false; // Empty input, let HTML5 validation handle it
                        }
                        
                        if (email && email.includes('@')) {
                            const parts = email.split('@');
                            if (parts.length === 2 && parts[0].length > 0) {
                                const domain = parts[1].trim();
                                
                                // Check if domain is in our list of valid domains
                                if (!validDomains.includes(domain)) {
                                    displayInlineError(input, 'Please use a common email domain like gmail.com, yahoo.com, outlook.com, etc.');
                                    isValid = false;
                                } else if (!/^[a-z0-9._%+-]+$/.test(parts[0])) {
                                    // Check if the username part contains valid characters
                                    displayInlineError(input, 'Email contains invalid characters');
                                    isValid = false;
                                } else {
                                    // Valid email
                                    input.classList.add('border-green-500');
                                    isValid = true;
                                }
                            } else {
                                displayInlineError(input, 'Please enter a valid email format');
                                isValid = false;
                            }
                        } else {
                            displayInlineError(input, 'Email must contain an @ symbol');
                            isValid = false;
                        }
                        
                        return isValid;
                    }
                    
                    // Function to display inline error messages
                    function displayInlineError(input, message) {
                        // Add red border
                        input.classList.add('border-red-500');
                        
                        // Show error message
                        const errorElement = input.nextElementSibling.nextElementSibling;
                        if (errorElement) {
                            errorElement.textContent = message;
                            errorElement.classList.remove('hidden');
                        }
                    }
                    
                    // Function to clear inline error messages
                    function clearInlineError(input) {
                        // Remove red border
                        input.classList.remove('border-red-500');
                        input.classList.add('border-gray-600');
                        // Find and hide error message element
                        const errorElement = input.nextElementSibling.nextElementSibling;
                        errorElement.textContent = '';
                        errorElement.classList.add('hidden');
                    }
                </script>

                <div class="relative mb-3">
                    <div class="relative">
                        <input type="password" name="password" id="password" placeholder="Password" required
                               class="p-3 w-full border border-gray-600 bg-transparent rounded pr-10"
                               minlength="8"/>
                        <button type="button" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-white" 
                                onclick="togglePasswordVisibility('password', 'passwordEye')">
                            <i id="passwordEye" class="fas fa-eye"></i>
                        </button>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Required, minimum 8 characters with uppercase, lowercase, and number</p>
                </div>

                <div class="relative mb-3">
                    <div class="relative">
                        <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Confirm Password" required
                               class="p-3 w-full border border-gray-600 bg-transparent rounded pr-10"
                               minlength="8"/>
                        <button type="button" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-white"
                                onclick="togglePasswordVisibility('password_confirmation', 'confirmEye')">
                            <i id="confirmEye" class="fas fa-eye"></i>
                        </button>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Required, must match password</p>
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
