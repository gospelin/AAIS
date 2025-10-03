<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Aunty Anne's International School - {{ strtoupper($student->full_name) }} - {{ $term->label() }} Term - {{ $session->year }}</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"
        integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>

        @font-face {
            font-family: 'Playfair Display';
            src: url('{{ public_path('fonts/PlayfairDisplay-Regular.ttf') }}') format('truetype');
            font-weight: normal;
        }
        @font-face {
            font-family: 'Georgia';
            src: url('{{ public_path('fonts/Georgia.ttf') }}') format('truetype');
            font-weight: normal;
        }

        @page {
            size: A4;
            margin: 9mm;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            font-size: 8pt;
            line-height: 1.3;
            color: #28a745;
        }

        .print-view {
            margin: 0;
            padding: 2mm;
            background-color: #fff;
            position: relative;
        }

        .school-header {
            width: 100%;
            border-bottom: 3px solid #28a745;
            padding: 12px 8px;
            margin-bottom: 8px;
            text-transform: uppercase;
            background-color: #f9f9f9;
        }

        .school-logo {
            width: 100px;
            height: auto;
            position: absolute;
            top: 8px;
            left: 8px;
        }

        .school-info {
            text-align: center;
            line-height: 1.6;
        }

        .school-info h1 {
            font-family: 'Playfair Display', sans-serif;
            font-size: 16pt;
            margin: 0;
            color: #28a745;
            text-transform: uppercase;
        }

        .school-info p {
            margin: 0;
            font-size: 8.5pt;
            color: #28a745;
        }

        .school-info .motto {
            font-size: 9pt;
            color: red;
            letter-spacing: 1.5px;
        }

        .school-info h2 {
            font-family: 'Montserrat', sans-serif;
            font-size: 11pt;
            margin: 4px 0 0;
            color: #28a745;
            text-transform: uppercase;
        }

        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 3px 0;
        }

        .custom-table th,
        .custom-table td {
            padding: 4px;
            vertical-align: top;
            font-size: 8.5pt;
            text-transform: uppercase;
            border-bottom: 0.5px solid #d3d3d3;
        }

        .custom-table tbody tr:nth-child(odd),
        .custom-table tbody tr:nth-child(even) {
            border-bottom: 0.5px solid #d3d3d3;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2px;
        }

        .table th,
        .table td {
            border: 1px solid #28a745;
            padding: 4px;
            font-size: 7.5pt;
            text-align: center;
        }

        .table th {
            background-color: #28a745;
            color: #fff;
            text-transform: uppercase;
        }

        .table .subject-column {
            width: 35%;
            text-align: left;
            text-transform: uppercase;
        }

        .summative-column, .classwork-column, .exam-column {
            width: 10%;
        }

        .remark-column {
            width: 15%;
        }

        .table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .grading {
            width: 100%;
            margin-bottom: 2px;
        }

        .grading th,
        .grading td {
            border: 1px solid #28a745;
            padding: 4px;
            font-size: 7.5pt;
            text-align: center;
        }

        .grading th {
            background-color: #28a745;
            color: #fff;
        }

        .remarks {
            margin-top: 10px;
            line-height: 1.4;
            font-size: 9pt;
            background-color: #e6f4ea;
            border: 2px solid #28a745;
            padding: 8px;
        }

        .remarks-heading {
            font-family: 'Montserrat', sans-serif;
            font-size: 8pt;
            color: #28a745;
            text-transform: uppercase;
            font-weight: bold;
            text-align: center;
            margin-bottom: 6px;
        }

        .remarks-table {
            width: 100%;
            border-collapse: collapse;
        }

        .remarks-table td {
            padding: 5px;
            vertical-align: top;
            font-size: 7.5pt;
        }

        .remarks-table strong {
            text-transform: uppercase;
            background-color: #1a7044;
            color: #fff;
            padding: 2px 6px;
            border-radius: 12px;
            display: inline-block;
            margin-bottom: 3px;
        }

        .remarks-table p {
            margin: 0;
            font-family: 'Georgia', serif;
            font-style: italic;
            color: #28a745;
        }

        .footer {
            margin-top: 5px;
            width: 100%;
            font-size: 7.5pt;
            color: #21a055;
        }

        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }

        .footer-table td {
            vertical-align: top;
            padding: 4px;
        }

        .footer .signature {
            width: 50%;
            text-align: left;
        }

        .footer .signature img {
            width: 100px;
            margin-bottom: -15px;
        }

        .footer .signature p {
            margin: 0;
            color: #555;
        }

        .footer .signature .principal-text {
            font-family: 'Playfair Display', sans-serif;
            font-size: 8pt;
            color: #28a745;
        }

        .footer .contact {
            width: 50%;
            text-align: right;
        }

        .footer .contact p {
            margin: 2px 0;
        }

        .download-info {
            font-size: 6pt;
            color: #28a745;
            margin: 2px 0;
        }

        .qrcode {
            margin: 2px 0;
            text-align: right;
        }

        .qrcode img {
            width: 80px;
            height: auto;
        }

        body::before {
            content: url('{{ $school_logo }}');
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-5deg);
            opacity: 0.1;
            width: 40%;
            z-index: -1;
            filter: grayscale(100%) sepia(20%);
        }
    </style>
</head>
<body>
    <div class="print-view">
        <div class="school-header">
            <img src="{{ $school_logo }}" alt="School Logo" class="school-logo">
            <div class="school-info">
                <h1>Aunty Anne's International School</h1>
                <p>6 Oomnne Drive, Abayi, Aba, Abia State</p>
                <h2>Report Sheet for {{ $term->label() }} Term {{ $session->year }} Academic Session</h2>
                <p class="motto">"Practical, Knowledge and Confidence"</p>
            </div>
        </div>

        <div>
            <table class="custom-table">
                <tr>
                    <td><strong>NAME:</strong></td>
                    <td>{{ strtoupper($student->full_name) }}</td>
                    <td><strong>CLASS:</strong></td>
                    <td>{{ strtoupper($currentClass->name ?? 'N/A') }}</td>
                </tr>
                <tr>
                    <td><strong>STUDENT ID:</strong></td>
                    <td>{{ $student->reg_no }}</td>
                    <td><strong>GENDER:</strong></td>
                    <td>{{ strtoupper($student->gender ?? 'N/A') }}</td>
                </tr>
                <tr>
                    <td><strong>CLOSING DATE:</strong></td>
                    <td>{{ $termSummary->closing_date ?? 'N/A' }}</td>
                    <td><strong>REOPENING DATE:</strong></td>
                    <td>{{ $termSummary->next_term_begins ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td><strong>LAST TERM AVERAGE:</strong></td>
                    <td>{{ $termSummary->last_term_average ?? 'N/A' }}</td>
                    <td><strong>CUMULATIVE AVERAGE:</strong></td>
                    <td>{{ number_format($termSummary->cumulative_average ?? 0, 2) }}</td>
                </tr>
                @if (!str_contains(strtoupper($currentClass->name ?? ''), 'JSS') && !str_contains(strtoupper($currentClass->name ?? ''), 'SSS') && ($currentClass->class_hierarchy ?? 0) <= 10)
                    <tr>
                        <td><strong>TERM AVERAGE:</strong></td>
                        <td>{{ number_format($termSummary->term_average ?? 0, 2) }}</td>
                        <td><strong>POSITION:</strong></td>
                        <td>{{ $termSummary->position ?? 'N/A' }}</td>
                    </tr>
                @else
                    <tr>
                        <td><strong>TERM AVERAGE:</strong></td>
                        <td>{{ number_format($termSummary->term_average ?? 0, 2) }}</td>
                        <td colspan="2"></td>
                    </tr>
                @endif
            </table>
        </div>

        <div>
            <table class="table">
                <thead>
                    <tr>
                        <th class="subject-column">Subjects</th>
                        <th class="classwork-column">Class Work (20)</th>
                        <th class="summative-column">Test (20)</th>
                        <th class="exam-column">Exam (60)</th>
                        <th class="total-column">Total (100)</th>
                        <th class="grade-column">Grade</th>
                        <th class="remark-column">Remark</th>
                    </tr>
                </thead>
                <tbody>
                    @php
$grandTotal = [
    'class_assessment' => 0,
    'summative_test' => 0,
    'exam' => 0,
    'total' => 0
];
$count = 0;
                    @endphp
                    @foreach ($results as $result)
                        @if ($result->total)
                            <tr>
                                <td class="subject-column">{{ strtoupper($result->subject->name ?? 'N/A') }}</td>
                                <td style="color: {{ ($result->class_assessment < 10 && $result->class_assessment !== null) ? 'red' : '#28a745' }}">
                                    {{ $result->class_assessment ?? '-' }}
                                </td>
                                <td style="color: {{ ($result->summative_test < 10 && $result->summative_test !== null) ? 'red' : '#28a745' }}">
                                    {{ $result->summative_test ?? '-' }}
                                </td>
                                <td style="color: {{ ($result->exam < 30 && $result->exam !== null) ? 'red' : '#28a745' }}">
                                    {{ $result->exam ?? '-' }}
                                </td>
                                <td>{{ $result->total ?? '-' }}</td>
                                <td>{{ $result->grade ?? '-' }}</td>
                                <td>{{ strtoupper($result->remark ?? '-') }}</td>
                            </tr>
                            @php
        $grandTotal['class_assessment'] += $result->class_assessment ?? 0;
        $grandTotal['summative_test'] += $result->summative_test ?? 0;
        $grandTotal['exam'] += $result->exam ?? 0;
        $grandTotal['total'] += $result->total ?? 0;
        $count++;
                            @endphp
                        @endif
                    @endforeach
                    <tr>
                        <td class="subject-column"><strong>GRAND TOTAL</strong></td>
                        <td><strong>{{ $grandTotal['class_assessment'] ? round($grandTotal['class_assessment'], 2) : '-' }}</strong></td>
                        <td><strong>{{ $grandTotal['summative_test'] ? round($grandTotal['summative_test'], 2) : '-' }}</strong></td>
                        <td><strong>{{ $grandTotal['exam'] ? round($grandTotal['exam'], 2) : '-' }}</strong></td>
                        <td><strong>{{ $grandTotal['total'] ? round($grandTotal['total'], 2) : '-' }}</strong></td>
                        <td colspan="2"></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="grading">
            <table class="table">
                <thead>
                    <tr>
                        <th colspan="9">GRADE</th>
                    </tr>
                    <tr>
                        <th>100 - 95</th>
                        <th>94 - 80</th>
                        <th>79 - 70</th>
                        <th>69 - 65</th>
                        <th>64 - 60</th>
                        <th>59 - 50</th>
                        <th>49 - 40</th>
                        <th>39 - 30</th>
                        <th>29 - 0</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>A+</td>
                        <td>A</td>
                        <td>B+</td>
                        <td>B</td>
                        <td>C+</td>
                        <td>C</td>
                        <td>D</td>
                        <td>E</td>
                        <td>F</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="remarks">
            <div class="remarks-heading">Remarks</div>
            <table class="remarks-table">
                <tr>
                    <td style="width: 50%;">
                        <strong>Principal's Remark:</strong>
                        <p>{{ $termSummary->principal_remark ?? 'N/A' }}</p>
                    </td>
                    <td style="width: 50%;">
                        <strong>Teacher's Remark:</strong>
                        <p>{{ $termSummary->teacher_remark ?? 'N/A' }}</p>
                    </td>
                </tr>
            </table>
        </div>

        <div class="footer">
            <table class="footer-table">
                <tr>
                    <td class="signature">
                        <img src="{{ $signature }}" alt="Signature">
                        <p>_______________________</p>
                        <p class="principal-text"><strong>Principal's Signature</strong></p>
                    </td>
                    <td class="contact">
                        <p>Aunty Anne's International School</p>
                        <p>Contact: info@auntyannesschool.com.ng | +234-803-668-8517</p>
                        <div class="download-info">
                            <p>Download: {{ $downloadUrl }}</p>
                        </div>
                        <div class="qrcode">
                            <img src="{{ $qrCodeImage }}" alt="Download QR Code">
                        </div>
                        <p>Generated on {{ $date }}</p>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
