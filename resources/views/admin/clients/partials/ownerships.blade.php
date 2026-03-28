<div class="row">
    <div class="col-12">
        <div class="card mb-0">
            <div class="card-body">
                <h5 class="card-title mb-3">Ownership Records</h5>

                <form method="POST" action="{{ route('admin.clients.ownerships.store', $client) }}" class="row g-2 mb-3">
                    @csrf
                    <div class="col-md-5">
                        @php($lotListId = 'availableLots_'.$client->id)
                        <input
                            type="text"
                            name="lot_number"
                            class="form-control"
                            placeholder="Lot number (type to search)"
                            list="{{ $lotListId }}"
                            inputmode="numeric"
                            value="{{ old('lot_number') }}"
                            required
                        >
                        <datalist id="{{ $lotListId }}">
                            @foreach ($availableLots as $lot)
                                <option value="{{ $lot->lot_number }}">Lot #{{ $lot->lot_number }} - {{ $lot->name }}{{ $lot->section ? ' ('.$lot->section.')' : '' }}</option>
                            @endforeach
                        </datalist>
                        <div class="form-text">Start typing a lot number to see suggestions.</div>
                    </div>
                    <div class="col-md-3">
                        <select name="ownership_type" class="form-select">
                            <option value="owner" @selected(old('ownership_type') === 'owner')>Owner</option>
                            <option value="co-owner" @selected(old('ownership_type') === 'co-owner')>Co-owner</option>
                            <option value="authorized" @selected(old('ownership_type') === 'authorized')>Authorized</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="started_at" class="form-control" value="{{ old('started_at') }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-success w-100">Save</button>
                    </div>
                    <div class="col-12">
                        <div class="row g-2">
                            <div class="col-md-3">
                                <input type="date" name="ended_at" class="form-control" value="{{ old('ended_at') }}">
                            </div>
                            <div class="col-md-9">
                                <input type="text" name="notes" class="form-control" value="{{ old('notes') }}" placeholder="Notes (optional)">
                            </div>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Lot</th>
                                <th>Type</th>
                                <th>Start</th>
                                <th>End</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($client->lotOwnerships as $ownership)
                                <tr>
                                    <td>
                                        @if ($ownership->lot)
                                            Lot #{{ $ownership->lot->lot_number }} - {{ $ownership->lot->name }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $ownership->ownership_type }}</td>
                                    <td>{{ $ownership->started_at?->format('Y-m-d') ?? '-' }}</td>
                                    <td>{{ $ownership->ended_at?->format('Y-m-d') ?? '-' }}</td>
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
                                        <td colspan="5" class="text-muted">{{ $ownership->notes }}</td>
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
