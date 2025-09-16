@extends('admin.layouts.app')

@section('title', 'Students in {{ $class->name }}')

@section('description', 'View and manage students in {{ $class->name }} at Aunty Anne\'s International School.')

@push('styles')
    <style>
        .content-container {
            max-width: 90rem;
            margin: 0 auto;
            padding: var(--space-lg) var(--space-md);
        }

        .header-container {
            margin-bottom: var(--space-xl);
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
        }

        .filter-container {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-xl);
            padding: var(--space-lg);
            margin-bottom: var(--space-xl);
        }

        .form-grid {
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

        .search-container {
            max-width: 400px;
            margin: var(--space-lg) auto;
        }

        .search-form {
            display: flex;
            gap: var(--space-sm);
        }

        .search-input {
            flex: 1;
            background: var(--bg-secondary);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-md);
            padding: var(--space-sm) var(--space-md);
            color: var(--text-primary);
            font-size: clamp(0.875rem, 2vw, 0.9375rem);
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary-green);
            box-shadow: 0 0 0 3px rgba(33, 160, 85, 0.2);
        }

        .btn-search {
            background: var(--gradient-primary);
            border: none;
            color: var(--white);
            padding: var(--space-sm) var(--space-md);
            border-radius: var(--radius-md);
            cursor: pointer;
        }

        .btn-search:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
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

        .fee-status-paid {
            color: var(--primary-green);
            font-weight: 500;
        }

        .fee-status-unpaid {
            color: var(--error);
            font-weight: 500;
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
            }

            .student-table th,
            .student-table td {
                padding: var(--space-sm);
                font-size: clamp(0.75rem, 2vw, 0.875rem);
            }

            .search-form {
                flex-direction: column;
            }

            .search-input, .btn-search {
                width: 100%;
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
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        <div class="header-container">
            <h2 class="form-header">Students in {{ $class->name }} ({{ $currentSession->year }} - {{ $currentTerm->name }})</h2>
        </div>

        <!-- Filter Form -->
        <div class="filter-container">
            <form method="GET" action="{{ route('admin.students_by_class', ['className' => urlencode($class->name), 'action' => $action]) }}">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="enrollment_status" class="form-label">Enrollment Status</label>
                        <select name="enrollment_status" id="enrollment_status" class="form-select">
                            <option value="all" {{ $enrollmentStatus == 'all' ? 'selected' : '' }}>All</option>
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
                </div>
                <button type="submit" class="btn-submit">Apply Filters</button>
            </form>
        </div>

        <!-- Search Form -->
        <div class="search-container">
            <form method="POST" action="{{ route('admin.search_students_by_class', ['className' => urlencode($class->name), 'action' => $action]) }}" class="search-form">
                @csrf
                <input type="text" name="query" class="search-input" placeholder="Search by name or reg number">
                <button type="submit" class="btn-search"><i class="bx bx-search"></i></button>
            </form>
        </div>

        <!-- Students Table -->
        <div class="student-table">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Reg Number</th>
                        <th>Fee Status</th>
                        <th>Approval Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($studentsClasses[$class->name] ?? [] as $student)
                        <tr>
                            <td>{{ $student->full_name }}</td>
                            <td>{{ $student->reg_no }}</td>
                            <td>
                                @php
                                    $feePayment = $student->feePayments->where('session_id', $currentSession->id)
                                        ->where('term', $currentTerm->value)
                                        ->first();
                                @endphp
                                <span class="fee-status-{{ $feePayment && $feePayment->has_paid_fee ? 'paid' : 'unpaid' }}">
                                    {{ $feePayment && $feePayment->has_paid_fee ? 'Paid' : 'Unpaid' }}
                                </span>
                            </td>
                            <td>{{ $student->approved ? 'Approved' : 'Unapproved' }}</td>
                            <td>
                                <div class="d-flex gap-2">
                                    @if($action == 'view_students')
                                        <form action="{{ route('admin.student_toggle_fee_status', $student->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="action-btn" title="Toggle Fee Status">
                                                <i class="bx bx-money"></i>
                                            </button>
                                        </form>
                                    @elseif($action == 'promote')
                                        <form action="{{ route('admin.promote_student', ['className' => urlencode($class->name), 'studentId' => $student->id, 'action' => $action]) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="action-btn" title="Promote Student">
                                                <i class="bx bx-up-arrow-alt"></i>
                                            </button>
                                        </form>
                                    @elseif($action == 'demote')
                                        <form action="{{ route('admin.demote_student', ['className' => urlencode($class->name), 'studentId' => $student->id, 'action' => $action]) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="action-btn" title="Demote Student">
                                                <i class="bx bx-down-arrow-alt"></i>
                                            </button>
                                        </form>
                                    @elseif($action == 'delete_from_class')
                                        <form action="{{ route('admin.delete_student_class_record', ['className' => urlencode($class->name), 'studentId' => $student->id, 'action' => $action]) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="action-btn" title="Delete from Class">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-[var(--text-secondary)]">No students found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="pagination">
            {{ $students->appends(['enrollment_status' => $enrollmentStatus, 'fee_status' => $feeStatus, 'approval_status' => $approvalStatus])->links() }}
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                gsap.from('.header-container', { opacity: 0, y: 20, duration: 0.6 });
                gsap.from('.filter-container', { opacity: 0, y: 20, duration: 0.6, delay: 0.2 });
                gsap.from('.search-container', { opacity: 0, y: 20, duration: 0.6, delay: 0.4 });
                gsap.from('.student-table', { opacity: 0, y: 20, duration: 0.6, delay: 0.6 });
                gsap.from('.pagination', { opacity: 0, y: 20, duration: 0.6, delay: 0.8 });
            });
        </script>
    @endpush
@endsection