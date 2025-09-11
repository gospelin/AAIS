{{-- resources/views/admin/classes/edit_class.blade.php --}}

@extends('admin.layouts.app')

@section('title', 'Edit Class')

@section('description', 'Edit class details for Aunty Anne\'s International School.')

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

        .form-section {
            margin-bottom: var(--space-xl);
        }

        .form-section-title {
            font-family: var(--font-display);
            font-size: clamp(1rem, 2.5vw, 1.125rem);
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: var(--space-md);
            padding-left: var(--space-sm);
            border-left: 3px solid var(--primary-green);
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: var(--space-md);
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
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            color: var(--text-primary);
            font-size: clamp(0.875rem, 2.5vw, 1rem);
            font-weight: 600;
            padding: var(--space-md) var(--space-lg);
            border-radius: var(--radius-lg);
            text-align: center;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            width: 100%;
            margin-top: var(--space-md);
        }

        .btn-cancel:hover,
        .btn-cancel:focus-visible {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .required::after {
            content: ' *';
            color: var(--error);
        }

        .alert {
            padding: var(--space-md);
            border-radius: var(--radius-md);
            margin-bottom: var(--space-lg);
            font-size: clamp(0.875rem, 2vw, 0.9375rem);
        }

        .alert-success {
            background: rgba(33, 160, 85, 0.1);
            border: 1px solid var(--primary-green);
            color: var(--text-primary);
        }

        .alert-error {
            background: rgba(220, 53, 69, 0.1);
            border: 1px solid var(--error);
            color: var(--text-primary);
        }

        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
                gap: var(--space-sm);
            }

            .form-container {
                padding: var(--space-xl);
            }
        }
    </style>
@endpush

@section('content')
    <div class="content-container">
        <!-- Success/Error Messages -->
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

        <!-- Edit Class Form -->
        <div class="form-container">
            <h2 class="form-header">Edit Class: {{ $class->name }}</h2>
            <form method="POST" action="{{ route('admin.classes.update', $class->id) }}" id="classForm">
                @csrf
                @method('PUT')
                <div class="form-section">
                    <h3 class="form-section-title">Class Details</h3>
                    <div class="form-grid">
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
                            <input type="text" name="section" id="section" class="form-control @error('section') is-invalid @enderror" 
                                   value="{{ old('section', $class->section) }}" placeholder="e.g., A">
                            @error('section')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="hierarchy" class="form-label required">Hierarchy</label>
                            <input type="number" name="hierarchy" id="hierarchy" class="form-control @error('hierarchy') is-invalid @enderror" 
                                   value="{{ old('hierarchy', $class->hierarchy) }}" placeholder="e.g., 1" required>
                            @error('hierarchy')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn-submit">
                    <i class="bx bx-save"></i> Update Class
                </button>
                <a href="{{ route('admin.classes') }}" class="btn-cancel">
                    <i class="bx bx-x"></i> Cancel
                </a>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const form = document.getElementById('classForm');

                form.addEventListener('submit', (e) => {
                    const hierarchy = document.getElementById('hierarchy').value;
                    if (hierarchy < 1) {
                        e.preventDefault();
                        alert('Hierarchy must be a positive integer.');
                    }
                });

                // GSAP Animations
                gsap.from('.form-container', { opacity: 0, y: 20, duration: 0.6 });
                gsap.from('.form-group', { opacity: 0, y: 20, stagger: 0.1, duration: 0.6, delay: 0.2 });
            });
        </script>
    @endpush
@endsection
