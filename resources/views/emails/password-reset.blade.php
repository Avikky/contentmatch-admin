@extends('layouts.email')

@section('title', 'Password Reset - ContentMatch Admin Portal')

@section('content')
    <div class="greeting">
        Hello {{ $userName }},
    </div>
    
    <div class="message">
        Your password has been successfully reset for your <strong>ContentMatch Admin Portal</strong> account. 
        A new secure password has been generated for you to ensure the safety of your account.
    </div>
    
    <div class="credentials-box">
        <div class="credentials-title">
            🔐 Your New Login Credentials
        </div>
        
        <div class="credential-item">
            <span class="credential-label">Username:</span>
            <span class="credential-value">{{ $userName }}</span>
        </div>
        
        <div class="credential-item">
            <span class="credential-label">New Password:</span>
            <span class="credential-value">{{ $newPassword }}</span>
        </div>
    </div>
    
    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ $loginUrl }}" class="button">
            🚪 Login to Your Account
        </a>
    </div>
    
    <div class="security-notice">
        <div class="security-notice-title">🛡️ Important Security Information</div>
        <div class="security-notice-text">
            <strong>Please change this temporary password immediately after logging in.</strong><br><br>
            
            For your security:
            <ul style="margin: 10px 0; padding-left: 20px;">
                <li>This password is valid for 30 days from the reset date</li>
                <li>Do not share this password with anyone</li>
                <li>Choose a strong, unique password when updating</li>
                <li>Log out completely when finished using the portal</li>
            </ul>
        </div>
    </div>
    
    <div class="message">
        If you did not request this password reset, please contact our support team immediately at 
    <a href="mailto:{{ $supportEmail }}" style="color: #4F2DBE; font-weight: 600;">{{ $supportEmail }}</a>.
    </div>
    
    <div class="message" style="margin-top: 30px; font-size: 14px; color: #666666;">
        <strong>Need Help?</strong><br>
        If you're having trouble logging in or need assistance, our support team is here to help you. 
        Contact us at {{ $supportEmail }} or through the portal's help section.
    </div>
    
    <div style="margin-top: 30px; padding: 20px; background: #f4f0ff; border-radius: 8px; border-left: 4px solid #4F2DBE;">
        <strong style="color: #4F2DBE;">Password Reset Details:</strong><br>
        <small style="color: #666666;">
            Reset Date: {{ now()->format('F j, Y \a\t g:i A T') }}<br>
            Expiration: {{ now()->addDays(30)->format('F j, Y') }}<br>
            IP Address: {{ request()->ip() ?? 'System Generated' }}
        </small>
    </div>
@endsection