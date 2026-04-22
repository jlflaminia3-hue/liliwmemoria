<?php

namespace App\Mail;

use App\Models\LotPayment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FullPaymentPdfMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly LotPayment $payment,
        public readonly string $pdfBinary,
        public readonly string $filename,
    ) {}

    public function build(): self
    {
        $subject = 'Lot Purchase Contract - Full Payment - '.$this->payment->payment_number;

        return $this->subject($subject)
            ->view('emails.full_payment_contract')
            ->with(['payment' => $this->payment])
            ->attachData($this->pdfBinary, $this->filename, ['mime' => 'application/pdf']);
    }
}
