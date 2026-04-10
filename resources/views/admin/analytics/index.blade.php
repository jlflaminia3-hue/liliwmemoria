@extends('admin.admin_master')

@section('admin')
<div class="content">
    <div class="container-xxl">
        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column gap-3">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">Analytics</h4>
                <div class="text-muted mt-1">Explore performance and operational insights across clients, plots, and payments.</div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-lg-4">
                <a href="{{ route('admin.analytics.clients') }}" class="text-decoration-none">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-semibold">Clients Analytics</div>
                                    <div class="text-muted small mt-1">Growth, activity, retention</div>
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
                <a href="{{ route('admin.analytics.plots') }}" class="text-decoration-none">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-semibold">Plots Analytics</div>
                                    <div class="text-muted small mt-1">Availability, section mix</div>
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
                <a href="{{ route('admin.analytics.payments') }}" class="text-decoration-none">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-semibold">Payments Analytics</div>
                                    <div class="text-muted small mt-1">Collections, methods</div>
                                </div>
                                <div class="border rounded-2 p-2">
                                    <i data-feather="credit-card" style="height: 18px; width: 18px;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-lg-4">
                <a href="{{ route('admin.analytics.documents') }}" class="text-decoration-none">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-semibold">Documents Analytics</div>
                                    <div class="text-muted small mt-1">Compliance, uploads</div>
                                </div>
                                <div class="border rounded-2 p-2">
                                    <i data-feather="folder" style="height: 18px; width: 18px;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-lg-4">
                <a href="{{ route('admin.analytics.interments') }}" class="text-decoration-none">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-semibold">Interments Analytics</div>
                                    <div class="text-muted small mt-1">Status, burials trend</div>
                                </div>
                                <div class="border rounded-2 p-2">
                                    <i data-feather="map-pin" style="height: 18px; width: 18px;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-lg-4">
                <a href="{{ route('admin.analytics.visitors') }}" class="text-decoration-none">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-semibold">Visitors Analytics</div>
                                    <div class="text-muted small mt-1">Visitor logs insights</div>
                                </div>
                                <div class="border rounded-2 p-2">
                                    <i data-feather="clipboard" style="height: 18px; width: 18px;"></i>
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
