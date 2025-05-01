@extends('layouts.admin')

@section('title', 'Trainer Management')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
                        <option value="gym" {{ $filter == 'gym' ? 'selected' : '' }}>Gym</option>
                        <option value="boxing" {{ $filter == 'boxing' ? 'selected' : '' }}>Boxing</option>
                        <option value="muay-thai" {{ $filter == 'muay-thai' ? 'selected' : '' }}>Muay Thai</option>
                        <option value="jiu-jitsu" {{ $filter == 'jiu-jitsu' ? 'selected' : '' }}>Jiu Jitsu</option>
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
                <img src="{{ $trainer->profile_url && strpos($trainer->profile_url, 'data:image') === 0 ? $trainer->profile_url : (asset($trainer->profile_url) ?: asset('assets/default-profile.jpg')) }}" alt="{{ $trainer->user->full_name }}" class="w-full h-48 object-cover transition-transform duration-300 hover:scale-105">
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
                            <label for="full_name" class="block text-[#9CA3AF] text-sm font-medium mb-2">Full Name *</label>
                            <input type="text" id="full_name" name="full_name" class="w-full bg-[#374151] border border-[#4B5563] text-white rounded-lg p-3 focus:outline-none focus:border-[#9CA3AF]" required>
                        </div>
                        
                        <div class="mb-4">
                            <label for="email" class="block text-[#9CA3AF] text-sm font-medium mb-2">Email *</label>
                            <input type="email" id="email" name="email" class="w-full bg-[#374151] border border-[#4B5563] text-white rounded-lg p-3 focus:outline-none focus:border-[#9CA3AF]" required>
                        </div>
                        
                        <div class="mb-4">
                            <label for="password" class="block text-[#9CA3AF] text-sm font-medium mb-2">Password *</label>
                            <input type="password" id="password" name="password" class="w-full bg-[#374151] border border-[#4B5563] text-white rounded-lg p-3 focus:outline-none focus:border-[#9CA3AF]" required>
                        </div>
                        
                        <div class="mb-4">
                            <label for="mobile_number" class="block text-[#9CA3AF] text-sm font-medium mb-2">Mobile Number</label>
                            <input type="text" id="mobile_number" name="mobile_number" class="w-full bg-[#374151] border border-[#4B5563] text-white rounded-lg p-3 focus:outline-none focus:border-[#9CA3AF]">
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-[#9CA3AF] text-sm font-medium mb-2">Gender *</label>
                            <div class="flex space-x-4">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="gender" value="male" class="text-blue-500" checked>
                                    <span class="ml-2 text-white">Male</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="gender" value="female" class="text-pink-500">
                                    <span class="ml-2 text-white">Female</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="gender" value="other" class="text-purple-500">
                                    <span class="ml-2 text-white">Other</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Professional Info -->
                    <div class="col-span-1">
                        <h4 class="text-white text-lg font-semibold mb-4 border-b border-[#374151] pb-2">Professional Information</h4>
                        
                        <div class="mb-4">
                            <label for="specialization" class="block text-[#9CA3AF] text-sm font-medium mb-2">Specialization *</label>
                            <input type="text" id="specialization" name="specialization" class="w-full bg-[#374151] border border-[#4B5563] text-white rounded-lg p-3 focus:outline-none focus:border-[#9CA3AF]" required>
                        </div>
                        
                        <div class="mb-4">
                            <label for="hourly_rate" class="block text-[#9CA3AF] text-sm font-medium mb-2">Hourly Rate (₱) *</label>
                            <input type="number" id="hourly_rate" name="hourly_rate" min="0" step="0.01" class="w-full bg-[#374151] border border-[#4B5563] text-white rounded-lg p-3 focus:outline-none focus:border-[#9CA3AF]" required>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-[#9CA3AF] text-sm font-medium mb-2">Instructor For *</label>
                            <div class="grid grid-cols-2 gap-2">
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="instructor_for[]" value="gym" class="text-blue-500">
                                    <span class="ml-2 text-white">Gym</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="instructor_for[]" value="boxing" class="text-red-500">
                                    <span class="ml-2 text-white">Boxing</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="instructor_for[]" value="muay-thai" class="text-yellow-500">
                                    <span class="ml-2 text-white">Muay Thai</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="instructor_for[]" value="jiu-jitsu" class="text-green-500">
                                    <span class="ml-2 text-white">Jiu Jitsu</span>
                                </label>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="short_intro" class="block text-[#9CA3AF] text-sm font-medium mb-2">Short Introduction</label>
                            <textarea id="short_intro" name="short_intro" rows="3" class="w-full bg-[#374151] border border-[#4B5563] text-white rounded-lg p-3 focus:outline-none focus:border-[#9CA3AF]"></textarea>
                        </div>
                        
                        <div class="mb-4">
                            <label for="profile_image" class="block text-[#9CA3AF] text-sm font-medium mb-2">Profile Image</label>
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
                                        <input id="profile_image" name="profile_image" type="file" accept="image/png, image/jpeg, image/gif" class="hidden" />
                                    </label>
                                </div>
                                <span id="selectedFileName" class="text-xs text-[#9CA3AF]"></span>
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
                    <button type="submit" class="px-5 py-2 bg-[#3B82F6] text-white rounded-lg hover:bg-[#2563EB] transition-colors duration-200">
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
                            <input type="text" id="edit_full_name" name="full_name" class="w-full bg-[#374151] border border-[#4B5563] text-white rounded-lg p-3 focus:outline-none focus:border-[#9CA3AF]" required>
                        </div>
                        
                        <div class="mb-4">
                            <label for="edit_email" class="block text-[#9CA3AF] text-sm font-medium mb-2">Email *</label>
                            <input type="email" id="edit_email" name="email" class="w-full bg-[#374151] border border-[#4B5563] text-white rounded-lg p-3 focus:outline-none focus:border-[#9CA3AF]" required>
                        </div>
                        
                        <div class="mb-4">
                            <label for="edit_password" class="block text-[#9CA3AF] text-sm font-medium mb-2">Password (leave blank to keep current)</label>
                            <input type="password" id="edit_password" name="password" class="w-full bg-[#374151] border border-[#4B5563] text-white rounded-lg p-3 focus:outline-none focus:border-[#9CA3AF]">
                        </div>
                        
                        <div class="mb-4">
                            <label for="edit_mobile_number" class="block text-[#9CA3AF] text-sm font-medium mb-2">Mobile Number</label>
                            <input type="text" id="edit_mobile_number" name="mobile_number" class="w-full bg-[#374151] border border-[#4B5563] text-white rounded-lg p-3 focus:outline-none focus:border-[#9CA3AF]">
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-[#9CA3AF] text-sm font-medium mb-2">Gender *</label>
                            <div class="flex space-x-4">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="gender" id="edit_gender_male" value="male" class="text-blue-500">
                                    <span class="ml-2 text-white">Male</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="gender" id="edit_gender_female" value="female" class="text-pink-500">
                                    <span class="ml-2 text-white">Female</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="gender" id="edit_gender_other" value="other" class="text-purple-500">
                                    <span class="ml-2 text-white">Other</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Professional Info -->
                    <div class="col-span-1">
                        <h4 class="text-white text-lg font-semibold mb-4 border-b border-[#374151] pb-2">Professional Information</h4>
                        
                        <div class="mb-4">
                            <label for="edit_specialization" class="block text-[#9CA3AF] text-sm font-medium mb-2">Specialization *</label>
                            <input type="text" id="edit_specialization" name="specialization" class="w-full bg-[#374151] border border-[#4B5563] text-white rounded-lg p-3 focus:outline-none focus:border-[#9CA3AF]" required>
                        </div>
                        
                        <div class="mb-4">
                            <label for="edit_hourly_rate" class="block text-[#9CA3AF] text-sm font-medium mb-2">Hourly Rate (₱) *</label>
                            <input type="number" id="edit_hourly_rate" name="hourly_rate" min="0" step="0.01" class="w-full bg-[#374151] border border-[#4B5563] text-white rounded-lg p-3 focus:outline-none focus:border-[#9CA3AF]" required>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-[#9CA3AF] text-sm font-medium mb-2">Instructor For *</label>
                            <div class="grid grid-cols-2 gap-2">
                                <label class="inline-flex items-center">
                                    <input type="checkbox" id="edit_instructor_gym" name="instructor_for[]" value="gym" class="text-blue-500">
                                    <span class="ml-2 text-white">Gym</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="checkbox" id="edit_instructor_boxing" name="instructor_for[]" value="boxing" class="text-red-500">
                                    <span class="ml-2 text-white">Boxing</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="checkbox" id="edit_instructor_muaythai" name="instructor_for[]" value="muay-thai" class="text-yellow-500">
                                    <span class="ml-2 text-white">Muay Thai</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="checkbox" id="edit_instructor_jiujitsu" name="instructor_for[]" value="jiu-jitsu" class="text-green-500">
                                    <span class="ml-2 text-white">Jiu Jitsu</span>
                                </label>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="edit_short_intro" class="block text-[#9CA3AF] text-sm font-medium mb-2">Short Introduction</label>
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

<script>
// Modal controls
function openAddTrainerModal() {
    document.getElementById('addTrainerModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
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
    fetch(`/admin/trainers/${trainerId}/archive`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
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
            
            // Set gender radio
            const genderRadios = document.querySelectorAll('input[name="gender"]');
            genderRadios.forEach(radio => {
                if (radio.value === data.user.gender) {
                    radio.checked = true;
                }
            });
            
            // Professional info
            document.getElementById('edit_specialization').value = data.specialization || '';
            document.getElementById('edit_hourly_rate').value = data.hourly_rate || '';
            document.getElementById('edit_short_intro').value = data.short_intro || '';
            
            // Set profile image if available
            const currentImage = document.getElementById('currentImage');
            const noImagePlaceholder = document.getElementById('noImagePlaceholder');
            
            if (data.profile_url) {
                // If it's already a base64 image or a URL
                if (data.profile_url.startsWith('data:image')) {
                    currentImage.src = data.profile_url;
                } else {
                    // It's a file path
                    currentImage.src = data.profile_url;
                }
                currentImage.classList.remove('hidden');
                noImagePlaceholder.classList.add('hidden');
            } else {
                currentImage.classList.add('hidden');
                noImagePlaceholder.classList.remove('hidden');
            }
            
            // Instructor for checkboxes
            const instructorFor = data.instructor_for ? data.instructor_for.split(',') : [];
            document.getElementById('edit_instructor_gym').checked = instructorFor.includes('gym');
            document.getElementById('edit_instructor_boxing').checked = instructorFor.includes('boxing');
            document.getElementById('edit_instructor_muaythai').checked = instructorFor.includes('muay-thai');
            document.getElementById('edit_instructor_jiujitsu').checked = instructorFor.includes('jiu-jitsu');
            
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

    // Image upload elements for add form
    const profileImageInput = document.getElementById('profile_image');
    const imagePreview = document.getElementById('imagePreview');
    const imagePreviewContainer = document.getElementById('imagePreviewContainer');
    const uploadPlaceholder = document.getElementById('uploadPlaceholder');
    const selectedFileName = document.getElementById('selectedFileName');
    
    // Image upload elements for edit form
    const editProfileImageInput = document.getElementById('edit_profile_image');
    const editImagePreview = document.getElementById('editImagePreview');
    const editImagePreviewContainer = document.getElementById('editImagePreviewContainer');
    const editUploadPlaceholder = document.getElementById('editUploadPlaceholder');
    const editSelectedFileName = document.getElementById('editSelectedFileName');
    
    // Handle image upload for add form
    if (profileImageInput) {
        profileImageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            
            if (file) {
                // File size validation (5MB max)
                if (file.size > 5 * 1024 * 1024) {
                    Swal.fire({
                        title: 'File Too Large',
                        text: 'File size should not exceed 5MB',
                        icon: 'error',
                        background: '#1F2937',
                        color: '#ffffff'
                    });
                    profileImageInput.value = '';
                    return;
                }
                
                // Show file name
                selectedFileName.textContent = file.name;
                
                // Show image preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    uploadPlaceholder.classList.add('hidden');
                    imagePreviewContainer.classList.remove('hidden');
                }
                reader.readAsDataURL(file);
            } else {
                // Reset preview
                selectedFileName.textContent = '';
                uploadPlaceholder.classList.remove('hidden');
                imagePreviewContainer.classList.add('hidden');
            }
        });
    }
    
    // Handle image upload for edit form
    if (editProfileImageInput) {
        editProfileImageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            
            if (file) {
                // File size validation (5MB max)
                if (file.size > 5 * 1024 * 1024) {
                    Swal.fire({
                        title: 'File Too Large',
                        text: 'File size should not exceed 5MB',
                        icon: 'error',
                        background: '#1F2937',
                        color: '#ffffff'
                    });
                    editProfileImageInput.value = '';
                    return;
                }
                
                // Show file name
                editSelectedFileName.textContent = file.name;
                
                // Show image preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    editImagePreview.src = e.target.result;
                    editUploadPlaceholder.classList.add('hidden');
                    editImagePreviewContainer.classList.remove('hidden');
                }
                reader.readAsDataURL(file);
            } else {
                // Reset preview
                editSelectedFileName.textContent = '';
                editUploadPlaceholder.classList.remove('hidden');
                editImagePreviewContainer.classList.add('hidden');
            }
        });
    }
    
    // Add Trainer button event
    document.getElementById('addTrainerBtn').addEventListener('click', openAddTrainerModal);
    
    // Form submission handlers
    document.getElementById('addTrainerForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const form = this;
        const formData = new FormData(form);
        
        // Debug logging for the file
        const fileInput = document.getElementById('profile_image');
        if (fileInput.files.length > 0) {
            console.log('File selected:', fileInput.files[0].name);
            console.log('File size:', fileInput.files[0].size);
        } else {
            console.log('No file selected');
        }
        
        // Handle instructor_for checkboxes
        const instructorCheckboxes = form.querySelectorAll('input[name="instructor_for[]"]:checked');
        if (instructorCheckboxes.length > 0) {
            formData.delete('instructor_for[]'); // Remove the array entries
            const instructorValues = Array.from(instructorCheckboxes).map(cb => cb.value);
            formData.set('instructor_for', instructorValues.join(','));
        } else {
            formData.set('instructor_for', ''); // Ensure field exists even if empty
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
        
        fetch('/admin/trainers', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
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
                if (data.errors) {
                    const errorList = Object.keys(data.errors).map(key => 
                        `<li>${data.errors[key].join('</li><li>')}</li>`
                    ).join('');
                    
                    Swal.fire({
                        title: 'Error!',
                        html: `<p>${errorMsg}</p><ul class="text-left mt-3">${errorList}</ul>`,
                        icon: 'error',
                        background: '#1F2937',
                        color: '#ffffff'
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: errorMsg,
                        icon: 'error',
                        background: '#1F2937',
                        color: '#ffffff'
                    });
                }
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
    
    document.getElementById('editTrainerForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const form = this;
        const formData = new FormData(form);
        const trainerId = document.getElementById('edit_trainer_id').value;
        
        // Handle instructor_for checkboxes
        const instructorCheckboxes = form.querySelectorAll('input[name="instructor_for[]"]:checked');
        if (instructorCheckboxes.length > 0) {
            formData.delete('instructor_for[]'); // Remove the array entries
            const instructorValues = Array.from(instructorCheckboxes).map(cb => cb.value);
            formData.set('instructor_for', instructorValues.join(','));
        } else {
            formData.set('instructor_for', ''); // Ensure field exists even if empty
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
        
        // Add method spoofing for PUT
        formData.append('_method', 'PUT');
        
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
        .then(response => response.json())
        .then(data => {
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
                if (data.errors) {
                    const errorList = Object.keys(data.errors).map(key => 
                        `<li>${data.errors[key].join('</li><li>')}</li>`
                    ).join('');
                    
                    Swal.fire({
                        title: 'Error!',
                        html: `<p>${errorMsg}</p><ul class="text-left mt-3">${errorList}</ul>`,
                        icon: 'error',
                        background: '#1F2937',
                        color: '#ffffff'
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: errorMsg,
                        icon: 'error',
                        background: '#1F2937',
                        color: '#ffffff'
                    });
                }
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
    
    // Day toggles for availability
    const dayToggles = document.querySelectorAll('.day-toggle, .edit-day-toggle');
    dayToggles.forEach(toggle => {
        toggle.addEventListener('change', function() {
            const day = this.getAttribute('data-day');
            const isEdit = this.classList.contains('edit-day-toggle');
            const container = document.querySelector(`.${isEdit ? 'edit-' : ''}day-time-container[data-day="${day}"]`);
            
            if (this.checked) {
                container.style.display = 'flex';
            } else {
                container.style.display = 'none';
            }
        });
    });
});
</script>
@endsection