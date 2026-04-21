@extends('admin.admin_master')

@section('admin')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div>
                        <h4 class="card-title mb-1">{{ $deceased->full_name }}</h4>
                        <div class="text-muted">
                            Interment #{{ $deceased->interment_number ?? $deceased->id }}
                            @if ($deceased->client)
                                · Client: <a href="{{ route('admin.clients.show', $deceased->client) }}">{{ $deceased->client->full_name }}</a>
                            @endif
                            @if ($deceased->lot)
                                · Lot {{ $deceased->lot->lot_number }} ({{ $deceased->lot->section }})
                            @endif
                        </div>
                        <div class="mt-2">
                            @php
                                $statusClass = match($deceased->status) {
                                    'confirmed' => 'success',
                                    'exhumed' => 'secondary',
                                    default => 'warning'
                                };
                                $paymentStatusClass = match($deceased->payment_status) {
                                    'fully_paid' => 'success',
                                    'partial' => 'warning',
                                    default => 'danger'
                                };
                            @endphp
                            <span class="badge bg-{{ $statusClass }}-subtle text-{{ $statusClass }} border border-{{ $statusClass }}-subtle">
                                {{ ucfirst($deceased->status) }}
                            </span>
                            <span class="badge bg-{{ $paymentStatusClass }}-subtle text-{{ $paymentStatusClass }} border border-{{ $paymentStatusClass }}-subtle">
                                {{ $deceased->payment_status_label }}
                            </span>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.interments.index') }}" class="btn btn-light">
                            <i data-feather="arrow-left" class="me-1" style="height: 14px; width: 14px;"></i>
                            Back
                        </a>
                        @if ($deceased->status !== 'exhumed')
                            <form action="{{ route('admin.interments.exhumations.store', $deceased) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-outline-secondary">
                                    <i data-feather="repeat" class="me-1" style="height: 14px; width: 14px;"></i>
                                    Request Exhumation
                                </button>
                            </form>
                        @else
                            <a href="{{ route('admin.exhumations.show', $deceased->exhumation) }}" class="btn btn-outline-secondary">
                                <i data-feather="eye" class="me-1" style="height: 14px; width: 14px;"></i>
                                View Exhumation
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

                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <div class="p-3 border rounded">
                            <div class="text-muted small">Total Fee</div>
                            <div class="h5 mb-0">₱{{ number_format($totalFee, 2) }}</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 border rounded">
                            <div class="text-muted small">Total Paid</div>
                            <div class="h5 mb-0 text-success">₱{{ number_format($totalPaid, 2) }}</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 border rounded">
                            <div class="text-muted small">Remaining Balance</div>
                            <div class="h5 mb-0 {{ $remainingBalance > 0 ? 'text-danger' : '' }}">₱{{ number_format($remainingBalance, 2) }}</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 border rounded">
                            <div class="text-muted small">Progress</div>
                            <div class="h5 mb-0">{{ $deceased->payment_progress }}%</div>
                            <div class="progress mt-1" style="height: 6px;">
                                <div class="progress-bar" role="progressbar" style="width: {{ $deceased->payment_progress }}%;" aria-valuenow="{{ $deceased->payment_progress }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    <div class="col-12">
                        <h5 class="mb-3">Payment History</h5>
                        @if ($deceased->payments->isEmpty())
                            <div class="alert alert-info mb-0">No payments recorded yet.</div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-sm table-hover align-middle">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Method</th>
                                            <th>Reference</th>
                                            <th class="text-end">Amount</th>
                                            <th class="text-end" style="width: 150px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($deceased->payments as $payment)
                                            <tr>
                                                <td>{{ optional($payment->payment_date)->format('Y-m-d') }}</td>
                                                <td>{{ ucfirst($payment->method) }}</td>
                                                <td>{{ $payment->reference_number ?? '-' }}</td>
                                                <td class="text-end">₱{{ number_format((float) $payment->amount, 2) }}</td>
                                                <td class="text-end">
                                                    <a class="btn btn-sm btn-light" href="{{ route('admin.interments.payments.invoice', [$deceased, $payment]) }}">
                                                        Invoice
                                                    </a>
                                                    @if ($payment->receipt_path)
                                                        <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.interments.payments.receipt', [$deceased, $payment]) }}">
                                                            Receipt
                                                        </a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-light">
                                            <th colspan="3" class="text-end">Total Paid</th>
                                            <th class="text-end">₱{{ number_format($totalPaid, 2) }}</th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        @endif

                        <h5 class="mt-4 mb-3">Interment Details</h5>
                        <div class="table-responsive">
                            <table class="table table-sm table-borderless">
                                <tbody>
                                    <tr>
                                        <td class="text-muted" style="width: 180px;">Date of Birth</td>
                                        <td>{{ $deceased->date_of_birth?->format('Y-m-d') ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Date of Death</td>
                                        <td>{{ $deceased->date_of_death?->format('Y-m-d') ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Burial Date</td>
                                        <td>{{ $deceased->burial_date?->format('Y-m-d') ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted" style="width: 180px;">Lot</td>
                                        <td>
                                            @if ($deceased->lot)
                                                <a href="{{ route('admin.lots.map', ['lot_id' => $deceased->lot->id]) }}">{{ $deceased->lot->lot_number }} ({{ $deceased->lot->section }})</a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Contract</td>
                                        <td>
                                            @if ($deceased->contract_path)
                                                <a href="{{ route('admin.interments.contract.download', $deceased) }}" class="btn btn-sm btn-light">
                                                    <i data-feather="download" class="me-1" style="height: 12px; width: 12px;"></i>
                                                    Download
                                                </a>
                                            @else
                                                <span class="text-muted">Not generated</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @if ($deceased->latestExhumation)
                                        <tr>
                                            <td class="text-muted">Exhumation</td>
                                            <td>
                                                <a href="{{ route('admin.exhumations.show', $deceased->latestExhumation) }}">
                                                    Case #{{ $deceased->latestExhumation->id }}
                                                </a>
                                            </td>
                                        </tr>
                                    @endif
                                    @if ($deceased->notes)
                                        <tr>
                                            <td class="text-muted">Notes</td>
                                            <td>{{ $deceased->notes }}</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
