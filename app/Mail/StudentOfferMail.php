<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\SystemSetting;

class StudentOfferMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $message;
    public $studentName;

    /**
     * Create a new message instance.
     */
    public function __construct(string $subject, string $message, string $studentName = '')
    {
        $this->subject = $subject;
        $this->message = $message;
        $this->studentName = $studentName;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        // Get system email settings (use mail_from_address if available, otherwise site_email)
        $fromAddress = SystemSetting::getValue('mail_from_address',
            SystemSetting::getValue('site_email', config('mail.from.address')));
        $fromName = SystemSetting::getValue('mail_from_name',
            SystemSetting::getValue('site_name', config('mail.from.name')));

        // Ensure from address is not empty
        if (empty($fromAddress)) {
            $fromAddress = config('mail.from.address', 'noreply@example.com');
        }

        // Ensure from name is not empty
        if (empty($fromName)) {
            $fromName = config('mail.from.name', 'KITAB ASAN');
        }

        return new Envelope(
            subject: $this->subject ?: 'Notification from ' . $fromName,
            from: new Address($fromAddress, $fromName),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // Ensure all variables are strings to prevent type errors in template
        $subject = is_string($this->subject) ? $this->subject : (string) ($this->subject ?? 'Notification');
        $message = is_string($this->message) ? $this->message : (string) ($this->message ?? '');
        $studentName = is_string($this->studentName) ? $this->studentName : (string) ($this->studentName ?? '');

        return new Content(
            view: 'emails.student-offer',
            with: [
                'subject' => $subject,
                'message' => $message,
                'studentName' => $studentName,
            ],
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
