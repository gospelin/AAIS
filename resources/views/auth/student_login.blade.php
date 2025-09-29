<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Student Login | Aunty Anne's International School</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"
        integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
        crossorigin="anonymous">
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
        }

        body {
            background: linear-gradient(135deg, #ffffff 0%, #E0E7FF 100%);
            min-height: 100vh;
            overflow-x: hidden;
            position: relative;
            font-family: var(--font-primary);
            color: var(--dark-gray);
        }

        .auth-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            width: 100%;
            max-width: 1100px;
            min-height: 600px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            border: 1px solid var(--light-gray);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 12px 20px -5px rgba(0, 0, 0, 0.15);
            position: relative;
            z-index: 2;
            margin: 2rem auto;
        }

        .content-section {
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
            position: relative;
            overflow: hidden;
        }

        .content-section::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 30% 70%, rgba(255, 255, 255, 0.15), transparent);
            z-index: 1;
        }

        .back-home {
            color: var(--white);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 2.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            position: relative;
            z-index: 2;
        }

        .back-home:hover {
            color: var(--gold);
            transform: translateX(-0.3rem);
        }

        .back-home::before {
            content: '←';
            font-size: 1rem;
        }

        .content-section h2 {
            font-family: var(--font-display);
            font-size: clamp(2rem, 3.5vw, 2.75rem);
            font-weight: 700;
            color: var(--white);
            margin-bottom: 1.5rem;
            line-height: 1.3;
            position: relative;
            z-index: 2;
        }

        .content-section p {
            font-size: 1.1rem;
            color: var(--light-gray);
            margin-bottom: 2rem;
            line-height: 1.7;
            position: relative;
            z-index: 2;
        }

        .cta-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.9rem 2rem;
            background: var(--gold);
            color: var(--dark-green);
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1rem;
            border: 1px solid rgba(255, 255, 255, 0.25);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            z-index: 2;
            width: fit-content;
        }

        .cta-btn:hover {
            background: var(--white);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px -1px rgba(0, 0, 0, 0.15);
        }

        .login-section {
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: var(--white);
        }

        .logo-section {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .logo {
            width: 90px;
            height: 90px;
            margin: 0 auto 1.5rem;
            position: relative;
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

        .welcome-text {
            font-family: var(--font-display);
            font-size: 2rem;
            font-weight: 700;
            color: var(--dark-green);
            margin-bottom: 0.5rem;
        }

        .subtitle {
            color: var(--dark-gray);
            font-size: 1rem;
            font-weight: 400;
        }

        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .form-input {
            width: 100%;
            padding: 0.9rem 1.25rem;
            background: var(--light-gray);
            border: 2px solid var(--dark-gray);
            border-radius: 10px;
            color: var(--dark-green);
            font-size: 1rem;
            font-weight: 400;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .form-input:focus {
            outline: none;
            border-color: var(--gold);
            background: var(--white);
            box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.2);
            transform: translateY(-1px);
        }

        .form-input:not(:placeholder-shown)+.form-label,
        .form-input:focus+.form-label {
            transform: translateY(-1.6rem) scale(0.85);
            color: var(--gold);
            font-weight: 500;
            background: var(--white);
            padding: 0 0.25rem;
        }

        .form-label {
            position: absolute;
            left: 1.25rem;
            top: 0.9rem;
            color: var(--dark-gray);
            font-size: 1rem;
            pointer-events: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            transform-origin: left center;
            background: var(--white);
            padding: 0 0.25rem;
        }

        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--dark-gray);
            cursor: pointer;
            font-size: 1.1rem;
            padding: 0.5rem;
            border-radius: 6px;
            transition: all 0.3s ease;
        }

        .password-toggle:hover,
        .password-toggle:focus {
            color: var(--dark-green);
            background: var(--light-gray);
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .checkbox {
            width: 1.25rem;
            height: 1.25rem;
            border: 2px solid var(--dark-gray);
            border-radius: 6px;
            background: var(--white);
            cursor: pointer;
            position: relative;
            appearance: none;
            transition: all 0.3s ease;
        }

        .checkbox:checked {
            background: var(--gold);
            border-color: var(--gold);
        }

        .checkbox:checked::after {
            content: '✓';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: var(--dark-green);
            font-size: 0.75rem;
            font-weight: 700;
        }

        .checkbox:focus {
            box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.2);
        }

        .checkbox-label {
            color: var(--dark-gray);
            font-size: 0.9rem;
            cursor: pointer;
            user-select: none;
        }

        .forgot-password a {
            color: var(--primary-green);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .forgot-password a:hover {
            color: var(--gold);
            text-decoration: underline;
        }

        .login-btn {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(90deg, var(--primary-green), var(--dark-green));
            border: none;
            border-radius: 10px;
            color: var(--white);
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            position: relative;
            overflow: hidden;
        }

        .login-btn:hover {
            background: linear-gradient(90deg, var(--dark-green), var(--primary-green));
            transform: translateY(-2px);
            box-shadow: 0 4px 8px -1px rgba(0, 0, 0, 0.15);
        }

        .login-btn:active {
            transform: translateY(0);
        }

        .login-btn:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.2);
        }

        .login-btn:disabled {
            pointer-events: none;
            opacity: 0.7;
        }

        .login-btn .spinner {
            display: none;
        }

        .login-btn:disabled .spinner {
            display: inline-block;
        }

        .progress-bar {
            position: absolute;
            top: 0;
            left: 0;
            height: 4px;
            background: var(--gold);
            width: 0;
            transition: width 1.5s ease;
        }

        .ripple {
            position: absolute;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            transform: scale(0);
            animation: rippleEffect 0.6s linear;
            pointer-events: none;
        }

        .error-message,
        .success-message {
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            font-weight: 500;
            border: 1px solid;
            text-align: center;
        }

        .error-message {
            background: #fef2f2;
            border-color: #fca5a5;
            color: #dc2626;
        }

        .success-message {
            background: #f0fdf4;
            border-color: #86efac;
            color: #166534;
        }

        .validation-message {
            font-size: 0.8rem;
            margin-top: 0.5rem;
        }

        .form-input.error {
            border-color: #dc2626;
            background: #fef2f2;
        }

        .form-input.success {
            border-color: #16a34a;
            background: #f0fdf4;
        }

        @keyframes rippleEffect {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }

        @media (max-width: 1024px) {
            .auth-container {
                grid-template-columns: 1fr;
                max-width: 480px;
                min-height: auto;
                margin: 1.5rem auto;
            }

            .content-section,
            .login-section {
                padding: 2rem;
                text-align: center;
            }

            .cta-btn {
                margin: 0 auto;
            }
        }

        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }

            .auth-container {
                border-radius: 12px;
            }

            .content-section,
            .login-section {
                padding: 1.5rem;
            }

            .content-section h2 {
                font-size: 1.75rem;
                margin-bottom: 1rem;
            }

            .content-section p {
                font-size: 1rem;
                margin-bottom: 1.5rem;
            }

            .welcome-text {
                font-size: 1.75rem;
            }

            .logo {
                width: 70px;
                height: 70px;
                margin-bottom: 1rem;
            }

            .form-options {
                flex-direction: column;
                gap: 0.75rem;
                align-items: center;
            }
        }
    </style>
</head>

<body>
    <div class="container min-h-screen d-flex align-items-center justify-content-center">
        <div class="auth-container">
            <div class="content-section">
                <a href="{{ route('home') }}" class="back-home">Back to Home</a>
                <h2>Student Login</h2>
                <p>Access your student portal to view results, fee status, and more.</p>
                <a href="{{ route('admin.login') }}" class="cta-btn">Login as Admin</a>
                <a href="{{ route('staff.login') }}" class="cta-btn mt-2">Login as Staff</a>
            </div>
            <div class="login-section">
                <div class="logo-section">
                    <div class="logo">
                        <img src="{{ asset('images/school_logo.png') }}" alt="Aunty Anne's International School Logo"
                            loading="lazy">
                    </div>
                    <h1 class="welcome-text">Student Portal</h1>
                    <p class="subtitle">Sign in with your Student ID</p>
                </div>
                @if (session('status'))
                    <div class="success-message" x-data="{ show: true }" x-show="show" x-transition>
                        {{ session('status') }}
                    </div>
                @else
                    <div class="success-message" x-data="{ show: false }" x-show="show" x-transition></div>
                @endif
                @if (session('error'))
                    <div class="error-message" x-data="{ show: true }" x-show="show" x-transition>
                        {{ session('error') }}
                    </div>
                @else
                    <div class="error-message" x-data="{ show: false }" x-show="show" x-transition></div>
                @endif
                <form method="POST" action="{{ route('login') }}" x-data="{ submitting: false }"
                    x-on:submit.prevent="submitting = true; $el.submit()" x-bind:disabled="submitting">
                    @csrf
                    <div class="form-group">
                        <input type="text" id="identifier" name="identifier" class="form-input"
                            placeholder="e.g., AAIS/0559/001" value="{{ old('identifier') }}" required
                            aria-describedby="identifierValidation">
                        <label for="identifier" class="form-label">Student ID</label>
                        <div class="validation-message" id="identifierValidation">
                            @error('identifier')
                                {{ $message }}
                            @enderror
                        </div>
                    </div>
                    <div class="form-group">
                        <input type="password" id="password" name="password" class="form-input" placeholder="Password"
                            required aria-describedby="passwordValidation">
                        <label for="password" class="form-label">Password</label>
                        <button type="button" class="password-toggle" aria-label="Show password">
                            <i class="fas fa-eye"></i>
                        </button>
                        <div class="validation-message" id="passwordValidation">
                            @error('password')
                                {{ $message }}
                            @enderror
                        </div>
                    </div>
                    <div class="form-options">
                        <div class="remember-me">
                            <input type="checkbox" id="remember" name="remember" class="checkbox"
                                aria-label="Remember me">
                            <label for="remember" class="checkbox-label">Remember me</label>
                        </div>
                        <div class="forgot-password">
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}">Forgot your password?</a>
                            @endif
                        </div>
                    </div>
                    <button type="submit" class="login-btn" x-bind:disabled="submitting">
                        <span x-show="!submitting">Sign In</span>
                        <span x-show="submitting" class="d-flex align-items-center justify-content-center">
                            <i class="fas fa-spinner fa-spin me-2 spinner"></i>
                            Signing In...
                        </span>
                        <div class="progress-bar"></div>
                    </button>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // GSAP Animations
            gsap.set(['.content-section > *', '.form-group', '.login-btn'], {
                opacity: 0,
                y: 20
            });
            gsap.set('.logo', {
                opacity: 0,
                scale: 0.9
            });
            const tl = gsap.timeline({ delay: 0.3 });
            tl.to('.auth-container', {
                opacity: 1,
                scale: 1,
                duration: 0.7,
                ease: 'power3.out'
            })
                .to('.logo', {
                    opacity: 1,
                    scale: 1,
                    duration: 0.6,
                    ease: 'back.out(1.4)'
                }, '-=0.5')
                .to('.content-section > *', {
                    opacity: 1,
                    y: 0,
                    duration: 0.5,
                    stagger: 0.1,
                    ease: 'power3.out'
                }, '-=0.5')
                .to(['.welcome-text', '.subtitle'], {
                    opacity: 1,
                    y: 0,
                    duration: 0.5,
                    ease: 'power3.out'
                }, '-=0.4')
                .to('.form-group', {
                    opacity: 1,
                    y: 0,
                    duration: 0.4,
                    stagger: 0.1,
                    ease: 'power3.out'
                }, '-=0.3')
                .to('.login-btn', {
                    opacity: 1,
                    y: 0,
                    duration: 0.5,
                    ease: 'back.out(1.4)'
                }, '-=0.2');

            // Password Visibility Toggle
            const passwordToggle = document.querySelector('.password-toggle');
            if (passwordToggle) {
                passwordToggle.addEventListener('click', () => {
                    const input = document.getElementById('password');
                    const icon = passwordToggle.querySelector('i');
                    const isVisible = input.type === 'password';
                    input.type = isVisible ? 'text' : 'password';
                    icon.classList.toggle('fa-eye', !isVisible);
                    icon.classList.toggle('fa-eye-slash', isVisible);
                    passwordToggle.setAttribute('aria-label', isVisible ? 'Hide password' : 'Show password');
                });
            }

            // Input Validation
            const identifierInput = document.getElementById('identifier');
            const passwordInput = document.getElementById('password');
            const studentIdRegex = /^AAIS\/0559\/\d{3}$/;

            if (identifierInput) {
                identifierInput.addEventListener('input', function () {
                    const value = this.value.trim();
                    const validation = document.getElementById('identifierValidation');
                    if (!value) {
                        this.classList.remove('success', 'error');
                        validation.textContent = '';
                    } else if (studentIdRegex.test(value)) {
                        this.classList.add('success');
                        this.classList.remove('error');
                        validation.textContent = '✓ Valid Student ID';
                    } else {
                        this.classList.add('error');
                        this.classList.remove('success');
                        validation.textContent = 'Invalid Student ID (use AAIS/0559/XXX)';
                    }
                });
            }

            if (passwordInput) {
                passwordInput.addEventListener('input', function () {
                    const validation = document.getElementById('passwordValidation');
                    if (!this.value) {
                        this.classList.remove('success', 'error');
                        validation.textContent = '';
                    } else {
                        this.classList.add('success');
                        this.classList.remove('error');
                        validation.textContent = '✓ Password entered';
                    }
                });
            }

            // Form Submission with Progress Bar and Ripple Effect
            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', function () {
                    const button = form.querySelector('.login-btn');
                    const progressBar = form.querySelector('.progress-bar');
                    button.disabled = true;
                    progressBar.style.width = '100%';
                    setTimeout(() => {
                        button.disabled = false;
                        progressBar.style.width = '0';
                    }, 1500);
                });
            });

            document.querySelectorAll('.login-btn').forEach(btn => {
                btn.addEventListener('click', function (e) {
                    const rect = btn.getBoundingClientRect();
                    const ripple = document.createElement('span');
                    ripple.classList.add('ripple');
                    const size = Math.max(rect.width, rect.height);
                    ripple.style.width = ripple.style.height = `${size}px`;
                    ripple.style.left = `${e.clientX - rect.left - size / 2}px`;
                    ripple.style.top = `${e.clientY - rect.top - size / 2}px`;
                    btn.appendChild(ripple);
                    setTimeout(() => ripple.remove(), 600);
                });
            });

            // Hover Effects
            document.querySelectorAll('.form-input, .login-btn, .cta-btn').forEach(element => {
                element.addEventListener('mouseenter', () => {
                    gsap.to(element, { scale: 1.02, duration: 0.3, ease: 'power2.out' });
                });
                element.addEventListener('mouseleave', () => {
                    gsap.to(element, { scale: 1, duration: 0.3, ease: 'power2.out' });
                });
            });
        });
    </script>
</body>

</html>