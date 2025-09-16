@extends('admin.layouts.app')

@section('title', 'Delete Academic Session')

@section('description', 'Confirm deletion of an academic session for Aunty Anne\'s International School.')

@push('styles')
    <style>
        .content-container {
            max-width: 90rem;
            margin: 0 auto;
            padding: var(--space-lg) var(--space-md);
        }

        .form-container {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-xl);
            padding: var(--space-xl);
            margin-bottom: var(--space-2xl);
            position: relative;
            overflow: hidden;
        }

        .form-container::before {
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
            font-size: clamp(1.5rem, 3.5vw, 2rem);
            font-weight: 700;
            background: var(--gradient-primary);
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-align: center;
            margin-bottom: var(--space-xl);
        }

        .confirm-message {
            font-size: clamp(1rem, 2.5vw, 1.125rem);
            color: var(--text-primary);
            text-align: center;
            margin-bottom: var(--space-lg);
        }

        .btn-container {
            display: flex;
            gap: var(--space-md);
            justify-content: center;
        }

        .btn-submit,
        .btn-cancel {
            background: var(--gradient-primary);
            border: none;
            color: var(--white);
            font-size: clamp(0.875rem, 2.5vw, 1rem);
            font-weight: 600;
            padding: var(--space-md) var(--space-lg);
            border-radius: var(--radius-lg);
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            width: 200px;
            text-align: center;
        }

        .btn-cancel {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            color: var(--text-primary);
        }

        .btn-submit:hover,
        .btn-submit:focus-visible,
        .btn-cancel:hover,
        .btn-cancel:focus-visible {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .alert {
            padding: var(--space-md);
            border-radius: var(--radius-md);
            margin-bottom: var(--space-lg);
            font-size: clamp(0.875rem, 2vw, 0.9375rem);
        }

        .alert-error {
            background: rgba(220, 53, 69, 0.1);
            border: 1px solid var(--error);
            color: var(--text-primary);
        }

        @media (max-width: 768px) {
            .form-container {
                padding: var(--space-xl);
            }

            .btn-container {
                flex-direction: column;
                gap: var(--space-sm);
            }

            .btn-submit,
            .btn-cancel {
                width: 100%;
            }
        }
    </style>
@endpush

@section('content')
    <div class="content-container">
        <!-- Error Messages -->
        @if (session('error'))
            <div class="alert alert-error">
                {{ session('error') }}
            </div>
        @endif

        <div class="form-container">
            <h2 class="form-header">Delete Academic Session</h2>
            <p class="confirm-message">Are you sure you want to delete the academic session
                <strong>{{ $session->year }}</strong>? This action cannot be undone.
            </p>
            <form method="POST" action="{{ route('admin.manage_academic_sessions.destroy', $session->id) }}"
                id="deleteSessionForm">
                @csrf
                @method('DELETE')
                <div class="btn-container">
                    <button type="submit" class="btn-submit">
                        <i class="bx bx-trash"></i> Delete Session
                    </button>
                    <a href="{{ route('admin.manage_academic_sessions') }}" class="btn-cancel">
                        <i class="bx bx-x"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const form = document.getElementById('deleteSessionForm');

                form.addEventListener('submit', (e) => {
                    if (!confirm('Are you sure you want to delete this academic session?')) {
                        e.preventDefault();
                    }
                });

                // GSAP Animations
                gsap.from('.form-container', { opacity: 0, y: 20, duration: 0.6 });
                gsap.from('.confirm-message', { opacity: 0, y: 20, duration: 0.6, delay: 0.2 });
                gsap.from('.btn-container', { opacity: 0, y: 20, duration: 0.6, delay: 0.4 });
            });
        </script>
    @endpush
@endsection
