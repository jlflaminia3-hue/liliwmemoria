<?php

namespace App\Mail;

use App\Models\ClientContract;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContractPdfMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly ClientContract $contract,
        public readonly string $pdfBinary,
        public readonly string $filename,
    ) {}

    public function build(): self
    {
        $subject = 'Contract PDF - ' . ($this->contract->contract_number ?? ('#' . $this->contract->id));

        return $this->subject($subject)
            ->view('emails.contract_pdf')
            ->with(['contract' => $this->contract])
            ->attachData($this->pdfBinary, $this->filename, ['mime' => 'application/pdf']);
    }
}

