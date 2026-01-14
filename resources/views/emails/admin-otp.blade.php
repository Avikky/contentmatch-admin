<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login Verification Code</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            background-color: #f5f5f5;
            padding: 20px;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        .email-header {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            color: #ffffff;
            padding: 40px 30px;
            text-align: center;
        }
        .email-header h1 {
            font-size: 24px;
            font-weight: 600;
            margin: 0;
        }
        .email-body {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 18px;
            color: #1f2937;
            margin-bottom: 20px;
        }
        .content-text {
            color: #4b5563;
            font-size: 16px;
            margin-bottom: 30px;
            line-height: 1.8;
        }
        .otp-container {
            background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
            border: 2px solid #6366f1;
            border-radius: 12px;
            padding: 30px;
            text-align: center;
            margin: 30px 0;
        }
        .otp-label {
            font-size: 14px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 10px;
        }
        .otp-code {
            font-size: 42px;
            font-weight: 700;
            color: #6366f1;
            letter-spacing: 8px;
            font-family: 'Courier New', monospace;
        }
        .otp-expiry {
            font-size: 13px;
            color: #ef4444;
            margin-top: 15px;
            font-weight: 500;
        }
        .security-notice {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px 20px;
            margin: 30px 0;
            border-radius: 4px;
        }
        .security-notice p {
            color: #92400e;
            font-size: 14px;
            margin: 0;
        }
        .security-notice strong {
            color: #78350f;
        }
        .email-footer {
            background-color: #f9fafb;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        .footer-text {
            color: #6b7280;
            font-size: 14px;
            margin-bottom: 10px;
        }
        .footer-disclaimer {
            color: #9ca3af;
            font-size: 12px;
            margin-top: 15px;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>🔐 Admin Login Verification</h1>
        </div>
        
        <div class="email-body">
            <p class="greeting">Hello {{ $admin->full_name }},</p>
            
            <p class="content-text">
                You have requested to log in to the Admin Panel. To complete your login, please use the verification code below:
            </p>
            
            <div class="otp-container">
                <div class="otp-label">Your Verification Code</div>
                <div class="otp-code">{{ $otp }}</div>
                <div class="otp-expiry">⏱️ Expires in 5 minutes</div>
            </div>
            
            <p class="content-text">
                Enter this code on the verification page to access your admin account.
            </p>
            
            <div class="security-notice">
                <p>
                    <strong>🛡️ Security Notice:</strong><br>
                    If you did not request this code, please ignore this email. Someone may have entered your email address by mistake. Your account remains secure.
                </p>
            </div>
        </div>
        
        <div class="email-footer">
            <p class="footer-text">
                <strong>Need Help?</strong><br>
                Contact your system administrator if you're experiencing issues.
            </p>
            <p class="footer-disclaimer">
                This is an automated email from the Admin Panel system.<br>
                Please do not reply to this email.
            </p>
        </div>
    </div>
</body>
</html>
