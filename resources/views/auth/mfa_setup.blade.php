<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>MFA Setup | Aunty Anne's International School</title>
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
            box-shadow: 0 12px 20px -5px rgba(0, 0, 0, 0.15);
            margin: 2rem auto;
            padding: 2rem;
            text-align: center;
        }

        .logo-section {
            margin-bottom: 2rem;
        }

        .logo {
            width: 80px;
            height: 80px;
            margin: 0 auto 1.5rem;
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
            margin-bottom: 1.5rem;
        }

        .qr-code {
            margin: 1.5rem auto;
            max-width: 200px;
        }

        .secret-key {
            font-family: monospace;
            background: var(--light-gray);
            padding: 0.5rem 1rem;
            border-radius: 8px;
            word-break: break-all;
            margin: 1rem auto;
            max-width: 300px;
        }

        .btn-primary {
            background: linear-gradient(90deg, var(--primary-green), var(--dark-green));
            border: none;
            border-radius: 10px;
            padding: 0.9rem 2rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(90deg, var(--dark-green), var(--primary-green));
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .error-message {
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            font-weight: 500;
            background: #fef2f2;
            border: 1px solid #fca5a5;
            color: #dc2626;
            text-align: center;
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
            transition: all 0.3s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--gold);
            background: var(--white);
            box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.2);
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
            transition: all 0.3s ease;
            background: var(--white);
            padding: 0 0.25rem;
        }

        .validation-message {
            font-size: 0.8rem;
            color: #dc2626;
            margin-top: 0.5rem;
        }

        .btn-primary .spinner {
            display: none;
        }

        .btn-primary:disabled .spinner {
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

        @media (max-width: 768px) {
            .auth-container {
                padding: 1.5rem;
                border-radius: 12px;
            }

            .welcome-text {
                font-size: 1.5rem;
            }

            .logo {
                width: 70px;
                height: 70px;
            }
        }
    </style>
</head>

<body>
    <div class="container min-h-screen d-flex align-items-center justify-content-center">
        <div class="auth-container">
            <div class="logo-section">
                <div class="logo">
                    <img src="{{ asset('images/school_logo.png') }}" alt="Aunty Anne's International School Logo"
                        loading="lazy">
                </div>
                <h1 class="welcome-text">Set Up Multi-Factor Authentication</h1>
                <p class="subtitle">Scan the QR code below with your authenticator app (e.g., Google Authenticator,
                    Authy).</p>
            </div>
            @if (session('error'))
                <div class="error-message" x-data="{ show: true }" x-show="show" x-transition>
                    {{ session('error') }}
                </div>
            @endif
            @if ($qrCodeUrl)
                <div class="qr-code">
                    {!! QrCode::size(200)->generate($qrCodeUrl) !!}
                </div>
                <p class="subtitle">Or manually enter this key in your authenticator app:</p>
                <div class="secret-key">{{ $mfaSecret }}</div>
                <form method="POST" action="{{ route('mfa.setup.verify') }}" x-data="{ submitting: false }"
                    x-on:submit.prevent="submitting = true; $el.submit()" x-bind:disabled="submitting">
                    @csrf
                    <div class="form-group">
                        <input type="text" id="mfa_code" name="mfa_code" class="form-input" placeholder="6-digit code"
                            required>
                        <label for="mfa_code" class="form-label">Verify MFA Code</label>
                        <div class="validation-message">
                            @error('mfa_code')
                                {{ $message }}
                            @enderror
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary" x-bind:disabled="submitting">
                        <span x-show="!submitting">Verify and Save</span>
                        <span x-show="submitting">Verifying...</span>
                    </button>
                </form>
            @else
                <div class="error-message">
                    Unable to generate QR code. Please contact support.
                </div>
            @endif
            <a href="{{ route('admin.dashboard') }}" class="btn btn-primary mt-3">Back to Dashboard</a>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            gsap.set(['.logo', '.welcome-text', '.subtitle', '.qr-code', '.secret-key', '.form-group', '.btn-primary'], {
                opacity: 0,
                y: 20
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
                .to(['.welcome-text', '.subtitle', '.qr-code', '.secret-key', '.form-group', '.btn-primary'], {
                    opacity: 1,
                    y: 0,
                    duration: 0.5,
                    stagger: 0.1,
                    ease: 'power3.out'
                }, '-=0.4');

            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', function () {
                    const button = form.querySelector('.btn-primary');
                    const progressBar = form.querySelector('.progress-bar');
                    button.disabled = true;
                    progressBar.style.width = '100%';
                    setTimeout(() => {
                        button.disabled = false;
                        progressBar.style.width = '0';
                    }, 1500);
                });
            });

            // Input Validation for MFA Code
            const mfaInput = document.getElementById('mfa_code');
            if (mfaInput) {
                mfaInput.addEventListener('input', function () {
                    const validation = document.querySelector('.validation-message');
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
        });
    </script>
</body>

</html>