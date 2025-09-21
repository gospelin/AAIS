@extends('admin.layouts.app')

@section('title', 'Manage Subjects')
@section('description', "Manage subjects at Aunty Anne's International School.")

@push('styles')
    <style>
        .content-container {
            max-width: 90rem;
            margin: 0 auto;
            padding: var(--space-lg) var(--space-md);
        }

        .form-section,
        .subject-section {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-xl);
            padding: var(--space-xl);
            margin-bottom: var(--space-2xl);
            position: relative;
        }

        .form-section::before,
        .subject-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gradient-primary);
        }

        .section-header {
            font-family: var(--font-display);
            font-size: clamp(1.5rem, 3vw, 1.75rem);
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: var(--space-md);
        }

        .form-header {
            font-family: var(--font-display);
            font-size: clamp(1.25rem, 3vw, 1.5rem);
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: var(--space-md);
            text-align: center;
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

        .form-text {
            font-size: clamp(0.75rem, 1.5vw, 0.8125rem);
            color: var(--text-secondary);
        }

        .btn-submit {
            background: var(--gradient-primary);
            border: none;
            color: var(--white);
            padding: var(--space-md) var(--space-lg);
            border-radius: var(--radius-md);
            cursor: pointer;
            font-size: clamp(0.875rem, 2vw, 0.9375rem);
            width: 100%;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .list-group {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .list-group-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-md);
            padding: var(--space-md);
            margin-bottom: var(--space-sm);
            font-size: clamp(0.875rem, 2vw, 0.9375rem);
            color: var(--text-primary);
        }

        .action-buttons {
            display: flex;
            gap: var(--space-sm);
        }

        .action-btn {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            color: var(--text-primary);
            font-size: clamp(0.75rem, 2vw, 0.875rem);
            padding: var(--space-sm) var(--space-md);
            border-radius: var(--radius-md);
            cursor: pointer;
        }

        .action-btn.edit {
            background: var(--warning);
            border-color: var(--warning);
            color: var(--white);
        }

        .action-btn.deactivate,
        .action-btn.delete {
            background: var(--error);
            border-color: var(--error);
            color: var(--white);
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-sm);
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

        .alert-warning {
            background: rgba(255, 193, 7, 0.1);
            border: 1px solid var(--warning);
            color: var(--text-primary);
        }

        .alert-danger {
            background: rgba(220, 53, 69, 0.1);
            border: 1px solid var(--error);
            color: var(--text-primary);
        }

        .loading-overlay {
            position: fixed;
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
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
@endpush

@section('content')
    <div class="content-container">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('warning'))
            <div class="alert alert-warning">{{ session('warning') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <!-- Add Subjects Form -->
        <div class="form-section">
            <h3 class="form-header">Add Subjects</h3>
            <form action="{{ route('admin.subjects.manage') }}" method="POST" id="create-subject-form">
                @csrf
                <div class="form-group">
                    <label for="names" class="form-label">Name (comma-separated)</label>
                    <input type="text" name="names" id="names" class="form-input" value="{{ old('names') }}"
                        placeholder="Enter multiple subjects separated by commas (e.g., Math, English, Science)" required>
                    <small class="form-text">Enter multiple subjects separated by commas (e.g., Math, English,
                        Science).</small>
                    @error('names')
                        <span class="text-[var(--error)]">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="section" class="form-label">Section</label>
                    <select name="section[]" id="section" class="form-select" multiple required>
                        @foreach(\App\Models\Subject::distinct('section')->pluck('section')->sort() as $section)
                            <option value="{{ $section }}" {{ in_array($section, old('section', [])) ? 'selected' : '' }}>
                                {{ $section }}
                            </option>
                        @endforeach
                    </select>
                    <small class="form-text">Select one or more sections for the subjects.</small>
                    @error('section')
                        <span class="text-[var(--error)]">{{ $message }}</span>
                    @enderror
                </div>
                <button type="submit" class="btn-submit">Add Subjects</button>
            </form>
        </div>

        <!-- Subjects List -->
        <div class="subject-section">
            <div class="loading-overlay" id="loadingOverlay">
                <div class="loading-spinner"></div>
            </div>
            @php
                $groupedSubjects = $subjects->groupBy('section')->sortKeys();
            @endphp
            @if($groupedSubjects->isEmpty())
                <p class="text-[var(--text-secondary)]">No subjects found.</p>
            @else
                @foreach($groupedSubjects as $section => $sectionSubjects)
                    <h2 class="section-header">{{ $section ?: 'General' }} Subjects</h2>
                    <ul class="list-group">
                        @foreach($sectionSubjects as $subject)
                            <li class="list-group-item" data-subject-id="{{ $subject->id }}">
                                {{ $subject->name }} {{ $subject->deactivated ? '(Deactivated)' : '' }}
                                <span class="action-buttons">
                                    <a href="{{ route('admin.subjects.edit', $subject->id) }}" class="action-btn edit">
                                        <i class="bx bx-edit"></i> Edit
                                    </a>
                                    @if(!$subject->deactivated)
                                        <form action="{{ route('admin.subjects.manage') }}" method="POST" style="display:inline;"
                                            class="deactivate-subject-form">
                                            @csrf
                                            <input type="hidden" name="deactivate_subject_id" value="{{ $subject->id }}">
                                            <button type="submit" class="action-btn deactivate" data-action="deactivate">
                                                <i class="bx bx-block"></i> Deactivate
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{ route('admin.subjects.manage') }}" method="POST" style="display:inline;"
                                        class="delete-subject-form">
                                        @csrf
                                        <input type="hidden" name="delete_subject_id" value="{{ $subject->id }}">
                                        <button type="submit" class="action-btn delete" data-action="delete">
                                            <i class="bx bx-trash"></i> Delete
                                        </button>
                                    </form>
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @endforeach
            @endif
        </div>
    </div>
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const form = document.getElementById('create-subject-form');
                const loadingOverlay = document.getElementById('loadingOverlay');

                function showLoading() {
                    if (loadingOverlay) loadingOverlay.style.display = 'flex';
                }

                function hideLoading() {
                    if (loadingOverlay) loadingOverlay.style.display = 'none';
                }

                // Form submission for creating subjects
                if (form) {
                    form.addEventListener('submit', (e) => {
                        e.preventDefault();
                        const formData = new FormData(form);

                        showLoading();
                        fetch(form.action, {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: formData
                        })
                            .then(response => response.json())
                            .then(data => {
                                hideLoading();
                                if (data.success) {
                                    location.reload(); // Reload to update grouped lists
                                    alert(data.message);
                                } else {
                                    alert(data.message || 'Error creating subjects.');
                                }
                            })
                            .catch(error => {
                                hideLoading();
                                console.error('Error:', error);
                                alert('Error creating subjects.');
                            });
                    });
                }

                // Handle deactivate and delete actions
                document.addEventListener('click', (e) => {
                    const deactivateButton = e.target.closest('form button[data-action="deactivate"]');
                    const deleteButton = e.target.closest('form button[data-action="delete"]');

                    if (deactivateButton) {
                        e.preventDefault();
                        const form = deactivateButton.closest('form');
                        if (confirm('Are you sure you want to deactivate this subject?')) {
                            showLoading();
                            fetch(form.action, {
                                method: 'POST',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json'
                                },
                                body: new FormData(form)
                            })
                                .then(response => response.json())
                                .then(data => {
                                    hideLoading();
                                    if (data.success) {
                                        location.reload(); // Reload to update status
                                        alert(data.message);
                                    } else {
                                        alert(data.message || 'Error deactivating subject.');
                                    }
                                })
                                .catch(error => {
                                    hideLoading();
                                    console.error('Error:', error);
                                    alert('Error deactivating subject.');
                                });
                        }
                    }

                    if (deleteButton) {
                        e.preventDefault();
                        const form = deleteButton.closest('form');
                        if (confirm('Are you sure you want to delete this subject?')) {
                            showLoading();
                            fetch(form.action, {
                                method: 'POST',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json'
                                },
                                body: new FormData(form)
                            })
                                .then(response => response.json())
                                .then(data => {
                                    hideLoading();
                                    if (data.success) {
                                        location.reload(); // Reload to remove the subject
                                        alert(data.message);
                                    } else {
                                        alert(data.message || 'Error deleting subject.');
                                    }
                                })
                                .catch(error => {
                                    hideLoading();
                                    console.error('Error:', error);
                                    alert('Error deleting subject.');
                                });
                        }
                    }
                });
            });
        </script>
    @endpush
@endsection
