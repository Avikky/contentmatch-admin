<?php

namespace App\Mail;

use App\Models\Report;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReportStatusNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Report $report,
        public User $reporter,
        public string $action
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = match ($this->action) {
            'resolved' => 'Your Report Has Been Resolved',
            'dismissed' => 'Report Update',
            'reviewing' => 'Your Report is Being Reviewed',
            default => 'Report Status Update'
        };

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.report-status-notification',
            with: [
                'reporterName' => $this->reporter->full_name ?? $this->reporter->username,
                'reportId' => $this->report->id,
                'action' => $this->action,
                'reportableType' => $this->getReportableTypeName(),
                'reason' => $this->report->reason,
                'resolutionNotes' => $this->report->resolution_notes,
                'resolvedAt' => $this->report->resolved_at,
            ],
        );
    }

    /**
     * Get human-readable reportable type name
     */
    private function getReportableTypeName(): string
    {
        return match (class_basename($this->report->reportable_type)) {
            'User' => 'User',
            'Content' => 'Content',
            'Community' => 'Community',
            'CommunityMessage' => 'Message',
            default => 'Item'
        };
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
