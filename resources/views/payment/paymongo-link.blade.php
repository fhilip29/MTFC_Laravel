@extends('layouts.app')

@section('title', 'Online Payment Link')

@section('content')
<div class="min-h-screen bg-[#121212] text-white py-8 px-4 sm:px-6 lg:px-8 flex items-center justify-center bg-cover bg-center bg-no-repeat" style="background-image: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('{{ asset('assets/gym-bg.jpg') }}');">
    <div class="max-w-md w-full mx-auto bg-[#2d2d2d] rounded-xl shadow-lg overflow-hidden">
        <!-- Header Section -->
        <div class="p-6 border-b border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold">Online Payment</h2>
                <a href="{{ $type === 'product' ? route('shop') : route('pricing.show', $type ?? 'gym') }}" class="text-gray-400 hover:text-white">
                    <i class="fas fa-times"></i>
                </a>
            </div>
            <p class="text-sm text-gray-400">Complete your payment by clicking the link below or copying it to your browser.</p>
        </div>

        <!-- Link Section -->
        <div class="p-6 space-y-6">
            <!-- Payment Details -->
            <div class="w-full p-4 bg-[#1e1e1e] rounded-lg">
                <h3 class="font-medium mb-2">Order Summary</h3>
                <div class="flex justify-between text-sm mb-1">
                    <span class="text-gray-400">Subscription:</span>
                    <span id="plan-display">{{ ucfirst($plan) }}</span>
                </div>
                <div class="flex justify-between text-sm mb-1">
                    <span class="text-gray-400">Type:</span>
                    <span id="type-display">{{ ucfirst($type) }}</span>
                </div>
                <div class="flex justify-between font-medium mt-2 pt-2 border-t border-gray-700">
                    <span>Total:</span>
                    <span id="amount-display">₱{{ number_format($amount, 2) }}</span>
                </div>
                <div class="mt-3 text-xs text-center text-gray-400">
                    Reference: {{ $reference }}
                </div>
            </div>
            
            <!-- Payment Link -->
            <div class="w-full">
                <label class="block text-sm font-medium text-gray-400 mb-2">Payment Link</label>
                <div class="flex">
                    <input id="payment-link" type="text" value="{{ $checkout_url }}" class="flex-grow px-3 py-2 bg-[#1e1e1e] border border-[#4B5563] text-white rounded-l-md focus:outline-none" readonly>
                    <button onclick="copyPaymentLink()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-r-md transition">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
                <p id="copy-status" class="mt-2 text-sm text-green-500 hidden">Link copied to clipboard!</p>
            </div>
            
            <!-- Pay Now Button -->
            <div class="text-center mt-6">
                <a href="{{ $checkout_url }}" target="_blank" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-lg transition">
                    <i class="fas fa-external-link-alt mr-2"></i> Open Payment Page
                </a>
            </div>
            
            <!-- Instructions -->
            <div class="w-full text-center">
                <p class="text-yellow-400 font-medium">Important Instructions:</p>
                <ol class="text-sm text-gray-300 list-decimal list-inside text-left mt-2 space-y-1">
                    <li>Click "Open Payment Page" or copy the link above</li>
                    <li>Complete the payment on the secure PayMongo page</li>
                    <li>After successful payment, you'll be redirected back to our site</li>
                    <li>Your subscription will be activated automatically</li>
                </ol>
            </div>
        </div>

        <!-- Information Section -->
        <div class="p-6 bg-[#1e1e1e] border-t border-gray-700">
            <div class="flex items-center justify-center space-x-2 text-sm text-gray-400">
                <a href="{{ route('profile') }}" class="px-4 py-2 bg-gray-700 text-white rounded hover:bg-gray-600 transition">
                    Cancel
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    function copyPaymentLink() {
        const linkInput = document.getElementById('payment-link');
        linkInput.select();
        linkInput.setSelectionRange(0, 99999); /* For mobile devices */
        
        navigator.clipboard.writeText(linkInput.value).then(() => {
            // Show success message
            const copyStatus = document.getElementById('copy-status');
            copyStatus.classList.remove('hidden');
            
            // Hide after 3 seconds
            setTimeout(() => {
                copyStatus.classList.add('hidden');
            }, 3000);
        }).catch(err => {
            console.error('Could not copy text: ', err);
            
            // Fallback
            document.execCommand('copy');
            
            // Show success message
            const copyStatus = document.getElementById('copy-status');
            copyStatus.classList.remove('hidden');
            
            // Hide after 3 seconds
            setTimeout(() => {
                copyStatus.classList.add('hidden');
            }, 3000);
        });
    }
</script>
@endsection 