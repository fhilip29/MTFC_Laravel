@extends('layouts.app')

@section('title', 'Add Payment Method')

@section('content')
<div class="min-h-screen bg-[#121212] text-white py-8 px-4 sm:px-6 lg:px-8 flex items-center justify-center bg-cover bg-center bg-no-repeat" style="background-image: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('{{ asset('assets/gym-bg.jpg') }}');">
    <div class="max-w-md mx-auto bg-[#2d2d2d] rounded-xl shadow-lg overflow-hidden">
        <!-- Header Section -->
        <div class="p-6 border-b border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold">Choose how to pay</h2>
                <a href="{{ route('profile') }}" class="text-gray-400 hover:text-white">
                    <i class="fas fa-times"></i>
                </a>
            </div>
            <p class="text-sm text-gray-400">Your payment is encrypted and you can change how you pay anytime.</p>
            <p class="text-xs text-gray-400 mt-1">Secure for peace of mind.</p>
        </div>

        <!-- Payment Methods Section -->
        <div class="p-6 space-y-4">
            <!-- Credit/Debit Card Option -->
            <button onclick="window.location.href='#'" class="w-full bg-[#1e1e1e] hover:bg-[#252525] transition-colors duration-200 p-4 rounded-lg flex items-center justify-between group">
                <div class="flex items-center space-x-4">
                    <div class="flex space-x-2">
                        <img src="https://raw.githubusercontent.com/danielmonteiro/credit-card-icons/master/assets/visa.svg" alt="Visa" class="h-6">
                        <img src="https://raw.githubusercontent.com/danielmonteiro/credit-card-icons/master/assets/mastercard.svg" alt="Mastercard" class="h-6">
                    </div>
                    <span class="font-medium">Credit or Debit Card</span>
                </div>
                <i class="fas fa-chevron-right text-gray-400 group-hover:text-white transition-colors duration-200"></i>
            </button>

            <!-- Digital Wallet Option -->
            <button onclick="window.location.href='#'" class="w-full bg-[#1e1e1e] hover:bg-[#252525] transition-colors duration-200 p-4 rounded-lg flex items-center justify-between group">
                <div class="flex items-center space-x-4">
                    <div class="flex space-x-2">
                        <img src="https://www.gcash.com/wp-content/uploads/2019/05/GCash-Logo-PNG-1024x1024.png" alt="GCash" class="h-6 w-6 object-contain">
                    </div>
                    <span class="font-medium">Digital Wallet</span>
                </div>
                <i class="fas fa-chevron-right text-gray-400 group-hover:text-white transition-colors duration-200"></i>
            </button>
        </div>

        <!-- Information Section -->
        <div class="p-6 bg-[#1e1e1e] border-t border-gray-700">
            <div class="flex items-center space-x-2 text-sm text-gray-400">
                <i class="fas fa-lock"></i>
                <span>Payments are secure and encrypted</span>
            </div>
        </div>
    </div>
</div>
@endsection