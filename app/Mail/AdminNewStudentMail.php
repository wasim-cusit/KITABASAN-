<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminNewStudentMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $student,
        public string $source = 'registration'
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[Admin] New student: ' . ($this->student->name ?? $this->student->email) . ' - ' . config('app.name', 'KITAB ASAN')
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.admin-new-student');
    }
}
