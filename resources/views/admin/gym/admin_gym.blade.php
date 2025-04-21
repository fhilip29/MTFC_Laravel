<!-- resources/views/admin/gym/admin_gym.blade.php -->

@extends('layouts.admin')

@section('content')
<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    /* FilePond customization */
    .filepond--panel-root {
        background-color: #374151;
        border: 1px solid #4B5563;
    }
    
    .filepond--drop-label {
        color: #9CA3AF;
    }
    
    .filepond--label-action {
        color: #ec4899;
        text-decoration-color: #ec4899;
    }
    
    .filepond--root .filepond--credits {
        display: none;
    }
    
    .filepond--file-action-button {
        background-color: rgba(255, 255, 255, 0.5);
    }
    
    .filepond--image-preview-overlay-success {
        color: #10B981;
    }
</style>

<div class="p-6" x-data="{ 
    showAddModal: false, 
    showEditModal: false, 
    currentEquipment: null,
    previewImage: null,
    
    openEditModal(equipment) {
        this.currentEquipment = JSON.parse(JSON.stringify(equipment));
        this.previewImage = equipment.image_url;
        this.showEditModal = true;
        
        // Initialize FilePond in the edit modal after a short delay
        setTimeout(() => {
            window.initializeEditFilePond();
        }, 100);
    },
    
    handleImagePreview(event, previewElement) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                document.getElementById(previewElement).src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    }
}">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-white">Equipment Management</h1>
        <div class="flex gap-4">
            <a href="{{ route('admin.gym.maintenance') }}" class="bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-2 rounded flex items-center">
                <i class="fas fa-clipboard-list mr-2"></i> Maintenance Logs
            </a>
            <a href="{{ route('admin.gym.vendors') }}" class="bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-2 rounded flex items-center">
                <i class="fas fa-truck-loading mr-2"></i> Manage Vendors
            </a>
            <button @click="showAddModal = true" class="bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-2 rounded flex items-center">
                <i class="fas fa-plus mr-2"></i> Add Equipment
            </button>
        </div>
    </div>

    <!-- Equipment Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-gray-800 rounded-lg p-6 shadow-lg flex items-center">
            <div class="rounded-full bg-red-600 p-3 mr-4">
                <i class="fas fa-dumbbell text-white text-xl"></i>
            </div>
            <div>
                <p class="text-gray-400 text-sm">Total Equipment</p>
                <p class="text-2xl font-bold text-white">{{ count($equipments) }}</p>
            </div>
        </div>
        <div class="bg-gray-800 rounded-lg p-6 shadow-lg flex items-center">
            <div class="rounded-full bg-green-600 p-3 mr-4">
                <i class="fas fa-truck-loading text-white text-xl"></i>
            </div>
            <div>
                <p class="text-gray-400 text-sm">Total Vendors</p>
                <p class="text-2xl font-bold text-white">{{ count($vendors) }}</p>
            </div>
        </div>
        <div class="bg-gray-800 rounded-lg p-6 shadow-lg flex items-center">
            <div class="rounded-full bg-blue-600 p-3 mr-4">
                <i class="fas fa-tools text-white text-xl"></i>
            </div>
            <div>
                <p class="text-gray-400 text-sm">Recent Maintenance</p>
                <p class="text-2xl font-bold text-white">{{ $maintenanceLogs->count() }}</p>
            </div>
        </div>
    </div>

    <!-- Equipment Table -->
    <div class="bg-gray-800 rounded-lg shadow-lg overflow-hidden">
        <div class="p-4 border-b border-gray-700 flex flex-col md:flex-row justify-between items-center gap-4">
            <h2 class="text-xl font-semibold text-white">Equipment Inventory</h2>
            <div class="flex flex-col md:flex-row gap-3 items-center w-full md:w-auto">
                <div class="relative w-full md:w-48">
                    <select id="qualityFilter" class="bg-gray-700 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5">
                        <option value="all">All Qualities</option>
                        <option value="new">New</option>
                        <option value="good">Good</option>
                        <option value="fair">Fair</option>
                        <option value="rusty">Rusty</option>
                    </select>
                </div>
                <div class="relative w-full md:w-64">
                    <input type="text" id="searchEquipment" placeholder="Search equipment..." class="bg-gray-700 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full pl-10 p-2.5">
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
                        <th scope="col" class="px-6 py-3 text-left tracking-wider">Image</th>
                        <th scope="col" class="px-6 py-3 text-left tracking-wider">Name</th>
                        <th scope="col" class="px-6 py-3 text-left tracking-wider">Quantity</th>
                        <th scope="col" class="px-6 py-3 text-left tracking-wider">Quality</th>
                        <th scope="col" class="px-6 py-3 text-left tracking-wider">Vendor</th>
                        <th scope="col" class="px-6 py-3 text-left tracking-wider">Date Purchased</th>
                        <th scope="col" class="px-6 py-3 text-left tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @forelse($equipments as $equipment)
                    <tr class="hover:bg-gray-700 transition">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <img src="{{ $equipment->getImageUrlAttribute() }}" alt="{{ $equipment->name }}" class="h-12 w-12 object-cover rounded">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap font-medium">{{ $equipment->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $equipment->qty }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full {{ 
                                $equipment->quality === 'new' ? 'bg-green-600' : 
                                ($equipment->quality === 'good' ? 'bg-blue-600' : 
                                ($equipment->quality === 'fair' ? 'bg-yellow-600' : 'bg-red-600')) 
                            }}">
                                {{ ucfirst($equipment->quality) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $equipment->vendor->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if(is_string($equipment->date_purchased))
                                {{ $equipment->date_purchased }}
                            @else
                                {{ $equipment->date_purchased->format('M d, Y') }}
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.gym.equipment.show', $equipment->id) }}" class="text-blue-400 hover:text-blue-300">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button @click="openEditModal({{ $equipment }})" class="text-yellow-400 hover:text-yellow-300">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="confirmDelete('{{ $equipment->id }}', '{{ $equipment->name }}')" class="text-red-400 hover:text-red-300">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-400">
                            No equipment found. Click "Add Equipment" to add your first equipment.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Equipment Modal -->
    <div x-show="showAddModal" class="fixed inset-0 z-50 overflow-auto bg-black bg-opacity-50 flex items-center justify-center" style="display: none;">
        <div class="relative bg-gray-800 rounded-lg shadow-lg max-w-3xl w-full mx-auto p-6 border border-gray-700" @click.away="showAddModal = false">
            <div class="flex justify-between items-center border-b border-gray-700 pb-3 mb-4">
                <h3 class="text-xl font-semibold text-white">Add New Equipment</h3>
                <button @click="showAddModal = false" class="text-gray-400 hover:text-white">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form action="{{ route('admin.gym.equipment.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-300 mb-1">Equipment Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5 @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="qty" class="block text-sm font-medium text-gray-300 mb-1">Quantity</label>
                        <input type="number" name="qty" id="qty" value="{{ old('qty') }}" min="1" required class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5 @error('qty') border-red-500 @enderror">
                        @error('qty')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-300 mb-1">Price (PHP)</label>
                        <input type="number" name="price" id="price" value="{{ old('price') }}" min="0" step="0.01" required class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5 @error('price') border-red-500 @enderror">
                        @error('price')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="quality" class="block text-sm font-medium text-gray-300 mb-1">Quality</label>
                        <select name="quality" id="quality" required class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5 @error('quality') border-red-500 @enderror">
                            <option value="new" {{ old('quality') == 'new' ? 'selected' : '' }}>New</option>
                            <option value="good" {{ old('quality') == 'good' || !old('quality') ? 'selected' : '' }}>Good</option>
                            <option value="fair" {{ old('quality') == 'fair' ? 'selected' : '' }}>Fair</option>
                            <option value="rusty" {{ old('quality') == 'rusty' ? 'selected' : '' }}>Rusty</option>
                        </select>
                        @error('quality')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="date_purchased" class="block text-sm font-medium text-gray-300 mb-1">Date Purchased</label>
                        <input type="date" name="date_purchased" id="date_purchased" value="{{ old('date_purchased') }}" required class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5 @error('date_purchased') border-red-500 @enderror">
                        @error('date_purchased')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="vendor_id" class="block text-sm font-medium text-gray-300 mb-1">Vendor</label>
                        <select name="vendor_id" id="vendor_id" required class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5 @error('vendor_id') border-red-500 @enderror">
                            @foreach($vendors as $vendor)
                                <option value="{{ $vendor->id }}" {{ old('vendor_id') == $vendor->id ? 'selected' : '' }}>{{ $vendor->name }}</option>
                            @endforeach
                        </select>
                        @error('vendor_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-300 mb-1">Description</label>
                        <textarea name="description" id="description" rows="3" class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label for="image" class="block text-sm font-medium text-gray-300 mb-1">Image</label>
                        <div class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg">
                            <input type="file" class="filepond" name="image" id="image-upload-add" 
                                accept="image/png, image/jpeg, image/jpg" />
                        </div>
                        <div class="mt-2 text-sm text-gray-400">Recommended size: 400x400px. Supported formats: JPG, PNG</div>
                        @error('image')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="flex justify-end mt-6 space-x-3">
                    <button type="button" @click="showAddModal = false" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded">Cancel</button>
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">Save Equipment</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Equipment Modal -->
    <div x-show="showEditModal" class="fixed inset-0 z-50 overflow-auto bg-black bg-opacity-50 flex items-center justify-center" style="display: none;">
        <div class="relative bg-gray-800 rounded-lg shadow-lg max-w-3xl w-full mx-auto p-6 border border-gray-700" @click.away="showEditModal = false">
            <div class="flex justify-between items-center border-b border-gray-700 pb-3 mb-4">
                <h3 class="text-xl font-semibold text-white">Edit Equipment</h3>
                <button @click="showEditModal = false" class="text-gray-400 hover:text-white">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form :action="'/admin/equipment/' + currentEquipment?.id" method="POST" enctype="multipart/form-data" x-ref="editForm">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" :value="currentEquipment?.id">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="edit_name" class="block text-sm font-medium text-gray-300 mb-1">Equipment Name</label>
                        <input type="text" name="name" id="edit_name" required class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5 @error('name') border-red-500 @enderror" :value="currentEquipment?.name">
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="edit_qty" class="block text-sm font-medium text-gray-300 mb-1">Quantity</label>
                        <input type="number" name="qty" id="edit_qty" min="1" required class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5 @error('qty') border-red-500 @enderror" :value="currentEquipment?.qty">
                        @error('qty')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="edit_price" class="block text-sm font-medium text-gray-300 mb-1">Price (PHP)</label>
                        <input type="number" name="price" id="edit_price" min="0" step="0.01" required class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5 @error('price') border-red-500 @enderror" :value="currentEquipment?.price">
                        @error('price')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="edit_quality" class="block text-sm font-medium text-gray-300 mb-1">Quality</label>
                        <select name="quality" id="edit_quality" required class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5 @error('quality') border-red-500 @enderror" x-model="currentEquipment?.quality">
                            <option value="new">New</option>
                            <option value="good">Good</option>
                            <option value="fair">Fair</option>
                            <option value="rusty">Rusty</option>
                        </select>
                        @error('quality')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="edit_date_purchased" class="block text-sm font-medium text-gray-300 mb-1">Date Purchased</label>
                        <input type="date" name="date_purchased" id="edit_date_purchased" required class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5 @error('date_purchased') border-red-500 @enderror" x-bind:value="currentEquipment?.date_purchased ? currentEquipment.date_purchased.split('T')[0] : ''">
                        @error('date_purchased')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="edit_vendor_id" class="block text-sm font-medium text-gray-300 mb-1">Vendor</label>
                        <select name="vendor_id" id="edit_vendor_id" required class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5 @error('vendor_id') border-red-500 @enderror" x-model="currentEquipment?.vendor_id">
                            @foreach($vendors as $vendor)
                                <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                            @endforeach
                        </select>
                        @error('vendor_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label for="edit_description" class="block text-sm font-medium text-gray-300 mb-1">Description</label>
                        <textarea name="description" id="edit_description" rows="3" class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5 @error('description') border-red-500 @enderror" x-text="currentEquipment?.description"></textarea>
                        @error('description')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-300 mb-1">Equipment Image</label>
                        <div class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg">
                            <input type="file" class="filepond" name="image" id="image-upload-edit" 
                                accept="image/png, image/jpeg, image/jpg" />
                        </div>
                        <div class="mt-2 text-sm text-gray-400">Recommended size: 400x400px. Supported formats: JPG, PNG</div>
                        @error('image')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="flex justify-end mt-6 space-x-3">
                    <button type="button" @click="showEditModal = false" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded">Cancel</button>
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">Update Equipment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function confirmDelete(id, name) {
    Swal.fire({
        title: 'Delete Equipment?',
        text: `Are you sure you want to delete ${name}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#4b5563',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/equipment/${id}`;
            form.innerHTML = `
                @csrf
                @method('DELETE')
            `;
            document.body.appendChild(form);
            form.submit();
        }
    });
}

// Search functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchEquipment');
    const qualityFilter = document.getElementById('qualityFilter');
    const rows = document.querySelectorAll('tbody tr');
    
    // Function to apply all filters
    function applyFilters() {
        const searchText = searchInput.value.toLowerCase();
        const qualityValue = qualityFilter.value;
        
        rows.forEach(row => {
            // Skip empty rows
            if (row.cells.length <= 1) return;
            
            // Get the quality cell (4th column, index 3)
            const qualityCell = row.cells[3];
            const qualityText = qualityCell.textContent.trim().toLowerCase();
            
            // Check if row matches search text
            const matchesSearch = Array.from(row.querySelectorAll('td')).some(cell => 
                cell.textContent.toLowerCase().includes(searchText)
            );
            
            // Check if row matches quality filter
            const matchesQuality = qualityValue === 'all' || qualityText.includes(qualityValue);
            
            // Show row only if it matches both filters
            row.style.display = (matchesSearch && matchesQuality) ? '' : 'none';
        });
    }
    
    // Add event listeners
    searchInput.addEventListener('keyup', applyFilters);
    qualityFilter.addEventListener('change', applyFilters);
    
    // Show success message if it exists in the session
    @if(session('success'))
        Swal.fire({
            title: 'Success!',
            text: "{{ session('success') }}",
            icon: 'success',
            confirmButtonColor: '#dc2626'
        });
    @endif
    
    @if(session('error'))
        Swal.fire({
            title: 'Error!',
            text: "{{ session('error') }}",
            icon: 'error',
            confirmButtonColor: '#dc2626'
        });
    @endif
});

document.addEventListener('DOMContentLoaded', function() {
    // Auto-open modals if there are validation errors
    @if($errors->any())
        // Wait a moment for Alpine to initialize
        setTimeout(() => {
            const app = document.querySelector('[x-data]').__x.$data;
            
            @if(old('_method') == 'PUT')
                // For edit form, recreate the equipment object from old values
                const equipment = {
                    id: {{ old('id', 0) }},
                    name: "{{ old('name', '') }}",
                    description: "{{ old('description', '') }}",
                    qty: {{ old('qty', 0) }},
                    price: {{ old('price', 0) }},
                    quality: "{{ old('quality', 'good') }}",
                    date_purchased: "{{ old('date_purchased', '') }}",
                    vendor_id: {{ old('vendor_id', 0) }},
                    image_url: "{{ old('image_url', '/images/placeholder.png') }}"
                };
                app.openEditModal(equipment);
            @else
                // For add form
                app.showAddModal = true;
            @endif
        }, 100);
    @endif
    
    // Initialize FilePond
    FilePond.registerPlugin(
        FilePondPluginFileValidateType,
        FilePondPluginFileValidateSize,
        FilePondPluginImagePreview
    );
    
    // Create the FilePond instance for add equipment form
    const addImagePond = FilePond.create(document.getElementById('image-upload-add'), {
        acceptedFileTypes: ['image/png', 'image/jpeg', 'image/jpg'],
        allowFileTypeValidation: true,
        allowFileSizeValidation: true,
        maxFileSize: '2MB',
        labelIdle: 'Drag & Drop your image or <span class="filepond--label-action">Browse</span>',
        labelFileTypeNotAllowed: 'Invalid file type. Only JPG and PNG are allowed.',
        labelMaxFileSize: 'File is too large. Max size is {filesize}.',
        stylePanelLayout: 'compact',
        styleButtonRemoveItemPosition: 'right',
        styleLoadIndicatorPosition: 'center',
        styleProgressIndicatorPosition: 'center',
        credits: false
    });
    
    // Handle the edit modal image upload
    window.initializeEditFilePond = function() {
        const editImageInput = document.getElementById('image-upload-edit');
        if (!editImageInput) return;
        
        // Create the FilePond instance for edit equipment form
        const editImagePond = FilePond.create(editImageInput, {
            acceptedFileTypes: ['image/png', 'image/jpeg', 'image/jpg'],
            allowFileTypeValidation: true,
            allowFileSizeValidation: true,
            maxFileSize: '2MB',
            labelIdle: 'Drag & Drop your image or <span class="filepond--label-action">Browse</span>',
            labelFileTypeNotAllowed: 'Invalid file type. Only JPG and PNG are allowed.',
            labelMaxFileSize: 'File is too large. Max size is {filesize}.',
            stylePanelLayout: 'compact',
            styleButtonRemoveItemPosition: 'right',
            styleLoadIndicatorPosition: 'center',
            styleProgressIndicatorPosition: 'center',
            credits: false
        });
    };
});
</script>
@endsection
