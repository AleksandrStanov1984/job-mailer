<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TestMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $mailSubject,
        public string $mailMessage,
        public array $uploadedAttachments = [],
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->mailSubject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.test',
            with: [
                'mailMessage' => $this->mailMessage,
            ],
        );
    }

    public function attachments(): array
    {
        return collect($this->uploadedAttachments)
            ->map(function (array $file) {
                return Attachment::fromPath($file['path'])
                    ->as($file['name'])
                    ->withMime($file['mime']);
            })
            ->all();
    }
}
