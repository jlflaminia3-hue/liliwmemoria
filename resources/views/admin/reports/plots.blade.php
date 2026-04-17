@extends('admin.admin_master')

@section('admin')
<div class="content">
    <div class="container-xxl">
        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column gap-3 no-print">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">Plots Report</h4>
                <div class="text-muted mt-1">Inventory snapshot for lot statuses and section distribution.</div>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary btn-sm no-print">
                    <i data-feather="arrow-left" class="me-1" style="height: 16px; width: 16px;"></i>
                    Back to Reports
                </a>
                <a href="{{ route('admin.lots.index') }}" class="btn btn-outline-secondary btn-sm no-print">
                    <i data-feather="grid" class="me-1" style="height: 16px; width: 16px;"></i>
                    Open Lots
                </a>
                <a href="{{ route('admin.analytics.plots') }}" class="btn btn-outline-secondary btn-sm no-print">
                    <i data-feather="bar-chart-2" class="me-1" style="height: 16px; width: 16px;"></i>
                    Plots Analytics
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

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="fw-semibold">Plots by Section</div>
                    <div class="text-muted small no-print">Snapshot</div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Section</th>
                                <th class="text-end">Total</th>
                                <th class="text-end">Available</th>
                                <th class="text-end">Reserved</th>
                                <th class="text-end">Occupied</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sections as $row)
                                <tr>
                                    <td>{{ $row->section }}</td>
                                    <td class="text-end">{{ number_format($row->total) }}</td>
                                    <td class="text-end">{{ number_format($row->available) }}</td>
                                    <td class="text-end">{{ number_format($row->reserved) }}</td>
                                    <td class="text-end">{{ number_format($row->occupied) }}</td>
                                </tr>
                            @endforeach
                            @if ($sections->isEmpty())
                                <tr>
                                    <td colspan="5" class="text-muted text-center py-3">No lots found.</td>
                                </tr>
                            @endif
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
