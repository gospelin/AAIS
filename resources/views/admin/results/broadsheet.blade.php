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

        .broadsheet-form {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-xl);
            padding: var(--space-xl);
            margin-bottom: var(--space-2xl);
            position: relative;
        }

        .broadsheet-form::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gradient-primary);
        }

        .form-header {
            font-family: var(--font-display);
            font-size: clamp(1.25rem, 3vw, 1.5rem);
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: var(--space-md);
        }

        .broadsheet-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: var(--space-lg);
        }

        .broadsheet-table th,
        .broadsheet-table td {
            padding: var(--space-md);
            font-size: clamp(0.875rem, 2vw, 0.9375rem);
            color: var(--text-primary);
            text-align: left;
        }

        .broadsheet-table th {
            background: var(--glass-bg);
            font-weight: 600;
        }

        .broadsheet-table tr {
            border-bottom: 1px solid var(--glass-border);
        }

        .broadsheet-table tr:last-child {
            border-bottom: none;
        }

        .broadsheet-table tbody tr:hover {
            background: rgba(33, 160, 85, 0.1);
        }

        .form-input {
            width: 100%;
            background: var(--bg-secondary);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-md);
            padding: var(--space-sm) var(--space-md);
            color: var(--text-primary);
            font-size: clamp(0.875rem, 2vw, 0.9375rem);
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary-green);
            box-shadow: 0 0 0 3px rgba(33, 160, 85, 0.2);
        }

        .btn-submit,
        .btn-download {
            background: var(--gradient-primary);
            border: none;
            color: var(--white);
            padding: var(--space-md) var(--space-lg);
            border-radius: var(--radius-md);
            cursor: pointer;
            font-size: clamp(0.875rem, 2vw, 0.9375rem);
            margin-right: var(--space-md);
        }

        .btn-submit:hover,
        .btn-download:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
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

        .loading-overlay {
            position: fixed;
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
            .broadsheet-table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }

            .broadsheet-table th,
            .broadsheet-table td {
                padding: var(--space-sm);
                font-size: clamp(0.75rem, 2vw, 0.875rem);
            }

            .content-container {
                padding: var(--space-md);
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
            <a href="{{ route('admin.students_by_class', ['className' => urlencode($class->name), 'action' => $action]) }}"
                class="back-link">
                <i class="bx bx-arrow-back"></i> Back to Students in {{ $class->name }}
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

            <div class="broadsheet-form">
                <h3 class="form-header">Broadsheet for {{ $class->name }} - {{ $currentSession->year }}
                    {{ $currentTerm->value }}
                </h3>
                <form id="broadsheetForm"
                    action="{{ route('admin.update_broadsheet', ['className' => urlencode($class->name), 'action' => $action]) }}"
                    method="POST">
                    @csrf
                    @method('PUT')
                    <div class="loading-overlay" id="loadingOverlay">
                        <div class="loading-spinner"></div>
                    </div>
                    <table class="broadsheet-table">
                        <thead>
                            <tr>
                                <th>Reg No</th>
                                <th>Student Name</th>
                                @foreach ($subjects as $subject)
                                    <th>{{ $subject->name }} (Total)</th>
                                    <th>{{ $subject->name }} (Grade)</th>
                                @endforeach
                                <th>Grand Total</th>
                                <th>Term Average</th>
                                <th>Principal Remark</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($broadsheetData as $data)
                                @php
        $student = $data['student'];
        $results = $data['results'];
        $termSummary = $data['termSummary'];
                                @endphp
                                <tr>
                                    <td>{{ $student->reg_no }}</td>
                                    <td>{{ $student->full_name }}</td>
                                    @foreach ($subjects as $subject)
                                        @php
            $result = $results->get($subject->id);
                                        @endphp
                                        <td>
                                            <input type="number"
                                                name="results[{{ $student->id }}][subjects][{{ $subject->id }}][class_assessment]"
                                                class="form-input" value="{{ $result ? $result->class_assessment : '' }}" min="0"
                                                max="20" step="0.1">
                                            <input type="number"
                                                name="results[{{ $student->id }}][subjects][{{ $subject->id }}][summative_test]"
                                                class="form-input" value="{{ $result ? $result->summative_test : '' }}" min="0" max="20"
                                                step="0.1">
                                            <input type="number" name="results[{{ $student->id }}][subjects][{{ $subject->id }}][exam]"
                                                class="form-input" value="{{ $result ? $result->exam : '' }}" min="0" max="60"
                                                step="0.1">
                                            <input type="hidden"
                                                name="results[{{ $student->id }}][subjects][{{ $subject->id }}][subject_id]"
                                                value="{{ $subject->id }}">
                                            <input type="hidden" name="results[{{ $student->id }}][student_id]"
                                                value="{{ $student->id }}">
                                            <span>{{ $result ? $result->total : '-' }}</span>
                                        </td>
                                        <td>{{ $result ? $result->grade : '-' }}</td>
                                    @endforeach
                                    <td>{{ $termSummary ? $termSummary->grand_total : '-' }}</td>
                                    <td>{{ $termSummary ? number_format($termSummary->term_average, 2) : '-' }}</td>
                                    <td>{{ $termSummary ? $termSummary->principal_remark : '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <button type="submit" class="btn-submit">Update Broadsheet</button>
                    <a href="{{ route('admin.download_broadsheet', ['className' => urlencode($class->name), 'action' => $action]) }}"
                        class="btn-download">
                        <i class="bx bx-download"></i> Download Broadsheet
                    </a>
                </form>
            </div>
        @endif
    </div>
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // GSAP Animations
            gsap.from('.broadsheet-form', { opacity: 0, y: 20, duration: 0.6 });

            const broadsheetForm = document.getElementById('broadsheetForm');
            const loadingOverlay = document.getElementById('loadingOverlay');

            if (broadsheetForm) {
                broadsheetForm.addEventListener('submit', (e) => {
                    loadingOverlay.style.display = 'flex';
                });
            }
        });
    </script>
@endpush
@endsection
