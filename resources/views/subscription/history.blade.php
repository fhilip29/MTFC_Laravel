@extends('layouts.app')

@section('title', 'Subscription History')

@section('content')
<div class="min-h-screen bg-[#121212] py-8 px-4 md:px-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
            <h1 class="text-2xl font-bold text-white flex items-center gap-2">
                <i class="fas fa-history text-red-500"></i> My Subscriptions
            </h1>
            <a href="{{ route('profile') }}" class="inline-flex items-center bg-[#374151] hover:bg-[#4B5563] text-white px-4 py-2 rounded-lg transition-colors">
                <i class="fas fa-arrow-left mr-2"></i> Back to Profile
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-800 bg-opacity-80 text-green-100 px-4 py-3 rounded-lg mb-6 flex items-start">
                <i class="fas fa-check-circle mt-1 mr-3"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-800 bg-opacity-80 text-red-100 px-4 py-3 rounded-lg mb-6 flex items-start">
                <i class="fas fa-exclamation-circle mt-1 mr-3"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Subscription List -->
        <div class="bg-[#2d2d2d] hover-card rounded-xl shadow-lg p-4 md:p-6 mb-6">
            @if($subscriptions->isEmpty())
                <div class="py-10 text-center">
                    <i class="fas fa-scroll text-gray-500 text-5xl mb-3"></i>
                    <p class="text-gray-400 text-lg">You don't have any subscriptions yet.</p>
                    <a href="{{ route('pricing') }}" class="mt-4 inline-flex items-center bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-lg transition-colors text-sm font-medium">
                        <i class="fas fa-plus mr-2"></i> Get a Membership
                    </a>
                </div>
            @else
                <div class="overflow-x-auto -mx-4 md:mx-0">
                    <div class="min-w-[700px] px-4 md:px-0">
                        <table class="w-full">
                            <thead>
                                <tr class="text-left border-b border-gray-700">
                                    <th class="pb-3 md:pb-4 text-gray-400 font-medium text-xs md:text-sm">Type</th>
                                    <th class="pb-3 md:pb-4 text-gray-400 font-medium text-xs md:text-sm">Plan</th>
                                    <th class="pb-3 md:pb-4 text-gray-400 font-medium text-xs md:text-sm">Price</th>
                                    <th class="pb-3 md:pb-4 text-gray-400 font-medium text-xs md:text-sm">Start Date</th>
                                    <th class="pb-3 md:pb-4 text-gray-400 font-medium text-xs md:text-sm">End Date</th>
                                    <th class="pb-3 md:pb-4 text-gray-400 font-medium text-xs md:text-sm">Status</th>
                                    <th class="pb-3 md:pb-4 text-gray-400 font-medium text-xs md:text-sm">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($subscriptions as $subscription)
                                    <tr class="border-b border-gray-700 hover:bg-[#374151]">
                                        <td class="py-3 md:py-4 text-xs md:text-sm font-medium text-white">
                                            <span class="inline-flex px-2 py-1 rounded-full text-xs
                                                @if($subscription->type === 'gym') bg-green-800 text-green-200
                                                @elseif($subscription->type === 'boxing') bg-red-800 text-red-200
                                                @elseif($subscription->type === 'muay') bg-purple-800 text-purple-200
                                                @elseif($subscription->type === 'jiu') bg-blue-800 text-blue-200
                                                @endif
                                            ">
                                                {{ ucfirst($subscription->type) }}
                                            </span>
                                        </td>
                                        <td class="py-3 md:py-4 text-xs md:text-sm text-white">{{ ucfirst($subscription->plan) }}</td>
                                        <td class="py-3 md:py-4 text-xs md:text-sm text-red-500 font-medium">₱{{ number_format($subscription->price, 2) }}</td>
                                        <td class="py-3 md:py-4 text-xs md:text-sm text-gray-300">{{ $subscription->start_date->format('M d, Y') }}</td>
                                        <td class="py-3 md:py-4 text-xs md:text-sm text-gray-300">
                                            @if($subscription->end_date)
                                                {{ $subscription->end_date->format('M d, Y') }}
                                            @else
                                                <span class="text-gray-400">Per-session</span>
                                            @endif
                                        </td>
                                        <td class="py-3 md:py-4 text-xs md:text-sm">
                                            @if($subscription->isActive())
                                                <span class="inline-flex px-2 py-1 rounded-full text-xs bg-green-800 text-green-200">Active</span>
                                            @else
                                                <span class="inline-flex px-2 py-1 rounded-full text-xs bg-red-800 text-red-200">Inactive</span>
                                            @endif
                                        </td>
                                        <td class="py-3 md:py-4 text-xs md:text-sm">
                                            @if($subscription->isActive())
                                                <form action="{{ route('subscription.cancel', $subscription->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" onclick="return confirm('Are you sure you want to cancel this subscription?')" 
                                                        class="inline-flex items-center text-red-400 hover:text-red-300 transition">
                                                        <i class="fas fa-ban mr-1"></i> Cancel
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-gray-500"><i class="fas fa-check-circle mr-1"></i> Completed</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>

        <!-- Quick Actions -->
        <div class="mt-6 flex flex-col sm:flex-row gap-3 justify-between">
            <a href="{{ route('profile') }}" class="bg-[#374151] text-white py-2 px-4 rounded-lg hover:bg-gray-600 transition duration-200 flex items-center justify-center text-sm md:text-base">
                <i class="fas fa-user mr-2"></i> Back to Profile
            </a>
            <a href="{{ route('pricing') }}" class="bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 transition duration-200 flex items-center justify-center text-sm md:text-base">
                <i class="fas fa-shopping-cart mr-2"></i> Browse Memberships
            </a>
        </div>
    </div>
</div>

<style>
    /* Card hover effects */
    .hover-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .hover-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px rgba(0,0,0,0.3);
    }
</style>
@endsection 