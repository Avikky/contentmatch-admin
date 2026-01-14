<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $userName;

    public string $newPassword;

    public string $loginUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(string $userName, string $newPassword)
    {
        $this->userName = $userName;
        $this->newPassword = $newPassword;
        $this->loginUrl = route('login');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Password Reset - PAL Gratuity Fund Portal',
            from: config('mail.from.address', 'noreply@palgratuity.com'),
            replyTo: config('mail.reply_to.address', 'support@palgratuity.com'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.password-reset',
            with: [
                'userName' => $this->userName,
                'newPassword' => $this->newPassword,
                'loginUrl' => $this->loginUrl,
                'supportEmail' => config('mail.reply_to.address', 'support@palgratuity.com'),
                'appName' => 'PAL Gratuity Fund Portal',
                'companyName' => 'PAL Gratuity Fund',
            ]
        );
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
