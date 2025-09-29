<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Aunty Anne's International School - {{ $student->full_name }} - {{ $term->label() }} Term -
        {{ $session->year }}</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Montserrat:wght@400;700&family=Georgia&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"
        integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        @page {
            size: A4;
            margin: 9mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', serif;
            font-weight: bold;
            line-height: 1.3;
            color: #21a055;
            font-size: 8pt;
            position: relative;
        }

        .print-view {
            margin: auto;
            padding: 2mm;
            background-color: #fff;
            position: relative;
            z-index: 0;
            overflow: hidden;
        }

        .school-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 3px solid;
            border-image: linear-gradient(to right, #21a055, #006400) 1;
            padding: 10px 8px;
            margin-bottom: 8px;
            text-transform: uppercase;
            border-radius: 4px;
            box-shadow: 0 0 8px rgba(33, 160, 85, 0.2);
            background-image: linear-gradient(45deg, rgba(255, 255, 255, 0.1) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.1) 50%, rgba(255, 255, 255, 0.1) 75%, transparent 75%, transparent);
            background-size: 20px 20px;
        }

        .school-logo {
            width: 80px;
            height: auto;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .school-info {
            text-align: center;
            width: 100%;
            line-height: 1.5;
        }

        .school-info h1 {
            font-family: 'Playfair Display', serif;
            font-size: 16pt;
            margin: 0;
            color: #006400;
            text-transform: uppercase;
            text-shadow: 1px 1px 2px rgba(33, 160, 85, 0.3);
        }

        .school-info p {
            margin: 2px 0;
            font-size: 8.5pt;
            color: #21a055;
        }

        .school-info .motto {
            font-family: 'Montserrat', sans-serif;
            font-size: 9pt;
            color: red;
            letter-spacing: 1.5px;
        }

        .school-info h2 {
            font-family: 'Montserrat', sans-serif;
            font-size: 11pt;
            margin: 4px 0 0;
            color: #006400;
            text-transform: uppercase;
        }

        .student-info {
            margin-bottom: 6px;
        }

        .student-info table {
            width: 100%;
            border-collapse: collapse;
        }

        .student-info td {
            padding: 3px;
            font-size: 8.5pt;
            line-height: 1.4;
        }

        .results-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .results-table th,
        .results-table td {
            border: 1px solid #21a055;
            padding: 4px;
            font-size: 7.5pt;
            text-align: center;
        }

        .results-table th {
            background-color: #21a055;
            color: #fff;
            text-transform: uppercase;
            border: 0.5px solid #fff;
        }

        .results-table .subject-column {
            width: 35%;
            text-align: left;
            text-transform: uppercase;
            color: #21a055;
        }

        .results-table .score-column {
            width: 10%;
        }

        .results-table .remark-column {
            width: 15%;
        }

        .results-table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .grading-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }

        .grading-table th,
        .grading-table td {
            border: 1px solid #21a055;
            padding: 3px;
            font-size: 7.5pt;
            text-align: center;
        }

        .grading-table th {
            background-color: #21a055;
            color: #fff;
        }

        .remarks {
            margin-top: 8px;
            font-size: 8.5pt;
            background-color: #e6f4ea;
            border: 2px solid;
            border-image: linear-gradient(to right, #21a055, #006400) 1;
            border-radius: 6px;
            padding: 8px;
            box-shadow: 0 0 8px rgba(33, 160, 85, 0.2);
            background-image: linear-gradient(45deg, rgba(255, 255, 255, 0.1) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.1) 50%, rgba(255, 255, 255, 0.1) 75%, transparent 75%, transparent);
            background-size: 20px 20px;
        }

        .remarks-heading {
            font-family: 'Montserrat', sans-serif;
            font-size: 8pt;
            color: #006400;
            text-transform: uppercase;
            text-align: center;
            margin-bottom: 6px;
            font-weight: bold;
        }

        .remarks-container {
            display: flex;
            justify-content: space-between;
            gap: 8px;
            position: relative;
        }

        .remarks-container::before {
            content: '';
            position: absolute;
            left: 50%;
            top: 0;
            bottom: 0;
            width: 1px;
            border-left: 1px dashed #21a055;
            opacity: 0.4;
        }

        .remark-item {
            flex: 1;
            display: flex;
            align-items: flex-start;
            padding: 4px;
        }

        .remark-item i {
            font-size: 10pt;
            color: #21a055;
            margin-right: 5px;
            margin-top: 2px;
        }

        .remark-content strong {
            text-transform: uppercase;
            font-size: 7.5pt;
            color: #fff;
            background-color: #006400;
            padding: 2px 6px;
            border-radius: 12px;
            display: inline-block;
            margin-bottom: 3px;
        }

        .remark-content p {
            margin: 0;
            font-family: 'Georgia', serif;
            font-style: italic;
            color: #21a055;
            max-height: 35px;
            overflow-y: auto;
        }

        .footer {
            margin-top: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 7.5pt;
            color: #21a055;
            position: relative;
        }

        .footer .signature {
            text-align: center;
        }

        .footer .signature img {
            width: 80px;
            margin-bottom: -12px;
        }

        .footer .signature p {
            margin: 0;
            color: #555;
        }

        .footer .signature .principal-text {
            font-family: 'Playfair Display', serif;
            font-size: 8pt;
            color: #006400;
        }

        .footer .contact {
            text-align: right;
        }

        .footer .contact p {
            margin: 2px 0;
        }

        .footer::before {
            content: '';
            position: absolute;
            left: 50%;
            top: 0;
            bottom: 0;
            width: 1px;
            background-color: #d3d3d3;
        }

        body::before {
            content: url('{{ public_path('images/school_logo.png') }}');
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
            <img src="{{ public_path('images/school_logo.png') }}" alt="School Logo" class="school-logo">
            <div class="school-info">
                <h1>Aunty Anne's International School</h1>
                <p>6 Oomne Drive, Abayi, Aba, Abia State</p>
                <h2>Report Sheet for {{ $term->label() }} Term {{ $session->year }} Academic Session</h2>
                <p class="motto">"Practical, Knowledge and Confidence"</p>
            </div>
        </div>

        <div class="student-info">
            <table>
                <tr>
                    <td><strong>Name:</strong></td>
                    <td>{{ strtoupper($student->full_name) }}</td>
                    <td><strong>Class:</strong></td>
                    <td>{{ strtoupper($currentClass ?? 'N/A') }}</td>
                </tr>
                <tr>
                    <td><strong>Student ID:</strong></td>
                    <td>{{ $student->reg_no }}</td>
                    <td><strong>Gender:</strong></td>
                    <td>{{ strtoupper($student->gender ?? 'N/A') }}</td>
                </tr>
                <tr>
                    <td><strong>Closing Date:</strong></td>
                    <td>{{ $termSummary->closing_date ?? 'N/A' }}</td>
                    <td><strong>Next Term Begins:</strong></td>
                    <td>{{ $termSummary->next_term_begins ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td><strong>Last Term Average:</strong></td>
                    <td>{{ $termSummary->last_term_average ?? 'N/A' }}</td>
                    <td><strong>Cumulative Average:</strong></td>
                    <td>{{ number_format($termSummary->cumulative_average ?? 0, 2) }}</td>
                </tr>
                <tr>
                    <td><strong>Term Average:</strong></td>
                    <td>{{ number_format($termSummary->term_average ?? 0, 2) }}</td>
                    <td><strong>Position:</strong></td>
                    <td>{{ $termSummary->position ?? 'N/A' }}</td>
                </tr>
            </table>
        </div>

        <table class="results-table">
            <thead>
                <tr>
                    <th class="subject-column">Subjects</th>
                    <th class="score-column">Class Work (20)</th>
                    <th class="score-column">Test (20)</th>
                    <th class="score-column">Exam (60)</th>
                    <th class="score-column">Total (100)</th>
                    <th class="score-column">Grade</th>
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
                            <td
                                style="color: {{ ($result->class_assessment < 10 && $result->class_assessment !== null) ? 'red' : '#21a055' }}">
                                {{ $result->class_assessment ?? '-' }}
                            </td>
                            <td
                                style="color: {{ ($result->summative_test < 10 && $result->summative_test !== null) ? 'red' : '#21a055' }}">
                                {{ $result->summative_test ?? '-' }}
                            </td>
                            <td style="color: {{ ($result->exam < 30 && $result->exam !== null) ? 'red' : '#21a055' }}">
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
                    <td><strong>{{ $grandTotal['class_assessment'] ? round($grandTotal['class_assessment'], 2) : '-' }}</strong>
                    </td>
                    <td><strong>{{ $grandTotal['summative_test'] ? round($grandTotal['summative_test'], 2) : '-' }}</strong>
                    </td>
                    <td><strong>{{ $grandTotal['exam'] ? round($grandTotal['exam'], 2) : '-' }}</strong></td>
                    <td><strong>{{ $grandTotal['total'] ? round($grandTotal['total'], 2) : '-' }}</strong></td>
                    <td colspan="2"></td>
                </tr>
            </tbody>
        </table>

        <table class="grading-table">
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

        <div class="remarks">
            <div class="remarks-heading">Remarks</div>
            <div class="remarks-container">
                <div class="remark-item">
                    <i class="fas fa-user-tie"></i>
                    <div class="remark-content">
                        <strong>Principal's Remark:</strong>
                        <p>{{ $termSummary->principal_remark ?? 'N/A' }}</p>
                    </div>
                </div>
                <div class="remark-item">
                    <i class="fas fa-chalkboard-teacher"></i>
                    <div class="remark-content">
                        <strong>Teacher's Remark:</strong>
                        <p>{{ $termSummary->teacher_remark ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer">
            <div class="signature">
                <img src="{{ public_path('images/signature.png') }}" alt="Signature">
                <p>_______________________</p>
                <p class="principal-text"><strong>Principal's Signature</strong></p>
            </div>
            <div class="contact">
                <p>Aunty Anne's International School</p>
                <p>Contact: info@aais.edu | +123-456-7890</p>
                <p>Generated on {{ $date }}</p>
            </div>
        </div>
    </div>
</body>

</html>
