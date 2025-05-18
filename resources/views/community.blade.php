@extends('layouts.app')

@section('title', 'Community')

@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
<!-- Alpine.js is already included in app.blade.php, no need to include again -->
<!-- SweetAlert2 is already included in app.blade.php -->
<style>
    body {
        background-color: #0f0f0f;
        overflow-x: hidden;
        margin: 0;
        padding: 0;
    }
    
    .page-container {
        display: flex;
        position: relative;
        min-height: 100vh;
        padding-top: 56px;
    }
    
    .sidebar {
        position: fixed;
        top: 56px;
        left: 0;
        width: 256px;
        height: calc(100vh - 56px);
        background-color: #1a1a1a;
        border-right: 1px solid #2a2a2a;
        overflow-y: auto;
        z-index: 10;
        transform: translateZ(0);
        -webkit-transform: translateZ(0);
        will-change: transform;
        backface-visibility: hidden;
        -webkit-backface-visibility: hidden;
        transition: top 0.2s ease;
    }
    
    .content-area {
        flex: 1;
        min-width: 0;
        overflow-x: hidden;
        margin-left: 256px;
        padding-top: 56px;
        position: relative;
    }
    
    @media (max-width: 1023px) {
        .sidebar {
            display: none;
        }
        .content-area {
            margin-left: 0;
        }
    }
    
    .post-card {
        transition: all 0.2s ease;
        border: 1px solid rgba(255, 255, 255, 0.1);
        word-wrap: break-word;
        overflow-wrap: break-word;
        word-break: break-word;
        max-width: 100%;
    }
    
    .post-text {
        white-space: normal !important;
        word-wrap: break-word !important;
        overflow-wrap: break-word !important;
        word-break: break-word !important;
        max-width: 100% !important;
        line-height: 1.5 !important;
        text-overflow: ellipsis !important;
        display: block !important;
        overflow: visible !important;
    }
    
    .post-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }
    
    .vote-button {
        transition: all 0.2s ease;
    }
    
    .vote-button.liked {
        color: #ff4040;
    }
    
    .vote-button:hover {
        transform: scale(1.1);
    }
    
    .modal-backdrop {
        background-color: rgba(0, 0, 0, 0.7);
        backdrop-filter: blur(3px);
    }
    
    .comment-area {
        background-color: #1a1a1a;
        transition: all 0.3s ease;
    }
    
    .comment-area:focus {
        background-color: #212121;
        border-color: #ffffff;
    }
    
    .image-grid-1 { grid-template-columns: repeat(1, 300px) !important; }
    .image-grid-2 { grid-template-columns: repeat(2, 300px) !important; }
    .image-grid-3 { grid-template-columns: repeat(3, 300px) !important; }
    .image-grid-4 { grid-template-columns: repeat(4, 300px) !important; }
    .image-grid-5 { grid-template-columns: repeat(5, 300px) !important; }
    
    .post-image-container {
        width: 300px !important;
        height: 300px !important;
        overflow: hidden !important;
        border-radius: 8px !important;
        margin-right: 16px !important;
        flex: 0 0 300px !important;
        padding: 8px !important;
    }
    
    .post-image {
        width: 100% !important;
        height: 100% !important;
        object-fit: cover !important;
        border-radius: 4px !important;
    }
    
    .post-image-sm {
        width: 80px;
        height: 80px;
    }
    
    /* Transition effects */
    .fade-enter-active, .fade-leave-active {
        transition: opacity 0.3s;
    }
    .fade-enter, .fade-leave-to {
        opacity: 0;
    }
    
    /* Custom scrollbar */
    ::-webkit-scrollbar {
        width: 8px;
    }
    
    ::-webkit-scrollbar-track {
        background: #0f0f0f;
    }
    
    ::-webkit-scrollbar-thumb {
        background: #333;
        border-radius: 4px;
    }
    
    ::-webkit-scrollbar-thumb:hover {
        background: #444;
    }
    
    /* Dropdown menus */
    .dropdown-menu {
        background-color: #1a1a1a;
        border: 1px solid rgba(255, 255, 255, 0.1);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }
    
    .dropdown-item {
        color: #f1f1f1;
        transition: all 0.2s ease;
    }
    
    .dropdown-item:hover {
        background-color: #2a2a2a;
    }

    .image-grid {
        display: flex !important;
        flex-direction: row !important;
        overflow-x: auto !important;
        padding: 16px !important;
        gap: 16px !important;
        scroll-snap-type: x mandatory !important;
        -webkit-overflow-scrolling: touch !important;
        margin: -8px !important;
        max-width: 100% !important;
        flex-wrap: wrap !important;
    }

    .image-grid::-webkit-scrollbar {
        height: 8px !important;
    }

    .image-grid::-webkit-scrollbar-track {
        background: #1a1a1a !important;
        border-radius: 4px !important;
    }

    .image-grid::-webkit-scrollbar-thumb {
        background: #4a4a4a !important;
        border-radius: 4px !important;
    }

    .image-grid::-webkit-scrollbar-thumb:hover {
        background: #5a5a5a !important;
    }

    /* Responsive adjustments for mobile */
    @media (max-width: 640px) {
        .image-grid {
            flex-wrap: wrap !important;
            justify-content: center !important;
        }
        
        .post-image-container {
            width: 100% !important;
            max-width: 300px !important;
            height: 250px !important;
            margin-bottom: 8px !important;
        }
        
        .post-image {
            width: 100% !important;
            height: 100% !important;
        }
    }
</style>
@endsection

@section('content')
<div class="bg-[#0f0f0f] text-white relative">
    <div class="max-w-screen-2xl mx-auto flex">
        <!-- Sidebar -->
        <div class="w-64 bg-[#1a1a1a] border-r border-[#2a2a2a] hidden lg:block sticky top-[56px] self-start h-screen overflow-y-auto">
            <div class="p-6 space-y-6">
                <a href="{{ route('community') }}" class="flex items-center space-x-3 {{ !request()->route('tag') && !request()->has('query') ? 'text-white' : 'text-gray-400 hover:text-white' }} transition-colors">
                    <i class="fas fa-home text-xl"></i>
                    <span class="font-medium">Home</span>
                </a>
                
                <a href="{{ route('community') }}?sort=popular" class="flex items-center space-x-3 text-gray-400 hover:text-white transition-colors">
                    <i class="fas fa-fire text-xl"></i>
                    <span class="font-medium">Popular</span>
                </a>
                
                <a href="{{ route('community') }}" class="flex items-center space-x-3 text-gray-400 hover:text-white transition-colors">
                    <i class="fas fa-calendar-alt text-xl"></i>
                    <span class="font-medium">Recent</span>
                </a>
                
                @auth
                <a href="{{ route('community') }}?user={{ auth()->id() }}" 
                   class="flex items-center space-x-3 {{ isset($currentUserId) && $currentUserId == auth()->id() ? 'text-white' : 'text-gray-400 hover:text-white' }} transition-colors">
                    <i class="fas fa-user text-xl"></i>
                    <span class="font-medium">My Posts</span>
                </a>
                @endauth
                
                <div class="pt-6 border-t border-[#2a2a2a]">
                    <h3 class="text-xs uppercase text-gray-500 font-semibold mb-3 tracking-wider">Categories</h3>
                    <div class="space-y-2">
                        @foreach($tags as $tagItem)
                        <a href="{{ route('community.tag', $tagItem->slug) }}" 
                           class="flex items-center space-x-2 {{ isset($tag) && $tag == $tagItem->slug ? 'text-white bg-[#2a2a2a]' : 'text-gray-400 hover:text-white' }} transition-colors p-2 rounded hover:bg-[#2a2a2a]">
                            @if($tagItem->slug == 'boxing')
                                @include('partials.boxing-icon', ['class' => 'w-4 h-4'])
                            @else
                                <i class="fas {{ 
                                    $tagItem->slug == 'workouts' ? 'fa-dumbbell' : 
                                    ($tagItem->slug == 'muay-thai' ? 'fa-fist-raised' : 
                                    ($tagItem->slug == 'jiu-jitsu' ? 'fa-hand-rock' : 
                                    ($tagItem->slug == 'nutrition' ? 'fa-utensils' : 
                                    ($tagItem->slug == 'cardio' ? 'fa-running' : 
                                    ($tagItem->slug == 'strength-training' ? 'fa-dumbbell' : 
                                    ($tagItem->slug == 'weight-loss' ? 'fa-weight' : 
                                    ($tagItem->slug == 'community-events' ? 'fa-users' : 'fa-tag')))))))
                                }}"></i>
                            @endif
                            <span>{{ $tagItem->name }}</span>
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 min-w-0 overflow-x-hidden">
            <div class="p-4 md:p-6">
                <!-- Top Bar with Search -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
                    <div>
                        <h1 class="text-3xl md:text-4xl font-extrabold text-white tracking-wide">
                            @if(isset($currentUserId) && $currentUserId == auth()->id())
                                My Posts
                            @elseif(isset($tag))
                                {{ ucfirst(str_replace('-', ' ', $tag)) }}
                            @else
                                Community
                            @endif
                        </h1>
                        <p class="text-gray-400 mt-1">
                            @if(isset($currentUserId) && $currentUserId == auth()->id())
                                Your contributions to the community
                            @elseif(isset($tag))
                                Posts related to {{ str_replace('-', ' ', $tag) }}
                            @else
                                Share and connect with other fitness enthusiasts
                            @endif
                        </p>
                    </div>
                    
                    <div class="flex items-center gap-4">
                        <form action="{{ route('community.search') }}" method="GET" class="flex-1 relative">
                            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            <input 
                                type="text" 
                                name="query" 
                                value="{{ request()->query('query') }}"
                                placeholder="Search posts..." 
                                class="py-2 pl-10 pr-10 rounded-full bg-[#1a1a1a] text-white border border-[#2a2a2a] focus:ring-2 focus:ring-white/20 outline-none w-full transition-all" 
                                aria-label="Search posts"
                            >
                            
                            <!-- Clear Button -->
                            @if(request()->has('query'))
                            <a href="{{ route('community') }}" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-white"
                                aria-label="Clear search">
                                <i class="fas fa-times"></i>
                            </a>
                            @endif
                        </form>
                        
                        @auth
                        <button x-data @click="$dispatch('open-modal')" 
                                class="flex items-center space-x-2 bg-white text-black font-semibold px-5 py-2 rounded-full shadow-lg hover:bg-gray-200 transition-colors">
                            <i class="fas fa-plus"></i>
                            <span class="hidden md:inline">Create Post</span>
                        </button>
                        @else
                        <a href="{{ route('login') }}" 
                           class="flex items-center space-x-2 bg-gray-700 text-white font-semibold px-5 py-2 rounded-full shadow-lg hover:bg-gray-600 transition-colors">
                            <i class="fas fa-sign-in-alt"></i>
                            <span class="hidden md:inline">Login to Post</span>
                        </a>
                        @endauth
                    </div>
                </div>

                <!-- Mobile category buttons (visible on small screens) -->
                <div class="flex flex-col gap-2 mb-6 lg:hidden">
                    <!-- Navigation options -->
                    <div class="flex flex-col gap-2 mb-3">
                        <h3 class="text-xs uppercase text-gray-500 font-semibold mb-1 tracking-wider px-2">Navigation</h3>
                        <div class="flex overflow-x-auto gap-2 pb-2">
                            <a href="{{ route('community') }}" class="flex-shrink-0 px-4 py-2 {{ !request()->has('sort') && !request()->has('user') ? 'bg-[#2a2a2a]' : 'bg-[#1a1a1a]' }} rounded-full text-sm text-white border border-[#2a2a2a] hover:bg-[#2a2a2a]">
                                <i class="fas fa-clock mr-2"></i>Recent
                            </a>
                            <a href="{{ route('community') }}?sort=popular" class="flex-shrink-0 px-4 py-2 {{ request()->has('sort') && request()->input('sort') === 'popular' ? 'bg-[#2a2a2a]' : 'bg-[#1a1a1a]' }} rounded-full text-sm text-white border border-[#2a2a2a] hover:bg-[#2a2a2a]">
                                <i class="fas fa-fire mr-2"></i>Popular
                            </a>
                            @auth
                            <a href="{{ route('community') }}?user={{ auth()->id() }}" class="flex-shrink-0 px-4 py-2 {{ request()->has('user') && request()->input('user') == auth()->id() ? 'bg-[#2a2a2a]' : 'bg-[#1a1a1a]' }} rounded-full text-sm text-white border border-[#2a2a2a] hover:bg-[#2a2a2a]">
                                <i class="fas fa-user mr-2"></i>My Posts
                            </a>
                            @endauth
                        </div>
                    </div>
                    
                    <!-- Category filter buttons -->
                    <div class="flex flex-col gap-2">
                        <h3 class="text-xs uppercase text-gray-500 font-semibold mb-1 tracking-wider px-2">Categories</h3>
                        <div class="flex overflow-x-auto gap-2 pb-2">
                            <a href="{{ route('community') }}" class="flex-shrink-0 px-4 py-2 bg-[#1a1a1a] rounded-full text-sm text-white border border-[#2a2a2a] hover:bg-[#2a2a2a]">
                                <i class="fas fa-home mr-2"></i>All
                            </a>
                            @foreach($tags as $tagItem)
                            <a href="{{ route('community.tag', $tagItem->slug) }}" 
                               class="flex-shrink-0 px-4 py-2 {{ isset($tag) && $tag == $tagItem->slug ? 'bg-[#2a2a2a]' : 'bg-[#1a1a1a]' }} rounded-full text-sm text-white border border-[#2a2a2a] hover:bg-[#2a2a2a] flex items-center">
                                @if($tagItem->slug == 'boxing')
                                    <span class="mr-2">@include('partials.boxing-icon', ['class' => 'w-4 h-4 inline-block'])</span>
                                @else
                                    <i class="fas {{ 
                                        $tagItem->slug == 'workouts' ? 'fa-dumbbell' : 
                                        ($tagItem->slug == 'muay-thai' ? 'fa-fist-raised' : 
                                        ($tagItem->slug == 'jiu-jitsu' ? 'fa-hand-rock' : 
                                        ($tagItem->slug == 'nutrition' ? 'fa-utensils' : 
                                        ($tagItem->slug == 'cardio' ? 'fa-running' : 
                                        ($tagItem->slug == 'strength-training' ? 'fa-dumbbell' : 
                                        ($tagItem->slug == 'weight-loss' ? 'fa-weight' : 
                                        ($tagItem->slug == 'community-events' ? 'fa-users' : 'fa-tag')))))))
                                    }} mr-2"></i>
                                @endif
                                <span>{{ $tagItem->name }}</span>
                            </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Create Post Modal -->
                <div x-data="{ open: false }" @open-modal.window="open = true" x-show="open" 
                     class="fixed inset-0 bg-black bg-opacity-70 z-50 flex items-center justify-center p-4"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     style="display: none;">
                    <!-- Modal Content -->
                    <div x-show="open" @click.away="open = false"
                         class="bg-[#1a1a1a] w-full max-w-xl rounded-xl shadow-2xl overflow-auto max-h-[90vh] border border-[#2a2a2a]"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 transform scale-95"
                         x-transition:enter-end="opacity-100 transform scale-100"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100 transform scale-100"
                         x-transition:leave-end="opacity-0 transform scale-95">
                        
                        <div class="p-4 border-b border-[#2a2a2a] flex justify-between items-center">
                            <h3 class="text-lg font-semibold text-white">Create New Post</h3>
                            <button @click="open = false" class="text-gray-400 hover:text-white">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        
                        <div class="p-4">
                            <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                                @csrf

                                <!-- Post Text -->
                                <div class="relative">
                                    <textarea id="postContent" name="content" rows="3" maxlength="1000"
                                            class="w-full bg-[#252525] text-white p-4 rounded-lg border border-[#3a3a3a] focus:ring-2 focus:ring-white/20 outline-none resize-none"
                                            placeholder="Share something with the community... (max 1000 characters)" required></textarea>
                                    <div class="absolute bottom-2 right-3 text-xs text-gray-400">
                                        <span id="charCount">0</span>/1000
                                    </div>
                                </div>
                                
                                <!-- Tags Selection -->
                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-gray-300 mb-2">
                                        Select Categories (optional)
                                    </label>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($tags as $tagItem)
                                        <label class="inline-flex items-center bg-[#252525] text-gray-300 px-3 py-2 rounded-full hover:bg-[#2a2a2a] cursor-pointer transition-all duration-200">
                                            <input type="checkbox" name="tags[]" value="{{ $tagItem->id }}" class="hidden">
                                            @if($tagItem->slug == 'boxing')
                                                @include('partials.boxing-icon', ['class' => 'w-4 h-4 mr-1'])
                                            @else
                                                <i class="fas {{ 
                                                    $tagItem->slug == 'workouts' ? 'fa-dumbbell' : 
                                                    ($tagItem->slug == 'muay-thai' ? 'fa-fist-raised' : 
                                                    ($tagItem->slug == 'jiu-jitsu' ? 'fa-hand-rock' : 
                                                    ($tagItem->slug == 'nutrition' ? 'fa-utensils' : 
                                                    ($tagItem->slug == 'cardio' ? 'fa-running' : 
                                                    ($tagItem->slug == 'strength-training' ? 'fa-dumbbell' : 
                                                    ($tagItem->slug == 'weight-loss' ? 'fa-weight' : 
                                                    ($tagItem->slug == 'community-events' ? 'fa-users' : 'fa-tag')))))))
                                                }} mr-1"></i>
                                            @endif
                                            {{ $tagItem->name }}
                                        </label>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Enhanced Image Upload UI -->
                                <div class="mt-4">
                                    <div class="relative">
                                        <label for="imageUpload" class="block w-full border-2 border-dashed border-[#3a3a3a] bg-[#252525] rounded-lg p-6 cursor-pointer text-center hover:border-gray-300 transition">
                                            <div class="flex flex-col items-center justify-center space-y-2">
                                                <i class="fas fa-cloud-upload-alt text-3xl text-gray-400"></i>
                                                <span class="text-sm text-gray-400">Click to upload an image (.jpg, .jpeg, .png)</span>
                                            </div>
                                            <input id="imageUpload" name="images[]" type="file" accept="image/jpeg,image/jpg,image/png" class="hidden" onchange="previewImages(this)" multiple>
                                        </label>
                                    </div>

                                    <!-- Image Preview -->
                                    <div id="imagePreview" class="grid grid-cols-2 md:grid-cols-3 gap-3 mt-4" style="display: none;">
                                        <!-- Preview images will be inserted here -->
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="flex justify-end items-center">
                                    <button type="submit"
                                            class="bg-white text-black font-semibold px-6 py-2 rounded-lg shadow-md hover:bg-gray-200 transition">
                                        Post
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Posts -->
                <div class="space-y-6">
                    @if($posts->isEmpty())
                        <div class="bg-[#1a1a1a] rounded-xl p-8 text-center">
                            @if(isset($currentUserId) && $currentUserId == auth()->id())
                                <i class="fas fa-pencil-alt text-4xl text-gray-500 mb-3"></i>
                                <h3 class="text-xl font-semibold text-white mb-2">You haven't created any posts yet</h3>
                                <p class="text-gray-400 mb-4">Start sharing your fitness journey with the community</p>
                                <button x-data @click="$dispatch('open-modal')" 
                                        class="bg-white text-black font-semibold px-5 py-2 rounded-full shadow-lg hover:bg-gray-200 transition-colors">
                                    <i class="fas fa-plus mr-2"></i>Create Your First Post
                                </button>
                            @elseif(request()->has('query'))
                                <i class="fas fa-search text-4xl text-gray-500 mb-3"></i>
                                <h3 class="text-xl font-semibold text-white mb-2">No results found</h3>
                                <p class="text-gray-400">Try a different search term or browse all posts</p>
                            @elseif(isset($tag))
                                <i class="fas fa-tag text-4xl text-gray-500 mb-3"></i>
                                <h3 class="text-xl font-semibold text-white mb-2">No posts in this category yet</h3>
                                <p class="text-gray-400">Be the first to post in this category</p>
                            @else
                                <i class="fas fa-users text-4xl text-gray-500 mb-3"></i>
                                <h3 class="text-xl font-semibold text-white mb-2">No posts yet</h3>
                                <p class="text-gray-400">Be the first to post in the community</p>
                            @endif
                        </div>
                    @endif
                    
                    @foreach ($posts as $post)
                        <div class="bg-[#1a1a1a] rounded-xl post-card shadow overflow-hidden">
                            <!-- Post Header -->
                            <div class="p-4 flex items-start space-x-3">
                                <img src="{{ $post->user->profile_image ? asset($post->user->profile_image) : asset('assets/default-user.png') }}" 
                                     alt="User" class="w-10 h-10 rounded-full object-cover">
                                <div class="flex-1 min-w-0 max-w-full break-words">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <div class="flex items-center">
                                                <h3 class="font-semibold text-white">{{ $post->user->full_name }}</h3>
                                                <span class="text-xs text-gray-300 ml-2 px-2 py-0.5 rounded-full {{ $post->user->role === 'admin' ? 'bg-red-900' : ($post->user->role === 'trainer' ? 'bg-blue-900' : 'bg-green-900') }}">
                                                    {{ ucfirst($post->user->role) }}
                                                </span>
                                            </div>
                                            <span class="text-xs text-gray-400">{{ $post->created_at->diffForHumans() }}</span>
                                        </div>
                                        
                                        @if (auth()->check() && auth()->id() === $post->user_id)
                                            <div class="relative" x-data="{ open: false }">
                                                <button @click="open = !open" class="text-gray-400 hover:text-white">
                                                    <i class="fas fa-ellipsis-h"></i>
                                                </button>
                                                <div x-show="open" @click.away="open = false" 
                                                     class="absolute right-0 mt-2 w-40 rounded-md shadow-lg bg-[#252525] border border-[#3a3a3a] z-10">
                                                    <form action="{{ route('posts.destroy', $post->id) }}" method="POST" class="delete-post-form">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-400 hover:text-red-500 flex items-center w-full p-3 text-sm">
                                                            <i class="fas fa-trash-alt mr-2"></i> Delete Post
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Post Content -->
                                    <div class="mt-2 w-full overflow-hidden">
                                        <p class="text-white post-text">{{ $post->content }}</p>
                                    </div>

                                    <!-- Post Tags -->
                                    @if($post->tags->count() > 0)
                                    <div class="mt-2 flex flex-wrap gap-1">
                                        @foreach($post->tags as $postTag)
                                        <a href="{{ route('community.tag', $postTag->slug) }}" class="inline-block px-2 py-1 bg-[#252525] hover:bg-[#353535] text-xs text-gray-300 rounded-full transition-colors">
                                            <i class="fas {{ 
                                                $postTag->slug == 'workouts' ? 'fa-dumbbell' : 
                                                ($postTag->slug == 'muay-thai' ? 'fa-fist-raised' : 
                                                ($postTag->slug == 'boxing' ? '' : 
                                                ($postTag->slug == 'jiu-jitsu' ? 'fa-hand-rock' : 
                                                ($postTag->slug == 'nutrition' ? 'fa-utensils' : 
                                                ($postTag->slug == 'cardio' ? 'fa-running' : 
                                                ($postTag->slug == 'strength-training' ? 'fa-dumbbell' : 
                                                ($postTag->slug == 'weight-loss' ? 'fa-weight' : 
                                                ($postTag->slug == 'community-events' ? 'fa-users' : 'fa-tag'))))))))
                                            }} mr-1"></i>
                                            @if($postTag->slug == 'boxing')
                                                @include('partials.boxing-icon', ['class' => 'w-4 h-4 inline-block mr-1'])
                                            @endif
                                            {{ $postTag->name }}
                                        </a>
                                        @endforeach
                                    </div>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Post Images with improved grid layout -->
                            @if ($post->images && $post->images->count())
                                <div class="mt-3 px-4 pb-4">
                                    @php
                                        $imageCount = $post->images->count();
                                        $gridClass = 'image-grid-1';
                                        if ($imageCount == 2) $gridClass = 'image-grid-2';
                                        elseif ($imageCount == 3) $gridClass = 'image-grid-3';
                                        elseif ($imageCount == 4) $gridClass = 'image-grid-4';
                                        elseif ($imageCount >= 5) $gridClass = 'image-grid-5';
                                    @endphp
                                    
                                    <div class="image-grid {{ $gridClass }}">
                                        @foreach ($post->images as $index => $image)
                                            @if ($index < 5)
                                                <div class="post-image-container {{ $imageCount > 5 && $index == 4 ? 'relative' : '' }}" style="width: 300px !important; height: 300px !important;">
                                                    <img src="{{ asset($image->path) }}" 
                                                        class="post-image"
                                                        alt="Post image"
                                                        style="width: 300px !important; height: 300px !important; object-fit: cover !important;">
                                                        
                                                    @if ($imageCount > 5 && $index == 4)
                                                        <div class="absolute inset-0 bg-black bg-opacity-60 flex items-center justify-center">
                                                            <span class="text-white text-2xl font-bold">+{{ $post->images->count() - 5 }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Post Actions -->
                            <div class="px-4 py-3 border-t border-[#2a2a2a] flex items-center space-x-6 text-gray-400">
                                @auth
                                <form method="POST" action="{{ route('posts.like', $post->id) }}" class="like-form">
                                    @csrf
                                    <button type="submit" class="vote-button hover:text-red-400 flex items-center space-x-2 transition-colors">
                                        <i class="fas fa-heart {{ $post->likes->where('user_id', auth()->id())->count() > 0 ? 'text-red-500' : '' }}"></i>
                                        <span class="like-count">{{ $post->likes->count() }}</span>
                                    </button>
                                </form>

                                <button class="vote-button hover:text-blue-400 flex items-center space-x-2 transition-colors comment-toggle">
                                    <i class="fas fa-comment"></i>
                                    <span>{{ $post->comments->count() }}</span>
                                </button>
                                @else
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-heart"></i>
                                    <span>{{ $post->likes->count() }}</span>
                                </div>
                                
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-comment"></i>
                                    <span>{{ $post->comments->count() }}</span>
                                </div>
                                
                                <a href="{{ route('login') }}" class="text-blue-400 hover:underline text-sm ml-auto">
                                    <i class="fas fa-sign-in-alt mr-1"></i>Login to interact
                                </a>
                                @endauth
                            </div>

                            <!-- Comments Section -->
                            <div class="comments-section bg-[#0f0f0f] border-t border-[#2a2a2a] p-4 space-y-4">
                                @foreach ($post->comments as $comment)
                                    <div class="flex items-start space-x-3">
                                        <img src="{{ $comment->user->profile_image ? asset($comment->user->profile_image) : asset('assets/default-user.png') }}"
                                             alt="User" class="w-8 h-8 rounded-full object-cover">
                                        <div class="flex-1 min-w-0 max-w-full break-words">
                                            <div class="bg-[#1a1a1a] p-3 rounded-lg">
                                                <div class="flex justify-between items-start">
                                                    <div>
                                                        <div class="flex items-center">
                                                            <span class="font-semibold text-white">{{ $comment->user->full_name }}</span>
                                                            <span class="text-xs text-gray-300 ml-2 px-2 py-0.5 rounded-full {{ $comment->user->role === 'admin' ? 'bg-red-900' : ($comment->user->role === 'trainer' ? 'bg-blue-900' : 'bg-green-900') }}">
                                                                {{ ucfirst($comment->user->role) }}
                                                            </span>
                                                            <span class="text-xs text-gray-500 ml-2">{{ $comment->created_at->diffForHumans() }}</span>
                                                        </div>
                                                    </div>

                                                    @if (auth()->check() && auth()->id() === $comment->user_id)
                                                        <div class="relative" x-data="{ open: false }">
                                                            <button @click="open = !open" class="text-gray-400 hover:text-white">
                                                                <i class="fas fa-ellipsis-h"></i>
                                                            </button>
                                                            <div x-show="open" @click.away="open = false" 
                                                                 class="absolute right-0 mt-2 w-40 rounded-md shadow-lg bg-[#252525] border border-[#3a3a3a] z-10">
                                                                <form action="{{ route('comments.destroy', $comment->id) }}" method="POST" 
                                                                    class="comment-delete-form">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="text-red-400 hover:text-red-500 flex items-center w-full p-3 text-sm">
                                                                        <i class="fas fa-trash-alt mr-2"></i> Delete
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                                <p class="mt-1 text-gray-200">{{ $comment->content }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                <!-- Comment Form -->
                                @auth
                                    <form action="{{ route('comments.store', $post->id) }}" method="POST" class="flex space-x-3 mt-4 comment-form">
                                        @csrf
                                        <img src="{{ auth()->user()->profile_image ? asset(auth()->user()->profile_image) : asset('assets/default-user.png') }}"
                                            alt="User" class="w-8 h-8 rounded-full object-cover">
                                        <div class="flex-1 relative">
                                            <textarea name="content" rows="1" 
                                                    class="comment-area w-full p-3 pr-12 bg-[#1a1a1a] border border-[#2a2a2a] rounded-lg text-white focus:outline-none focus:ring-1 focus:ring-white/20"
                                                    placeholder="Write a comment..." required></textarea>
                                            <button type="submit" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-white">
                                                <i class="fas fa-paper-plane"></i>
                                            </button>
                                        </div>
                                    </form>
                                @else
                                    <div class="bg-[#1a1a1a] p-3 rounded-lg text-center mt-4">
                                        <a href="{{ route('login') }}" class="text-blue-400 hover:underline">Log in</a> to leave a comment.
                                    </div>
                                @endauth
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle post deletion with SweetAlert
        const deletePostForms = document.querySelectorAll('.delete-post-form');
        deletePostForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                Swal.fire({
                    title: 'Delete Post?',
                    text: 'Are you sure you want to delete this post? This action cannot be undone.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
        
        // Character counter for post creation
        const postContent = document.getElementById('postContent');
        const charCount = document.getElementById('charCount');
        
        if (postContent && charCount) {
            postContent.addEventListener('input', function() {
                const remaining = this.value.length;
                charCount.textContent = remaining;
                
                // Change color when approaching limit
                if (remaining > 400) {
                    charCount.classList.add('text-amber-500');
                    if (remaining > 450) {
                        charCount.classList.add('text-red-500');
                        charCount.classList.remove('text-amber-500');
                    }
                } else {
                    charCount.classList.remove('text-amber-500', 'text-red-500');
                }
            });
        }
        
        // Tag selection styling
        document.querySelectorAll('input[name="tags[]"]').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const label = this.closest('label');
                if (this.checked) {
                    label.classList.add('bg-blue-900', 'text-white');
                    label.classList.remove('bg-[#252525]', 'text-gray-300');
                } else {
                    label.classList.remove('bg-blue-900', 'text-white');
                    label.classList.add('bg-[#252525]', 'text-gray-300');
                }
            });
        });
        
        // Make sure CSRF token is set for all AJAX requests
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        console.log('CSRF Token loaded:', csrfToken ? 'Yes' : 'No');
        
        // Set CSRF token in headers for all AJAX requests
        if (csrfToken) {
            // Set up jQuery AJAX if jQuery is available
            if (window.jQuery) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
            }
        }
        
        // Clear search input and button visibility
        const searchInput = document.querySelector('input[name="query"]');
        const clearSearchButton = document.getElementById('clearSearch');

        if (searchInput && clearSearchButton) {
            searchInput.addEventListener('input', () => {
                clearSearchButton.style.display = searchInput.value.length > 0 ? 'inline-block' : 'none';
            });

            clearSearchButton.addEventListener('click', () => {
                searchInput.value = '';
                clearSearchButton.style.display = 'none';
                searchInput.focus();
            });
        }

        // Simplified image preview function for handling a single image
        window.previewImages = function(input) {
            const preview = document.getElementById('imagePreview');
            preview.innerHTML = '';
            
            if (input.files && input.files.length > 0) {
                preview.style.display = 'grid';
                
                const file = input.files[0];
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    const imgContainer = document.createElement('div');
                    imgContainer.className = 'relative w-full h-40 rounded overflow-hidden border border-[#3a3a3a]';
                    
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'absolute w-full h-full object-cover rounded';
                    
                    const deleteButton = document.createElement('button');
                    deleteButton.type = 'button';
                    deleteButton.className = 'absolute top-2 right-2 bg-red-600 text-white text-xs p-1 rounded-full w-6 h-6 flex items-center justify-center';
                    deleteButton.innerHTML = '&times;';
                    deleteButton.onclick = function() {
                        imgContainer.remove();
                        input.value = '';
                        preview.style.display = 'none';
                    };
                    
                    imgContainer.appendChild(img);
                    imgContainer.appendChild(deleteButton);
                    preview.appendChild(imgContainer);
                };
                
                reader.readAsDataURL(file);
            } else {
                preview.style.display = 'none';
            }
        };
        
        // AJAX for likes
        document.querySelectorAll('.like-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                console.log('Like form submitted:', this.action);
                
                const button = this.querySelector('button');
                const heartIcon = button.querySelector('i');
                const likeCount = button.querySelector('.like-count');
                
                const formData = new FormData();
                formData.append('_token', csrfToken);
                
                fetch(this.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response error: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Like response:', data);
                    if (data.success) {
                        // Toggle heart icon
                        if (data.action === 'liked') {
                            heartIcon.classList.add('text-red-500');
                        } else {
                            heartIcon.classList.remove('text-red-500');
                        }
                        
                        // Update like count
                        likeCount.textContent = data.likes_count;
                        
                        // Add animation
                        button.classList.add('scale-125');
                        setTimeout(() => {
                            button.classList.remove('scale-125');
                        }, 200);
                    }
                })
                .catch(error => {
                    console.error('Error with like:', error);
                    alert('There was a problem liking the post. Please try again.');
                });
            });
        });
        
        // Auto-resize comment textarea
        document.querySelectorAll('.comment-area').forEach(textarea => {
            textarea.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
            });
        });
        
        // AJAX for comment submission
        document.querySelectorAll('.comment-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                console.log('Comment form submitted:', this.action);
                
                const formData = new FormData(this);
                const textarea = this.querySelector('textarea');
                const commentsSection = this.closest('.comments-section');
                
                fetch(this.getAttribute('action'), {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response error: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Comment response:', data);
                    if (data.success) {
                        // Add the new comment to the DOM
                        const newComment = document.createElement('div');
                        newComment.className = 'flex items-start space-x-3';
                        newComment.innerHTML = data.html;
                        
                        // Insert before the form
                        commentsSection.insertBefore(newComment, this);
                        
                        // Clear the textarea
                        textarea.value = '';
                        textarea.style.height = 'auto';
                        
                        // Update comment count
                        const commentCount = this.closest('.post-card').querySelector('.vote-button:nth-child(2) span');
                        commentCount.textContent = parseInt(commentCount.textContent) + 1;
                        
                        // Add event listeners to the new delete buttons
                        const deleteForm = newComment.querySelector('.comment-delete-form');
                        if (deleteForm) {
                            attachCommentDeleteListeners([deleteForm]);
                        }
                    }
                })
                .catch(error => {
                    console.error('Error with comment:', error);
                    alert('There was a problem posting your comment. Please try again.');
                });
            });
        });
        
        // Function to attach delete event listeners to comment delete forms
        function attachCommentDeleteListeners(forms) {
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    console.log('Delete comment form submitted:', this.action);
                    
                    Swal.fire({
                        title: 'Delete Comment?',
                        text: 'Are you sure you want to delete this comment? This action cannot be undone.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, delete it!',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const formData = new FormData(this);
                            formData.append('_method', 'DELETE');
                            
                            fetch(this.getAttribute('action'), {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': csrfToken
                                }
                            })
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error('Network response error: ' + response.status);
                                }
                                return response.json();
                            })
                            .then(data => {
                                console.log('Delete comment response:', data);
                                if (data.success) {
                                    // Remove the comment element from DOM
                                    const commentElement = this.closest('.flex.items-start.space-x-3');
                                    if (commentElement) {
                                        commentElement.remove();
                                        
                                        // Update comment count
                                        const postCard = this.closest('.post-card');
                                        const commentCount = postCard.querySelector('.vote-button:nth-child(2) span');
                                        const currentCount = parseInt(commentCount.textContent);
                                        commentCount.textContent = currentCount - 1;
                                        
                                        // Show success message
                                        Swal.fire({
                                            title: 'Deleted!',
                                            text: 'Your comment has been deleted.',
                                            icon: 'success',
                                            timer: 1500,
                                            showConfirmButton: false
                                        });
                                    }
                                }
                            })
                            .catch(error => {
                                console.error('Error deleting comment:', error);
                                // Still proceed with removing the comment from DOM
                                const commentElement = this.closest('.flex.items-start.space-x-3');
                                if (commentElement) {
                                    commentElement.remove();
                                    
                                    // Update comment count
                                    const postCard = this.closest('.post-card');
                                    const commentCount = postCard.querySelector('.vote-button:nth-child(2) span');
                                    const currentCount = parseInt(commentCount.textContent);
                                    commentCount.textContent = currentCount - 1;
                                    
                                    // Show notification that it was deleted despite error
                                    Swal.fire({
                                        title: 'Comment Deleted',
                                        text: 'Your comment has been deleted, but there was a server error. The changes may not persist after refresh.',
                                        icon: 'warning',
                                        timer: 2000,
                                        showConfirmButton: false
                                    });
                                } else {
                                    Swal.fire({
                                        title: 'Error!',
                                        text: 'There was a problem deleting the comment. Please try again.',
                                        icon: 'error'
                                    });
                                }
                            });
                        }
                    });
                });
            });
        }
        
        // Attach delete listeners to existing comment delete forms
        attachCommentDeleteListeners(document.querySelectorAll('.comment-delete-form'));
        
        // Toggle comments section
        document.querySelectorAll('.comment-toggle').forEach(button => {
            button.addEventListener('click', function() {
                const commentsSection = this.closest('.post-card').querySelector('.comments-section');
                commentsSection.classList.toggle('hidden');
                
                // Focus on comment input if section is shown
                if (!commentsSection.classList.contains('hidden')) {
                    const textarea = commentsSection.querySelector('textarea');
                    if (textarea) textarea.focus();
                }
            });
        });
        

    });
</script>
@endsection