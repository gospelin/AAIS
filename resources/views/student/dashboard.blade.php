@extends('student.layouts.app')

@section('title', 'Student Dashboard')

@section('description', 'View your academic progress, fee status, performance trends, and profile at Aunty Anne\'s International School.')

@push('styles')
    <style>
        .content-container {
            padding: var(--space-md);
        }

        .welcome-section {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-xl);
            padding: var(--space-lg);
            position: relative;
            margin-bottom: var(--space-md);
            min-height: 120px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .welcome-section:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }

        .welcome-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gradient-primary);
            transition: transform 0.5s ease;
        }

        .welcome-section:hover::before {
            transform: scaleX(1.05);
        }

        .welcome-header {
            font-family: var(--font-display);
            font-size: clamp(1.25rem, 2.5vw, 1.5rem);
            font-weight: 700;
            background: var(--gradient-primary);
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .welcome-subtitle {
            font-family: var(--font-primary);
            font-size: clamp(0.75rem, 1.8vw, 0.875rem);
            color: var(--text-secondary);
        }

        .avatar-container {
            width: clamp(50px, 10vw, 60px);
            height: clamp(50px, 10vw, 60px);
            border-radius: 50%;
            background: var(--gradient-primary);
            padding: 3px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid var(--glass-border);
        }

        .avatar-container:hover {
            transform: scale(1.1);
            box-shadow: 0 0 8px rgba(99, 102, 241, 0.5);
        }

        .avatar-img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--white);
        }

        .avatar-placeholder {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: var(--dark-card);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: clamp(0.875rem, 2vw, 1rem);
            font-weight: 700;
            color: var(--white);
            border: 2px solid var(--white);
        }

        html.dark .avatar-placeholder {
            background: var(--dark-card);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(clamp(200px, 45vw, 250px), 1fr));
            gap: var(--space-md);
            margin-bottom: var(--space-lg);
        }

        .stat-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-lg);
            padding: var(--space-md);
            position: relative;
            min-height: 110px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
            border-color: var(--primary);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gradient-primary);
            transition: transform 0.5s ease;
        }

        .stat-card:hover::before {
            transform: scaleX(1.05);
        }

        .stat-icon {
            width: clamp(24px, 6vw, 28px);
            height: clamp(24px, 6vw, 28px);
            background: var(--gradient-primary);
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: clamp(0.875rem, 2vw, 1rem);
            color: var(--white);
            transition: transform 0.3s ease;
        }

        .stat-card:hover .stat-icon {
            transform: rotate(360deg);
        }

        .stat-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: var(--space-xs);
        }

        .stat-value {
            font-family: var(--font-display);
            font-size: clamp(1rem, 2.5vw, 1.25rem);
            font-weight: 600;
            color: var(--text-primary);
        }

        .stat-label {
            font-family: var(--font-primary);
            font-size: clamp(0.75rem, 1.8vw, 0.875rem);
            color: var(--text-secondary);
        }

        .quick-access-link {
            font-family: var(--font-primary);
            font-size: clamp(0.75rem, 1.8vw, 0.875rem);
            color: var(--success);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition-fast);
        }

        .quick-access-link:hover {
            color: var(--electric);
            text-decoration: underline;
        }

        .progress-bar {
            height: 6px;
            background: var(--glass-bg);
            border-radius: var(--radius-sm);
            overflow: hidden;
            margin-top: var(--space-xs);
        }

        .progress-fill {
            height: 100%;
            background: var(--gradient-primary);
            transition: width 0.5s ease-in-out;
        }

        .results-section,
        .performance-section {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-xl);
            margin-bottom: var(--space-lg);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .results-section:hover,
        .performance-section:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }

        .section-header {
            border-bottom: 1px solid var(--glass-border);
            padding: var(--space-xs) var(--space-sm);
            background: var(--glass-bg);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .section-title {
            font-family: var(--font-display);
            font-size: clamp(0.875rem, 2vw, 1rem);
            font-weight: 600;
            color: var(--text-primary);
        }

        .chart-toggle {
            font-family: var(--font-primary);
            font-size: clamp(0.75rem, 1.8vw, 0.875rem);
            color: var(--success);
            cursor: pointer;
            transition: var(--transition-fast);
        }

        .chart-toggle:hover {
            color: var(--electric);
            text-decoration: underline;
        }

        .table-responsive {
            max-height: 180px;
            overflow-y: auto;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .table {
            margin-bottom: 0;
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background: var(--dark-card);
        }

        html.dark .table {
            background: var(--dark-card);
            --bs-table-bg: var(--dark-card) !important;
        }

        .table th,
        .table td {
            font-family: var(--font-primary);
            font-size: clamp(0.75rem, 1.8vw, 0.875rem);
            color: var(--text-primary);
            padding: var(--space-xs) var(--space-sm);
            border-bottom: 1px solid var(--glass-border);
            text-align: left;
            vertical-align: middle;
            white-space: nowrap;
        }

        .table th {
            background: var(--glass-bg);
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
            background: var(--gradient-primary);
            color: var(--white);
        }

        .chart-container {
            padding: var(--space-md);
            min-height: 300px;
            max-width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            position: relative;
        }

        .chart-responsive {
            max-width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .chart-responsive canvas#performanceChart {
            min-width: clamp(250px, 100%, 500px);
            width: 100% !important;
            height: clamp(200px, 40vw, 300px) !important;
            display: block !important;
        }

        .chart-responsive canvas#subjectChart {
            min-width: clamp(400px, 100%, 800px);
            width: 100% !important;
            height: clamp(200px, 40vw, 300px) !important;
            display: block !important;
        }

        .chart-loading {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: var(--space-xs);
            font-family: var(--font-primary);
            color: var(--text-secondary);
            font-size: clamp(0.75rem, 1.8vw, 0.875rem);
        }

        .spinner {
            width: 24px;
            height: 24px;
            border: 3px solid var(--primary);
            border-top: 3px solid transparent;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .text-error {
            color: var(--error);
        }

        .text-success {
            color: var(--success);
        }

        .no-results-message {
            font-family: var(--font-primary);
            font-size: clamp(0.75rem, 1.8vw, 0.875rem);
            color: var(--text-secondary);
            text-align: center;
            padding: var(--space-sm);
        }

        @media (max-width: 768px) {
            .content-container {
                padding: var(--space-sm);
            }

            .stats-grid {
                grid-template-columns: 1fr;
                gap: var(--space-sm);
            }

            .stat-card {
                min-height: 100px;
                padding: var(--space-sm);
            }

            .stat-icon {
                width: clamp(20px, 5vw, 24px);
                height: clamp(20px, 5vw, 24px);
                font-size: clamp(0.75rem, 1.8vw, 0.875rem);
            }

            .stat-value {
                font-size: clamp(0.875rem, 2vw, 1rem);
            }

            .stat-label,
            .quick-access-link,
            .chart-toggle {
                font-size: clamp(0.75rem, 1.8vw, 0.875rem);
            }

            .section-title {
                font-size: clamp(0.75rem, 1.8vw, 0.875rem);
            }

            .chart-container {
                padding: var(--space-sm);
                min-height: 250px;
            }

            .welcome-section {
                padding: var(--space-sm);
                min-height: 100px;
            }

            .welcome-header {
                font-size: clamp(1rem, 2vw, 1.25rem);
            }

            .avatar-container {
                width: clamp(40px, 8vw, 50px);
                height: clamp(40px, 8vw, 50px);
            }

            .avatar-placeholder {
                font-size: clamp(0.75rem, 1.8vw, 0.875rem);
            }
        }

        @media (max-width: 576px) {
            .content-container {
                padding: var(--space-xs);
            }

            .table-responsive {
                max-height: 150px;
            }

            .chart-container {
                min-height: 200px;
            }

            .chart-responsive canvas#performanceChart,
            .chart-responsive canvas#subjectChart {
                height: clamp(150px, 35vw, 200px) !important;
            }
        }

        @media (max-width: 360px) {
            .stats-grid {
                gap: var(--space-xs);
            }

            .stat-card {
                min-height: 90px;
            }

            .avatar-container {
                width: clamp(36px, 7vw, 40px);
                height: clamp(36px, 7vw, 40px);
            }
        }

        .stat-card:focus-visible,
        .quick-access-link:focus-visible,
        .chart-toggle:focus-visible {
            outline: 2px solid var(--primary);
            outline-offset: 2px;
        }
    </style>
@endpush

@section('content')
    <div class="content-container">
        <div class="row">
            <!-- Welcome Section -->
            <div class="col-12">
                <div class="welcome-section" role="banner">
                    <div class="d-flex align-items-center gap-2">
                        <div class="avatar-container" aria-label="User profile picture">
                            @if($student->profile_pic && Storage::disk('public')->exists('profiles/' . $student->profile_pic))
                                <img src="{{ Storage::url('profiles/' . $student->profile_pic) . '?t=' . time() }}"
                                    alt="{{ $student->full_name }}" class="avatar-img" loading="lazy">
                            @else
                                <div class="avatar-placeholder">
                                    <span>{{ strtoupper(substr($student->first_name, 0, 1)) }}</span>
                                </div>
                            @endif
                        </div>
                        <div>
                            <h3 class="welcome-header" id="welcomeHeader">Welcome, {{ $student->full_name }}!</h3>
                            <p class="welcome-subtitle" id="welcomeSubtitle">Explore your academic progress, performance trends, and more.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="col-12">
                <div class="stats-grid">
                    <div class="stat-card" tabindex="0" role="region" aria-label="Current Class">
                        <div class="stat-header">
                            <div class="stat-icon"><i class="fas fa-graduation-cap"></i></div>
                        </div>
                        <h3 class="stat-value">{{ $currentClass ?? 'Not Enrolled' }}</h3>
                        <p class="stat-label">Current Class</p>
                        <a href="{{ route('student.profile') }}" class="quick-access-link">View Profile →</a>
                    </div>
                    <div class="stat-card" tabindex="0" role="region" aria-label="Fee Status">
                        <div class="stat-header">
                            <div class="stat-icon"><i class="fas fa-money-check-alt"></i></div>
                        </div>
                        <h3 class="stat-value">{{ $feeStatus && $feeStatus->has_paid_fee ? 'Paid' : 'Unpaid' }}</h3>
                        <p class="stat-label">Fee Status ({{ $currentTerm->label() }})</p>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: {{ $feeStatus && $feeStatus->has_paid_fee ? '100' : '0' }}%;"></div>
                        </div>
                        <a href="{{ route('student.fee_status') }}" class="quick-access-link">View Fee Details →</a>
                    </div>
                    @if($hasPaidFees)
                        <div class="stat-card" tabindex="0" role="region" aria-label="Current Term Results">
                            <div class="stat-header">
                                <div class="stat-icon"><i class="fas fa-book"></i></div>
                            </div>
                            <h3 class="stat-value">{{ $recentResults->count() }} Subjects</h3>
                            <p class="stat-label">Current Term Results</p>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: {{ min($recentResults->count() / 10 * 100, 100) }}%;"></div>
                            </div>
                            <a href="{{ route('student.results') }}" class="quick-access-link">View Results →</a>
                        </div>
                        <div class="stat-card" tabindex="0" role="region" aria-label="Best Subject">
                            <div class="stat-header">
                                <div class="stat-icon"><i class="fas fa-star"></i></div>
                            </div>
                            <h3 class="stat-value">{{ $bestSubject->name ?? 'N/A' }}</h3>
                            <p class="stat-label">Best Subject ({{ $currentSession->year }})</p>
                            <p class="stat-label">Avg: {{ $bestSubject->average ?? 'N/A' }}%</p>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: {{ $bestSubject->average ?? 0 }}%;"></div>
                            </div>
                        </div>
                    @else
                        <div class="stat-card" tabindex="0" role="region" aria-label="Results Locked">
                            <div class="stat-header">
                                <div class="stat-icon"><i class="fas fa-exclamation-circle"></i></div>
                            </div>
                            <h3 class="stat-value">Results Locked</h3>
                            <p class="stat-label">Please pay your fees to view results.</p>
                            <a href="{{ route('student.fee_status') }}" class="quick-access-link">View Fee Details →</a>
                        </div>
                    @endif
                </div>
            </div>

            @if($hasPaidFees)
                <!-- Performance Overview -->
                <div class="col-12">
                    <section id="performance-overview" class="performance-section" role="region" aria-label="Performance Trends">
                        <div class="section-header">
                            <h3 class="section-title">Performance Trends ({{ $currentSession->year }})</h3>
                            <span class="chart-toggle" onclick="toggleChartDataset('performanceChart')" role="button" aria-label="Toggle performance chart dataset">Toggle Dataset</span>
                        </div>
                        <div class="chart-container">
                            <div class="chart-responsive">
                                <div class="chart-loading" id="performanceChartLoading">
                                    <div class="spinner"></div>
                                    <span>Loading performance data...</span>
                                </div>
                                <canvas id="performanceChart" aria-label="Performance trend chart"></canvas>
                            </div>
                        </div>
                        <div class="p-2">
                            @if(empty($performanceData['labels']))
                                <p class="no-results-message">No performance trend data available.</p>
                            @else
                                <p class="stat-label">Performance Slope:
                                    <span class="{{ $performanceSlope >= 0 ? 'text-success' : 'text-error' }}">
                                        {{ number_format($performanceSlope, 2) }}% per term
                                    </span>
                                </p>
                                <p class="stat-label">Positive slope indicates improvement, negative indicates decline.</p>
                            @endif
                        </div>
                    </section>
                </div>

                <!-- Subject Performance -->
                <div class="col-12">
                    <section id="subject-performance" class="performance-section" role="region" aria-label="Subject Performance">
                        <div class="section-header">
                            <h3 class="section-title">Subject Performance ({{ $currentSession->year }} - {{ $currentTerm->label() }})</h3>
                            <span class="chart-toggle" onclick="toggleChartDataset('subjectChart')" role="button" aria-label="Toggle subject chart dataset">Toggle Dataset</span>
                        </div>
                        <div class="chart-container">
                            <div class="chart-responsive">
                                <div class="chart-loading" id="subjectChartLoading">
                                    <div class="spinner"></div>
                                    <span>Loading subject data...</span>
                                </div>
                                <canvas id="subjectChart" aria-label="Subject performance chart"></canvas>
                            </div>
                        </div>
                    </section>
                </div>

                <!-- Areas Needing Improvement -->
                <div class="col-12">
                    <section id="areas-improvement" class="performance-section" role="region" aria-label="Areas Needing Improvement">
                        <div class="section-header">
                            <h3 class="section-title">Areas Needing Improvement</h3>
                        </div>
                        <div class="p-2">
                            @if(empty($subjectData['labels']))
                                <p class="no-results-message">No subject performance data available for this term.</p>
                            @elseif($weakSubjects->isEmpty())
                                <p class="stat-label">Great job! No subjects currently require significant improvement.</p>
                            @else
                                <ul class="list-disc pl-4 stat-label">
                                    @foreach($weakSubjects as $subject)
                                        <li>{{ $subject->name }} (Avg: {{ $subject->average }}%)</li>
                                    @endforeach
                                </ul>
                                <p class="stat-label mt-1">Focus on these subjects to boost your overall performance.</p>
                            @endif
                        </div>
                    </section>
                </div>

                <!-- Recent Results -->
                <div class="col-12">
                    <section id="recent-results" class="results-section" role="region" aria-label="Recent Results">
                        <div class="section-header">
                            <h3 class="section-title">Recent Results ({{ $currentSession->year }} - {{ $currentTerm->label() }})</h3>
                        </div>
                        <div class="p-2">
                            <div class="table-responsive">
                                <table class="table table-hover" role="grid">
                                    <thead>
                                        <tr>
                                            <th scope="col">Subject</th>
                                            <th scope="col">Class</th>
                                            <th scope="col">Total</th>
                                            <th scope="col">Grade</th>
                                            <th scope="col">Remark</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($recentResults as $result)
                                            <tr>
                                                <td>{{ $result->subject->name }}</td>
                                                <td>{{ $result->class->name }}</td>
                                                <td>{{ $result->total }}</td>
                                                <td>{{ $result->grade }}</td>
                                                <td>{{ $result->remark }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center stat-label">No results available for this term.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </section>
                </div>
            @else
                <!-- Fee Payment Warning -->
                <div class="col-12">
                    <section class="results-section" role="region" aria-label="Results Unavailable">
                        <div class="section-header">
                            <h3 class="section-title">Results Unavailable</h3>
                        </div>
                        <div class="p-2">
                            <p class="no-results-message">Please pay your fees for {{ $currentTerm->label() }} to access your results and performance data.</p>
                            <a href="{{ route('student.fee_status') }}" class="quick-access-link d-block text-center">View Fee Details →</a>
                        </div>
                    </section>
                </div>
            @endif
        </div>
    </div>
    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                // Typing Animation for Welcome Section
                const welcomeHeader = document.getElementById('welcomeHeader');
                const welcomeSubtitle = document.getElementById('welcomeSubtitle');
                const headerText = welcomeHeader.textContent;
                const subtitleText = welcomeSubtitle.textContent;
                welcomeHeader.textContent = '';
                welcomeSubtitle.textContent = '';

                let headerIndex = 0;
                let subtitleIndex = 0;

                function typeHeader() {
                    if (headerIndex < headerText.length) {
                        welcomeHeader.textContent += headerText[headerIndex];
                        headerIndex++;
                        setTimeout(typeHeader, 80);
                    } else {
                        setTimeout(typeSubtitle, 400);
                    }
                }

                function typeSubtitle() {
                    if (subtitleIndex < subtitleText.length) {
                        welcomeSubtitle.textContent += subtitleText[subtitleIndex];
                        subtitleIndex++;
                        setTimeout(typeSubtitle, 80);
                    }
                }

                setTimeout(typeHeader, 400);

                // CSS Animations for Stat Cards
                const statCards = document.querySelectorAll('.stat-card');
                statCards.forEach((card, index) => {
                    card.style.opacity = '0';
                    card.style.transform = 'translateY(20px)';
                    setTimeout(() => {
                        card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, 500 + index * 100);
                });

                // Chart Dataset Toggle Function
                const chartInstances = {};

                function toggleChartDataset(chartId) {
                    const chart = chartInstances[chartId];
                    if (chart) {
                        const dataset = chart.data.datasets[0];
                        dataset.hidden = !dataset.hidden;
                        chart.update();
                    }
                }

                @if($hasPaidFees)
                    try {
                        const performanceCanvas = document.getElementById('performanceChart');
                        const subjectCanvas = document.getElementById('subjectChart');
                        const performanceLoading = document.getElementById('performanceChartLoading');
                        const subjectLoading = document.getElementById('subjectChartLoading');

                        // Initialize default data
                        let performanceData = { labels: [], averages: [] };
                        let subjectData = { labels: [], averages: [] };

                        // Log raw JSON data for debugging
                        console.log('Raw Performance Data:', @json($performanceData));
                        console.log('Raw Subject Data:', @json($subjectData));

                        // Parse JSON data safely
                        try {
                            const rawPerformanceData = @json($performanceData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_INVALID_UTF8_IGNORE);
                            const rawSubjectData = @json($subjectData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_INVALID_UTF8_IGNORE);

                            // Validate performanceData
                            if (rawPerformanceData && typeof rawPerformanceData === 'object' && Array.isArray(rawPerformanceData.labels) && Array.isArray(rawPerformanceData.averages)) {
                                performanceData = {
                                    labels: rawPerformanceData.labels.map(label => String(label || '')),
                                    averages: rawPerformanceData.averages.map(avg => Number(avg) || 0)
                                };
                            } else {
                                console.warn('Invalid performanceData:', rawPerformanceData);
                            }

                            // Validate subjectData
                            if (rawSubjectData && typeof rawSubjectData === 'object' && Array.isArray(rawSubjectData.labels) && Array.isArray(rawSubjectData.averages)) {
                                subjectData = {
                                    labels: rawSubjectData.labels.map(label => String(label || '')),
                                    averages: rawSubjectData.averages.map(avg => Number(avg) || 0)
                                };
                            } else {
                                console.warn('Invalid subjectData:', rawSubjectData);
                            }
                        } catch (e) {
                            console.error('Error parsing chart data:', e, 'Performance Data:', @json($performanceData), 'Subject Data:', @json($subjectData));
                            performanceLoading.style.display = 'none';
                            subjectLoading.style.display = 'none';
                            return;
                        }

                        // Performance Chart (Line Chart)
                        if (!performanceCanvas) {
                            console.error('Performance canvas not found');
                            performanceLoading.style.display = 'none';
                        } else if (!performanceData.labels.length || !performanceData.averages.length) {
                            performanceLoading.style.display = 'none';
                            const ctx = performanceCanvas.getContext('2d');
                            ctx.font = '12px Inter, sans-serif';
                            ctx.fillStyle = 'var(--text-secondary)';
                            ctx.textAlign = 'center';
                            ctx.fillText('No performance data available', performanceCanvas.width / 2, performanceCanvas.height / 2);
                        } else {
                            setTimeout(() => {
                                performanceLoading.style.display = 'none';
                                chartInstances['performanceChart'] = new Chart(performanceCanvas, {
                                    type: 'line',
                                    data: {
                                        labels: performanceData.labels,
                                        datasets: [{
                                            label: 'Term Average (%)',
                                            data: performanceData.averages,
                                            borderColor: '#4b4bff',
                                            backgroundColor: (context) => {
                                                const ctx = context.chart.canvas.getContext('2d');
                                                const gradient = ctx.createLinearGradient(0, 0, 0, context.chart.height);
                                                gradient.addColorStop(0, 'rgba(75, 75, 255, 0.5)');
                                                gradient.addColorStop(1, 'rgba(75, 75, 255, 0.1)');
                                                return gradient;
                                            },
                                            fill: true,
                                            tension: 0.4,
                                            pointRadius: 6,
                                            pointHoverRadius: 8,
                                            pointBackgroundColor: '#ffffff',
                                            pointBorderColor: '#8b46ff',
                                            pointBorderWidth: 4,
                                            pointHoverBackgroundColor: '#8b46ff',
                                            pointHoverBorderColor: '#ffffff'
                                        }]
                                    },
                                    options: {
                                        responsive: true,
                                        maintainAspectRatio: false,
                                        scales: {
                                            y: {
                                                beginAtZero: true,
                                                max: 100,
                                                ticks: {
                                                    stepSize: 10,
                                                    color: '#4b4bff',
                                                    font: {
                                                        family: 'Inter, sans-serif',
                                                        size: 12
                                                    },
                                                    precision: 0
                                                },
                                                title: {
                                                    display: true,
                                                    text: 'Average Score (%)',
                                                    color: '#4b4bff',
                                                    font: {
                                                        family: 'Space Grotesk, sans-serif',
                                                        size: 14
                                                    }
                                                },
                                                grid: {
                                                    color: 'rgba(255, 255, 255, 0.1)',
                                                    borderDash: [5, 5]
                                                }
                                            },
                                            x: {
                                                title: {
                                                    display: true,
                                                    text: 'Term',
                                                    color: '#4b4bff',
                                                    font: {
                                                        family: 'Space Grotesk, sans-serif',
                                                        size: 14
                                                    }
                                                },
                                                ticks: {
                                                    color: '#4b4bff',
                                                    font: {
                                                        family: 'Inter, sans-serif',
                                                        size: 12
                                                    }
                                                },
                                                grid: {
                                                    color: 'rgba(255, 255, 255, 0.1)',
                                                    borderDash: [5, 5]
                                                }
                                            }
                                        },
                                        plugins: {
                                            legend: {
                                                display: true,
                                                position: 'top',
                                                labels: {
                                                    color: '#4b4bff',
                                                    font: {
                                                        family: 'Inter, sans-serif',
                                                        size: 12
                                                    },
                                                    padding: 15,
                                                    boxWidth: 15,
                                                    usePointStyle: true
                                                }
                                            },
                                            tooltip: {
                                                enabled: true,
                                                backgroundColor: 'rgba(255, 255, 255, 0.05)',
                                                titleColor: 'var(--white)',
                                                bodyColor: 'var(--white)',
                                                borderColor: 'rgba(255, 255, 255, 0.1)',
                                                borderWidth: 1,
                                                padding: 8,
                                                cornerRadius: 6,
                                                titleFont: {
                                                    family: 'Space Grotesk, sans-serif',
                                                    size: 12
                                                },
                                                bodyFont: {
                                                    family: 'Inter, sans-serif',
                                                    size: 11
                                                },
                                                callbacks: {
                                                    label: function(context) {
                                                        return `${context.dataset.label}: ${context.parsed.y.toFixed(1)}%`;
                                                    }
                                                }
                                            }
                                        },
                                        animation: {
                                            duration: 1500,
                                            easing: 'easeOutCubic',
                                            onProgress: function(animation) {
                                                const chart = animation.chart;
                                                chart.canvas.style.opacity = animation.currentStep / animation.numSteps;
                                            }
                                        },
                                        interaction: {
                                            mode: 'index',
                                            intersect: false
                                        }
                                    }
                                });
                            }, 1000); // Simulate loading delay
                        }

                        // Subject Performance Chart (Bar Chart)
                        if (!subjectCanvas) {
                            console.error('Subject canvas not found');
                            subjectLoading.style.display = 'none';
                        } else if (!subjectData.labels.length || !subjectData.averages.length) {
                            subjectLoading.style.display = 'none';
                            const ctx = subjectCanvas.getContext('2d');
                            ctx.font = '12px Inter, sans-serif';
                            ctx.fillStyle = 'var(--text-secondary)';
                            ctx.textAlign = 'center';
                            ctx.fillText('No subject data available', subjectCanvas.width / 2, subjectCanvas.height / 2);
                        } else {
                            setTimeout(() => {
                                subjectLoading.style.display = 'none';
                                chartInstances['subjectChart'] = new Chart(subjectCanvas, {
                                    type: 'bar',
                                    data: {
                                        labels: subjectData.labels,
                                        datasets: [{
                                            label: 'Subject Average (%)',
                                            data: subjectData.averages,
                                            backgroundColor: (context) => {
                                                const ctx = context.chart.canvas.getContext('2d');
                                                const gradient = ctx.createLinearGradient(0, 0, 0, context.chart.height);
                                                gradient.addColorStop(0, 'rgba(75, 75, 255, 0.7)');
                                                gradient.addColorStop(1, 'rgba(139, 70, 255, 0.3)');
                                                return gradient;
                                            },
                                            borderColor: '#8b46ff',
                                            borderWidth: 1,
                                            hoverBackgroundColor: 'rgba(139, 70, 255, 0.9)',
                                            hoverBorderColor: '#4b4bff'
                                        }]
                                    },
                                    options: {
                                        responsive: true,
                                        maintainAspectRatio: false,
                                        scales: {
                                            y: {
                                                beginAtZero: true,
                                                max: 100,
                                                ticks: {
                                                    stepSize: 10,
                                                    color: '#4b4bff',
                                                    font: {
                                                        family: 'Inter, sans-serif',
                                                        size: 12
                                                    },
                                                    precision: 0
                                                },
                                                title: {
                                                    display: true,
                                                    text: 'Average Score (%)',
                                                    color: '#4b4bff',
                                                    font: {
                                                        family: 'Space Grotesk, sans-serif',
                                                        size: 14
                                                    }
                                                },
                                                grid: {
                                                    color: 'rgba(255, 255, 255, 0.1)',
                                                    borderDash: [5, 5]
                                                }
                                            },
                                            x: {
                                                title: {
                                                    display: true,
                                                    text: 'Subject',
                                                    color: '#4b4bff',
                                                    font: {
                                                        family: 'Space Grotesk, sans-serif',
                                                        size: 14
                                                    }
                                                },
                                                ticks: {
                                                    color: '#4b4bff',
                                                    font: {
                                                        family: 'Inter, sans-serif',
                                                        size: 12
                                                    },
                                                    autoSkip: false,
                                                    maxRotation: 45,
                                                    minRotation: 45
                                                },
                                                grid: {
                                                    color: 'rgba(255, 255, 255, 0.1)',
                                                    borderDash: [5, 5]
                                                }
                                            }
                                        },
                                        plugins: {
                                            legend: {
                                                display: true,
                                                position: 'top',
                                                labels: {
                                                    color: '#4b4bff',
                                                    font: {
                                                        family: 'Inter, sans-serif',
                                                        size: 12
                                                    },
                                                    padding: 15,
                                                    boxWidth: 15,
                                                    usePointStyle: true
                                                }
                                            },
                                            tooltip: {
                                                enabled: true,
                                                backgroundColor: 'rgba(255, 255, 255, 0.05)',
                                                titleColor: 'var(--white)',
                                                bodyColor: 'var(--white)',
                                                borderColor: 'rgba(255, 255, 255, 0.1)',
                                                borderWidth: 1,
                                                padding: 8,
                                                cornerRadius: 6,
                                                titleFont: {
                                                    family: 'Space Grotesk, sans-serif',
                                                    size: 12
                                                },
                                                bodyFont: {
                                                    family: 'Inter, sans-serif',
                                                    size: 11
                                                },
                                                callbacks: {
                                                    label: function(context) {
                                                        return `${context.dataset.label}: ${context.parsed.y.toFixed(1)}%`;
                                                    }
                                                }
                                            }
                                        },
                                        animation: {
                                            duration: 1500,
                                            easing: 'easeOutCubic',
                                            onProgress: function(animation) {
                                                const chart = animation.chart;
                                                chart.canvas.style.opacity = animation.currentStep / animation.numSteps;
                                            }
                                        },
                                        interaction: {
                                            mode: 'index',
                                            intersect: false
                                        }
                                    }
                                });
                            }, 1000); // Simulate loading delay
                        }
                    } catch (e) {
                        console.error('Chart initialization error:', e);
                        performanceLoading.style.display = 'none';
                        subjectLoading.style.display = 'none';
                    }
                @endif
            });
        </script>
    @endpush
@endsection
