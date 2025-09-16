@extends('admin.layouts.app')

@section('title', 'Print Student Message')

@section('description', 'Print a message for selected students at Aunty Anne\'s International School.')

@push('styles')
    <style>
        .print-container {
            max-width: 800px;
            margin: 0 auto;
            background: var(--white);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-xl);
            padding: var(--space-2xl);
            position: relative;
            overflow: hidden;
            margin-bottom: var(--space-2xl);
        }

        .print-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gradient-primary);
        }

        .print-header {
            font-family: var(--font-display);
            font-size: clamp(1.5rem, 3.5vw, 2rem);
            font-weight: 700;
            color: var(--text-primary);
            text-align: center;
            margin-bottom: var(--space-xl);
        }

        .student-list {
            margin-bottom: var(--space-xl);
        }

        .student-list-title {
            font-family: var(--font-display);
            font-size: clamp(1rem, 2.5vw, 1.125rem);
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: var(--space-md);
            padding-left: var(--space-sm);
            border-left: 3px solid var(--primary-green);
        }

        .student-item {
            font-size: clamp(0.875rem, 2vw, 0.9375rem);
            color: var(--text-primary);
            margin-bottom: var(--space-sm);
        }

        .message-content {
            font-size: clamp(0.875rem, 2vw, 0.9375rem);
            color: var(--text-primary);
            line-height: 1.6;
            white-space: pre-wrap;
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-md);
            padding: var(--space-md);
            background: var(--bg-secondary);
        }

        .btn-print {
            background: var(--gradient-primary);
            border: none;
            color: var(--white);
            font-size: clamp(0.875rem, 2.5vw, 1rem);
            font-weight: 600;
            padding: var(--space-md) var(--space-lg);
            border-radius: var(--radius-lg);
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: block;
            margin: var(--space-md) auto 0;
            text-align: center;
        }

        .btn-print:hover,
        .btn-print:focus-visible {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
            color: var(--white);
        }

        @media print {
            .btn-print {
                display: none;
            }

            .print-container {
                border: none;
                padding: 0;
                margin: 0;
                width: 100%;
            }

            .print-container::before {
                display: none;
            }
        }
    </style>
@endpush

@section('content')
    <div class="content-container">
        <div class="print-container">
            <h2 class="print-header">Message for Students</h2>
            <div class="student-list">
                <h3 class="student-list-title">Recipients</h3>
                @foreach($students as $student)
                    <div class="student-item">{{ $student->full_name }} ({{ $student->reg_no }})</div>
                @endforeach
            </div>
            <div class="message-content">
                {{ $message }}
            </div>
            <button class="btn-print" onclick="window.print()">
                <i class="bx bx-printer"></i> Print Message
            </button>
        </div>
    </div>
@endsection
