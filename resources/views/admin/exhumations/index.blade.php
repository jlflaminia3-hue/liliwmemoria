@extends('admin.admin_master')

@section('admin')
@php
    $currentSearch = request('search', '');
    $currentStatus = request('workflow_status', '');
    $currentPerPage = (int) request('per_page', 20);
@endphp

<div class="content">
    <div class="container-xxl">
        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column gap-3">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">Exhumations</h4>
                <div class="text-muted mt-1">Manage exhumation transfers with permits, transport logs, and transfer certificates.</div>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('admin.interments.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i data-feather="map-pin" class="me-1" style="height: 16px; width: 16px;"></i>
                    Back to Interments
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.exhumations.index') }}" class="row g-3 align-items-end mb-4">
                    <div class="col-lg-5">
                        <label for="exhumation_search" class="form-label fw-semibold">Search</label>
                        <input id="exhumation_search" type="text" name="search" class="form-control" value="{{ $currentSearch }}" placeholder="Deceased, client, requester, destination cemetery">
                    </div>
                    <div class="col-md-4 col-lg-3">
                        <label for="exhumation_status_filter" class="form-label fw-semibold">Status</label>
                        <select id="exhumation_status_filter" name="workflow_status" class="form-select">
                            <option value="">All</option>
                            <option value="draft" @selected($currentStatus === 'draft')>Draft</option>
                            <option value="submitted" @selected($currentStatus === 'submitted')>Submitted</option>
                            <option value="approved" @selected($currentStatus === 'approved')>Approved</option>
                            <option value="scheduled" @selected($currentStatus === 'scheduled')>Scheduled</option>
                            <option value="completed" @selected($currentStatus === 'completed')>Completed</option>
                            <option value="archived" @selected($currentStatus === 'archived')>Archived</option>
                        </select>
                    </div>
                    <div class="col-md-4 col-lg-2">
                        <label for="exhumation_per_page" class="form-label fw-semibold">Rows</label>
                        <select id="exhumation_per_page" name="per_page" class="form-select">
                            <option value="10" @selected($currentPerPage === 10)>10</option>
                            <option value="20" @selected($currentPerPage === 20)>20</option>
                            <option value="50" @selected($currentPerPage === 50)>50</option>
                            <option value="100" @selected($currentPerPage === 100)>100</option>
                        </select>
                    </div>
                    <div class="col-md-4 col-lg-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100">Apply</button>
                        <a href="{{ route('admin.exhumations.index') }}" class="btn btn-light w-100">Reset</a>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Deceased</th>
                                <th>Lot</th>
                                <th>Status</th>
                                <th>Requested</th>
                                <th>Destination</th>
                                <th class="text-end" style="width: 70px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($exhumations as $ex)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $ex->deceased?->full_name ?? 'Unknown' }}</div>
                                        <div class="text-muted small">{{ $ex->deceased?->client?->full_name ?? 'No linked client' }}</div>
                                    </td>
                                    <td>
                                        @if ($ex->deceased?->lot)
                                            <div class="fw-semibold">{{ $ex->deceased->lot->lot_id }}</div>
                                            <div class="text-muted small">{{ $ex->deceased->lot->lot_category_label }}</div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">
                                            {{ ucwords(str_replace('_', ' ', (string) $ex->workflow_status)) }}
                                        </span>
                                    </td>
                                    <td>{{ $ex->requested_at?->format('Y-m-d') ?? '-' }}</td>
                                    <td>{{ $ex->destination_cemetery_name ?? '-' }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.exhumations.show', $ex) }}" class="btn btn-sm btn-light">Open</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">No exhumation records found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="pt-3 d-flex justify-content-end">
                    {{ $exhumations->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

