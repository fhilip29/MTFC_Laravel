@extends('layouts.app')

@section('title', 'Subscription History')

@section('content')
<div class="min-h-screen bg-gray-50 py-8 px-4 md:px-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
            <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-history text-red-600"></i> My Subscriptions
            </h1>
            <a href="{{ route('user.messages.compose', ['admin' => true]) }}" class="inline-flex items-center bg-gray-800 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors">
                <i class="fas fa-envelope mr-2"></i> Message Admin Support
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-start">
                <i class="fas fa-check-circle mt-1 mr-3"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6 flex items-start">
                <i class="fas fa-exclamation-circle mt-1 mr-3"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Subscription List -->
        <div class="bg-white rounded-xl shadow-lg p-4 md:p-6 mb-6">
            @if($subscriptions->isEmpty())
                <div class="py-10 text-center">
                    <i class="fas fa-scroll text-gray-400 text-5xl mb-3"></i>
                    <p class="text-gray-600 text-lg">You don't have any subscriptions yet.</p>
                    <a href="{{ route('pricing.gym') }}" class="mt-4 inline-flex items-center bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-lg transition-colors text-sm font-medium">
                        <i class="fas fa-plus mr-2"></i> Get a Membership
                    </a>
                </div>
            @else
                <div class="overflow-x-auto -mx-4 md:mx-0">
                    <div class="min-w-[700px] px-4 md:px-0">
                        <table class="w-full">
                            <thead>
                                <tr class="text-left border-b border-gray-200">
                                    <th class="pb-3 md:pb-4 text-gray-500 font-medium text-xs md:text-sm">Type</th>
                                    <th class="pb-3 md:pb-4 text-gray-500 font-medium text-xs md:text-sm">Plan</th>
                                    <th class="pb-3 md:pb-4 text-gray-500 font-medium text-xs md:text-sm">Price</th>
                                    <th class="pb-3 md:pb-4 text-gray-500 font-medium text-xs md:text-sm">Start Date</th>
                                    <th class="pb-3 md:pb-4 text-gray-500 font-medium text-xs md:text-sm">End Date</th>
                                    <th class="pb-3 md:pb-4 text-gray-500 font-medium text-xs md:text-sm">Status</th>
                                    <th class="pb-3 md:pb-4 text-gray-500 font-medium text-xs md:text-sm">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($subscriptions as $subscription)
                                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                                        <td class="py-3 md:py-4 text-xs md:text-sm font-medium text-gray-900">
                                            <span class="inline-flex px-2 py-1 rounded-full text-xs
                                                @if($subscription->type === 'gym') bg-green-100 text-green-800
                                                @elseif($subscription->type === 'boxing') bg-red-100 text-red-800
                                                @elseif($subscription->type === 'muay') bg-purple-100 text-purple-800
                                                @elseif($subscription->type === 'jiu') bg-blue-100 text-blue-800
                                                @endif
                                            ">
                                                {{ ucfirst($subscription->type) }}
                                            </span>
                                        </td>
                                        <td class="py-3 md:py-4 text-xs md:text-sm text-gray-900">{{ ucfirst($subscription->plan) }}</td>
                                        <td class="py-3 md:py-4 text-xs md:text-sm text-red-600 font-medium">₱{{ number_format($subscription->price, 2) }}</td>
                                        <td class="py-3 md:py-4 text-xs md:text-sm text-gray-600">{{ $subscription->start_date->format('M d, Y') }}</td>
                                        <td class="py-3 md:py-4 text-xs md:text-sm text-gray-600">
                                            @if($subscription->end_date)
                                                {{ $subscription->end_date->format('M d, Y') }}
                                            @else
                                                <span class="text-gray-400">Per-session</span>
                                            @endif
                                        </td>
                                        <td class="py-3 md:py-4 text-xs md:text-sm">
                                            @if($subscription->isActive())
                                                <span class="inline-flex px-2 py-1 rounded-full text-xs bg-green-100 text-green-800">Active</span>
                                            @else
                                                <span class="inline-flex px-2 py-1 rounded-full text-xs bg-red-100 text-red-800">Inactive</span>
                                            @endif
                                        </td>
                                        <td class="py-3 md:py-4 text-xs md:text-sm">
                                            @if($subscription->isActive())
                                                <form action="{{ route('subscription.cancel', $subscription->id) }}" method="POST" id="cancelForm{{ $subscription->id }}">
                                                    @csrf
                                                    <button type="button" onclick="confirmCancel({{ $subscription->id }})" 
                                                        class="inline-flex items-center text-red-600 hover:text-red-800 transition">
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
            <a href="{{ route('profile') }}" class="bg-gray-100 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-200 transition duration-200 flex items-center justify-center text-sm md:text-base">
                <i class="fas fa-user mr-2"></i> Back to Profile
            </a>
            <a href="{{ route('pricing.gym') }}" class="bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 transition duration-200 flex items-center justify-center text-sm md:text-base">
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
        box-shadow: 0 10px 15px rgba(0,0,0,0.1);
    }
</style>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function confirmCancel(subscriptionId) {
        Swal.fire({
            title: 'Cancel Subscription?',
            text: 'Are you sure you want to cancel this subscription? This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, cancel it!',
            cancelButtonText: 'No, keep it'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('cancelForm' + subscriptionId).submit();
            }
        });
    }
</script>
@endsection 