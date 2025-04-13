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
        background-color: rgba(0, 0, 0, 0.3); /* Reduced opacity */
        backdrop-filter: blur(2px); /* Reduced blur */
    }
    /* Improved file input styling */
    .file-input-button {
        background-color: #4285F4;
        color: white;
        border-radius: 4px;
        padding: 8px 16px;
        font-size: 14px;
        cursor: pointer;
        transition: background-color 0.2s;
    }
    .file-input-button:hover {
        background-color: #3367D6;
    }
    /* Create button styling */
    .create-button {
        background-color: #f0f0f0;
        color: #1a73e8;
        border-radius: 4px;
        padding: 8px 20px;
        font-size: 14px;
        font-weight: 500;
        transition: background-color 0.2s;
    }
    .create-button:hover {
        background-color: #e0e0e0;
    }
    /* Modal visibility animation */
    .modal-enter {
        opacity: 0;
    }
    .modal-enter-active {
        opacity: 1;
        transition: opacity 200ms;
    }
    .modal-exit {
        opacity: 1;
    }
    .modal-exit-active {
        opacity: 0;
        transition: opacity 200ms;
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
            <a href="#" class="flex items-center space-x-3 text-white hover:text-red-500 transition-colors">
                <i class="fas fa-fire text-xl"></i>
                <span class="font-medium">Popular</span>
            </a>
            <a href="#" class="flex items-center space-x-3 text-white hover:text-red-500 transition-colors">
                <i class="fas fa-compass text-xl"></i>
                <span class="font-medium">Explore</span>
            </a>
            <a href="#" class="flex items-center space-x-3 text-white hover:text-red-500 transition-colors">
                <i class="fas fa-list text-xl"></i>
                <span class="font-medium">All</span>
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 p-6">
        <!-- Top Bar with Search and Create Post -->
        <div class="flex flex-col md:flex-row items-center justify-between gap-4 mb-6">
            <!-- Search Bar -->
            <div class="relative w-full md:w-2/3">
                <input type="text" placeholder="Search posts..." class="w-full bg-[#2d2d2d] text-white rounded-lg py-3 px-4 pl-10 focus:outline-none focus:ring-2 focus:ring-red-500">
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            </div>
            
            <!-- Create Post Button -->
            <div class="w-full md:w-auto" x-data="{ isOpen: false }">
                <button @click="isOpen = true" class="w-full md:w-auto bg-red-600 text-white px-6 py-3 rounded-lg hover:bg-red-700 transition-colors flex items-center justify-center md:justify-start space-x-2">
                    <i class="fas fa-plus"></i>
                    <span>Create Post</span>
                </button>

                <!-- Updated Create Post Modal with Close Button -->
                <div 
                    x-show="isOpen" 
                    x-transition:enter="modal-enter"
                    x-transition:enter-start="modal-enter"
                    x-transition:enter-end="modal-enter-active"
                    x-transition:leave="modal-exit"
                    x-transition:leave-start="modal-exit"
                    x-transition:leave-end="modal-exit-active"
                    class="fixed inset-0 z-50 overflow-y-auto" 
                    style="display: none;"
                >
                    <div class="modal-backdrop fixed inset-0" @click="isOpen = false"></div>
                    <div class="flex items-center justify-center min-h-screen p-4">
                        <div class="bg-white w-full max-w-md rounded-lg shadow-lg z-50 relative" @click.stop>
                            <div class="p-6">
                                <div class="flex justify-between items-center mb-4">
                                    <h2 class="text-xl font-semibold text-gray-800">Create Post</h2>
                                    <button @click="isOpen = false" class="text-gray-500 hover:text-gray-700">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <form class="space-y-6">
                                    <!-- Post content textarea -->
                                    <textarea 
                                        placeholder="What's on your mind..." 
                                        class="w-full h-40 bg-white text-gray-800 border border-gray-300 rounded-lg p-4 focus:outline-none focus:ring-1 focus:ring-blue-500 resize-none"
                                    ></textarea>
                                    
                                    <!-- Attach image section -->
                                    <div class="flex flex-col space-y-2">
                                        <label class="text-gray-800 font-medium text-sm">Attach Image</label>
                                        <div class="flex justify-end">
                                            <input type="file" id="image-upload" class="hidden" accept="image/*">
                                            <label for="image-upload" class="file-input-button inline-flex items-center justify-center">
                                                Choose File
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <!-- Create button -->
                                    <div class="flex justify-end pt-2">
                                        <button type="button" @click="isOpen = false" class="mr-2 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100">
                                            Cancel
                                        </button>
                                        <button type="submit" class="create-button">
                                            Create
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Posts -->
        <div class="space-y-6">
            <!-- Post 1 -->
            <div class="bg-[#2d2d2d] rounded-xl p-6 post-card">
                <div class="flex items-start space-x-4">
                    <img src="{{ asset('assets/MTFC_LOGO.PNG') }}" alt="User" class="w-10 h-10 rounded-full">
                    <div class="flex-1">
                        <div class="flex items-center space-x-2">
                            <h3 class="font-semibold">Mike</h3>
                            <span class="text-gray-400 text-sm">2 hours ago</span>
                        </div>
                        <p class="mt-2">Hi everyone! Just joined ActiveGym today. Super excited to get started! Any tips for a newbie?</p>
                        <div class="mt-4 flex items-center space-x-4">
                            <button class="vote-button flex items-center space-x-1 text-gray-400 hover:text-red-500">
                                <i class="fas fa-arrow-up"></i>
                                <span>6.2k</span>
                            </button>
                            <button class="vote-button flex items-center space-x-1 text-gray-400 hover:text-red-500">
                                <i class="fas fa-comment"></i>
                                <span>584</span>
                            </button>
                            <button class="vote-button flex items-center space-x-1 text-gray-400 hover:text-red-500">
                                <i class="fas fa-share"></i>
                                <span>Share</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Post 2 -->
            <div class="bg-[#2d2d2d] rounded-xl p-6 post-card">
                <div class="flex items-start space-x-4">
                    <img src="{{ asset('assets/MTFC_LOGO.PNG') }}" alt="User" class="w-10 h-10 rounded-full">
                    <div class="flex-1">
                        <div class="flex items-center space-x-2">
                            <h3 class="font-semibold">Sarah</h3>
                            <span class="text-gray-400 text-sm">5 hours ago</span>
                        </div>
                        <p class="mt-2">Welcome, Mike! I'd recommend checking out the personalized plans feature - it helped me a lot when I was starting out. Also, don't miss the Zumba classes on Tuesdays, they're a blast!</p>
                        <div class="mt-4 flex items-center space-x-4">
                            <button class="vote-button flex items-center space-x-1 text-gray-400 hover:text-red-500">
                                <i class="fas fa-arrow-up"></i>
                                <span>538</span>
                            </button>
                            <button class="vote-button flex items-center space-x-1 text-gray-400 hover:text-red-500">
                                <i class="fas fa-comment"></i>
                                <span>42</span>
                            </button>
                            <button class="vote-button flex items-center space-x-1 text-gray-400 hover:text-red-500">
                                <i class="fas fa-share"></i>
                                <span>Share</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection