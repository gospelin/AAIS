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
                            <a href="{{ route('admin.students.edit', $student->id) }}" class="btn btn-sm btn-primary">
                                <i class="bx bx-edit"></i> Edit
                            </a>
                            <form action="{{ route('admin.students.destroy', $student->id) }}" method="POST"
                                style="display:inline;"
                                onsubmit="return confirm('Are you sure you want to permanently delete this student?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="bx bx-trash"></i> Delete
                                </button>
                            </form>
                            @if(!$student->approved)
                                <a href="{{ route('admin.student_approve', $student->id) }}" class="btn btn-sm btn-success">
                                    <i class="bx bx-check"></i> Approve
                                </a>
                            @endif
                            @if($isActiveInTerm)
                                <a href="{{ route('admin.student_mark_as_left', $student->id) }}" class="btn btn-sm btn-warning">
                                    <i class="bx bx-exit"></i> Mark as Left
                                </a>
                            @else
                                <a href="{{ route('admin.student_reenroll', $student->id) }}" class="btn btn-sm btn-info">
                                    <i class="bx bx-user-plus"></i> Re-enroll
                                </a>
                            @endif
                            <button type="button" class="btn btn-sm btn-secondary toggle-fee-status"
                                data-student-id="{{ $student->id }}" data-session-id="{{ $selectedSession->id }}"
                                data-term="{{ $currentTerm->value }}" data-status="{{ $hasPaid ? 'paid' : 'unpaid' }}">
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
        {{ $students->links() }}
    </div>
@endif
