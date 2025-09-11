<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>MFA Verification | Aunty Anne's International School</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous">
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
            width: 100%;
            max-width: 500px;
            min-height: 500px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            border: 1px solid var(--light-gray);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 12px 20px -5px rgba(0, 0, 0, 0.15);
            position: relative;
            z-index: 2;
            margin: 2rem auto;
            padding: 2rem;
        }

        .logo-section {
            text-align: center;
            margin-bottom: 2rem;
        }

        .logo {
            width: 80px;
            height: 80px;
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
            font-size: 1.75rem;
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

        .verify-btn {
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

        .verify-btn:hover {
            background: linear-gradient(90deg, var(--dark-green), var(--primary-green));
            transform: translateY(-2px);
            box-shadow: 0 4px 8px -1px rgba(0, 0, 0, 0.15);
        }

        .verify-btn:active {
            transform: translateY(0);
        }

        .verify-btn:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.2);
        }

        .verify-btn:disabled {
            pointer-events: none;
            opacity: 0.7;
        }

        .verify-btn .spinner {
            display: none;
        }

        .verify-btn:disabled .spinner {
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

        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }

            .auth-container {
                border-radius: 12px;
                padding: 1.5rem;
            }

            .welcome-text {
                font-size: 1.5rem;
            }

            .logo {
                width: 70px;
                height: 70px;
                margin-bottom: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="container min-h-screen d-flex align-items-center justify-content-center">
        <div class="auth-container">
            <div class="logo-section">
                <div class="logo">
                    <img src="{{ asset('images/school_logo.png') }}" alt="Aunty Anne's International School Logo" loading="lazy">
                </div>
                <h1 class="welcome-text">Verify Your Identity</h1>
                <p class="subtitle">Enter the code from your authenticator app</p>
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
            <form method="POST" action="{{ route('mfa.verify') }}"
                  x-data="{ submitting: false }" x-on:submit.prevent="submitting = true; $el.submit()" x-bind:disabled="submitting">
                @csrf
                <div class="form-group">
                    <input type="text" id="mfa_code" name="mfa_code" class="form-input"
                           placeholder="6-digit code" required aria-describedby="mfaValidation">
                    <label for="mfa_code" class="form-label">MFA Code</label>
                    <div class="validation-message" id="mfaValidation">
                        @error('mfa_code')
                            {{ $message }}
                        @enderror
                    </div>
                </div>
                <button type="submit" class="verify-btn" x-bind:disabled="submitting">
                    <span x-show="!submitting">Verify</span>
                    <span x-show="submitting" class="d-flex align-items-center justify-content-center">
                        <i class="fas fa-spinner fa-spin me-2 spinner"></i>
                        Verifying...
                    </span>
                    <div class="progress-bar"></div>
                </button>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // GSAP Animations
            gsap.set(['.form-group', '.verify-btn', '.welcome-text', '.subtitle'], {
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
                ease: 'power3.out'
            }, '-=0.3')
            .to('.verify-btn', {
                opacity: 1,
                y: 0,
                duration: 0.5,
                ease: 'back.out(1.4)'
            }, '-=0.2');

            // Input Validation
            const mfaInput = document.getElementById('mfa_code');
            if (mfaInput) {
                mfaInput.addEventListener('input', function () {
                    const validation = document.getElementById('mfaValidation');
                    if (!this.value) {
                        this.classList.remove('success', 'error');
                        validation.textContent = '';
                    } else if (/^\d{6}$/.test(this.value)) {
                        this.classList.add('success');
                        this.classList.remove('error');
                        validation.textContent = '✓ Valid MFA code';
                    } else {
                        this.classList.add('error');
                        this.classList.remove('success');
                        validation.textContent = 'MFA code must be 6 digits';
                    }
                });
            }

            // Form Submission with Progress Bar
            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', function () {
                    const button = form.querySelector('.verify-btn');
                    const progressBar = form.querySelector('.progress-bar');
                    button.disabled = true;
                    progressBar.style.width = '100%';
                    setTimeout(() => {
                        button.disabled = false;
                        progressBar.style.width = '0';
                    }, 1500);
                });
            });

            // Hover Effects
            document.querySelectorAll('.form-input, .verify-btn').forEach(element => {
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
