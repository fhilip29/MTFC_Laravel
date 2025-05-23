@extends('layouts.admin')
@section('title', 'Announcement Management')

@section('content')
<style>
    /* Custom styling for the toggle switch */
    .form-check-input.toggle-status {
        width: 3em;
        height: 1.5em;
        cursor: pointer;
    }
    
    .form-check-input.toggle-status:checked {
        background-color: #198754;
        border-color: #198754;
    }
    
    .form-check-input.toggle-status:focus {
        box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.25);
    }
    
    td .form-check.form-switch {
        margin-bottom: 0;
    }
    
    .form-check-label {
        font-weight: 500;
    }
</style>

<div class="container-fluid px-4">
    <h1 class="mt-4">Announcement Management</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Announcements</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-bullhorn me-1"></i>
                Announcements
            </div>
            <div class="d-flex align-items-center">
                <div class="d-flex">
                    <div class="input-group me-2" style="width: 300px;">
                        <input type="text" class="form-control" id="searchAnnouncement" placeholder="Search announcements...">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                    </div>
                    <div class="input-group me-2" style="width: 200px;">
                        <input type="date" class="form-control" id="dateFilter" placeholder="Filter by date">
                        <button class="btn btn-outline-secondary" type="button" id="clearDateFilter" title="Clear date filter">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <button type="button" class="btn btn-success" id="addAnnouncementBtn">
                    <i class="fas fa-plus"></i> Create Announcement
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="announcementsTable">
                    <thead class="table-dark">
                        <tr>
                            <th>Title</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($announcements as $announcement)
                        <tr data-id="{{ $announcement->id }}">
                            <td>{{ $announcement->title }}</td>
                            <td>{{ $announcement->created_at->format('Y-m-d') }}</td>
                            <td>
                                <div class="form-check form-switch d-flex justify-content-center">
                                    <input class="form-check-input toggle-status" type="checkbox" role="switch" 
                                           id="statusSwitch{{ $announcement->id }}" 
                                           data-id="{{ $announcement->id }}" 
                                           {{ $announcement->is_active ? 'checked' : '' }}>
                                    <label class="form-check-label ms-2" for="statusSwitch{{ $announcement->id }}">
                                        {{ $announcement->is_active ? 'Active' : 'Inactive' }}
                                    </label>
                                </div>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-info view-announcement" data-id="{{ $announcement->id }}">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-primary edit-announcement" data-id="{{ $announcement->id }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-danger delete-announcement" data-id="{{ $announcement->id }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center">No announcements found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Announcement Modal -->
<div class="modal fade" id="announcementModal" tabindex="-1" aria-labelledby="announcementModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="announcementModalLabel">Create Announcement</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="announcementForm">
                    <input type="hidden" id="announcement_id">
                    
                    <div class="mb-3">
                        <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="title" name="title" 
                               placeholder="Enter announcement title" 
                               maxlength="100" 
                               required>
                        <small class="form-text text-muted">Required. Enter a clear, concise title (max 100 characters)</small>
                        <div class="invalid-feedback" id="title-error"></div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="message" class="form-label">Message <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="message" name="message" 
                                  rows="5" 
                                  placeholder="Enter detailed announcement message" 
                                  required></textarea>
                        <small class="form-text text-muted">Required. Provide a detailed message for the announcement</small>
                        <div class="invalid-feedback" id="message-error"></div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                            <label class="form-check-label" for="is_active">
                                Make announcement active
                            </label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="schedule_later" name="schedule_later">
                            <label class="form-check-label" for="schedule_later">
                                Schedule for later
                            </label>
                        </div>
                    </div>
                    
                    <div id="schedulingOptions" class="mb-3 d-none">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="schedule_date" class="form-label">Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="schedule_date" name="schedule_date" min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                                <div class="invalid-feedback" id="schedule_date-error"></div>
                                <small class="form-text text-muted">Select a future date (tomorrow or later)</small>
                            </div>
                            <div class="col-md-6">
                                <label for="schedule_time" class="form-label">Time <span class="text-danger">*</span></label>
                                <input type="time" class="form-control" id="schedule_time" name="schedule_time">
                                <div class="invalid-feedback" id="schedule_time-error"></div>
                                <small class="form-text text-muted">Select the time when the announcement should be published</small>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveAnnouncement">Save Announcement</button>
            </div>
        </div>
    </div>
</div>

<!-- View Announcement Modal -->
<div class="modal fade" id="viewAnnouncementModal" tabindex="-1" aria-labelledby="viewAnnouncementModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="viewAnnouncementModalLabel">View Announcement</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <h4 id="view-title"></h4>
                    <hr>
                    <div id="view-message" class="p-3 bg-light rounded"></div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Status:</strong> <span id="view-status"></span></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Created:</strong> <span id="view-created"></span></p>
                    </div>
                </div>
                <div id="view-scheduled-section" class="d-none">
                    <p><strong>Scheduled for:</strong> <span id="view-scheduled"></span></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Set minimum date for scheduling (tomorrow)
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    
    // Toggle scheduling options
    $('#schedule_later').change(function() {
        if($(this).is(':checked')) {
            $('#schedulingOptions').removeClass('d-none');
            
            // Set default date to tomorrow if not already set
            if(!$('#schedule_date').val()) {
                $('#schedule_date').val(formatDate(tomorrow));
            }
            
            // Set default time to 9:00 AM if not already set
            if(!$('#schedule_time').val()) {
                $('#schedule_time').val('09:00');
            }
        } else {
            $('#schedulingOptions').addClass('d-none');
        }
    });
    
    // Date filter functionality
    $('#dateFilter').on('change', function() {
        filterAnnouncements();
    });
    
    $('#clearDateFilter').on('click', function() {
        $('#dateFilter').val('');
        filterAnnouncements();
    });
    
    // Search functionality
    $('#searchAnnouncement').on('keyup', function() {
        filterAnnouncements();
    });
    
    // Add Announcement button click
    $('#addAnnouncementBtn').click(function() {
        $('#announcementForm')[0].reset();
        $('#announcement_id').val('');
        $('#announcementModalLabel').text('Create Announcement');
        resetFormErrors();
        $('#announcementModal').modal('show');
    });
    
    // Save announcement
    $('#saveAnnouncement').click(function() {
        resetFormErrors();
        
        // Validate required fields
        if (!validateAnnouncementForm()) {
            return;
        }
        
        const id = $('#announcement_id').val();
        const isUpdate = id !== '';
        const url = isUpdate 
            ? `/admin/announcements/${id}` 
            : '/admin/announcements';
        const method = isUpdate ? 'PUT' : 'POST';
        
        const formData = {
            title: $('#title').val(),
            message: $('#message').val(),
            is_scheduled: $('#schedule_later').is(':checked') ? 1 : 0,
            is_active: $('#is_active').is(':checked') ? 1 : 0
        };
        
        if ($('#schedule_later').is(':checked')) {
            if (!$('#schedule_date').val() || !$('#schedule_time').val()) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Please select both date and time for scheduling.'
                });
                return;
            }
            
            // Validate that the selected date is in the future
            const selectedDate = new Date($('#schedule_date').val() + 'T' + $('#schedule_time').val());
            const now = new Date();
            
            if (selectedDate <= now) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Date/Time',
                    text: 'The scheduled date and time must be in the future.'
                });
                return;
            }
            
            formData.schedule_date = $('#schedule_date').val();
            formData.schedule_time = $('#schedule_time').val();
            // When scheduling, force status to pending regardless of active checkbox
            formData.is_active = 0;
            formData.is_pending = 1; // Add a flag to indicate pending status
        } else {
            // Explicitly set scheduled_at to null when unchecking
            formData.scheduled_at = null;
            // Use the is_active checkbox value
        }
        
        $.ajax({
            url: url,
            type: method,
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if(response.success) {
                    $('#announcementModal').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message,
                        timer: 1500
                    }).then(() => {
                        location.reload();
                    });
                }
            },
            error: function(xhr) {
                if(xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    for(const field in errors) {
                        const errorMsg = errors[field][0];
                        $(`#${field}`).addClass('is-invalid');
                        $(`#${field}-error`).text(errorMsg);
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while saving the announcement.'
                    });
                }
            }
        });
    });
    
    // View announcement
    $('.view-announcement').click(function() {
        const id = $(this).data('id');
        
        $.ajax({
            url: `/admin/announcements/${id}`,
            type: 'GET',
            success: function(response) {
                if(response.success) {
                    const announcement = response.announcement;
                    $('#view-title').text(announcement.title);
                    $('#view-message').html(announcement.message);
                    
                    // Handle different status values including pending
                    let statusBadge = '';
                    if (announcement.is_active === 'active') {
                        statusBadge = '<span class="badge bg-success">Active</span>';
                    } else if (announcement.is_active === 'pending') {
                        statusBadge = '<span class="badge bg-warning text-dark">Pending (Scheduled)</span>';
                    } else {
                        statusBadge = '<span class="badge bg-secondary">Inactive</span>';
                    }
                    
                    $('#view-status').html(statusBadge);
                    $('#view-created').text(new Date(announcement.created_at).toLocaleString());
                    
                    if(announcement.scheduled_at) {
                        $('#view-scheduled').text(new Date(announcement.scheduled_at).toLocaleString());
                        $('#view-scheduled-section').removeClass('d-none');
                    } else {
                        $('#view-scheduled-section').addClass('d-none');
                    }
                    
                    $('#viewAnnouncementModal').modal('show');
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while retrieving the announcement.'
                });
            }
        });
    });
    
    // Edit announcement
    $('.edit-announcement').click(function() {
        const id = $(this).data('id');
        
        $.ajax({
            url: `/admin/announcements/${id}`,
            type: 'GET',
            success: function(response) {
                if(response.success) {
                    resetFormErrors();
                    const announcement = response.announcement;
                    
                    $('#announcement_id').val(announcement.id);
                    $('#title').val(announcement.title);
                    $('#message').val(announcement.message);
                    
                    // Handle active status, considering "pending" as a special case
                    if (announcement.is_active === 'pending') {
                        $('#is_active').prop('checked', false);
                    } else {
                        $('#is_active').prop('checked', announcement.is_active === 'active');
                    }
                    
                    if(announcement.scheduled_at) {
                        $('#schedule_later').prop('checked', true).trigger('change');
                        const scheduledDate = new Date(announcement.scheduled_at);
                        $('#schedule_date').val(formatDate(scheduledDate));
                        $('#schedule_time').val(formatTime(scheduledDate));
                    } else {
                        $('#schedule_later').prop('checked', false).trigger('change');
                    }
                    
                    $('#announcementModalLabel').text('Edit Announcement');
                    $('#announcementModal').modal('show');
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while retrieving the announcement.'
                });
            }
        });
    });
    
    // Delete announcement
    $('.delete-announcement').click(function() {
        const id = $(this).data('id');
        
        Swal.fire({
            title: 'Are you sure?',
            text: "This announcement will be permanently deleted.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/admin/announcements/${id}`,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if(response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.message,
                                timer: 1500
                            }).then(() => {
                                $(`tr[data-id="${id}"]`).remove();
                                if($('#announcementsTable tbody tr').length === 0) {
                                    $('#announcementsTable tbody').append('<tr><td colspan="4" class="text-center">No announcements found</td></tr>');
                                }
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while deleting the announcement.'
                        });
                    }
                });
            }
        });
    });
    
    // Toggle status
    $('.toggle-status').change(function() {
        const id = $(this).data('id');
        const isActive = $(this).prop('checked');
        const statusLabel = $(this).siblings('label');
        const toggleSwitch = $(this);
        
        // Check if this announcement is scheduled
        const rowData = $(this).closest('tr').find('td:eq(2) span').text().trim();
        if (rowData === 'Pending' || rowData === 'Pending (Scheduled)') {
            // Prevent toggling for scheduled announcements
            toggleSwitch.prop('checked', false);
            Swal.fire({
                icon: 'warning',
                title: 'Cannot Toggle',
                text: 'Scheduled announcements cannot be activated manually. They will automatically activate at the scheduled time.'
            });
            return;
        }
        
        $.ajax({
            url: `/admin/announcements/${id}/toggle-active`,
            type: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if(response.success) {
                    statusLabel.text(isActive ? 'Active' : 'Inactive');
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Announcement status updated successfully.',
                        timer: 1500
                    });
                }
            },
            error: function(xhr) {
                // Revert the switch if there's an error
                toggleSwitch.prop('checked', !isActive);
                statusLabel.text(!isActive ? 'Active' : 'Inactive');
                
                let errorMessage = 'An error occurred while updating the announcement status.';
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMessage = xhr.responseJSON.error;
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMessage
                });
            }
        });
    });
    
    // Search functionality
    $('#searchAnnouncement').on('keyup', function() {
        filterAnnouncements();
    });
    
    // Filter announcements based on search text and date
    function filterAnnouncements() {
        const searchText = $('#searchAnnouncement').val().toLowerCase();
        const filterDate = $('#dateFilter').val();
        
        // Remove existing no-results row if it exists
        $('#no-results-row').remove();
        
        let visibleCount = 0;
        
        $('#announcementsTable tbody tr').each(function() {
            const $row = $(this);
            
            // Skip the "No announcements found" row
            if ($row.find('td[colspan]').length > 0) return;
            
            const title = $row.find('td:first-child').text().toLowerCase();
            const date = $row.find('td:nth-child(2)').text();
            
            const matchesSearch = !searchText || title.includes(searchText);
            const matchesDate = !filterDate || date.includes(filterDate);
            
            if (matchesSearch && matchesDate) {
                $row.show();
                visibleCount++;
            } else {
                $row.hide();
            }
        });
        
        // Show "No results" message if all rows are hidden
        if (visibleCount === 0) {
            $('#announcementsTable tbody').append('<tr id="no-results-row"><td colspan="4" class="text-center">No matching announcements found</td></tr>');
        }
    }

    // Format date to YYYY-MM-DD
    function formatDate(date) {
        const d = new Date(date);
        let month = '' + (d.getMonth() + 1);
        let day = '' + d.getDate();
        const year = d.getFullYear();

        if (month.length < 2) month = '0' + month;
        if (day.length < 2) day = '0' + day;

        return [year, month, day].join('-');
    }

    // Format time to HH:MM
    function formatTime(date) {
        const d = new Date(date);
        let hours = '' + d.getHours();
        let minutes = '' + d.getMinutes();

        if (hours.length < 2) hours = '0' + hours;
        if (minutes.length < 2) minutes = '0' + minutes;

        return [hours, minutes].join(':');
    }
    
    // Validate the announcement form
    function validateAnnouncementForm() {
        let isValid = true;
        
        // Validate title
        if (!$('#title').val().trim()) {
            $('#title').addClass('is-invalid');
            $('#title-error').text('Title is required');
            isValid = false;
        } else if ($('#title').val().length > 100) {
            $('#title').addClass('is-invalid');
            $('#title-error').text('Title must be less than 100 characters');
            isValid = false;
        }
        
        // Validate message
        if (!$('#message').val().trim()) {
            $('#message').addClass('is-invalid');
            $('#message-error').text('Message is required');
            isValid = false;
        }
        
        // Validate scheduling options if enabled
        if ($('#schedule_later').is(':checked')) {
            if (!$('#schedule_date').val()) {
                $('#schedule_date').addClass('is-invalid');
                $('#schedule_date-error').text('Date is required');
                isValid = false;
            }
            
            if (!$('#schedule_time').val()) {
                $('#schedule_time').addClass('is-invalid');
                $('#schedule_time-error').text('Time is required');
                isValid = false;
            }
        }
        
        return isValid;
    }
    
    function resetFormErrors() {
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');
    }
    
    function formatDate(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }
    
    function formatTime(date) {
        const hours = String(date.getHours()).padStart(2, '0');
        const minutes = String(date.getMinutes()).padStart(2, '0');
        return `${hours}:${minutes}`;
    }
});
</script>
@endpush 