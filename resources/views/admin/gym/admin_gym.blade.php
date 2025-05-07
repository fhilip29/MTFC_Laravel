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

<div class="p-3 md:p-6" x-data="{ 
    showAddModal: false, 
    showEditModal: false, 
    currentEquipment: null,
    previewImage: null,
    
    openEditModal(equipment) {
        this.currentEquipment = JSON.parse(JSON.stringify(equipment));
        this.previewImage = equipment.image_url;
        this.showEditModal = true;
    },
    
    handleImagePreview(event, previewElement, placeholderElement, previewContainer) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                document.getElementById(previewElement).src = e.target.result;
                document.getElementById(previewContainer).classList.remove('hidden');
                document.getElementById(placeholderElement).classList.add('hidden');
            };
            reader.readAsDataURL(file);
        }
    }
}">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <h1 class="text-xl sm:text-2xl font-bold text-white">Equipment Management</h1>
        <div class="flex flex-wrap gap-2 sm:gap-4 w-full sm:w-auto">
            <a href="{{ route('admin.gym.maintenance') }}" class="bg-gray-800 hover:bg-gray-700 text-white font-semibold px-3 py-2 text-sm md:px-4 md:py-2 rounded flex items-center justify-center">
                <i class="fas fa-clipboard-list mr-2"></i> <span class="whitespace-nowrap">Maintenance</span>
            </a>
            <a href="{{ route('admin.gym.vendors') }}" class="bg-gray-800 hover:bg-gray-700 text-white font-semibold px-3 py-2 text-sm md:px-4 md:py-2 rounded flex items-center justify-center">
                <i class="fas fa-truck-loading mr-2"></i> <span class="whitespace-nowrap">Vendors</span>
            </a>
            <button @click="showAddModal = true" class="bg-gray-800 hover:bg-gray-700 text-white font-semibold px-3 py-2 text-sm md:px-4 md:py-2 rounded flex items-center justify-center">
                <i class="fas fa-plus mr-2"></i> <span class="whitespace-nowrap">Add Equipment</span>
            </button>
        </div>
    </div>

    <!-- Equipment Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
        <div class="bg-gray-800 rounded-lg p-4 md:p-6 shadow-lg flex items-center">
            <div class="rounded-full bg-red-600 p-3 mr-4 flex-shrink-0">
                <i class="fas fa-dumbbell text-white text-xl"></i>
            </div>
            <div>
                <p class="text-gray-400 text-sm">Total Equipment</p>
                <p class="text-xl md:text-2xl font-bold text-white">{{ count($equipments) }}</p>
            </div>
        </div>
        <div class="bg-gray-800 rounded-lg p-4 md:p-6 shadow-lg flex items-center">
            <div class="rounded-full bg-green-600 p-3 mr-4 flex-shrink-0">
                <i class="fas fa-truck-loading text-white text-xl"></i>
            </div>
            <div>
                <p class="text-gray-400 text-sm">Total Vendors</p>
                <p class="text-xl md:text-2xl font-bold text-white">{{ count($vendors) }}</p>
            </div>
        </div>
        <div class="bg-gray-800 rounded-lg p-4 md:p-6 shadow-lg flex items-center">
            <div class="rounded-full bg-blue-600 p-3 mr-4 flex-shrink-0">
                <i class="fas fa-tools text-white text-xl"></i>
            </div>
            <div>
                <p class="text-gray-400 text-sm">Recent Maintenance</p>
                <p class="text-xl md:text-2xl font-bold text-white">{{ $maintenanceLogs->count() }}</p>
            </div>
        </div>
    </div>

    <!-- Equipment Table -->
    <div class="bg-gray-800 rounded-lg shadow-lg overflow-hidden">
        <div class="p-3 md:p-4 border-b border-gray-700 flex flex-col md:flex-row justify-between items-center gap-3">
            <h2 class="text-lg md:text-xl font-semibold text-white">Equipment Inventory</h2>
            <div class="flex flex-col sm:flex-row gap-3 items-center w-full md:w-auto">
                <div class="relative w-full sm:w-48">
                    <select id="qualityFilter" class="bg-gray-700 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5">
                        <option value="all">All Qualities</option>
                        <option value="new">New</option>
                        <option value="good">Good</option>
                        <option value="fair">Fair</option>
                        <option value="rusty">Rusty</option>
                    </select>
                </div>
                <div class="relative w-full sm:w-64">
                    <input type="text" id="searchEquipment" placeholder="Search equipment..." class="bg-gray-700 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full pl-10 p-2.5">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Mobile Card View (visible on small screens) -->
        <div class="md:hidden">
            @forelse($equipments as $equipment)
            <div class="p-4 border-b border-gray-700">
                <div class="flex items-center space-x-3 mb-3">
                    <img src="{{ $equipment->image_url }}" alt="{{ $equipment->name }}" class="h-16 w-16 object-cover rounded">
                    <div>
                        <h3 class="font-medium text-white">{{ $equipment->name }}</h3>
                        <span class="px-2 py-1 text-xs rounded-full inline-block mt-1 {{ 
                            $equipment->quality === 'new' ? 'bg-green-600' : 
                            ($equipment->quality === 'good' ? 'bg-blue-600' : 
                            ($equipment->quality === 'fair' ? 'bg-yellow-600' : 'bg-red-600')) 
                        }}">
                            {{ ucfirst($equipment->quality) }}
                        </span>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-y-2 text-sm text-gray-300 mb-3">
                    <div>
                        <span class="text-gray-400">Quantity:</span> {{ $equipment->qty }}
                    </div>
                    <div>
                        <span class="text-gray-400">Vendor:</span> {{ $equipment->vendor->name }}
                    </div>
                    <div class="col-span-2">
                        <span class="text-gray-400">Date Purchased:</span> 
                        @if(is_string($equipment->date_purchased))
                            {{ $equipment->date_purchased }}
                        @else
                            {{ $equipment->date_purchased->format('M d, Y') }}
                        @endif
                    </div>
                </div>
                <div class="flex justify-end space-x-3">
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
            </div>
            @empty
            <div class="p-4 text-center text-gray-400">
                No equipment found. Click "Add Equipment" to add your first equipment.
            </div>
            @endforelse
        </div>
        
        <!-- Desktop Table View (hidden on small screens) -->
        <div class="hidden md:block overflow-x-auto">
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
                            <img src="{{ $equipment->image_url }}" alt="{{ $equipment->name }}" class="h-12 w-12 object-cover rounded">
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
        <div class="relative bg-gray-800 rounded-lg shadow-lg max-w-3xl w-full mx-4 sm:mx-auto p-4 sm:p-6 border border-gray-700 max-h-[90vh] overflow-y-auto" @click.away="showAddModal = false">
            <div class="flex justify-between items-center border-b border-gray-700 pb-3 mb-4">
                <h3 class="text-lg sm:text-xl font-semibold text-white">Add New Equipment</h3>
                <button @click="showAddModal = false" class="text-gray-400 hover:text-white">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form action="{{ route('admin.gym.equipment.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
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
                    <div class="col-span-1 md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-300 mb-1">Description</label>
                        <textarea name="description" id="description" rows="3" class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="col-span-1 md:col-span-2">
                        <label for="image" class="block text-sm font-medium text-gray-300 mb-1">Image</label>
                        <div class="flex flex-col space-y-2">
                            <div class="flex items-center justify-center w-full">
                                <label for="image" class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed rounded-lg cursor-pointer bg-gray-700 border-gray-600 hover:bg-gray-600">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6" id="addUploadPlaceholder">
                                        <i class="fas fa-cloud-upload-alt text-2xl text-gray-400 mb-2"></i>
                                        <p class="mb-2 text-sm text-gray-400">Click to upload or drag and drop</p>
                                        <p class="text-xs text-gray-400">PNG, JPG or JPEG (MAX. 5MB)</p>
                                    </div>
                                    <div id="addImagePreviewContainer" class="hidden w-full h-full flex items-center justify-center">
                                        <img id="addImagePreview" class="max-h-28 max-w-full object-contain" src="#" alt="Preview">
                                    </div>
                                    <input id="image" name="image" type="file" accept="image/png, image/jpeg, image/jpg" class="hidden" 
                                        @change="handleImagePreview($event, 'addImagePreview', 'addUploadPlaceholder', 'addImagePreviewContainer')" />
                                </label>
                            </div>
                            <span id="addSelectedFileName" class="text-xs text-gray-400"></span>
                        </div>
                        @error('image')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row sm:justify-end sm:space-x-3 space-y-3 sm:space-y-0 mt-6 pt-4 border-t border-gray-700">
                    <button type="button" @click="showAddModal = false" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded">Cancel</button>
                    <button type="submit" class="bg-gray-800 hover:bg-gray-700 text-white px-4 py-2 rounded">Save Equipment</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Equipment Modal -->
    <div x-show="showEditModal" class="fixed inset-0 z-50 overflow-auto bg-black bg-opacity-50 flex items-center justify-center" style="display: none;">
        <div class="relative bg-gray-800 rounded-lg shadow-lg max-w-3xl w-full mx-4 sm:mx-auto p-4 sm:p-6 border border-gray-700 max-h-[90vh] overflow-y-auto" @click.away="showEditModal = false">
            <div class="flex justify-between items-center border-b border-gray-700 pb-3 mb-4">
                <h3 class="text-lg sm:text-xl font-semibold text-white">Edit Equipment</h3>
                <button @click="showEditModal = false" class="text-gray-400 hover:text-white">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form :action="'/admin/equipment/' + currentEquipment?.id" method="POST" enctype="multipart/form-data" x-ref="editForm">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" :value="currentEquipment?.id">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
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
                    <div class="col-span-1 md:col-span-2">
                        <label for="edit_description" class="block text-sm font-medium text-gray-300 mb-1">Description</label>
                        <textarea name="description" id="edit_description" rows="3" class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5 @error('description') border-red-500 @enderror" x-text="currentEquipment?.description"></textarea>
                        @error('description')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="col-span-1 md:col-span-2">
                        <label for="edit_image" class="block text-sm font-medium text-gray-300 mb-1">Image (leave blank to keep current)</label>
                        <div class="mb-3">
                            <div id="currentImageContainer" class="w-full h-32 sm:h-40 border border-gray-600 rounded-md flex items-center justify-center mb-2">
                                <img id="currentImage" class="max-h-full max-w-full object-contain rounded-md" :src="previewImage" alt="Current profile image">
                            </div>
                        </div>
                        <div class="flex flex-col space-y-2">
                            <div class="flex items-center justify-center w-full">
                                <label for="edit_image" class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed rounded-lg cursor-pointer bg-gray-700 border-gray-600 hover:bg-gray-600">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6" id="editUploadPlaceholder">
                                        <i class="fas fa-cloud-upload-alt text-2xl text-gray-400 mb-2"></i>
                                        <p class="mb-2 text-sm text-gray-400">Click to upload or drag and drop</p>
                                        <p class="text-xs text-gray-400">PNG, JPG or JPEG (MAX. 5MB)</p>
                                    </div>
                                    <div id="editImagePreviewContainer" class="hidden w-full h-full flex items-center justify-center">
                                        <img id="editImagePreview" class="max-h-28 max-w-full object-contain" src="#" alt="Preview">
                                    </div>
                                    <input id="edit_image" name="image" type="file" accept="image/png, image/jpeg, image/jpg" class="hidden" 
                                        @change="handleImagePreview($event, 'editImagePreview', 'editUploadPlaceholder', 'editImagePreviewContainer')" />
                                </label>
                            </div>
                            <span id="editSelectedFileName" class="text-xs text-gray-400"></span>
                        </div>
                        @error('image')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row sm:justify-end sm:space-x-3 space-y-3 sm:space-y-0 mt-6 pt-4 border-t border-gray-700">
                    <button type="button" @click="showEditModal = false" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded">Cancel</button>
                    <button type="submit" class="bg-gray-800 hover:bg-gray-700 text-white px-4 py-2 rounded">Update Equipment</button>
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
        confirmButtonColor: '#1f2937',
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

// Search and filter functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchEquipment');
    const qualityFilter = document.getElementById('qualityFilter');
    const rows = document.querySelectorAll('tbody tr');
    const mobileCards = document.querySelectorAll('.md\\:hidden > div');
    
    // Function to apply all filters
    function applyFilters() {
        const searchText = searchInput.value.toLowerCase();
        const qualityValue = qualityFilter.value;
        
        // Apply filters to table rows (desktop)
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
        
        // Apply filters to mobile cards
        mobileCards.forEach(card => {
            // Skip if not an equipment card
            if (!card.querySelector('h3')) return;
            
            const cardText = card.textContent.toLowerCase();
            const qualityElement = card.querySelector('.rounded-full');
            const qualityText = qualityElement ? qualityElement.textContent.trim().toLowerCase() : '';
            
            // Check if card matches search text
            const matchesSearch = cardText.includes(searchText);
            
            // Check if card matches quality filter
            const matchesQuality = qualityValue === 'all' || qualityText.includes(qualityValue);
            
            // Show card only if it matches both filters
            card.style.display = (matchesSearch && matchesQuality) ? 'block' : 'none';
        });
    }
    
    // Add event listeners
    if (searchInput) searchInput.addEventListener('keyup', applyFilters);
    if (qualityFilter) qualityFilter.addEventListener('change', applyFilters);
    
    // Show success message if it exists in the session
    @if(session('success'))
        Swal.fire({
            title: 'Success!',
            text: "{{ session('success') }}",
            icon: 'success',
            confirmButtonColor: '#1f2937'
        });
    @endif
    
    @if(session('error'))
        Swal.fire({
            title: 'Error!',
            text: "{{ session('error') }}",
            icon: 'error',
            confirmButtonColor: '#1f2937'
        });
    @endif
    
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
    
    // Adjust SweetAlert2 for mobile
    const mediaQuery = window.matchMedia('(max-width: 640px)');
    if (mediaQuery.matches) {
        Swal.mixin({
            customClass: {
                container: 'small-swal-container',
                popup: 'small-swal-popup',
                title: 'small-swal-title',
                content: 'small-swal-content'
            },
            width: 'auto',
            padding: '1em'
        });
    }
    
    // Image preview for add form
    const imageInput = document.getElementById('image');
    const selectedFileName = document.getElementById('addSelectedFileName');
    
    if (imageInput) {
        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            
            if (file) {
                // File size validation (5MB max)
                if (file.size > 5 * 1024 * 1024) {
                    Swal.fire({
                        title: 'File Too Large',
                        text: 'File size should not exceed 5MB',
                        icon: 'error',
                        confirmButtonColor: '#1f2937'
                    });
                    imageInput.value = '';
                    return;
                }
                
                // Show file name
                selectedFileName.textContent = file.name;
            } else {
                // Reset preview
                selectedFileName.textContent = '';
            }
        });
    }
    
    // Image preview for edit form
    const editImageInput = document.getElementById('edit_image');
    const editSelectedFileName = document.getElementById('editSelectedFileName');
    
    if (editImageInput) {
        editImageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            
            if (file) {
                // File size validation (5MB max)
                if (file.size > 5 * 1024 * 1024) {
                    Swal.fire({
                        title: 'File Too Large',
                        text: 'File size should not exceed 5MB',
                        icon: 'error',
                        confirmButtonColor: '#1f2937'
                    });
                    editImageInput.value = '';
                    return;
                }
                
                // Show file name
                editSelectedFileName.textContent = file.name;
            } else {
                // Reset preview
                editSelectedFileName.textContent = '';
            }
        });
    }
});
</script>
@endsection
