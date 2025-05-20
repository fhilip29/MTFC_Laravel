@extends('layouts.app')

@section('title', 'Messages')

@section('content')
<div class="bg-gray-100 min-h-screen py-6 md:py-10">
    <div class="container mx-auto px-4">
        <!-- Back Button -->
        <div class="flex justify-start mb-4">
            <a href="{{ Auth::user()->role == 'trainer' ? route('trainer.profile') : route('profile') }}" class="inline-flex items-center bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                <span>Back to Profile</span>
            </a>
        </div>

        <!-- Header with Compose Button -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-4 md:mb-0">Messages</h1>
            <a href="{{ route('user.messages.compose') }}" class="bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 transition duration-200 flex items-center">
                <i class="fas fa-pen mr-2"></i> Compose Message
            </a>
        </div>

        <!-- Message Controls -->
        <div class="bg-white rounded-lg shadow-md border border-gray-200 mb-6">
            <!-- Tabs -->
            <div class="border-b border-gray-200">
                <div class="flex">
                    <button type="button" class="tab-btn px-6 py-4 font-medium text-sm border-b-2 border-red-600 text-gray-800" id="inbox-tab">
                        <i class="fas fa-inbox mr-2"></i> Inbox
                        @if(isset($receivedMessages) && $receivedMessages->where('is_read', false)->count() > 0)
                            <span class="bg-red-600 text-white rounded-full text-xs px-2 py-0.5 ml-2">
                                {{ $receivedMessages->where('is_read', false)->count() }}
                            </span>
                        @endif
                    </button>
                    <a href="{{ route('user.messages.sent') }}" class="tab-btn px-6 py-4 font-medium text-sm border-b-2 border-transparent text-gray-500 hover:text-gray-700">
                        <i class="fas fa-paper-plane mr-2"></i> Sent
                    </a>
                </div>
            </div>

            <!-- Message Lists Container -->
            <div class="p-4">
                <!-- Inbox Messages -->
                <div class="space-y-2">
                    @if(isset($receivedMessages) && $receivedMessages->count() > 0)
                        @foreach($receivedMessages as $message)
                            <a href="{{ route('user.messages.show', $message->id) }}" class="block p-4 rounded-lg hover:bg-gray-50 border {{ $message->is_read ? 'border-gray-200' : 'border-red-200 bg-red-50' }} transition">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0">
                                            @if($message->sender->profile_image)
                                                <img src="{{ asset($message->sender->profile_image) }}" alt="{{ $message->sender->full_name }}" class="h-10 w-10 rounded-full">
                                            @else
                                                <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                    <span class="text-gray-600 font-semibold">{{ strtoupper(substr($message->sender->full_name, 0, 2)) }}</span>
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <h3 class="text-sm font-semibold {{ $message->is_read ? 'text-gray-800' : 'text-gray-900' }}">
                                                {{ $message->sender->full_name }}
                                                @if(isset($message->is_recent) && $message->is_recent)
                                                    <span class="ml-2 px-2 py-0.5 bg-green-100 text-green-800 text-xs rounded-full">New</span>
                                                @endif
                                            </h3>
                                            <p class="text-sm {{ $message->is_read ? 'text-gray-500' : 'text-gray-800 font-medium' }}">{{ $message->subject }}</p>
                                        </div>
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ \Carbon\Carbon::parse($message->created_at)->format('M d, Y') }}
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <p class="text-xs text-gray-500 truncate">{{ \Illuminate\Support\Str::limit($message->content, 100) }}</p>
                                </div>
                            </a>
                        @endforeach
                    @else
                        <div class="text-center py-10">
                            <div class="text-gray-400 text-5xl mb-4">
                                <i class="fas fa-inbox"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-800 mb-1">Your inbox is empty</h3>
                            <p class="text-gray-500 text-sm">Messages you receive will appear here</p>
                        </div>
                    @endif
                </div>

                <!-- Sent Messages section removed as it's now a separate page -->
            </div>
        </div>
        
        <!-- Tips Box -->
        <div class="bg-white rounded-lg shadow-md p-4 border border-gray-200">
            <h3 class="text-lg font-medium text-gray-800 mb-4">Messaging Tips</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i class="fas fa-bell text-blue-500 text-lg"></i>
                    </div>
                    <div class="ml-3">
                        <h4 class="text-sm font-medium text-gray-900">Notifications</h4>
                        <p class="text-sm text-gray-500">
                            You'll receive notifications when you get new messages from staff or other members.
                        </p>
                    </div>
                </div>
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i class="fas fa-question-circle text-yellow-500 text-lg"></i>
                    </div>
                    <div class="ml-3">
                        <h4 class="text-sm font-medium text-gray-900">Need Help?</h4>
                        <p class="text-sm text-gray-500">
                            You can message our staff for any questions about your membership or gym services.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Include SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Show success message if present
    @if(session('success'))
        Swal.fire({
            title: 'Success!',
            text: "{{ session('success') }}",
            icon: 'success',
            confirmButtonColor: '#10B981'
        });
    @endif
</script>
@endsection 