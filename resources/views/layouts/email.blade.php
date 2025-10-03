<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'ContentMatch Admin Portal')</title>
    <style>
        /* Reset Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333333;
            background-color: #f8f9fc;
        }
        
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        /* Header Styles */
        .header {
            background: linear-gradient(135deg, #0F051D 0%, #4F2DBE 50%, #6C4FE0 100%);
            padding: 30px;
            text-align: center;
            color: white;
        }
        
        .logo {
            width: 120px;
            height: auto;
            margin-bottom: 15px;
            filter: brightness(0) invert(1);
        }
        
        .app-name {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 5px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
            letter-spacing: 1px;
        }
        
        .app-tagline {
            font-size: 14px;
            opacity: 0.9;
            font-weight: 300;
        }
        
        /* Content Styles */
        .content {
            padding: 40px 30px;
        }
        
        .greeting {
            font-size: 24px;
            color: #09518c;
            margin-bottom: 20px;
            font-weight: 600;
        }
        
        .message {
            font-size: 16px;
            line-height: 1.7;
            color: #555555;
            margin-bottom: 25px;
        }
        
        .credentials-box {
            background: #f8f9fc;
            border: 2px solid #e3e6f0;
            border-radius: 8px;
            padding: 25px;
            margin: 25px 0;
            text-align: center;
        }
        
        .credentials-title {
            font-size: 18px;
            color: #09518c;
            margin-bottom: 15px;
            font-weight: 600;
        }
        
        .credential-item {
            margin: 12px 0;
            font-size: 16px;
        }
        
        .credential-label {
            font-weight: 600;
            color: #333333;
            display: inline-block;
            width: 100px;
            text-align: left;
        }
        
        .credential-value {
            font-family: 'Courier New', monospace;
            background: #ffffff;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            color: #333333;
            font-weight: bold;
            font-size: 16px;
        }
        
        .button {
            display: inline-block;
            padding: 15px 30px;
            background: linear-gradient(135deg, #6C4FE0 0%, #0F051D 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            text-align: center;
            margin: 20px 0;
            transition: transform 0.2s ease;
            box-shadow: 0 4px 12px rgba(141, 198, 63, 0.3);
        }
        
        .button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(141, 198, 63, 0.4);
        }
        
        .security-notice {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 6px;
            padding: 20px;
            margin: 25px 0;
            color: #856404;
        }
        
        .security-notice-title {
            font-weight: 600;
            margin-bottom: 8px;
            color: #856404;
        }
        
        .security-notice-text {
            font-size: 14px;
            line-height: 1.5;
        }
        
        /* Footer Styles */
        .footer {
            background-color: #f8f9fc;
            padding: 30px;
            border-top: 1px solid #e3e6f0;
            text-align: center;
        }
        
        .footer-content {
            color: #666666;
            font-size: 14px;
            line-height: 1.6;
        }
        
        .footer-links {
            margin: 15px 0;
        }
        
        .footer-link {
            color: #09518c;
            text-decoration: none;
            margin: 0 10px;
            font-weight: 500;
        }
        
        .footer-link:hover {
            text-decoration: underline;
        }
        
        .copyright {
            margin-top: 20px;
            font-size: 12px;
            color: #999999;
        }
        
        /* Responsive Design */
        @media (max-width: 600px) {
            .container {
                margin: 0;
                border-radius: 0;
            }
            
            .header, .content, .footer {
                padding: 20px;
            }
            
            .app-name {
                font-size: 24px;
            }
            
            .greeting {
                font-size: 20px;
            }
            
            .credentials-box {
                padding: 20px 15px;
            }
            
            .credential-label {
                width: 80px;
                font-size: 14px;
            }
            
            .credential-value {
                font-size: 14px;
                padding: 6px 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="app-name">ContentMatch</div>
            <div class="app-tagline">Admin Portal</div>
        </div>
        
        <!-- Main Content -->
        <div class="content">
            @yield('content')
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <div class="footer-content">
                <strong>{{ $companyName ?? 'ContentMatch' }}</strong><br>
                Powering community-led social media intelligence
                
                <div class="footer-links">
                    <a href="{{ $loginUrl ?? '#' }}" class="footer-link">Login Portal</a>
                    <a href="mailto:{{ $supportEmail ?? 'support@contentmatch.io' }}" class="footer-link">Support</a>
                </div>
                
                <div class="copyright">
                    &copy; {{ date('Y') }} ContentMatch. All rights reserved.<br>
                    This is an automated message. Please do not reply to this email.
                </div>
            </div>
        </div>
    </div>
</body>
</html>