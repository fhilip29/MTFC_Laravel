@extends('layouts.app')

@section('content')
<!-- Add Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- CSRF Token -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Custom styling for the checkout page -->
<style>
    /* Custom scrollbar styling */
    #checkoutItems::-webkit-scrollbar {
        width: 6px;
    }
    
    #checkoutItems::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    #checkoutItems::-webkit-scrollbar-thumb {
        background: #d1d5db;
        border-radius: 10px;
    }
    
    #checkoutItems::-webkit-scrollbar-thumb:hover {
        background: #9ca3af;
    }
    
    /* Hide scrollbar for Firefox */
    #checkoutItems {
        scrollbar-width: thin;
        scrollbar-color: #d1d5db #f1f1f1;
    }
    
    /* Item hover effect */
    .checkout-item {
        transition: transform 0.2s, box-shadow 0.2s;
    }
    
    .checkout-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
</style>

<div class="min-h-screen bg-gray-100 py-8">
    <div class="container mx-auto px-4">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Checkout</h1>

        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Delivery Address Form -->
            <div class="lg:w-2/3 space-y-8">
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h2 class="text-xl font-semibold mb-6">Delivery Address</h2>
                    <form class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                                <input type="text" name="first_name" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                                <input type="text" name="last_name" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Street Address</label>
                            <input type="text" name="street" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Barangay</label>
                                <input type="text" name="barangay" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                                <input type="text" name="city" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Postal Code</label>
                                <input type="text" name="postal_code" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                            <input type="tel" name="phone_number" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500">
                        </div>
                    </form>
                </div>

                <!-- Payment Methods -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h2 class="text-xl font-semibold mb-6">Payment Method</h2>
                    <div class="space-y-4">
                        <div>
                            <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-2">Select Payment Method</label>
                            <div class="relative">
                                <select id="payment_method" name="payment_method" class="appearance-none block w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 bg-white">
                                    <option value="" disabled selected>Choose a payment method</option>
                                    <option value="cod">Cash on Delivery</option>
                                    <option value="gcash">GCash</option>
                                    <option value="bank">Bank Transfer</option>
                                    <option value="credit">Credit/Debit Card</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Additional information based on selected payment -->
                        <div id="payment-details" class="hidden space-y-4 mt-4 p-4 bg-gray-50 rounded-md border border-gray-200">
                            <!-- Content will be dynamically inserted based on selection -->
                        </div>
                        
                        <!-- Add new payment method button -->
                        <div class="mt-4">
                            <button type="button" id="addNewPaymentBtn" class="flex items-center text-red-600 hover:text-red-800 transition-colors">
                                <i class="fas fa-plus-circle mr-2"></i>
                                <span>Add New Payment Method</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="lg:w-1/3 bg-white rounded-lg shadow-lg p-6 h-fit">
                <h2 class="text-xl font-semibold mb-6">Order Summary</h2>
                
                <!-- Item count indicator -->
                <div id="itemCount" class="text-sm text-gray-600 mb-3">
                    <span id="selectedItemCount">0</span> of <span id="totalItemCount">0</span> items selected
                </div>
                
                <!-- Cart Items with Scrollable Container -->
                <div id="checkoutItemsWrapper" class="mb-6 relative">
                    <p id="emptyCartMessage" class="text-gray-500 text-center py-4 hidden">Your cart is empty</p>
                    
                    <!-- Scrollable container for cart items -->
                    <div id="checkoutItems" class="space-y-4 max-h-[300px] overflow-y-auto pr-2">
                        <!-- Items will be loaded dynamically -->
                    </div>
                    
                    <!-- Scroll to top button -->
                    <button id="scrollTopBtn" class="absolute bottom-2 right-2 bg-white rounded-full w-8 h-8 shadow flex items-center justify-center text-gray-600 hover:bg-gray-100 hidden">
                        <i class="fas fa-chevron-up"></i>
                    </button>
                </div>

                <!-- Order Total -->
                <div class="space-y-2 mb-6">
                    <div class="flex justify-between text-sm">
                        <span>Subtotal</span>
                        <span id="subtotal">₱0.00</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span>Shipping</span>
                        <span id="shipping">₱150.00</span>
                    </div>
                    <div class="flex justify-between font-semibold text-lg pt-4 border-t border-gray-200">
                        <span>Total</span>
                        <span id="total">₱150.00</span>
                    </div>
                </div>

                <!-- Place Order Button -->
                <button id="placeOrderBtn" class="w-full bg-red-600 text-white py-3 rounded-md font-semibold hover:bg-red-700 transition-colors duration-200">
                    Place Order
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Check authentication status
        const isAuthenticated = {{ Auth::check() ? 'true' : 'false' }};
        if (!isAuthenticated) {
            // Save cart to localStorage
            localStorage.setItem('cart_redirect', 'checkout');
            window.location.href = '{{ route('login.form') }}';
            return;
        }
        
        // Get DOM elements
        const checkoutItems = document.getElementById('checkoutItems');
        const emptyCartMessage = document.getElementById('emptyCartMessage');
        const subtotalElement = document.getElementById('subtotal');
        const shippingElement = document.getElementById('shipping');
        const totalElement = document.getElementById('total');
        const placeOrderBtn = document.getElementById('placeOrderBtn');
        const paymentMethodSelect = document.getElementById('payment_method');
        const paymentDetails = document.getElementById('payment-details');
        const addNewPaymentBtn = document.getElementById('addNewPaymentBtn');
        
        // Get cart data - from server or localStorage
        let cart = [];
        @if(isset($cartItems) && !empty($cartItems))
            cart = @json($cartItems);
        @else
            cart = JSON.parse(localStorage.getItem('cart')) || [];
        @endif
        
        // Basic checks for empty cart
        if (!cart || cart.length === 0) {
            emptyCartMessage.classList.remove('hidden');
            placeOrderBtn.disabled = true;
            placeOrderBtn.classList.add('opacity-50', 'cursor-not-allowed');
            shippingElement.textContent = '₱0.00';
            totalElement.textContent = '₱0.00';
            subtotalElement.textContent = '₱0.00';
            return;
        }
        
        // We have items - hide empty message
        emptyCartMessage.classList.add('hidden');
        
        // Set default shipping cost
        const shippingCost = 150;
        let subtotal = 0;
        
        // Display cart items
        cart.forEach((item, index) => {
            const itemElement = document.createElement('div');
            itemElement.className = 'flex items-center justify-between pb-4 border-b border-gray-200 checkout-item rounded p-2 mb-2';
            
            const itemPrice = parseFloat(item.price) * item.quantity;
            
            itemElement.innerHTML = `
                <div class="flex items-center space-x-4">
                    <input type="checkbox" data-index="${index}" data-price="${item.price}" 
                           class="item-checkbox w-5 h-5 text-red-600 rounded focus:ring-red-500" checked>
                    <img src="${item.image || '{{ asset("assets/default-product.jpg") }}'}" 
                         alt="${item.name}" class="w-16 h-16 object-cover rounded">
                    <div>
                        <h3 class="font-medium">${item.name}</h3>
                        <p class="text-sm text-gray-500">₱${parseFloat(item.price).toFixed(2)} per item</p>
                        <div class="flex items-center space-x-2 mt-2">
                            <button type="button" class="quantity-btn decrease-btn bg-gray-200 text-gray-600 w-8 h-8 rounded-full flex items-center justify-center hover:bg-gray-300 focus:outline-none" data-index="${index}">
                                <i class="fas fa-minus text-xs"></i>
                            </button>
                            <span class="quantity-value text-center w-8" data-index="${index}">${item.quantity}</span>
                            <button type="button" class="quantity-btn increase-btn bg-gray-200 text-gray-600 w-8 h-8 rounded-full flex items-center justify-center hover:bg-gray-300 focus:outline-none" data-index="${index}">
                                <i class="fas fa-plus text-xs"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <p class="font-semibold item-total" data-index="${index}">₱${itemPrice.toFixed(2)}</p>
                </div>
            `;
            
            checkoutItems.appendChild(itemElement);
            
            // Add to subtotal
            subtotal += itemPrice;
        });
        
        // Update totals
        subtotalElement.textContent = `₱${subtotal.toFixed(2)}`;
        totalElement.textContent = `₱${(subtotal + shippingCost).toFixed(2)}`;
        
        // Add event listeners to checkboxes
        const checkboxes = document.querySelectorAll('.item-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateTotals);
        });
        
        // Add event listeners to quantity buttons
        const decreaseButtons = document.querySelectorAll('.decrease-btn');
        const increaseButtons = document.querySelectorAll('.increase-btn');
        
        decreaseButtons.forEach(button => {
            button.addEventListener('click', function() {
                const index = parseInt(this.dataset.index);
                if (cart[index].quantity > 1) {
                    cart[index].quantity -= 1;
                    updateItemDisplay(index);
                    updateTotals();
                    saveCart();
                }
            });
        });
        
        increaseButtons.forEach(button => {
            button.addEventListener('click', function() {
                const index = parseInt(this.dataset.index);
                cart[index].quantity += 1;
                updateItemDisplay(index);
                updateTotals();
                saveCart();
            });
        });
        
        // Function to update item display after quantity change
        function updateItemDisplay(index) {
            if (!cart[index]) return;
            
            const quantityValue = document.querySelector(`.quantity-value[data-index="${index}"]`);
            const itemTotal = document.querySelector(`.item-total[data-index="${index}"]`);
            const checkbox = document.querySelector(`.item-checkbox[data-index="${index}"]`);
            
            if (!quantityValue || !itemTotal || !checkbox) return;
            
            quantityValue.textContent = cart[index].quantity;
            
            const newTotal = parseFloat(cart[index].price) * cart[index].quantity;
            itemTotal.textContent = `₱${newTotal.toFixed(2)}`;
            
            // Update checkbox data-price attribute
            checkbox.dataset.price = cart[index].price;
        }
        
        // Function to save cart
        function saveCart() {
            if (!cart) return;
            
            localStorage.setItem('cart', JSON.stringify(cart));
            
            // Sync with server if user is logged in
            @auth
            fetch('{{ route('cart.sync') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ items: cart })
            })
            .catch(error => console.error('Error syncing cart:', error));
            @endauth
        }
        
        // Update item counts
        const totalItemCount = document.getElementById('totalItemCount');
        const selectedItemCount = document.getElementById('selectedItemCount');
        
        if (totalItemCount) totalItemCount.textContent = cart.length;
        if (selectedItemCount) selectedItemCount.textContent = cart.length;
        
        // Setup scroll to top button
        const scrollTopBtn = document.getElementById('scrollTopBtn');
        const checkoutItemsContainer = document.getElementById('checkoutItems');
        
        if (scrollTopBtn && checkoutItemsContainer) {
            // Show button when scrolling down
            checkoutItemsContainer.addEventListener('scroll', function() {
                if (this.scrollTop > 100) {
                    scrollTopBtn.classList.remove('hidden');
                } else {
                    scrollTopBtn.classList.add('hidden');
                }
            });
            
            // Scroll to top when clicked
            scrollTopBtn.addEventListener('click', function() {
                checkoutItemsContainer.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        }
        
        // Function to update totals when checkboxes change
        function updateTotals() {
            if (!checkboxes || checkboxes.length === 0) return;
            
            let selectedSubtotal = 0;
            let selectedCount = 0;
            
            checkboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    const index = parseInt(checkbox.dataset.index);
                    if (cart[index]) {
                        selectedSubtotal += parseFloat(cart[index].price) * cart[index].quantity;
                        selectedCount++;
                    }
                }
            });
            
            // Update selected item count
            if (selectedItemCount) selectedItemCount.textContent = selectedCount;
            
            // If no items selected, set shipping to 0
            const finalShipping = selectedSubtotal > 0 ? shippingCost : 0;
            
            if (subtotalElement) subtotalElement.textContent = `₱${selectedSubtotal.toFixed(2)}`;
            if (shippingElement) shippingElement.textContent = `₱${finalShipping.toFixed(2)}`;
            if (totalElement) totalElement.textContent = `₱${(selectedSubtotal + finalShipping).toFixed(2)}`;
            
            // Disable order button if no items selected
            if (selectedSubtotal === 0) {
                if (placeOrderBtn) {
                    placeOrderBtn.disabled = true;
                    placeOrderBtn.classList.add('opacity-50', 'cursor-not-allowed');
                }
            } else {
                if (placeOrderBtn) {
                    placeOrderBtn.disabled = false;
                    placeOrderBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                }
            }
        }
        
        // Setup payment method dropdown
        if (paymentMethodSelect) {
            paymentMethodSelect.addEventListener('change', function() {
                const selectedMethod = this.value;
                
                // Show payment details section
                paymentDetails.classList.remove('hidden');
                
                // Update payment details based on selection
                switch(selectedMethod) {
                    case 'cod':
                        paymentDetails.innerHTML = `
                            <div class="flex items-center text-gray-700">
                                <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                                <p>You'll pay the delivery person when you receive your order.</p>
                            </div>
                        `;
                        break;
                    case 'gcash':
                        paymentDetails.innerHTML = `
                            <div class="space-y-3">
                                <div class="flex items-center text-gray-700">
                                    <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                                    <p>Pay using your GCash account.</p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <div class="w-1/2">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Account Name</label>
                                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500">
                                    </div>
                                    <div class="w-1/2">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Mobile Number</label>
                                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500">
                                    </div>
                                </div>
                            </div>
                        `;
                        break;
                    case 'bank':
                        paymentDetails.innerHTML = `
                            <div class="space-y-3">
                                <div class="flex items-center text-gray-700">
                                    <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                                    <p>Make a direct bank transfer to our account.</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Select Bank</label>
                                    <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500">
                                        <option value="">Select a bank</option>
                                        <option value="bdo">BDO</option>
                                        <option value="bpi">BPI</option>
                                        <option value="metrobank">Metrobank</option>
                                        <option value="unionbank">Unionbank</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Account Number</label>
                                    <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500">
                                </div>
                            </div>
                        `;
                        break;
                    case 'credit':
                        paymentDetails.innerHTML = `
                            <div class="space-y-3">
                                <div class="flex items-center text-gray-700">
                                    <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                                    <p>Pay securely with your credit or debit card.</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Card Number</label>
                                    <input type="text" placeholder="XXXX XXXX XXXX XXXX" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500">
                                </div>
                                <div class="flex space-x-2">
                                    <div class="w-1/2">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Expiry Date</label>
                                        <input type="text" placeholder="MM / YY" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500">
                                    </div>
                                    <div class="w-1/2">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">CVV</label>
                                        <input type="text" placeholder="XXX" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500">
                                    </div>
                                </div>
                            </div>
                        `;
                        break;
                    default:
                        paymentDetails.classList.add('hidden');
                }
            });
        }
        
        // Handle "Add New Payment Method" button
        if (addNewPaymentBtn) {
            addNewPaymentBtn.addEventListener('click', function() {
                // Create modal for adding new payment method
                const modal = document.createElement('div');
                modal.className = 'fixed inset-0 z-50 flex items-center justify-center';
                modal.innerHTML = `
                    <div class="fixed inset-0 bg-black opacity-50" id="modalOverlay"></div>
                    <div class="bg-white rounded-lg shadow-xl p-6 max-w-md w-full relative z-10">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-xl font-semibold">Add New Payment Method</h3>
                            <button id="closeModal" class="text-gray-500 hover:text-gray-800">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Payment Type</label>
                                <select id="newPaymentType" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500">
                                    <option value="gcash">GCash</option>
                                    <option value="bank">Bank Account</option>
                                    <option value="credit">Credit/Debit Card</option>
                                </select>
                            </div>
                            
                            <div id="newPaymentFields" class="space-y-3">
                                <!-- Fields will be dynamically updated based on selection -->
                            </div>
                            
                            <div class="flex justify-end space-x-2 mt-6">
                                <button id="cancelAddPayment" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 transition">
                                    Cancel
                                </button>
                                <button id="saveNewPayment" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition">
                                    Save Payment Method
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                
                document.body.appendChild(modal);
                
                // Setup new payment type change event
                const newPaymentType = document.getElementById('newPaymentType');
                const newPaymentFields = document.getElementById('newPaymentFields');
                
                // Initial content based on default selection
                updateNewPaymentFields(newPaymentType.value);
                
                newPaymentType.addEventListener('change', function() {
                    updateNewPaymentFields(this.value);
                });
                
                // Close modal functions
                document.getElementById('closeModal').addEventListener('click', closeModal);
                document.getElementById('cancelAddPayment').addEventListener('click', closeModal);
                document.getElementById('modalOverlay').addEventListener('click', closeModal);
                
                // Save new payment method
                document.getElementById('saveNewPayment').addEventListener('click', function() {
                    // Here you would typically save the payment method to the user's account
                    alert('New payment method saved successfully!');
                    closeModal();
                    
                    // Add the new payment to the dropdown
                    const newOption = document.createElement('option');
                    newOption.value = newPaymentType.value + '_saved';
                    newOption.text = getPaymentMethodName(newPaymentType.value) + ' (Saved)';
                    paymentMethodSelect.appendChild(newOption);
                    
                    // Select the new option
                    paymentMethodSelect.value = newOption.value;
                    
                    // Trigger change event to update payment details
                    const event = new Event('change');
                    paymentMethodSelect.dispatchEvent(event);
                });
                
                function updateNewPaymentFields(type) {
                    switch(type) {
                        case 'gcash':
                            newPaymentFields.innerHTML = `
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Account Name</label>
                                    <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Mobile Number</label>
                                    <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500">
                                </div>
                            `;
                            break;
                        case 'bank':
                            newPaymentFields.innerHTML = `
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Bank Name</label>
                                    <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500">
                                        <option value="bdo">BDO</option>
                                        <option value="bpi">BPI</option>
                                        <option value="metrobank">Metrobank</option>
                                        <option value="unionbank">Unionbank</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Account Number</label>
                                    <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Account Name</label>
                                    <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500">
                                </div>
                            `;
                            break;
                        case 'credit':
                            newPaymentFields.innerHTML = `
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Card Number</label>
                                    <input type="text" placeholder="XXXX XXXX XXXX XXXX" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Cardholder Name</label>
                                    <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500">
                                </div>
                                <div class="flex space-x-2">
                                    <div class="w-1/2">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Expiry Date</label>
                                        <input type="text" placeholder="MM / YY" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500">
                                    </div>
                                    <div class="w-1/2">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">CVV</label>
                                        <input type="text" placeholder="XXX" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500">
                                    </div>
                                </div>
                            `;
                            break;
                    }
                }
                
                function closeModal() {
                    document.body.removeChild(modal);
                }
                
                function getPaymentMethodName(type) {
                    switch(type) {
                        case 'gcash': return 'GCash';
                        case 'bank': return 'Bank Account';
                        case 'credit': return 'Credit/Debit Card';
                        default: return 'Payment Method';
                    }
                }
            });
        }
        
        // Place order button
        placeOrderBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Get selected items
            const selectedItems = Array.from(checkboxes)
                .filter(checkbox => checkbox.checked)
                .map(checkbox => {
                    const index = parseInt(checkbox.dataset.index);
                    return cart[index];
                });
            
            if (selectedItems.length === 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'No Items Selected',
                    text: 'Please select at least one item to order'
                });
                return;
            }
            
            // Get selected payment method
            const selectedPayment = document.getElementById('payment_method');
            if (!selectedPayment || !selectedPayment.value) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Payment Method Required',
                    text: 'Please select a payment method'
                });
                return;
            }
            
            // Get form data
            const firstName = document.querySelector('input[name="first_name"]').value;
            const lastName = document.querySelector('input[name="last_name"]').value;
            const street = document.querySelector('input[name="street"]').value;
            const barangay = document.querySelector('input[name="barangay"]').value;
            const city = document.querySelector('input[name="city"]').value;
            const postalCode = document.querySelector('input[name="postal_code"]').value;
            const phoneNumber = document.querySelector('input[name="phone_number"]').value;
            
            // Validate form fields
            if (!firstName || !lastName || !street || !barangay || !city || !postalCode || !phoneNumber) {
                Swal.fire({
                    icon: 'error',
                    title: 'Missing Information',
                    text: 'Please fill in all delivery address fields'
                });
                return;
            }
            
            // Show confirmation dialog
            Swal.fire({
                title: 'Confirm Your Order',
                text: 'Are you sure you want to place this order?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Yes, place order!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Prepare order data
                    const orderData = {
                        first_name: firstName,
                        last_name: lastName,
                        street: street,
                        barangay: barangay,
                        city: city,
                        postal_code: postalCode,
                        phone_number: phoneNumber,
                        payment_method: selectedPayment.value,
                        items: selectedItems.map(item => ({
                            id: item.id,
                            quantity: item.quantity,
                            price: item.price
                        }))
                    };
                    
                    // Show loading state
                    Swal.fire({
                        title: 'Processing Order',
                        text: 'Please wait while we process your order...',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    // Submit order to server
                    fetch('{{ route("orders.store") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(orderData)
                    })
                    .then(response => {
                        if (!response.ok) {
                            if (response.status === 401) {
                                throw new Error('You must be logged in to place an order');
                            }
                            throw new Error('Server error');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            // Show success message
                            Swal.fire({
                                icon: 'success',
                                title: 'Order Placed Successfully!',
                                text: 'Your order has been confirmed and is now being processed.',
                                confirmButtonColor: '#dc2626',
                                confirmButtonText: 'OK',
                                showDenyButton: true,
                                denyButtonText: 'View Order',
                                denyButtonColor: '#1F2937'
                            }).then((result) => {
                                if (result.isDenied) {
                                    // Redirect to orders page
                                    window.location.href = '{{ route("orders") }}';
                                } else {
                                    // Clear cart and redirect to home
                                    localStorage.removeItem('cart');
                                    window.location.href = '{{ route("home") }}';
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Order Failed',
                                text: data.message || 'There was an error processing your order.'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Order Failed',
                            text: error.message || 'There was an error processing your order.'
                        });
                        
                        // If authentication error, redirect to login
                        if (error.message === 'You must be logged in to place an order') {
                            localStorage.setItem('cart_redirect', 'checkout');
                            setTimeout(() => {
                                window.location.href = '{{ route("login.form") }}';
                            }, 2000);
                        }
                    });
                }
            });
        });
    });
</script>

<!-- Success Order Modal Template (Hidden) -->
<div id="successOrderModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Order Successful!</h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">Your order has been placed successfully and is now being processed.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <a href="{{ route('orders') }}" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">View Order</a>
                <button type="button" id="closeSuccessModal" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection