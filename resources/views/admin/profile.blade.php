@extends('layouts.admin')

@section('title', 'Admin Profile')

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

<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row gap-8">
        <!-- Left Column - Profile Summary -->
        <div class="md:w-1/3">
            <div class="bg-[#1F2937] rounded-2xl shadow-md border border-[#374151] overflow-hidden">
                <!-- Profile Header -->
                <div class="p-6 text-center border-b border-[#374151]">
                    <div class="relative inline-block mb-4">
                        <div class="h-28 w-28 mx-auto rounded-full overflow-hidden border-4 border-[#374151]">
                            @if(Auth::user()->profile_image)
                                <img src="{{ asset(Auth::user()->profile_image) }}" alt="{{ Auth::user()->full_name }}" class="h-full w-full object-cover">
                            @else
                                <div class="h-full w-full bg-red-600 flex items-center justify-center">
                                    <span class="text-white font-bold text-2xl">{{ strtoupper(substr(Auth::user()->full_name, 0, 2)) }}</span>
                                </div>
                            @endif
                        </div>
                        <div class="absolute bottom-0 right-0 bg-green-500 h-4 w-4 rounded-full border-2 border-[#1F2937]"></div>
                    </div>
                    <h2 class="text-xl font-bold text-white">{{ Auth::user()->full_name }}</h2>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-500 text-white mt-2">
                        Administrator
                    </span>
                </div>
                
                <!-- Contact Information -->
                <div class="p-6 border-b border-[#374151]">
                    <h3 class="text-sm uppercase text-[#9CA3AF] font-semibold mb-4">Contact Information</h3>
                    <div class="space-y-3">
                        <div class="flex items-center text-sm">
                            <i class="fas fa-envelope text-[#9CA3AF] w-5"></i>
                            <span class="ml-3 text-white">{{ Auth::user()->email }}</span>
                        </div>
                        <div class="flex items-center text-sm">
                            <i class="fas fa-phone text-[#9CA3AF] w-5"></i>
                            <span class="ml-3 text-white">{{ Auth::user()->mobile_number ?? 'Not provided' }}</span>
                        </div>
                    </div>
                </div>
                
                <!-- Account Information -->
                <div class="p-6">
                    <h3 class="text-sm uppercase text-[#9CA3AF] font-semibold mb-4">Account Information</h3>
                    <div class="space-y-3">
                        <div class="flex items-center text-sm">
                            <i class="fas fa-calendar text-[#9CA3AF] w-5"></i>
                            <span class="ml-3 text-white">Joined: {{ Auth::user()->created_at->format('M d, Y') }}</span>
                        </div>
                        <div class="flex items-center text-sm">
                            <i class="fas fa-clock text-[#9CA3AF] w-5"></i>
                            <span class="ml-3 text-white">Last login: {{ now()->subHours(rand(1, 24))->format('M d, Y H:i A') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Column - Profile Settings -->
        <div class="md:w-2/3">
            <div class="bg-[#1F2937] rounded-2xl shadow-md border border-[#374151] overflow-hidden">
                <div class="p-6 border-b border-[#374151]">
                    <h2 class="text-xl font-bold text-white">Profile Settings</h2>
                </div>
                
                <!-- Settings Form -->
                <div class="p-6">
                    <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Personal Information Section -->
                        <div class="mb-8">
                            <h3 class="text-white font-semibold mb-4 pb-2 border-b border-[#374151]">Personal Information</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="md:col-span-2">
                                    <label for="full_name" class="block text-sm font-medium text-[#9CA3AF] mb-2">Full Name</label>
                                    <input type="text" name="full_name" id="full_name" value="{{ old('full_name', Auth::user()->full_name) }}" class="w-full px-3 py-2 bg-[#374151] border border-[#4B5563] rounded-md text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    @error('full_name')
                                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label for="email" class="block text-sm font-medium text-[#9CA3AF] mb-2">Email Address</label>
                                    <input type="email" name="email" id="email" value="{{ old('email', Auth::user()->email) }}" 
                                           oninput="validateEmail(this)"
                                           class="w-full px-3 py-2 bg-[#374151] border border-[#4B5563] rounded-md text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    @error('email')
                                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                    @enderror
                                    <p class="text-red-500 text-sm mt-1 email-error-message hidden"></p>
                                </div>
                                
                                <div>
                                    <label for="mobile_number" class="block text-sm font-medium text-[#9CA3AF] mb-2">Mobile Number</label>
                                    <input type="text" name="mobile_number" id="mobile_number" 
                                           value="{{ old('mobile_number', Auth::user()->mobile_number) }}" 
                                           placeholder="+63 9XX XXX XXXX" 
                                           pattern="\+63\s?9\d{2}\s?\d{3}\s?\d{4}|\+639\d{9}" 
                                           maxlength="14"
                                           onfocus="if(this.value === '+63 ') { this.setSelectionRange(4, 4); }" 
                                           onkeydown="if(event.key === 'Backspace' && this.value.length <= 4) { event.preventDefault(); }" 
                                           onkeyup="this.value = this.value.replace(/[^0-9+\s]/g, ''); if(!this.value.startsWith('+63 ')) { this.value = '+63 ' + this.value.substring(4); }" 
                                           onblur="validateMobileNumber(this)"
                                           class="w-full px-3 py-2 bg-[#374151] border border-[#4B5563] rounded-md text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    @error('mobile_number')
                                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                    @enderror
                                    <div class="error-message text-red-500 text-xs mt-1 hidden" id="mobile_number_error"></div>
                                    <p class="text-xs text-[#9CA3AF] mt-1">Format: +63 9XX XXX XXXX</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Profile Image Section -->
                        <div class="mb-8">
                            <h3 class="text-white font-semibold mb-4 pb-2 border-b border-[#374151]">Profile Image</h3>
                            
                            <div class="flex items-start space-x-4">
                                <div class="h-20 w-20 rounded-full overflow-hidden bg-[#374151]">
                                    @if(Auth::user()->profile_image)
                                        <img src="{{ asset(Auth::user()->profile_image) }}" alt="{{ Auth::user()->full_name }}" class="h-full w-full object-cover" id="profile-preview">
                                    @else
                                        <div class="h-full w-full bg-red-600 flex items-center justify-center" id="profile-preview-placeholder">
                                            <span class="text-white font-bold text-xl">{{ strtoupper(substr(Auth::user()->full_name, 0, 2)) }}</span>
                                        </div>
                                        <img src="" alt="" class="h-full w-full object-cover hidden" id="profile-preview">
                                    @endif
                                </div>
                                
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-[#9CA3AF] mb-2">Update Profile Image</label>
                                    <input type="file" name="profile_image" id="profile_image" accept="image/png, image/jpeg, image/jpg" class="w-full text-[#9CA3AF] file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-[#374151] file:text-white hover:file:bg-[#4B5563]">
                                    <input type="hidden" id="cropped_image" name="cropped_image">
                                    @error('profile_image')
                                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                    @enderror
                                    <p class="mt-1 text-xs text-[#9CA3AF]">PNG, JPG or JPEG. Recommended size: 300x300 pixels. Max file size: 2MB.</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Password Change Section -->
                        <div class="mb-8">
                            <h3 class="text-white font-semibold mb-4 pb-2 border-b border-[#374151]">Change Password</h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <label for="current_password" class="block text-sm font-medium text-[#9CA3AF] mb-2">Current Password</label>
                                    <input type="password" name="current_password" id="current_password" class="w-full px-3 py-2 bg-[#374151] border border-[#4B5563] rounded-md text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    @error('current_password')
                                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                    @enderror
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="new_password" class="block text-sm font-medium text-[#9CA3AF] mb-2">New Password</label>
                                        <input type="password" name="new_password" id="new_password" class="w-full px-3 py-2 bg-[#374151] border border-[#4B5563] rounded-md text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        @error('new_password')
                                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    
                                    <div>
                                        <label for="new_password_confirmation" class="block text-sm font-medium text-[#9CA3AF] mb-2">Confirm New Password</label>
                                        <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="w-full px-3 py-2 bg-[#374151] border border-[#4B5563] rounded-md text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>
                                </div>
                                
                                <p class="text-xs text-[#9CA3AF]">Leave password fields empty if you don't want to change your password.</p>
                            </div>
                        </div>
                        
                        <!-- Submit Button -->
                        <div class="flex justify-end">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-lg transition">
                                <i class="fas fa-save mr-2"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Activity Logs -->
            <div class="bg-[#1F2937] rounded-2xl shadow-md border border-[#374151] overflow-hidden mt-6">
                <div class="p-6 border-b border-[#374151]">
                    <h2 class="text-xl font-bold text-white">Recent Activity</h2>
                </div>
                
                <div class="p-6">
                    <div class="space-y-4">
                        <!-- Sample activity logs -->
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 h-8 w-8 bg-blue-500 rounded-full flex items-center justify-center">
                                <i class="fas fa-sign-in-alt text-white"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-white font-medium">System Login</p>
                                <p class="text-xs text-[#9CA3AF]">{{ now()->subHours(2)->format('M d, Y H:i A') }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 h-8 w-8 bg-green-500 rounded-full flex items-center justify-center">
                                <i class="fas fa-edit text-white"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-white font-medium">Updated Membership Plan</p>
                                <p class="text-xs text-[#9CA3AF]">{{ now()->subDays(1)->format('M d, Y H:i A') }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 h-8 w-8 bg-purple-500 rounded-full flex items-center justify-center">
                                <i class="fas fa-user-plus text-white"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-white font-medium">Added New Trainer</p>
                                <p class="text-xs text-[#9CA3AF]">{{ now()->subDays(3)->format('M d, Y H:i A') }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 h-8 w-8 bg-yellow-500 rounded-full flex items-center justify-center">
                                <i class="fas fa-chart-line text-white"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-white font-medium">Generated Monthly Report</p>
                                <p class="text-xs text-[#9CA3AF]">{{ now()->subDays(7)->format('M d, Y H:i A') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Image Cropper Modal -->
<div id="cropperModal" class="fixed inset-0 bg-black bg-opacity-70 z-[100] flex items-center justify-center overflow-y-auto">
    <div class="bg-[#1F2937] rounded-xl shadow-xl border border-[#374151] w-full max-w-2xl max-h-[90vh] overflow-y-auto my-4 mx-2">
        <div class="p-6 border-b border-[#374151] sticky top-0 bg-[#1F2937] z-10">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-bold text-white">Crop Profile Image</h3>
                <button type="button" id="closeCropperModal" class="text-[#9CA3AF] hover:text-white">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        
        <div class="image-cropper-container p-6">
            <div id="cropperContainer" class="mb-4">
                <img id="cropperImage" src="" class="max-w-full">
            </div>
            
            <div class="preview mb-6"></div>
            
            <div class="flex justify-end space-x-3 sticky bottom-0 pt-4 pb-2 bg-[#1F2937] border-t border-[#374151]">
                <button type="button" id="cancelCrop" class="px-5 py-2 bg-[#374151] text-white rounded-lg hover:bg-[#4B5563] transition-colors">
                    Cancel
                </button>
                <button type="button" id="applyCrop" class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Apply Crop
                </button>
            </div>
        </div>
    </div>
</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Function to validate email with common domains
    function validateEmail(input) {
        const validDomains = [
            'gmail.com', 'yahoo.com', 'outlook.com', 'hotmail.com', 'icloud.com', 
            'msn.com', 'aol.com', 'ymail.com', 'me.com', 'live.com', 
            'protonmail.com', 'zoho.com'
        ];
        
        const email = input.value.toLowerCase().trim();
        let isValid = false;
        
        // Find the error element
        let errorElement = input.nextElementSibling;
        while (errorElement && !errorElement.classList.contains('email-error-message')) {
            errorElement = errorElement.nextElementSibling;
        }
        
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
                    input.classList.add('border-red-500');
                    if (errorElement) {
                        errorElement.textContent = 'Please use a common email domain like gmail.com, yahoo.com, outlook.com, etc.';
                        errorElement.classList.remove('hidden');
                    }
                    isValid = false;
                } else if (!/^[a-z0-9._%+-]+$/.test(parts[0])) {
                    // Check if the username part contains valid characters
                    input.classList.add('border-red-500');
                    if (errorElement) {
                        errorElement.textContent = 'Email contains invalid characters';
                        errorElement.classList.remove('hidden');
                    }
                    isValid = false;
                } else {
                    // Valid email
                    input.classList.add('border-green-500');
                    isValid = true;
                }
            } else {
                input.classList.add('border-red-500');
                if (errorElement) {
                    errorElement.textContent = 'Please enter a valid email format';
                    errorElement.classList.remove('hidden');
                }
                isValid = false;
            }
        } else {
            input.classList.add('border-red-500');
            if (errorElement) {
                errorElement.textContent = 'Email must contain an @ symbol';
                errorElement.classList.remove('hidden');
            }
            isValid = false;
        }
        
        return isValid;
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-populate +63 prefix for mobile number
        const mobileInput = document.getElementById('mobile_number');
        if (mobileInput && !mobileInput.value) {
            mobileInput.value = '+63 ';
        }
        
        // Add event listener for form submission
        document.querySelector('form').addEventListener('submit', function(event) {
            if (mobileInput) {
                if (!validateMobileNumberOnSubmit(mobileInput)) {
                    event.preventDefault();
                }
            }
        });
        
        // Image Cropper
        let cropper;
        const profileImageInput = document.getElementById('profile_image');
        const previewImage = document.getElementById('profile-preview');
        const croppedImageInput = document.getElementById('cropped_image');
        const cropperModal = document.getElementById('cropperModal');
        const cropperImage = document.getElementById('cropperImage');
        const closeCropperModal = document.getElementById('closeCropperModal');
        const cancelCrop = document.getElementById('cancelCrop');
        const applyCrop = document.getElementById('applyCrop');
        const profilePlaceholder = document.getElementById('profile-preview-placeholder');
        
        // Initialize - hide the cropper modal
        cropperModal.style.display = 'none';
        
        // Handle file selection
        profileImageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            
            if (file) {
                const allowedTypes = ['image/png', 'image/jpeg', 'image/jpg'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Please upload only PNG, JPG or JPEG files.');
                    profileImageInput.value = '';
                    return;
                }
                if (file.size > 2 * 1024 * 1024) {
                    alert('File size should not exceed 2MB');
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
            previewImage.classList.remove('hidden');
            
            if (profilePlaceholder) {
                profilePlaceholder.classList.add('hidden');
            }
            
            // Close the cropper dialog
            closeCropperDialog();
        });
        
        // SweetAlert for success message
        @if(session('success'))
            Swal.fire({
                title: 'Success!',
                text: "{{ session('success') }}",
                icon: 'success',
                confirmButtonColor: '#3B82F6',
                background: '#1F2937',
                color: '#FFFFFF',
                customClass: {
                    popup: 'rounded-lg border border-[#374151]',
                    title: 'text-white text-xl',
                    htmlContainer: 'text-[#9CA3AF]',
                    confirmButton: 'rounded-md px-4 py-2'
                }
            });
        @endif
        
        // SweetAlert for error messages
        @if($errors->any())
            Swal.fire({
                title: 'Error!',
                html: `<ul class="text-left">
                    @foreach($errors->all() as $error)
                        <li class="text-sm text-gray-300 mb-1">• {{ $error }}</li>
                    @endforeach
                </ul>`,
                icon: 'error',
                confirmButtonColor: '#EF4444',
                background: '#1F2937',
                color: '#FFFFFF',
                customClass: {
                    popup: 'rounded-lg border border-[#374151]',
                    title: 'text-white text-xl',
                    htmlContainer: 'text-[#9CA3AF] pt-4',
                    confirmButton: 'rounded-md px-4 py-2'
                }
            });
        @endif
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
        
        // Error element ID based on input ID
        const errorId = input.id + '_error';
        const errorElement = document.getElementById(errorId);
        
        if (isValid) {
            clearInlineError(input);
            
            // Check uniqueness if requested and valid format
            if (checkUnique && isValid) {
                checkMobileNumberUnique(input, '{{ Auth::user()->id }}');
            }
        } else if (input.value.length > 4) { // Only show error if user has started typing (after +63)
            displayInlineError(input, 'Please enter a valid 11-digit Philippine mobile number starting with 9.');
        }
        
        return isValid;
    }
    
    // Function to check if mobile number is unique in the system
    function checkMobileNumberUnique(input, currentId = null) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const mobileNumber = input.value.trim();
        
        // Display 'checking' status
        const errorId = input.id + '_error';
        const errorElement = document.getElementById(errorId);
        if (errorElement) {
            errorElement.textContent = 'Checking mobile number availability...';
            errorElement.classList.remove('hidden');
            errorElement.classList.add('text-blue-500');
            errorElement.classList.remove('text-red-500');
        }
        
        // Send AJAX request to check uniqueness
        fetch('/api/validate/mobile-number-unique', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                mobile_number: mobileNumber,
                current_id: currentId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (!data.unique) {
                // Mobile number already exists
                displayInlineError(input, 'This mobile number is already registered by another user.');
                return false;
            } else {
                // Mobile number is unique
                clearInlineError(input);
                if (errorElement) {
                    errorElement.textContent = 'Mobile number is available';
                    errorElement.classList.remove('text-blue-500');
                    errorElement.classList.add('text-green-500');
                    errorElement.classList.remove('hidden');
                    
                    // Hide the success message after 2 seconds
                    setTimeout(() => {
                        errorElement.classList.add('hidden');
                    }, 2000);
                }
                return true;
            }
        })
        .catch(error => {
            console.error('Error checking mobile number uniqueness:', error);
            return true; // Continue with validation to avoid blocking user on error
        });
    }
    
    function validateMobileNumberOnSubmit(input) {
        return validateMobileNumber(input);
    }
    
    // Function to display inline error messages
    function displayInlineError(input, message) {
        // Add red border
        input.classList.remove('border-[#4B5563]');
        input.classList.add('border-red-500');
        
        // Find or create error message element
        let errorElement = input.nextElementSibling;
        if (!errorElement || !errorElement.classList.contains('error-message')) {
            errorElement = document.createElement('p');
            errorElement.className = 'text-xs text-red-500 mt-1 error-message';
            input.parentNode.insertBefore(errorElement, input.nextElementSibling);
        }
        
        // Set error message and make sure it's visible
        errorElement.textContent = message;
        errorElement.classList.remove('hidden');
    }
    
    // Function to clear inline error messages
    function clearInlineError(input) {
        // Remove red border
        input.classList.remove('border-red-500');
        input.classList.add('border-[#4B5563]');
        
        // Find and remove error message element
        const errorElement = input.nextElementSibling;
        if (errorElement && errorElement.classList.contains('error-message')) {
            errorElement.textContent = '';
            errorElement.classList.add('hidden');
        }
    }
</script>
@endsection 