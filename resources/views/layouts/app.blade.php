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

    /* Optional: Make sure Swiper arrows don’t show if not used */
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
    <div class="p-4 space-y-4">
        <div class="flex items-center space-x-4 border-b pb-4">
            <img src="{{ asset('assets/Product2_MTFC.jpg') }}" alt="Product" class="w-16 h-16 object-cover">
            <div class="flex-1">
                <h4 class="text-md font-semibold">Product Name</h4>
                <p class="text-sm text-gray-500">₱500</p>
                <div class="flex items-center space-x-2 mt-1">
                    <button class="bg-gray-200 px-2 py-1">-</button>
                    <span>1</span>
                    <button class="bg-gray-200 px-2 py-1">+</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Total and Checkout -->
    <div class="p-4 border-t">
        <div class="flex justify-between text-md mb-2">
            <span>Total Quantity:</span>
            <span>2</span>
        </div>
        <div class="flex justify-between text-md mb-4">
            <span>Total Price:</span>
            <span>₱1000</span>
        </div>
        <button class="w-full bg-black text-white py-2 rounded hover:bg-gray-800 transition">Proceed to Checkout</button>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const cartButton = document.getElementById('cartButton');
        const cartDrawer = document.getElementById('cartDrawer');
        const closeCart = document.getElementById('closeCart');

        if (cartButton && cartDrawer && closeCart) {
            cartButton.addEventListener('click', () => {
                cartDrawer.classList.remove('translate-x-full');
            });

            closeCart.addEventListener('click', () => {
                cartDrawer.classList.add('translate-x-full');
            });

            // Optional: close drawer if clicking outside
            window.addEventListener('click', (e) => {
                if (!cartDrawer.contains(e.target) && !cartButton.contains(e.target)) {
                    cartDrawer.classList.add('translate-x-full');
                }
            });
        }
    });
</script>


</body>
</html>
