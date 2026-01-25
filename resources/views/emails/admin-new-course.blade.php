<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Course</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; background: #f5f5f5; }
        .container { background: #fff; border: 1px solid #e0e0e0; border-radius: 8px; padding: 30px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .header { text-align: center; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 2px solid #ca8a04; }
        .header h1 { color: #ca8a04; margin: 0; font-size: 22px; }
        .content p { margin: 12px 0; font-size: 15px; color: #4b5563; }
        .box { background: #fefce8; border: 1px solid #fde047; border-radius: 6px; padding: 14px; margin: 16px 0; font-size: 14px; }
        .footer { margin-top: 24px; padding-top: 16px; border-top: 1px solid #e0e0e0; text-align: center; font-size: 12px; color: #6b7280; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>New course created</h1>
        </div>
        <div class="content">
            <p>A new course has been created on the platform.</p>
            <div class="box">
                <strong>Course:</strong> {{ $course->title ?? 'N/A' }}<br>
                <strong>Teacher:</strong> {{ $course->teacher->name ?? ($course->teacher->email ?? 'N/A') }}<br>
                <strong>Status:</strong> {{ $course->status ?? 'N/A' }}<br>
                <strong>Source:</strong> {{ $source === 'admin' ? 'Created by administrator' : 'Created by teacher' }}<br>
                <strong>Date:</strong> {{ $course->created_at?->format('M d, Y H:i') ?? 'N/A' }}
            </div>
            <p>View in admin: <a href="{{ route('admin.courses.index') }}">Courses</a></p>
        </div>
        <div class="footer">
            <p>Admin notification from {{ config('app.name', 'KITAB ASAN') }}.</p>
        </div>
    </div>
</body>
</html>
