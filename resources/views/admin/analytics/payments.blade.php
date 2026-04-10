@extends('admin.admin_master')

@section('admin')
<link rel="stylesheet" href="{{ asset('backend/assets/libs/apexcharts/apexcharts.css') }}" />

<div class="content">
    <div class="container-xxl">
        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column gap-3">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">Payments Analytics</h4>
                <div class="text-muted mt-1">Collections trends and payment plan overview.</div>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('admin.analytics.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i data-feather="arrow-left" class="me-1" style="height: 16px; width: 16px;"></i>
                    Back to Analytics
                </a>
                <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i data-feather="credit-card" class="me-1" style="height: 16px; width: 16px;"></i>
                    Open Payments
                </a>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6 col-xl-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase fw-semibold">Total Collections</div>
                        <div class="fs-3 fw-bold">₱{{ number_format($collectionsTotal, 2) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase fw-semibold">Collections This Month</div>
                        <div class="fs-3 fw-bold text-success">₱{{ number_format($collectionsThisMonth, 2) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase fw-semibold">Payment Plans</div>
                        <div class="fs-3 fw-bold">{{ number_format($plansTotal) }}</div>
                        <div class="text-muted small">{{ number_format($plansActive) }} active • {{ number_format($plansCanceled) }} canceled</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase fw-semibold">Transactions</div>
                        <div class="fs-3 fw-bold">{{ number_format($transactionsTotal) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-xl-7">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="fw-semibold">Monthly Collections</div>
                            <div class="text-muted small">Last 12 months</div>
                        </div>
                        <div id="payments_collections_chart" style="height: 320px;"></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-5">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="fw-semibold">Payment Methods</div>
                            <div class="text-muted small">Top methods</div>
                        </div>
                        <div id="payments_methods_chart" style="height: 320px;"></div>
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

        var colEl = document.getElementById('payments_collections_chart');
        if (colEl) {
            new ApexCharts(colEl, {
                chart: { type: 'area', height: 320, toolbar: { show: false } },
                stroke: { width: 3, curve: 'smooth' },
                series: [{ name: 'Collections', data: @json($collectionsByMonth) }],
                xaxis: { categories: @json($months) },
                colors: ['#10b981'],
                fill: { opacity: 0.2 },
                grid: { strokeDashArray: 4 },
                yaxis: { labels: { formatter: function (v) { return '₱' + (v || 0).toFixed(0); } } },
                tooltip: { y: { formatter: function (v) { return '₱' + (v || 0).toFixed(2); } } },
            }).render();
        }

        var mEl = document.getElementById('payments_methods_chart');
        if (mEl) {
            new ApexCharts(mEl, {
                chart: { type: 'donut', height: 320 },
                labels: @json($methodLabels),
                series: @json($methodSeries),
                legend: { position: 'bottom' },
            }).render();
        }
    })();
</script>
@endsection

