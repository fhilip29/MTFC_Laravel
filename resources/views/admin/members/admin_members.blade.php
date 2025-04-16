@extends('layouts.admin')
@section('title', 'Manage Members')

@section('content')
<div class="container mx-auto px-4 py-6 sm:py-8">
    <div class="bg-[#1F2937] p-4 sm:p-6 rounded-2xl shadow-md border border-[#374151]">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 sm:gap-2 mb-6">
            <h1 class="text-xl sm:text-2xl font-bold text-white flex items-center gap-2">
                <i class="fas fa-users text-[#9CA3AF]"></i> Manage Members
            </h1>
            <button class="w-full sm:w-auto bg-[#374151] hover:bg-[#4B5563] text-white font-semibold flex items-center justify-center gap-2 px-4 py-2 rounded-lg shadow transition-colors">
                <i class="fas fa-plus"></i> Add Member
            </button>
        </div>

        <div class="mb-6 flex flex-col sm:flex-row gap-4">
            <div class="relative w-full sm:w-2/3 md:w-1/2 lg:w-1/3">
                <input 
                    type="text" 
                    placeholder="Search members..." 
                    class="w-full pl-10 pr-4 py-3 bg-[#374151] border border-[#4B5563] text-white rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-[#9CA3AF] placeholder-[#9CA3AF]"
                >
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-[#9CA3AF]"></i>
            </div>
            <select class="w-full sm:w-48 px-4 py-3 bg-[#374151] border border-[#4B5563] text-white rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-[#9CA3AF]">
                <option value="all">All Members</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="archived">Archived</option>
            </select>
        </div>

        <div class="-mx-4 sm:mx-0 overflow-x-auto rounded-lg shadow-md bg-[#1F2937]">
            <div class="min-w-full inline-block align-middle">
                <div class="overflow-hidden">
                    <table class="min-w-full divide-y divide-[#374151] text-sm">
                        <thead class="bg-[#374151] text-[#9CA3AF] uppercase text-xs sticky top-0 z-10">
                            <tr>
                                <th class="px-4 py-3 text-left">Member</th>
                                <th class="hidden sm:table-cell px-4 py-3 text-left">Email</th>
                                <th class="hidden md:table-cell px-4 py-3 text-left">Join Date</th>
                                <th class="px-4 py-3 text-left">Status</th>
                                <th class="px-4 py-3 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#374151] text-[#9CA3AF]">
                            @php
                                $members = [
                                    [
                                        'name' => 'John Doe',
                                        'email' => 'john.doe@example.com',
                                        'join_date' => 'Jan 15, 2024',
                                        'status' => 'Active',
                                        'status_class' => 'bg-green-500',
                                    ],
                                    [
                                        'name' => 'Jane Smith',
                                        'email' => 'jane.smith@example.com',
                                        'join_date' => 'Feb 1, 2024',
                                        'status' => 'Active',
                                        'status_class' => 'bg-green-500',
                                    ],
                                    [
                                        'name' => 'Mike Johnson',
                                        'email' => 'mike.johnson@example.com',
                                        'join_date' => 'Jan 20, 2024',
                                        'status' => 'Inactive',
                                        'status_class' => 'bg-red-500',
                                    ],
                                ];
                            @endphp

                            @foreach($members as $member)
                                <tr class="hover:bg-[#374151] transition-colors">
                                    <td class="px-4 py-3 text-white">
                                        <div class="flex items-center gap-3">
                                            <img src="{{ asset('assets/default-profile.jpg') }}" alt="{{ $member['name'] }}" class="w-10 h-10 rounded-full object-cover">
                                            <div class="flex flex-col">
                                                <span class="font-medium">{{ $member['name'] }}</span>
                                                <span class="text-xs text-[#9CA3AF] sm:hidden">{{ $member['email'] }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="hidden sm:table-cell px-4 py-3">{{ $member['email'] }}</td>
                                    <td class="hidden md:table-cell px-4 py-3">{{ $member['join_date'] }}</td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full text-white {{ $member['status_class'] }}">
                                            {{ $member['status'] }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="flex justify-center items-center gap-3">
                                            <button class="text-blue-400 hover:text-blue-300 transition-colors" title="Edit Member">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="text-blue-400 hover:text-blue-300 transition-colors" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="text-blue-400 hover:text-blue-300 transition-colors" title="Archive Member">
                                                <i class="fas fa-archive"></i>
                                            </button>
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
</div>
@endsection