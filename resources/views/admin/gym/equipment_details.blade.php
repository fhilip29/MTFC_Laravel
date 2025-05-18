@extends('layouts.admin')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="p-6">
    <div class="mb-6">
        <a href="{{ route('admin.gym.gym') }}" class="text-blue-400 hover:text-blue-300 flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Back to Equipment List
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Equipment Details Card -->
        <div class="bg-gray-800 rounded-lg shadow-lg overflow-hidden lg:col-span-2">
            <div class="p-4 border-b border-gray-700">
                <h2 class="text-xl font-semibold text-white">Equipment Details</h2>
            </div>
            <div class="p-6">
                <div class="flex flex-col md:flex-row gap-6">
                    <!-- Equipment Image -->
                    <div class="md:w-1/3">
                        <div class="bg-gray-700 rounded-lg p-2 h-64 flex items-center justify-center">
                            <img src="{{ $equipment->getImageUrlAttribute() }}" alt="{{ $equipment->name }}" class="max-h-60 max-w-full object-contain">
                        </div>
                    </div>
                    
                    <!-- Equipment Info -->
                    <div class="md:w-2/3">
                        <h1 class="text-2xl font-bold text-white mb-4">{{ $equipment->name }}</h1>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-gray-400 text-sm">Quantity</p>
                                <p class="text-white">{{ $equipment->qty }}</p>
                            </div>
                            <div>
                                <p class="text-gray-400 text-sm">Price</p>
                                <p class="text-white">₱{{ number_format($equipment->price, 2) }}</p>
                            </div>
                            <div>
                                <p class="text-gray-400 text-sm">Quality</p>
                                <p>
                                    <span class="px-2 py-1 text-xs rounded-full {{ 
                                        $equipment->quality === 'new' ? 'bg-green-600 text-white' : 
                                        ($equipment->quality === 'good' ? 'bg-blue-600 text-white' : 
                                        ($equipment->quality === 'fair' ? 'bg-yellow-600 text-white' : 'bg-red-600 text-white')) 
                                    }}">
                                        {{ ucfirst($equipment->quality) }}
                                    </span>
                                </p>
                            </div>
                            <div>
                                <p class="text-gray-400 text-sm">Vendor</p>
                                <p class="text-white">{{ $equipment->vendor->name }}</p>
                            </div>
                            <div>
                                <p class="text-gray-400 text-sm">Date Purchased</p>
                                <p class="text-white">
                                    @if(is_string($equipment->date_purchased))
                                        {{ $equipment->date_purchased }}
                                    @else
                                        {{ $equipment->date_purchased->format('m/d/Y') }}
                                    @endif
                                </p>
                            </div>
                            <div>
                                <p class="text-gray-400 text-sm">Last Maintenance</p>
                                <p class="text-white">
                                    @if($equipment->maintenanceLogs->count() > 0)
                                        @php
                                            $lastLog = $equipment->maintenanceLogs->sortByDesc('maintenance_date')->first();
                                            $lastDate = $lastLog->maintenance_date;
                                        @endphp
                                        
                                        @if(is_string($lastDate))
                                            {{ $lastDate }}
                                        @else
                                            {{ $lastDate->format('m/d/Y') }}
                                        @endif
                                    @else
                                        No maintenance records
                                    @endif
                                </p>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <p class="text-gray-400 text-sm">Description</p>
                            <p class="text-white">{{ $equipment->description ?: 'No description available' }}</p>
                        </div>
                        
                        <div class="mt-6 flex gap-3">
                            <button onclick="window.location.href='{{ route('admin.gym.gym') }}'" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded flex items-center">
                                <i class="fas fa-arrow-left mr-2"></i> Back
                            </button>
                            
                            <button onclick="showEditModal()" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded flex items-center">
                                <i class="fas fa-edit mr-2"></i> Edit
                            </button>
                            
                            <button onclick="confirmDelete('{{ $equipment->id }}', '{{ $equipment->name }}')" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded flex items-center">
                                <i class="fas fa-trash mr-2"></i> Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Add Maintenance Record Card -->
        <div class="bg-gray-800 rounded-lg shadow-lg overflow-hidden">
            <div class="p-4 border-b border-gray-700">
                <h2 class="text-xl font-semibold text-white">Add Maintenance Record</h2>
            </div>
            <div class="p-6">
                <form action="{{ route('admin.gym.maintenance.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="equipment_id" value="{{ $equipment->id }}">
                    
                    <div class="mb-4">
                        <label for="performed_by" class="block text-sm font-medium text-gray-300 mb-1">Performed By <span class="text-red-500">*</span></label>
                        <input type="text" name="performed_by" id="performed_by" 
                               placeholder="Enter name of technician or staff member" 
                               required 
                               class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5">
                        <p class="text-xs text-gray-400 mt-1">Required. Enter the full name of the person who performed the maintenance.</p>
                    </div>
                    
                    <div class="mb-4">
                        <label for="maintenance_date" class="block text-sm font-medium text-gray-300 mb-1">Maintenance Date <span class="text-red-500">*</span></label>
                        <input type="date" name="maintenance_date" id="maintenance_date" 
                               required 
                               max="{{ now()->format('Y-m-d') }}" 
                               class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5" 
                               value="{{ now()->format('Y-m-d') }}">
                        <p class="text-xs text-gray-400 mt-1">Required. Select the date when maintenance was performed (cannot be in the future).</p>
                    </div>
                    
                    <div class="mb-4">
                        <label for="notes" class="block text-sm font-medium text-gray-300 mb-1">Notes <span class="text-red-500">*</span></label>
                        <textarea name="notes" id="notes" 
                                  rows="4" 
                                  required 
                                  class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5" 
                                  placeholder="Describe the maintenance performed, parts replaced, issues found, etc."></textarea>
                        <p class="text-xs text-gray-400 mt-1">Required. Provide detailed information about the maintenance work performed, including any parts replaced or issues found.</p>
                    </div>
                    
                    <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg">
                        <i class="fas fa-tools mr-2"></i> Record Maintenance
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Maintenance History -->
    <div class="mt-8 bg-gray-800 rounded-lg shadow-lg overflow-hidden">
        <div class="p-4 border-b border-gray-700">
            <h2 class="text-xl font-semibold text-white">Maintenance History</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-gray-800 text-white">
                <thead class="bg-gray-700 text-xs uppercase font-medium">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left tracking-wider">Date</th>
                        <th scope="col" class="px-6 py-3 text-left tracking-wider">Performed By</th>
                        <th scope="col" class="px-6 py-3 text-left tracking-wider">Notes</th>
                        <th scope="col" class="px-6 py-3 text-left tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @forelse($equipment->maintenanceLogs->sortByDesc('maintenance_date') as $log)
                    <tr class="hover:bg-gray-700 transition">
                        <td class="px-6 py-4 whitespace-nowrap">{{ $log->maintenance_date->format('m/d/Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $log->performed_by }}</td>
                        <td class="px-6 py-4">{{ $log->notes }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <button onclick="confirmDeleteMaintenance('{{ $log->id }}')" class="text-red-400 hover:text-red-300">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-400">
                            No maintenance records found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Edit Equipment Modal -->
<div id="editModal" class="fixed inset-0 z-50 overflow-auto bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div class="relative bg-gray-800 rounded-lg shadow-lg max-w-3xl w-full mx-auto p-6 border border-gray-700">
        <div class="flex justify-between items-center border-b border-gray-700 pb-3 mb-4">
            <h3 class="text-xl font-semibold text-white">Edit Equipment</h3>
            <button onclick="hideEditModal()" class="text-gray-400 hover:text-white">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form action="{{ route('admin.gym.equipment.update', $equipment->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="edit_name" class="block text-sm font-medium text-gray-300 mb-1">Equipment Name</label>
                    <input type="text" name="name" id="edit_name" required class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5" value="{{ $equipment->name }}">
                </div>
                <div>
                    <label for="edit_qty" class="block text-sm font-medium text-gray-300 mb-1">Quantity</label>
                    <input type="number" name="qty" id="edit_qty" min="1" required class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5" value="{{ $equipment->qty }}">
                </div>
                <div>
                    <label for="edit_price" class="block text-sm font-medium text-gray-300 mb-1">Price (PHP)</label>
                    <input type="number" name="price" id="edit_price" min="0" step="0.01" required class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5" value="{{ $equipment->price }}">
                </div>
                <div>
                    <label for="edit_quality" class="block text-sm font-medium text-gray-300 mb-1">Quality</label>
                    <select name="quality" id="edit_quality" required class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5">
                        <option value="new" {{ $equipment->quality === 'new' ? 'selected' : '' }}>New</option>
                        <option value="good" {{ $equipment->quality === 'good' ? 'selected' : '' }}>Good</option>
                        <option value="fair" {{ $equipment->quality === 'fair' ? 'selected' : '' }}>Fair</option>
                        <option value="rusty" {{ $equipment->quality === 'rusty' ? 'selected' : '' }}>Rusty</option>
                    </select>
                </div>
                <div>
                    <label for="edit_date_purchased" class="block text-sm font-medium text-gray-300 mb-1">Date Purchased</label>
                    <input type="date" name="date_purchased" id="edit_date_purchased" required class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5" value="{{ $equipment->date_purchased->format('Y-m-d') }}">
                </div>
                <div>
                    <label for="edit_vendor_id" class="block text-sm font-medium text-gray-300 mb-1">Vendor</label>
                    <select name="vendor_id" id="edit_vendor_id" required class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5">
                        @foreach($vendors as $vendor)
                            <option value="{{ $vendor->id }}" {{ $equipment->vendor_id === $vendor->id ? 'selected' : '' }}>{{ $vendor->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label for="edit_description" class="block text-sm font-medium text-gray-300 mb-1">Description</label>
                    <textarea name="description" id="edit_description" rows="3" class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5">{{ $equipment->description }}</textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-300 mb-1">Current Image</label>
                    <img src="{{ $equipment->getImageUrlAttribute() }}" alt="Current image" class="h-32 w-32 object-cover rounded mb-2">
                    <label for="edit_image" class="block text-sm font-medium text-gray-300 mb-1">New Image (optional)</label>
                    <input type="file" name="image" id="edit_image" accept="image/*" class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5">
                </div>
            </div>
            <div class="flex justify-end mt-6 space-x-3">
                <button type="button" onclick="hideEditModal()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded">Cancel</button>
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">Update Equipment</button>
            </div>
        </form>
    </div>
</div>

<script>
function showEditModal() {
    document.getElementById('editModal').classList.remove('hidden');
}

function hideEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}

function confirmDelete(id, name) {
    Swal.fire({
        title: 'Delete Equipment?',
        text: `Are you sure you want to delete ${name}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
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

function confirmDeleteMaintenance(id) {
    Swal.fire({
        title: 'Delete Maintenance Record?',
        text: `Are you sure you want to delete this maintenance record?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#4b5563',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/equipment/maintenance/${id}`;
            form.innerHTML = `
                @csrf
                @method('DELETE')
            `;
            document.body.appendChild(form);
            form.submit();
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    @if(session('success'))
        Swal.fire({
            title: 'Success!',
            text: "{{ session('success') }}",
            icon: 'success',
            confirmButtonColor: '#ef4444'
        });
    @endif
    
    @if(session('error'))
        Swal.fire({
            title: 'Error!',
            text: "{{ session('error') }}",
            icon: 'error',
            confirmButtonColor: '#ef4444'
        });
    @endif
});
</script>
@endsection 