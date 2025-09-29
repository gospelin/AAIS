@extends('admin.layouts.app')

@section('title', 'Broadsheet for {{ $class->name }}')
@section('description', 'View and manage broadsheet for {{ $class->name }} at Aunty Anne\'s International School.')

@push('styles')
    <style>
        .content-container {
            max-width: 90rem;
            margin: 0 auto;
            padding: var(--space-lg) var(--space-md);
        }

        .table-container {
            overflow-x: auto;
            margin-top: var(--space-md);
            position: relative;
        }

        .broadsheet-form {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-xl);
            padding: var(--space-xl);
            margin-bottom: var(--space-2xl);
        }

        .form-header {
            font-family: var(--font-display);
            font-size: clamp(1.25rem, 3vw, 1.5rem);
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: var(--space-md);
            text-align: center;
        }

        .broadsheet-table .subjects-col {
            @if (count($broadsheetData) >= 5)
                position: sticky;
                left: 0;
                background-color: var(--bg-secondary);
                z-index: 2;
            @endif
            max-width: 150px;
            min-width: 120px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .broadsheet-table .subjects-col:first-child {
            z-index: 3;
        }

        .narrow-column {
            max-width: 200px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .broadsheet-table {
            table-layout: auto;
            width: 100%;
            min-width: 1200px;
            border-collapse: collapse;
        }

        .broadsheet-table th,
        .broadsheet-table td {
            padding: var(--space-sm);
            font-size: clamp(0.85rem, 2vw, 0.875rem);
            color: var(--text-primary);
            text-align: center;
            vertical-align: middle;
            border-bottom: 1px solid var(--glass-border);
            min-width: 70px;
            max-width: 120px;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .broadsheet-table th {
            background: var(--glass-bg);
            font-weight: 600;
        }

        .broadsheet-table tbody tr:hover {
            background: rgba(33, 160, 85, 0.1);
        }

        .form-input {
            width: 100%;
            background: var(--bg-secondary);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-md);
            padding: 4px;
            color: var(--text-primary);
            font-size: clamp(0.85rem, 2vw, 0.875rem);
            text-align: center;
            box-sizing: border-box;
            max-width: 90px;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary-green);
            box-shadow: 0 0 0 3px rgba(33, 160, 85, 0.2);
        }

        .status-indicator {
            min-width: 40px;
            max-width: 60px;
            font-size: clamp(0.75rem, 2vw, 0.8rem);
            padding: 4px;
            transition: color 0.3s ease;
            white-space: nowrap;
            position: relative;
            z-index: 1;
        }

        .status-indicator.text-success {
            color: var(--primary-green);
        }

        .status-indicator.text-danger {
            color: var(--error);
        }

        .btn-submit,
        .btn-download,
        .btn-back {
            background: var(--gradient-primary);
            border: none;
            color: var(--white);
            padding: var(--space-sm) var(--space-md);
            border-radius: var(--radius-md);
            cursor: pointer;
            font-size: clamp(0.9rem, 2vw, 0.95rem);
            margin: var(--space-sm);
            display: inline-flex;
            align-items: center;
        }

        .btn-submit:hover,
        .btn-download:hover,
        .btn-back:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .btn-submit.saving {
            position: relative;
            pointer-events: none;
        }

        .btn-submit.saving::after {
            content: '';
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid var(--white);
            border-top: 2px solid var(--primary-green);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-left: var(--space-sm);
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            color: var(--primary-green);
            font-size: clamp(0.875rem, 2vw, 0.9375rem);
            margin-bottom: var(--space-md);
            text-decoration: none;
        }

        .back-link:hover {
            text-decoration: underline;
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

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @media (max-width: 768px) {
            .content-container {
                padding: var(--space-md);
            }

            .broadsheet-table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }

            .broadsheet-table th,
            .broadsheet-table td {
                font-size: clamp(0.8rem, 2vw, 0.85rem);
                padding: 3px;
                min-width: 50px;
            }

            .form-input {
                font-size: clamp(0.8rem, 2vw, 0.85rem);
                padding: 3px;
                max-width: 80px;
            }

            .status-indicator {
                font-size: clamp(0.7rem, 2vw, 0.75rem);
                min-width: 30px;
                max-width: 50px;
            }

            .subjects-col {
                font-weight: bold;
                text-align: left;
                background-color: var(--bg-secondary);
                padding-left: 6px;
                z-index: 2;
            }

            .form-header {
                font-size: clamp(1.2rem, 3vw, 1.4rem);
            }

            .btn-submit,
            .btn-download,
            .btn-back {
                font-size: clamp(0.85rem, 2vw, 0.9rem);
                padding: 6px 12px;
            }
        }

        @media (max-width: 576px) {
            .broadsheet-table th,
            .broadsheet-table td {
                font-size: clamp(0.75rem, 2vw, 0.8rem);
                padding: 2px;
                min-width: 40px;
            }

            .form-input {
                font-size: clamp(0.75rem, 2vw, 0.8rem);
                padding: 2px;
                max-width: 70px;
            }

            .status-indicator {
                font-size: clamp(0.65rem, 2vw, 0.7rem);
                min-width: 25px;
                max-width: 40px;
            }

            .subjects-col {
                font-weight: bold;
                text-align: left;
                background-color: var(--bg-secondary);
                padding-left: 4px;
                font-size: clamp(0.6rem, 2vw, 0.65rem);
                z-index: 2;
            }

            .form-header {
                font-size: clamp(1rem, 3vw, 1.2rem);
            }

            .btn-submit,
            .btn-download,
            .btn-back {
                font-size: clamp(0.75rem, 2vw, 0.8rem);
                padding: 5px 10px;
            }

            .narrow-column {
                max-width: 80px;
                font-size: clamp(0.75rem, 2vw, 0.8rem);
                padding: 2px;
            }
        }
    </style>
@endpush

@section('content')
        <div class="content-container">
            @if (!$currentSession || !$currentTerm)
                <div class="alert alert-danger">
                    No academic session or term is set. Please <a
                        href="{{ route('admin.select_class', ['action' => $action]) }}">select a class</a>.
                </div>
            @else
                <div class="mb-3">
                    <a href="{{ route('admin.students_by_class', ['className' => urlencode($class->name), 'action' => $action, 'session_id' => $currentSession->id, 'term' => $currentTerm->value]) }}"
                       class="back-link">
                        <i class="bx bx-arrow-back"></i> Back to Students in {{ $class->name }}
                    </a>
                    <a href="{{ route('admin.select_class', ['action' => $action]) }}"
                       class="back-link">
                        <i class="bx bx-arrow-back"></i> Back to Classes
                    </a>
                </div>

                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="broadsheet-form">
                    <h3 class="form-header">Broadsheet for {{ $class->name }} - Session: {{ $currentSession->year }}, Term: {{ $currentTerm->label() }}</h3>

                    <form id="broadsheetForm" method="POST"
                          action="{{ route('admin.update_broadsheet', ['className' => urlencode($class->name), 'action' => $action, 'session_id' => $currentSession->id, 'term' => $currentTerm->value]) }}">
                        @csrf
                        @method('PUT')

                        <!-- Class-wide date inputs -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="next_term_begins" class="form-label">Next Term Begins:</label>
                                <input type="text" id="next_term_begins" name="next_term_begins"
                                       class="form-input date-input class-wide next-term-begins"
                                       value="{{ old('next_term_begins', $broadsheetData[0]['termSummary']->next_term_begins ?? '') }}"
                                       data-class-id="{{ $class->id }}">
                            </div>
                            <div class="col-md-6">
                                <label for="date_issued" class="form-label">Date Issued:</label>
                                <input type="text" id="date_issued" name="date_issued"
                                       class="form-input date-input class-wide date-issued"
                                       value="{{ old('date_issued', $broadsheetData[0]['termSummary']->date_issued ?? '') }}"
                                       data-class-id="{{ $class->id }}">
                            </div>
                        </div>

                        <div class="table-container">
                            <table class="broadsheet-table">
                                <thead>
                                    <tr>
                                        <th class="subjects-col @if (count($broadsheetData) < 5) narrow-column @endif" rowspan="2">Subjects</th>
                                        @foreach ($broadsheetData as $index => $data)
                                            <th colspan="5">{{ $data['student']->first_name }} {{ $data['student']->last_name }}</th>
                                        @endforeach
                                        <th>Class Average</th>
                                    </tr>
                                    <tr>
                                        @foreach ($broadsheetData as $data)
                                            <th>C/A</th>
                                            <th>S/T</th>
                                            <th>Exam</th>
                                            <th>Total</th>
                                            <th>Status</th>
                                        @endforeach
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($subjects as $subject)
                                        <tr data-subject-id="{{ $subject->id }}">
                                            <td class="subjects-col @if (count($broadsheetData) < 5) narrow-column @endif">{{ $subject->name }}</td>
                                            @foreach ($broadsheetData as $index => $data)
                                                @php
                $student = $data['student'];
                $result = $data['results']->get($subject->id);
                                                @endphp
                                                <td>
                                                    <input type="number"
                                                           name="results[{{$student->id}}][subjects][{{ $subject->id }}][class_assessment]"
                                                           class="form-input result-input class-assessment"
                                                           value="{{ old('results.' . $student->id . '.subjects.' . $subject->id . '.class_assessment', $result && $result->class_assessment !== null ? $result->class_assessment : '') }}"
                                                           min="0" max="20" step="1"
                                                           data-student-id="{{ $student->id }}"
                                                           data-subject-id="{{ $subject->id }}"
                                                           data-class-id="{{ $class->id }}">
                                                </td>
                                                <td>
                                                    <input type="number"
                                                           name="results[{{$student->id}}][subjects][{{ $subject->id }}][summative_test]"
                                                           class="form-input result-input summative-test"
                                                           value="{{ old('results.' . $student->id . '.subjects.' . $subject->id . '.summative_test', $result && $result->summative_test !== null ? $result->summative_test : '') }}"
                                                           min="0" max="20" step="1"
                                                           data-student-id="{{ $student->id }}"
                                                           data-subject-id="{{ $subject->id }}"
                                                           data-class-id="{{ $class->id }}">
                                                </td>
                                                <td>
                                                    <input type="number"
                                                           name="results[{{$student->id}}][subjects][{{ $subject->id }}][exam]"
                                                           class="form-input result-input exam"
                                                           value="{{ old('results.' . $student->id . '.subjects.' . $subject->id . '.exam', $result && $result->exam !== null ? $result->exam : '') }}"
                                                           min="0" max="60" step="1"
                                                           data-student-id="{{ $student->id }}"
                                                           data-subject-id="{{ $subject->id }}"
                                                           data-class-id="{{ $class->id }}">
                                                </td>
                                                <td class="total" data-student-id="{{ $student->id }}">{{ $result && $result->total !== null ? $result->total : '' }}</td>
                                                <td class="status-indicator" data-status-id="{{ $student->id }}_{{ $subject->id }}"></td>
                                            @endforeach
                                            <td>{{ isset($subjectAverages[$subject->id]['average']) && $subjectAverages[$subject->id]['average'] !== null ? number_format($subjectAverages[$subject->id]['average'], 2) : 'N/A' }}</td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td class="subjects-col @if (count($broadsheetData) < 5) narrow-column @endif">Grand Total</td>
                                        @foreach ($broadsheetData as $data)
                                            <td colspan="5" class="grand-total" data-student-id="{{ $data['student']->id }}">{{ $data['termSummary'] && $data['termSummary']->grand_total !== null ? $data['termSummary']->grand_total : '' }}</td>
                                        @endforeach
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td class="subjects-col @if (count($broadsheetData) < 5) narrow-column @endif">Average</td>
                                        @foreach ($broadsheetData as $data)
                                            <td colspan="5" class="term-average" data-student-id="{{ $data['student']->id }}">{{ $data['termSummary'] && $data['termSummary']->term_average !== null ? number_format($data['termSummary']->term_average, 2) : '' }}</td>
                                        @endforeach
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td class="subjects-col @if (count($broadsheetData) < 5) narrow-column @endif">Cumulative Average</td>
                                        @foreach ($broadsheetData as $data)
                                            <td colspan="5" class="cumulative-average" data-student-id="{{ $data['student']->id }}">{{ $data['termSummary'] && $data['termSummary']->cumulative_average !== null ? number_format($data['termSummary']->cumulative_average, 2) : '' }}</td>
                                        @endforeach
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td class="subjects-col @if (count($broadsheetData) < 5) narrow-column @endif">Position</td>
                                        @foreach ($broadsheetData as $data)
                                            <td colspan="4">
                                                <input type="text"
                                                       name="results[{{$data['student']->id}}][position]"
                                                       class="form-input position-input class-wide"
                                                       value="{{ old('results.' . $data['student']->id . '.position', $data['termSummary'] && $data['termSummary']->position !== null ? $data['termSummary']->position : '') }}"
                                                       data-student-id="{{ $data['student']->id }}"
                                                       data-class-id="{{ $class->id }}">
                                            </td>
                                            <td class="status-indicator" data-status-id="{{ $data['student']->id }}_position"></td>
                                        @endforeach
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td class="subjects-col @if (count($broadsheetData) < 5) narrow-column @endif">Principal Remark</td>
                                        @foreach ($broadsheetData as $data)
                                            <td colspan="5" class="principal-remark" data-student-id="{{ $data['student']->id }}">{{ $data['termSummary'] ? $data['termSummary']->principal_remark : '' }}</td>
                                        @endforeach
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td class="subjects-col @if (count($broadsheetData) < 5) narrow-column @endif">Teacher Remark</td>
                                        @foreach ($broadsheetData as $data)
                                            <td colspan="5" class="teacher-remark" data-student-id="{{ $data['student']->id }}">{{ $data['termSummary'] ? $data['termSummary']->teacher_remark : '' }}</td>
                                        @endforeach
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            <button type="submit" class="btn-submit" id="saveAllChangesBtn">Save All Changes</button>
                            <a href="{{ route('admin.download_broadsheet', ['className' => urlencode($class->name), 'action' => $action, 'session_id' => $currentSession->id, 'term' => $currentTerm->value]) }}"
                               class="btn-download">
                                <i class="bx bx-download"></i> Download Broadsheet
                            </a>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                // GSAP Animations
                gsap.from('.broadsheet-form', { opacity: 0, y: 20, duration: 0.6 });
                gsap.from('.form-header', { opacity: 0, y: 20, duration: 0.6, delay: 0.2 });
                gsap.from('.table-container', { opacity: 0, y: 20, duration: 0.6, delay: 0.4 });
                gsap.from('.btn-submit, .btn-download, .btn-back', { opacity: 0, y: 20, duration: 0.6, delay: 0.6 });

                const updateUrl = '{{ route('admin.update_result_field') }}';
                const csrfToken = '{{ csrf_token() }}';
                let saveTimeout;
                let pendingRequests = {};

              // Validate input based on field type
                const validateInput = ($input) => {
                    if ($input.hasClass('class-wide') && !$input.hasClass('date-input')) {
                        return ''; // Position inputs don't need range validation
                    }
                    if ($input.hasClass('date-input')) {
                        return $input.val().trim() === '' ? '' : /^\d{4}-\d{2}-\d{2}$/.test($input.val()) ? '' : 'Invalid date format.';
                    }
                    if ($input.val().trim() === '') {
                        return ''; // Allow empty inputs
                    }
                    const value = parseFloat($input.val()) || 0;
                    const field = $input.hasClass('class-assessment') ? 'Class Assessment' :
                        $input.hasClass('summative-test') ? 'Summative Test' :
                            $input.hasClass('exam') ? 'Examination' : '';
                    const max = $input.hasClass('class-assessment') || $input.hasClass('summative-test') ? 20 : 60;
                    return field && (value < 0 || value > max) ? `${field} must be between 0 and ${max}.` : '';
                };

                // Unified input handler for result, position, and date inputs
                $(document).on('blur', '.form-input', function () {
                    clearTimeout(saveTimeout);
                    const $input = $(this);
                    const $row = $input.closest('tr');
                    const studentId = $input.data('student-id');
                    const subjectId = $input.data('subject-id');
                    const classId = $input.data('class-id');
                    const isDateInput = $input.hasClass('date-input');
                    const isPositionInput = $input.hasClass('position-input');

                    // Validate input
                    const error = validateInput($input);
                    if (error) {
                        const statusId = isDateInput ? 'class_date' : isPositionInput ? `${studentId}_position` : `${studentId}_${subjectId}`;
                        $row.find(`.status-indicator[data-status-id="${statusId}"]`).text(error).addClass('text-danger').show().fadeOut(2000);
                        return;
                    }

                    // Determine field and value
                    let field, value, statusId, requestKey;
                    if (isDateInput) {
                        field = $input.hasClass('next-term-begins') ? 'next_term_begins' : 'date_issued';
                        value = $input.val().trim() === '' ? null : $input.val();
                        statusId = 'class_date';
                        requestKey = `class_${classId}_${field}`;
                    } else if (isPositionInput) {
                        field = 'position';
                        value = $input.val().trim() === '' ? null : $input.val();
                        statusId = `${studentId}_position`;
                        requestKey = `position_${studentId}`;
                    } else {
                        field = $input.hasClass('class-assessment') ? 'class_assessment' :
                            $input.hasClass('summative-test') ? 'summative_test' :
                                $input.hasClass('exam') ? 'exam' : null;
                        value = $input.val().trim() === '' ? null : parseFloat($input.val()) || null;
                        statusId = `${studentId}_${subjectId}`;
                        requestKey = `student_${studentId}_subject_${subjectId}`;
                    }

                    if (!field) {
                        console.warn('No valid field identified for input:', $input);
                        return;
                    }

                    if (!isDateInput && (!studentId || !classId || (!isPositionInput && !subjectId))) {
                        console.error('Missing required IDs:', { studentId, subjectId, classId });
                        $row.find(`.status-indicator[data-status-id="${statusId}"]`).text('✗').addClass('text-danger').show().fadeOut(2000);
                        return;
                    }

                    const requestData = {
                        student_id: isDateInput ? null : studentId,
                        subject_id: isPositionInput || isDateInput ? null : subjectId,
                        class_id: classId,
                        session_id: {{ $currentSession->id }},
                        term: '{{ $currentTerm->value }}',
                        field: field,
                        value: value
                    };

                    if (pendingRequests[requestKey]) {
                        clearTimeout(pendingRequests[requestKey].timeout);
                        pendingRequests[requestKey].aborted = true;
                    }

                    saveTimeout = setTimeout(() => {
                        pendingRequests[requestKey] = { timeout: saveTimeout, aborted: false };
                        const $statusCell = isDateInput ? $('.status-indicator').first() : $row.find(`.status-indicator[data-status-id="${statusId}"]`);
                        $statusCell.text('Saving...').addClass('text-success').show();

                        $.ajax({
                            url: updateUrl,
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            },
                            data: JSON.stringify(requestData),
                            success: function (response) {
                                if (pendingRequests[requestKey].aborted) return;
                                delete pendingRequests[requestKey];

                                if (response.success) {
                                    if (!isDateInput && !isPositionInput) {
                                        $row.find(`.total[data-student-id="${studentId}"]`).text(response.total !== null ? response.total : '');
                                        $row.find(`.class-assessment[data-student-id="${studentId}"]`).val(response.class_assessment !== null ? response.class_assessment : '');
                                        $row.find(`.summative-test[data-student-id="${studentId}"]`).val(response.summative_test !== null ? response.summative_test : '');
                                        $row.find(`.exam[data-student-id="${studentId}"]`).val(response.exam !== null ? response.exam : '');
                                        $(`.grand-total[data-student-id="${studentId}"]`).text(response.grand_total !== null ? response.grand_total : '');
                                        $(`.term-average[data-student-id="${studentId}"]`).text(response.term_average !== null ? Number(response.term_average).toFixed(2) : '');
                                        $(`.cumulative-average[data-student-id="${studentId}"]`).text(response.cumulative_average !== null ? Number(response.cumulative_average).toFixed(2) : '');
                                        $(`.principal-remark[data-student-id="${studentId}"]`).text(response.principal_remark || '');
                                        $(`.teacher-remark[data-student-id="${studentId}"]`).text(response.teacher_remark || '');
                                    } else if (isPositionInput) {
                                        $input.val(response.position !== null ? response.position : '');
                                    }
                                    $statusCell.text('✓').addClass('text-success').show();
                                } else {
                                    $statusCell.text(response.message || 'Error saving').addClass('text-danger').show().fadeOut(2000);
                                }
                            },
                            error: function (xhr) {
                                if (pendingRequests[requestKey].aborted) return;
                                delete pendingRequests[requestKey];
                                let errorMsg = isDateInput ? 'Error saving class-wide data.' : isPositionInput ? 'Error saving position.' : 'Error saving result.';
                                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMsg = typeof xhr.responseJSON.message === 'object'
                                        ? Object.values(xhr.responseJSON.message).flat().join(', ')
                                        : xhr.responseJSON.message;
                                } else if (xhr.status === 429) {
                                    errorMsg = 'Too many requests. Please try again later.';
                                } else if (xhr.status === 500) {
                                    errorMsg = 'Server error occurred. Please contact support.';
                                }
                                $statusCell.text(errorMsg).addClass('text-danger').show().fadeOut(2000);
                                console.error('AJAX error:', xhr.responseText, { requestData });
                            }
                        });
                    }, 1000);
                });

                // Form submission with validation
                $('#broadsheetForm').on('submit', function (e) {
                    let hasErrors = false;
                    $('.form-input').each(function () {
                        const $input = $(this);
                        const error = validateInput($input);
                        if (error) {
                            $input.after(`<span class="text-danger">${error}</span>`);
                            hasErrors = true;
                        }
                    });

                    if (hasErrors) {
                        e.preventDefault();
                        alert('Please correct the input errors before submitting.');
                    } else {
                        const saveButton = $('#saveAllChangesBtn');
                        saveButton.addClass('saving').prop('disabled', true).html('<i class="bx bx-save"></i> Saving All Changes...');
                    }
                });

                // Clear error messages on input focus
                $('.form-input').on('focus', function () {
                    $(this).next('.text-danger').remove();
                    const studentId = $(this).data('student-id');
                    const subjectId = $(this).data('subject-id');
                    const isDateInput = $(this).hasClass('date-input');
                    const isPositionInput = $(this).hasClass('position-input');
                    const statusId = isDateInput ? 'class_date' : isPositionInput ? `${studentId}_position` : `${studentId}_${subjectId}`;
                    const $statusCell = isDateInput ? $('.status-indicator').first() : $(this).closest('tr').find(`.status-indicator[data-status-id="${statusId}"]`);
                    $statusCell.text('').removeClass('text-success text-danger');
                });
            });
        </script>
    @endpush
@endsection
