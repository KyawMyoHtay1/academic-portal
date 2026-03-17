<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Receipt</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
            padding: 40px;
            background: #fff;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #1e3a8a;
            padding-bottom: 20px;
        }
        .header h1 {
            font-size: 28px;
            color: #1e3a8a;
            margin-bottom: 5px;
        }
        .header p {
            color: #666;
            font-size: 11px;
        }
        .receipt-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .info-box {
            flex: 1;
        }
        .info-box h3 {
            font-size: 14px;
            color: #1e3a8a;
            margin-bottom: 10px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 5px;
        }
        .info-box p {
            margin: 5px 0;
            line-height: 1.6;
        }
        .info-label {
            font-weight: bold;
            color: #555;
            display: inline-block;
            width: 120px;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin: 30px 0;
        }
        .details-table th {
            background-color: #1e3a8a;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: bold;
        }
        .details-table td {
            padding: 12px;
            border-bottom: 1px solid #e5e7eb;
        }
        .details-table tr:last-child td {
            border-bottom: none;
        }
        .amount-row {
            background-color: #f3f4f6;
            font-weight: bold;
        }
        .total-row {
            background-color: #1e3a8a;
            color: white;
            font-size: 16px;
        }
        .total-row td {
            padding: 15px 12px;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #e5e7eb;
            text-align: center;
            color: #666;
            font-size: 10px;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 11px;
        }
        .status-paid {
            background-color: #10b981;
            color: white;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>UNIVERSITY ACADEMIC PORTAL</h1>
        <p>Payment Receipt</p>
    </div>

    <div class="receipt-info">
        <div class="info-box">
            <h3>Receipt Information</h3>
            <p><span class="info-label">Receipt No:</span> {{ $receipt_number }}</p>
            <p><span class="info-label">Date:</span> {{ $generated_at }}</p>
            <p><span class="info-label">Status:</span> <span class="status-badge status-paid">PAID</span></p>
            @if($fee->processor)
            <p><span class="info-label">Processed by:</span> {{ $fee->processor->name }}</p>
            @if($fee->payment_processed_at)
            <p><span class="info-label">Processed at:</span> {{ $fee->payment_processed_at->format('F j, Y g:i A') }}</p>
            @endif
            @endif
        </div>
        <div class="info-box">
            <h3>Student Information</h3>
            <p><span class="info-label">Student No:</span> {{ $student->student_no }}</p>
            <p><span class="info-label">Name:</span> {{ $student->user?->name ?? 'N/A' }}</p>
            <p><span class="info-label">Email:</span> {{ $student->user?->email ?? 'N/A' }}</p>
            <p><span class="info-label">Programme:</span> {{ $student->programme ?? 'N/A' }}</p>
        </div>
    </div>

    <table class="details-table">
        <thead>
            <tr>
                <th>Description</th>
                <th>Due Date</th>
                <th>Paid Date</th>
                <th style="text-align: right;">Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $fee->description ?? 'Tuition Fee' }}</td>
                <td>{{ $fee->due_date->format('F j, Y') }}</td>
                <td>{{ $fee->paid_date ? $fee->paid_date->format('F j, Y') : 'N/A' }}</td>
                <td style="text-align: right;">£{{ number_format($fee->amount, 2) }}</td>
            </tr>
            <tr class="total-row">
                <td colspan="3" style="text-align: right; font-weight: bold;">TOTAL PAID:</td>
                <td style="text-align: right;">£{{ number_format($fee->amount, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>This is an official receipt from the University Academic Portal.</p>
        <p>Generated on {{ $generated_at }}</p>
        <p style="margin-top: 10px;">For inquiries, please contact the Finance Office.</p>
    </div>
</body>
</html>
