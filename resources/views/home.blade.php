
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
</head>
<body>

@section('content')

<!-- Hero Section -->
<section class="hero" style="background-image: url('{{ asset('assets/hero.jpg') }}');">
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <h1>Manila Total Fitness Center:</h1>
        <h2>Prepare Yourself At All Times</h2>
        <p>
            Achieve your fitness goals with personalized plans, real-time availability updates, and a supportive community.
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
            <button class="community-button">Join the Community Now!</button>
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
        <button class="about-button">Learn more</button>
    </div>
</section>


<!-- Products Section (Carousel) -->
@php
        $products = [
            [
                'name' => 'Resistance Bands',
                'price' => 499,
                'imgUrl' => 'https://via.placeholder.com/150?text=Resistance+Bands',
            ],
            [
                'name' => 'Yoga Mat',
                'price' => 699,
                'imgUrl' => 'https://via.placeholder.com/150?text=Yoga+Mat',
            ],
            [
                'name' => 'Dumbbells Set',
                'price' => 1999,
                'imgUrl' => 'https://via.placeholder.com/150?text=Dumbbells+Set',
            ],
            [
                'name' => 'Pull-up Bar',
                'price' => 999,
                'imgUrl' => 'https://via.placeholder.com/150?text=Pull-up+Bar',
            ],
            [
                'name' => 'Kettlebell',
                'price' => 849,
                'imgUrl' => 'https://via.placeholder.com/150?text=Kettlebell',
            ],
            [
                'name' => 'Protein Shaker',
                'price' => 349,
                'imgUrl' => 'https://via.placeholder.com/150?text=Protein+Shaker',
            ],
            [
                'name' => 'Treadmill',
                'price' => 25999,
                'imgUrl' => 'https://via.placeholder.com/150?text=Treadmill',
            ],
            [
                'name' => 'Stationary Bike',
                'price' => 18999,
                'imgUrl' => 'https://via.placeholder.com/150?text=Stationary+Bike',
            ],
        ];

        $chunks = collect($products)->chunk(4);
    @endphp
    <section class="bg-[#121212] products-section py-16 " data-animate>
    <div class=" container my-10">
        <h2 class="text-2xl font-bold mb-4 text-center">Top Rated Products</h2>

        <div id="topItemsCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                @foreach ($chunks as $chunkIndex => $chunk)
                    <div class="carousel-item {{ $chunkIndex === 0 ? 'active' : '' }}">
                        <div class=" d-flex justify-content-center gap-4 px-4">
                            @foreach ($chunk as $product)
                                <div class=" card text-center " style="width: 16rem;">
                                    <img src="{{ $product['imgUrl'] }}" class="card-img-top" alt="{{ $product['name'] }}">
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $product['name'] }}</h5>
                                        <p class="card-text font-semibold">₱{{ number_format($product['price'], 2) }}</p>
                                        <a href="#" class="btn btn-primary">Add to Cart</a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <button class="carousel-control-prev" type="button" data-bs-target="#topItemsCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#topItemsCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
        </div>
    </div>

    </section>

        <!-- Swiper Pagination & Navigation -->
        <div class="swiper-pagination mt-4"></div>
        <div class="swiper-button-prev"></div>
        <div class="swiper-button-next"></div>
    </div>
</section>

<!-- Scripts -->
<script>
    // Animate on Scroll
    document.addEventListener('DOMContentLoaded', function () {
        const elements = document.querySelectorAll('[data-animate]');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('active');
                }
            });
        }, { threshold: 0.2 });

        elements.forEach((el) => observer.observe(el));
    });

    // Hero Text Typing Animation
    document.addEventListener('DOMContentLoaded', () => {
        const heroText = document.querySelector('.hero-content h1');
        let text = heroText.textContent;
        heroText.textContent = '';
        let index = 0;

        function typeText() {
            if (index < text.length) {
                heroText.textContent += text[index];
                index++;
                setTimeout(typeText, 100);
            }
        }

        typeText();
    });

    // Buttons
    document.querySelector('.community-button').addEventListener('click', () => {
        window.location.href = '{{ url("community_dashboard") }}';
    });

    document.querySelector('.about-button').addEventListener('click', () => {
        window.location.href = '{{ url("about-us") }}';
    });

    document.querySelector('.products-button').addEventListener('click', () => {
        window.location.href = '{{ url("products") }}';
    });

    const swiper = new Swiper('.swiper-container', {
        slidesPerView: 1,
        spaceBetween: 20,
        loop: true,
        pagination: {
            el: '.swiper-pagination',
            clickable: true
        },
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        breakpoints: {
            640: {
                slidesPerView: 1.5,
            },
            768: {
                slidesPerView: 2,
            },
            1024: {
                slidesPerView: 3,
            }
        }
    });
</script>

@endsection

</body>
</html>

