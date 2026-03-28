@extends('admin.admin_master')

@section('admin')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h4 class="card-title mb-1">Payment Reports</h4>
                        <div class="text-muted">Collections and overdue accounts</div>
                    </div>
                </div>

                <form method="GET" action="{{ route('admin.reports.payments') }}" class="row g-2 align-items-end mb-3">
                    <div class="col-md-3">
                        <label class="form-label mb-1">From</label>
                        <input type="date" class="form-control" name="from" value="{{ $from->toDateString() }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label mb-1">To</label>
                        <input type="date" class="form-control" name="to" value="{{ $to->toDateString() }}">
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-primary" type="submit">Run</button>
                    </div>
                </form>

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <div class="p-3 border rounded">
                            <div class="text-muted small">Total Collections ({{ $from->toDateString() }} to {{ $to->toDateString() }})</div>
                            <div class="h5 mb-0">{{ number_format((float) $collections, 2) }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 border rounded">
                            <div class="text-muted small">Overdue Accounts (as of {{ now()->toDateString() }})</div>
                            <div class="h5 mb-0">{{ $overdueAccounts }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 border rounded">
                            <div class="text-muted small">Outstanding Total (as of {{ now()->toDateString() }})</div>
                            <div class="h5 mb-0">{{ number_format((float) $outstandingTotal, 2) }}</div>
                        </div>
                    </div>
                </div>

                <h5 class="mb-2">Overdue Accounts</h5>
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
                                    <th class="text-end" style="width: 100px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($overdue as $row)
                                    <tr>
                                        <td><a href="{{ route('admin.clients.show', $row['plan']->client) }}">{{ $row['plan']->client->full_name }}</a></td>
                                        <td>{{ $row['plan']->plan_number }}</td>
                                        <td class="text-end">{{ $row['count'] }}</td>
                                        <td class="text-end">{{ number_format((float) $row['amount'], 2) }}</td>
                                        <td class="text-end">
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
@endsection

