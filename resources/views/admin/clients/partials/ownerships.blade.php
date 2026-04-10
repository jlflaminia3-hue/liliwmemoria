<div class="row">
    <div class="col-12">
        <div class="card mb-0">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">Ownership Records</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-light border mb-3">
                    <div class="fw-semibold">Ownership is automated</div>
                    <div class="text-muted small">Ownership records are created/updated automatically when you create reservations/contracts. Manual ownership creation is disabled.</div>
                </div>

                <style>
                    /* Give extra scroll room so row action dropdowns aren't clipped inside scroll containers. */
                    .ownerships-table-scroll {
                        padding-bottom: 180px;
                    }
                </style>

                <div class="table-responsive ownerships-table-scroll">
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
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Ownership actions">
                                                <i data-feather="more-vertical"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <form method="POST" action="{{ route('admin.clients.ownerships.destroy', [$client, $ownership]) }}" class="dropdown-item p-0">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-link dropdown-item text-danger m-0" onclick="return confirm('Remove this ownership record?')">
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
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
