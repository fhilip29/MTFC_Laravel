@extends('layouts.app')

@section('title', 'Sent Messages')

@section('content')
<div class="bg-gray-100 min-h-screen py-6 md:py-10">
    <div class="container mx-auto px-4">
        <!-- Header with Navigation Buttons -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-4 md:mb-0">Sent Messages</h1>
            <div class="space-x-2 flex">
                <a href="{{ route('user.messages') }}" class="bg-gray-600 text-white py-2 px-4 rounded-lg hover:bg-gray-700 transition duration-200 flex items-center">
                    <i class="fas fa-inbox mr-2"></i> Inbox
                </a>
                <a href="{{ route('user.messages.compose') }}" class="bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 transition duration-200 flex items-center">
                    <i class="fas fa-pen mr-2"></i> Compose Message
                </a>
            </div>
        </div>

        <!-- Sent Messages Container -->
        <div class="bg-white rounded-lg shadow-md border border-gray-200 mb-6">
            <div class="p-4">
                <div class="space-y-2">
                    @if(isset($sentMessages) && $sentMessages->count() > 0)
                        @foreach($sentMessages as $message)
                            <a href="{{ route('user.messages.show', $message->id) }}" class="block p-4 rounded-lg hover:bg-gray-50 border border-gray-200 transition">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0">
                                            @if($message->recipient->profile_image)
                                                <img src="{{ asset($message->recipient->profile_image) }}" alt="{{ $message->recipient->full_name }}" class="h-10 w-10 rounded-full">
                                            @else
                                                <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                    <span class="text-gray-600 font-semibold">{{ strtoupper(substr($message->recipient->full_name, 0, 2)) }}</span>
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <h3 class="text-sm font-semibold text-gray-800">
                                                To: {{ $message->recipient->full_name }}
                                            </h3>
                                            <p class="text-sm text-gray-500">{{ $message->subject }}</p>
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
                                <i class="fas fa-paper-plane"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-800 mb-1">No sent messages</h3>
                            <p class="text-gray-500 text-sm">Messages you send will appear here</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Tips Box -->
        <div class="bg-white rounded-lg shadow-md p-4 border border-gray-200">
            <h3 class="text-lg font-medium text-gray-800 mb-4">Messaging Tips</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i class="fas fa-envelope text-blue-500 text-lg"></i>
                    </div>
                    <div class="ml-3">
                        <h4 class="text-sm font-medium text-gray-900">Track Your Communications</h4>
                        <p class="text-sm text-gray-500">
                            You can keep track of all messages you've sent to trainers and staff here.
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