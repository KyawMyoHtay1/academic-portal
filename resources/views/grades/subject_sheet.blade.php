<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Grade Sheet</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #0f172a; }
        h1 { font-size: 18px; margin: 0 0 4px; }
        h2 { font-size: 13px; margin: 0 0 10px; color: #334155; }
        .meta { margin-bottom: 12px; color: #475569; }
        .meta span { margin-right: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #cbd5e1; padding: 6px; text-align: left; vertical-align: top; }
        th { background: #e2e8f0; font-weight: 700; }
        .muted { color: #64748b; }
    </style>
</head>
<body>
    <h1>Grade Sheet</h1>
    <h2>
        {{ $subject['code'] }} - {{ $subject['title'] }}
        | {{ $subject['course_code'] }} - {{ $subject['course_title'] }}
        | Semester: {{ $subject['semester'] ?? 'N/A' }}
    </h2>

    <div class="meta">
        <span><strong>Generated:</strong> {{ $generatedAt }}</span>
        <span><strong>Status Filter:</strong> {{ $status }}</span>
        <span><strong>Search:</strong> {{ $search !== '' ? $search : 'N/A' }}</span>
    </div>

    <table>
        <thead>
            <tr>
                <th>Student No</th>
                <th>Student Name</th>
                <th>Score</th>
                <th>Letter</th>
                <th>Status</th>
                <th>Teacher</th>
                <th>Reviewer</th>
                <th>Reviewed At</th>
                <th>Rejection Reason</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $row)
                @php($grade = $row['grade'] ?? null)
                <tr>
                    <td>{{ $row['student']['student_no'] ?? 'N/A' }}</td>
                    <td>{{ $row['student']['full_name'] ?? 'N/A' }}</td>
                    <td>{{ $grade['score'] ?? 'N/A' }}</td>
                    <td>{{ $grade['letter_grade'] ?? 'N/A' }}</td>
                    <td>{{ $grade['status'] ?? 'N/A' }}</td>
                    <td>{{ $grade['graded_by'] ?? 'N/A' }}</td>
                    <td>{{ $grade['reviewed_by'] ?? 'N/A' }}</td>
                    <td>{{ $grade['reviewed_at'] ?? 'N/A' }}</td>
                    <td>{{ $grade['rejection_reason'] ?? '' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9">No grade rows match the current filters.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
