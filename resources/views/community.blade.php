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

    /* Admin Controls Styles */
    .bulk-delete-checkbox {
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .bulk-delete-mode .post-card {
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .bulk-delete-mode .post-card:hover {
        border-color: rgba(239, 68, 68, 0.5);
    }

    #bulk-delete-fab {
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s ease;
    }

    #bulk-delete-fab:hover {
        transform: scale(1.05);
    }
    
    /* Reported content styles */
    .reported {
        position: relative;
    }
    
    .reported-count.animate-pulse {
        animation: pulse 1.5s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        background-color: #ef4444;
    }
    
    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.7;
        }
    }
    
    .report-item {
        transition: all 0.3s ease;
    }
    
    /* Moderation mode styles */
    .moderation-highlighted {
        z-index: 5;
        transition: all 0.3s ease;
    }
    
    .moderation-dimmed {
        transition: all 0.3s ease;
    }
    
    .moderation-label {
        font-weight: 600;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }
    
    .unhide-btn {
        transition: all 0.2s ease;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }
    
    .unhide-btn:hover {
        background-color: #2563eb;
        transform: translateY(-1px);
    }
    
    .hidden-overlay {
        transition: all 0.3s ease;
    }
    
    .hidden-by-admin {
        position: relative;
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

                <!-- Admin Control Panel - Only visible for admins -->
                @if(auth()->check() && auth()->user()->role === 'admin')
                <div class="mb-6 bg-red-900 rounded-lg p-4 border border-red-700 shadow-lg">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <div>
                            <h2 class="text-xl font-bold text-white flex items-center">
                                <i class="fas fa-shield-alt mr-2"></i> Admin Control Panel
                            </h2>
                            <p class="text-red-200 text-sm">Manage community content and moderate posts</p>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <button id="flaggedContentBtn" class="px-3 py-2 bg-red-800 hover:bg-red-700 text-white rounded flex items-center text-sm">
                                <i class="fas fa-shield-alt mr-2"></i> Admin Flagged 
                                <span class="ml-2 bg-red-600 text-xs px-2 py-0.5 rounded-full">0</span>
                            </button>
                            <button id="reportedContentBtn" class="px-3 py-2 bg-red-800 hover:bg-red-700 text-white rounded flex items-center text-sm">
                                <i class="fas fa-exclamation-circle mr-2"></i> User Reports 
                                <span class="ml-2 bg-red-600 text-xs px-2 py-0.5 rounded-full reported-count">0</span>
                            </button>
                            <button id="bulkDeleteBtn" class="px-3 py-2 bg-red-800 hover:bg-red-700 text-white rounded flex items-center text-sm">
                                <i class="fas fa-trash-alt mr-2"></i> Bulk Delete
                            </button>
                            <button id="toggleModerateMode" class="px-3 py-2 bg-red-800 hover:bg-red-700 text-white rounded flex items-center text-sm">
                                <i class="fas fa-eye mr-2"></i> Moderation Mode
                            </button>
                        </div>
                    </div>
                </div>
                @endif

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
                        <div class="bg-[#1a1a1a] rounded-xl post-card shadow overflow-hidden" 
                             data-post-id="{{ $post->id }}" 
                             data-user-id="{{ $post->user_id }}">
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
                                        
                                        <div class="flex items-center">
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
                                            
                                            <!-- Admin Controls - Only visible to admins -->
                                            @if(auth()->check() && auth()->user()->role === 'admin')
                                                <div class="relative ml-2" x-data="{ adminOpen: false }">
                                                    <button @click="adminOpen = !adminOpen" class="text-red-400 hover:text-red-300 flex items-center">
                                                        <i class="fas fa-shield-alt"></i>
                                                    </button>
                                                    <div x-show="adminOpen" @click.away="adminOpen = false" 
                                                         class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-red-900 border border-red-700 z-20">
                                                        <form action="{{ route('posts.destroy', $post->id) }}" method="POST" class="admin-delete-post-form">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-white hover:bg-red-800 flex items-center w-full p-3 text-sm">
                                                                <i class="fas fa-trash-alt mr-2"></i> Delete Post
                                                            </button>
                                                        </form>
                                                        <button class="admin-flag-btn text-white hover:bg-red-800 flex items-center w-full p-3 text-sm"
                                                                data-post-id="{{ $post->id }}" 
                                                                data-post-author="{{ $post->user->full_name }}" 
                                                                data-post-content="{{ Str::limit($post->content, 100) }}">
                                                            <i class="fas fa-shield-alt mr-2"></i> Flag Content
                                                        </button>
                                                        <button class="hide-post-btn text-white hover:bg-red-800 flex items-center w-full p-3 text-sm"
                                                                data-post-id="{{ $post->id }}">
                                                            <i class="fas fa-eye-slash mr-2"></i> Hide Post
                                                        </button>
                                                    </div>
                                                </div>
                                            @endif
                                            
                                            <!-- Report button for non-admin users -->
                                            @if(auth()->check() && auth()->user()->role !== 'admin' && auth()->id() !== $post->user_id)
                                                <div class="ml-2">
                                                    <button class="text-red-400 hover:text-yellow-300 report-post-btn" data-post-id="{{ $post->id }}">
                                                        <i class="fas fa-flag"></i>
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
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
                                    <div class="flex items-start space-x-3 comment" data-comment-id="{{ $comment->id }}" data-user-id="{{ $comment->user_id }}">
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

                                                    <div class="flex items-center">
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
                                                                    <button class="admin-flag-comment-btn text-white hover:bg-red-800 flex items-center w-full p-3 text-sm"
                                                                            data-comment-id="{{ $comment->id }}" 
                                                                            data-comment-author="{{ $comment->user->full_name }}" 
                                                                            data-comment-content="{{ Str::limit($comment->content, 100) }}">
                                                                        <i class="fas fa-shield-alt mr-2"></i> Flag Content
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        
                                                        <!-- Admin Controls for Comments - Only visible to admins -->
                                                        @if (auth()->check() && auth()->user()->role === 'admin')
                                                            <div class="relative ml-2" x-data="{ adminCommentOpen: false }">
                                                                <button @click="adminCommentOpen = !adminCommentOpen" class="text-red-400 hover:text-red-300 flex items-center">
                                                                    <i class="fas fa-shield-alt"></i>
                                                                </button>
                                                                <div x-show="adminCommentOpen" @click.away="adminCommentOpen = false" 
                                                                    class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-red-900 border border-red-700 z-20">
                                                                    <form action="{{ route('comments.destroy', $comment->id) }}" method="POST" 
                                                                        class="admin-comment-delete-form">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" class="text-white hover:bg-red-800 flex items-center w-full p-3 text-sm">
                                                                            <i class="fas fa-trash-alt mr-2"></i> Delete Comment
                                                                        </button>
                                                                    </form>
                                                                    <button class="admin-flag-comment-btn text-white hover:bg-red-800 flex items-center w-full p-3 text-sm"
                                                                            data-comment-id="{{ $comment->id }}" 
                                                                            data-comment-author="{{ $comment->user->full_name }}" 
                                                                            data-comment-content="{{ Str::limit($comment->content, 100) }}">
                                                                        <i class="fas fa-shield-alt mr-2"></i> Flag Content
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                                <p class="mt-1 text-gray-200">{{ $comment->content }}</p>
                                                
                                                <!-- Report button for comments - only visible to non-admin users -->
                                                @if(auth()->check() && auth()->user()->role !== 'admin' && auth()->id() !== $comment->user_id)
                                                <div class="mt-1 flex items-center">
                                                    <button class="text-xs text-gray-400 hover:text-yellow-400 report-comment-btn flex items-center gap-1" data-comment-id="{{ $comment->id }}">
                                                        <i class="fas fa-flag"></i> Report
                                                    </button>
                                                </div>
                                                @endif
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
        
        // Check for moderation notices on page load
        checkForModerationNotices();
        
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

        // Handle post form submission
        const postForm = document.querySelector('form[action=\"{{ route('posts.store') }}\"]');
        if (postForm) {
            postForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                const textarea = this.querySelector('textarea[name="content"]');
                const imageInput = this.querySelector('input[name="images[]"]');
                const imagePreview = document.getElementById('imagePreview');
                
                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    } else {
                        // Clear form inputs
                        textarea.value = '';
                        if (imageInput) imageInput.value = '';
                        if (imagePreview) {
                            imagePreview.innerHTML = '';
                            imagePreview.style.display = 'none';
                        }
                        
                        // Reset character count
                        const charCount = document.getElementById('charCount');
                        if (charCount) charCount.textContent = '0';
                        
                        // Show content screening errors
                        Swal.fire({
                            title: '<span class="text-xl font-bold text-red-500">Content Screening Failed</span>',
                            html: `
                                <div class="bg-red-900/20 p-4 rounded-lg border border-red-500/50 mb-4">
                                    <i class="fas fa-exclamation-triangle text-red-500 text-2xl mb-2"></i>
                                    <p class="text-white">Your post contains inappropriate content that violates our community guidelines.</p>
                                </div>
                                <div class="text-left text-gray-300 mt-2">
                                    <p class="font-semibold mb-1">Issues found:</p>
                                    <ul class="list-disc pl-5 text-red-400">
                                        ${Array.isArray(data.errors) ? data.errors.map(error => `<li>${error}</li>`).join('') : `<li>${data.errors}</li>`}
                                    </ul>
                                </div>
                                <p class="mt-4 text-sm text-gray-400">Please revise your content and try again.</p>
                            `,
                            icon: 'error',
                            background: '#1a1a1a',
                            color: '#FFFFFF',
                            confirmButtonText: 'I Understand',
                            confirmButtonColor: '#EF4444'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        title: 'Error',
                        text: 'An error occurred while submitting your post.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                });
            });
        }
        
        // Handle comment form submission
        document.querySelectorAll('.comment-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                const textarea = this.querySelector('textarea');
                const commentsSection = this.closest('.comments-section');
                
                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Clear the textarea
                        textarea.value = '';
                        textarea.style.height = 'auto';
                        
                        // Add the new comment to the DOM
                        const newComment = document.createElement('div');
                        newComment.className = 'flex items-start space-x-3';
                        newComment.innerHTML = data.html;
                        
                        // Insert before the form
                        commentsSection.insertBefore(newComment, this);
                        
                        // Update comment count
                        const commentCount = this.closest('.post-card').querySelector('.vote-button:nth-child(2) span');
                        commentCount.textContent = parseInt(commentCount.textContent) + 1;
                    } else {
                        // Show content screening errors
                        Swal.fire({
                            title: '<span class="text-xl font-bold text-red-500">Content Screening Failed</span>',
                            html: `
                                <div class="bg-red-900/20 p-4 rounded-lg border border-red-500/50 mb-4">
                                    <i class="fas fa-exclamation-triangle text-red-500 text-2xl mb-2"></i>
                                    <p class="text-white">Your comment contains inappropriate content that violates our community guidelines.</p>
                                </div>
                                <div class="text-left text-gray-300 mt-2">
                                    <p class="font-semibold mb-1">Issues found:</p>
                                    <ul class="list-disc pl-5 text-red-400">
                                        ${Array.isArray(data.errors) ? data.errors.map(error => `<li>${error}</li>`).join('') : `<li>${data.errors}</li>`}
                                    </ul>
                                </div>
                                <p class="mt-4 text-sm text-gray-400">Please revise your comment and try again.</p>
                            `,
                            icon: 'error',
                            background: '#1a1a1a',
                            color: '#FFFFFF',
                            confirmButtonText: 'I Understand',
                            confirmButtonColor: '#EF4444'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        title: 'Error',
                        text: 'An error occurred while submitting your comment.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                });
            });
        });
        
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
        
        // Admin functionality
        if (document.querySelector('.admin-delete-post-form')) {
            // Handle admin post deletion with enhanced SweetAlert
            document.querySelectorAll('.admin-delete-post-form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    Swal.fire({
                        title: '<span class="text-xl font-bold">Admin Action: Delete Post</span>',
                        html: `
                            <div class="bg-red-900/20 p-4 rounded-lg border border-red-500/50 mb-4">
                                <p class="text-white">You are about to delete this post as an administrator.</p>
                                <p class="text-white mt-2">This action cannot be undone.</p>
                            </div>
                        `,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, delete it!',
                        cancelButtonText: 'Cancel',
                        background: '#1a1a1a',
                        color: '#FFFFFF'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
            
            // Handle admin comment deletion
            document.querySelectorAll('.admin-comment-delete-form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    Swal.fire({
                        title: '<span class="text-xl font-bold">Admin Action: Delete Comment</span>',
                        html: `
                            <div class="bg-red-900/20 p-4 rounded-lg border border-red-500/50 mb-4">
                                <p class="text-white">You are about to delete this comment as an administrator.</p>
                                <p class="text-white mt-2">This action cannot be undone.</p>
                            </div>
                        `,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, delete it!',
                        cancelButtonText: 'Cancel',
                        background: '#1a1a1a',
                        color: '#FFFFFF'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
            
            // Flag inappropriate post content
            document.querySelectorAll('.flag-inappropriate-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const postId = this.dataset.postId;
                    
                    Swal.fire({
                        title: '<span class="text-xl font-bold">Flag Inappropriate Content</span>',
                        html: `
                            <div class="bg-yellow-900/20 p-4 rounded-lg border border-yellow-500/50 mb-4">
                                <p class="text-white">Please select the reason for flagging this post:</p>
                            </div>
                            <div class="text-left mt-4">
                                <select id="flag-reason" class="w-full p-2 bg-gray-700 text-white rounded border border-gray-600">
                                    <option value="inappropriate">Inappropriate Language</option>
                                    <option value="spam">Spam</option>
                                    <option value="harassment">Harassment</option>
                                    <option value="violence">Violence</option>
                                    <option value="other">Other</option>
                                </select>
                                <textarea id="flag-notes" class="w-full mt-3 p-2 bg-gray-700 text-white rounded border border-gray-600" placeholder="Additional notes (optional)" rows="3"></textarea>
                            </div>
                        `,
                        showCancelButton: true,
                        confirmButtonText: 'Flag Content',
                        cancelButtonText: 'Cancel',
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        background: '#1a1a1a',
                        color: '#FFFFFF',
                        preConfirm: () => {
                            return {
                                reason: document.getElementById('flag-reason').value,
                                notes: document.getElementById('flag-notes').value
                            };
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Here you would make an AJAX call to your backend to flag the post
                            console.log(`Flagging post ${postId} for ${result.value.reason}: ${result.value.notes}`);
                            
                            // Show success notification
                            Swal.fire({
                                title: 'Content Flagged',
                                text: 'This post has been flagged for review.',
                                icon: 'success',
                                background: '#1a1a1a',
                                color: '#FFFFFF',
                                confirmButtonColor: '#3085d6',
                                timer: 2000,
                                timerProgressBar: true
                            });
                        }
                    });
                });
            });
            
            // Flag inappropriate comment content
            document.querySelectorAll('.flag-inappropriate-comment-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const commentId = this.dataset.commentId;
                    
                    Swal.fire({
                        title: '<span class="text-xl font-bold">Flag Inappropriate Comment</span>',
                        html: `
                            <div class="bg-yellow-900/20 p-4 rounded-lg border border-yellow-500/50 mb-4">
                                <p class="text-white">Please select the reason for flagging this comment:</p>
                            </div>
                            <div class="text-left mt-4">
                                <select id="flag-comment-reason" class="w-full p-2 bg-gray-700 text-white rounded border border-gray-600">
                                    <option value="inappropriate">Inappropriate Language</option>
                                    <option value="spam">Spam</option>
                                    <option value="harassment">Harassment</option>
                                    <option value="violence">Violence</option>
                                    <option value="other">Other</option>
                                </select>
                                <textarea id="flag-comment-notes" class="w-full mt-3 p-2 bg-gray-700 text-white rounded border border-gray-600" placeholder="Additional notes (optional)" rows="3"></textarea>
                            </div>
                        `,
                        showCancelButton: true,
                        confirmButtonText: 'Flag Comment',
                        cancelButtonText: 'Cancel',
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        background: '#1a1a1a',
                        color: '#FFFFFF',
                        preConfirm: () => {
                            return {
                                reason: document.getElementById('flag-comment-reason').value,
                                notes: document.getElementById('flag-comment-notes').value
                            };
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Here you would make an AJAX call to your backend to flag the comment
                            console.log(`Flagging comment ${commentId} for ${result.value.reason}: ${result.value.notes}`);
                            
                            // Show success notification
                            Swal.fire({
                                title: 'Comment Flagged',
                                text: 'This comment has been flagged for review.',
                                icon: 'success',
                                background: '#1a1a1a',
                                color: '#FFFFFF',
                                confirmButtonColor: '#3085d6',
                                timer: 2000,
                                timerProgressBar: true
                            });
                        }
                    });
                });
            });
            
            // Hide post functionality
            document.querySelectorAll('.hide-post-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const postId = this.dataset.postId;
                    const postCard = this.closest('.post-card');
                    
                    Swal.fire({
                        title: '<span class="text-xl font-bold">Hide Post</span>',
                        html: `
                            <div class="bg-gray-800 p-4 rounded-lg border border-gray-700 mb-4">
                                <p class="text-white">This will hide the post from community view.</p>
                                <p class="text-white mt-2">Users won't be able to see this post until it's unhidden.</p>
                            </div>
                        `,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Hide Post',
                        cancelButtonText: 'Cancel',
                        confirmButtonColor: '#4B5563',
                        cancelButtonColor: '#3085d6',
                        background: '#1a1a1a',
                        color: '#FFFFFF'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Store hidden post in localStorage
                            const hiddenPosts = JSON.parse(localStorage.getItem('hiddenPosts') || '[]');
                            const authorId = postCard ? postCard.dataset.userId : null;
                            
                            // Check if post is already hidden
                            if (!hiddenPosts.some(item => item.postId === postId)) {
                                hiddenPosts.push({
                                    postId: postId,
                                    authorId: authorId,
                                    hiddenAt: new Date().toISOString(),
                                    hiddenBy: '{{ auth()->check() ? auth()->user()->full_name : "Unknown" }}'
                                });
                                localStorage.setItem('hiddenPosts', JSON.stringify(hiddenPosts));
                            }
                            
                            // Visual feedback - add a hidden overlay
                            if (postCard) {
                                hidePostVisually(postCard);
                            }
                            
                            // Show success notification
                            Swal.fire({
                                title: 'Post Hidden',
                                text: 'The post has been hidden from community view.',
                                icon: 'success',
                                background: '#1a1a1a',
                                color: '#FFFFFF',
                                confirmButtonColor: '#3085d6',
                                timer: 2000,
                                timerProgressBar: true
                            });
                        }
                    });
                });
            });
            
            // Function to visually hide a post
            function hidePostVisually(postCard) {
                postCard.style.opacity = '0.5';
                postCard.style.position = 'relative';
                postCard.classList.add('hidden-by-admin');
                
                // Only add overlay if it doesn't already exist
                if (!postCard.querySelector('.hidden-overlay')) {
                    const overlay = document.createElement('div');
                    overlay.className = 'absolute inset-0 flex items-center justify-center bg-black bg-opacity-50 z-10 hidden-overlay';
                    overlay.innerHTML = '<span class="px-4 py-2 bg-gray-800 text-white rounded">Hidden by Admin</span>';
                    postCard.appendChild(overlay);
                }
            }
            
            // Apply hidden state to posts on page load
            function applyHiddenPostsState() {
                const hiddenPosts = JSON.parse(localStorage.getItem('hiddenPosts') || '[]');
                if (hiddenPosts.length === 0) return;
                
                document.querySelectorAll('.post-card').forEach(postCard => {
                    const postId = postCard.dataset.postId;
                    if (hiddenPosts.some(item => item.postId === postId)) {
                        hidePostVisually(postCard);
                    }
                });
            }
            
            // Call on page load
            applyHiddenPostsState();
            
            // Admin Control Panel functionality
            const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
            const toggleModerateBtn = document.getElementById('toggleModerateMode');
            const flaggedContentBtn = document.getElementById('flaggedContentBtn');
            
            if (bulkDeleteBtn) {
                bulkDeleteBtn.addEventListener('click', function() {
                    // Toggle bulk delete mode
                    document.body.classList.toggle('bulk-delete-mode');
                    
                    if (document.body.classList.contains('bulk-delete-mode')) {
                        // Enable bulk delete mode
                        Swal.fire({
                            title: 'Bulk Delete Mode Activated',
                            text: 'Click on posts to select them for deletion.',
                            icon: 'info',
                            background: '#1a1a1a',
                            color: '#FFFFFF',
                            confirmButtonColor: '#3085d6',
                            timer: 3000,
                            timerProgressBar: true
                        });
                        
                        // Change button style to indicate active mode
                        bulkDeleteBtn.classList.add('bg-red-600');
                        bulkDeleteBtn.innerHTML = '<i class="fas fa-times mr-2"></i> Cancel Bulk Delete';
                        
                        // Add checkboxes to all posts
                        document.querySelectorAll('.post-card').forEach(post => {
                            const checkbox = document.createElement('div');
                            checkbox.className = 'absolute top-2 left-2 w-6 h-6 rounded border-2 border-red-500 bg-transparent z-20 bulk-delete-checkbox';
                            checkbox.setAttribute('data-selected', 'false');
                            
                            post.style.position = 'relative';
                            post.appendChild(checkbox);
                            
                            post.addEventListener('click', function(e) {
                                // Only handle clicks if we're in bulk delete mode
                                if (!document.body.classList.contains('bulk-delete-mode')) return;
                                
                                // Don't trigger for clicks on buttons, links, etc.
                                if (e.target.tagName === 'BUTTON' || e.target.tagName === 'A' || e.target.closest('button') || e.target.closest('a')) return;
                                
                                const cb = this.querySelector('.bulk-delete-checkbox');
                                if (cb) {
                                    const isSelected = cb.getAttribute('data-selected') === 'true';
                                    cb.setAttribute('data-selected', !isSelected);
                                    
                                    if (!isSelected) {
                                        cb.classList.add('bg-red-500');
                                        cb.innerHTML = '<i class="fas fa-check text-white text-xs"></i>';
                                    } else {
                                        cb.classList.remove('bg-red-500');
                                        cb.innerHTML = '';
                                    }
                                }
                            });
                        });
                        
                        // Add a floating action button to delete selected
                        const fab = document.createElement('div');
                        fab.className = 'fixed bottom-6 right-6 bg-red-600 text-white rounded-full p-4 shadow-lg z-50 cursor-pointer';
                        fab.innerHTML = '<i class="fas fa-trash"></i> Delete Selected';
                        fab.id = 'bulk-delete-fab';
                        
                        fab.addEventListener('click', function() {
                            const selectedPosts = document.querySelectorAll('.bulk-delete-checkbox[data-selected="true"]');
                            
                            if (selectedPosts.length === 0) {
                                Swal.fire({
                                    title: 'No Posts Selected',
                                    text: 'Please select at least one post to delete.',
                                    icon: 'warning',
                                    background: '#1a1a1a',
                                    color: '#FFFFFF',
                                    confirmButtonColor: '#3085d6'
                                });
                                return;
                            }
                            
                            Swal.fire({
                                title: 'Confirm Bulk Delete',
                                text: `Are you sure you want to delete ${selectedPosts.length} selected posts?`,
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#d33',
                                cancelButtonColor: '#3085d6',
                                confirmButtonText: 'Yes, delete them!',
                                background: '#1a1a1a',
                                color: '#FFFFFF'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    // Here you would make an AJAX call to delete the posts
                                    console.log(`Deleting ${selectedPosts.length} posts`);
                                    
                                    // Remove the posts from the UI
                                    selectedPosts.forEach(checkbox => {
                                        const post = checkbox.closest('.post-card');
                                        if (post) post.remove();
                                    });
                                    
                                    // Exit bulk delete mode
                                    exitBulkDeleteMode();
                                    
                                    // Show success notification
                                    Swal.fire({
                                        title: 'Posts Deleted',
                                        text: `${selectedPosts.length} posts have been deleted.`,
                                        icon: 'success',
                                        background: '#1a1a1a',
                                        color: '#FFFFFF',
                                        confirmButtonColor: '#3085d6',
                                        timer: 2000,
                                        timerProgressBar: true
                                    });
                                }
                            });
                        });
                        
                        document.body.appendChild(fab);
                    } else {
                        // Exit bulk delete mode
                        exitBulkDeleteMode();
                    }
                });
                
                function exitBulkDeleteMode() {
                    document.body.classList.remove('bulk-delete-mode');
                    bulkDeleteBtn.classList.remove('bg-red-600');
                    bulkDeleteBtn.innerHTML = '<i class="fas fa-trash-alt mr-2"></i> Bulk Delete';
                    
                    // Remove all checkboxes
                    document.querySelectorAll('.bulk-delete-checkbox').forEach(cb => cb.remove());
                    
                    // Remove the floating action button
                    const fab = document.getElementById('bulk-delete-fab');
                    if (fab) fab.remove();
                }
            }
            
            // Moderation mode toggle
            if (toggleModerateBtn) {
                toggleModerateBtn.addEventListener('click', function() {
                    document.body.classList.toggle('moderate-mode');
                    
                    if (document.body.classList.contains('moderate-mode')) {
                        // Enable moderation mode
                        Swal.fire({
                            title: 'Moderation Mode Activated',
                            text: 'You can now see all flagged and hidden content. Regular posts are dimmed to help you focus on problematic content.',
                            icon: 'info',
                            background: '#1a1a1a',
                            color: '#FFFFFF',
                            confirmButtonColor: '#3085d6',
                            timer: 3000,
                            timerProgressBar: true
                        });
                        
                        toggleModerateBtn.classList.add('bg-red-600');
                        toggleModerateBtn.innerHTML = '<i class="fas fa-eye-slash mr-2"></i> Exit Moderation Mode';
                        
                        // Apply moderation mode styles
                        applyModerationModeStyles(true);
                    } else {
                        // Disable moderation mode
                        toggleModerateBtn.classList.remove('bg-red-600');
                        toggleModerateBtn.innerHTML = '<i class="fas fa-eye mr-2"></i> Moderation Mode';
                        
                        // Remove moderation mode styles
                        applyModerationModeStyles(false);
                    }
                });
            }
            
            // Function to apply or remove moderation mode styles
            function applyModerationModeStyles(enable) {
                // Get all posts
                const allPosts = document.querySelectorAll('.post-card');
                const allComments = document.querySelectorAll('.comment');
                
                // Get problematic content
                const hiddenPosts = JSON.parse(localStorage.getItem('hiddenPosts') || '[]');
                const flaggedContent = JSON.parse(localStorage.getItem('adminFlaggedContent') || '[]');
                const reportedContent = JSON.parse(localStorage.getItem('communityReports') || '[]');
                const removedContent = JSON.parse(localStorage.getItem('removedContent') || '[]');
                
                if (enable) {
                    // First, dim all content
                    allPosts.forEach(post => {
                        post.classList.add('moderation-dimmed');
                        post.style.opacity = '0.4';
                        post.style.transition = 'opacity 0.3s ease';
                    });
                    
                    allComments.forEach(comment => {
                        comment.classList.add('moderation-dimmed');
                        comment.style.opacity = '0.4';
                        comment.style.transition = 'opacity 0.3s ease';
                    });
                    
                    // Then highlight problematic content
                    allPosts.forEach(post => {
                        const postId = post.dataset.postId;
                        
                        // Check if post is problematic
                        const isHidden = hiddenPosts.some(item => item.postId === postId);
                        const isFlagged = flaggedContent.some(item => item.type === 'post' && item.contentId === postId);
                        const isReported = reportedContent.some(item => item.type === 'post' && item.postId === postId);
                        const isRemoved = removedContent.some(item => item.contentType === 'post' && item.contentId === postId);
                        
                        if (isHidden || isFlagged || isReported || isRemoved || post.classList.contains('reported') || post.classList.contains('admin-flagged')) {
                            post.classList.add('moderation-highlighted');
                            post.style.opacity = '1';
                            post.style.boxShadow = '0 0 0 2px #ef4444';
                            
                            // Add a label if not already present
                            if (!post.querySelector('.moderation-label')) {
                                const labelDiv = document.createElement('div');
                                labelDiv.className = 'moderation-label absolute top-0 right-0 bg-red-600 text-white text-xs px-2 py-1 m-2 rounded-full z-20';
                                
                                let labelText = '';
                                if (isHidden) labelText += '🚫 ';
                                if (isFlagged) labelText += '🚩 ';
                                if (isReported) labelText += '⚠️ ';
                                if (isRemoved) labelText += '❌ ';
                                
                                labelDiv.textContent = labelText + 'Flagged Content';
                                
                                // Ensure post has relative positioning
                                if (post.style.position !== 'relative') {
                                    post.style.position = 'relative';
                                }
                                
                                post.appendChild(labelDiv);
                            }
                            
                            // Add unhide button for hidden posts
                            if (isHidden && !post.querySelector('.unhide-btn')) {
                                const unhideBtn = document.createElement('button');
                                unhideBtn.className = 'unhide-btn absolute bottom-2 right-2 bg-blue-600 text-white text-xs px-3 py-1 rounded-full z-20';
                                unhideBtn.innerHTML = '<i class="fas fa-eye mr-1"></i> Unhide Post';
                                unhideBtn.dataset.postId = postId;
                                
                                unhideBtn.addEventListener('click', function(e) {
                                    e.stopPropagation();
                                    unhidePost(postId, post);
                                });
                                
                                post.appendChild(unhideBtn);
                            }
                        }
                    });
                    
                    // Highlight problematic comments
                    allComments.forEach(comment => {
                        const commentId = comment.dataset.commentId;
                        
                        // Check if comment is problematic
                        const isFlagged = flaggedContent.some(item => item.type === 'comment' && item.contentId === commentId);
                        const isReported = reportedContent.some(item => item.type === 'comment' && item.commentId === commentId);
                        const isRemoved = removedContent.some(item => item.contentType === 'comment' && item.contentId === commentId);
                        
                        if (isFlagged || isReported || isRemoved || comment.classList.contains('reported') || comment.classList.contains('admin-flagged')) {
                            comment.classList.add('moderation-highlighted');
                            comment.style.opacity = '1';
                            
                            const commentContainer = comment.querySelector('.bg-[#1a1a1a]');
                            if (commentContainer) {
                                commentContainer.style.boxShadow = '0 0 0 2px #ef4444';
                                
                                // Add a label if not already present
                                if (!commentContainer.querySelector('.moderation-label')) {
                                    const labelDiv = document.createElement('div');
                                    labelDiv.className = 'moderation-label absolute top-0 right-0 bg-red-600 text-white text-xs px-2 py-1 m-2 rounded-full z-20';
                                    
                                    let labelText = '';
                                    if (isFlagged) labelText += '🚩 ';
                                    if (isReported) labelText += '⚠️ ';
                                    if (isRemoved) labelText += '❌ ';
                                    
                                    labelDiv.textContent = labelText + 'Flagged';
                                    
                                    // Ensure comment container has relative positioning
                                    if (commentContainer.style.position !== 'relative') {
                                        commentContainer.style.position = 'relative';
                                    }
                                    
                                    commentContainer.appendChild(labelDiv);
                                }
                            }
                        }
                    });
                } else {
                    // Remove moderation styles
                    allPosts.forEach(post => {
                        post.classList.remove('moderation-dimmed', 'moderation-highlighted');
                        post.style.opacity = '';
                        post.style.boxShadow = '';
                        
                        // Remove moderation labels
                        const label = post.querySelector('.moderation-label');
                        if (label) label.remove();
                        
                        // Remove unhide buttons
                        const unhideBtn = post.querySelector('.unhide-btn');
                        if (unhideBtn) unhideBtn.remove();
                    });
                    
                    allComments.forEach(comment => {
                        comment.classList.remove('moderation-dimmed', 'moderation-highlighted');
                        comment.style.opacity = '';
                        
                        const commentContainer = comment.querySelector('.bg-[#1a1a1a]');
                        if (commentContainer) {
                            commentContainer.style.boxShadow = '';
                            
                            // Remove moderation labels
                            const label = commentContainer.querySelector('.moderation-label');
                            if (label) label.remove();
                        }
                    });
                    
                    // Re-apply hidden state to posts that should be hidden
                    applyHiddenPostsState();
                }
            }
            
            // Flagged content button
            if (flaggedContentBtn) {
                flaggedContentBtn.addEventListener('click', function() {
                    Swal.fire({
                        title: '<span class="text-xl font-bold">Admin Flagged Content</span>',
                        html: `
                            <div class="bg-gray-800 p-4 rounded-lg border border-gray-700 mb-4 text-center">
                                <p class="text-white">Admin flagged content is content that administrators have manually flagged for review or monitoring.</p>
                                <p class="text-white mt-2">Unlike user reports, these are internal flags visible only to admins.</p>
                            </div>
                            <div class="text-left mt-4 bg-[#252525] p-4 rounded-lg">
                                <p class="text-gray-300">No admin-flagged content at this time.</p>
                            </div>
                        `,
                        icon: 'info',
                        background: '#1a1a1a',
                        color: '#FFFFFF',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'Close',
                        width: '600px'
                    });
                });
            }
            
            // Function to update the report count in the admin panel
            function updateReportCount() {
                const reports = JSON.parse(localStorage.getItem('communityReports') || '[]');
                const countElements = document.querySelectorAll('.reported-count');
                
                countElements.forEach(el => {
                    el.textContent = reports.length;
                    
                    // Highlight the badge if there are reports
                    if (reports.length > 0) {
                        el.classList.add('animate-pulse');
                    } else {
                        el.classList.remove('animate-pulse');
                    }
                });
            }
            
            // Update report count on page load
            updateReportCount();
        }

        // Reporting System
        // Handle post reports from members and trainers
        const reportPostBtns = document.querySelectorAll('.report-post-btn');
        if (reportPostBtns.length > 0) {
            reportPostBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const postId = this.dataset.postId;
                    const postContent = this.closest('.post-card').querySelector('.post-text').textContent.trim();
                    const postAuthor = this.closest('.post-card').querySelector('.font-semibold').textContent.trim();
                    
                    Swal.fire({
                        title: '<span class="text-xl font-bold">Report Inappropriate Content</span>',
                        html: `
                            <div class="bg-yellow-900/20 p-4 rounded-lg border border-yellow-500/50 mb-4">
                                <p class="text-white">Please select the reason for reporting this post:</p>
                            </div>
                            <div class="text-left mt-4">
                                <select id="report-reason" class="w-full p-2 bg-gray-700 text-white rounded border border-gray-600">
                                    <option value="inappropriate">Inappropriate Language</option>
                                    <option value="spam">Spam</option>
                                    <option value="harassment">Harassment</option>
                                    <option value="violence">Violence</option>
                                    <option value="other">Other</option>
                                </select>
                                <textarea id="report-notes" class="w-full mt-3 p-2 bg-gray-700 text-white rounded border border-gray-600" placeholder="Additional notes (optional)" rows="3"></textarea>
                            </div>
                        `,
                        showCancelButton: true,
                        confirmButtonText: 'Submit Report',
                        cancelButtonText: 'Cancel',
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        background: '#1a1a1a',
                        color: '#FFFFFF',
                        preConfirm: () => {
                            return {
                                reason: document.getElementById('report-reason').value,
                                notes: document.getElementById('report-notes').value
                            };
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Here you would make an AJAX call to your backend to report the post
                            // For now, we'll simulate storing the report in localStorage
                            const reports = JSON.parse(localStorage.getItem('communityReports') || '[]');
                            reports.push({
                                id: Date.now(),
                                type: 'post',
                                postId: postId,
                                reason: result.value.reason,
                                notes: result.value.notes,
                                reporter: '{{ auth()->check() ? auth()->user()->full_name : "Anonymous" }}',
                                reporterRole: '{{ auth()->check() ? auth()->user()->role : "guest" }}',
                                timestamp: new Date().toISOString(),
                                content: postContent.substring(0, 100) + (postContent.length > 100 ? '...' : ''),
                                author: postAuthor
                            });
                            localStorage.setItem('communityReports', JSON.stringify(reports));
                            
                            // Update the report count in the admin panel
                            updateReportCount();
                            
                            // Show success notification
                            Swal.fire({
                                title: 'Report Submitted',
                                text: 'Thank you for helping keep our community safe. An administrator will review this content.',
                                icon: 'success',
                                background: '#1a1a1a',
                                color: '#FFFFFF',
                                confirmButtonColor: '#3085d6',
                                timer: 3000,
                                timerProgressBar: true
                            });
                            
                            // Add a visual indicator that the post has been reported
                            const postCard = this.closest('.post-card');
                            if (postCard && !postCard.classList.contains('reported')) {
                                postCard.classList.add('reported');
                                const reportBadge = document.createElement('div');
                                reportBadge.className = 'absolute top-2 right-2 bg-yellow-600 text-white text-xs px-2 py-1 rounded-full z-10';
                                reportBadge.innerHTML = '<i class="fas fa-flag mr-1"></i> Reported';
                                
                                if (postCard.style.position !== 'relative') {
                                    postCard.style.position = 'relative';
                                }
                                
                                postCard.appendChild(reportBadge);
                            }
                        }
                    });
                });
            });
        }
        
        // Handle comment reports from members and trainers
        const reportCommentBtns = document.querySelectorAll('.report-comment-btn');
        if (reportCommentBtns.length > 0) {
            reportCommentBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const commentId = this.dataset.commentId;
                    const commentContent = this.closest('.flex-1').querySelector('p').textContent.trim();
                    const commentAuthor = this.closest('.flex-1').querySelector('.font-semibold').textContent.trim();
                    
                    Swal.fire({
                        title: '<span class="text-xl font-bold">Report Inappropriate Comment</span>',
                        html: `
                            <div class="bg-yellow-900/20 p-4 rounded-lg border border-yellow-500/50 mb-4">
                                <p class="text-white">Please select the reason for reporting this comment:</p>
                            </div>
                            <div class="text-left mt-4">
                                <select id="report-comment-reason" class="w-full p-2 bg-gray-700 text-white rounded border border-gray-600">
                                    <option value="inappropriate">Inappropriate Language</option>
                                    <option value="spam">Spam</option>
                                    <option value="harassment">Harassment</option>
                                    <option value="violence">Violence</option>
                                    <option value="other">Other</option>
                                </select>
                                <textarea id="report-comment-notes" class="w-full mt-3 p-2 bg-gray-700 text-white rounded border border-gray-600" placeholder="Additional notes (optional)" rows="3"></textarea>
                            </div>
                        `,
                        showCancelButton: true,
                        confirmButtonText: 'Submit Report',
                        cancelButtonText: 'Cancel',
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        background: '#1a1a1a',
                        color: '#FFFFFF',
                        preConfirm: () => {
                            return {
                                reason: document.getElementById('report-comment-reason').value,
                                notes: document.getElementById('report-comment-notes').value
                            };
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Here you would make an AJAX call to your backend to report the comment
                            // For now, we'll simulate storing the report in localStorage
                            const reports = JSON.parse(localStorage.getItem('communityReports') || '[]');
                            reports.push({
                                id: Date.now(),
                                type: 'comment',
                                commentId: commentId,
                                reason: result.value.reason,
                                notes: result.value.notes,
                                reporter: '{{ auth()->check() ? auth()->user()->full_name : "Anonymous" }}',
                                reporterRole: '{{ auth()->check() ? auth()->user()->role : "guest" }}',
                                timestamp: new Date().toISOString(),
                                content: commentContent.substring(0, 100) + (commentContent.length > 100 ? '...' : ''),
                                author: commentAuthor
                            });
                            localStorage.setItem('communityReports', JSON.stringify(reports));
                            
                            // Update the report count in the admin panel
                            updateReportCount();
                            
                            // Show success notification
                            Swal.fire({
                                title: 'Report Submitted',
                                text: 'Thank you for helping keep our community safe. An administrator will review this comment.',
                                icon: 'success',
                                background: '#1a1a1a',
                                color: '#FFFFFF',
                                confirmButtonColor: '#3085d6',
                                timer: 3000,
                                timerProgressBar: true
                            });
                            
                            // Add a visual indicator that the comment has been reported
                            const commentElement = this.closest('.flex.items-start.space-x-3');
                            if (commentElement && !commentElement.classList.contains('reported')) {
                                commentElement.classList.add('reported');
                                const reportBadge = document.createElement('div');
                                reportBadge.className = 'absolute top-0 right-0 bg-yellow-600 text-white text-xs px-2 py-1 rounded-full z-10';
                                reportBadge.innerHTML = '<i class="fas fa-flag mr-1"></i> Reported';
                                
                                const commentContainer = commentElement.querySelector('.bg-[#1a1a1a]');
                                if (commentContainer) {
                                    if (commentContainer.style.position !== 'relative') {
                                        commentContainer.style.position = 'relative';
                                    }
                                    commentContainer.appendChild(reportBadge);
                                }
                            }
                        }
                    });
                });
            });
        }
        
        // Admin reported content functionality
        const reportedContentBtn = document.getElementById('reportedContentBtn');
        if (reportedContentBtn) {
            // Update report count on page load
            updateReportCount();
            
            reportedContentBtn.addEventListener('click', function() {
                const reports = JSON.parse(localStorage.getItem('communityReports') || '[]');
                
                if (reports.length === 0) {
                    Swal.fire({
                        title: 'No Reported Content',
                        html: `
                            <div class="bg-gray-800 p-4 rounded-lg border border-gray-700 mb-4 text-center">
                                <p class="text-white">There are no user reports to review at this time.</p>
                            </div>
                        `,
                        icon: 'info',
                        background: '#1a1a1a',
                        color: '#FFFFFF',
                        confirmButtonColor: '#3085d6'
                    });
                    return;
                }
                
                // Group reports by content
                const groupedReports = {};
                reports.forEach(report => {
                    const contentKey = `${report.type}-${report.type === 'post' ? report.postId : report.commentId}`;
                    
                    if (!groupedReports[contentKey]) {
                        groupedReports[contentKey] = {
                            type: report.type,
                            contentId: report.type === 'post' ? report.postId : report.commentId,
                            content: report.content,
                            author: report.author,
                            reports: [],
                            reportCount: 0,
                            firstReportTime: new Date(report.timestamp)
                        };
                    }
                    
                    groupedReports[contentKey].reports.push(report);
                    groupedReports[contentKey].reportCount++;
                    
                    // Keep track of earliest report time
                    const reportTime = new Date(report.timestamp);
                    if (reportTime < groupedReports[contentKey].firstReportTime) {
                        groupedReports[contentKey].firstReportTime = reportTime;
                    }
                });
                
                // Convert to array and sort by report count (descending) and then by first report time
                const sortedContent = Object.values(groupedReports).sort((a, b) => {
                    if (b.reportCount !== a.reportCount) {
                        return b.reportCount - a.reportCount; // Most reported first
                    }
                    return a.firstReportTime - b.firstReportTime; // Then oldest reports first
                });
                
                // Generate HTML for each grouped report
                const reportsHtml = sortedContent.map((item) => {
                    const date = item.firstReportTime.toLocaleString();
                    const reporterTypes = {};
                    
                    // Count reporter types
                    item.reports.forEach(report => {
                        if (!reporterTypes[report.reporterRole]) {
                            reporterTypes[report.reporterRole] = 0;
                        }
                        reporterTypes[report.reporterRole]++;
                    });
                    
                    // Generate reporter badges
                    let reporterBadges = '';
                    if (reporterTypes.trainer) {
                        reporterBadges += `<span class="bg-blue-900 text-white text-xs px-2 py-1 rounded-full mr-1">
                            ${reporterTypes.trainer} Trainer${reporterTypes.trainer !== 1 ? 's' : ''}
                        </span>`;
                    }
                    if (reporterTypes.member) {
                        reporterBadges += `<span class="bg-green-900 text-white text-xs px-2 py-1 rounded-full">
                            ${reporterTypes.member} Member${reporterTypes.member !== 1 ? 's' : ''}
                        </span>`;
                    }
                    
                    // Get reasons summary
                    const reasonCounts = {};
                    item.reports.forEach(report => {
                        if (!reasonCounts[report.reason]) {
                            reasonCounts[report.reason] = 0;
                        }
                        reasonCounts[report.reason]++;
                    });
                    
                    // Convert reason counts to text
                    const reasonTexts = Object.entries(reasonCounts).map(([reason, count]) => {
                        const reasonText = {
                            'inappropriate': 'Inappropriate Language',
                            'spam': 'Spam',
                            'harassment': 'Harassment',
                            'violence': 'Violence',
                            'other': 'Other'
                        }[reason] || reason;
                        
                        return `<span class="bg-red-900 text-white text-xs px-2 py-1 rounded-full mr-1 mb-1 inline-block">
                            ${reasonText} (${count})
                        </span>`;
                    }).join(' ');
                    
                    return `
                        <div class="bg-[#252525] p-4 rounded-lg mb-4 border border-[#3a3a3a] report-item" 
                             data-content-type="${item.type}" 
                             data-content-id="${item.contentId}">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <div class="flex items-center">
                                        <span class="font-semibold text-white">${item.type.charAt(0).toUpperCase() + item.type.slice(1)} by ${item.author}</span>
                                        <span class="ml-2 bg-red-600 text-white text-xs px-2 py-1 rounded-full">
                                            ${item.reportCount} Report${item.reportCount !== 1 ? 's' : ''}
                                        </span>
                                    </div>
                                    <div class="text-xs text-gray-400 mt-1">First reported on ${date}</div>
                                </div>
                                <div class="flex flex-wrap items-center gap-2">
                                    ${reporterBadges}
                                </div>
                            </div>
                            
                            <div class="mt-2">
                                <div class="text-sm text-gray-300 mb-2">Reported for:</div>
                                <div class="flex flex-wrap gap-1 mb-2">
                                    ${reasonTexts}
                                </div>
                            </div>
                            
                            <div class="mt-2 p-3 bg-[#1a1a1a] rounded border border-[#3a3a3a]">
                                <p class="text-gray-300">${item.content}</p>
                            </div>
                            
                            <div class="mt-3 flex justify-between items-center">
                                <button class="view-all-reports-btn px-3 py-1 bg-gray-700 hover:bg-gray-600 text-white rounded text-sm"
                                        data-content-type="${item.type}" 
                                        data-content-id="${item.contentId}">
                                    <i class="fas fa-list-ul mr-1"></i> View All Reports (${item.reportCount})
                                </button>
                                
                                <div class="flex gap-2">
                                    <button class="dismiss-all-reports-btn px-3 py-1 bg-gray-700 hover:bg-gray-600 text-white rounded text-sm"
                                            data-content-type="${item.type}" 
                                            data-content-id="${item.contentId}">
                                        Dismiss All
                                    </button>
                                    <button class="take-action-btn px-3 py-1 bg-red-700 hover:bg-red-600 text-white rounded text-sm"
                                            data-content-type="${item.type}" 
                                            data-content-id="${item.contentId}">
                                        Take Action
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                }).join('');
                
                Swal.fire({
                    title: '<span class="text-xl font-bold">User Reported Content</span>',
                    html: `
                        <div class="text-left max-h-[60vh] overflow-y-auto p-1">
                            ${reportsHtml}
                        </div>
                    `,
                    background: '#1a1a1a',
                    color: '#FFFFFF',
                    confirmButtonText: 'Close',
                    confirmButtonColor: '#3085d6',
                    width: '600px',
                    showClass: {
                        popup: 'animate__animated animate__fadeInDown animate__faster'
                    },
                    hideClass: {
                        popup: 'animate__animated animate__fadeOutUp animate__faster'
                    },
                    didOpen: () => {
                        // Add event listeners to buttons
                        document.querySelectorAll('.view-all-reports-btn').forEach(btn => {
                            btn.addEventListener('click', function() {
                                const contentType = this.dataset.contentType;
                                const contentId = this.dataset.contentId;
                                viewAllReports(contentType, contentId);
                            });
                        });
                        
                        document.querySelectorAll('.dismiss-all-reports-btn').forEach(btn => {
                            btn.addEventListener('click', function() {
                                const contentType = this.dataset.contentType;
                                const contentId = this.dataset.contentId;
                                dismissAllReports(contentType, contentId);
                            });
                        });
                        
                        document.querySelectorAll('.take-action-btn').forEach(btn => {
                            btn.addEventListener('click', function() {
                                const contentType = this.dataset.contentType;
                                const contentId = this.dataset.contentId;
                                takeActionOnContent(contentType, contentId);
                            });
                        });
                    }
                });
            });
        }
        
        // Function to view all reports for a specific content
        function viewAllReports(contentType, contentId) {
            const reports = JSON.parse(localStorage.getItem('communityReports') || '[]');
            
            // Filter reports for the specific content
            const contentReports = reports.filter(report => {
                if (contentType === 'post' && report.type === 'post') {
                    return report.postId === contentId;
                } else if (contentType === 'comment' && report.type === 'comment') {
                    return report.commentId === contentId;
                }
                return false;
            });
            
            // Sort reports by timestamp, newest first
            contentReports.sort((a, b) => new Date(b.timestamp) - new Date(a.timestamp));
            
            // Generate HTML for individual reports
            const reportsHtml = contentReports.map((report, index) => {
                const date = new Date(report.timestamp).toLocaleString();
                const reasonText = {
                    'inappropriate': 'Inappropriate Language',
                    'spam': 'Spam',
                    'harassment': 'Harassment',
                    'violence': 'Violence',
                    'other': 'Other'
                }[report.reason] || report.reason;
                
                const reporterBadge = report.reporterRole === 'trainer' 
                    ? '<span class="bg-blue-900 text-white text-xs px-2 py-1 rounded-full">Trainer</span>'
                    : '<span class="bg-green-900 text-white text-xs px-2 py-1 rounded-full">Member</span>';
                
                return `
                    <div class="bg-[#252525] p-3 rounded-lg mb-3 border border-[#3a3a3a]">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <span class="text-white">Reported by: ${report.reporter}</span>
                                <div class="text-xs text-gray-400 mt-1">Reported on ${date}</div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="bg-red-900 text-white text-xs px-2 py-1 rounded-full">${reasonText}</span>
                                ${reporterBadge}
                            </div>
                        </div>
                        ${report.notes ? `<div class="mt-2 text-sm text-gray-300 p-2 bg-[#1a1a1a] rounded">
                            <span class="font-semibold">Reporter notes:</span> ${report.notes}
                        </div>` : ''}
                    </div>
                `;
            }).join('');
            
            Swal.fire({
                title: `<span class="text-xl font-bold">All Reports (${contentReports.length})</span>`,
                html: `
                    <div class="text-left max-h-[60vh] overflow-y-auto p-1">
                        ${reportsHtml}
                    </div>
                `,
                background: '#1a1a1a',
                color: '#FFFFFF',
                confirmButtonText: 'Back to Summary',
                confirmButtonColor: '#3085d6',
                width: '600px'
            });
        }
        
        // Function to dismiss all reports for a specific content
        function dismissAllReports(contentType, contentId) {
            Swal.fire({
                title: 'Dismiss All Reports?',
                text: 'Are you sure you want to dismiss all reports for this content?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, dismiss all',
                background: '#1a1a1a',
                color: '#FFFFFF'
            }).then((result) => {
                if (result.isConfirmed) {
                    const reports = JSON.parse(localStorage.getItem('communityReports') || '[]');
                    
                    // Filter out reports for the specific content
                    const updatedReports = reports.filter(report => {
                        if (contentType === 'post' && report.type === 'post') {
                            return report.postId !== contentId;
                        } else if (contentType === 'comment' && report.type === 'comment') {
                            return report.commentId !== contentId;
                        }
                        return true;
                    });
                    
                    localStorage.setItem('communityReports', JSON.stringify(updatedReports));
                    
                    // Remove the report item from the UI
                    const reportItem = document.querySelector(`.report-item[data-content-type="${contentType}"][data-content-id="${contentId}"]`);
                    if (reportItem) {
                        reportItem.classList.add('animate__animated', 'animate__fadeOut');
                        setTimeout(() => {
                            reportItem.remove();
                            
                            // If no more reports, close the modal
                            if (document.querySelectorAll('.report-item').length === 0) {
                                Swal.close();
                                Swal.fire({
                                    title: 'All Reports Handled',
                                    text: 'There are no more reports to review.',
                                    icon: 'success',
                                    background: '#1a1a1a',
                                    color: '#FFFFFF',
                                    confirmButtonColor: '#3085d6',
                                    timer: 2000,
                                    timerProgressBar: true
                                });
                            }
                        }, 500);
                    }
                    
                    // Update the report count
                    updateReportCount();
                }
            });
        }
        
        // Take action on reported content
        function takeActionOnContent(contentType, contentId) {
            Swal.fire({
                title: '<span class="text-xl font-bold">Take Action</span>',
                html: `
                    <div class="bg-red-900/20 p-4 rounded-lg border border-red-500/50 mb-4">
                        <p class="text-white">Choose an action for this reported ${contentType}:</p>
                    </div>
                    <div class="text-left mt-4">
                        <select id="action-type" class="w-full p-2 bg-gray-700 text-white rounded border border-gray-600">
                            <option value="delete">Delete ${contentType}</option>
                            <option value="hide">Hide ${contentType}</option>
                            <option value="warn">Warn user</option>
                        </select>
                        <div class="mt-3">
                            <label class="block text-white mb-1">Violation type:</label>
                            <select id="violation-type" class="w-full p-2 bg-gray-700 text-white rounded border border-gray-600">
                                <option value="inappropriate">Inappropriate Language</option>
                                <option value="harassment">Harassment</option>
                                <option value="spam">Spam</option>
                                <option value="violence">Violence or Threats</option>
                                <option value="misinformation">Misinformation</option>
                                <option value="tos">Terms of Service Violation</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="mt-3">
                            <label class="block text-white mb-1">Message to user:</label>
                            <textarea id="user-message" class="w-full p-2 bg-gray-700 text-white rounded border border-gray-600" 
                                placeholder="Explain why this content was removed (will be sent to the user)" rows="3"></textarea>
                        </div>
                        <div class="mt-3">
                            <label class="block text-white mb-1">Internal admin notes:</label>
                            <textarea id="admin-notes" class="w-full p-2 bg-gray-700 text-white rounded border border-gray-600" 
                                placeholder="Notes for other admins (not sent to user)" rows="2"></textarea>
                        </div>
                        <div class="mt-3">
                            <label class="flex items-center text-white">
                                <input type="checkbox" id="track-offense" class="mr-2 bg-gray-700 border-gray-600" checked>
                                Track as user offense
                            </label>
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Confirm Action',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                background: '#1a1a1a',
                color: '#FFFFFF',
                width: '600px',
                preConfirm: () => {
                    const userMessage = document.getElementById('user-message').value;
                    if (!userMessage.trim()) {
                        Swal.showValidationMessage('Please provide a message for the user');
                        return false;
                    }
                    
                    return {
                        action: document.getElementById('action-type').value,
                        violation: document.getElementById('violation-type').value,
                        userMessage: userMessage,
                        adminNotes: document.getElementById('admin-notes').value,
                        trackOffense: document.getElementById('track-offense').checked
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Here you would make an AJAX call to your backend to take the selected action
                    console.log(`Taking action "${result.value.action}" on ${contentType} ${contentId}`);
                    
                    // For demo purposes, we'll dismiss all reports for this content
                    dismissAllReports(contentType, contentId);
                    
                    // Store the removed content information for the user to see in "My Posts"
                    const removedContent = JSON.parse(localStorage.getItem('removedContent') || '[]');
                    
                    // Find the content element
                    let contentElement, contentText, authorId;
                    
                    if (contentType === 'post') {
                        contentElement = document.querySelector(`.post-card[data-post-id="${contentId}"]`);
                        if (contentElement) {
                            contentText = contentElement.querySelector('.post-text')?.textContent.trim() || 'Post content';
                            authorId = contentElement.dataset.userId;
                            
                            // Apply visual effect based on action
                            if (result.value.action === 'delete' || result.value.action === 'hide') {
                                contentElement.style.opacity = '0.5';
                                
                                // Add removed badge
                                const removedBadge = document.createElement('div');
                                removedBadge.className = 'absolute inset-0 flex items-center justify-center bg-black bg-opacity-70 z-20';
                                removedBadge.innerHTML = `<div class="bg-red-900 text-white px-4 py-2 rounded">
                                    <i class="fas fa-ban mr-2"></i> ${result.value.action === 'delete' ? 'Deleted' : 'Hidden'} by Admin
                                </div>`;
                                
                                if (contentElement.style.position !== 'relative') {
                                    contentElement.style.position = 'relative';
                                }
                                
                                contentElement.appendChild(removedBadge);
                            }
                        }
                    } else if (contentType === 'comment') {
                        contentElement = document.querySelector(`.comment[data-comment-id="${contentId}"]`);
                        if (contentElement) {
                            contentText = contentElement.querySelector('p')?.textContent.trim() || 'Comment content';
                            authorId = contentElement.dataset.userId;
                            
                            // Apply visual effect based on action
                            if (result.value.action === 'delete' || result.value.action === 'hide') {
                                contentElement.style.opacity = '0.5';
                                
                                // Add removed badge
                                const commentContainer = contentElement.querySelector('.bg-[#1a1a1a]');
                                if (commentContainer) {
                                    const removedBadge = document.createElement('div');
                                    removedBadge.className = 'absolute inset-0 flex items-center justify-center bg-black bg-opacity-70 z-20';
                                    removedBadge.innerHTML = `<div class="bg-red-900 text-white px-4 py-2 rounded">
                                        <i class="fas fa-ban mr-2"></i> ${result.value.action === 'delete' ? 'Deleted' : 'Hidden'} by Admin
                                    </div>`;
                                    
                                    if (commentContainer.style.position !== 'relative') {
                                        commentContainer.style.position = 'relative';
                                    }
                                    
                                    commentContainer.appendChild(removedBadge);
                                }
                            }
                        }
                    }
                    
                    // Add to removed content list
                    if (authorId) {
                        removedContent.push({
                            id: Date.now(),
                            contentId: contentId,
                            contentType: contentType,
                            content: contentText,
                            action: result.value.action,
                            violation: result.value.violation,
                            message: result.value.userMessage,
                            timestamp: new Date().toISOString(),
                            userId: authorId
                        });
                        
                        localStorage.setItem('removedContent', JSON.stringify(removedContent));
                    }
                    
                    // If tracking offenses, store the offense
                    if (result.value.trackOffense && authorId) {
                        const userOffenses = JSON.parse(localStorage.getItem('userOffenses') || '{}');
                        
                        if (!userOffenses[authorId]) {
                            userOffenses[authorId] = [];
                        }
                        
                        userOffenses[authorId].push({
                            id: Date.now(),
                            type: result.value.violation,
                            action: result.value.action,
                            contentType: contentType,
                            adminNotes: result.value.adminNotes,
                            timestamp: new Date().toISOString()
                        });
                        
                        localStorage.setItem('userOffenses', JSON.stringify(userOffenses));
                    }
                    
                    // Show success notification
                    Swal.fire({
                        title: 'Action Taken',
                        text: `The ${contentType} has been ${result.value.action}ed.`,
                        icon: 'success',
                        background: '#1a1a1a',
                        color: '#FFFFFF',
                        confirmButtonColor: '#3085d6',
                        timer: 2000,
                        timerProgressBar: true
                    });
                }
            });
        }

        // Handle admin flagging for posts
        document.querySelectorAll('.admin-flag-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const postId = this.dataset.postId;
                const postAuthor = this.dataset.postAuthor;
                const postContent = this.dataset.postContent;
                
                Swal.fire({
                    title: '<span class="text-xl font-bold">Flag Content</span>',
                    html: `
                        <div class="bg-red-900/20 p-4 rounded-lg border border-red-500/50 mb-4">
                            <p class="text-white">This will add the post to your admin flagged content list for review.</p>
                        </div>
                        <div class="text-left mt-4">
                            <select id="flag-reason" class="w-full p-2 bg-gray-700 text-white rounded border border-gray-600">
                                <option value="inappropriate">Inappropriate Language</option>
                                <option value="spam">Spam</option>
                                <option value="harassment">Harassment</option>
                                <option value="violence">Violence</option>
                                <option value="misinformation">Misinformation</option>
                                <option value="other">Other</option>
                            </select>
                            <textarea id="flag-notes" class="w-full mt-3 p-2 bg-gray-700 text-white rounded border border-gray-600" placeholder="Admin notes (optional)" rows="3"></textarea>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Flag Content',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    background: '#1a1a1a',
                    color: '#FFFFFF',
                    preConfirm: () => {
                        return {
                            reason: document.getElementById('flag-reason').value,
                            notes: document.getElementById('flag-notes').value
                        };
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Add to admin flagged content
                        const flaggedContent = JSON.parse(localStorage.getItem('adminFlaggedContent') || '[]');
                        flaggedContent.push({
                            id: Date.now(),
                            type: 'post',
                            contentId: postId,
                            author: postAuthor,
                            content: postContent,
                            reason: result.value.reason,
                            notes: result.value.notes,
                            timestamp: new Date().toISOString(),
                            flaggedBy: '{{ auth()->user()->full_name }}'
                        });
                        localStorage.setItem('adminFlaggedContent', JSON.stringify(flaggedContent));
                        
                        // Update the flagged count
                        updateAdminFlaggedCount();
                        
                        // Show success notification
                        Swal.fire({
                            title: 'Content Flagged',
                            text: 'The post has been added to your admin flagged content list.',
                            icon: 'success',
                            background: '#1a1a1a',
                            color: '#FFFFFF',
                            confirmButtonColor: '#3085d6',
                            timer: 2000,
                            timerProgressBar: true
                        });
                        
                        // Add a visual indicator that the post has been flagged by admin
                        const postCard = this.closest('.post-card');
                        if (postCard && !postCard.classList.contains('admin-flagged')) {
                            postCard.classList.add('admin-flagged');
                            const flagBadge = document.createElement('div');
                            flagBadge.className = 'absolute top-2 right-2 bg-red-800 text-white text-xs px-2 py-1 rounded-full z-10';
                            flagBadge.innerHTML = '<i class="fas fa-shield-alt mr-1"></i> Admin Flagged';
                            
                            if (postCard.style.position !== 'relative') {
                                postCard.style.position = 'relative';
                            }
                            
                            postCard.appendChild(flagBadge);
                        }
                    }
                });
            });
        });
        
        // Handle admin flagging for comments
        document.querySelectorAll('.admin-flag-comment-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const commentId = this.dataset.commentId;
                const commentAuthor = this.dataset.commentAuthor;
                const commentContent = this.dataset.commentContent;
                
                Swal.fire({
                    title: '<span class="text-xl font-bold">Flag Content</span>',
                    html: `
                        <div class="bg-red-900/20 p-4 rounded-lg border border-red-500/50 mb-4">
                            <p class="text-white">This will add the comment to your admin flagged content list for review.</p>
                        </div>
                        <div class="text-left mt-4">
                            <select id="flag-comment-reason" class="w-full p-2 bg-gray-700 text-white rounded border border-gray-600">
                                <option value="inappropriate">Inappropriate Language</option>
                                <option value="spam">Spam</option>
                                <option value="harassment">Harassment</option>
                                <option value="violence">Violence</option>
                                <option value="other">Other</option>
                            </select>
                            <textarea id="flag-comment-notes" class="w-full mt-3 p-2 bg-gray-700 text-white rounded border border-gray-600" placeholder="Admin notes (optional)" rows="3"></textarea>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Flag Comment',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    background: '#1a1a1a',
                    color: '#FFFFFF',
                    preConfirm: () => {
                        return {
                            reason: document.getElementById('flag-comment-reason').value,
                            notes: document.getElementById('flag-comment-notes').value
                        };
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Add to admin flagged content
                        const flaggedContent = JSON.parse(localStorage.getItem('adminFlaggedContent') || '[]');
                        flaggedContent.push({
                            id: Date.now(),
                            type: 'comment',
                            contentId: commentId,
                            author: commentAuthor,
                            content: commentContent,
                            reason: result.value.reason,
                            notes: result.value.notes,
                            timestamp: new Date().toISOString(),
                            flaggedBy: '{{ auth()->user()->full_name }}'
                        });
                        localStorage.setItem('adminFlaggedContent', JSON.stringify(flaggedContent));
                        
                        // Update the flagged count
                        updateAdminFlaggedCount();
                        
                        // Show success notification
                        Swal.fire({
                            title: 'Comment Flagged',
                            text: 'The comment has been added to your admin flagged content list.',
                            icon: 'success',
                            background: '#1a1a1a',
                            color: '#FFFFFF',
                            confirmButtonColor: '#3085d6',
                            timer: 2000,
                            timerProgressBar: true
                        });
                        
                        // Add a visual indicator that the comment has been flagged by admin
                        const commentElement = this.closest('.comment');
                        if (commentElement && !commentElement.classList.contains('admin-flagged')) {
                            commentElement.classList.add('admin-flagged');
                            const flagBadge = document.createElement('div');
                            flagBadge.className = 'absolute top-0 right-0 bg-red-800 text-white text-xs px-2 py-1 rounded-full z-10';
                            flagBadge.innerHTML = '<i class="fas fa-shield-alt mr-1"></i> Admin Flagged';
                            
                            const commentContainer = commentElement.querySelector('.bg-[#1a1a1a]');
                            if (commentContainer) {
                                if (commentContainer.style.position !== 'relative') {
                                    commentContainer.style.position = 'relative';
                                }
                                commentContainer.appendChild(flagBadge);
                            }
                        }
                    }
                });
            });
        });

        // Admin flagged content functionality
        const flaggedContentBtn = document.getElementById('flaggedContentBtn');
        if (flaggedContentBtn) {
            // Update admin flagged count on page load
            updateAdminFlaggedCount();
            
            flaggedContentBtn.addEventListener('click', function() {
                const flaggedContent = JSON.parse(localStorage.getItem('adminFlaggedContent') || '[]');
                
                if (flaggedContent.length === 0) {
                    Swal.fire({
                        title: '<span class="text-xl font-bold">Admin Flagged Content</span>',
                        html: `
                            <div class="bg-gray-800 p-4 rounded-lg border border-gray-700 mb-4 text-center">
                                <p class="text-white">Admin flagged content is content that administrators have manually flagged for review or monitoring.</p>
                                <p class="text-white mt-2">Unlike user reports, these are internal flags visible only to admins.</p>
                            </div>
                            <div class="text-left mt-4 bg-[#252525] p-4 rounded-lg">
                                <p class="text-gray-300">No admin-flagged content at this time.</p>
                            </div>
                        `,
                        icon: 'info',
                        background: '#1a1a1a',
                        color: '#FFFFFF',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'Close',
                        width: '600px'
                    });
                    return;
                }
                
                // Sort flagged content by timestamp, newest first
                flaggedContent.sort((a, b) => new Date(b.timestamp) - new Date(a.timestamp));
                
                // Generate HTML for each flagged content
                const flaggedHtml = flaggedContent.map((item, index) => {
                    const date = new Date(item.timestamp).toLocaleString();
                    const reasonText = item.reason || 'Admin Review';
                    
                    return `
                        <div class="bg-[#252525] p-4 rounded-lg mb-4 border border-[#3a3a3a] flagged-item" data-flag-id="${item.id}">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <span class="font-semibold text-white">${item.type.charAt(0).toUpperCase() + item.type.slice(1)} by ${item.author}</span>
                                    <div class="text-xs text-gray-400 mt-1">Flagged on ${date}</div>
                                </div>
                                <div>
                                    <span class="bg-red-900 text-white text-xs px-2 py-1 rounded-full">${reasonText}</span>
                                </div>
                            </div>
                            
                            <div class="mt-2 p-3 bg-[#1a1a1a] rounded border border-[#3a3a3a]">
                                <p class="text-gray-300">${item.content}</p>
                            </div>
                            
                            ${item.notes ? `<div class="mt-2 text-sm text-gray-400"><span class="font-semibold">Admin notes:</span> ${item.notes}</div>` : ''}
                            
                            <div class="mt-3 flex justify-end gap-2">
                                <button class="unflag-content-btn px-3 py-1 bg-gray-700 hover:bg-gray-600 text-white rounded text-sm" data-flag-id="${item.id}">
                                    Remove Flag
                                </button>
                                <button class="take-action-on-flagged-btn px-3 py-1 bg-red-700 hover:bg-red-600 text-white rounded text-sm" 
                                        data-flag-id="${item.id}" 
                                        data-content-type="${item.type}" 
                                        data-content-id="${item.contentId}">
                                    Take Action
                                </button>
                            </div>
                        </div>
                    `;
                }).join('');
                
                Swal.fire({
                    title: '<span class="text-xl font-bold">Admin Flagged Content</span>',
                    html: `
                        <div class="text-left max-h-[60vh] overflow-y-auto p-1">
                            ${flaggedHtml}
                        </div>
                    `,
                    background: '#1a1a1a',
                    color: '#FFFFFF',
                    confirmButtonText: 'Close',
                    confirmButtonColor: '#3085d6',
                    width: '600px',
                    didOpen: () => {
                        // Add event listeners to buttons
                        document.querySelectorAll('.unflag-content-btn').forEach(btn => {
                            btn.addEventListener('click', function() {
                                const flagId = parseInt(this.dataset.flagId);
                                unflagContent(flagId);
                            });
                        });
                        
                        document.querySelectorAll('.take-action-on-flagged-btn').forEach(btn => {
                            btn.addEventListener('click', function() {
                                const flagId = parseInt(this.dataset.flagId);
                                const contentType = this.dataset.contentType;
                                const contentId = this.dataset.contentId;
                                takeActionOnFlaggedContent(flagId, contentType, contentId);
                            });
                        });
                    }
                });
            });
        }
        
        // Function to update the admin flagged count
        function updateAdminFlaggedCount() {
            const flaggedContent = JSON.parse(localStorage.getItem('adminFlaggedContent') || '[]');
            const countElements = document.querySelectorAll('#flaggedContentBtn span');
            
            countElements.forEach(el => {
                el.textContent = flaggedContent.length;
                
                // Highlight the badge if there are flagged items
                if (flaggedContent.length > 0) {
                    el.classList.add('animate-pulse');
                    el.style.backgroundColor = '#ef4444';
                } else {
                    el.classList.remove('animate-pulse');
                    el.style.backgroundColor = '';
                }
            });
        }
        
        // Function to remove a flag from admin flagged content
        function unflagContent(flagId) {
            const flaggedContent = JSON.parse(localStorage.getItem('adminFlaggedContent') || '[]');
            const updatedFlagged = flaggedContent.filter(item => item.id !== flagId);
            localStorage.setItem('adminFlaggedContent', JSON.stringify(updatedFlagged));
            
            // Remove the flagged item from the UI
            const flaggedItem = document.querySelector(`.flagged-item[data-flag-id="${flagId}"]`);
            if (flaggedItem) {
                flaggedItem.classList.add('animate__animated', 'animate__fadeOut');
                setTimeout(() => {
                    flaggedItem.remove();
                    
                    // If no more flagged items, close the modal
                    if (document.querySelectorAll('.flagged-item').length === 0) {
                        Swal.close();
                        Swal.fire({
                            title: 'All Flags Cleared',
                            text: 'There are no more flagged items to review.',
                            icon: 'success',
                            background: '#1a1a1a',
                            color: '#FFFFFF',
                            confirmButtonColor: '#3085d6',
                            timer: 2000,
                            timerProgressBar: true
                        });
                    }
                }, 500);
            }
            
            // Update the flagged count
            updateAdminFlaggedCount();
        }

        // Function to take action on flagged content
        function takeActionOnFlaggedContent(flagId, contentType, contentId) {
            Swal.fire({
                title: '<span class="text-xl font-bold">Take Action</span>',
                html: `
                    <div class="bg-red-900/20 p-4 rounded-lg border border-red-500/50 mb-4">
                        <p class="text-white">Choose an action for this flagged ${contentType}:</p>
                    </div>
                    <div class="text-left mt-4">
                        <select id="action-type" class="w-full p-2 bg-gray-700 text-white rounded border border-gray-600">
                            <option value="delete">Delete ${contentType}</option>
                            <option value="hide">Hide ${contentType}</option>
                            <option value="warn">Warn user</option>
                        </select>
                        <div class="mt-3">
                            <label class="block text-white mb-1">Violation type:</label>
                            <select id="violation-type" class="w-full p-2 bg-gray-700 text-white rounded border border-gray-600">
                                <option value="inappropriate">Inappropriate Language</option>
                                <option value="harassment">Harassment</option>
                                <option value="spam">Spam</option>
                                <option value="violence">Violence or Threats</option>
                                <option value="misinformation">Misinformation</option>
                                <option value="tos">Terms of Service Violation</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="mt-3">
                            <label class="block text-white mb-1">Message to user:</label>
                            <textarea id="user-message" class="w-full p-2 bg-gray-700 text-white rounded border border-gray-600" 
                                placeholder="Explain why this content was removed (will be sent to the user)" rows="3"></textarea>
                        </div>
                        <div class="mt-3">
                            <label class="block text-white mb-1">Internal admin notes:</label>
                            <textarea id="admin-notes" class="w-full p-2 bg-gray-700 text-white rounded border border-gray-600" 
                                placeholder="Notes for other admins (not sent to user)" rows="2"></textarea>
                        </div>
                        <div class="mt-3">
                            <label class="flex items-center text-white">
                                <input type="checkbox" id="track-offense" class="mr-2 bg-gray-700 border-gray-600" checked>
                                Track as user offense
                            </label>
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Confirm Action',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                background: '#1a1a1a',
                color: '#FFFFFF',
                width: '600px',
                preConfirm: () => {
                    const userMessage = document.getElementById('user-message').value;
                    if (!userMessage.trim()) {
                        Swal.showValidationMessage('Please provide a message for the user');
                        return false;
                    }
                    
                    return {
                        action: document.getElementById('action-type').value,
                        violation: document.getElementById('violation-type').value,
                        userMessage: userMessage,
                        adminNotes: document.getElementById('admin-notes').value,
                        trackOffense: document.getElementById('track-offense').checked
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Here you would make an AJAX call to your backend to take the selected action
                    console.log(`Taking action "${result.value.action}" on ${contentType} ${contentId}`);
                    
                    // For demo purposes, we'll just unflag the content and show a success message
                    unflagContent(flagId);
                    
                    // Show success notification
                    Swal.fire({
                        title: 'Action Taken',
                        text: `The ${contentType} has been ${result.value.action}ed.`,
                        icon: 'success',
                        background: '#1a1a1a',
                        color: '#FFFFFF',
                        confirmButtonColor: '#3085d6',
                        timer: 2000,
                        timerProgressBar: true
                    });
                    
                    // Store the removed content information for the user to see in "My Posts"
                    const removedContent = JSON.parse(localStorage.getItem('removedContent') || '[]');
                    
                    // Find the content element
                    let contentElement, contentText, authorId;
                    
                    if (contentType === 'post') {
                        contentElement = document.querySelector(`.post-card[data-post-id="${contentId}"]`);
                        if (contentElement) {
                            contentText = contentElement.querySelector('.post-text')?.textContent.trim() || 'Post content';
                            authorId = contentElement.dataset.userId;
                            
                            // Apply visual effect based on action
                            if (result.value.action === 'delete' || result.value.action === 'hide') {
                                contentElement.style.opacity = '0.5';
                                
                                // Add removed badge
                                const removedBadge = document.createElement('div');
                                removedBadge.className = 'absolute inset-0 flex items-center justify-center bg-black bg-opacity-70 z-20';
                                removedBadge.innerHTML = `<div class="bg-red-900 text-white px-4 py-2 rounded">
                                    <i class="fas fa-ban mr-2"></i> ${result.value.action === 'delete' ? 'Deleted' : 'Hidden'} by Admin
                                </div>`;
                                
                                if (contentElement.style.position !== 'relative') {
                                    contentElement.style.position = 'relative';
                                }
                                
                                contentElement.appendChild(removedBadge);
                            }
                        }
                    } else if (contentType === 'comment') {
                        contentElement = document.querySelector(`.comment[data-comment-id="${contentId}"]`);
                        if (contentElement) {
                            contentText = contentElement.querySelector('p')?.textContent.trim() || 'Comment content';
                            authorId = contentElement.dataset.userId;
                            
                            // Apply visual effect based on action
                            if (result.value.action === 'delete' || result.value.action === 'hide') {
                                contentElement.style.opacity = '0.5';
                                
                                // Add removed badge
                                const commentContainer = contentElement.querySelector('.bg-[#1a1a1a]');
                                if (commentContainer) {
                                    const removedBadge = document.createElement('div');
                                    removedBadge.className = 'absolute inset-0 flex items-center justify-center bg-black bg-opacity-70 z-20';
                                    removedBadge.innerHTML = `<div class="bg-red-900 text-white px-4 py-2 rounded">
                                        <i class="fas fa-ban mr-2"></i> ${result.value.action === 'delete' ? 'Deleted' : 'Hidden'} by Admin
                                    </div>`;
                                    
                                    if (commentContainer.style.position !== 'relative') {
                                        commentContainer.style.position = 'relative';
                                    }
                                    
                                    commentContainer.appendChild(removedBadge);
                                }
                            }
                        }
                    }
                    
                    // Add to removed content list
                    if (authorId) {
                        removedContent.push({
                            id: Date.now(),
                            contentId: contentId,
                            contentType: contentType,
                            content: contentText,
                            action: result.value.action,
                            violation: result.value.violation,
                            message: result.value.userMessage,
                            timestamp: new Date().toISOString(),
                            userId: authorId
                        });
                        
                        localStorage.setItem('removedContent', JSON.stringify(removedContent));
                    }
                    
                    // If tracking offenses, store the offense
                    if (result.value.trackOffense && authorId) {
                        const userOffenses = JSON.parse(localStorage.getItem('userOffenses') || '{}');
                        
                        if (!userOffenses[authorId]) {
                            userOffenses[authorId] = [];
                        }
                        
                        userOffenses[authorId].push({
                            id: Date.now(),
                            type: result.value.violation,
                            action: result.value.action,
                            contentType: contentType,
                            adminNotes: result.value.adminNotes,
                            timestamp: new Date().toISOString()
                        });
                        
                        localStorage.setItem('userOffenses', JSON.stringify(userOffenses));
                    }
                }
            });
        }
        
        // Check for removed content when viewing "My Posts"
        const isMyPostsPage = window.location.href.includes('?user=');
        if (isMyPostsPage) {
            const currentUserId = '{{ auth()->id() }}';
            const removedContent = JSON.parse(localStorage.getItem('removedContent') || '[]');
            
            // Filter removed content for current user
            const userRemovedContent = removedContent.filter(item => item.userId === currentUserId);
            
            if (userRemovedContent.length > 0) {
                // Add a small notice at the top to inform the user about moderation
                const postsContainer = document.querySelector('.space-y-6');
                if (postsContainer) {
                    const topInfoBar = document.createElement('div');
                    topInfoBar.className = 'mb-4 bg-red-900/10 rounded-lg p-3 border border-red-700/30 flex items-center justify-between';
                    
                    topInfoBar.innerHTML = `
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3"></i>
                            <div>
                                <h3 class="text-white font-medium">Content Moderation Notice</h3>
                                <p class="text-gray-300 text-sm">Some of your posts or comments have been moderated. See details on each item below.</p>
                            </div>
                        </div>
                    `;
                    
                    postsContainer.insertBefore(topInfoBar, postsContainer.firstChild);
                }
            }
        }

        // Check for moderation notices on posts when viewing "My Posts" or any page
        function checkForModerationNotices() {
            const removedContent = JSON.parse(localStorage.getItem('removedContent') || '[]');
            if (removedContent.length === 0) return;
            
            // Check all posts on the page
            document.querySelectorAll('.post-card').forEach(postCard => {
                const postId = postCard.dataset.postId;
                const userId = postCard.dataset.userId;
                
                // Find if this post has been moderated
                const moderationInfo = removedContent.find(item => 
                    item.contentType === 'post' && 
                    item.contentId === postId && 
                    item.userId === userId
                );
                
                if (moderationInfo) {
                    // Add moderation notice to the post
                    if (!postCard.querySelector('.moderation-notice')) {
                        const violationText = {
                            'inappropriate': 'Inappropriate Language',
                            'harassment': 'Harassment',
                            'spam': 'Spam',
                            'violence': 'Violence or Threats',
                            'misinformation': 'Misinformation',
                            'tos': 'Terms of Service Violation',
                            'other': 'Community Guidelines Violation'
                        }[moderationInfo.violation] || 'Community Guidelines Violation';
                        
                        const actionText = {
                            'delete': 'deleted',
                            'hide': 'hidden',
                            'warn': 'flagged'
                        }[moderationInfo.action] || 'moderated';
                        
                        // Create moderation notice
                        const noticeEl = document.createElement('div');
                        noticeEl.className = 'moderation-notice bg-red-900/10 p-3 border border-red-700/30 rounded-md mb-3';
                        noticeEl.innerHTML = `
                            <div class="flex items-start">
                                <div class="flex-shrink-0 mr-3">
                                    <i class="fas fa-exclamation-circle text-red-500 text-xl"></i>
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-white font-medium">This post has been ${actionText}</h4>
                                    <p class="text-white text-sm mt-1"><span class="font-semibold">Reason:</span> ${violationText}</p>
                                    <p class="text-white text-sm mt-1"><span class="font-semibold">Admin message:</span> ${moderationInfo.message}</p>
                                </div>
                            </div>
                        `;
                        
                        // Insert the notice at the top of the post content
                        const contentArea = postCard.querySelector('.post-text')?.parentNode;
                        if (contentArea) {
                            contentArea.insertBefore(noticeEl, contentArea.firstChild);
                        }
                        
                        // Apply visual effect based on action
                        if (moderationInfo.action === 'delete' || moderationInfo.action === 'hide') {
                            postCard.style.opacity = '0.75';
                            postCard.style.position = 'relative';
                        }
                    }
                }
            });
            
            // Check all comments on the page
            document.querySelectorAll('.comment').forEach(commentEl => {
                const commentId = commentEl.dataset.commentId;
                const userId = commentEl.dataset.userId;
                
                // Find if this comment has been moderated
                const moderationInfo = removedContent.find(item => 
                    item.contentType === 'comment' && 
                    item.contentId === commentId && 
                    item.userId === userId
                );
                
                if (moderationInfo) {
                    // Add moderation notice to the comment
                    if (!commentEl.querySelector('.moderation-notice')) {
                        const violationText = {
                            'inappropriate': 'Inappropriate Language',
                            'harassment': 'Harassment',
                            'spam': 'Spam',
                            'violence': 'Violence or Threats',
                            'misinformation': 'Misinformation',
                            'tos': 'Terms of Service Violation',
                            'other': 'Community Guidelines Violation'
                        }[moderationInfo.violation] || 'Community Guidelines Violation';
                        
                        const actionText = {
                            'delete': 'deleted',
                            'hide': 'hidden',
                            'warn': 'flagged'
                        }[moderationInfo.action] || 'moderated';
                        
                        // Create moderation notice
                        const noticeEl = document.createElement('div');
                        noticeEl.className = 'moderation-notice bg-red-900/10 p-2 border border-red-700/30 rounded-md mb-2 mt-1';
                        noticeEl.innerHTML = `
                            <div class="flex items-start">
                                <div class="flex-shrink-0 mr-2">
                                    <i class="fas fa-exclamation-circle text-red-500"></i>
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-white text-sm font-medium">This comment has been ${actionText}</h4>
                                    <p class="text-white text-xs mt-0.5"><span class="font-semibold">Reason:</span> ${violationText}</p>
                                </div>
                            </div>
                        `;
                        
                        // Insert the notice after the comment content
                        const commentContainer = commentEl.querySelector('.bg-[#1a1a1a]');
                        if (commentContainer) {
                            const commentContent = commentContainer.querySelector('p');
                            if (commentContent) {
                                commentContent.parentNode.insertBefore(noticeEl, commentContent.nextSibling);
                            }
                        }
                        
                        // Apply visual effect based on action
                        if (moderationInfo.action === 'delete' || moderationInfo.action === 'hide') {
                            commentEl.style.opacity = '0.75';
                        }
                    }
                }
            });
        }
        
        // Run the check when the page loads
        checkForModerationNotices();

        // Function to unhide a post
        function unhidePost(postId, postElement) {
            // Remove from localStorage
            const hiddenPosts = JSON.parse(localStorage.getItem('hiddenPosts') || '[]');
            const updatedHiddenPosts = hiddenPosts.filter(item => item.postId !== postId);
            localStorage.setItem('hiddenPosts', JSON.stringify(updatedHiddenPosts));
            
            // Update UI
            if (postElement) {
                // Remove hidden overlay
                const overlay = postElement.querySelector('.hidden-overlay');
                if (overlay) overlay.remove();
                
                // Remove unhide button
                const unhideBtn = postElement.querySelector('.unhide-btn');
                if (unhideBtn) unhideBtn.remove();
                
                // Reset styles if not in moderation mode
                if (!document.body.classList.contains('moderate-mode')) {
                    postElement.style.opacity = '';
                    postElement.classList.remove('hidden-by-admin');
                } else {
                    // Update label if in moderation mode
                    const label = postElement.querySelector('.moderation-label');
                    if (label) {
                        label.textContent = label.textContent.replace('🚫 ', '');
                        if (label.textContent === 'Flagged Content') {
                            label.textContent = 'Previously Hidden';
                        }
                    }
                }
            }
            
            // Show confirmation
            Swal.fire({
                title: 'Post Unhidden',
                text: 'The post is now visible to all users.',
                icon: 'success',
                background: '#1a1a1a',
                color: '#FFFFFF',
                confirmButtonColor: '#3085d6',
                timer: 2000,
                timerProgressBar: true
            });
        }
    });
</script>
@endsection