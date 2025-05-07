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
                                <input type="text" name="first_name" value="{{ Auth::user()->first_name ?? '' }}" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                                <input type="text" name="last_name" value="{{ Auth::user()->last_name ?? '' }}" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Street Address</label>
                            <input type="text" name="street" value="{{ Auth::user()->address ?? '' }}" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Barangay</label>
                                <input type="text" name="barangay" value="{{ Auth::user()->barangay ?? '' }}" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                                <input type="text" name="city" value="{{ Auth::user()->city ?? '' }}" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Postal Code</label>
                                <input type="text" name="postal_code" value="{{ Auth::user()->postal_code ?? '' }}" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                            <input type="tel" name="phone_number" value="{{ Auth::user()->mobile_number ?? '' }}" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500">
                        </div>
                    </form>
                </div>

                <!-- Payment Methods -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h2 class="text-xl font-semibold mb-6">Payment Method</h2>
                    <div class="space-y-4">
                        <!-- Payment Method Selection -->
                        <div class="space-y-3">
                            <div class="flex items-center space-x-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition-colors payment-method-option" data-method="cash">
                                <input type="radio" name="payment_method" value="cash" id="payment_cash" class="w-4 h-4 text-red-600" checked>
                                <label for="payment_cash" class="flex items-center cursor-pointer">
                                    <div class="w-8 h-8 flex items-center justify-center bg-gray-200 rounded-full mr-3">
                                        <i class="fas fa-money-bill text-green-600"></i>
                                    </div>
                                    <div>
                                        <span class="font-medium block">Cash on Delivery</span>
                                        <span class="text-sm text-gray-500">Pay when you receive your order</span>
                                    </div>
                                </label>
                            </div>
                            
                            <div class="flex items-center space-x-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition-colors payment-method-option" data-method="paymongo">
                                <input type="radio" name="payment_method" value="paymongo" id="payment_paymongo" class="w-4 h-4 text-red-600">
                                <label for="payment_paymongo" class="flex items-center cursor-pointer">
                                    <div class="w-8 h-8 flex items-center justify-center bg-blue-100 rounded-full mr-3">
                                        <i class="fas fa-credit-card text-blue-600"></i>
                                    </div>
                                    <div>
                                        <span class="font-medium block">Online Payment</span>
                                        <span class="text-sm text-gray-500">Pay with credit card, GCash, or other online methods</span>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Shipping Notes -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h2 class="text-xl font-semibold mb-6">Shipping Notes</h2>
                    <div class="space-y-4">
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Additional Notes (Optional)</label>
                            <textarea id="notes" name="notes" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500" placeholder="Special delivery instructions, preferred delivery time, etc."></textarea>
                        </div>

                        <!-- Payment Security Notice -->
                        <div class="mt-4 p-3 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="flex items-center space-x-2 text-sm text-gray-600">
                                <i class="fas fa-shield-alt"></i>
                                <span>Your payment information is secure and encrypted</span>
                            </div>
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

        // Place order button
        if (placeOrderBtn) {
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
                
                // Get form data
                const firstName = document.querySelector('input[name="first_name"]').value;
                const lastName = document.querySelector('input[name="last_name"]').value;
                const street = document.querySelector('input[name="street"]').value;
                const barangay = document.querySelector('input[name="barangay"]').value;
                const city = document.querySelector('input[name="city"]').value;
                const postalCode = document.querySelector('input[name="postal_code"]').value;
                const phoneNumber = document.querySelector('input[name="phone_number"]').value;
                const notes = document.getElementById('notes').value;
                
                // Get selected payment method
                const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
                
                // Validate form fields
                if (!firstName || !lastName || !street || !barangay || !city || !postalCode || !phoneNumber) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Missing Information',
                        text: 'Please fill in all delivery address fields'
                    });
                    return;
                }
                
                // Calculate total
                const subtotalText = document.getElementById('subtotal').textContent;
                const subtotal = parseFloat(subtotalText.replace('₱', '').replace(',', ''));
                const shippingText = document.getElementById('shipping').textContent;
                const shipping = parseFloat(shippingText.replace('₱', '').replace(',', ''));
                const total = subtotal + shipping;
                
                // Show processing message
                Swal.fire({
                    title: 'Processing Order...',
                    text: 'Please wait while we process your order',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Create order data
                const orderData = {
                    items: selectedItems,
                    shipping: {
                        first_name: firstName,
                        last_name: lastName,
                        street: street,
                        barangay: barangay,
                        city: city,
                        postal_code: postalCode,
                        phone_number: phoneNumber,
                        notes: notes
                    },
                    amount: total,
                    payment_method: paymentMethod
                };
                
                // If PayMongo is selected, handle differently
                if (paymentMethod === 'paymongo') {
                    handlePayMongoPayment(orderData);
                    return;
                }
                
                // For cash payment, continue with normal order process
                // Send order data to server
                fetch('{{ route('orders.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        first_name: firstName,
                        last_name: lastName,
                        street: street,
                        barangay: barangay,
                        city: city,
                        postal_code: postalCode,
                        phone_number: phoneNumber,
                        notes: notes || '',
                        payment_method: paymentMethod,
                        items: selectedItems.map(item => ({
                            id: item.id,
                            quantity: item.quantity,
                            price: parseFloat(item.price)
                        }))
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(errorData => {
                            throw new Error(errorData.message || 'Error placing order');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        console.log('Order placed successfully:', data);
                        // Clear cart for ordered items
                        const remainingItems = cart.filter((item, index) => {
                            return !Array.from(checkboxes)
                                .filter(checkbox => checkbox.checked)
                                .map(checkbox => parseInt(checkbox.dataset.index))
                                .includes(index);
                        });
                        
                        localStorage.setItem('cart', JSON.stringify(remainingItems));
                        
                        // Show success message
                        Swal.fire({
                            icon: 'success',
                            title: 'Order Placed Successfully!',
                            text: 'Your order has been placed and is now being processed.',
                            confirmButtonText: 'View My Orders',
                            confirmButtonColor: '#ef4444',
                            showCancelButton: true,
                            cancelButtonText: 'Continue Shopping'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = '{{ route('orders') }}';
                            } else {
                                window.location.href = '{{ route('shop') }}';
                            }
                        });
                        
                        // Sync cart with server
                        @auth
                        fetch('{{ route('cart.sync') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({ items: remainingItems })
                        });
                        @endauth
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Order Failed',
                            text: data.message || 'There was a problem placing your order. Please try again.'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error placing order:', error);
                    
                    // Check if the error message contains stock availability issues
                    const errorMessage = error.message || 'There was a problem placing your order. Please try again.';
                    const isStockError = errorMessage.includes('Not enough stock');
                    
                    Swal.fire({
                        icon: 'error',
                        title: isStockError ? 'Stock Availability Issue' : 'Order Failed',
                        text: errorMessage,
                        confirmButtonColor: '#ef4444',
                        confirmButtonText: isStockError ? 'Update Cart' : 'OK',
                        showCancelButton: isStockError,
                        cancelButtonText: isStockError ? 'Continue Shopping' : null
                    }).then((result) => {
                        if (isStockError) {
                            if (result.isConfirmed) {
                                // Redirect to cart to let user update quantities
                                window.location.href = '{{ route('cart') }}';
                            } else if (result.dismiss === Swal.DismissReason.cancel) {
                                window.location.href = '{{ route('shop') }}';
                            }
                        }
                    });
                });
            });
        }

        // Function to handle PayMongo payment
        function handlePayMongoPayment(orderData) {
            // Store order data in session storage for later use
            sessionStorage.setItem('orderData', JSON.stringify(orderData));
            
            // Clear cart for ordered items
            const checkboxes = document.querySelectorAll('.item-checkbox:checked');
            const selectedIndices = Array.from(checkboxes).map(checkbox => parseInt(checkbox.dataset.index));
            
            // Filter out selected items from cart
            const remainingItems = cart.filter((item, index) => !selectedIndices.includes(index));
            
            // Save remaining items to localStorage
            localStorage.setItem('cart', JSON.stringify(remainingItems));
            
            // Create form data for PayMongo payment
            const formData = new FormData();
            formData.append('type', 'product'); // This is a product payment, not a subscription
            formData.append('amount', orderData.amount);
            formData.append('payment_method', 'paymongo');
            formData.append('billing_name', `${orderData.shipping.first_name} ${orderData.shipping.last_name}`);
            formData.append('billing_email', '{{ Auth::user()->email ?? "" }}');
            formData.append('billing_phone', orderData.shipping.phone_number);
            formData.append('order_data', JSON.stringify(orderData));
            
            // These fields are required by the validation but don't affect product orders
            formData.append('plan', 'product'); 
            formData.append('waiver_accepted', '1');
            
            // Show processing message
            Swal.fire({
                title: 'Redirecting to Payment',
                text: 'Please wait while we prepare your payment...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Create a hidden form to submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route('payment.process') }}';
            
            // Add CSRF token
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = document.querySelector('meta[name="csrf-token"]').content;
            form.appendChild(csrfToken);
            
            // Add form data
            for (const [key, value] of formData.entries()) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = key;
                input.value = value;
                form.appendChild(input);
            }
            
            // Append form to body and submit
            document.body.appendChild(form);
            form.submit();
            
            // Sync cart with server if user is logged in
            @auth
            fetch('{{ route('cart.sync') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ items: remainingItems })
            }).catch(error => console.error('Error syncing cart:', error));
            @endauth
        }

        // Payment method selection
        const paymentOptions = document.querySelectorAll('.payment-method-option');
        paymentOptions.forEach(option => {
            option.addEventListener('click', function() {
                // Check the radio button
                const radio = this.querySelector('input[type="radio"]');
                radio.checked = true;
                
                // Add selected class to this option and remove from others
                paymentOptions.forEach(opt => {
                    if (opt === this) {
                        opt.classList.add('border-red-500', 'bg-red-50');
                    } else {
                        opt.classList.remove('border-red-500', 'bg-red-50');
                    }
                });
                
                // Update button text based on payment method
                const placeOrderBtn = document.getElementById('placeOrderBtn');
                if (placeOrderBtn) {
                    if (radio.value === 'paymongo') {
                        placeOrderBtn.textContent = 'Proceed to Payment';
                    } else {
                        placeOrderBtn.textContent = 'Place Order';
                    }
                }
            });
        });

        // Initialize the first payment option as selected
        if (paymentOptions.length > 0) {
            paymentOptions[0].click();
        }
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