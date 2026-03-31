<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Contract {{ $contract->contract_number ?? ('#'.$contract->id) }}</title>

    <style>
        @page { margin: 24px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111827; }
        .muted { color: #6b7280; }
        .header { margin-bottom: 16px; }
        .brand { font-size: 18px; font-weight: 700; }
        .section { margin-top: 14px; }
        .section-title { font-size: 12px; font-weight: 700; margin-bottom: 6px; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 6px 8px; vertical-align: top; }
        .kv td { border: 1px solid #e5e7eb; }
        .kv td:first-child { width: 34%; background: #f9fafb; font-weight: 700; }
        .signatures { margin-top: 28px; }
        .sig-line { border-top: 1px solid #111827; width: 46%; padding-top: 4px; }
        .row { width: 100%; }
        .row:after { content: ""; display: table; clear: both; }
        .col { float: left; width: 50%; }
        ol.clauses { margin: 6px 0 0 18px; padding: 0; }
        ol.clauses > li { margin: 0 0 6px 0; line-height: 1.35; }
    </style>
</head>

<body>
    <div class="header">
        <div class="brand">
            LiliwMemoria
        </div>
        <div class="muted">Memorial Park of San Sebastian</div>
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
                <td>{{ is_null($contract->total_amount) ? '-' : ('₱'.number_format((float) $contract->total_amount, 2)) }}</td>
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
        <div class="section-title">Terms & Clauses</div>
        <div class="muted">
            The following terms form part of this Contract and are binding upon the Purchaser and the Memorial Park, subject to applicable laws and duly issued park rules and regulations.
        </div>
        <ol class="clauses">
            <li><strong>Ownership & Rights:</strong> The Purchaser is granted the right to use the designated lot(s) for interment purposes only. Any transfer, assignment, or resale of rights requires prior written approval of the Memorial Park and completion of the required documentation and fees, if any.</li>
            <li><strong>Payment Terms:</strong> The Purchaser shall pay the contract price in accordance with the agreed schedule. Past-due amounts may be subject to reasonable penalties, charges, or interest as provided in the applicable payment policy. Failure to pay may result in suspension of privileges or cancellation, subject to notice and applicable laws.</li>
            <li><strong>Maintenance & Upkeep:</strong> The Memorial Park shall maintain common areas and grounds within its control. The Purchaser shall ensure that memorials, markers, and improvements comply with park standards and shall be responsible for the care of items not covered by the Memorial Park's maintenance obligations.</li>
            <li><strong>Interment & Use Restrictions:</strong> All interments shall comply with Memorial Park rules, health and safety standards, and applicable local ordinances. Only authorized persons may be interred in the lot(s), unless otherwise approved in writing by the Memorial Park.</li>
            <li><strong>Termination & Default:</strong> Material breach of these terms, violation of park rules, or continued non-payment after notice may constitute default and may result in termination of this Contract, without prejudice to other remedies available under law and the terms of this Contract.</li>
            <li><strong>Transfer & Succession:</strong> In the event of the Purchaser's death, rights may pass to lawful heirs or designated successors upon submission of sufficient proof and completion of record updates with the Memorial Park.</li>
            <li><strong>Liability & Compliance:</strong> The Memorial Park shall not be liable for loss or damage arising from force majeure events (including natural disasters) or acts of third parties (including vandalism), except as may be required by law. The Purchaser agrees to comply with all Memorial Park rules, government regulations, and applicable ordinances.</li>
            <li><strong>Governing Law & Dispute Resolution:</strong> This Contract shall be governed by the laws of the Republic of the Philippines. Any dispute shall be brought before the proper courts or appropriate forum having jurisdiction.</li>
        </ol>
    </div>

    <div class="signatures">
        <div class="row">
            <div class="col">
                <div class="sig-line">Client Signature</div>
            </div>
            <div class="col" style="float:right;">
                <div class="sig-line">Authorized Signature</div>
            </div>
        </div>
    </div>
</body>
</html>
