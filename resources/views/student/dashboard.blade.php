@extends('student.layouts.app')

@section('title', 'Student Dashboard')

@section('description', 'View your academic progress, fee status, performance trends, and profile at Aunty Anne\'s International School.')

@push('styles')
    <style>
        .content-container {
            padding: var(--space-xl);
        }

        .content-header {
            position: relative;
            margin-bottom: 24px;
        }

        .page-title,
        .page-subtitle {
            white-space: normal;
            width: 100%;
            display: block;
            overflow: hidden;
        }

        .page-title {
            font-family: var(--font-display);
            font-size: clamp(1.5rem, 5vw, 2rem);
            font-weight: 700;
            color: var(--white);
            min-height: 45px;
        }

        .page-subtitle {
            font-family: var(--font-primary);
            font-size: clamp(0.875rem, 3vw, 1rem);
            color: #9ca3af;
            min-height: 24px;
        }

        body.light .page-title,
        body.light .page-subtitle {
            color: #6b7280;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(clamp(200px, 40vw, 280px), 1fr));
            gap: var(--space-xl);
            margin-bottom: var(--space-2xl);
        }

        .stat-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-xl);
            padding: var(--space-xl);
            position: relative;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-2xl);
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
        }

        .stat-icon {
            width: clamp(40px, 10vw, 60px);
            height: clamp(40px, 10vw, 60px);
            background: var(--glass-bg);
            border-radius: var(--radius-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: clamp(1rem, 3vw, 1.5rem);
            color: var(--primary);
        }

        body.light .stat-card {
            background: rgba(0, 0, 0, 0.05);
            border-color: rgba(0, 0, 0, 0.1);
        }

        .stat-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        
        body.light .stat-icon {
            background: rgba(0, 0, 0, 0.1);
        }

        .stat-value {
            font-size: clamp(1.5rem, 5vw, 2.5rem);
            font-weight: 800;
            margin-bottom: var(--space-sm);
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .stat-label {
            color: var(--gray-400);
            font-weight: 500;
        }

        body.light .stat-value,
        body.light .stat-label {
            color: #6b7280;
        }

        .results-section,
        .performance-section {
            padding: 24px;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            margin-bottom: 24px;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.3s ease;
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 0.5s ease forwards;
        }

        .results-section:hover,
        .performance-section:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .section-header {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding: 8px 16px;
            background: rgba(255, 255, 255, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .section-title {
            font-family: var(--font-display);
            font-size: clamp(1rem, 3vw, 1.25rem);
            font-weight: 600;
            color: var(--white);
        }

        body.light .section-title {
            color: #6b7280;
        }

        .chart-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(clamp(250px, 45vw, 300px), 1fr));
            gap: var(--space-xl);
            margin-bottom: var(--space-2xl);
        }

        .chart-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-xl);
            padding: var(--space-xl);
            position: relative;
            overflow: hidden;
        }

        .chart-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: var(--space-xl);
            flex-wrap: wrap;
        }

        .chart-title {
            font-family: var(--font-display);
            font-size: clamp(1rem, 3vw, 1.25rem);
            font-weight: 600;
        }

        .chart-btn {
            color: var(--primary);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .chart-btn:hover {
            color: var(--primary-light);
        }

        .chart-toggle {
            font-family: var(--font-primary);
            font-size: clamp(0.75rem, 2.5vw, 0.875rem);
            color: #10b981;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .chart-toggle:hover {
            color: #6366f1;
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
            background: rgba(0, 0, 0, 0.2);
        }

        body.light .table {
            background: rgba(0, 0, 0, 0.05);
        }

        .table th,
        .table td {
            font-family: var(--font-primary);
            font-size: clamp(0.75rem, 2.5vw, 0.875rem);
            color: var(--white);
            padding: 8px 16px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            text-align: left;
            vertical-align: middle;
            white-space: nowrap;
        }

        body.light .table th,
        body.light .table td {
            color: #6b7280;
        }

        .table th {
            background: rgba(255, 255, 255, 0.05);
            position: sticky;
            top: 0;
            z-index: 10;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            cursor: pointer;
            transition: background 0.2s ease;
        }

        .table th:hover {
            background: rgba(99, 102, 241, 0.7);
            color: var(--white);
        }

        .chart-container {
            padding: 16px;
            min-height: 300px;
            max-width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            position: relative;
        }

        body.light .chart-container {
            background: #f3f4f6;
        }

        .chart-responsive {
            max-width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        body.light .chart-responsive canvas {
            background: rgba(0, 0, 0, 0.05);
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
            gap: 8px;
            font-family: var(--font-primary);
            color: #9ca3af;
            font-size: clamp(0.75rem, 2.5vw, 0.875rem);
        }

        body.light .chart-loading {
            color: #6b7280;
        }

        .spinner {
            width: 24px;
            height: 24px;
            border: 3px solid #6366f1;
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
            color: #ef4444;
        }

        .text-success {
            color: #10b981;
        }

        .no-results-message {
            font-family: var(--font-primary);
            font-size: clamp(0.75rem, 2.5vw, 0.875rem);
            color: #9ca3af;
            text-align: center;
            padding: 16px;
        }

        body.light .no-results-message {
            color: #6b7280;
        }

        @media (max-width: 768px) {
            .content-container {
                padding: 16px;
            }

            .results-section,
            .performance-section {
                padding: 16px;
            }

            .stat-icon {
                width: 32px;
                height: 32px;
                font-size: 14px;
            }

            .stat-value {
                font-size: clamp(1rem, 3.5vw, 1.25rem);
            }

            .stat-label {
                font-size: clamp(0.75rem, 2.5vw, 0.875rem);
            }

            .content-header {
                margin-bottom: 16px;
            }

            .page-title {
                font-size: clamp(1.25rem, 4vw, 1.5rem);
                min-height: 40px;
            }

            .page-subtitle {
                font-size: clamp(0.75rem, 2.5vw, 0.875rem);
                min-height: 20px;
            }

            .chart-container {
                padding: 12px;
                min-height: 250px;
            }

            .section-title {
                font-size: clamp(0.875rem, 2.5vw, 1rem);
            }

            .chart-toggle {
                font-size: clamp(0.75rem, 2.5vw, 0.875rem);
            }
        }

        @media (max-width: 576px) {
            .content-container {
                padding: 12px;
            }

            .results-section,
            .performance-section {
                padding: 12px;
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

            .stats-grid {
                gap: 16px;
                margin-bottom: 24px;
            }

            .stat-card {
                padding: 8px;
            }

            .stat-icon {
                width: 28px;
                height: 28px;
                font-size: 12px;
            }

            .stat-value {
                font-size: clamp(0.875rem, 3vw, 1rem);
            }

            .stat-label {
                font-size: clamp(0.625rem, 2vw, 0.75rem);
            }

            .page-title {
                font-size: clamp(1rem, 3.5vw, 1.25rem);
                min-height: 36px;
            }

            .page-subtitle {
                font-size: clamp(0.625rem, 2vw, 0.75rem);
                min-height: 18px;
            }
        }

        .stat-card:focus-visible,
        .chart-toggle:focus-visible {
            outline: 2px solid #6366f1;
            outline-offset: 2px;
        }
    </style>
@endpush

@section('content')
    <div class="content-container">
        <div class="row">
            <!-- Content Header -->
            <div class="col-12">
                <div class="content-header" role="banner">
                    <h2 class="page-title" id="pageTitle">Welcome, {{ $student->full_name }}!</h2>
                    <p class="page-subtitle" id="pageSubtitle">Explore your academic progress, performance trends, and more.
                    </p>
                </div>
            </div>

            <!-- First Stats Grid -->
            <div class="col-12">
                <div class="stats-grid">
                    <div class="stat-card" tabindex="0" role="region" aria-label="Current Class">
                        <div class="stat-header">
                            <div class="stat-icon"><i class="fas fa-graduation-cap"></i></div>
                        </div>
                        <h3 class="stat-value">{{ $currentClass ?? 'Not Enrolled' }}</h3>
                        <p class="stat-label">Current Class</p>
                    </div>
                    <div class="stat-card" tabindex="0" role="region" aria-label="Fee Status">
                        <div class="stat-header">
                            <div class="stat-icon"><i class="fas fa-money-check-alt"></i></div>
                        </div>
                        <h3 class="stat-value">{{ $feeStatus && $feeStatus->has_paid_fee ? 'Paid' : 'Unpaid' }}</h3>
                        <p class="stat-label">Fee Status ({{ $currentTerm->label() }})</p>
                    </div>
                </div>
            </div>

            <!-- Second Stats Grid -->
            <div class="col-12">
                <div class="stats-grid">
                    @if($hasPaidFees)
                        <div class="stat-card" tabindex="0" role="region" aria-label="Current Term Results">
                            <div class="stat-header">
                                <div class="stat-icon"><i class="fas fa-book"></i></div>
                            </div>
                            <h3 class="stat-value">{{ $recentResults->count() }} Subjects</h3>
                            <p class="stat-label">Current Term Results</p>
                        </div>
                        <div class="stat-card" tabindex="0" role="region" aria-label="Best Subject">
                            <div class="stat-header">
                                <div class="stat-icon"><i class="fas fa-star"></i></div>
                            </div>
                            <h3 class="stat-value">{{ $bestSubject->name ?? 'N/A' }}</h3>
                            <p class="stat-label">Best Subject ({{ $currentSession->year }})</p>
                        </div>
                    @else
                        <div class="stat-card" tabindex="0" role="region" aria-label="Results Locked">
                            <div class="stat-header">
                                <div class="stat-icon"><i class="fas fa-exclamation-circle"></i></div>
                            </div>
                            <h3 class="stat-value">Results Locked</h3>
                            <p class="stat-label">Please pay your fees to view results.</p>
                        </div>
                        <div class="stat-card" tabindex="0" role="region" aria-label="No Data">
                            <div class="stat-header">
                                <div class="stat-icon"><i class="fas fa-info-circle"></i></div>
                            </div>
                            <h3 class="stat-value">N/A</h3>
                            <p class="stat-label">No data available</p>
                        </div>
                    @endif
                </div>
            </div>

            @if($hasPaidFees)
                <!-- Performance Overview -->
                <div class="col-12">
                    <section id="performance-overview" class="performance-section" role="region"
                        aria-label="Performance Trends">
                        <div class="section-header">
                            <h3 class="section-title">Performance Trends ({{ $currentSession->year }})</h3>
                            <span class="chart-toggle" onclick="toggleChartDataset('performanceChart')" role="button"
                                aria-label="Toggle performance chart dataset" tabindex="0"
                                onkeydown="if(event.key === 'Enter') toggleChartDataset('performanceChart')">
                                Toggle Dataset
                            </span>
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
                    <section id="subject-performance" class="performance-section" role="region"
                        aria-label="Subject Performance">
                        <div class="section-header">
                            <h3 class="section-title">Subject Performance ({{ $currentSession->year }} -
                                {{ $currentTerm->label() }})</h3>
                            <span class="chart-toggle" onclick="toggleChartDataset('subjectChart')" role="button"
                                aria-label="Toggle subject performance chart dataset" tabindex="0"
                                onkeydown="if(event.key === 'Enter') toggleChartDataset('subjectChart')">
                                Toggle Dataset
                            </span>
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
                    <section id="areas-improvement" class="performance-section" role="region"
                        aria-label="Areas Needing Improvement">
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
                            <h3 class="section-title">Recent Results ({{ $currentSession->year }} - {{ $currentTerm->label() }})
                            </h3>
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
                                                <td colspan="5" class="text-center stat-label">No results available for this term.
                                                </td>
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
                            <p class="no-results-message">Please pay your fees for {{ $currentTerm->label() }} to access your
                                results and performance data.</p>
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
                // Typing Animation for Content Header
                const pageTitle = document.getElementById('pageTitle');
                const pageSubtitle = document.getElementById('pageSubtitle');
                const titleText = pageTitle.textContent;
                const subtitleText = pageSubtitle.textContent;
                pageTitle.textContent = '';
                pageSubtitle.textContent = '';

                let titleIndex = 0;
                let subtitleIndex = 0;

                function typeTitle() {
                    if (titleIndex < titleText.length) {
                        pageTitle.textContent += titleText[titleIndex];
                        titleIndex++;
                        setTimeout(typeTitle, 100);
                    } else {
                        setTimeout(typeSubtitle, 500);
                    }
                }

                function typeSubtitle() {
                    if (subtitleIndex < subtitleText.length) {
                        pageSubtitle.textContent += subtitleText[subtitleIndex];
                        subtitleIndex++;
                        setTimeout(typeSubtitle, 100);
                    }
                }

                setTimeout(typeTitle, 500);

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
                            ctx.fillStyle = '#9ca3af';
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
                                            borderColor: '#6366f1',
                                            backgroundColor: (context) => {
                                                const ctx = context.chart.canvas.getContext('2d');
                                                const gradient = ctx.createLinearGradient(0, 0, 0, context.chart.height);
                                                gradient.addColorStop(0, 'rgba(99, 102, 241, 0.5)');
                                                gradient.addColorStop(1, 'rgba(99, 102, 241, 0.1)');
                                                return gradient;
                                            },
                                            fill: true,
                                            tension: 0.4,
                                            pointRadius: 6,
                                            pointHoverRadius: 8,
                                            pointBackgroundColor: '#ffffff',
                                            pointBorderColor: '#6366f1',
                                            pointBorderWidth: 4,
                                            pointHoverBackgroundColor: '#6366f1',
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
                                                    color: '#9ca3af',
                                                    font: {
                                                        family: 'Inter, sans-serif',
                                                        size: 12
                                                    },
                                                    precision: 0
                                                },
                                                title: {
                                                    display: true,
                                                    text: 'Average Score (%)',
                                                    color: '#ffffff',
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
                                                    color: '#ffffff',
                                                    font: {
                                                        family: 'Space Grotesk, sans-serif',
                                                        size: 14
                                                    }
                                                },
                                                ticks: {
                                                    color: '#9ca3af',
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
                                                    color: '#ffffff',
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
                                                titleColor: '#ffffff',
                                                bodyColor: '#ffffff',
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
                                                    label: function (context) {
                                                        return `${context.dataset.label}: ${context.parsed.y.toFixed(1)}%`;
                                                    }
                                                }
                                            }
                                        },
                                        animation: {
                                            duration: 1500,
                                            easing: 'easeOutCubic',
                                            onProgress: function (animation) {
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
                            }, 1000);
                        }

                        // Subject Performance Chart (Bar Chart)
                        if (!subjectCanvas) {
                            console.error('Subject canvas not found');
                            subjectLoading.style.display = 'none';
                        } else if (!subjectData.labels.length || !subjectData.averages.length) {
                            subjectLoading.style.display = 'none';
                            const ctx = subjectCanvas.getContext('2d');
                            ctx.font = '12px Inter, sans-serif';
                            ctx.fillStyle = '#9ca3af';
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
                                            backgroundColor: ['#10b981', '#f59e0b', '#ef4444', '#6366f1', '#0ea5e9'],
                                            borderColor: ['#10b981', '#f59e0b', '#ef4444', '#6366f1', '#0ea5e9'],
                                            borderWidth: 1,
                                            borderRadius: 8
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
                                                    color: '#9ca3af',
                                                    font: {
                                                        family: 'Inter, sans-serif',
                                                        size: 12
                                                    },
                                                    precision: 0
                                                },
                                                title: {
                                                    display: true,
                                                    text: 'Average Score (%)',
                                                    color: '#ffffff',
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
                                                    color: '#ffffff',
                                                    font: {
                                                        family: 'Space Grotesk, sans-serif',
                                                        size: 14
                                                    }
                                                },
                                                ticks: {
                                                    color: '#9ca3af',
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
                                                    color: '#ffffff',
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
                                                titleColor: '#ffffff',
                                                bodyColor: '#ffffff',
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
                                                    label: function (context) {
                                                        return `${context.dataset.label}: ${context.parsed.y.toFixed(1)}%`;
                                                    }
                                                }
                                            }
                                        },
                                        animation: {
                                            duration: 1500,
                                            easing: 'easeOutCubic',
                                            onProgress: function (animation) {
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
                            }, 1000);
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