@extends('layouts.app')

@section('content')
<!-- Add Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<!-- Hero Section -->
<section class="relative w-full h-[400px] overflow-hidden">
    <!-- Background image with overlay -->
    <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('{{ asset('assets/shopping.jpg') }}');"></div>
    <div class="absolute inset-0 bg-black bg-opacity-50"></div>
    
    <!-- Hero content -->
    <div class="relative h-full flex items-center justify-center text-center px-4">
        <div>
            <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">Don't Miss Out On Our Exclusive Deals</h1>
            <p class="text-xl text-white mb-8">Product Special</p>
            <a href="#products" class="bg-red-600 text-white px-8 py-3 rounded-full font-semibold hover:bg-red-700 transition">Shop Now</a>
        </div>
    </div>
</section>

<!-- Products Section -->
<section id="products" class="py-16 pb-24 bg-gray-100">
    <div class="container mx-auto px-4">
        <!-- Equipment Section -->
        <div class="mb-16">
            <h2 class="text-3xl font-bold text-center mb-8">Equipment</h2>
            <div class="relative">
                <div class="overflow-hidden">
                    <div class="flex transition-transform duration-500 ease-in-out" id="equipment-carousel">
                        <!-- Equipment Card 1 -->
                        <div class="w-full md:w-1/5 flex-shrink-0 px-2">
                            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                                <img src="{{ asset('assets/Product2_MTFC.jpg') }}" alt="Equipment 1" class="w-full h-40 object-cover">
                                <div class="p-3">
                                    <h3 class="text-md font-semibold mb-1">Dumbbells Set</h3>
                                    <p class="text-gray-600 text-sm mb-2">5-50 lbs</p>
                                    <div class="flex justify-between items-center">
                                        <button class="flex items-center gap-2 text-red-600 hover:text-red-800 text-sm px-4 py-2 rounded-full border border-red-600 hover:bg-red-50 transition-all">
                                            <i class="fas fa-eye"></i>
                                            <span>View</span>
                                        </button>
                                        <button class="flex items-center gap-2 bg-red-600 text-white px-4 py-2 rounded-full text-sm hover:bg-red-700 transition-all">
                                            <i class="fas fa-shopping-cart"></i>
                                            <span>Add to Cart</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Add more equipment cards here -->
                    </div>
                </div>
                <!-- Navigation Arrows -->
                <button class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-24 text-gray-500 p-4 transition-all hover:text-gray-700" onclick="moveCarousel('equipment-carousel', -1)">
                    <i class="fas fa-chevron-left text-2xl"></i>
                </button>
                <button class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-24 text-gray-500 p-4 transition-all hover:text-gray-700" onclick="moveCarousel('equipment-carousel', 1)">
                    <i class="fas fa-chevron-right text-2xl"></i>
                </button>
                <!-- Pagination dots -->
                <div class="flex justify-center mt-2 gap-1.5" id="equipment-carousel-dots"></div>
            </div>
        </div>  

        <!-- Merchandise Section -->
        <div class="mb-16">
            <h2 class="text-3xl font-bold text-center mb-8">Merchandise</h2>
            <div class="relative">
                <div class="overflow-hidden">
                    <div class="flex transition-transform duration-500 ease-in-out" id="merchandise-carousel">
                        <!-- Merchandise Card 1 -->
                        <div class="w-full md:w-1/5 flex-shrink-0 px-2">
                            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                                <img src="{{ asset('assets/Product3_MTFC.jpg') }}" alt="Merchandise 1" class="w-full h-40 object-cover">
                                <div class="p-3">
                                    <h3 class="text-md font-semibold mb-1">MTFC T-Shirt</h3>
                                    <p class="text-gray-600 text-sm mb-2">Various Sizes</p>
                                    <div class="flex justify-between items-center">
                                        <button class="flex items-center gap-2 text-red-600 hover:text-red-800 text-sm px-4 py-2 rounded-full border border-red-600 hover:bg-red-50 transition-all">
                                            <i class="fas fa-eye"></i>
                                            <span>View</span>
                                        </button>
                                        <button class="flex items-center gap-2 bg-red-600 text-white px-4 py-2 rounded-full text-sm hover:bg-red-700 transition-all">
                                            <i class="fas fa-shopping-cart"></i>
                                            <span>Add to Cart</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Add more merchandise cards here -->
                    </div>
                </div>
                <!-- Navigation Arrows -->
                <button class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-24 text-gray-500 p-4 transition-all hover:text-gray-700" onclick="moveCarousel('merchandise-carousel', -1)">
                    <i class="fas fa-chevron-left text-2xl"></i>
                </button>
                <button class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-24 text-gray-500 p-4 transition-all hover:text-gray-700" onclick="moveCarousel('merchandise-carousel', 1)">
                    <i class="fas fa-chevron-right text-2xl"></i>
                </button>
                <!-- Pagination dots -->
                <div class="flex justify-center mt-2 gap-1.5" id="merchandise-carousel-dots"></div>
            </div>
        </div>

        <!-- Drinks Section -->
        <div>
            <h2 class="text-3xl font-bold text-center mb-8">Drinks & Supplements</h2>
            <div class="relative">
                <div class="overflow-hidden">
                    <div class="flex transition-transform duration-500 ease-in-out" id="drinks-carousel">
                        <!-- Drinks Card 1 -->
                        <div class="w-full md:w-1/5 flex-shrink-0 px-2">
                            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                                <img src="{{ asset('assets/Product4_MTFC.jpg') }}" alt="Drink 1" class="w-full h-40 object-cover">
                                <div class="p-3">
                                    <h3 class="text-md font-semibold mb-1">Pre-Workout</h3>
                                    <p class="text-gray-600 text-sm mb-2">30 Servings</p>
                                    <div class="flex justify-between items-center">
                                        <button class="flex items-center gap-2 text-red-600 hover:text-red-800 text-sm px-4 py-2 rounded-full border border-red-600 hover:bg-red-50 transition-all">
                                            <i class="fas fa-eye"></i>
                                            <span>View</span>
                                        </button>
                                        <button class="flex items-center gap-2 bg-red-600 text-white px-4 py-2 rounded-full text-sm hover:bg-red-700 transition-all">
                                            <i class="fas fa-shopping-cart"></i>
                                            <span>Add to Cart</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Add more drinks cards here -->
                    </div>
                </div>
                <!-- Navigation Arrows -->
                <button class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-24 text-gray-500 p-4 transition-all hover:text-gray-700" onclick="moveCarousel('drinks-carousel', -1)">
                    <i class="fas fa-chevron-left text-2xl"></i>
                </button>
                <button class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-24 text-gray-500 p-4 transition-all hover:text-gray-700" onclick="moveCarousel('drinks-carousel', 1)">
                    <i class="fas fa-chevron-right text-2xl"></i>
                </button>
                <!-- Pagination dots -->
                <div class="flex justify-center mt-2 gap-1.5" id="drinks-carousel-dots"></div>
            </div>
        </div>
    </div>
</section>
            
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
    const itemCount = container.children.length;
    const visibleItems = 5;
    const maxPosition = -(itemCount - visibleItems) * 20;

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
        const dotsContainer = document.getElementById(`${carouselId}-dots`);
        const itemCount = container.children.length;
        const visibleItems = 5;
        const dotCount = Math.ceil(itemCount / visibleItems);

        for (let i = 0; i < dotCount; i++) {
            const dot = document.createElement('div');
            dot.className = 'w-1.5 h-1.5 rounded-full bg-gray-300 opacity-40 hover:opacity-100 transition-all duration-300';
            dotsContainer.appendChild(dot);
        }
        updateDots(carouselId);
    });
}

function updateDots(carouselId) {
    const dotsContainer = document.getElementById(`${carouselId}-dots`);
    const dots = dotsContainer.children;
    const currentPosition = Math.abs(carousels[carouselId].currentPosition / 20);

    Array.from(dots).forEach((dot, index) => {
        dot.className = `w-1.5 h-1.5 rounded-full transition-all duration-300 ${index === currentPosition / 5 ? 'bg-red-600 opacity-100' : 'bg-gray-300 opacity-40 hover:opacity-100'}`;
    });
}

// Initialize dots when the page loads
document.addEventListener('DOMContentLoaded', createDots);

// Auto-slide functionality
const autoSlideInterval = 5000;

Object.keys(carousels).forEach(carouselId => {
    setInterval(() => {
        const container = document.getElementById(carouselId);
        const itemCount = container.children.length;
        const visibleItems = 5;
        const maxPosition = -(itemCount - visibleItems) * 20;

        if (carousels[carouselId].currentPosition <= maxPosition) {
            carousels[carouselId].currentPosition = 0;
        } else {
            carousels[carouselId].currentPosition -= 20;
        }

        container.style.transform = `translateX(${carousels[carouselId].currentPosition}%)`;
        updateDots(carouselId);
    }, autoSlideInterval);
});
</script>
@endsection