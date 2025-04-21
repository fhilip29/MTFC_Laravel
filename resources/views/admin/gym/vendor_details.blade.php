@extends('layouts.admin')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="p-6">
    <div class="mb-6">
        <a href="{{ route('admin.gym.vendors') }}" class="text-blue-400 hover:text-blue-300 flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Back to Vendors
        </a>
    </div>

    <!-- Vendor Details Card -->
    <div class="bg-gray-800 rounded-lg shadow-lg overflow-hidden mb-8">
        <div class="p-4 border-b border-gray-700">
            <h2 class="text-xl font-semibold text-white">Vendor Details</h2>
        </div>
        <div class="p-6">
            <div class="flex flex-col md:flex-row md:justify-between md:items-center">
                <div>
                    <h1 class="text-2xl font-bold text-white mb-2">{{ $vendor->name }}</h1>
                    <p class="text-gray-400">
                        <span class="mr-2"><i class="fas fa-phone-alt"></i></span>
                        {{ $vendor->contact_info ?: 'No contact information provided' }}
                    </p>
                </div>
                <div class="mt-4 md:mt-0 flex gap-3">
                    <button onclick="showEditModal()" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded flex items-center">
                        <i class="fas fa-edit mr-2"></i> Edit
                    </button>
                    
                    <button onclick="confirmDelete('{{ $vendor->id }}', '{{ $vendor->name }}', {{ $vendor->equipments->count() }})" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded flex items-center">
                        <i class="fas fa-trash mr-2"></i> Delete
                    </button>
                </div>
            </div>
            
            <div class="mt-8">
                <div class="flex items-center">
                    <h3 class="text-lg font-semibold text-white">Equipment from this Vendor</h3>
                    <span class="ml-2 px-2 py-1 text-xs rounded-full {{ $vendor->equipments->count() > 0 ? 'bg-green-600' : 'bg-gray-600' }}">
                        {{ $vendor->equipments->count() }}
                    </span>
                </div>
                
                @if($vendor->equipments->count() > 0)
                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($vendor->equipments as $equipment)
                    <div class="bg-gray-700 rounded-lg p-4 flex items-center">
                        <div class="mr-4">
                            <img src="{{ $equipment->getImageUrlAttribute() }}" alt="{{ $equipment->name }}" class="h-16 w-16 object-cover rounded">
                        </div>
                        <div>
                            <h4 class="font-medium text-white">{{ $equipment->name }}</h4>
                            <p class="text-sm text-gray-400">
                                <span class="px-2 py-1 text-xs rounded-full {{ 
                                    $equipment->quality === 'new' ? 'bg-green-600 text-white' : 
                                    ($equipment->quality === 'good' ? 'bg-blue-600 text-white' : 
                                    ($equipment->quality === 'fair' ? 'bg-yellow-600 text-white' : 'bg-red-600 text-white')) 
                                }}">
                                    {{ ucfirst($equipment->quality) }}
                                </span>
                                <span class="ml-2">{{ $equipment->date_purchased->format('M d, Y') }}</span>
                            </p>
                            <a href="{{ route('admin.gym.equipment.show', $equipment->id) }}" class="text-blue-400 hover:text-blue-300 text-sm mt-1 inline-block">
                                View Details
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="mt-4 p-4 bg-gray-700 rounded-lg text-center">
                    <p class="text-gray-400">No equipment from this vendor yet.</p>
                    <a href="{{ route('admin.gym.gym') }}" class="text-blue-400 hover:text-blue-300 mt-2 inline-block">
                        Add equipment
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Edit Vendor Modal -->
<div id="editModal" class="fixed inset-0 z-50 overflow-auto bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div class="relative bg-gray-800 rounded-lg shadow-lg max-w-md w-full mx-auto p-6 border border-gray-700">
        <div class="flex justify-between items-center border-b border-gray-700 pb-3 mb-4">
            <h3 class="text-xl font-semibold text-white">Edit Vendor</h3>
            <button onclick="hideEditModal()" class="text-gray-400 hover:text-white">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form action="{{ route('admin.gym.vendors.update', $vendor->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-300 mb-1">Vendor Name</label>
                <input type="text" name="name" id="name" required class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5" value="{{ $vendor->name }}">
            </div>
            <div class="mb-4">
                <label for="contact_info" class="block text-sm font-medium text-gray-300 mb-1">Contact Information</label>
                <input type="text" name="contact_info" id="contact_info" class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5" placeholder="Phone number, email, etc." value="{{ $vendor->contact_info }}">
            </div>
            <div class="flex justify-end mt-6 space-x-3">
                <button type="button" onclick="hideEditModal()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded">Cancel</button>
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">Update Vendor</button>
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