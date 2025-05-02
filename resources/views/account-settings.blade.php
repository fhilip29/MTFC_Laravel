@extends('layouts.app')

@section('title', 'Account Settings')

@section('content')
<div class="min-h-screen bg-[#121212] py-8 px-4 text-white">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-2xl font-bold flex items-center gap-2">
                <i class="fas fa-user-cog text-red-500"></i> Account Settings
            </h1>
            <p class="text-gray-400 mt-2">Manage your profile information and account preferences</p>
        </div>

        @if(session('success'))
            <div class="bg-green-800 bg-opacity-80 text-green-100 px-4 py-3 rounded-lg mb-6 flex items-start">
                <i class="fas fa-check-circle mt-1 mr-3"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
            <!-- Navigation Sidebar -->
            <div class="md:col-span-1">
                <div class="bg-[#1e1e1e] rounded-xl p-4">
                    <nav class="space-y-1">
                        <a href="#profile-section" class="block py-2 px-3 rounded-lg bg-[#2d2d2d] text-white font-medium">
                            <i class="fas fa-user mr-2"></i> Profile
                        </a>
                        <a href="#password-section" class="block py-2 px-3 rounded-lg hover:bg-[#2d2d2d] text-gray-300 transition-colors">
                            <i class="fas fa-lock mr-2"></i> Password
                        </a>
                        <a href="{{ route('profile') }}" class="block py-2 px-3 rounded-lg hover:bg-[#2d2d2d] text-gray-300 transition-colors">
                            <i class="fas fa-arrow-left mr-2"></i> Back to Profile
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="md:col-span-4 space-y-6">
                <!-- Profile Section -->
                <div id="profile-section" class="bg-[#1e1e1e] rounded-xl p-6">
                    <h2 class="text-xl font-semibold mb-6">Profile Information</h2>

                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        
                        <!-- Profile Picture -->
                        <div class="flex flex-col sm:flex-row items-center gap-6">
                            <div class="relative h-32 w-32 rounded-full overflow-hidden bg-[#2d2d2d] border-4 border-[#2d2d2d]">
                                <img id="preview-image" 
                                    src="{{ $user->profile_image ? asset('storage/'.$user->profile_image) : asset('assets/default-profile.jpg') }}" 
                                    alt="Profile Picture" class="h-full w-full object-cover">
                            </div>
                            <div class="flex flex-col space-y-2">
                                <label class="bg-[#374151] hover:bg-[#4B5563] text-white px-4 py-2 rounded-lg cursor-pointer text-center transition-colors inline-block">
                                    <i class="fas fa-camera mr-2"></i> Change Photo
                                    <input type="file" class="hidden" name="profile_image" accept="image/*" onchange="previewImage(event)">
                                </label>
                                <span class="text-xs text-gray-400">JPEG, PNG, GIF up to 4MB</span>
                                @error('profile_image')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- Form Fields -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-gray-300 text-sm mb-1">Full Name</label>
                                <input type="text" name="full_name" value="{{ old('full_name', $user->full_name) }}" 
                                    class="w-full px-4 py-2 bg-[#2d2d2d] border border-[#374151] rounded-lg focus:ring-red-500 focus:border-red-500 text-white">
                                @error('full_name')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-gray-300 text-sm mb-1">Email</label>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                    class="w-full px-4 py-2 bg-[#2d2d2d] border border-[#374151] rounded-lg focus:ring-red-500 focus:border-red-500 text-white">
                                @error('email')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-gray-300 text-sm mb-1">Mobile Number</label>
                                <input type="tel" name="mobile_number" value="{{ old('mobile_number', $user->mobile_number) }}"
                                    class="w-full px-4 py-2 bg-[#2d2d2d] border border-[#374151] rounded-lg focus:ring-red-500 focus:border-red-500 text-white">
                                @error('mobile_number')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-gray-300 text-sm mb-1">Gender</label>
                                <select name="gender" class="w-full px-4 py-2 bg-[#2d2d2d] border border-[#374151] rounded-lg focus:ring-red-500 focus:border-red-500 text-white">
                                    <option value="">Select Gender</option>
                                    <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="other" {{ old('gender', $user->gender) == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('gender')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-gray-300 text-sm mb-1">Fitness Goal</label>
                                <select name="fitness_goal" class="w-full px-4 py-2 bg-[#2d2d2d] border border-[#374151] rounded-lg focus:ring-red-500 focus:border-red-500 text-white">
                                    <option value="">Select Goal</option>
                                    <option value="weight-loss" {{ old('fitness_goal', $user->fitness_goal) == 'weight-loss' ? 'selected' : '' }}>Weight Loss</option>
                                    <option value="muscle-gain" {{ old('fitness_goal', $user->fitness_goal) == 'muscle-gain' ? 'selected' : '' }}>Build Muscle</option>
                                    <option value="endurance" {{ old('fitness_goal', $user->fitness_goal) == 'endurance' ? 'selected' : '' }}>Improve Endurance</option>
                                    <option value="flexibility" {{ old('fitness_goal', $user->fitness_goal) == 'flexibility' ? 'selected' : '' }}>Increase Flexibility</option>
                                    <option value="general-fitness" {{ old('fitness_goal', $user->fitness_goal) == 'general-fitness' ? 'selected' : '' }}>General Fitness</option>
                                </select>
                                @error('fitness_goal')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- Save Button -->
                        <div class="flex justify-end">
                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg transition-colors flex items-center">
                                <i class="fas fa-save mr-2"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Password Section -->
                <div id="password-section" class="bg-[#1e1e1e] rounded-xl p-6">
                    <h2 class="text-xl font-semibold mb-6">Change Password</h2>

                    <form action="{{ route('password.update') }}" method="POST" class="space-y-6">
                        @csrf
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-gray-300 text-sm mb-1">Current Password</label>
                                <input type="password" name="current_password"
                                    class="w-full px-4 py-2 bg-[#2d2d2d] border border-[#374151] rounded-lg focus:ring-red-500 focus:border-red-500 text-white">
                                @error('current_password', 'password')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-gray-300 text-sm mb-1">New Password</label>
                                <input type="password" name="password"
                                    class="w-full px-4 py-2 bg-[#2d2d2d] border border-[#374151] rounded-lg focus:ring-red-500 focus:border-red-500 text-white">
                                @error('password', 'password')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-gray-300 text-sm mb-1">Confirm New Password</label>
                                <input type="password" name="password_confirmation"
                                    class="w-full px-4 py-2 bg-[#2d2d2d] border border-[#374151] rounded-lg focus:ring-red-500 focus:border-red-500 text-white">
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end">
                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg transition-colors flex items-center">
                                <i class="fas fa-lock mr-2"></i> Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function previewImage(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview-image').src = e.target.result;
        }
        reader.readAsDataURL(file);
    }
}

// Tab navigation
document.addEventListener('DOMContentLoaded', function() {
    const navLinks = document.querySelectorAll('nav a');
    
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            // Remove active class from all links
            navLinks.forEach(l => {
                l.classList.remove('bg-[#2d2d2d]', 'text-white', 'font-medium');
                l.classList.add('text-gray-300');
            });
            
            // Add active class to clicked link
            this.classList.add('bg-[#2d2d2d]', 'text-white', 'font-medium');
            this.classList.remove('text-gray-300');
        });
    });
});
</script>
@endsection