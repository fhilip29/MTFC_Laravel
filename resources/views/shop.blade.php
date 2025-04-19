@extends('layouts.app')

@section('content')
<!-- Add Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<!-- Product Detail Modal -->
<div x-data="{ showModal: false, product: null, quantity: 1 }" x-cloak>
    <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black opacity-60 transition-opacity" @click="showModal = false"></div>
            <div class="relative bg-white rounded-xl max-w-2xl w-full mx-auto shadow-2xl transform transition-all" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform scale-100" x-transition:leave-end="opacity-0 transform scale-95">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-5 border-b">
                    <h3 class="text-xl font-bold" x-text="product?.name"></h3>
                    <button @click="showModal = false" class="text-gray-500 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-red-600 focus:ring-opacity-50 rounded-full p-1">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <!-- Modal body -->
                <div class="p-6">
                    <div class="flex flex-col md:flex-row gap-6">
                        <div class="md:w-1/2">
                            <div class="w-full h-72 bg-gray-100 rounded-lg flex items-center justify-center overflow-hidden shadow-inner">
                                <template x-if="product?.image">
                                    <img :src="product?.image" :alt="product?.name" class="w-full h-72 object-contain rounded-lg p-2 transition-all duration-300 hover:scale-105">
                                </template>
                                <template x-if="!product?.image">
                                    <div class="text-gray-400 text-5xl">
                                        <i class="fas fa-box"></i>
                                    </div>
                                </template>
                            </div>
                            <div class="mt-4" x-show="product?.category">
                                <span class="inline-block bg-gray-200 text-gray-800 text-sm px-3 py-1 rounded-full">
                                    <span x-text="product?.category"></span>
                                </span>
                            </div>
                        </div>
                        <div class="md:w-1/2 space-y-5">
                            <p class="text-3xl font-bold text-red-600" x-text="'₱' + parseFloat(product?.price).toFixed(2)"></p>
                            <template x-if="product?.stock !== undefined">
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm font-medium text-gray-500">Available Quantity:</span>
                                    <span x-text="product?.stock" class="text-sm font-medium"></span>
                                </div>
                            </template>
                            <div class="py-2">
                                <h4 class="text-sm font-medium text-gray-500 mb-1">Description:</h4>
                                <p class="text-gray-700" x-text="product?.description || 'No description available'"></p>
                            </div>
                            <div x-show="product?.stock > 0" class="flex flex-col space-y-2">
                                <div class="flex items-center space-x-3">
                                    <label class="text-sm font-medium text-gray-500">Quantity:</label>
                                    <div class="flex items-center">
                                        <button @click="quantity = Math.max(1, quantity - 1)" class="bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-l-md w-9 h-9 flex items-center justify-center focus:outline-none">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <input type="number" x-model.number="quantity" min="1" :max="product?.stock" class="w-12 h-9 text-center border-gray-200 focus:ring-0 focus:outline-none" />
                                        <button @click="quantity = Math.min(product?.stock, quantity + 1)" class="bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-r-md w-9 h-9 flex items-center justify-center focus:outline-none">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                                <button 
                                    class="w-full bg-red-600 text-white px-6 py-3 rounded-lg hover:bg-red-700 transition-all flex items-center justify-center gap-2 shadow-md transform hover:-translate-y-1 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50"
                                    :disabled="product?.stock <= 0 || quantity <= 0 || quantity > product?.stock"
                                    :class="{'opacity-50 cursor-not-allowed': product?.stock <= 0 || quantity <= 0 || quantity > product?.stock}"
                                    @click="
                                        if (!(product?.stock <= 0 || quantity <= 0 || quantity > product?.stock)) {
                                            addToCart({
                                                id: product?.id,
                                                name: product?.name,
                                                price: product?.price,
                                                image: product?.image,
                                                quantity: quantity,
                                                stock: product?.stock
                                            });
                                            showModal = false;
                                        }
                                    ">
                                    <i class="fas fa-shopping-cart"></i>
                                    <span>Add to Cart</span>
                                </button>
                            </div>
                            <div x-show="product?.stock <= 0" class="pt-2">
                                <button 
                                    class="w-full bg-gray-400 text-white px-6 py-3 rounded-lg cursor-not-allowed flex items-center justify-center gap-2 shadow-md opacity-70">
                                    <i class="fas fa-shopping-cart"></i>
                                    <span>Out of Stock</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Modal footer -->
                <div class="px-6 py-4 border-t border-gray-200 flex justify-end">
                    <button @click="showModal = false" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-opacity-50">
                        Close
                    </button>
                </div>
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

<!-- Category Navigation -->
<div class="bg-white sticky top-0 z-30 shadow-md py-4 border-b">
    <div class="container mx-auto px-4">
        <div class="flex justify-center space-x-4 overflow-x-auto pb-1 flex-nowrap">
            <a href="#equipment-section" class="whitespace-nowrap px-5 py-2 rounded-full border border-gray-200 hover:border-red-500 hover:text-red-600 transition font-medium text-sm">
                <i class="fas fa-dumbbell mr-2"></i>Equipment
            </a>
            <a href="#merchandise-section" class="whitespace-nowrap px-5 py-2 rounded-full border border-gray-200 hover:border-red-500 hover:text-red-600 transition font-medium text-sm">
                <i class="fas fa-tshirt mr-2"></i>Merchandise
            </a>
            <a href="#drinks-section" class="whitespace-nowrap px-5 py-2 rounded-full border border-gray-200 hover:border-red-500 hover:text-red-600 transition font-medium text-sm">
                <i class="fas fa-wine-bottle mr-2"></i>Drinks & Supplements
            </a>
        </div>
    </div>
</div>

<!-- Products Section -->
<section id="products" class="pt-16 pb-24 bg-gray-50">
    <div class="container mx-auto px-4">
        <!-- Equipment Section -->
        @if(count($equipment) > 0)
        <div id="equipment-section" class="mb-20">
            <div class="flex items-center justify-center mb-12">
                <div class="h-0.5 bg-gray-200 w-16 mr-4"></div>
                <h2 class="text-3xl font-bold text-center">Equipment</h2>
                <div class="h-0.5 bg-gray-200 w-16 ml-4"></div>
            </div>
            <div class="relative">
                <div class="overflow-hidden px-12 md:px-20">
                    <div class="flex transition-transform duration-500 ease-in-out" id="equipment-carousel">
                        @foreach($equipment as $product)
                        <div class="w-full md:w-1/5 flex-shrink-0 px-3">
                            <div class="bg-white rounded-xl shadow-lg overflow-hidden transform transition-all duration-300 hover:shadow-xl hover:-translate-y-1 h-full flex flex-col">
                                <div class="relative">
                                    @if($product->image)
                                    <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="w-full h-48 object-cover">
                                    @else
                                    <div class="w-full h-48 flex items-center justify-center bg-gray-100">
                                        <i class="fas fa-box text-gray-300 text-4xl"></i>
                                    </div>
                                    @endif
                                </div>
                                <div class="p-4 flex-grow flex flex-col justify-between">
                                    <div>
                                        <h3 class="text-md font-semibold mb-1">{{ $product->name }}</h3>
                                        <p class="text-red-600 font-bold mb-3">₱{{ number_format($product->price, 2) }}</p>
                                    </div>
                                    <div class="flex justify-between items-center pt-2 gap-2">
                                        <button @click="showModal = true; product = {
                                            id: {{ $product->id }},
                                            name: '{{ $product->name }}',
                                            price: '{{ $product->price }}',
                                            image: '{{ $product->image ? asset($product->image) : '' }}',
                                            description: '{{ addslashes($product->description) }}',
                                            stock: {{ $product->stock }},
                                            category: '{{ $product->category }}'
                                        }; quantity = 1;" 
                                        class="flex-1 flex items-center justify-center gap-1 text-red-600 hover:text-white hover:bg-red-600 text-sm px-3 py-2 rounded-lg border border-red-600 transition-colors">
                                            <i class="fas fa-eye"></i>
                                            <span>View</span>
                                        </button>
                                        <button class="flex-1 flex items-center justify-center gap-1 bg-red-600 text-white px-3 py-2 rounded-lg text-sm hover:bg-red-700 transition-colors {{ $product->stock <= 0 ? 'opacity-50 cursor-not-allowed' : '' }}" {{ $product->stock <= 0 ? 'disabled' : '' }} onclick="addToCart({
                                            id: {{ $product->id }},
                                            name: '{{ addslashes($product->name) }}',
                                            price: {{ $product->price }},
                                            image: '{{ $product->image ? asset($product->image) : '' }}',
                                            quantity: 1,
                                            stock: {{ $product->stock }}
                                        })">
                                            <i class="fas fa-shopping-cart"></i>
                                            <span>Add</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <!-- Navigation Arrows -->
                <button class="absolute left-0 top-1/2 -translate-y-1/2 text-white bg-red-600 hover:bg-red-700 p-3 rounded-full shadow-lg transition-all transform hover:scale-110 focus:outline-none" onclick="moveCarousel('equipment-carousel', -1)">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button class="absolute right-0 top-1/2 -translate-y-1/2 text-white bg-red-600 hover:bg-red-700 p-3 rounded-full shadow-lg transition-all transform hover:scale-110 focus:outline-none" onclick="moveCarousel('equipment-carousel', 1)">
                    <i class="fas fa-chevron-right"></i>
                </button>
                <!-- Pagination dots -->
                <div class="flex justify-center mt-6 gap-2" id="equipment-carousel-dots"></div>
            </div>
        </div>
        @endif

        <!-- Merchandise Section -->
        @if(count($merchandise) > 0)
        <div id="merchandise-section" class="mb-20">
            <div class="flex items-center justify-center mb-12">
                <div class="h-0.5 bg-gray-200 w-16 mr-4"></div>
                <h2 class="text-3xl font-bold text-center">Merchandise</h2>
                <div class="h-0.5 bg-gray-200 w-16 ml-4"></div>
            </div>
            <div class="relative">
                <div class="overflow-hidden px-12 md:px-20">
                    <div class="flex transition-transform duration-500 ease-in-out" id="merchandise-carousel">
                        @foreach($merchandise as $product)
                        <div class="w-full md:w-1/5 flex-shrink-0 px-3">
                            <div class="bg-white rounded-xl shadow-lg overflow-hidden transform transition-all duration-300 hover:shadow-xl hover:-translate-y-1 h-full flex flex-col">
                                <div class="relative">
                                    @if($product->image)
                                    <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="w-full h-48 object-cover">
                                    @else
                                    <div class="w-full h-48 flex items-center justify-center bg-gray-100">
                                        <i class="fas fa-box text-gray-300 text-4xl"></i>
                                    </div>
                                    @endif
                                </div>
                                <div class="p-4 flex-grow flex flex-col justify-between">
                                    <div>
                                        <h3 class="text-md font-semibold mb-1">{{ $product->name }}</h3>
                                        <p class="text-red-600 font-bold mb-3">₱{{ number_format($product->price, 2) }}</p>
                                    </div>
                                    <div class="flex justify-between items-center pt-2 gap-2">
                                        <button @click="showModal = true; product = {
                                            id: {{ $product->id }},
                                            name: '{{ $product->name }}',
                                            price: '{{ $product->price }}',
                                            image: '{{ $product->image ? asset($product->image) : '' }}',
                                            description: '{{ addslashes($product->description) }}',
                                            stock: {{ $product->stock }},
                                            category: '{{ $product->category }}'
                                        }; quantity = 1;" 
                                        class="flex-1 flex items-center justify-center gap-1 text-red-600 hover:text-white hover:bg-red-600 text-sm px-3 py-2 rounded-lg border border-red-600 transition-colors">
                                            <i class="fas fa-eye"></i>
                                            <span>View</span>
                                        </button>
                                        <button class="flex-1 flex items-center justify-center gap-1 bg-red-600 text-white px-3 py-2 rounded-lg text-sm hover:bg-red-700 transition-colors {{ $product->stock <= 0 ? 'opacity-50 cursor-not-allowed' : '' }}" {{ $product->stock <= 0 ? 'disabled' : '' }} onclick="addToCart({
                                            id: {{ $product->id }},
                                            name: '{{ addslashes($product->name) }}',
                                            price: {{ $product->price }},
                                            image: '{{ $product->image ? asset($product->image) : '' }}',
                                            quantity: 1,
                                            stock: {{ $product->stock }}
                                        })">
                                            <i class="fas fa-shopping-cart"></i>
                                            <span>Add</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <!-- Navigation Arrows -->
                <button class="absolute left-0 top-1/2 -translate-y-1/2 text-white bg-red-600 hover:bg-red-700 p-3 rounded-full shadow-lg transition-all transform hover:scale-110 focus:outline-none" onclick="moveCarousel('merchandise-carousel', -1)">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button class="absolute right-0 top-1/2 -translate-y-1/2 text-white bg-red-600 hover:bg-red-700 p-3 rounded-full shadow-lg transition-all transform hover:scale-110 focus:outline-none" onclick="moveCarousel('merchandise-carousel', 1)">
                    <i class="fas fa-chevron-right"></i>
                </button>
                <!-- Pagination dots -->
                <div class="flex justify-center mt-6 gap-2" id="merchandise-carousel-dots"></div>
            </div>
        </div>
        @endif

        <!-- Drinks & Supplements Section -->
        @if(count($drinksAndSupplements) > 0)
        <div id="drinks-section">
            <div class="flex items-center justify-center mb-12">
                <div class="h-0.5 bg-gray-200 w-16 mr-4"></div>
                <h2 class="text-3xl font-bold text-center">Drinks & Supplements</h2>
                <div class="h-0.5 bg-gray-200 w-16 ml-4"></div>
            </div>
            <div class="relative">
                <div class="overflow-hidden px-12 md:px-20">
                    <div class="flex transition-transform duration-500 ease-in-out" id="drinks-carousel">
                        @foreach($drinksAndSupplements as $product)
                        <div class="w-full md:w-1/5 flex-shrink-0 px-3">
                            <div class="bg-white rounded-xl shadow-lg overflow-hidden transform transition-all duration-300 hover:shadow-xl hover:-translate-y-1 h-full flex flex-col">
                                <div class="relative">
                                    @if($product->image)
                                    <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="w-full h-48 object-cover">
                                    @else
                                    <div class="w-full h-48 flex items-center justify-center bg-gray-100">
                                        <i class="fas fa-box text-gray-300 text-4xl"></i>
                                    </div>
                                    @endif
                                </div>
                                <div class="p-4 flex-grow flex flex-col justify-between">
                                    <div>
                                        <h3 class="text-md font-semibold mb-1">{{ $product->name }}</h3>
                                        <p class="text-red-600 font-bold mb-3">₱{{ number_format($product->price, 2) }}</p>
                                    </div>
                                    <div class="flex justify-between items-center pt-2 gap-2">
                                        <button @click="showModal = true; product = {
                                            id: {{ $product->id }},
                                            name: '{{ $product->name }}',
                                            price: '{{ $product->price }}',
                                            image: '{{ $product->image ? asset($product->image) : '' }}',
                                            description: '{{ addslashes($product->description) }}',
                                            stock: {{ $product->stock }},
                                            category: '{{ $product->category }}'
                                        }; quantity = 1;" 
                                        class="flex-1 flex items-center justify-center gap-1 text-red-600 hover:text-white hover:bg-red-600 text-sm px-3 py-2 rounded-lg border border-red-600 transition-colors">
                                            <i class="fas fa-eye"></i>
                                            <span>View</span>
                                        </button>
                                        <button class="flex-1 flex items-center justify-center gap-1 bg-red-600 text-white px-3 py-2 rounded-lg text-sm hover:bg-red-700 transition-colors {{ $product->stock <= 0 ? 'opacity-50 cursor-not-allowed' : '' }}" {{ $product->stock <= 0 ? 'disabled' : '' }} onclick="addToCart({
                                            id: {{ $product->id }},
                                            name: '{{ addslashes($product->name) }}',
                                            price: {{ $product->price }},
                                            image: '{{ $product->image ? asset($product->image) : '' }}',
                                            quantity: 1,
                                            stock: {{ $product->stock }}
                                        })">
                                            <i class="fas fa-shopping-cart"></i>
                                            <span>Add</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <!-- Navigation Arrows -->
                <button class="absolute left-0 top-1/2 -translate-y-1/2 text-white bg-red-600 hover:bg-red-700 p-3 rounded-full shadow-lg transition-all transform hover:scale-110 focus:outline-none" onclick="moveCarousel('drinks-carousel', -1)">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button class="absolute right-0 top-1/2 -translate-y-1/2 text-white bg-red-600 hover:bg-red-700 p-3 rounded-full shadow-lg transition-all transform hover:scale-110 focus:outline-none" onclick="moveCarousel('drinks-carousel', 1)">
                    <i class="fas fa-chevron-right"></i>
                </button>
                <!-- Pagination dots -->
                <div class="flex justify-center mt-6 gap-2" id="drinks-carousel-dots"></div>
            </div>
        </div>
        @endif
    </div>
</section>

<!-- Carousel JavaScript -->
<script>
const carousels = {
    'equipment-carousel': { currentPosition: 0 },
    'merchandise-carousel': { currentPosition: 0 },
    'drinks-carousel': { currentPosition: 0 }
};

function moveCarousel(carouselId, direction) {
    const container = document.getElementById(carouselId);
    if (!container) return;
    
    const itemCount = container.children.length;
    const visibleItems = window.innerWidth >= 1280 ? 5 : window.innerWidth >= 1024 ? 4 : window.innerWidth >= 768 ? 3 : 1;
    const maxPosition = Math.max(-(itemCount - visibleItems) * 20, -80);

    carousels[carouselId].currentPosition = Math.max(
        Math.min(carousels[carouselId].currentPosition + (direction * 20), 0),
        maxPosition
    );

    container.style.transform = `translateX(${carousels[carouselId].currentPosition}%)`;
    updateDots(carouselId);
}

function createDots() {
    Object.keys(carousels).forEach(carouselId => {
        const container = document.getElementById(carouselId);
        if (!container) return;
        
        const dotsContainer = document.getElementById(`${carouselId}-dots`);
        const itemCount = container.children.length;
        const visibleItems = window.innerWidth >= 1280 ? 5 : window.innerWidth >= 1024 ? 4 : window.innerWidth >= 768 ? 3 : 1;
        const dotCount = Math.max(Math.ceil(itemCount / visibleItems), 1);

        dotsContainer.innerHTML = '';
        for (let i = 0; i < dotCount; i++) {
            const dot = document.createElement('div');
            dot.className = 'w-2.5 h-2.5 rounded-full bg-gray-300 opacity-40 hover:opacity-100 transition-all duration-300';
            dot.addEventListener('click', () => {
                const newPosition = -i * 20 * visibleItems;
                carousels[carouselId].currentPosition = Math.max(newPosition, -(itemCount - visibleItems) * 20);
                container.style.transform = `translateX(${carousels[carouselId].currentPosition}%)`;
                updateDots(carouselId);
            });
            dotsContainer.appendChild(dot);
        }
        updateDots(carouselId);
    });
}

function updateDots(carouselId) {
    const dotsContainer = document.getElementById(`${carouselId}-dots`);
    if (!dotsContainer) return;
    
    const dots = dotsContainer.children;
    const container = document.getElementById(carouselId);
    if (!container) return;
    
    const itemCount = container.children.length;
    const visibleItems = window.innerWidth >= 1280 ? 5 : window.innerWidth >= 1024 ? 4 : window.innerWidth >= 768 ? 3 : 1;
    const position = Math.abs(carousels[carouselId].currentPosition / 20);
    const activeDotIndex = Math.floor(position / visibleItems);

    for (let i = 0; i < dots.length; i++) {
        if (i === activeDotIndex) {
            dots[i].classList.add('bg-red-600', 'opacity-100', 'transform', 'scale-110');
            dots[i].classList.remove('bg-gray-300', 'opacity-40');
        } else {
            dots[i].classList.remove('bg-red-600', 'opacity-100', 'transform', 'scale-110');
            dots[i].classList.add('bg-gray-300', 'opacity-40');
        }
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    createDots();
    
    // Handle window resize
    window.addEventListener('resize', function() {
        createDots();
        
        // Reset positions on resize
        Object.keys(carousels).forEach(carouselId => {
            const container = document.getElementById(carouselId);
            if (container) {
                carousels[carouselId].currentPosition = 0;
                container.style.transform = 'translateX(0%)';
            }
        });
    });
    
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

// Add slow zoom animation
document.addEventListener('DOMContentLoaded', function() {
    // Add this to your existing styles or in a style tag
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slow-zoom {
            0% {
                transform: scale(1);
            }
            100% {
                transform: scale(1.1);
            }
        }
        
        .animate-slow-zoom {
            animation: slow-zoom 30s linear infinite alternate;
        }
    `;
    document.head.appendChild(style);
});
</script>
@endsection