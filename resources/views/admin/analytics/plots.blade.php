@extends('admin.admin_master')

@section('admin')

<div class="content">
    <div class="container-xxl">
        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column gap-3">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">Plots Analytics</h4>
                <div class="text-muted mt-1">Availability and section distribution for cemetery plots.</div>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('admin.analytics.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i data-feather="arrow-left" class="me-1" style="height: 16px; width: 16px;"></i>
                    Back to Analytics
                </a>
                <a href="{{ route('admin.lots.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i data-feather="grid" class="me-1" style="height: 16px; width: 16px;"></i>
                    Open Lots
                </a>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6 col-xl-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase fw-semibold">Total Plots</div>
                        <div class="fs-3 fw-bold">{{ number_format($lotsTotal) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase fw-semibold">Available</div>
                        <div class="fs-3 fw-bold text-success">{{ number_format($lotsAvailable) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase fw-semibold">Reserved</div>
                        <div class="fs-3 fw-bold text-warning">{{ number_format($lotsReserved) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase fw-semibold">Occupied</div>
                        <div class="fs-3 fw-bold text-danger">{{ number_format($lotsOccupied) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-xl-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="fw-semibold">Status Mix</div>
                            <div class="text-muted small">Available vs reserved vs occupied</div>
                        </div>
                        <div id="plots_status_chart" style="height: 320px;"></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-8">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="fw-semibold">By Section</div>
                            <div class="text-muted small">Stacked distribution by section</div>
                        </div>
                        <div id="plots_section_chart" style="height: 320px;"></div>
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
    var statusEl = document.getElementById('plots_status_chart');
    if (statusEl) {
        new ApexCharts(statusEl, {
            chart: { type: 'donut', height: 320 },
            labels: @json($statusLabels),
            series: @json($statusSeries),
            colors: ['#10b981', '#f59e0b', '#ef4444'],
            legend: { position: 'bottom' },
        }).render();
    }

    var sectionEl = document.getElementById('plots_section_chart');
    if (sectionEl) {
        new ApexCharts(sectionEl, {
            chart: { type: 'bar', height: 320, stacked: true, toolbar: { show: false } },
            series: @json($sectionSeries),
            xaxis: { categories: @json($sectionCategories) },
            colors: ['#10b981', '#f59e0b', '#ef4444'],
            plotOptions: { bar: { horizontal: false, columnWidth: '55%' } },
            grid: { strokeDashArray: 4 },
            legend: { position: 'top' },
        }).render();
    }
});
</script>
@endpush

