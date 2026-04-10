@extends('admin.admin_master')

@section('admin')
@php
    $currentSearch = request('search', '');
    $currentStatus = request('status', '');
    $currentCompliance = request('compliance', 'all');
    $currentPerPage = (int) request('per_page', 20);
@endphp

<div class="content">
    <div class="container-xxl">
        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column gap-3">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">Interments</h4>
                <div class="text-muted mt-1">Track burial records, lot occupancy, document compliance, and client links in one place.</div>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('admin.lots.map') }}" class="btn btn-outline-secondary btn-sm">
                    <i data-feather="map" class="me-1" style="height: 16px; width: 16px;"></i>
                    Open Map
                </a>
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createIntermentModal">
                    <i data-feather="plus" class="me-1" style="height: 16px; width: 16px;"></i>
                    Add Interment
                </button>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row g-3 mb-3">
            <div class="col-md-6 col-xl">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase fw-semibold">All Records</div>
                        <div class="fs-3 fw-bold">{{ number_format($stats['total']) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase fw-semibold">Pending</div>
                        <div class="fs-3 fw-bold text-warning">{{ number_format($stats['pending']) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase fw-semibold">Confirmed</div>
                        <div class="fs-3 fw-bold text-success">{{ number_format($stats['confirmed']) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase fw-semibold">Exhumed</div>
                        <div class="fs-3 fw-bold text-secondary">{{ number_format($stats['exhumed']) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase fw-semibold">Needs Attention</div>
                        <div class="fs-3 fw-bold text-danger">{{ number_format($stats['missing_docs']) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.interments.index') }}" class="row g-3 align-items-end mb-4">
                    <div class="col-lg-4">
                        <label for="interment_search" class="form-label fw-semibold">Search</label>
                        <input id="interment_search" type="text" name="search" class="form-control" value="{{ $currentSearch }}" placeholder="Deceased, client, lot owner, section, or lot number">
                    </div>
                    <div class="col-md-4 col-lg-2">
                        <label for="interment_status_filter" class="form-label fw-semibold">Status</label>
                        <select id="interment_status_filter" name="status" class="form-select">
                            <option value="">All</option>
                            <option value="pending" @selected($currentStatus === 'pending')>Pending</option>
                            <option value="confirmed" @selected($currentStatus === 'confirmed')>Confirmed</option>
                            <option value="exhumed" @selected($currentStatus === 'exhumed')>Exhumed</option>
                        </select>
                    </div>
                    <div class="col-md-4 col-lg-3">
                        <label for="interment_compliance_filter" class="form-label fw-semibold">Compliance</label>
                        <select id="interment_compliance_filter" name="compliance" class="form-select">
                            <option value="all" @selected($currentCompliance === 'all')>All records</option>
                            <option value="missing" @selected($currentCompliance === 'missing')>Missing documents or links</option>
                            <option value="ready" @selected($currentCompliance === 'ready')>Ready / complete</option>
                        </select>
                    </div>
                    <div class="col-md-4 col-lg-1">
                        <label for="interment_per_page" class="form-label fw-semibold">Rows</label>
                        <select id="interment_per_page" name="per_page" class="form-select">
                            <option value="10" @selected($currentPerPage === 10)>10</option>
                            <option value="20" @selected($currentPerPage === 20)>20</option>
                            <option value="50" @selected($currentPerPage === 50)>50</option>
                            <option value="100" @selected($currentPerPage === 100)>100</option>
                        </select>
                    </div>
                    <div class="col-lg-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100">Apply</button>
                        <a href="{{ route('admin.interments.index') }}" class="btn btn-light w-100">Reset</a>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Deceased</th>
                                <th>Interment</th>
                                <th>Lot</th>
                                <th>Status</th>
                                <th>Client</th>
                                <th>Documents</th>
                                <th>Compliance</th>
                                <th class="text-end" style="width: 70px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($interments as $record)
                                @php($missingItems = $record->missingComplianceItems())
                                @php
                                    $recordJson = [
                                        'id' => $record->id,
                                        'client_id' => $record->client_id,
                                        'lot_id' => $record->lot_id,
                                        'first_name' => $record->first_name,
                                        'last_name' => $record->last_name,
                                        'date_of_birth' => $record->date_of_birth?->format('Y-m-d'),
                                        'date_of_death' => $record->date_of_death?->format('Y-m-d'),
                                        'burial_date' => $record->burial_date?->format('Y-m-d'),
                                        'status' => $record->status,
                                        'notes' => $record->notes,
                                        'has_existing_permit' => (bool) $record->burial_permit_path,
                                    ];
                                @endphp
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $record->full_name }}</div>
                                        <div class="text-muted small">Died {{ $record->date_of_death?->format('Y-m-d') ?? 'Not recorded' }}</div>
                                    </td>
                                    <td>
                                        <div>{{ $record->burial_date?->format('Y-m-d') ?? 'Pending schedule' }}</div>
                                        <div class="text-muted small">DOB {{ $record->date_of_birth?->format('Y-m-d') ?? '-' }}</div>
                                    </td>
                                    <td>
                                        @if ($record->lot)
                                            <div class="fw-semibold">{{ $record->lot->lot_id }}</div>
                                            <div class="text-muted small">{{ $record->lot->lot_category_label }}</div>
                                        @else
                                            <span class="text-muted">No lot</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($record->status === 'confirmed')
                                            <span class="badge bg-success-subtle text-success border border-success-subtle">Confirmed</span>
                                        @elseif ($record->status === 'exhumed')
                                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">Exhumed</span>
                                        @else
                                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle">Pending</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($record->client)
                                            <div>{{ $record->client->full_name }}</div>
                                        @else
                                            <span class="text-muted">No linked client</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            @if ($record->death_certificate_path)
                                                <a href="{{ route('admin.interments.documents.download', [$record, 'death_certificate']) }}" class="btn btn-soft-primary btn-sm">Death Cert</a>
                                            @endif
                                            @if ($record->burial_permit_path)
                                                <a href="{{ route('admin.interments.documents.download', [$record, 'burial_permit']) }}" class="btn btn-soft-success btn-sm">Permit</a>
                                            @endif
                                            @if ($record->interment_form_path)
                                                <a href="{{ route('admin.interments.documents.download', [$record, 'interment_form']) }}" class="btn btn-soft-secondary btn-sm">Other File</a>
                                            @endif
                                            @if (! $record->death_certificate_path && ! $record->burial_permit_path && ! $record->interment_form_path)
                                                <span class="text-muted small">No files</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if ($missingItems === [])
                                            <span class="text-success fw-semibold">Complete</span>
                                        @else
                                            <div class="text-danger fw-semibold">Missing {{ count($missingItems) }}</div>
                                            <div class="text-muted small">{{ implode(', ', $missingItems) }}</div>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i data-feather="more-vertical"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <button
                                                    type="button"
                                                    class="dropdown-item js-edit-interment"
                                                    data-record='@json($recordJson)'
                                                >
                                                    Edit
                                                </button>
                                                <form method="POST" action="{{ route('admin.interments.destroy', $record) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Delete this interment record?')">Delete</button>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">No interment records found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="pt-3 d-flex justify-content-end">
                    {{ $interments->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="createIntermentModal" tabindex="-1" aria-labelledby="createIntermentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <form method="POST" action="{{ route('admin.interments.store') }}" enctype="multipart/form-data" class="modal-content">
            @csrf
            <input type="hidden" name="_modal" value="create">
            <div class="modal-header">
                <h5 class="modal-title" id="createIntermentModalLabel">Add Interment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @include('admin.interments.partials.form-fields', ['idPrefix' => 'create_'])
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Interment</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="editIntermentModal" tabindex="-1" aria-labelledby="editIntermentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <form id="editIntermentForm" method="POST" action="" enctype="multipart/form-data" class="modal-content">
            @csrf
            @method('PUT')
            <input type="hidden" name="_modal" value="edit">
            <input type="hidden" name="_record_id" id="edit_record_id" value="">
            <div class="modal-header">
                <h5 class="modal-title" id="editIntermentModalLabel">Edit Interment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @include('admin.interments.partials.form-fields', ['idPrefix' => 'edit_'])
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
    (function () {
        var editButtons = document.querySelectorAll('.js-edit-interment');
        var editForm = document.getElementById('editIntermentForm');

        function optionData(selectEl) {
            return Array.from(selectEl.options)
                .filter(function (opt, idx) { return idx !== 0 && opt.value; })
                .map(function (opt) { return { value: opt.value, text: opt.text }; });
        }

        function setLotPickerValue(selectEl, inputEl, value) {
            selectEl.value = value || '';
            var selected = selectEl.selectedOptions && selectEl.selectedOptions[0];
            inputEl.value = (selected && selected.value) ? selected.text : '';
        }

        function renderLotMenu(menuEl, items, onPick) {
            menuEl.innerHTML = '';

            if (!items.length) {
                var empty = document.createElement('div');
                empty.className = 'dropdown-item text-muted';
                empty.textContent = 'No matching lots';
                menuEl.appendChild(empty);
                return;
            }

            items.slice(0, 80).forEach(function (item) {
                var btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'dropdown-item';
                btn.textContent = item.text;
                btn.addEventListener('click', function () { onPick(item); });
                menuEl.appendChild(btn);
            });
        }

        function showLotMenu(wrapper, inputEl, menuEl) {
            menuEl.classList.add('show');
            inputEl.setAttribute('aria-expanded', 'true');
            wrapper.classList.add('is-open');
        }

        function hideLotMenu(wrapper, inputEl, menuEl) {
            menuEl.classList.remove('show');
            inputEl.setAttribute('aria-expanded', 'false');
            wrapper.classList.remove('is-open');
        }

        function initLotPickers(scope) {
            var wrappers = scope.querySelectorAll('.js-lot-picker');
            wrappers.forEach(function (wrapper) {
                var inputEl = wrapper.querySelector('.js-lot-picker-input');
                var selectEl = wrapper.querySelector('.js-lot-picker-select');
                var menuEl = wrapper.querySelector('.js-lot-picker-menu');
                if (!inputEl || !selectEl || !menuEl) return;

                var all = optionData(selectEl);

                // Prefill input from current select value.
                setLotPickerValue(selectEl, inputEl, selectEl.value);

                function refreshMenu() {
                    var q = (inputEl.value || '').trim().toLowerCase();
                    var filtered = q === ''
                        ? all
                        : all.filter(function (it) { return it.text.toLowerCase().indexOf(q) !== -1; });

                    renderLotMenu(menuEl, filtered, function (picked) {
                        setLotPickerValue(selectEl, inputEl, picked.value);
                        hideLotMenu(wrapper, inputEl, menuEl);
                    });
                }

                inputEl.addEventListener('focus', function () {
                    refreshMenu();
                    showLotMenu(wrapper, inputEl, menuEl);
                });

                inputEl.addEventListener('click', function () {
                    refreshMenu();
                    showLotMenu(wrapper, inputEl, menuEl);
                });

                inputEl.addEventListener('input', function () {
                    refreshMenu();
                    showLotMenu(wrapper, inputEl, menuEl);
                });

                inputEl.addEventListener('keydown', function (e) {
                    if (e.key === 'Escape') {
                        hideLotMenu(wrapper, inputEl, menuEl);
                    }
                });

                // Hide when clicking outside.
                document.addEventListener('click', function (e) {
                    if (wrapper.contains(e.target)) return;
                    hideLotMenu(wrapper, inputEl, menuEl);
                });
            });
        }

        function syncStatusRequirements(scope) {
            var statusInput = scope.querySelector('.js-interment-status');
            var burialDateInput = scope.querySelector('.js-burial-date');
            var burialPermitInput = scope.querySelector('.js-burial-permit');

            if (!statusInput || !burialDateInput || !burialPermitInput) {
                return;
            }

            var confirmed = statusInput.value === 'confirmed';
            burialDateInput.required = confirmed;
            burialPermitInput.required = confirmed && !burialPermitInput.dataset.hasExisting;
        }

        document.querySelectorAll('#createIntermentModal, #editIntermentModal').forEach(function (modal) {
            initLotPickers(modal);
            modal.querySelectorAll('.js-interment-status').forEach(function (statusInput) {
                statusInput.addEventListener('change', function () {
                    syncStatusRequirements(modal);
                });
                syncStatusRequirements(modal);
            });
        });

        editButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                if (!editForm) {
                    return;
                }

                var record = JSON.parse(button.getAttribute('data-record') || '{}');
                editForm.action = "{{ url('admin/interments') }}/" + record.id;

                var setValue = function (id, value) {
                    var input = document.getElementById(id);
                    if (!input) {
                        return;
                    }
                    input.value = value || '';
                };

                setValue('edit_record_id', record.id);
                setValue('edit_first_name', record.first_name);
                setValue('edit_last_name', record.last_name);
                setValue('edit_client_id', record.client_id);
                setValue('edit_lot_id', record.lot_id);
                setValue('edit_date_of_birth', record.date_of_birth);
                setValue('edit_date_of_death', record.date_of_death);
                setValue('edit_burial_date', record.burial_date);
                setValue('edit_status', record.status);
                setValue('edit_notes', record.notes);

                var permitInput = document.getElementById('edit_burial_permit');
                if (permitInput) {
                    permitInput.value = '';
                    permitInput.dataset.hasExisting = record.has_existing_permit ? '1' : '';
                }

                var modalEl = document.getElementById('editIntermentModal');
                initLotPickers(modalEl);
                syncStatusRequirements(modalEl);
                bootstrap.Modal.getOrCreateInstance(modalEl).show();
            });
        });
    })();
</script>

@if ($errors->any() && old('_modal') === 'create')
    <script>
        bootstrap.Modal.getOrCreateInstance(document.getElementById('createIntermentModal')).show();
    </script>
@endif

@if ($errors->any() && old('_modal') === 'edit')
    <script>
        bootstrap.Modal.getOrCreateInstance(document.getElementById('editIntermentModal')).show();
    </script>
@endif
@endsection
