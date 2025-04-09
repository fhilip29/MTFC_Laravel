<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MTFC</title>
    <meta name="description" content="sample description">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Include fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Geist&family=Geist+Mono&display=swap" rel="stylesheet">
    
    <!-- Your global CSS (like globals.css) -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/uploadthing.css') }}" rel="stylesheet">

    @vite(['resources/js/app.js']) <!-- If using Vite with Laravel -->
</head>
<body class="antialiased overflow-x-hidden font-sans">

    @include('components.client-header')
    @include('components.cart-drawer')
    @include('components.my-profile-modal')
    @include('components.view-product-modal')

    <main>
        @yield('content')
    </main>

    @include('components.delayed-footer')

</body>
</html>
