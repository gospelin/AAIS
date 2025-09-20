@extends('admin.layouts.app')

@section('title', 'Edit Subject Assignments for {{ $class->name }}')
@section('description', 'Assign or remove subjects for {{ $class->name }} at Aunty Anne\'s International School.')

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

        .form-select {
            width: 100%;
            background: var(--bg-secondary);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-md);
            padding: var(--space-sm) var(--space-md);
            color: var(--text-primary);
            font-size: clamp(0.875rem, 2vw, 0.9375rem);
            transition: all 0.2s ease;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
            background-position: right var(--space-md) center;
            background-repeat: no-repeat;
            background-size: 1.5em 1.5em;
            padding-right: 2.5rem;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
        }

        .form-select:focus {
            outline: none;
            border-color: var(--primary-green);
            box-shadow: 0 0 0 3px rgba(33, 160, 85, 0.2);
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

        .subject-section {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-xl);
            overflow: hidden;
            margin-bottom: var(--space-2xl);
            position: relative;
        }

        .subject-table {
            width: 100%;
            border-collapse: collapse;
        }

        .subject-table th,
        .subject-table td {
            padding: var(--space-md);
            font-size: clamp(0.875rem, 2vw, 0.9375rem);
            color: var(--text-primary);
            text-align: left;
        }

        .subject-table th {
            background: var(--glass-bg);
            font-weight: 600;
        }

        .subject-table tr {
            border-bottom: 1px solid var(--glass-border);
        }

        .subject-table tr:last-child {
            border-bottom: none;
        }

        .subject-table tbody tr:hover {
            background: rgba(33, 160, 85, 0.1);
        }

        .action-buttons {
            display: flex;
            gap: var(--space-sm);
            flex-wrap: wrap;
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

        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .loading-spinner {
            border: 4px solid var(--white);
            border-top: 4px solid var(--primary-green);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @media (max-width: 768px) {
            .content-container {
                padding: var(--space-md);
            }

            .subject-table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }

            .action-buttons {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
@endpush

@section('content')
    <div class="content-container">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{!! session('error') !!}</div>
        @endif

        <div class="form-section">
            <h3 class="form-header">Assign Subjects to {{ $class->name }} {{ $class->section ? ' - ' . $class->section : '' }}</h3>
            <form action="{{ route('admin.subjects.edit_assignment', urlencode($class->name)) }}" method="POST">
                @csrf
                @method('POST')
                <div class="form-group">
                    <label for="subject_ids" class="form-label">Select Subjects</label>
                    <select name="subject_ids[]" id="subject_ids" class="form-select" multiple>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ in_array($subject->id, $assignedSubjects) ? 'selected' : '' }}>
                                {{ $subject->name }} {{ $subject->section ? '(' . $subject->section . ')' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('subject_ids')
                        <span class="text-[var(--error)]">{{ $message }}</span>
                    @enderror
                </div>
                <button type="submit" class="btn-submit">Update Assignments</button>
            </form>
        </div>

        <div class="subject-section">
            <div class="loading-overlay" id="loadingOverlay">
                <div class="loading-spinner"></div>
            </div>
            @if($class->subjects->isEmpty())
                <table class="subject-table">
                    <tbody>
                        <tr>
                            <td colspan="3" class="text-center text-[var(--text-secondary)]">No subjects assigned to this class.</td>
                        </tr>
                    </tbody>
                </table>
            @else
                <table class="subject-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Section</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="subjects-table-body">
                        @foreach($class->subjects as $subject)
                            <tr>
                                <td>{{ $subject->name }}</td>
                                <td>{{ $subject->section ?? 'N/A' }}</td>
                                <td>{{ $subject->description ?? 'N/A' }}</td>
                                <td>
                                    <div class="action-buttons">
                                        <form action="{{ route('admin.subjects.remove') }}" method="POST"
                                              style="display:inline;"
                                              onsubmit="return confirm('Are you sure you want to remove this subject from the class?');">
                                            @csrf
                                            <input type="hidden" name="subject_id" value="{{ $subject->id }}">
                                            <input type="hidden" name="class_id" value="{{ $class->id }}">
                                            <button type="submit" class="btn btn-sm btn-danger action-btn">
                                                <i class="bx bx-trash"></i> Remove
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @foreach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const subjectSection = document.querySelector('.subject-section');
            const loadingOverlay = document.getElementById('loadingOverlay');

            function showLoading() {
                if (loadingOverlay) {
                    loadingOverlay.style.display = 'flex';
                }
            }

            function hideLoading() {
                if (loadingOverlay) {
                    loadingOverlay.style.display = 'none';
                }
            }

            if (subjectSection) {
                subjectSection.addEventListener('click', (e) => {
                    const removeButton = e.target.closest('form button');
                    if (removeButton) {
                        e.preventDefault();
                        const form = removeButton.closest('form');
                        const url = form.action;
                        const formData = new FormData(form);

                        if (confirm('Are you sure you want to remove this subject from the class?')) {
                            showLoading();
                            fetch(url, {
                                method: 'POST',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json'
                                },
                                body: formData
                            })
                                .then(response => {
                                    if (!response.ok) {
                                        return response.text().then(text => {
                                            throw new Error(`HTTP error! Status: ${response.status}, Response: ${text.substring(0, 500)}...`);
                                        });
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    if (data.success) {
                                        const row = removeButton.closest('tr');
                                        row.remove();
                                        if (document.querySelectorAll('#subjects-table-body tr').length === 0) {
                                            document.getElementById('subjects-table-body').innerHTML = `
                                                <tr>
                                                    <td colspan="3" class="text-center text-[var(--text-secondary)]">No subjects assigned to this class.</td>
                                                </tr>`;
                                        }
                                        alert(data.message || 'Subject removed successfully!');
                                    } else {
                                        alert(data.message || 'Error removing subject.');
                                    }
                                    hideLoading();
                                })
                                .catch(error => {
                                    console.error('Error removing subject:', error);
                                    alert('Error removing subject: ' + error.message);
                                    hideLoading();
                                });
                        }
                    }
                });
            }
        });
    </script>
@endpush
@endsection
