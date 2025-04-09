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

  <!-- SweetAlert2 (optional for later use) -->
  <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-[#1D1B20] min-h-screen flex items-center justify-center">

  <div class="flex w-full h-screen">
    <!-- Left Section -->
    <div class="flex flex-1 items-center justify-center p-4">
      <div class="bg-white rounded-lg shadow-lg w-[320px] md:w-[400px] text-center p-6"
           x-data="forgotPassword()"
           x-init="init()"
      >

        <!-- Step 1: Enter Email -->
        <div x-show="step === 1">
          <img src="{{ asset('assets/sent.png') }}" class="mx-auto mb-4 w-10 h-10" />
          <h2 class="text-xl font-bold text-gray-800 mb-2">Enter your email address</h2>
          <p class="text-sm text-gray-600 mb-4">Enter your email and we’ll send you a reset code. Check your inbox and spam folder.</p>

          <input type="email" x-model="email" placeholder="Enter email address"
                 class="w-full p-3 border border-gray-300 rounded text-center mb-3" />

          <button @click="sendCode"
                  class="w-full bg-gray-800 text-white py-2 rounded hover:bg-gray-700">
            Send Code
          </button>
        </div>

        <!-- Step 2: Verify Code -->
        <div x-show="step === 2">
          <img src="{{ asset('assets/lock.png') }}" class="mx-auto mb-4 w-10 h-10" />
          <h2 class="text-xl font-bold text-gray-800 mb-2">Enter the Verification Code</h2>
          <p class="text-sm text-gray-600 mb-2">We sent a code to <strong x-text="email"></strong>. Enter it below.</p>

          <input type="text" x-model="verificationCode" placeholder="Enter 4-digit code"
                 class="w-full p-3 border border-gray-300 rounded text-center mb-3" />

          <button @click="verifyCode"
                  class="w-full bg-gray-800 text-white py-2 rounded hover:bg-gray-700">
            Verify Code
          </button>

          <p class="text-sm text-gray-600 mt-2">
            Didn't receive an email?
            <span class="text-blue-600 font-semibold cursor-pointer" @click="tryAgain">Try Again</span>
          </p>
        </div>

        <!-- Step 3: New Password -->
        <div x-show="step === 3">
          <h2 class="text-xl font-bold text-gray-800 mb-2">Reset Password</h2>

          <input type="password" x-model="newPassword" placeholder="New Password"
                 class="w-full p-3 border border-gray-300 rounded text-center mb-2" />

          <input type="password" x-model="newPasswordMatch" placeholder="Confirm Password"
                 class="w-full p-3 border border-gray-300 rounded text-center mb-3" />

                 <button @click="confirmChangePassword"
                 class="w-full bg-gray-800 text-white py-2 rounded hover:bg-gray-700">
                  Save
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

        init() {
          console.log("Forgot Password Component Loaded");
        },

        async sendCode() {
          console.log("Sending code to", this.email);
          // Backend call will be added later
          this.step = 2;
        },

        async verifyCode() {
          console.log("Verifying code:", this.verificationCode);
          // Logic check with backend
          this.step = 3;
        },

        async confirmChangePassword() {
          if (this.newPassword !== this.newPasswordMatch) {
            Swal.fire('Oops!', 'Passwords do not match!', 'error');
            return;
          }

          console.log("Changing password for", this.email);
          // Backend API call will go here
          Swal.fire('Success', 'Password changed successfully!', 'success');
          // window.location.href = "/login"; // optional
        },

        tryAgain() {
          console.log("Trying again");
          // Re-trigger sendCode()
        }
      };
    }
  </script>

</body>
</html>
