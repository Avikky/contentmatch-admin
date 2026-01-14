<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Account Created</title>
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
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
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
            margin-bottom: 20px;
            line-height: 1.8;
        }
        .credentials-box {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            border: 2px solid #10b981;
            border-radius: 12px;
            padding: 25px;
            margin: 30px 0;
        }
        .credential-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #d1fae5;
        }
        .credential-row:last-child {
            border-bottom: none;
        }
        .credential-label {
            font-size: 14px;
            color: #047857;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .credential-value {
            font-size: 16px;
            color: #065f46;
            font-weight: 600;
            font-family: 'Courier New', monospace;
            background-color: #ffffff;
            padding: 8px 16px;
            border-radius: 6px;
            border: 1px solid #a7f3d0;
        }
        .login-button {
            display: inline-block;
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            color: #ffffff;
            text-decoration: none;
            padding: 14px 32px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            text-align: center;
            margin: 20px 0;
            transition: transform 0.2s;
        }
        .login-button:hover {
            transform: translateY(-2px);
        }
        .instructions {
            background-color: #eff6ff;
            border-left: 4px solid #3b82f6;
            padding: 20px;
            margin: 30px 0;
            border-radius: 4px;
        }
        .instructions h3 {
            color: #1e40af;
            font-size: 16px;
            margin-bottom: 12px;
        }
        .instructions ol {
            color: #1e3a8a;
            font-size: 14px;
            margin-left: 20px;
        }
        .instructions li {
            margin-bottom: 8px;
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
            <h1>🎉 Welcome to Admin Panel</h1>
        </div>
        
        <div class="email-body">
            <p class="greeting">Hello {{ $admin->full_name }},</p>
            
            <p class="content-text">
                Your admin account has been successfully created! You now have access to the Admin Panel with <strong>{{ ucfirst($admin->role) }}</strong> privileges.
            </p>
            
            <div class="credentials-box">
                <div class="credential-row">
                    <span class="credential-label">Full Name</span>
                    <span class="credential-value">{{ $admin->full_name }}</span>
                </div>
                <div class="credential-row">
                    <span class="credential-label">Login Email</span>
                    <span class="credential-value">{{ $admin->email }}</span>
                </div>
                <div class="credential-row">
                    <span class="credential-label">Temporary Password</span>
                    <span class="credential-value">{{ $password }}</span>
                </div>
                <div class="credential-row">
                    <span class="credential-label">Role</span>
                    <span class="credential-value">{{ ucfirst($admin->role) }}</span>
                </div>
            </div>
            
            <div class="instructions">
                <h3>📋 Login Instructions:</h3>
                <ol>
                    <li>Click the login button below or visit the admin panel</li>
                    <li>Enter your email and the temporary password provided above</li>
                    <li>You'll receive a 6-digit OTP (One-Time Password) via email</li>
                    <li>Enter the OTP to complete your login</li>
                    <li>Once logged in, consider changing your password for security</li>
                </ol>
            </div>
            
            <center>
                <a href="{{ config('app.url') }}/admin/login" class="login-button">
                    🔐 Login to Admin Panel
                </a>
            </center>
            
            <div class="security-notice">
                <p>
                    <strong>🛡️ Security Reminder:</strong><br>
                    Keep your credentials secure and do not share them with anyone. For your security, this system uses two-factor authentication (2FA) with email OTP verification.
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
                Please do not reply to this email.<br>
                If you did not request this account, please contact the administrator immediately.
            </p>
        </div>
    </div>
</body>
</html>
