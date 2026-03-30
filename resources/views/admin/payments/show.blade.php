@extends('admin.admin_master')

@section('admin')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h4 class="card-title mb-1">Payment Plan: {{ $plan->plan_number }}</h4>
                        <div class="text-muted">
                            Client:
                            <a href="{{ route('admin.clients.show', $plan->client) }}">{{ $plan->client->full_name }}</a>
                            · Terms: {{ $plan->term_months }} months ({{ number_format((float) $plan->interest_rate_percent, 2) }}%)
                            · Start: {{ optional($plan->start_date)->format('Y-m-d') }}
                        </div>
                        @php
                            $lots = $plan->client->lotOwnerships->map(fn ($o) => $o->lot)->filter();
                        @endphp
                        @if ($lots->isNotEmpty())
                            <div class="text-muted small mt-1">
                                Owned lots:
                                {{ $lots->map(fn ($l) => 'Lot ' . $l->lot_number)->unique()->values()->implode(', ') }}
                            </div>
                        @endif
                    </div>
                    <div class="d-flex gap-2">
                        <form method="POST" action="{{ route('admin.payments.notify', $plan) }}" onsubmit="return confirm('Send payment schedule email to {{ $plan->client->email ?? 'this client' }}?')">
                            @csrf
                            <button type="submit" class="btn btn-secondary" @disabled(empty($plan->client->email))>
                                Email Payment
                            </button>
                        </form>
                        <a href="{{ route('admin.payments.index', ['client_id' => $plan->client_id]) }}" class="btn btn-light">Back</a>
                    </div>
                </div>

                <hr class="my-4">

                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                @if ($plan->last_notified_at)
                    <div class="text-muted small mb-2">Last payment email sent: {{ $plan->last_notified_at->format('Y-m-d H:i') }}</div>
                @endif

                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <div class="p-3 border rounded">
                            <div class="text-muted small">Principal</div>
                            <div class="h5 mb-0">₱{{ number_format((float) $totals['principal_total'], 2) }}</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 border rounded">
                            <div class="text-muted small">Interest</div>
                            <div class="h5 mb-0">₱{{ number_format((float) $totals['interest_total'], 2) }}</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 border rounded">
                            <div class="text-muted small">Paid</div>
                            <div class="h5 mb-0">₱{{ number_format((float) $totals['paid_total'], 2) }}</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 border rounded">
                            <div class="text-muted small">Outstanding (incl. penalties)</div>
                            <div class="h5 mb-0">₱{{ number_format((float) $totals['outstanding_total'], 2) }}</div>
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    <div class="col-lg-8">
                        <h5 class="mb-2">Monthly Installment Schedule</h5>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th style="width: 60px;">#</th>
                                        <th>Type</th>
                                        <th>Due</th>
                                        <th class="text-end">Amount</th>
                                        <th class="text-end">Paid</th>
                                        <th class="text-end">Penalty</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($plan->installments as $inst)
                                        @php
                                            $penaltyDue = max(0, (float) $inst->penalty_accrued - (float) $inst->penalty_paid);
                                            $instDue = max(0, (float) $inst->amount_due - (float) $inst->amount_paid);
                                        @endphp
                                        <tr>
                                            <td>{{ $inst->sequence }}</td>
                                            <td>
                                                <div class="fw-semibold">{{ ucfirst($inst->type) }}</div>
                                                @if ($inst->type === 'installment')
                                                    <div class="text-muted small">P ₱{{ number_format((float) $inst->principal_due, 2) }} · I ₱{{ number_format((float) $inst->interest_due, 2) }}</div>
                                                @endif
                                            </td>
                                            <td>{{ optional($inst->due_date)->format('Y-m-d') }}</td>
                                            <td class="text-end">₱{{ number_format((float) $inst->amount_due, 2) }}</td>
                                            <td class="text-end">₱{{ number_format((float) $inst->amount_paid, 2) }}</td>
                                            <td class="text-end">
                                                <div>₱{{ number_format((float) $inst->penalty_accrued, 2) }}</div>
                                                @if ($penaltyDue > 0)
                                                    <div class="text-muted small">Due: ₱{{ number_format($penaltyDue, 2) }}</div>
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $badge = match ($inst->status) {
                                                        'paid' => 'success',
                                                        'overdue' => 'danger',
                                                        'partial' => 'warning',
                                                        default => 'secondary',
                                                    };
                                                @endphp
                                                <span class="badge bg-{{ $badge }}">{{ ucfirst($inst->status) }}</span>
                                                @if ($instDue > 0)
                                                    <div class="text-muted small">Due: ₱{{ number_format($instDue, 2) }}</div>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <h5 class="mt-4 mb-2">Transaction History</h5>
                        @if ($plan->transactions->isEmpty())
                            <div class="alert alert-info mb-0">No transactions yet.</div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-sm table-hover align-middle">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Method</th>
                                            <th>Reference</th>
                                            <th class="text-end">Amount</th>
                                            <th class="text-end">Unapplied</th>
                                            <th class="text-end" style="width: 220px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($plan->transactions as $tx)
                                            <tr>
                                                <td>{{ optional($tx->transaction_date)->format('Y-m-d') }}</td>
                                                <td>{{ $tx->method }}</td>
                                                <td>{{ $tx->reference_number ?? '-' }}</td>
                                                <td class="text-end">₱{{ number_format((float) $tx->amount, 2) }}</td>
                                                <td class="text-end">₱{{ number_format((float) $tx->unapplied_amount, 2) }}</td>
                                                <td class="text-end">
                                                    <a class="btn btn-sm btn-light" href="{{ route('admin.paymentTransactions.invoice', $tx) }}">Invoice</a>
                                                    <a class="btn btn-sm btn-light" href="{{ route('admin.paymentTransactions.invoice', [$tx, 'download' => 1]) }}">Download</a>
                                                    @if ($tx->receipt_path)
                                                        <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.paymentTransactions.receipt', $tx) }}">Receipt</a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                    <div class="col-lg-4">
                        <div class="card border">
                            <div class="card-body">
                                <h5 class="card-title mb-2">Add Payment</h5>
                                <form method="POST" action="{{ route('admin.payments.transactions.store', $plan) }}" enctype="multipart/form-data" class="row g-2">
                                    @csrf
                                    <div class="col-12">
                                        <label class="form-label mb-1">Date</label>
                                        <input type="date" name="transaction_date" class="form-control" value="{{ old('transaction_date', now()->toDateString()) }}" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label mb-1">Amount</label>
                                        <input type="number" step="0.01" min="0" name="amount" class="form-control" value="{{ old('amount') }}" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label mb-1">Method</label>
                                        <select name="method" class="form-select" required>
                                            @foreach (['cash' => 'Cash', 'bank' => 'Bank Transfer', 'gcash' => 'GCash', 'card' => 'Card', 'check' => 'Check', 'other' => 'Other'] as $k => $v)
                                                <option value="{{ $k }}" @selected(old('method', 'cash') === $k)>{{ $v }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label mb-1">Reference No. (optional)</label>
                                        <input type="text" name="reference_number" class="form-control" value="{{ old('reference_number') }}">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label mb-1">Receipt (optional)</label>
                                        <input type="file" name="receipt" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                                        <div class="form-text">PDF/JPG/PNG up to 10MB.</div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label mb-1">Notes (optional)</label>
                                        <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
                                    </div>
                                    <div class="col-12 d-grid">
                                        <button class="btn btn-primary" type="submit">Record Payment</button>
                                    </div>
                                </form>
                                <div class="text-muted small mt-2">
                                    Payments auto-apply to penalties first, then to the oldest due installments.
                                </div>
                            </div>
                        </div>
                        @if ($errors->any())
                            <div class="alert alert-danger mt-3 mb-0">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
