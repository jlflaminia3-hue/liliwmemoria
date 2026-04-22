<p>Hello{{ $payment->client?->full_name ? ' '.$payment->client->full_name : '' }},</p>

<p>
    Thank you for your full payment for Lot {{ $payment->lot?->lot_number ?? 'N/A' }}.
    Your lot ownership is now complete!
</p>

<p>
    <strong>Payment Details:</strong><br>
    Payment Number: {{ $payment->payment_number ?? 'N/A' }}<br>
    Amount Paid: &#8369;{{ number_format((float) $payment->amount, 2) }}<br>
    Payment Date: {{ $payment->payment_date?->format('Y-m-d') ?? '-' }}<br>
    @if($payment->reference_number)
    Reference: {{ $payment->reference_number }}<br>
    @endif
</p>

<p>
    Please find attached your official <strong>Lot Reservation Contract - Full Payment</strong>.
    This document serves as your proof of ownership and should be kept in a safe place.
</p>

<p>
    <strong>Next Steps:</strong><br>
    • Your lot is now reserved exclusively for you<br>
    • For interment services, please contact us at least 7 days in advance<br>
    • First interment fee: &#8369;15,000.00
</p>

<p class="text-muted">
    If you have any questions, please don't hesitate to contact us.
</p>

<p>Best regards,<br>
Memorial Park of San Sebastian<br>
Liliw, Laguna</p>