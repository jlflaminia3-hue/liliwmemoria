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
                    Map
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
                        <div class="text-muted small text-uppercase fw-semibold">Unpaid</div>
                        <div class="fs-3 fw-bold text-danger">{{ number_format($stats['unpaid']) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase fw-semibold">Fully Paid</div>
                        <div class="fs-3 fw-bold text-success">{{ number_format($stats['fully_paid']) }}</div>
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
                                <th>Payment</th>
                                <th>Contract</th>
                                <th class="text-end" style="width: 70px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($interments as $record)
                                @php
                                    $missingItems = $record->missingComplianceItems();
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
                                        <a href="{{ route('admin.interments.show', $record) }}" class="text-decoration-none">
                                            <div class="fw-semibold text-dark">{{ $record->full_name }}</div>
                                            <div class="text-muted small">{{ $record->interment_number ?? 'INT-' . $record->id }}</div>
                                        </a>
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
                                        @php
                                            $paymentClass = match($record->payment_status) {
                                                'fully_paid' => 'success',
                                                'partial' => 'warning',
                                                default => 'danger'
                                            };
                                            $paymentBgClass = match($record->payment_status) {
                                                'fully_paid' => 'bg-success-subtle text-success border-success-subtle',
                                                'partial' => 'bg-warning-subtle text-warning border-warning-subtle',
                                                default => 'bg-danger-subtle text-danger border-danger-subtle'
                                            };
                                        @endphp
                                        <span class="badge {{ $paymentBgClass }}">{{ $record->payment_status_label }}</span>
<<<<<<< HEAD
                                        <div class="text-muted small mt-1">
                                            ₱{{ number_format((float) $record->total_paid, 2) }} / ₱{{ number_format((float) ($record->interment_fee ?? 15000), 2) }}
                                        </div>
=======
                                        @if ($record->interment_fee)
                                            <div class="text-muted small mt-1">
                                                ₱{{ number_format((float) ($record->payment_before_excavation ?? 0) + (float) ($record->payment_after_interment ?? 0), 2) }} / ₱{{ number_format((float) $record->interment_fee, 2) }}
                                            </div>
                                        @endif
>>>>>>> parent of f08d954 (lassst)
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            @if ($record->contract_path)
                                                <a href="{{ route('admin.interments.contract.download', $record) }}" class="btn btn-soft-dark btn-sm">
                                                    <i data-feather="download" class="me-1" style="height: 12px; width: 12px;"></i>
                                                    Download
                                                </a>
                                                @if ($record->client?->email)
                                                    <form method="POST" action="{{ route('admin.interments.contract.send', $record) }}" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-soft-primary btn-sm">
                                                            <i data-feather="send" class="me-1" style="height: 12px; width: 12px;"></i>
                                                            Email
                                                        </button>
                                                    </form>
                                                @endif
                                            @else
                                                <span class="badge bg-secondary-subtle text-secondary">No Contract</span>
                                                @if ($record->payment_status === 'fully_paid')
                                                    <span class="text-muted small">Complete payment to generate</span>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i data-feather="more-vertical"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                @if ($record->latestExhumation)
                                                    <a class="dropdown-item" href="{{ route('admin.exhumations.show', $record->latestExhumation) }}">Exhumation Case</a>
                                                @else
                                                    <form method="POST" action="{{ route('admin.exhumations.store', $record) }}" class="dropdown-item p-0">
                                                        @csrf
                                                        <button type="submit" class="btn btn-link dropdown-item m-0">Start Exhumation</button>
                                                    </form>
                                                @endif
                                                @if ($record->death_certificate_path)
                                                    <a class="dropdown-item" href="{{ route('admin.interments.documents.download', [$record, 'death_certificate']) }}">
                                                        <i data-feather="file" class="me-1" style="height: 14px; width: 14px;"></i>
                                                        Download Death Cert
                                                    </a>
                                                @endif
                                                @if ($record->burial_permit_path)
                                                    <a class="dropdown-item" href="{{ route('admin.interments.documents.download', [$record, 'burial_permit']) }}">
                                                        <i data-feather="file" class="me-1" style="height: 14px; width: 14px;"></i>
                                                        Download Permit
                                                    </a>
                                                @endif
                                                <a class="dropdown-item" href="{{ route('admin.interments.show', $record) }}">
                                                    <i data-feather="file-text" class="me-1" style="height: 14px; width: 14px;"></i>
                                                    View / Payments
                                                </a>
                                                <button
                                                    type="button"
                                                    class="dropdown-item js-record-payment"
                                                    data-record-id="{{ $record->id }}"
                                                    data-record-name="{{ $record->full_name }}"
                                                    data-payment-status="{{ $record->payment_status }}"
                                                    data-payment-before="{{ $record->payment_before_excavation ?? 0 }}"
                                                    data-payment-after="{{ $record->payment_after_interment ?? 0 }}"
                                                    data-interment-fee="{{ $record->interment_fee ?? 15000 }}"
                                                >
                                                    <i data-feather="credit-card" class="me-1" style="height: 14px; width: 14px;"></i>
                                                    Record Payment
                                                </button>
                                                <button
                                                    type="button"
                                                    class="dropdown-item js-edit-interment"
                                                    data-record='@json($recordJson)'
                                                >
                                                    <i data-feather="edit-2" class="me-1" style="height: 14px; width: 14px;"></i>
                                                    Edit
                                                </button>
                                                <form method="POST" action="{{ route('admin.interments.destroy', $record) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Delete this interment record?')">
                                                        <i data-feather="trash-2" class="me-1" style="height: 14px; width: 14px;"></i>
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">No interment records found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center pt-3">
                    <div class="text-muted small">
                        Showing {{ $interments->firstItem() ?? 0 }} to {{ $interments->lastItem() ?? 0 }} of {{ $interments->total() }} results
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        @if ($interments->onFirstPage())
                            <span class="btn btn-sm btn-outline-secondary disabled">Previous</span>
                        @else
                            <a class="btn btn-sm btn-outline-secondary" href="{{ $interments->previousPageUrl() }}">Previous</a>
                        @endif
                        <span class="text-muted">Page {{ $interments->currentPage() }} of {{ $interments->lastPage() }}</span>
                        @if ($interments->hasMorePages())
                            <a class="btn btn-sm btn-outline-secondary" href="{{ $interments->nextPageUrl() }}">Next</a>
                        @else
                            <span class="btn btn-sm btn-outline-secondary disabled">Next</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Interment Modal -->
<div class="modal fade" id="createIntermentModal" tabindex="-1" aria-labelledby="createIntermentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <form id="createIntermentForm" method="POST" action="{{ route('admin.interments.store') }}" enctype="multipart/form-data" class="modal-content">
            @csrf
            <input type="hidden" name="_modal" value="create">
            <div class="modal-header">
                <h5 class="modal-title" id="createIntermentModalLabel">Add Interment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info py-2 px-3 small mb-3" id="create_lot_eligibility_info" style="display: none;">
                    <span id="create_lot_eligibility_text"></span>
                </div>
                @include('admin.interments.partials.form-fields', ['idPrefix' => 'create_'])
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary" id="create_submit_btn">Save Interment</button>
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

<div class="modal fade" id="recordPaymentModal" tabindex="-1" aria-labelledby="recordPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="recordPaymentForm" method="POST" action="" class="modal-content">
            @csrf
            <input type="hidden" name="_modal" value="payment">
            <div class="modal-header">
                <h5 class="modal-title" id="recordPaymentModalLabel">Record Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-light border mb-3">
                    <div class="mb-2">
                        <strong>Deceased:</strong> <span id="payment_deceased_name">—</span>
                    </div>
                    <div class="mb-2">
                        <strong>Total Fee:</strong> ₱<span id="payment_total_fee">0.00</span>
                    </div>
                    <div class="mb-2">
                        <strong>Already Paid:</strong> ₱<span id="payment_already_paid">0.00</span>
                    </div>
                    <div>
                        <strong>Remaining:</strong> ₱<span id="payment_remaining" class="text-danger fw-semibold">0.00</span>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="payment_type" class="form-label fw-semibold">Payment For</label>
                    <select id="payment_type" name="payment_type" class="form-select" required>
                        <option value="before_excavation">Before Excavation (₱7,500.00)</option>
                        <option value="after_interment">After Interment (₱7,500.00)</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="payment_amount" class="form-label fw-semibold">Amount</label>
                    <div class="input-group">
                        <span class="input-group-text">₱</span>
                        <input type="number" step="0.01" min="0" id="payment_amount" name="amount" class="form-control" value="0" required>
                    </div>
                </div>

                <div class="form-text">
                    Payment before excavation: ₱7,500.00 | Payment after interment: ₱7,500.00 | Total: ₱15,000.00
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Record Payment</button>
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

        function renderClientMenu(menuEl, items, onPick) {
            menuEl.innerHTML = '';

            if (!items.length) {
                var empty = document.createElement('div');
                empty.className = 'dropdown-item text-muted';
                empty.textContent = 'No matching clients';
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

        function showClientMenu(wrapper, inputEl, menuEl) {
            menuEl.classList.add('show');
            inputEl.setAttribute('aria-expanded', 'true');
            wrapper.classList.add('is-open');
        }

        function hideClientMenu(wrapper, inputEl, menuEl) {
            menuEl.classList.remove('show');
            inputEl.setAttribute('aria-expanded', 'false');
            wrapper.classList.remove('is-open');
        }

        function initLotPickers(scope, clientIdValue) {
            var wrappers = scope.querySelectorAll('.js-lot-picker');
            wrappers.forEach(function (wrapper) {
                var inputEl = wrapper.querySelector('.js-lot-picker-input');
                var selectEl = wrapper.querySelector('.js-lot-picker-select');
                var menuEl = wrapper.querySelector('.js-lot-picker-menu');
                if (!inputEl || !selectEl || !menuEl) return;

                // Prefill input from current select value.
                setLotPickerValue(selectEl, inputEl, selectEl.value);

                function refreshMenu() {
                    // Get current options each time instead of using cached value
                    var all = optionData(selectEl);
                    var q = (inputEl.value || '').trim().toLowerCase();
                    var filtered = q === ''
                        ? all
                        : all.filter(function (it) { return it.text.toLowerCase().indexOf(q) !== -1; });

                    renderLotMenu(menuEl, filtered, function (picked) {
                        setLotPickerValue(selectEl, inputEl, picked.value);
                        hideLotMenu(wrapper, inputEl, menuEl);
                        var idPrefix = '';
                        if (selectEl.id && selectEl.id.indexOf('lot_id') !== -1) {
                            idPrefix = selectEl.id.replace(/lot_id$/, '');
                        }
                        checkLotEligibility(idPrefix, picked.value);
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

        function initClientPickers(scope) {
            var wrappers = scope.querySelectorAll('.js-client-picker');
            wrappers.forEach(function (wrapper) {
                var inputEl = wrapper.querySelector('.js-client-picker-input');
                var selectEl = wrapper.querySelector('.js-client-picker-select');
                var menuEl = wrapper.querySelector('.js-client-picker-menu');
                if (!inputEl || !selectEl || !menuEl) return;

                // Derive the prefix from the select id (e.g. "create_client_id" -> "create_")
                // The input has no id, so relying on inputEl.id breaks lot lookups.
                var idPrefix = '';
                if (selectEl.id && selectEl.id.indexOf('client_id') !== -1) {
                    idPrefix = selectEl.id.replace(/client_id$/, '');
                }
                var lotSelectForPrefix = idPrefix
                    ? scope.querySelector('#' + idPrefix + 'lot_id.js-lot-picker-select') || scope.querySelector('#' + idPrefix + 'lot_id')
                    : null;
                var lotPickerWrapper = lotSelectForPrefix
                    ? lotSelectForPrefix.closest('.js-lot-picker')
                    : scope.querySelector('.js-lot-picker');

                var all = optionData(selectEl);

                // Prefill input from current select value.
                var currentValue = selectEl.value;
                var selected = Array.from(selectEl.options).find(function (opt) { return opt.value === currentValue; });
                inputEl.value = (selected && selected.value) ? selected.text : '';

                function refreshMenu() {
                    var q = (inputEl.value || '').trim().toLowerCase();
                    var filtered = q === ''
                        ? all
                        : all.filter(function (it) { return it.text.toLowerCase().indexOf(q) !== -1; });

                    renderClientMenu(menuEl, filtered, function (picked) {
                        selectEl.value = picked.value;
                        inputEl.value = picked.text;
                        hideClientMenu(wrapper, inputEl, menuEl);

                        // When client is selected, fetch their lots
                        if (picked.value) {
                            var initialLotValue = lotPickerWrapper.querySelector('.js-lot-picker-initial-value').value;
                            fetchClientLots(picked.value, scope, idPrefix, initialLotValue);
                        } else {
                            // Reset to all lots
                            resetLotsToAll(scope);
                        }
                    });
                }

                inputEl.addEventListener('focus', function () {
                    refreshMenu();
                    showClientMenu(wrapper, inputEl, menuEl);
                });

                inputEl.addEventListener('click', function () {
                    refreshMenu();
                    showClientMenu(wrapper, inputEl, menuEl);
                });

                inputEl.addEventListener('input', function () {
                    refreshMenu();
                    showClientMenu(wrapper, inputEl, menuEl);
                });

                inputEl.addEventListener('keydown', function (e) {
                    if (e.key === 'Escape') {
                        hideClientMenu(wrapper, inputEl, menuEl);
                    }
                });

                // Hide when clicking outside.
                document.addEventListener('click', function (e) {
                    if (wrapper.contains(e.target)) return;
                    hideClientMenu(wrapper, inputEl, menuEl);
                });
            });
        }

        function fetchClientLots(clientId, scope, idPrefix, initialLotId) {
            var lotsUrl = "{{ url('admin/interments/api/clients') }}/" + clientId + "/lots";

            fetch(lotsUrl)
                .then(function (response) { return response.json(); })
                .then(function (data) {
                    updateLotOptions(data, scope, idPrefix, initialLotId);
                })
                .catch(function (error) { console.error('Error fetching lots:', error); });
        }

        function updateLotOptions(lots, scope, idPrefix, initialLotId) {
            var selectEl = scope.querySelector('#' + idPrefix + 'lot_id.js-lot-picker-select');
            var inputEl = scope.querySelector('#' + idPrefix + 'lot_id').parentElement.querySelector('.js-lot-picker-input');
            if (!selectEl) return;

            // Clear existing options except the first one
            while (selectEl.options.length > 1) {
                selectEl.remove(1);
            }

            // Add new options
            lots.forEach(function (lot) {
                var option = document.createElement('option');
                option.value = lot.id;
                option.text = lot.label;
                selectEl.appendChild(option);
            });

            // Restore initial value if provided
            if (initialLotId) {
                selectEl.value = initialLotId;
                var selected = Array.from(selectEl.options).find(function (opt) { return opt.value === (initialLotId ? String(initialLotId) : ''); });
                if (selected && inputEl) {
                    inputEl.value = selected.text;
                }
            } else if (inputEl) {
                inputEl.value = '';
            }
        }

        function resetLotsToAll(scope) {
            // This would reset to all lots - for now we keep the filtered list
            // This could be called if client is deselected
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

        function checkLotEligibility(idPrefix, lotId) {
            var feedbackEl = document.getElementById(idPrefix + 'lot_eligibility_feedback');
            var infoEl = document.getElementById(idPrefix + 'lot_eligibility_info');
            var infoTextEl = document.getElementById(idPrefix + 'lot_eligibility_text');
            var submitBtn = document.querySelector('#' + idPrefix + 'intermentForm button[type="submit"]') || document.querySelector('#' + idPrefix + 'submit_btn');

            if (!lotId) {
                if (feedbackEl) feedbackEl.textContent = '';
                if (infoEl) infoEl.style.display = 'none';
                return;
            }

            fetch('/admin/interments/api/check-lot-eligibility?lot_id=' + encodeURIComponent(lotId))
                .then(function(response) { return response.json(); })
                .then(function(data) {
                    if (data.eligible) {
                        if (infoEl) {
                            infoEl.style.display = 'block';
                            infoEl.className = 'alert alert-success py-2 px-3 small mb-3';
                            infoTextEl.textContent = 'Lot eligible for interment (' + data.interment_count + '/' + data.max_interments + ' used)';
                        }
                        if (feedbackEl) feedbackEl.textContent = '';
                        if (submitBtn) submitBtn.disabled = false;
                    } else {
                        if (infoEl) {
                            infoEl.style.display = 'block';
                            infoEl.className = 'alert alert-danger py-2 px-3 small mb-3';
                            infoTextEl.textContent = data.reason || 'Cannot add interment to this lot.';
                        }
                        if (feedbackEl) feedbackEl.textContent = data.reason || 'Lot is not eligible.';
                        if (submitBtn) submitBtn.disabled = true;
                    }
                })
                .catch(function(error) {
                    console.error('Error checking lot eligibility:', error);
                });
        }

        document.querySelectorAll('#createIntermentModal').forEach(function (modal) {
            initClientPickers(modal);
            initLotPickers(modal);
            modal.querySelectorAll('.js-interment-status').forEach(function (statusInput) {
                statusInput.addEventListener('change', function () {
                    syncStatusRequirements(modal);
                });
                syncStatusRequirements(modal);
            });
        });

        document.querySelectorAll('#editIntermentModal').forEach(function (modal) {
            initClientPickers(modal);
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

                // Set the initial lot value in the hidden input
                var lotInitialInput = document.querySelector('#edit_lot_id').parentElement.querySelector('.js-lot-picker-initial-value');
                if (lotInitialInput) {
                    lotInitialInput.value = record.lot_id || '';
                    checkLotEligibility('edit_', record.lot_id);
                }

                var permitInput = document.getElementById('edit_burial_permit');
                if (permitInput) {
                    permitInput.value = '';
                    permitInput.dataset.hasExisting = record.has_existing_permit ? '1' : '';
                }

                var modalEl = document.getElementById('editIntermentModal');

                // When editing, if a client is selected, fetch their lots
                if (record.client_id) {
                    fetchClientLots(record.client_id, modalEl, 'edit_', record.lot_id);
                    // Set the client picker input
                    var clientSelectEl = modalEl.querySelector('#edit_client_id.js-client-picker-select');
                    var clientInputEl = modalEl.querySelector('.js-client-picker-input');
                    if (clientSelectEl && clientInputEl) {
                        var clientOption = Array.from(clientSelectEl.options).find(function (opt) {
                            return opt.value === (record.client_id ? String(record.client_id) : '');
                        });
                        if (clientOption) {
                            clientInputEl.value = clientOption.text;
                        }
                    }
                }

                initClientPickers(modalEl);
                syncStatusRequirements(modalEl);
                bootstrap.Modal.getOrCreateInstance(modalEl).show();
            });
        });

        // Record Payment Modal
        var paymentModal = document.getElementById('recordPaymentModal');
        var paymentForm = document.getElementById('recordPaymentForm');
        var paymentButtons = document.querySelectorAll('.js-record-payment');

        paymentButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                var recordId = button.getAttribute('data-record-id');
                var recordName = button.getAttribute('data-record-name');
                var paymentStatus = button.getAttribute('data-payment-status');
                var paymentBefore = parseFloat(button.getAttribute('data-payment-before')) || 0;
                var paymentAfter = parseFloat(button.getAttribute('data-payment-after')) || 0;
                var totalFee = parseFloat(button.getAttribute('data-interment-fee')) || 15000;

                var alreadyPaid = paymentBefore + paymentAfter;
                var remaining = Math.max(0, totalFee - alreadyPaid);

                document.getElementById('payment_deceased_name').textContent = recordName;
                document.getElementById('payment_total_fee').textContent = totalFee.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                document.getElementById('payment_already_paid').textContent = alreadyPaid.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                document.getElementById('payment_remaining').textContent = remaining.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');

                if (paymentStatus === 'fully_paid') {
                    document.getElementById('payment_remaining').closest('div').querySelector('strong').textContent = 'Remaining:';
                    document.getElementById('payment_remaining').classList.remove('text-danger');
                    document.getElementById('payment_remaining').classList.add('text-success');
                    document.getElementById('payment_remaining').textContent = '0.00 (Paid)';
                }

                paymentForm.action = "{{ url('admin/interments') }}/" + recordId + "/payment";

                bootstrap.Modal.getOrCreateInstance(paymentModal).show();
            });
        });

        if (paymentModal) {
            paymentModal.addEventListener('hidden.bs.modal', function () {
                document.getElementById('payment_amount').value = '0';
                document.getElementById('payment_type').value = 'before_excavation';
                document.getElementById('payment_remaining').classList.remove('text-success');
                document.getElementById('payment_remaining').classList.add('text-danger');
            });
        }
    })();
</script>

@if ($errors->any() && old('_modal') === 'edit')
    <script>
        bootstrap.Modal.getOrCreateInstance(document.getElementById('editIntermentModal')).show();
    </script>
@endif

@if ($errors->any() && old('_modal') === 'create')
    <script>
        bootstrap.Modal.getOrCreateInstance(document.getElementById('createIntermentModal')).show();
    </script>
@endif
@endsection
