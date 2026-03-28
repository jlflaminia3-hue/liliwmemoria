<div class="row">
    <div class="col-12">
        <div class="card mb-0">
            <div class="card-body">
                <h5 class="card-title mb-3">Contracts / Agreements</h5>

                <form method="POST" action="{{ route('admin.clients.contracts.store', $client) }}" class="row g-2 mb-3">
                    @csrf
                    <div class="col-md-3">
                        <select name="contract_type" class="form-select" required>
                            <option value="purchase" @selected(old('contract_type') === 'purchase')>Purchase</option>
                            <option value="reservation" @selected(old('contract_type') === 'reservation')>Reservation</option>
                            <option value="other" @selected(old('contract_type') === 'other')>Other</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-select" required>
                            <option value="draft" @selected(old('status') === 'draft')>Draft</option>
                            <option value="active" @selected(old('status') === 'active')>Active</option>
                            <option value="past_due" @selected(old('status') === 'past_due')>Past Due</option>
                            <option value="completed" @selected(old('status') === 'completed')>Completed</option>
                            <option value="cancelled" @selected(old('status') === 'cancelled')>Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="contract_number" class="form-control" value="{{ old('contract_number') }}" placeholder="Contract # (optional)">
                    </div>
                    <div class="col-md-3">
                        <select name="lot_id" class="form-select">
                            <option value="">Related lot (optional)...</option>
                            @foreach ($lots as $lot)
                                <option value="{{ $lot->id }}" @selected(old('lot_id') == $lot->id)>
                                    Lot #{{ $lot->lot_number }} - {{ $lot->name }}{{ $lot->section ? ' ('.$lot->section.')' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="number" step="0.01" min="0" name="total_amount" class="form-control" value="{{ old('total_amount') }}" placeholder="Total">
                    </div>
                    <div class="col-md-3">
                        <input type="number" step="0.01" min="0" name="amount_paid" class="form-control" value="{{ old('amount_paid') }}" placeholder="Paid">
                    </div>
                    <div class="col-md-3">
                        <input type="date" name="due_date" class="form-control" value="{{ old('due_date') }}">
                    </div>
                    <div class="col-md-3">
                        <input type="date" name="signed_at" class="form-control" value="{{ old('signed_at') }}">
                    </div>
                    <div class="col-12">
                        <input type="text" name="notes" class="form-control" value="{{ old('notes') }}" placeholder="Notes (optional)">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-success">Add Contract</button>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Contract #</th>
                                <th>Lot</th>
                                <th>Total</th>
                                <th>Paid</th>
                                <th>Due</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($client->contracts as $contract)
                                <tr>
                                    <td>{{ $contract->contract_type }}</td>
                                    <td>{{ $contract->status }}</td>
                                    <td>{{ $contract->contract_number ?? '-' }}</td>
                                    <td>{{ $contract->lot ? 'Lot #'.$contract->lot->lot_number : '-' }}</td>
                                    <td>{{ $contract->total_amount ?? '-' }}</td>
                                    <td>{{ $contract->amount_paid ?? '-' }}</td>
                                    <td>{{ $contract->due_date?->format('Y-m-d') ?? '-' }}</td>
                                    <td class="text-end">
                                        <form method="POST" action="{{ route('admin.clients.contracts.destroy', [$client, $contract]) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this contract?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                @if ($contract->notes)
                                    <tr>
                                        <td colspan="8" class="text-muted">{{ $contract->notes }}</td>
                                    </tr>
                                @endif
                            @endforeach
                            @if ($client->contracts->isEmpty())
                                <tr>
                                    <td colspan="8" class="text-muted text-center py-3">No contracts yet.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

