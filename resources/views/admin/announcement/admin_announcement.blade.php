@extends('layouts.admin')
@section('title', 'Announcement Management')

@section('content')
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
                <div class="input-group me-2" style="width: 300px;">
                    <input type="text" class="form-control" id="searchAnnouncement" placeholder="Search announcements...">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
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
                                @if($announcement->is_active)
                                <span class="badge bg-success">Active</span>
                                @else
                                <span class="badge bg-secondary">Inactive</span>
                                @endif
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
                                <button type="button" class="btn btn-sm {{ $announcement->is_active ? 'btn-warning' : 'btn-success' }} toggle-status" data-id="{{ $announcement->id }}">
                                    <i class="fas {{ $announcement->is_active ? 'fa-ban' : 'fa-check' }}"></i>
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
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                        <div class="invalid-feedback" id="title-error"></div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="message" class="form-label">Message</label>
                        <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
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
                                <label for="schedule_date" class="form-label">Date</label>
                                <input type="date" class="form-control" id="schedule_date" name="schedule_date">
                                <div class="invalid-feedback" id="schedule_date-error"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="schedule_time" class="form-label">Time</label>
                                <input type="time" class="form-control" id="schedule_time" name="schedule_time">
                                <div class="invalid-feedback" id="schedule_time-error"></div>
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
    // Toggle scheduling options
    $('#schedule_later').change(function() {
        if($(this).is(':checked')) {
            $('#schedulingOptions').removeClass('d-none');
        } else {
            $('#schedulingOptions').addClass('d-none');
        }
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
        
        const id = $('#announcement_id').val();
        const isUpdate = id !== '';
        const url = isUpdate 
            ? `/admin/announcements/${id}` 
            : '/admin/announcements';
        const method = isUpdate ? 'PUT' : 'POST';
        
        const formData = {
            title: $('#title').val(),
            message: $('#message').val(),
            is_active: $('#is_active').is(':checked') ? 1 : 0
        };
        
        if($('#schedule_later').is(':checked')) {
            formData.schedule_date = $('#schedule_date').val();
            formData.schedule_time = $('#schedule_time').val();
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
                    $('#view-status').html(announcement.is_active 
                        ? '<span class="badge bg-success">Active</span>' 
                        : '<span class="badge bg-secondary">Inactive</span>');
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
                    $('#is_active').prop('checked', announcement.is_active);
                    
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
    $('.toggle-status').click(function() {
        const id = $(this).data('id');
        const currentStatus = $(this).find('i').hasClass('fa-ban') ? 'active' : 'inactive';
        const newStatus = currentStatus === 'active' ? 'inactive' : 'active';
        
        $.ajax({
            url: `/admin/announcements/${id}/toggle`,
            type: 'POST',
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
                        location.reload();
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while updating the announcement status.'
                });
            }
        });
    });
    
    // Search functionality
    $('#searchAnnouncement').on('keyup', function() {
        const value = $(this).val().toLowerCase();
        $('#announcementsTable tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
        
        if($('#announcementsTable tbody tr:visible').length === 0) {
            if($('#no-results-row').length === 0) {
                $('#announcementsTable tbody').append('<tr id="no-results-row"><td colspan="4" class="text-center">No matching announcements found</td></tr>');
            }
        } else {
            $('#no-results-row').remove();
        }
    });
    
    // Helper functions
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