{{-- resources/views/admin/students/view_students.blade.php --}}

@extends('admin.layouts.app')

@section('title', 'View Students')

@section('description', 'View and manage students at Aunty Anne\'s International School.')

@push('styles')
    <style>
        .content-container {
            max-width: 90rem;
            margin: 0 auto;
            padding: var(--space-lg) var(--space-md);
        }

        .filter-section {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-xl);
            padding: var(--space-xl);
            margin-bottom: var(--space-2xl);
        }

        .filter-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gradient-primary);
        }

        .filter-header {
            font-family: var(--font-display);
            font-size: clamp(1.25rem, 3vw, 1.5rem);
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: var(--space-md);
        }

        .filter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
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

        .student-section {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-xl);
            overflow: hidden;
            margin-bottom: var(--space-2xl);
        }

        .student-section-header {
            font-family: var(--font-display);
            font-size: clamp(1.25rem, 3vw, 1.5rem);
            font-weight: 600;
            color: var(--text-primary);
            padding: var(--space-md) var(--space-lg);
            border-bottom: 1px solid var(--glass-border);
        }

        .student-table {
            width: 100%;
            border-collapse: collapse;
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

        .pagination-container {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: var(--space-lg);
        }

        .pagination-link {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            color: var(--text-primary);
            padding: var(--space-sm) var(--space-md);
            margin: 0 var(--space-xs);
            border-radius: var(--radius-md);
            text-decoration: none;
            font-size: clamp(0.875rem, 2vw, 0.9375rem);
            transition: all 0.2s ease;
        }

        .pagination-link:hover,
        .pagination-link.active {
            background: var(--primary-green);
            border-color: var(--primary-green);
            color: var(--white);
        }

        .pagination-link.disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        @media (max-width: 768px) {
            .filter-grid {
                grid-template-columns: 1fr;
            }

            .student-table th,
            .student-table td {
                padding: var(--space-sm);
                font-size: clamp(0.75rem, 2vw, 0.875rem);
            }

            .content-container {
                padding: var(--space-md);
            }
        }
    </style>
@endpush

@section('content')
    <div class="content-container">
        <!-- Filter Section -->
        <div class="filter-section">
            <h3 class="filter-header">Filter Students</h3>
            <form id="filterForm" action="{{ route('admin.students', ['action' => $action]) }}" method="GET">
                <div class="filter-grid">
                    <div class="form-group">
                        <label for="enrollment_status" class="form-label">Enrollment Status</label>
                        <select name="enrollment_status" id="enrollment_status" class="form-select">
                            <option value="active" {{ $enrollmentStatus == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ $enrollmentStatus == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="fee_status" class="form-label">Fee Status</label>
                        <select name="fee_status" id="fee_status" class="form-select">
                            <option value="" {{ $feeStatus == '' ? 'selected' : '' }}>All</option>
                            <option value="paid" {{ $feeStatus == 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="unpaid" {{ $feeStatus == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="approval_status" class="form-label">Approval Status</label>
                        <select name="approval_status" id="approval_status" class="form-select">
                            <option value="" {{ $approvalStatus == '' ? 'selected' : '' }}>All</option>
                            <option value="approved" {{ $approvalStatus == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="unapproved" {{ $approvalStatus == 'unapproved' ? 'selected' : '' }}>Unapproved</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="term" class="form-label">Term</label>
                        <select name="term" id="term" class="form-select">
                            @foreach(\App\Enums\TermEnum::cases() as $term)
                                <option value="{{ $term->value }}" {{ $term->value == request('term', $currentTerm->value) ? 'selected' : '' }}>{{ $term->value }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </form>
        </div>

        <!-- Students List -->
        @foreach($studentsClasses as $className => $students)
            <div class="student-section">
                <h3 class="student-section-header">{{ $className }}</h3>
                <table class="student-table">
                    <thead>
                        <tr>
                            <th>Registration No</th>
                            <th>Name</th>
                            <th>Gender</th>
                            <th>Fee Status</th>
                            <th>Approval Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                            <tr>
                                <td>{{ $student->reg_no }}</td>
                                <td>{{ $student->full_name }}</td>
                                <td>{{ ucfirst($student->gender) }}</td>
                                <td>
                                    @php
                                        $feePayment = $student->feePayments->where('session_id', $currentSession->id)->where('term', request('term', $currentTerm->value))->first();
                                    @endphp
                                    <span class="{{ $feePayment && $feePayment->has_paid_fee ? 'text-success' : 'text-error' }}">
                                        {{ $feePayment && $feePayment->has_paid_fee ? 'Paid' : 'Unpaid' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="{{ $student->approved ? 'text-success' : 'text-error' }}">
                                        {{ $student->approved ? 'Approved' : 'Unapproved' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('admin.students', ['action' => 'edit', 'student' => $student->id]) }}" class="action-btn" title="Edit Student">
                                            <i class="bx bx-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.students', ['action' => 'delete_from_school', 'student' => $student->id]) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this student?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="action-btn" title="Delete Student">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        </form>
                                        @if(!$student->approved)
                                            <form action="{{ route('admin.approve_student', ['studentId' => $student->id]) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="action-btn" title="Approve Student">
                                                    <i class="bx bx-check-circle"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-[var(--text-secondary)]">No students found in this class.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endforeach

        <!-- Pagination -->
        <div class="pagination-container">
            {{ $paginatedStudents->links('vendor.pagination.custom') }}
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const filterForm = document.getElementById('filterForm');
                const filterSelects = filterForm.querySelectorAll('select');

                filterSelects.forEach(select => {
                    select.addEventListener('change', () => {
                        filterForm.submit();
                    });
                });

                // GSAP Animations
                gsap.from('.filter-section', { opacity: 0, y: 20, duration: 0.6 });
                gsap.from('.student-section', { opacity: 0, y: 20, stagger: 0.2, duration: 0.6, delay: 0.2 });
                gsap.from('.pagination-container', { opacity: 0, y: 20, duration: 0.6, delay: 0.4 });
            });
        </script>
    @endpush
@endsection
