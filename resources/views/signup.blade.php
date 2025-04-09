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

    <!-- SweetAlert2 (optional for feedback) -->
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
        <div class="flex-1 flex justify-center items-center px-6" x-data="signUpForm()">
            <div class="w-full max-w-md">

                <h3 class="text-3xl font-bold text-center mb-2">Sign Up now!</h3>
                <p class="text-center text-gray-300 mb-6">Unlock Your Potential: Join Us Today!</p>

                <input type="text" placeholder="First Name" x-model="firstName"
                       class="p-3 mb-3 w-full border border-gray-600 bg-transparent rounded focus:outline-none focus:ring focus:ring-purple-400"/>

                <input type="text" placeholder="Last Name" x-model="lastName"
                       class="p-3 mb-3 w-full border border-gray-600 bg-transparent rounded"/>

                <select x-model="gender"
                        class="p-3 mb-3 w-full border border-gray-600 bg-transparent rounded">
                    <option value="" disabled selected>Gender</option>
                    <option value="male" class="text-black">Male</option>
                    <option value="female" class="text-black">Female</option>
                </select>

                <select x-model="fitnessGoal"
                        class="p-3 mb-3 w-full border border-gray-600 bg-transparent rounded">
                    <option value="" disabled selected>Fitness Goal</option>
                    <option value="lose-weight" class="text-black">Lose Weight</option>
                    <option value="build-muscle" class="text-black">Build Muscle</option>
                    <option value="maintain" class="text-black">Maintain</option>
                </select>

                <input type="email" placeholder="Email Address" x-model="email"
                       class="p-3 mb-3 w-full border border-gray-600 bg-transparent rounded"/>

                <input type="password" placeholder="Password" x-model="password"
                       class="p-3 mb-3 w-full border border-gray-600 bg-transparent rounded"/>

                <input type="password" placeholder="Confirm Password" x-model="confirmPassword"
                       class="p-3 mb-3 w-full border border-gray-600 bg-transparent rounded"/>

                <button @click="submitForm"
                        class="w-full py-3 bg-white text-black rounded hover:bg-gray-200 transition mt-4">
                    Sign Up
                </button>

                <p class="text-center text-sm mt-6">
                    Already have an account?
                    <a href="/login" class="text-white font-semibold underline hover:text-purple-300">Log in</a>
                </p>
            </div>
        </div>

        <!-- RIGHT: Clipped background image -->
        <div class="hidden md:block flex-1 relative clip-path-custom">
            <img src="{{ asset('assets/signup_background.png') }}"
                 alt="Sign Up Image"
                 class="absolute inset-0 w-full h-full object-cover z-[-1]" />
        </div>

    </div>

    <script>
        function signUpForm() {
            return {
                firstName: '',
                lastName: '',
                gender: '',
                fitnessGoal: '',
                email: '',
                password: '',
                confirmPassword: '',

                submitForm() {
                    if (this.password !== this.confirmPassword) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops!',
                            text: 'Passwords do not match.'
                        });
                        return;
                    }

                    // Mock submit
                    Swal.fire({
                        icon: 'success',
                        title: 'Signed Up!',
                        text: 'Redirecting to login...'
                    }).then(() => {
                        window.location.href = '/login';
                    });
                }
            }
        }
    </script>
</body>
</html>
