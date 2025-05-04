@extends('layouts.app')

@section('title', 'Cash Payment QR Code')

@section('content')
<div class="min-h-screen bg-[#121212] text-white py-8 px-4 sm:px-6 lg:px-8 flex items-center justify-center bg-cover bg-center bg-no-repeat" style="background-image: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('{{ asset('assets/gym-bg.jpg') }}');">
    <div class="max-w-md w-full mx-auto bg-[#2d2d2d] rounded-xl shadow-lg overflow-hidden">
        <!-- Header Section -->
        <div class="p-6 border-b border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold">Cash Payment</h2>
                <a href="{{ route('pricing') }}" class="text-gray-400 hover:text-white">
                    <i class="fas fa-times"></i>
                </a>
            </div>
            <p class="text-sm text-gray-400">Show this QR code to the staff at the counter to complete your payment.</p>
        </div>

        <!-- QR Code Section -->
        <div class="p-6 space-y-6 flex flex-col items-center">
            <!-- QR Code -->
            <div class="bg-white p-4 rounded-lg">
                <div id="qrcode" class="w-64 h-64 flex items-center justify-center">
                    <div class="text-gray-500">Loading QR Code...</div>
                </div>
            </div>

            <!-- Payment Details -->
            <div class="w-full p-4 bg-[#1e1e1e] rounded-lg">
                <h3 class="font-medium mb-2">Order Summary</h3>
                <div class="flex justify-between text-sm mb-1">
                    <span class="text-gray-400">Subscription:</span>
                    <span id="plan-display">{{ ucfirst($paymentData['plan']) }}</span>
                </div>
                <div class="flex justify-between text-sm mb-1">
                    <span class="text-gray-400">Type:</span>
                    <span id="type-display">{{ ucfirst($paymentData['type']) }}</span>
                </div>
                <div class="flex justify-between font-medium mt-2 pt-2 border-t border-gray-700">
                    <span>Total:</span>
                    <span id="amount-display">₱{{ number_format($paymentData['amount'], 2) }}</span>
                </div>
                <div class="mt-3 text-xs text-center text-gray-400">
                    Reference: {{ $paymentData['reference'] }}
                </div>
            </div>
            
            <!-- Instructions -->
            <div class="w-full text-center">
                <p class="text-yellow-400 font-medium">Important Instructions:</p>
                <ol class="text-sm text-gray-300 list-decimal list-inside text-left mt-2 space-y-1">
                    <li>Present this QR code to our staff at the counter</li>
                    <li>Make the payment as shown in the order summary</li>
                    <li>The staff will scan your code to activate your subscription</li>
                    <li>You'll receive a confirmation once payment is processed</li>
                </ol>
            </div>
        </div>

        <!-- Information Section -->
        <div class="p-6 bg-[#1e1e1e] border-t border-gray-700">
            <div class="flex items-center justify-center space-x-2 text-sm text-gray-400">
                <a href="{{ route('profile') }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                    Go to My Profile
                </a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Wait a moment to ensure QR library is loaded
        setTimeout(function() {
            try {
                // Generate QR code
                const qrContainer = document.getElementById('qrcode');
                
                // Clear the loading text
                qrContainer.innerHTML = '';
                
                // Create QR code with the data
                const qrData = {!! $qrContent !!}; // Use proper JSON encoding
                
                new QRCode(qrContainer, {
                    text: JSON.stringify(qrData),
                    width: 256,
                    height: 256,
                    colorDark: "#000000",
                    colorLight: "#ffffff",
                    correctLevel: QRCode.CorrectLevel.H
                });
                
                console.log("QR code generated successfully");
            } catch (error) {
                console.error("Error generating QR code:", error);
                document.getElementById('qrcode').innerHTML = '<div class="text-red-500">Error generating QR. Please refresh the page.</div>';
            }
        }, 500);
    });
</script>
@endsection 