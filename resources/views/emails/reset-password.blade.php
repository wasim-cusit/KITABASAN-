<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background-color: #ffffff;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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
            color: #4b5563;
        }
        .greeting {
            font-weight: bold;
            color: #1f2937;
            font-size: 18px;
        }
        .button-container {
            text-align: center;
            margin: 30px 0;
        }
        .button {
            display: inline-block;
            padding: 14px 28px;
            background-color: #3b82f6;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            font-size: 16px;
        }
        .button:hover {
            background-color: #2563eb;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
            text-align: center;
            font-size: 12px;
            color: #6b7280;
        }
        .warning {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 12px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .warning p {
            margin: 0;
            font-size: 14px;
            color: #92400e;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Reset Your Password</h1>
        </div>
        
        <div class="content">
            <p class="greeting">Hello {{ $user->name }},</p>
            
            <p>You are receiving this email because we received a password reset request for your account.</p>
            
            <p>Click the button below to reset your password:</p>
            
            <div class="button-container">
                <a href="{{ $url }}" class="button">Reset Password</a>
            </div>
            
            <p>Or copy and paste this link into your browser:</p>
            <p style="word-break: break-all; color: #3b82f6; font-size: 14px;">{{ $url }}</p>
            
            <div class="warning">
                <p><strong>Security Notice:</strong> This password reset link will expire in 60 minutes. If you did not request a password reset, please ignore this email or contact support if you have concerns.</p>
            </div>
            
            <p>If you did not request a password reset, no further action is required.</p>
        </div>
        
        <div class="footer">
            <p>This is an automated message from {{ config('app.name', 'KITAB ASAN') }}.</p>
            <p>Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>
