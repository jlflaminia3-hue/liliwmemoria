<div class="row">
    <div class="col-12">
        <div class="card mb-0">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">Ownership Records</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-light border mb-3">
                    <div class="fw-semibold">Ownership is automated</div>
                    <div class="text-muted small">Ownership records are created/updated automatically when you create reservations/contracts. Manual ownership creation is disabled (use Transfer for ownership changes).</div>
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
                                                <button
                                                    type="button"
                                                    class="dropdown-item js-transfer-ownership"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#transferOwnershipModal"
                                                    data-ownership-id="{{ $ownership->id }}"
                                                    data-lot-label="{{ $ownership->lot ? ($ownership->lot->lot_id.' - '.$ownership->lot->name) : '-' }}"
                                                    @disabled(! $ownership->lot || empty($otherClients) || count($otherClients) === 0)
                                                >
                                                    Transfer
                                                </button>
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

<!-- Transfer Ownership Modal -->
<div class="modal fade" id="transferOwnershipModal" tabindex="-1" aria-labelledby="transferOwnershipModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form
                id="transferOwnershipForm"
                method="POST"
                data-action-template="{{ route('admin.clients.ownerships.transfer', [$client, '__OWNERSHIP__']) }}"
            >
                @csrf
                <input type="hidden" name="_modal" value="transferOwnership">
                <input type="hidden" name="ownership_id" id="transfer_ownership_id" value="{{ old('ownership_id') }}">

                <div class="modal-header">
                    <h5 class="modal-title" id="transferOwnershipModalLabel">Transfer Lot Ownership</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="alert alert-light border">
                        <div class="fw-semibold">This updates the lot owner shown on the map</div>
                        <div class="text-muted small">Transfers are blocked when the lot has an active interment (not exhumed).</div>
                    </div>

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Lot</label>
                            <input type="text" class="form-control" id="transfer_lot_label" value="{{ old('lot_label', '') }}" readonly>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">New Owner</label>
                            <select class="form-select @error('new_client_id') is-invalid @enderror" name="new_client_id" required>
                                <option value="" @selected(old('new_client_id') === null || old('new_client_id') === '')>Select a client...</option>
                                @foreach (($otherClients ?? []) as $otherClient)
                                    <option value="{{ $otherClient->id }}" @selected((string) old('new_client_id') === (string) $otherClient->id)>
                                        {{ $otherClient->full_name ?? (trim(($otherClient->first_name ?? '').' '.($otherClient->last_name ?? '')) ?: ('Client #'.$otherClient->id)) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('new_client_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Effective Date</label>
                            <input
                                type="date"
                                class="form-control @error('effective_date') is-invalid @enderror"
                                name="effective_date"
                                value="{{ old('effective_date', now()->toDateString()) }}"
                            >
                            @error('effective_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Ownership Type</label>
                            <select class="form-select @error('ownership_type') is-invalid @enderror" name="ownership_type">
                                @php($typeOld = old('ownership_type', 'owner'))
                                <option value="owner" @selected($typeOld === 'owner')>Owner</option>
                                <option value="co-owner" @selected($typeOld === 'co-owner')>Co-owner</option>
                                <option value="authorized" @selected($typeOld === 'authorized')>Authorized</option>
                            </select>
                            @error('ownership_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Reason</label>
                            <select class="form-select @error('reason') is-invalid @enderror" name="reason">
                                @php($reasonOld = old('reason'))
                                <option value="" @selected($reasonOld === null || $reasonOld === '')>—</option>
                                <option value="transfer" @selected($reasonOld === 'transfer')>Transfer</option>
                                <option value="exhumation" @selected($reasonOld === 'exhumation')>Exhumation</option>
                            </select>
                            @error('reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label">Notes (optional)</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" name="notes" rows="3" placeholder="Add details (e.g., exhumed remains, family agreement, reference number)">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Transfer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    (function () {
        var modalEl = document.getElementById('transferOwnershipModal');
        if (!modalEl) return;

        var formEl = document.getElementById('transferOwnershipForm');
        var actionTemplate = formEl ? formEl.getAttribute('data-action-template') : null;
        var ownershipIdInput = document.getElementById('transfer_ownership_id');
        var lotLabelInput = document.getElementById('transfer_lot_label');

        function setFormAction(ownershipId) {
            if (!formEl || !actionTemplate) return;
            formEl.action = actionTemplate.replace('__OWNERSHIP__', String(ownershipId || ''));
        }

        modalEl.addEventListener('show.bs.modal', function (event) {
            var trigger = event.relatedTarget;
            if (!trigger) return;

            var ownershipId = trigger.getAttribute('data-ownership-id');
            var lotLabel = trigger.getAttribute('data-lot-label');

            if (ownershipIdInput) ownershipIdInput.value = ownershipId || '';
            if (lotLabelInput && lotLabel) lotLabelInput.value = lotLabel;

            setFormAction(ownershipId);
        });

        // If validation fails, re-open the modal and restore the action.
        var shouldReopen = @json($errors->any() && old('_modal') === 'transferOwnership');
        if (shouldReopen) {
            var oldOwnershipId = @json(old('ownership_id'));
            setFormAction(oldOwnershipId);

            var oldTrigger = document.querySelector('.js-transfer-ownership[data-ownership-id=\"' + String(oldOwnershipId || '') + '\"]');
            if (oldTrigger && lotLabelInput) {
                lotLabelInput.value = oldTrigger.getAttribute('data-lot-label') || '';
            }

            bootstrap.Modal.getOrCreateInstance(modalEl).show();
        }
    })();
</script>
