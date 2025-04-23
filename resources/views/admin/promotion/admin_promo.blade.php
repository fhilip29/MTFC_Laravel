@extends('layouts.admin')

@section('title', 'Announcement Management')

@section('content')
<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
                <p class="text-2xl font-bold text-white">{{ $announcements->where('is_active', true)->count() }}</p>
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
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @forelse($announcements as $announcement)
                    <tr class="hover:bg-gray-700 transition" data-id="{{ $announcement->id }}">
                        <td class="px-6 py-4 whitespace-nowrap font-medium">{{ $announcement->title }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $announcement->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full {{ $announcement->statusClass }}">
                                {{ $announcement->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex space-x-2">
                                <button @click="openViewModal({{ $announcement }})" class="text-blue-400 hover:text-blue-300 view-announcement" data-id="{{ $announcement->id }}">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button @click="openEditModal({{ $announcement }})" class="text-yellow-400 hover:text-yellow-300 edit-announcement" data-id="{{ $announcement->id }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button @click="confirmDelete('{{ $announcement->id }}', '{{ $announcement->title }}')" class="text-red-400 hover:text-red-300 delete-announcement" data-id="{{ $announcement->id }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <button @click="toggleStatus('{{ $announcement->id }}')" class="toggle-status {{ $announcement->is_active ? 'text-green-400 hover:text-green-300' : 'text-gray-400 hover:text-gray-300' }}" data-id="{{ $announcement->id }}">
                                    <i class="fas {{ $announcement->is_active ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-400">
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
                <div class="space-y-4">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-300 mb-1">Title</label>
                        <input type="text" name="title" id="title" required class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5">
                        <div class="text-red-500 text-xs mt-1">@error('title'){{ $message }}@enderror</div>
                    </div>
                    
                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-300 mb-1">Message</label>
                        <textarea name="message" id="message" rows="5" required class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5"></textarea>
                        <div class="text-red-500 text-xs mt-1">@error('message'){{ $message }}@enderror</div>
                    </div>
                    
                    <div class="flex items-center">
                        <input type="checkbox" name="is_active" id="is_active" checked class="w-4 h-4 text-red-600 bg-gray-700 border-gray-600 rounded focus:ring-red-500">
                        <label for="is_active" class="ml-2 text-sm font-medium text-gray-300">Make announcement active</label>
                    </div>
                    
                    <div class="flex items-center">
                        <input type="checkbox" name="schedule_later" id="schedule_later" class="w-4 h-4 text-red-600 bg-gray-700 border-gray-600 rounded focus:ring-red-500">
                        <label for="schedule_later" class="ml-2 text-sm font-medium text-gray-300">Schedule for later</label>
                    </div>
                    
                    <div id="schedulingOptions" class="hidden space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="schedule_date" class="block text-sm font-medium text-gray-300 mb-1">Date</label>
                                <input type="date" name="schedule_date" id="schedule_date" class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5">
                                <div class="text-red-500 text-xs mt-1">@error('schedule_date'){{ $message }}@enderror</div>
                            </div>
                            <div>
                                <label for="schedule_time" class="block text-sm font-medium text-gray-300 mb-1">Time</label>
                                <input type="time" name="schedule_time" id="schedule_time" class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5">
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
                <div class="space-y-4">
                    <div>
                        <label for="edit_title" class="block text-sm font-medium text-gray-300 mb-1">Title</label>
                        <input type="text" name="title" id="edit_title" required class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5" x-bind:value="currentAnnouncement?.title">
                        <div class="text-red-500 text-xs mt-1">@error('title'){{ $message }}@enderror</div>
                    </div>
                    
                    <div>
                        <label for="edit_message" class="block text-sm font-medium text-gray-300 mb-1">Message</label>
                        <textarea name="message" id="edit_message" rows="5" required class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5" x-text="currentAnnouncement?.message"></textarea>
                        <div class="text-red-500 text-xs mt-1">@error('message'){{ $message }}@enderror</div>
                    </div>
                    
                    <div class="flex items-center">
                        <input type="checkbox" name="is_active" id="edit_is_active" class="w-4 h-4 text-red-600 bg-gray-700 border-gray-600 rounded focus:ring-red-500" x-bind:checked="currentAnnouncement?.is_active">
                        <label for="edit_is_active" class="ml-2 text-sm font-medium text-gray-300">Make announcement active</label>
                    </div>
                    
                    <div class="flex items-center">
                        <input type="checkbox" name="schedule_later" id="edit_schedule_later" class="w-4 h-4 text-red-600 bg-gray-700 border-gray-600 rounded focus:ring-red-500" x-bind:checked="currentAnnouncement?.scheduled_at !== null">
                        <label for="edit_schedule_later" class="ml-2 text-sm font-medium text-gray-300">Schedule for later</label>
                    </div>
                    
                    <div id="editSchedulingOptions" class="hidden space-y-4" x-bind:class="{ 'hidden': !currentAnnouncement?.scheduled_at }">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="edit_schedule_date" class="block text-sm font-medium text-gray-300 mb-1">Date</label>
                                <input type="date" name="schedule_date" id="edit_schedule_date" class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5" x-bind:value="currentAnnouncement?.scheduled_at ? currentAnnouncement.scheduled_at.split('T')[0] : ''">
                                <div class="text-red-500 text-xs mt-1">@error('schedule_date'){{ $message }}@enderror</div>
                            </div>
                            <div>
                                <label for="edit_schedule_time" class="block text-sm font-medium text-gray-300 mb-1">Time</label>
                                <input type="time" name="schedule_time" id="edit_schedule_time" class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5" x-bind:value="currentAnnouncement?.scheduled_at ? currentAnnouncement.scheduled_at.split('T')[1].substring(0, 5) : ''">
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
                        <p class="text-sm"><span class="font-medium">Status:</span> 
                            <span x-show="currentAnnouncement?.is_active" class="px-2 py-1 text-xs rounded-full bg-green-600 text-white">Active</span>
                            <span x-show="!currentAnnouncement?.is_active" class="px-2 py-1 text-xs rounded-full bg-gray-600 text-white">Inactive</span>
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
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle scheduling options in Add modal
    const scheduleLater = document.getElementById('schedule_later');
    const schedulingOptions = document.getElementById('schedulingOptions');
    
    if (scheduleLater && schedulingOptions) {
        scheduleLater.addEventListener('change', function() {
            schedulingOptions.classList.toggle('hidden', !this.checked);
        });
    }
    
    // Toggle scheduling options in Edit modal
    const editScheduleLater = document.getElementById('edit_schedule_later');
    const editSchedulingOptions = document.getElementById('editSchedulingOptions');
    
    if (editScheduleLater && editSchedulingOptions) {
        editScheduleLater.addEventListener('change', function() {
            editSchedulingOptions.classList.toggle('hidden', !this.checked);
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
                newRow.innerHTML = '<td colspan="4" class="px-6 py-4 text-center text-gray-400">No matching announcements found</td>';
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
    }
    
    // Handle form submissions
    const announcementForm = document.getElementById('announcementForm');
    if (announcementForm) {
        announcementForm.addEventListener('submit', function(e) {
            // Native form submission - no need for preventDefault()
        });
    }
    
    const editAnnouncementForm = document.getElementById('editAnnouncementForm');
    if (editAnnouncementForm) {
        editAnnouncementForm.addEventListener('submit', function(e) {
            // Native form submission - no need for preventDefault()
        });
    }
    
    // Show success message if it exists in the session
    @if(session('success'))
        Swal.fire({
            title: 'Success!',
            text: "{{ session('success') }}",
            icon: 'success',
            background: '#1F2937',
            color: '#FFFFFF',
            confirmButtonColor: '#DC2626'
        });
    @endif
    
    @if(session('error'))
        Swal.fire({
            title: 'Error!',
            text: "{{ session('error') }}",
            icon: 'error',
            background: '#1F2937',
            color: '#FFFFFF',
            confirmButtonColor: '#DC2626'
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

// Update the toggleStatus function to use direct URL
function toggleStatus(id) {
    fetch(`/admin/announcements/${id}/toggle-active`, {
        method: 'PATCH',
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
                title: 'Status Updated!',
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
            text: 'An error occurred while updating the announcement status.',
            background: '#1F2937',
            color: '#FFFFFF',
            confirmButtonColor: '#DC2626'
        });
    });
}
</script>
@endsection 