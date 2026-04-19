@extends('admin.admin_master')

@section('admin')

<div class="content">
    <div class="container-xxl">
        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column gap-3">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">Deceased Analytics</h4>
                <div class="text-muted mt-1">Monitor record status, burials over time, and compliance attention.</div>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('admin.analytics.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i data-feather="arrow-left" class="me-1" style="height: 16px; width: 16px;"></i>
                    Back to Analytics
                </a>
                <a href="{{ route('admin.interments.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i data-feather="map-pin" class="me-1" style="height: 16px; width: 16px;"></i>
                    Open Deceased
                </a>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6 col-xl-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase fw-semibold">All Records</div>
                        <div class="fs-3 fw-bold">{{ number_format($total) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase fw-semibold">Pending</div>
                        <div class="fs-3 fw-bold text-warning">{{ number_format($pending) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase fw-semibold">Confirmed</div>
                        <div class="fs-3 fw-bold text-success">{{ number_format($confirmed) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase fw-semibold">Needs Attention</div>
                        <div class="fs-3 fw-bold text-danger">{{ number_format($missingDocs) }}</div>
                        <div class="text-muted small">Missing required links/dates/docs</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-xl-5">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="fw-semibold">Status Distribution</div>
                            <div class="text-muted small">Pending / confirmed / exhumed</div>
                        </div>
                        <div id="interments_status_chart" style="height: 320px;"></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-7">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="fw-semibold">Monthly Burials</div>
                            <div class="text-muted small">Last 12 months</div>
                        </div>
                        <div id="interments_burials_chart" style="height: 320px;"></div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="fw-semibold">Recent Interment Records</div>
                            <div class="text-muted small">Top 10</div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Deceased</th>
                                        <th>Client</th>
                                        <th>Lot</th>
                                        <th class="text-end">Burial</th>
                                        <th class="text-end">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($recent as $record)
                                        <tr>
                                            <td>{{ $record->full_name }}</td>
                                            <td>{{ $record->client?->full_name ?? '—' }}</td>
                                            <td>{{ $record->lot?->lot_id ?? '—' }}</td>
                                            <td class="text-end">{{ $record->burial_date?->format('Y-m-d') ?? '—' }}</td>
                                            <td class="text-end">{{ ucfirst($record->status) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-muted text-center py-3">No records yet.</td>
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

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var statusEl = document.getElementById('interments_status_chart');
    if (statusEl) {
        new ApexCharts(statusEl, {
            chart: { type: 'donut', height: 320 },
            labels: @json($statusLabels),
            series: @json($statusSeries),
            colors: ['#f59e0b', '#10b981', '#94a3b8'],
            legend: { position: 'bottom' },
        }).render();
    }

    var burialsEl = document.getElementById('interments_burials_chart');
    if (burialsEl) {
        new ApexCharts(burialsEl, {
            chart: { type: 'line', height: 320, toolbar: { show: false } },
            stroke: { width: 3, curve: 'smooth' },
            series: [{ name: 'Burials', data: @json($burialsByMonth) }],
            xaxis: { categories: @json($months) },
            colors: ['#0ea5e9'],
            grid: { strokeDashArray: 4 },
            markers: { size: 3 },
        }).render();
    }
});
</script>
@endpush

