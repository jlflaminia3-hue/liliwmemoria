@extends('admin.admin_master')

@section('admin')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h4 class="card-title mb-1">Payment Invoice</h4>
                        <div class="text-muted">Interment #{{ $deceased->interment_number ?? $deceased->id }} · Payment #{{ $payment->id }} · {{ optional($payment->payment_date)->format('Y-m-d') }}</div>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.interment-payments.show', $deceased) }}" class="btn btn-light">Back</a>
                        <a href="{{ route('admin.interment-payments.invoice', [$deceased, $payment, 'download' => 1]) }}" class="btn btn-light">
                            <i data-feather="download" class="me-1" style="height: 14px; width: 14px;"></i>
                            Download
                        </a>
                        <button class="btn btn-primary" type="button" onclick="window.print()">Print</button>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="border rounded p-3">
                            <div class="text-muted small">Billed To</div>
                            <div class="fw-semibold">{{ $deceased->client->full_name ?? 'N/A' }}</div>
                            @if ($deceased->client?->email)
                                <div class="text-muted small">{{ $deceased->client->email }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border rounded p-3">
                            <div class="text-muted small">Payment Details</div>
                            <div>Method: <strong>{{ ucfirst($payment->method) }}</strong></div>
                            <div>Reference: {{ $payment->reference_number ?? '-' }}</div>
                            <div>Date: {{ optional($payment->payment_date)->format('Y-m-d') }}</div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Description</th>
                                <th class="text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <strong>Interment Fee Payment</strong>
                                    <div class="text-muted small">
                                        {{ $deceased->full_name }}
                                        @if ($deceased->lot)
                                            (Lot {{ $deceased->lot->lot_number }}, {{ $deceased->lot->section }})
                                        @endif
                                    </div>
                                </td>
                                <td class="text-end">₱{{ number_format((float) $payment->amount, 2) }}</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th class="text-end">Total</th>
                                <th class="text-end">₱{{ number_format((float) $payment->amount, 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="row mt-4">
                    <div class="col-md-6">
                        @if ($payment->notes)
                            <div>
                                <div class="text-muted small">Notes</div>
                                <div>{{ $payment->notes }}</div>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-6 text-end">
                        <div class="text-muted small mb-1">Payment Summary</div>
                        <div>Total Fee: ₱{{ number_format((float) ($deceased->interment_fee ?? 15000), 2) }}</div>
                        <div class="text-success">Previous Payments: ₱{{ number_format((float) $deceased->total_paid - (float) $payment->amount, 2) }}</div>
                        <div class="text-success">This Payment: ₱{{ number_format((float) $payment->amount, 2) }}</div>
                        <div class="fw-semibold">New Balance: ₱{{ number_format(max(0, (float) ($deceased->interment_fee ?? 15000) - (float) $deceased->total_paid), 2) }}</div>
                    </div>
                </div>

                @if ($payment->receipt_path)
                    <div class="mt-4 pt-3 border-top">
                        <a href="{{ route('admin.interment-payments.receipt', [$deceased, $payment]) }}" class="btn btn-outline-primary">
                            <i data-feather="file-text" class="me-1" style="height: 14px; width: 14px;"></i>
                            View Receipt
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
