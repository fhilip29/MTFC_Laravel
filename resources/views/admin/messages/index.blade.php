@extends('layouts.admin')

@section('title', 'Messages')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Page Heading -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-xl font-semibold text-white">User Messages</h1>
        <a href="{{ route('admin.dashboard') }}" class="bg-gray-600 hover:bg-gray-700 text-white text-sm px-4 py-2 rounded-lg transition flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
        </a>
    </div>

    <!-- Messages Table -->
    <div class="bg-[#1F2937] rounded-2xl shadow-md border border-[#374151] overflow-hidden">
        <div class="p-5 border-b border-[#374151] flex justify-between items-center">
            <h2 class="text-lg font-semibold text-white">All Messages</h2>
            <span class="bg-blue-600 text-white text-xs px-3 py-1 rounded-full">
                {{ $messages->where('is_read', false)->count() }} Unread
            </span>
        </div>
        
        <div class="p-5">
            @if($messages->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-[#374151] text-[#9CA3AF] uppercase text-xs">
                            <tr>
                                <th class="py-3 px-4 text-left">From</th>
                                <th class="py-3 px-4 text-left">Subject</th>
                                <th class="py-3 px-4 text-left">Date</th>
                                <th class="py-3 px-4 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="text-[#9CA3AF]">
                            @foreach($messages as $message)
                                <tr class="border-b border-[#374151] hover:bg-[#2D3748] {{ $message->is_read ? '' : 'bg-[#2D3848]' }}">
                                    <td class="py-3 px-4">
                                        <div class="flex items-center space-x-3">
                                            @if($message->sender->profile_image)
                                                <img src="{{ asset($message->sender->profile_image) }}" alt="{{ $message->sender->full_name }}" class="h-8 w-8 rounded-full">
                                            @else
                                                <div class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center">
                                                    <span class="text-white font-semibold text-xs">{{ strtoupper(substr($message->sender->full_name, 0, 2)) }}</span>
                                                </div>
                                            @endif
                                            <div>
                                                <p class="text-white font-medium">{{ $message->sender->full_name }}</p>
                                                <p class="text-xs text-[#9CA3AF]">{{ $message->sender->email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4 font-medium {{ $message->is_read ? 'text-white' : 'text-white' }}">
                                        <div class="pl-0">{{ $message->subject }}</div>
                                        @if(!$message->is_read)
                                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-500 text-white">
                                                New
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4">
                                        {{ \Carbon\Carbon::parse($message->created_at)->format('M d, Y h:i A') }}
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        <a href="{{ route('admin.messages.show', $message->id) }}" class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white text-xs px-3 py-1 rounded transition">
                                            <i class="fas fa-eye mr-1"></i> View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-6">
                    {{ $messages->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-inbox text-5xl text-[#4B5563] mb-4"></i>
                    <p class="text-white text-lg">No messages found</p>
                    <p class="text-[#9CA3AF] mt-2">Messages from your users will appear here</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('success'))
            Swal.fire({
                title: 'Success!',
                text: "{{ session('success') }}",
                icon: 'success',
                confirmButtonColor: '#3B82F6',
                background: '#1F2937',
                color: '#FFFFFF',
                customClass: {
                    popup: 'rounded-lg border border-[#374151]',
                    title: 'text-white text-xl',
                    htmlContainer: 'text-[#9CA3AF]',
                    confirmButton: 'rounded-md px-4 py-2'
                }
            });
        @endif
    });
</script>
@endsection 