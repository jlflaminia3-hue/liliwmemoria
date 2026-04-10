@extends('admin.admin_master')

@section('admin')
<div class="content">
    <div class="container-xxl">
        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column gap-3">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">Reports</h4>
                <div class="text-muted mt-1">Operational exports and audit-friendly summaries based on your records.</div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-lg-4">
                <a href="{{ route('admin.reports.clients') }}" class="text-decoration-none">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-semibold">Clients Report</div>
                                    <div class="text-muted small mt-1">New clients and contact coverage</div>
                                </div>
                                <div class="border rounded-2 p-2">
                                    <i data-feather="users" style="height: 18px; width: 18px;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-lg-4">
                <a href="{{ route('admin.reports.plots') }}" class="text-decoration-none">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-semibold">Plots Report</div>
                                    <div class="text-muted small mt-1">Status and section breakdown</div>
                                </div>
                                <div class="border rounded-2 p-2">
                                    <i data-feather="grid" style="height: 18px; width: 18px;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-lg-4">
                <a href="{{ route('admin.reports.payments') }}" class="text-decoration-none">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-semibold">Payments Report</div>
                                    <div class="text-muted small mt-1">Collections and overdue accounts</div>
                                </div>
                                <div class="border rounded-2 p-2">
                                    <i data-feather="credit-card" style="height: 18px; width: 18px;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
