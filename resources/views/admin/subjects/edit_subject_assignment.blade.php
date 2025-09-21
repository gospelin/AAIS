@extends('admin.layouts.app')

@section('title', 'Edit Subject Assignment')
@section('description', "Edit subject assignments for class {{ $class->name }} at Aunty Anne's International School.")

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

        .form-text {
            font-size: clamp(0.75rem, 1.5vw, 0.8125rem);
            color: var(--text-secondary);
        }

        .btn-submit, .btn-back {
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

        .btn-submit:hover, .btn-back:hover {
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

        <div class="form-section">
            <h3 class="form-header">Edit Subject Assignment for {{ $class->name }}</h3>
            <form action="{{ route('admin.subjects.edit_assignment', urlencode($class->name)) }}" method="POST" id="edit-subjects-form">
                @csrf
                <div class="form-group">
                    <label for="subject_ids" class="form-label">Select Subjects</label>
                    <select name="subject_ids[]" id="subject_ids" class="form-select" multiple>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ in_array($subject->id, $assignedSubjects) ? 'selected' : '' }}>
                                {{ $subject->name }} {{ $subject->section ? '(' . $subject->section . ')' : '' }}
                            </option>
                        @endforeach
                    </select>
                    <small class="form-text">Select subjects to assign to {{ $class->name }}. Leave empty to remove all assignments.</small>
                    @error('subject_ids')
                        <span class="text-[var(--error)]">{{ $message }}</span>
                    @enderror
                </div>
                <button type="submit" class="btn-submit">Update Assignments</button>
                <a href="{{ route('admin.subjects.assign') }}" class="btn-back">Back to Assign Subjects</a>
            </form>
        </div>

        <div class="loading-overlay" id="loadingOverlay">
            <div class="loading-spinner"></div>
        </div>
    </div>
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const editForm = document.getElementById('edit-subjects-form');
            const loadingOverlay = document.getElementById('loadingOverlay');

            function showLoading() {
                if (loadingOverlay) loadingOverlay.style.display = 'flex';
            }

            function hideLoading() {
                if (loadingOverlay) loadingOverlay.style.display = 'none';
            }

            if (editForm) {
                editForm.addEventListener('submit', (e) => {
                    e.preventDefault();
                    const formData = new FormData(editForm);

                    showLoading();
                    fetch(editForm.action, {
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
                                alert(data.message || 'Subjects updated successfully!');
                                window.location.href = '{{ route('admin.subjects.assign') }}';
                            } else {
                                alert(data.message || 'Error updating subjects.');
                            }
                        })
                        .catch(error => {
                            hideLoading();
                            console.error('Error:', error);
                            alert('Error updating subjects.');
                        });
                });
            }
        });
    </script>
@endpush
@endsection