<?php

namespace App\Mail;

use App\Models\Event;
use App\Models\Memory;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class MemoryReady extends Mailable
{
    public function __construct(public Memory $memory, public Event $event) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: __('Tu recuerdo de la Ruta del Chocolate está listo'));
    }

    public function content(): Content
    {
        return new Content(view: 'emails.memory');
    }
}
