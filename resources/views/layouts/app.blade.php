<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ActiveGym</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Fonts & CSS -->
    <link href="https://fonts.googleapis.com/css2?family=Geist&display=swap" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased font-sans overflow-x-hidden bg-white text-black flex flex-col min-h-screen">

    @include('components.header')

    <!-- Optional: Add modals like cart/profile -->
    @include('components.cart-drawer')
    @include('components.my-profile-modal')
    @include('components.view-product-modal')

    <main class="flex-grow">
        @yield('content')
    </main>

    @include('components.footer')

</body>
</html>
