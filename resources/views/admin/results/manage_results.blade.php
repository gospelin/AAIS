@extends('admin.layouts.app')

@section('title', 'Manage Results for {{ $student->first_name }} {{ $student->last_name }}')
@section('description', 'Manage student results for {{ $student->first_name }} {{ $student->last_name }} in {{ $class->name }} at {{ $school_name }}.')

@push('styles')
    <style>
        .content-container {
            max-width: 90rem;
            margin: 0 auto;
            padding: var(--space-lg) var(--space-md);
        }

        .card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-xl);
            padding: var(--space-xl);
            margin-bottom: var(--space-2xl);
        }

        .school-header {
            text-align: center;
            margin-bottom: var(--space-xl);
        }

        .school-header img {
            width: 100px;
            height: auto;
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

        .form-control {
            width: 100%;
            background: var(--bg-secondary);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-md);
            padding: var(--space-sm) var(--space-md);
            color: var(--text-primary);
            font-size: clamp(0.875rem, 2vw, 0.9375rem);
            transition: all 0.2s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-green);
            box-shadow: 0 0 0 3px rgba(33, 160, 85, 0.2);
        }

        .form-control[readonly] {
            background: var(--bg-tertiary);
            cursor: not-allowed;
        }

        .input-group {
            display: flex;
            align-items: center;
            gap: var(--space-sm);
        }

        .input-group-text {
            background: var(--bg-secondary);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-md);
            padding: var(--space-sm) var(--space-md);
            font-size: clamp(0.875rem, 2vw, 0.9375rem);
            color: var(--text-primary);
            display: flex;
            align-items: center;
        }

        .input-group-text i {
            margin-right: var(--space-xs);
        }

        .result-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: var(--space-lg);
        }

        .result-table th,
        .result-table td {
            padding: var(--space-md);
            font-size: clamp(0.875rem, 2vw, 0.9375rem);
            color: var(--text-primary);
            text-align: left;
            border-bottom: 1px solid var(--glass-border);
        }

        .result-table th {
            background: var(--bg-secondary);
            font-weight: 600;
        }

        .result-table tbody tr:hover {
            background: rgba(33, 160, 85, 0.1);
        }

        .action-btn {
            background: var(--gradient-primary);
            border: none;
            color: var(--white);
            font-size: clamp(0.875rem, 2vw, 0.9375rem);
            padding: var(--space-sm) var(--space-md);
            border-radius: var(--radius-md);
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .action-btn.saving {
            position: relative;
            pointer-events: none;
        }

        .action-btn.saving::after {
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

        .progress {
            background: var(--bg-secondary);
            border-radius: var(--radius-md);
            height: 20px;
            margin-bottom: var(--space-lg);
        }

        .progress-bar {
            background: var(--gradient-primary);
            border-radius: var(--radius-md);
            transition: width 0.3s ease;
        }

        .status-indicator {
            font-size: clamp(0.75rem, 2vw, 0.875rem);
            transition: opacity 0.3s ease;
        }

        .status-indicator.text-success {
            color: var(--primary-green);
        }

        .status-indicator.text-danger {
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

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        @media (max-width: 768px) {
            .content-container {
                padding: var(--space-md);
            }

            .result-table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }

            .input-group {
                flex-direction: column;
                align-items: flex-start;
            }

            .form-control,
            .input-group-text {
                width: 100%;
            }
        }
    </style>
@endpush

@section('content')
    <div class="content-container">
        <a href="{{ route('admin.students_by_class', ['className' => urlencode($class->name), 'action' => $action, 'session_id' => $currentSession->id, 'term' => $currentTerm->value]) }}"
            class="back-link">
            <i class="bx bx-arrow-back"></i> Back to Students
        </a>

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

        <div class="card">
            <div class="school-header">
                <img src="{{ asset('images/school_logo.png') }}" alt="School Logo" class="img-fluid">
            </div>

            <form id="resultsForm" method="POST" action="{{ route('admin.update_result', [
        'className' => urlencode($class->name),
        'studentId' => $student->id,
        'action' => $action,
        'session_id' => $currentSession->id,
        'term' => $currentTerm->value
    ]) }}">
                @csrf
                <div class="row mb-3">
                    <div class="col-md-6 form-group">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bx bx-user"></i> First Name</span>
                            <span class="form-control" aria-label="First Name">{{ $student->first_name }}</span>
                        </div>
                    </div>
                    <div class="col-md-6 form-group">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bx bx-user"></i> Last Name</span>
                            <span class="form-control" aria-label="Last Name">{{ $student->last_name }}</span>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6 form-group">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bx bx-calendar"></i> Term</span>
                            <span class="form-control" aria-label="Term">{{ $currentTerm->label() }}</span>
                        </div>
                    </div>
                    <div class="col-md-6 form-group">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bx bx-calendar"></i> Academic Session</span>
                            <span class="form-control" aria-label="Academic Session">{{ $currentSession->year }}</span>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6 form-group">
                        <div class="input-group">
                            <label class="input-group-text" for="next_term_begins"><i class="bx bx-calendar-event"></i>
                                Reopening Date</label>
                            <input type="date" id="next_term_begins" name="next_term_begins"
                                class="form-control result-input class-wide"
                                value="{{ old('next_term_begins', $termSummary->next_term_begins ?? '') }}">
                        </div>
                    </div>
                    <div class="col-md-6 form-group">
                        <div class="input-group">
                            <label class="input-group-text" for="date_issued"><i class="bx bx-calendar-check"></i> Date
                                Issued</label>
                            <input type="date" id="date_issued" name="date_issued"
                                class="form-control result-input class-wide"
                                value="{{ old('date_issued', $termSummary->date_issued ?? '') }}">
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6 form-group">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bx bx-building"></i> Class</span>
                            <span class="form-control" aria-label="Class">{{ $class->name }}</span>
                        </div>
                    </div>
                    <div class="col-md-6 form-group">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bx bx-sum"></i> Grand Total</span>
                            <span class="form-control aggregate-field"
                                aria-label="Grand Total">{{ $termSummary->grand_total ?? '' }}</span>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6 form-group">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bx bx-stats"></i> Last Term Average</span>
                            <span class="form-control aggregate-field"
                                aria-label="Last Term Average">{{ $termSummary->last_term_average ?? '' }}</span>
                        </div>
                    </div>
                    <div class="col-md-6 form-group">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bx bx-stats"></i> Cumulative Average</span>
                            <span class="form-control aggregate-field"
                                aria-label="Cumulative Average">{{ $termSummary->cumulative_average ?? '' }}</span>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6 form-group">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bx bx-stats"></i> Average for the Term</span>
                            <span class="form-control aggregate-field"
                                aria-label="Average for the Term">{{ $termSummary->term_average ?? '' }}</span>
                        </div>
                    </div>
                    <div class="col-md-6 form-group">
                        <div class="input-group">
                            <label class="input-group-text" for="position"><i class="bx bx-trophy"></i> Position</label>
                            <input type="text" id="position" name="position" class="form-control result-input class-wide"
                                value="{{ old('position', $termSummary->position ?? '') }}">
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6 form-group">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bx bx-comment"></i> Principal Remark</span>
                            <span class="form-control aggregate-field"
                                aria-label="Principal Remark">{{ $termSummary->principal_remark ?? '' }}</span>
                        </div>
                    </div>
                    <div class="col-md-6 form-group">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bx bx-comment"></i> Teacher Remark</span>
                            <span class="form-control aggregate-field"
                                aria-label="Teacher Remark">{{ $termSummary->teacher_remark ?? '' }}</span>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="result-table">
                        <thead>
                            <tr>
                                <th>Subject</th>
                                <th>Class Assessment (20)</th>
                                <th>Summative Test (20)</th>
                                <th>Examination (60)</th>
                                <th>Total</th>
                                <th>Grade</th>
                                <th>Remark</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($subjects as $index => $subject)
                                @php
                                    $result = $results->get($subject->id);
                                @endphp
                                <tr>
                                    <input type="hidden" name="results[{{ $index }}][subject_id]" value="{{ $subject->id }}">
                                    <input type="hidden" name="results[{{ $index }}][result_id]"
                                        value="{{ $result ? $result->id : '' }}"
                                        data-result-id="{{ $result ? $result->id : '' }}" data-subject-id="{{ $subject->id }}">
                                    <td>{{ $subject->name }}</td>
                                    <td>
                                        <input type="number" name="results[{{ $index }}][class_assessment]"
                                            class="form-control result-input class-assessment" min="0" max="20" step="1"
                                            value="{{ old('results.' . $index . '.class_assessment', $result && $result->class_assessment !== null ? $result->class_assessment : '') }}">
                                        @error('results.' . $index . '.class_assessment')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </td>
                                    <td>
                                        <input type="number" name="results[{{ $index }}][summative_test]"
                                            class="form-control result-input summative-test" min="0" max="20" step="1"
                                            value="{{ old('results.' . $index . '.summative_test', $result && $result->summative_test !== null ? $result->summative_test : '') }}">
                                        @error('results.' . $index . '.summative_test')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </td>
                                    <td>
                                        <input type="number" name="results[{{ $index }}][exam]"
                                            class="form-control result-input exam" min="0" max="60" step="1"
                                            value="{{ old('results.' . $index . '.exam', $result && $result->exam !== null ? $result->exam : '') }}">
                                        @error('results.' . $index . '.exam')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </td>
                                    <td>
                                        <span class="form-control total"
                                            aria-label="Total">{{ $result && $result->total !== null ? $result->total : '' }}</span>
                                    </td>
                                    <td>
                                        <span class="form-control grade"
                                            aria-label="Grade">{{ $result && $result->grade ? $result->grade : '' }}</span>
                                    </td>
                                    <td>
                                        <span class="form-control remark"
                                            aria-label="Remark">{{ $result && $result->remark ? $result->remark : '' }}</span>
                                    </td>
                                    <td>
                                        <span class="status-indicator" id="status_{{ $subject->id }}"></span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="progress">
                    <div id="resultProgressBar" class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0"
                        aria-valuemin="0" aria-valuemax="100">
                        0%
                    </div>
                </div>

                <button type="submit" class="action-btn" id="saveResultsBtn"><i class="bx bx-save"></i> Save
                    Results</button>
            </form>
        </div>
    </div>
    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                // GSAP Animations
                gsap.from('.card', { opacity: 0, y: 20, duration: 0.6 });
                gsap.from('.school-header', { opacity: 0, y: 20, duration: 0.6, delay: 0.2 });
                gsap.from('.form-group', { opacity: 0, y: 20, duration: 0.6, delay: 0.4, stagger: 0.1 });
                gsap.from('.result-table', { opacity: 0, y: 20, duration: 0.6, delay: 0.6 });
                gsap.from('.progress', { opacity: 0, y: 20, duration: 0.6, delay: 0.8 });
                gsap.from('.action-btn', { opacity: 0, y: 20, duration: 0.6, delay: 0.01 });

                const progressBar = document.getElementById('resultProgressBar');
                const saveButton = document.getElementById('saveResultsBtn');
                const updateUrl = '{{ route('admin.update_result_field') }}';
                const csrfToken = '{{ csrf_token() }}';

                // Update progress bar
                const updateProgress = () => {
                    const totalFields = $('.result-input:not(.class-wide)').length;
                    let filledFields = 0;

                    $('.result-input:not(.class-wide)').each(function () {
                        if ($(this).val().trim() !== '') {
                            filledFields++;
                        }
                    });

                    const progress = totalFields > 0 ? Math.round((filledFields / totalFields) * 100) : 0;
                    progressBar.style.width = `${progress}%`;
                    progressBar.setAttribute('aria-valuenow', progress);
                    progressBar.textContent = `${progress}%`;
                };

                // Update row aggregates
                const updateRowAggregates = ($row) => {
                    const classAssessment = parseFloat($row.find('.class-assessment').val()) || 0;
                    const summativeTest = parseFloat($row.find('.summative-test').val()) || 0;
                    const exam = parseFloat($row.find('.exam').val()) || 0;
                    const total = classAssessment + summativeTest + exam;
                    $row.find('.total').text(total > 0 ? total : '');
                    // Clear grade and remark if total is 0
                    $row.find('.grade').text(total > 0 ? calculateGrade(total) : '');
                    $row.find('.remark').text(total > 0 ? generateRemark(total) : '');
                    updateProgress();
                };

                // Calculate grade (matches server-side logic)
                const calculateGrade = (total) => {
                    if (total >= 70) return 'A';
                    if (total >= 60) return 'B';
                    if (total >= 50) return 'C';
                    if (total >= 40) return 'D';
                    return 'F';
                };

                // Generate remark (matches server-side logic)
                const generateRemark = (total) => {
                    if (total >= 70) return 'Excellent';
                    if (total >= 60) return 'Very Good';
                    if (total >= 50) return 'Good';
                    if (total >= 40) return 'Pass';
                    return 'Fail';
                };

                // Update all aggregates
                const updateAllAggregates = () => {
                    let grandTotal = 0;
                    let subjectCount = 0;

                    $('.result-table tbody tr').each(function () {
                        const $row = $(this);
                        const classAssessment = parseFloat($row.find('.class-assessment').val()) || 0;
                        const summativeTest = parseFloat($row.find('.summative-test').val()) || 0;
                        const exam = parseFloat($row.find('.exam').val()) || 0;
                        const total = classAssessment + summativeTest + exam;
                        $row.find('.total').text(total > 0 ? total : '');
                        $row.find('.grade').text(total > 0 ? calculateGrade(total) : '');
                        $row.find('.remark').text(total > 0 ? generateRemark(total) : '');
                        if (total > 0) {
                            grandTotal += total;
                            subjectCount++;
                        }
                    });

                    $('.aggregate-field[aria-label="Grand Total"]').text(grandTotal > 0 ? grandTotal : '');
                    $('.aggregate-field[aria-label="Average for the Term"]').text(subjectCount > 0 ? (grandTotal / subjectCount).toFixed(1) : '');
                };

                // Initialize row totals and aggregates on page load
                $('.result-table tbody tr').each(function () {
                    updateRowAggregates($(this));
                });
                updateAllAggregates();
                updateProgress();

                // Handle input changes for real-time updates
                $('.result-table tbody tr').each(function () {
                    const $row = $(this);
                    $row.find('.class-assessment, .summative-test, .exam').on('input', () => {
                        updateRowAggregates($row);
                        updateAllAggregates();
                    });
                });

                // AJAX for result fields
                let saveTimeout;
                let pendingRequests = {};

                $(document).on('blur', '.result-input', function () {
                    clearTimeout(saveTimeout);
                    const $input = $(this);
                    const $row = $input.closest('tr');
                    const $resultIdInput = $row.find('input[name*="result_id"]');
                    const resultId = $resultIdInput.val() || null;
                    const $subjectIdInput = $row.find('input[name*="subject_id"]');
                    let subjectId = $subjectIdInput.val() ? parseInt($subjectIdInput.val()) : null;

                    const field = $input.hasClass('class-assessment') ? 'class_assessment' :
                        $input.hasClass('summative-test') ? 'summative_test' :
                            $input.hasClass('exam') ? 'exam' :
                                $input.hasClass('class-wide') ? $input.attr('name') : null;

                    if (!field) {
                        console.warn('No valid field identified for input:', $input);
                        return;
                    }

                    if (!['next_term_begins', 'date_issued', 'position'].includes(field) && (!subjectId || isNaN(subjectId))) {
                        $row.find('.status-indicator').text('Invalid subject ID').addClass('text-danger').show().fadeOut(2000);
                        console.error('Invalid subject_id:', { subjectId, row: $row.html() });
                        return;
                    }

                    // Collect all fields for the subject
                    const classAssessment = $row.find('.class-assessment').val().trim() === '' ? null : parseFloat($row.find('.class-assessment').val()) || null;
                    const summativeTest = $row.find('.summative-test').val().trim() === '' ? null : parseFloat($row.find('.summative-test').val()) || null;
                    const exam = $row.find('.exam').val().trim() === '' ? null : parseFloat($row.find('.exam').val()) || null;
                    const isClassWide = $input.hasClass('class-wide');
                    const value = $input.val().trim() === '' ? null : (isClassWide ? $input.val() : parseFloat($input.val()) || null);

                    const requestData = {
                        result_id: resultId,
                        subject_id: isClassWide ? null : subjectId,
                        student_id: {{ $student->id }},
                        class_id: {{ $class->id }},
                        session_id: {{ $currentSession->id }},
                        term: '{{ $currentTerm->value }}',
                        field: field,
                        value: value,
                        class_assessment: isClassWide ? null : classAssessment,
                        summative_test: isClassWide ? null : summativeTest,
                        exam: isClassWide ? null : exam,
                        next_term_begins: $('#next_term_begins').val() || null,
                        date_issued: $('#date_issued').val() || null,
                        position: $('#position').val() || null
                    };

                    // Use subjectId as key to prevent race conditions for the same subject
                    const requestKey = isClassWide ? field : `subject_${subjectId}`;
                    if (pendingRequests[requestKey]) {
                        clearTimeout(pendingRequests[requestKey].timeout);
                        pendingRequests[requestKey].aborted = true;
                    }

                    saveTimeout = setTimeout(() => {
                        pendingRequests[requestKey] = { timeout: saveTimeout, aborted: false };
                        $row.find('.status-indicator').text('Saving...').addClass('text-success').show();

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
                                if (pendingRequests[requestKey].aborted) {
                                    return;
                                }
                                delete pendingRequests[requestKey];
                                if (response.success) {
                                    if (!isClassWide) {
                                        // Update client-side only if server total matches
                                        const classAssessmentVal = parseFloat($row.find('.class-assessment').val()) || 0;
                                        const summativeTestVal = parseFloat($row.find('.summative-test').val()) || 0;
                                        const examVal = parseFloat($row.find('.exam').val()) || 0;
                                        const expectedTotal = classAssessmentVal + summativeTestVal + examVal;

                                        $row.find('.status-indicator').text('Saved').addClass('text-success').show().fadeOut(2000);
                                    } else {
                                        $row.find('.status-indicator').text('Saved').addClass('text-success').show().fadeOut(2000);
                                    }

                                    // Update aggregated fields
                                    $('.aggregate-field[aria-label="Grand Total"]').text(response.grand_total !== null ? response.grand_total : '');
                                    $('.aggregate-field[aria-label="Average for the Term"]').text(response.term_average !== null ? response.term_average : '');
                                    $('.aggregate-field[aria-label="Cumulative Average"]').text(response.cumulative_average !== null ? response.cumulative_average : '');
                                    $('.aggregate-field[aria-label="Principal Remark"]').text(response.principal_remark || '');
                                    $('.aggregate-field[aria-label="Teacher Remark"]').text(response.teacher_remark || '');

                                    // Update result_id
                                    if (response.result_id) {
                                        $resultIdInput.val(response.result_id);
                                        $row.find(`input[name="results[${$row.index()}][result_id]"]`).val(response.result_id);
                                    }
                                    updateAllAggregates();
                                    updateProgress();
                                } else {
                                    $row.find('.status-indicator').text(response.message || 'Error saving').addClass('text-danger').show().fadeOut(2000);
                                }
                            },
                            error: function (xhr) {
                                if (pendingRequests[requestKey].aborted) {
                                    return;
                                }
                                delete pendingRequests[requestKey];
                                let errorMsg = 'Error saving result.';
                                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMsg = typeof xhr.responseJSON.message === 'object'
                                        ? Object.values(xhr.responseJSON.message).flat().join(', ')
                                        : xhr.responseJSON.message;
                                } else if (xhr.status === 429) {
                                    errorMsg = 'Too many requests. Please try again later.';
                                } else if (xhr.status === 500) {
                                    errorMsg = 'Server error occurred. Please contact support.';
                                }
                                $row.find('.status-indicator').text(errorMsg).addClass('text-danger').show().fadeOut(2000);
                                console.error('AJAX error:', xhr.responseText, { requestData: requestData });
                            }
                        });
                    }, 1000); // Increased debounce to 1000ms
                });

                // Form submission with spinner
                $('#resultsForm').on('submit', function (e) {
                    let hasErrors = false;
                    $('.result-input:not(.class-wide)').each(function () {
                        const $input = $(this);
                        const value = parseFloat($input.val()) || 0;
                        const field = $input.hasClass('class-assessment') ? 'Class Assessment' :
                            $input.hasClass('summative-test') ? 'Summative Test' :
                                $input.hasClass('exam') ? 'Examination' : '';
                        const max = $input.hasClass('class-assessment') || $input.hasClass('summative-test') ? 20 : 60;

                        if (value < 0 || value > max) {
                            $input.closest('td').find('.text-danger').remove();
                            $input.after(`<span class="text-danger">${field} must be between 0 and ${max}.</span>`);
                            hasErrors = true;
                        }
                    });

                    if (hasErrors) {
                        e.preventDefault();
                        alert('Please correct the input errors before submitting.');
                    } else {
                        saveButton.classList.add('saving');
                        saveButton.disabled = true;
                        saveButton.innerHTML = '<i class="bx bx-save"></i> Saving Results...';
                    }
                });

                // Clear error messages on input focus
                $('.result-input').on('focus', function () {
                    $(this).closest('td').find('.text-danger').remove();
                    $(this).closest('tr').find('.status-indicator').text('').removeClass('text-success text-danger');
                });
            });
        </script>
    @endpush
@endsection
