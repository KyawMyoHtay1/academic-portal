<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Attendance Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #0f172a; }
        h1 { font-size: 18px; margin: 0 0 8px; }
        h2 { font-size: 12px; margin: 10px 0 6px; color: #334155; }
        .meta { margin-bottom: 10px; color: #475569; }
        .meta span { margin-right: 10px; display: inline-block; }
        .stats { margin-bottom: 12px; }
        .stats span { margin-right: 14px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid #cbd5e1; padding: 5px; text-align: left; vertical-align: top; }
        th { background: #e2e8f0; font-weight: 700; }
    </style>
</head>
<body>
    <h1>Student Attendance Report</h1>
    <div class="meta">
        <span><strong>Generated:</strong> {{ $generatedAt }}</span>
        <span><strong>Student:</strong> {{ $student['student_no'] }} - {{ $student['full_name'] }}</span>
        <span><strong>Programme:</strong> {{ $student['programme'] ?? 'N/A' }}</span>
        <span><strong>Intake:</strong> {{ $student['intake_year'] ?? 'N/A' }}</span>
    </div>

    <div class="stats">
        <span><strong>Total:</strong> {{ $overall['total'] ?? 0 }}</span>
        <span><strong>Present:</strong> {{ $overall['present'] ?? 0 }}</span>
        <span><strong>Absent:</strong> {{ $overall['absent'] ?? 0 }}</span>
        <span><strong>Rate:</strong> {{ $overall['rate'] ?? 0 }}%</span>
    </div>

    <h2>By Course</h2>
    <table>
        <thead>
            <tr>
                <th>Course</th>
                <th>Total</th>
                <th>Present</th>
                <th>Absent</th>
                <th>Rate</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($byCourse as $row)
                <tr>
                    <td>{{ $row['course_code'] }} - {{ $row['title'] }}</td>
                    <td>{{ $row['total'] }}</td>
                    <td>{{ $row['present'] }}</td>
                    <td>{{ $row['absent'] }}</td>
                    <td>{{ $row['rate'] }}%</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">No course attendance records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <h2>By Subject</h2>
    <table>
        <thead>
            <tr>
                <th>Subject</th>
                <th>Course</th>
                <th>Total</th>
                <th>Present</th>
                <th>Absent</th>
                <th>Rate</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($bySubject as $row)
                <tr>
                    <td>{{ $row['subject_code'] }} - {{ $row['title'] }}</td>
                    <td>{{ $row['course_code'] }}</td>
                    <td>{{ $row['total'] }}</td>
                    <td>{{ $row['present'] }}</td>
                    <td>{{ $row['absent'] }}</td>
                    <td>{{ $row['rate'] }}%</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">No subject attendance records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <h2>Detailed Records</h2>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Subject</th>
                <th>Course</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $row)
                <tr>
                    <td>{{ $row['date'] }}</td>
                    <td>{{ $row['subject_code'] }} - {{ $row['subject_title'] }}</td>
                    <td>{{ $row['course_code'] }}</td>
                    <td>{{ $row['status'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">No attendance records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
