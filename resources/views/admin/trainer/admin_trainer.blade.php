@extends('layouts.admin')

@section('title', 'Trainer Management')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Cropper.js for image cropping -->
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

<div class="container mx-auto px-4 py-4">
    <div class="bg-[#111827] p-6 rounded-xl shadow-md mb-8 border border-[#374151]">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
            <h1 class="text-3xl font-bold text-white flex items-center">
                <i class="fas fa-dumbbell mr-3 text-[#9CA3AF]"></i>
                Trainer Management
            </h1>
            <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4 w-full md:w-auto">
                <div class="relative flex-grow md:flex-grow-0 md:w-64">
                    <input 
                        id="trainerSearch"
                        type="text" 
                        placeholder="Search trainers..." 
                        class="w-full pl-10 pr-4 py-3 bg-[#374151] border-2 border-[#4B5563] text-white rounded-lg focus:outline-none focus:border-[#9CA3AF] focus:ring-1 focus:ring-[#9CA3AF] transition-all duration-200 placeholder-gray-400"
                    >
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-[#9CA3AF]"></i>
                </div>
                
                <!-- Filter Dropdown -->
                <div class="relative flex-grow md:flex-grow-0 md:w-48">
                    <select id="filterDropdown" onchange="applyFilter(this.value)" class="w-full pl-4 pr-8 py-3 bg-[#374151] border-2 border-[#4B5563] text-white rounded-lg focus:outline-none focus:border-[#9CA3AF] focus:ring-1 focus:ring-[#9CA3AF] transition-all duration-200 appearance-none">
                        <option value="all" {{ $filter == 'all' || !$filter ? 'selected' : '' }}>All Active</option>
                        @foreach($sports as $sport)
                            <option value="{{ $sport->slug }}" {{ $filter == $sport->slug ? 'selected' : '' }}>{{ $sport->name }}</option>
                        @endforeach
                        <option value="archived" {{ $filter == 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-white">
                        <i class="fas fa-chevron-down"></i>
                    </div>
                </div>
                
                <button id="addTrainerBtn" class="bg-[#374151] hover:bg-[#4B5563] text-white py-2 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center">
                    <i class="fas fa-plus mr-2"></i>
                    Add Trainer
                </button>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <!-- Trainer Cards -->
        @forelse($trainers as $trainer)
        <div class="bg-[#1F2937] rounded-lg shadow-lg overflow-hidden transform transition-all duration-300 hover:shadow-xl hover:-translate-y-1 border border-[#374151]">
            <div class="relative">
                <img src="{{ $trainer->profile_image_url }}" alt="{{ $trainer->user->full_name }}" class="w-full h-48 object-cover transition-transform duration-300 hover:scale-105">
                <div class="absolute top-2 right-2">
                    <span class="px-3 py-1 text-sm font-medium rounded-full bg-[#374151] text-[#9CA3AF] shadow-sm">
                        @if($trainer->user->is_archived)
                            Archived
                        @else
                            {{-- Assuming instructor_for is passed as an array or collection --}}
                            {{ $trainer->instructor_for ? implode(', ', (array)$trainer->instructor_for) : 'Active' }}
                        @endif
                    </span>
                </div>
            </div>
            <div class="p-6">
                <h3 class="text-xl font-bold text-white mb-1 hover:text-[#9CA3AF] transition-colors duration-200">{{ $trainer->user->full_name }}</h3>
                <p class="text-[#9CA3AF] text-sm mb-3">{{ $trainer->specialization }}</p>
                <p class="text-xs text-gray-400 mb-1">Hired: {{ $trainer->hired_date ? date('M d, Y', strtotime($trainer->hired_date)) : 'N/A' }}</p>
                @if($trainer->user->is_archived && $trainer->resigned_date)
                    <p class="text-xs text-red-400 mb-1">Resigned: {{ date('M d, Y', strtotime($trainer->resigned_date)) }}</p>
                @elseif($trainer->user->is_archived)
                    <p class="text-xs text-red-400 mb-1">Archived</p>
                @endif
                <div class="flex items-center text-sm text-[#9CA3AF] mb-4">
                    <i class="fas fa-users mr-2"></i>
                    <span>{{ $trainer->instructed_clients_count }} Instructed Clients</span>
                </div>

                <!-- Weekly Schedule -->
                <div class="mb-4">
                    <h4 class="text-white text-sm font-semibold mb-2">Weekly Schedule</h4>
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        @foreach(['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $day)
                            <div class="text-[#9CA3AF]">
                                {{ $day }}: {{ $trainer->formatted_schedule[$day] ?? 'Off' }}
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="border-t border-[#374151] pt-4">
                    <div class="flex justify-between items-center space-x-4">
                        <button 
                            onclick="editTrainer({{ $trainer->id }})"
                            class="flex-1 bg-[#374151] hover:bg-[#4B5563] text-white py-2 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center">
                            <i class="fas fa-edit mr-2"></i>
                            Edit
                        </button>
                        <button 
                            onclick="confirmArchiveTrainer({{ $trainer->id }}, '{{ $trainer->user->full_name }}', {{ $trainer->user->is_archived ? 'true' : 'false' }})"
                            class="flex-1 bg-[#374151] hover:bg-[#4B5563] text-white py-2 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center">
                            <i class="fas fa-{{ $trainer->user->is_archived ? 'undo' : 'archive' }} mr-2"></i>
                            {{ $trainer->user->is_archived ? 'Unarchive' : 'Archive' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-8">
            <p class="text-[#9CA3AF] text-lg">No trainers found. Add your first trainer to get started.</p>
        </div>
        @endforelse
     </div>

    <!-- Pagination -->
    @if(count($trainers) > 0)
    <div class="mt-8 flex justify-center">
        <nav class="flex items-center space-x-2">
            <button class="px-3 py-1 rounded-lg bg-[#374151] text-[#9CA3AF] hover:bg-[#4B5563] transition-colors">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button class="px-3 py-1 rounded-lg bg-[#374151] text-white">1</button>
            <button class="px-3 py-1 rounded-lg bg-[#374151] text-[#9CA3AF] hover:bg-[#4B5563] transition-colors">2</button>
            <button class="px-3 py-1 rounded-lg bg-[#374151] text-[#9CA3AF] hover:bg-[#4B5563] transition-colors">3</button>
            <button class="px-3 py-1 rounded-lg bg-[#374151] text-[#9CA3AF] hover:bg-[#4B5563] transition-colors">
                <i class="fas fa-chevron-right"></i>
            </button>
        </nav>
    </div>
    @endif

    <!-- Add Trainer Modal -->
    <div id="addTrainerModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
        <div class="bg-[#1F2937] rounded-xl shadow-xl border border-[#374151] w-full max-w-3xl max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-[#374151]">
                <div class="flex justify-between items-center">
                    <h3 class="text-xl font-bold text-white">Add New Trainer</h3>
                    <button class="text-[#9CA3AF] hover:text-white" onclick="closeAddTrainerModal()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <form id="addTrainerForm" enctype="multipart/form-data" class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Basic Info -->
                    <div class="col-span-1">
                        <h4 class="text-white text-lg font-semibold mb-4 border-b border-[#374151] pb-2">Personal Information</h4>
                        
                        <div class="mb-4">
                            <label for="full_name" class="block text-[#9CA3AF] text-sm font-medium mb-2">Full Name <span class="text-red-500">*</span></label>
                            <input type="text" id="full_name" name="full_name" placeholder="Enter trainer's full name" 
                                class="w-full bg-[#374151] border border-[#4B5563] text-white rounded-lg p-3 focus:outline-none focus:border-[#9CA3AF]" 
                                required minlength="3" maxlength="50">
                            <p class="text-xs text-[#9CA3AF] mt-1">Required. Enter first and last name (3-50 characters).</p>
                        </div>
                        
                        <div class="mb-4">
                            <label for="email" class="block text-[#9CA3AF] text-sm font-medium mb-2">Email <span class="text-red-500">*</span></label>
                            <input type="email" id="email" name="email" placeholder="trainer@example.com" 
                                class="w-full bg-[#374151] border border-[#4B5563] text-white rounded-lg p-3 focus:outline-none focus:border-[#9CA3AF]" 
                                required>
                            <p class="text-xs text-[#9CA3AF] mt-1">Required. Enter a valid email address that the trainer can access.</p>
                        </div>
                        
                        <div class="mb-4">
                            <label for="password" class="block text-[#9CA3AF] text-sm font-medium mb-2">Password <span class="text-red-500">*</span></label>
                            <input type="password" id="password" name="password" placeholder="Create a secure password" 
                                class="w-full bg-[#374151] border border-[#4B5563] text-white rounded-lg p-3 focus:outline-none focus:border-[#9CA3AF]" 
                                required minlength="8">
                            <p class="text-xs text-[#9CA3AF] mt-1">Required. Minimum 8 characters with at least one uppercase letter, number, and special character.</p>
                        </div>
                        
                        <div class="mb-4">
                            <label for="mobile_number" class="block text-[#9CA3AF] text-sm font-medium mb-2">Mobile Number <span class="text-red-500">*</span></label>
                            <input type="text" id="mobile_number" name="mobile_number" placeholder="+63 917 123 4567" 
                                class="w-full bg-[#374151] border border-[#4B5563] text-white rounded-lg p-3 focus:outline-none focus:border-[#9CA3AF]" 
                                required
                                onfocus="if(this.value === '+63 ') { this.setSelectionRange(4, 4); }" 
                                onkeydown="if(event.key === 'Backspace' && this.value.length <= 4) { event.preventDefault(); }" 
                                onkeyup="if(!this.value.startsWith('+63 ')) { this.value = '+63 ' + this.value.substring(4); }">
                            <p class="text-xs text-[#9CA3AF] mt-1">Required. Enter a valid Philippine mobile number (e.g., +63 917 123 4567).</p>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-[#9CA3AF] text-sm font-medium mb-2">Gender <span class="text-red-500">*</span></label>
                            <div class="flex space-x-4">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="gender" value="male" class="text-blue-500 gender-radio" checked>
                                    <span class="ml-2 text-white">Male</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="gender" value="female" class="text-pink-500 gender-radio">
                                    <span class="ml-2 text-white">Female</span>
                                </label>
                            </div>
                            <p class="text-xs text-[#9CA3AF] mt-1">Required. Select the trainer's gender.</p>
                        </div>

                        <div class="mb-4">
                            <label for="hired_date" class="block text-[#9CA3AF] text-sm font-medium mb-2">Hired Date <span class="text-red-500">*</span></label>
                            <input type="date" id="hired_date" name="hired_date" 
                                class="w-full bg-[#374151] border border-[#4B5563] text-white rounded-lg p-3 focus:outline-none focus:border-[#9CA3AF]" 
                                required>
                            <p class="text-xs text-[#9CA3AF] mt-1">Required. Enter the date when the trainer was hired.</p>
                        </div>
                    </div>
                    
                    <!-- Professional Info -->
                    <div class="col-span-1">
                        <h4 class="text-white text-lg font-semibold mb-4 border-b border-[#374151] pb-2">Professional Information</h4>
                        
                        <div class="mb-4">
                            <label for="specialization" class="block text-[#9CA3AF] text-sm font-medium mb-2">Specialization <span class="text-red-500">*</span></label>
                            <input type="text" id="specialization" name="specialization" placeholder="e.g., Strength Training, Boxing Coach" 
                                class="w-full bg-[#374151] border border-[#4B5563] text-white rounded-lg p-3 focus:outline-none focus:border-[#9CA3AF]" 
                                required>
                            <p class="text-xs text-[#9CA3AF] mt-1">Required. Enter the trainer's area of expertise or specialization.</p>
                        </div>
                        
                        <div class="mb-4">
                            <label for="instructor_for" class="block text-[#9CA3AF] text-sm font-medium mb-2">Instructor For <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <select id="instructor_for" name="instructor_for_select[]" multiple 
                                    class="w-full bg-[#374151] border border-[#4B5563] text-white rounded-lg p-3 focus:outline-none focus:border-[#9CA3AF] min-h-[120px]"
                                    required>
                                    @foreach($sports as $sport)
                                        <option value="{{ $sport->slug }}" class="p-2 my-1 hover:bg-[#4B5563] cursor-pointer">{{ $sport->name }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute right-3 top-3 text-[#9CA3AF] text-xs bg-[#1F2937] p-1 rounded">
                                    <span class="selected-count">0</span> selected
                                </div>
                            </div>
                            <input type="hidden" name="instructor_for" id="instructor_for_hidden">
                            <div class="flex flex-wrap gap-2 mt-2 selected-sports-display"></div>
                            <p class="text-xs text-[#9CA3AF] mt-1">Required. Select at least one area where this trainer will provide instruction. Hold Ctrl/Cmd key to select multiple options.</p>
                        </div>
                        
                        <div class="mb-4">
                            <label for="short_intro" class="block text-[#9CA3AF] text-sm font-medium mb-2">Bio</label>
                            <textarea id="short_intro" name="short_intro" rows="3" 
                                placeholder="Brief description of trainer's experience and background" 
                                class="w-full bg-[#374151] border border-[#4B5563] text-white rounded-lg p-3 focus:outline-none focus:border-[#9CA3AF]"></textarea>
                            <p class="text-xs text-[#9CA3AF] mt-1">Optional. A short description of the trainer's experience and background (max 500 characters).</p>
                        </div>
                        
                        <div class="mb-4">
                            <label for="profile_image" class="block text-[#9CA3AF] text-sm font-medium mb-2">Profile Image <span class="text-red-500">*</span></label>
                            <div class="flex flex-col space-y-2">
                                <div class="flex items-center justify-center w-full">
                                    <label for="profile_image" class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed rounded-lg cursor-pointer bg-[#374151] border-[#4B5563] hover:bg-[#424B5D]">
                                        <div class="flex flex-col items-center justify-center pt-5 pb-6" id="uploadPlaceholder">
                                            <i class="fas fa-cloud-upload-alt text-2xl text-[#9CA3AF] mb-2"></i>
                                            <p class="mb-2 text-sm text-[#9CA3AF]">Click to upload or drag and drop</p>
                                            <p class="text-xs text-[#9CA3AF]">PNG, JPG or JPEG (MAX. 5MB)</p>
                                        </div>
                                        <div id="imagePreviewContainer" class="hidden w-full h-full flex items-center justify-center">
                                            <img id="imagePreview" class="max-h-28 max-w-full object-contain" src="#" alt="Preview">
                                        </div>
                                        <input id="profile_image" name="profile_image" type="file" accept="image/png, image/jpeg, image/jpg" class="hidden" required />
                                    </label>
                                </div>
                                <span id="selectedFileName" class="text-xs text-[#9CA3AF]"></span>
                                <p class="text-xs text-[#9CA3AF]">PNG, JPG or JPEG (MAX. 5MB)</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Weekly Schedule - Full Width -->
                    <div class="col-span-1 md:col-span-2">
                        <h4 class="text-white text-lg font-semibold mb-4 border-b border-[#374151] pb-2">Weekly Schedule</h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                            <div class="mb-4">
                                <div class="flex justify-between items-center mb-2">
                                    <label class="text-[#9CA3AF] text-sm font-medium">{{ $day }}</label>
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" class="day-toggle" data-day="{{ $day }}" checked>
                                        <span class="ml-2 text-white text-sm">Available</span>
                                    </label>
                                </div>
                                <div class="flex space-x-2 day-time-container" data-day="{{ $day }}">
                                    <div class="flex-1">
                                        <label class="block text-[#9CA3AF] text-xs mb-1">Start Time</label>
                                        <input type="time" name="schedule[{{ $day }}][start]" class="w-full bg-[#374151] border border-[#4B5563] text-white rounded-lg p-2 focus:outline-none focus:border-[#9CA3AF] text-sm" value="09:00">
                                    </div>
                                    <div class="flex-1">
                                        <label class="block text-[#9CA3AF] text-xs mb-1">End Time</label>
                                        <input type="time" name="schedule[{{ $day }}][end]" class="w-full bg-[#374151] border border-[#4B5563] text-white rounded-lg p-2 focus:outline-none focus:border-[#9CA3AF] text-sm" value="17:00">
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                
                <div class="mt-6 flex justify-end space-x-3 border-t border-[#374151] pt-6">
                    <button type="button" onclick="closeAddTrainerModal()" class="px-5 py-2 bg-[#4B5563] text-white rounded-lg hover:bg-[#6B7280] transition-colors duration-200">
                        Cancel
                    </button>
                    <button type="button" onclick="submitAddTrainerForm()" class="px-5 py-2 bg-[#3B82F6] text-white rounded-lg hover:bg-[#2563EB] transition-colors duration-200">
                        Save Trainer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Trainer Modal -->
    <div id="editTrainerModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
        <div class="bg-[#1F2937] rounded-xl shadow-xl border border-[#374151] w-full max-w-3xl max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-[#374151]">
                <div class="flex justify-between items-center">
                    <h3 class="text-xl font-bold text-white">Edit Trainer</h3>
                    <button class="text-[#9CA3AF] hover:text-white" onclick="closeEditTrainerModal()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <form id="editTrainerForm" enctype="multipart/form-data" class="p-6">
                <input type="hidden" id="edit_trainer_id" name="trainer_id">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Basic Info -->
                    <div class="col-span-1">
                        <h4 class="text-white text-lg font-semibold mb-4 border-b border-[#374151] pb-2">Personal Information</h4>
                        
                        <div class="mb-4">
                            <label for="edit_full_name" class="block text-[#9CA3AF] text-sm font-medium mb-2">Full Name *</label>
                            <input type="text" id="edit_full_name" name="full_name" class="w-full bg-[#374151] border border-[#4B5563] text-white rounded-lg p-3 focus:outline-none focus:border-[#9CA3AF]">
                        </div>
                        
                        <div class="mb-4">
                            <label for="edit_email" class="block text-[#9CA3AF] text-sm font-medium mb-2">Email *</label>
                            <input type="email" id="edit_email" name="email" class="w-full bg-[#374151] border border-[#4B5563] text-white rounded-lg p-3 focus:outline-none focus:border-[#9CA3AF]">
                        </div>
                        
                        <div class="mb-4">
                            <label for="edit_mobile_number" class="block text-[#9CA3AF] text-sm font-medium mb-2">Mobile Number *</label>
                            <input type="text" id="edit_mobile_number" name="mobile_number" placeholder="+63 917 123 4567" 
                                class="w-full bg-[#374151] border border-[#4B5563] text-white rounded-lg p-3 focus:outline-none focus:border-[#9CA3AF]" 
                                onfocus="if(!this.value.startsWith('+63 ')) { this.value = '+63 ' + this.value.replace(/^\+63\s*/, ''); this.setSelectionRange(4, this.value.length); }" 
                                onkeydown="if(event.key === 'Backspace' && this.value.length <= 4) { event.preventDefault(); }" 
                                onkeyup="if(!this.value.startsWith('+63 ')) { this.value = '+63 ' + this.value.substring(4); }">
                        </div>
                        
                        <div class="mb-4">
                            <label for="edit_gender" class="block text-[#9CA3AF] text-sm font-medium mb-2">Gender <span class="text-red-500">*</span></label>
                            <select id="edit_gender" name="gender" class="w-full bg-[#374151] border border-[#4B5563] text-white rounded-lg p-3 focus:outline-none focus:border-[#9CA3AF]" required>
                                <option value="">Select Gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                            <p class="text-xs text-[#9CA3AF] mt-1">Required. Select the trainer's gender.</p>
                        </div>
                        
                        <div class="mb-4">
                            <label for="edit_hired_date" class="block text-[#9CA3AF] text-sm font-medium mb-2">Hired Date <span class="text-red-500">*</span></label>
                            <input type="date" id="edit_hired_date" name="hired_date" 
                                class="w-full bg-[#374151] border border-[#4B5563] text-white rounded-lg p-3 focus:outline-none focus:border-[#9CA3AF]" 
                                required>
                            <p class="text-xs text-[#9CA3AF] mt-1">Required. Enter the date when the trainer was hired.</p>
                        </div>
                        
                        <div class="mb-4">
                            <label for="edit_resigned_date" class="block text-[#9CA3AF] text-sm font-medium mb-2">Resigned Date</label>
                            <input type="date" id="edit_resigned_date" name="resigned_date" 
                                class="w-full bg-[#374151] border border-[#4B5563] text-white rounded-lg p-3 focus:outline-none focus:border-[#9CA3AF]">
                            <p class="text-xs text-[#9CA3AF] mt-1">Optional. Enter the date when the trainer resigned (automatically set when archived).</p>
                        </div>
                    </div>
                    
                    <!-- Professional Info -->
                    <div class="col-span-1">
                        <h4 class="text-white text-lg font-semibold mb-4 border-b border-[#374151] pb-2">Professional Information</h4>
                        
                        <div class="mb-4">
                            <label for="edit_specialization" class="block text-[#9CA3AF] text-sm font-medium mb-2">Specialization <span class="text-red-500">*</span></label>
                            <input type="text" id="edit_specialization" name="specialization" placeholder="e.g., Strength Training, Boxing Coach"
                                class="w-full bg-[#374151] border border-[#4B5563] text-white rounded-lg p-3 focus:outline-none focus:border-[#9CA3AF]"
                                required>
                            <p class="text-xs text-[#9CA3AF] mt-1">Required. Enter the trainer's area of expertise or specialization.</p>
                        </div>
                        
                        <div class="mb-4">
                            <label for="edit_instructor_for" class="block text-[#9CA3AF] text-sm font-medium mb-2">Instructor For <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <select id="edit_instructor_for" name="edit_instructor_for_select[]" multiple 
                                    class="w-full bg-[#374151] border border-[#4B5563] text-white rounded-lg p-3 focus:outline-none focus:border-[#9CA3AF] min-h-[120px]"
                                    required>
                                    @foreach($sports as $sport)
                                        <option value="{{ $sport->slug }}" class="p-2 my-1 hover:bg-[#4B5563] cursor-pointer">{{ $sport->name }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute right-3 top-3 text-[#9CA3AF] text-xs bg-[#1F2937] p-1 rounded">
                                    <span class="edit-selected-count">0</span> selected
                                </div>
                            </div>
                            <input type="hidden" name="instructor_for" id="edit_instructor_for_hidden">
                            <div class="flex flex-wrap gap-2 mt-2 edit-selected-sports-display"></div>
                            <p class="text-xs text-[#9CA3AF] mt-1">Required. Select at least one area where this trainer will provide instruction. Hold Ctrl/Cmd key to select multiple options.</p>
                        </div>
                        
                        <div class="mb-4">
                            <label for="edit_short_intro" class="block text-[#9CA3AF] text-sm font-medium mb-2">Bio</label>
                            <textarea id="edit_short_intro" name="short_intro" rows="3" class="w-full bg-[#374151] border border-[#4B5563] text-white rounded-lg p-3 focus:outline-none focus:border-[#9CA3AF]"></textarea>
                        </div>
                        
                        <div class="mb-4">
                            <label for="edit_profile_image" class="block text-[#9CA3AF] text-sm font-medium mb-2">Profile Image (leave blank to keep current)</label>
                            <div class="mb-2">
                                <div id="currentImageContainer" class="w-full h-40 border border-[#4B5563] rounded-md flex items-center justify-center mb-2">
                                    <img id="currentImage" class="max-h-full max-w-full object-contain rounded-md" src="" alt="Current profile image">
                                    <div id="noImagePlaceholder" class="flex items-center justify-center w-40 h-40 bg-[#374151] rounded-md hidden">
                                        <i class="fas fa-user text-[#9CA3AF] text-4xl"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-col space-y-2">
                                <div class="flex items-center justify-center w-full">
                                    <label for="edit_profile_image" class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed rounded-lg cursor-pointer bg-[#374151] border-[#4B5563] hover:bg-[#424B5D]">
                                        <div class="flex flex-col items-center justify-center pt-5 pb-6" id="editUploadPlaceholder">
                                            <i class="fas fa-cloud-upload-alt text-2xl text-[#9CA3AF] mb-2"></i>
                                            <p class="mb-2 text-sm text-[#9CA3AF]">Click to upload or drag and drop</p>
                                            <p class="text-xs text-[#9CA3AF]">PNG, JPG or JPEG (MAX. 5MB)</p>
                                        </div>
                                        <div id="editImagePreviewContainer" class="hidden w-full h-full flex items-center justify-center">
                                            <img id="editImagePreview" class="max-h-28 max-w-full object-contain" src="#" alt="Preview">
                                        </div>
                                        <input id="edit_profile_image" name="profile_image" type="file" accept="image/png, image/jpeg, image/jpg" class="hidden" />
                                    </label>
                                </div>
                                <span id="editSelectedFileName" class="text-xs text-[#9CA3AF]"></span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Weekly Schedule - Full Width -->
                    <div class="col-span-1 md:col-span-2">
                        <h4 class="text-white text-lg font-semibold mb-4 border-b border-[#374151] pb-2">Weekly Schedule</h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                            <div class="mb-4">
                                <div class="flex justify-between items-center mb-2">
                                    <label class="text-[#9CA3AF] text-sm font-medium">{{ $day }}</label>
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" class="edit-day-toggle" data-day="{{ $day }}" checked>
                                        <span class="ml-2 text-white text-sm">Available</span>
                                    </label>
                                </div>
                                <div class="flex space-x-2 edit-day-time-container" data-day="{{ $day }}">
                                    <div class="flex-1">
                                        <label class="block text-[#9CA3AF] text-xs mb-1">Start Time</label>
                                        <input type="time" name="schedule[{{ $day }}][start]" class="edit-time-start w-full bg-[#374151] border border-[#4B5563] text-white rounded-lg p-2 focus:outline-none focus:border-[#9CA3AF] text-sm" value="09:00">
                                    </div>
                                    <div class="flex-1">
                                        <label class="block text-[#9CA3AF] text-xs mb-1">End Time</label>
                                        <input type="time" name="schedule[{{ $day }}][end]" class="edit-time-end w-full bg-[#374151] border border-[#4B5563] text-white rounded-lg p-2 focus:outline-none focus:border-[#9CA3AF] text-sm" value="17:00">
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                
                <div class="mt-6 flex justify-end space-x-3 border-t border-[#374151] pt-6">
                    <button type="button" onclick="closeEditTrainerModal()" class="px-5 py-2 bg-[#4B5563] text-white rounded-lg hover:bg-[#6B7280] transition-colors duration-200">
                        Cancel
                    </button>
                    <button type="submit" class="px-5 py-2 bg-[#3B82F6] text-white rounded-lg hover:bg-[#2563EB] transition-colors duration-200">
                        Update Trainer
                    </button>
                </div>
            </form>
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
                <button type="button" id="cancelCrop" class="px-5 py-2 bg-[#4B5563] text-white rounded-lg hover:bg-[#6B7280] transition-colors">
                    Cancel
                </button>
                <button type="button" id="applyCrop" class="px-5 py-2 bg-[#3B82F6] text-white rounded-lg hover:bg-[#2563EB] transition-colors">
                    Apply Crop
                </button>
            </div>
        </div>
    </div>
</div>

<script>
console.log('Admin Trainer Script Loaded - ' + new Date().toISOString());

// Modal controls
function openAddTrainerModal() {
    document.getElementById('addTrainerModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    // Reset the form
    document.getElementById('addTrainerForm').reset();
    
    // Reset the instructor_for hidden field
    document.getElementById('instructor_for_hidden').value = '';
    
    // Reset image preview if it exists
    const imagePreviewContainer = document.getElementById('imagePreviewContainer');
    const uploadPlaceholder = document.getElementById('uploadPlaceholder');
    if (imagePreviewContainer && uploadPlaceholder) {
        imagePreviewContainer.classList.add('hidden');
        uploadPlaceholder.classList.remove('hidden');
    }
    
    // Initialize mobile number field with +63 prefix
    const mobileInput = document.getElementById('mobile_number');
    if (mobileInput) {
        mobileInput.value = '+63 ';
    }
    
    // Set default hired date to today
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('hired_date').value = today;
}

function closeAddTrainerModal() {
    document.getElementById('addTrainerModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function openEditTrainerModal() {
    document.getElementById('editTrainerModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeEditTrainerModal() {
    document.getElementById('editTrainerModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Function to handle filter changes
function applyFilter(filterValue) {
    window.location.href = "{{ route('admin.trainer.admin_trainer') }}" + "?filter=" + filterValue;
}

// Confirmation dialog using SweetAlert
function confirmArchiveTrainer(trainerId, trainerName, isArchived) {
    const action = isArchived ? 'unarchive' : 'archive';
    
    Swal.fire({
        title: `${action.charAt(0).toUpperCase() + action.slice(1)} Trainer?`,
        html: `Are you sure you want to ${action} <strong>${trainerName}</strong>?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: isArchived ? '#3085d6' : '#d33',
        cancelButtonColor: '#6B7280',
        confirmButtonText: `Yes, ${action} trainer!`,
        cancelButtonText: 'Cancel',
        background: '#1F2937',
        color: '#ffffff',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            archiveTrainer(trainerId);
        }
    });
}

// Archive/Unarchive a trainer
function archiveTrainer(trainerId) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    
    // Create form data or JSON payload
    const formData = new FormData();
    formData.append('_token', csrfToken);
    
    fetch(`/admin/trainers/${trainerId}/archive`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken
        },
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            Swal.fire({
                title: 'Success!',
                text: data.message,
                icon: 'success',
                background: '#1F2937',
                color: '#ffffff',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                // Reload the page to show the updated status
                window.location.reload();
            });
        } else {
            Swal.fire({
                title: 'Error!',
                text: data.message || 'Failed to update trainer status.',
                icon: 'error',
                background: '#1F2937',
                color: '#ffffff'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            title: 'Error!',
            text: 'An unexpected error occurred. Please try again.',
            icon: 'error',
            background: '#1F2937',
            color: '#ffffff'
        });
    });
}

// Function to handle add trainer form submission
function submitAddTrainerForm() {
    console.log('Submit Add Trainer Form function called');
    
    // Get the form
    const form = document.getElementById('addTrainerForm');
    
    // Validate instructor_for field
    const instructorSelect = document.getElementById('instructor_for');
    const instructorHidden = document.getElementById('instructor_for_hidden');
    const selectedValues = Array.from(instructorSelect.selectedOptions).map(opt => opt.value);
    
    console.log('Selected instructor values:', selectedValues);
    
    if (selectedValues.length === 0) {
        Swal.fire({
            title: 'Missing Information!',
            text: 'Please select at least one instruction area (Gym, Boxing, etc.)',
            icon: 'error',
            background: '#1F2937',
            color: '#ffffff'
        });
        return;
    }
    
    // Update the hidden field with selected values
    instructorHidden.value = selectedValues.join(',');
    console.log('instructor_for hidden value updated to:', instructorHidden.value);
    
    // Create FormData from the form
    const formData = new FormData(form);
    
    // Ensure instructor_for is set in the formData
    formData.set('instructor_for', instructorHidden.value);
    
    // Handle cropped image if present
    const croppedInput = document.querySelector('input[name="profile_image_cropped"]');
    if (croppedInput && croppedInput.value) {
        formData.delete('profile_image');
        formData.set('profile_image_base64', croppedInput.value);
    }
    
    // Handle day toggles for schedule
    document.querySelectorAll('.day-toggle').forEach(toggle => {
        const day = toggle.getAttribute('data-day');
        if (!toggle.checked) {
            formData.delete(`schedule[${day}][start]`);
            formData.delete(`schedule[${day}][end]`);
        }
    });
    
    // Add other gender if selected
    if (form.querySelector('input[name="gender"]:checked').value === 'other') {
        const otherGenderValue = form.querySelector('input[name="other_gender"]').value;
        formData.set('other_gender', otherGenderValue);
    }
    
    // Show loading state
    Swal.fire({
        title: 'Adding trainer...',
        text: 'Please wait while we process your request.',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        },
        background: '#1F2937',
        color: '#ffffff'
    });
    
    // Log all form data entries for debugging
    console.log('Form data entries:');
    for (const pair of formData.entries()) {
        console.log(pair[0] + ': ' + (pair[0] === 'profile_image_base64' ? '[Base64 data]' : pair[1]));
    }
    
    // Send the request
    fetch('{{ route('admin.trainers.store') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: formData
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        if (data.success) {
            Swal.fire({
                title: 'Success!',
                text: data.message,
                icon: 'success',
                background: '#1F2937',
                color: '#ffffff'
            }).then(() => {
                window.location.reload();
            });
        } else {
            let errorMsg = data.message || 'Failed to add trainer';
            let errorHtml = '<p>' + errorMsg + '</p>';
            
            if (data.errors) {
                errorHtml += '<div class="mt-4 text-left">';
                for (const [key, messages] of Object.entries(data.errors)) {
                    errorHtml += `<p class="text-red-400 font-semibold">${key}:</p>`;
                    errorHtml += '<ul class="list-disc pl-5 mb-2">';
                    messages.forEach(message => {
                        errorHtml += `<li class="text-sm">${message}</li>`;
                    });
                    errorHtml += '</ul>';
                }
                errorHtml += '</div>';
            }
            
            Swal.fire({
                title: 'Error!',
                html: errorHtml,
                icon: 'error',
                background: '#1F2937',
                color: '#ffffff'
            });
            
            console.error('Form submission errors:', data.errors);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            title: 'Error!',
            text: 'An unexpected error occurred. Please check console for details.',
            icon: 'error',
            background: '#1F2937',
            color: '#ffffff'
        });
    });
}

// Fetch trainer data for editing
function editTrainer(trainerId) {
    fetch(`/admin/trainers/${trainerId}/edit`)
        .then(response => response.json())
        .then(data => {
            // Populate the edit form with trainer data
            document.getElementById('edit_trainer_id').value = data.id;
            document.getElementById('edit_full_name').value = data.user.full_name;
            document.getElementById('edit_email').value = data.user.email;
            document.getElementById('edit_mobile_number').value = data.user.mobile_number || '';
            
            // Set gender in the dropdown
            const genderSelect = document.getElementById('edit_gender');
            genderSelect.value = data.user.gender || '';
            
            // Set hired and resigned dates
            if (data.hired_date) {
                document.getElementById('edit_hired_date').value = data.hired_date;
            } else {
                document.getElementById('edit_hired_date').value = new Date().toISOString().split('T')[0]; // Today as default
            }
            
            if (data.resigned_date) {
                document.getElementById('edit_resigned_date').value = data.resigned_date;
            } else {
                document.getElementById('edit_resigned_date').value = '';
            }
            
            // Professional info
            document.getElementById('edit_specialization').value = data.specialization || '';
            
            // Set profile image if available
            const currentImage = document.getElementById('currentImage');
            const noImagePlaceholder = document.getElementById('noImagePlaceholder');
            
            if (data.profile_url) {
                // Make sure we have the full URL by checking for http or https
                if (data.profile_url.startsWith('data:image') || data.profile_url.startsWith('http')) {
                    currentImage.src = data.profile_url;
                } else {
                    // It's a relative file path - prepend with site base URL
                    currentImage.src = '/' + data.profile_url;
                }
                currentImage.classList.remove('hidden');
                noImagePlaceholder.classList.add('hidden');
            } else {
                currentImage.classList.add('hidden');
                noImagePlaceholder.classList.remove('hidden');
            }
            
            // Handle instructor_for multiselect
            const editInstructorSelect = document.getElementById('edit_instructor_for');
            const editInstructorHidden = document.getElementById('edit_instructor_for_hidden');
            
            // Clear previous selections
            for (let i = 0; i < editInstructorSelect.options.length; i++) {
                editInstructorSelect.options[i].selected = false;
            }
            
            // Set new selections based on instructor_for string
            const instructorFor = data.instructor_for ? data.instructor_for.split(',') : [];
            
            for (let i = 0; i < editInstructorSelect.options.length; i++) {
                const option = editInstructorSelect.options[i];
                if (instructorFor.includes(option.value)) {
                    option.selected = true;
                }
            }
            
            // Update hidden field
            editInstructorHidden.value = instructorFor.join(',');
            
            // Set schedules
            const schedules = {};
            data.schedules.forEach(schedule => {
                schedules[schedule.day_of_week] = {
                    start: schedule.start_time.substring(0, 5), // HH:MM format
                    end: schedule.end_time.substring(0, 5)
                };
            });
            
            // Update schedule fields
            ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'].forEach(day => {
                const dayContainer = document.querySelector(`.edit-day-time-container[data-day="${day}"]`);
                const dayToggle = document.querySelector(`.edit-day-toggle[data-day="${day}"]`);
                
                if (schedules[day]) {
                    dayToggle.checked = true;
                    dayContainer.style.display = 'flex';
                    
                    const startInput = dayContainer.querySelector('.edit-time-start');
                    const endInput = dayContainer.querySelector('.edit-time-end');
                    
                    startInput.value = schedules[day].start;
                    endInput.value = schedules[day].end;
                } else {
                    dayToggle.checked = false;
                    dayContainer.style.display = 'none';
                }
            });
            
            // Trigger event for mobile number initialization
            const event = new Event('editTrainerModalOpened');
            document.dispatchEvent(event);
            
            // Open the modal
            openEditTrainerModal();
        })
        .catch(error => {
            console.error('Error fetching trainer data:', error);
            Swal.fire({
                title: 'Error!',
                text: 'Failed to load trainer data. Please try again.',
                icon: 'error',
                background: '#1F2937',
                color: '#ffffff'
            });
        });
}

// Handle search functionality
document.addEventListener('DOMContentLoaded', function() {
    // Search input handling
    const searchInput = document.getElementById('trainerSearch');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const trainerCards = document.querySelectorAll('.grid > div.bg-[#1F2937]');
            
            trainerCards.forEach(card => {
                const trainerName = card.querySelector('h3').innerText.toLowerCase();
                const specialization = card.querySelector('p.text-[#9CA3AF]').innerText.toLowerCase();
                
                if (trainerName.includes(searchTerm) || specialization.includes(searchTerm)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    }

    // Gender field toggle handling
    const genderRadios = document.querySelectorAll('.gender-radio');
    const otherGenderField = document.getElementById('otherGenderField');
    
    genderRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'other' && this.checked) {
                otherGenderField.classList.remove('hidden');
            } else {
                otherGenderField.classList.add('hidden');
            }
        });
    });
    
    // Edit form gender toggle
    const editGenderSelect = document.getElementById('edit_gender');
    const editOtherGenderField = document.getElementById('editOtherGenderField');
    
    if (editGenderSelect) {
        editGenderSelect.addEventListener('change', function() {
            if (this.value === 'other') {
                editOtherGenderField.classList.remove('hidden');
            } else {
                editOtherGenderField.classList.add('hidden');
            }
        });
    }

    // Cropper.js setup
    let cropper;
    const cropperModal = document.getElementById('cropperModal');
    const cropperImage = document.getElementById('cropperImage');
    const closeCropperModal = document.getElementById('closeCropperModal');
    const cancelCrop = document.getElementById('cancelCrop');
    const applyCrop = document.getElementById('applyCrop');
    
    // Variables to store the active input and preview elements
    let activeImageInput;
    let activeImagePreview;
    let activeImagePreviewContainer;
    let activeUploadPlaceholder;
    let activeCroppedImageData;
    
    // Profile image input handling for add form
    const profileImageInput = document.getElementById('profile_image');
    const imagePreview = document.getElementById('imagePreview');
    const imagePreviewContainer = document.getElementById('imagePreviewContainer');
    const uploadPlaceholder = document.getElementById('uploadPlaceholder');
    
    if (profileImageInput) {
        profileImageInput.addEventListener('change', function(e) {
            handleImageSelection(e, this, imagePreview, imagePreviewContainer, uploadPlaceholder);
        });
    }
    
    // Profile image input handling for edit form
    const editProfileImageInput = document.getElementById('edit_profile_image');
    const editImagePreview = document.getElementById('editImagePreview');
    const editImagePreviewContainer = document.getElementById('editImagePreviewContainer');
    const editUploadPlaceholder = document.getElementById('editUploadPlaceholder');
    
    if (editProfileImageInput) {
        editProfileImageInput.addEventListener('change', function(e) {
            handleImageSelection(e, this, editImagePreview, editImagePreviewContainer, editUploadPlaceholder);
        });
    }
    
    function handleImageSelection(e, inputElement, previewElement, previewContainer, placeholderElement) {
        const file = e.target.files[0];
        if (file) {
            const allowedTypes = ['image/png', 'image/jpeg', 'image/jpg'];
            if (!allowedTypes.includes(file.type)) {
                alert('Please upload only PNG, JPG or JPEG files.');
                inputElement.value = '';
                return;
            }
            if (file.size > 5 * 1024 * 1024) {
                alert('File size should not exceed 5MB');
                inputElement.value = '';
                return;
            }
            // Store references to active elements
            activeImageInput = inputElement;
            activeImagePreview = previewElement;
            activeImagePreviewContainer = previewContainer;
            activeUploadPlaceholder = placeholderElement;
            
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
    }
    
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
        if (activeImageInput) {
            activeImageInput.value = '';
        }
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
        
        // Store the cropped image data for form submission
        activeCroppedImageData = croppedImageData;
        
        // Update the preview
        activeImagePreview.src = croppedImageData;
        activeUploadPlaceholder.classList.add('hidden');
        activeImagePreviewContainer.classList.remove('hidden');
        
        // Create a hidden input to store the cropped image data
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = activeImageInput.name + '_cropped';
        hiddenInput.value = croppedImageData;
        
        // Remove any existing hidden input for this field
        const existingHiddenInput = document.querySelector(`input[name="${activeImageInput.name}_cropped"]`);
        if (existingHiddenInput) {
            existingHiddenInput.remove();
        }
        
        // Add the hidden input to the form
        activeImageInput.parentNode.appendChild(hiddenInput);
        
        // Close the cropper dialog
        closeCropperDialog();
    });

    // Add Trainer button event
    const addTrainerBtn = document.getElementById('addTrainerBtn');
    if (addTrainerBtn) {
        addTrainerBtn.addEventListener('click', function() {
            openAddTrainerModal();
        });
    }
    
    // Save Trainer button direct click handler
    const saveTrainerBtn = document.getElementById('saveTrainerBtn');
    if (saveTrainerBtn) {
        saveTrainerBtn.addEventListener('click', function(e) {
            console.log('Save Trainer button clicked directly');
            // Form will still trigger its own submit event
        });
    }
    
    // Form submission handlers - Add validation for mobile number and email
    const addTrainerForm = document.getElementById('addTrainerForm');
    if (addTrainerForm) {
        addTrainerForm.addEventListener('submit', function(e) {
            console.log('Add Trainer Form Submit Event Triggered');
            e.preventDefault();
            
            // 1. Validate instructor_for first - most common issue
            const instructorSelect = document.getElementById('instructor_for');
            const instructorHidden = document.getElementById('instructor_for_hidden');
            const selectedValues = Array.from(instructorSelect.selectedOptions).map(opt => opt.value);
            
            console.log('Selected instructor values:', selectedValues);
            
            if (selectedValues.length === 0) {
                Swal.fire({
                    title: 'Missing Information!',
                    text: 'Please select at least one instruction area (Gym, Boxing, etc.)',
                    icon: 'error',
                    background: '#1F2937',
                    color: '#ffffff'
                });
                return;
            }
            
            // 2. Update the hidden field with the selected values
            instructorHidden.value = selectedValues.join(',');
            console.log('instructor_for values:', selectedValues, 'hidden value:', instructorHidden.value);
            
            // 3. Create form data and explicitly set instructor_for
            const form = this;
            const formData = new FormData(form);
            formData.set('instructor_for', instructorHidden.value);
            
            // Debug logging for the file
            const fileInput = document.getElementById('profile_image');
            if (fileInput.files.length > 0) {
                console.log('File selected:', fileInput.files[0].name);
                console.log('File size:', fileInput.files[0].size);
            } else {
                console.log('No file selected');
            }
            
            // If we have cropped image data, use it instead of the file
            const croppedInput = document.querySelector('input[name="profile_image_cropped"]');
            if (croppedInput && croppedInput.value) {
                // Remove the original file input from the formData
                formData.delete('profile_image');
                
                // Add the cropped image data
                formData.set('profile_image_base64', croppedInput.value);
            }
            
            // Log form data entries for debugging
            console.log('Form data entries:');
            for (const pair of formData.entries()) {
                console.log(pair[0] + ': ' + (pair[0] === 'profile_image_base64' ? '[Base64 data]' : pair[1]));
            }
            
            // Handle day toggles for schedule
            document.querySelectorAll('.day-toggle').forEach(toggle => {
                const day = toggle.getAttribute('data-day');
                if (!toggle.checked) {
                    // Remove this day's schedule from formData
                    formData.delete(`schedule[${day}][start]`);
                    formData.delete(`schedule[${day}][end]`);
                }
            });
            
            // Add other gender if selected
            if (form.querySelector('input[name="gender"]:checked').value === 'other') {
                const otherGenderValue = form.querySelector('input[name="other_gender"]').value;
                formData.set('other_gender', otherGenderValue);
            }
            
            // Show loading state
            Swal.fire({
                title: 'Adding trainer...',
                text: 'Please wait while we process your request.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                },
                background: '#1F2937',
                color: '#ffffff'
            });
            
            fetch('{{ route('admin.trainers.store') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData
            })
            .then(response => {
                console.log('Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                if (data.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: data.message,
                        icon: 'success',
                        background: '#1F2937',
                        color: '#ffffff'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    let errorMsg = data.message || 'Failed to add trainer';
                    let errorHtml = '<p>' + errorMsg + '</p>';
                    
                    if (data.errors) {
                        errorHtml += '<div class="mt-4 text-left">';
                        for (const [key, messages] of Object.entries(data.errors)) {
                            errorHtml += `<p class="text-red-400 font-semibold">${key}:</p>`;
                            errorHtml += '<ul class="list-disc pl-5 mb-2">';
                            messages.forEach(message => {
                                errorHtml += `<li class="text-sm">${message}</li>`;
                            });
                            errorHtml += '</ul>';
                        }
                        errorHtml += '</div>';
                    }
                    
                    Swal.fire({
                        title: 'Error!',
                        html: errorHtml,
                        icon: 'error',
                        background: '#1F2937',
                        color: '#ffffff'
                    });
                    
                    console.error('Form submission errors:', data.errors);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'An unexpected error occurred. Please check console for details.',
                    icon: 'error',
                    background: '#1F2937',
                    color: '#ffffff'
                });
            });
        });
    }
    
    // Edit Trainer Form
    const editTrainerForm = document.getElementById('editTrainerForm');
    if (editTrainerForm) {
        editTrainerForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // 1. Validate instructor_for first - most common issue
            const editInstructorSelect = document.getElementById('edit_instructor_for');
            const editInstructorHidden = document.getElementById('edit_instructor_for_hidden');
            const selectedValues = Array.from(editInstructorSelect.selectedOptions).map(opt => opt.value);
            
            if (selectedValues.length === 0) {
                Swal.fire({
                    title: 'Missing Information!',
                    text: 'Please select at least one instruction area (Gym, Boxing, etc.)',
                    icon: 'error',
                    background: '#1F2937',
                    color: '#ffffff'
                });
                return;
            }
            
            // 2. Update the hidden field with the selected values
            editInstructorHidden.value = selectedValues.join(',');
            console.log('Edit form - instructor_for values:', selectedValues, 'hidden value:', editInstructorHidden.value);
            
            // 3. Create form data and explicitly set instructor_for
            const form = this;
            const formData = new FormData(form);
            const trainerId = document.getElementById('edit_trainer_id').value;
            
            // Explicitly set the instructor_for field value
            formData.set('instructor_for', editInstructorHidden.value);
            
            console.log('Submitting edit trainer form...');
            
            // If we have cropped image data, use it instead of the file
            const croppedInput = document.querySelector('input[name="profile_image_cropped"]');
            if (croppedInput && croppedInput.value) {
                // Remove the original file input from the formData
                formData.delete('profile_image');
                
                // Add the cropped image data
                formData.set('profile_image_base64', croppedInput.value);
            }
            
            // Handle day toggles for schedule
            document.querySelectorAll('.edit-day-toggle').forEach(toggle => {
                const day = toggle.getAttribute('data-day');
                if (!toggle.checked) {
                    // Remove this day's schedule from formData
                    formData.delete(`schedule[${day}][start]`);
                    formData.delete(`schedule[${day}][end]`);
                }
            });
            
            // Add other gender if selected - get value from dropdown in edit form
            const selectedGender = document.getElementById('edit_gender').value;
            if (selectedGender === 'other') {
                const otherGenderValue = document.getElementById('edit_other_gender').value;
                formData.set('other_gender', otherGenderValue);
            }
            
            // Add method spoofing for PUT
            formData.append('_method', 'PUT');
            
            // Log form data entries for debugging
            console.log('Edit form data entries:');
            for (const pair of formData.entries()) {
                console.log(pair[0] + ': ' + (pair[0] === 'profile_image_base64' ? '[Base64 data]' : pair[1]));
            }
            
            // Show loading state
            Swal.fire({
                title: 'Updating trainer...',
                text: 'Please wait while we process your request.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                },
                background: '#1F2937',
                color: '#ffffff'
            });
            
            fetch(`/admin/trainers/${trainerId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData
            })
            .then(response => {
                console.log('Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                if (data.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: data.message,
                        icon: 'success',
                        background: '#1F2937',
                        color: '#ffffff'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    let errorMsg = data.message || 'Failed to update trainer';
                    let errorHtml = '<p>' + errorMsg + '</p>';
                    
                    if (data.errors) {
                        errorHtml += '<div class="mt-4 text-left">';
                        for (const [key, messages] of Object.entries(data.errors)) {
                            errorHtml += `<p class="text-red-400 font-semibold">${key}:</p>`;
                            errorHtml += '<ul class="list-disc pl-5 mb-2">';
                            messages.forEach(message => {
                                errorHtml += `<li class="text-sm">${message}</li>`;
                            });
                            errorHtml += '</ul>';
                        }
                        errorHtml += '</div>';
                    }
                    
                    Swal.fire({
                        title: 'Error!',
                        html: errorHtml,
                        icon: 'error',
                        background: '#1F2937',
                        color: '#ffffff'
                    });
                    
                    console.error('Edit form - Submission errors:', data.errors);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'An unexpected error occurred. Please check console for details.',
                    icon: 'error',
                    background: '#1F2937',
                    color: '#ffffff'
                });
            });
        });
    }
    
    // Handle instructor_for multiselect in Add form
    const instructorSelect = document.getElementById('instructor_for');
    const instructorHidden = document.getElementById('instructor_for_hidden');
    const selectedSportsDisplay = document.querySelector('.selected-sports-display');
    const selectedCountDisplay = document.querySelector('.selected-count');
    
    if (instructorSelect && instructorHidden) {
        // Function to update the selected sports display
        function updateSelectedSportsDisplay() {
            const selectedOptions = Array.from(instructorSelect.selectedOptions);
            const selectedValues = selectedOptions.map(option => option.value);
            
            // Update hidden field
            instructorHidden.value = selectedValues.join(',');
            
            // Update count display
            if (selectedCountDisplay) {
                selectedCountDisplay.textContent = selectedValues.length;
            }
            
            // Update visual display of selected sports
            if (selectedSportsDisplay) {
                selectedSportsDisplay.innerHTML = '';
                
                selectedOptions.forEach(option => {
                    const chip = document.createElement('div');
                    chip.className = 'bg-blue-600 text-white text-xs rounded-full px-3 py-1 flex items-center';
                    chip.innerHTML = `
                        <span>${option.textContent}</span>
                    `;
                    selectedSportsDisplay.appendChild(chip);
                });
            }
            
            console.log('Add form - instructor_for values:', selectedValues, instructorHidden.value);
        }
        
        // Initial update
        updateSelectedSportsDisplay();
        
        // Update on change
        instructorSelect.addEventListener('change', updateSelectedSportsDisplay);
    }
    
    // Handle instructor_for multiselect in Edit form
    const editInstructorSelect = document.getElementById('edit_instructor_for');
    const editInstructorHidden = document.getElementById('edit_instructor_for_hidden');
    const editSelectedSportsDisplay = document.querySelector('.edit-selected-sports-display');
    const editSelectedCountDisplay = document.querySelector('.edit-selected-count');
    
    if (editInstructorSelect && editInstructorHidden) {
        // Function to update the selected sports display
        function updateEditSelectedSportsDisplay() {
            const selectedOptions = Array.from(editInstructorSelect.selectedOptions);
            const selectedValues = selectedOptions.map(option => option.value);
            
            // Update hidden field
            editInstructorHidden.value = selectedValues.join(',');
            
            // Update count display
            if (editSelectedCountDisplay) {
                editSelectedCountDisplay.textContent = selectedValues.length;
            }
            
            // Update visual display of selected sports
            if (editSelectedSportsDisplay) {
                editSelectedSportsDisplay.innerHTML = '';
                
                selectedOptions.forEach(option => {
                    const chip = document.createElement('div');
                    chip.className = 'bg-blue-600 text-white text-xs rounded-full px-3 py-1 flex items-center';
                    chip.innerHTML = `
                        <span>${option.textContent}</span>
                    `;
                    editSelectedSportsDisplay.appendChild(chip);
                });
            }
            
            console.log('Edit form - instructor_for values:', selectedValues, editInstructorHidden.value);
        }
        
        // Initial update
        updateEditSelectedSportsDisplay();
        
        // Update on change
        editInstructorSelect.addEventListener('change', updateEditSelectedSportsDisplay);
    }
});
</script>
@endsection