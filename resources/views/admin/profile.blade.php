@extends('layouts.admin')

@section('title', 'Admin Profile')

@section('content')
<!-- Add Cropper.js library -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>

<style>
    .profile-container {
        max-width: 1000px;
        margin: 0 auto;
    }
    
    .profile-card {
        background-color: #1F2937;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        margin-bottom: 24px;
        border: 1px solid #374151;
    }
    
    .profile-card-header {
        padding: 20px 24px;
        background-color: #111827;
        border-bottom: 1px solid #374151;
        display: flex;
        align-items: center;
    }
    
    .profile-card-header h3 {
        margin: 0;
        color: white;
        font-size: 18px;
        font-weight: 600;
    }
    
    .profile-card-header .icon {
        width: 24px;
        height: 24px;
        margin-right: 12px;
        color: #3B82F6;
    }
    
    .profile-card-body {
        padding: 24px;
    }
    
    .profile-image-container {
        position: relative;
        width: 160px;
        height: 160px;
        margin: 0 auto 24px;
    }
    
    .profile-image {
        width: 160px;
        height: 160px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #3B82F6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3);
    }
    
    .profile-image-edit {
        position: absolute;
        bottom: 0;
        right: 0;
        background-color: #3B82F6;
        color: white;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        border: 2px solid #1F2937;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        transition: all 0.2s ease;
    }
    
    .profile-image-edit:hover {
        background-color: #2563EB;
        transform: scale(1.05);
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    .form-label {
        display: block;
        margin-bottom: 8px;
        color: #D1D5DB;
        font-size: 14px;
        font-weight: 500;
    }
    
    .form-control {
        width: 100%;
        padding: 10px 14px;
        background-color: #374151;
        border: 1px solid #4B5563;
        border-radius: 6px;
        color: white;
        font-size: 15px;
        transition: all 0.2s ease;
    }
    
    .form-control:focus {
        border-color: #3B82F6;
        outline: none;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
    }
    
    .btn {
        padding: 10px 16px;
        font-size: 15px;
        font-weight: 500;
        border-radius: 6px;
        transition: all 0.2s ease;
        cursor: pointer;
        border: none;
    }
    
    .btn-primary {
        background-color: #3B82F6;
        color: white;
    }
    
    .btn-primary:hover {
        background-color: #2563EB;
        transform: translateY(-1px);
    }
    
    .btn-secondary {
        background-color: #4B5563;
        color: white;
    }
    
    .btn-secondary:hover {
        background-color: #374151;
        transform: translateY(-1px);
    }
    
    .btn-icon {
        margin-right: 8px;
    }
    
    .alert {
        padding: 12px 16px;
        margin-bottom: 20px;
        border-radius: 6px;
        font-size: 15px;
        display: flex;
        align-items: center;
    }
    
    .alert-success {
        background-color: rgba(16, 185, 129, 0.1);
        border: 1px solid rgba(16, 185, 129, 0.2);
        color: #10B981;
    }
    
    .alert-danger {
        background-color: rgba(239, 68, 68, 0.1);
        border: 1px solid rgba(239, 68, 68, 0.2);
        color: #EF4444;
    }
    
    .alert-icon {
        margin-right: 12px;
        font-size: 18px;
    }
    
    .text-danger {
        color: #EF4444;
        font-size: 13px;
        margin-top: 4px;
        display: block;
    }
    
    .profile-info {
        text-align: center;
        margin-bottom: 24px;
    }
    
    .profile-name {
        font-size: 24px;
        font-weight: 700;
        color: white;
        margin: 8px 0;
    }
    
    .profile-role {
        display: inline-block;
        background-color: #3B82F6;
        color: white;
        padding: 4px 12px;
        border-radius: 9999px;
        font-size: 13px;
        font-weight: 500;
        margin-bottom: 8px;
    }
    
    .profile-email {
        color: #9CA3AF;
        font-size: 15px;
    }
    
    .tab-container {
        margin-bottom: 24px;
    }
    
    .tab-nav {
        display: flex;
        border-bottom: 1px solid #374151;
        margin-bottom: 24px;
    }
    
    .tab-btn {
        padding: 12px 20px;
        font-size: 15px;
        font-weight: 500;
        color: #9CA3AF;
        background: none;
        border: none;
        border-bottom: 2px solid transparent;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .tab-btn.active {
        color: #3B82F6;
        border-bottom-color: #3B82F6;
    }
    
    .tab-btn:hover:not(.active) {
        color: #D1D5DB;
    }
    
    .tab-content {
        display: none;
    }
    
    .tab-content.active {
        display: block;
    }
    
    .cropper-container {
        max-width: 100%;
    }
    
    .preview {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        overflow: hidden;
        margin: 0 auto;
    }
    
    .modal-content {
        background-color: #1F2937;
        border: 1px solid #374151;
    }
    
    .modal-header {
        border-bottom: 1px solid #374151;
    }
    
    .modal-footer {
        border-top: 1px solid #374151;
    }
    
    .breadcrumb-item a {
        color: #3B82F6;
        text-decoration: none;
    }
    
    .breadcrumb-item a:hover {
        text-decoration: underline;
    }
    
    .breadcrumb-item.active {
        color: #9CA3AF;
    }
</style>

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mt-4 mb-1">Admin Profile</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Profile</li>
                </ol>
            </nav>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success" role="alert">
            <i class="fas fa-check-circle alert-icon"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger" role="alert">
            <i class="fas fa-exclamation-circle alert-icon"></i>
            {{ session('error') }}
        </div>
    @endif

    <div class="profile-container">
        <div class="profile-card">
            <div class="profile-card-body">
                <div class="profile-image-container">
                    <img id="preview-image" 
                        src="{{ Auth::user()->profile_image ? asset(Auth::user()->profile_image) : asset('assets/default-profile.jpg') }}" 
                        alt="Profile Picture" 
                        class="profile-image">
                    <div id="change-photo-btn" class="profile-image-edit">
                        <i class="fas fa-camera"></i>
                    </div>
                </div>
                <div class="profile-info">
                    <h2 class="profile-name">{{ Auth::user()->full_name }}</h2>
                    <span class="profile-role">Administrator</span>
                    <p class="profile-email">{{ Auth::user()->email }}</p>
                </div>
            </div>
        </div>
        
        <div class="tab-container">
            <div class="tab-nav">
                <button class="tab-btn active" data-tab="profile">Account Information</button>
                <button class="tab-btn" data-tab="password">Change Password</button>
            </div>
            
            <div id="profile-tab" class="tab-content active">
                <div class="profile-card">
                    <div class="profile-card-header">
                        <i class="fas fa-user icon"></i>
                        <h3>Personal Information</h3>
                    </div>
                    <div class="profile-card-body">
                        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" id="cropped_image" name="cropped_image">
                            <input type="file" id="profile_image" class="d-none" name="profile_image" accept="image/*">
                            
                            <div class="form-group">
                                <label for="full_name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="full_name" name="full_name" value="{{ Auth::user()->full_name }}">
                                @error('full_name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ Auth::user()->email }}">
                                @error('email')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <label for="mobile_number" class="form-label">Mobile Number</label>
                                <input type="text" class="form-control" id="mobile_number" name="mobile_number" value="{{ Auth::user()->mobile_number }}">
                                @error('mobile_number')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save btn-icon"></i> Save Changes
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div id="password-tab" class="tab-content">
                <div class="profile-card">
                    <div class="profile-card-header">
                        <i class="fas fa-key icon"></i>
                        <h3>Change Password</h3>
                    </div>
                    <div class="profile-card-body">
                        <form action="{{ route('password.update') }}" method="POST">
                            @csrf
                            
                            <div class="form-group">
                                <label for="current_password" class="form-label">Current Password</label>
                                <input type="password" class="form-control" id="current_password" name="current_password">
                                @error('current_password')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <label for="password" class="form-label">New Password</label>
                                <input type="password" class="form-control" id="password" name="password">
                                @error('password')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-key btn-icon"></i> Update Password
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Image Cropper Modal -->
<div class="modal fade" id="cropperModal" tabindex="-1" aria-labelledby="cropperModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="cropperModalLabel">Crop Profile Image</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="image-cropper-container">
                    <div id="cropperContainer">
                        <img id="cropperImage" src="" alt="Image to crop" style="max-width: 100%;">
                    </div>
                    <div class="mt-3">
                        <div class="preview"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="crop-btn">Apply & Save</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Tab functionality
        $('.tab-btn').click(function() {
            const tabId = $(this).data('tab');
            
            // Update active tab button
            $('.tab-btn').removeClass('active');
            $(this).addClass('active');
            
            // Show selected tab content
            $('.tab-content').removeClass('active');
            $(`#${tabId}-tab`).addClass('active');
        });
        
        // Image cropper functionality
        let cropper;
        
        // Initialize the image cropper when an image is selected
        $('#change-photo-btn').click(function() {
            $('#profile_image').click();
        });
        
        $('#profile_image').change(function(e) {
            if (e.target.files.length > 0) {
                const file = e.target.files[0];
                const reader = new FileReader();
                
                reader.onload = function(event) {
                    // Set the image source
                    $('#cropperImage').attr('src', event.target.result);
                    
                    // Show the cropper modal
                    $('#cropperModal').modal('show');
                    
                    // Initialize cropper after the modal is shown
                    $('#cropperModal').on('shown.bs.modal', function() {
                        if (cropper) {
                            cropper.destroy();
                        }
                        
                        cropper = new Cropper(document.getElementById('cropperImage'), {
                            aspectRatio: 1,
                            viewMode: 1,
                            preview: '.preview',
                            dragMode: 'move',
                            autoCropArea: 0.8,
                            responsive: true,
                            restore: false,
                            guides: true,
                            center: true,
                            highlight: false,
                            cropBoxMovable: true,
                            cropBoxResizable: true,
                            toggleDragModeOnDblclick: false
                        });
                    });
                };
                
                reader.readAsDataURL(file);
            }
        });
        
        // Crop and save the image
        $('#crop-btn').click(function() {
            if (!cropper) return;
            
            // Get the cropped canvas
            const canvas = cropper.getCroppedCanvas({
                width: 300,
                height: 300
            });
            
            if (canvas) {
                // Convert canvas to base64 string
                const croppedImageData = canvas.toDataURL('image/jpeg');
                
                // Set the cropped image to the preview
                $('#preview-image').attr('src', croppedImageData);
                
                // Set the cropped image data to the hidden input
                $('#cropped_image').val(croppedImageData);
                
                // Close the modal
                $('#cropperModal').modal('hide');
                
                // Reset cropper
                cropper.destroy();
                cropper = null;
            }
        });
    });
</script>
@endpush 