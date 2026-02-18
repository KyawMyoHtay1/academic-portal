<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Enrollment Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #0f172a; }
        h1 { font-size: 18px; margin: 0 0 6px; }
        .meta { margin-bottom: 12px; color: #475569; }
        .meta span { margin-right: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #cbd5e1; padding: 6px; text-align: left; vertical-align: top; }
        th { background: #e2e8f0; font-weight: 700; }
        .muted { color: #64748b; }
    </style>
</head>
<body>
    <h1>Enrollment Report</h1>
    <div class="meta">
        <span><strong>Generated:</strong> {{ $generatedAt }}</span>
        <span><strong>Status:</strong> {{ $filters['status'] ?? 'all' }}</span>
        <span><strong>Search:</strong> {{ $filters['search'] !== '' ? $filters['search'] : 'N/A' }}</span>
        <span><strong>Date From:</strong> {{ $filters['date_from'] !== '' ? $filters['date_from'] : 'N/A' }}</span>
        <span><strong>Date To:</strong> {{ $filters['date_to'] !== '' ? $filters['date_to'] : 'N/A' }}</span>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Student</th>
                <th>Course</th>
                <th>Programme</th>
                <th>Semester</th>
                <th>Status</th>
                <th>Requested At</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $row)
                <tr>
                    <td>{{ $row['enrollment_id'] }}</td>
                    <td>
                        <div><strong>{{ $row['student_name'] }}</strong></div>
                        <div class="muted">{{ $row['student_no'] }}</div>
                        <div class="muted">{{ $row['student_email'] }}</div>
                    </td>
                    <td>
                        <div><strong>{{ $row['course_code'] }}</strong></div>
                        <div class="muted">{{ $row['course_title'] }}</div>
                        <div class="muted">Credits: {{ $row['credits'] }}</div>
                    </td>
                    <td>{{ $row['programme'] ?: 'N/A' }}</td>
                    <td>{{ $row['semester'] ?: 'N/A' }}</td>
                    <td>{{ $row['status'] }}</td>
                    <td>{{ $row['requested_at'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">No enrollments found for current filters.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
