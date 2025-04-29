@extends('layouts.app')

@section('title', 'Community')

@section('head')
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
<style>
    .post-card {
        transition: transform 0.2s ease;
    }
    .post-card:hover {
        transform: translateY(-2px);
    }
    .vote-button {
        transition: all 0.2s ease;
    }
    .vote-button:hover {
        transform: scale(1.1);
    }
    .modal-backdrop {
        background-color: rgba(0, 0, 0, 0.3);
        backdrop-filter: blur(2px);
    }
</style>
@endsection

@section('content')
<div class="flex min-h-screen bg-[#121212] text-white">
    <!-- Sidebar -->
    <div class="w-64 bg-[#1e1e1e] p-6 space-y-6 hidden md:block">
        <div class="space-y-4">
            <a href="#" class="flex items-center space-x-3 text-white hover:text-red-500 transition-colors">
                <i class="fas fa-home text-xl"></i>
                <span class="font-medium">Home</span>
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 p-6">
        <!-- Top Bar with Search -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Community</h1>
            <form action="{{ route('community.search') }}" method="GET" class="flex items-center">
                <input type="text" name="search" placeholder="Search posts..." class="p-2 rounded-lg bg-gray-700 text-white" required>
                <button type="submit" class="ml-2 p-2 rounded-lg bg-blue-600 text-white">Search</button>
            </form>
        </div>

        <!-- Create Post Dropdown -->
        <div x-data="{ open: false }">
            <button @click="open = !open" class="bg-blue-600 text-white px-4 py-2 rounded mb-6">
                Create Post
            </button>
            <div x-show="open" x-transition class="bg-white text-black p-4 rounded-lg shadow-lg max-w-xl w-full">
                <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <textarea name="content" rows="4" class="w-full p-2 border rounded focus:outline-none" placeholder="What's on your mind?" required></textarea>
                    <input type="file" name="images[]" multiple class="mt-2">
                    <div class="mt-3 text-right">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                            Post
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Posts -->
        <div class="space-y-6">
        @foreach ($posts as $post)
    <div class="bg-[#2d2d2d] p-4 rounded-lg post-card">
        <div class="flex items-start space-x-4">
            <img src="{{ $post->user->profile_image ? asset('storage/' . $post->user->profile_image) : asset('assets/default-user.png') }}" alt="User" class="w-10 h-10 rounded-full">
            <div>
                <div class="flex items-center space-x-2">
                    <h3 class="font-semibold">{{ $post->user->full_name }}</h3>
                    <span class="text-sm text-gray-400">{{ $post->created_at->diffForHumans() }}</span>
                </div>
                <p class="mt-2 text-white">{{ $post->content }}</p>

                @if ($post->images && $post->images->count())
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-2 mt-4">
                        @foreach ($post->images as $image)
                            <img src="{{ asset('storage/' . $image->path) }}" class="rounded w-full h-40 object-cover">
                        @endforeach
                    </div>
                @endif

                <div class="mt-4 flex space-x-4 text-gray-400">
                    <form method="POST" action="{{ route('posts.like', $post->id) }}">
                        @csrf
                        <button type="submit" class="vote-button hover:text-red-500">
                            <i class="fas fa-heart"></i> {{ $post->likes->count() }}
                        </button>
                    </form>

                    <span class="vote-button text-gray-400">
                        <i class="fas fa-comment"></i> {{ $post->comments->count() }}
                    </span>
                </div>

                <!-- Comments Section -->
                <div class="mt-4 space-y-2">
                    @foreach ($post->comments as $comment)
                        <div class="bg-[#1e1e1e] p-3 rounded">
                            <div class="flex justify-between">
                                <span class="text-sm font-semibold">{{ $comment->user->full_name }}</span>
                                <span class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-sm mt-1">{{ $comment->content }}</p>

                            @if (auth()->check() && auth()->id() === $comment->user_id)
                                <div class="flex space-x-2 mt-2">
                                    <!-- Delete Comment -->
                                    <form action="{{ route('comments.destroy', $comment->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-400 text-xs">Delete</button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <!-- Comment Form -->
                @auth
                    <form action="{{ route('comments.store', $post->id) }}" method="POST" class="mt-4">
                        @csrf
                        <textarea name="content" rows="2" class="w-full p-2 border rounded text-black" placeholder="Add a comment..." required></textarea>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 mt-2 rounded hover:bg-blue-700">
                            Comment
                        </button>
                    </form>
                @else
                    <p class="text-sm text-gray-400 mt-2">You need to log in to comment.</p>
                @endauth
            </div>
        </div>
    </div>
@endforeach

        </div>
    </div>
</div>
@endsection
