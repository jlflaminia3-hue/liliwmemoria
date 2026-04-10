<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Clients Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h1 { font-size: 18px; margin: 0 0 6px; }
        .meta { color: #555; margin-bottom: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 6px 8px; vertical-align: top; }
        th { background: #f4f6f8; text-align: left; }
        .muted { color: #666; }
    </style>
</head>
<body>
    <h1>Clients Report</h1>
    <div class="meta">Generated: {{ $generatedAt->format('Y-m-d H:i') }}</div>

    <table>
        <thead>
            <tr>
                <th style="width: 16%;">Name</th>
                <th style="width: 16%;">Email</th>
                <th style="width: 11%;">Phone</th>
                <th>Address</th>
                <th style="width: 9%;">Added</th>
                <th style="width: 9%;">Last</th>
                <th style="width: 7%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($clients as $client)
                @php
                    $address = collect([
                        $client->address_line1,
                        $client->address_line2,
                        $client->barangay,
                        $client->city,
                        $client->province,
                        $client->postal_code,
                        $client->country,
                    ])->filter()->implode(', ');

                    $lastActivity = collect([
                        $client->created_at,
                        $client->updated_at,
                        $client->last_communication_at ?? null,
                        $client->last_reservation_at ?? null,
                        $client->last_maintenance_at ?? null,
                    ])->filter()->max();

                    $inactiveCutoff = now()->subMonths(6);
                    $status = ($lastActivity && \Carbon\CarbonImmutable::parse($lastActivity)->lt(\Carbon\CarbonImmutable::parse($inactiveCutoff)))
                        ? 'inactive'
                        : 'active';
                @endphp
                <tr>
                    <td>{{ $client->full_name }}</td>
                    <td>{{ $client->email ?: '—' }}</td>
                    <td>{{ $client->phone ?: '—' }}</td>
                    <td class="muted">{{ $address ?: '—' }}</td>
                    <td>{{ optional($client->created_at)->format('Y-m-d') }}</td>
                    <td>{{ $lastActivity ? \Carbon\CarbonImmutable::parse($lastActivity)->format('Y-m-d') : '—' }}</td>
                    <td>{{ ucfirst($status) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
