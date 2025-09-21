<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="auth-id" content="{{ auth()->id() ?? '' }}">
    <title id="pageTitle">{{ config('app.name', 'Aunty Anne\'s International School') }} |
        @yield('title', 'Admin Dashboard')</title>
    <meta name="description"
        content="@yield('description', 'Admin dashboard for managing students, staff, courses, grades, and announcements.')">
    <meta name="keywords" content="admin, school management, students, staff, courses, grades, announcements">
    <meta name="author" content="Aunty Anne's International School">

    <link rel="icon" type="image/png" href="{{ asset('images/favicons/favicon-96x96.png') }}" sizes="96x96">
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicons/favicon.svg') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/favicons/apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('images/favicons/site.webmanifest') }}">
    <link rel="canonical" href="https://auntyannesschools.com.ng">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=playfair-display:400,500,600,700|inter:400,500,600,700"
        rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- GSAP -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.4/gsap.min.js"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <!-- Vite -->
    @vite(['resources/js/app.js'])

    <style>
        :root {
            --primary-green: #21a055;
            --dark-green: #006400;
            --gold: #D4AF37;
            --white: #ffffff;
            --dark-gray: #6c757d;
            --light-gray: #f8f9fa;
            --font-display: "Playfair Display", serif;
            --font-primary: "Inter", sans-serif;
            --success: #16a34a;
            --error: #dc2626;
            --glass-bg: rgba(255, 255, 255, 0.05);
            --glass-border: rgba(255, 255, 255, 0.1);
            --gradient-primary: linear-gradient(135deg, var(--primary-green) 0%, var(--dark-green) 100%);
            --space-xs: 0.25rem;
            --space-sm: 0.5rem;
            --space-md: 1rem;
            --space-lg: 1.5rem;
            --space-xl: 2rem;
            --space-2xl: 3rem;
            --radius-md: 0.5rem;
            --radius-lg: 0.75rem;
            --radius-xl: 1rem;
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            --shadow-2xl: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            --text-primary: #1f2937;
            --text-secondary: #4b5563;
            --bg-primary: #f5f5f5;
            --bg-secondary: #e5e7eb;
        }

        html.dark {
            --text-primary: #f9fafb;
            --text-secondary: #d1d5db;
            --bg-primary: #0a0a0f;
            --bg-secondary: #1f2937;
            --light-gray: #1a1a2e;
            --glass-bg: rgba(255, 255, 255, 0.05);
            --glass-border: rgba(255, 255, 255, 0.1);
        }

        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--bg-secondary);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--primary-green);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--dark-green);
        }

        html {
            scroll-behavior: smooth;
            font-size: clamp(14px, 2.5vw, 16px);
            -webkit-tap-highlight-color: transparent;
        }

        body {
            font-family: var(--font-primary);
            background: var(--bg-primary);
            color: var(--text-primary);
            line-height: 1.6;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }

        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease;
        }

        .overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .sidebar {
            width: clamp(250px, 20vw, 280px);
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border-right: 1px solid var(--glass-border);
            padding: var(--space-lg);
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 1000;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow-y: auto;
        }

        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }
        }

        .sidebar-header {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: var(--space-xl);
            padding-bottom: var(--space-md);
            border-bottom: 1px solid var(--glass-border);
        }

        .logo {
            width: 80px;
            height: 80px;
            transition: transform 0.3s ease;
        }

        .logo:hover {
            transform: scale(1.05);
        }

        .logo img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            border: 3px solid var(--gold);
            object-fit: contain;
        }

        .nav-section-title {
            font-family: var(--font-display);
            font-size: clamp(0.75rem, 2vw, 0.875rem);
            font-weight: 600;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: var(--space-md);
            padding-left: var(--space-sm);
        }

        .nav-list li a,
        .nav-list li button {
            color: var(--text-secondary);
            text-decoration: none;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: var(--space-sm);
            padding: var(--space-sm) var(--space-md);
            border-radius: var(--radius-lg);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            font-size: clamp(0.875rem, 2.5vw, 1rem);
        }

        .nav-list li a:hover,
        .nav-list li button:hover {
            background: var(--glass-bg);
            color: var(--text-primary);
            transform: translateX(4px);
        }

        .nav-list li a.active {
            background: var(--gradient-primary);
            color: var(--white);
            box-shadow: var(--shadow-lg);
        }

        .nav-list .nav-sublist {
            margin-left: var(--space-md);
            margin-top: var(--space-sm);
        }

        .nav-link[aria-expanded="true"] i.bx-chevron-down {
            transform: rotate(180deg);
        }

        .nav-link i.bx-chevron-down {
            transition: transform 0.3s ease;
        }

        .main-content {
            flex: 1;
            margin-left: clamp(200px, 70vw, 280px);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        @media (max-width: 1024px) {
            .main-content {
                margin-left: 0;
            }
        }

        .top-nav {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--glass-border);
            padding: var(--space-md) var(--space-lg);
            position: sticky;
            top: 0;
            z-index: 999;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: var(--space-md);
        }

        .nav-left {
            display: flex;
            align-items: center;
            gap: var(--space-sm);
            flex: 1;
        }

        .menu-toggle {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            color: var(--text-primary);
            font-size: clamp(1rem, 3vw, 1.25rem);
            padding: var(--space-sm);
            border-radius: var(--radius-md);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .menu-toggle:hover,
        .menu-toggle:focus-visible {
            background: var(--primary-green);
            border-color: var(--primary-green);
            color: var(--white);
        }

        @media (min-width: 1024px) {
            .menu-toggle {
                display: none;
            }
        }

        .search-container {
            position: relative;
            max-width: clamp(150px, 40vw, 300px);
            width: 100%;
        }

        .search-input {
            width: 100%;
            background: var(--bg-secondary);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-xl);
            padding: var(--space-sm) var(--space-md) var(--space-sm) 2.5rem;
            color: var(--text-primary);
            font-size: clamp(0.75rem, 2.5vw, 0.875rem);
            transition: all 0.2s ease;
        }

        .search-input::placeholder {
            color: var(--text-secondary);
            opacity: 0.7;
        }

        .search-input:focus {
            outline: none;
            border-color: var(--gold);
            box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.2);
        }

        .search-icon {
            position: absolute;
            left: var(--space-sm);
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
            font-size: clamp(0.75rem, 2.5vw, 0.875rem);
        }

        .search-results {
            position: absolute;
            top: calc(100% + 8px);
            left: 0;
            right: 0;
            background: var(--bg-secondary);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-lg);
            padding: var(--space-sm);
            max-height: 300px;
            overflow-y: auto;
            box-shadow: var(--shadow-2xl);
            z-index: 1000;
            display: none;
        }

        .search-results.active {
            display: block;
        }

        .search-result-item {
            padding: var(--space-sm) var(--space-md);
            color: var(--text-primary);
            font-size: clamp(0.75rem, 2.5vw, 0.875rem);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .search-result-item:hover,
        .search-result-item:focus-visible {
            background: var(--primary-green);
            color: var(--white);
        }

        .search-result-item span {
            display: block;
            font-size: clamp(0.625rem, 2vw, 0.75rem);
            color: var(--text-secondary);
        }

        .nav-right {
            display: flex;
            align-items: center;
            gap: var(--space-sm);
        }

        .action-btn {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            color: var(--text-primary);
            font-size: clamp(0.75rem, 2.5vw, 0.875rem);
            padding: var(--space-sm);
            border-radius: var(--radius-md);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: var(--space-xs);
            transition: all 0.2s ease;
            position: relative;
        }

        .action-btn:hover,
        .action-btn:focus-visible {
            background: var(--primary-green);
            border-color: var(--primary-green);
            color: var(--white);
        }

        .action-btn .badge {
            background: var(--error);
            color: var(--white);
            font-size: clamp(0.5rem, 2vw, 0.625rem);
            padding: 0.125rem 0.375rem;
            border-radius: 10px;
            position: absolute;
            top: -4px;
            right: -4px;
        }

        .theme-btn {
            width: 36px;
            height: 36px;
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: var(--text-primary);
            transition: all 0.2s ease;
        }

        .theme-btn:hover,
        .theme-btn:focus-visible {
            background: var(--primary-green);
            border-color: var(--primary-green);
            color: var(--white);
            transform: scale(1.05);
        }

        .theme-btn .theme-icon-dark,
        .theme-btn .theme-icon-light {
            display: none;
        }

        .theme-btn.is-dark .theme-icon-dark {
            display: inline-block;
        }

        .theme-btn:not(.is-dark) .theme-icon-light {
            display: inline-block;
        }

        .notification-dropdown {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            background: var(--bg-secondary);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-lg);
            padding: var(--space-md);
            min-width: 300px;
            box-shadow: var(--shadow-2xl);
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
            z-index: 9999;
        }

        .notification-toggle.active+.notification-dropdown {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .notification-item {
            display: flex;
            align-items: center;
            gap: var(--space-sm);
            padding: var(--space-sm);
            border-bottom: 1px solid var(--glass-border);
            font-size: clamp(0.75rem, 2.5vw, 0.875rem);
            color: var(--text-primary);
        }

        .notification-item:last-child {
            border-bottom: none;
        }

        .notification-icon {
            width: 32px;
            height: 32px;
            background: var(--glass-bg);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            color: var(--success);
        }

        .notification-content h5 {
            font-size: clamp(0.75rem, 2.5vw, 0.875rem);
            font-weight: 600;
            margin-bottom: 4px;
            color: var(--text-primary);
        }

        .notification-content p {
            font-size: clamp(0.625rem, 2vw, 0.75rem);
            color: var(--text-secondary);
        }

        .notification-time {
            font-size: clamp(0.625rem, 2vw, 0.75rem);
            color: var(--text-secondary);
            margin-left: auto;
        }

        .user-menu {
            position: relative;
            display: flex;
            align-items: center;
        }

        .user-trigger {
            display: flex;
            align-items: center;
            gap: var(--space-sm);
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-md);
            padding: var(--space-sm);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .user-trigger:hover,
        .user-trigger:focus-visible {
            background: var(--primary-green);
            border-color: var(--primary-green);
            color: var(--white);
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            background: var(--gradient-primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: clamp(0.75rem, 2.5vw, 0.875rem);
            font-weight: 600;
            color: var(--white);
        }

        .user-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }

        .user-info h4 {
            font-size: clamp(0.75rem, 2.5vw, 0.875rem);
            font-weight: 500;
            color: var(--text-primary);
        }

        .dropdown-icon {
            font-size: clamp(0.75rem, 2.5vw, 0.875rem);
            color: var(--text-secondary);
        }

        .user-dropdown {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            background: var(--bg-secondary);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-lg);
            padding: var(--space-sm);
            min-width: 200px;
            box-shadow: var(--shadow-2xl);
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
            z-index: 9999;
        }

        .user-menu.active .user-dropdown {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            gap: var(--space-sm);
            padding: var(--space-sm) var(--space-md);
            color: var(--text-secondary);
            font-size: clamp(0.75rem, 2.5vw, 0.875rem);
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .dropdown-item:hover,
        .dropdown-item:focus-visible {
            background: var(--primary-green);
            color: var(--white);
        }

        .content {
            padding: var(--space-lg);
            flex: 1;
        }

        .alert {
            padding: var(--space-sm) var(--space-md);
            border-radius: var(--radius-md);
            margin-bottom: var(--space-md);
            color: var(--text-primary);
            font-size: clamp(0.75rem, 2.5vw, 0.875rem);
        }

        .alert-success {
            background: rgba(22, 163, 74, 0.1);
            color: var(--success);
        }

        .alert-error {
            background: rgba(220, 38, 38, 0.1);
            color: var(--error);
        }

        .btn-close-sidebar {
            display: none;
            position: absolute;
            top: var(--space-md);
            right: var(--space-md);
            z-index: 2002;
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            color: var(--text-primary);
            padding: var(--space-xs);
            border-radius: var(--radius-md);
            cursor: pointer;
        }

        @media (max-width: 992px) {
            .btn-close-sidebar {
                display: block;
            }
        }

        /* Accessibility Enhancements */
        .nav-link:focus-visible,
        .action-btn:focus-visible,
        .theme-btn:focus-visible,
        .menu-toggle:focus-visible,
        .dropdown-item:focus-visible {
            outline: 2px solid var(--gold);
            outline-offset: 2px;
        }
    </style>

    @stack('styles')
</head>

<body
    class="h-full font-sans antialiased bg-[var(--bg-primary)] text-[var(--text-primary)] transition-colors duration-300">
    <div class="dashboard-container">
        <div class="overlay" id="overlay"></div>
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <img src="{{ asset('images/school_logo.png') }}" alt="Aunty Anne's International School Logo"
                        loading="lazy">
                </div>
            </div>
            <nav class="nav-section">
                <div class="nav-section-title">School Management</div>
                <ul class="nav-list list-unstyled">
                    <!-- Student Management -->
                    <li class="nav-item">
                        <a href="#studentManagement" class="nav-link" data-bs-toggle="collapse" data-bs-target="#studentManagement"
                            aria-expanded="false">
                            <i class='bx bxs-graduation nav-icon'></i> <span>Student Management</span>
                            <i class="bx bx-chevron-down ms-auto"></i>
                        </a>
                        <div class="collapse" id="studentManagement">
                            <ul class="nav-list nav-sublist list-unstyled">
                                <li><a href="{{ route('admin.add_student') }}"
                                        class="{{ request()->routeIs('admin.add_student') ? 'active' : '' }}"><i
                                            class="bx bx-user-plus nav-icon"></i><span>Add New Student</span></a></li>
                                <li><a href="{{ route('admin.students', ['action' => 'view_students']) }}"
                                        class="{{ request()->routeIs('admin.students') && request('action') === 'view_students' ? 'active' : '' }}"><i
                                            class="bx bx-list-ul nav-icon"></i><span>View All Students</span></a></li>
                                <li><a href="{{ route('admin.students', ['action' => 'toggle_fees_status']) }}"
                                        class="{{ request()->routeIs('admin.students') && request('action') === 'toggle_fees_status' ? 'active' : '' }}"><i
                                            class="bx bx-money nav-icon"></i><span>Check Fees Status</span></a></li>
                                <li><a href="{{ route('admin.students', ['action' => 'delete_from_school']) }}"
                                        class="{{ request()->routeIs('admin.students') && request('action') === 'delete_from_school' ? 'active' : '' }}"><i
                                            class="bx bx-trash nav-icon"></i><span>Delete Student Records</span></a></li>
                                <li><a href="{{ route('admin.select_class', ['action' => 'promote']) }}"
                                        class="{{ request()->routeIs('admin.select_class') && request('action') === 'promote' ? 'active' : '' }}"><i
                                            class="bx bx-up-arrow-alt nav-icon"></i><span>Promote Students</span></a></li>
                                <li><a href="{{ route('admin.select_class', ['action' => 'demote']) }}"
                                        class="{{ request()->routeIs('admin.select_class') && request('action') === 'demote' ? 'active' : '' }}"><i
                                            class="bx bx-down-arrow-alt nav-icon"></i><span>Demote Students</span></a></li>
                                <li><a href="{{ route('admin.bulk_upload_students') }}"
                                        class="{{ request()->routeIs('admin.bulk_upload_students') ? 'active' : '' }}"><i
                                            class="bx bx-upload nav-icon"></i><span>Bulk Upload Students</span></a></li>
                                <li><a href="{{ route('admin.search_students', ['action' => 'view_students']) }}"
                                        class="{{ request()->routeIs('admin.search_students') ? 'active' : '' }}"><i
                                            class="bx bx-search nav-icon"></i><span>Search and Filter Students</span></a></li>
                            </ul>
                        </div>
                    </li>
                    <!-- Class Management -->
                    <li class="nav-item">
                        <a href="#classManagement" class="nav-link" data-bs-toggle="collapse" data-bs-target="#classManagement"
                            aria-expanded="false">
                            <i class='bx bxs-group nav-icon'></i><span>Class Management</span>
                            <i class="bx bx-chevron-down ms-auto"></i>
                        </a>
                        <div class="collapse" id="classManagement">
                            <ul class="nav-list nav-sublist list-unstyled">
                                {{-- }}
                                <li><a href="{{ route('admin.classes.index') }}"
                                        class="{{ request()->routeIs('admin.classes.*') ? 'active' : '' }}"><i
                                            class="bx bx-plus nav-icon"></i><span>Add Class</span></a></li> --}}
                                <li><a href="{{ route('admin.classes.index') }}"
                                        class="{{ request()->routeIs('admin.classes.*') ? 'active' : '' }}"><i
                                            class="bx bx-list-check nav-icon"></i><span>Manage Classes</span></a></li>
                                <li><a href="{{ route('admin.select_class', ['action' => 'view_students']) }}"
                                        class="{{ request()->routeIs('admin.select_class') && request('action') === 'view_students' ? 'active' : '' }}"><i
                                            class="bx bx-user nav-icon"></i><span>View Students by Classes</span></a>
                                </li>
                                <li><a href="{{ route('admin.select_class', ['action' => 'delete_from_class']) }}"
                                        class="{{ request()->routeIs('admin.select_class') && request('action') === 'delete_from_class' ? 'active' : '' }}"><i
                                            class="bx bx-minus-circle nav-icon"></i><span>Delete Student From
                                            Class</span></a></li>
                                <li><a href="{{ route('admin.classes.index') }}"
                                        class="{{ request()->routeIs('admin.classes.edit') ? 'active' : '' }}"><i
                                            class="bx bx-edit-alt nav-icon"></i><span>Edit Class/Section
                                            Details</span></a></li>
                                <li><a href="{{ route('admin.assign_teacher_to_class') }}"
                                        class="{{ request()->routeIs('admin.assign_teacher_to_class') ? 'active' : '' }}"><i
                                            class="bx bx-link nav-icon"></i><span>Assign Teachers to Classes</span></a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <!-- Subject Management -->
                    <li class="nav-item">
                        <a href="#subjectManagement" class="nav-link" data-bs-toggle="collapse" data-bs-target="#subjectManagement"
                            aria-expanded="false">
                            <i class="fas fa-book nav-icon"></i><span>Subject Management</span>
                            <i class="bx bx-chevron-down ms-auto"></i>
                        </a>
                        <div class="collapse" id="subjectManagement">
                            <ul class="nav-list nav-sublist list-unstyled">
                            <li>
                                <a href="{{ route('admin.subjects.manage') }}"
                                    class="{{ request()->routeIs('admin.subjects.manage') ? 'active' : '' }}">
                                    <i class="bx bx-book-open nav-icon"></i>
                                    <span>Manage Subjects</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.subjects.assign') }}"
                                    class="{{ request()->routeIs('admin.subjects.assign') ? 'active' : '' }}">
                                    <i class="bx bx-link-alt nav-icon"></i>
                                    <span>Assign Subjects to Classes</span>
                                </a>
                            </li>
                                <li><a href="{{ route('admin.assign_subject_to_teacher') }}"
                                        class="{{ request()->routeIs('admin.assign_subject_to_teacher') ? 'active' : '' }}"><i
                                            class="bx bx-user-pin nav-icon"></i><span>Assign Subjects to
                                            Teachers</span></a></li>
                            </ul>
                        </div>
                    </li>
                    <!-- Result Management -->
                    <li class="nav-item">
                        <a href="#resultManagement" class="nav-link" data-bs-toggle="collapse" data-bs-target="#resultManagement"
                            aria-expanded="false">
                            <i class="fas fa-chart-line nav-icon"></i><span>Result Management</span>
                            <i class="bx bx-chevron-down ms-auto"></i>
                        </a>
                        <div class="collapse" id="resultManagement">
                            <ul class="nav-list nav-sublist list-unstyled">
                                <li><a href="#" class=""><i class="bx bx-upload nav-icon"></i><span>Add or Upload
                                            Results</span></a></li>
                                <li><a href="{{ route('admin.select_class', ['action' => 'manage_result']) }}"
                                        class="{{ request()->routeIs('admin.select_class') && request('action') === 'manage_result' ? 'active' : '' }}"><i
                                            class="bx bx-file nav-icon"></i><span>Manage Results by Student</span></a>
                                </li>
                                <li><a href="{{ route('admin.select_class', ['action' => 'generate_broadsheet']) }}"
                                        class="{{ request()->routeIs('admin.select_class') && request('action') === 'generate_broadsheet' ? 'active' : '' }}"><i
                                            class="bx bx-table nav-icon"></i><span>Edit Results by Broadsheet</span></a>
                                </li>
                                <li><a href="{{ route('admin.select_class', ['action' => 'download_broadsheet']) }}"
                                        class="{{ request()->routeIs('admin.select_class') && request('action') === 'download_broadsheet' ? 'active' : '' }}"><i
                                            class="bx bx-download nav-icon"></i><span>Download Broadsheet</span></a>
                                </li>
                                <li><a href="{{ route('admin.print_student_message') }}"
                                        class="{{ request()->routeIs('admin.print_student_message') ? 'active' : '' }}"><i
                                            class="bx bx-message nav-icon"></i><span>Notify Students</span></a></li>
                            </ul>
                        </div>
                    </li>
                    <!-- Teacher Management -->
                    <li class="nav-item">
                        <a href="#teacherManagement" class="nav-link" data-bs-toggle="collapse" data-bs-target="#teacherManagement"
                            aria-expanded="false">
                            <i class="fas fa-chalkboard-teacher nav-icon"></i><span>Staff Management</span>
                            <i class="bx bx-chevron-down ms-auto"></i>
                        </a>
                        <div class="collapse" id="teacherManagement">
                            <ul class="nav-list nav-sublist list-unstyled">
                                <li><a href="{{ route('admin.manage_teachers') }}"
                                        class="{{ request()->routeIs('admin.manage_teachers') ? 'active' : '' }}"><i
                                            class="bx bx-user-plus nav-icon"></i><span>Add New Staff</span></a></li>
                                <li><a href="#" class=""><i class="bx bx-list-ul nav-icon"></i><span>View All
                                            Teachers</span></a></li>
                                <li><a href="#" class=""><i class="bx bx-edit nav-icon"></i><span>Edit Teacher
                                            Details</span></a></li>
                                <li><a href="{{ route('admin.assign_teacher_to_class') }}"
                                        class="{{ request()->routeIs('admin.assign_teacher_to_class') ? 'active' : '' }}"><i
                                            class="bx bx-link nav-icon"></i><span>Assign Teachers to Classes</span></a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <!-- Manage Academic Session -->
                    <li class="nav-item">
                        <a href="#sessionManagement" class="nav-link" data-bs-toggle="collapse" data-bs-target="#sessionManagement"
                            aria-expanded="false">
                            <i class="fas fa-calendar-alt nav-icon"></i><span>Session Management</span>
                            <i class="bx bx-chevron-down ms-auto"></i>
                        </a>
                        <div class="collapse" id="sessionManagement">
                            <ul class="nav-list nav-sublist list-unstyled">
                                <li><a href="{{ route('admin.manage_academic_sessions') }}"
                                        class="{{ request()->routeIs('admin.manage_academic_sessions') ? 'active' : '' }}"><i
                                            class="bx bx-calendar nav-icon"></i><span>Manage Academic Sessions</span></a></li>
                                <li><a href="{{ route('admin.set_current_session') }}"
                                        class="{{ request()->routeIs('admin.set_current_session') ? 'active' : '' }}"><i
                                            class="bx bx-calendar-check nav-icon"></i><span>Set Current Session & Term</span></a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <!-- Admin Management -->
                    <li class="nav-item">
                        <a href="#adminManagement" class="nav-link" data-bs-toggle="collapse" data-bs-target="#adminManagement"
                            aria-expanded="false">
                            <i class="fas fa-user-shield nav-icon"></i><span>Admin Management</span>
                            <i class="bx bx-chevron-down ms-auto"></i>
                        </a>
                        <div class="collapse" id="adminManagement">
                            <ul class="nav-list nav-sublist list-unstyled">
                                @if (Auth::check() && Auth::user()->hasRole('admin'))
                                    <li><a href="{{ route('admin.create_admin') }}"
                                            class="{{ request()->routeIs('admin.create_admin') ? 'active' : '' }}"><i
                                                class="bx bx-user-plus nav-icon"></i><span>Create Admin User</span></a></li>
                                    <li><a href="{{ route('admin.view_admins') }}"
                                            class="{{ request()->routeIs('admin.view_admins') ? 'active' : '' }}"><i
                                                class="bx bx-list-ul nav-icon"></i><span>View Admins</span></a></li>
                                    <li><a href="{{ route('admin.view_admins') }}"
                                            class="{{ request()->routeIs('admin.view_admins') ? 'active' : '' }}"><i
                                                class="bx bx-edit nav-icon"></i><span>Edit Admin Details</span></a></li>
                                    <li><a href="{{ route('admin.view_admins') }}"
                                            class="{{ request()->routeIs('admin.view_admins') ? 'active' : '' }}"><i
                                                class="bx bx-shield-alt nav-icon"></i><span>Edit Privileges</span></a></li>
                                    <li><a href="{{ route('admin.view_admins') }}"
                                            class="{{ request()->routeIs('admin.view_admins') ? 'active' : '' }}"><i
                                                class="bx bx-trash nav-icon"></i><span>Delete Admin</span></a></li>
                                @endif
                            </ul>
                        </div>
                    </li>
                    <!-- Settings -->
                    <li class="nav-item">
                        <a href="#" class="nav-link"><i class="bx bx-cog nav-icon"></i><span>Settings</span></a>
                    </li>
                    <!-- Log Out -->
                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="nav-link"><i class="bx bx-log-out nav-icon"></i><span>Log
                                    Out</span></button>
                        </form>
                    </li>
                </ul>
            </nav>
            <button class="btn-close-sidebar" id="closeSidebar" aria-label="Close sidebar">
                <i class="bx bx-x"></i>
            </button>
        </aside>
        <main class="main-content">
            <header class="top-nav">
                <div class="nav-left">
                    <button class="menu-toggle" id="menuToggle" aria-label="Toggle sidebar">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="search-container">
                        <form action="#" method="GET">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" name="query" class="search-input"
                                placeholder="Search students, staff, courses..." value="{{ request('query') }}"
                                aria-label="Search">
                            <button type="submit" style="display: none;">Search</button>
                        </form>
                        <div class="search-results" id="searchResults" role="listbox"></div>
                    </div>
                </div>
                <div class="nav-right">
                    <button class="theme-btn" id="themeToggle" aria-label="Toggle theme">
                        <i class="fas fa-sun theme-icon-light"></i>
                        <i class="fas fa-moon theme-icon-dark"></i>
                    </button>
                    <div class="nav-actions">
                        @if (auth()->check())
                            <button class="action-btn notification-toggle" id="notificationToggle" title="Notifications"
                                aria-label="View notifications">
                                <i class="fas fa-bell"></i>
                                <span class="badge" id="notificationCount">{{ $unreadNotificationsCount ?? 0 }}</span>
                            </button>
                            <div class="notification-dropdown" id="notificationDropdown">
                                <div id="notificationList">
                                    @foreach ($unreadNotifications ?? [] as $notification)
                                        <div class="notification-item" data-id="{{ $notification->id }}">
                                            <div class="notification-icon"><i class="fas fa-bell"></i></div>
                                            <div class="notification-content">
                                                <h5>{{ class_basename($notification->type) }}</h5>
                                                <p>{{ $notification->data['message'] ?? 'No message available' }}</p>
                                            </div>
                                            <span
                                                class="notification-time">{{ $notification->created_at->diffForHumans() }}</span>
                                        </div>
                                    @endforeach
                                    @if (empty($unreadNotifications) || $unreadNotifications->isEmpty())
                                        <div class="notification-item text-center p-4">
                                            <p class="text-[var(--text-secondary)]">No new notifications</p>
                                        </div>
                                    @endif
                                </div>
                                <a href="#" class="action-btn block text-center w-full mt-2">
                                    <i class="fas fa-list-ul"></i> View All Notifications
                                </a>
                            </div>
                        @endif
                    </div>
                    <div class="user-menu" id="userMenu">
                        <div class="user-trigger" aria-label="User menu" aria-expanded="false" aria-haspopup="true">
                            <div class="user-avatar">
                                @if(auth()->user() && auth()->user()->avatar && Storage::disk('public')->exists('avatars/' . auth()->user()->avatar))
                                    <img src="{{ Storage::url('avatars/' . auth()->user()->avatar) . '?t=' . time() }}"
                                        alt="{{ auth()->user()->name }}" class="h-full w-full rounded-full object-cover"
                                        loading="lazy">
                                @else
                                    <span>{{ auth()->check() ? strtoupper(substr(auth()->user()->name, 0, 1)) : 'A' }}</span>
                                @endif
                            </div>
                            <div class="user-info">
                                <h4>{{ auth()->check() ? auth()->user()->name : 'Admin' }}</h4>
                            </div>
                            <i class="fas fa-chevron-down dropdown-icon"></i>
                        </div>
                        <div class="user-dropdown">
                            <a href="#" class="dropdown-item"><i class="fas fa-user"></i> Profile</a>
                            <a href="{{ route('mfa.setup') }}" class="dropdown-item"><i class="fas fa-shield-alt"></i>
                                MFA Setup</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item"><i class="fas fa-sign-out-alt"></i> Sign
                                    Out</button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>
            <section class="content">
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-error">
                        {{ session('error') }}
                    </div>
                @endif
                @yield('content')
            </section>
        </main>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Theme Toggle
            const themeToggle = document.getElementById('themeToggle');
            const htmlElement = document.documentElement;

            function updateTheme() {
                const isDark = localStorage.getItem('theme') === 'dark' ||
                    (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches);
                htmlElement.classList.toggle('dark', isDark);
                htmlElement.classList.toggle('light', !isDark);
                themeToggle.classList.toggle('is-dark', isDark);
                localStorage.setItem('theme', isDark ? 'dark' : 'light');
            }

            updateTheme();
            themeToggle.addEventListener('click', () => {
                const isDark = htmlElement.classList.contains('dark');
                localStorage.setItem('theme', isDark ? 'light' : 'dark');
                updateTheme();
            });

            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
                if (!localStorage.getItem('theme')) {
                    updateTheme();
                }
            });

            // Sidebar Toggle
            const menuToggle = document.getElementById('menuToggle');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');
            const closeSidebar = document.getElementById('closeSidebar');

            function toggleSidebar() {
                sidebar.classList.toggle('active');
                overlay.classList.toggle('active');
                gsap.to(sidebar, { x: sidebar.classList.contains('active') ? 0 : '-100%', duration: 0.3 });
                gsap.to(overlay, { opacity: sidebar.classList.contains('active') ? 1 : 0, duration: 0.3 });
            }

            menuToggle.addEventListener('click', (e) => {
                e.stopPropagation();
                toggleSidebar();
            });

            closeSidebar.addEventListener('click', (e) => {
                e.stopPropagation();
                if (sidebar.classList.contains('active')) {
                    toggleSidebar();
                }
            });

            overlay.addEventListener('click', (e) => {
                e.stopPropagation();
                if (sidebar.classList.contains('active')) {
                    toggleSidebar();
                }
            });

            sidebar.addEventListener('click', (e) => {
                e.stopPropagation();
            });

            document.querySelectorAll('.nav-list li a, .nav-list li button').forEach(item => {
                item.addEventListener('click', () => {
                    if (window.innerWidth <= 992 && sidebar.classList.contains('active')) {
                        toggleSidebar();
                    }
                });
            });

            // Collapse Toggle for Sublists
            document.querySelectorAll('.nav-link[data-bs-toggle="collapse"]').forEach(toggle => {
                toggle.addEventListener('click', (e) => {
                    const targetId = toggle.getAttribute('data-bs-target');
                    const target = document.querySelector(targetId);
                    const sublist = target.querySelector('.nav-sublist');
                    if (target.classList.contains('show')) {
                        sublist.style.display = 'none';
                    } else {
                        sublist.style.display = 'block';
                        gsap.from(sublist.children, { opacity: 0, y: 10, stagger: 0.1, duration: 0.3 });
                    }
                });
            });

            // User Menu
            const userMenu = document.getElementById('userMenu');
            const userTrigger = userMenu.querySelector('.user-trigger');

            userTrigger.addEventListener('click', (e) => {
                e.stopPropagation();
                userMenu.classList.toggle('active');
                gsap.to('.user-dropdown', {
                    opacity: userMenu.classList.contains('active') ? 1 : 0,
                    y: userMenu.classList.contains('active') ? 0 : -10,
                    duration: 0.3
                });
            });

            document.addEventListener('click', (e) => {
                if (!userMenu.contains(e.target) && userMenu.classList.contains('active')) {
                    userMenu.classList.remove('active');
                    gsap.to('.user-dropdown', { opacity: 0, y: -10, duration: 0.3 });
                }
            });

            // Notifications
            const notificationToggle = document.getElementById('notificationToggle');
            const notificationDropdown = document.getElementById('notificationDropdown');

            if (notificationToggle && notificationDropdown) {
                notificationToggle.addEventListener('click', (e) => {
                    e.stopPropagation();
                    notificationToggle.classList.toggle('active');
                    gsap.to(notificationDropdown, {
                        opacity: notificationToggle.classList.contains('active') ? 1 : 0,
                        y: notificationToggle.classList.contains('active') ? 0 : -10,
                        duration: 0.3
                    });
                });

                document.addEventListener('click', (e) => {
                    if (!notificationToggle.contains(e.target) && !notificationDropdown.contains(e.target)) {
                        notificationToggle.classList.remove('active');
                        gsap.to(notificationDropdown, { opacity: 0, y: -10, duration: 0.3 });
                    }
                });
            }

            // Search Functionality
            const searchInput = document.querySelector('.search-input');
            const searchResults = document.getElementById('searchResults');

            function debounce(func, wait) {
                let timeout;
                return function (...args) {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => func.apply(this, args), wait);
                };
            }

            function performSearch(query) {
                if (query.trim().length < 3) {
                    searchResults.innerHTML = '';
                    searchResults.classList.remove('active');
                    return;
                }

                fetch(`/admin/search?query=${encodeURIComponent(query)}`, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        searchResults.innerHTML = '';
                        if (data.length === 0) {
                            searchResults.innerHTML = '<div class="search-result-item text-center">No results found</div>';
                        } else {
                            data.forEach(item => {
                                const div = document.createElement('div');
                                div.className = 'search-result-item';
                                div.innerHTML = `
                                    <div>${item.name || item.title}</div>
                                    <span>${item.type}</span>
                                `;
                                div.addEventListener('click', () => {
                                    window.location.href = item.url;
                                });
                                searchResults.appendChild(div);
                            });
                            gsap.from(searchResults.children, { opacity: 0, y: 20, stagger: 0.1 });
                        }
                        searchResults.classList.add('active');
                    })
                    .catch(error => {
                        console.error('Search error:', error);
                        searchResults.innerHTML = '<div class="search-result-item text-center">Error fetching results</div>';
                        searchResults.classList.add('active');
                    });
            }

            const debouncedSearch = debounce(performSearch, 300);
            searchInput.addEventListener('input', (e) => debouncedSearch(e.target.value));

            document.addEventListener('click', (e) => {
                if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                    searchResults.classList.remove('active');
                    searchResults.innerHTML = '';
                }
            });

            // Close sidebar on resize
            window.addEventListener('resize', () => {
                if (window.innerWidth > 992 && sidebar.classList.contains('active')) {
                    toggleSidebar();
                }
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    @stack('scripts')
</body>

</html>