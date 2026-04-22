@php($logs = $auditLogs)

<div class="table-cesponsive">
    <table class="table table-striped table-hover mb-0">
        <thead class="table-light">
            <tr>
                <th style="width: 180px;">Date/Time</th>
                <th style="width: 110px;">Event</th>
                <th>Model</th>
                <th style="width: 90px;">ID</th>
                <th>User</th>
                <th>Summary</th>
                <th style="width: 120px;">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($logs as $log)
                @php($openUrl = null)
                @if ($log->event !== 'deleted')
                    @php($openUrl = match($log->auditable_type) {
                        \App\Models\Lot::class => route('admin.lots.edit', $log->auditable_id),
                        \App\Models\Client::class => route('admin.clients.edit', $log->auditable_id),
                        \App\Models\PaymentPlan::class => route('admin.payments.show', $log->auditable_id),
                        \App\Models\User::class => route('master.users.edit', $log->auditable_id),
                        \App\Models\Deceased::class => route('admin.interments.show', $log->auditable_id),
                        \App\Models\IntermentPayment::class => route('admin.interment-payments.show', $log->auditable_id),
                        \App\Models\LotPayment::class => route('admin.lot-payments.show', $log->auditable_id),
                        default => null,
                    })
                @php($isLoginEvent = in_array($log->event, ['login', 'logout']))
                @endif
                <tr>
                    <td class="text-muted">{{ optional($log->created_at)->format('Y-m-d H:i:s') }}</td>
                    <td>
                        @php($badge = match($log->event) { 'created' => 'success', 'updated' => 'warning', 'deleted' => 'danger', 'login' => 'primary', 'logout' => 'secondary', default => 'secondary' })
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
                        @if ($isLoginEvent)
                            {{ ucfirst($log->event) }} - {{ $log->new_values['role'] ?? 'Unknown' }}
                        @elseif ($log->event === 'updated' && is_array($log->old_values) && is_array($log->new_values))
                            @php($changes = array_diff_assoc($log->new_values, $log->old_values))
                            @php($changeLabels = array_map(fn($key) => \Illuminate\Support\Str::title(str_replace('_', ' ', $key)), array_keys($changes)))
                            @if (!empty($changes))
                                Updated: {{ implode(', ', $changeLabels) }}
                            @else
                                Record updated
                            @endif
                        @elseif ($log->event === 'created')
                            New record created
                        @elseif ($log->event === 'deleted')
                            Record deleted
                        @else
                            —
                        @endif
                    </td>
                    <td>
                        @if ($openUrl)
                            <a class="btn btn-sm btn-outline-primary" href="{{ $openUrl }}">Open record</a>
                        @else
                            <span class="text-muted">—</span>
                        @endif
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

<div class="d-flex justify-content-between align-items-center p-3">
    <div class="text-muted small">
        Showing {{ $logs->firstItem() ?? 0 }} to {{ $logs->lastItem() ?? 0 }} of {{ $logs->total() }} results
    </div>
    <div class="d-flex align-items-center gap-2">
        @if ($logs->onFirstPage())
            <span class="btn btn-sm btn-outline-secondary disabled">Previous</span>
        @else
            <a class="btn btn-sm btn-outline-secondary" href="{{ $logs->previousPageUrl() }}">Previous</a>
        @endif
        
        <span class="text-muted">Page {{ $logs->currentPage() }} of {{ $logs->lastPage() }}</span>
        
        @if ($logs->hasMorePages())
            <a class="btn btn-sm btn-outline-secondary" href="{{ $logs->nextPageUrl() }}">Next</a>
        @else
            <span class="btn btn-sm btn-outline-secondary disabled">Next</span>
        @endif
    </div>
</div>
