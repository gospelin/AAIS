@php
    use App\Enums\TermEnum;
@endphp
@extends('student.layouts.app')

@section('title', 'Fee Status')

@section('description', 'View your fee payment status for different sessions and terms at Aunty Anne\'s International School.')

@push('styles')
    <style>
        .content-container {
            max-width: 90rem;
            margin: 0 auto;
            padding: var(--space-md) var(--space-sm);
            overflow-x: hidden;
        }

        .filter-section {
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

        .filter-section:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }

        .filter-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gradient-primary);
            transition: transform 0.5s ease;
        }

        .filter-section:hover::before {
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

        .form-label {
            font-family: var(--font-primary);
            font-size: clamp(0.625rem, 1.6vw, 0.75rem);
            color: var(--text-secondary);
            margin-bottom: var(--space-xs);
            font-weight: 500;
        }

        .form-select {
            background: var(--glass-bg);
            color: var(--text-primary);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-lg);
            padding: var(--space-sm) var(--space-md);
            font-family: var(--font-primary);
            font-size: clamp(0.625rem, 1.6vw, 0.75rem);
            transition: border-color 0.2s ease, background 0.2s ease, box-shadow 0.2s ease;
            cursor: pointer;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
        }

        /* Enhanced dark mode styling for form-select and dropdown */
        html.dark .form-select,
        html.dark .form-select option {
            background: var(--glass-bg) !important;
            color: var(--primary) !important;
            backdrop-filter: blur(20px);
        }

        /* Fallback for browsers ignoring backdrop-filter */
        html.dark .form-select {
            background-color: rgba(31, 41, 55, 0.9) !important;
            /* Matches typical --glass-bg in dark mode */
        }

        .form-select:focus {
            border-color: var(--primary);
            /* #4b4bff */
            outline: none;
            box-shadow: 0 0 0 3px rgba(75, 75, 255, 0.3);
        }

        .fees-section {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-xl);
            overflow: hidden;
            min-height: 200px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .fees-section:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
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

        .status-paid {
            color: var(--success);
            font-weight: 600;
        }

        .status-unpaid {
            color: var(--error);
            font-weight: 600;
        }

        .btn-primary {
            background: var(--primary);
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
        }

        .btn-primary:hover {
            background: var(--secondary);
            border-color: var(--primary);
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .btn-primary:focus-visible {
            outline: 2px solid var(--secondary);
            outline-offset: 2px;
        }

        @media (max-width: 768px) {
            .content-container {
                padding: var(--space-xs);
            }

            .filter-section {
                padding: var(--space-sm);
            }

            .filter-section .row {
                flex-direction: column;
            }

            .fees-section {
                min-height: 150px;
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

            .filter-section,
            .fees-section {
                min-height: 120px;
            }
        }

        .filter-section:focus-visible,
        .fees-section:focus-visible,
        .btn-primary:focus-visible {
            outline: 2px solid var(--primary);
            outline-offset: 2px;
        }
    </style>
@endpush

@section('content')
    <div class="content-container">
        <!-- Filter Section -->
        <div class="filter-section" tabindex="0">
            <h3 class="section-title">Filter Fee Status</h3>
            <form method="GET" action="{{ route('student.fee_status') }}">
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
                            <i class="fas fa-filter"></i> Filter Fees
                        </button>
                        <button type="button" class="btn btn-primary" id="resetFilters">
                            <i class="fas fa-undo"></i> Reset
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Fee Status Section -->
        <div class="fees-section" tabindex="0">
            <div class="section-header">
                <h3 class="section-title">Fee Status for {{ $currentSession->year }} - {{ $currentTerm->label() }}</h3>
            </div>
            <div class="p-4">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Session</th>
                                <th>Term</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Payment Date</th>
                            </tr>
                        </thead>
                        <tbody id="feeTable">
                            @forelse ($feePayments as $payment)
                                <tr>
                                    <td>{{ $payment->session->year }}</td>
                                    <td>{{ TermEnum::from($payment->term->value)->label() }}</td>
                                    <td>{{ $payment->amount ?? 'N/A' }}</td>
                                    <td class="{{ $payment->has_paid_fee ? 'status-paid' : 'status-unpaid' }}">
                                        {{ $payment->has_paid_fee ? 'Paid' : 'Unpaid' }}
                                    </td>
                                    <td>{{ $payment->payment_date ? $payment->payment_date->format('Y-m-d') : 'N/A' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center stat-label">No fee records available for this term.</td>
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
                const filterSection = document.querySelector('.filter-section');
                const feesSection = document.querySelector('.fees-section');
                filterSection.style.opacity = '0';
                filterSection.style.transform = 'translateY(20px)';
                feesSection.style.opacity = '0';
                feesSection.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    filterSection.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                    filterSection.style.opacity = '1';
                    filterSection.style.transform = 'translateY(0)';
                }, 400);
                setTimeout(() => {
                    feesSection.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                    feesSection.style.opacity = '1';
                    feesSection.style.transform = 'translateY(0)';
                }, 600);

                // Table Row Animation
                const rows = document.querySelectorAll('#feeTable tr');
                rows.forEach((row, index) => {
                    row.style.opacity = '0';
                    row.style.transform = 'translateY(20px)';
                    setTimeout(() => {
                        row.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                        row.style.opacity = '1';
                        row.style.transform = 'translateY(0)';
                    }, 500 + index * 100);
                });

                // Reset Filters
                const resetButton = document.getElementById('resetFilters');
                resetButton.addEventListener('click', () => {
                    const form = resetButton.closest('form');
                    form.querySelector('#session_id').selectedIndex = 0;
                    form.querySelector('#term').selectedIndex = 0;
                    form.submit();
                });
            });
        </script>
    @endpush
@endsection