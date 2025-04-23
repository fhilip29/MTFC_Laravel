@extends('layouts.admin')

@section('title', 'Trainer Management')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- FilePond CSS -->
<link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">
<link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">

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
                        type="text" 
                        placeholder="Search trainers..." 
                        class="w-full pl-10 pr-4 py-3 bg-[#374151] border-2 border-[#4B5563] text-white rounded-lg focus:outline-none focus:border-[#9CA3AF] focus:ring-1 focus:ring-[#9CA3AF] transition-all duration-200 placeholder-gray-400"
                    >
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-[#9CA3AF]"></i>
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
                        {{ $trainer->user->is_archived ? 'Archived' : 'Active' }}
                    </span>
                </div>
            </div>
            <div class="p-6">
                <h3 class="text-xl font-bold text-white mb-1 hover:text-[#9CA3AF] transition-colors duration-200">{{ $trainer->user->full_name }}</h3>
                <p class="text-[#9CA3AF] text-sm mb-3">{{ $trainer->specialization }}</p>
                <div class="flex items-center text-sm text-[#9CA3AF] mb-4">
                    <i class="fas fa-users mr-2"></i>
                    <span>{{ $trainer->active_clients_count }} Active Clients</span>
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
            <form id="addTrainerForm" class="p-6">
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
                            <input type="file" class="filepond" name="filepond" id="profile_image" accept="image/png, image/jpeg, image/gif">
                            <input type="hidden" name="profile_image" id="profile_image_hidden">
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
            <form id="editTrainerForm" class="p-6">
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
                            <input type="file" class="filepond" name="filepond" id="edit_profile_image" accept="image/png, image/jpeg, image/gif">
                            <input type="hidden" name="profile_image" id="edit_profile_image_hidden">
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

<!-- FilePond JS -->
<script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>
<script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
<script src="https://unpkg.com/filepond-plugin-image-exif-orientation/dist/filepond-plugin-image-exif-orientation.js"></script>
<script src="https://unpkg.com/filepond/dist/filepond.js"></script>

<script>
// Register FilePond plugins
FilePond.registerPlugin(
    FilePondPluginFileValidateType,
    FilePondPluginImagePreview,
    FilePondPluginImageExifOrientation
);

// Modal controls
function openAddTrainerModal() {
    document.getElementById('addTrainerModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    // Initialize FilePond
    initFilePond('profile_image', 'profile_image_hidden');
}

function closeAddTrainerModal() {
    document.getElementById('addTrainerModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function openEditTrainerModal() {
    document.getElementById('editTrainerModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    // Initialize FilePond for edit
    initFilePond('edit_profile_image', 'edit_profile_image_hidden');
}

function closeEditTrainerModal() {
    document.getElementById('editTrainerModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Initialize FilePond
function initFilePond(inputId, hiddenInputId) {
    const inputElement = document.getElementById(inputId);
    const pond = FilePond.create(inputElement, {
        acceptedFileTypes: ['image/png', 'image/jpeg', 'image/gif'],
        server: {
            process: {
                url: '/upload',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                onload: (response) => {
                    console.log('File uploaded successfully:', response);
                    // Set the serverID as the value of the hidden input
                    document.getElementById(hiddenInputId).value = response;
                    return response;
                },
                onerror: (response) => {
                    console.error('FilePond error:', response);
                    return response.data;
                }
            },
            revert: {
                url: '/upload',
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                onload: (response) => {
                    console.log('File removed successfully:', response);
                    // Clear the hidden input value
                    document.getElementById(hiddenInputId).value = '';
                    return response;
                }
            },
            load: '/upload/'
        },
        labelIdle: 'Drag & Drop your image or <span class="filepond--label-action">Browse</span>',
        imagePreviewHeight: 170,
        stylePanelLayout: 'compact',
        credits: false,
        // This prevents FilePond from removing the file when the form is submitted
        instantUpload: true,
        allowRevert: true
    });
    
    return pond;
}

// Frontend-only confirmation dialog
function confirmArchiveTrainer(trainerId, trainerName, isArchived) {
    const action = isArchived ? 'unarchive' : 'archive';
    if (confirm(`Are you sure you want to ${action} ${trainerName}?`)) {
        archiveTrainer(trainerId);
    }
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
            alert(data.message);
            // Reload the page to show the updated status
            window.location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to update trainer status.'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An unexpected error occurred. Please try again.');
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
            alert('Failed to load trainer data. Please try again.');
        });
}

// Document ready event handler
document.addEventListener('DOMContentLoaded', function() {
    // Add Trainer button event
    document.getElementById('addTrainerBtn').addEventListener('click', openAddTrainerModal);
    
    // Form submission handlers
    document.getElementById('addTrainerForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const form = this;
        const formData = new FormData(form);
        
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
                alert(data.message);
                window.location.reload();
            } else {
                let errorMsg = data.message || 'Failed to add trainer';
                if (data.errors) {
                    errorMsg += ':\n';
                    Object.keys(data.errors).forEach(key => {
                        errorMsg += `- ${data.errors[key].join('\n- ')}\n`;
                    });
                }
                alert('Error: ' + errorMsg);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An unexpected error occurred. Please check console for details.');
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
                alert(data.message);
                window.location.reload();
            } else {
                let errorMsg = data.message || 'Failed to update trainer';
                if (data.errors) {
                    errorMsg += ':\n';
                    Object.keys(data.errors).forEach(key => {
                        errorMsg += `- ${data.errors[key].join('\n- ')}\n`;
                    });
                }
                alert('Error: ' + errorMsg);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An unexpected error occurred. Please check console for details.');
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