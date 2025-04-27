@extends('layouts.app')

@section('title', 'Post')

@section('content')
<div class="min-h-screen bg-[#121212] text-white p-6">
    <div class="max-w-2xl mx-auto bg-[#1e1e1e] p-6 rounded-lg shadow-lg">
        <div class="flex items-start space-x-4">
            <img src="{{ $post->user->profile_image ? asset('storage/' . $post->user->profile_image) : asset('assets/default-user.png') }}" class="w-12 h-12 rounded-full" alt="User">
            <div>
                <h2 class="text-xl font-bold">{{ $post->user->full_name }}</h2>
                <p class="text-gray-400 text-sm">{{ $post->created_at->diffForHumans() }}</p>
                <p class="mt-4">{{ $post->content }}</p>

                @if ($post->images && $post->images->count())
                    <div class="grid grid-cols-2 gap-2 mt-4">
                        @foreach ($post->images as $image)
                            <img src="{{ asset('storage/' . $image->path) }}" class="rounded w-full h-40 object-cover">
                        @endforeach
                    </div>
                @endif

                <div class="mt-6">
                    <h3 class="text-lg font-semibold mb-2">Comments</h3>
                    <div class="space-y-3">
                        @foreach ($post->comments as $comment)
                            <div class="bg-[#2d2d2d] p-3 rounded">
                                <div class="flex justify-between">
                                    <span class="text-sm font-semibold">{{ $comment->user->full_name }}</span>
                                    <span class="text-xs text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="mt-1 text-sm">{{ $comment->content }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
