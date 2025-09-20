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

        .stats-widget {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: var(--space-md);
            margin-bottom: var(--space-2xl);
        }

        .stat-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-xl);
            padding: var(--space-lg);
            text-align: center;
            transition: transform 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card i {
            font-size: clamp(1.5rem, 4vw, 2rem);
            color: var(--primary-green);
            margin-bottom: var(--space-sm);
        }

        .stat-card h3 {
            font-family: var(--font-display);
            font-size: clamp(1.25rem, 3vw, 1.5rem);
            font-weight: 600;
            color: var(--text-primary);
            margin: 0;
        }

        .stat-card p {
            font-size: clamp(0.875rem, 2vw, 0.9375rem);
            color: var(--text-secondary);
            margin-top: var(--space-xs);
        }

        .filter-section {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-xl);
            padding: var(--space-xl);
            margin-bottom: var(--space-2xl);
            position: relative;
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

        .badge {
            padding: var(--space-xs) var(--space-sm);
            border-radius: var(--radius-sm);
            font-size: clamp(0.75rem, 2vw, 0.875rem);
        }

        .badge-success {
            background: rgba(33, 160, 85, 0.1);
            color: var(--primary-green);
        }

        .badge-danger {
            background: rgba(220, 53, 69, 0.1);
            color: var(--error);
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

        .pagination {
            display: flex;
            justify-content: center;
            gap: var(--space-md);
            margin-top: var(--space-xl);
        }

        .pagination .page-item {
            margin: 0 var(--space-xs);
        }

        .pagination .page-link {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            color: var(--text-primary);
            padding: var(--space-sm) var(--space-md);
            border-radius: var(--radius-md);
            font-size: clamp(0.875rem, 2vw, 0.9375rem);
            transition: all 0.2s ease;
        }

        .pagination .page-link:hover {
            background: var(--primary-green);
            border-color: var(--primary-green);
            color: var(--white);
        }

        .pagination .active .page-link {
            background: var(--primary-green);
            border-color: var(--primary-green);
            color: var(--white);
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .modal.show {
            display: flex;
        }

        .modal-content {
            background: var(--bg-primary);
            border-radius: var(--radius-xl);
            padding: var(--space-xl);
            max-width: 500px;
            width: 90%;
            position: relative;
        }

        .modal-content h3 {
            font-family: var(--font-display);
            font-size: clamp(1.25rem, 3vw, 1.5rem);
            margin-bottom: var(--space-md);
        }

        .close-modal {
            position: absolute;
            top: var(--space-md);
            right: var(--space-md);
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--text-primary);
        }

        .modal-form .form-group {
            margin-bottom: var(--space-lg);
        }

        .btn-modal-submit {
            background: var(--gradient-primary);
            border: none;
            color: var(--white);
            padding: var(--space-md) var(--space-lg);
            border-radius: var(--radius-md);
            cursor: pointer;
            width: 100%;
        }

        .btn-modal-submit:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
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

        .bulk-actions {
            margin-bottom: var(--space-md);
            display: flex;
            align-items: center;
            gap: var(--space-sm);
        }

        .select-all-checkbox {
            margin-right: var(--space-sm);
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            color: var(--primary-green);
            font-size: clamp(0.875rem, 2vw, 0.9375rem);
            margin-bottom: var(--space-md);
            text-decoration: none;
        }

        .back-link i {
            margin-right: var(--space-xs);
        }

        .back-link:hover {
            text-decoration: underline;
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

            .stats-widget {
                grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            }

            .search-form {
                flex-direction: column;
            }

            .search-input,
            .btn-search {
                width: 100%;
            }

            .student-table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }

            .action-buttons {
                flex-direction: column;
                align-items: flex-start;
            }

            .modal-content {
                width: 95%;
            }
        }
    </style>
@endpush

@section('content')
    <div class="content-container">
        @if (!$selectedSession || !$currentTerm)
            <div class="alert alert-danger">
                No academic session or term is set. Please <a href="{{ route('admin.select_class', ['action' => $action]) }}">select a class</a>.
            </div>
        @else
            <a href="{{ route('admin.select_class', ['action' => $action]) }}" class="back-link">
                <i class="bx bx-arrow-back"></i> Back to Select Class
            </a>

            <!-- Stats Section -->
            <div class="stats-widget">
                <div class="stat-card">
                    <i class="bi bi-person-fill"></i>
                    <h3 id="total-students" data-stat="total_students">{{ $stats['total_students'] ?? 0 }}</h3>
                    <p>Total Students</p>
                </div>
                <div class="stat-card">
                    <i class="bi bi-check-circle-fill"></i>
                    <h3 id="approved-students" data-stat="approved_students">{{ $stats['approved_students'] ?? 0 }}</h3>
                    <p>Approved</p>
                </div>
                <div class="stat-card">
                    <i class="bi bi-wallet2"></i>
                    <h3 id="fees-paid" data-stat="fees_paid">{{ $stats['fees_paid'] ?? 0 }}</h3>
                    <p>Fees Paid</p>
                </div>
                <div class="stat-card">
                    <i class='bx bx-message-x'></i>
                    <h3 id="fees-not-paid" data-stat="fees_not_paid">{{ $stats['fees_not_paid'] ?? 0 }}</h3>
                    <p>Fees Not Paid</p>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="filter-section">
                <h3 class="filter-header">Filter Students in {{ $class->name }} ({{ $selectedSession->year }} - {{ $currentTerm->value }})</h3>
                <form id="filterForm" action="{{ route('admin.students_by_class', ['className' => urlencode($class->name), 'action' => $action]) }}" method="GET">
                    <div class="filter-grid">
                        <div class="form-group">
                            <label for="session_id" class="form-label">Academic Session</label>
                            <select name="session_id" id="session_id" class="form-select">
                                @foreach($sessions as $session)
                                    <option value="{{ $session->id }}" {{ $selectedSession->id == $session->id ? 'selected' : '' }}>{{ $session->year }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="term" class="form-label">Term</label>
                            <select name="term" id="term" class="form-select">
                                @foreach($termChoices as $term)
                                    <option value="{{ $term->value }}" {{ $currentTerm->value === $term->value ? 'selected' : '' }}>{{ $term->label }}</option>
                                @endforeach
                            </select>
                        </div>
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
                    </div>
                </form>
            </div>

            <!-- Search Form -->
            <div class="search-container">
                <form action="{{ route('admin.search_students_by_class', ['className' => urlencode($class->name), 'action' => $action]) }}" method="GET" class="search-form">
                    <input type="text" name="query" class="search-input" placeholder="Search by name or reg number" value="{{ request('query') }}">
                    <input type="hidden" name="session_id" value="{{ $selectedSession->id ?? '' }}">
                    <input type="hidden" name="term" value="{{ $currentTerm->value ?? '' }}">
                    <input type="hidden" name="enrollment_status" value="{{ $enrollmentStatus }}">
                    <input type="hidden" name="fee_status" value="{{ $feeStatus }}">
                    <input type="hidden" name="approval_status" value="{{ $approvalStatus }}">
                    <button type="submit" class="btn-search"><i class="bx bx-search"></i></button>
                </form>
            </div>

            <!-- Bulk Actions -->
            <div class="bulk-actions">
                <input type="checkbox" id="select-all" class="select-all-checkbox">
                <label for="select-all">Select All</label>
                @if($action == 'promote')
                    <button type="button" class="btn btn-sm btn-primary action-btn bulk-action-btn" id="bulk-promote-btn" disabled>
                        <i class="bx bx-up-arrow-alt"></i> Bulk Promote
                    </button>
                @elseif($action == 'demote')
                    <button type="button" class="btn btn-sm btn-warning action-btn bulk-action-btn" id="bulk-demote-btn" disabled>
                        <i class="bx bx-down-arrow-alt"></i> Bulk Demote
                    </button>
                @endif
            </div>

            <!-- Students List -->
            <div class="student-section">
                <div class="loading-overlay" id="loadingOverlay">
                    <div class="loading-spinner"></div>
                </div>
                @if($students->isEmpty())
                    <table class="student-table">
                        <tbody>
                            <tr>
                                <td colspan="10" class="text-center text-[var(--text-secondary)]">No students found for the selected filters.</td>
                            </tr>
                        </tbody>
                    </table>
                @else
                    <table class="student-table">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="select-all-header"></th>
                                <th>Registration No</th>
                                <th>First Name</th>
                                <th>Middle Name</th>
                                <th>Last Name</th>
                                <th>Gender</th>
                                <th>Enrollment Status</th>
                                <th>Fee Status</th>
                                <th>Approval Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="students-table-body">
                            @foreach($students as $student)
                                <tr>
                                    <td><input type="checkbox" class="student-checkbox" value="{{ $student->id }}"></td>
                                    <td>{{ $student->reg_no }}</td>
                                    <td>{{ $student->first_name }}</td>
                                    <td>{{ $student->middle_name ?? 'N/A' }}</td>
                                    <td>{{ $student->last_name }}</td>
                                    <td>{{ ucfirst($student->gender) }}</td>
                                    <td>
                                        @php
                                            $isActiveInTerm = $student->classHistory
                                                ->where('session_id', $selectedSession->id)
                                                ->contains(function ($history) use ($selectedSession, $currentTerm) {
                                                    return $history->isActiveInTerm($selectedSession->id, $currentTerm->value);
                                                });
                                        @endphp
                                        @if($isActiveInTerm)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $hasPaid = $student->feePayments
                                                ->where('session_id', $selectedSession->id)
                                                ->where('term', $currentTerm->value)
                                                ->where('has_paid_fee', true)
                                                ->count() > 0;
                                        @endphp
                                        @if($hasPaid)
                                            <span class="badge badge-success">Paid</span>
                                        @else
                                            <span class="badge badge-danger">Unpaid</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($student->approved)
                                            <span class="badge badge-success">Approved</span>
                                        @else
                                            <span class="badge badge-danger">Not Approved</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            @if($action == 'view_students')
                                                <button type="button" class="btn btn-sm btn-secondary toggle-fee-status action-btn"
                                                        data-student-id="{{ $student->id }}"
                                                        data-session-id="{{ $selectedSession->id }}"
                                                        data-term="{{ $currentTerm->value }}"
                                                        data-status="{{ $hasPaid ? 'paid' : 'unpaid' }}">
                                                    <i class="bx bx-wallet"></i>
                                                    {{ $hasPaid ? 'Mark as Unpaid' : 'Mark as Paid' }}
                                                </button>
                                                <button type="button" class="btn btn-sm {{ $student->approved ? 'btn-warning' : 'btn-success' }} toggle-approval-status action-btn"
                                                        data-student-id="{{ $student->id }}"
                                                        data-status="{{ $student->approved ? 'approved' : 'unapproved' }}">
                                                    <i class="bx bx-check"></i>
                                                    {{ $student->approved ? 'Unapprove' : 'Approve' }}
                                                </button>
                                            @elseif($action == 'promote')
                                                <button type="button" class="btn btn-sm btn-primary action-btn open-promotion-modal"
                                                        data-student-id="{{ $student->id }}"
                                                        data-student-name="{{ $student->first_name }} {{ $student->last_name }}"
                                                        data-class-name="{{ urlencode($class->name) }}"
                                                        data-session-id="{{ $selectedSession->id }}"
                                                        data-term="{{ $currentTerm->value }}"
                                                        data-action="promote">
                                                    <i class="bx bx-up-arrow-alt"></i> Promote
                                                </button>
                                            @elseif($action == 'demote')
                                                <button type="button" class="btn btn-sm btn-warning action-btn open-promotion-modal"
                                                        data-student-id="{{ $student->id }}"
                                                        data-student-name="{{ $student->first_name }} {{ $student->last_name }}"
                                                        data-class-name="{{ urlencode($class->name) }}"
                                                        data-session-id="{{ $selectedSession->id }}"
                                                        data-term="{{ $currentTerm->value }}"
                                                        data-action="demote">
                                                    <i class="bx bx-down-arrow-alt"></i> Demote
                                                </button>
                                            @elseif($action == 'delete_from_class')
                                                <a href="{{ route('admin.delete_student_class_record', ['className' => urlencode($class->name), 'studentId' => $student->id, 'action' => $action, 'session_id' => $selectedSession->id, 'term' => $currentTerm->value]) }}"
                                                   class="btn btn-sm btn-danger action-btn"
                                                   onclick="return confirm('Are you sure you want to delete this student\'s class record?');">
                                                    <i class="bx bx-trash"></i> Delete
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="pagination" id="paginationLinks">
                        {{ $students->appends(['session_id' => $selectedSession->id, 'term' => $currentTerm->value, 'enrollment_status' => $enrollmentStatus, 'fee_status' => $feeStatus, 'approval_status' => $approvalStatus])->links('vendor.pagination.bootstrap-5') }}
                    </div>
                @endif
            </div>

            <!-- Promotion/Demotion Modal -->
            <div class="modal" id="promotionModal">
                <div class="modal-content">
                    <span class="close-modal">&times;</span>
                    <h3 id="modalTitle">Promote/Demote Student</h3>
                    <form id="promotionForm" method="POST" action="">
                        @csrf
                        <div class="form-group">
                            <label for="promotion_session_id" class="form-label">Promotion Session</label>
                            <select name="promotion_session_id" id="promotion_session_id" class="form-select">
                                @foreach($sessions as $session)
                                    <option value="{{ $session->id }}" {{ $nextSession && $session->id == $nextSession->id ? 'selected' : '' }}>
                                        {{ $session->year }} {{ $session->is_current ? '(Current)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="target_class_id" class="form-label">Target Class</label>
                            <select name="target_class_id" id="target_class_id" class="form-select">
                                @foreach(\App\Models\Classes::orderBy('hierarchy')->get() as $classOption)
                                    <option value="{{ $classOption->id }}">{{ $classOption->name }} {{ $classOption->section ? ' - ' . $classOption->section : '' }} (Hierarchy: {{ $classOption->hierarchy }})</option>
                                @endforeach
                            </select>
                        </div>
                        <input type="hidden" name="session_id" id="modal_session_id" value="{{ $selectedSession->id ?? '' }}">
                        <input type="hidden" name="term" id="modal_term" value="{{ $currentTerm->value ?? '' }}">
                        <button type="submit" class="btn-modal-submit">Submit</button>
                    </form>
                </div>
            </div>

            <!-- Bulk Promotion/Demotion Modal -->
            <div class="modal" id="bulkActionModal">
                <div class="modal-content">
                    <span class="close-modal">&times;</span>
                    <h3 id="bulkModalTitle">Bulk Action</h3>
                    <form id="bulkActionForm" method="POST" action="">
                        @csrf
                        <div id="student-ids-container"></div>
                        <div class="form-group">
                            <label for="bulk_promotion_session_id" class="form-label">Target Session</label>
                            <select name="promotion_session_id" id="bulk_promotion_session_id" class="form-select">
                                @foreach($sessions as $session)
                                    <option value="{{ $session->id }}" {{ $nextSession && $session->id == $nextSession->id ? 'selected' : '' }}>
                                        {{ $session->year }} {{ $session->is_current ? '(Current)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="bulk_target_class_id" class="form-label">Target Class</label>
                            <select name="target_class_id" id="bulk_target_class_id" class="form-select">
                                @foreach(\App\Models\Classes::orderBy('hierarchy')->get() as $classOption)
                                    <option value="{{ $classOption->id }}">{{ $classOption->name }} {{ $classOption->section ? ' - ' . $classOption->section : '' }} (Hierarchy: {{ $classOption->hierarchy }})</option>
                                @endforeach
                            </select>
                        </div>
                        <input type="hidden" name="session_id" id="bulk_modal_session_id" value="{{ $selectedSession->id ?? '' }}">
                        <input type="hidden" name="term" id="bulk_modal_term" value="{{ $currentTerm->value ?? '' }}">
                        <button type="submit" class="btn-modal-submit">Submit</button>
                    </form>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // GSAP Animations
            gsap.from('.stats-widget', { opacity: 0, y: 20, duration: 0.6 });
            gsap.from('.filter-section', { opacity: 0, y: 20, duration: 0.6, delay: 0.2 });
            gsap.from('.search-container', { opacity: 0, y: 20, duration: 0.6, delay: 0.4 });
            gsap.from('.bulk-actions', { opacity: 0, y: 20, duration: 0.6, delay: 0.5 });
            gsap.from('.student-section', { opacity: 0, y: 20, duration: 0.6, delay: 0.6 });
            gsap.from('.pagination', { opacity: 0, y: 20, duration: 0.6, delay: 0.8 });

            const loadingOverlay = document.getElementById('loadingOverlay');
            const promotionModal = document.getElementById('promotionModal');
            const bulkActionModal = document.getElementById('bulkActionModal');
            const closeModals = document.querySelectorAll('.close-modal');
            const promotionForm = document.getElementById('promotionForm');
            const bulkActionForm = document.getElementById('bulkActionForm');
            const modalTitle = document.getElementById('modalTitle');
            const bulkModalTitle = document.getElementById('bulkModalTitle');
            const targetClassSelect = document.getElementById('target_class_id');
            const bulkTargetClassSelect = document.getElementById('bulk_target_class_id');
            const selectAllCheckbox = document.getElementById('select-all');
            const selectAllHeader = document.getElementById('select-all-header');
            const studentCheckboxes = document.querySelectorAll('.student-checkbox');
            const bulkPromoteBtn = document.getElementById('bulk-promote-btn');
            const bulkDemoteBtn = document.getElementById('bulk-demote-btn');
            const studentIdsContainer = document.getElementById('student-ids-container');

            // Auto-submit filter form on change
            const filterForm = document.getElementById('filterForm');
            if (filterForm) {
                const filterSelects = filterForm.querySelectorAll('select');
                filterSelects.forEach(select => {
                    select.addEventListener('change', () => {
                        filterForm.submit();
                    });
                });
            }

            // Fetch stats dynamically
            const selectors = {
                totalStudentsStat: '[data-stat="total_students"]',
                approvedStudentsStat: '[data-stat="approved_students"]',
                feesPaidStat: '[data-stat="fees_paid"]',
                feesNotPaidStat: '[data-stat="fees_not_paid"]'
            };

            function updateStats() {
                const sessionId = document.getElementById('session_id')?.value || '{{ $selectedSession->id ?? '' }}';
                const term = document.getElementById('term')?.value || '{{ $currentTerm->value ?? '' }}';
                fetch(`{{ route('admin.class_stats', ['classId' => $class->id]) }}?session_id=${encodeURIComponent(sessionId)}&term=${encodeURIComponent(term)}`, {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        document.querySelector(selectors.totalStudentsStat).textContent = data.total_students || 0;
                        document.querySelector(selectors.approvedStudentsStat).textContent = data.approved_students || 0;
                        document.querySelector(selectors.feesPaidStat).textContent = data.fees_paid || 0;
                        document.querySelector(selectors.feesNotPaidStat).textContent = data.fees_not_paid || 0;
                    })
                    .catch(error => console.error('Error fetching stats:', error));
            }

            // Toggle fee status
            function attachFeeStatusListeners() {
                document.querySelectorAll('.toggle-fee-status').forEach(button => {
                    button.addEventListener('click', () => {
                        const studentId = button.getAttribute('data-student-id');
                        const sessionId = button.getAttribute('data-session-id');
                        const term = button.getAttribute('data-term');
                        const currentStatus = button.getAttribute('data-status');
                        const url = '{{ route('admin.class_toggle_fee_status', ':studentId') }}'.replace(':studentId', studentId);

                        loadingOverlay.style.display = 'flex';
                        fetch(url, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                session_id: sessionId,
                                term: term
                            })
                        })
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error('Network response was not ok');
                                }
                                return response.json();
                            })
                            .then(data => {
                                loadingOverlay.style.display = 'none';
                                if (data.success) {
                                    const newStatus = data.new_status;
                                    button.setAttribute('data-status', newStatus);
                                    button.innerHTML = `<i class="bx bx-wallet"></i> ${newStatus === 'paid' ? 'Mark as Unpaid' : 'Mark as Paid'}`;
                                    const badge = button.closest('tr').querySelector('td:nth-child(8) .badge');
                                    badge.textContent = newStatus === 'paid' ? 'Paid' : 'Unpaid';
                                    badge.classList.toggle('badge-success', newStatus === 'paid');
                                    badge.classList.toggle('badge-danger', newStatus === 'unpaid');
                                    updateStats();
                                    alert(data.message);
                                } else {
                                    alert(data.message || 'Error updating fee status');
                                }
                            })
                            .catch(error => {
                                loadingOverlay.style.display = 'none';
                                alert('Error updating fee status: ' + error.message);
                            });
                    });
                });
            }

            // Toggle approval status
            function attachApprovalStatusListeners() {
                document.querySelectorAll('.toggle-approval-status').forEach(button => {
                    button.addEventListener('click', () => {
                        const studentId = button.getAttribute('data-student-id');
                        const currentStatus = button.getAttribute('data-status');
                        const url = '{{ route('admin.class_toggle_approval_status', ':studentId') }}'.replace(':studentId', studentId);

                        loadingOverlay.style.display = 'flex';
                        fetch(url, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            }
                        })
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error('Network response was not ok');
                                }
                                return response.json();
                            })
                            .then(data => {
                                loadingOverlay.style.display = 'none';
                                if (data.success) {
                                    const newStatus = currentStatus === 'approved' ? 'unapproved' : 'approved';
                                    button.setAttribute('data-status', newStatus);
                                    button.innerHTML = `<i class="bx bx-check"></i> ${newStatus === 'approved' ? 'Unapprove' : 'Approve'}`;
                                    button.classList.toggle('btn-success', newStatus === 'approved');
                                    button.classList.toggle('btn-warning', newStatus === 'unapproved');
                                    const badge = button.closest('tr').querySelector('td:nth-child(9) .badge');
                                    badge.textContent = newStatus === 'approved' ? 'Approved' : 'Not Approved';
                                    badge.classList.toggle('badge-success', newStatus === 'approved');
                                    badge.classList.toggle('badge-danger', newStatus === 'unapproved');
                                    updateStats();
                                    alert(data.message);
                                } else {
                                    alert(data.message || 'Error updating approval status');
                                }
                            })
                            .catch(error => {
                                loadingOverlay.style.display = 'none';
                                alert('Error updating approval status: ' + error.message);
                            });
                    });
                });
            }

            // Promotion/Demotion Modal
            function attachPromotionModalListeners() {
                document.querySelectorAll('.open-promotion-modal').forEach(button => {
                    button.addEventListener('click', () => {
                        const studentId = button.getAttribute('data-student-id');
                        const studentName = button.getAttribute('data-student-name');
                        const className = button.getAttribute('data-class-name');
                        const sessionId = button.getAttribute('data-session-id');
                        const term = button.getAttribute('data-term');
                        const action = button.getAttribute('data-action');

                        // Set modal title
                        modalTitle.textContent = action === 'promote' ? `Promote ${studentName}` : `Demote ${studentName}`;

                        // Set form action based on action type
                        const route = action === 'promote'
                            ? '{{ route('admin.promote_student', ['className' => ':className', 'studentId' => ':studentId', 'action' => ':action']) }}'
                            : '{{ route('admin.demote_student', ['className' => ':className', 'studentId' => ':studentId', 'action' => ':action']) }}';
                        promotionForm.action = route
                            .replace(':className', className)
                            .replace(':studentId', studentId)
                            .replace(':action', action);

                        // Set hidden inputs
                        document.getElementById('modal_session_id').value = sessionId;
                        document.getElementById('modal_term').value = term;

                        // Set default promotion session
                        document.getElementById('promotion_session_id').value = action === 'promote' && '{{ $nextSession->id ?? '' }}' ? '{{ $nextSession->id }}' : sessionId;

                        // Fetch suggested target class via AJAX
                        const suggestUrl = action === 'promote'
                            ? '{{ route('admin.suggest_next_class', ['classId' => $class->id]) }}'
                            : '{{ route('admin.suggest_previous_class', ['classId' => $class->id]) }}';

                        loadingOverlay.style.display = 'flex';
                        fetch(suggestUrl, {
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        })
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error('Network response was not ok');
                                }
                                return response.json();
                            })
                            .then(data => {
                                loadingOverlay.style.display = 'none';
                                if (data.success && data.class_id) {
                                    targetClassSelect.value = data.class_id;
                                } else {
                                    targetClassSelect.value = '';
                                    alert(data.message || 'No suitable class found.');
                                }
                                promotionModal.style.display = 'flex';
                            })
                            .catch(error => {
                                loadingOverlay.style.display = 'none';
                                alert('Error fetching suggested class: ' + error.message);
                            });
                    });
                });
            }

            // Bulk Action Modal
            function attachBulkActionListeners() {
                const openBulkModal = (action, title) => {
                    const selectedStudentIds = Array.from(studentCheckboxes)
                        .filter(cb => cb.checked)
                        .map(cb => cb.value);
                    if (selectedStudentIds.length === 0) {
                        alert('Please select at least one student.');
                        return;
                    }

                    bulkModalTitle.textContent = title;
                    const route = action === 'promote'
                        ? '{{ route('admin.bulk_promote_students', ['className' => ':className', 'action' => ':action']) }}'
                        : '{{ route('admin.bulk_demote_students', ['className' => ':className', 'action' => ':action']) }}';
                    bulkActionForm.action = route
                        .replace(':className', '{{ urlencode($class->name) }}')
                        .replace(':action', '{{ $action }}');

                    // Clear previous student IDs
                    studentIdsContainer.innerHTML = '';

                    // Add hidden inputs for each student ID
                    selectedStudentIds.forEach(id => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'student_ids[]';
                        input.value = id;
                        studentIdsContainer.appendChild(input);
                    });

                    // Set default session
                    document.getElementById('bulk_promotion_session_id').value = action === 'promote' && '{{ $nextSession->id ?? '' }}' ? '{{ $nextSession->id }}' : '{{ $selectedSession->id ?? '' }}';
                    document.getElementById('bulk_modal_session_id').value = '{{ $selectedSession->id ?? '' }}';
                    document.getElementById('bulk_modal_term').value = '{{ $currentTerm->value ?? '' }}';

                    // Fetch suggested target class
                    const suggestUrl = action === 'promote'
                        ? '{{ route('admin.suggest_next_class', ['classId' => $class->id]) }}'
                        : '{{ route('admin.suggest_previous_class', ['classId' => $class->id]) }}';

                    loadingOverlay.style.display = 'flex';
                    fetch(suggestUrl, {
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            loadingOverlay.style.display = 'none';
                            if (data.success && data.class_id) {
                                bulkTargetClassSelect.value = data.class_id;
                            } else {
                                bulkTargetClassSelect.value = '';
                                alert(data.message || 'No suitable class found.');
                            }
                            bulkActionModal.classList.add('show');
                            gsap.from(bulkActionModal.querySelector('.modal-content'), { opacity: 0, y: 20, duration: 0.4 });
                        })
                        .catch(error => {
                            loadingOverlay.style.display = 'none';
                            alert('Error fetching suggested class: ' + error.message);
                        });
                };

                if (bulkPromoteBtn) {
                    bulkPromoteBtn.addEventListener('click', () => openBulkModal('promote', 'Bulk Promote Students'));
                }
                if (bulkDemoteBtn) {
                    bulkDemoteBtn.addEventListener('click', () => openBulkModal('demote', 'Bulk Demote Students'));
                }
            }

            // Checkbox handling
            function updateBulkButtons() {
                const checkedCount = document.querySelectorAll('.student-checkbox:checked').length;
                if (bulkPromoteBtn) bulkPromoteBtn.disabled = checkedCount === 0;
                if (bulkDemoteBtn) bulkDemoteBtn.disabled = checkedCount === 0;
            }

            selectAllCheckbox.addEventListener('change', function () {
                studentCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                selectAllHeader.checked = this.checked;
                updateBulkButtons();
            });

            selectAllHeader.addEventListener('change', function () {
                studentCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                selectAllCheckbox.checked = this.checked;
                updateBulkButtons();
            });

            studentCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', () => {
                    selectAllCheckbox.checked = document.querySelectorAll('.student-checkbox:checked').length === studentCheckboxes.length;
                    selectAllHeader.checked = selectAllCheckbox.checked;
                    updateBulkButtons();
                });
            });

            // Close modals
            closeModals.forEach(closeBtn => {
                closeBtn.addEventListener('click', () => {
                    promotionModal.style.display = 'none';
                    bulkActionModal.classList.remove('show');
                });
            });

            // Close modals when clicking outside
            window.addEventListener('click', (event) => {
                if (event.target === promotionModal) {
                    promotionModal.style.display = 'none';
                }
                if (event.target === bulkActionModal) {
                    bulkActionModal.classList.remove('show');
                }
            });

            // Initialize listeners
            attachFeeStatusListeners();
            attachApprovalStatusListeners();
            attachPromotionModalListeners();
            attachBulkActionListeners();

            // Update stats on page load
            updateStats();
        });
    </script>
@endpush
