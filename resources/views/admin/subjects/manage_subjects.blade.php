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
        }

        .action-btn {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            color: var(--text-primary);
            font-size: clamp(0.75rem, 2vw, 0.875rem);
            padding: var(--space-sm);
            border-radius: var(--radius-md);
            cursor: pointer;
        }

        .action-btn:hover {
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
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <!-- Create Subject Form -->
        <div class="form-section">
            <h3 class="form-header">Add New Subject</h3>
            <form action="{{ route('admin.subjects.manage') }}" method="POST" id="create-subject-form">
                @csrf
                <div class="form-group">
                    <label for="name" class="form-label">Subject Name</label>
                    <input type="text" name="name" id="name" class="form-input" value="{{ old('name') }}" required>
                    @error('name')
                        <span class="text-[var(--error)]">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="section" class="form-label">Section (Optional)</label>
                    <input type="text" name="section" id="section" class="form-input" value="{{ old('section') }}">
                    @error('section')
                        <span class="text-[var(--error)]">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="description" class="form-label">Description (Optional)</label>
                    <textarea name="description" id="description" class="form-input">{{ old('description') }}</textarea>
                    @error('description')
                        <span class="text-[var(--error)]">{{ $message }}</span>
                    @enderror
                </div>
                <button type="submit" class="btn-submit">Create Subject</button>
            </form>
        </div>

        <!-- Subjects List -->
        <div class="subject-section">
            <div class="loading-overlay" id="loadingOverlay">
                <div class="loading-spinner"></div>
            </div>
            @if($subjects->isEmpty())
                <table class="subject-table">
                    <tbody>
                        <tr>
                            <td colspan="5" class="text-center text-[var(--text-secondary)]">No subjects found.</td>
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
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="subjects-table-body">
                        @foreach($subjects as $subject)
                            @include('admin.subjects.subject_row', ['subject' => $subject])
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-4">
                    {{ $subjects->links('vendor.pagination.custom') }}
                </div>
            @endif
        </div>
    </div>
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const form = document.getElementById('create-subject-form');
                const subjectsTableBody = document.getElementById('subjects-table-body');
                const loadingOverlay = document.getElementById('loadingOverlay');

                function showLoading() {
                    if (loadingOverlay) loadingOverlay.style.display = 'flex';
                }

                function hideLoading() {
                    if (loadingOverlay) loadingOverlay.style.display = 'none';
                }

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
                                    subjectsTableBody.insertAdjacentHTML('afterbegin', data.html);
                                    form.reset();
                                    alert(data.message);
                                    if (subjectsTableBody.querySelector('tr td.text-center')) {
                                        subjectsTableBody.innerHTML = data.html;
                                    }
                                } else {
                                    alert(data.message || 'Error creating subject.');
                                }
                            })
                            .catch(error => {
                                hideLoading();
                                console.error('Error:', error);
                                alert('Error creating subject.');
                            });
                    });
                }

                subjectsTableBody.addEventListener('click', (e) => {
                    const deleteButton = e.target.closest('form button[data-action="delete"]');
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
                                        deleteButton.closest('tr').remove();
                                        alert(data.message || 'Subject deleted successfully!');
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
