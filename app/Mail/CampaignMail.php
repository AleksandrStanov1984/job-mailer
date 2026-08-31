<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class CampaignMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $mailSubject,
        public string $mailMessage,
        public array $attachmentPaths = [],
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->mailSubject
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.campaign',
            with: [
                'mailMessage' =>
                    $this->mailMessage,
            ]
        );
    }

    public function attachments(): array
    {
        return collect(
            $this->attachmentPaths
        )
            ->map(function (
                string $path
            ): Attachment {
                $fullPath =
                    Storage::disk('local')
                        ->path($path);

                /*
                 * При сохранении впереди добавляется
                 * служебный префикс:
                 *
                 * 01_ab12CD34_resume.pdf
                 *
                 * В письме его убираем.
                 */
                $storedName =
                    basename($path);

                $originalName =
                    preg_replace(
                        '/^\d{2}_[A-Za-z0-9]{8}_/',
                        '',
                        $storedName
                    );

                return Attachment::fromPath(
                    $fullPath
                )->as(
                    $originalName
                );
            })
            ->all();
    }
}
