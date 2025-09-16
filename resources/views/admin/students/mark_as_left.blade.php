@extends('admin.layouts.app')

@section('title', 'Mark Student as Left')

@section('description', 'Mark a student as having left Aunty Anne\'s International School.')

@push('styles')
    <style>
        .form-container {
            max-width: 600px;
            margin: 2rem auto;
            padding: 2rem;
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-xl);
        }

        .form-header {
            font-family: var(--font-display);
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 1.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            font-size: 0.9375rem;
            font-weight: 500;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .form-select,
        .form-input {
            width: 100%;
            background: var(--bg-secondary);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-md);
            padding: 0.75rem;
            color: var(--text-primary);
            font-size: 0.9375rem;
        }

        .form-select:focus,
        .form-input:focus {
            outline: none;
            border-color: var(--primary-green);
            box-shadow: 0 0 0 3px rgba(33, 160, 85, 0.2);
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: var(--radius-md);
            font-size: 0.9375rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background: var(--primary-green);
            color: var(--white);
            border: none;
        }

        .btn-primary:hover {
            background: darken(var(--primary-green), 10%);
        }

        .btn-secondary {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            color: var(--text-primary);
        }

        .btn-secondary:hover {
            background: var(--primary-green);
            color: var(--white);
        }

        .alert {
            padding: 1rem;
            border-radius: var(--radius-md);
            margin-bottom: 1.5rem;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
        }
    </style>
@endpush

@section('content')
    <div class="form-container">
        <h2 class="form-header">Mark {{ $student->full_name }} ({{ $student->reg_no }}) as Left</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.student_mark_as_left', $student->id) }}" method="POST"
            onsubmit="return confirm('Are you sure you want to mark {{ $student->full_name }} as having left the school in the selected session and term?');">
            @csrf
            @method('POST')

            <div class="form-group">
                <label for="session_id" class="form-label">Academic Session</label>
                <select name="session_id" id="session_id" class="form-select" required>
                    @if($sessions->isEmpty())
                        <option value="" disabled selected>No sessions available</option>
                    @else
                        @foreach($sessions as $session)
                            <option value="{{ $session->id }}" {{ $session->id == $currentSession->id ? 'selected' : '' }}>
                                {{ $session->year }}
                            </option>
                        @endforeach
                    @endif
                </select>
                @error('session_id')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="term" class="form-label">Term</label>
                <select name="term" id="term" class="form-select" required>
                    @foreach($termChoices as $term)
                        <option value="{{ $term->value }}" {{ $term->value === $currentTerm->value ? 'selected' : '' }}>
                            {{ $term->label }}
                        </option>
                    @endforeach
                </select>
                @error('term')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary" {{ $sessions->isEmpty() ? 'disabled' : '' }}>
                    <i class="bx bx-exit"></i> Mark as Left
                </button>
                <a href="{{ route('admin.students', ['action' => 'view_students']) }}" class="btn btn-secondary">
                    <i class="bx bx-arrow-back"></i> Cancel
                </a>
            </div>
        </form>
    </div>
@endsection
