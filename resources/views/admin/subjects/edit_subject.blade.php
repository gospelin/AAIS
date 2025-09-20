@extends('admin.layouts.app')

@section('title', 'Edit Subject - {{ $subject->name }}')
@section('description', "Edit subject {{ $subject->name }} at Aunty Anne's International School.")

@push('styles')
    <style>
        .content-container {
            max-width: 90rem;
            margin: 0 auto;
            padding: var(--space-lg) var(--space-md);
        }

        .form-section {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-xl);
            padding: var(--space-xl);
            margin-bottom: var(--space-2xl);
            position: relative;
        }

        .form-section::before {
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
            margin-bottom: var(--space-md);
        }

        .form-label {
            display: block;
            font-size: clamp(0.875rem, 2vw, 0.9375rem);
            font-weight: 500;
            color: var(--text-primary);
            margin-bottom: var(--space-xs);
        }

        .form-input {
            width: 100%;
            background: var(--bg-secondary);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-md);
            padding: var(--space-sm) var(--space-md);
            color: var(--text-primary);
            font-size: clamp(0.875rem, 2vw, 0.9375rem);
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary-green);
            box-shadow: 0 0 0 3px rgba(33, 160, 85, 0.2);
        }

        .form-checkbox {
            margin-right: var(--space-xs);
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
    </style>
@endpush

@section('content')
    <div class="content-container">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="form-section">
            <h3 class="form-header">Edit Subject: {{ $subject->name }}</h3>
            <form action="{{ route('admin.subjects.edit', $subject->id) }}" method="POST">
                @csrf
                @method('POST')
                <div class="form-group">
                    <label for="name" class="form-label">Subject Name</label>
                    <input type="text" name="name" id="name" class="form-input" value="{{ old('name', $subject->name) }}"
                        required>
                    @error('name')
                        <span class="text-[var(--error)]">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="section" class="form-label">Section (Optional)</label>
                    <input type="text" name="section" id="section" class="form-input"
                        value="{{ old('section', $subject->section) }}">
                    @error('section')
                        <span class="text-[var(--error)]">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="description" class="form-label">Description (Optional)</label>
                    <textarea name="description" id="description"
                        class="form-input">{{ old('description', $subject->description) }}</textarea>
                    @error('description')
                        <span class="text-[var(--error)]">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">
                        <input type="checkbox" name="deactivated" class="form-checkbox" {{ $subject->deactivated ? 'checked' : '' }}>
                        Deactivate Subject
                    </label>
                    @error('deactivated')
                        <span class="text-[var(--error)]">{{ $message }}</span>
                    @enderror
                </div>
                <button type="submit" class="btn-submit">Update Subject</button>
            </form>
        </div>
    </div>
@endsection
