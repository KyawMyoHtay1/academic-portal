<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Attendance Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #0f172a; }
        h1 { font-size: 18px; margin: 0 0 8px; }
        .meta { margin-bottom: 10px; color: #475569; }
        .meta span { margin-right: 10px; display: inline-block; }
        .stats { margin-bottom: 12px; }
        .stats span { margin-right: 14px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #cbd5e1; padding: 5px; text-align: left; vertical-align: top; }
        th { background: #e2e8f0; font-weight: 700; }
    </style>
</head>
<body>
    <h1>Attendance Report (Staff)</h1>
    <div class="meta">
        <span><strong>Generated:</strong> {{ $generatedAt }}</span>
        <span><strong>Programme:</strong> {{ $filters['programme'] ?? 'all' }}</span>
        <span><strong>Intake:</strong> {{ $filters['intake_year'] ?? 'all' }}</span>
        <span><strong>Semester:</strong> {{ $filters['semester'] ?? 'all' }}</span>
    </div>

    <div class="stats">
        <span><strong>Total:</strong> {{ $overall['total'] ?? 0 }}</span>
        <span><strong>Present:</strong> {{ $overall['present'] ?? 0 }}</span>
        <span><strong>Absent:</strong> {{ $overall['absent'] ?? 0 }}</span>
        <span><strong>Rate:</strong> {{ $overall['rate'] ?? 0 }}%</span>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Student</th>
                <th>Programme</th>
                <th>Subject</th>
                <th>Course</th>
                <th>Semester</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $row)
                <tr>
                    <td>{{ $row['date'] }}</td>
                    <td>{{ $row['student_no'] }} - {{ $row['student_name'] }}</td>
                    <td>{{ $row['programme'] }}</td>
                    <td>{{ $row['subject_code'] }} - {{ $row['subject_title'] }}</td>
                    <td>{{ $row['course_code'] }}</td>
                    <td>{{ $row['semester'] }}</td>
                    <td>{{ $row['status'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">No attendance records found for current filters.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
