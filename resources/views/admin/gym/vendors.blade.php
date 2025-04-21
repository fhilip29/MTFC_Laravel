@extends('layouts.admin')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="p-6" x-data="{ showAddModal: false, showEditModal: false, currentVendor: null }">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-white">Vendor Management</h1>
        <div class="flex gap-4">
            <a href="{{ route('admin.gym.gym') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold px-4 py-2 rounded flex items-center">
                <i class="fas fa-dumbbell mr-2"></i> Manage Equipment
            </a>
            <button @click="showAddModal = true" class="bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-2 rounded flex items-center">
                <i class="fas fa-plus mr-2"></i> Add Vendor
            </button>
        </div>
    </div>

    <!-- Vendor List Card -->
    <div class="bg-gray-800 rounded-lg shadow-lg overflow-hidden">
        <div class="p-4 border-b border-gray-700 flex justify-between items-center">
            <h2 class="text-xl font-semibold text-white">Vendors</h2>
            <div class="relative">
                <input type="text" id="searchVendor" placeholder="Search vendors..." class="bg-gray-700 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full pl-10 p-2.5">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-gray-800 text-white">
                <thead class="bg-gray-700 text-xs uppercase font-medium">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left tracking-wider">ID</th>
                        <th scope="col" class="px-6 py-3 text-left tracking-wider">Name</th>
                        <th scope="col" class="px-6 py-3 text-left tracking-wider">Contact Info</th>
                        <th scope="col" class="px-6 py-3 text-left tracking-wider">Equipment Count</th>
                        <th scope="col" class="px-6 py-3 text-left tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @forelse($vendors as $vendor)
                    <tr class="hover:bg-gray-700 transition">
                        <td class="px-6 py-4 whitespace-nowrap">{{ $vendor->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap font-medium">{{ $vendor->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $vendor->contact_info ?: 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full {{ $vendor->equipments_count > 0 ? 'bg-green-600' : 'bg-gray-600' }}">
                                {{ $vendor->equipments_count }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.gym.vendors.show', $vendor->id) }}" class="text-blue-400 hover:text-blue-300">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button @click="currentVendor = {{ $vendor }}; showEditModal = true" class="text-yellow-400 hover:text-yellow-300">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="confirmDelete('{{ $vendor->id }}', '{{ $vendor->name }}', {{ $vendor->equipments_count }})" class="text-red-400 hover:text-red-300">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-400">
                            No vendors found. Click "Add Vendor" to add your first vendor.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Vendor Modal -->
    <div x-show="showAddModal" class="fixed inset-0 z-50 overflow-auto bg-black bg-opacity-50 flex items-center justify-center" style="display: none;">
        <div class="relative bg-gray-800 rounded-lg shadow-lg max-w-md w-full mx-auto p-6 border border-gray-700" @click.away="showAddModal = false">
            <div class="flex justify-between items-center border-b border-gray-700 pb-3 mb-4">
                <h3 class="text-xl font-semibold text-white">Add New Vendor</h3>
                <button @click="showAddModal = false" class="text-gray-400 hover:text-white">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form action="{{ route('admin.gym.vendors.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-300 mb-1">Vendor Name</label>
                    <input type="text" name="name" id="name" required class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5">
                </div>
                <div class="mb-4">
                    <label for="contact_info" class="block text-sm font-medium text-gray-300 mb-1">Contact Information</label>
                    <input type="text" name="contact_info" id="contact_info" class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5" placeholder="Phone number, email, etc.">
                </div>
                <div class="flex justify-end mt-6 space-x-3">
                    <button type="button" @click="showAddModal = false" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded">Cancel</button>
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">Save Vendor</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Vendor Modal -->
    <div x-show="showEditModal" class="fixed inset-0 z-50 overflow-auto bg-black bg-opacity-50 flex items-center justify-center" style="display: none;">
        <div class="relative bg-gray-800 rounded-lg shadow-lg max-w-md w-full mx-auto p-6 border border-gray-700" @click.away="showEditModal = false">
            <div class="flex justify-between items-center border-b border-gray-700 pb-3 mb-4">
                <h3 class="text-xl font-semibold text-white">Edit Vendor</h3>
                <button @click="showEditModal = false" class="text-gray-400 hover:text-white">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form :action="'/admin/vendors/' + currentVendor?.id" method="POST" x-ref="editForm">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <label for="edit_name" class="block text-sm font-medium text-gray-300 mb-1">Vendor Name</label>
                    <input type="text" name="name" id="edit_name" required class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5" :value="currentVendor?.name">
                </div>
                <div class="mb-4">
                    <label for="edit_contact_info" class="block text-sm font-medium text-gray-300 mb-1">Contact Information</label>
                    <input type="text" name="contact_info" id="edit_contact_info" class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5" placeholder="Phone number, email, etc." :value="currentVendor?.contact_info">
                </div>
                <div class="flex justify-end mt-6 space-x-3">
                    <button type="button" @click="showEditModal = false" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded">Cancel</button>
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">Update Vendor</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function confirmDelete(id, name, equipmentCount) {
    if (equipmentCount > 0) {
        Swal.fire({
            title: 'Cannot Delete Vendor',
            text: `${name} has ${equipmentCount} equipment associated with it. Remove the equipment first.`,
            icon: 'warning',
            confirmButtonColor: '#ef4444'
        });
        return;
    }
    
    Swal.fire({
        title: 'Delete Vendor?',
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
            form.action = `/admin/vendors/${id}`;
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
    const searchInput = document.getElementById('searchVendor');
    searchInput.addEventListener('keyup', function() {
        const searchText = this.value.toLowerCase();
        const rows = document.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            const visible = Array.from(row.querySelectorAll('td')).some(cell => 
                cell.textContent.toLowerCase().includes(searchText)
            );
            row.style.display = visible ? '' : 'none';
        });
    });
    
    // Show success message if it exists in the session
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