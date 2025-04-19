<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'ActiveGym')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
   

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

    <style>
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
</style>

</head>
<body class="bg-gray-100 text-gray-900">

    @include('components.header')

    <main>
        @yield('content')
    </main>

    @include('components.footer')

   <!-- 🛒 Cart Drawer -->
<div id="cartDrawer" class="fixed top-0 right-0 w-96 h-full bg-white shadow-lg transform translate-x-full transition-transform z-50 overflow-y-auto">
    <!-- Drawer Header -->
    <div class="flex items-center justify-between px-5 py-4 border-b">
        <h2 class="text-xl font-semibold">Shopping Cart</h2>
        <button id="closeCart" class="text-gray-600 hover:text-black text-2xl">&times;</button>
    </div>

    <!-- Cart Items -->
    <div id="cartItems" class="p-4 space-y-4">
        <!-- Cart items will be inserted here dynamically -->
    </div>

    <!-- Empty Cart Message -->
    <div id="emptyCartMessage" class="p-8 text-center text-gray-500">
        <i class="fas fa-shopping-cart text-4xl mb-4 block"></i>
        <p>Your cart is empty</p>
        <p class="text-sm mt-2">Start shopping to add items to your cart</p>
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
    document.addEventListener('DOMContentLoaded', function () {
        const cartButton = document.getElementById('cartButton');
        const cartDrawer = document.getElementById('cartDrawer');
        const closeCart = document.getElementById('closeCart');
        const cartItems = document.getElementById('cartItems');
        const emptyCartMessage = document.getElementById('emptyCartMessage');
        const cartFooter = document.getElementById('cartFooter');
        const cartTotalQuantity = document.getElementById('cartTotalQuantity');
        const cartTotalPrice = document.getElementById('cartTotalPrice');

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
                    }
                }
            })
            .catch(error => console.error('Error syncing cart:', error));
        @endauth

        if (cartButton && cartDrawer && closeCart) {
            cartButton.addEventListener('click', (e) => {
                e.stopPropagation(); // Prevent event from bubbling up
                cartDrawer.classList.remove('translate-x-full');
                renderCart();
            });

            closeCart.addEventListener('click', () => {
                cartDrawer.classList.add('translate-x-full');
            });

            // Only close drawer when clicking outside, but not on the drawer or its children
            window.addEventListener('click', (e) => {
                // Only close if drawer is open and click is outside both the drawer and cart button
                if (!cartDrawer.classList.contains('translate-x-full') && 
                    !cartDrawer.contains(e.target) && 
                    !cartButton.contains(e.target) &&
                    !e.target.closest('.confirm-dialog')) { // Don't close if clicking on confirmation dialog
                    cartDrawer.classList.add('translate-x-full');
                }
            });
            
            // Prevent clicks inside the drawer from closing it
            cartDrawer.addEventListener('click', (e) => {
                e.stopPropagation();
            });
        }

        // Function to add item to cart with confirmation
        window.addToCart = function(product) {
            showConfirmDialog(
                `Add "${product.name}" to cart?`, 
                function() { // On confirm
                    // Check if product is already in cart
                    const existingItemIndex = cart.findIndex(item => item.id === product.id);
                    
                    if (existingItemIndex > -1) {
                        // Update quantity if product already exists
                        cart[existingItemIndex].quantity += product.quantity;
                    } else {
                        // Add new product to cart
                        cart.push(product);
                    }
                    
                    // Save to localStorage
                    localStorage.setItem('cart', JSON.stringify(cart));
                    
                    // Update the cart badge
                    updateCartBadge();
                    
                    // Sync with server if logged in
                    @auth
                    syncCartWithServer();
                    @endauth
                    
                    // Show a confirmation message or animation
                    showAddedToCartMessage();
                    
                    // Optional: Show the cart drawer
                    cartDrawer.classList.remove('translate-x-full');
                    renderCart();
                    
                    return cart;
                }
            );
        };
        
        // Function to display a confirmation dialog
        window.showConfirmDialog = function(message, onConfirm, onCancel) {
            // Remove any existing confirm dialogs
            const existingDialogs = document.querySelectorAll('.confirm-dialog');
            existingDialogs.forEach(dialog => dialog.remove());
            
            const dialog = document.createElement('div');
            dialog.className = 'confirm-dialog fixed inset-0 z-50 flex items-center justify-center';
            dialog.innerHTML = `
                <div class="fixed inset-0 bg-black opacity-50" id="confirmOverlay"></div>
                <div class="bg-white rounded-lg shadow-xl p-6 max-w-sm w-full relative z-10 transform transition-all">
                    <p class="text-lg mb-4">${message}</p>
                    <div class="flex justify-end space-x-2">
                        <button id="confirmCancel" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 transition focus:outline-none">Cancel</button>
                        <button id="confirmOk" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition focus:outline-none">OK</button>
                    </div>
                </div>
            `;
            
            document.body.appendChild(dialog);
            
            // Handle clicks
            document.getElementById('confirmOk').addEventListener('click', () => {
                if (typeof onConfirm === 'function') onConfirm();
                dialog.remove();
            });
            
            document.getElementById('confirmCancel').addEventListener('click', () => {
                if (typeof onCancel === 'function') onCancel();
                dialog.remove();
            });
            
            document.getElementById('confirmOverlay').addEventListener('click', () => {
                if (typeof onCancel === 'function') onCancel();
                dialog.remove();
            });
            
            // Prevent clicks on the dialog from closing the cart drawer
            dialog.addEventListener('click', (e) => {
                e.stopPropagation();
            });
        };

        // Function to show confirmation message
        function showAddedToCartMessage() {
            const message = document.createElement('div');
            message.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded shadow-lg z-50 animate-fade-in-out';
            message.textContent = 'Added to cart!';
            document.body.appendChild(message);
            
            setTimeout(() => {
                message.remove();
            }, 2000);
        }

        // Function to render cart items
        function renderCart() {
            // Clear current cart display
            cartItems.innerHTML = '';
            
            if (cart.length === 0) {
                // Show empty cart message
                emptyCartMessage.classList.remove('hidden');
                cartFooter.classList.add('hidden');
                return;
            }
            
            // Hide empty cart message and show footer
            emptyCartMessage.classList.add('hidden');
            cartFooter.classList.remove('hidden');
            
            // Calculate totals
            let totalQuantity = 0;
            let totalPrice = 0;
            
            // Render each cart item
            cart.forEach((item, index) => {
                totalQuantity += item.quantity;
                totalPrice += item.price * item.quantity;
                
                const itemElement = document.createElement('div');
                itemElement.className = 'flex items-center space-x-4 border-b pb-4';
                itemElement.innerHTML = `
                    <img src="${item.image || '{{ asset('assets/default-product.jpg') }}'}" alt="${item.name}" class="w-16 h-16 object-cover rounded">
                    <div class="flex-1">
                        <h4 class="text-md font-semibold">${item.name}</h4>
                        <p class="text-sm text-gray-500">₱${parseFloat(item.price).toFixed(2)}</p>
                        <div class="flex items-center space-x-2 mt-1">
                            <button type="button" class="bg-gray-200 px-2 py-1 rounded-l hover:bg-gray-300" onclick="event.stopPropagation(); decreaseQuantity(${index})">-</button>
                            <span>${item.quantity}</span>
                            <button type="button" class="bg-gray-200 px-2 py-1 rounded-r hover:bg-gray-300" onclick="event.stopPropagation(); increaseQuantity(${index})">+</button>
                            <button type="button" class="ml-2 text-red-500 hover:text-red-700" onclick="event.stopPropagation(); confirmRemoveFromCart(${index})">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </div>
                `;
                
                cartItems.appendChild(itemElement);
            });
            
            // Update totals
            cartTotalQuantity.textContent = totalQuantity;
            cartTotalPrice.textContent = `₱${totalPrice.toFixed(2)}`;
        }

        // Function to update cart badge
        function updateCartBadge() {
            const badge = document.querySelector('#cartButton .badge');
            
            if (!badge) {
                // Create badge if it doesn't exist
                const newBadge = document.createElement('span');
                newBadge.className = 'badge absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs';
                
                if (cart.length > 0) {
                    const totalItems = cart.reduce((total, item) => total + item.quantity, 0);
                    newBadge.textContent = totalItems > 9 ? '9+' : totalItems;
                    document.getElementById('cartButton').appendChild(newBadge);
                }
            } else {
                // Update existing badge
                if (cart.length > 0) {
                    const totalItems = cart.reduce((total, item) => total + item.quantity, 0);
                    badge.textContent = totalItems > 9 ? '9+' : totalItems;
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
            }
        }

        // Increase quantity
        window.increaseQuantity = function(index) {
            if (index >= 0 && index < cart.length) {
                cart[index].quantity += 1;
                localStorage.setItem('cart', JSON.stringify(cart));
                renderCart();
                updateCartBadge();
                
                // Sync with server if logged in
                @auth
                syncCartWithServer();
                @endauth
            }
        };

        // Decrease quantity
        window.decreaseQuantity = function(index) {
            if (index >= 0 && index < cart.length) {
                if (cart[index].quantity > 1) {
                    cart[index].quantity -= 1;
                    localStorage.setItem('cart', JSON.stringify(cart));
                    renderCart();
                    updateCartBadge();
                    
                    // Sync with server if logged in
                    @auth
                    syncCartWithServer();
                    @endauth
                } else {
                    confirmRemoveFromCart(index);
                }
            }
        };

        // Confirm remove from cart
        window.confirmRemoveFromCart = function(index) {
            if (index >= 0 && index < cart.length) {
                const item = cart[index];
                showConfirmDialog(
                    `Remove "${item.name}" from cart?`,
                    function() {
                        removeFromCart(index);
                    }
                );
            }
        };

        // Remove from cart
        window.removeFromCart = function(index) {
            if (index >= 0 && index < cart.length) {
                cart.splice(index, 1);
                localStorage.setItem('cart', JSON.stringify(cart));
                renderCart();
                updateCartBadge();
                
                // Sync with server if logged in
                @auth
                syncCartWithServer();
                @endauth
            }
        };
        
        // Add animation style for notification
        const style = document.createElement('style');
        style.textContent = `
            @keyframes fadeInOut {
                0% { opacity: 0; transform: translateY(-20px); }
                10% { opacity: 1; transform: translateY(0); }
                90% { opacity: 1; transform: translateY(0); }
                100% { opacity: 0; transform: translateY(-20px); }
            }
            .animate-fade-in-out {
                animation: fadeInOut 2s ease-in-out;
            }
        `;
        document.head.appendChild(style);

        // Function to sync cart with server
        function syncCartWithServer() {
            @auth
            fetch('{{ route('cart.sync') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ items: cart })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('Cart synced with server');
                } else {
                    console.error('Failed to sync cart with server');
                }
            })
            .catch(error => console.error('Error syncing cart:', error));
            @endauth
        }
    });
</script>


</body>
</html>
