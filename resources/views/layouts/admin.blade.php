<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>MTFC Admin - @yield('title', 'Dashboard')</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon/favicon-16x16.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="48x48" href="{{ asset('favicon/favicon-48x48.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('favicon/favicon-192x192.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon/favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon/apple-touch-icon.png') }}">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- FilePond CSS and JS -->
    <link href="https://unpkg.com/filepond@^4/dist/filepond.css" rel="stylesheet" />
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet" />
    <script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
    <script src="https://unpkg.com/filepond@^4/dist/filepond.js"></script>
    
    @vite(['resources/css/app.css'])
    <style>
        body {
            background-color: #111827;
            min-height: 100vh;
        }

        .sidebar {
            width: 280px;
            min-height: 100vh;
            background: #1F2937;
            transition: all 0.3s ease;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 100;
        }

        .sidebar.collapsed {
            width: 80px;
        }

        .sidebar-header {
            padding: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            position: relative;
        }

        .logo-container {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding-left: 1rem;
        }

        .logo-container img {
            width: 170px;
            height: 170px;
            object-fit: contain;
        }

        .logo-text {
            color: white;
            font-size: 1.5rem;
            font-weight: bold;
            transition: opacity 0.3s ease;
        }

        .sidebar.collapsed .logo-text {
            opacity: 0;
            width: 0;
            overflow: hidden;
        }

        .toggle-btn {
            color: white;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 0.375rem;
            transition: background-color 0.3s ease;
        }

        .toggle-btn:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .nav-links {
            padding: 1rem;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            height: calc(100vh - 220px); /* Adjust based on header height */
            overflow-y: auto;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.75rem 1rem;
            color: #9CA3AF;
            text-decoration: none;
            border-radius: 0.375rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .nav-link:hover,
        .nav-link.active {
            transform: translateX(4px);
            background-color: #374151;
            color: white;
        }

        .nav-link i {
            width: 1.5rem;
            text-align: center;
        }

        .nav-link span {
            transition: opacity 0.3s ease;
        }

        .sidebar.collapsed .nav-link span {
            opacity: 0;
            width: 0;
            overflow: hidden;
        }

        .main-content {
            margin-left: 280px;
            min-height: 100vh;
            transition: margin-left 0.3s ease;
            padding-top: 4rem;
            background-color: #111827;
        }

        .main-content.expanded {
            margin-left: 80px;
        }

        .nav-button {
            position: absolute;
            top: 0.5rem;
            color: white;
            padding: 0.25rem;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .nav-button:hover {
            color: #4B5563;
            transform: scale(1.1);
        }

        .nav-button.home {
            left: 1rem;
        }

        .nav-button.profile {
            right: 1rem;
        }

        .nav-button i {
            font-size: 1rem;
        }

        .sidebar.collapsed .nav-button {
            opacity: 0;
            width: 0;
            height: 0;
            overflow: hidden;
        }
        
        /* Dropdown menu styles */
        .dropdown-menu {
            position: absolute;
            right: 0;
            top: 2rem;
            width: 220px;
            background-color: #1F2937;
            border: 1px solid #4B5563;
            border-radius: 0.375rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            z-index: 50;
            transform-origin: top right;
            opacity: 0;
            transform: scale(0.95);
            pointer-events: none;
            transition: all 0.2s ease;
        }
        
        .dropdown-menu.active {
            opacity: 1;
            transform: scale(1);
            pointer-events: auto;
        }
        
        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            color: #D1D5DB;
            border-bottom: 1px solid #374151;
            transition: all 0.2s ease;
        }
        
        .dropdown-item:last-child {
            border-bottom: none;
        }
        
        .dropdown-item:hover {
            background-color: #374151;
            color: white;
        }
        
        .dropdown-item i {
            width: 1.25rem;
            text-align: center;
        }
        
        .dropdown-item.danger {
            color: #EF4444;
        }
        
        .dropdown-item.danger:hover {
            background-color: #991B1B;
            color: white;
        }
        
        /* Logout button at bottom */
        .logout-button {
            margin-top: auto;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 1rem;
            margin-top: 1rem;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                position: fixed;
                top: 0;
                left: 0;
                height: 100vh;
                width: 280px;
                z-index: 50;
                box-shadow: 4px 0 10px rgba(0, 0, 0, 0.1);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
                padding-top: 4rem;
            }

            .mobile-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.5);
                z-index: 40;
            }

            .mobile-overlay.show {
                display: block;
            }
        }

        /* Page Loader Styles */
        #page-loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(17, 24, 39, 0.9); /* Darker background to match admin theme */
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
    </style>
</head>
<body class="dark:bg-gray-900">
<!-- Page Loader -->
@include('components.loader')

<!-- Sidebar -->
<nav class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="logo-container">
            <a href="/admin/dashboard" title="Go to Dashboard">
                <img src="{{ asset('assets/MTFC_LOGO.PNG') }}" alt="MTFC Logo">
            </a>
        </div>
        <a href="/" class="nav-button home" title="Go to Website">
            <i class="fas fa-home text-xl"></i>
        </a>
        <a href="/admin/profile" class="nav-button profile" title="Profile Settings">
            <div class="w-8 h-8 rounded-full overflow-hidden border-2 border-white">
                <img src="{{ Auth::user()->profile_image ? asset(Auth::user()->profile_image) : asset('assets/default-profile.jpg') }}" 
                     alt="Admin Profile" class="w-full h-full object-cover">
            </div>
        </a>
        <button class="toggle-btn" id="sidebarToggle">
            <i class="fas fa-chevron-left"></i>
        </button>
    </div>
    <div class="nav-links">
        <a href="/admin/dashboard" class="nav-link {{ request()->is('admin/dashboard') ? 'active' : '' }}">
            <i class="fas fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
        <a href="/admin/members/admin_members" class="nav-link {{ request()->is('admin/members/admin_members') ? 'active' : '' }}">
            <i class="fas fa-users"></i>
            <span>Manage Members</span>
        </a>
        <a href="/admin/session/admin_session" class="nav-link {{ request()->is('admin/session/admin_session') ? 'active' : '' }}">
            <i class="fas fa-calendar-alt"></i>
            <span>Session Management</span>
        </a>
        <a href="{{ route('admin.invoice.invoice') }}" class="nav-link {{ request()->is('admin/invoice*') ? 'active' : '' }}">
            <i class="fas fa-file-invoice"></i>
            <span>Manage Invoice</span>
        </a>
        <a href="{{ route('admin.pricing.index') }}" class="nav-link {{ request()->is('admin/pricing*') || request()->is('admin/sports*') ? 'active' : '' }}">
            <i class="fas fa-tags"></i>
            <span>Pricing Management</span>
        </a>
        <a href="/admin/trainer/admin_trainer" class="nav-link {{ request()->is('admin/trainer/admin_trainer') ? 'active' : '' }}">
            <i class="fas fa-user-tie"></i>
            <span>Trainer Management</span>
        </a>
        <a href="/admin/promotion/admin_promo" class="nav-link {{ request()->is('admin/promotion/admin_promo') ? 'active' : '' }}">
            <i class="fas fa-bullhorn"></i>
            <span>Announce Management</span>
        </a>
        <a href="/admin/orders/admin_orders" class="nav-link {{ request()->is('admin/orders/admin_orders') ? 'active' : '' }}">
            <i class="fas fa-shopping-cart"></i>
            <span>Manage Orders</span>
        </a>
        <a href="/admin/products" class="nav-link {{ request()->is('admin/products') ? 'active' : '' }}">
            <i class="fas fa-box"></i>
            <span>Product Management</span>
        </a>
        <a href="/admin/equipment" class="nav-link {{ request()->is('admin/equipment') ? 'active' : '' }}">
            <i class="fas fa-dumbbell"></i>
            <span>Gym Equipment</span>
        </a>
        
        <!-- Logout Button at the bottom -->
        <button onclick="confirmAdminLogout()" class="nav-link logout-button text-red-400 hover:text-red-300">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </button>
    </div>
</nav>

<main class="main-content" id="mainContent">
    @yield('content')
</main>

<form id="admin-logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
    @csrf
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const icon = sidebarToggle.querySelector('i');

    sidebarToggle.addEventListener('click', function() {
        sidebar.classList.toggle('collapsed');
        mainContent.classList.toggle('expanded');

        // Toggle icon direction
        if (sidebar.classList.contains('collapsed')) {
            icon.classList.remove('fa-chevron-left');
            icon.classList.add('fa-chevron-right');
        } else {
            icon.classList.remove('fa-chevron-right');
            icon.classList.add('fa-chevron-left');
        }
    });

    // Create mobile overlay
    const mobileOverlay = document.createElement('div');
    mobileOverlay.className = 'mobile-overlay';
    document.body.appendChild(mobileOverlay);

    // Mobile menu toggle
    const mobileToggle = document.createElement('button');
    mobileToggle.className = 'fixed top-4 left-4 z-50 md:hidden bg-gray-800 text-white p-2 rounded-lg hover:bg-gray-700 transition-colors';
    mobileToggle.innerHTML = '<i class="fas fa-bars text-xl"></i>';
    document.body.appendChild(mobileToggle);

    mobileToggle.addEventListener('click', function() {
        sidebar.classList.toggle('show');
        mobileOverlay.classList.toggle('show');
        document.body.style.overflow = sidebar.classList.contains('show') ? 'hidden' : '';
    });

    mobileOverlay.addEventListener('click', function() {
        sidebar.classList.remove('show');
        mobileOverlay.classList.remove('show');
        document.body.style.overflow = '';
    });
});

function confirmAdminLogout() {
    console.log('confirmAdminLogout called');
    Swal.fire({
        title: '<span class="text-2xl font-bold">Logout Confirmation</span>',
        html: '<p class="text-lg mt-2">Are you sure you want to log out from the admin panel?</p>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#DC2626',
        cancelButtonColor: '#4B5563',
        confirmButtonText: '<i class="fas fa-sign-out-alt mr-2"></i>Yes, log me out!',
        cancelButtonText: '<i class="fas fa-times mr-2"></i>Cancel',
        background: '#1F2937',
        color: '#FFFFFF',
        customClass: {
            popup: 'rounded-xl border-2 border-red-500/20 shadow-2xl',
            title: 'text-white text-xl',
            htmlContainer: 'text-[#9CA3AF]',
            confirmButton: 'rounded-md px-4 py-2',
            cancelButton: 'rounded-md px-4 py-2'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            console.log('Logout confirmed, submitting form');
            
            // Get the form element explicitly
            const logoutForm = document.getElementById('admin-logout-form');
            
            if (!logoutForm) {
                console.error('Logout form not found!');
                // Fallback direct link if form not found
                window.location.href = '/logout';
                return;
            }
            
            // Ensure form has CSRF token
            if (!logoutForm.querySelector('input[name="_token"]')) {
                console.warn('CSRF token not found in form, adding it');
                const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                const tokenInput = document.createElement('input');
                tokenInput.type = 'hidden';
                tokenInput.name = '_token';
                tokenInput.value = csrfToken;
                logoutForm.appendChild(tokenInput);
            }
            
            try {
                logoutForm.submit();
            } catch (e) {
                console.error('Error submitting form:', e);
                // Fallback if form submission fails
                window.location.href = '/logout';
            }
        }
    });
}
</script>
@stack('scripts')
</body>
</html>
