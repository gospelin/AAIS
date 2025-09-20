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
            position: relative;
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

        .pagination {
            display: flex;
            justify-content: center;
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
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
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

            .student-table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }

            .action-buttons {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
@endpush

@section('content')
    <div class="content-container">
        @if(!$selectedSession)
            <div class="alert alert-danger">
                No academic session is selected. Please <a href="{{ route('admin.manage_academic_sessions') }}">select a
                    session</a> to view students.
            </div>
        @else
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
                <h3 class="filter-header">Filter Students</h3>
                <form id="filterForm" action="{{ route('admin.students', ['action' => $action]) }}" method="GET">
                    <div class="filter-grid">
                        <div class="form-group">
                            <label for="session_id" class="form-label">Academic Session</label>
                            <select name="session_id" id="session_id" class="form-select">
                                @foreach($sessions as $session)
                                    <option value="{{ $session->id }}" {{ $selectedSession->id == $session->id ? 'selected' : '' }}>
                                        {{ $session->year }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="term" class="form-label">Term</label>
                            <select name="term" id="term" class="form-select">
                                @foreach(\App\Enums\TermEnum::cases() as $term)
                                    <option value="{{ $term->value }}" {{ $term->value === ($currentTerm instanceof \App\Enums\TermEnum ? $currentTerm->value : $currentTerm) ? 'selected' : '' }}>
                                        {{ $term->value }}
                                    </option>
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
                                <option value="unapproved" {{ $approvalStatus == 'unapproved' ? 'selected' : '' }}>Unapproved
                                </option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Search Form -->
            <div class="search-container">
                <form action="{{ route('admin.search_students', ['action' => 'view_students']) }}" method="GET" id="searchForm"
                    class="search-form">
                    <input type="text" name="query" class="search-input" value="{{ request('query') }}"
                        placeholder="Search by name or reg number">
                    <input type="hidden" name="session_id" value="{{ $selectedSession->id }}">
                    <input type="hidden" name="term" value="{{ $currentTerm->value }}">
                    <input type="hidden" name="enrollment_status" value="{{ $enrollmentStatus }}">
                    <input type="hidden" name="fee_status" value="{{ $feeStatus }}">
                    <input type="hidden" name="approval_status" value="{{ $approvalStatus }}">
                    <button type="submit" class="btn-search"><i class="bx bx-search"></i></button>
                </form>
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
                                <td colspan="10" class="text-center text-[var(--text-secondary)]">No students found for the selected
                                    filters.</td>
                            </tr>
                        </tbody>
                    </table>
                @else
                    <table class="student-table">
                        <thead>
                            <tr>
                                <th>Registration No</th>
                                <th>First Name</th>
                                <th>Middle Name</th>
                                <th>Last Name</th>
                                <th>Class Name</th>
                                <th>Gender</th>
                                <th>Enrollment Status</th>
                                <th>Fee Status</th>
                                <th>Approval Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $student)
                                <tr>
                                    <td>{{ $student->reg_no }}</td>
                                    <td>{{ $student->first_name }}</td>
                                    <td>{{ $student->middle_name ?? 'N/A' }}</td>
                                    <td>{{ $student->last_name }}</td>
                                    <td>{{ $student->class_name ?? 'Unassigned' }}</td>
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
                                            <a href="{{ route('admin.students.edit', ['student' => $student->id, 'session_id' => $selectedSession->id, 'term' => $currentTerm->value]) }}"
                                                class="btn btn-sm btn-primary action-btn">
                                                <i class="bx bx-edit"></i> Edit
                                            </a>
                                            <form action="{{ route('admin.students.destroy', $student->id) }}" method="POST"
                                                style="display:inline;"
                                                onsubmit="return confirm('Are you sure you want to permanently delete this student?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger action-btn">
                                                    <i class="bx bx-trash"></i> Delete
                                                </button>
                                            </form>
                                            @if($isActiveInTerm)
                                                <a href="{{ route('admin.student_mark_as_left', $student->id) }}"
                                                    class="btn btn-sm btn-warning action-btn">
                                                    <i class="bx bx-exit"></i> Mark as Left
                                                </a>
                                            @else
                                                <a href="{{ route('admin.student_reenroll', $student->id) }}"
                                                    class="btn btn-sm btn-info action-btn">
                                                    <i class="bx bx-user-plus"></i> Re-enroll
                                                </a>
                                            @endif
                                            <button type="button" class="btn btn-sm btn-secondary toggle-fee-status action-btn"
                                                data-student-id="{{ $student->id }}" data-session-id="{{ $selectedSession->id }}"
                                                data-term="{{ $currentTerm->value }}" data-status="{{ $hasPaid ? 'paid' : 'unpaid' }}">
                                                <i class="bx bx-wallet"></i>
                                                {{ $hasPaid ? 'Mark as Unpaid' : 'Mark as Paid' }}
                                            </button>
                                            <button type="button"
                                                class="btn btn-sm {{ $student->approved ? 'btn-warning' : 'btn-success' }} toggle-approval-status action-btn"
                                                data-student-id="{{ $student->id }}"
                                                data-status="{{ $student->approved ? 'approved' : 'unapproved' }}">
                                                <i class="bx bx-check"></i>
                                                {{ $student->approved ? 'Unapprove' : 'Approve' }}
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
            <div class="pagination">
                {{ $students->links('vendor.pagination.bootstrap-5') }}
            </div>
        @endif
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const filterForm = document.getElementById('filterForm');
                const searchForm = document.getElementById('searchForm');
                const studentSection = document.querySelector('.student-section');
                const loadingOverlay = document.getElementById('loadingOverlay');

                // Show/hide loading overlay
                function showLoading() {
                    if (loadingOverlay) {
                        loadingOverlay.style.display = 'flex';
                    }
                }

                function hideLoading() {
                    if (loadingOverlay) {
                        loadingOverlay.style.display = 'none';
                    }
                }

                // Handle filter form submission via AJAX
                if (filterForm) {
                    const filterSelects = filterForm.querySelectorAll('select');
                    filterSelects.forEach(select => {
                        select.addEventListener('change', () => {
                            fetchStudents(filterForm.action, new URLSearchParams(new FormData(filterForm)).toString());
                        });
                    });
                }

                // Handle search form submission via AJAX
                if (searchForm) {
                    searchForm.addEventListener('submit', (e) => {
                        e.preventDefault();
                        fetchStudents(searchForm.action, new URLSearchParams(new FormData(searchForm)).toString());
                    });
                }

                // Handle pagination clicks via AJAX
                studentSection.addEventListener('click', (e) => {
                    const pageLink = e.target.closest('.page-link');
                    if (pageLink && !pageLink.closest('.active')) {
                        e.preventDefault();
                        const url = pageLink.href;
                        fetchStudents(url);
                    }
                });

                // Fetch students via AJAX
                function fetchStudents(url, queryString = '') {
                    showLoading();
                    fetch(`${url}${queryString ? '?' + queryString : ''}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                        .then(response => {
                            if (!response.ok) {
                                return response.text().then(text => {
                                    throw new Error(`HTTP error! Status: ${response.status}, Response: ${text.substring(0, 500)}...`);
                                });
                            }
                            if (response.headers.get('content-type')?.includes('application/json')) {
                                return response.json();
                            } else {
                                return response.text().then(text => {
                                    throw new Error(`Expected JSON response, but received: ${text.substring(0, 500)}...`);
                                });
                            }
                        })
                        .then(data => {
                            studentSection.innerHTML = data.html;
                            updateStats();
                            hideLoading();
                        })
                        .catch(error => {
                            console.error('Error fetching students:', error);
                            alert('Error loading students: ' + error.message);
                            hideLoading();
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
                    const sessionId = document.getElementById('session_id')?.value || '{{ $selectedSession->id }}';
                    const term = document.getElementById('term')?.value || '{{ $currentTerm instanceof \App\Enums\TermEnum ? $currentTerm->value : ($currentTerm ?: 'First') }}';
                    const enrollmentStatus = document.getElementById('enrollment_status')?.value || '{{ $enrollmentStatus }}';
                    const feeStatus = document.getElementById('fee_status')?.value || '{{ $feeStatus }}';
                    const approvalStatus = document.getElementById('approval_status')?.value || '{{ $approvalStatus }}';
                    const query = '{{ request('query') }}';

                    fetch(`{{ route('admin.student_stats') }}?session_id=${encodeURIComponent(sessionId)}&term=${encodeURIComponent(term)}&enrollment_status=${encodeURIComponent(enrollmentStatus)}&fee_status=${encodeURIComponent(feeStatus)}&approval_status=${encodeURIComponent(approvalStatus)}&query=${encodeURIComponent(query)}`, {
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                        .then(response => {
                            if (!response.ok) {
                                return response.text().then(text => {
                                    throw new Error(`HTTP error! Status: ${response.status}, Response: ${text.substring(0, 500)}...`);
                                });
                            }
                            if (response.headers.get('content-type')?.includes('application/json')) {
                                return response.json();
                            } else {
                                return response.text().then(text => {
                                    throw new Error(`Expected JSON response, but received: ${text.substring(0, 500)}...`);
                                });
                            }
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
                studentSection.addEventListener('click', (e) => {
                    const button = e.target.closest('.toggle-fee-status');
                    if (button) {
                        const studentId = button.getAttribute('data-student-id');
                        const sessionId = button.getAttribute('data-session-id');
                        const term = button.getAttribute('data-term');
                        const currentStatus = button.getAttribute('data-status');
                        const url = '{{ route('admin.student_toggle_fee_status', ':student') }}'.replace(':student', studentId);

                        showLoading();
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
                                    return response.text().then(text => {
                                        throw new Error(`HTTP error! Status: ${response.status}, Response: ${text.substring(0, 500)}...`);
                                    });
                                }
                                if (response.headers.get('content-type')?.includes('application/json')) {
                                    return response.json();
                                } else {
                                    return response.text().then(text => {
                                        throw new Error(`Expected JSON response, but received: ${text.substring(0, 500)}...`);
                                    });
                                }
                            })
                            .then(data => {
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
                                hideLoading();
                            })
                            .catch(error => {
                                alert('Error updating fee status: ' + error.message);
                                hideLoading();
                            });
                    }
                });

                // Toggle approval status
                studentSection.addEventListener('click', (e) => {
                    const button = e.target.closest('.toggle-approval-status');
                    if (button) {
                        const studentId = button.getAttribute('data-student-id');
                        const currentStatus = button.getAttribute('data-status');
                        const url = '{{ route('admin.student_approve', ':student') }}'.replace(':student', studentId);

                        showLoading();
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
                                    return response.text().then(text => {
                                        throw new Error(`HTTP error! Status: ${response.status}, Response: ${text.substring(0, 500)}...`);
                                    });
                                }
                                if (response.headers.get('content-type')?.includes('application/json')) {
                                    return response.json();
                                } else {
                                    return response.text().then(text => {
                                        throw new Error(`Expected JSON response, but received: ${text.substring(0, 500)}...`);
                                    });
                                }
                            })
                            .then(data => {
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
                                hideLoading();
                            })
                            .catch(error => {
                                alert('Error updating approval status: ' + error.message);
                                hideLoading();
                            });
                    }
                });

                // Update stats when session or term changes
                const sessionSelect = document.getElementById('session_id');
                const termSelect = document.getElementById('term');
                if (sessionSelect) {
                    sessionSelect.addEventListener('change', updateStats);
                }
                if (termSelect) {
                    termSelect.addEventListener('change', updateStats);
                }

                updateStats();
            });
        </script>
    @endpush
@endsection
