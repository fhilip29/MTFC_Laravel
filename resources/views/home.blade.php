

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
</head>
<body>

@include('components.header')

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

<!-- Products Section (Moved Up Below Hero) -->
<section class="products" data-animate>
    <h2>Available Products</h2>
    <div class="products-container">
        <div class="product-item">
            <img src="{{ asset('assets/Product2_MTFC.jpg') }}" alt="Shoes">
            <p></p>
        </div>
        <div class="product-item">
            <img src="{{ asset('assets/Product3_MTFC.jpg') }}" alt="Shirt">
            <p></p>
        </div>
        <div class="product-item">
            <img src="{{ asset('assets/Product4_MTFC.jpg') }}" alt="Cap">
            <p></p>
        </div>
        <div class="product-item">
            <img src="{{ asset('assets/Product5_MTFC.jpg') }}" alt="Gloves">
            <p></p>
        </div>
        <div class="product-item">
            <img src="{{ asset('assets/Product5_MTFC.jpg') }}" alt="Other Product">
            <p></p>
        </div>
    </div>
    <button class="products-button">Browse Products</button>
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
</script>

@include('components.footer')

</body>
</html>

