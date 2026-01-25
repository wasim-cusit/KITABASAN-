<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enrollment Confirmation</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; background: #f5f5f5; }
        .container { background: #fff; border: 1px solid #e0e0e0; border-radius: 8px; padding: 30px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .header { text-align: center; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 2px solid #16a34a; }
        .header h1 { color: #16a34a; margin: 0; font-size: 22px; }
        .content p { margin: 12px 0; font-size: 15px; color: #4b5563; }
        .box { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 6px; padding: 14px; margin: 16px 0; font-size: 14px; }
        .footer { margin-top: 24px; padding-top: 16px; border-top: 1px solid #e0e0e0; text-align: center; font-size: 12px; color: #6b7280; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>You're enrolled!</h1>
        </div>
        <div class="content">
            <p>Hello {{ $student->first_name ?: $student->name }},</p>
            <p>You have been successfully enrolled in the following course.</p>
            <div class="box">
                <strong>Course:</strong> {{ $enrollment->book->title ?? 'N/A' }}<br>
                <strong>Enrolled at:</strong> {{ $enrollment->enrolled_at?->format('M d, Y H:i') ?? 'N/A' }}
            </div>
            <p>Start learning: <a href="{{ route('student.learning.index', $enrollment->book_id) }}">Go to course</a></p>
        </div>
        <div class="footer">
            <p>This is an automated message from {{ config('app.name', 'KITAB ASAN') }}. You can change these emails in Settings.</p>
        </div>
    </div>
</body>
</html>
