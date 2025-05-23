@extends('layouts.admin')

@section('title', 'Announcement Management')

@section('content')
<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Toggle Switch Styles -->
<style>
    /* Toggle Switch Styles */
    .switch {
        position: relative;
        display: inline-block;
        width: 48px;
        height: 24px;
    }
    
    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    
    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #4B5563;
        transition: .4s;
        border-radius: 24px;
    }
    
    .slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }
    
    input:checked + .slider {
        background-color: #10B981;
    }
    
    input:focus + .slider {
        box-shadow: 0 0 1px #10B981;
    }
    
    input:checked + .slider:before {
        transform: translateX(24px);
    }
    
    .status-label {
        margin-left: 8px;
        font-size: 12px;
        font-weight: 500;
    }
</style>

<div class="p-6" x-data="{ 
    showAddModal: false, 
    showViewModal: false,
    showEditModal: false,
    currentAnnouncement: null,
    
    openEditModal(announcement) {
        this.currentAnnouncement = JSON.parse(JSON.stringify(announcement));
        this.showEditModal = true;
    },
    
    openViewModal(announcement) {
        this.currentAnnouncement = JSON.parse(JSON.stringify(announcement));
        this.showViewModal = true;
    }
}">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-white">Announcement Management</h1>
        <div class="flex gap-4">
            <button @click="showAddModal = true" class="bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-2 rounded flex items-center">
                <i class="fas fa-plus mr-2"></i> Create Announcement
            </button>
        </div>
    </div>

   <!-- Announcement Stats -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-gray-800 rounded-lg p-6 shadow-lg flex items-center">
        <div class="rounded-full bg-red-600 p-3 mr-4">
            <i class="fas fa-bullhorn text-white text-xl"></i>
        </div>
        <div>
            <p class="text-gray-400 text-sm">Total Announcements</p>
            <p class="text-2xl font-bold text-white">{{ count($announcements) }}</p>
        </div>
    </div>
    <div class="bg-gray-800 rounded-lg p-6 shadow-lg flex items-center">
        <div class="rounded-full bg-green-600 p-3 mr-4">
            <i class="fas fa-check-circle text-white text-xl"></i>
        </div>
        <div>
            <p class="text-gray-400 text-sm">Active Announcements</p>
            <p class="text-2xl font-bold text-white">{{ $announcements->where('is_active', 'active')->count() }}</p>
        </div>
    </div>
    <div class="bg-gray-800 rounded-lg p-6 shadow-lg flex items-center">
        <div class="rounded-full bg-blue-600 p-3 mr-4">
            <i class="fas fa-calendar-alt text-white text-xl"></i>
        </div>
        <div>
            <p class="text-gray-400 text-sm">Scheduled Announcements</p>
            <p class="text-2xl font-bold text-white">{{ $announcements->whereNotNull('scheduled_at')->where('scheduled_at', '>', now())->count() }}</p>
        </div>
    </div>
</div>


    <!-- Announcement Table -->
    <div class="bg-gray-800 rounded-lg shadow-lg overflow-hidden">
        <div class="p-4 border-b border-gray-700 flex flex-col md:flex-row justify-between items-center gap-4">
            <h2 class="text-xl font-semibold text-white">Announcement Records</h2>
            <div class="flex flex-col md:flex-row gap-3 items-center w-full md:w-auto">
                <div class="relative w-full md:w-64">
                    <input type="text" id="searchAnnouncement" placeholder="Search announcements..." class="bg-gray-700 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full pl-10 p-2.5">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                </div>
                
                <!-- Status Filter -->
                <div class="relative w-full md:w-40">
                    <select id="statusFilter" class="bg-gray-700 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full pl-10 p-2.5">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="pending">Pending</option>
                    </select>
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i class="fas fa-filter text-gray-400"></i>
                    </div>
                </div>
                
                <!-- Date Filter -->
                <div class="flex items-center gap-2 w-full md:w-auto">
                    <div class="relative w-full md:w-48">
                        <input type="date" id="dateFilter" class="bg-gray-700 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full pl-10 p-2.5">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i class="fas fa-calendar text-gray-400"></i>
                        </div>
                    </div>
                    <button id="clearDateFilter" class="bg-gray-700 hover:bg-gray-600 text-white rounded-lg p-2.5">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <!-- Reset All Filters Button -->
                <button id="resetAllFilters" class="bg-gray-700 hover:bg-gray-600 text-white rounded-lg px-4 py-2.5 flex items-center gap-2">
                    <i class="fas fa-undo-alt"></i>
                    <span>Reset</span>
                </button>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-gray-800 text-white">
                <thead class="bg-gray-700 text-xs uppercase font-medium">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left tracking-wider">Title</th>
                        <th scope="col" class="px-6 py-3 text-left tracking-wider">Date</th>
                        <th scope="col" class="px-6 py-3 text-left tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left tracking-wider">Actions</th>
                        <th scope="col" class="px-6 py-3 text-left tracking-wider">Enable</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @forelse($announcements as $announcement)
                    <tr class="hover:bg-gray-700 transition" data-id="{{ $announcement->id }}">
                        <td class="px-6 py-4 whitespace-nowrap font-medium">{{ $announcement->title }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $announcement->created_at->format('m/d/Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full {{ $announcement->statusClass }}">
                                {{ $announcement->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex space-x-2 items-center">
                                <button @click="openViewModal({{ $announcement }})" class="text-blue-400 hover:text-blue-300 view-announcement" data-id="{{ $announcement->id }}">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button @click="openEditModal({{ $announcement }})" class="text-yellow-400 hover:text-yellow-300 edit-announcement" data-id="{{ $announcement->id }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button @click="confirmDelete('{{ $announcement->id }}', '{{ $announcement->title }}')" class="text-red-400 hover:text-red-300 delete-announcement" data-id="{{ $announcement->id }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <label class="switch">
                                    <input type="checkbox" 
                                           {{ $announcement->is_active === 'active' ? 'checked' : '' }} 
                                           {{ $announcement->is_active === 'pending' ? 'disabled' : '' }}
                                           onchange="toggleStatus('{{ $announcement->id }}')">
                                    <span class="slider"></span>
                                </label>
                                <span class="status-label text-xs 
                                    {{ $announcement->is_active === 'active' ? 'text-green-400' : 
                                      ($announcement->is_active === 'pending' ? 'text-yellow-400' : 'text-gray-400') }}">
                                    {{ $announcement->is_active === 'active' ? 'Active' : 
                                      ($announcement->is_active === 'pending' ? 'Pending' : 'Inactive') }}
                                </span>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-400">
                            No announcements found. Click "Create Announcement" to add your first announcement.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Announcement Modal -->
    <div x-show="showAddModal" class="fixed inset-0 z-50 overflow-auto bg-black bg-opacity-50 flex items-center justify-center" style="display: none;">
        <div class="relative bg-gray-800 rounded-lg shadow-lg max-w-3xl w-full mx-auto p-6 border border-gray-700" @click.away="showAddModal = false">
            <div class="flex justify-between items-center border-b border-gray-700 pb-3 mb-4">
                <h3 class="text-xl font-semibold text-white">Create New Announcement</h3>
                <button @click="showAddModal = false" class="text-gray-400 hover:text-white">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="announcementForm" action="{{ route('admin.announcements.store') }}" method="POST">
                @csrf
                <input type="hidden" name="is_scheduled" value="0" id="is_scheduled_flag">
                <div class="space-y-4">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-300 mb-1">Title <span class="text-red-500">*</span></label>
                        <input type="text" name="title" id="title" required 
                               placeholder="Enter a clear, descriptive title" 
                               class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5">
                        <div class="text-gray-400 text-xs mt-1">Required. Enter a clear, descriptive title for the announcement</div>
                        <div class="text-red-500 text-xs mt-1">@error('title'){{ $message }}@enderror</div>
                    </div>
                    
                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-300 mb-1">Message <span class="text-red-500">*</span></label>
                        <textarea name="message" id="message" rows="5" required 
                                  placeholder="Enter detailed announcement message" 
                                  class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5"></textarea>
                        <div class="text-gray-400 text-xs mt-1">Required. Provide a detailed message for the announcement</div>
                        <div class="text-red-500 text-xs mt-1">@error('message'){{ $message }}@enderror</div>
                    </div>
                    
                    <div class="flex items-center">
                        <input type="checkbox" name="is_active" id="is_active" checked class="w-4 h-4 text-red-600 bg-gray-700 border-gray-600 rounded focus:ring-red-500">
                        <label for="is_active" class="ml-2 text-sm font-medium text-gray-300">Make announcement active</label>
                    </div>
                    
                    <div class="flex items-center">
                        <input type="checkbox" name="schedule_later" id="schedule_later" class="w-4 h-4 text-red-600 bg-gray-700 border-gray-600 rounded focus:ring-red-500">
                        <label for="schedule_later" class="ml-2 text-sm font-medium text-gray-300">Schedule for later</label>
                        <div class="ml-2 text-xs text-yellow-400" id="schedule_note" style="display: none;">
                            <i class="fas fa-info-circle"></i> Status will be set to "Pending" until scheduled time
                        </div>
                    </div>
                    
                    <div id="schedulingOptions" class="hidden space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="schedule_date" class="block text-sm font-medium text-gray-300 mb-1">Date <span class="text-red-500">*</span></label>
                                <input type="date" name="schedule_date" id="schedule_date" 
                                       min="{{ date('Y-m-d', strtotime('+1 day')) }}" 
                                       required 
                                       class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5">
                                <div class="text-gray-400 text-xs mt-1">Select a future date (cannot be today or earlier)</div>
                                <div class="text-red-500 text-xs mt-1">@error('schedule_date'){{ $message }}@enderror</div>
                            </div>
                            <div>
                                <label for="schedule_time" class="block text-sm font-medium text-gray-300 mb-1">Time <span class="text-red-500">*</span></label>
                                <input type="time" name="schedule_time" id="schedule_time" 
                                       required 
                                       class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5">
                                <div class="text-gray-400 text-xs mt-1">Select the time when this announcement should become active</div>
                                <div class="text-red-500 text-xs mt-1">@error('schedule_time'){{ $message }}@enderror</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end mt-6 space-x-3">
                    <button type="button" @click="showAddModal = false" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded">Cancel</button>
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">Save Announcement</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Announcement Modal -->
    <div x-show="showEditModal" class="fixed inset-0 z-50 overflow-auto bg-black bg-opacity-50 flex items-center justify-center" style="display: none;">
        <div class="relative bg-gray-800 rounded-lg shadow-lg max-w-3xl w-full mx-auto p-6 border border-gray-700" @click.away="showEditModal = false">
            <div class="flex justify-between items-center border-b border-gray-700 pb-3 mb-4">
                <h3 class="text-xl font-semibold text-white">Edit Announcement</h3>
                <button @click="showEditModal = false" class="text-gray-400 hover:text-white">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="editAnnouncementForm" method="POST" x-bind:action="'/admin/announcements/' + currentAnnouncement?.id">
                @csrf
                <input type="hidden" name="_method" value="PUT">
                <input type="hidden" name="id" x-bind:value="currentAnnouncement?.id">
                <input type="hidden" name="is_scheduled" value="0" id="edit_is_scheduled_flag">
                <div class="space-y-4">
                    <div>
                        <label for="edit_title" class="block text-sm font-medium text-gray-300 mb-1">Title <span class="text-red-500">*</span></label>
                        <input type="text" name="title" id="edit_title" required 
                               placeholder="Enter a clear, descriptive title" 
                               class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5" 
                               x-bind:value="currentAnnouncement?.title">
                        <div class="text-gray-400 text-xs mt-1">Required. Enter a clear, descriptive title for the announcement</div>
                        <div class="text-red-500 text-xs mt-1">@error('title'){{ $message }}@enderror</div>
                    </div>
                    
                    <div>
                        <label for="edit_message" class="block text-sm font-medium text-gray-300 mb-1">Message <span class="text-red-500">*</span></label>
                        <textarea name="message" id="edit_message" rows="5" required 
                                  placeholder="Enter detailed announcement message" 
                                  class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5" 
                                  x-text="currentAnnouncement?.message"></textarea>
                        <div class="text-gray-400 text-xs mt-1">Required. Provide a detailed message for the announcement</div>
                        <div class="text-red-500 text-xs mt-1">@error('message'){{ $message }}@enderror</div>
                    </div>
                    
                    <div class="flex items-center" x-show="currentAnnouncement?.is_active !== 'pending'">
                        <input type="checkbox" name="is_active" id="edit_is_active" class="w-4 h-4 text-red-600 bg-gray-700 border-gray-600 rounded focus:ring-red-500" x-bind:checked="currentAnnouncement?.is_active === 'active'">
                        <label for="edit_is_active" class="ml-2 text-sm font-medium text-gray-300">Make announcement active</label>
                    </div>
                    
                    <div class="flex items-center">
                        <input type="checkbox" name="schedule_later" id="edit_schedule_later" class="w-4 h-4 text-red-600 bg-gray-700 border-gray-600 rounded focus:ring-red-500" x-bind:checked="currentAnnouncement?.scheduled_at !== null">
                        <label for="edit_schedule_later" class="ml-2 text-sm font-medium text-gray-300">Schedule for later</label>
                        <div class="ml-2 text-xs text-yellow-400" id="edit_schedule_note" style="display: none;">
                            <i class="fas fa-info-circle"></i> Status will be set to "Pending" until scheduled time
                        </div>
                    </div>
                    
                    <div id="edit_schedulingOptions" class="hidden space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="edit_schedule_date" class="block text-sm font-medium text-gray-300 mb-1">Date <span class="text-red-500">*</span></label>
                                <input type="date" name="schedule_date" id="edit_schedule_date" 
                                       min="{{ date('Y-m-d', strtotime('+1 day')) }}" 
                                       required 
                                       class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5" 
                                       x-bind:value="formatScheduleDate(currentAnnouncement?.scheduled_at)">
                                <div class="text-gray-400 text-xs mt-1">Select a future date (cannot be today or earlier)</div>
                                <div class="text-red-500 text-xs mt-1">@error('schedule_date'){{ $message }}@enderror</div>
                            </div>
                            <div>
                                <label for="edit_schedule_time" class="block text-sm font-medium text-gray-300 mb-1">Time <span class="text-red-500">*</span></label>
                                <input type="time" name="schedule_time" id="edit_schedule_time" 
                                       required 
                                       class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5" 
                                       x-bind:value="formatScheduleTime(currentAnnouncement?.scheduled_at)">
                                <div class="text-gray-400 text-xs mt-1">Select the time when this announcement should become active</div>
                                <div class="text-red-500 text-xs mt-1">@error('schedule_time'){{ $message }}@enderror</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end mt-6 space-x-3">
                    <button type="button" @click="showEditModal = false" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded">Cancel</button>
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">Update Announcement</button>
                </div>
            </form>
        </div>
    </div>

    <!-- View Announcement Modal -->
    <div x-show="showViewModal" class="fixed inset-0 z-50 overflow-auto bg-black bg-opacity-50 flex items-center justify-center" style="display: none;">
        <div class="relative bg-gray-800 rounded-lg shadow-lg max-w-3xl w-full mx-auto p-6 border border-gray-700" @click.away="showViewModal = false">
            <div class="flex justify-between items-center border-b border-gray-700 pb-3 mb-4">
                <h3 class="text-xl font-semibold text-white">View Announcement</h3>
                <button @click="showViewModal = false" class="text-gray-400 hover:text-white">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="space-y-4">
                <div>
                    <h4 x-text="currentAnnouncement?.title" class="text-xl text-white font-semibold"></h4>
                </div>
                <div class="bg-gray-700 p-4 rounded-lg">
                    <p x-text="currentAnnouncement?.message" class="text-gray-300"></p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-gray-300">
                    <div>
                    <p class="text-sm">
    <span class="font-medium">Status:</span>
    <template x-if="currentAnnouncement?.status === 'Active'">
        <span class="px-2 py-1 text-xs rounded-full bg-green-600 text-white">Active</span>
    </template>
    <template x-if="currentAnnouncement?.status === 'Inactive'">
        <span class="px-2 py-1 text-xs rounded-full bg-gray-600 text-white">Inactive</span>
    </template>
    <template x-if="currentAnnouncement?.status === 'Pending'">
        <span class="px-2 py-1 text-xs rounded-full bg-yellow-600 text-white">Pending</span>
    </template>
    <template x-if="currentAnnouncement?.status === 'Sent'">
        <span class="px-2 py-1 text-xs rounded-full bg-green-700 text-white">Sent</span>
    </template>
</p>

                    </div>
                    <div>
                        <p class="text-sm"><span class="font-medium">Created:</span> <span x-text="new Date(currentAnnouncement?.created_at).toLocaleString()"></span></p>
                    </div>
                    <div x-show="currentAnnouncement?.scheduled_at">
                        <p class="text-sm"><span class="font-medium">Scheduled for:</span> <span x-text="new Date(currentAnnouncement?.scheduled_at).toLocaleString()"></span></p>
                    </div>
                </div>
            </div>
            <div class="flex justify-end mt-6">
                <button type="button" @click="showViewModal = false" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded">Close</button>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize date filter functionality
    initDateFilter();

    // Toggle scheduling options in Add modal
    const scheduleLater = document.getElementById('schedule_later');
    const schedulingOptions = document.getElementById('schedulingOptions');
    const isScheduledFlag = document.getElementById('is_scheduled_flag');
    const scheduleNote = document.getElementById('schedule_note');
    const isActiveCheckbox = document.getElementById('is_active');
    
    if (scheduleLater && schedulingOptions) {
        scheduleLater.addEventListener('change', function() {
            schedulingOptions.classList.toggle('hidden', !this.checked);
            
            // Set the is_scheduled flag and show note
            if (this.checked) {
                isScheduledFlag.value = '1';
                scheduleNote.style.display = 'block';
                // When scheduling, disable and uncheck active checkbox
                if (isActiveCheckbox) {
                    isActiveCheckbox.checked = false;
                    isActiveCheckbox.disabled = true;
                }
            } else {
                isScheduledFlag.value = '0';
                scheduleNote.style.display = 'none';
                // Re-enable active checkbox when not scheduling
                if (isActiveCheckbox) {
                    isActiveCheckbox.disabled = false;
                }
            }
        });
    }
    
    // Toggle scheduling options in Edit modal
    const editScheduleLater = document.getElementById('edit_schedule_later');
    const editSchedulingOptions = document.getElementById('edit_schedulingOptions');
    const editIsScheduledFlag = document.getElementById('edit_is_scheduled_flag');
    const editScheduleNote = document.getElementById('edit_schedule_note');
    const editIsActiveCheckbox = document.getElementById('edit_is_active');
    
    if (editScheduleLater && editSchedulingOptions) {
        editScheduleLater.addEventListener('change', function() {
            editSchedulingOptions.classList.toggle('hidden', !this.checked);
            
            // Set the is_scheduled flag and show note
            if (this.checked) {
                editIsScheduledFlag.value = '1';
                editScheduleNote.style.display = 'block';
                // When scheduling, disable and uncheck active checkbox
                if (editIsActiveCheckbox) {
                    editIsActiveCheckbox.checked = false;
                    editIsActiveCheckbox.disabled = true;
                }
            } else {
                editIsScheduledFlag.value = '0';
                editScheduleNote.style.display = 'none';
                // Re-enable active checkbox when not scheduling
                if (editIsActiveCheckbox) {
                    editIsActiveCheckbox.disabled = false;
                }
            }
        });
    }
    
    // Search functionality
    const searchInput = document.getElementById('searchAnnouncement');
    const rows = document.querySelectorAll('tbody tr');
    
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const searchText = this.value.toLowerCase();
            
            rows.forEach(row => {
                // Skip empty rows
                if (row.cells.length <= 1) return;
                
                // Check if row matches search text
                const matchesSearch = Array.from(row.querySelectorAll('td')).some(cell => 
                    cell.textContent.toLowerCase().includes(searchText)
                );
                
                // Show/hide row
                row.style.display = matchesSearch ? '' : 'none';
            });
            
            // Show no results message if needed
            const visibleRows = document.querySelectorAll('tbody tr:not([style*="display: none"])');
            const noResultsRow = document.getElementById('no-results-row');
            
            if (visibleRows.length === 0 && !noResultsRow) {
                const tbody = document.querySelector('tbody');
                const newRow = document.createElement('tr');
                newRow.id = 'no-results-row';
                newRow.innerHTML = '<td colspan="5" class="px-6 py-4 text-center text-gray-400">No matching announcements found</td>';
                tbody.appendChild(newRow);
            } else if (visibleRows.length > 0 && noResultsRow) {
                noResultsRow.remove();
            }
        });
    }

    // Initialize state when modals are opened
    const app = document.querySelector('[x-data]')?.__x?.$data;
    if (app) {
        const originalShowAddModal = app.showAddModal;
        Object.defineProperty(app, 'showAddModal', {
            get() { return originalShowAddModal; },
            set(value) {
                originalShowAddModal = value;
                if (value) {
                    // Reset form when modal is opened
                    setTimeout(() => {
                        const form = document.getElementById('announcementForm');
                        if (form) form.reset();
                        const schedulingOptions = document.getElementById('schedulingOptions');
                        if (schedulingOptions) schedulingOptions.classList.add('hidden');
                    }, 100);
                }
            }
        });

        // Reset edit modal form when opened
        const originalShowEditModal = app.showEditModal;
        Object.defineProperty(app, 'showEditModal', {
            get() { return originalShowEditModal; },
            set(value) {
                originalShowEditModal = value;
                if (value) {
                    // Reset form and properly show/hide scheduling options based on the announcement data
                    setTimeout(() => {
                        const form = document.getElementById('editAnnouncementForm');
                        if (form) {
                            // Don't use reset() as it would remove the values set by Alpine.js
                            // Instead, let the x-bind directives populate the values
                        }
                        
                        const editScheduleLater = document.getElementById('edit_schedule_later');
                        const editSchedulingOptions = document.getElementById('edit_schedulingOptions');
                        const editScheduleNote = document.getElementById('edit_schedule_note');
                        const editIsActiveCheckbox = document.getElementById('edit_is_active');
                        const editIsScheduledFlag = document.getElementById('edit_is_scheduled_flag');
                        
                        if (editScheduleLater && app.currentAnnouncement) {
                            const hasScheduledTime = app.currentAnnouncement.scheduled_at !== null;
                            editScheduleLater.checked = hasScheduledTime;
                            
                            if (editSchedulingOptions) {
                                editSchedulingOptions.classList.toggle('hidden', !hasScheduledTime);
                            }
                            
                            if (editIsScheduledFlag) {
                                editIsScheduledFlag.value = hasScheduledTime ? '1' : '0';
                            }
                            
                            if (editScheduleNote) {
                                editScheduleNote.style.display = hasScheduledTime ? 'block' : 'none';
                            }
                            
                            // If announcement is scheduled, disable the active checkbox
                            if (editIsActiveCheckbox && hasScheduledTime) {
                                editIsActiveCheckbox.checked = false;
                                editIsActiveCheckbox.disabled = true;
                            } else if (editIsActiveCheckbox) {
                                editIsActiveCheckbox.disabled = false;
                            }
                        }
                    }, 100);
                }
            }
        });
    }

    // Handle form submissions
    const announcementForm = document.getElementById('announcementForm');
    if (announcementForm) {
        announcementForm.addEventListener('submit', function(e) {
            if (!formIsValid()) {
                e.preventDefault();
                Swal.fire({
                    title: 'Error!',
                    text: 'Please fill all required fields.',
                    icon: 'error',
                    background: '#1F2937',
                    color: '#FFFFFF',
                    confirmButtonColor: '#DC2626'
                });
            }
        });
    }

    const editAnnouncementForm = document.getElementById('editAnnouncementForm');
    if (editAnnouncementForm) {
        editAnnouncementForm.addEventListener('submit', function(e) {
            if (!formIsValid()) {
                e.preventDefault();
                Swal.fire({
                    title: 'Error!',
                    text: 'Please fill all required fields.',
                    icon: 'error',
                    background: '#1F2937',
                    color: '#FFFFFF',
                    confirmButtonColor: '#DC2626'
                });
            }
        });
    }

    // Function to validate forms
    function formIsValid() {
        // Validate create announcement form
        if (document.getElementById('announcementForm') && document.getElementById('announcementForm').checkValidity()) {
            // Check if scheduling is enabled, then validate scheduling fields
            const scheduleLater = document.getElementById('schedule_later');
            if (scheduleLater && scheduleLater.checked) {
                const scheduleDate = document.getElementById('schedule_date');
                const scheduleTime = document.getElementById('schedule_time');
                
                // Set default values for scheduling
                const tomorrow = new Date();
                tomorrow.setDate(tomorrow.getDate() + 1);
                
                // Format tomorrow's date as YYYY-MM-DD
                const formattedDate = tomorrow.toISOString().split('T')[0];
                
                if (scheduleDate) {
                    scheduleDate.min = formattedDate;
                }
                
                if (!scheduleDate.value || !scheduleTime.value) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Please select both date and time for scheduling.',
                        icon: 'error',
                        background: '#1F2937',
                        color: '#FFFFFF',
                        confirmButtonColor: '#DC2626'
                    });
                    return false;
                }
                
                // Validate that scheduled date/time is in the future
                const scheduledDateTime = new Date(scheduleDate.value + 'T' + scheduleTime.value);
                const now = new Date();
                
                if (scheduledDateTime <= now) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Scheduled time must be in the future.',
                        icon: 'error',
                        background: '#1F2937',
                        color: '#FFFFFF',
                        confirmButtonColor: '#DC2626'
                    });
                    return false;
                }
            }
            
            return true;
        }
        
        // Validate edit announcement form
        if (document.getElementById('editAnnouncementForm') && document.getElementById('editAnnouncementForm').checkValidity()) {
            // Check if scheduling is enabled, then validate scheduling fields
            const editScheduleLater = document.getElementById('edit_schedule_later');
            if (editScheduleLater && editScheduleLater.checked) {
                const editScheduleDate = document.getElementById('edit_schedule_date');
                const editScheduleTime = document.getElementById('edit_schedule_time');
                
                if (!editScheduleDate.value || !editScheduleTime.value) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Please select both date and time for scheduling.',
                        icon: 'error',
                        background: '#1F2937',
                        color: '#FFFFFF',
                        confirmButtonColor: '#DC2626'
                    });
                    return false;
                }
                
                // Validate that scheduled date/time is in the future
                const scheduledDateTime = new Date(editScheduleDate.value + 'T' + editScheduleTime.value);
                const now = new Date();
                
                if (scheduledDateTime <= now) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Scheduled time must be in the future.',
                        icon: 'error',
                        background: '#1F2937',
                        color: '#FFFFFF',
                        confirmButtonColor: '#DC2626'
                    });
                    return false;
                }
            }
            
            return true;
        }
        
        return false;
    }

    // Show success message if it exists in the session
    @if(session('success'))
        Swal.fire({
            title: 'Success!',
            text: "{{ session('success') }}",
            icon: 'success',
            background: '#1F2937',
            color: '#FFFFFF',
            confirmButtonColor: '#DC2626',
            customClass: {
                popup: 'swal-popup-custom',
                confirmButton: 'swal-btn-custom'
            }
        });
    @endif
    
    @if(session('error'))
        Swal.fire({
            title: 'Error!',
            text: "{{ session('error') }}",
            icon: 'error',
            background: '#1F2937',
            color: '#FFFFFF',
            confirmButtonColor: '#DC2626',
            customClass: {
                popup: 'swal-popup-custom',
                confirmButton: 'swal-btn-custom'
            }
        });
    @endif
});

// Update the confirmDelete function to use direct URL
function confirmDelete(id, title) {
    Swal.fire({
        title: 'Delete Announcement?',
        text: `Are you sure you want to delete "${title}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#4b5563',
        confirmButtonText: 'Yes, delete it!',
        background: '#1F2937',
        color: '#FFFFFF'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/admin/announcements/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: data.message,
                        background: '#1F2937',
                        color: '#FFFFFF',
                        confirmButtonColor: '#DC2626'
                    }).then(() => {
                        location.reload();
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while deleting the announcement.',
                    background: '#1F2937',
                    color: '#FFFFFF',
                    confirmButtonColor: '#DC2626'
                });
            });
        }
    });
}

// Update the toggleStatus function to handle toggle switches
// Initialize filter functionality
function initDateFilter() {
    const dateFilter = document.getElementById('dateFilter');
    const clearDateFilter = document.getElementById('clearDateFilter');
    const searchInput = document.getElementById('searchAnnouncement');
    const statusFilter = document.getElementById('statusFilter');
    const resetAllFilters = document.getElementById('resetAllFilters');
    
    if (dateFilter && clearDateFilter) {
        // Allow selection of any date for filtering (including past dates)
        
        // Filter announcements when date changes
        dateFilter.addEventListener('change', function() {
            filterAnnouncements();
        });
        
        // Clear date filter when clear button is clicked
        clearDateFilter.addEventListener('click', function() {
            dateFilter.value = '';
            filterAnnouncements();
        });
    }
    
    // Filter announcements when search input changes
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            filterAnnouncements();
        });
    }
    
    // Status filter functionality
    if (statusFilter) {
        statusFilter.addEventListener('change', function() {
            filterAnnouncements();
        });
    }
    
    // Reset all filters
    if (resetAllFilters) {
        resetAllFilters.addEventListener('click', function() {
            if (searchInput) searchInput.value = '';
            if (dateFilter) dateFilter.value = '';
            if (statusFilter) statusFilter.value = '';
            filterAnnouncements();
            
            // Show reset toast notification
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'All filters have been reset',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true,
                background: '#1F2937',
                color: '#FFFFFF'
            });
        });
    }
}

// Filter announcements based on search text, date, and status
function filterAnnouncements() {
    const searchText = document.getElementById('searchAnnouncement').value.toLowerCase();
    const filterDate = document.getElementById('dateFilter').value;
    const statusFilter = document.getElementById('statusFilter').value.toLowerCase();
    const rows = document.querySelectorAll('tbody tr');
    let visibleCount = 0;
    
    // Remove existing no-results row if it exists
    const noResultsRow = document.getElementById('no-results-row');
    if (noResultsRow) {
        noResultsRow.remove();
    }
    
    rows.forEach(row => {
        // Skip rows that are not announcement rows (like "no announcements found")
        if (row.cells.length <= 1) return;
        
        const title = row.cells[0].textContent.toLowerCase();
        const date = row.cells[1].textContent;
        
        // Get status from the status cell
        const statusCell = row.cells[2];
        let status = '';
        
        if (statusCell) {
            const statusText = statusCell.textContent.trim().toLowerCase();
            status = statusText;
        }
        
        const matchesSearch = !searchText || title.includes(searchText);
        
        // Improved date matching logic
        let matchesDate = true;
        if (filterDate) {
            try {
                // Convert the filter date to a format we can compare
                const filterDateObj = new Date(filterDate);
                
                // Try to extract a date from the cell text (assuming MM/DD/YYYY format)
                const dateMatch = date.match(/(\d{1,2})\/(\d{1,2})\/(\d{4})/);
                if (dateMatch) {
                    const month = parseInt(dateMatch[1]) - 1; // JS months are 0-indexed
                    const day = parseInt(dateMatch[2]);
                    const year = parseInt(dateMatch[3]);
                    const rowDate = new Date(year, month, day);
                    
                    // Compare year, month, and day directly
                    matchesDate = (
                        rowDate.getFullYear() === filterDateObj.getFullYear() &&
                        rowDate.getMonth() === filterDateObj.getMonth() &&
                        rowDate.getDate() === filterDateObj.getDate()
                    );
                } else {
                    matchesDate = false;
                }
            } catch (e) {
                console.error('Date parsing error:', e);
                matchesDate = false;
            }
        }
        
        const matchesStatus = !statusFilter || status.includes(statusFilter);
        
        if (matchesSearch && matchesDate && matchesStatus) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    
    // Show "No results" message if all rows are hidden
    if (visibleCount === 0 && rows.length > 0) {
        const tbody = document.querySelector('tbody');
        if (tbody) {
            const noResultsRow = document.createElement('tr');
            noResultsRow.id = 'no-results-row';
            noResultsRow.innerHTML = '<td colspan="5" class="px-6 py-4 text-center text-gray-400">No matching announcements found</td>';
            tbody.appendChild(noResultsRow);
        }
    }
}

function toggleStatus(id) {
    // Find the toggle switch, label, and status badge for this announcement
    const toggleSwitch = document.querySelector(`tr[data-id="${id}"] .switch input`);
    const statusLabel = document.querySelector(`tr[data-id="${id}"] .status-label`);
    const statusCell = document.querySelector(`tr[data-id="${id}"] td:nth-child(3) span`);
    
    // The current checked state before we send to server
    const currentChecked = toggleSwitch.checked;
    
    // Store the text of the status cell to check if it's in pending state
    const currentStatus = statusCell ? statusCell.textContent.trim() : '';
    
    // Check if the announcement is in pending state (scheduled)
    if (currentStatus === 'Pending') {
        toggleSwitch.checked = false; // Revert toggle switch
        Swal.fire({
            icon: 'warning',
            title: 'Cannot Toggle',
            text: 'Scheduled announcements cannot be activated manually. They will automatically activate at the scheduled time.',
            background: '#1F2937',
            color: '#FFFFFF',
            confirmButtonColor: '#DC2626'
        });
        return; // Stop execution
    }
    
    // We're going to send the current checked state as our intended state
    // If the switch is checked, we want to activate, if unchecked, deactivate
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    
    // Temporarily disable the switch until we get a response
    toggleSwitch.disabled = true;
    
    // Make API request to toggle status
    fetch(`/admin/announcements/${id}/toggle-active`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ 
            is_active: currentChecked ? 'active' : 'inactive',
            status: currentStatus
        })
    })
    .then(res => {
        if (!res.ok) throw new Error('Request failed');
        return res.json();
    })
    .then(data => {
        if (data.success) {
            // For successful responses, we keep the switch state as it is now
            // (user toggled it before we sent the request)
            
            // Update status label next to toggle switch
            if (statusLabel) {
                statusLabel.textContent = currentChecked ? 'Active' : 'Inactive';
                statusLabel.className = `status-label text-xs ${currentChecked ? 'text-green-400' : 'text-gray-400'}`;
            }
            
            // Update status badge in status column
            if (statusCell) {
                statusCell.textContent = currentChecked ? 'Active' : 'Inactive';
                statusCell.className = `px-2 py-1 text-xs rounded-full ${currentChecked ? 'bg-green-500' : 'bg-gray-500'}`;
            }
            
            // Show success notification with correct message
            Swal.fire({
                icon: 'success',
                title: 'Status Updated',
                text: `Announcement ${currentChecked ? 'activated' : 'deactivated'} successfully.`,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                background: '#1F2937',
                color: '#FFFFFF'
            });
        } else {
            throw new Error(data.message || 'Operation failed');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        
        // On error, revert the switch to its original state (opposite of what user clicked)
        toggleSwitch.checked = !currentChecked;
        
        // Show error notification
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Failed to update announcement status. Please try again.',
            background: '#1F2937',
            color: '#FFFFFF',
            confirmButtonColor: '#DC2626'
        });
    })
    .finally(() => {
        // Re-enable the switch
        toggleSwitch.disabled = false;
    });
}

</script>

@endsection