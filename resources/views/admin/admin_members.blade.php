@extends('layouts.admin')
@section('title', 'Manage Members')

@section('content')
<div class="min-h-screen bg-[#1F2937] w-full">
    <div class="max-w-[95%] mx-auto px-6 py-10">
        <div class="bg-[#2D3748] p-8 rounded-2xl shadow-2xl mb-10 border border-[#4A5568]">
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center space-y-6 lg:space-y-0">
                <div class="flex items-center space-x-4">
                    <div class="bg-[#374151] p-3 rounded-lg">
                        <i class="fas fa-users text-2xl text-[#9CA3AF]"></i>
                    </div>
                    <h1 class="text-4xl font-bold text-white tracking-tight">Manage Members</h1>
                </div>
                <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4 w-full lg:w-auto">
                    <div class="relative flex-grow lg:flex-grow-0 lg:w-80">
                        <input 
                            type="text" 
                            placeholder="Search members..." 
                            class="w-full pl-12 pr-4 py-4 bg-[#374151] border-2 border-[#4B5563] text-white rounded-xl focus:outline-none focus:border-[#60A5FA] focus:ring-2 focus:ring-[#60A5FA] transition-all duration-300 placeholder-gray-400 text-lg"
                        >
                        <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-[#9CA3AF] text-lg"></i>
                    </div>
                    <select class="w-full sm:w-56 py-4 px-6 bg-[#374151] border-2 border-[#4B5563] text-white rounded-xl focus:outline-none focus:border-[#60A5FA] focus:ring-2 focus:ring-[#60A5FA] transition-all duration-300 cursor-pointer text-lg appearance-none">
                        <option value="all">All Members</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="archived">Archived</option>
                    </select>
                </div>
            </div>
        </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <!-- Static Member Cards -->
        <div class="bg-[#2D3748] rounded-xl shadow-xl overflow-hidden transform transition-all duration-300 hover:shadow-2xl hover:-translate-y-2 border border-[#4A5568] group">
            <div class="relative">
                <img src="{{ asset('assets/default-profile.jpg') }}" alt="John Doe" class="w-full h-56 object-cover transition-transform duration-500 group-hover:scale-110">
                <div class="absolute top-4 right-4">
                    <span class="px-4 py-2 text-sm font-semibold rounded-full bg-[#374151] text-[#60A5FA] shadow-lg border border-[#4A5568]">
                        Active
                    </span>
                </div>
            </div>
            <div class="p-8">
                <h3 class="text-2xl font-bold text-white mb-2 group-hover:text-[#60A5FA] transition-colors duration-300">John Doe</h3>
                <p class="text-[#9CA3AF] text-base mb-4">john.doe@example.com</p>
                <div class="flex items-center text-base text-[#9CA3AF] mb-6">
                    <i class="fas fa-calendar-alt mr-3 text-[#60A5FA]"></i>
                    <span>Joined Jan 15, 2024</span>
                </div>
                <div class="border-t border-[#4A5568] pt-6">
                    <div class="flex justify-between items-center space-x-4">
                        <button class="flex-1 bg-[#374151] hover:bg-[#60A5FA] text-white py-3 px-6 rounded-xl transition-all duration-300 flex items-center justify-center text-base font-semibold hover:shadow-lg">
                            <i class="fas fa-edit mr-2"></i>
                            Edit
                        </button>
                        <button class="flex-1 bg-[#374151] hover:bg-[#60A5FA] text-white py-3 px-6 rounded-xl transition-all duration-300 flex items-center justify-center text-base font-semibold hover:shadow-lg">
                            <i class="fas fa-archive mr-2"></i>
                            Archive
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-[#1F2937] rounded-lg shadow-lg overflow-hidden transform transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
            <div class="relative">
                <img src="{{ asset('assets/about_1.jpg') }}" alt="Jane Smith" class="w-full h-48 object-cover transition-transform duration-300 hover:scale-105">
                <div class="absolute top-2 right-2">
                    <span class="px-3 py-1 text-sm font-medium rounded-full bg-[#374151] text-[#9CA3AF] shadow-sm">
                        Active
                    </span>
                </div>
            </div>
            <div class="p-6">
                <h3 class="text-xl font-bold text-white mb-1 hover:text-[#9CA3AF] transition-colors duration-200">Jane Smith</h3>
                <p class="text-[#9CA3AF] text-sm mb-3">jane.smith@example.com</p>
                <div class="flex items-center text-sm text-[#9CA3AF] mb-4">
                    <i class="fas fa-calendar-alt mr-2"></i>
                    <span>Joined Feb 1, 2024</span>
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

        <div class="bg-[#1F2937] rounded-lg shadow-lg overflow-hidden transform transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
            <div class="relative">
                <img src="{{ asset('assets/default-profile.jpg') }}" alt="Mike Johnson" class="w-full h-48 object-cover transition-transform duration-300 hover:scale-105">
                <div class="absolute top-2 right-2">
                    <span class="px-3 py-1 text-sm font-medium rounded-full bg-[#374151] text-[#9CA3AF] shadow-sm">
                        Inactive
                    </span>
                </div>
            </div>
            <div class="p-6">
                <h3 class="text-xl font-bold text-white mb-1 hover:text-[#9CA3AF] transition-colors duration-200">Mike Johnson</h3>
                <p class="text-[#9CA3AF] text-sm mb-3">mike.johnson@example.com</p>
                <div class="flex items-center text-sm text-[#9CA3AF] mb-4">
                    <i class="fas fa-calendar-alt mr-2"></i>
                    <span>Joined Dec 20, 2023</span>
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

        <div class="bg-[#1F2937] rounded-lg shadow-lg overflow-hidden transform transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
            <div class="relative">
                <img src="{{ asset('assets/default-profile.jpg') }}" alt="Sarah Wilson" class="w-full h-48 object-cover transition-transform duration-300 hover:scale-105">
                <div class="absolute top-2 right-2">
                    <span class="px-3 py-1 text-sm font-medium rounded-full bg-[#374151] text-[#9CA3AF] shadow-sm">
                        Active
                    </span>
                </div>
            </div>
            <div class="p-6">
                <h3 class="text-xl font-bold text-white mb-1 hover:text-[#9CA3AF] transition-colors duration-200">Sarah Wilson</h3>
                <p class="text-[#9CA3AF] text-sm mb-3">sarah.wilson@example.com</p>
                <div class="flex items-center text-sm text-[#9CA3AF] mb-4">
                    <i class="fas fa-calendar-alt mr-2"></i>
                    <span>Joined Jan 5, 2024</span>
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

    <!-- Enhanced Pagination -->
    <div class="mt-12 mb-8 flex justify-center">
        <nav class="flex items-center space-x-3">
            <button class="px-4 py-2 rounded-xl bg-[#374151] text-[#60A5FA] hover:bg-[#60A5FA] hover:text-white border border-[#4A5568] transition-all duration-300 hover:shadow-lg">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button class="px-6 py-2 rounded-xl bg-[#60A5FA] text-white border border-[#4A5568] shadow-lg font-semibold min-w-[40px]">1</button>
            <button class="px-6 py-2 rounded-xl bg-[#374151] text-[#9CA3AF] hover:bg-[#60A5FA] hover:text-white border border-[#4A5568] transition-all duration-300 hover:shadow-lg font-semibold min-w-[40px]">2</button>
            <button class="px-6 py-2 rounded-xl bg-[#374151] text-[#9CA3AF] hover:bg-[#60A5FA] hover:text-white border border-[#4A5568] transition-all duration-300 hover:shadow-lg font-semibold min-w-[40px]">3</button>
            <button class="px-4 py-2 rounded-xl bg-[#374151] text-[#60A5FA] hover:bg-[#60A5FA] hover:text-white border border-[#4A5568] transition-all duration-300 hover:shadow-lg">
                <i class="fas fa-chevron-right"></i>
            </button>
        </nav>
    </div>
</div>
</div>

<script>
// Frontend-only confirmation dialog
function confirmArchive() {
    if (confirm('Are you sure you want to archive this member?')) {
        alert('Member archived successfully!');
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