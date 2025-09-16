@extends('admin.layouts.app')

@section('title', 'Edit Class')

@section('description', 'Edit class details for Aunty Anne\'s International School.')

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

        .form-group {
            margin-bottom: var(--space-md);
        }

        .form-label {
            display: block;
            font-size: clamp(0.875rem, 2vw, 0.9375rem);
            font-weight: 500;
            color: var(--text-primary);
            margin-bottom: var(--space-xs);
        }

        .form-control {
            width: 100%;
            background: var(--bg-secondary);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-md);
            padding: var(--space-sm) var(--space-md);
            color: var(--text-primary);
            font-size: clamp(0.875rem, 2vw, 0.9375rem);
            transition: all 0.2s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-green);
            box-shadow: 0 0 0 3px rgba(33, 160, 85, 0.2);
        }

        .form-control::placeholder {
            color: var(--text-secondary);
            opacity: 0.7;
        }

        .btn-submit {
            background: var(--gradient-primary);
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

        .btn-submit:hover,
        .btn-submit:focus-visible {
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

        .required::after {
            content: ' *';
            color: var(--error);
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
            <h2 class="form-header">Edit Class: {{ $class->name }}</h2>
            <form method="POST" action="{{ route('admin.classes.update', $class->id) }}" id="editClassForm">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="name" class="form-label required">Class Name</label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name', $class->name) }}" placeholder="e.g., Primary 1" required>
                    @error('name')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="section" class="form-label">Section (Optional)</label>
                    <input type="text" name="section" id="section"
                        class="form-control @error('section') is-invalid @enderror"
                        value="{{ old('section', $class->section) }}" placeholder="e.g., A">
                    @error('section')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="hierarchy" class="form-label required">Hierarchy</label>
                    <input type="number" name="hierarchy" id="hierarchy"
                        class="form-control @error('hierarchy') is-invalid @enderror"
                        value="{{ old('hierarchy', $class->hierarchy) }}" placeholder="e.g., 1" required>
                    @error('hierarchy')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn-submit">
                    <i class="bx bx-save"></i> Update Class
                </button>
                <a href="{{ route('admin.classes.index') }}" class="btn-cancel">Cancel</a>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const form = document.getElementById('editClassForm');
                form.addEventListener('submit', (e) => {
                    const hierarchy = document.getElementById('hierarchy').value;
                    if (hierarchy < 1) {
                        e.preventDefault();
                        alert('Hierarchy must be a positive integer.');
                    }
                });
                gsap.from('.form-container', { opacity: 0, y: 20, duration: 0.6 });
                gsap.from('.form-group', { opacity: 0, y: 20, stagger: 0.1, duration: 0.6, delay: 0.2 });
            });
        </script>
    @endpush
@endsection
