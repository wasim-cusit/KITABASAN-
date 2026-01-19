<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redirecting to Payment Gateway...</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            text-align: center;
            max-width: 500px;
        }
        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        h2 {
            color: #333;
            margin-bottom: 10px;
        }
        p {
            color: #666;
            margin-bottom: 20px;
        }
        .info {
            background: #f0f7ff;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin: 20px 0;
            text-align: left;
            border-radius: 4px;
        }
        .info strong {
            display: block;
            margin-bottom: 5px;
            color: #333;
        }
        .info span {
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Redirecting to Payment Gateway</h2>
        <p>Please wait while we redirect you to complete your payment...</p>
        
        <div class="spinner"></div>
        
        <div class="info">
            <strong>Payment Details:</strong>
            <span>Course: {{ $course->title }}</span><br>
            <span>Amount: Rs. {{ number_format($payment->amount, 2) }}</span><br>
            <span>Transaction ID: {{ $payment->transaction_id }}</span>
        </div>
        
        <p style="font-size: 12px; color: #999; margin-top: 20px;">
            If you are not redirected automatically, please click the button below.
        </p>
        
        <form id="paymentForm" action="{{ $gateway['url'] }}" method="{{ $gateway['method'] }}">
            @foreach($gateway['fields'] as $key => $value)
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endforeach
            <button type="submit" style="background: #667eea; color: white; border: none; padding: 12px 30px; border-radius: 5px; cursor: pointer; font-size: 16px; margin-top: 10px;">
                Continue to Payment
            </button>
        </form>
    </div>

    <script>
        // Auto-submit form after 2 seconds
        setTimeout(function() {
            document.getElementById('paymentForm').submit();
        }, 2000);
    </script>
</body>
</html>
