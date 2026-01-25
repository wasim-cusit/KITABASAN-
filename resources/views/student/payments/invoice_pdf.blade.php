<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $payment->transaction_id }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; color: #111827; margin: 24px; }
        .container { max-width: 760px; margin: 0 auto; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; }
        .logo { font-size: 18px; font-weight: 700; color: #111827; }
        .title { font-size: 22px; font-weight: 700; margin-bottom: 4px; }
        .muted { color: #6b7280; font-size: 12px; }
        .badge { display: inline-block; padding: 4px 10px; border-radius: 999px; font-size: 11px; font-weight: 700; }
        .badge-success { background: #dcfce7; color: #166534; }
        .badge-failed { background: #fee2e2; color: #991b1b; }
        .badge-pending { background: #fef3c7; color: #92400e; }
        .card { background: #f9fafb; padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; }
        .grid { display: table; width: 100%; }
        .grid-col { display: table-cell; width: 50%; vertical-align: top; padding-right: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { text-align: left; padding: 10px 12px; border-bottom: 1px solid #e5e7eb; }
        th { background: #f3f4f6; font-size: 11px; text-transform: uppercase; letter-spacing: 0.04em; color: #6b7280; }
        tfoot td { background: #f3f4f6; font-weight: 700; }
        .right { text-align: right; }
        .total { font-size: 14px; }
        .divider { height: 1px; background: #e5e7eb; margin: 16px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div>
                <div class="logo">KITAB ASAN</div>
                <div class="muted">Learning Management System</div>
            </div>
            <div style="text-align: right;">
                <div class="title">Invoice</div>
                <div class="muted">Invoice ID: {{ $payment->transaction_id }}</div>
                <div class="muted">Date: {{ $payment->created_at->format('M d, Y h:i A') }}</div>
                <div style="margin-top: 6px;">
                    @if($payment->status === 'completed')
                        <span class="badge badge-success">Completed</span>
                    @elseif($payment->status === 'failed')
                        <span class="badge badge-failed">Failed</span>
                    @else
                        <span class="badge badge-pending">Pending</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="grid">
            <div class="grid-col">
                <div class="card">
                    <div class="muted">Billed To</div>
                    <div>{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</div>
                    <div class="muted">{{ Auth::user()->email }}</div>
                </div>
            </div>
            <div class="grid-col">
                <div class="card">
                    <div class="muted">Payment Method</div>
                    <div>{{ $payment->paymentMethod->name ?? 'Manual' }}</div>
                    <div class="muted">Currency: {{ $payment->currency ?? 'PKR' }}</div>
                </div>
            </div>
        </div>

        <div class="divider"></div>

        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th class="right">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div><strong>{{ $course->title ?? 'Course' }}</strong></div>
                        <div class="muted">Course purchase</div>
                    </td>
                    <td class="right">Rs. {{ number_format($course->price ?? $payment->amount, 2) }}</td>
                </tr>
                @if($feeAmount > 0)
                    <tr>
                        <td>Transaction Fee</td>
                        <td class="right">Rs. {{ number_format($feeAmount, 2) }}</td>
                    </tr>
                @endif
            </tbody>
            <tfoot>
                <tr>
                    <td class="right total">Total Paid</td>
                    <td class="right total">Rs. {{ number_format($payment->amount, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</body>
</html>
