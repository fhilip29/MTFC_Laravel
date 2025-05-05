@extends('layouts.app')

@section('title', 'Payment Method')

@section('content')
<div class="min-h-screen bg-[#121212] text-white py-8 px-4 sm:px-6 lg:px-8 flex items-center justify-center bg-cover bg-center bg-no-repeat" style="background-image: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('{{ asset('assets/gym-bg.jpg') }}');">
    <div class="max-w-md w-full mx-auto bg-[#2d2d2d] rounded-xl shadow-lg overflow-hidden">
        <!-- Header Section -->
        <div class="p-6 border-b border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold">Choose Payment Method</h2>
                <a href="{{ route('pricing') }}" class="text-gray-400 hover:text-white">
                    <i class="fas fa-times"></i>
                </a>
            </div>
            <p class="text-sm text-gray-400">Select how you would like to pay for your subscription.</p>
            
            <!-- Order Summary -->
            <div class="mt-4 p-4 bg-[#1e1e1e] rounded-lg">
                <h3 class="font-medium mb-2">Order Summary</h3>
                <div class="flex justify-between text-sm mb-1">
                    <span class="text-gray-400">Subscription:</span>
                    <span id="plan-display">{{ request()->query('plan') }}</span>
                </div>
                <div class="flex justify-between text-sm mb-1">
                    <span class="text-gray-400">Type:</span>
                    <span id="type-display">{{ request()->query('type') }}</span>
                </div>
                <div id="order-items" class="mt-2 pt-2 border-t border-gray-700">
                    <!-- Order items will be displayed here -->
                </div>
                <div class="flex justify-between font-medium mt-2 pt-2 border-t border-gray-700">
                    <span>Total:</span>
                    <span id="amount-display">₱{{ number_format(request()->query('amount', 0), 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Payment Methods Section -->
        <div class="p-6 space-y-4">
            <div class="space-y-4">
                <!-- Cash Option -->
                <form id="cashForm" action="{{ route('payment.cash.qr') }}" method="POST">
                    @csrf
                    <input type="hidden" name="type" value="{{ request()->query('type') }}">
                    <input type="hidden" name="plan" value="{{ request()->query('plan') }}">
                    <input type="hidden" name="amount" value="{{ request()->query('amount') }}">
                    <input type="hidden" name="waiver_accepted" value="{{ request()->query('waiver_accepted', 0) }}">
                    <input type="hidden" name="payment_method" value="cash">
                    <input type="hidden" name="payment_status" value="pending">
                    <input type="hidden" name="order_data" id="order-data-cash">
                    
                    <button type="submit" class="w-full bg-[#1e1e1e] hover:bg-[#252525] transition-colors duration-200 p-4 rounded-lg flex items-center justify-between group">
                        <div class="flex items-center space-x-4">
                            <div class="w-8 h-8 flex items-center justify-center bg-gray-700 rounded-full">
                                <i class="fas fa-money-bill text-green-400"></i>
                            </div>
                            <div>
                                <span class="font-medium block">Cash Payment</span>
                                <span class="text-sm text-gray-400">Pay at the counter</span>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400 group-hover:text-white transition-colors duration-200"></i>
                    </button>
                </form>
                
                <!-- PayMongo Option -->
                <form id="paymongoForm" action="{{ route('payment.process') }}" method="POST">
                    @csrf
                    <input type="hidden" name="type" value="{{ request()->query('type') }}">
                    <input type="hidden" name="plan" value="{{ request()->query('plan') }}">
                    <input type="hidden" name="amount" value="{{ request()->query('amount') }}">
                    <input type="hidden" name="waiver_accepted" value="{{ request()->query('waiver_accepted', 0) }}">
                    <input type="hidden" name="payment_method" value="paymongo">
                    <input type="hidden" name="billing_name" value="{{ auth()->user()->name ?? 'Guest User' }}">
                    <input type="hidden" name="billing_email" value="{{ auth()->user()->email ?? '' }}">
                    <input type="hidden" name="billing_phone" value="{{ auth()->user()->mobile_number ?? '' }}">
                    <input type="hidden" name="order_data" id="order-data-paymongo">
                    
                    <button type="submit" class="w-full bg-[#1e1e1e] hover:bg-[#252525] transition-colors duration-200 p-4 rounded-lg flex items-center justify-between group">
                        <div class="flex items-center space-x-4">
                            <div class="w-8 h-8 flex items-center justify-center bg-blue-700 rounded-full">
                                <i class="fas fa-credit-card text-white"></i>
                            </div>
                            <div>
                                <span class="font-medium block">Online Payment</span>
                                <span class="text-sm text-gray-400">Pay with PayMongo (Card, GCash, etc.)</span>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400 group-hover:text-white transition-colors duration-200"></i>
                    </button>
                </form>
            </div>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get order data from sessionStorage
    const orderDataStr = sessionStorage.getItem('orderData');
    if (orderDataStr) {
        const orderData = JSON.parse(orderDataStr);
        
        // Set hidden form fields with order data
        document.getElementById('order-data-cash').value = orderDataStr;
        document.getElementById('order-data-paymongo').value = orderDataStr;
        
        // Show order items in summary
        const orderItemsContainer = document.getElementById('order-items');
        if (orderItemsContainer && orderData.items && orderData.items.length > 0) {
            // Clear existing content
            orderItemsContainer.innerHTML = '';
            
            // Add items heading
            const heading = document.createElement('div');
            heading.className = 'text-sm text-gray-400 mb-2';
            heading.textContent = 'Items:';
            orderItemsContainer.appendChild(heading);
            
            // Add each item
            orderData.items.forEach(item => {
                const itemElement = document.createElement('div');
                itemElement.className = 'flex justify-between text-sm mb-1 pl-2';
                
                const nameElement = document.createElement('span');
                nameElement.className = 'text-gray-300';
                nameElement.textContent = `${item.name} x${item.quantity}`;
                
                const priceElement = document.createElement('span');
                priceElement.textContent = `₱${(parseFloat(item.price) * item.quantity).toFixed(2)}`;
                
                itemElement.appendChild(nameElement);
                itemElement.appendChild(priceElement);
                orderItemsContainer.appendChild(itemElement);
            });
        }
    }
});
</script>
@endsection