@extends('admin.admin_master')

@section('admin')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="card-title mb-1">New Regular Lot Payment</h4>
                        <div class="text-muted">Record a one-time full settlement</div>
                    </div>
                    <a href="{{ route('admin.lot-payments.index') }}" class="btn btn-light">
                        <i data-feather="arrow-left" class="me-1" style="height: 14px; width: 14px;"></i>
                        Back
                    </a>
                </div>

                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.lot-payments.store') }}" class="row g-3">
                    @csrf

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Client <span class="text-danger">*</span></label>
                        <select name="client_id" class="form-select" required>
                            <option value="">Select client</option>
                            @foreach ($clients as $client)
                                <option value="{{ $client->id }}" @selected(old('client_id', $selectedClient?->id) == $client->id)>
                                    {{ $client->full_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('client_id')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Lot <span class="text-danger">*</span></label>
                        <select name="lot_id" class="form-select" required>
                            <option value="">Select lot</option>
                            @foreach ($lots as $lot)
                                <option value="{{ $lot->id }}" @selected(old('lot_id', $selectedLot?->id) == $lot->id)>
                                    Lot {{ $lot->lot_number }} - {{ $lot->section }}
                                    @if ($lot->status !== 'available')
                                        ({{ ucfirst($lot->status) }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('lot_id')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Amount <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">₱</span>
                            <input type="number" step="0.01" min="0" name="amount" class="form-control" value="{{ old('amount') }}" required>
                        </div>
                        @error('amount')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Due Date</label>
                        <input type="date" name="due_date" class="form-control" value="{{ old('due_date') }}">
                        @error('due_date')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Initial Status</label>
                        <select name="status" class="form-select">
                            @foreach (['pending' => 'Pending', 'paid' => 'Paid (if paid now)'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('status', 'pending') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('status')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Payment Date</label>
                        <input type="date" name="payment_date" class="form-control" value="{{ old('payment_date', now()->toDateString()) }}">
                        @error('payment_date')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Payment Method</label>
                        <select name="method" class="form-select">
                            <option value="">Select method</option>
                            @foreach (['cash' => 'Cash', 'bank' => 'Bank Transfer', 'gcash' => 'GCash', 'card' => 'Card', 'check' => 'Check', 'other' => 'Other'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('method') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('method')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Reference Number</label>
                        <input type="text" name="reference_number" class="form-control" value="{{ old('reference_number') }}" placeholder="Optional">
                        @error('reference_number')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Notes</label>
                        <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <hr class="my-3">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success">
                                <i data-feather="save" class="me-1" style="height: 14px; width: 14px;"></i>
                                Create Payment
                            </button>
                            <a href="{{ route('admin.lot-payments.index') }}" class="btn btn-light">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
