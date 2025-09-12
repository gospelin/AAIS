{{-- resources/views/admin/classes/students_by_class.blade.php --}}

@extends('admin.layouts.app')

@section('title', 'Students in {{ $class->name }}')

@section('description', 'Manage students in {{ $class->name }} for Aunty Anne\'s International School.')

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

        .btn-action {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            color: var(--text-primary);
            font-size: clamp(0.75rem, 2vw, 0.875rem);
            padding: var(--space-sm);
            border-radius: var(--radius-md);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-action:hover,
        .btn-action:focus-visible {
            background: var(--primary-green);
            border-color: var(--primary-green);
            color: var(--white);
        }

        .btn-promote:hover {
            background: var(--primary-green);
        }

        .btn-demote:hover {
            background: var(--warning);
        }

        .btn-delete:hover {
            background: var(--error);
        }

        .student-table {
            width: 100%;
            border-collapse: collapse;
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-xl);
            overflow: hidden;
            margin-bottom: var(--space-2xl);
        }

        .student-table th,
        .student-table td {
            padding: var(--space-md);
            font-size: clamp(0.875rem, 2vw, 0.9375rem);
            color: var(--text-primary);
            text-align: left;
        }

        .student-table th {
            background: var(--glass-bg);
            font-weight: 600;
        }

        .student-table tr {
            border-bottom: 1px solid var(--glass-border);
        }

        .student-table tr:last-child {
            border-bottom: none;
        }

        .student-table tbody tr:hover {
            background: rgba(33, 160, 85, 0.1);
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

        .badge {
            display: inline-block;
            padding: 0.25em 0.5em;
            border-radius: var(--radius-sm);
            font-size: 0.75rem;
            font-weight: 500;
        }

        .bg-success {
            background: var(--primary-green);
            color: var(--white);
        }

        .bg-danger {
            background: var(--error);
            color: var(--white);
        }

        .bg-warning {
            background: var(--warning);
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

            .student-table th,
            .student-table td {
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

        <!-- Filters Form -->
        <div class="form-container">
            <h2 class="form-header">
                @switch($action)
                    @case('view_students')
                        Students in {{ $class->name }}
                        @break
                    @case('promote')
                        Promote Students in {{ $class->name }}
                        @break
                    @case('demote')
                        Demote Students in {{ $class->name }}
                        @break
                    @case('delete_from_class')
                        Delete Students from {{ $class->name }}
                        @break
                    @case('manage_result')
                        Manage Results for {{ $class->name }}
                        @break
                    @case('generate_broadsheet')
                        Generate Broadsheet for {{ $class->name }}
                        @break
                    @case('download_broadsheet')
                        Download Broadsheet for {{ $class->name }}
                        @break
                    @default
                        Manage Students in {{ $class->name }}
                @endswitch
            </h2>
            <form method="GET" action="{{ route('admin.students_by_class', ['className' => urlencode($class->name), 'action' => $action]) }}" id="filterForm">
                @csrf
                <div class="form-section">
                    <h3 class="form-section-title">Filter Students</h3>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="enrollment_status" class="form-label">Enrollment Status</label>
                            <select name="enrollment_status" id="enrollment_status" class="form-control form-select">
                                <option value="active" {{ $enrollmentStatus == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ $enrollmentStatus == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="" {{ $enrollmentStatus == '' ? 'selected' : '' }}>All</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="fee_status" class="form-label">Fee Status</label>
                            <select name="fee_status" id="fee_status" class="form-control form-select">
                                <option value="paid" {{ $feeStatus == 'paid' ? 'selected' : '' }}>Paid</option>
                                <option value="unpaid" {{ $feeStatus == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                                <option value="" {{ $feeStatus == '' ? 'selected' : '' }}>All</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="approval_status" class="form-label">Approval Status</label>
                            <select name="approval_status" id="approval_status" class="form-control form-select">
                                <option value="approved" {{ $approvalStatus == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="pending" {{ $approvalStatus == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="" {{ $approvalStatus == '' ? 'selected' : '' }}>All</option>
                            </select>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn-submit">
                    <i class="bx bx-filter"></i> Apply Filters
                </button>
            </form>
        </div>

        <!-- Students List -->
        <div class="student-table">
            <table>
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Registration Number</th>
                        <th>Enrollment Status</th>
                        <th>Fee Status</th>
                        <th>Approval Status</th>
                        @if(in_array($action, ['promote', 'demote', 'delete_from_class']))
                            <th>Actions</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($studentsClasses as $student)
                        <tr>
                            <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                            <td>{{ $student->reg_no }}</td>
                            <td>
                                <span class="badge {{ $student->is_active ? 'bg-success' : 'bg-danger' }}">
                                    {{ $student->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $feePayment = $student->feePayments->where('session_id', $currentSession->id)->where('term', $currentTerm->value)->first();
                                @endphp
                                <span class="badge {{ $feePayment && $feePayment->has_paid_fee ? 'bg-success' : 'bg-warning' }}">
                                    {{ $feePayment && $feePayment->has_paid_fee ? 'Paid' : 'Unpaid' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $student->approved ? 'bg-success' : 'bg-warning' }}">
                                    {{ $student->approved ? 'Approved' : 'Pending' }}
                                </span>
                            </td>
                            @if(in_array($action, ['promote', 'demote', 'delete_from_class']))
                                <td>
                                    <div class="d-flex gap-2">
                                        @if($action == 'promote')
                                            <form method="POST" action="{{ route('admin.promote_student', ['className' => urlencode($class->name), 'studentId' => $student->id, 'action' => $action]) }}" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="btn-action btn-promote" title="Promote Student">
                                                    <i class="bx bx-arrow-up"></i>
                                                </button>
                                            </form>
                                        @endif
                                        @if($action == 'demote')
                                            <form method="POST" action="{{ route('admin.demote_student', ['className' => urlencode($class->name), 'studentId' => $student->id, 'action' => $action]) }}" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="btn-action btn-demote" title="Demote Student">
                                                    <i class="bx bx-arrow-down"></i>
                                                </button>
                                            </form>
                                        @endif
                                        @if($action == 'delete_from_class')
                                            <form method="POST" action="{{ route('admin.delete_student_class_record', ['className' => urlencode($class->name), 'studentId' => $student->id, 'action' => $action]) }}" style="display: inline;" onsubmit="return confirm('Are you sure you want to remove this student from the class?')">
                                                @csrf
                                                <button type="submit" class="btn-action btn-delete" title="Delete from Class">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ in_array($action, ['promote', 'demote', 'delete_from_class']) ? 6 : 5 }}" class="text-center text-[var(--text-secondary)]">No students found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($students->hasPages())
            <div class="pagination">
                {{ $students->appends(['enrollment_status' => $enrollmentStatus, 'fee_status' => $feeStatus, 'approval_status' => $approvalStatus])->links('vendor.pagination.custom') }}
            </div>
        @endif
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                // GSAP Animations
                gsap.from('.form-container', { opacity: 0, y: 20, duration: 0.6 });
                gsap.from('.form-group', { opacity: 0, y: 20, stagger: 0.1, duration: 0.6, delay: 0.2 });
                gsap.from('.student-table', { opacity: 0, y: 20, duration: 0.6, delay: 0.4 });
                gsap.from('.pagination', { opacity: 0, y: 20, duration: 0.6, delay: 0.6 });
            });
        </script>
    @endpush
@endsection
