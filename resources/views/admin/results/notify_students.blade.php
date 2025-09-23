@extends('admin.layouts.app')

@section('title', 'Notify Students for {{ $class->name }}')
@section('description', 'Send result notifications for {{ $class->name }} at Aunty Anne\'s International School.')

@push('styles')
    <style>
        .content-container {
            max-width: 90rem;
            margin: 0 auto;
            padding: var(--space-lg) var(--space-md);
        }

        .notify-form {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-xl);
            padding: var(--space-xl);
            margin-bottom: var(--space-2xl);
            position: relative;
        }

        .notify-form::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gradient-primary);
        }

        .form-header {
            font-family: var(--font-display);
            font-size: clamp(1.25rem, 3vw, 1.5rem);
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: var(--space-md);
        }

        .form-group {
            margin-bottom: var(--space-lg);
        }

        .form-label {
            display: block;
            font-size: clamp(0.875rem, 2vw, 0.9375rem);
            font-weight: 500;
            color: var(--text-primary);
            margin-bottom: var(--space-xs);
        }

        .form-textarea {
            width: 100%;
            background: var(--bg-secondary);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-md);
            padding: var(--space-sm) var(--space-md);
            color: var(--text-primary);
            font-size: clamp(0.875rem, 2vw, 0.9375rem);
            min-height: 150px;
        }

        .form-textarea:focus {
            outline: none;
            border-color: var(--primary-green);
            box-shadow: 0 0 0 3px rgba(33, 160, 85, 0.2);
        }

        .btn-submit {
            background: var(--gradient-primary);
            border: none;
            color: var(--white);
            padding: var(--space-md) var(--space-lg);
            border-radius: var(--radius-md);
            cursor: pointer;
            font-size: clamp(0.875rem, 2vw, 0.9375rem);
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            color: var(--primary-green);
            font-size: clamp(0.875rem, 2vw, 0.9375rem);
            margin-bottom: var(--space-md);
            text-decoration: none;
        }

        .back-link i {
            margin-right: var(--space-xs);
        }

        .back-link:hover {
            text-decoration: underline;
        }

        .alert {
            padding: var(--space-md);
            border-radius: var(--radius-md);
            margin-bottom: var(--space-lg);
        }

        .alert-success {
            background: rgba(33, 160, 85, 0.1);
            border: 1px solid var(--primary-green);
            color: var(--text-primary);
        }

        .alert-danger {
            background: rgba(220, 53, 69, 0.1);
            border: 1px solid var(--error);
            color: var(--text-primary);
        }

        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .loading-spinner {
            border: 4px solid var(--white);
            border-top: 4px solid var(--primary-green);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        @media (max-width: 768px) {
            .content-container {
                padding: var(--space-md);
            }
        }
    </style>
@endpush

@section('content')
    <div class="content-container">
        @if (!$currentSession || !$currentTerm)
            <div class="alert alert-danger">
                No academic session or term is set. Please <a
                    href="{{ route('admin.select_class', ['action' => 'notify_students']) }}">select a class</a>.
            </div>
        @else
            <a href="{{ route('admin.select_class', ['action' => 'notify_students']) }}" class="back-link">
                <i class="bx bx-arrow-back"></i> Back to Select Class
            </a>

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="notify-form">
                <h3 class="form-header">Notify Students for {{ $class->name }} - {{ $currentSession->year }}
                    {{ $currentTerm->value }}
                </h3>
                <form id="notifyForm" action="{{ route('admin.notify_students', ['className' => urlencode($class->name)]) }}"
                    method="POST">
                    @csrf
                    <div class="loading-overlay" id="loadingOverlay">
                        <div class="loading-spinner"></div>
                    </div>
                    <div class="form-group">
                        <label for="message" class="form-label">Notification Message</label>
                        <textarea name="message" id="message" class="form-textarea"
                            placeholder="Enter your message to students/parents" required></textarea>
                        <p class="text-[var(--text-secondary)] mt-2">
                            This message will be included in the email sent to parents, along with the term summary (grand
                            total,
                            term average, and principal's remark).
                        </p>
                    </div>
                    <button type="submit" class="btn-submit">Send Notifications</button>
                </form>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // GSAP Animations
            gsap.from('.notify-form', { opacity: 0, y: 20, duration: 0.6 });

            const notifyForm = document.getElementById('notifyForm');
            const loadingOverlay = document.getElementById('loadingOverlay');

            if (notifyForm) {
                notifyForm.addEventListener('submit', (e) => {
                    loadingOverlay.style.display = 'flex';
                });
            }
        });
    </script>
@endpush
