@foreach($studentsClasses as $className => $students)
    <div class="table-container mt-4">
        <h3 class="table-header">{{ $className }}</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Reg No</th>
                    <th>Name</th>
                    <th>Class</th>
                    <th>Gender</th>
                    <th>Parent Phone</th>
                    <th>Enrollment</th>
                    <th>Fee Status</th>
                    <th>Approval</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($students as $index => $student)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $student->reg_no }}</td>
                        <td>{{ $student->full_name }}</td>
                        <td>{{ $student->getCurrentClass($currentSession->id, $currentTerm) ?? 'Unassigned' }}</td>
                        <td>{{ ucfirst($student->gender) }}</td>
                        <td>{{ $student->parent_phone_number ?? 'N/A' }}</td>
                        <td>
                            @if($student->classHistory->where('session_id', $currentSession->id)->where('is_active', true)->whereNull('leave_date')->count() > 0)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-danger">Inactive</span>
                            @endif
                        </td>
                        <td>
                            @if($student->feePayments->where('session_id', $currentSession->id)->where('term', $currentTerm->value)->where('has_paid_fee', true)->count() > 0)
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
                                @if($student->classHistory->where('session_id', $currentSession->id)->where('is_active', true)->whereNull('leave_date')->count() == 0)
                                    <a href="{{ route('admin.student_reenroll', $student->id) }}" class="btn btn-sm btn-info">
                                        <i class="bx bx-user-plus"></i> Re-enroll
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endforeach
