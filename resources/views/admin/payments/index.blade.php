@extends('admin.admin_master')

@section('admin')
@php
    $currentPerPage = (int) request('per_page', 20);
@endphp
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h4 class="card-title mb-1">Payments</h4>
                        <div class="text-muted">
                            @if ($client)
                                Viewing payment plans for <strong>{{ $client->full_name }}</strong>
                            @else
                                Financial transactions including cash payments and installment schedules
                            @endif
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        @if ($client)
                            <a href="{{ route('admin.clients.show', $client) }}" class="btn btn-light">Back to Client</a>
                        @endif
                        <a href="{{ route('admin.payments.create', $client ? ['client_id' => $client->id] : []) }}" class="btn btn-success">
                            <i data-feather="plus"></i> Create Payment Plan
                        </a>
                    </div>
                </div>

                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <ul class="nav nav-tabs" id="paymentTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="cash-tab" data-bs-toggle="tab" data-bs-target="#cash-pane" type="button" role="tab" aria-controls="cash-pane" aria-selected="true">
                            Cash / One-Time
                            @if ($lotPayments->isNotEmpty())
                                <span class="badge bg-secondary ms-1">{{ $lotPayments->total() }}</span>
                            @endif
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="installment-tab" data-bs-toggle="tab" data-bs-target="#installment-pane" type="button" role="tab" aria-controls="installment-pane" aria-selected="false">
                            Installments
                            @if ($plans->isNotEmpty())
                                <span class="badge bg-secondary ms-1">{{ $plans->total() }}</span>
                            @endif
                        </button>
                    </li>
                </ul>

                <form method="GET" action="{{ route('admin.payments.index') }}" class="row g-3 mb-4 mt-3">
                    <div class="col-md-2">
                        <select name="status" class="form-select" onchange="this.form.submit()">
                            <option value="all" @selected($status === 'all')>All Statuses</option>
                            <option value="active" @selected($status === 'active')>Active</option>
                            <option value="completed" @selected($status === 'completed')>Completed</option>
                            <option value="canceled" @selected($status === 'canceled')>Canceled</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="client_id" class="form-select" onchange="this.form.submit()">
                            <option value="">All Clients</option>
                            @foreach ($clients as $c)
                                <option value="{{ $c->id }}" @selected($clientId == $c->id)>{{ $c->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="search" class="form-control" placeholder="Search payment number, client, lot..." value="{{ $search }}">
                    </div>
                    <div class="col-md-2">
                        <select name="per_page" class="form-select" onchange="this.form.submit()">
                            <option value="10" @selected($currentPerPage === 10)>10 rows</option>
                            <option value="20" @selected($currentPerPage === 20)>20 rows</option>
                            <option value="50" @selected($currentPerPage === 50)>50 rows</option>
                            <option value="100" @selected($currentPerPage === 100)>100 rows</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-outline-secondary w-100">Apply</button>
                    </div>
                </form>

                <div class="tab-content" id="paymentTabsContent">
                    <div class="tab-pane fade show active" id="cash-pane" role="tabpanel" aria-labelledby="cash-tab">
                        @if ($lotPayments->isEmpty())
                            <div class="alert alert-info mb-0">No cash payments found.</div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover mb-0 align-middle">
                                    <thead>
                                        <tr>
                                            <th>Payment #</th>
                                            <th>Client</th>
                                            <th>Lot</th>
                                            <th class="text-end">Amount</th>
                                            <th>Payment Date</th>
                                            <th>Method</th>
                                            <th>Status</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($lotPayments as $payment)
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
                                            @endphp
                                            <tr>
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
                                                    @if ($payment->payment_date)
                                                        {{ $payment->payment_date->format('Y-m-d') }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>{{ $payment->method ?? '-' }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $badgeClass }}">{{ $payment->status_label ?? ucfirst($payment->status) }}</span>
                                                </td>
                                                <td class="text-end">
                                                    <a href="{{ route('admin.lot-payments.show', $payment) }}" class="btn btn-sm btn-light">View</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-between align-items-center pt-3">
                                <div class="text-muted small">
                                    Showing {{ $lotPayments->firstItem() ?? 0 }} to {{ $lotPayments->lastItem() ?? 0 }} of {{ $lotPayments->total() }} results
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    @if ($lotPayments->onFirstPage())
                                        <span class="btn btn-sm btn-outline-secondary disabled">Previous</span>
                                    @else
                                        <a class="btn btn-sm btn-outline-secondary" href="{{ $lotPayments->previousPageUrl() }}">Previous</a>
                                    @endif
                                    <span class="text-muted">Page {{ $lotPayments->currentPage() }} of {{ $lotPayments->lastPage() }}</span>
                                    @if ($lotPayments->hasMorePages())
                                        <a class="btn btn-sm btn-outline-secondary" href="{{ $lotPayments->nextPageUrl() }}">Next</a>
                                    @else
                                        <span class="btn btn-sm btn-outline-secondary disabled">Next</span>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="tab-pane fade" id="installment-pane" role="tabpanel" aria-labelledby="installment-tab">
                        @if ($plans->isEmpty())
                            <div class="alert alert-info mb-0">No installment payment plans found.</div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover mb-0 align-middle">
                                    <thead>
                                        <tr>
                                            <th>Plan</th>
                                            <th>Client</th>
                                            <th>Lot / Contract</th>
                                            <th>Terms</th>
                                            <th class="text-end">Principal</th>
                                            <th class="text-end">Interest</th>
                                            <th class="text-end">Paid</th>
                                            <th class="text-end">Outstanding</th>
                                            <th>Status</th>
                                            <th class="text-end" style="width: 80px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($plans as $plan)
                                            <tr>
                                                <td>
                                                    <div class="fw-semibold">{{ $plan->plan_number }}</div>
                                                    <div class="text-muted small">Start: {{ optional($plan->start_date)->format('Y-m-d') }}</div>
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin.clients.show', $plan->client) }}">{{ $plan->client->full_name }}</a>
                                                </td>
                                                <td>
                                                    @php
                                                        $lotLabel = $plan->lot ? ('Lot ' . $plan->lot->lot_number) : ($plan->contract?->lot ? ('Lot ' . $plan->contract->lot->lot_number) : null);
                                                        $contractLabel = $plan->contract?->contract_number ? ('Contract ' . $plan->contract->contract_number) : null;
                                                        $meta = array_filter([$lotLabel, $contractLabel]);
                                                    @endphp
                                                    {{ !empty($meta) ? implode(' · ', $meta) : '-' }}
                                                </td>
                                                <td>
                                                    {{ $plan->term_months > 0 ? $plan->term_months . ' months' : 'Cash' }}
                                                    @if ($plan->term_months > 0)
                                                        <div class="text-muted small">{{ number_format((float) $plan->interest_rate_percent, 2) }}%</div>
                                                    @endif
                                                </td>
                                                <td class="text-end">₱{{ number_format((float) $plan->principal_amount, 2) }}</td>
                                                <td class="text-end">₱{{ number_format((float) $plan->interest_amount, 2) }}</td>
                                                <td class="text-end">₱{{ number_format((float) ($plan->paid_total ?? 0), 2) }}</td>
                                                <td class="text-end">₱{{ number_format((float) ($plan->outstanding_total ?? 0), 2) }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $plan->status === 'completed' ? 'success' : ($plan->status === 'canceled' ? 'secondary' : 'primary') }}">
                                                        {{ ucfirst($plan->status) }}
                                                    </span>
                                                </td>
                                                <td class="text-end">
                                                    <a href="{{ route('admin.payments.show', $plan) }}" class="btn btn-sm btn-light">View</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-between align-items-center pt-3">
                                <div class="text-muted small">
                                    Showing {{ $plans->firstItem() ?? 0 }} to {{ $plans->lastItem() ?? 0 }} of {{ $plans->total() }} results
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    @if ($plans->onFirstPage())
                                        <span class="btn btn-sm btn-outline-secondary disabled">Previous</span>
                                    @else
                                        <a class="btn btn-sm btn-outline-secondary" href="{{ $plans->previousPageUrl() }}">Previous</a>
                                    @endif
                                    <span class="text-muted">Page {{ $plans->currentPage() }} of {{ $plans->lastPage() }}</span>
                                    @if ($plans->hasMorePages())
                                        <a class="btn btn-sm btn-outline-secondary" href="{{ $plans->nextPageUrl() }}">Next</a>
                                    @else
                                        <span class="btn btn-sm btn-outline-secondary disabled">Next</span>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection