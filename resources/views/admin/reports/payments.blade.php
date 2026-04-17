@extends('admin.admin_master')

@section('admin')
@php($asOf = now()->toDateString())

<div class="content">
    <div class="container-xxl">
        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column gap-3 no-print">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">Payment Reports</h4>
                <div class="text-muted mt-1">Collections summary and overdue accounts for follow-up.</div>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary btn-sm no-print">
                    <i data-feather="arrow-left" class="me-1" style="height: 16px; width: 16px;"></i>
                    Back to Reports
                </a>
                <a href="{{ route('admin.analytics.payments') }}" class="btn btn-outline-secondary btn-sm no-print">
                    <i data-feather="bar-chart-2" class="me-1" style="height: 16px; width: 16px;"></i>
                    Payments Analytics
                </a>
                <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary btn-sm no-print">
                    <i data-feather="credit-card" class="me-1" style="height: 16px; width: 16px;"></i>
                    Open Payments
                </a>
                <button onclick="window.print()" class="btn btn-primary btn-sm no-print">
                    <i data-feather="printer" class="me-1" style="height: 16px; width: 16px;"></i>
                    Print Report
                </button>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.reports.payments') }}" class="row g-3 align-items-end no-print">
                    <div class="col-md-4 col-lg-3">
                        <label for="payments_report_from" class="form-label fw-semibold">From</label>
                        <input id="payments_report_from" type="date" class="form-control" name="from" value="{{ $from->toDateString() }}">
                    </div>
                    <div class="col-md-4 col-lg-3">
                        <label for="payments_report_to" class="form-label fw-semibold">To</label>
                        <input id="payments_report_to" type="date" class="form-control" name="to" value="{{ $to->toDateString() }}">
                    </div>
                    <div class="col-md-4 col-lg-3 d-flex gap-2">
                        <button class="btn btn-primary w-100" type="submit">Run Report</button>
                        <a class="btn btn-outline-secondary w-100" href="{{ route('admin.reports.payments') }}">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6 col-xl-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase fw-semibold">Total Collections</div>
                        <div class="fs-3 fw-bold">₱{{ number_format((float) $collections, 2) }}</div>
                        <div class="text-muted small">{{ $from->toDateString() }} to {{ $to->toDateString() }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase fw-semibold">Overdue Accounts</div>
                        <div class="fs-3 fw-bold text-danger">{{ number_format($overdueAccounts) }}</div>
                        <div class="text-muted small">As of {{ $asOf }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase fw-semibold">Outstanding Total</div>
                        <div class="fs-3 fw-bold">₱{{ number_format((float) $outstandingTotal, 2) }}</div>
                        <div class="text-muted small">As of {{ $asOf }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="fw-semibold">Overdue Accounts</div>
                    <div class="text-muted small no-print">Installments past due (including penalties)</div>
                </div>

                @if ($overdue->isEmpty())
                    <div class="alert alert-success mb-0">No overdue accounts found.</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Client</th>
                                    <th>Plan</th>
                                    <th class="text-end">Overdue Items</th>
                                    <th class="text-end">Overdue Amount</th>
                                    <th class="text-end no-print" style="width: 110px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($overdue as $row)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.clients.show', $row['plan']->client) }}">{{ $row['plan']->client->full_name }}</a>
                                        </td>
                                        <td>{{ $row['plan']->plan_number }}</td>
                                        <td class="text-end">{{ number_format($row['count']) }}</td>
                                        <td class="text-end">₱{{ number_format((float) $row['amount'], 2) }}</td>
                                        <td class="text-end no-print">
                                            <a class="btn btn-sm btn-light" href="{{ route('admin.payments.show', $row['plan']) }}">View</a>
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

<style>
@media print {
    .no-print { display: none !important; }
    .card { box-shadow: none !important; border: 1px solid #ddd !important; }
    body { font-size: 12px; }
}
</style>
@endsection
