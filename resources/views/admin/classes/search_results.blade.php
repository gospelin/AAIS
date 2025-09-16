@extends('admin.layouts.app')

@section('title', 'Search Results for {{ $class->name }}')@section('description', 'Search results for students in {{ $class->name }} at Aunty Anne\'s International School.')@push('styles')
    <style>
        .content-container {
            max-width: 90rem;
            margin: 0 auto;
            padding: var(--space-lg) var(--space-md);
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

        @media (max-width: 768px) {
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

        <h2 class="form-header">Search Results for {{ $class->name }} ({{ $currentSession->year }} - {{ $currentTerm->name }})</h2>

        <!-- Search Form -->
        <div class="search-container">
            <form method="POST" action="{{ route('admin.search_students_by_class', ['className' => urlencode($class->name), 'action' => $action]) }}" class="search-form">
                @csrf
                <input type="text" name="query" class="search-input" value="{{ $query }}" placeholder="Search by name or reg number">
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

        <div class="text-center">
            <a href="{{ route('admin.students_by_class', ['className' => urlencode($class->name), 'action' => $action]) }}" class="btn-cancel">Back to All Students</a>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                gsap.from('.form-header', { opacity: 0, y: 20, duration: 0.6 });
                gsap.from('.search-container', { opacity: 0, y: 20, duration: 0.6, delay: 0.2 });
                gsap.from('.student-table', { opacity: 0, y: 20, duration: 0.6, delay: 0.4 });
                gsap.from('.btn-cancel', { opacity: 0, y: 20, duration: 0.6, delay: 0.6 });
            });
        </script>
    @endpush
@endsection