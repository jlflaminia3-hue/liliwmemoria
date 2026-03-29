<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payment Schedule</title>
</head>
<body style="font-family: Arial, Helvetica, sans-serif; line-height: 1.4; color: #222;">
    <h2 style="margin: 0 0 8px;">Payment Schedule</h2>
    <p style="margin: 0 0 16px;">
        Hello {{ $plan->client->full_name }},
    </p>

    <p style="margin: 0 0 16px;">
        This is a payment reminder for your payment plan <strong>{{ $plan->plan_number }}</strong>.
    </p>

    @if ($nextInstallment)
        @php
            $nextBalance = $nextInstallment->installmentBalance() + $nextInstallment->penaltyBalance();
        @endphp
        <div style="padding: 12px; border: 1px solid #ddd; border-radius: 6px; margin-bottom: 16px;">
            <div style="font-size: 12px; color: #666;">Next payment due</div>
            <div style="font-size: 18px; font-weight: 700; margin: 4px 0;">
                {{ number_format((float) $nextBalance, 2) }}
            </div>
            <div style="color: #333;">
                Due date: <strong>{{ $nextInstallment->due_date->format('Y-m-d') }}</strong>
            </div>
            @if ($nextInstallment->amount_paid > 0)
                <div style="color: #666; font-size: 12px; margin-top: 6px;">
                    Installment: {{ number_format((float) $nextInstallment->amount_due, 2) }}
                    &middot; Paid: {{ number_format((float) $nextInstallment->amount_paid, 2) }}
                    &middot; Remaining: {{ number_format((float) $nextInstallment->installmentBalance(), 2) }}
                </div>
            @endif
        </div>
    @endif

    @if ($upcomingInstallments->isNotEmpty())
        <h3 style="margin: 0 0 8px;">Installment schedule (balances due)</h3>
        <table cellpadding="8" cellspacing="0" border="1" style="border-collapse: collapse; width: 100%; margin-bottom: 16px; border-color: #ddd;">
            <thead>
                <tr style="background: #f7f7f7;">
                    <th align="left">Due Date</th>
                    <th align="right">Monthly Amount</th>
                    <th align="right">Paid</th>
                    <th align="right">Balance Due</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($upcomingInstallments as $inst)
                    @php
                        $balance = $inst->installmentBalance() + $inst->penaltyBalance();
                    @endphp
                    <tr>
                        <td>{{ $inst->due_date->format('Y-m-d') }}</td>
                        <td align="right">{{ number_format((float) $inst->amount_due, 2) }}</td>
                        <td align="right">{{ number_format((float) $inst->amount_paid, 2) }}</td>
                        <td align="right">{{ number_format((float) $balance, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <h3 style="margin: 0 0 8px;">How to send your payment</h3>
    <div style="white-space: pre-line; padding: 12px; border: 1px solid #ddd; border-radius: 6px; margin-bottom: 16px;">
        {{ $instructions }}
    </div>

    <p style="margin: 0;">
        Reference: <strong>{{ $plan->plan_number }}</strong>
    </p>

    <p style="margin: 16px 0 0; color: #666; font-size: 12px;">
        If you have already paid, please ignore this message.
    </p>
</body>
</html>
