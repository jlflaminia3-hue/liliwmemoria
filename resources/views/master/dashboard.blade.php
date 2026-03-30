@extends('admin.admin_master')
@section('admin')

<div class="content">
    <div class="container-xxl">
        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">Master Admin Dashboard</h4>
                <div class="text-muted mt-1">Audit log + full user control</div>
            </div>
            <div class="mt-3 mt-sm-0 d-flex gap-2 flex-wrap">
                <a href="{{ route('master.auditLogs.index') }}" class="btn btn-primary btn-sm">Audit Logs</a>
                <a href="{{ route('master.users.index') }}" class="btn btn-outline-secondary btn-sm">Users</a>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-6 col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <div class="text-muted">Total Users</div>
                        <div class="fs-22 fw-semibold text-black">{{ number_format($totalUsers) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <div class="text-muted">Admins</div>
                        <div class="fs-22 fw-semibold text-black">{{ number_format($totalAdmins) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div class="fw-semibold">Recent Activity</div>
                <a href="{{ route('master.auditLogs.index') }}" class="btn btn-sm btn-outline-secondary">View all</a>
            </div>
            <div class="card-body p-0">
                @include('master.partials.audit-table', ['auditLogs' => $auditLogs])
            </div>
        </div>
    </div>
</div>

@endsection

