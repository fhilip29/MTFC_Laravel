<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Forgot Password</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Alpine.js -->
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-[#1D1B20] min-h-screen flex items-center justify-center">

  <div class="flex w-full h-screen">
    <!-- Left Section -->
    <div class="flex flex-1 items-center justify-center p-4">
      <div class="bg-white rounded-lg shadow-lg w-[320px] md:w-[400px] text-center p-6"
           x-data="forgotPassword()"
           x-init="init()"
      >
        @if(session('error'))
          <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
            {{ session('error') }}
          </div>
        @endif

        <!-- Step 1: Enter Email -->
        <div x-show="step === 1">
          <img src="{{ asset('assets/MTFC_LOGO.PNG') }}" class="mx-auto mb-4 h-12" />
          <h2 class="text-xl font-bold text-gray-800 mb-2">Enter your email address</h2>
          <p class="text-sm text-gray-600 mb-4">Enter your email and we'll send you a reset code. Check your inbox and spam folder.</p>

          <input type="email" x-model="email" placeholder="Enter email address"
                 class="w-full p-3 border border-gray-300 rounded text-center mb-3" />

          <button @click="sendCode"
                  class="w-full bg-gray-800 text-white py-2 rounded hover:bg-gray-700 mb-3">
            Send Code
          </button>
          
          <p class="text-sm text-gray-600 mt-4">
            <a href="{{ route('login') }}" class="text-blue-600 hover:underline">
              Return to login
            </a>
          </p>
        </div>

        <!-- Step 2: Verify Code -->
        <div x-show="step === 2">
          <img src="{{ asset('assets/MTFC_LOGO.PNG') }}" class="mx-auto mb-4 h-12" />
          <h2 class="text-xl font-bold text-gray-800 mb-2">Enter the Verification Code</h2>
          <p class="text-sm text-gray-600 mb-2">We sent a code to <strong x-text="email"></strong>. Enter it below.</p>

          <input type="text" x-model="verificationCode" placeholder="Enter 6-digit code"
                 class="w-full p-3 border border-gray-300 rounded text-center mb-3" 
                 maxlength="6" autocomplete="off" />

          <button @click="verifyCode"
                  class="w-full bg-gray-800 text-white py-2 rounded hover:bg-gray-700 mb-3">
            Verify Code
          </button>

          <button @click="resendCode" 
                 class="w-full border border-gray-800 text-gray-800 py-2 rounded hover:bg-gray-100 mb-3"
                 x-bind:disabled="resendCountdown > 0"
                 x-bind:class="resendCountdown > 0 ? 'opacity-50 cursor-not-allowed' : ''">
            <span x-show="resendCountdown === 0">Send Again</span>
            <span x-show="resendCountdown > 0">Send Again (<span x-text="resendCountdown"></span>s)</span>
          </button>

          <p class="text-sm text-gray-600">
            <span class="text-blue-600 font-semibold cursor-pointer" @click="tryAgain">Try with different email</span>
          </p>
        </div>

        <!-- Step 3: New Password -->
        <div x-show="step === 3">
          <img src="{{ asset('assets/MTFC_LOGO.PNG') }}" class="mx-auto mb-4 h-12" />
          <h2 class="text-xl font-bold text-gray-800 mb-2">Reset Password</h2>
          <p class="text-sm text-gray-600 mb-4">Create a new secure password for your account.</p>

          <input type="password" x-model="newPassword" placeholder="New Password"
                 class="w-full p-3 border border-gray-300 rounded text-center mb-2" />

          <input type="password" x-model="newPasswordMatch" placeholder="Confirm Password"
                 class="w-full p-3 border border-gray-300 rounded text-center mb-3" />

          <button @click="confirmChangePassword"
                 class="w-full bg-gray-800 text-white py-2 rounded hover:bg-gray-700">
            Save New Password
          </button>
        </div>
      </div>
    </div>

    <!-- Right Section -->
    <div class="flex-1 hidden md:block relative overflow-hidden"
         style="clip-path: polygon(0 0, 100% 0, 100% 100%, 25% 100%)">
      <img src="{{ asset('assets/signup_background.png') }}"
           class="w-full h-full object-cover absolute inset-0 z-0" />
    </div>
  </div>

  <!-- Alpine Component Script -->
  <script>
    function forgotPassword() {
      return {
        step: 1,
        email: '',
        verificationCode: '',
        newPassword: '',
        newPasswordMatch: '',
        resendCountdown: 0,
        resendTimer: null,

        init() {
          console.log("Forgot Password Component Loaded");
        },

        async sendCode() {
          if (!this.email) {
            Swal.fire('Error', 'Please enter your email address', 'error');
            return;
          }
          
          // Show loading state
          Swal.fire({
            title: 'Sending...',
            text: 'Sending verification code to your email',
            allowOutsideClick: false,
            didOpen: () => {
              Swal.showLoading();
            }
          });
          
          try {
            console.log('Sending password reset request...');
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 30000); // 30 second timeout
            
            const response = await fetch('{{ route("password.email") }}', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
              },
              body: JSON.stringify({ email: this.email }),
              signal: controller.signal
            });
            
            clearTimeout(timeoutId);
            console.log('Response status:', response.status);
            
            // First check if the response is valid
            let data;
            try {
              data = await response.json();
              console.log('Response received');
            } catch (jsonError) {
              console.error('JSON parsing error:', jsonError);
              throw new Error('Invalid server response');
            }
            
            if (response.ok) {
              this.step = 2;
              this.startResendCountdown();
              
              Swal.fire({
                icon: 'success',
                title: 'Success',
                text: 'Verification code sent to your email',
                footer: 'Please check your inbox and spam folder'
              });
            } else {
              console.error('Server error:', data);
              Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message || 'Failed to send verification code',
                confirmButtonText: 'Try Again'
              });
            }
          } catch (error) {
            console.error('Network error:', error);
            
            let errorMessage = 'Could not connect to the server.';
            let errorDetail = 'Please check your internet connection and try again.';
            
            if (error.name === 'AbortError') {
              errorMessage = 'Request timeout';
              errorDetail = 'The server took too long to respond. Please try again later.';
            }
            
            Swal.fire({
              icon: 'error',
              title: 'Connection Error',
              text: errorMessage,
              footer: errorDetail,
              confirmButtonText: 'Try Again'
            });
          }
        },

        startResendCountdown() {
          this.resendCountdown = 60; // 1 minute countdown
          
          if (this.resendTimer) {
            clearInterval(this.resendTimer);
          }
          
          this.resendTimer = setInterval(() => {
            if (this.resendCountdown > 0) {
              this.resendCountdown--;
            } else {
              clearInterval(this.resendTimer);
            }
          }, 1000);
        },

        async resendCode() {
          if (this.resendCountdown > 0) return;
          
          // Show loading state
          Swal.fire({
            title: 'Resending...',
            text: 'Sending a new verification code',
            allowOutsideClick: false,
            didOpen: () => {
              Swal.showLoading();
            }
          });
          
          try {
            console.log('Resending password reset code...');
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 30000);
            
            const response = await fetch('{{ route("password.email") }}', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
              },
              body: JSON.stringify({ email: this.email }),
              signal: controller.signal
            });
            
            clearTimeout(timeoutId);
            console.log('Response status:', response.status);
            
            let data;
            try {
              data = await response.json();
              console.log('Response received');
            } catch (jsonError) {
              console.error('JSON parsing error:', jsonError);
              throw new Error('Invalid server response');
            }
            
            if (response.ok) {
              this.startResendCountdown();
              
              Swal.fire({
                icon: 'success',
                title: 'Code Resent',
                text: 'Verification code resent to your email',
                footer: 'Please check your inbox and spam folder'
              });
            } else {
              console.error('Server error:', data);
              Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message || 'Failed to resend verification code',
                confirmButtonText: 'Try Again'
              });
            }
          } catch (error) {
            console.error('Network error:', error);
            
            let errorMessage = 'Could not connect to the server.';
            let errorDetail = 'Please check your internet connection and try again.';
            
            if (error.name === 'AbortError') {
              errorMessage = 'Request timeout';
              errorDetail = 'The server took too long to respond. Please try again later.';
            }
            
            Swal.fire({
              icon: 'error',
              title: 'Connection Error',
              text: errorMessage,
              footer: errorDetail,
              confirmButtonText: 'Try Again'
            });
          }
        },

        async verifyCode() {
          if (!this.verificationCode) {
            Swal.fire('Error', 'Please enter the verification code', 'error');
            return;
          }
          
          // Show loading state
          Swal.fire({
            title: 'Verifying...',
            text: 'Checking your verification code',
            allowOutsideClick: false,
            didOpen: () => {
              Swal.showLoading();
            }
          });
          
          try {
            console.log('Verifying code...');
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 30000);
            
            const response = await fetch('{{ route("password.verify") }}', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
              },
              body: JSON.stringify({ 
                email: this.email, 
                code: this.verificationCode 
              }),
              signal: controller.signal
            });
            
            clearTimeout(timeoutId);
            console.log('Response status:', response.status);
            
            let data;
            try {
              data = await response.json();
              console.log('Response received');
            } catch (jsonError) {
              console.error('JSON parsing error:', jsonError);
              throw new Error('Invalid server response');
            }
            
            if (response.ok && data.valid) {
              this.step = 3;
              if (this.resendTimer) {
                clearInterval(this.resendTimer);
              }
              
              Swal.fire({
                icon: 'success',
                title: 'Code Verified',
                text: 'Your code has been verified. Please create a new password.',
                timer: 2000,
                showConfirmButton: false
              });
            } else {
              console.error('Verification error:', data);
              Swal.fire({
                icon: 'error',
                title: 'Verification Failed',
                text: data.message || 'Invalid verification code',
                confirmButtonText: 'Try Again'
              });
            }
          } catch (error) {
            console.error('Network error:', error);
            
            let errorMessage = 'Could not connect to the server.';
            let errorDetail = 'Please check your internet connection and try again.';
            
            if (error.name === 'AbortError') {
              errorMessage = 'Request timeout';
              errorDetail = 'The server took too long to respond. Please try again later.';
            }
            
            Swal.fire({
              icon: 'error',
              title: 'Connection Error',
              text: errorMessage,
              footer: errorDetail,
              confirmButtonText: 'Try Again'
            });
          }
        },

        async confirmChangePassword() {
          if (!this.newPassword || !this.newPasswordMatch) {
            Swal.fire('Error', 'Please enter both password fields', 'error');
            return;
          }
          
          if (this.newPassword !== this.newPasswordMatch) {
            Swal.fire('Error', 'Passwords do not match!', 'error');
            return;
          }
          
          if (this.newPassword.length < 8) {
            Swal.fire('Error', 'Password must be at least 8 characters long', 'error');
            return;
          }

          // Show loading state
          Swal.fire({
            title: 'Saving...',
            text: 'Setting your new password',
            allowOutsideClick: false,
            didOpen: () => {
              Swal.showLoading();
            }
          });

          try {
            console.log('Resetting password...');
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 30000);
            
            const response = await fetch('{{ route("password.reset") }}', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
              },
              body: JSON.stringify({ 
                email: this.email, 
                code: this.verificationCode,
                password: this.newPassword,
                password_confirmation: this.newPasswordMatch
              }),
              signal: controller.signal
            });
            
            clearTimeout(timeoutId);
            console.log('Response status:', response.status);
            
            let data;
            try {
              data = await response.json();
              console.log('Response received');
            } catch (jsonError) {
              console.error('JSON parsing error:', jsonError);
              throw new Error('Invalid server response');
            }
            
            if (response.ok && data.success) {
              Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Your password has been changed successfully.',
                confirmButtonText: 'Log In Now'
              }).then(() => {
                window.location.href = "{{ route('login') }}";
              });
            } else {
              console.error('Password reset error:', data);
              Swal.fire({
                icon: 'error',
                title: 'Password Reset Failed',
                text: data.message || 'Failed to reset password',
                confirmButtonText: 'Try Again'
              });
            }
          } catch (error) {
            console.error('Network error:', error);
            
            let errorMessage = 'Could not connect to the server.';
            let errorDetail = 'Please check your internet connection and try again.';
            
            if (error.name === 'AbortError') {
              errorMessage = 'Request timeout';
              errorDetail = 'The server took too long to respond. Please try again later.';
            }
            
            Swal.fire({
              icon: 'error',
              title: 'Connection Error',
              text: errorMessage,
              footer: errorDetail,
              confirmButtonText: 'Try Again'
            });
          }
        },

        tryAgain() {
          // Only allow trying again if not in cooldown
          if (this.resendCountdown > 0) {
            Swal.fire({
              icon: 'warning',
              title: 'Please wait',
              text: `You need to wait ${this.resendCountdown} seconds before requesting a new code`,
              timer: 3000,
              showConfirmButton: false
            });
            return;
          }
          
          this.step = 1;
          if (this.resendTimer) {
            clearInterval(this.resendTimer);
          }
        }
      };
    }
  </script>
</body>
</html> 