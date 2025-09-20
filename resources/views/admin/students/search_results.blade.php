<!-- Student Section -->
<div class="student-section">
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner"></div>
    </div>
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
    <div class="pagination">
        {{ $students->links('vendor.pagination.bootstrap-5') }}
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
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

            // Handle search form submission via AJAX
            if (searchForm) {
                searchForm.addEventListener('submit', (e) => {
                    e.preventDefault();
                    fetchStudents(searchForm.action, new URLSearchParams(new FormData(searchForm)).toString());
                });
            }

            // Handle pagination clicks via AJAX
            if (studentSection) {
                studentSection.addEventListener('click', (e) => {
                    const pageLink = e.target.closest('.page-link');
                    if (pageLink && !pageLink.closest('.active')) {
                        e.preventDefault();
                        const url = pageLink.href;
                        fetchStudents(url);
                    }
                });
            }

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
                        if (studentSection) {
                            studentSection.innerHTML = data.html;
                        }
                        hideLoading();
                    })
                    .catch(error => {
                        console.error('Error fetching students:', error);
                        alert('Error loading students: ' + error.message);
                        hideLoading();
                    });
            }

            // Toggle fee status
            if (studentSection) {
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
                                    const badge = button.closest('tr')?.querySelector('td:nth-child(8) .badge');
                                    if (badge) {
                                        badge.textContent = newStatus === 'paid' ? 'Paid' : 'Unpaid';
                                        badge.classList.toggle('badge-success', newStatus === 'paid');
                                        badge.classList.toggle('badge-danger', newStatus === 'unpaid');
                                    }
                                    alert(data.message);
                                } else {
                                    alert(data.message || 'Error updating fee status');
                                }
                                hideLoading();
                            })
                            .catch(error => {
                                console.error('Error toggling fee status:', error);
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
                                    const badge = button.closest('tr')?.querySelector('td:nth-child(9) .badge');
                                    if (badge) {
                                        badge.textContent = newStatus === 'approved' ? 'Approved' : 'Not Approved';
                                        badge.classList.toggle('badge-success', newStatus === 'approved');
                                        badge.classList.toggle('badge-danger', newStatus === 'unapproved');
                                    }
                                    alert(data.message);
                                } else {
                                    alert(data.message || 'Error updating approval status');
                                }
                                hideLoading();
                            })
                            .catch(error => {
                                console.error('Error toggling approval status:', error);
                                alert('Error updating approval status: ' + error.message);
                                hideLoading();
                            });
                    }
                });
            }
        });
    </script>
@endpush
