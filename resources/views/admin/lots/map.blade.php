@extends('admin.admin_master')

@section('admin')
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-3">
                <div class="map-toolbar">
                    <div class="map-toolbar__left">
                        <h4 class="map-toolbar__title mb-0">
                            <i data-feather="map" class="me-2" style="height: 20px; width: 20px;"></i>Cemetery Map
                        </h4>
                    </div>
                    <div class="map-toolbar__center">
                        <div class="map-toolbar__search">
                            <i data-feather="search" class="map-toolbar__search-icon"></i>
                            <input type="text" id="lotSearchInput" class="form-control" placeholder="Search lots...">
                        </div>
                        <div class="map-toolbar__divider"></div>
                        <div class="btn-group map-toolbar__draw-tools" role="group" aria-label="Drawing tools">
                            <button type="button" class="btn btn-sm btn-outline-secondary active" id="toolSelect" title="Select (S)">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 3 7.07 16.97 2.51-7.39 7.39-2.51L3 3z"/><path d="m13 13 6 6"/></svg>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="toolRect" title="Draw Rectangle (R)">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2"/></svg>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="toolPoly" title="Draw Polygon (P)">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2 2 7l10 5 10-5-10-5Z"/><path d="m2 17 10 5 10-5"/><path d="m2 12 10 5 10-5"/></svg>
                            </button>
                            <button type="button" class="btn btn-sm btn-success" id="btnFinishPoly" disabled title="Finish Drawing">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                            </button>
                            <button type="button" class="btn btn-sm btn-secondary" id="btnCancelDraw" disabled title="Cancel Drawing">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                            </button>
                        </div>
                    </div>
                    <div class="map-toolbar__right">
                        <a href="{{ route('admin.reservations.index') }}" class="btn btn-sm btn-outline-primary">
                            <i data-feather="calendar"></i>
                            <span class="d-none d-md-inline">Reservations</span>
                        </a>
                        <a href="{{ route('admin.interments.index') }}" class="btn btn-sm btn-outline-primary">
                            <i data-feather="map-pin"></i>
                            <span class="d-none d-md-inline">Interments</span>
                        </a>
                    </div>
                </div>

                <div id="map" class="map-container"></div>

                <div class="lot-legend-container">
                    <span class="lot-legend">
                        <span class="lot-swatch lot-swatch--available"></span>
                        Available
                    </span>
                    <span class="lot-legend">
                        <span class="lot-swatch lot-swatch--reserved"></span>
                        Reserved
                    </span>
                    <span class="lot-legend">
                        <span class="lot-swatch lot-swatch--occupied"></span>
                        Occupied
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Lot Modal -->
<div class="modal fade" id="addLotModal" tabindex="-1" aria-labelledby="addLotModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addLotModalLabel">Add New Burial Lot</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('admin.lots.storeWithDeceased') }}">
                @csrf
                <div class="modal-body">
                    <h6 class="text-muted mb-3">Lot Information</h6>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Lot ID</label>
                            <input type="text" id="modal_lot_id" class="form-control" readonly>
                            <input type="hidden" name="lot_number" id="modal_lot_number">
                            <div class="form-text">Auto-generated.</div>
                        </div>
                        <div class="col-md-4 mb-3" id="owner_field_wrap">
                            <label class="form-label">Lot Owner</label>
                            <input type="text" name="name" id="modal_owner" class="form-control">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="form-label">Block</label>
                            <input type="text" name="block" id="modal_block" class="form-control" placeholder="e.g. A">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Lot Category</label>
                            <select name="section" id="modal_category" class="form-select" required>
                                <option value="phase_1" selected>Phase 1</option>
                                <option value="phase_2">Phase 2</option>
                                <option value="garden_lot">Garden Lot</option>
                                <option value="back_office_lot">Back Office Lot</option>
                                <option value="narra">Narra</option>
                                <option value="mausoleum">Mausoleum</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Lot Status</label>
                            <select name="status" id="modal_status" class="form-select" required>
                                <option value="available" selected>Available (Green)</option>
                                <option value="occupied">Occupied (Red)</option>
                                <option value="reserved">Reserved (Blue)</option>
                            </select>
                        </div>
                    </div>
                    <input type="hidden" name="latitude" id="modal_latitude">
                    <input type="hidden" name="longitude" id="modal_longitude">
                    <input type="hidden" name="geometry_type" id="modal_geometry_type">
                    <input type="hidden" name="geometry" id="modal_geometry">

                    <div id="details_fields">
                        <hr>
                        <div id="deceased_fields">
                            <h6 class="text-muted mb-3">Deceased Information</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">First Name</label>
                                    <input type="text" name="first_name" id="modal_first_name" class="form-control">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Last Name</label>
                                    <input type="text" name="last_name" id="modal_last_name" class="form-control">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Date of Birth</label>
                                    <input type="date" name="date_of_birth" class="form-control">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Date of Death</label>
                                    <input type="date" name="date_of_death" class="form-control">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Burial Date</label>
                                    <input type="date" name="burial_date" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
  </div>

<!-- Reservation Modal -->
<div class="modal fade" id="reserveLotModal" tabindex="-1" aria-labelledby="reserveLotModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <form method="POST" action="{{ route('admin.reservations.store') }}" id="reserveLotForm" enctype="multipart/form-data" class="modal-content">
            @csrf
            <input type="hidden" name="_modal" value="map">
            <div class="modal-header">
                <h5 class="modal-title" id="reserveLotModalLabel">Reserve Lot</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-light border small mb-3">
                    <span class="fw-semibold">Lot:</span> <span id="reserve_lot_label">—</span>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Lot</label>
                        <select class="form-select js-lot-picker-select" name="lot_id" id="reserve_lot_id" required>
                            <option value="">Select an available lot…</option>
                            @foreach ($lots->filter(fn($lot) => ($lot->status === 'available' || $lot->status === null) && !$lot->is_occupied) as $lot)
                                @php($label = ($lot->lot_id ?? ('L-'.$lot->lot_number)).' — '.($lot->lot_category_label ?? $lot->section).' • Block '.($lot->block ?: '—'))
                                <option value="{{ $lot->id }}" data-lot-category="{{ $lot->lot_category_label ?? $lot->section }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Lot category</label>
                        <input type="text" class="form-control" id="reserve_lot_category_display" value="—" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Client</label>
                        <select class="form-select js-reservation-client" name="client_id" id="reserve_client_id" required>
                            <option value="">Select client…</option>
                            @foreach ($clients as $client)
                                <option value="{{ $client->id }}" data-email="{{ $client->email }}">{{ $client->full_name }}{{ $client->phone ? ' • '.$client->phone : '' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Payment status</label>
                        <select class="form-select" name="payment_status" id="reserve_payment_status" required>
                            <option value="">Select payment status…</option>
                            <option value="downpayment">Downpayment</option>
                            <option value="installment">Installment</option>
                            <option value="fully_paid">Fully paid</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Contract duration</label>
                        <select class="form-select js-contract-duration" name="contract_duration_months" id="reserve_contract_duration" required>
                            <option value="">Select duration…</option>
                            <option value="12">12 months</option>
                            <option value="18">18 months</option>
                            <option value="24">24 months</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Reservation date</label>
                        <input type="date" class="form-control" name="reserved_at" id="reserve_reserved_at" value="{{ now()->format('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Contract amount</label>
                        <input type="number" step="0.01" min="0" class="form-control" name="total_amount" id="reserve_total_amount" placeholder="0.00">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Downpayment</label>
                        <input type="number" step="0.01" min="0" class="form-control" name="amount_paid" id="reserve_amount_paid" placeholder="0.00">
                    </div>
                    <div class="col-12">
                        <div class="form-check">
                            <input class="form-check-input js-email-pdf" type="checkbox" value="1" id="reserve_email_pdf" name="email_pdf" checked>
                            <label class="form-check-label" for="reserve_email_pdf">
                                Email contract PDF to client <span id="reserve_email_target" class="text-muted"></span>
                            </label>
                        </div>
                        <div class="form-text text-warning d-none" id="reserve_no_email_warning">Client has no email on file.</div>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Notes (optional)</label>
                        <textarea class="form-control" name="notes" id="reserve_notes" rows="2"></textarea>
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

<!-- Interment Modal -->
<div class="modal fade" id="intermentLotModal" tabindex="-1" aria-labelledby="intermentLotModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <form method="POST" action="{{ route('admin.interments.store') }}" id="intermentLotForm" enctype="multipart/form-data" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="intermentLotModalLabel">Add Interment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-12">
                        <div class="alert alert-light border small mb-0">
                            <span class="fw-semibold">Lot:</span> <span id="interment_lot_label">—</span>
                            <span class="text-muted mx-2">|</span>
                            <span class="fw-semibold">Owner:</span> <span id="interment_client_name">—</span>
                            <span class="text-muted mx-2">|</span>
                            <span id="interment_client_email_wrap" style="display: none;">
                                <span class="fw-semibold">Email:</span> <span id="interment_client_email">—</span>
                            </span>
                        </div>
                    </div>
                </div>
                <div id="interment_lot_eligibility" class="alert alert-info py-2 px-3 small mt-4" style="display: none;">
                    <span id="interment_lot_eligibility_text"></span>
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-md-6">
                        <label for="interment_first_name" class="form-label fw-semibold">First Name</label>
                        <input type="text" id="interment_first_name" name="first_name" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label for="interment_last_name" class="form-label fw-semibold">Last Name</label>
                        <input type="text" id="interment_last_name" name="last_name" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label for="interment_client_id" class="form-label fw-semibold">Client</label>
                        <select class="form-select" name="client_id" id="interment_client_id" required>
                            <option value="">Select client…</option>
                            @foreach ($clients as $client)
                                <option value="{{ $client->id }}" data-full-name="{{ $client->full_name }}" data-email="{{ $client->email }}">{{ $client->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="interment_lot_id" class="form-label fw-semibold">Lot</label>
                        <input type="hidden" name="lot_id" id="interment_lot_id">
                        <input type="text" class="form-control" id="interment_lot_display" readonly>
                    </div>
                    <div class="col-md-4">
                        <label for="interment_status" class="form-label fw-semibold">Status</label>
                        <select id="interment_status" name="status" class="form-select" required>
                            <option value="pending">Pending</option>
                            <option value="confirmed">Confirmed</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="interment_burial_date" class="form-label fw-semibold">Interment Date</label>
                        <input type="date" id="interment_burial_date" name="burial_date" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label for="interment_date_of_death" class="form-label fw-semibold">Date of Death</label>
                        <input type="date" id="interment_date_of_death" name="date_of_death" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label for="interment_date_of_birth" class="form-label fw-semibold">Date of Birth</label>
                        <input type="date" id="interment_date_of_birth" name="date_of_birth" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label for="interment_death_certificate" class="form-label fw-semibold">Death Certificate</label>
                        <input type="file" id="interment_death_certificate" name="death_certificate" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                    </div>
                    <div class="col-md-4">
                        <label for="interment_burial_permit" class="form-label fw-semibold">Burial Permit</label>
                        <input type="file" id="interment_burial_permit" name="burial_permit" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                    </div>

                    <input type="hidden" name="interment_fee" value="15000">

                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="interment_excavation_scheduled" name="excavation_scheduled" value="1">
                            <label class="form-check-label" for="interment_excavation_scheduled">
                                Excavation Scheduled
                            </label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="interment_excavation_date" class="form-label fw-semibold">Excavation Date</label>
                        <input type="date" id="interment_excavation_date" name="excavation_date" class="form-control">
                    </div>

                    <div class="col-12">
                        <label for="interment_notes" class="form-label fw-semibold">Notes</label>
                        <textarea id="interment_notes" name="notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Interment</button>
            </div>
        </form>
    </div>
</div>

<style>
    /* Map Toolbar */
    .map-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #e9ecef;
        margin-bottom: 1rem;
        flex-wrap: wrap;
    }

    .map-toolbar__left {
        flex-shrink: 0;
    }

    .map-toolbar__title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #0f172a;
        display: flex;
        align-items: center;
    }

    .map-toolbar__center {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .map-toolbar__search {
        position: relative;
        min-width: 220px;
    }

    .map-toolbar__search-icon {
        position: absolute;
        left: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        width: 16px;
        height: 16px;
        color: #94a3b8;
        pointer-events: none;
    }

    .map-toolbar__search .form-control {
        padding-left: 2.5rem;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        font-size: 0.875rem;
        height: 38px;
    }

    .map-toolbar__search .form-control:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.1);
    }

    .map-toolbar__divider {
        width: 1px;
        height: 24px;
        background: #e2e8f0;
    }

    .map-toolbar__draw-tools .btn {
        padding: 0.375rem 0.5rem;
        border-radius: 6px;
    }

    .map-toolbar__draw-tools .btn.active {
        background: #0d6efd;
        border-color: #0d6efd;
        color: #fff;
    }
    
    .map-toolbar__draw-tools .btn:hover:not(.active) {
        background: #f1f5f9;
    }

    .map-toolbar__right {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-shrink: 0;
    }

    .map-toolbar__right .btn {
        border-radius: 8px;
        font-weight: 500;
    }

    .map-toolbar__right .btn i {
        width: 16px;
        height: 16px;
        margin-right: 0.25rem;
    }

    .map-toolbar__status {
        font-size: 0.8rem;
        color: #64748b;
    }

    /* Map Container */
    .map-container {
        height: 520px;
        width: 100%;
        position: relative;
        z-index: 1;
        border-radius: 12px;
        overflow: hidden;
        background: #f8f9fa;
        border: 1px solid #e9ecef;
    }

    /* Legend Styles */
    .lot-legend-container {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        padding: 0.875rem 0;
        border-top: 1px solid #f1f5f9;
        flex-wrap: wrap;
    }

    .lot-legend {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.8rem;
        color: #64748b;
        font-weight: 500;
    }

    .lot-swatch {
        display: inline-block;
        width: 12px;
        height: 12px;
        border-radius: 3px;
        border: 1px solid rgba(0, 0, 0, 0.1);
    }

    .lot-swatch--available { background: #22c55e; }
    .lot-swatch--reserved { background: #3b82f6; }
    .lot-swatch--occupied { background: #ef4444; }

    /* Lot Marker Styles */
    .lot-marker-icon {
        background: transparent !important;
        border: 0 !important;
    }

    .lot-marker {
        width: 14px;
        height: 14px;
        position: relative;
        transform: translateZ(0);
    }

    .lot-marker--available { --lot-color: #28a745; --lot-rgb: 34, 197, 94; }
    .lot-marker--occupied { --lot-color: #dc3545; --lot-rgb: 239, 68, 68; }
    .lot-marker--reserved { --lot-color: #0d6efd; --lot-rgb: 59, 130, 246; }

    .lot-marker__rect {
        width: 14px;
        height: 14px;
        border-radius: 2px;
        background: rgba(var(--lot-rgb), var(--lot-alpha, 0.65));
        border: 1px solid rgba(0, 0, 0, 0.25);
    }

    .lot-marker--new .lot-marker__rect {
        outline: 2px solid rgba(255, 255, 255, 0.95);
        outline-offset: 1px;
        animation: lot-pop 420ms ease-out 1;
    }

    @keyframes lot-pop {
        0% { transform: scale(0.7); }
        100% { transform: scale(1); }
    }

    /* Legend Swatches */
    .lot-swatch {
        display: inline-block;
        width: 12px;
        height: 12px;
        border-radius: 3px;
        border: 1px solid rgba(0, 0, 0, 0.1);
    }

    .lot-swatch--available { background: #22c55e; }
    .lot-swatch--reserved { background: #3b82f6; }
    .lot-swatch--occupied { background: #ef4444; }

    .leaflet-tooltip.lot-hover-tooltip {
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.12);
        border-radius: 10px;
        padding: 8px 12px;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.18);
        color: #212529;
        max-width: 160px;
        font-size: 0.85rem;
    }

    .leaflet-tooltip.lot-hover-tooltip::before {
        border-top-color: rgba(0, 0, 0, 0.12);
    }

    .lot-hover-title {
        font-weight: 700;
        margin-bottom: 0;
    }

    .lot-hover-grid {
        display: flex;
        flex-direction: column;
        gap: 2px;
        margin-bottom: 6px;
    }

    .lot-hover-row {
        display: grid;
        grid-template-columns: 64px 1fr;
        column-gap: 10px;
        align-items: start;
        font-size: 0.8rem;
    }

    .lot-hover-k {
        color: #6c757d;
        font-weight: 500;
    }

    .lot-hover-v {
        color: #212529;
        line-height: 1.25;
    }

    .lot-hover-line {
        font-size: 0.9rem;
        line-height: 1.3;
    }

    /* Leaflet Popup Styles */
    .leaflet-popup-content .btn-lot-reserve {
        background-color: #142C14 !important;
        border-color: #142C14 !important;
        color: #ffffff !important;
        font-size: 0.85rem;
    }

    .leaflet-popup-content .btn-lot-reserve:hover,
    .leaflet-popup-content .btn-lot-reserve:focus {
        background-color: #0f1f0f !important;
        border-color: #0f1f0f !important;
        color: #ffffff !important;
    }

    .leaflet-popup-content .btn-lot-interment {
        background-color: #198754 !important;
        border-color: #198754 !important;
        color: #ffffff !important;
        font-size: 0.85rem;
    }

    .leaflet-popup-content .btn-lot-interment:hover,
    .leaflet-popup-content .btn-lot-interment:focus {
        background-color: #157347 !important;
        border-color: #157347 !important;
        color: #ffffff !important;
    }

    /* Leaflet Control Positioning */
    #map .leaflet-control-container { 
        position: absolute !important; 
        top: 0 !important; 
        left: 0 !important; 
        right: 0 !important; 
        bottom: 0 !important; 
        pointer-events: none; 
    }

    #map .leaflet-control { 
        pointer-events: auto; 
    }

    #map .leaflet-control-zoom { 
        position: absolute !important; 
        top: 12px !important; 
        left: 12px !important; 
        z-index: 999 !important; 
    }

    /* Reservation Modal Styles */
    #reserveLotModal .modal-body {
        overflow: auto;
        max-height: calc(100vh - 220px);
    }

    #reserveLotModal .modal-header {
        border-bottom: 1px solid rgba(15, 23, 42, 0.08);
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    }

    #reserveLotModal .modal-title {
        font-weight: 700;
        color: #0f172a;
    }

    #reserveLotModal .form-label {
        font-weight: 600;
        color: #334155;
        margin-bottom: 0.35rem;
    }

    #reserveLotModal .form-text {
        color: #64748b;
    }

    /* Interment Modal Styles */
    #intermentLotModal .modal-body {
        overflow: auto;
        max-height: calc(100vh - 220px);
    }

    #intermentLotModal .modal-header {
        border-bottom: 1px solid rgba(15, 23, 42, 0.08);
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    }

    #intermentLotModal .modal-title {
        font-weight: 700;
        color: #0f172a;
    }

    #intermentLotModal .form-label {
        font-weight: 600;
        color: #334155;
        margin-bottom: 0.35rem;
    }

    #intermentLotModal .form-text {
        color: #64748b;
    }
</style>

<script>


document.addEventListener('DOMContentLoaded', function() {
    // Ensure the map reflects latest DB state when navigating back after edits/deletes.
    window.addEventListener('pageshow', function (event) {
        try {
            var nav = performance && performance.getEntriesByType ? performance.getEntriesByType('navigation')[0] : null;
            var isBackForward = nav && nav.type === 'back_forward';
            if (event.persisted || isBackForward) {
                window.location.reload();
            }
        } catch (e) {
            // no-op
        }
    });

    var imageUrl = "{{ asset('backend/assets/images/map.jpg') }}";

    // Legacy bounds from the old lat/lng overlay (used only to transform older records).
    var legacyA = [14.5995, 120.9842];
    var legacyB = [14.6000, 120.9850];
    var legacyTop = Math.max(legacyA[0], legacyB[0]);
    var legacyBottom = Math.min(legacyA[0], legacyB[0]);
    var legacyLeft = Math.min(legacyA[1], legacyB[1]);
    var legacyRight = Math.max(legacyA[1], legacyB[1]);

    // Image-coordinate map (no GPS): Leaflet lat=Y(px), lng=X(px).
    var map = L.map('map', {
        crs: L.CRS.Simple,
        minZoom: -5,
        maxZoom: 2,
        zoomSnap: 0.25,
        zoomDelta: 0.25,
        attributionControl: false,
        doubleClickZoom: false,
    });

    var imageW = 1000;
    var imageH = 700;
    var overlayBounds = L.latLngBounds([[0, 0], [imageH, imageW]]);
    var overlay = null;

    function fitMapToOverlay() {
        map.invalidateSize();
        var padding = [16, 16];
        var zoom = map.getBoundsZoom(overlayBounds, true, padding);
        map.setView(overlayBounds.getCenter(), zoom, { animate: false });
    }

        var lots = @json($lots);
    var allLots = lots.slice(); // Keep original unfiltered list
    var reservationsUrl = @json(route('admin.reservations.index'));
    var lotSnapshotUrlTemplate = @json(route('admin.lots.snapshot', ['lot' => 0]));
    var newLotId = @json(session('new_lot_id'));
    var focusedLotId = @json(request('lot'));
    var newLotLayer = null;
    var focusedLotLayer = null;
    var lotRefreshInFlight = {};
    var lotLayers = {}; // Track lot layers for quick access
    var mapViewUrl = @json(route('admin.lots.map'));

    // Search functionality
    var searchInput = document.getElementById('lotSearchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            var query = String(e.target.value || '').toLowerCase().trim();
            
            if (!query) {
                lots = allLots.slice();
            } else {
                lots = allLots.filter(function(lot) {
                    var lotId = String(lot.lot_id || '').toLowerCase();
                    var owner = String(lot.name || '').toLowerCase();
                    return lotId.includes(query) || owner.includes(query);
                });
            }
            
            // Re-render the map
            map.eachLayer(function(layer) {
                if (layer.__lotId !== undefined) {
                    map.removeLayer(layer);
                }
            });
            lotLayers = {};
            renderLots();
        });
    }

    function lotSnapshotUrl(lotId) {
        return String(lotSnapshotUrlTemplate).replace(/\/0\/snapshot$/, '/' + encodeURIComponent(String(lotId)) + '/snapshot');
    }

    loadImageDimensions(imageUrl, function(dim) {
        imageW = dim.width || 1000;
        imageH = dim.height || 700;
        overlayBounds = L.latLngBounds([[0, 0], [imageH, imageW]]);
        overlay = L.imageOverlay(imageUrl, overlayBounds).addTo(map);
        fitMapToOverlay();
        overlay.on('load', fitMapToOverlay);
        window.setTimeout(fitMapToOverlay, 50);
        window.setTimeout(fitMapToOverlay, 250);
        window.addEventListener('resize', fitMapToOverlay);

        renderLots();
        bindDrawingEvents();
    });

    var statusSelect = document.getElementById('modal_status');
    var deceasedFields = document.getElementById('deceased_fields');
    var firstNameInput = document.getElementById('modal_first_name');
    var lastNameInput = document.getElementById('modal_last_name');
    var ownerWrap = document.getElementById('owner_field_wrap');
    var ownerInput = document.getElementById('modal_owner');
    var detailsFields = document.getElementById('details_fields');
    var categorySelect = document.getElementById('modal_category');
    var lotIdInput = document.getElementById('modal_lot_id');
    var lotNumberInput = document.getElementById('modal_lot_number');
    var geometryTypeInput = document.getElementById('modal_geometry_type');
    var geometryInput = document.getElementById('modal_geometry');

    var toolSelectBtn = document.getElementById('toolSelect');
    var toolRectBtn = document.getElementById('toolRect');
    var toolPolyBtn = document.getElementById('toolPoly');
    var finishPolyBtn = document.getElementById('btnFinishPoly');
    var cancelDrawBtn = document.getElementById('btnCancelDraw');

    var currentTool = 'select'; // select | rect | poly
    var pending = null; // { type, dragging?, start?, layer, points? }

    var nextLotNumberUrl = @json(
        \Illuminate\Support\Facades\Route::has('admin.lots.nextLotNumber')
            ? route('admin.lots.nextLotNumber')
            : url('/admin/lots/next-lot-number')
    );

    function prefillLotNumber() {
        if (!lotNumberInput || !lotIdInput) return;

        lotNumberInput.value = '';
        lotIdInput.value = '';

        var category = categorySelect ? String(categorySelect.value || '') : '';

        var url = nextLotNumberUrl + (category ? ('?category=' + encodeURIComponent(category)) : '');

        fetch(url, {
            headers: { 'Accept': 'application/json' }
        })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data && data.lot_number) {
                    lotNumberInput.value = String(data.lot_number);
                    lotIdInput.value = String(data.lot_id || '');
                }
            })
            .catch(function() {
                // If this fails for any reason, the server will still generate a unique number on save.
            });
    }

    var addLotModalEl = document.getElementById('addLotModal');
    if (addLotModalEl) {
        addLotModalEl.addEventListener('show.bs.modal', function() {
            prefillLotNumber();
        });
        addLotModalEl.addEventListener('hidden.bs.modal', function() {
            if (lotNumberInput) lotNumberInput.value = '';
            if (lotIdInput) lotIdInput.value = '';
        });
    }

    if (categorySelect) {
        categorySelect.addEventListener('change', function() {
            prefillLotNumber();
        });
    }

    function syncModalFields() {
        if (!statusSelect) return;

        var status = statusSelect.value;
        var isAvailable = status === 'available';

        var showOwner = !isAvailable;
        if (ownerWrap) ownerWrap.style.display = showOwner ? '' : 'none';
        if (ownerInput) ownerInput.required = showOwner;
        if (!showOwner && ownerInput) ownerInput.value = '';

        var showDetails = !isAvailable;
        if (detailsFields) detailsFields.style.display = showDetails ? '' : 'none';

        var showDeceased = status === 'occupied';
        if (deceasedFields) deceasedFields.style.display = showDeceased ? '' : 'none';
        if (firstNameInput) firstNameInput.required = showDeceased;
        if (lastNameInput) lastNameInput.required = showDeceased;
        if (!showDeceased) {
            if (firstNameInput) firstNameInput.value = '';
            if (lastNameInput) lastNameInput.value = '';
        }
    }

    if (statusSelect) {
        statusSelect.addEventListener('change', syncModalFields);
        syncModalFields();
    }

    // Tooling + drawing behavior replaces the old "click anywhere to add a lot" flow.
    setTool('select');

    if (toolSelectBtn) toolSelectBtn.addEventListener('click', function() { setTool('select'); });
    if (toolRectBtn) toolRectBtn.addEventListener('click', function() { setTool('rect'); });
    if (toolPolyBtn) toolPolyBtn.addEventListener('click', function() { setTool('poly'); });
    if (finishPolyBtn) finishPolyBtn.addEventListener('click', function() { finishPolygon(); });
    if (cancelDrawBtn) cancelDrawBtn.addEventListener('click', function() { cancelPending(); });

    function setTool(tool) {
        currentTool = tool;
        setActiveToolButton(tool);
        if (map && map.dragging) {
            if (tool === 'select') {
                map.dragging.enable();
            } else {
                map.dragging.disable();
            }
        }
        if (cancelDrawBtn) cancelDrawBtn.disabled = tool === 'select';
        if (finishPolyBtn) finishPolyBtn.disabled = !(tool === 'poly' && pending && pending.type === 'poly' && pending.points && pending.points.length >= 3);
        if (tool !== 'poly' && pending && pending.type === 'poly') cancelPending();
        if (tool !== 'rect' && pending && pending.type === 'rect' && pending.dragging) cancelPending();
    }

    function setActiveToolButton(tool) {
        [toolSelectBtn, toolRectBtn, toolPolyBtn].forEach(function(btn) {
            if (!btn) return;
            btn.classList.remove('active');
        });
        var active = tool === 'rect' ? toolRectBtn : (tool === 'poly' ? toolPolyBtn : toolSelectBtn);
        if (active) {
            active.classList.add('active');
        }
        if (window.feather && typeof window.feather.replace === 'function') window.feather.replace();
    }

    function titleStatus(status) {
        return status.charAt(0).toUpperCase() + status.slice(1);
    }

    function categoryLabel(category) {
        switch (String(category || '')) {
            case 'phase_1': return 'Phase 1';
            case 'phase_2': return 'Phase 2';
            case 'garden_lot': return 'Garden Lot';
            case 'back_office_lot': return 'Back Office Lot';
            case 'narra': return 'Narra';
            case 'mausoleum': return 'Mausoleum';
            default: return category || 'N/A';
        }
    }

    function statusStyle(status, isNew) {
        var colors = {
            available: '#198754',
            occupied: '#dc3545',
            reserved: '#0d6efd',
        };
        var fillColor = colors[status] || colors.available;
        return {
            stroke: true,
            color: fillColor,
            weight: 1,
            opacity: 0.8,
            fillColor: fillColor,
            fillOpacity: isNew ? 0.44 : 0.3,
        };
    }

    function lotStatus(lot) {
        var status = (lot && lot.status) ? String(lot.status) : (lot && lot.is_occupied ? 'occupied' : 'available');
        if (status !== 'available' && status !== 'occupied' && status !== 'reserved') status = 'available';
        return status;
    }

    function buildPopupContent(lot) {
        var status = lotStatus(lot);
        var lotIdLabel = lot.lot_id ? String(lot.lot_id) : ('L-' + String(lot.lot_number || lot.id));

        var popupContent = '<b>' + lotIdLabel + '</b><br>' +
            'Owner: ' + (lot.name || '') + '<br>' +
            'Status: ' + titleStatus(status) + '<br>' +
            'Lot Category: ' + categoryLabel(lot.section) + '<br>' +
            'Block: ' + (lot.block || '—') + '<br>';

        if (lot.deceased && lot.deceased.length > 0) {
            lot.deceased.forEach(function(d) {
                popupContent += '<br><strong>Deceased:</strong> ' + d.first_name + ' ' + d.last_name + '<br>';
                if (d.date_of_birth) popupContent += 'Born: ' + d.date_of_birth + '<br>';
                if (d.date_of_death) popupContent += 'Died: ' + d.date_of_death + '<br>';
            });
        } else {
            popupContent += '<br><em>No deceased recorded</em>';
        }

        if (status === 'available') {
            popupContent += '<br><button class="btn btn-sm btn-lot-reserve mt-2" type="button" onclick="openReserveModal(' + lot.id + ', \'' + escapeHtml(lotIdLabel) + '\', \'' + escapeHtml(categoryLabel(lot.section)) + '\')">Reserve this lot</button>';
        }

        if (status === 'reserved') {
            popupContent += '<br><button class="btn btn-sm btn-lot-interment mt-2" type="button" onclick="openIntermentModal(' + lot.id + ', \'' + escapeHtml(lotIdLabel) + '\')">Add Interment</button>';
        }

        return popupContent;
    }

    function buildHoverContent(lot) {
        var lotIdLabel = lot.lot_id ? String(lot.lot_id) : ('L-' + String(lot.lot_number || lot.id));
        return '<div class="lot-hover-title">' + lotIdLabel + '</div>';
    }

    function applyLotToLayer(layer, lot) {
        var status = lotStatus(lot);
        layer.setStyle(statusStyle(status, !!layer.__isNew));

        if (layer.getPopup && layer.getPopup()) {
            layer.getPopup().setContent(buildPopupContent(lot));
        }

        if (layer.getTooltip && layer.getTooltip()) {
            layer.getTooltip().setContent(buildHoverContent(lot));
        }
    }

    function refreshLotLayer(layer) {
        if (!layer || !layer.__lotId) return;
        if (currentTool !== 'select') return;

        var lotId = String(layer.__lotId);
        if (lotRefreshInFlight[lotId]) return;
        lotRefreshInFlight[lotId] = true;

        fetch(lotSnapshotUrl(lotId), { headers: { 'Accept': 'application/json' } })
            .then(function(r) { return r.ok ? r.json() : Promise.reject(); })
            .then(function(data) {
                var lot = data && data.lot ? data.lot : null;
                if (!lot) return;

                for (var i = 0; i < lots.length; i++) {
                    if (String(lots[i].id) === String(lot.id)) {
                        lots[i] = lot;
                        break;
                    }
                }

                applyLotToLayer(layer, lot);
            })
            .catch(function() {
                // no-op
            })
            .then(function() {
                lotRefreshInFlight[lotId] = false;
            });
    }

    function lotAnchorLatLng(lot) {
        var lat = Number(lot.latitude);
        var lng = Number(lot.longitude);
        if (!Number.isFinite(lat) || !Number.isFinite(lng)) return null;

        // New system (image coordinates): 0..H and 0..W.
        if (lat >= 0 && lng >= 0 && lat <= imageH && lng <= imageW) {
            return L.latLng(lat, lng);
        }

        // Legacy lat/lng -> image coordinates.
        if (lat >= legacyBottom && lat <= legacyTop && lng >= legacyLeft && lng <= legacyRight) {
            var x = ((lng - legacyLeft) / (legacyRight - legacyLeft)) * imageW;
            var y = ((legacyTop - lat) / (legacyTop - legacyBottom)) * imageH;
            return L.latLng(y, x);
        }

        return null;
    }

    function lotToLayer(lot, style) {
        if (lot.geometry_type && lot.geometry) {
            if (lot.geometry_type === 'rect' && lot.geometry.x != null) {
                var x = Number(lot.geometry.x);
                var y = Number(lot.geometry.y);
                var w = Number(lot.geometry.w);
                var h = Number(lot.geometry.h);
                if ([x, y, w, h].some(function(v) { return !Number.isFinite(v); })) return null;
                return L.rectangle([[y, x], [y + h, x + w]], style);
            }
            if (lot.geometry_type === 'poly' && lot.geometry.points && Array.isArray(lot.geometry.points)) {
                var latlngs = lot.geometry.points
                    .map(function(p) { return [Number(p.y), Number(p.x)]; })
                    .filter(function(p) { return Number.isFinite(p[0]) && Number.isFinite(p[1]); });
                if (latlngs.length < 3) return null;
                return L.polygon(latlngs, style);
            }
        }

        var c = lotAnchorLatLng(lot);
        if (!c) return null;
        var s = 10;
        return L.rectangle([[c.lat - s / 2, c.lng - s / 2], [c.lat + s / 2, c.lng + s / 2]], style);
    }

    function renderLots() {
        lots.forEach(function(lot) {
            var isNew = newLotId !== null && String(lot.id) === String(newLotId);
            var status = lotStatus(lot);

            var layer = lotToLayer(lot, statusStyle(status, isNew));
            if (!layer) return;

            var lotIdLabel = lot.lot_id ? String(lot.lot_id) : ('L-' + String(lot.lot_number || lot.id));
            var popupContent = '<b>' + lotIdLabel + '</b><br>' +
                'Owner: ' + lot.name + '<br>' +
                'Status: ' + titleStatus(status) + '<br>' +
                'Lot Category: ' + categoryLabel(lot.section) + '<br>' +
                'Block: ' + (lot.block || '—') + '<br>';

            if (lot.deceased && lot.deceased.length > 0) {
                lot.deceased.forEach(function(d) {
                    popupContent += '<br><strong>Deceased:</strong> ' + d.first_name + ' ' + d.last_name + '<br>';
                    if (d.date_of_birth) popupContent += 'Born: ' + d.date_of_birth + '<br>';
                    if (d.date_of_death) popupContent += 'Died: ' + d.date_of_death + '<br>';
                });
            } else {
                popupContent += '<br><em>No deceased recorded</em>';
            }

            if (status === 'available') {
                popupContent += '<br><button class="btn btn-sm btn-lot-reserve mt-2" type="button" onclick="openReserveModal(' + lot.id + ', \'' + escapeHtml(lotIdLabel) + '\', \'' + escapeHtml(categoryLabel(lot.section)) + '\')">Reserve this lot</button>';
            }

            if (status === 'reserved') {
                popupContent += '<br><button class="btn btn-sm btn-lot-interment mt-2" type="button" onclick="openIntermentModal(' + lot.id + ', \'' + escapeHtml(lotIdLabel) + '\')">Add Interment</button>';
            }

            var hoverContent = '<div class="lot-hover-title">' + lotIdLabel + '</div>' +
                '<div class="lot-hover-grid">' +
                    '<div class="lot-hover-row"><div class="lot-hover-k">Owner</div><div class="lot-hover-v">' + lot.name + '</div></div>' +
                    '<div class="lot-hover-row"><div class="lot-hover-k">Status</div><div class="lot-hover-v">' + titleStatus(status) + '</div></div>' +
                    '<div class="lot-hover-row"><div class="lot-hover-k">Lot Category</div><div class="lot-hover-v">' + categoryLabel(lot.section) + '</div></div>' +
                    '<div class="lot-hover-row"><div class="lot-hover-k">Block</div><div class="lot-hover-v">' + (lot.block || '—') + '</div></div>' +
                '</div>';

            if (lot.deceased && lot.deceased.length > 0) {
                var d0 = lot.deceased[0];
                hoverContent += '<div class="lot-hover-line"><strong>' + d0.first_name + ' ' + d0.last_name + '</strong></div>';
                if (lot.deceased.length > 1) {
                    hoverContent += '<div class="lot-hover-line" style="color:#6c757d;">+' + (lot.deceased.length - 1) + ' more</div>';
                }
            } else if (status === 'occupied') {
                hoverContent += '<div class="lot-hover-line"><em>No deceased recorded</em></div>';
            }

            // Cursor hover should only show the plot/lot ID.
            hoverContent = '<div class="lot-hover-title">' + lotIdLabel + '</div>';

            layer.addTo(map);
            lotLayers[lot.id] = layer;
            layer.bindPopup(popupContent);
            layer.bindTooltip(hoverContent, {
                className: 'lot-hover-tooltip',
                direction: 'top',
                opacity: 1,
                sticky: true,
            });

            layer.__lotId = lot.id;
            layer.__isNew = isNew;
            layer.on('popupopen', function() { refreshLotLayer(this); });

            if (isNew) newLotLayer = layer;
            if (focusedLotId !== null && String(lot.id) === String(focusedLotId)) {
                focusedLotLayer = layer;
            }
        });

        var priorityLayer = focusedLotLayer || newLotLayer;
        if (priorityLayer && priorityLayer.getBounds) {
            map.fitBounds(priorityLayer.getBounds().pad(0.6), { animate: true });
            priorityLayer.openTooltip();
        }
    }

    var drawingBound = false;
    function bindDrawingEvents() {
        if (drawingBound) return;
        drawingBound = true;

        map.on('mousedown', function(e) {
            if (currentTool !== 'rect') return;
            cancelPending();
            pending = {
                type: 'rect',
                dragging: true,
                start: e.latlng,
                layer: L.rectangle([e.latlng, e.latlng], {
                    stroke: true,
                    color: '#0dcaf0',
                    weight: 1,
                    opacity: 0.75,
                    fillColor: '#0dcaf0',
                    fillOpacity: 0.3,
                }).addTo(map),
            };
            if (cancelDrawBtn) cancelDrawBtn.disabled = false;
        });

        map.on('mousemove', function(e) {
            if (!pending || pending.type !== 'rect' || !pending.dragging) return;
            pending.layer.setBounds([pending.start, e.latlng]);
        });

        map.on('mouseup', function() {
            if (!pending || pending.type !== 'rect' || !pending.dragging) return;
            pending.dragging = false;
            var b = pending.layer.getBounds();
            var sw = b.getSouthWest();
            var ne = b.getNorthEast();
            var w = Math.abs(ne.lng - sw.lng);
            var h = Math.abs(ne.lat - sw.lat);
            if (w < 4 || h < 4) {
                cancelPending();
                return;
            }
            var x = Math.min(sw.lng, ne.lng);
            var y = Math.min(sw.lat, ne.lat);
            openAddLotModal('rect', { x: x, y: y, w: w, h: h }, { lat: y + h / 2, lng: x + w / 2 });
        });

        map.on('click', function(e) {
            if (currentTool !== 'poly') return;
            if (!pending || pending.type !== 'poly') {
                cancelPending();
                pending = {
                    type: 'poly',
                    points: [],
                    layer: L.polygon([], {
                        stroke: false,
                        color: '#0dcaf0',
                        weight: 0,
                        opacity: 0,
                        fillColor: '#0dcaf0',
                        fillOpacity: 0.3,
                    }).addTo(map),
                };
            }
            pending.points.push(e.latlng);
            pending.layer.setLatLngs(pending.points);
            if (finishPolyBtn) finishPolyBtn.disabled = pending.points.length < 3;
            if (cancelDrawBtn) cancelDrawBtn.disabled = false;
        });

        map.on('dblclick', function() {
            if (currentTool === 'poly') finishPolygon();
        });
    }

    function finishPolygon() {
        if (!pending || pending.type !== 'poly') return;
        if (!pending.points || pending.points.length < 3) return;
        var points = pending.points;
        var geometry = { points: points.map(function(p) { return { x: p.lng, y: p.lat }; }) };
        var sumLat = 0;
        var sumLng = 0;
        points.forEach(function(p) { sumLat += p.lat; sumLng += p.lng; });
        openAddLotModal('poly', geometry, { lat: sumLat / points.length, lng: sumLng / points.length });
    }

    function cancelPending() {
        if (pending && pending.layer) map.removeLayer(pending.layer);
        pending = null;
        if (finishPolyBtn) finishPolyBtn.disabled = true;
        if (cancelDrawBtn) cancelDrawBtn.disabled = currentTool === 'select';
    }

    function openAddLotModal(type, geometry, centroid) {
        if (geometryTypeInput) geometryTypeInput.value = type;
        if (geometryInput) geometryInput.value = JSON.stringify(geometry);
        document.getElementById('modal_latitude').value = Number(centroid.lat).toFixed(2);
        document.getElementById('modal_longitude').value = Number(centroid.lng).toFixed(2);
        syncModalFields();

        var modalEl = document.getElementById('addLotModal');
        var modal = new bootstrap.Modal(modalEl);
        modal.show();

        modalEl.addEventListener('hidden.bs.modal', function onHidden() {
            modalEl.removeEventListener('hidden.bs.modal', onHidden);
            cancelPending();
            if (geometryTypeInput) geometryTypeInput.value = '';
            if (geometryInput) geometryInput.value = '';
        });
    }

    function loadImageDimensions(src, cb) {
        var img = new Image();
        img.onload = function() { cb({ width: img.naturalWidth, height: img.naturalHeight }); };
        img.onerror = function() { cb({ width: 1000, height: 700 }); };
        img.src = src;
    }

    function escapeHtml(text) {
        var div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML.replace(/'/g, "\\'").replace(/"/g, '\\"');
    }

    window.openReserveModal = function(lotId, lotLabel, lotCategory) {
        var lotSelect = document.getElementById('reserve_lot_id');
        var lotLabelDisplay = document.getElementById('reserve_lot_label');
        var lotCategoryDisplay = document.getElementById('reserve_lot_category_display');
        
        if (lotSelect) lotSelect.value = lotId;
        if (lotLabelDisplay) lotLabelDisplay.textContent = lotLabel;
        if (lotCategoryDisplay) lotCategoryDisplay.value = lotCategory || '—';

        var modalEl = document.getElementById('reserveLotModal');
        if (modalEl) {
            var modal = new bootstrap.Modal(modalEl);
            modal.show();
        }
    };

    var reserveForm = document.getElementById('reserveLotForm');
    if (reserveForm) {
        reserveForm.addEventListener('submit', function(e) {
            e.preventDefault();
            var formData = new FormData(reserveForm);
            var submitBtn = reserveForm.querySelector('button[type="submit"]');
            var originalText = submitBtn ? submitBtn.textContent : 'Save';
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Saving...';
            }

            fetch(reserveForm.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'Accept': 'application/json',
                },
                body: formData,
            })
            .then(function(response) {
                if (response.redirected) {
                    window.location.href = response.url || mapViewUrl;
                    return;
                }
                return response.json();
            })
            .then(function(data) {
                if (data && data.success) {
                    var modalEl = document.getElementById('reserveLotModal');
                    var modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) modal.hide();
                    // Refresh the map to show updated lot status
                    window.location.reload();
                } else if (data && data.errors) {
                    var errorMessages = Object.values(data.errors).flat().join('\n');
                    alert('Error: ' + errorMessages);
                }
            })
            .catch(function(error) {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            })
            .finally(function() {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                }
            });
        });
    }

    var reserveClientSelect = document.getElementById('reserve_client_id');
    var reserveEmailTarget = document.getElementById('reserve_email_target');
    var reserveEmailCheckbox = document.getElementById('reserve_email_pdf');
    var reserveNoEmailWarning = document.getElementById('reserve_no_email_warning');
    var reserveLotSelect = document.getElementById('reserve_lot_id');
    var reserveLotCategoryDisplay = document.getElementById('reserve_lot_category_display');

    function syncReserveEmailUi() {
        if (!reserveClientSelect || !reserveEmailTarget) return;
        var opt = reserveClientSelect.selectedOptions && reserveClientSelect.selectedOptions[0];
        var email = opt ? String(opt.getAttribute('data-email') || '').trim() : '';
        reserveEmailTarget.textContent = email ? '(' + email + ')' : '(no email)';
        var wantsEmail = reserveEmailCheckbox && reserveEmailCheckbox.checked;
        var showWarn = wantsEmail && !email;
        if (reserveNoEmailWarning) {
            reserveNoEmailWarning.classList.toggle('d-none', !showWarn);
        }
    }

    function syncReserveLotCategory() {
        if (!reserveLotSelect || !reserveLotCategoryDisplay) return;
        var opt = reserveLotSelect.selectedOptions && reserveLotSelect.selectedOptions[0];
        var category = opt ? String(opt.getAttribute('data-lot-category') || '').trim() : '';
        reserveLotCategoryDisplay.value = category !== '' ? category : '—';
    }

    if (reserveClientSelect) reserveClientSelect.addEventListener('change', syncReserveEmailUi);
    if (reserveEmailCheckbox) reserveEmailCheckbox.addEventListener('change', syncReserveEmailUi);
    if (reserveLotSelect) reserveLotSelect.addEventListener('change', syncReserveLotCategory);
    syncReserveEmailUi();

    // Interment Modal
    window.openIntermentModal = function(lotId, lotLabel) {
        var lotIdInput = document.getElementById('interment_lot_id');
        var lotDisplay = document.getElementById('interment_lot_display');
        var lotLabelDisplay = document.getElementById('interment_lot_label');
        var eligibilityInfo = document.getElementById('interment_lot_eligibility');
        var eligibilityText = document.getElementById('interment_lot_eligibility_text');
        var clientSelect = document.getElementById('interment_client_id');
        var submitBtn = document.querySelector('#intermentLotForm button[type="submit"]');
        var modalEl = document.getElementById('intermentLotModal');
        
        if (lotIdInput) lotIdInput.value = lotId;
        if (lotDisplay) lotDisplay.value = lotLabel;
        if (lotLabelDisplay) lotLabelDisplay.textContent = lotLabel;

        // Show modal immediately
        if (modalEl) {
            var modal = new bootstrap.Modal(modalEl);
            modal.show();
        }

        // Show loading state
        if (eligibilityInfo) {
            eligibilityInfo.style.display = 'block';
            eligibilityInfo.className = 'alert alert-info py-2 px-3 small mb-3';
            eligibilityText.textContent = 'Loading lot information...';
        }

        // Fetch lot info asynchronously
        fetch('/admin/interments/api/lot-info?lot_id=' + encodeURIComponent(lotId))
            .then(function(response) { return response.json(); })
            .then(function(data) {
                var clientNameEl = document.getElementById('interment_client_name');
                var clientEmailEl = document.getElementById('interment_client_email');
                var clientEmailWrap = document.getElementById('interment_client_email_wrap');
                
                // Auto-populate client if available
                if (data.client && clientSelect) {
                    clientSelect.value = data.client.id;
                    if (clientNameEl) clientNameEl.textContent = data.client.full_name;
                    if (clientEmailEl) clientEmailEl.textContent = data.client.email || '—';
                    if (clientEmailWrap) {
                        clientEmailWrap.style.display = data.client.email ? 'inline' : 'none';
                    }
                } else {
                    if (clientNameEl) clientNameEl.textContent = '—';
                    if (clientEmailEl) clientEmailEl.textContent = '—';
                    if (clientEmailWrap) clientEmailWrap.style.display = 'none';
                }

                // Show eligibility status
                if (!data.eligible) {
                    if (eligibilityInfo) {
                        eligibilityInfo.style.display = 'block';
                        eligibilityInfo.className = 'alert alert-danger py-2 px-3 small mb-3';
                        eligibilityText.textContent = data.reason || 'Cannot add interment to this lot.';
                    }
                    if (submitBtn) submitBtn.disabled = true;
                } else {
                    if (eligibilityInfo) {
                        eligibilityInfo.style.display = 'block';
                        eligibilityInfo.className = 'alert alert-success py-2 px-3 small mb-3';
                        eligibilityText.textContent = 'Lot eligible (' + data.interment_count + '/' + data.max_interments + ' interments). Owner: ' + (data.client ? data.client.full_name : 'Not registered');
                    }
                    if (submitBtn) submitBtn.disabled = false;
                }
            })
            .catch(function(error) {
                console.error('Error fetching lot info:', error);
                if (eligibilityInfo) {
                    eligibilityInfo.style.display = 'block';
                    eligibilityInfo.className = 'alert alert-warning py-2 px-3 small mb-3';
                    eligibilityText.textContent = 'Could not load lot information.';
                }
                if (submitBtn) submitBtn.disabled = false;
            });
    };

    // Update info bar when client selection changes
    var intermentClientSelect = document.getElementById('interment_client_id');
    if (intermentClientSelect) {
        intermentClientSelect.addEventListener('change', function() {
            var clientNameEl = document.getElementById('interment_client_name');
            var clientEmailEl = document.getElementById('interment_client_email');
            var clientEmailWrap = document.getElementById('interment_client_email_wrap');
            var selectedOption = this.options[this.selectedIndex];
            
            if (selectedOption && selectedOption.value) {
                var fullName = selectedOption.getAttribute('data-full-name') || selectedOption.textContent;
                var email = selectedOption.getAttribute('data-email') || '';
                if (clientNameEl) clientNameEl.textContent = fullName;
                if (clientEmailEl) clientEmailEl.textContent = email || '—';
                if (clientEmailWrap) clientEmailWrap.style.display = email ? 'inline' : 'none';
            } else {
                if (clientNameEl) clientNameEl.textContent = '—';
                if (clientEmailEl) clientEmailEl.textContent = '—';
                if (clientEmailWrap) clientEmailWrap.style.display = 'none';
            }
        });
    }

    var intermentForm = document.getElementById('intermentLotForm');
    if (intermentForm) {
        intermentForm.addEventListener('submit', function(e) {
            e.preventDefault();
            var formData = new FormData(intermentForm);
            var submitBtn = intermentForm.querySelector('button[type="submit"]');
            var originalText = submitBtn ? submitBtn.textContent : 'Save';
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Saving...';
            }

            fetch(intermentForm.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                },
                body: formData,
            })
            .then(function(response) {
                if (response.ok) {
                    var modalEl = document.getElementById('intermentLotModal');
                    var modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) modal.hide();
                    window.location.reload();
                } else if (response.status === 422) {
                    return response.json().then(function(data) {
                        var errorMessages = Object.values(data.errors || {}).flat().join('\n') || 'Validation failed.';
                        alert('Error: ' + errorMessages);
                        throw new Error(errorMessages);
                    });
                } else {
                    alert('An error occurred. Please try again.');
                    throw new Error('Request failed');
                }
            })
            .catch(function(error) {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            })
            .finally(function() {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                }
            });
        });
    }

    if (window.feather && typeof window.feather.replace === 'function') window.feather.replace();
});
</script>
@endsection
