@extends('admin.admin_master')

@section('admin')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h4 class="card-title mb-1">Invoice</h4>
                        <div class="text-muted">Transaction #{{ $transaction->id }} · {{ optional($transaction->transaction_date)->format('Y-m-d') }}</div>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.payments.show', $transaction->plan) }}" class="btn btn-light">Back</a>
                        <button class="btn btn-primary" type="button" onclick="window.print()">Print</button>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="border rounded p-3">
                            <div class="text-muted small">Billed To</div>
                            <div class="fw-semibold">{{ $transaction->plan->client->full_name }}</div>
                            <div class="text-muted small">Plan: {{ $transaction->plan->plan_number }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border rounded p-3">
                            <div class="text-muted small">Payment Details</div>
                            <div>Method: <strong>{{ $transaction->method }}</strong></div>
                            <div>Reference: {{ $transaction->reference_number ?? '-' }}</div>
                            <div class="mt-2 h5 mb-0">Amount: {{ number_format((float) $transaction->amount, 2) }}</div>
                        </div>
                    </div>
                </div>

                <h5 class="mt-4 mb-2">Allocations</h5>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Installment</th>
                                <th>Due Date</th>
                                <th class="text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($transaction->allocations as $alloc)
                                <tr>
                                    <td>{{ ucfirst($alloc->type) }}</td>
                                    <td>
                                        @if ($alloc->installment)
                                            #{{ $alloc->installment->sequence }} ({{ ucfirst($alloc->installment->type) }})
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $alloc->installment?->due_date?->format('Y-m-d') ?? '-' }}</td>
                                    <td class="text-end">{{ number_format((float) $alloc->amount_applied, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end">Total</th>
                                <th class="text-end">{{ number_format((float) $transaction->amount, 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                @if ($transaction->notes)
                    <div class="mt-3">
                        <div class="text-muted small">Notes</div>
                        <div>{{ $transaction->notes }}</div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

