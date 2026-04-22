@extends('admin.admin_master')

@section('admin')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div>
                        <h4 class="card-title mb-1">Regular Lot Payment: {{ $lotPayment->payment_number }}</h4>
                        <div class="text-muted">
                            @if ($lotPayment->client)
                                Client: <a href="{{ route('admin.clients.show', $lotPayment->client) }}">{{ $lotPayment->client->full_name }}</a>
                            @endif
                            @if ($lotPayment->lot)
                                · Lot {{ $lotPayment->lot->lot_number }} ({{ $lotPayment->lot->section }})
                            @endif
                        </div>
                        <div class="mt-2">
                            @php
                                $badgeClass = match($lotPayment->status) {
                                    'pending' => 'warning',
                                    'paid' => 'info',
                                    'verified' => 'primary',
                                    'completed' => 'success',
                                    'overdue' => 'danger',
                                    'cancelled' => 'secondary',
                                    default => 'secondary'
                                };
                                $isOverdue = $lotPayment->status === 'pending' && $lotPayment->due_date && $lotPayment->due_date->isPast();
                            @endphp
                            <span class="badge bg-{{ $badgeClass }}">{{ $lotPayment->status_label }}</span>
                            @if ($isOverdue)
                                <span class="badge bg-danger ms-1">Overdue</span>
                            @endif
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.lot-payments.index') }}" class="btn btn-light">
                            <i data-feather="arrow-left" class="me-1" style="height: 14px; width: 14px;"></i>
                            Back
                        </a>
                        <a href="{{ route('admin.lot-payments.downloadContract', $lotPayment) }}" class="btn btn-outline-primary">
                            <i data-feather="file-text" class="me-1" style="height: 14px; width: 14px;"></i>
                            Download Contract
                        </a>
                        @if ($lotPayment->receipt_path)
                            <a href="{{ route('admin.lot-payments.downloadReceipt', $lotPayment) }}" class="btn btn-outline-secondary">
                                <i data-feather="download" class="me-1" style="height: 14px; width: 14px;"></i>
                                Receipt
                            </a>
                        @endif
                    </div>
                </div>

                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <div class="row g-4">
                    <div class="col-lg-8">
                        <h5 class="mb-3">Payment Details</h5>
                        <div class="table-responsive">
                            <table class="table table-sm table-borderless">
                                <tbody>
                                    <tr>
                                        <td class="text-muted" style="width: 180px;">Payment Number</td>
                                        <td class="fw-semibold">{{ $lotPayment->payment_number }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Amount</td>
                                        <td class="fw-semibold h5 text-success">₱{{ number_format((float) $lotPayment->amount, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Due Date</td>
                                        <td>
                                            @if ($lotPayment->due_date)
                                                <span class="{{ $isOverdue ? 'text-danger fw-semibold' : '' }}">
                                                    {{ $lotPayment->due_date->format('Y-m-d') }}
                                                </span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Payment Date</td>
                                        <td>{{ $lotPayment->payment_date?->format('Y-m-d') ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Payment Method</td>
                                        <td>{{ $lotPayment->method ? ucfirst($lotPayment->method) : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Reference Number</td>
                                        <td>{{ $lotPayment->reference_number ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Status</td>
                                        <td><span class="badge bg-{{ $badgeClass }}">{{ $lotPayment->status_label }}</span></td>
                                    </tr>
                                    @if ($lotPayment->verified_at)
                                        <tr>
                                            <td class="text-muted">Verified At</td>
                                            <td>{{ $lotPayment->verified_at->format('Y-m-d H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Verified By</td>
                                            <td>{{ $lotPayment->verifier?->name ?? '-' }}</td>
                                        </tr>
                                    @endif
                                    @if ($lotPayment->completed_at)
                                        <tr>
                                            <td class="text-muted">Completed At</td>
                                            <td>{{ $lotPayment->completed_at->format('Y-m-d H:i') }}</td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td class="text-muted">Contract Email</td>
                                        <td>
                                            @if ($lotPayment->contract_emailed_at)
                                                <span class="text-success">
                                                    <i data-feather="check-circle" style="height: 14px; width: 14px;"></i>
                                                    Sent {{ $lotPayment->contract_emailed_at->format('Y-m-d H:i') }}
                                                </span>
                                            @else
                                                <form method="POST" action="{{ route('admin.lot-payments.sendContractEmail', $lotPayment) }}" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-primary" @if(!$lotPayment->client?->email) disabled @endif>
                                                        <i data-feather="send" class="me-1" style="height: 12px; width: 12px;"></i>
                                                        Send Contract Email
                                                    </button>
                                                </form>
                                                @if(!$lotPayment->client?->email)
                                                    <span class="text-muted small">No client email</span>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                    @if ($lotPayment->notes)
                                        <tr>
                                            <td class="text-muted">Notes</td>
                                            <td>{{ $lotPayment->notes }}</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        @if ($lotPayment->lot)
                            <h5 class="mt-4 mb-3">Lot Details</h5>
                            <div class="table-responsive">
                                <table class="table table-sm table-borderless">
                                    <tbody>
                                        <tr>
                                            <td class="text-muted" style="width: 180px;">Lot Number</td>
                                            <td>{{ $lotPayment->lot->lot_number }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Section</td>
                                            <td>{{ $lotPayment->lot->section }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Price</td>
                                            <td>₱{{ number_format((float) $lotPayment->lot->price, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Status</td>
                                            <td><span class="badge bg-{{ $lotPayment->lot->status === 'available' ? 'success' : 'secondary' }}">{{ ucfirst($lotPayment->lot->status) }}</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                    <div class="col-lg-4">
                        @if ($lotPayment->status === 'pending')
                            <div class="card border border-warning">
                                <div class="card-body">
                                    <h5 class="card-title mb-3 text-warning">Mark as Paid</h5>
                                    <form method="POST" action="{{ route('admin.lot-payments.markPaid', $lotPayment) }}" enctype="multipart/form-data">
                                        @csrf
                                        <div class="mb-3">
                                            <label class="form-label mb-1">Payment Date</label>
                                            <input type="date" name="payment_date" class="form-control" value="{{ now()->toDateString() }}">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label mb-1">Method</label>
                                            <select name="method" class="form-select">
                                                @foreach (['cash' => 'Cash', 'bank' => 'Bank Transfer', 'gcash' => 'GCash', 'card' => 'Card', 'check' => 'Check', 'other' => 'Other'] as $value => $label)
                                                    <option value="{{ $value }}">{{ $label }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label mb-1">Reference Number</label>
                                            <input type="text" name="reference_number" class="form-control" placeholder="Optional">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label mb-1">Receipt</label>
                                            <input type="file" name="receipt" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                                            <div class="form-text">PDF/Image up to 10MB</div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label mb-1">Notes</label>
                                            <textarea name="notes" class="form-control" rows="2"></textarea>
                                        </div>
                                        <div class="d-grid">
                                            <button type="submit" class="btn btn-warning">Mark as Paid</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endif

                        @if ($lotPayment->status === 'paid')
                            <div class="card border border-primary">
                                <div class="card-body">
                                    <h5 class="card-title mb-3">Verify Payment</h5>
                                    <p class="text-muted small">Once verified, the payment will proceed to completion.</p>
                                    <form method="POST" action="{{ route('admin.lot-payments.verify', $lotPayment) }}">
                                        @csrf
                                        <div class="d-grid">
                                            <button type="submit" class="btn btn-primary">Verify Payment</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endif

                        @if ($lotPayment->status === 'verified')
                            <div class="card border border-success">
                                <div class="card-body">
                                    <h5 class="card-title mb-3">Complete Payment</h5>
                                    <p class="text-muted small">This will mark the lot as sold and complete the transaction.</p>
                                    <form method="POST" action="{{ route('admin.lot-payments.complete', $lotPayment) }}">
                                        @csrf
                                        <div class="d-grid">
                                            <button type="submit" class="btn btn-success">Complete Payment</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endif

                        @if ($lotPayment->status === 'completed')
                            <div class="card border border-success">
                                <div class="card-body text-center">
                                    <i data-feather="check-circle" class="text-success mb-2" style="height: 48px; width: 48px;"></i>
                                    <h5 class="card-title mb-1 text-success">Payment Completed</h5>
                                    <p class="text-muted small mb-0">Completed on {{ $lotPayment->completed_at?->format('Y-m-d H:i') }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <hr class="my-4">

                <div class="text-muted small">
                    Created: {{ $lotPayment->created_at->format('Y-m-d H:i') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
