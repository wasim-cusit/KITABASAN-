<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminNewTeacherMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $teacher)
    {}

    public function envelope(): Envelope
    {
        $name = $this->teacher->first_name ? trim($this->teacher->first_name . ' ' . $this->teacher->last_name) : $this->teacher->name;
        return new Envelope(
            subject: '[Admin] New teacher: ' . ($name ?: $this->teacher->email) . ' - ' . config('app.name', 'KITAB ASAN')
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.admin-new-teacher');
    }
}
