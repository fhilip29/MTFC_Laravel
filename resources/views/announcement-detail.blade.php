@extends('layouts.app')

@section('title', $announcement->title)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto bg-white rounded-xl shadow-lg overflow-hidden">
        <!-- Announcement Header -->
        <div class="p-6 border-b border-gray-100">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ $announcement->title }}</h1>
                    <p class="text-sm text-gray-500">
                        {{ $announcement->created_at->format('F j, Y') }}
                    </p>
                </div>
                @if($announcement->created_at->isToday())
                    <span class="px-2 py-1 bg-red-100 text-red-600 text-xs font-semibold rounded-full flex-shrink-0 ml-2">New</span>
                @endif
            </div>
        </div>
        
        <!-- Announcement Content -->
        <div class="p-6">
            <div class="prose max-w-none text-gray-700 break-words whitespace-normal">
                {!! nl2br(e($announcement->message)) !!}
            </div>
        </div>
        
        <!-- Back to Announcements link -->
        <div class="p-6 bg-gray-50">
            <a href="{{ route('announcements') }}" class="text-gray-600 hover:text-gray-800 transition-colors flex items-center w-max">
                <i class="fas fa-arrow-left mr-2"></i> Back to Announcements
            </a>
        </div>
    </div>
</div>
@endsection 