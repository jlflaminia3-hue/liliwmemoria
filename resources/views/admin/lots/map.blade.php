@extends('admin.admin_master')

@section('admin')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title">Cemetery Map</h4>
                    <div>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLotModal">
                            <i data-feather="plus"></i> Add Lot
                        </button>
                    </div>
                </div>

                <div id="map" style="height: 500px; width: 100%;"></div>

                <div class="mt-3">
                    <span class="lot-legend me-3"><span class="lot-swatch lot-swatch--available"></span> Available</span>
                    <span class="lot-legend me-3"><span class="lot-swatch lot-swatch--occupied"></span> Occupied</span>
                    <span class="lot-legend me-3"><span class="lot-swatch lot-swatch--reserved"></span> Reserved</span>
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
                            <label class="form-label">Lot Number</label>
                            <input type="text" name="lot_number" id="modal_lot_number" class="form-control" readonly>
                            <div class="form-text">Auto-generated.</div>
                        </div>
                        <div class="col-md-5 mb-3">
                            <label class="form-label">Lot Owner</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Section</label>
                            <input type="text" name="section" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Lot Status</label>
                            <select name="status" id="modal_status" class="form-select" required>
                                <option value="available">Available (Green)</option>
                                <option value="occupied" selected>Occupied (Red)</option>
                                <option value="reserved">Reserved (Blue)</option>
                            </select>
                        </div>
                    </div>
                    <input type="hidden" name="latitude" id="modal_latitude">
                    <input type="hidden" name="longitude" id="modal_longitude">

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
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
 </div>

<style>
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

    .lot-marker--available {
        --lot-color: #28a745;
        --lot-rgb: 40, 167, 69;
    }

    .lot-marker--occupied {
        --lot-color: #dc3545;
        --lot-rgb: 220, 53, 69;
    }

    .lot-marker--reserved {
        --lot-color: #0d6efd;
        --lot-rgb: 13, 110, 253;
    }

    /* Simple flat rectangle marker (like the reference image) */
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

    .lot-legend {
        display: inline-flex;
        align-items: center;
        font-size: 0.9rem;
        color: #6c757d;
    }

    .lot-swatch {
        display: inline-block;
        width: 14px;
        height: 14px;
        border-radius: 2px;
        border: 1px solid rgba(0, 0, 0, 0.25);
        margin-right: 8px;
        background: rgba(var(--lot-rgb), var(--lot-alpha, 0.65));
    }

    .lot-swatch--available { --lot-rgb: 40, 167, 69; }
    .lot-swatch--occupied { --lot-rgb: 220, 53, 69; }
    .lot-swatch--reserved { --lot-rgb: 13, 110, 253; }

    .leaflet-tooltip.lot-hover-tooltip {
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.12);
        border-radius: 10px;
        padding: 10px 12px;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.18);
        color: #212529;
        max-width: 260px;
    }

    .leaflet-tooltip.lot-hover-tooltip::before {
        border-top-color: rgba(0, 0, 0, 0.12);
    }

    .lot-hover-title {
        font-weight: 700;
        margin-bottom: 4px;
    }

    .lot-hover-meta {
        font-size: 0.85rem;
        color: #6c757d;
        margin-bottom: 6px;
    }

    .lot-hover-line {
        font-size: 0.9rem;
        line-height: 1.3;
    }
</style>

<script>


document.addEventListener('DOMContentLoaded', function() {
    var map = L.map('map').setView([14.5995, 120.9842], 15);

    var imageUrl = "{{ asset('backend/assets/images/map.jpg') }}";
    var imageBounds = [[14.5995, 120.9842], [14.6000, 120.9850]];
    var overlay = L.imageOverlay(imageUrl, imageBounds).addTo(map);

    var overlayBounds = L.latLngBounds(imageBounds);

    function fitMapToOverlay() {
        // Fix common Leaflet issue where the map doesn't fill its container on refresh until a zoom/resize happens.
        map.invalidateSize();

        // "Cover" behavior (like your 2nd screenshot): zoom in so the overlay fills the container,
        // even if that means cropping a bit of the overlay edges.
        var padding = [16, 16];
        var zoom = map.getBoundsZoom(overlayBounds, true, padding);
        map.setView(overlayBounds.getCenter(), zoom, { animate: false });
    }

    // Set map to image bounds (presentable default zoom).
    fitMapToOverlay();

    // Ensure correct sizing after layout settles / image loads.
    overlay.on('load', fitMapToOverlay);
    window.setTimeout(fitMapToOverlay, 50);
    window.setTimeout(fitMapToOverlay, 250);
    window.addEventListener('resize', fitMapToOverlay);


    var lots = @json($lots);
    var newLotId = @json(session('new_lot_id'));
    var newLotMarker = null;

    var statusSelect = document.getElementById('modal_status');
    var deceasedFields = document.getElementById('deceased_fields');
    var firstNameInput = document.getElementById('modal_first_name');
    var lastNameInput = document.getElementById('modal_last_name');
    var lotNumberInput = document.getElementById('modal_lot_number');
    var nextLotNumberUrl = @json(
        \Illuminate\Support\Facades\Route::has('admin.lots.nextLotNumber')
            ? route('admin.lots.nextLotNumber')
            : url('/admin/lots/next-lot-number')
    );

    function prefillLotNumber() {
        if (!lotNumberInput) return;

        lotNumberInput.value = '';

        fetch(nextLotNumberUrl, {
            headers: { 'Accept': 'application/json' }
        })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data && data.lot_number) {
                    lotNumberInput.value = String(data.lot_number);
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
        });
    }

    function syncDeceasedFields() {
        if (!statusSelect || !deceasedFields) return;
        var showDeceased = statusSelect.value === 'occupied';
        deceasedFields.style.display = showDeceased ? '' : 'none';
        if (firstNameInput) firstNameInput.required = showDeceased;
        if (lastNameInput) lastNameInput.required = showDeceased;
    }

    if (statusSelect) {
        statusSelect.addEventListener('change', syncDeceasedFields);
        syncDeceasedFields();
    }

    lots.forEach(function(lot) {
        var isNew = newLotId !== null && String(lot.id) === String(newLotId);

        var status = lot.status || (lot.is_occupied ? 'occupied' : 'available');
        if (status !== 'available' && status !== 'occupied' && status !== 'reserved') {
            status = 'available';
        }

        var markerClass = 'lot-marker--' + status;
        if (isNew) markerClass += ' lot-marker--new';

        var markerHtml =
            '<div class="lot-marker ' + markerClass + '">' +
                '<div class="lot-marker__rect"></div>' +
            '</div>';

        var icon = L.divIcon({
            className: 'lot-marker-icon',
            html: markerHtml,
            iconSize: [14, 14],
            iconAnchor: [7, 7],
            popupAnchor: [0, -12],
        });

        var statusLabel = status.charAt(0).toUpperCase() + status.slice(1);

        var lotNumberLabel = (lot.lot_number ? ('Lot #' + lot.lot_number) : ('Lot #' + lot.id));
        var popupContent = '<b>' + lotNumberLabel + '</b><br>';
        popupContent += 'Owner: ' + lot.name + '<br>';
        popupContent += 'Status: ' + statusLabel + '<br>';
        popupContent += 'Section: ' + (lot.section || 'N/A') + '<br>';
        
        if (lot.deceased && lot.deceased.length > 0) {
            lot.deceased.forEach(function(d) {
                popupContent += '<br><strong>Deceased:</strong> ' + d.first_name + ' ' + d.last_name + '<br>';
                if (d.date_of_birth) popupContent += 'Born: ' + d.date_of_birth + '<br>';
                if (d.date_of_death) popupContent += 'Died: ' + d.date_of_death + '<br>';
            });
        } else {
            popupContent += '<br><em>No deceased recorded</em>';
        }

        var hoverContent = '<div class="lot-hover-title">' + lotNumberLabel + '</div>' +
            '<div class="lot-hover-meta">' + lot.name + ' • ' + statusLabel + (lot.section ? (' • ' + lot.section) : '') + '</div>';

        if (lot.deceased && lot.deceased.length > 0) {
            var d0 = lot.deceased[0];
            hoverContent += '<div class="lot-hover-line"><strong>' + d0.first_name + ' ' + d0.last_name + '</strong></div>';
            if (lot.deceased.length > 1) {
                hoverContent += '<div class="lot-hover-line" style="color:#6c757d;">+' + (lot.deceased.length - 1) + ' more</div>';
            }
        } else if (status === 'occupied') {
            hoverContent += '<div class="lot-hover-line"><em>No deceased recorded</em></div>';
        }

        var marker = L.marker([lot.latitude, lot.longitude], {icon: icon}).addTo(map);
        marker.bindPopup(popupContent);
        marker.bindTooltip(hoverContent, {
            className: 'lot-hover-tooltip',
            direction: 'top',
            opacity: 1,
            offset: [0, -10],
        });

        if (isNew) {
            newLotMarker = marker;
        }
    });

    if (newLotMarker) {
        // Bring the newly-added lot into focus after saving.
        map.panTo(newLotMarker.getLatLng(), { animate: true, duration: 0.6 });
        newLotMarker.openTooltip();
    }

    map.on('click', function(e) {
        document.getElementById('modal_latitude').value = e.latlng.lat.toFixed(6);
        document.getElementById('modal_longitude').value = e.latlng.lng.toFixed(6);
        syncDeceasedFields();
        var modal = new bootstrap.Modal(document.getElementById('addLotModal'));
        modal.show();
    });
});
</script>
@endsection
