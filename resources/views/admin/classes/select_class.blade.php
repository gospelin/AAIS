{{-- resources/views/admin/classes/select_class.blade.php --}}

@extends('admin.layouts.app')

@section('title', 'Select Class')

@section('description', 'Select a class to perform actions for Aunty Anne\'s International School.')

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

        .form-group {
            margin-bottom: var(--space-md);
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
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

        .form-select {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
            background-position: right var(--space-md) center;
            background-repeat: no-repeat;
            background-size: 1.5em 1.5em;
            padding-right: 2.5rem;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
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
            .form-container {
                padding: var(--space-xl);
            }

            .form-group {
                max-width: 100%;
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

        <!-- Select Class Form -->
        <div class="form-container">
            <h2 class="form-header">
                @switch($action)
                    @case('view_students')
                        Select Class to View Students
                        @break
                    @case('promote')
                        Select Class to Promote Students
                        @break
                    @case('demote')
                        Select Class to Demote Students
                        @break
                    @case('delete_from_class')
                        Select Class to Delete Students
                        @break
                    @case('manage_result')
                        Select Class to Manage Results
                        @break
                    @case('generate_broadsheet')
                        Select Class to Generate Broadsheet
                        @break
                    @case('download_broadsheet')
                        Select Class to Download Broadsheet
                        @break
                    @default
                        Select Class
                @endswitch
            </h2>
            <form method="POST" action="{{ route('admin.select_class', ['action' => $action]) }}" id="selectClassForm">
                @csrf
                <div class="form-section">
                    <h3 class="form-section-title">Choose a Class</h3>
                    <div class="form-group">
                        <label for="class_id" class="form-label required">Class</label>
                        <select name="class_id" id="class_id" class="form-control form-select @error('class_id') is-invalid @enderror" required>
                            <option value="">Select a Class</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}">{{ $class->name }} {{ $class->section ? '(' . $class->section . ')' : '' }}</option>
                            @endforeach
                        </select>
                        @error('class_id')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <button type="submit" class="btn-submit">
                    <i class="bx bx-check"></i> Proceed
                </button>
                <a href="{{ route('admin.classes.index') }}" class="btn-cancel">
                    <i class="bx bx-x"></i> Cancel
                </a>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const form = document.getElementById('selectClassForm');

                form.addEventListener('submit', (e) => {
                    const classId = document.getElementById('class_id').value;
                    if (!classId) {
                        e.preventDefault();
                        alert('Please select a class before proceeding.');
                    }
                });

                // GSAP Animations
                gsap.from('.form-container', { opacity: 0, y: 20, duration: 0.6 });
                gsap.from('.form-group', { opacity: 0, y: 20, duration: 0.6, delay: 0.2 });
                gsap.from('.btn-submit, .btn-cancel', { opacity: 0, y: 20, duration: 0.6, delay: 0.4 });
            });
        </script>
    @endpush
@endsection
