<?php

namespace App\Mail;

use App\Models\Event;
use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class EnrollmentConfirmed extends Mailable
{
    public function __construct(public Event $event, public User $user) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: __('Inscripción confirmada — :tour', ['tour' => $this->event->tour->tr('name')]));
    }

    public function content(): Content
    {
        return new Content(view: 'emails.enrollment');
    }
}
