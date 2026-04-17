@extends('admin.admin_master')

@section('admin')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="card-title mb-1">Regular Lot Payments</h4>
                        <div class="text-muted">One-time full settlement payments</div>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.lot-payments.create') }}" class="btn btn-success">
                            <i data-feather="plus" class="me-1" style="height: 14px; width: 14px;"></i>
                            New Payment
                        </a>
                    </div>
                </div>

                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <div class="row g-3 mb-4">
                    <div class="col-md-2">
                        <div class="p-3 border rounded text-center">
                            <div class="text-muted small">Total</div>
                            <div class="h5 mb-0">{{ $stats['total'] }}</div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="p-3 border rounded text-center">
                            <div class="text-muted small">Pending</div>
                            <div class="h5 mb-0 text-warning">{{ $stats['pending'] }}</div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="p-3 border rounded text-center">
                            <div class="text-muted small">Paid</div>
                            <div class="h5 mb-0 text-info">{{ $stats['paid'] }}</div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="p-3 border rounded text-center">
                            <div class="text-muted small">Verified</div>
                            <div class="h5 mb-0 text-primary">{{ $stats['verified'] }}</div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="p-3 border rounded text-center">
                            <div class="text-muted small">Completed</div>
                            <div class="h5 mb-0 text-success">{{ $stats['completed'] }}</div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="p-3 border rounded text-center">
                            <div class="text-muted small">Overdue</div>
                            <div class="h5 mb-0 text-danger">{{ $stats['overdue'] }}</div>
                        </div>
                    </div>
                </div>

                <form method="GET" action="{{ route('admin.lot-payments.index') }}" class="row g-3 mb-4">
                    <div class="col-md-3">
                        <select name="status" class="form-select" onchange="this.form.submit()">
                            <option value="all">All Statuses</option>
                            @foreach (['pending' => 'Pending', 'paid' => 'Paid', 'verified' => 'Verified', 'completed' => 'Completed', 'overdue' => 'Overdue', 'cancelled' => 'Cancelled'] as $value => $label)
                                <option value="{{ $value }}" @selected($status === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="client_id" class="form-select" onchange="this.form.submit()">
                            <option value="">All Clients</option>
                            @foreach ($clients as $client)
                                <option value="{{ $client->id }}" @selected($clientId == $client->id)>{{ $client->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" placeholder="Search payment number, reference, client, lot..." value="{{ $search }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-outline-secondary w-100">Search</button>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Payment #</th>
                                <th>Client</th>
                                <th>Lot</th>
                                <th class="text-end">Amount</th>
                                <th>Due Date</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($payments as $payment)
                                @php
                                    $badgeClass = match($payment->status) {
                                        'pending' => 'warning',
                                        'paid' => 'info',
                                        'verified' => 'primary',
                                        'completed' => 'success',
                                        'overdue' => 'danger',
                                        'cancelled' => 'secondary',
                                        default => 'secondary'
                                    };
                                    $isOverdue = $payment->status === 'pending' && $payment->due_date && $payment->due_date->isPast();
                                @endphp
                                <tr class="{{ $isOverdue ? 'table-danger' : '' }}">
                                    <td>
                                        <div class="fw-semibold">{{ $payment->payment_number }}</div>
                                        @if ($payment->reference_number)
                                            <div class="text-muted small">{{ $payment->reference_number }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($payment->client)
                                            <a href="{{ route('admin.clients.show', $payment->client) }}">{{ $payment->client->full_name }}</a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if ($payment->lot)
                                            {{ $payment->lot->lot_number }} ({{ $payment->lot->section }})
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-end">₱{{ number_format((float) $payment->amount, 2) }}</td>
                                    <td>
                                        @if ($payment->due_date)
                                            <span class="{{ $isOverdue ? 'text-danger fw-semibold' : '' }}">
                                                {{ $payment->due_date->format('Y-m-d') }}
                                            </span>
                                            @if ($isOverdue)
                                                <span class="badge bg-danger ms-1">Overdue</span>
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $badgeClass }}">{{ $payment->status_label }}</span>
                                    </td>
                                    <td>
                                        @if ($payment->payment_date)
                                            {{ $payment->payment_date->format('Y-m-d') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.lot-payments.show', $payment) }}" class="btn btn-sm btn-light">View</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">No regular lot payments found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center pt-3">
                    <div class="text-muted small">
                        Showing {{ $payments->firstItem() ?? 0 }} to {{ $payments->lastItem() ?? 0 }} of {{ $payments->total() }} results
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        @if ($payments->onFirstPage())
                            <span class="btn btn-sm btn-outline-secondary disabled">Previous</span>
                        @else
                            <a class="btn btn-sm btn-outline-secondary" href="{{ $payments->previousPageUrl() }}">Previous</a>
                        @endif
                        <span class="text-muted">Page {{ $payments->currentPage() }} of {{ $payments->lastPage() }}</span>
                        @if ($payments->hasMorePages())
                            <a class="btn btn-sm btn-outline-secondary" href="{{ $payments->nextPageUrl() }}">Next</a>
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
