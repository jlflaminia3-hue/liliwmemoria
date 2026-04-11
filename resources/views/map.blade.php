@extends('home.home_master')

@section('home')
<style>
    .visit-page { padding-top: 120px !important; }
    #publicMap { height: 720px; width: 100%; border-radius: 12px; overflow: hidden; }
    .map-search { position: relative; }
    .map-search-results { position: absolute; top: calc(100% + 6px); left: 0; right: 0; z-index: 1000; max-height: 320px; overflow: auto; }
    .map-search-results .list-group-item { cursor: pointer; }
</style>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<section class="lonyo-section-padding6 visit-page">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-11">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                    <div>
                        <h3 class="mb-1">Cemetery Map</h3>
                        <div class="text-muted">Search a name to locate the lot on the map.</div>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('public.visit.create') }}" class="btn btn-outline-secondary">Visitor Log</a>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-3 p-md-4">
                        <div class="map-search mb-3">
                            <input type="text" id="searchInput" class="form-control" placeholder="Search deceased name...">
                            <div id="searchResults" class="map-search-results list-group d-none"></div>
                        </div>

                        <div id="publicMap"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var lots = @json($lots);
    var imageUrl = @json(asset(config('cemetery.map_image')));

    function loadImageDimensions(src, cb) {
        var img = new Image();
        img.onload = function () { cb({ width: img.naturalWidth, height: img.naturalHeight }); };
        img.onerror = function () { cb({ width: 1000, height: 700 }); };
        img.src = src;
    }

    function lotStyle(lot) {
        var status = (lot.status || (lot.is_occupied ? 'occupied' : 'available'));
        var color = status === 'occupied' ? '#dc3545' : (status === 'reserved' ? '#0d6efd' : '#198754');
        return { color: color, weight: 2, opacity: 0.9, fillColor: color, fillOpacity: 0.18 };
    }

    function lotPopupHtml(lot) {
        var title = '<div class="fw-semibold mb-1">Lot ' + (lot.lot_id || '') + '</div>';
        var status = lot.status || (lot.is_occupied ? 'occupied' : 'available');
        var body = '<div class="text-muted mb-2">Status: ' + status + '</div>';
        if (Array.isArray(lot.deceased) && lot.deceased.length) {
            body += '<div class="fw-semibold mb-1">Deceased</div><ul class="mb-0 ps-3">';
            lot.deceased.forEach(function (d) {
                body += '<li>' + (d.last_name || '') + ', ' + (d.first_name || '') + '</li>';
            });
            body += '</ul>';
        } else {
            body += '<div class="text-muted">No interment record.</div>';
        }
        return '<div style="min-width:220px">' + title + body + '</div>';
    }

    function makeLayerForLot(lot) {
        var style = lotStyle(lot);
        if (lot.geometry_type === 'rect' && lot.geometry) {
            var x = Number(lot.geometry.x || 0);
            var y = Number(lot.geometry.y || 0);
            var w = Number(lot.geometry.w || 0);
            var h = Number(lot.geometry.h || 0);
            return L.rectangle([[y, x], [y + h, x + w]], style);
        }
        if (lot.geometry_type === 'poly' && lot.geometry && Array.isArray(lot.geometry.points)) {
            var pts = lot.geometry.points
                .map(function (p) { return [Number(p.y || 0), Number(p.x || 0)]; })
                .filter(function (p) { return isFinite(p[0]) && isFinite(p[1]); });
            if (pts.length >= 3) return L.polygon(pts, style);
        }
        var lat = Number(lot.latitude || 0);
        var lng = Number(lot.longitude || 0);
        return L.circleMarker([lat, lng], { radius: 6, color: style.color, fillColor: style.fillColor, fillOpacity: 0.9 });
    }

    loadImageDimensions(imageUrl, function (dim) {
        var map = L.map('publicMap', { crs: L.CRS.Simple, minZoom: -2, zoomSnap: 0.25, zoomDelta: 0.25, attributionControl: false });
        var bounds = [[0, 0], [dim.height, dim.width]];
        L.imageOverlay(imageUrl, bounds).addTo(map);
        map.fitBounds(bounds);

        var lotLayers = [];
        lots.forEach(function (lot) {
            var layer = makeLayerForLot(lot).addTo(map);
            layer.bindPopup(lotPopupHtml(lot));
            lotLayers.push({ lot: lot, layer: layer });
        });

        var searchInput = document.getElementById('searchInput');
        var searchResults = document.getElementById('searchResults');

        function getDeceasedIndex() {
            var items = [];
            lotLayers.forEach(function (entry) {
                (entry.lot.deceased || []).forEach(function (d) {
                    var name = ((d.last_name || '') + ', ' + (d.first_name || '')).trim();
                    if (!name) return;
                    items.push({ name: name, lot: entry.lot, layer: entry.layer });
                });
            });
            return items;
        }

        var deceasedIndex = getDeceasedIndex();

        function renderResults(matches) {
            searchResults.innerHTML = '';
            if (!matches.length) {
                searchResults.classList.add('d-none');
                return;
            }
            searchResults.classList.remove('d-none');
            matches.slice(0, 12).forEach(function (m) {
                var a = document.createElement('a');
                a.className = 'list-group-item list-group-item-action';
                a.textContent = m.name + ' (Lot ' + (m.lot.lot_id || '') + ')';
                a.addEventListener('click', function () {
                    var target = m.layer.getBounds ? m.layer.getBounds().pad(0.8) : L.latLngBounds([m.layer.getLatLng()]).pad(0.8);
                    map.fitBounds(target, { animate: true });
                    m.layer.openPopup();
                    searchResults.classList.add('d-none');
                    searchInput.blur();
                });
                searchResults.appendChild(a);
            });
        }

        searchInput.addEventListener('input', function () {
            var q = (searchInput.value || '').trim().toLowerCase();
            if (q.length < 2) {
                searchResults.classList.add('d-none');
                return;
            }
            var matches = deceasedIndex.filter(function (d) { return d.name.toLowerCase().includes(q); });
            renderResults(matches);
        });

        document.addEventListener('click', function (e) {
            if (!searchResults.contains(e.target) && e.target !== searchInput) {
                searchResults.classList.add('d-none');
            }
        });
    });
});
</script>
@endsection
