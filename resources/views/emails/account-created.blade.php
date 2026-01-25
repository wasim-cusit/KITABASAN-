<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Created</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; background: #f5f5f5; }
        .container { background: #fff; border: 1px solid #e0e0e0; border-radius: 8px; padding: 30px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .header { text-align: center; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 2px solid #3b82f6; }
        .header h1 { color: #3b82f6; margin: 0; font-size: 22px; }
        .content p { margin: 12px 0; font-size: 15px; color: #4b5563; }
        .highlight { background: #eff6ff; border-left: 4px solid #3b82f6; padding: 12px; margin: 16px 0; border-radius: 4px; font-size: 14px; }
        .footer { margin-top: 24px; padding-top: 16px; border-top: 1px solid #e0e0e0; text-align: center; font-size: 12px; color: #6b7280; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Welcome to {{ config('app.name', 'KITAB ASAN') }}</h1>
        </div>
        <div class="content">
            <p>Hello {{ $user->first_name ?: $user->name }},</p>
            @if($plainPassword)
                <p>An account has been created for you. Use the password provided by your administrator to sign in.</p>
                <div class="highlight">
                    <strong>Email:</strong> {{ $user->email }}
                </div>
                <p>We recommend changing your password after your first login.</p>
            @else
                <p>Your account has been created successfully. You can now sign in with your email and the password you set.</p>
            @endif
            <p>Sign in here: <a href="{{ url('/login') }}">{{ url('/login') }}</a></p>
        </div>
        <div class="footer">
            <p>This is an automated message from {{ config('app.name', 'KITAB ASAN') }}.</p>
        </div>
    </div>
</body>
</html>
