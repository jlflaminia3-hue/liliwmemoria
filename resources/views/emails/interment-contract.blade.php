<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Interment Contract</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 2px solid #2e7d32;
            margin-bottom: 20px;
        }
        .brand {
            font-size: 24px;
            font-weight: bold;
            color: #2e7d32;
        }
        .subtitle {
            color: #666;
            font-size: 14px;
        }
        .content {
            margin-bottom: 20px;
        }
        .info-box {
            background: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            border-bottom: 1px solid #eee;
        }
        .info-label {
            font-weight: 600;
            color: #666;
        }
        .footer {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #666;
            font-size: 12px;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #2e7d32;
            color: white !important;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="brand">Memorial Park of San Sebastian</div>
        <div class="subtitle">Interment Contract</div>
    </div>

    <div class="content">
        <p>Dear {{ $interment->client->full_name ?? 'Valued Client' }},</p>
        
        <p>Please find attached your Interment Contract for the burial of <strong>{{ $interment->full_name }}</strong>.</p>

        <div class="info-box">
            <div class="info-row">
                <span class="info-label">Interment Number:</span>
                <span>{{ $interment->interment_number ?? '-' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Deceased:</span>
                <span>{{ $interment->full_name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Burial Date:</span>
                <span>{{ $interment->burial_date?->format('F d, Y') ?? 'Pending' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Lot ID:</span>
                <span>{{ $interment->lot->lot_id ?? '-' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Total Fee:</span>
                <span>&#8369; {{ number_format((float) ($interment->interment_fee ?? 15000), 2) }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Payment Status:</span>
                <span>{{ $interment->payment_status_label }}</span>
            </div>
        </div>

        <p>Please keep this contract for your records. If you have any questions, please don't hesitate to contact us.</p>

        <p>Thank you for choosing Memorial Park of San Sebastian for your final resting place needs.</p>
    </div>

    <div class="footer">
        <p><strong>Memorial Park of San Sebastian</strong></p>
        <p>This is an automated message. Please do not reply directly to this email.</p>
    </div>
</body>
</html>
