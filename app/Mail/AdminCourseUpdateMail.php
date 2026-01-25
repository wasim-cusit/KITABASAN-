<?php

namespace App\Mail;

use App\Models\Book;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminCourseUpdateMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Book $course,
        public string $changeSummary
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[Admin] Course update: ' . ($this->course->title ?? 'Course') . ' - ' . config('app.name', 'KITAB ASAN')
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.admin-course-update');
    }
}
