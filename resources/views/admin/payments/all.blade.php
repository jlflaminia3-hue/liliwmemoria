@extends('admin.admin_master')

@section('admin')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="card-title mb-1">All Payments</h4>
                        <div class="text-muted">Cash and Installment payments for reserved lots</div>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.reservations.index') }}" class="btn btn-outline-primary">
                            <i data-feather="calendar" class="me-1" style="height: 14px; width: 14px;"></i>
                            Reservations
                        </a>
                        <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-primary">
                            <i data-feather="credit-card" class="me-1" style="height: 14px; width: 14px;"></i>
                            Installments
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
                            <div class="text-muted small">Cash</div>
                            <div class="h5 mb-0">{{ $stats['cash_count'] ?? 0 }}</div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="p-3 border rounded text-center">
                            <div class="text-muted small">Installments</div>
                            <div class="h5 mb-0">{{ $stats['installment_count'] ?? 0 }}</div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="p-3 border rounded text-center">
                            <div class="text-muted small">Total Amount</div>
                            <div class="h6 mb-0">₱{{ number_format($stats['total_amount'], 2) }}</div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="p-3 border rounded text-center">
                            <div class="text-muted small">Total Paid</div>
                            <div class="h6 mb-0 text-success">₱{{ number_format($stats['total_paid'], 2) }}</div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="p-3 border rounded text-center">
                            <div class="text-muted small">Total Due</div>
                            <div class="h6 mb-0 text-warning">₱{{ number_format($stats['total_due'], 2) }}</div>
                        </div>
                    </div>
                </div>

                <form method="GET" action="{{ route('admin.all-payments.index') }}" class="row g-3 mb-4">
                    <div class="col-md-3">
                        <select name="type" class="form-select" onchange="this.form.submit()">
                            <option value="all" @selected($type === 'all')>All Payments</option>
                            <option value="cash" @selected($type === 'cash')>Cash</option>
                            <option value="installment" @selected($type === 'installment')>Installment</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-select" onchange="this.form.submit()">
                            <option value="all" @selected($status === 'all')>All Statuses</option>
                            <option value="cash" @selected($status === 'cash')>Cash</option>
                            <option value="installment" @selected($status === 'installment')>Installment</option>
                            <option value="pending" @selected($status === 'pending')>Pending</option>
                            <option value="paid" @selected($status === 'paid')>Paid</option>
                            <option value="completed" @selected($status === 'completed')>Completed</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="client_id" class="form-select" onchange="this.form.submit()">
                            <option value="">All Clients</option>
                            @foreach ($clients as $client)
                                <option value="{{ $client->id }}" @selected($clientId == $client->id)>{{ $client->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="search" class="form-control" placeholder="Search..." value="{{ $search }}">
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
                                <th>Type</th>
                                <th>Client</th>
                                <th>Lot</th>
                                <th class="text-end">Amount</th>
                                <th class="text-end">Paid</th>
                                <th class="text-end">Due</th>
                                <th>Due Date</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($allPayments as $payment)
                                @php
                                    $badgeClass = match($payment['status']) {
                                        'cash' => 'success',
                                        'installment' => 'primary',
                                        'pending' => 'warning',
                                        'paid' => 'info',
                                        'overdue' => 'danger',
                                        'completed' => 'success',
                                        default => 'secondary'
                                    };
                                    $isOverdue = $payment['status'] === 'pending' && $payment['due_date'] && \Carbon\Carbon::parse($payment['due_date'])->isPast();
                                @endphp
                                <tr class="{{ $isOverdue ? 'table-danger' : '' }}">
                                    <td>
                                        <div class="fw-semibold">{{ $payment['payment_number'] }}</div>
                                        @if ($payment['payment_method'])
                                            <div class="text-muted small text-capitalize">{{ $payment['payment_method'] }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $badgeClass }}">{{ $payment['status_label'] }}</span>
                                    </td>
                                    <td>
                                        @if ($payment['client'])
                                            <a href="{{ route('admin.clients.show', $payment['client']) }}">{{ $payment['client']->full_name }}</a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if ($payment['lot'])
                                            {{ $payment['lot']->lot_number }} ({{ $payment['lot']->section }})
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-end">₱{{ number_format($payment['amount'], 2) }}</td>
                                    <td class="text-end">₱{{ number_format($payment['amount_paid'], 2) }}</td>
                                    <td class="text-end">₱{{ number_format($payment['amount_due'], 2) }}</td>
                                    <td>
                                        @if ($payment['due_date'])
                                            <span class="{{ $isOverdue ? 'text-danger fw-semibold' : '' }}">
                                                {{ \Carbon\Carbon::parse($payment['due_date'])->format('Y-m-d') }}
                                            </span>
                                            @if ($isOverdue)
                                                <span class="badge bg-danger ms-1">Overdue</span>
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $badgeClass }}">{{ $payment['status_label'] }}</span>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.reservations.index', ['search' => $payment['reservation']->id ?? '']) }}" class="btn btn-sm btn-light">View</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center py-4 text-muted">No payments found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection