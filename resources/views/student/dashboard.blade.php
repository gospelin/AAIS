@extends('student.layouts.app')

@section('title', 'Student Dashboard')

@section('description', 'View your academic progress, fee status, performance trends, and profile at Aunty Anne\'s International School.')

@push('styles')
    <style>
        .content-container {
            max-width: 90rem;
            margin: 0 auto;
            padding: var(--space-md) var(--space-sm);
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
        }

        .welcome-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gradient-primary);
        }

        .welcome-header {
            font-family: var(--font-display);
            font-size: clamp(1.25rem, 3vw, 1.75rem);
            font-weight: 700;
            background: var(--gradient-primary);
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .welcome-subtitle {
            font-family: var(--font-primary);
            font-size: clamp(0.625rem, 1.8vw, 0.75rem);
            color: var(--text-secondary);
        }

        .avatar-container {
            width: clamp(40px, 7vw, 48px);
            height: clamp(40px, 7vw, 48px);
            border-radius: 50%;
            background: var(--gradient-primary);
            padding: 2px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .avatar-placeholder {
            background: var(--light-gray);
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
            grid-template-columns: repeat(auto-fit, minmax(clamp(180px, 22vw, 220px), 1fr));
            gap: var(--space-md);
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
            transition: border-color 0.2s ease;
        }

        .stat-card:hover {
            border-color: var(--gold);
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
            width: clamp(28px, 5vw, 36px);
            height: clamp(28px, 5vw, 36px);
            background: var(--glass-bg);
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: clamp(0.75rem, 1.8vw, 0.875rem);
            color: var(--primary-green);
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
            color: var(--primary-green);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s ease;
        }

        .quick-access-link:hover {
            color: var(--dark-green);
        }

        .results-section, .performance-section {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-xl);
            overflow: hidden;
            margin-bottom: var(--space-xl);
        }

        .section-header {
            border-bottom: 1px solid var(--glass-border);
            padding: var(--space-xs) var(--space-sm);
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
        
        .chart-container {
            padding: var(--space-sm);
            min-height: 200px;
            position: relative;
        }

        .chart-scrollable {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .chart-scrollable canvas {
            min-width: 600px;
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
                min-height: 100px;
                padding: var(--space-xs);
            }

            .stat-icon {
                width: 28px;
                height: 28px;
                font-size: 0.75rem;
            }

            .stat-value {
                font-size: clamp(0.875rem, 2vw, 1rem);
            }

            .stat-label, .quick-access-link {
                font-size: clamp(0.5rem, 1.5vw, 0.625rem);
            }

            .section-title {
                font-size: clamp(0.75rem, 1.8vw, 0.875rem);
            }

            .chart-container {
                padding: var(--space-xs);
                min-height: 150px;
            }

            .chart-scrollable canvas {
                min-width: 400px;
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

            .welcome-section, .results-section, .performance-section {
                min-height: 70px;
                margin-bottom: var(--space-md);
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

            .welcome-header {
                font-size: clamp(0.875rem, 2vw, 1rem);
            }
        }

        .stat-card:focus-visible,
        .quick-access-link:focus-visible {
            outline: 2px solid var(--gold);
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
                    <p class="welcome-subtitle" id="welcomeSubtitle">Explore your academic progress, performance trends, and more.</p>
                </div>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
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
                <div class="chart-container chart-scrollable">
                    <canvas id="performanceChart" height="200"></canvas>
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
                    <h3 class="section-title">Subject Performance ({{ $currentSession->year }} - {{ $currentTerm->label() }})</h3>
                </div>
                <div class="chart-container chart-scrollable">
                    <canvas id="subjectChart" height="200"></canvas>
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
                    <p class="no-results-message">Please pay your fees for {{ $currentTerm->label() }} to access your results and performance data.</p>
                    <a href="{{ route('student.fee_status') }}" class="quick-access-link d-block text-center">View Fee Details →</a>
                </div>
            </section>
        @endif
    </div>
@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Verify canvas elements exist
            const performanceCanvas = document.getElementById('performanceChart');
            const subjectCanvas = document.getElementById('subjectChart');
            console.log('Performance Canvas:', performanceCanvas ? 'Found' : 'Not Found');
            console.log('Subject Canvas:', subjectCanvas ? 'Found' : 'Not Found');

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

            @if($hasPaidFees)
                try {
                    // Clone data to prevent Chart.js modification
                    const performanceData = JSON.parse(JSON.stringify(@json($performanceData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP)));
                    const subjectData = JSON.parse(JSON.stringify(@json($subjectData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP)));
                    console.log('Performance Data:', performanceData);
                    console.log('Subject Data:', subjectData);

                    // Performance Chart (Line Chart)
                    if (!performanceCanvas) {
                        console.error('Performance canvas not found');
                    } else if (!performanceData.labels || performanceData.labels.length === 0) {
                        const ctx = performanceCanvas.getContext('2d');
                        ctx.font = '14px Arial, var(--font-primary)';
                        ctx.fillStyle = 'var(--text-secondary)';
                        ctx.textAlign = 'center';
                        ctx.fillText('No performance data available', performanceCanvas.width / 2, performanceCanvas.height / 2);
                        console.log('No performance data available');
                    } else {
                        new Chart(performanceCanvas, {
                            type: 'line',
                            data: {
                                labels: performanceData.labels,
                                datasets: [{
                                    label: 'Term Average (%)',
                                    data: performanceData.averages,
                                    borderColor: '#21a055', // --primary-green
                                    backgroundColor: 'rgba(33, 160, 85, 0.3)',
                                    fill: true,
                                    tension: 0.4
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        max: 100,
                                        title: {
                                            display: true,
                                            text: 'Average Score (%)',
                                            color: '#333333', // --text-primary
                                            font: {
                                                family: 'Arial, var(--font-primary)',
                                                size: 16
                                            }
                                        },
                                        ticks: {
                                            color: '#666666', // --text-secondary
                                            font: {
                                                size: 14
                                            },
                                            precision: 0
                                        },
                                        grid: {
                                            color: 'rgba(255, 255, 255, 0.2)' // --glass-border
                                        }
                                    },
                                    x: {
                                        title: {
                                            display: true,
                                            text: 'Term',
                                            color: '#333333', // --text-primary
                                            font: {
                                                family: 'Arial, var(--font-primary)',
                                                size: 16
                                            }
                                        },
                                        ticks: {
                                            color: '#666666', // --text-secondary
                                            font: {
                                                size: 14
                                            }
                                        },
                                        grid: {
                                            color: 'rgba(255, 255, 255, 0.2)' // --glass-border
                                        }
                                    }
                                },
                                plugins: {
                                    legend: {
                                        labels: {
                                            color: '#333333', // --text-primary
                                            font: {
                                                family: 'Arial, var(--font-primary)',
                                                size: 16
                                            }
                                        }
                                    }
                                }
                            }
                        });
                        console.log('Performance chart initialized');
                    }

                    // Subject Performance Chart (Bar Chart)
                    if (!subjectCanvas) {
                        console.error('Subject canvas not found');
                    } else if (!subjectData.labels || subjectData.labels.length === 0) {
                        const ctx = subjectCanvas.getContext('2d');
                        ctx.font = '14px Arial, var(--font-primary)';
                        ctx.fillStyle = 'var(--text-secondary)';
                        ctx.textAlign = 'center';
                        ctx.fillText('No subject data available', subjectCanvas.width / 2, subjectCanvas.height / 2);
                        console.log('No subject data available');
                    } else {
                        new Chart(subjectCanvas, {
                            type: 'bar',
                            data: {
                                labels: subjectData.labels,
                                datasets: [{
                                    label: 'Subject Average (%)',
                                    data: subjectData.averages,
                                    backgroundColor: '#21a055', // --primary-green
                                    borderColor: '#1a8044', // --dark-green
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        max: 100,
                                        title: {
                                            display: true,
                                            text: 'Average Score (%)',
                                            color: '#333333', // --text-primary
                                            font: {
                                                family: 'Arial, var(--font-primary)',
                                                size: 16
                                            }
                                        },
                                        ticks: {
                                            color: '#666666', // --text-secondary
                                            font: {
                                                size: 14
                                            },
                                            precision: 0
                                        },
                                        grid: {
                                            color: 'rgba(255, 255, 255, 0.2)' // --glass-border
                                        }
                                    },
                                    x: {
                                        title: {
                                            display: true,
                                            text: 'Subject',
                                            color: '#333333', // --text-primary
                                            font: {
                                                family: 'Arial, var(--font-primary)',
                                                size: 16
                                            }
                                        },
                                        ticks: {
                                            color: '#666666', // --text-secondary
                                            font: {
                                                size: 14
                                            },
                                            autoSkip: false,
                                            maxRotation: 45,
                                            minRotation: 45
                                        },
                                        grid: {
                                            color: 'rgba(255, 255, 255, 0.2)' // --glass-border
                                        }
                                    }
                                },
                                plugins: {
                                    legend: {
                                        labels: {
                                            color: '#333333', // --text-primary
                                            font: {
                                                family: 'Arial, var(--font-primary)',
                                                size: 16
                                            }
                                        }
                                    }
                                }
                            }
                        });
                        console.log('Subject chart initialized');
                    }
                } catch (e) {
                    console.error('Chart initialization error:', e);
                }
            @endif
        });
    </script>
@endpush
@endsection
