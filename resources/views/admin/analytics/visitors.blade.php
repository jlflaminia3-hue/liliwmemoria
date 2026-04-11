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
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <div class="border rounded p-3 h-100">
                            <div class="text-muted">Total Logs</div>
                            <div class="fs-24 fw-semibold">{{ number_format($stats['total'] ?? 0) }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-3 h-100">
                            <div class="text-muted">Today</div>
                            <div class="fs-24 fw-semibold">{{ number_format($stats['today'] ?? 0) }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-3 h-100">
                            <div class="text-muted">This Week</div>
                            <div class="fs-24 fw-semibold">{{ number_format($stats['this_week'] ?? 0) }}</div>
                        </div>
                    </div>
                </div>

                <form method="GET" class="row g-2 align-items-end mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Search</label>
                        <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Visitor, deceased, lot...">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">From</label>
                        <input type="date" name="from" class="form-control" value="{{ request('from') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">To</label>
                        <input type="date" name="to" class="form-control" value="{{ request('to') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Per Page</label>
                        <select name="per_page" class="form-select">
                            @foreach ([10, 20, 50, 100] as $size)
                                <option value="{{ $size }}" {{ (int) request('per_page', 20) === $size ? 'selected' : '' }}>{{ $size }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 d-flex gap-2">
                        <button class="btn btn-primary btn-sm">
                            <i data-feather="search" class="me-1" style="height: 16px; width: 16px;"></i>
                            Filter
                        </button>
                        <a href="{{ route('admin.analytics.visitors') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date/Time</th>
                                <th>Visitor</th>
                                <th>Deceased</th>
                                <th>Lot</th>
                                <th>Contact</th>
                                <th>Purpose</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($logs as $log)
                                <tr>
                                    <td class="text-muted">{{ optional($log->visited_at)->format('Y-m-d h:i A') }}</td>
                                    <td class="fw-semibold">{{ $log->visitor_name }}</td>
                                    <td>
                                        @if ($log->deceased)
                                            {{ $log->deceased->last_name }}, {{ $log->deceased->first_name }}
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($log->deceased && $log->deceased->lot)
                                            {{ $log->deceased->lot->lot_id }}
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td class="text-muted">{{ $log->contact_number ?? '—' }}</td>
                                    <td class="text-muted">{{ $log->purpose ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">No visitor logs found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $logs->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
