@extends('admin.layouts.app')

@section('title', 'Assign Subjects to Classes')
@section('description', "Assign subjects to classes at Aunty Anne's International School.")

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

        .form-header,
        .section-header {
            font-family: var(--font-display);
            font-size: clamp(1.5rem, 3vw, 1.75rem);
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
            font-weight: 600;
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

        .form-text {
            font-size: clamp(0.75rem, 1.5vw, 0.8125rem);
            color: var(--text-secondary);
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

        .subject-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid var(--glass-border);
        }

        .subject-table th,
        .subject-table td {
            padding: var(--space-md);
            font-size: clamp(0.875rem, 2vw, 0.9375rem);
            color: var(--text-primary);
            text-align: left;
            border: 1px solid var(--glass-border);
        }

        .subject-table th {
            background: var(--glass-bg);
            font-weight: 600;
        }

        .subject-table tbody tr:hover {
            background: rgba(33, 160, 85, 0.1);
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
            background: var(--primary-green);
            border-color: var(--primary-green);
            color: var(--white);
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-sm);
        }

        .remove-btn {
            color: var(--error);
            background: none;
            border: none;
            padding: 0;
            font-size: clamp(0.75rem, 2vw, 0.875rem);
            cursor: pointer;
        }

        .remove-btn:hover {
            text-decoration: underline;
        }

        .list-unstyled {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .text-muted {
            color: var(--text-secondary);
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

        <!-- Assign Subjects Form -->
        <div class="form-section">
            <h3 class="section-header">Assign Subjects to Classes</h3>
            <form action="{{ route('admin.subjects.assign') }}" method="POST" id="assign-subjects-form" class="mb-4">
                @csrf
                <div class="form-group">
                    <label for="class_ids" class="form-label">Select Classes</label>
                    <select name="class_ids[]" id="class_ids" class="form-select" multiple required>
                        @foreach(\App\Models\Classes::orderBy('hierarchy')->get() as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}
                                {{ $class->section ? '(' . $class->section . ')' : '' }}
                            </option>
                        @endforeach
                    </select>
                    <small class="form-text">Select one or more classes to assign subjects to.</small>
                    @error('class_ids')
                        <span class="text-[var(--error)]">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="subject_ids" class="form-label">Select Subjects</label>
                    <select name="subject_ids[]" id="subject_ids" class="form-select" multiple required>
                        @foreach(\App\Models\Subject::where('deactivated', false)->orderBy('name')->get() as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->name }}
                                {{ $subject->section ? '(' . $subject->section . ')' : '' }}
                            </option>
                        @endforeach
                    </select>
                    <small class="form-text">Select one or more subjects to assign.</small>
                    @error('subject_ids')
                        <span class="text-[var(--error)]">{{ $message }}</span>
                    @enderror
                </div>
                <button type="submit" class="btn-submit">Assign Subjects</button>
            </form>
        </div>

        <!-- Assigned Subjects by Class -->
        <div class="subject-section">
            <h3 class="section-header">Assigned Subjects by Class</h3>
            <div class="loading-overlay" id="loadingOverlay">
                <div class="loading-spinner"></div>
            </div>
            @if($classesWithSubjects->isEmpty())
                <p class="text-muted">No classes or subjects found.</p>
            @else
                <div class="table-responsive">
                    <table class="subject-table">
                        <thead>
                            <tr>
                                <th>Class Name</th>
                                <th>Assigned Subjects</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($classesWithSubjects as $className => $subjects)
                                <tr>
                                    <td>{{ $className }}</td>
                                    <td>
                                        @if($subjects->isEmpty())
                                            <span class="text-muted">No subjects assigned</span>
                                        @else
                                            <ul class="list-unstyled mb-0">
                                                @foreach($subjects as $subject)
                                                    <li>
                                                        {{ $subject->name }} {{ $subject->section ? '(' . $subject->section . ')' : '' }}
                                                        <form action="{{ route('admin.subjects.remove') }}" method="POST" class="d-inline"
                                                            onsubmit="return confirm('Are you sure you want to remove {{ $subject->name }} from {{ $className }}?');">
                                                            @csrf
                                                            <input type="hidden" name="class_id" value="{{ $subject->pivot->class_id }}">
                                                            <input type="hidden" name="subject_id" value="{{ $subject->id }}">
                                                            <button type="submit" class="remove-btn">Remove</button>
                                                        </form>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.subjects.edit_assignment', urlencode($className)) }}"
                                            class="action-btn edit">Edit</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
            <div class="text-center mt-3">
                <a href="{{ route('admin.subjects.manage') }}" class="btn-back">Back to Subjects</a>
            </div>
        </div>
    </div>
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const assignForm = document.getElementById('assign-subjects-form');
                const loadingOverlay = document.getElementById('loadingOverlay');

                function showLoading() {
                    if (loadingOverlay) loadingOverlay.style.display = 'flex';
                }

                function hideLoading() {
                    if (loadingOverlay) loadingOverlay.style.display = 'none';
                }

                // Handle assign form submission
                if (assignForm) {
                    assignForm.addEventListener('submit', (e) => {
                        e.preventDefault();
                        const formData = new FormData(assignForm);

                        showLoading();
                        fetch(assignForm.action, {
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
                                    alert(data.message || 'Subjects assigned successfully!');
                                    location.reload();
                                } else {
                                    alert(data.message || 'Error assigning subjects.');
                                }
                            })
                            .catch(error => {
                                hideLoading();
                                console.error('Error:', error);
                                alert('Error assigning subjects.');
                            });
                    });
                }

                // Handle remove form submission
                document.querySelectorAll('.remove-btn').forEach(button => {
                    button.addEventListener('click', (e) => {
                        if (!confirm(button.closest('form').getAttribute('onsubmit').replace('return confirm(', '').replace(');', ''))) {
                            e.preventDefault();
                        } else {
                            const form = button.closest('form');
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
                                        alert(data.message || 'Subject removed successfully!');
                                        location.reload();
                                    } else {
                                        alert(data.message || 'Error removing subject.');
                                    }
                                })
                                .catch(error => {
                                    hideLoading();
                                    console.error('Error:', error);
                                    alert('Error removing subject.');
                                });
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection