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

    <!-- Animation Styles -->
    <style>
        /* Hero image animation */
        .hero {
            opacity: 0;
            transition: opacity 1.5s ease-in-out;
            background-size: cover;
            background-position: center;
        }
        
        .hero.loaded {
            opacity: 1;
        }
        
        .hero-content h1, 
        .hero-content h2, 
        .hero-content p {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.8s ease, transform 0.8s ease;
            transition-delay: 0.5s;
        }
        
        .hero-content h2 {
            transition-delay: 0.7s;
        }
        
        .hero-content p {
            transition-delay: 0.9s;
        }
        
        .hero.loaded .hero-content h1,
        .hero.loaded .hero-content h2,
        .hero.loaded .hero-content p {
            opacity: 1;
            transform: translateY(0);
        }
        
        /* Scroll reveal animation for sections */
        .reveal {
            opacity: 0;
            transform: translateY(30px); /* Reduced from 50px for less white gap */
            transition: opacity 0.8s ease, transform 0.8s ease;
            background-color: #1e1e1e; /* Add dark background to match the page */
        }
        
        .reveal.active {
            opacity: 1;
            transform: translateY(0);
        }
        
        /* Staggered animation for grid items */
        .grid-item {
            opacity: 0;
            transform: translateY(20px); /* Reduced from 30px */
            transition: opacity 0.5s ease, transform 0.5s ease;
        }
        
        .grid-item.active {
            opacity: 1;
            transform: translateY(0);
        }
        
        /* Fix background color for the entire page */
        body {
            background-color: #1e1e1e;
        }
        
        /* Ensure containers maintain dark background */
        .container {
            background-color: #1e1e1e;
        }
        
        /* Prevent white background flashes */
        html {
            background-color: #1e1e1e;
        }
        
        /* Fix for section gaps */
        section {
            margin-top: -1px; /* Prevent gap between sections */
            position: relative;
            z-index: 1;
            background-color: #1e1e1e;
        }
        
        /* Content area fix */
        #app, main {
            background-color: #1e1e1e;
        }
    </style>
</head>
<body>

@section('content')

<div style="background-color: #1e1e1e;">
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
<section class="community-dashboard bg-[#1e1e1e] py-16 relative overflow-hidden reveal">
    <!-- Background pattern -->
    <div class="absolute inset-0 opacity-5">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%23ffffff\" fill-opacity=\"1\"%3E%3Cpath d=\"M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')"></div>
    </div>

    <div class="container mx-auto px-4 relative z-10">
        <div class="text-center mb-12 reveal">
            <span class="inline-block px-3 py-1 bg-[#FA5455] text-white text-xs font-bold rounded-full mb-3">COMMUNITY</span>
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">Community Dashboard</h2>
            <p class="text-gray-400 max-w-xl mx-auto">Connect with fellow fitness enthusiasts and get inspired on your wellness journey.</p>
        </div>

        <div class="dashboard-container flex flex-col lg:flex-row items-start gap-8">
            <div class="dashboard-image lg:w-1/2 reveal">
                <img src="{{ asset('assets/dashboard.png') }}" alt="Community" class="rounded-xl shadow-2xl transform hover:-translate-y-2 transition-all duration-500 w-full">
            </div>
            
            <div class="dashboard-content lg:w-1/2">
                <div class="space-y-4">
                    <div class="message bg-[#222] p-4 rounded-xl shadow-lg transform hover:-translate-y-1 transition-all duration-300 reveal">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-10 h-10 overflow-hidden rounded-full">
                                <img src="{{ asset('assets/profile1.jpg') }}" alt="Sarah" class="w-full h-full object-cover" onerror="this.src='https://ui-avatars.com/api/?name=Sarah&background=FA5455&color=fff'">
                            </div>
                            <div class="text">
                                <span class="name block text-white font-bold mb-1">Sarah</span>
                                <p class="text-gray-300 text-sm">Welcome, Mike! I'd recommend checking out the personalized plans feature - it helped me a lot when I was starting out. Also, don't miss the Zumba classes on Tuesdays, they're a blast!</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="message bg-[#222] p-4 rounded-xl shadow-lg transform hover:-translate-y-1 transition-all duration-300 reveal">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-10 h-10 overflow-hidden rounded-full">
                                <img src="{{ asset('assets/profile2.jpg') }}" alt="MTFC" class="w-full h-full object-cover" onerror="this.src='https://ui-avatars.com/api/?name=MTFC&background=FA5455&color=fff'">
                            </div>
                            <div class="text">
                                <span class="name block text-white font-bold mb-1">MTFC</span>
                                <p class="text-gray-300 text-sm">Welcome to the ActiveGym community, Sarah! We're thrilled to have you. Don't hesitate to reach out if you have any questions.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6 text-center reveal">
                        <a href="{{ route('community') }}">
                            <button class="community-button bg-[#FA5455] text-white hover:bg-[#e84142] transition px-8 py-3 rounded-lg font-semibold transform hover:-translate-y-1 hover:shadow-lg">
                                <i class="fas fa-users mr-2"></i> Join the Community Now!
                            </button>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- About Us Section -->
<section class="about-us bg-[#1e1e1e] py-16 relative overflow-hidden reveal">
    <!-- Background pattern -->
    <div class="absolute inset-0 opacity-5">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%23ffffff\" fill-opacity=\"1\"%3E%3Cpath d=\"M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')"></div>
    </div>

    <div class="container mx-auto px-4 relative z-10">
        <div class="text-center mb-12 reveal">
            <span class="inline-block px-3 py-1 bg-[#FA5455] text-white text-xs font-bold rounded-full mb-3">OUR STORY</span>
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">About Us</h2>
            <h3 class="text-xl text-[#FA5455] mb-4">Stronger Together, Healthier Forever</h3>
        </div>

        <div class="about-container max-w-4xl mx-auto"> 
            <p class="text-gray-300 text-center mb-8 reveal">Manila Total Fitness Center is dedicated to helping you achieve a healthier, stronger lifestyle. With top-notch equipment, expert guidance, and a supportive community, we empower you to reach your fitness goals and embrace wellness as a way of life.</p>
            
            <div class="about-images flex flex-col md:flex-row gap-6 mb-8 reveal">
                <img src="{{ asset('assets/about_1.jpg') }}" alt="Fitness Image 1" class="rounded-xl shadow-lg w-full">
                <img src="{{ asset('assets/about_2.jpg') }}" alt="Fitness Image 2" class="rounded-xl shadow-lg w-full">
            </div>
            
            <div class="text-center reveal">
                <a href="{{ route('about') }}">
                    <button class="about-button bg-[#FA5455] text-white hover:bg-[#e84142] transition px-8 py-3 rounded-lg font-semibold transform hover:-translate-y-1 hover:shadow-lg">
                        <i class="fas fa-info-circle mr-2"></i> Learn More
                    </button>
                </a>
            </div>
        </div>
    </div>
</section>


<!-- Top Rated Products Section -->
@php
    $topFourProducts = $topRatedProducts->take(4);
@endphp

<div class="bg-[#1e1e1e] mb-0 py-16 relative overflow-hidden reveal">
    <!-- Background pattern -->
    <div class="absolute inset-0 opacity-5">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%23ffffff\" fill-opacity=\"1\"%3E%3Cpath d=\"M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')"></div>
    </div>

    <section class="products-section relative z-10" x-data="cartHandler">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12 reveal">
                <span class="inline-block px-3 py-1 bg-[#FA5455] text-white text-xs font-bold rounded-full mb-3">TOP SELLERS</span>
                <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">Most Purchased Products</h2>
                <p class="text-gray-400 max-w-xl mx-auto">Quality fitness equipment and merchandise to support your journey to a stronger, healthier you.</p>
            </div>

            @if($topFourProducts->isEmpty())
                <p class="text-center text-white text-lg">No Products Available Yet</p>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($topFourProducts as $product)
                        <div class="bg-[#222] rounded-xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 h-full flex flex-col group grid-item">
                            <div class="relative overflow-hidden h-56">
                                <img src="{{ asset($product->image) }}" class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-500" alt="{{ $product->name }}">
                                <div class="absolute inset-0 bg-gradient-to-t from-black to-transparent opacity-60"></div>
                                <div class="absolute top-4 right-4">
                                    <span class="bg-[#FA5455] text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg">HOT</span>
                                </div>
                            </div>
                            
                            <div class="p-5 flex-1 flex flex-col">
                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="text-lg font-bold text-white">{{ $product->name }}</h3>
                                </div>
                                
                                <p class="text-gray-400 text-sm mb-4 flex-1">{{ \Illuminate\Support\Str::limit($product->description, 60) }}</p>
                                
                                <div class="mt-auto">
                                    <div class="flex justify-between items-center mb-4">
                                        <span class="text-xl font-bold text-white">₱{{ number_format($product->price, 2) }}</span>
                                        <span class="text-sm text-gray-400">{{ $product->stock }} in stock</span>
                                    </div>
                                    
                                    <div class="flex space-x-2">
                                        <button @click="showProductModal({
                                            id: {{ $product->id }},
                                            name: '{{ addslashes($product->name) }}',
                                            price: {{ $product->price }},
                                            image: '{{ asset($product->image) }}',
                                            description: '{{ addslashes($product->description) }}',
                                            stock: {{ $product->stock }}
                                        })" class="flex-1 bg-[#333] text-white py-2 px-4 rounded-lg hover:bg-[#444] transition flex items-center justify-center">
                                            <i class="fas fa-eye mr-2"></i> View
                                        </button>
                                        
                                        <button @click="{{ $product->stock > 0 ? 'addToCart({
                                            id: ' . $product->id . ',
                                            name: \'' . addslashes($product->name) . '\',
                                            price: ' . $product->price . ',
                                            image: \'' . asset($product->image) . '\',
                                            stock: ' . $product->stock . '
                                        })' : 'void(0)' }}" class="flex-1 bg-[#FA5455] text-white py-2 px-4 rounded-lg hover:bg-[#e84142] transition flex items-center justify-center {{ $product->stock <= 0 ? 'opacity-60 cursor-not-allowed !bg-gray-500 hover:!bg-gray-500' : '' }}">
                                            <i class="fas {{ $product->stock > 0 ? 'fa-shopping-cart' : 'fa-ban' }} mr-2"></i> {{ $product->stock > 0 ? 'Add' : 'Sold Out' }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="text-center mt-10 reveal">
                    <a href="{{ route('shop') }}" class="inline-block bg-[#FA5455] hover:bg-[#e84142] text-white font-semibold py-3 px-8 rounded-lg transition duration-300 transform hover:-translate-y-1 hover:shadow-lg no-underline">
                        View All Products <i class="fas fa-arrow-right ml-2"></i>
                    </a>
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
                                        <p class="text-lg font-bold text-gray-900 mb-2">₱<span x-text="Number(activeProduct?.price).toFixed(2)"></span></p>
                                        <p class="text-sm text-gray-600 mb-4">Stock: <span x-text="activeProduct?.stock || 0"></span> available</p>
                                        @auth
                                        <button 
                                            @click="activeProduct.stock > 0 ? (addToCart(activeProduct), modalOpen = false) : void(0)" 
                                            class="bg-[#FA5455] text-white px-4 py-2 rounded hover:bg-[#e84142] transition w-full"
                                            :class="{'opacity-60 cursor-not-allowed !bg-gray-500 hover:!bg-gray-500': activeProduct?.stock <= 0}"
                                        >
                                            <span x-show="activeProduct?.stock > 0">Add to Cart</span>
                                            <span x-show="activeProduct?.stock <= 0">Out of Stock</span>
                                        </button>
                                        @else
                                        <button @click="showLoginPrompt()" class="bg-[#FA5455] text-white px-4 py-2 rounded hover:bg-[#e84142] transition w-full">
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

<!-- Add dark spacer div for consistent spacing before footer -->
<div class="bg-[#1e1e1e] py-16"></div>
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

    // Hero image loading animation
    document.addEventListener('DOMContentLoaded', function() {
        // Add loaded class to hero after a small delay for smoother animation
        setTimeout(function() {
            const hero = document.querySelector('.hero');
            if (hero) {
                hero.classList.add('loaded');
            }
        }, 200);
        
        // Initialize scroll animations
        initScrollAnimations();
    });
    
    // Scroll animation function
    function initScrollAnimations() {
        // Reveal elements on scroll
        const revealElements = document.querySelectorAll('.reveal');
        const gridItems = document.querySelectorAll('.grid-item');
        
        // Create an observer for the reveal elements
        const observerReveal = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    // Add a slight delay before animating to prevent flash
                    setTimeout(() => {
                        entry.target.classList.add('active');
                    }, 50);
                    observerReveal.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.05, // Reduced threshold to start animation earlier
            rootMargin: '0px 0px -10px 0px' // Changed from -50px to -10px
        });
        
        // Observe each reveal element
        revealElements.forEach(el => {
            // Apply background color immediately to prevent white flash
            el.style.backgroundColor = '#1e1e1e';
            observerReveal.observe(el);
        });
        
        // Create an observer for the grid items with staggered animation
        const observerGrid = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    // Add staggered delay based on index
                    setTimeout(() => {
                        entry.target.classList.add('active');
                    }, 50 + (50 * (index % 4))); // Reduced from 100ms to 50ms per item
                    
                    observerGrid.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.05, // Reduced threshold
            rootMargin: '0px 0px -10px 0px' // Changed from -50px to -10px
        });
        
        // Observe each grid item
        gridItems.forEach((item, index) => {
            // Set transition delay inline for staggered effect
            item.style.transitionDelay = `${(index % 4) * 0.05}s`; // Reduced from 0.1s to 0.05s
            observerGrid.observe(item);
        });
    }
</script>

@endsection

</body>
</html>

