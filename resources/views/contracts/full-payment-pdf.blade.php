<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Lot Reservation Contract - Full Payment {{ $payment->payment_number ?? ('#'.$payment->id) }}</title>

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
        .payment-status { display: inline-block; padding: 2px 8px; border-radius: 3px; font-size: 10px; font-weight: 600; }
        .payment-paid { background: #d1fae5; color: #065f46; }
        .highlight { background: #fef3c7; padding: 4px 8px; border-radius: 4px; font-weight: 600; }
    </style>
</head>

<body>
    <div class="header">
        <div class="brand">Lot Reservation Contract - Full Payment</div>
        <div class="subtitle">Memorial Park of San Sebastian, Liliw, Laguna</div>
    </div>

    <div class="section">
        <div class="section-intro">
            This Lot Reservation Contract ("Agreement") is entered into between:
        </div>
        <table class="kv">
            <tr>
                <td>Cemetery Authority</td>
                <td><strong>Memorial Park of San Sebastian</strong><br>
                    <span class="muted">Municipality of Liliw, Laguna, Philippines</span>
                </td>
            </tr>
            <tr>
                <td>Lot Owner / Purchaser</td>
                <td>
                    <strong>{{ $client->full_name ?? '-' }}</strong><br>
                    @if($client->address_line1)
                        {{ $client->address_line1 }}<br>
                    @endif
                    @if($client->barangay || $client->city)
                        {{ trim(($client->barangay ?? '').' '.($client->city ?? '')) }}<br>
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
                <td>Payment Number</td>
                <td>{{ $payment->payment_number ?? ('#'.$payment->id) }}</td>
            </tr>
            <tr>
                <td>Contract Date</td>
                <td>{{ $payment->payment_date?->format('Y-m-d') ?? now()->format('Y-m-d') }}</td>
            </tr>
            <tr>
                <td>Payment Method</td>
                <td>{{ ucfirst($payment->method ?? 'N/A') }}</td>
            </tr>
            @if($payment->reference_number)
            <tr>
                <td>Reference Number</td>
                <td>{{ $payment->reference_number }}</td>
            </tr>
            @endif
            <tr>
                <td>Payment Status</td>
                <td>
                    <span class="payment-status payment-paid">{{ strtoupper($payment->status_label ?? 'PAID') }}</span>
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Lot Details</div>
        <table class="kv">
            <tr>
                <td>Lot ID</td>
                <td><strong>{{ $lot->lot_number ?? '-' }}</strong></td>
            </tr>
            <tr>
                <td>Lot Name</td>
                <td>{{ $lot->name ?? '-' }}</td>
            </tr>
            <tr>
                <td>Section / Category</td>
                <td>{{ str_replace('_', ' ', ucfirst($lot->section ?? '-')) }}</td>
            </tr>
            @if($lot->block)
            <tr>
                <td>Block</td>
                <td>{{ $lot->block }}</td>
            </tr>
            @endif
        </table>
    </div>

    <div class="section">
        <div class="section-title">Payment Summary</div>
        <table class="kv">
            <tr>
                <td>Amount Paid</td>
                <td><strong class="highlight">&#8369;{{ number_format((float) $payment->amount, 2) }}</strong></td>
            </tr>
            <tr>
                <td>Payment Date</td>
                <td>{{ $payment->payment_date?->format('Y-m-d') ?? '-' }}</td>
            </tr>
            <tr>
                <td>Total Lot Value</td>
                <td>&#8369;{{ number_format((float) $payment->amount, 2) }}</td>
            </tr>
            <tr>
                <td>Remaining Balance</td>
                <td><strong>&#8369;0.00</strong> <span class="payment-status payment-paid">FULLY PAID</span></td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Terms & Conditions</div>
        
        <div class="terms">
            <div class="term-item">
                <span class="term-number">1.</span> <span class="term-title">Reservation and Ownership</span>
                <div class="term-content">Upon receipt of full payment, the Client is granted exclusive rights to the reserved lot identified as Lot No. {{ $lot->lot_number ?? '-' }}. This reservation is legally binding and recognized under the jurisdiction of the Municipality of Liliw, Laguna.</div>
            </div>

            <div class="term-item">
                <span class="term-number">2.</span> <span class="term-title">Payment Confirmation</span>
                <div class="term-content">The Client acknowledges that the payment made constitutes full settlement of the lot reservation. A receipt and this contract serve as proof of ownership rights. No further payments shall be required for the lot acquisition.</div>
            </div>

            <div class="term-item">
                <span class="term-number">3.</span> <span class="term-title">Interment Policy</span>
                <div class="term-content">
                    <div class="term-bullet">• The <strong>first interment</strong> in the reserved lot shall be subject to a fee of <strong>Fifteen Thousand Pesos (&#8369;15,000.00)</strong>.</div>
                    <div class="term-bullet">• The <strong>second interment</strong> shall require a fee of <strong>Twenty Thousand Pesos (&#8369;20,000.00)</strong>.</div>
                    <div class="term-bullet">• The <strong>third and final interment</strong> shall require a fee of <strong>Twenty-Five Thousand Pesos (&#8369;25,000.00)</strong>.</div>
                    <div class="term-bullet">• Interment fees are subject to change with prior notice.</div>
                </div>
            </div>

            <div class="term-item">
                <span class="term-number">4.</span> <span class="term-title">Number of Interments</span>
                <div class="term-content">
                    A maximum of three (3) deceased persons may be interred in the designated lot. No interment shall exceed this limit.
                </div>
            </div>

            <div class="term-item">
                <span class="term-number">5.</span> <span class="term-title">Interment Interval</span>
                <div class="term-content">
                    After the first interment, a minimum of ten (10) years must elapse before excavation and subsequent interment may occur. The Cemetery Authority reserves the right to refuse interment if this condition is not met.
                </div>
            </div>

            <div class="term-item">
                <span class="term-number">6.</span> <span class="term-title">Perpetual Maintenance</span>
                <div class="term-content">
                    The Cemetery Authority shall provide perpetual care and maintenance of the cemetery grounds, including grass cutting, clearing of debris, and general upkeep of common areas. This service is funded through maintenance fees collected from lot owners.
                </div>
            </div>

            <div class="term-item">
                <span class="term-number">7.</span> <span class="term-title">Compliance with Laws and Regulations</span>
                <div class="term-content">
                    All interments shall comply with:
                    <div class="term-sub">• Local health and sanitation regulations of Liliw, Laguna</div>
                    <div class="term-sub">• Municipal ordinances and cemetery rules</div>
                    <div class="term-sub">• National laws governing burial and exhumation (e.g., Republic Act 10364 - Ecological Solid Waste Management Act)</div>
                </div>
            </div>

            <div class="term-item">
                <span class="term-number">8.</span> <span class="term-title">Prohibited Actions</span>
                <div class="term-content">
                    The following are strictly prohibited within the cemetery grounds:
                    <div class="term-sub">• Construction of permanent structures without written approval</div>
                    <div class="term-sub">• Planting of trees or shrubs without authorization</div>
                    <div class="term-sub">• Installation of markers exceeding approved dimensions</div>
                    <div class="term-sub">• Disposal of waste materials</div>
                    <div class="term-sub">• Any activity that may disturb neighboring lots</div>
                </div>
            </div>

            <div class="term-item">
                <span class="term-number">9.</span> <span class="term-title">Transfer and Succession</span>
                <div class="term-content">
                    Rights under this Agreement may be transferred or inherited only with written approval of the Cemetery Authority. Unauthorized transfers are void and may result in forfeiture of lot rights.
                </div>
            </div>

            <div class="term-item">
                <span class="term-number">10.</span> <span class="term-title">Cancellation and Refund Policy</span>
                <div class="term-content">
                    In case of cancellation:
                    <div class="term-sub">• Requests must be submitted in writing to the Cemetery Authority.</div>
                    <div class="term-sub">• A processing fee of 10% of the total payment shall be deducted.</div>
                    <div class="term-sub">• Refunds shall be processed within thirty (30) business days.</div>
                    <div class="term-sub">• Refunds are not applicable once interment has occurred.</div>
                </div>
            </div>

            <div class="term-item">
                <span class="term-number">11.</span> <span class="term-title">Dispute Resolution</span>
                <div class="term-content">
                    Any dispute arising under this Agreement shall be resolved through mediation or arbitration, prior to court action, in accordance with applicable local arbitration rules.
                </div>
            </div>

            <div class="term-item">
                <span class="term-number">12.</span> <span class="term-title">Records and Transparency</span>
                <div class="term-content">
                    The Cemetery Authority shall maintain accurate records and audit logs for compliance and transparency. Lot owners may request copies of their records upon written request.
                </div>
            </div>

            <div class="term-item">
                <span class="term-number">13.</span> <span class="term-title">Entire Agreement</span>
                <div class="term-content">
                    This Agreement constitutes the entire understanding between the parties and supersedes all prior agreements regarding the lot. Any amendments must be in writing and signed by both parties.
                </div>
            </div>
        </div>
    </div>

    <div class="signatures">
        <div class="section-title">Signatures</div>
        
        <p style="margin-bottom: 16px;">Signed this {{ $payment->payment_date?->format('d') ?? now()->format('d') }} day of {{ $payment->payment_date?->format('F Y') ?? now()->format('F Y') }} at Memorial Park of San Sebastian, Liliw, Laguna.</p>
        
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

    <div style="margin-top: 24px; padding: 12px; background: #f3f4f6; border-radius: 8px; font-size: 10px; color: #6b7280;">
        <strong>Important Notice:</strong> This contract serves as your official proof of lot ownership. Please keep this document in a safe place. For inquiries, contact Memorial Park of San Sebastian, Liliw, Laguna.
    </div>
</body>
</html>