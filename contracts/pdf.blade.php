<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Contract {{ $contract->contract_number ?? ('#'.$contract->id) }}</title>

    <style>
        @page { margin: 20px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1f2937; line-height: 1.4; }
        .muted { color: #6b7280; }
        .header { margin-bottom: 20px; border-bottom: 2px solid #374151; padding-bottom: 12px; }
        .brand { font-size: 20px; font-weight: 700; color: #111827; }
        .subtitle { font-size: 10px; color: #6b7280; margin-top: 2px; }
        .section { margin-top: 16px; page-break-inside: avoid; }
        .section-title { font-size: 12px; font-weight: 700; margin-bottom: 8px; color: #111827; border-bottom: 1px solid #d1d5db; padding-bottom: 4px; }
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
        .signatures { margin-top: 24px; page-break-inside: avoid; }
        .sig-section { margin-bottom: 16px; }
        .sig-label { font-size: 10px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px; }
        .sig-line { border-bottom: 1px solid #111827; height: 20px; margin-bottom: 4px; }
        .sig-fields { display: inline-block; width: 100%; }
        .sig-field-row { display: inline-block; width: 48%; margin-right: 4%; }
        .sig-field-row:last-child { margin-right: 0; }
        .sig-field-label { font-size: 9px; color: #6b7280; margin-top: 4px; }
    </style>
</head>

<body>
    <div class="header">
        <div class="brand">LiliwMemoria</div>
        <div class="subtitle">Memorial Park of San Sebastian</div>
    </div>

    <div class="section">
        <div class="section-title">Contract Details</div>
        <table class="kv">
            <tr>
                <td>Contract Number</td>
                <td>{{ $contract->contract_number ?? ('#'.$contract->id) }}</td>
            </tr>
            <tr>
                <td>Created At</td>
                <td>{{ $contract->created_at?->format('Y-m-d H:i:s') ?? '-' }}</td>
            </tr>
            <tr>
                <td>Status</td>
                <td>{{ $contract->status ? ucwords(str_replace('_', ' ', $contract->status)) : '-' }}</td>
            </tr>
            <tr>
                <td>Contract Type</td>
                <td>{{ $contract->contract_type ? ucwords(str_replace('_', ' ', $contract->contract_type)) : '-' }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Client</div>
        <table class="kv">
            <tr>
                <td>Name</td>
                <td>{{ $client->full_name ?? '-' }}</td>
            </tr>
            <tr>
                <td>Email</td>
                <td>{{ $client->email ?? '-' }}</td>
            </tr>
            <tr>
                <td>Phone</td>
                <td>{{ $client->phone ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Lot</div>
        <table class="kv">
            <tr>
                <td>Lot ID</td>
                <td>{{ $lot?->lot_number ? ('Lot ID '.$lot->lot_number) : '-' }}</td>
            </tr>
            <tr>
                <td>Lot Category</td>
                <td>{{ $contract->lot_kind ? ucwords(str_replace('_', ' ', $contract->lot_kind)) : ($lot?->section ?? '-') }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Amounts & Dates</div>
        <table class="kv">
            <tr>
                <td>Contract Amount</td>
                <td>{{ is_null($contract->total_amount) ? '-' : ("\u{20B1}".number_format((float) $contract->total_amount, 2)) }}</td>
            </tr>
            <tr>
                <td>Effective</td>
                <td>{{ $contract->signed_at?->format('Y-m-d') ?? '-' }}</td>
            </tr>
            <tr>
                <td>Completion</td>
                <td>{{ $contract->due_date?->format('Y-m-d') ?? '-' }}</td>
            </tr>
            <tr>
                <td>Duration</td>
                <td>{{ $contract->contract_duration_months ? ($contract->contract_duration_months.' months') : '-' }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Terms & Conditions</div>
        
        <div class="terms">
            <div class="term-item">
                <span class="term-title">1. Ownership Rights</span>
                <div class="term-content">The Client acquires the exclusive right of use for the reserved lot, subject to full payment and compliance with cemetery rules. Title remains with the Company until full payment is completed.</div>
            </div>

            <div class="term-item">
                <span class="term-title">2. Payment Terms</span>
                <div class="term-content">
                    {{-- <div class="term-bullet"><strong>Schedule:</strong> ₱[Installment Amount] due every [Day] of the month.</div> --}}
                    <div class="term-bullet"><strong>Methods:</strong> Cash, bank transfer, or official receipt at the Company office.</div>
                    <div class="term-bullet"><strong>Late Payments:</strong> A penalty of ₱500 per month applies after a [7]-day grace period.</div>
                </div>
            </div>

            <div class="term-item">
                <span class="term-title">3. Maintenance</span>
                <div class="term-content">
                    The Management shall maintain common areas. Annual maintenance fee of ₱300 per year for landscaping and cleaning services.
                </div>
            </div>

            <div class="term-item">
                <span class="term-title">4. Interment Restrictions</span>
                <div class="term-content">No interment shall be made until the lot is fully paid and all required permits are secured.</div>
            </div>

            <div class="term-item">
                <span class="term-title">5. Termination</span>
                <div class="term-content">
                    <div class="term-bullet">Either party may terminate with [30]-day written notice.</div>
                    <div class="term-bullet">Payments made are non-refundable but may be transferred to another lot subject to Company approval.</div>
                    <div class="term-bullet">The Company may terminate for non-payment after [60] days of default.</div>
                </div>
            </div>

            <div class="term-item">
                <span class="term-title">6. Succession</span>
                <div class="term-content">
                    In case of death, heirs must present:
                    <div class="term-bullet">• Death certificate</div>
                    <div class="term-bullet">• Notarized affidavit of heirship or court order</div>
                    Rights shall then be transferred accordingly.
                </div>
            </div>

            <div class="term-item">
                <span class="term-title">7. Liability</span>
                <div class="term-content">The Company is not liable for damages caused by natural disasters, force majeure, or acts beyond its control.</div>
            </div>

            <div class="term-item">
                <span class="term-title">8. Dispute Resolution</span>
                <div class="term-content">Any dispute shall first undergo mediation under the Philippine Mediation Center. If unresolved, parties may resort to litigation under Philippine law.</div>
            </div>

            <div class="term-item">
                <span class="term-title">9. Governing Law</span>
                <div class="term-content">This contract is governed by the laws of the Republic of the Philippines.</div>
            </div>

            <div class="term-item">
                <span class="term-title">10. Data Privacy</span>
                <div class="term-content">The Client consents to the collection and processing of personal data in compliance with the Data Privacy Act of 2012.</div>
            </div>
        </div>
    </div>

    <div class="signatures">
        <div class="section-title">Signatures</div>
        
        <div class="sig-section">
            <div class="sig-label">Client</div>
            <div class="sig-field-row">
                <div class="sig-field-label">Signature / Date</div>
            </div>
            <div style="margin-top: 8px;">
                <div class="sig-field-row">
                    <div style="border-bottom: 1px solid #111827; height: 16px; margin-bottom: 2px;"></div>
                    <div class="sig-field-label">Printed Name</div>
                </div>
            </div>
        </div>

        <div class="sig-section">
            <div class="sig-label">Authorized Representative (Company)</div>
            <div class="sig-field-row">
                <div class="sig-field-label">Signature / Date</div>
            </div>
            <div style="margin-top: 8px;">
                <div class="sig-field-row">
                    <div style="border-bottom: 1px solid #111827; height: 16px; margin-bottom: 2px;"></div>
                    <div class="sig-field-label">Printed Name</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
