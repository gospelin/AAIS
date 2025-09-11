<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>
    <meta name="description"
        content="Explore {{ config('app.name') }} – dedicated to practical learning, knowledge, and confidence-building for young minds.">
    <meta name="keywords"
        content="{{ config('app.name') }}, Aunty Anne's, Auntie Anne's, Aunty Annes, AAIS, Best Schools in Nigeria, Private Schools in Aba, Best Schools in Aba, Best Schools in Abia, Education in Aba, Knowledge and Confidence">
    <meta name="author" content="{{ config('app.name') }}">
    <meta property="og:title" content="@yield('title', config('app.name'))">
    <meta property="og:description"
        content="In {{ config('app.name') }}, we provide education focused on practical learning and confidence-building.">
    <meta property="og:image" content="{{ asset('images/favicons/favicon-96x96.png') }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:image" content="{{ asset('images/favicons/favicon-96x96.png') }}">

    <link rel="preload" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" as="style"
        onload="this.onload=null;this.rel='stylesheet'">
    <link rel="preload"
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Lora:wght@400;700&family=Open+Sans:wght@400;600&display=swap"
        as="style">
    <noscript>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    </noscript>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Lora:wght@400;700&family=Open+Sans:wght@400;600&display=swap"
        rel="stylesheet">

    @vite(['resources/js/app.js', 'resources/css/styles.css'])

    <link rel="icon" type="image/png" href="{{ asset('images/favicons/favicon-96x96.png') }}" sizes="96x96">
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicons/favicon.svg') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/favicons/apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('images/favicons/site.webmanifest') }}">
    <link rel="canonical" href="https://auntyannesschools.com.ng">
    <link rel="sitemap" type="application/xml" href="{{ asset('sitemap.xml') }}">

    <!-- Structured Data for SEO -->
    <script type="application/ld+json">
    {!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'School',
    'name' => "{{ config('app.name') }}",
    'address' => [
        '@type' => 'PostalAddress',
        'streetAddress' => "No 6 Oomnne Drive by winner's bus-stop opposite Ngwa high school",
        'addressLocality' => 'Abayi, Aba',
        'addressRegion' => 'Abia',
        'addressCountry' => 'NG'
    ],
    'telephone' => '+234-806-967-8968, +234-803-668-8517',
    'email' => 'info@auntyannesschools.com.ng',
    'url' => 'https://auntyannesschools.com.ng',
    'logo' => [
        '@type' => 'ImageObject',
        'url' => 'https://auntyannesschools.com.ng/images/school_logo.png',
        'description' => "Aunty Anne's International School Logo"
    ],
    'founder' => [
        '@type' => 'Person',
        'name' => 'Mrs. Anne Isaac'
    ],
    'contactPoint' => [
        '@type' => 'ContactPoint',
        'telephone' => '+234-806-967-8968',
        'contactType' => 'Customer Service',
        'email' => 'info@auntyannesschools.com.ng'
    ]
], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>

    <!-- Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-9CLQVHQ1D2"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag() { dataLayer.push(arguments); }
        gtag('js', new Date());
        gtag('config', 'G-9CLQVHQ1D2');
    </script>

    @stack('styles')
</head>

<body>
    <div class="d-flex flex-column min-vh-100">
        <header class="sticky-top" role="banner">
            <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
                <div class="container-fluid">
                    <a class="school-name navbar-brand d-flex align-items-center" href="{{ route('home') }}">
                        <img src="{{ asset('images/school_logo.png') }}" alt="Aunty Anne's International School Logo"
                            loading="lazy" class="d-inline-block align-top img-fluid" style="max-height: 50px; margin-right: 10px;">
                        <span class="logo-text">{{ config('app.name') }}</span>
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarNav"
                        aria-controls="sidebarNav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="toggler-icon top-bar"></span>
                        <span class="toggler-icon middle-bar"></span>
                        <span class="toggler-icon bottom-bar"></span>
                    </button>

                    <div class="navbar-nav ms-auto d-none d-xl-flex">
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('/') ? 'active' : '' }}"
                                    href="{{ route('home') }}">Home</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('about') ? 'active' : '' }}"
                                    href="{{ route('about') }}">About Us</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('admissions') ? 'active' : '' }}"
                                    href="{{ route('admissions') }}">Admissions</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('programs') ? 'active' : '' }}"
                                    href="{{ route('programs') }}">Programs</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('news') ? 'active' : '' }}"
                                    href="{{ route('news') }}">News</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('gallery') ? 'active' : '' }}"
                                    href="{{ route('gallery') }}">Gallery</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="https://gigo.pythonanywhere.com/portal"
                                    target="_blank">Portal</a>
                            </li>
                            @auth
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('student.dashboard') }}">Dashboard</a>
                                </li>
                                <li class="nav-item">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="nav-link btn btn-link">Logout</button>
                                    </form>
                                </li>
                            @else
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endauth
                        </ul>
                    </div>
                </div>
            </nav>
            <!-- Sidebar for mobile -->
            <div class="sidebar" id="sidebarNav" aria-hidden="true">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="{{ route('home') }}">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('about') ? 'active' : '' }}"
                            href="{{ route('about') }}">About Us</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admissions') ? 'active' : '' }}"
                            href="{{ route('admissions') }}">Admissions</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('programs') ? 'active' : '' }}"
                            href="{{ route('programs') }}">Programs</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('news') ? 'active' : '' }}"
                            href="{{ route('news') }}">News</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('gallery') ? 'active' : '' }}"
                            href="{{ route('gallery') }}">Gallery</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="https://gigo.pythonanywhere.com/portal" target="_blank">Portal</a>
                    </li>
                    @auth
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('student.dashboard') }}">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="nav-link btn btn-link">Logout</button>
                            </form>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                        </li>
                    @endauth
                </ul>
            </div>
            <div class="sidebar-backdrop" id="sidebarBackdrop"></div>
        </header>

        <main class="flex-fill" role="main">
            @yield('content')
        </main>

        <footer class="footer" role="contentinfo">
            <div class="container-fluid">
                <div class="row footer-content g-4">
                    <div class="col-12 col-md-6 col-lg-3 footer-section">
                        <h3>{{ config('app.name') }} (AAIS)</h3>
                        <p>Leading the future of education with innovative approaches that empower students to achieve
                            excellence.</p>
                    </div>
                    <div class="col-12 col-md-6 col-lg-3 footer-section">
                        <h3>Quick Links</h3>
                        <ul class="list-unstyled">
                            <li><a href="{{ route('home') }}" class="text-white">Home</a></li>
                            <li><a href="{{ route('about') }}" class="text-white">About Us</a></li>
                            <li><a href="{{ route('admissions') }}" class="text-white">Admissions</a></li>
                            <li><a href="{{ route('programs') }}" class="text-white">Programs</a></li>
                            <li><a href="{{ route('gallery') }}" class="text-white">Gallery</a></li>
                            <li><a href="{{ route('contact') }}" class="text-white">Contact</a></li>
                            <li><a href="{{ route('news') }}" class="text-white">News</a></li>
                        </ul>
                    </div>
                    <div class="col-12 col-md-6 col-lg-3 footer-section">
                        <h3>Contact</h3>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-map-marker-alt me-2"></i> No 6 Oomnne Drive, Abayi, Aba</li>
                            <li><i class="fas fa-phone-alt me-2"></i> +234-806-967-8968</li>
                            <li><i class="fas fa-envelope me-2"></i> <a href="mailto:info@auntyannesschools.com.ng"
                                    class="text-white">contact@auntyannesschools.com.ng</a></li>
                        </ul>
                        <div class="social-icons mt-3 d-flex justify-content-start gap-3">
                            <a href="https://facebook.com/auntyannesschools" target="_blank" aria-label="Facebook"><i
                                    class="fab fa-facebook-f"></i></a>
                            <a href="https://twitter.com/auntyannesschools" target="_blank" aria-label="Twitter"><i
                                    class="fab fa-twitter"></i></a>
                            <a href="https://instagram.com/auntyannesschools" target="_blank" aria-label="Instagram"><i
                                    class="fab fa-instagram"></i></a>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-3 footer-section">
                        <h3>Newsletter</h3>
                        <form action="#" method="POST">
                            @csrf
                            <div class="mb-3">
                                <input type="text" class="form-control" name="name" placeholder="Enter Your Name" required
                                    maxlength="255" value="{{ old('name') }}" aria-label="Your Name">
                            </div>
                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                            <div class="mb-3">
                                <input type="email" class="form-control" name="email" placeholder="Enter Your Email"
                                    required maxlength="255" value="{{ old('email') }}" aria-label="Your Email">
                            </div>
                            @error('email')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                            <button class="btn btn-primary w-100" type="submit"
                                aria-label="Subscribe to Newsletter">Subscribe</button>
                        </form>
                    </div>
                </div>
                <div class="text-center mt-4 border-top pt-3">
                    <p class="mb-0">© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
                </div>
            </div>
        </footer>

        <!-- Back to Top Button -->
        <button type="button" id="back-to-top" class="btn btn-primary back-to-top" aria-label="Scroll to top">↑</button>
        <!-- Floating CTA -->
        <a href="{{ route('admissions') }}" class="floating-cta btn btn-primary" aria-label="Apply Now">Apply Now</a>
    </div>
    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    @stack('scripts')
</body>

</html>
