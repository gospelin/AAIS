@extends('admin.layouts.app')

@section('title', 'Admin Dashboard')

@section('description', 'Manage students, staff, courses, grades, and announcements for Aunty Anne\'s International School.')

@push('styles')
    <style>
        .content-container {
            max-width: 90rem;
            margin: 0 auto;
            padding: var(--space-lg) var(--space-md);
        }

        .welcome-section {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-xl);
            padding: var(--space-xl);
            position: relative;
            overflow: hidden;
            margin-bottom: var(--space-2xl);
            min-height: 160px;
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
            font-size: clamp(1.75rem, 4vw, 2.5rem);
            font-weight: 700;
            background: var(--gradient-primary);
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .welcome-subtitle {
            font-size: clamp(0.875rem, 2.5vw, 1rem);
            color: var(--text-secondary);
        }

        .avatar-container {
            width: clamp(60px, 10vw, 80px);
            height: clamp(60px, 10vw, 80px);
            border-radius: 50%;
            background: var(--gradient-primary);
            padding: 3px;
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
            font-size: clamp(1.25rem, 3vw, 1.5rem);
            font-weight: 700;
            color: var(--white);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(clamp(250px, 30vw, 280px), 1fr));
            gap: var(--space-lg);
            margin-bottom: var(--space-2xl);
        }

        .stat-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-lg);
            padding: var(--space-lg);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            min-height: 160px;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-2xl);
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
            width: clamp(40px, 8vw, 48px);
            height: clamp(40px, 8vw, 48px);
            background: var(--glass-bg);
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: clamp(1rem, 2.5vw, 1.25rem);
            color: var(--primary-green);
        }

        .stat-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: var(--space-md);
        }

        .stat-trend {
            display: flex;
            align-items: center;
            gap: var(--space-xs);
            font-size: clamp(0.75rem, 2vw, 0.875rem);
            color: var(--text-secondary);
        }

        .stat-trend.up {
            color: var(--success);
        }

        .stat-trend.down {
            color: var(--error);
        }

        .stat-value {
            font-family: var(--font-display);
            font-size: clamp(1.5rem, 3.5vw, 1.75rem);
            font-weight: 600;
            color: var(--text-primary);
        }

        .stat-label {
            font-size: clamp(0.875rem, 2.5vw, 1rem);
            color: var(--text-secondary);
        }

        .quick-access-link {
            font-size: clamp(0.875rem, 2.5vw, 1rem);
            color: var(--primary-green);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s ease;
        }

        .quick-access-link:hover {
            color: var(--dark-green);
        }

        .chart-section {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-xl);
            overflow: hidden;
            min-height: 250px;
            margin-bottom: var(--space-2xl);
        }

        .chart-header {
            border-bottom: 1px solid var(--glass-border);
            padding: var(--space-md) var(--space-lg);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .chart-title {
            font-family: var(--font-display);
            font-size: clamp(1.25rem, 3vw, 1.5rem);
            font-weight: 600;
            color: var(--text-primary);
        }

        .chart-actions {
            display: flex;
            gap: var(--space-sm);
        }

        .chart-btn {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-md);
            padding: var(--space-sm) var(--space-md);
            color: var(--text-secondary);
            font-size: clamp(0.75rem, 2vw, 0.875rem);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .chart-btn:hover,
        .chart-btn.active {
            background: var(--primary-green);
            border-color: var(--primary-green);
            color: var(--white);
        }

        .activity-section {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-xl);
            overflow: hidden;
            min-height: 250px;
        }

        .activity-header {
            border-bottom: 1px solid var(--glass-border);
            padding: var(--space-md) var(--space-lg);
        }

        .activity-content {
            padding: var(--space-lg);
        }

        .activity-item {
            display: flex;
            align-items: center;
            gap: var(--space-sm);
            padding: var(--space-sm) 0;
        }

        .activity-dot {
            width: 10px;
            height: 10px;
            background: var(--primary-green);
            border-radius: 50%;
        }

        .activity-description {
            font-size: clamp(0.875rem, 2.5vw, 1rem);
            color: var(--text-secondary);
        }

        .activity-time {
            font-size: clamp(0.75rem, 2vw, 0.875rem);
            color: var(--text-secondary);
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }

            .welcome-section {
                padding: var(--space-lg);
                min-height: 120px;
            }

            .avatar-container {
                width: clamp(48px, 12vw, 60px);
                height: clamp(48px, 12vw, 60px);
            }
        }

        @media (max-width: 576px) {
            .content-container {
                padding: var(--space-md);
            }

            .chart-section,
            .activity-section {
                min-height: 200px;
            }
        }

        /* Accessibility Enhancements */
        .stat-card:focus-visible,
        .quick-access-link:focus-visible,
        .chart-btn:focus-visible {
            outline: 2px solid var(--gold);
            outline-offset: 2px;
        }
    </style>
@endpush

@section('content')
    <div class="content-container">
        <!-- Welcome Section -->
        <div class="welcome-section">
            <div class="d-flex align-items-center gap-3">
                <div class="avatar-container">
                    <div class="avatar-placeholder h-full w-full">
                        <span>{{ auth()->check() ? strtoupper(substr(auth()->user()->name, 0, 1)) : 'A' }}</span>
                    </div>
                </div>
                <div>
                    <h3 class="welcome-header">Welcome back, {{ auth()->check() ? auth()->user()->name : 'Admin' }}!</h3>
                    <p class="welcome-subtitle" id="pageSubtitle">Manage students, staff, courses, grades, and
                        announcements.</p>
                </div>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid" data-total-students="150" data-student-trend-value="5%" data-student-trend-status="up"
            data-total-staff="20" data-staff-trend-value="2%" data-staff-trend-status="up" data-total-courses="25"
            data-course-trend-value="0%" data-course-trend-status="stable">
            <div class="stat-card" id="totalStudents" tabindex="0">
                <div class="stat-header">
                    <div class="stat-icon"><i class="fas fa-users"></i></div>
                    <div class="stat-trend up">
                        <i class="fas fa-arrow-up"></i>
                        <span>5%</span>
                    </div>
                </div>
                <h3 class="stat-value">150</h3>
                <p class="stat-label">Total Students</p>
                <a href="#" class="quick-access-link">Manage Students →</a>
            </div>
            <div class="stat-card" id="totalStaff" tabindex="0">
                <div class="stat-header">
                    <div class="stat-icon"><i class="fas fa-user-tie"></i></div>
                    <div class="stat-trend up">
                        <i class="fas fa-arrow-up"></i>
                        <span>2%</span>
                    </div>
                </div>
                <h3 class="stat-value">20</h3>
                <p class="stat-label">Total Staff</p>
                <a href="#" class="quick-access-link">Manage Staff →</a>
            </div>
            <div class="stat-card" id="totalCourses" tabindex="0">
                <div class="stat-header">
                    <div class="stat-icon"><i class="fas fa-book"></i></div>
                    <div class="stat-trend stable">
                        <i class="fas fa-arrow-right"></i>
                        <span>0%</span>
                    </div>
                </div>
                <h3 class="stat-value">25</h3>
                <p class="stat-label">Total Courses</p>
                <a href="#" class="quick-access-link">Manage Courses →</a>
            </div>
            <div class="stat-card" id="totalGrades" tabindex="0">
                <div class="stat-header">
                    <div class="stat-icon"><i class="fas fa-clipboard-list"></i></div>
                    <div class="stat-trend">
                        <i class="fas fa-arrow-right"></i>
                        <span>View All</span>
                    </div>
                </div>
                <h3 class="stat-value">Grades</h3>
                <p class="stat-label">Manage Student Grades</p>
                <a href="#" class="quick-access-link">Manage Grades →</a>
            </div>
            <div class="stat-card" id="totalAnnouncements" tabindex="0">
                <div class="stat-header">
                    <div class="stat-icon"><i class="fas fa-bullhorn"></i></div>
                    <div class="stat-trend">
                        <i class="fas fa-arrow-right"></i>
                        <span>View All</span>
                    </div>
                </div>
                <h3 class="stat-value">Announcements</h3>
                <p class="stat-label">Manage School Announcements</p>
                <a href="#" class="quick-access-link">Manage Announcements →</a>
            </div>
        </div>

        <!-- Chart Section -->
        <div class="chart-section" data-student-growth-daily="[5, 3, 7, 2, 4, 6, 1]"
            data-labels-daily='["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"]'
            data-student-growth-weekly="[20, 25, 30, 28, 35, 40]"
            data-labels-weekly='["Week 1", "Week 2", "Week 3", "Week 4", "Week 5", "Week 6"]'
            data-student-growth-monthly="[100, 120, 130, 150, 140, 160]"
            data-labels-monthly='["Jan", "Feb", "Mar", "Apr", "May", "Jun"]'>
            <div class="chart-header">
                <h4 class="chart-title">Student Growth</h4>
                <div class="chart-actions">
                    <button class="chart-btn" data-period="daily">Day</button>
                    <button class="chart-btn" data-period="weekly">Week</button>
                    <button class="chart-btn active" data-period="monthly">Month</button>
                </div>
            </div>
            <div class="p-4">
                <canvas id="studentGrowthChart"></canvas>
            </div>
        </div>

        <!-- Recent Activity -->
        <section id="activity">
            <div class="activity-section">
                <div class="activity-header">
                    <h3 class="chart-title">Recent Admin Activity</h3>
                </div>
                <div class="activity-content">
                    <div class="space-y-4">
                        <div class="activity-item">
                            <div class="activity-dot"></div>
                            <div>
                                <p class="activity-description">Student John Doe added to class 5A</p>
                                <p class="activity-time">Sep 10, 2025 14:30</p>
                            </div>
                        </div>
                        <div class="activity-item">
                            <div class="activity-dot"></div>
                            <div>
                                <p class="activity-description">Teacher Jane Smith assigned to Math</p>
                                <p class="activity-time">Sep 10, 2025 10:15</p>
                            </div>
                        </div>
                        <div class="activity-item">
                            <div class="activity-dot"></div>
                            <div>
                                <p class="activity-description">Class 6B results updated</p>
                                <p class="activity-time">Sep 9, 2025 09:00</p>
                            </div>
                        </div>
                        <div class="activity-item">
                            <div class="activity-dot"></div>
                            <div>
                                <p class="activity-description">New session 2025/2026 created</p>
                                <p class="activity-time">Sep 8, 2025 16:45</p>
                            </div>
                        </div>
                        <div class="activity-item">
                            <div class="activity-dot"></div>
                            <div>
                                <p class="activity-description">Student fees status updated</p>
                                <p class="activity-time">Sep 7, 2025 12:20</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                // Typing Animation for Welcome Section
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
                        setTimeout(typeTitle, 80);
                    } else {
                        setTimeout(typeSubtitle, 400);
                    }
                }

                function typeSubtitle() {
                    if (subtitleIndex < subtitleText.length) {
                        pageSubtitle.textContent += subtitleText[subtitleIndex];
                        subtitleIndex++;
                        setTimeout(typeSubtitle, 80);
                    }
                }

                setTimeout(typeTitle, 400);

                // GSAP Animations
                // gsap.from('.stat-card', { opacity: 0, y: 20, stagger: 0.15, duration: 0.5 });
                // gsap.from('.chart-section', { opacity: 0, y: 20, duration: 0.5, delay: 0.4 });
                // gsap.from('.activity-section', { opacity: 0, y: 20, duration: 0.5, delay: 0.6 });

                // Chart.js Initialization
                const chartCard = document.querySelector('.chart-section');
                const studentGrowthDaily = JSON.parse(chartCard.dataset.studentGrowthDaily);
                const labelsDaily = JSON.parse(chartCard.dataset.labelsDaily);
                const studentGrowthWeekly = JSON.parse(chartCard.dataset.studentGrowthWeekly);
                const labelsWeekly = JSON.parse(chartCard.dataset.labelsWeekly);
                const studentGrowthMonthly = JSON.parse(chartCard.dataset.studentGrowthMonthly);
                const labelsMonthly = JSON.parse(chartCard.dataset.labelsMonthly);

                const validateData = (data) => Array.isArray(data) ? data.map(val => isFinite(val) ? Number(val) : 0) : [0, 0, 0, 0, 0, 0];

                const ctx = document.getElementById('studentGrowthChart').getContext('2d');
                const chart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labelsMonthly,
                        datasets: [{
                            label: 'Student Growth',
                            data: validateData(studentGrowthMonthly),
                            backgroundColor: 'rgba(33, 160, 85, 0.5)',
                            borderColor: 'var(--primary-green)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: { color: 'rgba(255, 255, 255, 0.1)' },
                                ticks: { color: 'var(--text-secondary)' }
                            },
                            x: {
                                grid: { display: false },
                                ticks: { color: 'var(--text-secondary)' }
                            }
                        },
                        plugins: { legend: { display: false } }
                    }
                });

                document.querySelectorAll('.chart-btn').forEach(button => {
                    button.addEventListener('click', () => {
                        document.querySelectorAll('.chart-btn').forEach(btn => btn.classList.remove('active'));
                        button.classList.add('active');

                        const period = button.dataset.period;
                        let data, labels;
                        if (period === 'daily') {
                            data = validateData(studentGrowthDaily);
                            labels = labelsDaily;
                        } else if (period === 'weekly') {
                            data = validateData(studentGrowthWeekly);
                            labels = labelsWeekly;
                        } else {
                            data = validateData(studentGrowthMonthly);
                            labels = labelsMonthly;
                        }

                        chart.data.labels = labels;
                        chart.data.datasets[0].data = data;
                        chart.update();
                    });
                });
            });
        </script>
    @endpush
@endsection
