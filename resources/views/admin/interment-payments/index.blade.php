@extends('admin.admin_master')

@section('admin')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="card-title mb-1">Interment Payments</h4>
                        <div class="text-muted">Payment tracking for interment services</div>
                    </div>
                </div>

                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <div class="row g-3 mb-4">
                    <div class="col-md-2">
                        <div class="p-3 border rounded text-center">
                            <div class="text-muted small">Total Interments</div>
                            <div class="h5 mb-0">{{ $stats['total'] }}</div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="p-3 border rounded text-center">
                            <div class="text-muted small">Unpaid</div>
                            <div class="h5 mb-0 text-danger">{{ $stats['unpaid'] }}</div>
                            <div class="text-muted small">₱{{ number_format($stats['total_unpaid_amount'], 2) }}</div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="p-3 border rounded text-center">
                            <div class="text-muted small">Partial</div>
                            <div class="h5 mb-0 text-warning">{{ $stats['partial'] }}</div>
                            <div class="text-muted small">₱{{ number_format($stats['total_partial_amount'], 2) }}</div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="p-3 border rounded text-center">
                            <div class="text-muted small">Fully Paid</div>
                            <div class="h5 mb-0 text-success">{{ $stats['fully_paid'] }}</div>
                        </div>
                    </div>
                </div>

                <form method="GET" action="{{ route('admin.interment-payments.index') }}" class="row g-3 mb-4">
                    <div class="col-md-3">
                        <select name="status" class="form-select" onchange="this.form.submit()">
                            <option value="all" @selected($status === 'all')>All Statuses</option>
                            <option value="unpaid" @selected($status === 'unpaid')>Unpaid</option>
                            <option value="partial" @selected($status === 'partial')>Partial</option>
                            <option value="fully_paid" @selected($status === 'fully_paid')>Fully Paid</option>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <input type="text" name="search" class="form-control" placeholder="Search name, interment #, client, lot..." value="{{ $search }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-outline-secondary w-100">Search</button>
                    </div>
                    <div class="col-md-2">
                        <a class="btn btn-outline-secondary w-100" href="{{ route('admin.interment-payments.index') }}">Reset</a>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Interment</th>
                                <th>Client</th>
                                <th>Lot</th>
                                <th>Burial Date</th>
                                <th class="text-end">Total Fee</th>
                                <th class="text-end">Paid</th>
                                <th class="text-end">Balance</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($interments as $interment)
                                @php
                                    $fee = (float) ($interment->interment_fee ?? 15000);
                                    $paid = $interment->total_paid;
                                    $balance = max(0, $fee - $paid);
                                    $progress = $fee > 0 ? min(100, round(($paid / $fee) * 100)) : 0;

                                    $statusClass = match($interment->payment_status) {
                                        'fully_paid' => 'success',
                                        'partial' => 'warning',
                                        default => 'danger'
                                    };
                                @endphp
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $interment->full_name }}</div>
                                        <div class="text-muted small">{{ $interment->interment_number ?? 'INT-' . $interment->id }}</div>
                                    </td>
                                    <td>
                                        @if ($interment->client)
                                            <a href="{{ route('admin.clients.show', $interment->client) }}">{{ $interment->client->full_name }}</a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if ($interment->lot)
                                            {{ $interment->lot->lot_number }} ({{ $interment->lot->section }})
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $interment->burial_date?->format('Y-m-d') ?? '-' }}</td>
                                    <td class="text-end">₱{{ number_format($fee, 2) }}</td>
                                    <td class="text-end text-success">₱{{ number_format($paid, 2) }}</td>
                                    <td class="text-end {{ $balance > 0 ? 'text-danger' : '' }}">₱{{ number_format($balance, 2) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $statusClass }}">{{ $interment->payment_status_label }}</span>
                                        @if ($progress > 0 && $progress < 100)
                                            <div class="progress mt-1" style="height: 4px; width: 80px;">
                                                <div class="progress-bar bg-{{ $statusClass }}" role="progressbar" style="width: {{ $progress }}%"></div>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.interment-payments.show', $interment) }}" class="btn btn-sm btn-light">View / Record</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4 text-muted">No interment payments found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center pt-3">
                    <div class="text-muted small">
                        Showing {{ $interments->firstItem() ?? 0 }} to {{ $interments->lastItem() ?? 0 }} of {{ $interments->total() }} results
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        @if ($interments->onFirstPage())
                            <span class="btn btn-sm btn-outline-secondary disabled">Previous</span>
                        @else
                            <a class="btn btn-sm btn-outline-secondary" href="{{ $interments->previousPageUrl() }}">Previous</a>
                        @endif
                        <span class="text-muted">Page {{ $interments->currentPage() }} of {{ $interments->lastPage() }}</span>
                        @if ($interments->hasMorePages())
                            <a class="btn btn-sm btn-outline-secondary" href="{{ $interments->nextPageUrl() }}">Next</a>
                        @else
                            <span class="btn btn-sm btn-outline-secondary disabled">Next</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
