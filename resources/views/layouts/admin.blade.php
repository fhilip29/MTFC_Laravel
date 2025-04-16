<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MTFC Admin - @yield('title', 'Dashboard')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
        }

        .nav-button:hover {
            color: #4B5563;
            transform: scale(1.1);
        }

        .nav-button.home {
            left: 0.5rem;
        }

        .nav-button.profile {
            right: 0.5rem;
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
    </style>
</head>
<body>
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="/" class="nav-button home" title="Go to Home">
                <i class="fas fa-home"></i>
            </a>
            <a href="/profile" class="nav-button profile" title="Go to Profile">
                <i class="fas fa-user"></i>
            </a>
            <div class="logo-container">
                <a href="/admin/dashboard" title="Go to Dashboard">
                    <img src="{{ asset('assets/MTFC_LOGO.PNG') }}" alt="MTFC Logo">
                </a>
            </div>
            <button class="toggle-btn" id="sidebarToggle">
                <i class="fas fa-chevron-left"></i>
            </button>
        </div>
        <div class="nav-links">
            <a href="/admin/dashboard" class="nav-link {{ request()->is('admin/dashboard') ? 'active' : '' }}">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
            <a href="/admin/members/admin_members" class="nav-link {{ request()->is('admin//membersadmin_members') ? 'active' : '' }}">
                <i class="fas fa-users"></i>
                <span>Manage Members</span>
            </a>
            <a href="/admin/orders" class="nav-link {{ request()->is('admin/orders') ? 'active' : '' }}">
                <i class="fas fa-shopping-cart"></i>
                <span>Manage Orders</span>
            </a>
            <a href="/admin/invoices" class="nav-link {{ request()->is('admin/invoices') ? 'active' : '' }}">
                <i class="fas fa-file-invoice"></i>
                <span>Manage Invoice</span>
            </a>
            <a href="/admin/sessions" class="nav-link {{ request()->is('admin/sessions') ? 'active' : '' }}">
                <i class="fas fa-calendar-alt"></i>
                <span>Session Management</span>
            </a>
            <a href="/admin/trainer/admin_trainer" class="nav-link {{ request()->is('admin/trainer/admin_trainer') ? 'active' : '' }}">
                <i class="fas fa-dumbbell"></i>
                <span>Trainer Management</span>
            </a>
            <a href="/admin/promotions" class="nav-link {{ request()->is('admin/promotions') ? 'active' : '' }}">
                <i class="fas fa-bullhorn"></i>
                <span>Announce Management</span>
            </a>
            <a href="/admin/equipment" class="nav-link {{ request()->is('admin/equipment') ? 'active' : '' }}">
                <i class="fas fa-dumbbell"></i>
                <span>Gym Equipment</span>
            </a>
            <a href="/admin/products" class="nav-link {{ request()->is('admin/products') ? 'active' : '' }}">
                <i class="fas fa-box"></i>
                <span>Product Management</span>
            </a>
        </div>
    </nav>

    <main class="main-content" id="mainContent">
        @yield('content')
    </main>

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
    </script>
</body>
</html>
