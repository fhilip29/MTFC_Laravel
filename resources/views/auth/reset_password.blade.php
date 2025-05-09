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

                <form method="POST" action="{{ route('password.reset') }}" id="resetPasswordForm">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    <input type="hidden" name="email" value="{{ $email }}">

                    <div class="mb-4">
                        <input type="password" name="password" id="password" placeholder="New Password"
                            class="w-full p-3 border border-gray-300 rounded text-center mb-2"
                            required minlength="8">
                        
                        <div class="text-xs text-left text-gray-600 mt-1">
                            <p>Password must contain:</p>
                            <ul class="list-disc pl-4 mt-1">
                                <li id="length-check" class="text-red-500">At least 8 characters</li>
                                <li id="uppercase-check" class="text-red-500">At least one uppercase letter</li>
                                <li id="lowercase-check" class="text-red-500">At least one lowercase letter</li>
                                <li id="number-check" class="text-red-500">At least one number</li>
                            </ul>
                        </div>
                    </div>

                    <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Confirm Password"
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('password');
            const lengthCheck = document.getElementById('length-check');
            const uppercaseCheck = document.getElementById('uppercase-check');
            const lowercaseCheck = document.getElementById('lowercase-check');
            const numberCheck = document.getElementById('number-check');
            const form = document.getElementById('resetPasswordForm');
            
            // Function to validate password
            function validatePassword() {
                const password = passwordInput.value;
                
                // Check length
                if (password.length >= 8) {
                    lengthCheck.classList.remove('text-red-500');
                    lengthCheck.classList.add('text-green-500');
                } else {
                    lengthCheck.classList.remove('text-green-500');
                    lengthCheck.classList.add('text-red-500');
                }
                
                // Check uppercase
                if (/[A-Z]/.test(password)) {
                    uppercaseCheck.classList.remove('text-red-500');
                    uppercaseCheck.classList.add('text-green-500');
                } else {
                    uppercaseCheck.classList.remove('text-green-500');
                    uppercaseCheck.classList.add('text-red-500');
                }
                
                // Check lowercase
                if (/[a-z]/.test(password)) {
                    lowercaseCheck.classList.remove('text-red-500');
                    lowercaseCheck.classList.add('text-green-500');
                } else {
                    lowercaseCheck.classList.remove('text-green-500');
                    lowercaseCheck.classList.add('text-red-500');
                }
                
                // Check number
                if (/[0-9]/.test(password)) {
                    numberCheck.classList.remove('text-red-500');
                    numberCheck.classList.add('text-green-500');
                } else {
                    numberCheck.classList.remove('text-green-500');
                    numberCheck.classList.add('text-red-500');
                }
            }
            
            // Listen for input changes
            passwordInput.addEventListener('input', validatePassword);
            
            // Form submission validation
            form.addEventListener('submit', function(event) {
                const password = passwordInput.value;
                const confirmPassword = document.getElementById('password_confirmation').value;
                
                const isLengthValid = password.length >= 8;
                const isUppercaseValid = /[A-Z]/.test(password);
                const isLowercaseValid = /[a-z]/.test(password);
                const isNumberValid = /[0-9]/.test(password);
                
                if (!isLengthValid || !isUppercaseValid || !isLowercaseValid || !isNumberValid) {
                    event.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Password',
                        text: 'Your password must meet all the requirements.'
                    });
                    return;
                }
                
                if (password !== confirmPassword) {
                    event.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Passwords Do Not Match',
                        text: 'Please make sure your passwords match.'
                    });
                }
            });
        });
    </script>
</body>
</html> 