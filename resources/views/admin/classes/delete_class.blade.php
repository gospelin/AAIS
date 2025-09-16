@extends('admin.layouts.app')

@section('title', 'Delete Class')

@section('description', 'Confirm deletion of a class at Aunty Anne\'s International School.')

@push('styles')
    <style>
        .form-container {
            max-width: 600px;
            margin: 0 auto;
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-xl);
            padding: var(--space-2xl);
            position: relative;
            overflow: hidden;
            margin-bottom: var(--space-2xl);
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

        .warning-text {
            color: var(--error);
            font-size: clamp(0.875rem, 2vw, 0.9375rem);
            margin-bottom: var(--space-lg);
            text-align: center;
        }

        .btn-delete {
            background: var(--error);
            border: none;
            color: var(--white);
            font-size: clamp(0.875rem, 2.5vw, 1rem);
            font-weight: 600;
            padding: var(--space-md) var(--space-lg);
            border-radius: var(--radius-lg);
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            width: 100%;
            margin-top: var(--space-md);
        }

        .btn-delete:hover,
        .btn-delete:focus-visible {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
            color: var(--white);
        }

        .btn-cancel {
            background: var(--bg-secondary);
            border: 1px solid var(--glass-border);
            color: var(--text-primary);
            font-size: clamp(0.875rem, 2.5vw, 1rem);
            font-weight: 600;
            padding: var(--space-md) var(--space-lg);
            border-radius: var(--radius-lg);
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            width: 100%;
            margin-top: var(--space-sm);
            text-align: center;
            text-decoration: none;
        }

        .btn-cancel:hover,
        .btn-cancel:focus-visible {
            background: var(--primary-green);
            color: var(--white);
        }

        .alert {
            padding: var(--space-md);
            border-radius: var(--radius-md);
            margin-bottom: var(--space-md);
        }

        .alert-error {
            background: rgba(220, 53, 69, 0.1);
            border: 1px solid var(--error);
            color: var(--text-primary);
        }
    </style>
@endpush

@section('content')
    <div class="content-container">
        @if (session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        <div class="form-container">
            <h2 class="form-header">Delete Class: {{ $class->name }}</h2>
            <p class="warning-text">Are you sure you want to delete the class "{{ $class->name }}"? This action cannot be undone.</p>
            <form method="POST" action="{{ route('admin.classes.destroy', $class->id) }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-delete">
                    <i class="bx bx-trash"></i> Delete Class
                </button>
                <a href="{{ route('admin.classes.index') }}" class="btn-cancel">Cancel</a>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                gsap.from('.form-container', { opacity: 0, y: 20, duration: 0.6 });
                gsap.from('.warning-text', { opacity: 0, y: 20, duration: 0.6, delay: 0.2 });
                gsap.from('button, a', { opacity: 0, y: 20, stagger: 0.1, duration: 0.6, delay: 0.4 });
            });
        </script>
    @endpush
@endsection