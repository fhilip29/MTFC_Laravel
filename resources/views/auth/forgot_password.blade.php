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
          
          try {
            const response = await fetch('{{ route("password.email") }}', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
              },
              body: JSON.stringify({ email: this.email })
            });
            
            const data = await response.json();
            
            if (response.ok) {
              this.step = 2;
              this.startResendCountdown();
              Swal.fire('Success', 'Verification code sent to your email', 'success');
              
              // Remove auto-fill functionality for security
              // Even in development mode, users should enter the code manually
            } else {
              Swal.fire('Error', data.message || 'Failed to send verification code', 'error');
            }
          } catch (error) {
            console.error('Error:', error);
            Swal.fire('Error', 'Something went wrong. Please try again.', 'error');
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
          
          try {
            const response = await fetch('{{ route("password.email") }}', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
              },
              body: JSON.stringify({ email: this.email })
            });
            
            const data = await response.json();
            
            if (response.ok) {
              this.startResendCountdown();
              Swal.fire('Success', 'Verification code resent to your email', 'success');
            } else {
              Swal.fire('Error', data.message || 'Failed to resend verification code', 'error');
            }
          } catch (error) {
            console.error('Error:', error);
            Swal.fire('Error', 'Something went wrong. Please try again.', 'error');
          }
        },

        async verifyCode() {
          if (!this.verificationCode) {
            Swal.fire('Error', 'Please enter the verification code', 'error');
            return;
          }
          
          try {
            const response = await fetch('{{ route("password.verify") }}', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
              },
              body: JSON.stringify({ 
                email: this.email, 
                code: this.verificationCode 
              })
            });
            
            const data = await response.json();
            
            if (response.ok && data.valid) {
              this.step = 3;
              if (this.resendTimer) {
                clearInterval(this.resendTimer);
              }
            } else {
              Swal.fire('Error', data.message || 'Invalid verification code', 'error');
            }
          } catch (error) {
            console.error('Error:', error);
            Swal.fire('Error', 'Something went wrong. Please try again.', 'error');
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

          try {
            const response = await fetch('{{ route("password.reset") }}', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
              },
              body: JSON.stringify({ 
                email: this.email, 
                code: this.verificationCode,
                password: this.newPassword,
                password_confirmation: this.newPasswordMatch
              })
            });
            
            const data = await response.json();
            
            if (response.ok && data.success) {
              Swal.fire('Success', 'Password changed successfully!', 'success')
                .then(() => {
                  window.location.href = "{{ route('login') }}";
                });
            } else {
              Swal.fire('Error', data.message || 'Failed to reset password', 'error');
            }
          } catch (error) {
            console.error('Error:', error);
            Swal.fire('Error', 'Something went wrong. Please try again.', 'error');
          }
        },

        tryAgain() {
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