@extends('layouts.app')

@section('content')
<!-- Add Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<!-- Notification Section -->
<div x-data="{ modalOpen: false, currentNotification: null }" class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8 text-gray-800">Notifications</h1>
    
    <div class="space-y-4">
        <!-- Muay Thai Notification -->
        <div @click="modalOpen = true; currentNotification = { title: 'Muay Thai', time: '2 hours ago', icon: 'bell', color: 'red', content: 'New Muay Thai class schedule available. Check out the updated timings and book your spot!', details: 'Join our exciting Muay Thai classes! We\'ve updated our schedule to accommodate more students. Classes are now available in the morning (6 AM - 8 AM), afternoon (2 PM - 4 PM), and evening (7 PM - 9 PM) slots. Book your preferred timing through the member portal or contact the front desk. Don\'t forget to bring your gear!' }" class="bg-white rounded-lg shadow p-4 flex items-start space-x-4 hover:bg-gray-50 transition-colors duration-200 cursor-pointer">
            <div class="flex-shrink-0">
                <i class="fas fa-bell text-red-500 text-xl"></i>
            </div>
            <div class="flex-1">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800">Muay Thai</h3>
                    <span class="text-sm text-gray-500">2 hours ago</span>
                </div>
                <p class="text-gray-600 mt-1">New Muay Thai class schedule available. Check out the updated timings and book your spot!</p>
            </div>
        </div>

        <!-- Boxing Tournament Notification -->
        <div class="bg-white rounded-lg shadow p-4 flex items-start space-x-4 hover:bg-gray-50 transition-colors duration-200">
            <div class="flex-shrink-0">
                <i class="fas fa-bell text-red-500 text-xl"></i>
            </div>
            <div class="flex-1">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800">Boxing Tournament</h3>
                    <span class="text-sm text-gray-500">1 day ago</span>
                </div>
                <p class="text-gray-600 mt-1">Upcoming boxing tournament registration is now open. Don't miss out on this exciting opportunity!</p>
            </div>
        </div>

        <!-- SOntukan Event Notification -->
        <div class="bg-white rounded-lg shadow p-4 flex items-start space-x-4 hover:bg-gray-50 transition-colors duration-200">
            <div class="flex-shrink-0">
                <i class="fas fa-bell text-red-500 text-xl"></i>
            </div>
            <div class="flex-1">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800">SOntukan Event</h3>
                    <span class="text-sm text-gray-500">2 days ago</span>
                </div>
                <p class="text-gray-600 mt-1">Special SOntukan training session this weekend. Limited spots available!</p>
            </div>
        </div>

        <!-- Tournament Update Notification -->
        <div class="bg-white rounded-lg shadow p-4 flex items-start space-x-4 hover:bg-gray-50 transition-colors duration-200">
            <div class="flex-shrink-0">
                <i class="fas fa-bell text-red-500 text-xl"></i>
            </div>
            <div class="flex-1">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800">Tournament Update</h3>
                    <span class="text-sm text-gray-500">3 days ago</span>
                </div>
                <p class="text-gray-600 mt-1">Important updates regarding the upcoming tournament schedule and rules.</p>
            </div>
        </div>

        <!-- Test Notification -->
        <div class="bg-white rounded-lg shadow p-4 flex items-start space-x-4 hover:bg-gray-50 transition-colors duration-200">
            <div class="flex-shrink-0">
                <i class="fas fa-bell text-red-500 text-xl"></i>
            </div>
            <div class="flex-1">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800">Skill Assessment Test</h3>
                    <span class="text-sm text-gray-500">4 days ago</span>
                </div>
                <p class="text-gray-600 mt-1">Monthly skill assessment test results are now available. Check your progress!</p>
            </div>
        </div>
    </div>

    <!-- Notification Modal -->
    <div x-show="modalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div x-show="modalOpen" @click="modalOpen = false" class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <!-- Modal panel -->
            <div x-show="modalOpen" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i :class="`fas fa-${currentNotification?.icon} text-${currentNotification?.color}-500 text-xl`"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" x-text="currentNotification?.title"></h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500" x-text="currentNotification?.details"></p>
                            </div>
                            <div class="mt-4 text-right">
                                <span class="text-sm text-gray-500" x-text="currentNotification?.time"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button @click="modalOpen = false" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-500 text-base font-medium text-white hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection