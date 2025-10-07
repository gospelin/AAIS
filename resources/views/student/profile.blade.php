@extends('student.layouts.app')

@section('title', 'Student Profile')

@section('description', 'View your profile, class history, and manage your profile picture at Aunty Anne\'s International School.')

@push('styles')
    <style>
        .content-container {
            max-width: 90rem;
            margin: 0 auto;
            padding: var(--space-md) var(--space-sm);
            overflow-x: hidden;
        }

        .profile-section {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-xl);
            padding: var(--space-md);
            margin-bottom: var(--space-xl);
            position: relative;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .profile-section:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }

        .profile-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gradient-primary);
            transition: transform 0.5s ease;
        }

        .profile-section:hover::before {
            transform: scaleX(1.05);
        }

        .section-title {
            font-family: var(--font-display);
            font-size: clamp(0.875rem, 2vw, 1rem);
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: var(--space-md);
            background: var(--gradient-primary);
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .profile-card {
            display: flex;
            gap: var(--space-lg);
            align-items: flex-start;
            flex-wrap: wrap;
        }

        .profile-image {
            width: clamp(80px, 12vw, 100px);
            height: clamp(80px, 12vw, 100px);
            border-radius: 50%;
            background: var(--gradient-primary);
            padding: 3px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .profile-image img,
        .profile-placeholder {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            background: var(--dark-card);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: var(--font-primary);
            font-size: clamp(1.25rem, 2.5vw, 1.5rem);
            font-weight: 700;
            color: var(--white);
        }

        html.dark .profile-image img,
        html.dark .profile-placeholder {
            background: var(--dark-card);
        }

        .profile-details p {
            font-family: var(--font-primary);
            font-size: clamp(0.75rem, 2vw, 0.875rem);
            color: var(--text-primary);
            margin-bottom: var(--space-sm);
        }

        .profile-details strong {
            color: var(--text-secondary);
            font-weight: 500;
        }

        .profile-upload-form {
            margin-top: var(--space-md);
            display: flex;
            flex-direction: column;
            gap: var(--space-sm);
            max-width: 300px;
        }

        .form-label {
            font-family: var(--font-primary);
            font-size: clamp(0.625rem, 1.6vw, 0.75rem);
            color: var(--text-secondary);
            margin-bottom: var(--space-xs);
            font-weight: 500;
        }

        .form-control {
            background: var(--glass-bg);
            color: var(--text-primary);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-lg);
            padding: var(--space-sm) var(--space-md);
            font-family: var(--font-primary);
            font-size: clamp(0.625rem, 1.6vw, 0.75rem);
            transition: border-color 0.2s ease, background 0.2s ease, box-shadow 0.2s ease;
        }

        html.dark .form-control {
            background: var(--glass-bg) !important;
            color: var(--text-primary);
            backdrop-filter: blur(20px);
        }

        .form-control:focus {
            border-color: var(--primary);
            /* #4b4bff */
            outline: none;
            box-shadow: 0 0 0 3px rgba(75, 75, 255, 0.3);
        }

        .btn-primary,
        .btn-danger {
            background: var(--primary);
            /* #4b4bff */
            color: var(--white);
            padding: var(--space-sm) var(--space-lg);
            border-radius: var(--radius-lg);
            text-decoration: none;
            font-family: var(--font-primary);
            font-size: clamp(0.625rem, 1.6vw, 0.75rem);
            font-weight: 500;
            border: 1px solid transparent;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            cursor: pointer;
        }

        .btn-danger {
            background: var(--error);
        }

        .btn-primary:hover {
            background: var(--secondary);
            /* #8b46ff */
            border-color: var(--primary);
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .btn-danger:hover {
            background: darken(var(--error), 10%);
            border-color: var(--error);
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .btn-primary:focus-visible,
        .btn-danger:focus-visible {
            outline: 2px solid var(--secondary);
            outline-offset: 2px;
        }

        .history-section {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-xl);
            overflow: hidden;
            min-height: 200px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .history-section:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }

        .history-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gradient-primary);
            transition: transform 0.5s ease;
        }

        .history-section:hover::before {
            transform: scaleX(1.05);
        }

        .section-header {
            border-bottom: 1px solid var(--glass-border);
            padding: var(--space-xs) var(--space-sm);
            background: var(--glass-bg);
        }

        .table-responsive {
            max-height: 300px;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: rgba(75, 75, 255, 0.7) var(--glass-bg);
            /* --primary */
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
            padding: var(--space-md) var(--space-md);
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
            transition: background 0.2s ease;
        }

        .table th:hover {
            background: var(--gradient-primary);
            color: var(--white);
        }

        .table tbody tr {
            transition: background 0.2s ease, transform 0.3s ease;
        }

        .table tbody tr:nth-child(even) {
            background: rgba(255, 255, 255, 0.05);
        }

        html.dark .table tbody tr:nth-child(even) {
            background: rgba(255, 255, 255, 0.05);
        }

        .table tbody tr:hover {
            background: rgba(75, 75, 255, 0.1);
            transform: translateY(-2px);
        }

        .table tbody td {
            border-right: 1px solid var(--glass-border);
        }

        .table tbody td:last-child {
            border-right: none;
        }

        .alert {
            padding: var(--space-sm) var(--space-md);
            border-radius: var(--radius-lg);
            font-family: var(--font-primary);
            font-size: clamp(0.625rem, 1.6vw, 0.75rem);
            margin-bottom: var(--space-md);
        }

        .alert-success {
            background: rgba(0, 128, 0, 0.1);
            /* --success with opacity */
            color: var(--success);
            border: 1px solid var(--success);
        }

        .alert-danger,
        .alert-error {
            background: rgba(220, 53, 69, 0.1);
            /* --error with opacity */
            color: var(--error);
            border: 1px solid var(--error);
        }

        .alert-info {
            background: rgba(75, 75, 255, 0.1);
            /* --primary with opacity */
            color: var(--primary);
            border: 1px solid var(--primary);
        }

        @media (max-width: 768px) {
            .content-container {
                padding: var(--space-xs);
            }

            .profile-section,
            .history-section {
                padding: var(--space-sm);
            }

            .profile-card {
                flex-direction: column;
                align-items: flex-start;
            }

            .profile-image {
                width: clamp(60px, 10vw, 80px);
                height: clamp(60px, 10vw, 80px);
            }

            .table th,
            .table td {
                font-size: clamp(0.625rem, 1.6vw, 0.75rem);
                padding: var(--space-sm) var(--space-md);
            }
        }

        @media (max-width: 576px) {
            .table-responsive {
                max-height: 250px;
            }

            .section-title {
                font-size: clamp(0.75rem, 1.8vw, 0.875rem);
            }
        }

        @media (max-width: 360px) {
            .content-container {
                padding: calc(var(--space-xs) / 2);
            }

            .profile-section,
            .history-section {
                min-height: 120px;
            }
        }

        .profile-section:focus-visible,
        .history-section:focus-visible,
        .btn-primary:focus-visible,
        .btn-danger:focus-visible {
            outline: 2px solid var(--primary);
            outline-offset: 2px;
        }
    </style>
@endpush

@section('content')
    <div class="content-container">
        <!-- Profile Section -->
        <div class="profile-section" tabindex="0">
            <h3 class="section-title">Your Profile</h3>
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-error">
                    {{ session('error') }}
                </div>
            @endif
            @if (session('info'))
                <div class="alert alert-info">
                    {{ session('info') }}
                </div>
            @endif
            <div class="profile-card">
                <div class="profile-image">
                    @if($student->profile_pic && Storage::disk('public')->exists('profiles/' . $student->profile_pic))
                        <img src="{{ Storage::url('profiles/' . $student->profile_pic) . '?t=' . time() }}"
                            alt="{{ $student->full_name }}" loading="lazy">
                    @else
                        <div class="profile-placeholder">
                            <span>{{ strtoupper(substr($student->first_name, 0, 1)) }}</span>
                        </div>
                    @endif
                </div>
                <div class="profile-details">
                    <p><strong>Full Name:</strong> {{ $student->full_name }}</p>
                    <p><strong>Registration Number:</strong> {{ $student->reg_no }}</p>
                    <p><strong>Gender:</strong> {{ $student->gender }}</p>
                    <p><strong>Date of Birth:</strong>
                        {{ $student->date_of_birth ? $student->date_of_birth->format('Y-m-d') : 'Not Set' }}</p>
                    <p><strong>Parent Name:</strong> {{ $student->parent_name ?? 'Not Set' }}</p>
                    <p><strong>Parent Phone:</strong> {{ $student->parent_phone_number ?? 'Not Set' }}</p>
                    <p><strong>Address:</strong> {{ $student->address ?? 'Not Set' }}</p>
                    <p><strong>Current Class:</strong>
                        {{ $student->getCurrentClass($currentSession->id, $currentTerm) ?? 'Not Enrolled' }}</p>
                </div>
                <div class="profile-upload-form">
                    <form action="{{ route('student.profile.picture.update') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div>
                            <label for="profile_picture" class="form-label">Upload Profile Picture</label>
                            <input type="file" name="profile_picture" id="profile_picture" class="form-control"
                                accept="image/jpeg,image/png">
                            @error('profile_picture')
                                <div class="alert alert-error mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload"></i> Upload Picture
                        </button>
                    </form>
                    @if($student->profile_pic && Storage::disk('public')->exists('profiles/' . $student->profile_pic))
                        <form action="{{ route('student.profile.picture.delete') }}" method="POST" class="mt-2">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash"></i> Delete Picture
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- Class History Section -->
        <div class="history-section" tabindex="0">
            <div class="section-header">
                <h3 class="section-title">Class History</h3>
            </div>
            <div class="p-4">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Session</th>
                                <th>Class</th>
                                <th>Start Term</th>
                                <th>End Term</th>
                                <th>Join Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="historyTable">
                            @forelse ($classHistory as $history)
                                <tr>
                                    <td>{{ $history->session->year }}</td>
                                    <td>{{ $history->class->name }}</td>
                                    <td>{{ $history->start_term ? $history->start_term->label() : 'Not Set' }}</td>
                                    <td>{{ $history->end_term ? $history->end_term->label() : 'Ongoing' }}</td>
                                    <td>{{ $history->join_date->format('Y-m-d') }}</td>
                                    <td>{{ $history->is_active ? 'Active' : 'Inactive' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center stat-label">No class history available.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                // Section Animations
                const profileSection = document.querySelector('.profile-section');
                const historySection = document.querySelector('.history-section');
                profileSection.style.opacity = '0';
                profileSection.style.transform = 'translateY(20px)';
                historySection.style.opacity = '0';
                historySection.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    profileSection.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                    profileSection.style.opacity = '1';
                    profileSection.style.transform = 'translateY(0)';
                }, 400);
                setTimeout(() => {
                    historySection.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                    historySection.style.opacity = '1';
                    historySection.style.transform = 'translateY(0)';
                }, 600);

                // Table Row Animation
                const rows = document.querySelectorAll('#historyTable tr');
                rows.forEach((row, index) => {
                    row.style.opacity = '0';
                    row.style.transform = 'translateY(20px)';
                    setTimeout(() => {
                        row.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                        row.style.opacity = '1';
                        row.style.transform = 'translateY(0)';
                    }, 500 + index * 100);
                });
            });
        </script>
    @endpush
@endsection
