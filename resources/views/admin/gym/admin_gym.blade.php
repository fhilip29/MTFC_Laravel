@extends('layouts.admin')

@section('title', 'Gym Equipment Management')

@section('content')
<div class="container mx-auto px-2 sm:px-4 py-4 sm:py-8">
    <div class="bg-[#1F2937] p-4 sm:p-6 rounded-2xl shadow-md border border-[#374151]">
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4 sm:gap-6 mb-6">
            <h1 class="text-xl sm:text-2xl font-bold text-white flex items-center gap-2 w-full sm:w-auto">
                <i class="fas fa-dumbbell text-[#9CA3AF]"></i> Gym Equipment Management
            </h1>
            <button class="bg-[#374151] hover:bg-[#4B5563] text-white font-semibold flex items-center gap-2 px-4 py-2 rounded-lg shadow transition-colors w-full sm:w-auto justify-center">
                <i class="fas fa-plus"></i> <span class="sm:inline">Add Equipment</span>
            </button>
        </div>

        <div class="mb-6">
            <div class="relative w-full sm:w-1/3">
                <input 
                    type="text" 
                    placeholder="Search equipment..." 
                    class="w-full pl-10 pr-4 py-2 bg-[#374151] border border-[#4B5563] text-white rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-[#9CA3AF] placeholder-[#9CA3AF] text-sm sm:text-base"
                >
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-[#9CA3AF]"></i>
            </div>
        </div>

        <div class="overflow-x-auto rounded-lg shadow-sm -mx-4 sm:mx-0">
            <div class="inline-block min-w-full align-middle">
            <table class="min-w-full text-xs sm:text-sm table-auto">
                <thead class="bg-[#374151] text-[#9CA3AF] uppercase text-xs">
                    <tr>
                        <th class="py-4 px-4 text-left">Equipment Name</th>
                        <th class="py-4 px-4 text-left">Contact</th>
                        <th class="py-4 px-4 text-left">Vendor</th>
                        <th class="py-4 px-4 text-left">Purchase Date</th>
                        <th class="py-4 px-4 text-left">Quality</th>
                        <th class="py-4 px-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-[#9CA3AF]">
                    @php
                        $equipments = [
                            [
                                'name' => 'Inclined Press',
                                'contact' => '09171155987',
                                'vendor' => 'Arnold Swahehshs',
                                'date' => '03-05-2025',
                                'quality' => 'Broken',
                                'quality_class' => 'bg-red-500',
                            ],
                            [
                                'name' => 'Bench Press',
                                'contact' => '09171055687',
                                'vendor' => 'Arnold Swahehshs',
                                'date' => '03-06-2025',
                                'quality' => 'Good',
                                'quality_class' => 'bg-green-500',
                            ],
                            [
                                'name' => 'Pull Machine',
                                'contact' => '09171155987',
                                'vendor' => 'Gyms Gold',
                                'date' => '03-05-2025',
                                'quality' => 'Rusty',
                                'quality_class' => 'bg-orange-500',
                            ],
                        ];
                    @endphp

                    @foreach($equipments as $equipment)
                        <tr class="hover:bg-[#374151] border-b border-[#374151]">
                            <td class="py-4 px-4 text-white align-middle">{{ $equipment['name'] }}</td>
                            <td class="py-4 px-4 align-middle">{{ $equipment['contact'] }}</td>
                            <td class="py-4 px-4 align-middle">{{ $equipment['vendor'] }}</td>
                            <td class="py-4 px-4 align-middle">{{ $equipment['date'] }}</td>
                            <td class="py-4 px-4 align-middle">
                                <span class="text-white px-2 py-1 rounded text-xs {{ $equipment['quality_class'] }}">
                                    {{ $equipment['quality'] }}
                                </span>
                            </td>
                            <td class="py-4 px-4 text-center align-middle">
                                <div class="flex justify-center gap-2">
                                    <a href="#" class="text-blue-400 hover:text-blue-300 cursor-pointer" title="Edit Equipment">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="#" class="text-blue-400 hover:text-blue-300 cursor-pointer" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="#" class="text-blue-400 hover:text-blue-300 cursor-pointer" title="Delete Equipment">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
        </div>
    </div>
</div>

<script>
// Frontend-only confirmation dialog
function confirmDelete() {
    if (confirm('Are you sure you want to delete this equipment?')) {
        alert('Equipment deleted successfully!');
    }
}

// Add click handlers to all delete buttons
document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('a:has(i.fa-trash)');
    deleteButtons.forEach(button => {
        button.addEventListener('click', confirmDelete);
    });
});
</script>
@endsection