<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="auth-id" content="{{ auth()->id() ?? '' }}">
    <title id="pageTitle">@yield('title', 'Student Portal') | {{ config('app.name') }}</title>
    <meta name="description" content="@yield('description', 'Student portal for viewing results, fee status, and profile.')">
    <meta name="keywords" content="student, school portal, results, fees, profile">
    <meta name="author" content="Aunty Anne's International School">

    <link rel="icon" type="image/png" href="{{ asset('images/favicons/favicon-96x96.png') }}" sizes="96x96">
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicons/favicon.svg') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/favicons/apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('images/favicons/site.webmanifest') }}">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&family=Space+Grotesk:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <!-- Vite -->
    @vite(['resources/js/app.js'])

    <style>
        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --primary-light: #8b5cf6;
            --secondary: #0ea5e9;
            --accent: #f59e0b;
            --success: #10b981;
            --warning: #f59e0b;
            --error: #ef4444;
            --white: #ffffff;
            --gray-50: #f8fafc;
            --gray-100: #f1f5f9;
            --gray-200: #e2e8f0;
            --gray-300: #cbd5e1;
            --gray-400: #94a3b8;
            --gray-500: #64748b;
            --gray-600: #475569;
            --gray-700: #334155;
            --gray-800: #1e293b;
            --gray-900: #0f172a;
            --dark-bg: #0a0a0f;
            --dark-surface: #1a1a2e;
            --dark-card: #16213e;
            --dark-border: #0f3460;
            --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --gradient-secondary: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --gradient-accent: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            --gradient-success: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --glass-bg: rgba(255, 255, 255, 0.05);
            --glass-border: rgba(255, 255, 255, 0.1);
            --glass-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            --font-primary: "Inter", system-ui, -apple-system, sans-serif;
            --font-display: "Space Grotesk", sans-serif;
            --font-mono: "JetBrains Mono", monospace;
            --space-xs: 0.25rem;
            --space-sm: 0.5rem;
            --space-md: 1rem;
            --space-lg: 1.5rem;
            --space-xl: 2rem;
            --space-2xl: 3rem;
            --radius-sm: 0.375rem;
            --radius-md: 0.5rem;
            --radius-lg: 0.75rem;
            --radius-xl: 1rem;
            --radius-2xl: 1.5rem;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            --shadow-2xl: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        body.light {
            --dark-bg: #ffffff;
            --dark-surface: #f8fafc;
            --dark-card: #ffffff;
            --dark-border: #e2e8f0;
            --white: #0f172a;
            --gray-300: #64748b;
            --gray-400: #475569;
            --gray-500: #334155;
            --glass-bg: rgba(0, 0, 0, 0.02);
            --glass-border: rgba(0, 0, 0, 0.1);
        }

        /* Global reset */
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html {
            scroll-behavior: smooth;
            font-size: clamp(14px, 3.5vw, 16px);
            -webkit-tap-highlight-color: transparent;
        }

        body {
            font-family: var(--font-primary);
            background: var(--dark-bg);
            color: var(--white);
            line-height: 1.6;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: var(--dark-surface);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--primary);
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--primary-dark);
        }

        .dashboard-container {
            display: flex;
            min-height: 100vh;
            background: var(--dark-bg);
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
            width: clamp(200px, 70vw, 280px);
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

        @media (max-width: 1024px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.active {
                transform: translateX(0);
            }
        }

        .sidebar-header {
            display: flex;
            align-items: center;
            gap: var(--space-md);
            margin-bottom: var(--space-xl);
            padding-bottom: var(--space-md);
            border-bottom: 1px solid var(--glass-border);
        }

        .logo {
            width: clamp(36px, 10vw, 48px);
            height: clamp(36px, 10vw, 48px);
            background: var(--gradient-primary);
            border-radius: var(--radius-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: clamp(1rem, 4vw, 1.5rem);
            font-weight: 700;
            color: var(--white);
        }

        .logo img {
            width: 100%;
            height: 100%;
            border-radius: var(--radius-lg);
            object-fit: cover;
        }

        .brand-text {
            color: var(--white);
        }

        .brand-text h1 {
            font-family: var(--font-display);
            font-size: clamp(1rem, 4vw, 1.5rem);
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .brand-text p {
            font-size: clamp(0.625rem, 2.5vw, 0.875rem);
            color: var(--gray-400);
        }

        body.light .brand-text h1,
        body.light .brand-text p {
            color: var(--gray-500);
        }

        .nav-section {
            margin-bottom: var(--space-xl);
        }

        .nav-section-title {
            font-size: clamp(0.625rem, 2.5vw, 0.75rem);
            font-weight: 600;
            color: var(--gray-500);
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: var(--space-md);
            padding-left: var(--space-sm);
        }

        .nav-section-title::before {
            content: '';
            display: inline-block;
            width: 4px;
            height: 16px;
            background: var(--primary);
            border-radius: var(--radius-sm);
            margin-right: var(--space-xs);
        }

        .nav-section-title::after {
            content: '';
            display: block;
            width: 100%;
            height: 1px;
            background: var(--glass-border);
            margin-top: var(--space-xs);
        }

        .nav-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .nav-list li a,
        .nav-list li button {
            color: var(--gray-300);
            text-decoration: none;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: var(--space-sm);
            padding: var(--space-sm) var(--space-md);
            border-radius: var(--radius-lg);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            background: none;
            border: none;
            width: 100%;
            text-align: left;
            cursor: pointer;
            font-size: clamp(0.875rem, 3vw, 1rem);
        }

        .nav-list li a:hover,
        .nav-list li button:hover {
            background: var(--glass-bg);
            color: var(--white);
            transform: translateX(4px);
        }

        .nav-list li a.active,
        .nav-list li button.active {
            background: var(--gradient-primary);
            color: var(--white);
            box-shadow: var(--shadow-lg);
        }

        .nav-icon {
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .nav-badge {
            background: var(--error);
            color: var(--white);
            font-size: clamp(0.5rem, 2vw, 0.625rem);
            font-weight: 600;
            padding: 0.125rem 0.375rem;
            border-radius: 10px;
            margin-left: auto;
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
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 999;
            flex-wrap: nowrap;
            gap: var(--space-sm);
        }

        .nav-left {
            display: flex;
            align-items: center;
            gap: var(--space-sm);
            flex: 1;
            min-width: 0;
        }

        .menu-toggle {
            display: none;
            background: none;
            border: none;
            color: var(--white);
            font-size: clamp(1.25rem, 4vw, 1.5rem);
            cursor: pointer;
            padding: var(--space-sm);
            border-radius: var(--radius-md);
            transition: all 0.2s ease;
            position: relative;
            z-index: 1001;
        }

        .menu-toggle:hover {
            background: var(--glass-bg);
        }

        .menu-toggle.active i::before {
            content: '\F2EA'; /* Bootstrap Icons 'x' */
        }

        @media (max-width: 1024px) {
            .menu-toggle {
                display: block;
            }
        }

        .search-container {
            position: relative;
            max-width: clamp(150px, 40vw, 300px);
            width: 100%;
        }

        .search-icon {
            position: absolute;
            left: var(--space-sm);
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-400);
            font-size: clamp(0.75rem, 2.5vw, 0.875rem);
        }

        .search-input {
            width: 100%;
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-xl);
            padding: var(--space-sm) var(--space-md) var(--space-sm) 2.5rem;
            color: var(--white);
            font-size: clamp(0.75rem, 2.5vw, 0.875rem);
            transition: all 0.2s ease;
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary);
            background: rgba(255, 255, 255, 0.1);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        body.light .search-input {
            color: var(--gray-500);
            background: var(--glass-bg);
            border-color: var(--glass-border);
        }

        .nav-right {
            display: flex;
            align-items: center;
            gap: var(--space-sm);
            flex-shrink: 0;
        }

        .action-btn {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            color: var(--gray-300);
            font-size: clamp(0.75rem, 2.5vw, 0.875rem);
            padding: var(--space-xs) var(--space-sm);
            border-radius: var(--radius-md);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: var(--space-xs);
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .action-btn:hover {
            background: var(--primary);
            border-color: var(--primary);
            color: var(--white);
        }

        .action-btn i {
            font-size: clamp(0.75rem, 2.5vw, 0.875rem);
        }

        .theme-toggle {
            position: fixed;
            bottom: var(--space-md);
            right: var(--space-md);
            z-index: 1001;
        }

        .theme-btn {
            width: clamp(36px, 10vw, 48px);
            height: clamp(36px, 10vw, 48px);
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            color: var(--gray-400);
        }

        .theme-btn:hover {
            background: var(--primary);
            border-color: var(--primary);
            color: var(--white);
            transform: rotate(20deg) scale(1.05);
        }

        .user-menu {
            position: relative;
        }

        .user-trigger {
            display: flex;
            align-items: center;
            gap: var(--space-sm);
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-xl);
            padding: var(--space-sm);
            cursor: pointer;
            transition: all 0.2s ease;
            min-width: clamp(120px, 30vw, 180px);
        }

        .user-trigger:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: var(--primary);
        }

        .user-avatar {
            position: relative;
            width: clamp(32px, 8vw, 40px);
            height: clamp(32px, 8vw, 40px);
            border-radius: 50%;
            overflow: hidden;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            background: var(--gradient-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid var(--glass-border);
        }

        .user-avatar:hover {
            transform: scale(1.05);
            box-shadow: var(--shadow-sm);
        }

        .user-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
            transition: opacity 0.3s ease;
        }

        .user-avatar img[loading="lazy"] {
            opacity: 0;
        }

        .user-avatar img[loading="lazy"][loaded] {
            opacity: 1;
        }

        .user-avatar span {
            font-size: clamp(0.875rem, 3vw, 1rem);
            font-weight: 600;
            color: var(--white);
        }

        .user-info {
            flex: 1;
            overflow: hidden;
        }

        .user-info h4 {
            font-size: clamp(0.75rem, 2.5vw, 0.875rem);
            font-weight: 600;
            margin-bottom: 0.125rem;
            color: var(--white);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .user-info p {
            font-size: clamp(0.625rem, 2vw, 0.75rem);
            color: var(--gray-400);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        body.light .user-info h4 {
            color: var(--gray-500);
        }

        body.light .user-info p {
            color: var(--gray-500);
        }

        .dropdown-icon {
            color: var(--gray-400);
            font-size: clamp(0.75rem, 2.5vw, 0.875rem);
            transition: transform 0.2s ease;
        }

        .user-menu.active .dropdown-icon {
            transform: rotate(180deg);
        }

        .user-dropdown {
            position: absolute;
            top: calc(100% + var(--space-sm));
            right: 0;
            background: var(--dark-surface);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-lg);
            padding: var(--space-sm);
            min-width: clamp(160px, 50vw, 200px);
            box-shadow: var(--shadow-2xl);
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1000;
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
            color: var(--gray-300);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s ease;
            margin-bottom: var(--space-xs);
            background: none;
            border: none;
            width: 100%;
            text-align: left;
            cursor: pointer;
            font-size: clamp(0.75rem, 2.5vw, 0.875rem);
        }

        .dropdown-item:hover {
            background: var(--glass-bg);
            color: var(--white);
        }

        body.light .user-dropdown .dropdown-item {
            color: var(--gray-500);
        }

        body.light .user-dropdown .dropdown-item:hover {
            background: rgba(0, 0, 0, 0.05);
            color: var(--gray-500);
        }

        .dropdown-divider {
            height: 1px;
            background: var(--glass-border);
            margin: var(--space-sm) 0;
        }

        .content {
            padding: var(--space-xl);
            flex: 1;
            background: var(--dark-bg);
        }

        .alert {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-lg);
            padding: var(--space-md);
            margin-bottom: var(--space-lg);
            color: var(--white);
            font-size: clamp(0.75rem, 2.5vw, 0.875rem);
            position: relative;
            backdrop-filter: blur(20px);
        }

        .alert-success {
            border-color: var(--success);
            background: rgba(34, 197, 94, 0.1);
        }

        .alert-error {
            border-color: var(--error);
            background: rgba(239, 68, 68, 0.1);
        }

        .btn-close-sidebar {
            display: none;
            position: absolute;
            top: var(--space-sm);
            right: var(--space-sm);
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            color: var(--gray-300);
            padding: var(--space-xs);
            border-radius: var(--radius-md);
            cursor: pointer;
        }

        .btn-close-sidebar:hover {
            background: var(--primary);
            border-color: var(--primary);
            color: var(--white);
        }

        @media (max-width: 1024px) {
            .btn-close-sidebar {
                display: block;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                width: clamp(180px, 60vw, 240px);
            }

            .top-nav {
                flex-wrap: wrap;
                padding: var(--space-md);
            }

            .nav-left {
                width: 100%;
                justify-content: space-between;
            }

            .nav-right {
                width: 100%;
                justify-content: flex-end;
            }

            .search-container {
                max-width: 100%;
            }

            .user-trigger {
                min-width: clamp(100px, 25vw, 150px);
            }

            .user-avatar {
                width: clamp(28px, 7vw, 36px);
                height: clamp(28px, 7vw, 36px);
            }

            .theme-toggle {
                bottom: var(--space-md);
                right: var(--space-md);
            }
        }

        @media (max-width: 576px) {
            .sidebar {
                width: clamp(160px, 50vw, 200px);
                padding: var(--space-md);
            }

            .search-input {
                padding: var(--space-xs) var(--space-sm) var(--space-xs) 2rem;
                font-size: clamp(0.625rem, 2vw, 0.75rem);
            }

            .search-icon {
                font-size: clamp(0.625rem, 2vw, 0.75rem);
            }

            .user-trigger {
                padding: var(--space-xs);
            }

            .user-info h4 {
                font-size: clamp(0.625rem, 2vw, 0.75rem);
            }

            .user-info p {
                display: none;
            }

            .theme-toggle {
                bottom: var(--space-sm);
                right: var(--space-sm);
            }
        }

        @media (max-width: 360px) {
            .sidebar {
                width: clamp(140px, 45vw, 180px);
            }

            .user-trigger {
                min-width: clamp(80px, 20vw, 120px);
            }

            .user-avatar {
                width: clamp(24px, 6vw, 32px);
                height: clamp(24px, 6vw, 32px);
            }
        }

        body.light .sidebar {
            background: var(--dark-surface);
        }

        body.light .nav-list li a,
        body.light .nav-list li button {
            color: var(--gray-500);
        }

        body.light .nav-list li a.active,
        body.light .nav-list li button.active {
            color: var(--white);
        }

        body.light .top-nav {
            background: var(--dark-surface);
        }

        body.light .search-input,
        body.light .action-btn,
        body.light .user-trigger {
            color: var(--gray-500);
            background: var(--glass-bg);
            border-color: var(--glass-border);
        }

        body.light .user-trigger:hover {
            background: rgba(0, 0, 0, 0.05);
            border-color: var(--primary);
            color: var(--gray-500);
        }

        body.light .user-avatar {
            background: var(--gradient-primary);
            color: var(--white);
        }

        body.light .user-dropdown {
            background: var(--dark-card);
            border-color: var(--dark-border);
        }

        body.light .content {
            background: var(--dark-bg);
            color: var(--white);
        }
    </style>
    @stack('styles')
</head>

<body class="h-full">
    <div class="dashboard-container">
        <div class="overlay" id="overlay"></div>
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <img src="{{ asset('images/school_logo.png') }}" alt="Aunty Anne's International School Logo" loading="lazy">
                </div>
                <div class="brand-text">
                    <h1>AAIS</h1>
                    <p>Student Portal</p>
                </div>
            </div>
            <nav class="nav-section">
                <div class="nav-section-title">Main</div>
                <ul class="nav-list">
                    <li><a href="{{ route('student.dashboard') }}" class="{{ request()->routeIs('student.dashboard') ? 'active' : '' }}"><i class="bi bi-grid nav-icon"></i> Dashboard</a></li>
                    <li><a href="{{ route('student.results') }}" class="{{ request()->routeIs('student.results') ? 'active' : '' }}"><i class="bi bi-file-text nav-icon"></i> View Results <span class="nav-badge">New</span></a></li>
                    <li><a href="{{ route('student.fee_status') }}" class="{{ request()->routeIs('student.fee_status') ? 'active' : '' }}"><i class="bi bi-wallet2 nav-icon"></i> Fee Status</a></li>
                </ul>
            </nav>
            <nav class="nav-section">
                <div class="nav-section-title">Account</div>
                <ul class="nav-list">
                    <li><a href="{{ route('student.profile') }}" class="{{ request()->routeIs('student.profile') ? 'active' : '' }}"><i class="bi bi-person nav-icon"></i> Profile</a></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"><i class="bi bi-box-arrow-right nav-icon"></i> Log Out</button>
                        </form>
                    </li>
                </ul>
            </nav>
            <button class="btn-close-sidebar" id="closeSidebar" aria-label="Close sidebar">
                <i class="bi bi-x-lg"></i>
            </button>
        </aside>
        <main class="main-content">
            <header class="top-nav">
                <div class="nav-left">
                    <button class="menu-toggle" id="menuToggle" aria-label="Toggle sidebar">
                        <i class="bi bi-list"></i>
                    </button>
                    <div class="search-container">
                        <form action="#" method="GET">
                            <i class="bi bi-search search-icon"></i>
                            <input type="text" name="query" class="search-input" placeholder="Search..." value="{{ request('query') }}">
                            <button type="submit" style="display: none;">Search</button>
                        </form>
                    </div>
                </div>
                <div class="nav-right">
                    <div class="user-menu" id="userMenu">
                        <div class="user-trigger">
                            <div class="user-avatar">
                                @if(auth()->user() && auth()->user()->student && auth()->user()->student->profile_pic && Storage::disk('public')->exists('profiles/' . auth()->user()->student->profile_pic))
                                    <img src="{{ Storage::url('profiles/' . auth()->user()->student->profile_pic) . '?t=' . time() }}"
                                        alt="Profile picture of {{ auth()->user()->student->full_name }}"
                                        class="h-full w-full rounded-full object-cover"
                                        loading="lazy"
                                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                @endif
                                <span class="flex items-center justify-center text-white font-semibold"
                                    style="display: {{ auth()->user() && auth()->user()->student && auth()->user()->student->profile_pic && Storage::disk('public')->exists('profiles/' . auth()->user()->student->profile_pic) ? 'none' : 'flex' }};">
                                    {{ auth()->check() && auth()->user()->student ? strtoupper(substr(auth()->user()->student->first_name, 0, 1)) : 'S' }}
                                </span>
                            </div>
                            <div class="user-info">
                                <h4>{{ auth()->check() && auth()->user()->student ? auth()->user()->student->full_name : 'Student' }}</h4>
                            </div>
                            <i class="bi bi-chevron-down dropdown-icon"></i>
                        </div>
                        <div class="user-dropdown">
                            <a href="{{ route('student.profile') }}" class="dropdown-item {{ request()->routeIs('student.profile') ? 'active' : '' }}">
                                <i class="bi bi-person"></i> Profile
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="bi bi-box-arrow-right"></i> Sign Out
                                </button>
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
        <div class="theme-toggle">
            <button class="theme-btn" id="themeToggle" title="Toggle Theme">
                <i class="bi bi-moon-stars"></i>
            </button>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const menuToggle = document.getElementById('menuToggle');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');
            const closeSidebar = document.getElementById('closeSidebar');
            const userMenu = document.getElementById('userMenu');
            const themeToggle = document.getElementById('themeToggle');

            function toggleSidebar() {
                sidebar.classList.toggle('active');
                overlay.classList.toggle('active');
            }

            menuToggle.addEventListener('click', toggleSidebar);
            closeSidebar.addEventListener('click', toggleSidebar);
            overlay.addEventListener('click', () => {
                if (sidebar.classList.contains('active')) {
                    toggleSidebar();
                }
            });

            document.querySelectorAll('.nav-list li a, .nav-list li button').forEach(item => {
                item.addEventListener('click', () => {
                    if (window.innerWidth <= 1024 && sidebar.classList.contains('active')) {
                        toggleSidebar();
                    }
                });
            });

            userMenu.addEventListener('click', (e) => {
                e.stopPropagation();
                userMenu.classList.toggle('active');
            });

            document.addEventListener('click', (e) => {
                if (!userMenu.contains(e.target) && userMenu.classList.contains('active')) {
                    userMenu.classList.remove('active');
                }
            });

            themeToggle.addEventListener('click', () => {
                document.body.classList.toggle('light');
                themeToggle.innerHTML = document.body.classList.contains('light')
                    ? '<i class="bi bi-sun"></i>'
                    : '<i class="bi bi-moon-stars"></i>';
            });

            sidebar.addEventListener('click', (e) => {
                e.stopPropagation();
            });

            window.addEventListener('resize', () => {
                if (window.innerWidth > 1024 && sidebar.classList.contains('active')) {
                    toggleSidebar();
                }
            });

            // Mark images as loaded to trigger CSS transitions
            document.querySelectorAll('img[loading="lazy"]').forEach(img => {
                img.addEventListener('load', () => {
                    img.setAttribute('loaded', 'true');
                });
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
