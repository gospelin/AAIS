@extends('admin.layouts.app')

@section('title', 'Search Students')

@section('description', 'Search results for students at Aunty Anne\'s International School.')

@push('styles')
    <style>
        .content-container {
            max-width: 90rem;
            margin: 0 auto;
            padding: var(--space-lg) var(--space-md);
        }

        .student-section {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-xl);
            overflow: hidden;
            margin-bottom: var(--space-2xl);
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

        .badge {
            padding: var(--space-xs) var(--space-sm);
            border-radius: var(--radius-sm);
            font-size: clamp(0.75rem, 2vw, 0.875rem);
        }

        .badge-success {
            background: var(--primary-green);
            color: var(--white);
        }

        .badge-danger {
            background: var(--danger);
            color: var(--white);
        }

        @media (max-width: 768px) {
            .student-table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }

            .student-table th,
            .student-table td {
                padding: var(--space-sm);
                font-size: clamp(0.75rem, 2vw, 0.875rem);
            }

            .content-container {
                padding: var(--space-md);
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
        <div class="student-section">
            @if($students->isEmpty())
                <table class="student-table">
                    <tbody>
                        <tr>
                            <td colspan="10" class="text-center text-[var(--text-secondary)]">No students found for the search query.</td>
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
                                            ->where('session_id', $currentSession->id)
                                            ->contains(function ($history) use ($currentSession, $currentTerm) {
                                                return $history->isActiveInTerm($currentSession->id, $currentTerm->value);
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
                                            ->where('session_id', $currentSession->id)
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
                                        <a href="{{ route('admin.students.edit', $student->id) }}" class="btn btn-sm btn-primary action-btn">
                                            <i class="bx bx-edit"></i> Edit
                                        </a>
                                        <form action="{{ route('admin.students.destroy', $student->id) }}" method="POST" style="display:inline;"
                                            onsubmit="return confirm('Are you sure you want to permanently delete this student?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger action-btn">
                                                <i class="bx bx-trash"></i> Delete
                                            </button>
                                        </form>
                                        @if(!$student->approved)
                                            <a href="{{ route('admin.student_approve', $student->id) }}" class="btn btn-sm btn-success action-btn">
                                                <i class="bx bx-check"></i> Approve
                                            </a>
                                        @endif
                                        @if($isActiveInTerm)
                                            <a href="{{ route('admin.student_mark_as_left', $student->id) }}" class="btn btn-sm btn-warning action-btn">
                                                <i class="bx bx-exit"></i> Mark as Left
                                            </a>
                                        @else
                                            <a href="{{ route('admin.student_reenroll', $student->id) }}" class="btn btn-sm btn-info action-btn">
                                                <i class="bx bx-user-plus"></i> Re-enroll
                                            </a>
                                        @endif
                                        <button type="button" class="btn btn-sm btn-secondary toggle-fee-status action-btn" 
                                            data-student-id="{{ $student->id }}"
                                            data-session-id="{{ $currentSession->id }}" 
                                            data-term="{{ $currentTerm->value }}"
                                            data-status="{{ $hasPaid ? 'paid' : 'unpaid' }}">
                                            <i class="bx bx-wallet"></i>
                                            {{ $hasPaid ? 'Mark as Unpaid' : 'Mark as Paid' }}
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="pagination">
                    {{ $students->links('vendor.pagination.custom') }}
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                // Toggle fee status
                document.querySelectorAll('.toggle-fee-status').forEach(button => {
                    button.addEventListener('click', () => {
                        const studentId = button.getAttribute('data-student-id');
                        const sessionId = button.getAttribute('data-session-id');
                        const term = button.getAttribute('data-term');
                        const currentStatus = button.getAttribute('data-status');

                        fetch('{{ route('admin.student_toggle_fee_status', ':student') }}'.replace(':student', studentId), {
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
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    const newStatus = data.new_status;
                                    button.setAttribute('data-status', newStatus);
                                    button.innerHTML = `<i class="bx bx-wallet"></i> ${newStatus === 'paid' ? 'Mark as Unpaid' : 'Mark as Paid'}`;
                                    const badge = button.closest('tr').querySelector('td:nth-child(8) .badge');
                                    badge.textContent = newStatus === 'paid' ? 'Paid' : 'Unpaid';
                                    badge.classList.toggle('badge-success', newStatus === 'paid');
                                    badge.classList.toggle('badge-danger', newStatus === 'unpaid');
                                    alert(data.message);
                                } else {
                                    alert(data.message || 'Error updating fee status');
                                }
                            })
                            .catch(error => {
                                console.error('Error toggling fee status:', error);
                                alert('Error updating fee status: ' + error.message);
                            });
                    });
                });
            });
        </script>
    @endpush
@endsection