<div class="row">
    <div class="col-12">
        <div class="card mb-0">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">Contracts / Agreements</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.clients.contracts.store', $client) }}" class="row g-2 align-items-end mb-3">
                    @csrf
                    <div class="col-md-2">
                        <div class="form-floating">
                            <select id="contract_type_{{ $client->id }}" name="contract_type" class="form-select" required>
                                <option value="purchase" @selected(old('contract_type') === 'purchase')>Purchase</option>
                                <option value="reservation" @selected(old('contract_type') === 'reservation')>Reservation</option>
                                <option value="other" @selected(old('contract_type') === 'other')>Other</option>
                            </select>
                            <label for="contract_type_{{ $client->id }}">Contract Type</label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-floating">
                            <select id="contract_status_{{ $client->id }}" name="status" class="form-select" required>
                                <option value="active" @selected(old('status') === 'active')>Active</option>
                                <option value="pending" @selected(old('status') === 'pending')>Pending</option>
                                <option value="completed" @selected(old('status') === 'completed')>Completed</option>
                                <option value="cancelled" @selected(old('status') === 'cancelled')>Cancelled</option>
                                <option value="transfered" @selected(old('status') === 'transfered')>Transfered</option>
                            </select>
                            <label for="contract_status_{{ $client->id }}">Status</label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-floating">
                            <input
                                type="text"
                                id="contract_number_{{ $client->id }}"
                                name="contract_number"
                                class="form-control"
                                value="{{ old('contract_number') }}"
                                placeholder="Contract #"
                            >
                            <label for="contract_number_{{ $client->id }}">Contract No.</label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-floating">
                            <select id="contract_lot_kind_{{ $client->id }}" name="lot_kind" class="form-select">
                                <option value="" @selected(old('lot_kind') === null || old('lot_kind') === '')>Lot Category</option>
                                <option value="phase_1" @selected(old('lot_kind') === 'phase_1')>Phase 1</option>
                                <option value="phase_2" @selected(old('lot_kind') === 'phase_2')>Phase 2</option>
                                <option value="garden_lot" @selected(old('lot_kind') === 'garden_lot')>Garden Lot</option>
                                <option value="back_office_lot" @selected(old('lot_kind') === 'back_office_lot')>Back Office Lot</option>
                                <option value="mausoleum" @selected(old('lot_kind') === 'mausoleum')>Mausoleum</option>
                            </select>
                            <label for="contract_lot_kind_{{ $client->id }}">Lot Category</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        @php($contractLotListId = 'availableContractLots_'.$client->id)
                        <div class="input-group">
                            <div class="form-floating flex-grow-1">
                                <input
                                    type="text"
                                    id="contract_lot_id_{{ $client->id }}"
                                    name="contract_lot_id"
                                    class="form-control"
                                    placeholder="Related Lot ID"
                                    list="{{ $contractLotListId }}"
                                    value="{{ old('contract_lot_id') }}"
                                >
                                <label for="contract_lot_id_{{ $client->id }}">
                                    <i data-feather="search" class="me-1" aria-hidden="true"></i>
                                    Lot ID
                                </label>
                            </div>
                        </div>
                        <datalist id="{{ $contractLotListId }}">
                            @foreach ($availableLots as $lot)
                                <option value="{{ $lot->lot_id }}">{{ $lot->lot_id }} - {{ $lot->name }} ({{ $lot->lot_category_label }})</option>
                            @endforeach
                        </datalist>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating">
                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                id="contract_amount_{{ $client->id }}"
                                name="total_amount"
                                class="form-control"
                                value="{{ old('total_amount') }}"
                                placeholder="Contract Amount"
                            >
                            <label for="contract_amount_{{ $client->id }}">Contract Amount</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating">
                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                id="contract_down_payment_{{ $client->id }}"
                                name="amount_paid"
                                class="form-control"
                                value="{{ old('amount_paid') }}"
                                placeholder="Down Payment"
                            >
                            <label for="contract_down_payment_{{ $client->id }}">Down Payment</label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-floating">
                            <input
                                type="date"
                                id="contract_effective_{{ $client->id }}"
                                name="signed_at"
                                class="form-control"
                                value="{{ old('signed_at') }}"
                                placeholder="YYYY-MM-DD"
                            >
                            <label for="contract_effective_{{ $client->id }}">Effective</label>
                        </div>
                    </div>
                    <div class="col-md-2" id="contract_duration_col_{{ $client->id }}">
                        <div class="form-floating">
                            <select id="contract_duration_{{ $client->id }}" name="contract_duration_months" class="form-select">
                                <option value="" @selected(old('contract_duration_months') === null || old('contract_duration_months') === '')>Select duration</option>
                                <option value="12" @selected((string) old('contract_duration_months') === '12')>12 months</option>
                                <option value="18" @selected((string) old('contract_duration_months') === '18')>18 months</option>
                                <option value="24" @selected((string) old('contract_duration_months') === '24')>24 months</option>
                            </select>
                            <label for="contract_duration_{{ $client->id }}">Contract Duration</label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-floating">
                            <input
                                type="date"
                                id="contract_completion_{{ $client->id }}"
                                name="due_date"
                                class="form-control"
                                value="{{ old('due_date') }}"
                                placeholder="YYYY-MM-DD"
                            >
                            <label for="contract_completion_{{ $client->id }}">Completion</label>
                        </div>
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
                                <th>Lot Category</th>
                                <th>Lot</th>
                                <th>Contract Amount</th>
                                <th>Down Payment</th>
                                <th>Effective</th>
                                <th>Completion</th>
                                <th>Duration</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($client->contracts as $contract)
                                <tr>
                                    <td class="text-capitalize">{{ str_replace('-', ' ', $contract->contract_type) }}</td>
                                    <td class="text-capitalize">{{ str_replace('-', ' ', $contract->status) }}</td>
                                    <td>{{ $contract->contract_number ?? '-' }}</td>
                                    <td>{{ $contract->lot_kind ? ucwords(str_replace('_', ' ', $contract->lot_kind)) : '-' }}</td>
                                    <td>{{ $contract->lot ? ('Lot ID '.$contract->lot->lot_id) : '-' }}</td>
                                    <td>
                                        @if (is_null($contract->total_amount))
                                            -
                                        @else
                                            &#8369;{{ number_format((float) $contract->total_amount, 2) }}
                                        @endif
                                    </td>
                                    <td>
                                        @if (is_null($contract->amount_paid))
                                            -
                                        @else
                                            &#8369;{{ number_format((float) $contract->amount_paid, 2) }}
                                        @endif
                                    </td>
                                    <td>{{ $contract->signed_at?->format('Y-m-d') ?? '-' }}</td>
                                    <td>{{ $contract->due_date?->format('Y-m-d') ?? '-' }}</td>
                                    <td>{{ $contract->contract_duration_months ? ($contract->contract_duration_months.' months') : '-' }}</td>
                                    <td class="text-end">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Contract actions">
                                                <i data-feather="more-vertical"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#editContractModal_{{ $contract->id }}">
                                                    Edit
                                                </button>
                                                <form method="POST" action="{{ route('admin.clients.contracts.destroy', [$client, $contract]) }}" class="dropdown-item p-0">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-link dropdown-item text-danger m-0" onclick="return confirm('Delete this contract?')">
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @if ($contract->notes)
                                    <tr>
                                        <td colspan="11" class="text-muted">{{ $contract->notes }}</td>
                                    </tr>
                                @endif

                            @endforeach
                            @if ($client->contracts->isEmpty())
                                <tr>
                                    <td colspan="11" class="text-muted text-center py-3">No contracts yet.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                @foreach ($client->contracts as $contract)
                    <div class="modal fade" id="editContractModal_{{ $contract->id }}" tabindex="-1" aria-labelledby="editContractModalLabel_{{ $contract->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <form method="POST" action="{{ route('admin.clients.contracts.update', [$client, $contract]) }}">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="_modal" value="contract_edit">
                                    <input type="hidden" name="_contract_id" value="{{ $contract->id }}">
                                    @php($isEditingThis = old('_modal') === 'contract_edit' && (int) old('_contract_id') === $contract->id)

                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editContractModalLabel_{{ $contract->id }}">Edit Contract</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row g-2 align-items-end">
                                            <div class="col-md-3">
                                                <div class="form-floating">
                                                    <select name="contract_type" class="form-select" required>
                                                        @php($contractTypeValue = $isEditingThis ? old('contract_type') : $contract->contract_type)
                                                        <option value="purchase" @selected($contractTypeValue === 'purchase')>Purchase</option>
                                                        <option value="reservation" @selected($contractTypeValue === 'reservation')>Reservation</option>
                                                        <option value="other" @selected($contractTypeValue === 'other')>Other</option>
                                                    </select>
                                                    <label>Contract Type</label>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-floating">
                                                    <select name="status" class="form-select" required>
                                                        @php($statusValue = $isEditingThis ? old('status') : $contract->status)
                                                        <option value="active" @selected($statusValue === 'active')>Active</option>
                                                        <option value="pending" @selected($statusValue === 'pending')>Pending</option>
                                                        <option value="completed" @selected($statusValue === 'completed')>Completed</option>
                                                        <option value="cancelled" @selected($statusValue === 'cancelled')>Cancelled</option>
                                                        <option value="transfered" @selected($statusValue === 'transfered')>Transfered</option>
                                                    </select>
                                                    <label>Status</label>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-floating">
                                                    <input
                                                        type="text"
                                                        name="contract_number"
                                                        class="form-control"
                                                        placeholder="Contract #"
                                                        value="{{ $isEditingThis ? old('contract_number') : $contract->contract_number }}"
                                                    >
                                                    <label>Contract #</label>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-floating">
                                                    <select name="lot_kind" class="form-select">
                                                        @php($lotKindValue = $isEditingThis ? old('lot_kind') : $contract->lot_kind)
                                                        <option value="" @selected($lotKindValue === null || $lotKindValue === '')>Lot Category (optional)</option>
                                                        <option value="phase_1" @selected($lotKindValue === 'phase_1')>Phase 1</option>
                                                        <option value="phase_2" @selected($lotKindValue === 'phase_2')>Phase 2</option>
                                                        <option value="garden_lot" @selected($lotKindValue === 'garden_lot')>Garden Lot</option>
                                                        <option value="back_office_lot" @selected($lotKindValue === 'back_office_lot')>Back Office Lot</option>
                                                        <option value="mausoleum" @selected($lotKindValue === 'mausoleum')>Mausoleum</option>
                                                    </select>
                                                    <label>Lot Category</label>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                @php($editLotListId = 'availableContractLots_edit_'.$contract->id)
                                                <div class="form-floating">
                                                    <input
                                                        type="text"
                                                        name="contract_lot_id"
                                                        class="form-control"
                                                        placeholder="Related Lot ID"
                                                        list="{{ $editLotListId }}"
                                                        value="{{ $isEditingThis ? old('contract_lot_id') : ($contract->lot?->lot_id ?? '') }}"
                                                    >
                                                    <label>Related Lot</label>
                                                </div>
                                                <datalist id="{{ $editLotListId }}">
                                                    @foreach ($availableLots as $lot)
                                                        <option value="{{ $lot->lot_id }}">{{ $lot->lot_id }} - {{ $lot->name }} ({{ $lot->lot_category_label }})</option>
                                                    @endforeach
                                                    @if ($contract->lot && ! $availableLots->contains('id', $contract->lot_id))
                                                        <option value="{{ $contract->lot->lot_id }}">{{ $contract->lot->lot_id }} - {{ $contract->lot->name }} ({{ $contract->lot->lot_category_label }}) (current)</option>
                                                    @endif
                                                </datalist>
                                                <div class="form-text">Leave blank to unlink the lot.</div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-floating">
                                                    <input
                                                        type="number"
                                                        step="0.01"
                                                        min="0"
                                                        name="total_amount"
                                                        class="form-control"
                                                        placeholder="Contract Amount"
                                                        value="{{ $isEditingThis ? old('total_amount') : $contract->total_amount }}"
                                                    >
                                                    <label>Contract Amount</label>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-floating">
                                                    <input
                                                        type="number"
                                                        step="0.01"
                                                        min="0"
                                                        name="amount_paid"
                                                        class="form-control"
                                                        placeholder="Down Payment"
                                                        value="{{ $isEditingThis ? old('amount_paid') : $contract->amount_paid }}"
                                                    >
                                                    <label>Down Payment</label>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-floating">
                                                    <input
                                                        type="date"
                                                        name="signed_at"
                                                        class="form-control"
                                                        placeholder="YYYY-MM-DD"
                                                        value="{{ $isEditingThis ? old('signed_at') : ($contract->signed_at?->format('Y-m-d') ?? '') }}"
                                                    >
                                                    <label>Effective</label>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-floating">
                                                    <select name="contract_duration_months" class="form-select">
                                                        @php($durationValue = $isEditingThis ? (string) old('contract_duration_months') : (string) ($contract->contract_duration_months ?? ''))
                                                        <option value="" @selected($durationValue === '')>Select duration</option>
                                                        <option value="12" @selected($durationValue === '12')>12 months</option>
                                                        <option value="18" @selected($durationValue === '18')>18 months</option>
                                                        <option value="24" @selected($durationValue === '24')>24 months</option>
                                                    </select>
                                                    <label>Contract Duration</label>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-floating">
                                                    <input
                                                        type="date"
                                                        name="due_date"
                                                        class="form-control"
                                                        placeholder="YYYY-MM-DD"
                                                        value="{{ $isEditingThis ? old('due_date') : ($contract->due_date?->format('Y-m-d') ?? '') }}"
                                                    >
                                                    <label>Completion</label>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-floating">
                                                    <input
                                                        type="text"
                                                        name="notes"
                                                        class="form-control"
                                                        placeholder="Notes"
                                                        value="{{ $isEditingThis ? old('notes') : $contract->notes }}"
                                                    >
                                                    <label>Notes (optional)</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary">Save Changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach

                @if ($errors->any() && old('_modal') === 'contract_edit' && old('_contract_id'))
                    <script>
                        bootstrap.Modal.getOrCreateInstance(document.getElementById('editContractModal_{{ old('_contract_id') }}')).show();
                    </script>
                @endif

                <script>
                    (function () {
                        const contractTypeEl = document.getElementById('contract_type_{{ $client->id }}');
                        const effectiveEl = document.getElementById('contract_effective_{{ $client->id }}');
                        const durationEl = document.getElementById('contract_duration_{{ $client->id }}');
                        const completionEl = document.getElementById('contract_completion_{{ $client->id }}');
                        const durationColEl = document.getElementById('contract_duration_col_{{ $client->id }}');
                        const lotSearchBtnEl = document.getElementById('contract_lot_search_btn_{{ $client->id }}');
                        const lotNumberEl = document.getElementById('contract_lot_id_{{ $client->id }}');

                        if (!contractTypeEl || !effectiveEl || !durationEl || !completionEl) {
                            return;
                        }

                        function isReservation() {
                            return contractTypeEl.value === 'reservation';
                        }

                        function pad2(n) {
                            return String(n).padStart(2, '0');
                        }

                        function daysInMonth(year, month) {
                            return new Date(year, month, 0).getDate();
                        }

                        function parseYmd(value) {
                            if (!value) return null;
                            const parts = value.split('-').map(Number);
                            if (parts.length !== 3) return null;
                            const [year, month, day] = parts;
                            if (!year || !month || !day) return null;
                            return { year, month, day };
                        }

                        function formatYmd(dateParts) {
                            return `${dateParts.year}-${pad2(dateParts.month)}-${pad2(dateParts.day)}`;
                        }

                        function addMonthsNoOverflow(dateParts, monthsToAdd) {
                            const totalMonths = (dateParts.month - 1) + monthsToAdd;
                            const year = dateParts.year + Math.floor(totalMonths / 12);
                            const month = (totalMonths % 12) + 1;
                            const day = Math.min(dateParts.day, daysInMonth(year, month));
                            return { year, month, day };
                        }

                        function syncReservationUi() {
                            const enabled = isReservation();
                            durationEl.disabled = !enabled;
                            durationEl.required = enabled;

                            if (!enabled) {
                                durationEl.value = '';
                            }

                            if (durationColEl) {
                                durationColEl.classList.toggle('opacity-50', !enabled);
                            }
                        }

                        function syncCompletionDate() {
                            if (!isReservation()) return;

                            const effective = parseYmd(effectiveEl.value);
                            const months = parseInt(durationEl.value, 10);
                            if (!effective || !months) return;

                            completionEl.value = formatYmd(addMonthsNoOverflow(effective, months));
                        }

                        contractTypeEl.addEventListener('change', function () {
                            syncReservationUi();
                            syncCompletionDate();
                        });
                        effectiveEl.addEventListener('change', syncCompletionDate);
                        durationEl.addEventListener('change', syncCompletionDate);

                        if (lotSearchBtnEl && lotNumberEl) {
                            lotSearchBtnEl.addEventListener('click', function () {
                                lotNumberEl.focus();
                            });
                        }

                        syncReservationUi();
                        syncCompletionDate();
                    })();
                </script>
            </div>
        </div>
    </div>
</div>
