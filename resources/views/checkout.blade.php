@extends('layouts.app')

@section('content')
<!-- Add Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="min-h-screen bg-gray-100 py-8">
    <div class="container mx-auto px-4">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Checkout</h1>

        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Delivery Address Form -->
            <div class="lg:w-2/3 bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-semibold mb-6">Delivery Address</h2>
                <form class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                            <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                            <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Street Address</label>
                        <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Barangay</label>
                            <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                            <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Postal Code</label>
                            <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                        <input type="tel" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500">
                    </div>
                </form>
            </div>

            <!-- Order Summary -->
            <div class="lg:w-1/3 bg-white rounded-lg shadow-lg p-6 h-fit">
                <h2 class="text-xl font-semibold mb-6">Order Summary</h2>
                <div class="space-y-4 mb-6">
                    <!-- Sample Order Item -->
                    <div class="flex items-center justify-between pb-4 border-b border-gray-200">
                        <div class="flex items-center space-x-4">
                            <img src="{{ asset('assets/Product2_MTFC.jpg') }}" alt="Product" class="w-16 h-16 object-cover rounded">
                            <div>
                                <h3 class="font-medium">Dumbbells Set</h3>
                                <p class="text-sm text-gray-500">5-50 lbs</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3">
                            <button class="text-gray-500 hover:text-gray-700">
                                <i class="fas fa-minus"></i>
                            </button>
                            <span class="w-8 text-center">1</span>
                            <button class="text-gray-500 hover:text-gray-700">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Order Total -->
                <div class="space-y-2 mb-6">
                    <div class="flex justify-between text-sm">
                        <span>Subtotal</span>
                        <span>₱2,000.00</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span>Shipping</span>
                        <span>₱150.00</span>
                    </div>
                    <div class="flex justify-between font-semibold text-lg pt-4 border-t border-gray-200">
                        <span>Total</span>
                        <span>₱2,150.00</span>
                    </div>
                </div>

                <!-- Place Order Button -->
                <button class="w-full bg-red-600 text-white py-3 rounded-md font-semibold hover:bg-red-700 transition-colors duration-200">
                    Place Order
                </button>
            </div>
        </div>
    </div>
</div>
@endsection