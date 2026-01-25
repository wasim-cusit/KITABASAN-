<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AccountCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $role = 'student',
        public ?string $plainPassword = null
    ) {}

    public function envelope(): Envelope
    {
        $app = config('app.name', 'KITAB ASAN');
        $subject = $this->plainPassword
            ? "Your {$app} account has been created"
            : "Welcome to {$app}";

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.account-created');
    }
}
