@extends('admin.admin_master')

@section('admin')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h4 class="card-title mb-1">Create Payment Plan</h4>
                        <div class="text-muted">12 months = 10%, 18 months = 15%, 24 months = 20% (interest starts after downpayment).</div>
                    </div>
                    <a href="{{ route('admin.payments.index', $clientId ? ['client_id' => $clientId] : []) }}" class="btn btn-light">Back</a>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.payments.store') }}" class="row g-3">
                    @csrf

                    <div class="col-md-6">
                        <label class="form-label">Client</label>
                        <select class="form-select" name="client_id" id="client_id" required>
                            <option value="">Select client...</option>
                            @foreach ($clients as $client)
                                <option value="{{ $client->id }}" @selected(old('client_id', $clientId) == $client->id)>
                                    {{ $client->full_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Contract (optional)</label>
                        <select class="form-select" name="client_contract_id" id="client_contract_id">
                            <option value="">None</option>
                            @foreach ($contracts as $contract)
                                @php
                                    $labelParts = array_filter([
                                        $contract->contract_number ? ('Contract ' . $contract->contract_number) : null,
                                        $contract->client?->full_name,
                                        $contract->lot ? ('Lot ' . $contract->lot->lot_number) : null,
                                    ]);
                                @endphp
                                <option
                                    value="{{ $contract->id }}"
                                    data-client-id="{{ $contract->client_id }}"
                                    data-lot-id="{{ $contract->lot_id ?? '' }}"
                                    data-total="{{ $contract->total_amount ?? '' }}"
                                    @selected(old('client_contract_id') == $contract->id)
                                >
                                    {{ implode(' · ', $labelParts) }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text">Selecting a contract can pre-fill the principal and lot.</div>
                    </div>

                    <input type="hidden" name="lot_id" id="lot_id" value="{{ old('lot_id') }}">

                    <div class="col-md-4">
                        <label class="form-label">Principal Amount</label>
                        <input type="number" step="0.01" min="0" class="form-control" id="principal_amount" name="principal_amount" value="{{ old('principal_amount') }}" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Downpayment</label>
                        <input type="number" step="0.01" min="0" class="form-control" id="downpayment_amount" name="downpayment_amount" value="{{ old('downpayment_amount', 0) }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Terms</label>
                        <select class="form-select" name="term_months" id="term_months" required>
                            @foreach ($terms as $months => $rate)
                                <option value="{{ $months }}" @selected(old('term_months', 12) == $months)>
                                    {{ $months }} months ({{ $rate }}%)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Start Date</label>
                        <input type="date" class="form-control" name="start_date" value="{{ old('start_date', now()->toDateString()) }}" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Penalty Rate % (per 30 days overdue)</label>
                        <input type="number" step="0.01" min="0" max="100" class="form-control" name="penalty_rate_percent" value="{{ old('penalty_rate_percent', 0) }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Penalty Grace Days</label>
                        <input type="number" min="0" max="365" class="form-control" name="penalty_grace_days" value="{{ old('penalty_grace_days', 0) }}">
                    </div>

                    <div class="col-12">
                        <label class="form-label">Notes (optional)</label>
                        <textarea class="form-control" name="notes" rows="3">{{ old('notes') }}</textarea>
                    </div>

                    <div class="col-12 d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.payments.index', $clientId ? ['client_id' => $clientId] : []) }}" class="btn btn-light">Cancel</a>
                        <button class="btn btn-primary" type="submit">Create Plan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        var contractSelect = document.getElementById('client_contract_id');
        var clientSelect = document.getElementById('client_id');
        var principalInput = document.getElementById('principal_amount');
        var lotIdInput = document.getElementById('lot_id');

        if (!contractSelect) return;

        contractSelect.addEventListener('change', function () {
            var option = contractSelect.options[contractSelect.selectedIndex];
            if (!option || !option.value) return;

            var clientId = option.getAttribute('data-client-id');
            var lotId = option.getAttribute('data-lot-id');
            var total = option.getAttribute('data-total');

            if (clientId && clientSelect) clientSelect.value = clientId;
            if (lotIdInput) lotIdInput.value = lotId || '';
            if (total && principalInput && !principalInput.value) principalInput.value = total;
        });
    })();
</script>
@endsection

