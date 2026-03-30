@extends('admin.admin_master')

@section('admin')
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
                                Financial transactions and installment schedules
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

                @if ($plans->isEmpty())
                    <div class="alert alert-info mb-0">No payment plans yet.</div>
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
                                            {{ $plan->term_months }} months
                                            <div class="text-muted small">{{ number_format((float) $plan->interest_rate_percent, 2) }}%</div>
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
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
