<?php

namespace App\Mail;

use App\Models\PaymentInstallment;
use App\Models\PaymentPlan;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class PaymentScheduleMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly PaymentPlan $plan,
        public readonly ?PaymentInstallment $nextInstallment,
        /** @var Collection<int, PaymentInstallment> */
        public readonly Collection $upcomingInstallments,
        public readonly string $instructions,
    ) {}

    public function build(): self
    {
        $subject = "Payment Schedule - {$this->plan->plan_number}";

        return $this->subject($subject)
            ->view('emails.payment_schedule')
            ->with([
                'plan' => $this->plan,
                'nextInstallment' => $this->nextInstallment,
                'upcomingInstallments' => $this->upcomingInstallments,
                'instructions' => $this->instructions,
            ]);
    }
}

