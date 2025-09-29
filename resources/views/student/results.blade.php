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
        }

        .section-title {
            font-family: var(--font-display);
            font-size: clamp(1.25rem, 3vw, 1.5rem);
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: var(--space-md);
        }

        .results-section {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-xl);
            overflow: hidden;
            min-height: 200px;
        }

        .section-header {
            border-bottom: 1px solid var(--glass-border);
            padding: var(--space-md) var(--space-lg);
        }

        .table-responsive {
            max-height: 400px;
            overflow-y: auto;
        }

        .table {
            margin-bottom: 0;
        }

        .table th,
        .table td {
            font-size: clamp(0.75rem, 2.5vw, 0.875rem);
            color: var(--text-primary);
        }

        .table th {
            background: var(--glass-bg);
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .summary-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-lg);
            padding: var(--space-lg);
            margin-top: var(--space-lg);
        }

        .summary-card h4 {
            font-family: var(--font-display);
            font-size: clamp(1rem, 2.5vw, 1.25rem);
            font-weight: 600;
            color: var(--text-primary);
        }

        .download-form {
            margin-top: var(--space-md);
            padding: 0 var(--space-lg);
        }

        @media (max-width: 576px) {
            .content-container {
                padding: var(--space-md);
            }

            .results-section {
                min-height: 150px;
            }
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
                <h3 class="section-title">Results for {{ $currentSession->year }} - {{ $currentTerm->label() }}</h3>
            </div>
            <div class="p-4">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Subject</th>
                                <th>Class</th>
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
                                    <td>{{ $result->class->name }}</td>
                                    <td>{{ $result->class_assessment }}</td>
                                    <td>{{ $result->summative_test }}</td>
                                    <td>{{ $result->exam }}</td>
                                    <td>{{ $result->total }}</td>
                                    <td>{{ $result->grade }}</td>
                                    <td>{{ $result->remark }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">No results available for this term.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($termSummary)
                    <div class="summary-card">
                        <h4>Term Summary</h4>
                        <p><strong>Grand Total:</strong> {{ $termSummary->grand_total }}</p>
                        <p><strong>Term Average:</strong> {{ number_format($termSummary->term_average, 2) }}</p>
                        <p><strong>Position:</strong> {{ $termSummary->position }}</p>
                        <p><strong>Principal's Remark:</strong> {{ $termSummary->principal_remark }}</p>
                        <p><strong>Teacher's Remark:</strong> {{ $termSummary->teacher_remark }}</p>
                    </div>
                @endif
                <div class="download-form">
                    <form method="GET" action="{{ route('student.results.download') }}">
                        <input type="hidden" name="session_id" value="{{ request('session_id', $currentSession->id) }}">
                        <input type="hidden" name="term" value="{{ request('term', $currentTerm->value) }}">
                        <button type="submit" class="btn btn-primary">Download Results as PDF</button>
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