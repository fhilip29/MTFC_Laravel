@extends('layouts.admin')

@section('title', 'Announcement Management')

@section('content')
<div class="container mx-auto px-2 sm:px-4 py-4 sm:py-8">
    <div class="bg-[#1F2937] p-4 sm:p-6 rounded-2xl shadow-md border border-[#374151]">
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4 sm:gap-6 mb-6">
            <h1 class="text-xl sm:text-2xl font-bold text-white flex items-center gap-2 w-full sm:w-auto">
                <i class="fas fa-bullhorn text-[#9CA3AF]"></i> Announcement Management
            </h1>
            <button class="bg-[#374151] hover:bg-[#4B5563] text-white font-semibold flex items-center gap-2 px-4 py-2 rounded-lg shadow transition-colors w-full sm:w-auto justify-center">
                <i class="fas fa-plus"></i> <span class="sm:inline">Create Announcement</span>
            </button>
        </div>

        <!-- Create Announcement Form -->
        <div class="bg-[#374151] p-6 rounded-xl mb-8 border border-[#4B5563]">
            <h2 class="text-xl font-semibold text-white mb-4">Create New Announcement</h2>
            <form action="#" method="POST">
                <div class="grid grid-cols-1 gap-4 sm:gap-6 mb-6">
                    <div>
                        <label for="title" class="block text-[#9CA3AF] mb-2 text-sm sm:text-base">Announcement Title</label>
                        <input 
                            type="text" 
                            id="title" 
                            placeholder="Enter announcement title..." 
                            class="w-full px-3 sm:px-4 py-2 bg-[#1F2937] border border-[#4B5563] text-white rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-[#9CA3AF] placeholder-[#9CA3AF] text-sm sm:text-base"
                        >
                    </div>
                    
                    <div>
                        <label for="message" class="block text-[#9CA3AF] mb-2">Announcement Message</label>
                        <textarea 
                            id="message" 
                            rows="5" 
                            placeholder="Enter announcement message..." 
                            class="w-full px-4 py-2 bg-[#1F2937] border border-[#4B5563] text-white rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-[#9CA3AF] placeholder-[#9CA3AF]"
                        ></textarea>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="send-to" class="block text-[#9CA3AF] mb-2">Send To</label>
                            <select 
                                id="send-to" 
                                class="w-full px-4 py-2 bg-[#1F2937] border border-[#4B5563] text-white rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-[#9CA3AF]"
                            >
                                <option value="all">All Users</option>
                                <option value="active">Active Members</option>
                                <option value="trainers">Trainers</option>
                                <option value="staff">Staff</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="priority" class="block text-[#9CA3AF] mb-2">Priority Level</label>
                            <select 
                                id="priority" 
                                class="w-full px-4 py-2 bg-[#1F2937] border border-[#4B5563] text-white rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-[#9CA3AF]"
                            >
                                <option value="normal">Normal</option>
                                <option value="important">Important</option>
                                <option value="urgent">Urgent</option>
                            </select>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-[#9CA3AF] mb-2">Delivery Method</label>
                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 sm:gap-6">
                            <label class="inline-flex items-center">
                                <input type="checkbox" class="form-checkbox bg-[#1F2937] text-blue-500 border-[#4B5563] rounded" checked>
                                <span class="ml-2 text-white text-sm sm:text-base">In-App Notification</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="checkbox" class="form-checkbox bg-[#1F2937] text-blue-500 border-[#4B5563] rounded">
                                <span class="ml-2 text-white text-sm sm:text-base">Email</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="checkbox" class="form-checkbox bg-[#1F2937] text-blue-500 border-[#4B5563] rounded">
                                <span class="ml-2 text-white text-sm sm:text-base">SMS</span>
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="flex flex-col sm:flex-row justify-end gap-3 sm:gap-4">
                    <button type="button" class="w-full sm:w-auto px-4 py-2 bg-[#1F2937] text-white text-sm sm:text-base rounded-lg hover:bg-[#374151] transition-colors order-2 sm:order-1">
                        Schedule for Later
                    </button>
                    <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-blue-600 text-white text-sm sm:text-base rounded-lg hover:bg-blue-700 transition-colors order-1 sm:order-2">
                        Send Announcement
                    </button>
                </div>
            </form>
        </div>

        <!-- Previous Announcements -->
        <div class="mb-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-white">Recent Announcements</h2>
                <div class="relative w-full sm:w-1/3">
                    <input 
                        type="text" 
                        placeholder="Search announcements..." 
                        class="w-full pl-10 pr-4 py-2 bg-[#374151] border border-[#4B5563] text-white rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-[#9CA3AF] placeholder-[#9CA3AF]"
                    >
                    <i class="fas fa-search absolute left-3 top-3 text-[#9CA3AF]"></i>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto rounded-lg shadow-sm -mx-4 sm:mx-0">
            <div class="inline-block min-w-full align-middle">
            <table class="min-w-full text-xs sm:text-sm table-auto">
                <thead class="bg-[#374151] text-[#9CA3AF] uppercase text-xs">
                    <tr>
                        <th class="py-4 px-4 text-left">Title</th>
                        <th class="py-4 px-4 text-left">Date</th>
                        <th class="py-4 px-4 text-left">Sent To</th>
                        <th class="py-4 px-4 text-left">Method</th>
                        <th class="py-4 px-4 text-left">Status</th>
                        <th class="py-4 px-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-[#9CA3AF]">
                    @php
                        $announcements = [
                            [
                                'title' => 'Holiday Gym Hours',
                                'date' => '2024-06-15',
                                'sent_to' => 'All Users',
                                'method' => 'In-App, Email',
                                'status' => 'Sent',
                                'status_class' => 'bg-green-500',
                            ],
                            [
                                'title' => 'New Fitness Classes',
                                'date' => '2024-06-10',
                                'sent_to' => 'Active Members',
                                'method' => 'In-App',
                                'status' => 'Sent',
                                'status_class' => 'bg-green-500',
                            ],
                            [
                                'title' => 'Facility Maintenance',
                                'date' => '2024-06-22',
                                'sent_to' => 'All Users',
                                'method' => 'In-App, Email, SMS',
                                'status' => 'Scheduled',
                                'status_class' => 'bg-blue-500',
                            ],
                        ];
                    @endphp

                    @foreach($announcements as $announcement)
                        <tr class="hover:bg-[#374151] border-b border-[#374151]">
                            <td class="py-4 px-4 text-white align-middle">{{ $announcement['title'] }}</td>
                            <td class="py-4 px-4 align-middle">{{ $announcement['date'] }}</td>
                            <td class="py-4 px-4 align-middle">{{ $announcement['sent_to'] }}</td>
                            <td class="py-4 px-4 align-middle">{{ $announcement['method'] }}</td>
                            <td class="py-4 px-4 align-middle">
                                <span class="text-white px-2 py-1 rounded text-xs {{ $announcement['status_class'] }}">
                                    {{ $announcement['status'] }}
                                </span>
                            </td>
                            <td class="py-4 px-4 text-center align-middle">
                                <div class="flex justify-center gap-2">
                                    <a href="#" class="text-blue-400 hover:text-blue-300 cursor-pointer" title="View Announcement">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="#" class="text-blue-400 hover:text-blue-300 cursor-pointer" title="Edit Announcement">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="#" class="text-blue-400 hover:text-blue-300 cursor-pointer" title="Resend Announcement">
                                        <i class="fas fa-share"></i>
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
document.addEventListener('DOMContentLoaded', function() {
    // Toggle form visibility
    const createBtn = document.querySelector('button:has(i.fa-plus)');
    const form = document.querySelector('form').closest('div.bg-[#374151]');
    
    // Initially hide the form
    form.style.display = 'none';
    
    createBtn.addEventListener('click', function() {
        if (form.style.display === 'none') {
            form.style.display = 'block';
            createBtn.innerHTML = '<i class="fas fa-times"></i> Cancel';
        } else {
            form.style.display = 'none';
            createBtn.innerHTML = '<i class="fas fa-plus"></i> Create Announcement';
        }
    });
});
</script>
@endsection