<div class="row">
    <div class="col-12">
        <div class="card mb-0">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">Maintenance Records</h5>
                @if (auth()->check() && auth()->user()->role === 'master_admin')
                    <span class="badge bg-primary">Master Admin Only</span>
                @endif
            </div>
            <div class="card-body">
                @if (auth()->check() && auth()->user()->role === 'master_admin')
                    <form method="POST" action="{{ route('admin.clients.maintenance.store', $client) }}" class="row g-2 align-items-end mb-3">
                        @csrf
                        <div class="col-md-3">
                            @php($maintenanceLotListId = 'maintenanceLots_'.$client->id)
                            <div class="form-floating">
                                <input
                                    type="text"
                                    id="maintenance_lot_id_{{ $client->id }}"
                                    name="lot_id"
                                    class="form-control"
                                    placeholder="Lot ID"
                                    list="{{ $maintenanceLotListId }}"
                                    value="{{ old('lot_id') }}"
                                >
                                <label for="maintenance_lot_id_{{ $client->id }}">
                                    <i data-feather="search" class="me-1" aria-hidden="true"></i>
                                    Lot ID (optional)
                                </label>
                            </div>
                            <datalist id="{{ $maintenanceLotListId }}">
                                @foreach ($client->lotOwnerships as $ownership)
                                    @if ($ownership->lot)
                                        <option value="{{ $ownership->lot->lot_id }}">{{ $ownership->lot->lot_id }} - {{ $ownership->lot->name }} ({{ $ownership->lot->lot_category_label }})</option>
                                    @endif
                                @endforeach
                            </datalist>
                        </div>
                        <div class="col-md-2">
                            <div class="form-floating">
                                <select id="maintenance_service_type_{{ $client->id }}" name="service_type" class="form-select" required>
                                    @foreach (['general' => 'General', 'cleaning' => 'Cleaning', 'landscaping' => 'Landscaping', 'repair' => 'Repair', 'flowers' => 'Flowers', 'other' => 'Other'] as $value => $label)
                                        <option value="{{ $value }}" @selected(old('service_type', 'general') === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <label for="maintenance_service_type_{{ $client->id }}">Service</label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-floating">
                                <select id="maintenance_status_{{ $client->id }}" name="status" class="form-select" required>
                                    @foreach (['scheduled' => 'Scheduled', 'completed' => 'Completed', 'cancelled' => 'Cancelled'] as $value => $label)
                                        <option value="{{ $value }}" @selected(old('status', 'scheduled') === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <label for="maintenance_status_{{ $client->id }}">Status</label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-floating">
                                <input
                                    type="date"
                                    id="maintenance_service_date_{{ $client->id }}"
                                    name="service_date"
                                    class="form-control"
                                    value="{{ old('service_date') }}"
                                    placeholder="YYYY-MM-DD"
                                    required
                                >
                                <label for="maintenance_service_date_{{ $client->id }}">Service Date</label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-floating">
                                <input
                                    type="number"
                                    id="maintenance_amount_{{ $client->id }}"
                                    name="amount"
                                    class="form-control"
                                    value="{{ old('amount') }}"
                                    min="0"
                                    step="0.01"
                                    placeholder="0.00"
                                >
                                <label for="maintenance_amount_{{ $client->id }}">Amount</label>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <button type="submit" class="btn btn-success w-100">Save</button>
                        </div>
                        <div class="col-12">
                            <input
                                type="text"
                                name="notes"
                                class="form-control"
                                value="{{ old('notes') }}"
                                placeholder="Notes (optional)"
                            >
                        </div>
                    </form>
                @else
                    <div class="alert alert-info mb-3">
                        Only Master Admin can add or delete maintenance records.
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Lot</th>
                                <th>Service</th>
                                <th>Status</th>
                                <th class="text-end">Amount</th>
                                <th>Notes</th>
                                <th>Created By</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($client->maintenanceRecords as $record)
                                <tr>
                                    <td class="text-nowrap">{{ $record->service_date?->format('Y-m-d') ?? '-' }}</td>
                                    <td class="text-nowrap">
                                        @if ($record->lot)
                                            {{ $record->lot->lot_id }} - {{ $record->lot->name }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-capitalize">{{ str_replace('_', ' ', $record->service_type) }}</td>
                                    <td class="text-capitalize">{{ $record->status }}</td>
                                    <td class="text-nowrap text-end">
                                        @if ($record->amount !== null)
                                            {{ number_format((float) $record->amount, 2) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-muted">{{ $record->notes ?: '-' }}</td>
                                    <td class="text-nowrap">{{ $record->creator?->name ?? '-' }}</td>
                                    <td class="text-end">
                                        @if (auth()->check() && auth()->user()->role === 'master_admin')
                                            <form method="POST" action="{{ route('admin.clients.maintenance.destroy', [$client, $record]) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this maintenance record?')">Delete</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            @if ($client->maintenanceRecords->isEmpty())
                                <tr>
                                    <td colspan="8" class="text-muted text-center py-3">No maintenance records yet.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
