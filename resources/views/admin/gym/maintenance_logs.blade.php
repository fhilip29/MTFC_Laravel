@extends('layouts.admin')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-white">Equipment Maintenance Logs</h1>
        <div class="flex gap-4">
            <a href="{{ route('admin.gym.gym') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold px-4 py-2 rounded flex items-center">
                <i class="fas fa-tools mr-2"></i> Equipment List
            </a>
        </div>
    </div>

    <!-- Date Range Filter -->
    <div class="bg-gray-800 rounded-lg p-4 shadow-lg mb-6">
        <div class="flex flex-wrap gap-4 items-end">
            <div>
                <label for="filterDateFrom" class="block text-sm font-medium text-gray-300 mb-1">From Date</label>
                <input type="date" id="filterDateFrom" class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5">
            </div>
            <div>
                <label for="filterDateTo" class="block text-sm font-medium text-gray-300 mb-1">To Date</label>
                <input type="date" id="filterDateTo" class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5">
            </div>
            <button id="applyFilters" class="bg-red-600 hover:bg-red-700 text-white font-medium text-sm px-4 py-2 rounded h-10">
                <i class="fas fa-filter mr-1"></i> Apply
            </button>
            <button id="clearFilters" class="bg-gray-600 hover:bg-gray-700 text-white font-medium text-sm px-4 py-2 rounded h-10">
                <i class="fas fa-times mr-1"></i> Clear
            </button>
        </div>
    </div>

    <!-- Maintenance Logs Table -->
    <div class="bg-gray-800 rounded-lg shadow-lg overflow-hidden">
        <div class="p-4 border-b border-gray-700 flex justify-between items-center">
            <h2 class="text-xl font-semibold text-white">All Maintenance Records</h2>
            <div class="relative">
                <input type="text" id="searchMaintenanceLogs" placeholder="Search logs..." class="bg-gray-700 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full pl-10 p-2.5">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-gray-800 text-white">
                <thead class="bg-gray-700 text-xs uppercase font-medium">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left tracking-wider">Equipment</th>
                        <th scope="col" class="px-6 py-3 text-left tracking-wider">Performed By</th>
                        <th scope="col" class="px-6 py-3 text-left tracking-wider">Date</th>
                        <th scope="col" class="px-6 py-3 text-left tracking-wider">Notes</th>
                        <th scope="col" class="px-6 py-3 text-left tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @forelse($maintenanceLogs as $log)
                    <tr class="hover:bg-gray-700 transition" data-date="{{ $log->maintenance_date->format('Y-m-d') }}">
                        <td class="px-6 py-4 whitespace-nowrap font-medium">
                            {{ $log->equipment->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $log->performed_by }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if(is_string($log->maintenance_date))
                                {{ $log->maintenance_date }}
                            @else
                                {{ $log->maintenance_date->format('M d, Y') }}
                            @endif
                        </td>
                        <td class="px-6 py-4">{{ Str::limit($log->notes, 50) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.gym.equipment.show', $log->equipment_id) }}" class="text-gray-400 hover:text-gray-300" title="View Equipment Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button onclick="confirmDeleteMaintenance('{{ $log->id }}')" class="text-red-400 hover:text-red-300" title="Delete Maintenance Record">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-400">
                            No maintenance logs found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
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
    const searchInput = document.getElementById('searchMaintenanceLogs');
    const filterDateFrom = document.getElementById('filterDateFrom');
    const filterDateTo = document.getElementById('filterDateTo');
    const applyFiltersBtn = document.getElementById('applyFilters');
    const clearFiltersBtn = document.getElementById('clearFilters');
    const rows = document.querySelectorAll('tbody tr');
    
    // Search functionality
    searchInput.addEventListener('keyup', function() {
        applyFilters();
    });
    
    // Apply filters button
    applyFiltersBtn.addEventListener('click', function() {
        applyFilters();
    });
    
    // Clear filter button
    clearFiltersBtn.addEventListener('click', function() {
        searchInput.value = '';
        filterDateFrom.value = '';
        filterDateTo.value = '';
        applyFilters();
    });
    
    // Apply filters function
    function applyFilters() {
        const searchText = searchInput.value.toLowerCase();
        const dateFromFilter = filterDateFrom.value ? new Date(filterDateFrom.value) : null;
        const dateToFilter = filterDateTo.value ? new Date(filterDateTo.value) : null;
        
        rows.forEach(row => {
            if (row.classList.contains('empty-row')) return;
            
            const rowDateStr = row.dataset.date;
            const rowDate = rowDateStr ? new Date(rowDateStr) : null;
            const textContent = row.textContent.toLowerCase();
            
            let isVisible = true;
            
            // Apply search filter
            if (searchText && !textContent.includes(searchText)) {
                isVisible = false;
            }
            
            // Apply date from filter
            if (isVisible && dateFromFilter && rowDate && rowDate < dateFromFilter) {
                isVisible = false;
            }
            
            // Apply date to filter
            if (isVisible && dateToFilter && rowDate) {
                // Adjust to end of day for inclusive filtering
                const adjustedDateTo = new Date(dateToFilter);
                adjustedDateTo.setDate(adjustedDateTo.getDate() + 1);
                if (rowDate >= adjustedDateTo) {
                    isVisible = false;
                }
            }
            
            row.style.display = isVisible ? '' : 'none';
        });
    }
    
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