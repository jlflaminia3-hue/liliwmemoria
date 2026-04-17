@extends('admin.admin_master')

@section('admin')

<div class="content">
    <div class="container-xxl">
        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column gap-3">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">Documents Analytics</h4>
                <div class="text-muted mt-1">Track uploads and compliance readiness across interments, contracts, and payments.</div>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('admin.analytics.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i data-feather="arrow-left" class="me-1" style="height: 16px; width: 16px;"></i>
                    Back to Analytics
                </a>
                <a href="{{ route('admin.interments.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i data-feather="map-pin" class="me-1" style="height: 16px; width: 16px;"></i>
                    Open Interments
                </a>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6 col-xl-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase fw-semibold">Interment Records</div>
                        <div class="fs-3 fw-bold">{{ number_format($intermentsTotal) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase fw-semibold">Compliance Ready</div>
                        <div class="fs-3 fw-bold text-success">{{ number_format($complianceReadyCount) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase fw-semibold">Missing Compliance</div>
                        <div class="fs-3 fw-bold text-danger">{{ number_format($missingComplianceCount) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase fw-semibold">Payment Receipts</div>
                        <div class="fs-3 fw-bold">{{ number_format($receiptCount) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-xl-5">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="fw-semibold">Documents by Type</div>
                            <div class="text-muted small">Uploaded counts</div>
                        </div>
                        <div id="documents_type_chart" style="height: 320px;"></div>
                        <div class="text-muted small mt-2">
                            This summarizes stored document paths (uploaded files). It does not validate file contents.
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-7">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="fw-semibold">Most Recent Missing Compliance</div>
                            <div class="text-muted small">Top 10</div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Record</th>
                                        <th>Client</th>
                                        <th>Lot</th>
                                        <th class="text-end">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($topMissing as $record)
                                        @php
                                            $lotLabel = $record->lot ? ($record->lot->lot_id ?? 'Lot') : '—';
                                        @endphp
                                        <tr>
                                            <td>{{ $record->full_name }}</td>
                                            <td>{{ $record->client?->full_name ?? '—' }}</td>
                                            <td>{{ $lotLabel }}</td>
                                            <td class="text-end">{{ ucfirst($record->status) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-muted text-center py-3">No missing compliance items found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="text-muted small mt-2">
                            For full compliance tracking, use the Interments page filters (e.g. “Needs Attention”).
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
    var el = document.getElementById('documents_type_chart');
    if (!el) return;

    new ApexCharts(el, {
        chart: { type: 'bar', height: 320, toolbar: { show: false } },
        series: [{
            name: 'Uploads',
            data: @json($documentTypeSeries),
        }],
        xaxis: { categories: @json($documentTypeLabels) },
        colors: ['#6366f1'],
        grid: { strokeDashArray: 4 },
        plotOptions: { bar: { horizontal: true, barHeight: '70%' } },
    }).render();
});
</script>
@endpush

