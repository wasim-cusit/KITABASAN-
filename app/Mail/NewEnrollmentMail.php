<?php

namespace App\Mail;

use App\Models\CourseEnrollment;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewEnrollmentMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public CourseEnrollment $enrollment,
        public User $teacher
    ) {}

    public function envelope(): Envelope
    {
        $course = $this->enrollment->book->title ?? 'a course';
        return new Envelope(
            subject: 'New enrollment in: ' . $course . ' - ' . config('app.name', 'KITAB ASAN')
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.new-enrollment');
    }
}
