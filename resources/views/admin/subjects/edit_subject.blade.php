@extends('admin.layouts.app')

@section('title', 'Edit Subject')
@section('description', "Edit subject details at Aunty Anne's International School.")

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

        .form-input,
        .form-select {
            width: 100%;
            background: var(--bg-secondary);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-md);
            padding: var(--space-sm) var(--space-md);
            color: var(--text-primary);
            font-size: clamp(0.875rem, 2vw, 0.9375rem);
        }

        .form-input:focus,
        .form-select:focus {
            outline: none;
            border-color: var(--primary-green);
            box-shadow: 0 0 0 3px rgba(33, 160, 85, 0.2);
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

        .btn-submit,
        .btn-back {
            background: var(--gradient-primary);
            border: none;
            color: var(--white);
            padding: var(--space-md) var(--space-lg);
            border-radius: var(--radius-md);
            cursor: pointer;
            font-size: clamp(0.875rem, 2vw, 0.9375rem);
        }

        .btn-back {
            background: var(--bg-secondary);
            border: 1px solid var(--glass-border);
        }

        .btn-submit:hover,
        .btn-back:hover {
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
                    <label for="section" class="form-label">Section</label>
                    <select name="section[]" id="section" class="form-select" multiple required>
                        @php
                            $currentSections = \App\Models\Subject::where('name', $subject->name)->pluck('section')->toArray();
                        @endphp
                        @foreach(\App\Models\Subject::distinct('section')->pluck('section')->sort() as $section)
                            <option value="{{ $section }}" {{ in_array($section, old('section', $currentSections)) ? 'selected' : '' }}>
                                {{ $section }}</option>
                        @endforeach
                    </select>
                    <small class="form-text">Select one or more sections for the subject.</small>
                    @error('section')
                        <span class="text-[var(--error)]">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="deactivated" class="form-label">Status</label>
                    <select name="deactivated" id="deactivated" class="form-select">
                        <option value="0" {{ !$subject->deactivated ? 'selected' : '' }}>Active</option>
                        <option value="1" {{ $subject->deactivated ? 'selected' : '' }}>Deactivated</option>
                    </select>
                </div>
                <button type="submit" class="btn-submit">Update Subject</button>
                <a href="{{ route('admin.subjects.manage') }}" class="btn-back">Back to Subjects</a>
            </form>
        </div>
    </div>
@endsection