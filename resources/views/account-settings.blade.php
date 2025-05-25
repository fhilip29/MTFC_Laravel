@extends('layouts.app')

@section('title', 'Account Settings')

@section('content')
<!-- Add Cropper.js library -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>

<style>
    .image-cropper-container {
        max-width: 500px;
        margin: 0 auto;
    }
    .cropper-container {
        margin-bottom: 15px;
    }
    .cropper-view-box,
    .cropper-face {
        border-radius: 50%;
    }
    .preview {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        overflow: hidden;
        margin: 0 auto;
    }
    /* Hide the cropper modal by default */
    #cropperModal {
        display: none;
    }
    /* Additional styles for cropper responsiveness */
    @media (max-height: 700px) {
        .image-cropper-container {
            max-height: calc(100vh - 200px);
            overflow-y: auto;
        }
        
        #cropperContainer {
            max-height: 400px;
            overflow: hidden;
        }
    }
</style>

<div class="min-h-screen bg-white py-8 px-4 md:px-8 text-gray-800">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-user-cog text-red-500"></i> Account Settings
            </h1>
            <p class="text-gray-600 mt-2">Manage your profile information and account preferences</p>
        </div>

        @if(session('success'))
            <div class="bg-green-100 text-green-800 px-4 py-3 rounded-lg mb-6 flex items-start">
                <i class="fas fa-check-circle mt-1 mr-3"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
            <!-- Navigation Sidebar -->
            <div class="md:col-span-1">
                <div class="bg-gray-100 rounded-xl p-4">
                    <nav class="space-y-1">
                        <a href="#profile-section" class="block py-2 px-3 rounded-lg bg-gray-200 text-gray-800 font-medium">
                            <i class="fas fa-user mr-2"></i> Profile
                        </a>
                        <a href="#password-section" class="block py-2 px-3 rounded-lg hover:bg-gray-200 text-gray-600 transition-colors">
                            <i class="fas fa-lock mr-2"></i> Password
                        </a>
                        <a href="{{ auth()->user()->isTrainer() ? route('trainer.profile') : route('profile') }}" class="block py-2 px-3 rounded-lg hover:bg-gray-200 text-gray-600 transition-colors">
                            <i class="fas fa-arrow-left mr-2"></i> Back to Profile
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="md:col-span-4 space-y-6">
                <!-- Profile Section -->
                <div id="profile-section" class="bg-white rounded-xl p-6 shadow-md border border-gray-200">
                    <h2 class="text-xl font-semibold mb-6 text-gray-800">Profile Information</h2>

                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        
                        <!-- Profile Picture -->
                        <div class="flex flex-col sm:flex-row items-center gap-6">
                            <div class="relative h-32 w-32 rounded-full overflow-hidden bg-gray-100 border-4 border-gray-200">
                                <img id="preview-image" 
                                    src="{{ $user->profile_image ? asset($user->profile_image) : asset('assets/default-profile.jpg') }}" 
                                    alt="Profile Picture" class="h-full w-full object-cover">
                            </div>
                            <div class="flex flex-col space-y-2">
                                <label class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg cursor-pointer text-center transition-colors inline-block">
                                    <i class="fas fa-camera mr-2"></i> Change Photo
                                    <input type="file" id="profile_image" class="hidden" name="profile_image" accept="image/png, image/jpeg, image/jpg">
                                    <input type="hidden" id="cropped_image" name="cropped_image">
                                </label>
                                <span class="text-xs text-gray-500">PNG, JPG or JPEG up to 4MB</span>
                                @error('profile_image')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- Form Fields -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-gray-600 text-sm mb-1">Full Name</label>
                                <input type="text" name="full_name" value="{{ old('full_name', $user->full_name) }}" 
                                    class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 text-gray-800">
                                @error('full_name')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-gray-600 text-sm mb-1">Email</label>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                    class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 text-gray-800">
                                @error('email')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-gray-600 text-sm mb-1">Mobile Number</label>
                                <input type="tel" name="mobile_number" value="{{ old('mobile_number', $user->mobile_number) }}"
                                    placeholder="Philippine Phone Number (e.g., 09123456789)" 
                                    pattern="^(\+63|0)9\d{9}$"
                                    title="Please enter a valid Philippine mobile number (e.g., 09123456789 or +639123456789)"
                                    class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 text-gray-800">
                                @error('mobile_number')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-gray-600 text-sm mb-1">Gender</label>
                                <select name="gender" id="gender" class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 text-gray-800">
                                    <option value="">Select Gender</option>
                                    <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="other" {{ old('gender', $user->gender) == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('gender')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                            
                            <!-- Custom gender field, hidden by default -->
                            <div id="otherGenderField" class="mt-2 {{ old('gender', $user->gender) == 'other' ? '' : 'hidden' }}">
                                <label class="block text-gray-600 text-sm mb-1">Specify Gender</label>
                                <input type="text" name="other_gender" value="{{ old('other_gender', $user->other_gender) }}"
                                    class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 text-gray-800"
                                    placeholder="Please specify your gender">
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-gray-600 text-sm mb-1">Fitness Goal</label>
                                <select name="fitness_goal" class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 text-gray-800">
                                    <option value="">Select Goal (Optional)</option>
                                    <option value="lose-weight" {{ old('fitness_goal', $user->fitness_goal) == 'lose-weight' ? 'selected' : '' }}>Weight Loss</option>
                                    <option value="build-muscle" {{ old('fitness_goal', $user->fitness_goal) == 'build-muscle' ? 'selected' : '' }}>Build Muscle</option>
                                    <option value="maintain" {{ old('fitness_goal', $user->fitness_goal) == 'maintain' ? 'selected' : '' }}>Maintain Fitness</option>
                                    <option value="boxing" {{ old('fitness_goal', $user->fitness_goal) == 'boxing' ? 'selected' : '' }}>Boxing</option>
                                    <option value="muay-thai" {{ old('fitness_goal', $user->fitness_goal) == 'muay-thai' ? 'selected' : '' }}>Muay Thai</option>
                                    <option value="jiu-jitsu" {{ old('fitness_goal', $user->fitness_goal) == 'jiu-jitsu' ? 'selected' : '' }}>Jiu-Jitsu</option>
                                </select>
                                <p class="text-xs text-gray-500 mt-1">Choose your primary fitness or training goal</p>
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
                <div id="password-section" class="bg-white rounded-xl p-6 shadow-md border border-gray-200">
                    <h2 class="text-xl font-semibold mb-6 text-gray-800">Change Password</h2>

                    <form action="{{ route('password.update') }}" method="POST" class="space-y-6">
                        @csrf
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-gray-600 text-sm mb-1">Current Password</label>
                                <input type="password" name="current_password"
                                    class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 text-gray-800">
                                @error('current_password', 'password')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-gray-600 text-sm mb-1">New Password</label>
                                <input type="password" name="password"
                                    class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 text-gray-800">
                                @error('password', 'password')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-gray-600 text-sm mb-1">Confirm New Password</label>
                                <input type="password" name="password_confirmation"
                                    class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 text-gray-800">
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

<!-- Image Cropper Modal -->
<div id="cropperModal" class="fixed inset-0 bg-black bg-opacity-70 z-[100] flex items-center justify-center overflow-y-auto">
    <div class="bg-white rounded-xl shadow-xl border border-gray-300 w-full max-w-2xl max-h-[90vh] overflow-y-auto my-4 mx-2">
        <div class="p-6 border-b border-gray-300 sticky top-0 bg-white z-10">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-bold text-gray-800">Crop Profile Image</h3>
                <button type="button" id="closeCropperModal" class="text-gray-500 hover:text-gray-800">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        
        <div class="image-cropper-container p-6">
            <div id="cropperContainer" class="mb-4">
                <img id="cropperImage" src="" class="max-w-full">
            </div>
            
            <div class="preview mb-6"></div>
            
            <div class="flex justify-end space-x-3 sticky bottom-0 pt-4 pb-2 bg-white border-t border-gray-300">
                <button type="button" id="cancelCrop" class="px-5 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition-colors">
                    Cancel
                </button>
                <button type="button" id="applyCrop" class="px-5 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    Apply Crop
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Gender field toggle
document.addEventListener('DOMContentLoaded', function() {
    const genderSelect = document.getElementById('gender');
    const otherGenderField = document.getElementById('otherGenderField');
    
    // Initialize based on current value
    if (genderSelect.value === 'other') {
        otherGenderField.classList.remove('hidden');
    } else {
        otherGenderField.classList.add('hidden');
    }
    
    // Add change event listener
    genderSelect.addEventListener('change', function() {
        if (this.value === 'other') {
            otherGenderField.classList.remove('hidden');
        } else {
            otherGenderField.classList.add('hidden');
        }
    });
    
    // Image Cropper
    let cropper;
    const profileImageInput = document.getElementById('profile_image');
    const previewImage = document.getElementById('preview-image');
    const croppedImageInput = document.getElementById('cropped_image');
    const cropperModal = document.getElementById('cropperModal');
    const cropperImage = document.getElementById('cropperImage');
    const closeCropperModal = document.getElementById('closeCropperModal');
    const cancelCrop = document.getElementById('cancelCrop');
    const applyCrop = document.getElementById('applyCrop');
    
    // Initialize - hide the cropper modal
    cropperModal.style.display = 'none';
    
    // Handle file selection
    profileImageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        
        if (file) {
            // File type validation
            const allowedTypes = ['image/png', 'image/jpeg', 'image/jpg'];
            if (!allowedTypes.includes(file.type)) {
                alert('Please upload only PNG, JPG or JPEG files.');
                profileImageInput.value = '';
                return;
            }
            
            // File size validation (4MB max)
            if (file.size > 4 * 1024 * 1024) {
                alert('File size should not exceed 4MB');
                profileImageInput.value = '';
                return;
            }
            
            // Create a FileReader to read the image
            const reader = new FileReader();
            reader.onload = function(e) {
                // Set the image source for the cropper
                cropperImage.src = e.target.result;
                
                // Show the cropper modal
                cropperModal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
                
                // Initialize Cropper.js after the image has loaded
                setTimeout(() => {
                    if (cropper) {
                        cropper.destroy();
                    }
                    
                    cropper = new Cropper(cropperImage, {
                        aspectRatio: 1, // 1:1 ratio for profile picture
                        viewMode: 1,     // Restrict the crop box to not exceed the size of the canvas
                        guides: true,    // Show the dashed lines for guiding
                        center: true,    // Show the center indicator for guiding
                        minContainerWidth: 250,
                        minContainerHeight: 250,
                        dragMode: 'move',
                        preview: '.preview',
                        cropBoxMovable: true,
                        cropBoxResizable: true,
                        toggleDragModeOnDblclick: false
                    });
                }, 200);
            };
            reader.readAsDataURL(file);
        }
    });
    
    // Handle closing the cropper modal
    closeCropperModal.addEventListener('click', closeCropperDialog);
    cancelCrop.addEventListener('click', closeCropperDialog);
    
    function closeCropperDialog() {
        cropperModal.style.display = 'none';
        document.body.style.overflow = 'auto';
        
        if (cropper) {
            cropper.destroy();
            cropper = null;
        }
        
        // Reset the file input
        profileImageInput.value = '';
    }
    
    // Handle applying the crop
    applyCrop.addEventListener('click', function() {
        if (!cropper) return;
        
        // Get the cropped canvas
        const canvas = cropper.getCroppedCanvas({
            width: 300,
            height: 300,
            minWidth: 100,
            minHeight: 100,
            maxWidth: 4096,
            maxHeight: 4096,
            fillColor: '#fff',
            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high',
        });
        
        // Convert canvas to data URL
        const croppedImageData = canvas.toDataURL('image/jpeg', 0.8);
        
        // Set the cropped image data to the hidden input
        croppedImageInput.value = croppedImageData;
        
        // Update the preview image
        previewImage.src = croppedImageData;
        
        // Close the cropper dialog
        closeCropperDialog();
    });

    // Tab navigation
    const navLinks = document.querySelectorAll('nav a');
    
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            // Remove active class from all links
            navLinks.forEach(l => {
                l.classList.remove('bg-gray-200', 'text-gray-800', 'font-medium');
                l.classList.add('text-gray-600');
            });
            
            // Add active class to clicked link
            this.classList.add('bg-gray-200', 'text-gray-800', 'font-medium');
            this.classList.remove('text-gray-600');
        });
    });
});
</script>
@endsection