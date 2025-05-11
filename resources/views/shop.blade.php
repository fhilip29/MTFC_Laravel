@extends('layouts.app')

@section('content')
<!-- Add Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<!-- Add CSRF token meta tag for AJAX requests -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Initialize Cart JS -->
<script>
// Global cart functions
let cartItems = [];

// Make addToCart accessible globally
window.addToCart = function(product) {
    console.log('Adding to cart:', product);
    
    @auth
    @if(Auth::user()->role === 'admin')
    // For admin accounts, show a message they can't purchase
    Swal.fire({
        title: 'Admin Account',
        text: 'Admin accounts cannot make purchases. Please use a member account to shop.',
        icon: 'info',
        confirmButtonColor: '#EF4444'
    });
    return;
    @endif
    
    // Get existing cart from localStorage
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    
    // Check if product already exists in cart
    const existingItemIndex = cart.findIndex(item => item.id === product.id);
    
    if (existingItemIndex > -1) {
        // Product exists, increase quantity
        cart[existingItemIndex].quantity += product.quantity || 1;
    } else {
        // Product doesn't exist, add new item
        cart.push({
            id: product.id,
            name: product.name,
            price: product.price,
            image: product.image,
            quantity: product.quantity || 1,
            stock: product.stock
        });
    }
    
    // Save updated cart to localStorage
    localStorage.setItem('cart', JSON.stringify(cart));
    
    // Update cart badge count
    const cartCount = cart.reduce((total, item) => total + item.quantity, 0);
    const cartBadge = document.getElementById('cartCount');
    if (cartBadge) {
        cartBadge.textContent = cartCount;
    }
    
    // Update cart UI if renderCart function exists
    if (typeof window.renderCart === 'function') {
        window.renderCart();
    }
    
    // Show confirmation message
    Swal.fire({
        title: 'Added to Cart!',
        text: `${product.name} has been added to your cart.`,
        icon: 'success',
        confirmButtonColor: '#EF4444',
        timer: 2000,
        showConfirmButton: true,
        confirmButtonText: 'Continue'
    });
    
    // Sync with server if user is authenticated
    syncCartWithServer(cart);
    @else
    // Show login prompt for non-authenticated users
    Swal.fire({
        title: 'Login Required',
        text: 'Please login or sign up to add items to your cart',
        icon: 'info',
        confirmButtonColor: '#EF4444',
        showCancelButton: true,
        confirmButtonText: 'Login',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '{{ route("login") }}';
        }
    });
    @endauth
}

// Function to sync cart with server for logged in users
@auth
function syncCartWithServer(cart) {
    fetch('{{ route('cart.sync') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            items: cart
        })
    })
    .then(response => response.json())
    .then(data => {
        console.log('Cart synced with server:', data);
    })
    .catch(error => console.error('Error syncing cart:', error));
}
@endauth

// Current product in modal
let currentProduct = null;

// Function to show product modal
function showProductModal(id, name, price, image, description, stock, category, discount = 0) {
    currentProduct = {
        id: id,
        name: name,
        price: price,
        originalPrice: price,
        image: image,
        description: description,
        stock: stock,
        category: category,
        quantity: 1,
        discount: discount
    };
    
    // Set product details to modal
    document.getElementById('modalProductName').textContent = name;
    
    // Show discounted price if applicable
    const modalPriceElement = document.getElementById('modalProductPrice');
    if (discount > 0) {
        const discountedPrice = (price * (1 - discount/100)).toFixed(2);
        modalPriceElement.innerHTML = `
            <span class="line-through text-gray-400 text-sm">₱${parseFloat(price).toFixed(2)}</span>
            <span>₱${discountedPrice}</span>
            <span class="bg-red-600 text-white text-xs px-2 py-0.5 rounded-full ml-1">${discount}% OFF</span>
        `;
        // Update the product's price to discounted price for cart
        currentProduct.price = discountedPrice;
    } else {
        modalPriceElement.textContent = `₱${parseFloat(price).toFixed(2)}`;
    }
    
    document.getElementById('modalProductImage').src = image || '{{ asset("assets/default-product.jpg") }}';
    document.getElementById('modalProductImage').alt = name;
    document.getElementById('modalProductDescription').textContent = description || 'No description available';
    document.getElementById('modalProductStock').textContent = stock;
    document.getElementById('modalProductCategory').textContent = category;
    
    // Handle stock display
    if (stock <= 0) {
        document.getElementById('modalQuantitySection').classList.add('hidden');
        document.getElementById('modalOutOfStockSection').classList.remove('hidden');
    } else {
        document.getElementById('modalQuantitySection').classList.remove('hidden');
        document.getElementById('modalOutOfStockSection').classList.add('hidden');
    }
    
    // Show modal
    document.getElementById('productModal').classList.remove('hidden');
}

// Function to close product modal
function closeProductModal() {
    document.getElementById('productModal').classList.add('hidden');
    currentProduct = null;
}

// Quantity-related functions removed as they are no longer needed

// Function to add to cart from modal
function addToCartFromModal() {
    if (!currentProduct) return;
    
    // Fixed quantity of 1 since quantity selection has been removed
    const quantity = 1;
    
    if (quantity > 0 && quantity <= currentProduct.stock) {
        // Create product object with quantity
        const productToAdd = {
            id: currentProduct.id,
            name: currentProduct.name,
            price: currentProduct.price,
            image: currentProduct.image,
            quantity: quantity,
            stock: currentProduct.stock
        };
        
        // Add to cart
        window.addToCart(productToAdd);
        
        // Close modal
        closeProductModal();
    }
}
</script>

<div id="appContainer">
    <!-- Product Detail Modal -->
    <div id="productModal" class="fixed inset-0 z-50 overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 transition-opacity bg-black bg-opacity-60" onclick="closeProductModal()"></div>
            <div class="relative bg-white rounded-xl w-full max-w-md md:max-w-2xl mx-auto shadow-2xl transform transition-all">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 md:p-5 border-b">
                    <h3 class="text-lg md:text-xl font-bold" id="modalProductName">Product Details</h3>
                    <button onclick="closeProductModal()" class="text-gray-500 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-red-600 focus:ring-opacity-50 rounded-full p-1">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <!-- Modal body -->
                <div class="p-4 md:p-6">
                    <div class="flex flex-col md:flex-row gap-4 md:gap-6">
                        <div class="w-full md:w-1/2">
                            <div class="w-full h-56 md:h-72 bg-gray-100 rounded-lg flex items-center justify-center overflow-hidden shadow-inner">
                                <img id="modalProductImage" class="w-full h-full object-contain rounded-lg p-2 transition-all duration-300 hover:scale-105" src="" alt="Product image">
                            </div>
                            <div class="mt-3" id="modalCategoryContainer">
                                <span class="inline-block bg-gray-200 text-gray-800 text-sm px-3 py-1 rounded-full" id="modalProductCategory"></span>
                            </div>
                        </div>
                        <div class="w-full md:w-1/2 space-y-3 md:space-y-4">
                            <p class="text-2xl md:text-3xl font-bold text-red-600" id="modalProductPrice">
                                <!-- Price will be set dynamically by JavaScript -->
                            </p>
                            <div class="flex items-center space-x-2">
                                <span class="text-sm font-medium text-gray-500">Available Quantity:</span>
                                <span id="modalProductStock" class="text-sm font-medium"></span>
                            </div>
                            <div class="py-2">
                                <h4 class="text-sm font-medium text-gray-500 mb-1">Description:</h4>
                                <p class="text-gray-700 text-sm md:text-base" id="modalProductDescription">Loading description...</p>
                            </div>
                            <div id="modalQuantitySection" class="flex flex-col space-y-2">
                                <!-- Quantity section removed as requested -->
                                @auth
                                @if(Auth::user()->role === 'admin')
                                <button disabled class="w-full bg-gray-400 text-white px-4 py-2 md:px-6 md:py-3 rounded-lg flex items-center justify-center gap-2 shadow-md opacity-70 cursor-not-allowed">
                                    <i class="fas fa-lock"></i>
                                    <span>Admin</span>
                                </button>
                                @else
                                <button id="modalAddToCartBtn" onclick="addToCartFromModal()" class="w-full bg-red-600 text-white px-4 py-2 md:px-6 md:py-3 rounded-lg hover:bg-red-700 transition-all flex items-center justify-center gap-2 shadow-md transform hover:-translate-y-1 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50">
                                    <i class="fas fa-shopping-cart"></i>
                                    <span>Add to Cart</span>
                                </button>
                                @endif
                                @else
                                <button id="modalLoginBtn" onclick="showLoginPrompt()" class="w-full bg-red-600 text-white px-4 py-2 md:px-6 md:py-3 rounded-lg hover:bg-red-700 transition-all flex items-center justify-center gap-2 shadow-md transform hover:-translate-y-1 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50">
                                    <i class="fas fa-sign-in-alt"></i>
                                    <span>Login to Add to Cart</span>
                                </button>
                                @endauth
                            </div>
                            <div id="modalOutOfStockSection" class="pt-2 hidden">
                                <button class="w-full bg-gray-400 text-white px-4 py-2 md:px-6 md:py-3 rounded-lg cursor-not-allowed flex items-center justify-center gap-2 shadow-md opacity-70">
                                    <i class="fas fa-shopping-cart"></i>
                                    <span>Out of Stock</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Modal footer -->
                <div class="px-4 md:px-6 py-3 md:py-4 border-t border-gray-200 flex justify-end">
                    <button onclick="closeProductModal()" class="px-3 py-2 md:px-4 md:py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-opacity-50">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Hero Section -->
    <section class="relative w-full h-[500px] overflow-hidden">
        <!-- Background image with overlay -->
        <div class="absolute inset-0 bg-cover bg-center scale-105 transition-transform duration-30000 transform animate-slow-zoom" style="background-image: url('{{ asset('assets/shopping.jpg') }}');"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-black/60 to-black/40"></div>
        
        <!-- Hero content -->
        <div class="relative h-full flex items-center justify-center text-center px-4">
            <div class="max-w-3xl mx-auto">
                <h1 class="text-4xl md:text-6xl font-bold text-white mb-6 leading-tight drop-shadow-lg">Don't Miss Out On Our <span class="text-red-500">Exclusive Deals</span></h1>
                <p class="text-xl text-white mb-10 opacity-90">Premium fitness products for champions</p>
                <a href="#products" class="bg-red-600 text-white px-10 py-4 rounded-lg font-semibold hover:bg-red-700 transition transform hover:-translate-y-1 shadow-lg inline-flex items-center">
                    <span>Shop Now</span>
                    <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Category Navigation and Search -->
    <div class="bg-white sticky top-0 z-30 shadow-md py-3 md:py-4 border-b">
        <div class="container mx-auto px-4">
            <div class="flex justify-center items-center">
                <div class="flex justify-center space-x-2 md:space-x-4 overflow-x-auto pb-1 flex-nowrap w-full">
                    <a href="#equipment-section" class="whitespace-nowrap px-3 md:px-5 py-1 md:py-2 rounded-full border border-gray-200 hover:border-red-500 hover:text-red-600 transition font-medium text-xs md:text-sm">
                        <i class="fas fa-dumbbell mr-1 md:mr-2"></i>Equipment
                    </a>
                    <a href="#merchandise-section" class="whitespace-nowrap px-3 md:px-5 py-1 md:py-2 rounded-full border border-gray-200 hover:border-red-500 hover:text-red-600 transition font-medium text-xs md:text-sm">
                        <i class="fas fa-tshirt mr-1 md:mr-2"></i>Merchandise
                    </a>
                    <a href="#drinks-section" class="whitespace-nowrap px-3 md:px-5 py-1 md:py-2 rounded-full border border-gray-200 hover:border-red-500 hover:text-red-600 transition font-medium text-xs md:text-sm">
                        <i class="fas fa-wine-bottle mr-1 md:mr-2"></i>Drinks & Supplements
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Section -->
    <section id="products" class="pt-16 pb-24 bg-gray-50">
        <div class="container mx-auto px-4">
            <!-- Search Bar -->
            <div class="mb-10 flex justify-center">
                <div class="relative w-full max-w-md">
                    <input 
                        type="text" 
                        id="productSearch" 
                        placeholder="Search products..." 
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-full focus:ring-2 focus:ring-red-500 focus:border-red-500"
                    >
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>
            
            <!-- Equipment Section -->
            @if(count($equipment) > 0)
            <div id="equipment-section" class="mb-20 product-section">
                <div class="flex items-center justify-center mb-12 section-header">
                    <div class="h-0.5 bg-gray-200 w-16 mr-4"></div>
                    <h2 class="text-3xl font-bold text-center">Equipment</h2>
                    <div class="h-0.5 bg-gray-200 w-16 ml-4"></div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
                    @foreach($equipment as $product)
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden transform transition-all duration-300 hover:shadow-xl hover:-translate-y-1 h-full flex flex-col product-item" data-category="{{ $product->category }}">
                        <div class="relative h-64 overflow-hidden">
                            @if($product->image)
                            <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-contain bg-gray-50 p-4" loading="lazy">
                            @else
                            <div class="w-full h-full flex items-center justify-center bg-gray-100">
                                <i class="fas fa-box text-gray-300 text-4xl"></i>
                            </div>
                            @endif
                        </div>
                        <div class="p-4 flex-grow flex flex-col justify-between">
                            <div>
                                <h3 class="text-md font-semibold mb-1 product-name">{{ $product->name }}</h3>
                                @if(isset($product->discount) && $product->discount > 0)
                                    <div class="mb-1">
                                        <span class="line-through text-gray-400 text-sm">₱{{ number_format($product->price, 2) }}</span>
                                        <span class="text-red-600 font-bold">₱{{ number_format($product->price * (1 - $product->discount/100), 2) }}</span>
                                        <span class="bg-red-600 text-white text-xs px-2 py-0.5 rounded-full ml-1">{{ $product->discount }}% OFF</span>
                                    </div>
                                @else
                                    <p class="text-red-600 font-bold mb-3">₱{{ number_format($product->price, 2) }}</p>
                                @endif
                            </div>
                            <div class="flex justify-between items-center pt-2 gap-2">
                                <button type="button" 
                                    onclick="showProductModal({{ $product->id }}, '{{ $product->name }}', '{{ $product->price }}', '{{ $product->image ? asset($product->image) : '' }}', '{{ addslashes($product->description) }}', {{ $product->stock }}, '{{ $product->category }}', {{ $product->discount ?? 0 }})"
                                    class="flex-1 flex items-center justify-center gap-1 text-red-600 hover:text-white hover:bg-red-600 text-sm px-3 py-2 rounded-lg border border-red-600 transition-colors">
                                    <i class="fas fa-eye"></i>
                                    <span>View</span>
                                </button>
                                @auth
                                @if(Auth::user()->role === 'admin')
                                <button disabled class="flex-1 flex items-center justify-center gap-1 bg-gray-400 text-white px-3 py-2 rounded-lg text-sm cursor-not-allowed opacity-70">
                                    <i class="fas fa-lock"></i>
                                    <span>Admin</span>
                                </button>
                                @else
                                <button class="flex-1 flex items-center justify-center gap-1 bg-red-600 text-white px-3 py-2 rounded-lg text-sm hover:bg-red-700 transition-colors {{ $product->stock <= 0 ? 'opacity-50 cursor-not-allowed' : '' }}" {{ $product->stock <= 0 ? 'disabled' : '' }} onclick="window.addToCart({
                                    id: {{ $product->id }},
                                    name: '{{ addslashes($product->name) }}',
                                    price: {{ $product->discount > 0 ? $product->price * (1 - $product->discount/100) : $product->price }},
                                    originalPrice: {{ $product->price }},
                                    image: '{{ $product->image ? asset($product->image) : '' }}',
                                    quantity: 1,
                                    stock: {{ $product->stock }},
                                    discount: {{ $product->discount ?? 0 }}
                                })">
                                    <i class="fas fa-shopping-cart"></i>
                                    <span>Add</span>
                                </button>
                                @endif
                                @else
                                <button onclick="showLoginPrompt()" class="flex-1 flex items-center justify-center gap-1 bg-red-600 text-white px-3 py-2 rounded-lg text-sm hover:bg-red-700 transition-colors">
                                    <i class="fas fa-sign-in-alt"></i>
                                    <span>Login</span>
                                </button>
                                @endauth
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Merchandise Section -->
            @if(count($merchandise) > 0)
            <div id="merchandise-section" class="mb-20 product-section">
                <div class="flex items-center justify-center mb-12 section-header">
                    <div class="h-0.5 bg-gray-200 w-16 mr-4"></div>
                    <h2 class="text-3xl font-bold text-center">Merchandise</h2>
                    <div class="h-0.5 bg-gray-200 w-16 ml-4"></div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
                    @foreach($merchandise as $product)
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden transform transition-all duration-300 hover:shadow-xl hover:-translate-y-1 h-full flex flex-col product-item" data-category="{{ $product->category }}">
                        <div class="relative h-64 overflow-hidden">
                            @if($product->image)
                            <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-contain bg-gray-50 p-4" loading="lazy">
                            @else
                            <div class="w-full h-full flex items-center justify-center bg-gray-100">
                                <i class="fas fa-box text-gray-300 text-4xl"></i>
                            </div>
                            @endif
                        </div>
                        <div class="p-4 flex-grow flex flex-col justify-between">
                            <div>
                                <h3 class="text-md font-semibold mb-1 product-name">{{ $product->name }}</h3>
                                @if(isset($product->discount) && $product->discount > 0)
                                    <div class="mb-1">
                                        <span class="line-through text-gray-400 text-sm">₱{{ number_format($product->price, 2) }}</span>
                                        <span class="text-red-600 font-bold">₱{{ number_format($product->price * (1 - $product->discount/100), 2) }}</span>
                                        <span class="bg-red-600 text-white text-xs px-2 py-0.5 rounded-full ml-1">{{ $product->discount }}% OFF</span>
                                    </div>
                                @else
                                    <p class="text-red-600 font-bold mb-3">₱{{ number_format($product->price, 2) }}</p>
                                @endif
                            </div>
                            <div class="flex justify-between items-center pt-2 gap-2">
                                <button type="button" 
                                    onclick="showProductModal({{ $product->id }}, '{{ $product->name }}', '{{ $product->price }}', '{{ $product->image ? asset($product->image) : '' }}', '{{ addslashes($product->description) }}', {{ $product->stock }}, '{{ $product->category }}', {{ $product->discount ?? 0 }})"
                                    class="flex-1 flex items-center justify-center gap-1 text-red-600 hover:text-white hover:bg-red-600 text-sm px-3 py-2 rounded-lg border border-red-600 transition-colors">
                                    <i class="fas fa-eye"></i>
                                    <span>View</span>
                                </button>
                                @auth
                                @if(Auth::user()->role === 'admin')
                                <button disabled class="flex-1 flex items-center justify-center gap-1 bg-gray-400 text-white px-3 py-2 rounded-lg text-sm cursor-not-allowed opacity-70">
                                    <i class="fas fa-lock"></i>
                                    <span>Admin</span>
                                </button>
                                @else
                                <button class="flex-1 flex items-center justify-center gap-1 bg-red-600 text-white px-3 py-2 rounded-lg text-sm hover:bg-red-700 transition-colors {{ $product->stock <= 0 ? 'opacity-50 cursor-not-allowed' : '' }}" {{ $product->stock <= 0 ? 'disabled' : '' }} onclick="window.addToCart({
                                    id: {{ $product->id }},
                                    name: '{{ addslashes($product->name) }}',
                                    price: {{ $product->discount > 0 ? $product->price * (1 - $product->discount/100) : $product->price }},
                                    originalPrice: {{ $product->price }},
                                    image: '{{ $product->image ? asset($product->image) : '' }}',
                                    quantity: 1,
                                    stock: {{ $product->stock }},
                                    discount: {{ $product->discount ?? 0 }}
                                })">
                                    <i class="fas fa-shopping-cart"></i>
                                    <span>Add</span>
                                </button>
                                @endif
                                @else
                                <button onclick="showLoginPrompt()" class="flex-1 flex items-center justify-center gap-1 bg-red-600 text-white px-3 py-2 rounded-lg text-sm hover:bg-red-700 transition-colors">
                                    <i class="fas fa-sign-in-alt"></i>
                                    <span>Login</span>
                                </button>
                                @endauth
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Drinks & Supplements Section -->
            @if(count($drinksAndSupplements) > 0)
            <div id="drinks-section" class="product-section">
                <div class="flex items-center justify-center mb-12 section-header">
                    <div class="h-0.5 bg-gray-200 w-16 mr-4"></div>
                    <h2 class="text-3xl font-bold text-center">Drinks & Supplements</h2>
                    <div class="h-0.5 bg-gray-200 w-16 ml-4"></div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
                    @foreach($drinksAndSupplements as $product)
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden transform transition-all duration-300 hover:shadow-xl hover:-translate-y-1 h-full flex flex-col product-item" data-category="{{ $product->category }}">
                        <div class="relative h-64 overflow-hidden">
                            @if($product->image)
                            <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-contain bg-gray-50 p-4" loading="lazy">
                            @else
                            <div class="w-full h-full flex items-center justify-center bg-gray-100">
                                <i class="fas fa-box text-gray-300 text-4xl"></i>
                            </div>
                            @endif
                        </div>
                        <div class="p-4 flex-grow flex flex-col justify-between">
                            <div>
                                <h3 class="text-md font-semibold mb-1 product-name">{{ $product->name }}</h3>
                                @if(isset($product->discount) && $product->discount > 0)
                                    <div class="mb-1">
                                        <span class="line-through text-gray-400 text-sm">₱{{ number_format($product->price, 2) }}</span>
                                        <span class="text-red-600 font-bold">₱{{ number_format($product->price * (1 - $product->discount/100), 2) }}</span>
                                        <span class="bg-red-600 text-white text-xs px-2 py-0.5 rounded-full ml-1">{{ $product->discount }}% OFF</span>
                                    </div>
                                @else
                                    <p class="text-red-600 font-bold mb-3">₱{{ number_format($product->price, 2) }}</p>
                                @endif
                            </div>
                            <div class="flex justify-between items-center pt-2 gap-2">
                                <button type="button" 
                                    onclick="showProductModal({{ $product->id }}, '{{ $product->name }}', '{{ $product->price }}', '{{ $product->image ? asset($product->image) : '' }}', '{{ addslashes($product->description) }}', {{ $product->stock }}, '{{ $product->category }}', {{ $product->discount ?? 0 }})"
                                    class="flex-1 flex items-center justify-center gap-1 text-red-600 hover:text-white hover:bg-red-600 text-sm px-3 py-2 rounded-lg border border-red-600 transition-colors">
                                    <i class="fas fa-eye"></i>
                                    <span>View</span>
                                </button>
                                @auth
                                @if(Auth::user()->role === 'admin')
                                <button disabled class="flex-1 flex items-center justify-center gap-1 bg-gray-400 text-white px-3 py-2 rounded-lg text-sm cursor-not-allowed opacity-70">
                                    <i class="fas fa-lock"></i>
                                    <span>Admin</span>
                                </button>
                                @else
                                <button class="flex-1 flex items-center justify-center gap-1 bg-red-600 text-white px-3 py-2 rounded-lg text-sm hover:bg-red-700 transition-colors {{ $product->stock <= 0 ? 'opacity-50 cursor-not-allowed' : '' }}" {{ $product->stock <= 0 ? 'disabled' : '' }} onclick="window.addToCart({
                                    id: {{ $product->id }},
                                    name: '{{ addslashes($product->name) }}',
                                    price: {{ $product->discount > 0 ? $product->price * (1 - $product->discount/100) : $product->price }},
                                    originalPrice: {{ $product->price }},
                                    image: '{{ $product->image ? asset($product->image) : '' }}',
                                    quantity: 1,
                                    stock: {{ $product->stock }},
                                    discount: {{ $product->discount ?? 0 }}
                                })">
                                    <i class="fas fa-shopping-cart"></i>
                                    <span>Add</span>
                                </button>
                                @endif
                                @else
                                <button onclick="showLoginPrompt()" class="flex-1 flex items-center justify-center gap-1 bg-red-600 text-white px-3 py-2 rounded-lg text-sm hover:bg-red-700 transition-colors">
                                    <i class="fas fa-sign-in-alt"></i>
                                    <span>Login</span>
                                </button>
                                @endauth
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </section>
</div>

<!-- SweetAlert for notifications -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Pagination Component -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const productSearch = document.getElementById('productSearch');
    if(productSearch) {
        productSearch.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const allProducts = document.querySelectorAll('.product-item');
            
            allProducts.forEach(product => {
                const productName = product.querySelector('.product-name').textContent.toLowerCase();
                const productCategory = product.dataset.category.toLowerCase();
                
                if (productName.includes(searchTerm) || productCategory.includes(searchTerm)) {
                    product.style.display = 'flex';
                } else {
                    product.style.display = 'none';
                }
            });
            
            // Check if sections are empty
            document.querySelectorAll('.product-section').forEach(section => {
                const visibleProducts = section.querySelectorAll('.product-item[style="display: flex;"]');
                const sectionHeader = section.querySelector('.section-header');
                
                if (visibleProducts.length === 0) {
                    sectionHeader.style.display = 'none';
                } else {
                    sectionHeader.style.display = 'flex';
                }
            });
        });
    }
    
    // Smooth scroll for category navigation
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);
            
            if (targetElement) {
                window.scrollTo({
                    top: targetElement.offsetTop - 100,
                    behavior: 'smooth'
                });
            }
        });
    });
});
</script>

<script>
// Show login prompt for non-authenticated users
function showLoginPrompt() {
    Swal.fire({
        title: 'Login Required',
        text: 'Please login or sign up to add items to your cart',
        icon: 'info',
        confirmButtonColor: '#EF4444',
        showCancelButton: true,
        confirmButtonText: 'Login',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '{{ route("login") }}';
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    // Update product buttons for non-authenticated users
    @guest
    const addButtons = document.querySelectorAll('.product-item button:nth-child(2)');
    addButtons.forEach(button => {
        button.removeAttribute('onclick');
        button.addEventListener('click', showLoginPrompt);
    });
    @endguest
    
    // ... existing code ...
});
</script>

@endsection