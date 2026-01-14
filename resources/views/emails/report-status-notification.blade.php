<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Status Update</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #4F46E5;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #f9fafb;
            padding: 30px;
            border: 1px solid #e5e7eb;
            border-top: none;
            border-radius: 0 0 8px 8px;
        }
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 4px;
            font-weight: bold;
            margin: 10px 0;
        }
        .status-resolved {
            background-color: #10b981;
            color: white;
        }
        .status-dismissed {
            background-color: #6b7280;
            color: white;
        }
        .status-reviewing {
            background-color: #f59e0b;
            color: white;
        }
        .details {
            background-color: white;
            padding: 20px;
            margin: 20px 0;
            border-radius: 6px;
            border-left: 4px solid #4F46E5;
        }
        .details-row {
            margin: 10px 0;
        }
        .details-label {
            font-weight: bold;
            color: #6b7280;
        }
        .footer {
            text-align: center;
            color: #6b7280;
            font-size: 12px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Report Status Update</h1>
    </div>
    
    <div class="content">
        <p>Hello {{ $reporterName }},</p>
        
        @if($action === 'resolved')
            <p>Your report (ID: #{{ $reportId }}) has been <strong>resolved</strong> by our moderation team.</p>
        @elseif($action === 'dismissed')
            <p>Your report (ID: #{{ $reportId }}) has been reviewed and <strong>dismissed</strong> by our moderation team.</p>
        @elseif($action === 'reviewing')
            <p>Your report (ID: #{{ $reportId }}) is currently being <strong>reviewed</strong> by our moderation team.</p>
        @else
            <p>The status of your report (ID: #{{ $reportId }}) has been updated.</p>
        @endif
        
        <div class="details">
            <div class="details-row">
                <span class="details-label">Report ID:</span> #{{ $reportId }}
            </div>
            <div class="details-row">
                <span class="details-label">Reported Item:</span> {{ $reportableType }}
            </div>
            <div class="details-row">
                <span class="details-label">Reason:</span> {{ ucfirst(str_replace('_', ' ', $reason)) }}
            </div>
            
            @if($action === 'resolved')
                <div class="status-badge status-resolved">✓ Resolved</div>
            @elseif($action === 'dismissed')
                <div class="status-badge status-dismissed">Dismissed</div>
            @elseif($action === 'reviewing')
                <div class="status-badge status-reviewing">Under Review</div>
            @endif
            
            @if($resolutionNotes)
                <div class="details-row" style="margin-top: 15px;">
                    <span class="details-label">Admin Notes:</span><br>
                    <p style="margin: 5px 0;">{{ $resolutionNotes }}</p>
                </div>
            @endif
            
            @if($resolvedAt)
                <div class="details-row">
                    <span class="details-label">Resolved At:</span> {{ $resolvedAt->format('M d, Y H:i:s') }}
                </div>
            @endif
        </div>
        
        @if($action === 'resolved')
            <p>Thank you for helping us maintain a safe and respectful community. Your report has been reviewed and appropriate action has been taken.</p>
        @elseif($action === 'dismissed')
            <p>After careful review, we determined that the reported content does not violate our community guidelines at this time. If you have additional concerns, please feel free to submit a new report.</p>
        @elseif($action === 'reviewing')
            <p>We take all reports seriously and will investigate this matter thoroughly. We'll notify you once a decision has been made.</p>
        @endif
        
        <p>If you have any questions or concerns, please don't hesitate to contact our support team.</p>
        
        <p>Best regards,<br>
        <strong>ContentMatch Moderation Team</strong></p>
    </div>
    
    <div class="footer">
        <p>&copy; {{ date('Y') }} ContentMatch. All rights reserved.</p>
        <p>This is an automated message. Please do not reply to this email.</p>
    </div>
</body>
</html>
