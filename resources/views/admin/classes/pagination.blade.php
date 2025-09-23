<table class="student-table">
    <thead>
        <tr>
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
                        data-student-id="{{ $student->id }}" data-session-id="{{ $selectedSession->id }}"
                        data-term="{{ $currentTerm->value }}" data-status="{{ $hasPaid ? 'paid' : 'unpaid' }}">
                        <i class="bx bx-wallet"></i>
                        {{ $hasPaid ? 'Mark as Unpaid' : 'Mark as Paid' }}
                    </button>
                    <button type="button"
                        class="btn btn-sm {{ $student->approved ? 'btn-warning' : 'btn-success' }} toggle-approval-status action-btn"
                        data-student-id="{{ $student->id }}" data-status="{{ $student->approved ? 'approved' : 'unapproved' }}">
                        <i class="bx bx-check"></i>
                        {{ $student->approved ? 'Unapprove' : 'Approve' }}
                    </button>
                    <a href="{{ route('admin.manage_result', ['className' => urlencode($class->name), 'studentId' => $student->id, 'action' => 'manage_result', 'session_id' => $selectedSession->id, 'term' => $currentTerm->value]) }}"
                       class="btn btn-sm btn-primary action-btn">
                        <i class="bx bx-file"></i> Manage Results
                    </a>
                    @elseif($action == 'promote')
                    <button type="button" class="btn btn-sm btn-primary action-btn open-promotion-modal"
                        data-student-id="{{ $student->id }}"
                        data-student-name="{{ $student->first_name }} {{ $student->last_name }}"
                        data-class-name="{{ urlencode($class->name) }}" data-session-id="{{ $selectedSession->id }}"
                        data-term="{{ $currentTerm->value }}" data-action="{{ $action }}">
                        <i class="bx bx-up-arrow-alt"></i> Promote
                    </button>
                    @elseif($action == 'demote')
                    <button type="button" class="btn btn-sm btn-warning action-btn open-promotion-modal"
                        data-student-id="{{ $student->id }}"
                        data-student-name="{{ $student->first_name }} {{ $student->last_name }}"
                        data-class-name="{{ urlencode($class->name) }}" data-session-id="{{ $selectedSession->id }}"
                        data-term="{{ $currentTerm->value }}" data-action="{{ $action }}">
                        <i class="bx bx-down-arrow-alt"></i> Demote
                    </button>
                    @elseif($action == 'delete_from_class')
                    <a href="{{ route('admin.delete_student_class_record', ['className' => urlencode($class->name), 'studentId' => $student->id, 'action' => $action, 'session_id' => $selectedSession->id, 'term' => $currentTerm->value]) }}"
                        class="btn btn-sm btn-danger action-btn"
                        onclick="return confirm('Are you sure you want to delete this student\'s class record?');">
                        <i class="bx bx-trash"></i> Delete
                    </a>
                    @elseif($action == 'manage_result')
                    <a href="{{ route('admin.manage_result', ['className' => urlencode($class->name), 'studentId' => $student->id, 'action' => 'manage_result', 'session_id' => $selectedSession->id, 'term' => $currentTerm->value]) }}"
                       class="btn btn-sm btn-primary action-btn">
                        <i class="bx bx-file"></i> Manage Results
                    </a>
                    @endif
                </div>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
