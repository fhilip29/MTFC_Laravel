@extends('layouts.admin')

@section('title', 'Announcement Management')

@section('content')
<div class="container mx-auto px-2 sm:px-4 py-4 sm:py-8">
    <div class="bg-[#1F2937] p-4 sm:p-6 rounded-2xl shadow-md border border-[#374151]">
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4 sm:gap-6 mb-6">
            <h1 class="text-xl sm:text-2xl font-bold text-white flex items-center gap-2 w-full sm:w-auto">
                <i class="fas fa-bullhorn text-[#9CA3AF]"></i> Announcement Management
            </h1>
            <div class="flex gap-4 w-full sm:w-auto">
                <div class="relative flex-grow sm:flex-grow-0">
                    <input 
                        type="text" 
                        id="searchAnnouncement"
                        placeholder="Search announcements..." 
                        class="w-full pl-10 pr-4 py-2 bg-[#374151] border border-[#4B5563] text-white rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-[#9CA3AF] placeholder-[#9CA3AF]"
                    >
                    <i class="fas fa-search absolute left-3 top-3 text-[#9CA3AF]"></i>
                </div>
                <button type="button" id="createAnnouncementBtn" class="bg-red-600 hover:bg-red-700 text-white font-semibold flex items-center gap-2 px-4 py-2 rounded-lg shadow transition-colors">
                    <i class="fas fa-plus"></i> <span class="hidden sm:inline">Create Announcement</span>
                </button>
            </div>
        </div>

        <!-- Announcements Table -->
        <div class="overflow-x-auto rounded-lg shadow-sm">
            <div class="inline-block min-w-full align-middle">
                <table class="min-w-full divide-y divide-[#374151]">
                    <thead class="bg-[#374151]">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-[#9CA3AF] uppercase tracking-wider">Title</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-[#9CA3AF] uppercase tracking-wider">Date</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-[#9CA3AF] uppercase tracking-wider">Target Audience</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-[#9CA3AF] uppercase tracking-wider">Delivery</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-[#9CA3AF] uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-[#9CA3AF] uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-[#1F2937] divide-y divide-[#374151]" id="announcementsTableBody">
                        @foreach($announcements as $announcement)
                        <tr class="hover:bg-[#374151] transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-white">{{ $announcement['title'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-[#9CA3AF]">{{ $announcement['date'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-[#9CA3AF]">{{ $announcement['sent_to'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-[#9CA3AF]">{{ $announcement['method'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $announcement['status_class'] }}">
                                    {{ $announcement['status'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                <div class="flex justify-center space-x-3">
                                    <button type="button"
                                        class="text-blue-400 hover:text-blue-300 transition-colors view-btn"
                                        data-id="{{ $announcement['id'] }}"
                                        title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button type="button"
                                        class="text-yellow-400 hover:text-yellow-300 transition-colors edit-btn"
                                        data-id="{{ $announcement['id'] }}"
                                        title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button"
                                        class="text-red-400 hover:text-red-300 transition-colors delete-btn"
                                        data-id="{{ $announcement['id'] }}"
                                        title="Delete">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                    <button type="button"
                                        class="text-green-400 hover:text-green-300 transition-colors toggle-btn"
                                        data-id="{{ $announcement['id'] }}"
                                        title="Toggle Status">
                                        <i class="fas fa-toggle-on"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Announcement Modal -->
<div id="announcementModal" class="fixed inset-0 z-50 hidden overflow-y-auto" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity"></div>
        
        <div class="relative bg-[#1F2937] rounded-lg max-w-2xl w-full shadow-xl transform transition-all border border-[#374151]">
            <form id="announcementForm" class="relative">
                @csrf
                <input type="hidden" id="announcement_id" name="id">
                
                <!-- Modal Header -->
                <div class="px-6 py-4 border-b border-[#374151] flex items-center justify-between">
                    <h3 class="text-lg font-medium text-white" id="modalTitle">Create New Announcement</h3>
                    <button type="button" class="text-[#9CA3AF] hover:text-white close-modal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <!-- Modal Body -->
                <div class="px-6 py-4 space-y-4">
                    <div>
                        <label for="title" class="block text-sm font-medium text-[#9CA3AF] mb-1">Title</label>
                        <input type="text" id="title" name="title" required
                            class="w-full rounded-md bg-[#374151] border-[#4B5563] text-white focus:ring-red-500 focus:border-red-500">
                    </div>
                    
                    <div>
                        <label for="message" class="block text-sm font-medium text-[#9CA3AF] mb-1">Message</label>
                        <textarea id="message" name="message" rows="4" required
                            class="w-full rounded-md bg-[#374151] border-[#4B5563] text-white focus:ring-red-500 focus:border-red-500"></textarea>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="target_audience" class="block text-sm font-medium text-[#9CA3AF] mb-1">Target Audience</label>
                            <select id="target_audience" name="target_audience" required
                                class="w-full rounded-md bg-[#374151] border-[#4B5563] text-white focus:ring-red-500 focus:border-red-500">
                                <option value="all">All Users</option>
                                <option value="active">Active Members</option>
                                <option value="trainers">Trainers Only</option>
                                <option value="staff">Admin Only</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-[#9CA3AF] mb-1">Delivery Method</label>
                            <div class="space-y-2">
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="send_in_app" checked
                                        class="rounded bg-[#374151] border-[#4B5563] text-red-600 focus:ring-red-500">
                                    <span class="ml-2 text-white text-sm">In-App Notification</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="send_email"
                                        class="rounded bg-[#374151] border-[#4B5563] text-red-600 focus:ring-red-500">
                                    <span class="ml-2 text-white text-sm">Email</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <label class="inline-flex items-center">
                            <input type="checkbox" id="schedule_later" name="schedule_later"
                                class="rounded bg-[#374151] border-[#4B5563] text-red-600 focus:ring-red-500">
                            <span class="ml-2 text-white text-sm">Schedule for later</span>
                        </label>
                    </div>
                    
                    <div id="scheduleFields" class="hidden grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="schedule_date" class="block text-sm font-medium text-[#9CA3AF] mb-1">Date</label>
                            <input type="date" id="schedule_date" name="schedule_date"
                                class="w-full rounded-md bg-[#374151] border-[#4B5563] text-white focus:ring-red-500 focus:border-red-500">
                        </div>
                        <div>
                            <label for="schedule_time" class="block text-sm font-medium text-[#9CA3AF] mb-1">Time</label>
                            <input type="time" id="schedule_time" name="schedule_time"
                                class="w-full rounded-md bg-[#374151] border-[#4B5563] text-white focus:ring-red-500 focus:border-red-500">
                        </div>
                    </div>
                </div>
                
                <!-- Modal Footer -->
                <div class="px-6 py-4 border-t border-[#374151] flex justify-end space-x-3">
                    <button type="button" class="close-modal px-4 py-2 text-sm font-medium text-[#9CA3AF] hover:text-white bg-transparent border border-[#4B5563] rounded-md hover:bg-[#374151] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#4B5563]">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Save Announcement
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Simple direct DOM access
    var modal = document.getElementById('announcementModal');
    var createBtn = document.getElementById('createAnnouncementBtn');
    var closeButtons = document.querySelectorAll('.close-modal');
    var form = document.getElementById('announcementForm');
    var scheduleLater = document.getElementById('schedule_later');
    var scheduleFields = document.getElementById('scheduleFields');
    var searchInput = document.getElementById('searchAnnouncement');
    
    // Open modal
    createBtn.onclick = function() {
        console.log('Create button clicked');
        modal.classList.remove('hidden');
        form.reset();
        document.getElementById('modalTitle').textContent = 'Create New Announcement';
        document.getElementById('announcement_id').value = '';
        scheduleFields.classList.add('hidden');
    };
    
    // Close modal
    closeButtons.forEach(function(button) {
        button.onclick = function() {
            modal.classList.add('hidden');
        };
    });
    
    // Close modal when clicking outside
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.classList.add('hidden');
        }
    };
    
    // Toggle schedule fields
    scheduleLater.onchange = function() {
        scheduleFields.classList.toggle('hidden', !this.checked);
    };
    
    // Search functionality
    searchInput.oninput = function() {
        var searchTerm = this.value.toLowerCase();
        var rows = document.querySelectorAll('#announcementsTableBody tr');
        
        rows.forEach(function(row) {
            var text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    };
    
    // Form submission
    form.onsubmit = function(e) {
        e.preventDefault();
        
        var formData = new FormData(form);
        var id = formData.get('id');
        var url = id ? '/admin/announcements/' + id : '/admin/announcements';
        var method = id ? 'PUT' : 'POST';
        
        // Convert FormData to object
        var formObject = {};
        formData.forEach(function(value, key) {
            // Handle checkboxes
            if (key === 'send_in_app' || key === 'send_email' || key === 'schedule_later') {
                formObject[key] = value === 'on';
            } else {
                formObject[key] = value;
            }
        });
        
        // Ajax request
        fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify(formObject)
        })
        .then(function(response) {
            return response.json();
        })
        .then(function(data) {
            if (data.success) {
                Swal.fire({
                    title: 'Success!',
                    text: data.message || 'Announcement saved successfully',
                    icon: 'success',
                    confirmButtonColor: '#DC2626',
                    background: '#1F2937',
                    color: '#FFFFFF'
                }).then(function() {
                    window.location.reload();
                });
            } else {
                throw new Error(data.message || 'Something went wrong');
            }
        })
        .catch(function(error) {
            console.error('Error:', error);
            Swal.fire({
                title: 'Error!',
                text: error.message || 'Something went wrong',
                icon: 'error',
                confirmButtonColor: '#DC2626',
                background: '#1F2937',
                color: '#FFFFFF'
            });
        });
    };
    
    // Setup view buttons
    document.querySelectorAll('.view-btn').forEach(function(button) {
        button.onclick = function() {
            var id = this.getAttribute('data-id');
            viewAnnouncement(id);
        };
    });
    
    // Setup edit buttons
    document.querySelectorAll('.edit-btn').forEach(function(button) {
        button.onclick = function() {
            var id = this.getAttribute('data-id');
            editAnnouncement(id);
        };
    });
    
    // Setup delete buttons
    document.querySelectorAll('.delete-btn').forEach(function(button) {
        button.onclick = function() {
            var id = this.getAttribute('data-id');
            deleteAnnouncement(id);
        };
    });
    
    // Setup toggle buttons
    document.querySelectorAll('.toggle-btn').forEach(function(button) {
        button.onclick = function() {
            var id = this.getAttribute('data-id');
            toggleAnnouncementStatus(id);
        };
    });
});

// View announcement
function viewAnnouncement(id) {
    fetch('/admin/announcements/' + id)
        .then(function(response) {
            return response.json();
        })
        .then(function(data) {
            if (data.success) {
                Swal.fire({
                    title: data.announcement.title,
                    html: `
                        <div class="text-left">
                            <p class="mb-4">${data.announcement.message}</p>
                            <p class="text-sm text-gray-400">Target: ${data.announcement.target_audience}</p>
                            <p class="text-sm text-gray-400">Created: ${new Date(data.announcement.created_at).toLocaleDateString()}</p>
                        </div>
                    `,
                    confirmButtonColor: '#DC2626',
                    background: '#1F2937',
                    color: '#FFFFFF'
                });
            }
        })
        .catch(function(error) {
            console.error('Error:', error);
        });
}

// Edit announcement
function editAnnouncement(id) {
    fetch('/admin/announcements/' + id + '/edit')
        .then(function(response) {
            return response.json();
        })
        .then(function(data) {
            if (data.success) {
                var modal = document.getElementById('announcementModal');
                var form = document.getElementById('announcementForm');
                
                // Set form values
                document.getElementById('modalTitle').textContent = 'Edit Announcement';
                document.getElementById('announcement_id').value = data.announcement.id;
                document.getElementById('title').value = data.announcement.title;
                document.getElementById('message').value = data.announcement.message;
                document.getElementById('target_audience').value = data.announcement.target_audience;
                
                // Checkboxes
                form.querySelector('[name="send_in_app"]').checked = data.announcement.send_in_app;
                form.querySelector('[name="send_email"]').checked = data.announcement.send_email;
                
                // Schedule fields
                var scheduleLater = document.getElementById('schedule_later');
                var scheduleFields = document.getElementById('scheduleFields');
                
                if (data.announcement.scheduled_at) {
                    scheduleLater.checked = true;
                    scheduleFields.classList.remove('hidden');
                    
                    var date = new Date(data.announcement.scheduled_at);
                    var dateString = date.toISOString().split('T')[0];
                    var timeString = date.toTimeString().slice(0, 5);
                    
                    document.getElementById('schedule_date').value = dateString;
                    document.getElementById('schedule_time').value = timeString;
                } else {
                    scheduleLater.checked = false;
                    scheduleFields.classList.add('hidden');
                }
                
                // Show modal
                modal.classList.remove('hidden');
            }
        })
        .catch(function(error) {
            console.error('Error:', error);
        });
}

// Delete announcement
function deleteAnnouncement(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "This action cannot be undone!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#DC2626',
        cancelButtonColor: '#374151',
        confirmButtonText: 'Yes, delete it!',
        background: '#1F2937',
        color: '#FFFFFF'
    }).then(function(result) {
        if (result.isConfirmed) {
            fetch('/admin/announcements/' + id, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(function(response) {
                return response.json();
            })
            .then(function(data) {
                if (data.success) {
                    Swal.fire({
                        title: 'Deleted!',
                        text: data.message,
                        icon: 'success',
                        confirmButtonColor: '#DC2626',
                        background: '#1F2937',
                        color: '#FFFFFF'
                    }).then(function() {
                        window.location.reload();
                    });
                }
            })
            .catch(function(error) {
                console.error('Error:', error);
            });
        }
    });
}

// Toggle announcement status
function toggleAnnouncementStatus(id) {
    fetch('/admin/announcements/' + id + '/toggle-active', {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(function(response) {
        return response.json();
    })
    .then(function(data) {
        if (data.success) {
            window.location.reload();
        }
    })
    .catch(function(error) {
        console.error('Error:', error);
    });
}
</script>
@endpush 