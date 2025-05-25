@extends('layouts.admin')

@section('title', 'Sports Management')

<style>
    /* Custom toggle switch styles */
    .custom-toggle {
        display: flex;
        align-items: center;
    }
    
    .custom-toggle-switch {
        position: relative;
        display: inline-block;
        width: 56px;
        height: 30px;
        background-color: #4B5563;
        border-radius: 15px;
        transition: all 0.3s;
    }
    
    .custom-toggle-switch:after {
        content: '';
        position: absolute;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background-color: white;
        top: 3px;
        left: 3px;
        transition: all 0.3s;
    }
    
    .custom-toggle-checkbox:checked + .custom-toggle-switch {
        background-color: #2563EB;
    }
    
    .custom-toggle-checkbox:checked + .custom-toggle-switch:after {
        left: 29px;
    }
    
    .custom-toggle-checkbox {
        display: none;
    }
</style>

@section('content')
<div class="container px-4 py-6 mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-white">Sports Management</h1>
        <div class="flex space-x-2">
            <a href="{{ route('admin.pricing.index') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg flex items-center">
                <i class="fas fa-tag mr-2"></i> Pricing Plans
            </a>
            <button id="addSportBtn" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg flex items-center">
                <i class="fas fa-plus mr-2"></i> Add Sport
            </button>
        </div>
    </div>

    <!-- Sports List -->
    <div class="bg-gray-800 rounded-lg p-4">
        @if(count($sports) > 0)
            <div class="overflow-x-auto relative">
                <table class="w-full text-sm text-left text-gray-400">
                    <thead class="text-xs uppercase bg-gray-700 text-gray-400">
                        <tr>
                            <th scope="col" class="py-3 px-6">Name</th>
                            <th scope="col" class="py-3 px-6">Status</th>
                            <th scope="col" class="py-3 px-6">Order</th>
                            <th scope="col" class="py-3 px-6">Trainers</th>
                            <th scope="col" class="py-3 px-6">Plans</th>
                            <th scope="col" class="py-3 px-6">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sports as $sport)
                            <tr class="border-b bg-gray-900 border-gray-700">
                                <td class="py-4 px-6 font-medium text-white whitespace-nowrap">
                                    {{ $sport->name }}
                                </td>
                                <td class="py-4 px-6">
                                    <span class="px-2 py-1 rounded-full text-xs {{ $sport->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $sport->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    {{ $sport->display_order }}
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">
                                        {{ $sport->trainers_count }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded-full text-xs">
                                        {{ $sport->plans_count }}
                                    </span>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="flex space-x-2">
                                        <button class="edit-sport-btn text-blue-500 hover:text-blue-300" data-sport-id="{{ $sport->id }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="manage-trainers-btn text-yellow-500 hover:text-yellow-300" data-sport-id="{{ $sport->id }}" data-sport-name="{{ $sport->name }}">
                                            <i class="fas fa-users"></i>
                                        </button>
                                        <button class="delete-sport-btn text-red-500 hover:text-red-300" data-sport-id="{{ $sport->id }}" data-sport-name="{{ $sport->name }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="bg-gray-900 rounded-lg p-6 text-center">
                <p class="text-gray-400 mb-4">No sports found. Add a sport to get started.</p>
                <button id="noSportsAddBtn" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg">
                    <i class="fas fa-plus mr-2"></i> Add Your First Sport
                </button>
            </div>
        @endif
    </div>
</div>

<!-- Add/Edit Sport Modal -->
<div id="sportFormModal" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-50 hidden w-full overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full items-center justify-center">
    <div class="fixed inset-0 bg-black bg-opacity-50 backdrop-filter backdrop-blur-sm"></div>
    <div class="relative w-full max-w-2xl max-h-full">
        <!-- Modal content -->
        <div class="relative bg-gray-800 rounded-lg shadow">
            <!-- Modal header -->
            <div class="flex items-center justify-between p-4 border-b border-gray-700">
                <h3 class="text-xl font-semibold text-white modal-title">
                    Add New Sport
                </h3>
                <button type="button" class="close-modal text-gray-400 bg-transparent hover:bg-gray-700 hover:text-white rounded-lg text-sm p-1.5 ml-auto inline-flex items-center">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <!-- Modal body -->
            <form id="sportForm" enctype="multipart/form-data">
                <div class="p-6 space-y-6">
                    <input type="hidden" id="sportId" name="id">
                    <!-- Add _method field for PUT requests -->
                    <input type="hidden" id="methodField" name="_method" value="POST">
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="sportName" class="block mb-2 text-sm font-medium text-white">Sport Name <span class="text-red-500">*</span></label>
                            <input type="text" id="sportName" name="name" class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" 
                                   required maxlength="100">
                            <p class="mt-1 text-xs text-gray-400">Required. Maximum 100 characters.</p>
                        </div>
                        <div>
                            <label for="displayOrder" class="block mb-2 text-sm font-medium text-white">Display Order</label>
                            <input type="number" id="displayOrder" name="display_order" min="0" step="1" class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" value="0">
                            <p class="mt-1 text-xs text-gray-400">Optional. Lower numbers appear first.</p>
                        </div>
                    </div>
                    
                    <div>
                        <label for="shortDescription" class="block mb-2 text-sm font-medium text-white">Short Description</label>
                        <input type="text" id="shortDescription" name="short_description" maxlength="255" class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                        <p class="mt-1 text-xs text-gray-400">Brief description for display in listings (maximum 255 characters)</p>
                    </div>
                    
                    <div>
                        <label for="sportDescription" class="block mb-2 text-sm font-medium text-white">Full Description <span class="text-red-500">*</span></label>
                        <textarea id="sportDescription" name="description" rows="3" class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required></textarea>
                        <p class="mt-1 text-xs text-gray-400">Required. Detailed description shown on the sport's page.</p>
                    </div>
                    
                    <div>
                        <label for="backgroundImage" class="block mb-2 text-sm font-medium text-white">Background Image</label>
                        <input type="file" id="backgroundImage" name="background_image" accept="image/jpeg,image/png,image/gif" class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                        <p class="mt-1 text-xs text-gray-400">Recommended size: 1920x1080px (16:9 ratio). If no image is uploaded, a default image will be used.</p>
                    </div>
                    
                    <div class="flex justify-between">
                        <div>
                            <div class="custom-toggle">
                                <input type="checkbox" id="isActive" name="is_active" value="1" class="custom-toggle-checkbox" checked>
                                <label for="isActive" class="custom-toggle-switch"></label>
                                <span class="ml-3 text-sm font-medium text-white">Active</span>
                            </div>
                            <p class="ml-14 text-xs text-gray-400">Display this sport in the pricing page.</p>
                        </div>
                    </div>
                </div>
                <!-- Modal footer -->
                <div class="flex items-center p-6 space-x-2 border-t border-gray-700">
                    <button type="submit" class="save-sport-btn text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                        Save Sport
                    </button>
                    <button type="button" class="close-modal text-gray-300 bg-gray-700 hover:bg-gray-600 focus:ring-4 focus:outline-none focus:ring-gray-600 rounded-lg border border-gray-600 text-sm font-medium px-5 py-2.5">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Manage Trainers Modal -->
<div id="trainersModal" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-50 hidden w-full overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full items-center justify-center">
    <div class="fixed inset-0 bg-black bg-opacity-50 backdrop-filter backdrop-blur-sm"></div>
    <div class="relative w-full max-w-2xl max-h-full">
        <!-- Modal content -->
        <div class="relative bg-gray-800 rounded-lg shadow">
            <!-- Modal header -->
            <div class="flex items-center justify-between p-4 border-b border-gray-700">
                <h3 class="text-xl font-semibold text-white trainers-modal-title">
                    Manage Trainers
                </h3>
                <button type="button" class="close-trainers-modal text-gray-400 bg-transparent hover:bg-gray-700 hover:text-white rounded-lg text-sm p-1.5 ml-auto inline-flex items-center">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <!-- Modal body -->
            <form id="trainersForm">
                <div class="p-6 space-y-6">
                    <input type="hidden" id="trainersSportId" name="sport_id">
                    
                    <div class="mb-4">
                        <p class="text-white">Select trainers for this sport:</p>
                    </div>
                    
                    <div id="trainersContainer" class="max-h-64 overflow-y-auto p-2 bg-gray-900 rounded-lg">
                        <!-- Trainers will be loaded here dynamically -->
                        <div class="text-gray-400 text-center p-4">
                            Loading trainers...
                        </div>
                    </div>
                </div>
                <!-- Modal footer -->
                <div class="flex items-center p-6 space-x-2 border-t border-gray-700">
                    <button type="submit" class="save-trainers-btn text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                        Save Trainers
                    </button>
                    <button type="button" class="close-trainers-modal text-gray-300 bg-gray-700 hover:bg-gray-600 focus:ring-4 focus:outline-none focus:ring-gray-600 rounded-lg border border-gray-600 text-sm font-medium px-5 py-2.5">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Helper functions for form validation and error handling
    function showFieldError(field, message) {
        // Remove any existing error message
        const existingError = field.parentElement.querySelector('.error-message');
        if (existingError) {
            existingError.remove();
        }
        
        // Add error styling
        field.classList.add('border-red-500');
        field.classList.remove('border-green-500');
        
        // Create and append error message
        const errorMessage = document.createElement('p');
        errorMessage.className = 'error-message text-xs text-red-500 mt-1';
        errorMessage.textContent = message;
        field.parentElement.appendChild(errorMessage);
    }
    
    function clearFieldError(field) {
        // Remove any existing error message
        const existingError = field.parentElement.querySelector('.error-message');
        if (existingError) {
            existingError.remove();
        }
        
        // Add success styling
        field.classList.remove('border-red-500');
        field.classList.add('border-green-500');
    }
    
    function handleError(result) {
        let errorMessage = 'Please check the form and try again.';
        
        if (result.errors) {
            // Handle validation errors
            const errors = result.errors;
            errorMessage = Object.values(errors).flat().join('<br>');
            
            // Highlight fields with errors
            if (errors.name) {
                showFieldError(document.getElementById('sportName'), errors.name[0]);
            }
            
            if (errors.description) {
                showFieldError(document.getElementById('sportDescription'), errors.description[0]);
            }
        } else if (result.message) {
            errorMessage = result.message;
        }
        
        Swal.fire({
            title: 'Error!',
            html: errorMessage,
            icon: 'error',
            background: '#1F2937',
            color: '#FFFFFF',
            confirmButtonColor: '#EF4444'
        });
    }
    // Sport Modal functionality
    const sportModal = document.getElementById('sportFormModal');
    const openModalBtns = document.querySelectorAll('#addSportBtn, #noSportsAddBtn');
    const closeModalBtns = document.querySelectorAll('.close-modal');
    const sportForm = document.getElementById('sportForm');
    const modalTitle = document.querySelector('.modal-title');
    
    // Open Modal buttons
    openModalBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            resetSportForm();
            modalTitle.textContent = 'Add New Sport';
            sportModal.classList.remove('hidden');
            sportModal.classList.add('flex');
        });
    });
    
    // Reset form function
    function resetSportForm() {
        sportForm.reset();
        document.getElementById('sportId').value = '';
        
        // Clear validation classes and messages
        const formInputs = sportForm.querySelectorAll('input, textarea');
        formInputs.forEach(input => {
            input.classList.remove('border-red-500', 'border-green-500');
            input.removeAttribute('data-previously-validated');
            
            // Remove any existing error messages
            const errorMsg = input.parentElement.querySelector('.error-message');
            if (errorMsg) {
                errorMsg.remove();
            }
        });
        
        // Clear any additional error messages in the form
        document.querySelectorAll('.error-message').forEach(el => el.remove());
    }
    
    // Close Modal buttons
    closeModalBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            sportModal.classList.add('hidden');
            sportModal.classList.remove('flex');
            
            // Clear any active SweetAlert2 popups
            if (Swal.isVisible()) {
                Swal.close();
            }
        });
    });
    
    // Close modal when clicking outside
    sportModal.addEventListener('click', (e) => {
        if (e.target === sportModal) {
            sportModal.classList.add('hidden');
            sportModal.classList.remove('flex');
            
            // Clear any active SweetAlert2 popups
            if (Swal.isVisible()) {
                Swal.close();
            }
        }
    });
    
    // Live form validation
    const requiredInputs = sportForm.querySelectorAll('input[required], textarea[required]');
    
    requiredInputs.forEach(input => {
        // Validate on blur (when user leaves the field)
        input.addEventListener('blur', () => {
            validateField(input);
        });
        
        // Validate on input for immediate feedback
        input.addEventListener('input', () => {
            validateField(input);
        });
    });
    
    function validateField(field) {
        // Remove any existing error messages
        const existingError = field.parentElement.querySelector('.error-message');
        if (existingError) {
            existingError.remove();
        }
        
        // Special handling for when we're editing - don't require background_image
        const sportId = document.getElementById('sportId').value;
        if (sportId && field.id === 'backgroundImage') {
            // Don't validate required if we're editing and this is the background image
            field.classList.remove('border-red-500');
            return true;
        }
        
        // If the field has content, mark it as valid regardless of other validation rules
        // This fixes issues where fields appear filled but validation fails
        if (field.value && field.value.trim() !== '') {
            field.classList.remove('border-red-500');
            field.classList.add('border-green-500');
            // Set this attribute to remember that the field has been validated
            field.setAttribute('data-previously-validated', 'true');
            return true;
        }
        
        // Check validation
        if (!field.checkValidity()) {
            field.classList.add('border-red-500');
            field.classList.remove('border-green-500');
            
            // Add error message
            const errorMessage = document.createElement('p');
            errorMessage.className = 'error-message text-xs text-red-500 mt-1';
            
            if (field.validity.valueMissing) {
                errorMessage.textContent = 'This field is required';
            } else if (field.validity.typeMismatch) {
                errorMessage.textContent = 'Please enter a valid format';
            } else if (field.validity.tooLong) {
                errorMessage.textContent = `Maximum length is ${field.maxLength} characters`;
            } else if (field.validity.rangeUnderflow) {
                errorMessage.textContent = `Minimum value is ${field.min}`;
            } else {
                errorMessage.textContent = 'Invalid value';
            }
            
            field.parentElement.appendChild(errorMessage);
            return false;
        } else {
            field.classList.remove('border-red-500');
            field.classList.add('border-green-500');
            field.setAttribute('data-previously-validated', 'true');
            return true;
        }
    }
    
    // Edit sport buttons
    const editButtons = document.querySelectorAll('.edit-sport-btn');
    
    editButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const sportId = btn.dataset.sportId;
            fetchSportDetails(sportId);
        });
    });
    
    // Delete sport buttons
    const deleteButtons = document.querySelectorAll('.delete-sport-btn');
    
    deleteButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const sportId = btn.dataset.sportId;
            const sportName = btn.dataset.sportName;
            confirmDeleteSport(sportId, sportName);
        });
    });
    
    // Trainers Modal functionality
    const trainersModal = document.getElementById('trainersModal');
    const manageTrainersBtns = document.querySelectorAll('.manage-trainers-btn');
    const closeTrainersModalBtns = document.querySelectorAll('.close-trainers-modal');
    const trainersForm = document.getElementById('trainersForm');
    const trainersModalTitle = document.querySelector('.trainers-modal-title');
    
    // Open Trainers Modal buttons
    manageTrainersBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const sportId = btn.dataset.sportId;
            const sportName = btn.dataset.sportName;
            document.getElementById('trainersSportId').value = sportId;
            trainersModalTitle.textContent = `Manage Trainers for ${sportName}`;
            fetchTrainers(sportId);
            trainersModal.classList.remove('hidden');
            trainersModal.classList.add('flex');
        });
    });
    
    // Close Trainers Modal buttons
    closeTrainersModalBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            trainersModal.classList.add('hidden');
            trainersModal.classList.remove('flex');
            
            // Clear any active SweetAlert2 popups
            if (Swal.isVisible()) {
                Swal.close();
            }
        });
    });
    
    // Close trainers modal when clicking outside
    trainersModal.addEventListener('click', (e) => {
        if (e.target === trainersModal) {
            trainersModal.classList.add('hidden');
            trainersModal.classList.remove('flex');
            
            // Clear any active SweetAlert2 popups
            if (Swal.isVisible()) {
                Swal.close();
            }
        }
    });
    
    // Sport Form submission
    sportForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Remove all existing error messages
        document.querySelectorAll('.error-message').forEach(el => el.remove());
        
        // Get form fields
        const sportId = document.getElementById('sportId').value;
        const nameField = document.getElementById('sportName');
        const descriptionField = document.getElementById('sportDescription');
        const shortDescriptionField = document.getElementById('shortDescription');
        const displayOrderField = document.getElementById('displayOrder');
        const isActiveField = document.getElementById('isActive');
        const backgroundImageField = document.getElementById('backgroundImage');
        
        // Basic validation
        let isValid = true;
        
        if (!nameField.value || nameField.value.trim() === '') {
            showFieldError(nameField, 'The name field is required');
            isValid = false;
        } else {
            clearFieldError(nameField);
        }
        
        if (!descriptionField.value || descriptionField.value.trim() === '') {
            showFieldError(descriptionField, 'The description field is required');
            isValid = false;
        } else {
            clearFieldError(descriptionField);
        }
        
        if (!isValid) {
            Swal.fire({
                title: 'Validation Error',
                text: 'Please check the form and fix the highlighted errors',
                icon: 'error',
                background: '#1F2937',
                color: '#FFFFFF',
                confirmButtonColor: '#EF4444'
            });
            return;
        }
        
        // Show loading state
        const submitButton = document.querySelector('.save-sport-btn');
        const originalButtonText = submitButton.innerHTML;
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Saving...';
        
        // Create a new FormData object
        const formData = new FormData();
        
        // Check if we're editing
        const isEditing = !!sportId;
        
        // Add CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        formData.append('_token', csrfToken);
        
        // Set method to PUT if editing
        if (isEditing) {
            formData.append('_method', 'PUT');
        }
        
        // Add required form data
        formData.append('name', nameField.value.trim());
        formData.append('description', descriptionField.value.trim());
        formData.append('short_description', shortDescriptionField.value.trim());
        formData.append('display_order', displayOrderField.value || 0);
        formData.append('is_active', isActiveField.checked ? 1 : 0);
        
        // Add file if selected
        if (backgroundImageField.files.length > 0) {
            formData.append('background_image', backgroundImageField.files[0]);
        }
        
        // Determine URL
        const url = isEditing ? `/admin/sports/${sportId}` : '/admin/sports';
        
        // Use XMLHttpRequest instead of fetch for better multipart form handling
        const xhr = new XMLHttpRequest();
        xhr.open('POST', url, true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        
        xhr.onload = function() {
            let result;
            try {
                result = JSON.parse(xhr.responseText);
            } catch (e) {
                console.error('Error parsing response:', e);
                result = {
                    success: false,
                    message: 'An unexpected error occurred. Server response was not valid JSON.'
                };
            }
            
            submitButton.disabled = false;
            submitButton.innerHTML = originalButtonText;
            
            if (xhr.status === 200 || xhr.status === 201) {
                if (result.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: result.message || 'Sport saved successfully!',
                        icon: 'success',
                        background: '#1F2937',
                        color: '#FFFFFF',
                        confirmButtonColor: '#3B82F6'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    handleError(result);
                }
            } else if (xhr.status === 422) { // Validation error
                handleError(result);
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: 'An unexpected error occurred. Please try again.',
                    icon: 'error',
                    background: '#1F2937',
                    color: '#FFFFFF',
                    confirmButtonColor: '#EF4444'
                });
            }
        };
        
        xhr.onerror = function() {
            submitButton.disabled = false;
            submitButton.innerHTML = originalButtonText;
            
            Swal.fire({
                title: 'Error!',
                text: 'Network error occurred. Please try again.',
                icon: 'error',
                background: '#1F2937',
                color: '#FFFFFF',
                confirmButtonColor: '#EF4444'
            });
        };
        
        xhr.send(formData);
    });
    
    // Trainers Form submission
    trainersForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const sportId = document.getElementById('trainersSportId').value;
        
        // Get all selected trainers
        const selectedTrainers = Array.from(document.querySelectorAll('.trainer-checkbox:checked')).map(cb => cb.value);
        
        // Show loading state
        const submitButton = document.querySelector('.save-trainers-btn');
        const originalButtonText = submitButton.innerHTML;
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Saving...';
        
        fetch(`/admin/sports/${sportId}/trainers`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                trainers: selectedTrainers
            })
        })
        .then(response => response.json())
        .then(result => {
            submitButton.disabled = false;
            submitButton.innerHTML = originalButtonText;
            
            if (result.success) {
                Swal.fire({
                    title: 'Success!',
                    text: result.message,
                    icon: 'success',
                    background: '#1F2937',
                    color: '#FFFFFF',
                    confirmButtonColor: '#3B82F6'
                }).then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: result.message || 'Failed to update trainers',
                    icon: 'error',
                    background: '#1F2937',
                    color: '#FFFFFF',
                    confirmButtonColor: '#EF4444'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            submitButton.disabled = false;
            submitButton.innerHTML = originalButtonText;
            
            Swal.fire({
                title: 'Error!',
                text: 'An unexpected error occurred. Please try again.',
                icon: 'error',
                background: '#1F2937',
                color: '#FFFFFF',
                confirmButtonColor: '#EF4444'
            });
        });
    });
    
    // Fetch sport details for editing
    function fetchSportDetails(sportId) {
        // Show loading indicator
        Swal.fire({
            title: 'Loading...',
            text: 'Fetching sport details',
            showConfirmButton: false,
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        fetch(`/admin/sports/${sportId}`)
            .then(response => response.json())
            .then(result => {
                Swal.close();
                
                if (result.success) {
                    populateSportForm(result.sport);
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Failed to fetch sport details',
                        icon: 'error',
                        background: '#1F2937',
                        color: '#FFFFFF',
                        confirmButtonColor: '#EF4444'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.close();
                Swal.fire({
                    title: 'Error!',
                    text: 'An unexpected error occurred. Please try again.',
                    icon: 'error',
                    background: '#1F2937',
                    color: '#FFFFFF',
                    confirmButtonColor: '#EF4444'
                });
            });
    }
    
    // Populate form with sport details
    function populateSportForm(sport) {
        resetSportForm();
        
        document.getElementById('sportId').value = sport.id;
        document.getElementById('sportName').value = sport.name;
        document.getElementById('displayOrder').value = sport.display_order;
        document.getElementById('shortDescription').value = sport.short_description;
        document.getElementById('sportDescription').value = sport.description;
        document.getElementById('isActive').checked = sport.is_active;
        
        // Mark all fields with values as validated
        const inputs = sportForm.querySelectorAll('input, textarea');
        inputs.forEach(input => {
            if (input.value && input.value.trim() !== '') {
                input.setAttribute('data-previously-validated', 'true');
                input.classList.add('border-green-500');
            }
        });
        
        // Update modal title
        document.querySelector('.modal-title').textContent = `Edit Sport: ${sport.name}`;
        sportModal.classList.remove('hidden');
        sportModal.classList.add('flex');
    }
    
    // Confirm delete sport
    function confirmDeleteSport(sportId, sportName) {
        Swal.fire({
            title: 'Are you sure?',
            html: `You are about to delete the sport <b>${sportName}</b>.<br><br>This will also delete all associated pricing plans.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#EF4444',
            cancelButtonColor: '#4B5563',
            confirmButtonText: 'Yes, delete it!',
            background: '#1F2937',
            color: '#FFFFFF',
        }).then((result) => {
            if (result.isConfirmed) {
                deleteSport(sportId);
            }
        });
    }
    
    // Delete sport
    function deleteSport(sportId) {
        fetch(`/admin/sports/${sportId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                Swal.fire({
                    title: 'Deleted!',
                    text: result.message,
                    icon: 'success',
                    background: '#1F2937',
                    color: '#FFFFFF',
                    confirmButtonColor: '#3B82F6'
                }).then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: result.message || 'Failed to delete sport',
                    icon: 'error',
                    background: '#1F2937',
                    color: '#FFFFFF',
                    confirmButtonColor: '#EF4444'
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
                color: '#FFFFFF',
                confirmButtonColor: '#EF4444'
            });
        });
    }
    
    // Fetch trainers for a sport
    function fetchTrainers(sportId) {
        const trainersContainer = document.getElementById('trainersContainer');
        trainersContainer.innerHTML = '<div class="text-center p-4"><i class="fas fa-spinner fa-spin text-blue-500 text-xl mr-2"></i> Loading trainers...</div>';
        
        // Show loading indicator in the container
        Promise.all([
            fetch(`/api/trainers`).then(response => {
                if (!response.ok) {
                    throw new Error(`Failed to fetch trainers: ${response.status} ${response.statusText}`);
                }
                return response.json();
            }).catch(error => {
                console.error('Error fetching trainers:', error);
                return { error: error.message };
            }),
            fetch(`/api/sports/${sportId}/trainers`).then(response => {
                if (!response.ok) {
                    throw new Error(`Failed to fetch sport trainers: ${response.status} ${response.statusText}`);
                }
                return response.json();
            }).catch(error => {
                console.error('Error fetching sport trainers:', error);
                return { error: error.message };
            })
        ])
        .then(([trainers, sportTrainers]) => {
            // Check for errors in responses
            if (trainers.error || sportTrainers.error) {
                let errorMessage = '';
                if (trainers.error) errorMessage += `All trainers: ${trainers.error}\n`;
                if (sportTrainers.error) errorMessage += `Sport trainers: ${sportTrainers.error}`;
                
                trainersContainer.innerHTML = `
                    <div class="text-red-400 text-center p-4">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Error loading trainers: ${errorMessage}
                        <button id="retry-fetch-trainers" class="block mx-auto mt-2 px-3 py-1 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            Retry
                        </button>
                    </div>
                `;
                
                // Add retry button functionality
                document.getElementById('retry-fetch-trainers')?.addEventListener('click', () => {
                    fetchTrainers(sportId);
                });
                
                return;
            }
            
            const sportTrainerIds = sportTrainers.map(trainer => trainer.id);
            
            // Build the trainer list
            if (trainers.length > 0) {
                let html = '<div class="space-y-2">';
                
                trainers.forEach(trainer => {
                    const isChecked = sportTrainerIds.includes(trainer.id);
                    
                    html += `
                        <div class="flex items-center p-2 hover:bg-gray-800 rounded">
                            <input id="trainer-${trainer.id}" type="checkbox" value="${trainer.id}" 
                                   class="trainer-checkbox w-4 h-4 text-blue-600 bg-gray-700 border-gray-600 rounded focus:ring-blue-500"
                                   ${isChecked ? 'checked' : ''}>
                            <label for="trainer-${trainer.id}" class="ml-2 text-sm font-medium text-gray-300 flex items-center">
                                <div class="w-8 h-8 rounded-full overflow-hidden mr-2">
                                    <img src="${trainer.profile_image_url || '/assets/default_profile.png'}" 
                                         alt="${trainer.name}" class="w-full h-full object-cover">
                                </div>
                                ${trainer.name}
                            </label>
                        </div>
                    `;
                });
                
                html += '</div>';
                trainersContainer.innerHTML = html;
            } else {
                trainersContainer.innerHTML = '<div class="text-gray-400 text-center p-4">No trainers available</div>';
            }
        })
        .catch(error => {
            console.error('Error fetching trainers:', error);
            trainersContainer.innerHTML = `
                <div class="text-red-400 text-center p-4">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Error loading trainers: ${error.message}
                    <button id="retry-fetch-trainers" class="block mx-auto mt-2 px-3 py-1 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Retry
                    </button>
                </div>
            `;
            
            // Add retry button functionality
            document.getElementById('retry-fetch-trainers')?.addEventListener('click', () => {
                fetchTrainers(sportId);
            });
        });
    }
});
</script>
@endpush
@endsection