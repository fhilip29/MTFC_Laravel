@extends('layouts.admin')

@section('title', 'Pricing Management')

@section('content')
<div class="container px-4 py-6 mx-auto">
    <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-3">
        <h1 class="text-2xl font-bold text-white">Pricing Management</h1>
        <div class="flex flex-wrap gap-2 justify-center sm:justify-end">
            <a href="{{ route('admin.sports.index') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg flex items-center">
                <i class="fas fa-running mr-2"></i> Manage Sports
            </a>
            <button id="addPlanBtn" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg flex items-center">
                <i class="fas fa-plus mr-2"></i> Add Pricing Plan
            </button>
        </div>
    </div>

    <!-- Tabs for Sports -->
    <div class="mb-6 border-b border-gray-700 overflow-x-auto pb-1">
        <ul class="flex flex-nowrap -mb-px min-w-max" id="sportsTabs" role="tablist">
            @foreach($sports as $index => $sport)
                <li class="mr-2" role="presentation">
                    <button class="inline-block p-3 sm:p-4 border-b-2 rounded-t-lg whitespace-nowrap {{ $index === 0 ? 'text-blue-500 border-blue-500' : 'text-gray-400 border-transparent hover:text-gray-300 hover:border-gray-300' }}" 
                            id="sport-tab-{{ $sport->id }}" 
                            data-tabs-target="#sport-content-{{ $sport->id }}" 
                            type="button" 
                            role="tab" 
                            aria-controls="sport-content-{{ $sport->id }}" 
                            aria-selected="{{ $index === 0 ? 'true' : 'false' }}">
                        {{ $sport->name }}
                        <span class="ml-1 text-xs px-2 py-0.5 bg-gray-700 rounded-full">
                            {{ count($sport->plans) }}
                        </span>
                    </button>
                </li>
            @endforeach
        </ul>
    </div>

    <!-- Tab Content for each sport -->
    <div id="sportTabsContent">
        @foreach($sports as $index => $sport)
            <div class="bg-gray-800 p-4 rounded-lg {{ $index === 0 ? 'block' : 'hidden' }}" 
                 id="sport-content-{{ $sport->id }}" 
                 role="tabpanel" 
                 aria-labelledby="sport-tab-{{ $sport->id }}">
                
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-white flex items-center">
                        {{ $sport->name }} Pricing Plans
                        <span class="ml-2 {{ $sport->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} text-xs font-medium px-2.5 py-0.5 rounded">
                            {{ $sport->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </h2>
                    <button class="add-plan-btn px-3 py-1 bg-green-600 text-white rounded-lg text-sm" 
                            data-sport="{{ $sport->slug }}" 
                            data-sport-id="{{ $sport->id }}" 
                            data-sport-name="{{ $sport->name }}">
                        <i class="fas fa-plus mr-1"></i> Add Plan
                    </button>
                </div>
                
                @if(count($sport->plans) > 0)
                    <div class="overflow-x-auto relative md:rounded-lg">
                        <div class="w-full md:px-0">
                            <table class="w-full text-sm text-left text-gray-400">
                                <thead class="text-xs uppercase bg-gray-700 text-gray-400">
                                    <tr>
                                        <th scope="col" class="py-3 px-6">Name</th>
                                        <th scope="col" class="py-3 px-6">Plan Type</th>
                                        <th scope="col" class="py-3 px-6">Price</th>
                                        <th scope="col" class="py-3 px-6 hidden md:table-cell">Status</th>
                                        <th scope="col" class="py-3 px-6 hidden md:table-cell">Featured</th>
                                        <th scope="col" class="py-3 px-6 hidden md:table-cell">Promo</th>
                                        <th scope="col" class="py-3 px-6">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($sport->plans as $plan)
                                        <tr class="border-b bg-gray-900 border-gray-700 hover:bg-gray-800 transition-colors">
                                            <td class="py-4 px-6 font-medium text-white whitespace-nowrap">
                                                {{ $plan->name }}
                                                <!-- Mobile indicators -->
                                                <div class="flex flex-wrap gap-1 mt-1 md:hidden">
                                                    @if($plan->is_active)
                                                        <span class="px-1.5 py-0.5 rounded-full text-xs bg-green-100 text-green-800">Active</span>
                                                    @else
                                                        <span class="px-1.5 py-0.5 rounded-full text-xs bg-red-100 text-red-800">Inactive</span>
                                                    @endif
                                                    
                                                    @if($plan->is_featured)
                                                        <span class="px-1.5 py-0.5 rounded-full text-xs bg-yellow-100 text-yellow-800">Featured</span>
                                                    @endif
                                                    
                                                    @if($plan->is_promo)
                                                        <span class="px-1.5 py-0.5 rounded-full text-xs bg-purple-100 text-purple-800">Promo</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="py-4 px-6">
                                                <span class="px-2 py-1 rounded text-xs 
                                                    {{ $plan->plan === 'monthly' ? 'bg-blue-200 text-blue-800' : 
                                                    ($plan->plan === 'daily' ? 'bg-purple-200 text-purple-800' : 
                                                    'bg-green-200 text-green-800') }}">
                                                    {{ ucfirst($plan->plan) }}
                                                </span>
                                            </td>
                                            <td class="py-4 px-6">
                                                ₱{{ number_format($plan->price, 2) }}
                                                @if($plan->is_promo && $plan->original_price)
                                                    <span class="text-xs line-through text-gray-500">
                                                        ₱{{ number_format($plan->original_price, 2) }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="py-4 px-6 hidden md:table-cell">
                                                <span class="px-2 py-1 rounded-full text-xs {{ $plan->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ $plan->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td class="py-4 px-6 hidden md:table-cell">
                                                @if($plan->is_featured)
                                                    <span class="text-yellow-300">
                                                        <i class="fas fa-star"></i>
                                                    </span>
                                                @else
                                                    <span class="text-gray-600">
                                                        <i class="far fa-star"></i>
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="py-4 px-6 hidden md:table-cell">
                                                @if($plan->is_promo)
                                                    <span class="text-red-500">
                                                        <i class="fas fa-tags"></i>
                                                        @if($plan->promo_ends_at)
                                                            <span class="text-xs ml-1">
                                                                Until {{ $plan->promo_ends_at->format('M d, Y') }}
                                                            </span>
                                                        @endif
                                                    </span>
                                                @else
                                                    <span class="text-gray-600">
                                                        <i class="far fa-circle"></i>
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="py-4 px-6">
                                                <div class="flex space-x-2">
                                                    <button class="edit-plan-btn text-blue-500 hover:text-blue-300 transition" data-plan-id="{{ $plan->id }}">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="delete-plan-btn text-red-500 hover:text-red-300 transition" data-plan-id="{{ $plan->id }}" data-plan-name="{{ $plan->name }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @else
                    <div class="bg-gray-900 rounded-lg p-6 text-center">
                        <p class="text-gray-400">No pricing plans found for {{ $sport->name }}.</p>
                        <button class="mt-4 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm add-plan-btn" 
                                data-sport="{{ $sport->slug }}" 
                                data-sport-id="{{ $sport->id }}" 
                                data-sport-name="{{ $sport->name }}">
                            <i class="fas fa-plus mr-1"></i> Add Your First Plan
                        </button>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>

<!-- Add/Edit Plan Modal -->
<div id="planFormModal" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full flex items-center justify-center">
    <div class="relative w-full max-w-2xl max-h-full">
        <!-- Modal content -->
        <div class="relative bg-gray-800 rounded-lg shadow">
            <!-- Modal header -->
            <div class="flex items-center justify-between p-4 border-b border-gray-700">
                <h3 class="text-xl font-semibold text-white modal-title">
                    Add New Pricing Plan
                </h3>
                <button type="button" class="close-modal text-gray-400 bg-transparent hover:bg-gray-700 hover:text-white rounded-lg text-sm p-1.5 ml-auto inline-flex items-center">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <!-- Modal body -->
            <form id="planForm">
                <div class="p-6 space-y-6">
                    <input type="hidden" id="planId" name="id">
                    <input type="hidden" id="sportType" name="type">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="planName" class="block mb-2 text-sm font-medium text-white">Plan Name <span class="text-red-500">*</span></label>
                            <input type="text" id="planName" name="name" class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" 
                                required 
                                maxlength="100"
                                placeholder="e.g. Monthly Membership">
                            <p class="mt-1 text-xs text-gray-400">Required. Maximum 100 characters.</p>
                        </div>
                        <div>
                            <label for="planDuration" class="block mb-2 text-sm font-medium text-white">Plan Type <span class="text-red-500">*</span></label>
                            <select id="planDuration" name="plan" class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                                <option value="">Select plan type</option>
                                <option value="monthly">Monthly</option>
                                <option value="daily">Daily</option>
                                <option value="per-session">Per Session</option>
                            </select>
                            <p class="mt-1 text-xs text-gray-400">Required. The billing frequency.</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="planPrice" class="block mb-2 text-sm font-medium text-white">Price (₱) <span class="text-red-500">*</span></label>
                            <input type="number" id="planPrice" name="price" min="0" step="0.01" class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" 
                                required
                                placeholder="0.00">
                            <p class="mt-1 text-xs text-gray-400">Required. The current price in Philippine Peso.</p>
                        </div>
                        <div>
                            <label for="displayOrder" class="block mb-2 text-sm font-medium text-white">Display Order</label>
                            <input type="number" id="displayOrder" name="display_order" min="0" step="1" class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                placeholder="0">
                            <p class="mt-1 text-xs text-gray-400">Optional. Lower numbers appear first.</p>
                        </div>
                    </div>
                    
                    <div>
                        <label class="relative inline-flex items-center mb-1 cursor-pointer">
                            <input type="checkbox" id="isPromo" name="is_promo" value="1" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-700 rounded-full peer peer-focus:ring-4 peer-focus:ring-blue-800 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            <span class="ml-3 text-sm font-medium text-white">Promotional Price</span>
                        </label>
                        <p class="ml-14 text-xs text-gray-400 mb-3">Enable if this plan is on sale or special promotion.</p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 promo-fields hidden">
                        <div>
                            <label for="originalPrice" class="block mb-2 text-sm font-medium text-white">Original Price (₱) <span class="text-red-500">*</span></label>
                            <input type="number" id="originalPrice" name="original_price" min="0" step="0.01" class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                placeholder="0.00">
                            <p class="mt-1 text-xs text-gray-400">The regular price before discount.</p>
                        </div>
                        <div>
                            <label for="promoEndsAt" class="block mb-2 text-sm font-medium text-white">Promo End Date</label>
                            <input type="date" id="promoEndsAt" name="promo_ends_at" min="{{ date('Y-m-d') }}" class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            <p class="mt-1 text-xs text-gray-400">Optional. Leave empty if promotion has no end date.</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 md:gap-8">
                        <div>
                            <label class="relative inline-flex items-center mb-1 cursor-pointer">
                                <input type="checkbox" id="isActive" name="is_active" value="1" class="sr-only peer" checked>
                                <div class="w-11 h-6 bg-gray-700 rounded-full peer peer-focus:ring-4 peer-focus:ring-blue-800 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                <span class="ml-3 text-sm font-medium text-white">Active</span>
                            </label>
                            <p class="ml-14 text-xs text-gray-400">Make this plan visible on the pricing page.</p>
                        </div>
                        <div>
                            <label class="relative inline-flex items-center mb-1 cursor-pointer">
                                <input type="checkbox" id="isFeatured" name="is_featured" value="1" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-700 rounded-full peer peer-focus:ring-4 peer-focus:ring-blue-800 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                <span class="ml-3 text-sm font-medium text-white">Featured</span>
                            </label>
                            <p class="ml-14 text-xs text-gray-400">Highlight this as a recommended plan.</p>
                        </div>
                    </div>
                    
                    <div>
                        <label for="planFeatures" class="block mb-2 text-sm font-medium text-white">Features (One per line) <span class="text-red-500">*</span></label>
                        <textarea id="planFeatures" name="features" rows="4" class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" 
                            required
                            placeholder="Free use of gym&#10;Shower access&#10;Free WiFi"></textarea>
                        <p class="mt-1 text-xs text-gray-400">Enter one feature per line. These will be displayed as bullet points.</p>
                    </div>
                </div>
                <!-- Modal footer -->
                <div class="flex items-center p-6 space-x-2 border-t border-gray-700">
                    <button type="submit" class="save-plan-btn text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                        Save Plan
                    </button>
                    <button type="button" class="close-modal text-gray-300 bg-gray-700 hover:bg-gray-600 focus:ring-4 focus:outline-none focus:ring-gray-600 rounded-lg border border-gray-600 text-sm font-medium px-5 py-2.5">
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
    // Tab functionality
    const tabs = document.querySelectorAll('[data-tabs-target]');
    const tabContents = document.querySelectorAll('[role="tabpanel"]');
    
    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            tabContents.forEach(content => content.classList.add('hidden'));
            tabs.forEach(t => {
                t.classList.remove('text-blue-500', 'border-blue-500');
                t.classList.add('text-gray-400', 'border-transparent');
                t.setAttribute('aria-selected', false);
            });
            
            const target = document.querySelector(tab.dataset.tabsTarget);
            target.classList.remove('hidden');
            
            tab.classList.remove('text-gray-400', 'border-transparent');
            tab.classList.add('text-blue-500', 'border-blue-500');
            tab.setAttribute('aria-selected', true);
        });
    });
    
    // Mobile responsiveness for tables
    const tables = document.querySelectorAll('table');
    tables.forEach(table => {
        const tableWrapper = table.parentElement;
        if (tableWrapper.classList.contains('overflow-x-auto')) {
            // Add a helper message for mobile users
            const mobileHelper = document.createElement('p');
            mobileHelper.className = 'text-xs text-gray-500 mt-2 text-center md:hidden';
            mobileHelper.textContent = 'Swipe horizontally to see more columns';
            tableWrapper.appendChild(mobileHelper);
        }
    });
    
    // Modal functionality
    const modal = document.getElementById('planFormModal');
    const addPlanBtns = document.querySelectorAll('.add-plan-btn');
    const closeModalBtns = document.querySelectorAll('.close-modal');
    const planForm = document.getElementById('planForm');
    const sportTypeInput = document.getElementById('sportType');
    const modalTitle = document.querySelector('.modal-title');
    
    // Main Add Plan button
    document.getElementById('addPlanBtn').addEventListener('click', () => {
        resetForm();
        // Select first sport type by default
        const firstSport = document.querySelector('[data-sport]');
        if (firstSport) {
            sportTypeInput.value = firstSport.dataset.sport;
            modalTitle.textContent = `Add New Plan for ${firstSport.dataset.sportName}`;
        }
        togglePromoFields();
        openModal();
    });
    
    // Add Plan buttons for each sport
    addPlanBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            resetForm();
            sportTypeInput.value = btn.dataset.sport;
            modalTitle.textContent = `Add New Plan for ${btn.dataset.sportName}`;
            togglePromoFields();
            openModal();
        });
    });
    
    // Form reset function
    function resetForm() {
        planForm.reset();
        document.getElementById('planId').value = '';
        
        // Set minimum date for promo end date to today
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('promoEndsAt').min = today;
        
        // Clear validation classes and messages
        const formInputs = planForm.querySelectorAll('input, select, textarea');
        formInputs.forEach(input => {
            input.classList.remove('border-red-500', 'border-green-500');
            
            // Remove any existing error messages
            const errorMsg = input.parentElement.querySelector('.error-message');
            if (errorMsg) {
                errorMsg.remove();
            }
        });
    }
    
    // Open modal function
    function openModal() {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        // Focus on the first input field for better UX
        setTimeout(() => {
            document.getElementById('planName').focus();
        }, 100);
    }
    
    // Close Modal buttons
    closeModalBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        });
    });
    
    // Close modal when clicking outside
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    });
    
    // Promotional price toggle
    const isPromoCheckbox = document.getElementById('isPromo');
    const promoFields = document.querySelector('.promo-fields');
    const originalPriceInput = document.getElementById('originalPrice');
    
    isPromoCheckbox.addEventListener('change', togglePromoFields);
    
    function togglePromoFields() {
        if (isPromoCheckbox.checked) {
            promoFields.classList.remove('hidden');
            // Make original price required if promo is checked
            originalPriceInput.required = true;
        } else {
            promoFields.classList.add('hidden');
            // Make original price not required if promo is unchecked
            originalPriceInput.required = false;
        }
    }
    
    // Live form validation
    const formInputs = planForm.querySelectorAll('input[required], select[required], textarea[required]');
    
    formInputs.forEach(input => {
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
        } else {
            field.classList.remove('border-red-500');
            field.classList.add('border-green-500');
        }
    }
    
    // Form submission with validation
    planForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validate all required fields
        let isValid = true;
        formInputs.forEach(input => {
            if (!input.checkValidity()) {
                validateField(input);
                isValid = false;
            }
        });
        
        // Also validate original price if promo is checked
        if (isPromoCheckbox.checked && !originalPriceInput.checkValidity()) {
            validateField(originalPriceInput);
            isValid = false;
        }
        
        // Prevent submission if validation fails
        if (!isValid) {
            // Show error message at the top of the form
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
        const submitButton = document.querySelector('.save-plan-btn');
        const originalButtonText = submitButton.innerHTML;
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Saving...';
        
        const formData = new FormData(planForm);
        const planId = document.getElementById('planId').value;
        
        // Convert features textarea to array
        const featuresText = formData.get('features');
        const featuresArray = featuresText.split('\n')
            .map(feature => feature.trim())
            .filter(feature => feature.length > 0);
        
        // Build the data object
        const data = {
            name: formData.get('name'),
            type: formData.get('type'),
            plan: formData.get('plan'),
            price: formData.get('price'),
            display_order: formData.get('display_order') || 0,
            is_active: formData.get('is_active') ? true : false,
            is_featured: formData.get('is_featured') ? true : false,
            is_promo: formData.get('is_promo') ? true : false,
            features: featuresArray
        };
        
        // Add promo fields if promo is checked
        if (formData.get('is_promo')) {
            data.original_price = formData.get('original_price');
            data.promo_ends_at = formData.get('promo_ends_at');
        }
        
        // Determine if we're creating or updating
        const url = planId ? `/admin/pricing/${planId}` : '/admin/pricing';
        const method = planId ? 'PUT' : 'POST';
        
        fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
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
                submitButton.disabled = false;
                submitButton.innerHTML = originalButtonText;
                
                let errorMessage = 'Please check the form and try again.';
                if (result.errors) {
                    errorMessage = Object.values(result.errors).flat().join('<br>');
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
    
    // Edit plan buttons
    const editButtons = document.querySelectorAll('.edit-plan-btn');
    
    editButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const planId = btn.dataset.planId;
            fetchPlanDetails(planId);
        });
    });
    
    // Delete plan buttons
    const deleteButtons = document.querySelectorAll('.delete-plan-btn');
    
    deleteButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const planId = btn.dataset.planId;
            const planName = btn.dataset.planName;
            confirmDeletePlan(planId, planName);
        });
    });
    
    // Fetch plan details for editing
    function fetchPlanDetails(planId) {
        // Show loading indicator
        Swal.fire({
            title: 'Loading...',
            text: 'Fetching plan details',
            showConfirmButton: false,
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        fetch(`/admin/pricing/${planId}`)
            .then(response => response.json())
            .then(result => {
                Swal.close();
                
                if (result.success) {
                    populateForm(result.plan);
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Failed to fetch plan details',
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
    
    // Populate form with plan details
    function populateForm(plan) {
        resetForm();
        
        document.getElementById('planId').value = plan.id;
        document.getElementById('sportType').value = plan.type;
        document.getElementById('planName').value = plan.name;
        document.getElementById('planDuration').value = plan.plan;
        document.getElementById('planPrice').value = plan.price;
        document.getElementById('displayOrder').value = plan.display_order || 0;
        document.getElementById('isActive').checked = plan.is_active;
        document.getElementById('isFeatured').checked = plan.is_featured;
        document.getElementById('isPromo').checked = plan.is_promo;
        
        // Set minimum date for promo_ends_at to today
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('promoEndsAt').min = today;
        
        if (plan.is_promo) {
            document.getElementById('originalPrice').value = plan.original_price || '';
            if (plan.promo_ends_at) {
                const date = new Date(plan.promo_ends_at);
                const formattedDate = date.toISOString().split('T')[0];
                
                // Only set the date if it's not in the past
                if (formattedDate >= today) {
                    document.getElementById('promoEndsAt').value = formattedDate;
                } else {
                    // If date is in the past, clear the field
                    document.getElementById('promoEndsAt').value = '';
                }
            } else {
                document.getElementById('promoEndsAt').value = '';
            }
        }
        
        // Format features array back to text
        const featuresText = Array.isArray(plan.features) ? plan.features.join('\n') : '';
        document.getElementById('planFeatures').value = featuresText;
        
        togglePromoFields();
        
        // Find sport name for modal title
        const sportElement = document.querySelector(`[data-sport="${plan.type}"]`);
        const sportName = sportElement ? sportElement.dataset.sportName : plan.type.charAt(0).toUpperCase() + plan.type.slice(1);
        
        modalTitle.textContent = `Edit Plan for ${sportName}`;
        openModal();
    }
    
    // Confirm delete plan
    function confirmDeletePlan(planId, planName) {
        Swal.fire({
            title: 'Are you sure?',
            html: `You are about to delete the plan <b>${planName}</b>.<br><br>This action cannot be undone.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#EF4444',
            cancelButtonColor: '#4B5563',
            confirmButtonText: 'Yes, delete it!',
            background: '#1F2937',
            color: '#FFFFFF',
        }).then((result) => {
            if (result.isConfirmed) {
                deletePlan(planId);
            }
        });
    }
    
    // Delete plan
    function deletePlan(planId) {
        // Show loading indicator
        Swal.fire({
            title: 'Deleting...',
            text: 'Removing the pricing plan',
            showConfirmButton: false,
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        fetch(`/admin/pricing/${planId}`, {
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
                    text: result.message || 'Failed to delete plan',
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
});
</script>
@endpush
@endsection 