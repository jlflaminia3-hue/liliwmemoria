<div class="row">
    <div class="col-12">
        <div class="card mb-0">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">Ownership Records</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.clients.ownerships.store', $client) }}" class="row g-2 align-items-end mb-3">
                    @csrf
                    <div class="col-md-4">
                        @php($lotListId = 'availableLots_'.$client->id)
                        <div class="form-floating">
                            <input
                                type="text"
                                id="ownership_lot_number_{{ $client->id }}"
                                name="lot_id"
                                class="form-control"
                                placeholder="Lot ID"
                                list="{{ $lotListId }}"
                                value="{{ old('lot_id') }}"
                                required
                            >
                            <label for="ownership_lot_number_{{ $client->id }}">
                                <i data-feather="search" class="me-1" aria-hidden="true"></i>
                                Lot ID
                            </label>
                        </div>
                        <datalist id="{{ $lotListId }}">
                            @foreach ($availableLots as $lot)
                                <option value="{{ $lot->lot_id }}">{{ $lot->lot_id }} - {{ $lot->name }} ({{ $lot->lot_category_label }})</option>
                            @endforeach
                        </datalist>
                    </div>
                    <div class="col-md-2">
                        <div class="form-floating">
                            <select id="ownership_type_{{ $client->id }}" name="ownership_type" class="form-select">
                                <option value="owner" @selected(old('ownership_type') === 'owner')>Owner</option>
                                <option value="co-owner" @selected(old('ownership_type') === 'co-owner')>Co-owner</option>
                                <option value="authorized" @selected(old('ownership_type') === 'authorized')>Authorized</option>
                            </select>
                            <label for="ownership_type_{{ $client->id }}">Ownership Type</label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-floating">
                            <input
                                type="date"
                                id="ownership_started_at_{{ $client->id }}"
                                name="started_at"
                                class="form-control"
                                value="{{ old('started_at') }}"
                                placeholder="YYYY-MM-DD"
                                aria-label="Ownership Date"
                            >
                            <label for="ownership_started_at_{{ $client->id }}">Ownership Date</label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-floating">
                            <input
                                type="date"
                                id="ownership_ended_at_{{ $client->id }}"
                                name="ended_at"
                                class="form-control"
                                value="{{ old('ended_at') }}"
                                placeholder="YYYY-MM-DD"
                                aria-label="Reservation End"
                            >
                            <label for="ownership_ended_at_{{ $client->id }}">Reservation End</label>
                        </div>
                    </div>
                    <div class="col-md-2">
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

                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Lot</th>
                                <th>Type</th>
                                <th>Ownership Date</th>
                                <th>Reservation End</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($client->lotOwnerships as $ownership)
                                <tr>
                                    <td>
                                        @if ($ownership->lot)
                                            {{ $ownership->lot->lot_id }} - {{ $ownership->lot->name }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-capitalize">{{ str_replace('-', ' ', $ownership->ownership_type) }}</td>
                                    <td class="text-nowrap">{{ $ownership->started_at?->format('Y-m-d') ?? '-' }}</td>
                                    <td class="text-nowrap">{{ $ownership->ended_at?->format('Y-m-d') ?? '-' }}</td>
                                    <td class="text-end">
                                        <form method="POST" action="{{ route('admin.clients.ownerships.destroy', [$client, $ownership]) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Remove this ownership record?')">Remove</button>
                                        </form>
                                    </td>
                                </tr>
                                @if ($ownership->notes)
                                    <tr>
                                        <td colspan="5" class="text-muted">
                                            <small>{{ $ownership->notes }}</small>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                            @if ($client->lotOwnerships->isEmpty())
                                <tr>
                                    <td colspan="5" class="text-muted text-center py-3">No ownership records yet.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
