<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #1D1B20;
            padding: 20px;
            text-align: center;
        }
        .header img {
            max-height: 50px;
        }
        .content {
            background-color: #fff;
            padding: 30px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .verification-code {
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            letter-spacing: 5px;
            margin: 20px 0;
            padding: 10px;
            background-color: #f7f7f7;
            border-radius: 5px;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #666;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #1D1B20;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        @media only screen and (max-width: 600px) {
            .container {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="https://{{ $_SERVER['HTTP_HOST'] ?? env('APP_URL', 'yourdomain.com') }}/assets/MTFC_LOGO.PNG" alt="MTFC Logo">
        </div>
        <div class="content">
            <h2>Password Reset Request</h2>
            <p>Hello {{ $user->full_name }},</p>
            <p>We received a request to reset your MTFC account password. Please use the verification code below to complete the password reset process:</p>
            
            <div class="verification-code">{{ $code }}</div>
            
            <p>This code will expire in 1 hour.</p>
            
            <p>If you didn't request a password reset, please ignore this email or contact support if you have concerns about your account security.</p>
            
            <p>Thank you,<br>The MTFC Team</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} MTFC. All rights reserved.</p>
            <p>This is an automated message, please do not reply.</p>
        </div>
    </div>
</body>
</html> 