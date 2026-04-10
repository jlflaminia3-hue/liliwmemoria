@extends('admin.admin_master')

@section('admin')
<link rel="stylesheet" href="{{ asset('backend/assets/libs/apexcharts/apexcharts.css') }}" />

<div class="content">
    <div class="container-xxl">
        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column gap-3">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">Clients Analytics</h4>
                <div class="text-muted mt-1">Growth, engagement, and retention insights.</div>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('admin.analytics.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i data-feather="arrow-left" class="me-1" style="height: 16px; width: 16px;"></i>
                    Back to Analytics
                </a>
                <a href="{{ route('admin.clients.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i data-feather="users" class="me-1" style="height: 16px; width: 16px;"></i>
                    Open Clients
                </a>
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
                        <div class="text-muted small text-uppercase fw-semibold">New This Month</div>
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

        <div class="row g-3 mb-3">
            <div class="col-xl-8">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="fw-semibold">Monthly Growth</div>
                            <div class="text-muted small">New clients per month (last 12 months)</div>
                        </div>
                        <div id="client_growth_chart" style="height: 300px;"></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="fw-semibold">Retention Rate</div>
                            <div class="text-muted small">% with 2+ interactions</div>
                        </div>
                        <div class="display-6 fw-bold">{{ $retentionRate }}%</div>
                        <div class="text-muted small">Based on reservations, communications, and maintenance records.</div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="fw-semibold">Top 10 Most Active Clients</div>
                            <div class="text-muted small">Last 6 months</div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Client</th>
                                        <th class="text-end">Activity Score</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($topActiveClients as $c)
                                        <tr>
                                            <td>
                                                <a href="{{ route('admin.clients.show', $c->id) }}">{{ $c->first_name }} {{ $c->last_name }}</a>
                                            </td>
                                            <td class="text-end">{{ $c->activity_score }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="text-muted text-center py-3">No activity data yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('backend/assets/libs/apexcharts/apexcharts.min.js') }}"></script>
<script>
    (function () {
        if (!window.ApexCharts) return;
        var growthEl = document.getElementById('client_growth_chart');
        if (!growthEl) return;

        new ApexCharts(growthEl, {
            chart: { type: 'line', height: 300, toolbar: { show: false } },
            stroke: { width: 3, curve: 'smooth' },
            series: [{ name: 'New Clients', data: @json($growthCounts) }],
            xaxis: { categories: @json($growthMonths) },
            colors: ['#3b82f6'],
            grid: { strokeDashArray: 4 },
            markers: { size: 3 },
        }).render();
    })();
</script>
@endsection

