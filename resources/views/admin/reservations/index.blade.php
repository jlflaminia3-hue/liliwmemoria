@extends('admin.admin_master')

@section('admin')
@php
    $currentSearch = request('search', '');
    $currentStatus = request('status', '');
    $currentPaymentStatus = request('payment_status', '');
    $currentPerPage = (int) request('per_page', 20);
    $shouldOpenCreate = (bool) ($openCreateModal ?? false);
    $prefillLotId = $prefillLotId ?? null;
@endphp

<div class="content">
    <div class="container-xxl">
        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column gap-3">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">Reservations</h4>
                <div class="text-muted mt-1">Manage lot bookings and client commitments prior to burial. Active reservations lock lots on the map.</div>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('admin.lots.map') }}" class="btn btn-outline-secondary btn-sm">
                    <i data-feather="map" class="me-1" style="height: 16px; width: 16px;"></i>
                    Open Map
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if (session('warning'))
            <div class="alert alert-warning">{{ session('warning') }}</div>
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
                        <div class="fs-3 fw-bold">{{ number_format($stats['total'] ?? 0) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase fw-semibold">Active</div>
                        <div class="fs-3 fw-bold text-primary">{{ number_format($stats['active'] ?? 0) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase fw-semibold">Expired</div>
                        <div class="fs-3 fw-bold text-danger">{{ number_format($stats['expired'] ?? 0) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase fw-semibold">Fulfilled</div>
                        <div class="fs-3 fw-bold text-success">{{ number_format($stats['fulfilled'] ?? 0) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.reservations.index') }}" class="row g-3 align-items-end mb-4">
                    <div class="col-lg-4">
                        <label for="reservation_search" class="form-label fw-semibold">Search</label>
                        <input id="reservation_search" type="text" name="search" class="form-control" value="{{ $currentSearch }}" placeholder="Client, phone, email, section, block, lot number">
                    </div>
                    <div class="col-md-4 col-lg-2">
                        <label for="reservation_status_filter" class="form-label fw-semibold">Status</label>
                        <select id="reservation_status_filter" name="status" class="form-select">
                            <option value="">All</option>
                            <option value="active" @selected($currentStatus === 'active')>Active</option>
                            <option value="expired" @selected($currentStatus === 'expired')>Expired</option>
                            <option value="fulfilled" @selected($currentStatus === 'fulfilled')>Fulfilled</option>
                        </select>
                    </div>
                    <div class="col-md-4 col-lg-3">
                        <label for="reservation_payment_filter" class="form-label fw-semibold">Payment</label>
                        <select id="reservation_payment_filter" name="payment_status" class="form-select">
                            <option value="">All</option>
                            <option value="downpayment" @selected($currentPaymentStatus === 'downpayment')>Downpayment</option>
                            <option value="installment" @selected($currentPaymentStatus === 'installment')>Installment</option>
                            <option value="fully_paid" @selected($currentPaymentStatus === 'fully_paid')>Fully paid</option>
                        </select>
                    </div>
                    <div class="col-md-4 col-lg-1">
                        <label for="reservation_per_page" class="form-label fw-semibold">Rows</label>
                        <select id="reservation_per_page" name="per_page" class="form-select">
                            <option value="10" @selected($currentPerPage === 10)>10</option>
                            <option value="20" @selected($currentPerPage === 20)>20</option>
                            <option value="50" @selected($currentPerPage === 50)>50</option>
                            <option value="100" @selected($currentPerPage === 100)>100</option>
                        </select>
                    </div>
                    <div class="col-lg-2 d-flex gap-2">
                        <button class="btn btn-primary w-100" type="submit">Apply</button>
                        <a class="btn btn-outline-secondary w-100" href="{{ route('admin.reservations.index') }}">Reset</a>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Client</th>
                                <th>Contact</th>
                                <th>Lot</th>
                                <th>Reserved</th>
                                <th>Expiry</th>
                                <th>Status</th>
                                <th>Payment</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($reservations as $reservation)
                                @php
                                    $client = $reservation->client;
                                    $lot = $reservation->lot;
                                    $lotLabel = $lot ? ($lot->lot_id ?? ('L-'.$lot->lot_number)) : '-';
                                    $sectionLabel = $lot ? ($lot->lot_category_label ?? ($lot->section ?? 'N/A')) : 'N/A';
                                    $blockLabel = $lot && $lot->block ? $lot->block : '-';
                                    $status = $reservation->status ?? 'active';
                                    $payment = $reservation->payment_status ?? '-';
                                @endphp
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $client?->full_name ?? '—' }}</div>
                                        <div class="text-muted small">#{{ $reservation->id }}</div>
                                    </td>
                                    <td class="small">
                                        <div>{{ $client?->phone ?: '—' }}</div>
                                        <div class="text-muted">{{ $client?->email ?: '—' }}</div>
                                    </td>
                                    <td class="small">
                                        <div class="fw-semibold">{{ $lotLabel }}</div>
                                        <div class="text-muted">{{ $sectionLabel }} • Block {{ $blockLabel }} • Lot {{ $lot?->lot_number ?? '—' }}</div>
                                    </td>
                                    <td class="small">{{ optional($reservation->reserved_at)->format('Y-m-d') }}</td>
                                    <td class="small">{{ $reservation->expires_at ? optional($reservation->expires_at)->format('Y-m-d') : '—' }}</td>
                                    <td>
                                        @if ($status === 'active')
                                            <span class="badge bg-primary">Active</span>
                                        @elseif ($status === 'expired')
                                            <span class="badge bg-danger">Expired</span>
                                        @elseif ($status === 'fulfilled')
                                            <span class="badge bg-success">Fulfilled</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($status) }}</span>
                                        @endif
                                    </td>
                                    <td class="small">
                                        <div>{{ $payment !== '-' ? str_replace('_', ' ', ucwords($payment, '_')) : '—' }}</div>
                                        @if ($reservation->paymentPlan)
                                            <div class="text-muted">Plan: {{ $reservation->paymentPlan->plan_number }}</div>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="d-inline-flex align-items-center gap-1">
                                            @if ($reservation->contract_path)
                                                <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.reservations.contract.download', $reservation) }}">Contract</a>
                                            @endif
                                            <button type="button"
                                                class="btn btn-outline-primary btn-sm js-edit-reservation"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editReservationModal"
                                                data-id="{{ $reservation->id }}"
                                                data-client-id="{{ $reservation->client_id }}"
                                                data-lot-id="{{ $reservation->lot_id }}"
                                                data-lot-label="{{ $lotLabel }}"
                                                data-reserved-at="{{ optional($reservation->reserved_at)->format('Y-m-d') }}"
                                                data-expires-at="{{ $reservation->expires_at ? optional($reservation->expires_at)->format('Y-m-d') : '' }}"
                                                data-status="{{ $reservation->status }}"
                                                data-payment-status="{{ $reservation->payment_status ?? '' }}"
                                                data-contract-duration-months="{{ $reservation->contract?->contract_duration_months ?? '' }}"
                                                data-total-amount="{{ $reservation->contract?->total_amount ?? '' }}"
                                                data-amount-paid="{{ $reservation->contract?->amount_paid ?? '' }}"
                                                data-notes="{{ e($reservation->notes ?? '') }}"
                                            >Edit</button>
                                            <form method="POST" action="{{ route('admin.reservations.destroy', $reservation) }}" onsubmit="return confirm('Delete this reservation?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">No reservations found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center pt-3">
                    <div class="text-muted small">
                        Showing {{ $reservations->firstItem() ?? 0 }} to {{ $reservations->lastItem() ?? 0 }} of {{ $reservations->total() }} results
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        @if ($reservations->onFirstPage())
                            <span class="btn btn-sm btn-outline-secondary disabled">Previous</span>
                        @else
                            <a class="btn btn-sm btn-outline-secondary" href="{{ $reservations->previousPageUrl() }}">Previous</a>
                        @endif
                        <span class="text-muted">Page {{ $reservations->currentPage() }} of {{ $reservations->lastPage() }}</span>
                        @if ($reservations->hasMorePages())
                            <a class="btn btn-sm btn-outline-secondary" href="{{ $reservations->nextPageUrl() }}">Next</a>
                        @else
                            <span class="btn btn-sm btn-outline-secondary disabled">Next</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .reservation-modal-scroll .modal-body {
        overflow: auto;
        max-height: calc(100vh - 220px);
    }

    .reservation-modal-scroll .modal-header {
        border-bottom: 1px solid rgba(15, 23, 42, 0.08);
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    }

    .reservation-modal-scroll .modal-title {
        font-weight: 700;
        color: #0f172a;
    }

    .reservation-modal-scroll .form-label {
        font-weight: 600;
        color: #334155;
        margin-bottom: 0.35rem;
    }

    .reservation-modal-scroll .form-text {
        color: #64748b;
    }
</style>

<!-- Create Modal -->
<div class="modal fade" id="createReservationModal" tabindex="-1" aria-labelledby="createReservationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable reservation-modal-scroll">
        <form method="POST" action="{{ route('admin.reservations.store') }}" enctype="multipart/form-data" class="modal-content">
            @csrf
            <input type="hidden" name="_modal" value="create">
            <div class="modal-header">
                <h5 class="modal-title" id="createReservationModalLabel">Create Reservation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Lot</label>
                        <div class="js-lot-picker position-relative">
                            <input
                                type="text"
                                class="form-control js-lot-picker-input"
                                placeholder="Search and select an available lot"
                                autocomplete="off"
                                role="combobox"
                                aria-expanded="false"
                                aria-haspopup="listbox"
                            >
                            <select class="form-select js-lot-picker-select d-none" name="lot_id" id="create_lot_id" required>
                                <option value="">Select an available lot…</option>
                                @foreach ($lots as $lot)
                                    @php($label = ($lot->lot_id ?? ('L-'.$lot->lot_number)).' — '.($lot->lot_category_label ?? $lot->section).' • Block '.($lot->block ?: '—'))
                                    <option
                                        value="{{ $lot->id }}"
                                        data-lot-kind="{{ $lot->section }}"
                                        data-lot-category="{{ $lot->lot_category_label ?? $lot->section }}"
                                        @selected((string) old('lot_id', $prefillLotId) === (string) $lot->id)
                                    >{{ $label }}</option>
                                @endforeach
                            </select>
                            <div class="dropdown-menu w-100 mt-1 js-lot-picker-menu" style="max-height: 260px; overflow:auto;"></div>
                            <div class="form-text">Tip: pick a lot from the map and click “Reserve this lot”.</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Lot category</label>
                        <input type="text" class="form-control" id="create_lot_category_display" value="—" readonly>
                        <div class="form-text">Auto-filled from the selected lot.</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Client</label>
                        <select class="form-select js-reservation-client" name="client_id" required>
                            <option value="">Select client…</option>
                            @foreach ($clients as $client)
                                <option value="{{ $client->id }}" data-email="{{ $client->email }}">{{ $client->full_name }}{{ $client->phone ? ' • '.$client->phone : '' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 order-3 order-md-3">
                        <label class="form-label fw-semibold">Reservation date</label>
                        <input type="date" class="form-control" name="reserved_at" value="{{ old('reserved_at', now()->format('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-6 order-2 order-md-2" id="create_contract_duration_wrap">
                        <label class="form-label fw-semibold">Contract duration</label>
                        <select class="form-select js-contract-duration" name="contract_duration_months" id="create_contract_duration_months">
                            <option value="">Select duration…</option>
                            <option value="12" @selected((string) old('contract_duration_months') === '12')>12 months</option>
                            <option value="18" @selected((string) old('contract_duration_months') === '18')>18 months</option>
                            <option value="24" @selected((string) old('contract_duration_months') === '24')>24 months</option>
                        </select>
                        <div class="form-text text-muted">Required for installment payments only</div>
                    </div>
                    <div class="col-md-6 order-1 order-md-1">
                        <label class="form-label fw-semibold">Payment status</label>
                        <select class="form-select" name="payment_status" id="create_payment_status" required>
                                <option value="">Select payment status…</option>
                                <option value="cash" @selected(old('payment_status') === 'cash')>Cash</option>
                                <option value="installment" @selected(old('payment_status') === 'installment')>Installment</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Contract amount</label>
                        <input type="number" step="0.01" min="0" class="form-control" name="total_amount" value="{{ old('total_amount') }}" placeholder="0.00">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Downpayment</label>
                        <input type="number" step="0.01" min="0" class="form-control" name="amount_paid" value="{{ old('amount_paid') }}" placeholder="0.00">
                    </div>
                    

                    <div class="col-12 order-4 order-md-4">
                        <div class="form-check">
                            <input
                                class="form-check-input js-email-pdf"
                                type="checkbox"
                                value="1"
                                id="reservation_email_pdf"
                                name="email_pdf"
                                @checked(old('email_pdf', 1))
                            >
                            <label class="form-check-label" for="reservation_email_pdf">
                                Email contract PDF to client <span id="reservation_email_target" class="text-muted"></span>
                            </label>
                        </div>
                        <div class="form-text text-warning d-none" id="reservation_no_email_warning">Client has no email on file.</div>
                    </div>
                    <div class="col-12 order-5 order-md-5">
                        <label class="form-label fw-semibold">Notes (optional)</label>
                        <textarea class="form-control" name="notes" rows="3">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save reservation</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editReservationModal" tabindex="-1" aria-labelledby="editReservationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable reservation-modal-scroll">
        <form method="POST" id="editReservationForm" enctype="multipart/form-data" class="modal-content">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title" id="editReservationModalLabel">Edit Reservation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                        <div class="col-12">
                            <div class="alert alert-light border small mb-0">
                                <span class="fw-semibold">Lot:</span> <span id="edit_lot_label">—</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Client</label>
                            <select class="form-select" name="client_id" id="edit_client_id" required>
                                <option value="">Select client…</option>
                                @foreach ($clients as $client)
                                    <option value="{{ $client->id }}">{{ $client->full_name }}{{ $client->phone ? ' • '.$client->phone : '' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status</label>
                            <select class="form-select" name="status" id="edit_status" required>
                                <option value="active">Active</option>
                                <option value="expired">Expired</option>
                                <option value="fulfilled">Fulfilled</option>
                            </select>
                        </div>
                        <div class="col-md-6 order-3 order-md-3">
                            <label class="form-label fw-semibold">Reservation date</label>
                            <input type="date" class="form-control" name="reserved_at" id="edit_reserved_at" required>
                        </div>
                        <div class="col-md-6 order-2 order-md-2">
                            <label class="form-label fw-semibold">Contract duration</label>
                            <select class="form-select" name="contract_duration_months" id="edit_contract_duration_months" required>
                                <option value="">Select duration…</option>
                                <option value="12">12 months</option>
                                <option value="18">18 months</option>
                                <option value="24">24 months</option>
                            </select>
                        </div>
                        <div class="col-md-6 order-4 order-md-4">
                            <label class="form-label fw-semibold">Expiry date</label>
                            <input type="date" class="form-control" name="expires_at" id="edit_expires_at" readonly>
                            <div class="form-text">Auto-calculated from reservation date + duration.</div>
                        </div>
                        <div class="col-md-6 order-1 order-md-1">
                            <label class="form-label fw-semibold">Payment status</label>
                            <select class="form-select" name="payment_status" id="edit_payment_status" required>
                                <option value="">Select payment status…</option>
                                <option value="cash">Cash</option>
                                <option value="installment">Installment</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Contract amount (optional)</label>
                            <input type="number" step="0.01" min="0" class="form-control" name="total_amount" id="edit_total_amount" placeholder="0.00">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Downpayment (optional)</label>
                            <input type="number" step="0.01" min="0" class="form-control" name="amount_paid" id="edit_amount_paid" placeholder="0.00">
                        </div>
                        <div class="col-12 order-5 order-md-5">
                            <label class="form-label fw-semibold">Special arrangements / notes (optional)</label>
                            <textarea class="form-control" name="notes" id="edit_notes" rows="3"></textarea>
                        </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save changes</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var editForm = document.getElementById('editReservationForm');
    var editLotLabel = document.getElementById('edit_lot_label');
    var editClientId = document.getElementById('edit_client_id');
    var editStatus = document.getElementById('edit_status');
    var editReservedAt = document.getElementById('edit_reserved_at');
    var editExpiresAt = document.getElementById('edit_expires_at');
    var editContractDuration = document.getElementById('edit_contract_duration_months');
    var editPaymentStatus = document.getElementById('edit_payment_status');
    var editTotalAmount = document.getElementById('edit_total_amount');
    var editAmountPaid = document.getElementById('edit_amount_paid');
    var editNotes = document.getElementById('edit_notes');

    var createExpiresAt = document.getElementById('create_expires_at');
    var createLotCategoryDisplay = document.getElementById('create_lot_category_display');

    function setDateInputValue(input, value) {
        if (!input) return;
        if (window.AdminDatePickers && typeof window.AdminDatePickers.setValue === 'function') {
            window.AdminDatePickers.setValue(input, value);
            return;
        }
        input.value = value || '';
    }

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
            setLotPickerValue(selectEl, inputEl, selectEl.value);

            function syncLotMeta() {
                if (!createLotCategoryDisplay) return;
                if (!scope || scope.id !== 'createReservationModal') return;

                var selected = selectEl.selectedOptions && selectEl.selectedOptions[0];
                var category = selected ? String(selected.getAttribute('data-lot-category') || '').trim() : '';
                createLotCategoryDisplay.value = category !== '' ? category : '—';
            }

            function refreshMenu() {
                var q = (inputEl.value || '').trim().toLowerCase();
                var filtered = q === ''
                    ? all
                    : all.filter(function (it) { return it.text.toLowerCase().indexOf(q) !== -1; });

                renderLotMenu(menuEl, filtered, function (picked) {
                    setLotPickerValue(selectEl, inputEl, picked.value);
                    syncLotMeta();
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

            document.addEventListener('click', function (e) {
                if (wrapper.contains(e.target)) return;
                hideLotMenu(wrapper, inputEl, menuEl);
            });

            syncLotMeta();
        });
    }

    initLotPickers(document.getElementById('createReservationModal'));

    document.querySelectorAll('.js-edit-reservation').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var id = btn.getAttribute('data-id');
            if (!id || !editForm) return;

            editForm.action = @json(route('admin.reservations.index')) + '/' + encodeURIComponent(id);

            if (editLotLabel) editLotLabel.textContent = btn.getAttribute('data-lot-label') || '—';
            if (editClientId) editClientId.value = btn.getAttribute('data-client-id') || '';
            if (editStatus) editStatus.value = btn.getAttribute('data-status') || 'active';
            setDateInputValue(editReservedAt, btn.getAttribute('data-reserved-at') || '');

            var durationMonths = btn.getAttribute('data-contract-duration-months') || '';
            if (editContractDuration) editContractDuration.value = durationMonths;
            if (editTotalAmount) editTotalAmount.value = btn.getAttribute('data-total-amount') || '';
            if (editAmountPaid) editAmountPaid.value = btn.getAttribute('data-amount-paid') || '';

            var reservedAtValue = btn.getAttribute('data-reserved-at') || '';
            var expiresAtValue = btn.getAttribute('data-expires-at') || '';
            var m = parseInt(durationMonths || '0', 10);
            if ([12, 18, 24].includes(m)) {
                var computed = addMonthsNoOverflow(reservedAtValue, m);
                if (computed) expiresAtValue = computed;
            }

            setDateInputValue(editExpiresAt, expiresAtValue);
            if (editPaymentStatus) editPaymentStatus.value = btn.getAttribute('data-payment-status') || '';
            if (editNotes) editNotes.value = btn.getAttribute('data-notes') || '';
        });
    });

    function addMonthsNoOverflow(dateString, months) {
        var parts = String(dateString || '').split('-');
        if (parts.length !== 3) return '';
        var y = parseInt(parts[0], 10);
        var m = parseInt(parts[1], 10) - 1;
        var d = parseInt(parts[2], 10);
        if (!Number.isFinite(y) || !Number.isFinite(m) || !Number.isFinite(d)) return '';

        var base = new Date(Date.UTC(y, m, d));
        if (isNaN(base.getTime())) return '';

        var targetMonth = base.getUTCMonth() + months;
        var target = new Date(Date.UTC(base.getUTCFullYear(), targetMonth, base.getUTCDate()));

        // If month overflowed (e.g. Jan 31 + 1 month => Mar 3), clamp to last day of target month.
        while (target.getUTCMonth() !== ((targetMonth % 12) + 12) % 12) {
            target.setUTCDate(target.getUTCDate() - 1);
        }

        var mm = String(target.getUTCMonth() + 1).padStart(2, '0');
        var dd = String(target.getUTCDate()).padStart(2, '0');
        return target.getUTCFullYear() + '-' + mm + '-' + dd;
    }

    function syncEditExpiry() {
        if (!editReservedAt || !editContractDuration || !editExpiresAt) return;
        var m = parseInt(editContractDuration.value || '0', 10);
        if (![12, 18, 24].includes(m)) return;
        var computed = addMonthsNoOverflow(editReservedAt.value, m);
        if (!computed) return;
        setDateInputValue(editExpiresAt, computed);
    }

    if (editReservedAt) editReservedAt.addEventListener('change', syncEditExpiry);
    if (editContractDuration) editContractDuration.addEventListener('change', syncEditExpiry);

    var createModal = document.getElementById('createReservationModal');
    if (createModal) {
        var reservedAt = createModal.querySelector('input[name="reserved_at"]');
        var duration = createModal.querySelector('select[name="contract_duration_months"]');
        var expiresAt = createExpiresAt;
        var clientSelect = createModal.querySelector('.js-reservation-client');
        var emailTarget = document.getElementById('reservation_email_target');
        var emailCheckbox = createModal.querySelector('.js-email-pdf');
        var noEmailWarning = document.getElementById('reservation_no_email_warning');

        function selectedClientEmail() {
            if (!clientSelect) return '';
            var opt = clientSelect.selectedOptions && clientSelect.selectedOptions[0];
            if (!opt) return '';
            return String(opt.getAttribute('data-email') || '').trim();
        }

        function syncEmailUi() {
            var email = selectedClientEmail();
            if (emailTarget) {
                emailTarget.textContent = email ? '(' + email + ')' : '(no email)';
            }

            var wantsEmail = !!(emailCheckbox && emailCheckbox.checked);
            var showWarn = wantsEmail && !email;
            if (noEmailWarning) {
                noEmailWarning.classList.toggle('d-none', !showWarn);
            }
        }

        function syncExpiry() {
            if (!reservedAt || !duration || !expiresAt) return;
            var m = parseInt(duration.value || '0', 10);
            if (![12, 18, 24].includes(m)) return;
            var computed = addMonthsNoOverflow(reservedAt.value, m);
            if (computed) {
                expiresAt.value = computed;
            }
        }

        function syncContractDurationRequirement() {
            var paymentStatus = createModal.querySelector('select[name="payment_status"]');
            var durationWrap = document.getElementById('create_contract_duration_wrap');
            var durationSelect = document.getElementById('create_contract_duration_months');

            if (!paymentStatus || !durationWrap || !durationSelect) return;

            var isInstallment = paymentStatus.value === 'installment';
            durationSelect.required = isInstallment;
            durationWrap.style.opacity = isInstallment ? '1' : '0.5';
        }

        var paymentStatusSelect = createModal.querySelector('select[name="payment_status"]');
        if (paymentStatusSelect) {
            paymentStatusSelect.addEventListener('change', syncContractDurationRequirement);
            syncContractDurationRequirement();
        }

        if (reservedAt) reservedAt.addEventListener('change', function () {
            syncExpiry();
        });
        if (duration) duration.addEventListener('change', function () {
            syncExpiry();
        });
        if (clientSelect) clientSelect.addEventListener('change', syncEmailUi);
        if (emailCheckbox) emailCheckbox.addEventListener('change', syncEmailUi);
        syncEmailUi();
        syncExpiry();
    }

    if (@json($shouldOpenCreate)) {
        var modalEl = document.getElementById('createReservationModal');
        if (modalEl && window.bootstrap && window.bootstrap.Modal) {
            var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
        }
    }
});
</script>

@if ($errors->any() && old('_modal') === 'create')
    <script>
        bootstrap.Modal.getOrCreateInstance(document.getElementById('createReservationModal')).show();
    </script>
@endif
@endsection
