<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Fee Ledger Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #0f172a; }
        h1 { font-size: 18px; margin: 0 0 6px; }
        .meta { margin-bottom: 12px; color: #475569; }
        .meta span { margin-right: 12px; display: inline-block; margin-bottom: 4px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #cbd5e1; padding: 6px; text-align: left; vertical-align: top; }
        th { background: #e2e8f0; font-weight: 700; }
        .muted { color: #64748b; }
    </style>
</head>
<body>
    <h1>Fee Ledger Report</h1>
    <div class="meta">
        <span><strong>Generated:</strong> {{ $generatedAt }}</span>
        <span><strong>Status:</strong> {{ $filters['status'] ?? 'all' }}</span>
        <span><strong>Search:</strong> {{ ($filters['search'] ?? '') !== '' ? $filters['search'] : 'N/A' }}</span>
        <span><strong>Overdue only:</strong> {{ ($filters['overdue_only'] ?? false) ? 'Yes' : 'No' }}</span>
        <span><strong>Due bucket:</strong> {{ $filters['due_bucket'] ?? 'all' }}</span>
    </div>

    <table>
        <thead>
            <tr>
                <th>Fee ID</th>
                <th>Receipt No</th>
                <th>Student</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Due Date</th>
                <th>Paid Date</th>
                <th>Processed</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $row)
                <tr>
                    <td>{{ $row['fee_id'] }}</td>
                    <td>{{ $row['receipt_no'] ?: '-' }}</td>
                    <td>
                        <div><strong>{{ $row['student_name'] }}</strong></div>
                        <div class="muted">{{ $row['student_no'] }}</div>
                    </td>
                    <td>GBP {{ number_format((float) $row['amount'], 2) }}</td>
                    <td>{{ $row['status'] }}</td>
                    <td>{{ $row['due_date'] ?: '-' }}</td>
                    <td>{{ $row['paid_date'] ?: '-' }}</td>
                    <td>
                        <div>{{ $row['processed_by'] ?: '-' }}</div>
                        <div class="muted">{{ $row['processed_at'] ?: '-' }}</div>
                    </td>
                    <td>{{ $row['description'] ?: '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9">No fee records found for current filters.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>

