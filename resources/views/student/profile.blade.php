@extends('student.layouts.app')

@section('title', 'Student Profile')

@section('description', 'View your profile and class history at Aunty Anne\'s International School.')

@push('styles')
    <style>
        .content-container {
            max-width: 90rem;
            margin: 0 auto;
            padding: var(--space-lg) var(--space-md);
        }

        .profile-section {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-xl);
            padding: var(--space-xl);
            margin-bottom: var(--space-2xl);
        }

        .section-title {
            font-family: var(--font-display);
            font-size: clamp(1.25rem, 3vw, 1.5rem);
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: var(--space-md);
        }

        .profile-card {
            display: flex;
            gap: var(--space-lg);
            align-items: center;
        }

        .profile-image {
            width: clamp(100px, 15vw, 120px);
            height: clamp(100px, 15vw, 120px);
            border-radius: 50%;
            background: var(--gradient-primary);
            padding: 3px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .profile-image img,
        .profile-placeholder {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            background: var(--light-gray);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: clamp(1.5rem, 3vw, 2rem);
            font-weight: 700;
            color: var(--white);
        }

        .profile-details p {
            font-size: clamp(0.875rem, 2.5vw, 1rem);
            color: var(--text-primary);
            margin-bottom: var(--space-sm);
        }

        .history-section {
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
            max-height: 300px;
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

        @media (max-width: 768px) {
            .profile-card {
                flex-direction: column;
                align-items: flex-start;
            }

            .profile-image {
                width: clamp(80px, 12vw, 100px);
                height: clamp(80px, 12vw, 100px);
            }
        }

        @media (max-width: 576px) {
            .content-container {
                padding: var(--space-md);
            }

            .history-section {
                min-height: 150px;
            }
        }
    </style>
@endpush

@section('content')
    <div class="content-container">
        <!-- Profile Section -->
        <div class="profile-section">
            <h3 class="section-title">Your Profile</h3>
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
                    <p><strong>Date of Birth:</strong> {{ $student->date_of_birth->format('Y-m-d') }}</p>
                    <p><strong>Parent Name:</strong> {{ $student->parent_name }}</p>
                    <p><strong>Parent Phone:</strong> {{ $student->parent_phone_number }}</p>
                    <p><strong>Address:</strong> {{ $student->address }}</p>
                    <p><strong>Current Class:</strong>
                        {{ $student->getCurrentClass($currentSession->id, $currentTerm) ?? 'Not Enrolled' }}</p>
                </div>
            </div>
        </div>

        <!-- Class History Section -->
        <div class="history-section">
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
                        <tbody>
                            @forelse ($classHistory as $history)
                                <tr>
                                    <td>{{ $history->session->year }}</td>
                                    <td>{{ $history->class->name }}</td>
                                    <td>{{ $history->start_term->label() }}</td>
                                    <td>{{ $history->end_term ? $history->end_term->label() : 'Ongoing' }}</td>
                                    <td>{{ $history->join_date->format('Y-m-d') }}</td>
                                    <td>{{ $history->is_active ? 'Active' : 'Inactive' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No class history available.</td>
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
                gsap.from('.profile-section', { opacity: 0, y: 20, duration: 0.5 });
                gsap.from('.history-section', { opacity: 0, y: 20, duration: 0.5, delay: 0.2 });
            });
        </script>
    @endpush
@endsection