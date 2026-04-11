<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Contract {{ $contract->contract_number ?? ('#'.$contract->id) }}</title>

    <style>
        @page { margin: 20px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111827; }
        .muted { color: #6b7280; }
        .header { margin-bottom: 10px; }
        .brand { font-size: 18px; font-weight: 700; }
        .section { margin-top: 10px; page-break-inside: avoid; }
        .section-title { font-size: 11px; font-weight: 700; margin-bottom: 4px; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 4px 6px; vertical-align: top; }
        .kv td { border: 1px solid #e5e7eb; }
        .kv td:first-child { width: 34%; background: #f9fafb; font-weight: 700; }
        .signatures { margin-top: 18px; page-break-inside: avoid; }
        .sig-line { border-top: 1px solid #111827; width: 46%; padding-top: 4px; }
        .row { width: 100%; }
        .row:after { content: ""; display: table; clear: both; }
        .col { float: left; width: 50%; }
        .clauses-wrap { width: 100%; margin-top: 4px; }
        .clauses-wrap:after { content: ""; display: table; clear: both; }
        .clauses-col { float: left; width: 49%; }
        .clauses-col.right { float: right; }
        ol.clauses { margin: 0 0 0 16px; padding: 0; }
        ol.clauses > li { margin: 0 0 4px 0; line-height: 1.25; }
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
        <div class="section-title">Terms & Clauses</div>
        <div class="muted">
            The following terms form part of this Contract and are subject to applicable laws and Memorial Park rules and regulations.
        </div>
        <div class="clauses-wrap">
            <div class="clauses-col">
                <ol class="clauses">
                    <li><strong>Ownership & Rights:</strong> The Purchaser is granted the right to use the designated lot(s) for interment purposes only. Any transfer, assignment, or resale requires prior written approval and completion of required documentation.</li>
                    <li><strong>Payment Terms:</strong> Payments shall be made per the agreed schedule. Past-due amounts may be subject to penalties/charges under the applicable policy. Continued non-payment may result in suspension or cancellation, subject to notice and law.</li>
                    <li><strong>Maintenance & Upkeep:</strong> The Memorial Park maintains common areas. The Purchaser shall ensure memorials/markers comply with park standards and is responsible for items not covered by park maintenance.</li>
                    <li><strong>Interment & Use Restrictions:</strong> Interments must comply with park rules, health and safety standards, and applicable ordinances. Only authorized persons may be interred unless approved in writing.</li>
                </ol>
            </div>
            <div class="clauses-col right">
                <ol class="clauses" start="5">
                    <li><strong>Termination & Default:</strong> Material breach, violation of park rules, or continued non-payment after notice may constitute default and may result in termination, without prejudice to other remedies under law.</li>
                    <li><strong>Transfer & Succession:</strong> Upon the Purchaser's death, rights may pass to lawful heirs/designated successors upon submission of sufficient proof and completion of record updates.</li>
                    <li><strong>Liability & Compliance:</strong> The Memorial Park is not liable for force majeure or third-party acts (including vandalism), except as required by law. The Purchaser shall comply with all rules and ordinances.</li>
                    <li><strong>Data Privacy Consent:</strong> The Client hereby consents to the collection, storage, and processing of their personal data, including but not limited to name, contact information, and property details, in accordance with the Data Privacy Act of 2012 and the Company's Privacy Policy. The Client understands that this data will be used solely for administrative, legal, and operational purposes related to cemetery management. The Client may withdraw consent at any time by providing written notice, subject to applicable laws and regulations.</li>
                    <li><strong>Governing Law:</strong> This Contract is governed by the laws of the Republic of the Philippines. Disputes shall be filed before the proper courts or forum with jurisdiction.</li>
                </ol>
            </div>
        </div>
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
