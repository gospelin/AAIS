@extends('student.layouts.app')

@section('title', 'Student Dashboard')

@section('description', 'View your academic progress, fee status, performance trends, and profile at Aunty Anne\'s International School.')

@push('styles')
    <style>
        .content-container {
            max-width: 90rem;
            margin: 0 auto;
            padding: var(--space-md) var(--space-sm);
            overflow-x: hidden;
        }

        .welcome-section {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-xl);
            padding: var(--space-md);
            position: relative;
            overflow: hidden;
            margin-bottom: var(--space-md);
            min-height: 150px;
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
            font-size: clamp(2rem, 3.5vw, 2.5rem);
            font-weight: 700;
            background: var(--gradient-primary);
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .welcome-subtitle {
            font-family: var(--font-primary);
            font-size: clamp(0.825rem, 1.8vw, 0.95rem);
            color: var(--text-secondary);
        }

        .avatar-container {
            width: clamp(60px, 9vw, 65px);
            height: clamp(60px, 9vw, 65px);
            border-radius: 50%;
            background: var(--gradient-primary);
            padding: 2px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.3s ease;
        }

        .avatar-container:hover {
            transform: scale(1.1);
        }

        .avatar-placeholder {
            background: var(--gray-200);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: clamp(0.875rem, 2vw, 1rem);
            font-weight: 700;
            color: var(--white);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(clamp(250px, 22vw, 200px), 1fr));
            gap: 4rem;
            margin-bottom: var(--space-xl);
        }

        .stat-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-lg);
            padding: var(--space-sm);
            position: relative;
            overflow: hidden;
            min-height: 120px;
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
            width: clamp(28px, 5vw, 36px);
            height: clamp(28px, 5vw, 36px);
            background: var(--gradient-primary);
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: clamp(0.75rem, 1.8vw, 0.875rem);
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
            font-size: clamp(0.625rem, 1.6vw, 0.75rem);
            color: var(--text-secondary);
        }

        .quick-access-link {
            font-family: var(--font-primary);
            font-size: clamp(0.625rem, 1.6vw, 0.75rem);
            color: var(--success);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition-fast);
        }

        .quick-access-link:hover {
            color: var(--electric);
            text-decoration: underline;
        }

        .results-section,
        .performance-section {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-xl);
            overflow: hidden;
            margin-bottom: var(--space-xl);
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
        }

        .section-title {
            font-family: var(--font-display);
            font-size: clamp(0.875rem, 2vw, 1rem);
            font-weight: 600;
            color: var(--text-primary);
        }

        .table-responsive {
            max-height: 200px;
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
            font-size: clamp(0.75rem, 2vw, 0.875rem);
            color: var(--text-primary);
            padding: var(--space-sm) var(--space-md);
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
            padding: var(--space-sm);
            min-height: 200px;
            max-width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .chart-responsive {
            max-width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .chart-responsive canvas#performanceChart {
            min-width: clamp(300px, 100%, 600px);
            width: 100% !important;
            height: 200px !important;
            display: block !important;
        }

        .chart-responsive canvas#subjectChart {
            min-width: clamp(600px, 100%, 1200px);
            width: 100% !important;
            height: 200px !important;
            display: block !important;
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
            padding: var(--space-md);
        }

        @media (max-width: 768px) {
            .content-container {
                padding: var(--space-xs);
            }

            .stats-grid {
                grid-template-columns: 1fr;
                gap: var(--space-sm);
                margin-bottom: var(--space-lg);
            }

            .avatar-container {
                width: clamp(36px, 8vw, 40px);
                height: clamp(36px, 8vw, 40px);
            }

            .welcome-header {
                font-size: clamp(1rem, 2.5vw, 1.25rem);
            }

            .welcome-subtitle {
                font-size: clamp(0.5rem, 1.5vw, 0.625rem);
            }

            .stat-card {
                min-height: 150px;
                padding: 2rem;
            }

            .stat-icon {
                width: 28px;
                height: 28px;
                font-size: 1rem;
            }

            .stat-value {
                font-size: clamp(1rem, 2.5vw, 1.5rem);
            }

            .stat-label,
            .quick-access-link {
                font-size: clamp(1rem, 2vw, 1.5rem);
            }

            .section-title {
                font-size: clamp(1rem, 2vw, 1.5rem);
            }

            .chart-container {
                padding: var(--space-xs);
                min-height: 250px;
            }

            .chart-responsive canvas#performanceChart {
                min-width: clamp(250px, 100%, 400px);
                height: 150px !important;
            }

            .chart-responsive canvas#subjectChart {
                min-width: clamp(400px, 100%, 800px);
                height: 150px !important;
            }

            .table-responsive {
                max-height: 180px;
            }

            .no-results-message {
                font-size: clamp(0.625rem, 1.6vw, 0.75rem);
            }
        }

        @media (max-width: 576px) {
            .content-container {
                padding: calc(var(--space-xs) / 2);
            }

            .table-responsive {
                max-height: 150px;
            }

            .chart-container {
                min-height: 120px;
            }
        }

        @media (max-width: 360px) {
            .stats-grid {
                gap: calc(var(--space-xs) / 2);
            }

            .stat-card {
                min-height: 90px;
            }

            .avatar-container {
                width: 32px;
                height: 32px;
            }
        }

        .stat-card:focus-visible,
        .quick-access-link:focus-visible {
            outline: 2px solid var(--primary);
            outline-offset: 2px;
        }
    </style>
@endpush

@section('content')
    <div class="content-container">
        <!-- Welcome Section -->
        <div class="welcome-section">
            <div class="d-flex align-items-center gap-2">
                <div class="avatar-container">
                    @if($student->profile_pic && Storage::disk('public')->exists('profiles/' . $student->profile_pic))
                        <img src="{{ Storage::url('profiles/' . $student->profile_pic) . '?t=' . time() }}"
                            alt="{{ $student->full_name }}" class="h-full w-full rounded-full object-cover" loading="lazy">
                    @else
                        <div class="avatar-placeholder h-full w-full">
                            <span>{{ strtoupper(substr($student->first_name, 0, 1)) }}</span>
                        </div>
                    @endif
                </div>
                <div>
                    <h3 class="welcome-header" id="welcomeHeader">Welcome, {{ $student->full_name }}!</h3>
                    <p class="welcome-subtitle" id="welcomeSubtitle">Explore your academic progress, performance trends, and
                        more.</p>
                </div>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid gap-2">
            <div class="stat-card" tabindex="0">
                <div class="stat-header">
                    <div class="stat-icon"><i class="fas fa-graduation-cap"></i></div>
                </div>
                <h3 class="stat-value">{{ $currentClass ?? 'Not Enrolled' }}</h3>
                <p class="stat-label">Current Class</p>
                <a href="{{ route('student.profile') }}" class="quick-access-link">View Profile →</a>
            </div>
            <div class="stat-card" tabindex="0">
                <div class="stat-header">
                    <div class="stat-icon"><i class="fas fa-money-check-alt"></i></div>
                </div>
                <h3 class="stat-value">{{ $feeStatus && $feeStatus->has_paid_fee ? 'Paid' : 'Unpaid' }}</h3>
                <p class="stat-label">Fee Status ({{ $currentTerm->label() }})</p>
                <a href="{{ route('student.fee_status') }}" class="quick-access-link">View Fee Details →</a>
            </div>
            @if($hasPaidFees)
                <div class="stat-card" tabindex="0">
                    <div class="stat-header">
                        <div class="stat-icon"><i class="fas fa-book"></i></div>
                    </div>
                    <h3 class="stat-value">{{ $recentResults->count() }} Subjects</h3>
                    <p class="stat-label">Current Term Results</p>
                    <a href="{{ route('student.results') }}" class="quick-access-link">View Results →</a>
                </div>
                <div class="stat-card" tabindex="0">
                    <div class="stat-header">
                        <div class="stat-icon"><i class="fas fa-star"></i></div>
                    </div>
                    <h3 class="stat-value">{{ $bestSubject->name ?? 'N/A' }}</h3>
                    <p class="stat-label">Best Subject ({{ $currentSession->year }})</p>
                    <p class="stat-label">Avg: {{ $bestSubject->average ?? 'N/A' }}%</p>
                </div>
            @else
                <div class="stat-card" tabindex="0">
                    <div class="stat-header">
                        <div class="stat-icon"><i class="fas fa-exclamation-circle"></i></div>
                    </div>
                    <h3 class="stat-value">Results Locked</h3>
                    <p class="stat-label">Please pay your fees to view results.</p>
                    <a href="{{ route('student.fee_status') }}" class="quick-access-link">View Fee Details →</a>
                </div>
            @endif
        </div>

        @if($hasPaidFees)
            <!-- Performance Overview -->
            <section id="performance-overview" class="performance-section">
                <div class="section-header">
                    <h3 class="section-title">Performance Trends ({{ $currentSession->year }})</h3>
                </div>
                <div class="chart-container">
                    <div class="chart-responsive">
                        <canvas id="performanceChart"></canvas>
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

            <!-- Subject Performance -->
            <section id="subject-performance" class="performance-section">
                <div class="section-header">
                    <h3 class="section-title">Subject Performance ({{ $currentSession->year }} - {{ $currentTerm->label() }})
                    </h3>
                </div>
                <div class="chart-container">
                    <div class="chart-responsive">
                        <canvas id="subjectChart"></canvas>
                    </div>
                </div>
            </section>

            <!-- Areas Needing Improvement -->
            <section id="areas-improvement" class="performance-section">
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

            <!-- Recent Results -->
            <section id="recent-results" class="results-section">
                <div class="section-header">
                    <h3 class="section-title">Recent Results ({{ $currentSession->year }} - {{ $currentTerm->label() }})</h3>
                </div>
                <div class="p-2">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Subject</th>
                                    <th>Class</th>
                                    <th>Total</th>
                                    <th>Grade</th>
                                    <th>Remark</th>
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
        @else
            <!-- Fee Payment Warning -->
            <section class="results-section">
                <div class="section-header">
                    <h3 class="section-title">Results Unavailable</h3>
                </div>
                <div class="p-2">
                    <p class="no-results-message">Please pay your fees for {{ $currentTerm->label() }} to access your results
                        and performance data.</p>
                    <a href="{{ route('student.fee_status') }}" class="quick-access-link d-block text-center">View Fee Details
                        →</a>
                </div>
            </section>
        @endif
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

                @if($hasPaidFees)
                    try {
                        const performanceCanvas = document.getElementById('performanceChart');
                        const subjectCanvas = document.getElementById('subjectChart');

                        // Safely parse data
                        let performanceData = { labels: [], averages: [] };
                        let subjectData = { labels: [], averages: [] };
                        try {
                            performanceData = @json($performanceData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
                            subjectData = @json($subjectData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
                        } catch (e) {
                            console.error('Error parsing chart data:', e);
                        }

                        // Performance Chart (Line Chart)
                        if (!performanceCanvas) {
                            console.error('Performance canvas not found');
                        } else if (!performanceData.labels || performanceData.labels.length === 0 || !performanceData.averages) {
                            const ctx = performanceCanvas.getContext('2d');
                            ctx.font = 'clamp(12px, 1.8vw, 14px) Inter, sans-serif';
                            ctx.fillStyle = 'var(--text-secondary)';
                            ctx.textAlign = 'center';
                            ctx.fillText('No performance data available', performanceCanvas.width / 2, performanceCanvas.height / 2);
                        } else {
                            new Chart(performanceCanvas, {
                                type: 'line',
                                data: {
                                    labels: performanceData.labels,
                                    datasets: [{
                                        label: 'Term Average (%)',
                                        data: performanceData.averages,
                                        borderColor: '#4b4bff', // --primary
                                        backgroundColor: 'rgba(75, 75, 255, 0.3)', // Semi-transparent --primary
                                        fill: true,
                                        tension: 0.4,
                                        pointRadius: 6,
                                        pointHoverRadius: 8,
                                        pointBackgroundColor: '#ffffff', // --white
                                        pointBorderColor: '#8b46ff', // --secondary
                                        pointBorderWidth: 4
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
                                                stepSize: 10, // Count by 10s for better readability
                                                color: '#4b4bff',
                                                font: {
                                                    family: 'Inter, sans-serif',
                                                    size: 14
                                                },
                                                precision: 0
                                            },
                                            title: {
                                                display: true,
                                                text: 'Average Score (%)',
                                                color: '#4b4bff',
                                                font: {
                                                    family: 'Space Grotesk, sans-serif',
                                                    size: 16
                                                }
                                            },
                                            grid: {
                                                color: 'rgba(255, 255, 255, 0.1)', // --glass-border
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
                                                    size: 16
                                                }
                                            },
                                            ticks: {
                                                color: '#4b4bff',
                                                font: {
                                                    family: 'Inter, sans-serif',
                                                    size: 14
                                                }
                                            },
                                            grid: {
                                                color: 'rgba(255, 255, 255, 0.1)', // --glass-border
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
                                                    size: 14
                                                },
                                                padding: 20,
                                                boxWidth: 20
                                            }
                                        },
                                        tooltip: {
                                            enabled: true,
                                            backgroundColor: 'rgba(255, 255, 255, 0.05)', // --glass-bg
                                            titleColor: '#ffffff', // --white
                                            bodyColor: '#ffffff', // --white
                                            borderColor: 'rgba(255, 255, 255, 0.1)', // --glass-border
                                            borderWidth: 1,
                                            padding: 10,
                                            cornerRadius: 8,
                                            titleFont: {
                                                family: 'Space Grotesk, sans-serif',
                                                size: 14
                                            },
                                            bodyFont: {
                                                family: 'Inter, sans-serif',
                                                size: 13
                                            }
                                        }
                                    },
                                    animation: {
                                        duration: 1000,
                                        easing: 'easeOutCubic'
                                    }
                                }
                            });
                        }

                        // Subject Performance Chart (Bar Chart)
                        if (!subjectCanvas) {
                            console.error('Subject canvas not found');
                        } else if (!subjectData.labels || subjectData.labels.length === 0 || !subjectData.averages) {
                            const ctx = subjectCanvas.getContext('2d');
                            ctx.font = 'clamp(12px, 1.8vw, 14px) Inter, sans-serif';
                            ctx.fillStyle = 'var(--text-secondary)';
                            ctx.textAlign = 'center';
                            ctx.fillText('No subject data available', subjectCanvas.width / 2, subjectCanvas.height / 2);
                        } else {
                            new Chart(subjectCanvas, {
                                type: 'bar',
                                data: {
                                    labels: subjectData.labels,
                                    datasets: [{
                                        label: 'Subject Average (%)',
                                        data: subjectData.averages,
                                        backgroundColor: 'rgba(75, 75, 255, 0.7)', // Derived from --primary
                                        borderColor: '#8b46ff', // --secondary
                                        borderWidth: 1,
                                        hoverBackgroundColor: 'rgba(139, 70, 255, 0.7)', // Derived from --secondary
                                        hoverBorderColor: '#4b4bff' // --primary
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
                                                stepSize: 10, // Count by 10s for better readability
                                                color: '#4b4bff',
                                                font: {
                                                    family: 'Inter, sans-serif',
                                                    size: 14
                                                },
                                                precision: 0
                                            },
                                            title: {
                                                display: true,
                                                text: 'Average Score (%)',
                                                color: '#4b4bff',
                                                font: {
                                                    family: 'Space Grotesk, sans-serif',
                                                    size: 16
                                                }
                                            },
                                            grid: {
                                                color: 'rgba(255, 255, 255, 0.1)', // --glass-border
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
                                                    size: 16
                                                }
                                            },
                                            ticks: {
                                                color: '#4b4bff',
                                                font: {
                                                    family: 'Inter, sans-serif',
                                                    size: 14
                                                },
                                                autoSkip: false,
                                                maxRotation: 45,
                                                minRotation: 45
                                            },
                                            grid: {
                                                color: 'rgba(255, 255, 255, 0.1)', // --glass-border
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
                                                    size: 16
                                                },
                                                padding: 20,
                                                boxWidth: 20
                                            }
                                        },
                                        tooltip: {
                                            enabled: true,
                                            backgroundColor: 'rgba(255, 255, 255, 0.05)', // --glass-bg
                                            titleColor: '#ffffff', // --white
                                            bodyColor: '#ffffff', // --white
                                            borderColor: 'rgba(255, 255, 255, 0.1)', // --glass-border
                                            borderWidth: 1,
                                            padding: 10,
                                            cornerRadius: 8,
                                            titleFont: {
                                                family: 'Space Grotesk, sans-serif',
                                                size: 14
                                            },
                                            bodyFont: {
                                                family: 'Inter, sans-serif',
                                                size: 13
                                            }
                                        }
                                    },
                                    animation: {
                                        duration: 1000,
                                        easing: 'easeOutCubic'
                                    }
                                }
                            });
                        }
                    } catch (e) {
                        console.error('Chart initialization error:', e);
                    }
                @endif
                });
        </script>
    @endpush
@endsection
