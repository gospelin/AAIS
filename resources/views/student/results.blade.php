@extends('student.layouts.app')

@section('title', 'View Results')

@section('description', 'View your academic results for different sessions and terms at Aunty Anne\'s International School.')

@push('styles')
    <style>
        .content-container {
            max-width: 90rem;
            margin: 0 auto;
            padding: var(--space-lg) var(--space-md);
        }

        .filter-section {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-xl);
            padding: var(--space-lg);
            margin-bottom: var(--space-2xl);
            box-shadow: var(--shadow-lg);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .filter-section:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-2xl);
        }

        .section-title {
            font-family: var(--font-display);
            font-size: clamp(1.25rem, 3vw, 1.5rem);
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: var(--space-md);
            letter-spacing: 0.02em;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .section-title .session-term {
            flex: 1;
            text-align: left;
        }

        .section-title .class-info {
            flex: 1;
            text-align: right;
        }

        .form-label {
            font-size: clamp(0.875rem, 2vw, 1rem);
            color: var(--text-secondary);
            margin-bottom: var(--space-xs);
        }

        .form-select {
            background: var(--bg-secondary);
            color: var(--text-primary);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-md);
            padding: var(--space-sm);
            font-size: clamp(0.875rem, 2vw, 1rem);
            transition: border-color 0.2s ease, background 0.2s ease;
        }

        .form-select:focus {
            border-color: var(--primary-green);
            outline: none;
            box-shadow: 0 0 0 3px rgba(33, 160, 85, 0.2);
        }

        .results-section {
            background: var(--bg-secondary);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-xl);
            overflow: hidden;
            min-height: 200px;
            box-shadow: var(--shadow-lg);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .results-section:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-2xl);
        }

        .section-header {
            background: var(--gradient-primary);
            padding: var(--space-md) var(--space-lg);
            border-bottom: 1px solid var(--glass-border);
        }

        .section-header .section-title {
            color: var(--white);
            margin: 0;
        }

        .table-responsive {
            max-height: 400px;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: var(--primary-green) var(--bg-secondary);
        }

        .table {
            margin-bottom: 0;
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background: var(--bg-primary);
        }

        .table th,
        .table td {
            font-size: clamp(0.75rem, 2vw, 0.875rem);
            color: var(--text-primary);
            padding: var(--space-sm) var(--space-md);
            border-bottom: 1px solid var(--glass-border);
            text-align: left;
            vertical-align: middle;
        }

        .table th {
            background: var(--bg-secondary);
            position: sticky;
            top: 0;
            z-index: 10;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-primary);
        }

        .table tbody tr {
            transition: background 0.2s ease;
        }

        .table tbody tr:nth-child(even) {
            background: rgba(0, 0, 0, 0.03);
        }

        .table tbody tr:hover {
            background: rgba(33, 160, 85, 0.1);
        }

        .table tbody td {
            border-right: 1px solid var(--glass-border);
        }

        .table tbody td:last-child {
            border-right: none;
        }

        .summary-card {
            background: var(--gradient-primary);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-lg);
            padding: var(--space-lg);
            margin-top: var(--space-lg);
            color: var(--white);
            box-shadow: var(--shadow-lg);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .summary-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-2xl);
        }

        .summary-card h4 {
            font-family: var(--font-display);
            font-size: clamp(1rem, 2.5vw, 1.25rem);
            font-weight: 600;
            color: var(--white);
            margin-bottom: var(--space-md);
            border-bottom: 2px solid var(--gold);
            padding-bottom: var(--space-xs);
        }

        .summary-card p {
            font-size: clamp(0.875rem, 2vw, 1rem);
            margin-bottom: var(--space-sm);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .summary-card p strong {
            font-weight: 600;
            color: var(--gold);
        }

        .summary-card p:last-child {
            margin-bottom: 0;
        }

        .download-form {
            margin-top: var(--space-md);
            padding: 0 var(--space-lg);
            display: flex;
            gap: var(--space-md);
            justify-content: flex-end;
        }

        .btn-primary {
            background: var(--primary-green);
            color: var(--white);
            padding: var(--space-sm) var(--space-lg);
            border-radius: var(--radius-md);
            text-decoration: none;
            font-size: clamp(0.875rem, 2vw, 1rem);
            font-weight: 500;
            border: 1px solid transparent;
            transition: all 0.2s ease;
        }

        .btn-primary:hover {
            background: var(--dark-green);
            border-color: var(--gold);
            transform: translateY(-1px);
        }

        .btn-primary:focus-visible {
            outline: 2px solid var(--gold);
            outline-offset: 2px;
        }

        @media (max-width: 768px) {
            .content-container {
                padding: var(--space-md);
            }

            .results-section {
                min-height: 150px;
            }

            .table th,
            .table td {
                font-size: clamp(0.7rem, 1.8vw, 0.8rem);
                padding: var(--space-xs) var(--space-sm);
            }

            .download-form {
                flex-direction: column;
                gap: var(--space-sm);
                justify-content: center;
            }

            .section-title {
                flex-direction: column;
                align-items: flex-start;
                gap: var(--space-xs);
            }

            .section-title .class-info {
                text-align: left;
                font-size: clamp(0.875rem, 2vw, 1rem);
            }
        }

        @media (max-width: 576px) {
            .table-responsive {
                max-height: 300px;
            }

            .section-title {
                font-size: clamp(1rem, 2.5vw, 1.25rem);
            }

            .summary-card h4 {
                font-size: clamp(0.875rem, 2vw, 1rem);
            }

            .summary-card p {
                font-size: clamp(0.75rem, 1.8vw, 0.875rem);
            }
        }

        /* Ensure high contrast in dark mode */
        html.dark .results-section {
            background: var(--bg-secondary);
        }

        html.dark .form-select {
            background: var(--bg-secondary);
            color: var(--text-primary);
        }

        html.dark .table {
            background: #1c1c2e;
            /* Slightly lighter than --bg-primary for contrast */
        }

        html.dark .table th {
            background: var(--bg-secondary);
            color: var(--text-primary);
        }

        html.dark .table tbody tr:nth-child(even) {
            background: rgba(255, 255, 255, 0.15);
        }

        html.dark .table tbody tr:hover {
            background: rgba(33, 160, 85, 0.2);
        }
    </style>
@endpush

@section('content')
    <div class="content-container">
        <!-- Filter Section -->
        <div class="filter-section">
            <h3 class="section-title">Filter Results</h3>
            <form method="GET" action="{{ route('student.results') }}">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="session_id" class="form-label">Academic Session</label>
                        <select name="session_id" id="session_id" class="form-select">
                            @foreach ($sessions as $session)
                                <option value="{{ $session->id }}" {{ $session->id == request('session_id', $currentSession->id) ? 'selected' : '' }}>
                                    {{ $session->year }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="term" class="form-label">Term</label>
                        <select name="term" id="term" class="form-select">
                            @foreach ($terms as $term)
                                <option value="{{ $term->value }}" {{ $term->value == request('term', $currentTerm->value) ? 'selected' : '' }}>
                                    {{ $term->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Filter Results</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Results Section -->
        <div class="results-section">
            <div class="section-header">
                <h3 class="section-title">
                    <span class="session-term">
                        Results for
                        {{ $sessions->firstWhere('id', request('session_id', $currentSession->id))->year ?? $currentSession->year }}
                        -
                        {{ \App\Enums\TermEnum::tryFrom(request('term', $currentTerm->value))?->label() ?? $currentTerm->label() }}
                    </span>
                    <span class="class-info">
                        Class:
                        {{ $student->getCurrentClass(request('session_id', $currentSession->id), request('term', $currentTerm->value)) ?? 'N/A' }}
                    </span>
                </h3>
            </div>
            <div class="p-4">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Subject</th>
                                <th>Class Assessment</th>
                                <th>Summative Test</th>
                                <th>Exam</th>
                                <th>Total</th>
                                <th>Grade</th>
                                <th>Remark</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($results as $result)
                                <tr>
                                    <td>{{ $result->subject->name }}</td>
                                    <td>{{ $result->class_assessment }}</td>
                                    <td>{{ $result->summative_test }}</td>
                                    <td>{{ $result->exam }}</td>
                                    <td>{{ $result->total }}</td>
                                    <td>{{ $result->grade }}</td>
                                    <td>{{ $result->remark }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No results available for this term.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($termSummary)
                    <div class="summary-card">
                        <h4>Term Summary</h4>
                        <p><strong>Grand Total:</strong> <span>{{ $termSummary->grand_total }}</span></p>
                        <p><strong>Term Average:</strong> <span>{{ number_format($termSummary->term_average, 2) }}</span></p>
                        <p><strong>Position:</strong> <span>{{ $termSummary->position }}</span></p>
                        <p><strong>Principal's Remark:</strong> <span>{{ $termSummary->principal_remark }}</span></p>
                        <p><strong>Teacher's Remark:</strong> <span>{{ $termSummary->teacher_remark }}</span></p>
                    </div>
                @endif
                <div class="download-form">
                    <form method="GET" action="{{ route('student.results.download') }}">
                        <input type="hidden" name="session_id" value="{{ request('session_id', $currentSession->id) }}">
                        <input type="hidden" name="term" value="{{ request('term', $currentTerm->value) }}">
                        <button type="submit" class="btn btn-primary">Download Result</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                gsap.from('.filter-section', { opacity: 0, y: 20, duration: 0.5 });
                gsap.from('.results-section', { opacity: 0, y: 20, duration: 0.5, delay: 0.2 });
                gsap.from('.summary-card', { opacity: 0, y: 20, duration: 0.5, delay: 0.4 });
                gsap.from('.download-form', { opacity: 0, y: 20, duration: 0.5, delay: 0.6 });
            });
        </script>
    @endpush
@endsection