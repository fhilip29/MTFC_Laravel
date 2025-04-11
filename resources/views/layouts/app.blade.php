<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'ActiveGym')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- ✅ Tailwind CSS CDN (Quick Fix) -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- ✅ Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 text-gray-900">

    @include('components.header')

    <main>
        @yield('content')
    </main>

    @include('components.footer')

</body>
</html>
