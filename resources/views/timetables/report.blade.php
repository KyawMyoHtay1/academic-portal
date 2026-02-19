<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'Timetable Report' }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #0f172a; }
        h1 { font-size: 18px; margin: 0 0 8px; }
        .meta { margin-bottom: 10px; color: #475569; }
        .meta span { margin-right: 10px; display: inline-block; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #cbd5e1; padding: 5px; text-align: left; vertical-align: top; }
        th { background: #e2e8f0; font-weight: 700; }
    </style>
</head>
<body>
    @php
        $firstRow = $rows instanceof \Illuminate\Support\Collection ? $rows->first() : ($rows[0] ?? null);
        $showCreatorColumn = is_array($firstRow) && array_key_exists('creator_name', $firstRow);
    @endphp

    <h1>{{ $title ?? 'Timetable Report' }}</h1>
    <div class="meta">
        <span><strong>Generated:</strong> {{ $generatedAt ?? now()->format('Y-m-d H:i:s') }}</span>
        <span><strong>User:</strong> {{ $owner ?? '-' }}</span>
        @if (!empty($filters['day']) && $filters['day'] !== 'all')
            <span><strong>Day:</strong> {{ $filters['day'] }}</span>
        @endif
        @if (!empty($filters['course_id']) && $filters['course_id'] !== 'all')
            <span><strong>Course Filter:</strong> {{ $filters['course_id'] }}</span>
        @endif
        @if (!empty($filters['semester']) && $filters['semester'] !== 'all')
            <span><strong>Semester:</strong> {{ $filters['semester'] }}</span>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>Day</th>
                <th>Time</th>
                <th>Subject</th>
                <th>Course</th>
                <th>Location</th>
                @if ($showCreatorColumn)
                    <th>Created By</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $row)
                <tr>
                    <td>{{ $row['day_of_week'] ?? '-' }}</td>
                    <td>{{ ($row['start_time'] ?? '-') }} - {{ ($row['end_time'] ?? '-') }}</td>
                    <td>{{ ($row['subject_code'] ?? '-') }} - {{ ($row['subject_title'] ?? '-') }}</td>
                    <td>
                        {{ ($row['course_code'] ?? '-') }} - {{ ($row['course_title'] ?? '-') }}
                        @if (!empty($row['semester']))
                            <br><small>Semester: {{ $row['semester'] }}</small>
                        @endif
                    </td>
                    <td>{{ $row['location'] ?? '-' }}</td>
                    @if ($showCreatorColumn)
                        <td>{{ $row['creator_name'] ?? '-' }}</td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="6">No timetable entries found for current filters.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
