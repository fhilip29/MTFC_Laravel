@extends('layouts.admin')

@section('title', 'Message Details')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Page Heading -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-xl font-semibold text-white">Message Details</h1>
        <a href="{{ route('admin.messages') }}" class="bg-gray-600 hover:bg-gray-700 text-white text-sm px-4 py-2 rounded-lg transition flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Back to Messages
        </a>
    </div>

    <!-- Message Display -->
    <div class="bg-[#1F2937] rounded-2xl shadow-md border border-[#374151] overflow-hidden mb-6">
        <div class="p-5 border-b border-[#374151] flex justify-between items-center">
            <h2 class="text-lg font-semibold text-white">{{ $message->subject }}</h2>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                {{ $message->sender->role === 'admin' ? 'bg-red-500' : '' }}
                {{ $message->sender->role === 'trainer' ? 'bg-purple-500' : '' }}
                {{ $message->sender->role === 'member' ? 'bg-blue-500' : '' }}
                text-white">
                {{ ucfirst($message->sender->role) }}
            </span>
        </div>

        <div class="p-5">
            <!-- Sender Info -->
            <div class="mb-6 pb-4 border-b border-[#374151]">
                <div class="flex items-center space-x-4">
                    @if($message->sender->profile_image)
                        <img src="{{ asset($message->sender->profile_image) }}" alt="{{ $message->sender->full_name }}" class="h-14 w-14 rounded-full">
                    @else
                        <div class="h-14 w-14 rounded-full bg-blue-500 flex items-center justify-center">
                            <span class="text-white font-semibold">{{ strtoupper(substr($message->sender->full_name, 0, 2)) }}</span>
                        </div>
                    @endif
                    <div>
                        <h3 class="text-white font-semibold">{{ $message->sender->full_name }}</h3>
                        <p class="text-sm text-[#9CA3AF]">{{ $message->sender->email }}</p>
                        <p class="text-xs text-[#9CA3AF] mt-1">
                            {{ \Carbon\Carbon::parse($message->created_at)->format('M d, Y h:i A') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Message Content -->
            <div class="mb-6">
                <div class="p-4 bg-[#111827] rounded-lg">
                    <div class="text-[#9CA3AF] whitespace-pre-wrap">
                        {!! nl2br(e($message->content)) !!}
                    </div>
                </div>
            </div>

            <!-- Reply Form -->
            <div class="pt-4 border-t border-[#374151]">
                <h3 class="text-white font-semibold mb-4">Reply</h3>
                <form action="{{ route('admin.messages.reply', $message->id) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <textarea name="content" rows="5" class="w-full bg-[#111827] border border-[#374151] rounded-lg p-3 text-[#9CA3AF] focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Type your reply here..."></textarea>
                    </div>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg transition flex items-center text-sm">
                        <i class="fas fa-reply mr-2"></i> Send Reply
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Previous Replies -->
    @if($message->replies && $message->replies->count() > 0)
        <div class="bg-[#1F2937] rounded-2xl shadow-md border border-[#374151] overflow-hidden">
            <div class="p-5 border-b border-[#374151]">
                <h2 class="text-lg font-semibold text-white">Previous Replies</h2>
            </div>
            <div class="p-5">
                <div class="space-y-6">
                    @foreach($message->replies as $reply)
                        <div class="relative pl-8 before:absolute before:left-3 before:top-0 before:bottom-0 before:w-0.5 before:bg-blue-500">
                            <div class="absolute left-0 top-2 w-6 h-6 rounded-full bg-blue-500 flex items-center justify-center">
                                <i class="fas fa-reply text-xs text-white"></i>
                            </div>
                            <div class="bg-[#111827] rounded-lg overflow-hidden">
                                <div class="p-3 bg-[#1E293B] border-b border-[#374151] flex justify-between items-center">
                                    <div class="flex items-center space-x-3">
                                        @if($reply->sender->profile_image)
                                            <img src="{{ asset($reply->sender->profile_image) }}" alt="{{ $reply->sender->full_name }}" class="h-8 w-8 rounded-full">
                                        @else
                                            <div class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center">
                                                <span class="text-white font-semibold text-xs">{{ strtoupper(substr($reply->sender->full_name, 0, 2)) }}</span>
                                            </div>
                                        @endif
                                        <div>
                                            <h4 class="text-white text-sm font-semibold">{{ $reply->sender->full_name }}</h4>
                                            <p class="text-xs text-[#9CA3AF]">{{ \Carbon\Carbon::parse($reply->created_at)->format('M d, Y h:i A') }}</p>
                                        </div>
                                    </div>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-{{ $reply->sender->role === 'admin' ? 'red' : 'blue' }}-500 bg-opacity-20 text-{{ $reply->sender->role === 'admin' ? 'red' : 'blue' }}-500">
                                        {{ ucfirst($reply->sender->role) }}
                                    </span>
                                </div>
                                <div class="p-4">
                                    <div class="text-[#9CA3AF] text-sm whitespace-pre-wrap">
                                        {!! nl2br(e($reply->content)) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
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