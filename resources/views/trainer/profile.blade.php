@extends('layouts.app')

@section('title', 'Trainer Profile')

@section('content')
<div class="bg-[#EDEDED] min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Profile Header -->
        <div class="bg-gradient-to-r from-gray-800 to-black rounded-2xl shadow-xl overflow-hidden">
            <div class="p-8 flex flex-col md:flex-row gap-8 items-center text-white relative">
                <!-- Profile Picture -->
                <div class="relative">
                    <div class="h-32 w-32 md:h-40 md:w-40 rounded-full border-4 border-white overflow-hidden bg-white">
                        <img src="{{ $trainer->profile_image_url }}" 
                            alt="{{ $trainer->user->full_name }}" 
                            class="w-full h-full object-cover">
                    </div>
                    <div class="absolute bottom-0 right-0">
                        <span class="h-6 w-6 bg-green-500 rounded-full border-2 border-white"></span>
                    </div>
                </div>
                
                <!-- Profile Info (Centered) -->
                <div class="flex-1 text-center">
                    <h1 class="text-3xl font-bold">{{ $trainer->user->full_name }}</h1>
                    <p class="text-gray-300 mt-1 text-lg">
                        <span class="font-semibold">Specialization:</span> {{ $trainer->specialization }}
                    </p>
                    <p class="text-gray-300 mt-1">
                        <span class="font-semibold">Email:</span> {{ $trainer->user->email }}
                    </p>
                    <p class="text-gray-300 mt-1">
                        <span class="font-semibold">Instructor for:</span> {{ $trainer->instructor_for }}
                    </p>
                    <div class="mt-3 max-h-32 overflow-y-auto px-4">
                        <p class="text-gray-100">{{ $trainer->short_intro }}</p>
                    </div>
                </div>
                
                <!-- QR Code and Edit Button (Right aligned) -->
                <div class="flex flex-col items-center space-y-4">
                    <!-- QR Code button instead of displaying it directly -->
                    <a href="{{ route('user.qr') }}" class="px-6 py-3 bg-gray-800 text-white rounded-lg font-semibold hover:bg-gray-700 transition-colors flex items-center">
                        <i class="fas fa-qrcode mr-2"></i> View QR Code
                    </a>
                    
                    <!-- Edit Profile Button -->
                    <a href="{{ route('account.settings') }}" class="px-6 py-3 bg-gray-800 text-white rounded-lg font-semibold hover:bg-gray-700 transition-colors flex items-center">
                        <i class="fas fa-edit mr-2"></i> Edit Profile
                    </a>
                </div>
            </div>
            
            <!-- Profile Stats -->
            <div class="flex flex-wrap justify-center gap-4 p-4 bg-gray-900 bg-opacity-50">
                <div class="bg-gray-100 p-4 rounded-xl text-center min-w-[140px] shadow-md">
                    <h3 class="text-3xl font-bold text-gray-800">{{ $activeMembers }}</h3>
                    <p class="text-sm text-gray-600 font-medium">Active Members</p>
                </div>
                <div class="bg-gray-100 p-4 rounded-xl text-center min-w-[140px] shadow-md">
                    <h3 class="text-3xl font-bold text-gray-800">{{ count($upcomingSessions) }}</h3>
                    <p class="text-sm text-gray-600 font-medium">Upcoming Sessions</p>
                </div>
                <div class="bg-gray-100 p-4 rounded-xl text-center min-w-[140px] shadow-md">
                    <h3 class="text-3xl font-bold text-gray-800">{{ $newStudents ?? 0 }}</h3>
                    <p class="text-sm text-gray-600 font-medium">New Students</p>
                </div>
            </div>
        </div>
        
        <!-- Content Grid -->
        <div class="mt-8 grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Weekly Calendar -->
            <div class="lg:col-span-2 bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
                <div class="p-4 bg-gray-800 text-white border-b border-gray-700">
                    <h2 class="text-xl font-bold">Weekly Schedule</h2>
                    <p class="text-gray-300 text-sm">Your current training schedule</p>
                </div>
                <div class="p-4">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Day</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start Time</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">End Time</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                                    <tr class="{{ isset($weeklySchedule[$day]) ? 'bg-gray-50' : '' }}">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium {{ isset($weeklySchedule[$day]) ? 'text-gray-800' : 'text-gray-400' }}">
                                            {{ $day }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            {{ isset($weeklySchedule[$day]) ? $weeklySchedule[$day]['start'] : 'No Session' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            {{ isset($weeklySchedule[$day]) ? $weeklySchedule[$day]['end'] : '-' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">No schedule found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Upcoming Sessions -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
                <div class="p-4 bg-gray-800 text-white border-b border-gray-700">
                    <h2 class="text-xl font-bold">Upcoming Sessions</h2>
                    <p class="text-gray-300 text-sm">Your next 5 training sessions</p>
                </div>
                <div class="divide-y divide-gray-200">
                    @forelse($upcomingSessions as $session)
                        <div class="p-4 flex items-center gap-4">
                            <div class="bg-gray-200 text-gray-800 h-12 w-12 rounded-full flex items-center justify-center font-bold">
                                {{ substr($session['day'], 0, 3) }}
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-500">{{ $session['date'] }}</p>
                                <p class="font-semibold text-gray-800">{{ $session['start_time'] }} - {{ $session['end_time'] }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="p-4 text-center text-sm text-gray-500">No upcoming sessions found</div>
                    @endforelse
                </div>
            </div>
            
            <!-- Active Members -->
            <div class="lg:col-span-3 bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
                <div class="p-4 bg-gray-800 text-white border-b border-gray-700">
                    <h2 class="text-xl font-bold">Active Members</h2>
                    <p class="text-gray-300 text-sm">Members subscribed to your programs</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Member</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Program</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ends On</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($members as $member)
                                @foreach($member->subscriptions as $subscription)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="h-10 w-10 rounded-full overflow-hidden bg-gray-200">
                                                    <img src="{{ $member->profile_image ? asset($member->profile_image) : asset('assets/default-profile.jpg') }}" 
                                                        alt="{{ $member->full_name }}" 
                                                        class="h-10 w-10 object-cover">
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">{{ $member->full_name }}</div>
                                                    <div class="text-sm text-gray-500">{{ $member->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $subscription->type == 'gym' ? 'bg-gray-100 text-gray-800' : '' }}
                                                {{ $subscription->type == 'boxing' ? 'bg-red-100 text-red-800' : '' }}
                                                {{ $subscription->type == 'muay' ? 'bg-orange-100 text-orange-800' : '' }}
                                                {{ $subscription->type == 'jiu' ? 'bg-blue-100 text-blue-800' : '' }}">
                                                {{ ucfirst($subscription->type) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ ucfirst($subscription->plan) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $subscription->end_date ? date('M d, Y', strtotime($subscription->end_date)) : 'Ongoing' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <button class="text-gray-600 hover:text-gray-800" onclick="alert('Message feature coming soon!')">
                                                <i class="fas fa-envelope mr-1"></i> Message
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">No active members found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 