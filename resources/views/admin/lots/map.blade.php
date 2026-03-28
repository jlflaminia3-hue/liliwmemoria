@extends('admin.admin_master')

@section('admin')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title">Cemetery Map</h4>
                    <div>
                        <div class="btn-group" role="group" aria-label="Map tools">
                            <button type="button" class="btn btn-outline-secondary" id="toolSelect">Select</button>
                            <button type="button" class="btn btn-outline-secondary" id="toolRect">
                                <i data-feather="plus"></i> Rectangle
                            </button>
                            <button type="button" class="btn btn-outline-secondary" id="toolPoly">Polygon</button>
                            <button type="button" class="btn btn-outline-secondary" id="btnFinishPoly" disabled>Finish</button>
                            <button type="button" class="btn btn-outline-secondary" id="btnCancelDraw" disabled>Cancel</button>
                        </div>
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
                        <div class="col-md-5 mb-3" id="owner_field_wrap">
                            <label class="form-label">Lot Owner</label>
                            <input type="text" name="name" id="modal_owner" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Phase</label>
                            <input type="text" name="section" class="form-control">
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
    }

    .lot-hover-k {
        font-size: 0.8rem;
        color: #6c757d;
    }

    .lot-hover-v {
        font-size: 0.85rem;
        color: #212529;
        line-height: 1.25;
    }

    .lot-hover-line {
        font-size: 0.9rem;
        line-height: 1.3;
    }
</style>

<script>


document.addEventListener('DOMContentLoaded', function() {
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
    var newLotId = @json(session('new_lot_id'));
    var newLotLayer = null;

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

    if (false) {
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
        popupContent += 'Phase: ' + (lot.section || 'N/A') + '<br>';
        
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
            '<div class="lot-hover-grid">' +
                '<div class="lot-hover-row"><div class="lot-hover-k">Owner</div><div class="lot-hover-v">' + lot.name + '</div></div>' +
                '<div class="lot-hover-row"><div class="lot-hover-k">Status</div><div class="lot-hover-v">' + statusLabel + '</div></div>' +
                '<div class="lot-hover-row"><div class="lot-hover-k">Phase</div><div class="lot-hover-v">' + (lot.section || 'N/A') + '</div></div>' +
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
            btn.classList.remove('btn-secondary');
            btn.classList.add('btn-outline-secondary');
        });
        var active = tool === 'rect' ? toolRectBtn : (tool === 'poly' ? toolPolyBtn : toolSelectBtn);
        if (active) {
            active.classList.remove('btn-outline-secondary');
            active.classList.add('btn-secondary');
        }
    }

    function titleStatus(status) {
        return status.charAt(0).toUpperCase() + status.slice(1);
    }

    function statusStyle(status, isNew) {
        var colors = {
            available: { stroke: '#198754', fill: 'rgba(25, 135, 84, 0.22)' },
            occupied: { stroke: '#dc3545', fill: 'rgba(220, 53, 69, 0.22)' },
            reserved: { stroke: '#0d6efd', fill: 'rgba(13, 110, 253, 0.22)' },
        };
        var c = colors[status] || colors.available;
        return {
            color: c.stroke,
            weight: isNew ? 4 : 2,
            opacity: 1,
            fillColor: c.fill,
            fillOpacity: 0.35,
            dashArray: isNew ? '6 6' : null,
        };
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
            var status = lot.status || (lot.is_occupied ? 'occupied' : 'available');
            if (status !== 'available' && status !== 'occupied' && status !== 'reserved') status = 'available';

            var layer = lotToLayer(lot, statusStyle(status, isNew));
            if (!layer) return;

            var lotNumberLabel = (lot.lot_number ? ('Lot #' + lot.lot_number) : ('Lot #' + lot.id));
            var popupContent = '<b>' + lotNumberLabel + '</b><br>' +
                'Owner: ' + lot.name + '<br>' +
                'Status: ' + titleStatus(status) + '<br>' +
                'Phase: ' + (lot.section || 'N/A') + '<br>';

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
                '<div class="lot-hover-grid">' +
                    '<div class="lot-hover-row"><div class="lot-hover-k">Owner</div><div class="lot-hover-v">' + lot.name + '</div></div>' +
                    '<div class="lot-hover-row"><div class="lot-hover-k">Status</div><div class="lot-hover-v">' + titleStatus(status) + '</div></div>' +
                    '<div class="lot-hover-row"><div class="lot-hover-k">Phase</div><div class="lot-hover-v">' + (lot.section || 'N/A') + '</div></div>' +
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

            layer.addTo(map);
            layer.bindPopup(popupContent);
            layer.bindTooltip(hoverContent, {
                className: 'lot-hover-tooltip',
                direction: 'top',
                opacity: 1,
                sticky: true,
            });

            if (isNew) newLotLayer = layer;
        });

        if (newLotLayer && newLotLayer.getBounds) {
            map.fitBounds(newLotLayer.getBounds().pad(0.6), { animate: true });
            newLotLayer.openTooltip();
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
                    color: '#0dcaf0',
                    weight: 2,
                    fillColor: 'rgba(13, 202, 240, 0.15)',
                    fillOpacity: 0.25,
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
                    layer: L.polyline([], { color: '#0dcaf0', weight: 2 }).addTo(map),
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
});
</script>
@endsection
