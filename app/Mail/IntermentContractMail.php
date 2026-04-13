<?php

namespace App\Mail;

use App\Models\Deceased;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class IntermentContractMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Deceased $interment,
        public string $pdfBinary,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Interment Contract - '.($this->interment->interment_number ?? $this->interment->id),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.interment-contract',
        );
    }

    public function attachments(): array
    {
        $filename = 'Interment-Contract-'.($this->interment->interment_number ?? $this->interment->id).'.pdf';

        return [
            Attachment::fromData(
                fn () => $this->pdfBinary,
                $filename
            )->withMime('application/pdf'),
        ];
    }
}
