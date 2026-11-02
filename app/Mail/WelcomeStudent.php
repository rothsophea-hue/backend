<?php
namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class WelcomeStudent extends Mailable
{
    public function __construct(public string $studentName) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Welcome to School');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.welcome');
    }
}
