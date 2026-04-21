@extends('admin.admin_master')

@section('admin')
<div class="content">
    <div class="container-xxl">
        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column gap-3 no-print">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">Clients Report</h4>
                <div class="text-muted mt-1">Client portfolio snapshot for engagement and retention follow-up.</div>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary btn-sm no-print">
                    <i data-feather="arrow-left" class="me-1" style="height: 16px; width: 16px;"></i>
                    Back to Reports
                </a>
                <a href="{{ route('admin.clients.index') }}" class="btn btn-outline-secondary btn-sm no-print">
                    <i data-feather="users" class="me-1" style="height: 16px; width: 16px;"></i>
                    Open Clients
                </a>
                <button onclick="window.print()" class="btn btn-primary btn-sm no-print">
                    <i data-feather="printer" class="me-1" style="height: 16px; width: 16px;"></i>
                    Print Report
                </button>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6 col-xl-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase fw-semibold">Total Clients</div>
                        <div class="fs-3 fw-bold">{{ number_format($stats['total'] ?? 0) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase fw-semibold">Active Clients</div>
                        <div class="fs-3 fw-bold text-primary">{{ number_format($stats['active'] ?? 0) }}</div>
                        <div class="text-muted small">Activity in last 30 days</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase fw-semibold">New Clients This Month</div>
                        <div class="fs-3 fw-bold text-success">{{ number_format($stats['new_this_month'] ?? 0) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase fw-semibold">Inactive Clients</div>
                        <div class="fs-3 fw-bold text-danger">{{ number_format($stats['inactive'] ?? 0) }}</div>
                        <div class="text-muted small">No activity in 6 months</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2 no-print">
                    <div class="fw-semibold">Latest Clients</div>
                    <div class="text-muted small">Top 50</div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th class="text-end">Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($rows as $client)
                                <tr>
                                    <td><a href="{{ route('admin.clients.show', $client) }}">{{ $client->full_name }}</a></td>
                                    <td>{{ $client->email ?: '—' }}</td>
                                    <td>{{ $client->phone ?: '—' }}</td>
                                    <td class="text-end">{{ $client->created_at?->format('Y-m-d') ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-muted text-center py-3">No clients found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .no-print { display: none !important; }
    .card { box-shadow: none !important; border: 1px solid #ddd !important; }
    body { font-size: 12px; }
}
</style>
@endsection
