@props([
    'logs' => collect(),
    'title' => 'Activity History',
])

@if($logs->isNotEmpty())
<div class="card border-0 shadow-sm">
    <div class="card-header bg-transparent py-2">
        <div class="d-flex align-items-center justify-content-between">
            <h6 class="mb-0 fw-semibold">{{ $title }}</h6>
            <span class="badge bg-light text-dark">{{ $logs->count() }} events</span>
        </div>
    </div>
    <div class="card-body p-0">
        <ul class="list-group list-group-flush">
            @foreach($logs->take(10) as $log)
            <li class="list-group-item">
                <div class="d-flex align-items-start gap-2">
                    <div class="flex-shrink-0 mt-1">
                        @php($iconClass = match($log->event) {
                            'created' => 'bg-success-subtle text-success',
                            'updated' => 'bg-warning-subtle text-warning',
                            'deleted' => 'bg-danger-subtle text-danger',
                            'archived' => 'bg-info-subtle text-info',
                            'deactivated' => 'bg-secondary-subtle text-secondary',
                            'cancelled' => 'bg-danger-subtle text-danger',
                            'restored' => 'bg-primary-subtle text-primary',
                            'login' => 'bg-primary-subtle text-primary',
                            'logout' => 'bg-secondary-subtle text-secondary',
                            default => 'bg-light text-secondary',
                        })
                        <div class="rounded-circle p-1 {!! $iconClass !!}">
                            @php($icon = match($log->event) {
                                'created' => 'plus',
                                'updated' => 'edit-2',
                                'deleted' => 'trash-2',
                                'archived' => 'archive',
                                'deactivated' => 'x-circle',
                                'cancelled' => 'slash',
                                'restored' => 'refresh-cw',
                                'login' => 'log-in',
                                'logout' => 'log-out',
                                default => 'activity',
                            })
                            <i data-feather="{{ $icon }}" style="height: 12px; width: 12px;"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <span class="fw-medium text-capitalize">{{ $log->event }}</span>
                                <span class="text-muted ms-1">{{ class_basename($log->auditable_type ?? 'Record') }} #{{ $log->auditable_id ?? '—' }}</span>
                            </div>
                            <small class="text-muted flex-shrink-0">{{ $log->created_at?->diffForHumans() }}</small>
                        </div>
                        <div class="text-muted small mt-1">
                            <i data-feather="user" class="me-1" style="height: 10px; width: 10px;"></i>
                            {{ $log->user?->name ?? 'System' }}
                            @if($log->url)
                                <span class="mx-1">·</span>
                                <i data-feather="external-link" class="me-1" style="height: 10px; width: 10px;"></i>
                                <span class="text-truncate" style="max-width: 150px;">{{ parse_url($log->url, PHP_URL_PATH) }}</span>
                            @endif
                        </div>
                        @if($log->new_values && is_array($log->new_values) && !empty(array_filter($log->new_values, fn($v) => !is_array($v))))
                            <div class="text-muted small mt-1">
                                Changes: {{ implode(', ', array_slice(array_keys($log->new_values), 0, 5)) }}
                                @if(count($log->new_values) > 5)
                                    <span class="text-secondary">+{{ count($log->new_values) - 5 }} more</span>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </li>
            @endforeach
        </ul>
    </div>
    @if($logs->count() > 10)
    <div class="card-footer bg-transparent text-center py-2">
        <a href="{{ route('master.auditLogs.index') }}" class="text-muted small">View all activity →</a>
    </div>
    @endif
</div>
@endif