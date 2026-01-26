<?php

namespace App\Mail;

use App\Models\DeviceBinding;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminDeviceResetRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public DeviceBinding $binding) {}

    public function envelope(): Envelope
    {
        $userLabel = $this->binding->user?->name ?? $this->binding->user?->email ?? 'User';
        return new Envelope(
            subject: '[Admin] Device reset request: ' . $userLabel . ' - ' . config('app.name', 'KITAB ASAN')
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.admin-device-reset-request');
    }
}

