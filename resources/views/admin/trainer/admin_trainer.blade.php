@extends('layouts.admin')

@section('title', 'Trainer Management')

@section('content')
<div class="container mx-auto px-4 py-4">
    <div class="bg-[#111827] p-6 rounded-xl shadow-md mb-8 border border-[#374151]">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
            <h1 class="text-3xl font-bold text-white flex items-center">
                <i class="fas fa-dumbbell mr-3 text-[#9CA3AF]"></i>
                Trainer Management
            </h1>
            <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4 w-full md:w-auto">
                <div class="relative flex-grow md:flex-grow-0 md:w-64">
                    <input 
                        type="text" 
                        placeholder="Search trainers..." 
                        class="w-full pl-10 pr-4 py-3 bg-[#374151] border-2 border-[#4B5563] text-white rounded-lg focus:outline-none focus:border-[#9CA3AF] focus:ring-1 focus:ring-[#9CA3AF] transition-all duration-200 placeholder-gray-400"
                    >
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-[#9CA3AF]"></i>
                </div>
                <button class="bg-[#374151] hover:bg-[#4B5563] text-white py-2 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center">
                    <i class="fas fa-plus mr-2"></i>
                    Add Trainer
                </button>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <!-- Trainer Cards -->
        <div class="bg-[#1F2937] rounded-lg shadow-lg overflow-hidden transform transition-all duration-300 hover:shadow-xl hover:-translate-y-1 border border-[#374151]">
            <div class="relative">
                <img src="{{ asset('assets/default-profile.jpg') }}" alt="John Smith" class="w-full h-48 object-cover transition-transform duration-300 hover:scale-105">
                <div class="absolute top-2 right-2">
                    <span class="px-3 py-1 text-sm font-medium rounded-full bg-[#374151] text-[#9CA3AF] shadow-sm">
                        Active
                    </span>
                </div>
            </div>
            <div class="p-6">
                <h3 class="text-xl font-bold text-white mb-1 hover:text-[#9CA3AF] transition-colors duration-200">John Smith</h3>
                <p class="text-[#9CA3AF] text-sm mb-3">Personal Trainer</p>
                <div class="flex items-center text-sm text-[#9CA3AF] mb-4">
                    <i class="fas fa-users mr-2"></i>
                    <span>12 Active Clients</span>
                </div>
                <!-- Weekly Schedule -->
                <div class="mb-4">
                    <h4 class="text-white text-sm font-semibold mb-2">Weekly Schedule</h4>
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div class="text-[#9CA3AF]">Mon: 9AM - 5PM</div>
                        <div class="text-[#9CA3AF]">Tue: 9AM - 5PM</div>
                        <div class="text-[#9CA3AF]">Wed: 9AM - 5PM</div>
                        <div class="text-[#9CA3AF]">Thu: 9AM - 5PM</div>
                        <div class="text-[#9CA3AF]">Fri: 9AM - 3PM</div>
                        <div class="text-[#9CA3AF]">Sat: 10AM - 2PM</div>
                    </div>
                </div>
                <div class="border-t border-[#374151] pt-4">
                    <div class="flex justify-between items-center space-x-4">
                        <button class="flex-1 bg-[#374151] hover:bg-[#4B5563] text-white py-2 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center">
                            <i class="fas fa-edit mr-2"></i>
                            Edit
                        </button>
                        <button class="flex-1 bg-[#374151] hover:bg-[#4B5563] text-white py-2 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center">
                            <i class="fas fa-archive mr-2"></i>
                            Archive
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-[#1F2937] rounded-lg shadow-lg overflow-hidden transform transition-all duration-300 hover:shadow-xl hover:-translate-y-1 border border-[#374151]">
            <div class="relative">
                <img src="{{ asset('assets/default-profile.jpg') }}" alt="Sarah Johnson" class="w-full h-48 object-cover transition-transform duration-300 hover:scale-105">
                <div class="absolute top-2 right-2">
                    <span class="px-3 py-1 text-sm font-medium rounded-full bg-[#374151] text-[#9CA3AF] shadow-sm">
                        Active
                    </span>
                </div>
            </div>
            <div class="p-6">
                <h3 class="text-xl font-bold text-white mb-1 hover:text-[#9CA3AF] transition-colors duration-200">Sarah Johnson</h3>
                <p class="text-[#9CA3AF] text-sm mb-3">Fitness Coach</p>
                <div class="flex items-center text-sm text-[#9CA3AF] mb-4">
                    <i class="fas fa-users mr-2"></i>
                    <span>8 Active Clients</span>
                </div>
                <!-- Weekly Schedule -->
                <div class="mb-4">
                    <h4 class="text-white text-sm font-semibold mb-2">Weekly Schedule</h4>
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div class="text-[#9CA3AF]">Mon: 8AM - 4PM</div>
                        <div class="text-[#9CA3AF]">Tue: 8AM - 4PM</div>
                        <div class="text-[#9CA3AF]">Wed: 8AM - 4PM</div>
                        <div class="text-[#9CA3AF]">Thu: 8AM - 4PM</div>
                        <div class="text-[#9CA3AF]">Fri: 8AM - 2PM</div>
                        <div class="text-[#9CA3AF]">Sat: 9AM - 1PM</div>
                    </div>
                </div>
                <div class="border-t border-[#374151] pt-4">
                    <div class="flex justify-between items-center space-x-4">
                        <button class="flex-1 bg-[#374151] hover:bg-[#4B5563] text-white py-2 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center">
                            <i class="fas fa-edit mr-2"></i>
                            Edit
                        </button>
                        <button class="flex-1 bg-[#374151] hover:bg-[#4B5563] text-white py-2 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center">
                            <i class="fas fa-archive mr-2"></i>
                            Archive
                        </button>
                    </div>
                </div>
            </div>
        </div>
     </div>

    <!-- Pagination -->
    <div class="mt-8 flex justify-center">
        <nav class="flex items-center space-x-2">
            <button class="px-3 py-1 rounded-lg bg-[#374151] text-[#9CA3AF] hover:bg-[#4B5563] transition-colors">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button class="px-3 py-1 rounded-lg bg-[#374151] text-white">1</button>
            <button class="px-3 py-1 rounded-lg bg-[#374151] text-[#9CA3AF] hover:bg-[#4B5563] transition-colors">2</button>
            <button class="px-3 py-1 rounded-lg bg-[#374151] text-[#9CA3AF] hover:bg-[#4B5563] transition-colors">3</button>
            <button class="px-3 py-1 rounded-lg bg-[#374151] text-[#9CA3AF] hover:bg-[#4B5563] transition-colors">
                <i class="fas fa-chevron-right"></i>
            </button>
        </nav>
    </div>
</div>

<script>
// Frontend-only confirmation dialog
function confirmArchive() {
    if (confirm('Are you sure you want to archive this trainer?')) {
        alert('Trainer archived successfully!');
    }
}

// Add click handlers to all archive buttons
document.addEventListener('DOMContentLoaded', function() {
    const archiveButtons = document.querySelectorAll('button:has(i.fa-archive)');
    archiveButtons.forEach(button => {
        button.addEventListener('click', confirmArchive);
    });
});
</script>
@endsection