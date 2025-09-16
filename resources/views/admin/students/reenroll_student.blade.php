```blade
@extends('admin.layouts.app')

@section('title', 'Re-enroll Student')

@section('description', 'Re-enroll a student at Aunty Anne\'s International School.')

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

        .required::after {
            content: ' *';
            color: var(--error);
        }

        .alert {
            padding: var(--space-md);
            border-radius: var(--radius-md);
            margin-bottom: var(--space-md);
        }

        .alert-success {
            background: rgba(33, 160, 85, 0.1);
            color: var(--primary-green);
        }

        .alert-danger {
            background: rgba(220, 53, 69, 0.1);
            color: var(--error);
        }

        .text-danger {
            color: var(--error);
        }

        .is-invalid {
            border-color: var(--error);
        }

        @media (max-width: 768px) {
            .form-container {
                padding: var(--space-xl);
            }
        }
    </style>
@endpush

@section('content')
    <div class="content-container">
        @if(!$currentSession)
            <div class="alert alert-danger">
                No current academic session is set. Please <a href="{{ route('admin.manage_academic_sessions') }}">set a current
                    session</a> to re-enroll students.
            </div>
        @else
            <div class="form-container">
                <h2 class="form-header">Re-enroll Student: {{ $student->full_name ?? 'Unknown' }} ({{ $student->reg_no }})</h2>
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                <form method="POST" action="{{ route('admin.student_reenroll', $student->id) }}">
                    @csrf
                    <div class="form-section">
                        <h3 class="form-section-title">Re-enrollment Details</h3>
                        <div class="form-group">
                            <label for="class_id" class="form-label required">Class/Section</label>
                            <select name="class_id" id="class_id"
                                class="form-control form-select @error('class_id') is-invalid @enderror" required>
                                <option value="">Select Class</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                        {{ $class->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('class_id')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="start_term" class="form-label required">Starting Term</label>
                            <select name="start_term" id="start_term"
                                class="form-control form-select @error('start_term') is-invalid @enderror" required>
                                <option value="">Select Term</option>
                                @foreach($termChoices as $term)
                                    @if($term->value == $currentTerm->value || $term->value == 'Third')
                                        <option value="{{ $term->value }}" {{ old('start_term') == $term->value ? 'selected' : '' }}>
                                            {{ $term->label }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                            @error('start_term')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <button type="submit" class="btn-submit">Re-enroll Student</button>
                </form>
            </div>
        @endif
    </div>
@endsection
