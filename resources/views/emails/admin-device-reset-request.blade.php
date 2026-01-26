<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Device Reset Request</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; background: #f5f5f5; }
        .container { background: #fff; border: 1px solid #e0e0e0; border-radius: 8px; padding: 30px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .header { text-align: center; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 2px solid #f59e0b; }
        .header h1 { color: #b45309; margin: 0; font-size: 22px; }
        .content p { margin: 12px 0; font-size: 15px; color: #4b5563; }
        .box { background: #fffbeb; border: 1px solid #fde68a; border-radius: 6px; padding: 14px; margin: 16px 0; font-size: 14px; }
        .footer { margin-top: 24px; padding-top: 16px; border-top: 1px solid #e0e0e0; text-align: center; font-size: 12px; color: #6b7280; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Device reset requested</h1>
    </div>
    <div class="content">
        <p>A user requested a device reset and is waiting for admin approval.</p>
        <div class="box">
            <strong>User:</strong> {{ $binding->user?->name ?? 'N/A' }} ({{ $binding->user?->email ?? 'N/A' }})<br>
            <strong>Device:</strong> {{ $binding->device_name ?? 'Unknown Device' }}<br>
            <strong>IP:</strong> {{ $binding->ip_address ?? 'N/A' }}<br>
            <strong>Requested at:</strong> {{ $binding->reset_requested_at?->format('M d, Y H:i') ?? 'N/A' }}<br>
            <strong>Reason:</strong> {{ $binding->reset_request_reason ?? 'N/A' }}
        </div>
        <p>Review requests in admin: <a href="{{ route('admin.devices.index', ['status' => 'pending_reset']) }}">Pending Reset Requests</a></p>
    </div>
    <div class="footer">
        <p>Admin notification from {{ config('app.name', 'KITAB ASAN') }}.</p>
    </div>
</div>
</body>
</html>

