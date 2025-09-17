{{-- resources/views/admin/classes/manage_classes.blade.php --}}

@extends('admin.layouts.app')

@section('title', 'Manage Classes')

@section('description', 'Create, edit, and manage classes for Aunty Anne\'s International School.')

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

        .required::after {
            content: ' *';
            color: var(--error);
        }

        .class-table {
            width: 100%;
            border-collapse: collapse;
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-xl);
            overflow: hidden;
            margin-bottom: var(--space-2xl);
        }

        .class-table th,
        .class-table td {
            padding: var(--space-md);
            font-size: clamp(0.875rem, 2vw, 0.9375rem);
            color: var(--text-primary);
            text-align: left;
        }

        .class-table th {
            background: var(--glass-bg);
            font-weight: 600;
        }

        .class-table tr {
            border-bottom: 1px solid var(--glass-border);
        }

        .class-table tr:last-child {
            border-bottom: none;
        }

        .class-table tbody tr:hover {
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

        .pagination {
            display: flex;
            justify-content: center;
            gap: var(--space-md);
            margin-top: var(--space-lg);
        }

        .pagination a, .pagination span {
            padding: var(--space-sm) var(--space-md);
            border-radius: var(--radius-md);
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            color: var(--text-primary);
            font-size: clamp(0.875rem, 2vw, 0.9375rem);
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .pagination a:hover, .pagination span.current {
            background: var(--primary-green);
            border-color: var(--primary-green);
            color: var(--white);
        }

        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
                gap: var(--space-sm);
            }

            .form-container {
                padding: var(--space-xl);
            }

            .class-table th,
            .class-table td {
                padding: var(--space-sm);
                font-size: clamp(0.75rem, 2vw, 0.875rem);
            }

            .pagination {
                flex-wrap: wrap;
                gap: var(--space-sm);
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

        <!-- Create Class Form -->
        <div class="form-container">
            <h2 class="form-header">Create New Class</h2>
            <form method="POST" action="{{ route('admin.classes.store') }}" id="classForm">
                @csrf
                <div class="form-section">
                    <h3 class="form-section-title">Class Details</h3>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="name" class="form-label required">Class Name</label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name') }}" placeholder="e.g., Primary 1" required>
                            @error('name')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="section" class="form-label">Section (Optional)</label>
                            <input type="text" name="section" id="section" class="form-control @error('section') is-invalid @enderror" 
                                   value="{{ old('section') }}" placeholder="e.g., A">
                            @error('section')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="hierarchy" class="form-label required">Hierarchy</label>
                            <input type="number" name="hierarchy" id="hierarchy" class="form-control @error('hierarchy') is-invalid @enderror" 
                                   value="{{ old('hierarchy') }}" placeholder="e.g., 1" required>
                            @error('hierarchy')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn-submit">
                    <i class="bx bx-save"></i> Create Class
                </button>
            </form>
        </div>

        <!-- Classes List -->
        <div class="class-table">
            <table>
                <thead>
                    <tr>
                        <th>Class Name</th>
                        <th>Section</th>
                        <th>Hierarchy</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($classes as $class)
                        <tr>
                            <td>{{ $class->name }}</td>
                            <td>{{ $class->section ?? 'N/A' }}</td>
                            <td>{{ $class->hierarchy }}</td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.classes.edit', $class->id) }}" class="action-btn" title="Edit Class">
                                        <i class="bx bx-edit"></i>
                                    </a>
                                    <a href="{{ route('admin.classes.delete', $class->id) }}" class="action-btn" title="Delete Class">
                                        <i class="bx bx-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-[var(--text-secondary)]">No classes found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if (is_object($classes) && method_exists($classes, 'links'))
            <div class="pagination">
                {{ $classes->links('vendor.pagination.bootstrap-5') }}
            </div>
        @endif
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const form = document.getElementById('classForm');

                form.addEventListener('submit', (e) => {
                    const hierarchy = document.getElementById('hierarchy').value;
                    if (hierarchy < 1) {
                        e.preventDefault();
                        alert('Hierarchy must be a positive integer.');
                    }
                });

                // GSAP Animations
                gsap.from('.form-container', { opacity: 0, y: 20, duration: 0.6 });
                gsap.from('.form-group', { opacity: 0, y: 20, stagger: 0.1, duration: 0.6, delay: 0.2 });
                gsap.from('.class-table', { opacity: 0, y: 20, duration: 0.6, delay: 0.4 });
                gsap.from('.pagination', { opacity: 0, y: 20, duration: 0.6, delay: 0.6 });
            });
        </script>
    @endpush
@endsection
