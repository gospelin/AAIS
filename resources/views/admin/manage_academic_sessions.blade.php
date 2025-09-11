{{-- resources/views/admin/manage_academic_sessions.blade.php --}}

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

        .btn-preference {
            background: var(--primary-green);
            border: none;
            color: var(--white);
            font-size: clamp(0.875rem, 2.5vw, 1rem);
            font-weight: 600;
            padding: var(--space-sm) var(--space-md);
            border-radius: var(--radius-lg);
            cursor: pointer;
            transition: all 0.3s ease;
            width: auto;
        }

        .btn-preference:hover {
            background: var(--dark-green);
        }

        .required::after {
            content: ' *';
            color: var(--error);
        }

        .session-table {
            width: 100%;
            border-collapse: collapse;
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-xl);
            overflow: hidden;
            margin-bottom: var(--space-2xl);
        }

        .session-table th,
        .session-table td {
            padding: var(--space-md);
            font-size: clamp(0.875rem, 2vw, 0.9375rem);
            color: var(--text-primary);
            text-align: left;
        }

        .session-table th {
            background: var(--glass-bg);
            font-weight: 600;
        }

        .session-table tr {
            border-bottom: 1px solid var(--glass-border);
        }

        .session-table tr:last-child {
            border-bottom: none;
        }

        .session-table tbody tr:hover {
            background: rgba(33, 160, 85, 0.1);
        }

        .action-btn {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            color: var(--text-primary);
            font-size: clamp(0.75rem, 2vw, 0.875rem);
            padding: var(--space-sm);
            border-radius: var(--radius-md);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .action-btn:hover,
        .action-btn:focus-visible {
            background: var(--primary-green);
            border-color: var(--primary-green);
            color: var(--white);
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

            .session-table th,
            .session-table td {
                padding: var(--space-sm);
                font-size: clamp(0.75rem, 2vw, 0.875rem);
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

        <!-- Create/Edit Session Form -->
        <div class="form-container">
            <h2 class="form-header">{{ isset($editSession) ? 'Edit Academic Session' : 'Create New Academic Session' }}</h2>
            <form method="POST"
                action="{{ isset($editSession) ? route('admin.manage_academic_sessions.update', $editSession->id) : route('admin.manage_academic_sessions.store') }}"
                id="sessionForm">
                @csrf
                @if(isset($editSession))
                    @method('PUT')
                @endif

                <div class="form-section">
                    <h3 class="form-section-title">Session Details</h3>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="year" class="form-label required">Academic Year</label>
                            <input type="text" name="year" id="year"
                                class="form-control @error('year') is-invalid @enderror"
                                value="{{ old('year', isset($editSession) ? $editSession->year : '') }}"
                                placeholder="e.g., 2025/2026" required>
                            @error('year')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="is_current" class="form-label">Current Session</label>
                            <select name="is_current" id="is_current"
                                class="form-control form-select @error('is_current') is-invalid @enderror">
                                <option value="1" {{ old('is_current', isset($editSession) ? $editSession->is_current : 0) ? 'selected' : '' }}>Yes</option>
                                <option value="0" {{ old('is_current', isset($editSession) ? $editSession->is_current : 0) ? '' : 'selected' }}>No</option>
                            </select>
                            @error('is_current')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="current_term" class="form-label">Current Term</label>
                            <select name="current_term" id="current_term"
                                class="form-control form-select @error('current_term') is-invalid @enderror">
                                <option value="">Select Term</option>
                                @foreach($termChoices as $term)
                                    <option value="{{ $term->value }}" {{ old('current_term', isset($editSession) ? $editSession->current_term : '') == $term->value ? 'selected' : '' }}>{{ $term->label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('current_term')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="bx bx-save"></i> {{ isset($editSession) ? 'Update Session' : 'Create Session' }}
                </button>
            </form>
        </div>

        <!-- Set Current Session/Term Preference Form -->
        <div class="form-container">
            <h2 class="form-header">Set Current Session & Term</h2>
            <form method="POST" action="{{ route('admin.manage_academic_sessions') }}" id="preferenceForm">
                @csrf
                <div class="form-section">
                    <h3 class="form-section-title">Your Preference</h3>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="session_id" class="form-label required">Select Session</label>
                            <select name="session_id" id="session_id"
                                class="form-control form-select @error('session_id') is-invalid @enderror" required>
                                <option value="">Select Session</option>
                                @foreach($sessionChoices as $session)
                                    <option value="{{ $session->id }}" {{ $currentSession && $currentSession->id == $session->id ? 'selected' : '' }}>{{ $session->year }}</option>
                                @endforeach
                            </select>
                            @error('session_id')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="current_term_preference" class="form-label required">Current Term</label>
                            <select name="current_term" id="current_term_preference"
                                class="form-control form-select @error('current_term') is-invalid @enderror" required>
                                <option value="">Select Term</option>
                                @foreach($termChoices as $term)
                                    <option value="{{ $term->value }}" {{ $currentTerm && $currentTerm->value == $term->value ? 'selected' : '' }}>{{ $term->label }}</option>
                                @endforeach
                            </select>
                            @error('current_term')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn-preference">
                    <i class="bx bx-check"></i> Set Preference
                </button>
            </form>
        </div>

        <!-- Sessions List -->
        <div class="session-table">
            <table>
                <thead>
                    <tr>
                        <th>Academic Year</th>
                        <th>Current Session</th>
                        <th>Current Term</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sessionChoices as $session)
                        <tr>
                            <td>{{ $session->year }}</td>
                            <td>{{ $session->is_current ? 'Yes' : 'No' }}</td>
                            <td>{{ $session->current_term ?? 'N/A' }}</td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.manage_academic_sessions.edit', $session->id) }}"
                                        class="action-btn" title="Edit Session">
                                        <i class="bx bx-edit"></i>
                                    </a>
                                    <a href="{{ route('admin.manage_academic_sessions.delete', $session->id) }}"
                                        class="action-btn" title="Delete Session">
                                        <i class="bx bx-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-[var(--text-secondary)]">No academic sessions found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const sessionForm = document.getElementById('sessionForm');
                const preferenceForm = document.getElementById('preferenceForm');

                // Client-side validation for year format
                sessionForm.addEventListener('submit', (e) => {
                    const year = document.getElementById('year').value;
                    if (!year.match(/^\d{4}\/\d{4}$/)) {
                        e.preventDefault();
                        alert('Please enter a valid academic year in the format YYYY/YYYY (e.g., 2025/2026).');
                    }
                });

                // GSAP Animations
                gsap.from('.form-container', { opacity: 0, y: 20, stagger: 0.2, duration: 0.6 });
                gsap.from('.session-table', { opacity: 0, y: 20, duration: 0.6, delay: 0.4 });
            });
        </script>
    @endpush
@endsection
