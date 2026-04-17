@extends('admin.admin_master')

@section('admin')
<div class="content">
    <div class="container-xxl">
        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column gap-3">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">Payments Analytics</h4>
                <div class="text-muted mt-1">Overview of all payment collections including installment plans and interment services.</div>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('admin.analytics.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i data-feather="arrow-left" class="me-1" style="height: 16px; width: 16px;"></i>
                    Back to Analytics
                </a>
                <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i data-feather="credit-card" class="me-1" style="height: 16px; width: 16px;"></i>
                    Installment Payments
                </a>
                <a href="{{ route('admin.interment-payments.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i data-feather="credit-card" class="me-1" style="height: 16px; width: 16px;"></i>
                    Interment Payments
                </a>
            </div>
        </div>

        <h5 class="mt-4 mb-3 text-primary">Installment Payments</h5>
        <div class="row g-3 mb-4">
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
                        <div class="text-muted small">{{ number_format($plansActive) }} active / {{ number_format($plansCanceled) }} canceled</div>
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

        <h5 class="mt-4 mb-3 text-primary">Interment Payments</h5>
        <div class="row g-3 mb-4">
            <div class="col-md-6 col-xl-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase fw-semibold">Total Collectible</div>
                        <div class="fs-3 fw-bold">₱{{ number_format($totalIntermentCollectible, 2) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase fw-semibold">Total Collected</div>
                        <div class="fs-3 fw-bold text-success">₱{{ number_format($intermentCollectionsTotal, 2) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase fw-semibold">Collected This Month</div>
                        <div class="fs-3 fw-bold text-success">₱{{ number_format($intermentCollectionsThisMonth, 2) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase fw-semibold">Interment Payments</div>
                        <div class="fs-3 fw-bold">{{ number_format($intermentPaymentsTotal) }}</div>
                        <div class="text-muted small">{{ $intermentsUnpaid }} unpaid / {{ $intermentsPartial }} partial / {{ $intermentsFullyPaid }} paid</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-xl-6">
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
            <div class="col-xl-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="fw-semibold">Interment Monthly Collections</div>
                            <div class="text-muted small">Last 12 months</div>
                        </div>
                        <div id="interment_collections_chart" style="height: 320px;"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-xl-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="fw-semibold">Payment Methods (Installment)</div>
                            <div class="text-muted small">Top methods</div>
                        </div>
                        <div id="payments_methods_chart" style="height: 320px;"></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="fw-semibold">Payment Methods (Interment)</div>
                            <div class="text-muted small">Top methods</div>
                        </div>
                        <div id="interment_methods_chart" style="height: 320px;"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-xl-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header">
                        <h5 class="mb-0">Recent Interment Payments</h5>
                    </div>
                    <div class="card-body p-0">
                        @if ($recentIntermentPayments->isEmpty())
                            <div class="p-3 text-muted">No interment payments recorded.</div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-sm mb-0">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Interment</th>
                                            <th class="text-end">Amount</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($recentIntermentPayments as $payment)
                                            <tr>
                                                <td>{{ optional($payment->payment_date)->format('Y-m-d') }}</td>
                                                <td>
                                                    <div class="fw-semibold">{{ $payment->deceased->full_name ?? 'N/A' }}</div>
                                                </td>
                                                <td class="text-end">₱{{ number_format((float) $payment->amount, 2) }}</td>
                                                <td class="text-end">
                                                    <a href="{{ route('admin.interment-payments.show', $payment->deceased_id) }}" class="btn btn-sm btn-light">View</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
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

    var iColEl = document.getElementById('interment_collections_chart');
    if (iColEl) {
        new ApexCharts(iColEl, {
            chart: { type: 'bar', height: 320, toolbar: { show: false } },
            stroke: { width: 3, curve: 'smooth' },
            series: [{ name: 'Collections', data: @json($intermentCollectionsByMonth) }],
            xaxis: { categories: @json($months) },
            colors: ['#3b82f6'],
            fill: { opacity: 0.8 },
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

    var iMEl = document.getElementById('interment_methods_chart');
    if (iMEl) {
        new ApexCharts(iMEl, {
            chart: { type: 'donut', height: 320 },
            labels: @json($intermentMethodLabels),
            series: @json($intermentMethodSeries),
            legend: { position: 'bottom' },
        }).render();
    }
});
</script>
@endpush
