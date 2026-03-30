@php($logs = $auditLogs)

<div class="table-responsive">
    <table class="table table-striped table-hover mb-0">
        <thead class="table-light">
            <tr>
                <th style="width: 180px;">Date/Time</th>
                <th style="width: 110px;">Event</th>
                <th>Model</th>
                <th style="width: 90px;">ID</th>
                <th>User</th>
                <th>Summary</th>
                <th style="width: 120px;">Details</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($logs as $log)
                @php($collapseId = 'audit_' . $log->id)
                @php($openUrl = null)
                @if ($log->event !== 'deleted')
                    @php($openUrl = match($log->auditable_type) {
                        \App\Models\Lot::class => route('admin.lots.edit', $log->auditable_id),
                        \App\Models\Client::class => route('admin.clients.edit', $log->auditable_id),
                        \App\Models\PaymentPlan::class => route('admin.payments.show', $log->auditable_id),
                        \App\Models\User::class => route('master.users.edit', $log->auditable_id),
                        default => null,
                    })
                @endif
                <tr>
                    <td class="text-muted">{{ optional($log->created_at)->format('Y-m-d H:i:s') }}</td>
                    <td>
                        @php($badge = match($log->event) { 'created' => 'success', 'updated' => 'warning', 'deleted' => 'danger', default => 'secondary' })
                        <span class="badge bg-{{ $badge }}">{{ strtoupper($log->event) }}</span>
                    </td>
                    <td class="text-muted">{{ \Illuminate\Support\Str::afterLast($log->auditable_type, '\\') }}</td>
                    <td class="text-muted">{{ $log->auditable_id }}</td>
                    <td>
                        @if ($log->user)
                            <div class="fw-semibold">{{ $log->user->name }}</div>
                            <div class="text-muted small">{{ $log->user->email }}</div>
                        @else
                            <span class="text-muted">System</span>
                        @endif
                    </td>
                    <td class="text-muted">
                        @if ($log->event === 'updated' && is_array($log->new_values))
                            Changed: {{ implode(', ', array_keys($log->new_values)) }}
                        @elseif ($log->event === 'created' && is_array($log->new_values))
                            Fields: {{ implode(', ', array_slice(array_keys($log->new_values), 0, 8)) }}@if (count($log->new_values) > 8)…@endif
                        @elseif ($log->event === 'deleted' && is_array($log->old_values))
                            Deleted record snapshot captured
                        @endif
                    </td>
                    <td>
                        <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}">
                            View
                        </button>
                    </td>
                </tr>
                <tr class="collapse" id="{{ $collapseId }}">
                    <td colspan="7" class="bg-light">
                        <div class="row g-3">
                            <div class="col-12 d-flex justify-content-between align-items-center">
                                <div class="text-muted small">Log #{{ $log->id }}</div>
                                @if ($openUrl)
                                    <a class="btn btn-sm btn-primary" href="{{ $openUrl }}">Open record</a>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <div class="fw-semibold mb-2">Old</div>
                                <pre class="small mb-0">{{ json_encode($log->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                            </div>
                            <div class="col-md-6">
                                <div class="fw-semibold mb-2">New</div>
                                <pre class="small mb-0">{{ json_encode($log->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                            </div>
                            <div class="col-12">
                                <div class="text-muted small">
                                    URL: {{ $log->url ?? '—' }} |
                                    IP: {{ $log->ip_address ?? '—' }}
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-muted p-4">No audit logs found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="p-3">
    {{ $logs->links() }}
</div>
