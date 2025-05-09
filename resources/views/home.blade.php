@extends('layouts.app')

@section('title', 'Home')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manila Total Fitness Center</title>

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">

    <!-- Custom Home CSS -->
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
     <!-- SwiperJS CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    
    <!-- Add CSRF token meta tag for AJAX requests -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>

@section('content')

<!-- Hero Section -->
<section class="hero h-[800px]" style="background-image: url('{{ asset('assets/hero.jpg') }}'); height: 900px; max-height: 900px; position: relative;">
    <div class="hero-overlay" style="height: 100%; position: absolute; top: 0; left: 0; right: 0; bottom: 0;"></div>
    <div class="hero-content" style="position: relative; height: 100%; display: flex; flex-direction: column; justify-content: center; padding: 0 2rem;">
        <h1>Manila Total Fitness Center:</h1>
        <h2>Prepare Yourself At All Times</h2>
        <p>
        Unleash your full potential with custom fitness plans, live class updates, and a community that drives you to succeed.
        </p>
    </div>
</section>



<!-- Community Dashboard Section -->
<section class="community-dashboard" data-animate>
    <h2>Community Dashboard</h2>
    <div class="dashboard-container">
        <div class="dashboard-image">
            <img src="{{ asset('assets/dashboard.png') }}" alt="Community">
        </div>
        <div class="dashboard-content">
            <div class="message">
                <div class="icon"><i class="fas fa-reply"></i></div>
                <div class="text">
                    <span class="name">Sarah</span>
                    <p>Welcome, Mike! I'd recommend checking out the personalized plans feature - it helped me a lot when I was starting out. Also, don't miss the Zumba classes on Tuesdays, they're a blast!</p>
                </div>
            </div>
            <div class="message">
                <div class="icon"><i class="fas fa-dumbbell"></i></div>
                <div class="text">
                    <span class="name">MTFC</span>
                    <p>Welcome to the ActiveGym community, Sarah! We're thrilled to have you. Don't hesitate to reach out if you have any questions.</p>
                </div>
            </div>
            <a href="{{ route('community') }}">
                <button class="community-button bg-red-900 text-white hover:bg-red-800 transition">
                Join the Community Now!
                </button>
            </a>
        </div>
    </div>
</section>

<!-- About Us Section -->
<section class="about-us" data-animate>
    <h2>About Us</h2>
    <h3>Stronger Together, Healthier Forever</h3>
    <div class="about-container"> 
        <p>Manila Total Fitness Center is dedicated to helping you achieve a healthier, stronger lifestyle. With top-notch equipment, expert guidance, and a supportive community, we empower you to reach your fitness goals and embrace wellness as a way of life.</p>
        <div class="about-images">
            <img src="{{ asset('assets/about_1.jpg') }}" alt="Fitness Image 1">
            <img src="{{ asset('assets/about_2.jpg') }}" alt="Fitness Image 2">
        </div>
        <a href="{{ route('about') }}">
            <button class="about-button bg-red-900 text-white hover:bg-red-800 transition">
            Learn more
            </button>
        </a>
    </div>
</section>


<!-- Top Rated Products Section -->
@php
    $chunks = $topRatedProducts->chunk(4);
@endphp

<div class="bg-[#121212] mb-0 pb-16">
    <section class="products-section py-16 pb-16" x-data="cartHandler">
        <div class="container mx-auto my-10">
            <h2 class="text-center mb-8 text-white">Most Purchased Products</h2>

            @if($topRatedProducts->isEmpty())
                <p class="text-center text-white text-lg">No Products Available Yet</p>
            @else
                <div id="topItemsCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        @foreach ($chunks as $chunkIndex => $chunk)
                            <div class="carousel-item {{ $chunkIndex === 0 ? 'active' : '' }}">
                                <div class="d-flex justify-content-center gap-4 flex-wrap">
                                    @foreach ($chunk as $product)
                                        <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-4" style="width: 16rem; max-width: 100%;">
                                            <img src="{{ asset($product->image) }}" class="w-full h-40 object-cover" alt="{{ $product->name }}">

                                            <div class="p-3">
                                                <h3 class="text-md font-semibold mb-1">{{ $product->name }}</h3>
                                                <p class="text-gray-600 text-sm mb-2">₱{{ number_format($product->price, 2) }}</p>
                                                <div class="flex justify-between items-center">
                                                    <button @click="showProductModal({
                                                        id: {{ $product->id }},
                                                        name: '{{ addslashes($product->name) }}',
                                                        price: {{ $product->price }},
                                                        image: '{{ asset($product->image) }}',
                                                        description: '{{ addslashes($product->description) }}',
                                                        stock: {{ $product->stock }}
                                                    })" class="flex items-center gap-2 text-red-600 hover:text-red-800 text-sm px-3 py-1 rounded-full border border-red-600 hover:bg-red-50 transition-all">
                                                        <i class="fas fa-eye"></i>
                                                        <span>View</span>
                                                    </button>

                                                    <button @click="addToCart({
                                                        id: {{ $product->id }},
                                                        name: '{{ addslashes($product->name) }}',
                                                        price: {{ $product->price }},
                                                        image: '{{ asset($product->image) }}',
                                                        stock: {{ $product->stock }}
                                                    })" class="flex items-center gap-2 bg-red-600 text-white px-3 py-1 rounded-full text-sm hover:bg-red-700 transition-all">
                                                        <i class="fas fa-shopping-cart"></i>
                                                        <span>Add</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Product Modal -->
        <div x-show="modalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="modalOpen" @click="modalOpen = false" class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>
                <!-- Modal Content -->
                <div x-show="modalOpen" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900" x-text="activeProduct?.name"></h3>
                                    <button @click="modalOpen = false" class="text-gray-400 hover:text-gray-500">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <div class="mt-2 flex flex-col md:flex-row gap-4">
                                    <div class="md:w-1/3">
                                        <img :src="activeProduct?.image" class="w-full rounded-lg" :alt="activeProduct?.name">
                                    </div>
                                    <div class="md:w-2/3">
                                        <p class="text-sm text-gray-500 mb-2">Product Details:</p>
                                        <p class="text-sm text-gray-700 mb-4" x-text="activeProduct?.description || 'High-quality fitness equipment designed for both home and gym use. Durable materials ensure long-lasting performance.'"></p>
                                        <p class="text-lg font-bold text-gray-900 mb-4">₱<span x-text="Number(activeProduct?.price).toFixed(2)"></span></p>
                                        @auth
                                        <button @click="addToCart(activeProduct); modalOpen = false" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition">
                                            Add to Cart
                                        </button>
                                        @else
                                        <button @click="showLoginPrompt()" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition">
                                            Login to Add to Cart
                                        </button>
                                        @endauth
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Toast Notification -->
        <div x-show="toastVisible" x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 translate-x-10"
             x-transition:enter-end="opacity-100 translate-x-0"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="opacity-100 translate-x-0"
             x-transition:leave-end="opacity-0 translate-x-10"
             class="fixed bottom-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg">
            Item added to cart!
        </div>
    </section>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('cartHandler', () => ({
            cartItems: JSON.parse(localStorage.getItem('cart')) || [],
            toastVisible: false,
            modalOpen: false,
            activeProduct: null,

            // Show product modal
            showProductModal(product) {
                this.activeProduct = product;
                this.modalOpen = true;
            },

            // Add item to cart
            addToCart(product) {
                @auth
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
                    confirmButtonText: 'View Cart'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Open cart drawer instead of redirecting
                        const cartDrawer = document.getElementById('cartDrawer');
                        if (cartDrawer) {
                            cartDrawer.classList.remove('translate-x-full');
                            if (typeof window.renderCart === 'function') {
                                window.renderCart();
                            }
                        }
                    }
                });
                
                // Sync with server
                this.syncCartWithServer(cart);
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
            },

            // Sync cart with server
            syncCartWithServer(cart) {
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
            },

            // Show login prompt
            showLoginPrompt() {
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
        }));
    });
</script>





@endsection

</body>
</html>

