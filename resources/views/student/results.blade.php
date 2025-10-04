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
            box-shadow: var(--shadow-lg), inset 0 2px 4px rgba(255, 255, 255, 0.1), inset 0 -2px 4px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .filter-section:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-2xl), inset 0 2px 4px rgba(255, 255, 255, 0.15), inset 0 -2px 4px rgba(0, 0, 0, 0.1);
        }

        .section-title {
            font-family: var(--font-display);
            font-size: clamp(1.5rem, 3.5vw, 1.75rem);
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: var(--space-md);
            letter-spacing: 0.02em;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
        }

        .section-title .session-term {
            flex: 1;
            text-align: left;
        }

        .section-title .class-info {
            flex: 1;
            text-align: right;
            color: var(--text-secondary);
            font-size: clamp(0.875rem, 2vw, 1rem);
            font-family: var(--font-primary);
        }

        .form-label {
            font-family: var(--font-primary);
            font-size: clamp(0.875rem, 2vw, 1rem);
            color: var(--text-secondary);
            margin-bottom: var(--space-xs);
            font-weight: 500;
        }

        .form-select {
            background: rgba(255, 255, 255, 0.9);
            color: var(--text-primary);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-lg);
            padding: var(--space-sm) var(--space-md);
            font-family: var(--font-primary);
            font-size: clamp(0.875rem, 2vw, 1rem);
            transition: border-color 0.2s ease, background 0.2s ease, box-shadow 0.2s ease;
            cursor: pointer;
        }

        html.dark .form-select {
            background: rgba(31, 41, 55, 0.9);
            color: var(--text-primary);
        }

        .form-select:focus {
            border-color: var(--primary-green);
            outline: none;
            box-shadow: 0 0 0 3px rgba(33, 160, 85, 0.3);
        }

        .results-section {
            background: var(--bg-secondary);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-xl);
            overflow: hidden;
            min-height: 200px;
            box-shadow: var(--shadow-lg), inset 0 2px 4px rgba(255, 255, 255, 0.1), inset 0 -2px 4px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .results-section:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-2xl), inset 0 2px 4px rgba(255, 255, 255, 0.15), inset 0 -2px 4px rgba(0, 0, 0, 0.1);
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

        .table-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: var(--space-sm) var(--space-lg);
            background: var(--bg-secondary);
            border-bottom: 1px solid var(--glass-border);
        }

        .table-search {
            position: relative;
            max-width: 200px;
        }

        .table-search input {
            width: 100%;
            padding: var(--space-sm) var(--space-md);
            padding-left: calc(var(--space-md) + 1.5rem);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-lg);
            background: rgba(255, 255, 255, 0.9);
            color: var(--text-primary);
            font-family: var(--font-primary);
            font-size: clamp(0.75rem, 2vw, 0.875rem);
            transition: border-color 0.2s ease;
        }

        html.dark .table-search input {
            background: rgba(31, 41, 55, 0.9);
        }

        .table-search input:focus {
            border-color: var(--primary-green);
            outline: none;
            box-shadow: 0 0 0 3px rgba(33, 160, 85, 0.3);
        }

        .table-search i {
            position: absolute;
            left: var(--space-sm);
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
            font-size: clamp(0.75rem, 2vw, 0.875rem);
        }

        .table-responsive {
            max-height: 400px;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: rgba(33, 160, 85, 0.7) var(--bg-secondary);
        }

        .table {
            margin-bottom: 0;
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background: var(--bg-primary);
        }

        html.dark .table {
            background: #1c1c2e;
            --bs-table-bg: #1c1c2e !important;
        }

        .table th,
        .table td {
            font-family: var(--font-primary);
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
            cursor: pointer;
            transition: background 0.2s ease;
        }

        .table th:hover {
            background: rgba(33, 160, 85, 0.1);
        }

        .table th.sortable::after {
            content: '\f0dc';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            margin-left: var(--space-xs);
            color: var(--text-secondary);
            font-size: 0.75rem;
        }

        .table th.sort-asc::after {
            content: '\f0de';
        }

        .table th.sort-desc::after {
            content: '\f0dd';
        }

        .table tbody tr {
            transition: background 0.2s ease;
        }

        .table tbody tr:nth-child(even) {
            background: rgba(0, 0, 0, 0.03);
        }

        html.dark .table tbody tr:nth-child(even) {
            background: rgba(255, 255, 255, 0.05);
        }

        .table tbody tr:hover {
            background: rgba(33, 160, 85, 0.08);
        }

        html.dark .table tbody tr:hover {
            background: rgba(33, 160, 85, 0.15);
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
            box-shadow: var(--shadow-lg), inset 0 2px 4px rgba(255, 255, 255, 0.1), inset 0 -2px 4px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .summary-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-2xl), inset 0 2px 4px rgba(255, 255, 255, 0.15), inset 0 -2px 4px rgba(0, 0, 0, 0.1);
        }

        .summary-card h4 {
            font-family: var(--font-display);
            font-size: clamp(1rem, 2.5vw, 1.25rem);
            font-weight: 700;
            color: var(--white);
            margin-bottom: var(--space-md);
            border-bottom: 1px solid var(--gold);
            padding-bottom: var(--space-xs);
        }

        .summary-card p {
            font-family: var(--font-primary);
            font-size: clamp(0.875rem, 2vw, 1rem);
            margin-bottom: var(--space-sm);
            display: flex;
            align-items: center;
            gap: var(--space-sm);
        }

        .summary-card p i {
            color: var(--gold);
            font-size: clamp(0.875rem, 2vw, 1rem);
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
            border-radius: var(--radius-lg);
            text-decoration: none;
            font-family: var(--font-primary);
            font-size: clamp(0.875rem, 2vw, 1rem);
            font-weight: 500;
            border: 1px solid transparent;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .btn-primary:hover {
            background: var(--dark-green);
            border-color: var(--gold);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .btn-primary:focus-visible {
            outline: 2px solid rgba(212, 175, 55, 0.8);
            outline-offset: 2px;
        }

        .btn-primary.loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 20px;
            height: 20px;
            border: 2px solid var(--white);
            border-top-color: transparent;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            transform: translate(-50%, -50%);
        }

        .btn-primary.loading span {
            opacity: 0;
        }

        @keyframes spin {
            to {
                transform: translate(-50%, -50%) rotate(360deg);
            }
        }

        @media (max-width: 768px) {
            .content-container {
                padding: var(--space-md);
            }

            .filter-section {
                padding: var(--space-md);
            }

            .filter-section .row {
                flex-direction: column;
            }

            .results-section {
                min-height: 150px;
            }

            .table th,
            .table td {
                font-size: clamp(0.7rem, 1.8vw, 0.8rem);
                padding: var(--space-xs) var(--space-sm);
            }

            .table-controls {
                flex-direction: column;
                gap: var(--space-sm);
            }

            .table-search {
                max-width: 100%;
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
                font-size: clamp(1.25rem, 3vw, 1.5rem);
            }

            .summary-card h4 {
                font-size: clamp(0.875rem, 2vw, 1rem);
            }

            .summary-card p {
                font-size: clamp(0.75rem, 1.8vw, 0.875rem);
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
                    <div class="col-12 d-flex gap-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter"></i> Filter Results
                        </button>
                        <button type="button" class="btn btn-primary" id="resetFilters">
                            <i class="fas fa-undo"></i> Reset
                        </button>
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
                <div class="table-controls">
                    <div class="table-search">
                        <i class="fas fa-search"></i>
                        <input type="text" id="tableSearch" placeholder="Search subjects..." aria-label="Search subjects">
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th class="sortable" data-sort="subject">Subject</th>
                                <th class="sortable" data-sort="class_assessment">Class Assessment</th>
                                <th class="sortable" data-sort="summative_test">Summative Test</th>
                                <th class="sortable" data-sort="exam">Exam</th>
                                <th class="sortable" data-sort="total">Total</th>
                                <th class="sortable" data-sort="grade">Grade</th>
                                <th class="sortable" data-sort="remark">Remark</th>
                            </tr>
                        </thead>
                        <tbody id="resultsTable">
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
                        <p><i class="fas fa-trophy"></i> <strong>Grand Total:</strong>
                            <span>{{ $termSummary->grand_total }}</span></p>
                        <p><i class="fas fa-calculator"></i> <strong>Term Average:</strong>
                            <span>{{ number_format($termSummary->term_average, 2) }}</span></p>
                        <p><i class="fas fa-award"></i> <strong>Position:</strong> <span>{{ $termSummary->position }}</span></p>
                        <p><i class="fas fa-comment"></i> <strong>Principal's Remark:</strong>
                            <span>{{ $termSummary->principal_remark }}</span></p>
                        <p><i class="fas fa-comment-alt"></i> <strong>Teacher's Remark:</strong>
                            <span>{{ $termSummary->teacher_remark }}</span></p>
                    </div>
                @endif
                <div class="download-form">
                    <form method="GET" action="{{ route('student.results.download') }}">
                        <input type="hidden" name="session_id" value="{{ request('session_id', $currentSession->id) }}">
                        <input type="hidden" name="term" value="{{ request('term', $currentTerm->value) }}">
                        <button type="submit" class="btn btn-primary">
                            <span><i class="fas fa-download"></i> Download Result</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                // Table Search
                const searchInput = document.getElementById('tableSearch');
                const tableBody = document.getElementById('resultsTable');
                const rows = tableBody.querySelectorAll('tr');

                searchInput.addEventListener('input', () => {
                    const query = searchInput.value.toLowerCase();
                    rows.forEach(row => {
                        const subject = row.cells[0].textContent.toLowerCase();
                        row.style.display = subject.includes(query) ? '' : 'none';
                    });
                });

                // Table Sorting
                const headers = document.querySelectorAll('.table th.sortable');
                headers.forEach(header => {
                    header.addEventListener('click', () => {
                        const sortKey = header.dataset.sort;
                        const isAsc = !header.classList.contains('sort-asc');
                        headers.forEach(h => {
                            h.classList.remove('sort-asc', 'sort-desc');
                        });
                        header.classList.add(isAsc ? 'sort-asc' : 'sort-desc');

                        const rowsArray = Array.from(rows);
                        rowsArray.sort((a, b) => {
                            let aValue = a.cells[Array.from(headers).findIndex(h => h.dataset.sort === sortKey)].textContent;
                            let bValue = b.cells[Array.from(headers).findIndex(h => h.dataset.sort === sortKey)].textContent;

                            if (sortKey === 'total' || sortKey === 'class_assessment' || sortKey === 'summative_test' || sortKey === 'exam') {
                                aValue = parseFloat(aValue) || 0;
                                bValue = parseFloat(bValue) || 0;
                            } else {
                                aValue = aValue.toLowerCase();
                                bValue = bValue.toLowerCase();
                            }

                            return isAsc ? (aValue > bValue ? 1 : -1) : (aValue < bValue ? 1 : -1);
                        });

                        tableBody.innerHTML = '';
                        rowsArray.forEach(row => tableBody.appendChild(row));
                    });
                });

                // Reset Filters
                const resetButton = document.getElementById('resetFilters');
                resetButton.addEventListener('click', () => {
                    const form = resetButton.closest('form');
                    form.querySelector('#session_id').selectedIndex = 0;
                    form.querySelector('#term').selectedIndex = 0;
                    form.submit();
                });

                // Download Button Loading State
                const downloadButton = document.querySelector('.download-form .btn-primary');
                downloadButton.addEventListener('click', () => {
                    downloadButton.classList.add('loading');
                    setTimeout(() => {
                        downloadButton.classList.remove('loading');
                    }, 2000); // Simulate loading
                });
            });
        </script>
    @endpush
@endsection