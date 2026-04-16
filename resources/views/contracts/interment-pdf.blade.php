<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Interment Contract {{ $interment->interment_number ?? ('#'.$interment->id) }}</title>

    <style>
        @page { margin: 20px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1f2937; line-height: 1.4; }
        .muted { color: #6b7280; }
        .header { margin-bottom: 20px; border-bottom: 2px solid #374151; padding-bottom: 12px; }
        .brand { font-size: 20px; font-weight: 700; color: #111827; }
        .subtitle { font-size: 10px; color: #6b7280; margin-top: 2px; }
        .section { margin-top: 16px; page-break-inside: avoid; }
        .section-title { font-size: 12px; font-weight: 700; margin-bottom: 8px; color: #111827; border-bottom: 1px solid #d1d5db; padding-bottom: 4px; }
        .section-intro { margin-bottom: 12px; color: #374151; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 6px 8px; vertical-align: top; }
        .kv td { border: 1px solid #e5e7eb; }
        .kv td:first-child { width: 34%; background: #f3f4f6; font-weight: 600; }
        .terms { margin-top: 8px; }
        .term-item { margin-bottom: 10px; page-break-inside: avoid; }
        .term-number { font-weight: 700; color: #111827; display: inline-block; min-width: 24px; }
        .term-title { font-weight: 600; color: #111827; display: inline; }
        .term-content { color: #374151; margin-top: 2px; margin-left: 24px; line-height: 1.35; }
        .term-bullet { margin-left: 24px; margin-top: 2px; line-height: 1.35; color: #374151; }
        .term-sub { margin-left: 32px; margin-top: 2px; line-height: 1.35; color: #374151; }
        .signatures { margin-top: 24px; page-break-inside: avoid; }
        .sig-section { margin-bottom: 16px; }
        .sig-label { font-size: 10px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px; }
        .sig-line { border-bottom: 1px solid #111827; height: 20px; margin-bottom: 4px; }
        .sig-fields { display: inline-block; width: 100%; }
        .sig-field-row { display: inline-block; width: 48%; margin-right: 4%; }
        .sig-field-row:last-child { margin-right: 0; }
        .sig-field-label { font-size: 9px; color: #6b7280; margin-top: 4px; }
        .fees-table { margin-top: 8px; }
        .fees-table td { border: 1px solid #e5e7eb; padding: 6px 8px; }
        .fees-table td:first-child { background: #f9fafb; }
        .payment-status { display: inline-block; padding: 2px 8px; border-radius: 3px; font-size: 10px; font-weight: 600; }
        .payment-paid { background: #d1fae5; color: #065f46; }
        .payment-partial { background: #fef3c7; color: #92400e; }
        .payment-unpaid { background: #fee2e2; color: #991b1b; }
    </style>
</head>

<body>
    <div class="header">
        <div class="brand">Cemetery Interment Contract</div>
        <div class="subtitle">Memorial Park of San Sebastian</div>
    </div>

    <div class="section">
        <div class="section-intro">
            This Interment Contract ("Agreement") is entered into between:
        </div>
        <table class="kv">
            <tr>
                <td>Cemetery Authority</td>
                <td><strong>Memorial Park of San Sebastian</strong></td>
            </tr>
            <tr>
                <td>Lot Owner / Purchaser</td>
                <td>
                    {{ $client->full_name ?? '-' }}<br>
                    @if($client->address)
                        {{ $client->address }}<br>
                    @endif
                    {{ $client->phone ?? '' }} @if($client->email) | {{ $client->email }} @endif
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Contract Details</div>
        <table class="kv">
            <tr>
                <td>Interment Number</td>
                <td>{{ $interment->interment_number ?? ('#'.$interment->id) }}</td>
            </tr>
            <tr>
                <td>Contract Date</td>
                <td>{{ $interment->created_at?->format('Y-m-d') ?? '-' }}</td>
            </tr>
            <tr>
                <td>Status</td>
                <td>
                    {{ ucfirst($interment->status ?? '-') }}
                    @php
                        $paymentStatusClass = match($interment->payment_status) {
                            'fully_paid' => 'payment-paid',
                            'partial' => 'payment-partial',
                            default => 'payment-unpaid'
                        };
                        $paymentStatusText = match($interment->payment_status) {
                            'fully_paid' => 'FULLY PAID',
                            'partial' => 'PARTIAL',
                            default => 'UNPAID'
                        };
                    @endphp
                    <span class="payment-status {{ $paymentStatusClass }}">{{ $paymentStatusText }}</span>
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Deceased</div>
        <table class="kv">
            <tr>
                <td>Name</td>
                <td>{{ $interment->full_name }}</td>
            </tr>
            <tr>
                <td>Date of Birth</td>
                <td>{{ $interment->date_of_birth?->format('Y-m-d') ?? '-' }}</td>
            </tr>
            <tr>
                <td>Date of Death</td>
                <td>{{ $interment->date_of_death?->format('Y-m-d') ?? '-' }}</td>
            </tr>
            <tr>
                <td>Burial Date</td>
                <td>{{ $interment->burial_date?->format('Y-m-d') ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Lot</div>
        <table class="kv">
            <tr>
                <td>Lot ID</td>
                <td>{{ $lot->lot_id ?? '-' }}</td>
            </tr>
            <tr>
                <td>Lot Category</td>
                <td>{{ $lot->lot_category_label ?? ($lot->section ?? '-') }}</td>
            </tr>
            <tr>
                <td>Block</td>
                <td>{{ $lot->block ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Payment Information</div>
        <table class="kv">
            <tr>
                <td>Total Interment Fee</td>
                <td><strong>&#8369;{{ number_format((float) ($interment->interment_fee ?? 15000), 2) }}</strong></td>
            </tr>
            <tr>
                <td>Payment Before Excavation</td>
                <td>
                    &#8369;{{ number_format((float) ($interment->payment_before_excavation ?? 0), 2) }}
                    @if($interment->payment_before_excavation_date)
                        <span class="muted"> (paid on {{ $interment->payment_before_excavation_date?->format('Y-m-d') }})</span>
                    @endif
                </td>
            </tr>
            <tr>
                <td>Payment After Interment</td>
                <td>
                    &#8369;{{ number_format((float) ($interment->payment_after_interment ?? 0), 2) }}
                    @if($interment->payment_after_interment_date)
                        <span class="muted"> (paid on {{ $interment->payment_after_interment_date?->format('Y-m-d') }})</span>
                    @endif
                </td>
            </tr>
            <tr>
                <td>Total Paid</td>
                <td>
                    &#8369;{{ number_format((float) ($interment->payment_before_excavation ?? 0) + (float) ($interment->payment_after_interment ?? 0), 2) }}
                </td>
            </tr>
            <tr>
                <td>Remaining Balance</td>
                <td>
                    <strong>&#8369;{{ number_format($interment->remaining_balance, 2) }}</strong>
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Terms & Conditions</div>
        
        <div class="terms">
            <div class="term-item">
                <span class="term-number">1.</span> <span class="term-title">Grant of Rights</span>
                <div class="term-content">The Cemetery Authority grants the Lot Owner the Right of Interment in Lot No. {{ $lot->lot_id ?? '-' }}, subject to the terms and conditions herein.</div>
            </div>

            <div class="term-item">
                <span class="term-number">2.</span> <span class="term-title">Number of Interments</span>
                <div class="term-content">
                    A maximum of three (3) deceased persons may be interred in the designated lot.<br>
                    No interment shall exceed this limit.
                </div>
            </div>

            <div class="term-item">
                <span class="term-number">3.</span> <span class="term-title">Interment Interval</span>
                <div class="term-content">
                    After the first interment, a minimum of ten (10) years must elapse before excavation and subsequent interment may occur.<br>
                    The Cemetery Authority reserves the right to refuse interment if this condition is not met.
                </div>
            </div>

            <div class="term-item">
                <span class="term-number">4.</span> <span class="term-title">Compliance with Laws</span>
                <div class="term-content">
                    All interments shall comply with:
                    <div class="term-sub">• Local health and sanitation regulations</div>
                    <div class="term-sub">• Municipal ordinances and cemetery rules</div>
                    <div class="term-sub">• Applicable national laws governing burial and exhumation</div>
                </div>
            </div>

            <div class="term-item">
                <span class="term-number">5.</span> <span class="term-title">Fees and Payment Terms</span>
                <div class="term-content">
                    The total interment fee is <strong>&#8369;15,000.00</strong>.<br>
                    Payment shall be made in two installments:
                    <div class="term-sub">• &#8369;7,500.00 before excavation (required prior to scheduling excavation).</div>
                    <div class="term-sub">• &#8369;7,500.00 after interment (required before issuance of the Certificate of Interment).</div>
                    Failure to complete payment may result in suspension of services or withholding of official documents.<br><br>
                    The Lot Owner agrees to pay any additional administrative or maintenance fees as determined by the Cemetery Authority.
                </div>
            </div>

            <div class="term-item">
                <span class="term-number">6.</span> <span class="term-title">Maintenance</span>
                <div class="term-content">
                    The Cemetery Authority shall provide perpetual care and maintenance of the cemetery grounds, funded by maintenance fees.
                </div>
            </div>

            <div class="term-item">
                <span class="term-number">7.</span> <span class="term-title">Transfer and Succession</span>
                <div class="term-content">
                    Rights under this Agreement may be transferred or inherited only with written approval of the Cemetery Authority.<br>
                    Unauthorized transfers are void.
                </div>
            </div>

            <div class="term-item">
                <span class="term-number">8.</span> <span class="term-title">Records and Audit</span>
                <div class="term-content">
                    The Cemetery Authority shall maintain accurate interment records and audit logs for compliance and transparency.<br>
                    The Lot Owner may request copies of interment records upon written request.
                </div>
            </div>

            <div class="term-item">
                <span class="term-number">9.</span> <span class="term-title">Dispute Resolution</span>
                <div class="term-content">
                    Any dispute arising under this Agreement shall be resolved through mediation or arbitration, prior to court action, in accordance with applicable local arbitration rules.
                </div>
            </div>

            <div class="term-item">
                <span class="term-number">10.</span> <span class="term-title">Termination</span>
                <div class="term-content">
                    The Cemetery Authority may terminate this Agreement for:
                    <div class="term-sub">• Non-payment of fees</div>
                    <div class="term-sub">• Violation of interment rules</div>
                    <div class="term-sub">• Breach of contract terms</div>
                </div>
            </div>

            <div class="term-item">
                <span class="term-number">11.</span> <span class="term-title">Entire Agreement</span>
                <div class="term-content">
                    This Agreement constitutes the entire understanding between the parties and supersedes all prior agreements regarding the lot.
                </div>
            </div>
        </div>
    </div>

    <div class="signatures">
        <div class="section-title">Signatures</div>
        
        <p style="margin-bottom: 16px;">Signed this {{ $interment->created_at?->format('d') ?? '__' }} day of {{ $interment->created_at?->format('F Y') ?? '_____________ 20____' }} at Memorial Park of San Sebastian.</p>
        
        <div class="sig-section">
            <div class="sig-label">Lot Owner / Purchaser</div>
            <div class="sig-field-row">
                <div class="sig-field-label">Signature / Date</div>
                <div style="border-bottom: 1px solid #111827; height: 24px; margin-top: 4px;"></div>
            </div>
            <div style="margin-top: 12px;">
                <div class="sig-field-row">
                    <div style="border-bottom: 1px solid #111827; height: 16px; margin-bottom: 2px;"></div>
                    <div class="sig-field-label">Printed Name</div>
                </div>
            </div>
        </div>

        <div class="sig-section">
            <div class="sig-label">Cemetery Authority</div>
            <div class="sig-field-row">
                <div class="sig-field-label">Signature / Date</div>
                <div style="border-bottom: 1px solid #111827; height: 24px; margin-top: 4px;"></div>
            </div>
            <div style="margin-top: 12px;">
                <div class="sig-field-row">
                    <div style="border-bottom: 1px solid #111827; height: 16px; margin-bottom: 2px;"></div>
                    <div class="sig-field-label">Authorized Representative Name & Position</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
