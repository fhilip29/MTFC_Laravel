<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'ActiveGym')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon/favicon-16x16.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="48x48" href="{{ asset('favicon/favicon-48x48.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('favicon/favicon-192x192.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon/favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon/apple-touch-icon.png') }}">
    
    <!-- ✅ Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>

    <!-- ✅ Tailwind CSS CDN (Quick Fix) -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        html, body {
            height: 100%;
            margin: 0;
        }

        body {
            display: flex;
            flex-direction: column;
        }

        main {
            flex: 1;
        }

        footer {
            margin-top: auto;
        }

        header a, footer a {
            text-decoration: none !important;
        }

        /* 🧹 Hide Bootstrap Carousel arrows */
        .carousel-control-prev-icon,
        .carousel-control-next-icon {
            background-image: none !important;
            width: 0;
            height: 0;
        }

        .carousel-control-prev-icon::after,
        .carousel-control-next-icon::after {
            display: none !important;
        }

        /* Optional: Make sure Swiper arrows don't show if not used */
        .swiper-button-prev,
        .swiper-button-next {
            display: none !important;
        }

        /* Custom scrollbar styling for cart */
        #cartItems::-webkit-scrollbar {
            width: 6px;
        }
        
        #cartItems::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        #cartItems::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 10px;
        }
        
        #cartItems::-webkit-scrollbar-thumb:hover {
            background: #9ca3af;
        }
        
        /* Firefox scrollbar */
        #cartItems {
            scrollbar-width: thin;
            scrollbar-color: #d1d5db #f1f1f1;
        }
        
        /* Item hover effect */
        .cart-item {
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .cart-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        /* Page Loader Styles */
        #page-loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.9);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
    </style>

</head>
<body class="flex flex-col min-h-screen">
    <!-- Page Loader -->
    @include('components.loader')

    @if (!Request::is('profile') && !Request::is('profile/qr') && !Request::is('user/attendance') && !Request::is('account-settings') && !Request::is('trainer/profile') && !Request::is('trainer/attendance') && !Request::is('messages') && !Request::is('messages/compose') && !Request::is('messages/*') && !Request::is('messages/sent'))
        @include('components.header')
    @else
        <!-- Home Button for Profile Pages -->
        <div class="fixed top-4 left-4 z-50">
            <a href="/" class="bg-gray-800 text-white p-2 rounded-lg hover:bg-gray-700 transition-colors flex items-center">
                <i class="fas fa-home text-xl"></i>
            </a>
        </div>
    @endif

    <main class="flex-grow">
        @yield('content')
        
        @if (!Request::is('contact') && !Request::is('pricing/*') && !Request::is('profile') && !Request::is('/') && !Request::is('profile/qr') &&
        !Request::is('pricing') && !Request::is('account-settings') && !Request::is ('payment/*') && !Request::is ('payment-method') && !Request::is('subscription') && !Request::is('subscription/*') && !Request::is('cart')
        && !Request::is ('terms') && !Request::is ('privacypolicy') && !Request::is('trainer/profile') && !Request::is('announcements') && !Request::is('orders') && !Request::is('subscription/history') && !Request::is('community/*') && !Request::is('community') && !Request::is('messages/*') && !Request::is('messages' )
        && !Request::is('checkout') && !Request::is('user/*') && !Request::is('about') && !Request::is('shop'))
            <!-- Empty space div removed -->
        @endif
    </main>

    
    @if (!Request::is('account-settings') && !Request::is('trainer/profile') && !Request::is('announcements') && !Request::is('orders') && !Request::is('subscription/history') && !Request::is('profile') && !Request::is('community/*') && !Request::is('community') && !Request::is('messages/*') && !Request::is('messages' )
    && !Request::is('checkout') && !Request::is('profile/qr') && !Request::is('user/*') && !Request::is('announcements/*') && !Request::is('trainer/attendance') && !Request::is('messages/sent'))
        @include('components.footer')
    @endif


   <!-- 🛒 Cart Drawer -->
   <div id="cartDrawer" class="fixed top-0 right-0 w-96 h-full bg-white shadow-lg transform translate-x-full transition-transform z-50 overflow-hidden flex flex-col">
        <!-- Drawer Header -->
        <div class="flex items-center justify-between px-5 py-4 border-b">
            <h2 class="text-xl font-semibold">Shopping Cart</h2>
            <button id="closeCart" class="text-gray-600 hover:text-black text-2xl">&times;</button>
        </div>

        <!-- Item count indicator -->
        <div id="cartItemCount" class="px-5 py-2 text-sm text-gray-600">
            <span id="cartTotalItems">0</span> items in your cart
        </div>

        <!-- Cart Items with Scrollable Container -->
        <div id="cartItems" class="p-4 space-y-4 overflow-y-auto h-full max-h-[calc(100vh-220px)]">
            <!-- Empty Cart Message -->
            <div id="emptyCartMessage" class="p-8 text-center text-gray-500">
                <i class="fas fa-shopping-cart text-4xl mb-4 block"></i>
                <p>Your cart is empty</p>
                <p class="text-sm mt-2">Start shopping to add items to your cart</p>
            </div>
            
            <!-- Scrollable Cart Items Container -->
            <div id="cartItems" class="p-4 space-y-4 overflow-y-auto h-full max-h-[calc(100vh-220px)]">
                <!-- Cart items will be inserted here dynamically -->
            </div>
            
            <!-- Scroll to top button -->
            <button id="cartScrollTopBtn" class="absolute bottom-2 right-2 bg-white rounded-full w-8 h-8 shadow flex items-center justify-center text-gray-600 hover:bg-gray-100 hidden">
                <i class="fas fa-chevron-up"></i>
            </button>
        </div>

        <!-- Total and Checkout -->
        <div id="cartFooter" class="p-4 border-t hidden">
            <div class="flex justify-between text-md mb-2">
                <span>Total Quantity:</span>
                <span id="cartTotalQuantity">0</span>
            </div>
            <div class="flex justify-between text-md mb-4">
                <span>Total Price:</span>
                <span id="cartTotalPrice">₱0.00</span>
            </div>
            <a href="{{ route('checkout') }}" class="block w-full bg-black text-white py-2 rounded hover:bg-gray-800 transition text-center">Proceed to Checkout</a>
        </div>
    </div>

    <script>
        // Global cart functions for use across pages
        function increaseQuantity(index) {
            let cart = JSON.parse(localStorage.getItem('cart')) || [];
            
            if (cart[index]) {
                // Check if increasing quantity would exceed available stock
                if (cart[index].stock && cart[index].quantity >= cart[index].stock) {
                    // Just return without updating (silently prevent exceeding stock)
                    return;
                }
                
                cart[index].quantity++;
                localStorage.setItem('cart', JSON.stringify(cart));
                
                // Always render cart to update UI immediately
                renderCart();
                
                // Update cart badge
                updateCartBadge();
                
                // Sync with server if user is logged in
                syncCartWithServer(cart);
            }
        }
        
        function decreaseQuantity(index) {
            let cart = JSON.parse(localStorage.getItem('cart')) || [];
            
            if (cart[index] && cart[index].quantity > 1) {
                cart[index].quantity--;
                localStorage.setItem('cart', JSON.stringify(cart));
                
                // Always render cart to update UI immediately
                renderCart();
                
                // Update cart badge
                updateCartBadge();
                
                // Sync with server if user is logged in
                syncCartWithServer(cart);
            }
        }
        
        function confirmRemoveFromCart(index) {
            let cart = JSON.parse(localStorage.getItem('cart')) || [];
            
            Swal.fire({
                title: 'Are you sure?',
                text: cart[index] ? `Do you want to remove "${cart[index].name}" from the cart?` : 'Remove this item?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, remove it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed && cart[index]) {
                    // Remove the item
                    cart.splice(index, 1);
                    localStorage.setItem('cart', JSON.stringify(cart));
                    
                    // Always render cart to update UI immediately
                    renderCart();
                    
                    // Update cart badge
                    updateCartBadge();
                    
                    // Sync with server if user is logged in
                    syncCartWithServer(cart);
                }
            });
        }
        
        function updateCartBadge() {
            const cart = JSON.parse(localStorage.getItem('cart')) || [];
            const cartCount = cart.reduce((total, item) => total + item.quantity, 0);
            const cartBadge = document.getElementById('cartCount');
            if (cartBadge) {
                cartBadge.textContent = cartCount;
            }
        }
        
        function syncCartWithServer(cart) {
            // Check if user is logged in and csrf token exists
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) return;
            
            fetch('{{ route('cart.sync') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken.content
                },
                body: JSON.stringify({
                    items: cart
                })
            }).catch(error => console.error('Error syncing cart:', error));
        }

        document.addEventListener('DOMContentLoaded', function () {
            const cartButton = document.getElementById('cartButton');
            const mobileCartButton = document.getElementById('mobileCartButton');
            const cartDrawer = document.getElementById('cartDrawer');
            const closeCart = document.getElementById('closeCart');
            const cartItems = document.getElementById('cartItems');
            const emptyCartMessage = document.getElementById('emptyCartMessage');
            const cartFooter = document.getElementById('cartFooter');
            const cartTotalQuantity = document.getElementById('cartTotalQuantity');
            const cartTotalPrice = document.getElementById('cartTotalPrice');
            const cartScrollTopBtn = document.getElementById('cartScrollTopBtn');

            // Initialize the cart from local storage or create a new one
            let cart = JSON.parse(localStorage.getItem('cart')) || [];
            
            // Update cart badge counter
            updateCartBadge();

            // Sync with server if user is logged in
            @auth
            // Initial sync when page loads - get cart from server
            fetch('{{ route('cart.get') }}')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.items) {
                        // If server cart exists and is different, update local cart
                        const serverCart = data.items;
                        if (JSON.stringify(serverCart) !== JSON.stringify(cart)) {
                            cart = serverCart;
                            localStorage.setItem('cart', JSON.stringify(cart));
                            updateCartBadge();
                            renderCart(); // Update UI after syncing
                        }
                    }
                })
                .catch(error => console.error('Error syncing cart:', error));
            @endauth

            // Make sure cart drawer has proper z-index
            if (cartDrawer) {
                cartDrawer.style.zIndex = '9999';
            }

            // Desktop cart button functionality
            if (cartButton && cartDrawer && closeCart) {
                // Fix for desktop: ensure touchable area is large enough
                cartButton.style.position = 'relative';
                cartButton.style.zIndex = '40';
                cartButton.style.cursor = 'pointer';
                
                // Increase touchable area with padding if needed
                cartButton.style.padding = '8px';
                cartButton.style.marginRight = '-8px';
                
                cartButton.addEventListener('click', (e) => {
                    console.log('Desktop cart button clicked');
                    e.preventDefault();
                    e.stopPropagation(); // Prevent event from bubbling up
                    cartDrawer.classList.remove('translate-x-full');
                    renderCart();
                });

                closeCart.addEventListener('click', () => {
                    cartDrawer.classList.add('translate-x-full');
                });

                // Only close drawer when clicking outside, but not on the drawer or its children
                window.addEventListener('click', (e) => {
                    // Only close if drawer is open and click is outside both the drawer and cart buttons
                    if (!cartDrawer.classList.contains('translate-x-full') && 
                        !cartDrawer.contains(e.target) && 
                        !cartButton.contains(e.target) &&
                        (mobileCartButton ? !mobileCartButton.contains(e.target) : true) &&
                        !e.target.closest('.confirm-dialog')) { // Don't close if clicking on confirmation dialog
                        cartDrawer.classList.add('translate-x-full');
                    }
                });
                
                // Prevent clicks inside the drawer from closing it
                cartDrawer.addEventListener('click', (e) => {
                    e.stopPropagation();
                });
            }
            
            // Mobile cart button functionality (backup in case header.blade.php event listener fails)
            if (mobileCartButton && cartDrawer) {
                console.log('Mobile cart button found in app.blade.php');
                
                // Ensure mobile cart button has proper z-index and styling
                mobileCartButton.style.position = 'relative';
                mobileCartButton.style.zIndex = '60';
                mobileCartButton.style.cursor = 'pointer';
                
                // Add event listener directly in app.blade.php as a backup
                mobileCartButton.addEventListener('click', (e) => {
                    console.log('Mobile cart button clicked in app.blade.php');
                    e.preventDefault();
                    e.stopPropagation();
                    cartDrawer.classList.remove('translate-x-full');
                    renderCart();
                    
                    // Try to close mobile menu if open
                    try {
                        const mobileMenuToggle = document.querySelector('[x-data]');
                        if (mobileMenuToggle && typeof mobileMenuToggle.__x !== 'undefined') {
                            mobileMenuToggle.__x.updateElements(mobileMenuToggle, () => {
                                mobileMenuToggle.__x.$data.mobileMenuOpen = false;
                            });
                        }
                    } catch (err) {
                        console.error('Error closing mobile menu:', err);
                    }
                });
            }

            // Set up scrolling features for cart items container
            if (cartItems) {
                cartItems.addEventListener('scroll', function() {
                    if (this.scrollTop > 100 && cartScrollTopBtn) {
                        cartScrollTopBtn.classList.remove('hidden');
                    } else if (cartScrollTopBtn) {
                        cartScrollTopBtn.classList.add('hidden');
                    }
                });
            }

            if (cartScrollTopBtn) {
                cartScrollTopBtn.addEventListener('click', function() {
                    if (cartItems) {
                        cartItems.scrollTo({ top: 0, behavior: 'smooth' });
                    }
                });
            }

            // Function to render cart items
            window.renderCart = function() {
                // Get latest cart data
                cart = JSON.parse(localStorage.getItem('cart')) || [];
                
                // Make sure cart items element exists
                if (!cartItems) return;
                
                // Update cart items count
                const cartTotalItems = document.getElementById('cartTotalItems');
                if (cartTotalItems) {
                    const totalItems = cart.reduce((total, item) => total + item.quantity, 0);
                    cartTotalItems.textContent = totalItems;
                }
                
                // Clear current cart display
                cartItems.innerHTML = '';
                
                if (cart.length === 0) {
                    // Show empty cart message
                    if (emptyCartMessage) emptyCartMessage.classList.remove('hidden');
                    if (cartFooter) cartFooter.classList.add('hidden');
                    return;
                }
                
                // Hide empty cart message and show footer
                if (emptyCartMessage) emptyCartMessage.classList.add('hidden');
                if (cartFooter) cartFooter.classList.remove('hidden');
                
                // Calculate totals
                let totalQuantity = 0;
                let totalPrice = 0;
                
                // Create document fragment for better performance
                const fragment = document.createDocumentFragment();
                
                // Render each cart item
                cart.forEach((item, index) => {
                    totalQuantity += item.quantity;
                    totalPrice += item.price * item.quantity;
                    
                    const itemElement = document.createElement('div');
                    itemElement.className = 'flex items-center space-x-4 border-b pb-4 mb-4 cart-item rounded p-2';

                    // Check if item is at stock limit
                    const isAtStockLimit = item.stock && item.quantity >= item.stock;
                    
                    itemElement.innerHTML = `
                        <img src="${item.image || '{{ asset('assets/default-product.jpg') }}'}" alt="${item.name}" class="w-16 h-16 object-cover rounded">
                        <div class="flex-1">
                            <h4 class="text-md font-semibold">${item.name}</h4>
                            <p class="text-sm text-gray-500">₱${parseFloat(item.price).toFixed(2)} per item</p>
                            ${isAtStockLimit ? `<p class="text-xs text-red-500 font-semibold">Maximum stock reached</p>` : ''}
                            <div class="flex items-center space-x-2 mt-2">
                                <button type="button" class="decrease-btn bg-gray-200 text-gray-600 w-6 h-6 rounded-full flex items-center justify-center hover:bg-gray-300" data-index="${index}">
                                    <i class="fas fa-minus text-xs"></i>
                                </button>
                                <span class="quantity-value text-center w-6">${item.quantity}</span>
                                <button type="button" class="increase-btn ${isAtStockLimit ? 'bg-gray-300 cursor-not-allowed' : 'bg-gray-200 hover:bg-gray-300'} text-gray-600 w-6 h-6 rounded-full flex items-center justify-center" data-index="${index}">
                                    <i class="fas fa-plus text-xs"></i>
                                </button>
                                <button type="button" class="ml-2 text-red-500 hover:text-red-700" data-index="${index}">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-lg font-semibold text-black">₱${(item.price * item.quantity).toFixed(2)}</span>
                        </div>
                    `;
                    
                    // Add event listeners directly to the new elements
                    const decreaseBtn = itemElement.querySelector('.decrease-btn');
                    if (decreaseBtn) {
                        decreaseBtn.addEventListener('click', function() {
                            decreaseQuantity(index);
                        });
                    }
                    
                    const increaseBtn = itemElement.querySelector('.increase-btn');
                    if (increaseBtn) {
                        increaseBtn.addEventListener('click', function() {
                            increaseQuantity(index);
                        });
                    }
                    
                    const removeBtn = itemElement.querySelector('.fa-trash-alt').closest('button');
                    if (removeBtn) {
                        removeBtn.addEventListener('click', function() {
                            confirmRemoveFromCart(index);
                        });
                    }
                    
                    fragment.appendChild(itemElement);
                });
                
                // Add all items at once for better performance
                cartItems.appendChild(fragment);
                
                // Update total quantity and price in the footer
                if (cartTotalQuantity) cartTotalQuantity.textContent = totalQuantity;
                if (cartTotalPrice) cartTotalPrice.textContent = `₱${totalPrice.toFixed(2)}`;
            }
        });
    </script>
</body>
</html>
