<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Transfer Certificate</title>
    <style>
        body{font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color:#0f172a;}
        .page{padding:18px 22px}
        .title{font-size:20px;font-weight:700;text-align:center;margin:6px 0 2px}
        .subtitle{text-align:center;color:#334155;margin:0 0 18px}
        .box{border:1px solid #cbd5e1;border-radius:10px;padding:12px 14px;margin-bottom:12px}
        .row{display:flex;gap:14px}
        .col{flex:1}
        .label{font-size:10px;text-transform:uppercase;letter-spacing:.06em;color:#475569;margin:0 0 4px}
        .value{font-size:12px;margin:0 0 10px}
        .muted{color:#64748b}
        .hr{height:1px;background:#e2e8f0;margin:12px 0}
        .sign-row{display:flex;gap:18px;margin-top:22px}
        .sign{flex:1;border-top:1px solid #94a3b8;padding-top:6px;text-align:center;color:#334155}
        .small{font-size:10px}
    </style>
</head>
<body>
<div class="page">
    <div class="title">Transfer Certificate</div>
    <div class="subtitle">Exhumation and Transfer Out of Remains</div>

    <div class="box">
        <div class="row">
            <div class="col">
                <div class="label">Deceased</div>
                <div class="value">{{ $deceased?->full_name ?? 'Unknown' }}</div>
            </div>
            <div class="col">
                <div class="label">Interment Lot</div>
                <div class="value">{{ $lot?->lot_id ?? '-' }}</div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="label">Interment Date</div>
                <div class="value">{{ $deceased?->burial_date?->format('Y-m-d') ?? '-' }}</div>
            </div>
            <div class="col">
                <div class="label">Client / Family Contact</div>
                <div class="value">
                    {{ $client?->full_name ?? '-' }}
                    @if ($client?->phone)
                        <span class="muted">({{ $client->phone }})</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="hr"></div>
        <div class="row">
            <div class="col">
                <div class="label">Transfer Status</div>
                <div class="value">{{ ucwords(str_replace('_', ' ', (string) $exhumation->workflow_status)) }}</div>
            </div>
            <div class="col">
                <div class="label">Generated At</div>
                <div class="value">{{ $exhumation->transfer_certificate_generated_at?->format('Y-m-d H:i') ?? '-' }}</div>
            </div>
        </div>
    </div>

    <div class="box">
        <div class="label">Destination Cemetery</div>
        <div class="value">{{ $exhumation->destination_cemetery_name ?? '-' }}</div>
        <div class="row">
            <div class="col">
                <div class="label">Address</div>
                <div class="value">{{ $exhumation->destination_address ?? '-' }}</div>
            </div>
            <div class="col">
                <div class="label">City / Province</div>
                <div class="value">
                    {{ $exhumation->destination_city ?? '-' }}
                    @if ($exhumation->destination_province)
                        , {{ $exhumation->destination_province }}
                    @endif
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="label">Receiving Contact</div>
                <div class="value">{{ $exhumation->destination_contact_person ?? '-' }}</div>
            </div>
            <div class="col">
                <div class="label">Phone / Email</div>
                <div class="value">
                    {{ $exhumation->destination_contact_phone ?? '-' }}
                    @if ($exhumation->destination_contact_email)
                        <span class="muted">({{ $exhumation->destination_contact_email }})</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="box">
        <div class="label">Transport Log</div>
        <div class="value">
            <span class="muted">Company:</span> {{ $exhumation->transport_company ?? '-' }}<br>
            <span class="muted">Vehicle:</span> {{ $exhumation->transport_vehicle_plate ?? '-' }}<br>
            <span class="muted">Driver:</span> {{ $exhumation->transport_driver_name ?? '-' }}<br>
            <span class="muted">Logged At:</span> {{ $exhumation->transport_logged_at?->format('Y-m-d H:i') ?? '-' }}
        </div>
        <div class="value">{{ $exhumation->transport_log ?? 'No transport notes.' }}</div>
    </div>

    <div class="sign-row">
        <div class="sign">
            Authorized Cemetery Representative
            <div class="small muted">Name and Signature</div>
        </div>
        <div class="sign">
            Family / Requesting Party
            <div class="small muted">Name and Signature</div>
        </div>
    </div>

    <div class="small muted" style="margin-top:10px;">
        Generated on {{ now()->format('Y-m-d H:i') }}. This document is for record-keeping and transfer coordination.
    </div>
</div>
</body>
</html>

