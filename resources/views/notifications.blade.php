@extends('layouts.app')

@section('content')
<!-- Add Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<!-- Notification Section -->
<div x-data="{ modalOpen: false, currentNotification: null }" class="container mx-auto px-4 py-8 pb-12">
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
        <div @click="modalOpen = true; currentNotification = { title: 'Boxing Tournament', time: '1 day ago', icon: 'bell', color: 'red', content: 'Upcoming boxing tournament registration is now open. Don\'t miss out on this exciting opportunity!', details: 'We are thrilled to announce our upcoming boxing tournament! Registration is now open for all skill levels. The tournament will be held on July 15th at the main gym. Entry fee is ₱500 which includes a tournament t-shirt and participation certificate. Winners will receive trophies and special prizes from our sponsors. Register before July 1st to secure your spot. Limited slots available!' }" class="bg-white rounded-lg shadow p-4 flex items-start space-x-4 hover:bg-gray-50 transition-colors duration-200 cursor-pointer">
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
        <div @click="modalOpen = true; currentNotification = { title: 'SOntukan Event', time: '2 days ago', icon: 'bell', color: 'red', content: 'Special SOntukan training session this weekend. Limited spots available!', details: 'We\'re hosting an exclusive SOntukan training session this weekend led by Master Juan Santos. This specialized Filipino martial arts training will focus on traditional techniques and their modern applications. The session will run from 9 AM to 3 PM on Saturday with a lunch break. Only 20 slots available to ensure personalized instruction. Secure your spot today by registering at the front desk. Fee: ₱1,200 including lunch and training materials.' }" class="bg-white rounded-lg shadow p-4 flex items-start space-x-4 hover:bg-gray-50 transition-colors duration-200 cursor-pointer">
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
        <div @click="modalOpen = true; currentNotification = { title: 'Tournament Update', time: '3 days ago', icon: 'bell', color: 'red', content: 'Important updates regarding the upcoming tournament schedule and rules.', details: 'Attention all tournament participants! There have been some important changes to the tournament schedule and rules. The event will now start at 10 AM instead of 8 AM. Weight-ins will be conducted the day before. Additionally, we\'ve updated the scoring system and protective gear requirements. Please review the complete rules on our website or pick up a printed copy at the front desk. If you have any questions, please contact our tournament coordinator at tournament@mtfc.ph.' }" class="bg-white rounded-lg shadow p-4 flex items-start space-x-4 hover:bg-gray-50 transition-colors duration-200 cursor-pointer">
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
        <div @click="modalOpen = true; currentNotification = { title: 'Skill Assessment Test', time: '4 days ago', icon: 'bell', color: 'red', content: 'Monthly skill assessment test results are now available. Check your progress!', details: 'The results of your monthly skill assessment are now available for review. You can check your scores and progress via your member dashboard or request a printed copy at the front desk. Your instructor has added personalized feedback and recommendations for improvement. Overall, there has been a 15% improvement in technique and 8% in strength compared to last month\'s assessment. Schedule a one-on-one session with your trainer to discuss your results in detail and create a targeted improvement plan.' }" class="bg-white rounded-lg shadow p-4 flex items-start space-x-4 hover:bg-gray-50 transition-colors duration-200 cursor-pointer">
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