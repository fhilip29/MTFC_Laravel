@extends('layouts.app')

@section('title', 'Message Details')

@section('content')
<div class="bg-gray-100 min-h-screen py-6 md:py-10">
    <div class="container mx-auto px-4">
        <!-- Breadcrumb -->
        <div class="mb-6">
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        @if(Auth::user()->role == 'trainer')
                            <a href="{{ route('trainer.profile') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-red-600">
                                <i class="fas fa-user mr-2"></i>
                                Profile
                            </a>
                        @else
                            <a href="{{ route('profile') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-red-600">
                                <i class="fas fa-user mr-2"></i>
                                Profile
                            </a>
                        @endif
                    </li>
                    <li class="inline-flex items-center">
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-400 text-xs mx-1"></i>
                            <a href="{{ route('user.messages') }}" class="text-sm font-medium text-gray-700 hover:text-red-600">
                                Messages
                            </a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-400 text-xs mx-1"></i>
                            <span class="text-sm font-medium text-gray-500">{{ $message->subject }}</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>

        <!-- Header with Back Button -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-4 md:mb-0">Message Details</h1>
            <a href="{{ route('user.messages') }}" class="bg-gray-800 text-white py-2 px-4 rounded-lg hover:bg-gray-700 transition duration-200 flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Back to Messages
            </a>
        </div>

        <!-- Message Display -->
        <div class="bg-white rounded-lg shadow overflow-hidden mb-6 border border-gray-200">
            <!-- Message Header -->
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                <div class="flex flex-col space-y-2">
                    <h2 class="text-xl font-bold text-gray-800">{{ $message->subject }}</h2>
                    <div class="flex flex-col md:flex-row md:justify-between md:items-center text-sm text-gray-500">
                        <div class="flex items-center space-x-2 mb-2 md:mb-0">
                            <div class="flex-shrink-0">
                                @if($message->sender->profile_image)
                                    <img src="{{ asset($message->sender->profile_image) }}" alt="{{ $message->sender->full_name }}" class="h-8 w-8 rounded-full">
                                @else
                                    <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                                        <span class="text-gray-600 font-semibold text-xs">{{ strtoupper(substr($message->sender->full_name, 0, 2)) }}</span>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">{{ $message->sender->full_name }}</span>
                                @if($message->sender->role === 'admin')
                                    <span class="ml-1 px-2 py-0.5 bg-red-100 text-red-800 text-xs rounded-full">Admin</span>
                                @elseif($message->sender->role === 'trainer')
                                    <span class="ml-1 px-2 py-0.5 bg-purple-100 text-purple-800 text-xs rounded-full">Trainer</span>
                                @endif
                            </div>
                        </div>
                        <div>
                            <span class="text-gray-500">{{ $message->formatted_created_at }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Message Content -->
            <div class="p-6">
                <div class="prose max-w-none">
                    {!! nl2br(e($message->content)) !!}
                </div>
            </div>
            
            <!-- Reply Form -->
            <div class="bg-gray-50 p-6 border-t border-gray-200">
                <h3 class="text-lg font-medium text-gray-800 mb-4">Reply</h3>
                <form action="{{ route('user.messages.reply', $message->id) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <textarea name="content" rows="4" class="w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-red-500 focus:border-red-500" placeholder="Write your reply here..."></textarea>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 transition duration-200 flex items-center">
                            <i class="fas fa-reply mr-2"></i> Send Reply
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Previous Replies -->
        @if($message->replies && $message->replies->count() > 0)
            <h3 class="text-xl font-bold text-gray-800 mb-4">Previous Replies</h3>
            <div class="space-y-4">
                @foreach($message->replies as $reply)
                    <div class="bg-white rounded-lg shadow overflow-hidden border border-gray-200">
                        <div class="bg-gray-50 px-6 py-3 border-b border-gray-200">
                            <div class="flex flex-col md:flex-row md:justify-between md:items-center text-sm">
                                <div class="flex items-center space-x-2 mb-2 md:mb-0">
                                    <div class="flex-shrink-0">
                                        @if($reply->sender->profile_image)
                                            <img src="{{ asset($reply->sender->profile_image) }}" alt="{{ $reply->sender->full_name }}" class="h-6 w-6 rounded-full">
                                        @else
                                            <div class="h-6 w-6 rounded-full bg-gray-300 flex items-center justify-center">
                                                <span class="text-gray-600 font-semibold text-xs">{{ strtoupper(substr($reply->sender->full_name, 0, 2)) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <span class="font-medium text-gray-700">{{ $reply->sender->full_name }}</span>
                                        @if($reply->sender->role === 'admin')
                                            <span class="ml-1 px-2 py-0.5 bg-red-100 text-red-800 text-xs rounded-full">Admin</span>
                                        @elseif($reply->sender->role === 'trainer')
                                            <span class="ml-1 px-2 py-0.5 bg-purple-100 text-purple-800 text-xs rounded-full">Trainer</span>
                                        @endif
                                    </div>
                                </div>
                                <div>
                                    <span class="text-gray-500">{{ \Carbon\Carbon::parse($reply->created_at)->format('M d, Y h:i A') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="p-4">
                            <div class="prose max-w-none text-sm">
                                {!! nl2br(e($reply->content)) !!}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // If the message is not read yet and the current user is the recipient, mark it as read
    @if(!$message->is_read && $message->recipient_id == Auth::id())
    console.log('Marking message as read');
    fetch('{{ route("user.messages.read", $message->id) }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        credentials: 'same-origin'
    }).then(response => {
        if (!response.ok) {
            console.error('Failed to mark message as read');
        } else {
            console.log('Message marked as read');
        }
    }).catch(error => {
        console.error('Error marking message as read:', error);
    });
    @endif
    
    // Show success message if present
    @if(session('success'))
        Swal.fire({
            title: 'Success!',
            text: "{{ session('success') }}",
            icon: 'success',
            confirmButtonColor: '#10B981'
        });
    @endif
});
</script>
@endsection 