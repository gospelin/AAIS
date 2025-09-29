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

        .fees-section {
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

        .status-paid {
            color: var(--success);
            font-weight: 600;
        }

        .status-unpaid {
            color: var(--error);
            font-weight: 600;
        }

        @media (max-width: 576px) {
            .content-container {
                padding: var(--space-md);
            }

            .fees-section {
                min-height: 150px;
            }
        }
    </style>
@endpush

@section('content')
    <div class="content-container">
        <!-- Filter Section -->
        <div class="filter-section">
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
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Filter Fees</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Fee Status Section -->
        <div class="fees-section">
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
                        <tbody>
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
                                    <td colspan="5" class="text-center">No fee records available for this term.</td>
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
                gsap.from('.filter-section', { opacity: 0, y: 20, duration: 0.5 });
                gsap.from('.fees-section', { opacity: 0, y: 20, duration: 0.5, delay: 0.2 });
            });
        </script>
    @endpush
@endsection