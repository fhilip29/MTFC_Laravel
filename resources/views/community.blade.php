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
            <a href="{{ route('community') }}" class="flex items-center space-x-3 text-white hover:text-red-500 transition-colors">
                <i class="fas fa-home text-xl"></i>
                <span class="font-medium">Home</span>
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 p-6">
        <!-- Top Bar with Search -->
        <div class="flex justify-between items-center mb-6">
        <h1 class="text-4xl font-extrabold text-white tracking-wide mb-2">Community</h1>
            <form action="{{ route('community.search') }}" method="GET" class="flex items-center space-x-2">
                <input 
                    type="text" 
                    name="query" 
                    placeholder="Search posts..." 
                    class="p-2 rounded-lg bg-gray-700 text-white focus:ring-2 focus:ring-blue-500 outline-none w-64" 
                    required 
                    aria-label="Search posts"
                >
                <button 
                    type="submit" 
                    class="ml-2 p-2 rounded-lg bg-blue-600 text-white hover:bg-blue-500 transition duration-200"
                    aria-label="Search"
                >
                    <i class="fas fa-search"></i>
                </button>
                <!-- Clear Button -->
                <button 
                    type="button" 
                    id="clearSearch" 
                    class="ml-2 p-2 rounded-lg bg-red-600 text-white hover:bg-red-500 transition duration-200"
                    aria-label="Clear search"
                    style="display: none;"
                >
                    <i class="fas fa-times"></i>
                </button>
            </form>
        </div>

        <!-- Create Post Box -->
        <div x-data="{ open: false }" class="mb-8">
            <button @click="open = !open"
                    class="flex items-center gap-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-5 py-3 rounded-xl shadow hover:from-indigo-500 hover:to-purple-500 transition-all">
                <i class="fas fa-plus"></i>
                <span>Create a Post</span>
            </button>

            <div x-show="open" x-transition
                 class="mt-4 bg-[#1e1e1e] border border-gray-700 rounded-xl p-6 shadow-lg w-full max-w-2xl">
                <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <!-- Post Text -->
                    <div class="relative">
                        <textarea id="postContent" name="content" rows="3"
                                  class="w-full bg-[#2c2c2c] text-white p-3 rounded-lg border border-gray-600 focus:ring-2 focus:ring-blue-500 outline-none resize-none"
                                  placeholder="Share something with the community..." required></textarea>
                    </div>

                    <!-- Enhanced Image Upload UI -->
                    <div x-data="imageUploader()" class="mt-4">
                        <label for="imageUpload"
                               class="block w-full border-2 border-dashed border-gray-600 bg-[#2c2c2c] rounded-lg p-6 cursor-pointer text-center hover:border-blue-500 transition">
                            <div class="flex flex-col items-center justify-center space-y-2">
                                <i class="fas fa-cloud-upload-alt text-3xl text-blue-400"></i>
                                <span class="text-sm text-gray-400">Click or drag images to upload (Max 5 images)</span>
                            </div>
                            <input id="imageUpload" name="images[]" type="file" multiple accept="image/*"
                                   class="hidden"
                                   @change="previewImages($event)">
                        </label>

                        <!-- Image Preview -->
                        <template x-if="files.length > 0">
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-3 mt-4">
                                <template x-for="(file, index) in files" :key="index">
                                    <div class="relative w-full h-40 rounded overflow-hidden border border-gray-600">
                                        <img :src="file" class="absolute w-full h-full object-cover rounded">
                                        <button type="button"
                                                class="absolute top-1 right-1 bg-black bg-opacity-60 text-white text-xs px-2 py-1 rounded-full"
                                                @click="removeImage(index)">
                                            &times;
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>

                    <!-- Submit Button -->
                    <div class="text-right">
                        <button type="submit"
                                class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-semibold px-6 py-2 rounded-lg shadow-md transition">
                            Post
                        </button>
                    </div>
                </form>
            </div>
        </div>

<!-- Posts -->
<div class="space-y-6">
    @foreach ($posts as $post)
        <div class="bg-[#2d2d2d] p-4 rounded-lg post-card shadow relative">
            <!-- Post Content -->
            <div class="flex items-start space-x-4">
                <img src="{{ $post->user->profile_image ? asset('storage/' . $post->user->profile_image) : asset('assets/default-user.png') }}" alt="User" class="w-10 h-10 rounded-full">
                <div class="w-full">
                    <div class="flex items-center space-x-2">
                        <h3 class="font-semibold">{{ $post->user->full_name }}</h3>
                        <span class="text-sm text-gray-400">{{ $post->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="mt-2 text-white">{{ $post->content }}</p>

                    @if ($post->images && $post->images->count())
                    <div class="grid grid-cols-5 md:grid-cols-4 gap-3 mt-0">
                        @foreach ($post->images as $image)
                            <img src="{{ asset('storage/' . $image->path) }}" class="rounded w-80 h-80 object-cover">
                        @endforeach
                    </div>
                    @endif

                    <!-- Like and Comment Buttons -->
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

                    <!-- Post Deletion (Only if the user is the post owner) -->
                    @if (auth()->check() && auth()->id() === $post->user_id)
                        <form action="{{ route('posts.destroy', $post->id) }}" method="POST" 
                            ="return confirm('Are you sure you want to delete this post?');" 
                            class="absolute top-3 right-3 z-10">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-500 hover:text-red-600 text-lg" title="Delete Post">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                        </form>
                    @endif

                    <!-- Comments Section -->
                    <div class="mt-4 space-y-2">
                        @foreach ($post->comments as $comment)
                            <div class="bg-[#1e1e1e] p-3 rounded-lg flex items-start space-x-3 shadow-sm">
                                <img src="{{ $comment->user->profile_image ? asset('storage/' . $comment->user->profile_image) : asset('assets/default-user.png') }}"
                                     alt="User" class="w-8 h-8 rounded-full mt-1">
                                <div class="flex-1">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <span class="text-sm font-semibold text-white">{{ $comment->user->full_name }}</span>
                                            <span class="text-xs text-gray-500 ml-2">{{ $comment->created_at->diffForHumans() }}</span>
                                        </div>

                                        @if (auth()->check() && auth()->id() === $comment->user_id)
                                            <form action="{{ route('comments.destroy', $comment->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this comment?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-400 text-xs hover:text-red-600 transition z-10">
                                                    Delete
                                                </button>
                                            </form>
                                        @endif
                                    </div>

                                    <p class="text-base md:text-lg mt-2 text-gray-200 leading-relaxed">{{ $comment->content }}</p>

                                </div>
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


<script>
    // Clear search input and button visibility
    const searchInput = document.querySelector('input[name="query"]');
    const clearSearchButton = document.getElementById('clearSearch');

    searchInput.addEventListener('input', () => {
        if (searchInput.value.length > 0) {
            clearSearchButton.style.display = 'inline-block';
        } else {
            clearSearchButton.style.display = 'none';
        }
    });

    clearSearchButton.addEventListener('click', () => {
        searchInput.value = '';
        clearSearchButton.style.display = 'none';
    });

    function imageUploader() {
        return {
            files: [],
            previewImages(event) {
                this.files = [];
                const selectedFiles = [...event.target.files].slice(0, 5); // limit to 5 images
                selectedFiles.forEach(file => {
                    const reader = new FileReader();
                    reader.onload = e => this.files.push(e.target.result);
                    reader.readAsDataURL(file);
                });
            },
            removeImage(index) {
                this.files.splice(index, 1);
            }
        };
    }
</script>
@endsection
