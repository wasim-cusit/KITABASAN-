<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        // Ensure subject is a string
        $safeSubject = is_string($subject ?? null) ? $subject : 'Notification';
        $safeSubject = (string) $safeSubject;
    @endphp
    <title>{{ e($safeSubject) }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .container {
            background-color: #ffffff;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 30px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #3b82f6;
        }
        .header h1 {
            color: #3b82f6;
            margin: 0;
            font-size: 24px;
        }
        .content {
            margin: 20px 0;
        }
        .content p {
            margin: 15px 0;
            font-size: 16px;
        }
        .greeting {
            font-weight: bold;
            color: #1f2937;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
            text-align: center;
            font-size: 12px;
            color: #6b7280;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #3b82f6;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ \App\Models\SystemSetting::getValue('site_name', 'KITAB ASAN') }}</h1>
        </div>

        <div class="content">
            @php
                // Ensure studentName is a string
                $safeStudentName = is_string($studentName ?? null) ? $studentName : '';
                $safeStudentName = (string) $safeStudentName;
            @endphp
            @if(!empty($safeStudentName))
            <p class="greeting">Dear {{ e($safeStudentName) }},</p>
            @else
            <p class="greeting">Dear Student,</p>
            @endif

            <div style="white-space: pre-wrap;">
                @php
                    // Ensure message is a string
                    $emailMessage = is_string($message ?? null) ? $message : '';
                    $emailMessage = (string) $emailMessage;
                @endphp
                {!! nl2br(e($emailMessage)) !!}
            </div>
        </div>

        <div class="footer">
            <p>This email was sent from {{ \App\Models\SystemSetting::getValue('site_name', 'KITAB ASAN') }}</p>
            <p>{{ \App\Models\SystemSetting::getValue('site_url', config('app.url')) }}</p>
            <p style="margin-top: 10px; color: #9ca3af;">
                If you have any questions, please contact us at {{ \App\Models\SystemSetting::getValue('site_email', config('mail.from.address')) }}
            </p>
        </div>
    </div>
</body>
</html>
