@extends('layouts.app')

@section('title', 'Compose Message')

@section('content')
<div class="bg-gray-100 min-h-screen py-6 md:py-10">
    <div class="container mx-auto px-4">
        <!-- Breadcrumb -->
        <div class="mb-6">
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('profile') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-red-600">
                            <i class="fas fa-user mr-2"></i>
                            Profile
                        </a>
                    </li>
                    <li class="inline-flex items-center">
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-400 text-xs mx-1"></i>
                            <a href="{{ route('user.messages') }}" class="text-sm font-medium text-gray-700 hover:text-red-600">
                                Messages
                            </a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-400 text-xs mx-1"></i>
                            <span class="text-sm font-medium text-gray-500">Compose Message</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>

        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-4 md:mb-0">Compose New Message</h1>
            <a href="{{ route('user.messages') }}" class="bg-gray-800 text-white py-2 px-4 rounded-lg hover:bg-gray-700 transition duration-200 flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Back to Messages
            </a>
        </div>

        <!-- Compose Form -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6 border border-gray-200">
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <strong>Oops!</strong> There were some problems with your input.
                    <ul class="mt-2 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('user.messages.store') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="recipient_id" class="block text-sm font-medium text-gray-700 mb-1">To:</label>
                        <div class="relative">
                            <select id="recipient_id" name="recipient_id" class="select2-recipient w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                                <option value="">Select or search for a recipient</option>
                                
                                @if(isset($admins) && count($admins) > 0)
                                    <optgroup label="Admin Team">
                                        @foreach($admins as $admin)
                                            <option value="{{ $admin->id }}" {{ isset($preSelectedRecipient) && $preSelectedRecipient->id == $admin->id ? 'selected' : '' }}>
                                                {{ $admin->full_name }} (Admin)
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @else
                                    <option value="1" {{ isset($preSelectedRecipient) && $preSelectedRecipient->id == 1 ? 'selected' : '' }}>
                                        Admin/Support Team
                                    </option>
                                @endif
                                
                                @if(isset($trainers) && count($trainers) > 0)
                                    <optgroup label="Trainers">
                                        @foreach($trainers as $trainer)
                                            <option value="{{ $trainer->id }}" {{ isset($preSelectedRecipient) && $preSelectedRecipient->id == $trainer->id ? 'selected' : '' }}>
                                                {{ $trainer->full_name }} (Trainer)
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endif
                                
                                @if(isset($members) && count($members) > 0)
                                    <optgroup label="Members">
                                        @foreach($members as $member)
                                            <option value="{{ $member->id }}" {{ isset($preSelectedRecipient) && $preSelectedRecipient->id == $member->id ? 'selected' : '' }}>
                                                {{ $member->full_name }} (Member)
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endif
                            </select>
                        </div>
                    </div>
                    
                    <div>
                        <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">Subject:</label>
                        <input type="text" id="subject" name="subject" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500" value="{{ old('subject') }}">
                    </div>
                    
                    <div>
                        <label for="content" class="block text-sm font-medium text-gray-700 mb-1">Message:</label>
                        <textarea id="content" name="content" rows="8" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">{{ old('content') }}</textarea>
                    </div>
                    
                    <div class="pt-4">
                        <button type="submit" class="w-full md:w-auto bg-red-600 text-white py-3 px-6 rounded-lg hover:bg-red-700 transition duration-200 flex items-center justify-center">
                            <i class="fas fa-paper-plane mr-2"></i> Send Message
                        </button>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Tips Box -->
        <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
            <h3 class="text-lg font-medium text-gray-800 mb-4">Messaging Tips</h3>
            <div class="space-y-3">
                <div class="flex items-start">
                    <div class="flex-shrink-0 text-blue-500">
                        <i class="fas fa-info-circle text-lg"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-gray-600">
                            Be clear and specific with your questions to get faster responses.
                        </p>
                    </div>
                </div>
                <div class="flex items-start">
                    <div class="flex-shrink-0 text-yellow-500">
                        <i class="fas fa-clock text-lg"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-gray-600">
                            Admin and trainer responses typically take 24-48 hours during business days.
                        </p>
                    </div>
                </div>
                <div class="flex items-start">
                    <div class="flex-shrink-0 text-green-500">
                        <i class="fas fa-check-circle text-lg"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-gray-600">
                            You'll receive a notification when someone replies to your message.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Include Select2 CSS and JS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- Include SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        
        // Initialize Select2 for recipient dropdown with search
        $(document).ready(function() {
            $('.select2-recipient').select2({
                placeholder: 'Select or search for a recipient',
                allowClear: true,
                width: '100%',
                dropdownCssClass: 'select2-dropdown-large',
                minimumInputLength: 0,
                templateResult: formatRecipient,
                templateSelection: formatRecipient,
                escapeMarkup: function(m) { return m; }
            });

            // Focus behavior - open dropdown on focus
            $('.select2-recipient').on('select2:open', function() {
                setTimeout(function() {
                    $('.select2-search__field').focus();
                }, 100);
            });
        });
        
        // Format recipient options with role badge
        function formatRecipient(recipient) {
            if (!recipient.id) return recipient.text;
            
            let roleClass = 'bg-gray-500';
            let role = '';
            
            if (recipient.text.includes('(Admin)')) {
                roleClass = 'bg-red-500';
                role = 'Admin';
            } else if (recipient.text.includes('(Trainer)')) {
                roleClass = 'bg-blue-500';
                role = 'Trainer';
            } else if (recipient.text.includes('(Member)')) {
                roleClass = 'bg-green-500';
                role = 'Member';
            }
            
            // Extract just the name (remove the role part)
            let name = recipient.text.split(' (')[0];
            
            return '<div class="flex items-center py-2">' +
                   '<span class="font-medium">' + name + '</span>' +
                   (role ? '<span class="ml-2 px-2 py-1 text-xs rounded-full text-white ' + roleClass + '">' + role + '</span>' : '') +
                   '</div>';
        }
        
        // Form validation
        if (form) {
            form.addEventListener('submit', function(e) {
                const recipientValue = document.getElementById('recipient_id').value;
                const subject = document.getElementById('subject').value;
                const content = document.getElementById('content').value;
                
                let hasError = false;
                
                if (!recipientValue) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Error!',
                        text: 'Please select a recipient',
                        icon: 'error',
                        confirmButtonColor: '#dc2626'
                    });
                    hasError = true;
                }
                
                if (!subject && !hasError) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Error!',
                        text: 'Please enter a subject',
                        icon: 'error',
                        confirmButtonColor: '#dc2626'
                    });
                    hasError = true;
                }
                
                if (!content && !hasError) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Error!',
                        text: 'Please enter a message',
                        icon: 'error',
                        confirmButtonColor: '#dc2626'
                    });
                }
            });
        }
        
        // Show success message if present
        @if(session('success'))
            Swal.fire({
                title: 'Success!',
                text: "{{ session('success') }}",
                icon: 'success',
                confirmButtonColor: '#10B981'
            });
        @endif
    });
</script>

<style>
    .select2-container--default .select2-selection--single {
        height: 50px;
        padding: 10px;
        font-size: 16px;
        line-height: 1.5;
        border-radius: 0.5rem;
        border: 1px solid #D1D5DB;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 30px;
        padding-left: 0;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 48px;
    }
    
    .select2-dropdown-large {
        font-size: 16px;
    }
    
    .select2-container--default .select2-search--dropdown .select2-search__field {
        padding: 10px;
        border-radius: 0.375rem;
    }
    
    .select2-results__option {
        padding: 10px;
    }
    
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #f3f4f6;
        color: #111827;
    }
    
    .select2-container--default .select2-results__option[aria-selected=true] {
        background-color: #e5e7eb;
    }
    
    .select2-dropdown {
        border-color: #D1D5DB;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
</style>
@endsection 