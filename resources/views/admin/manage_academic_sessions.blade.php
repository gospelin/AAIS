@extends('admin.layouts.app')

@section('title', 'Manage Academic Sessions')

@section('description', 'Create, edit, and manage academic sessions for Aunty Anne\'s International School.')

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
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-green);
            box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.2);
        }

        .btn-primary {
            background: var(--gradient-primary);
            border: none;
            border-radius: var(--radius-md);
            padding: var(--space-sm) var(--space-lg);
            font-weight: 600;
            color: var(--text-light);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .table-container {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-xl);
            padding: var(--space-lg);
        }

        .table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .table th,
        .table td {
            padding: var(--space-sm) var(--space-md);
            text-align: left;
            border-bottom: 1px solid var(--glass-border);
        }

        .table th {
            background: var(--bg-secondary);
            font-weight: 600;
            color: var(--text-primary);
        }

        .table tr:last-child td {
            border-bottom: none;
        }

        .action-links a {
            margin-right: var(--space-sm);
            color: var(--primary-green);
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .action-links a:hover {
            color: var(--primary-green-dark);
        }

        .alert {
            padding: var(--space-md);
            border-radius: var(--radius-md);
            margin-bottom: var(--space-lg);
        }

        .alert-success {
            background: rgba(34, 197, 94, 0.1);
            border: 1px solid var(--primary-green);
            color: var(--text-primary);
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid var(--error-red);
            color: var(--text-primary);
        }
    </style>
@endpush

@section('content')
    <div class="content-container">
        <!-- Display Success/Error Messages -->
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

        <!-- Current Session and Term -->
        <div class="form-container">
            <h2 class="form-header">Current Academic Session</h2>
            <div class="form-section">
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Current Session</label>
                        <input type="text" class="form-control"
                            value="{{ $currentSession ? $currentSession->year : 'None set' }}" readonly>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Current Term</label>
                        <input type="text" class="form-control"
                            value="{{ $currentTerm ? $currentTerm->value : 'None set' }}" readonly>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create/Edit Session Form -->
        <div class="form-container">
            <h2 class="form-header">{{ isset($editSession) ? 'Edit Academic Session' : 'Create New Academic Session' }}</h2>
            <form
                action="{{ isset($editSession) ? route('admin.manage_academic_sessions.update', $editSession->id) : route('admin.manage_academic_sessions.store') }}"
                method="POST">
                @csrf
                @if (isset($editSession))
                    @method('PUT')
                @endif

                <div class="form-section">
                    <div class="form-section-title">Session Details</div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="year" class="form-label">Academic Year (e.g., 2025/2026)</label>
                            <input type="text" name="year" id="year" class="form-control"
                                value="{{ old('year', isset($editSession) ? $editSession->year : '') }}"
                                placeholder="2025/2026">
                            @error('year')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group text-center">
                    <button type="submit"
                        class="btn-primary">{{ isset($editSession) ? 'Update Session' : 'Create Session' }}</button>
                </div>
            </form>
        </div>

        <!-- Sessions List -->
        <div class="table-container">
            <h2 class="form-header">Existing Academic Sessions</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>Year</th>
                        <th>Status</th>
                        <th>Current Term</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sessionChoices as $session)
                        <tr>
                            <td>{{ $session->year }}</td>
                            <td>{{ $session->is_current ? 'Current' : 'Not Current' }}</td>
                            <td>{{ $session->current_term ?? 'N/A' }}</td>
                            <td class="action-links">
                                <a href="{{ route('admin.manage_academic_sessions.edit', $session->id) }}">Edit</a>
                                <a href="{{ route('admin.manage_academic_sessions.delete', $session->id) }}">Delete</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
