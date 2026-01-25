<?php

namespace App\Mail;

use App\Models\Book;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StudentCourseUpdateMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Book $course,
        public User $student,
        public string $changeSummary
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Course update: ' . ($this->course->title ?? 'Course') . ' - ' . config('app.name', 'KITAB ASAN')
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.student-course-update');
    }
}
