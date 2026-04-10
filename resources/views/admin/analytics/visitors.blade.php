@extends('admin.admin_master')

@section('admin')
<div class="content">
    <div class="container-xxl">
        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column gap-3">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">Visitors Analytics</h4>
                <div class="text-muted mt-1">Operational insights for visitor logs.</div>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('admin.analytics.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i data-feather="arrow-left" class="me-1" style="height: 16px; width: 16px;"></i>
                    Back to Analytics
                </a>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="fw-semibold mb-2">Not Available Yet</div>
                <div class="text-muted">
                    Visitor Logs are not currently implemented in this system (no visitor log records table/models found),
                    so there is no data to analyze yet. Once Visitor Logs are added, this page can show daily visits,
                    peak hours, purpose of visit, and lot/interment destinations.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

