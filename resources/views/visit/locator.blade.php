@extends('home.home_master')

@section('home')
<style>
    .visit-page { padding-top: 120px !important; }
    #locatorMap { height: 640px; width: 100%; border-radius: 12px; overflow: hidden; }
    .locator-badge { display: inline-flex; align-items: center; gap: .5rem; padding: .4rem .65rem; border-radius: 999px; background: #f8f9fa; border: 1px solid #e9ecef; }
    .marker-entrance { background: #0d6efd; color: #fff; border-radius: 999px; padding: 6px 10px; font-weight: 600; box-shadow: 0 6px 16px rgba(0,0,0,.2); }
    .marker-lot { background: #dc3545; color: #fff; border-radius: 999px; padding: 6px 10px; font-weight: 700; box-shadow: 0 6px 16px rgba(0,0,0,.2); }
</style>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<section class="lonyo-section-padding6 visit-page">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-11">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                    <div>
                        <h3 class="mb-1">Tomb Locator</h3>
                        <div class="text-muted">
                            Visitor: <span class="fw-semibold">{{ $log->visitor_name }}</span> •
                            Visiting: <span class="fw-semibold">{{ $deceased->last_name }}, {{ $deceased->first_name }}</span>
                        </div>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('public.visit.create') }}" class="btn btn-outline-secondary">New Visitor</a>
                        <a href="{{ route('public.map') }}" class="btn btn-outline-secondary">Public Map</a>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-3 p-md-4">
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <span class="locator-badge">
                                <span class="fw-semibold">{{ $entrance['label'] }}</span>
                                <span class="text-muted">→</span>
                                <span class="fw-semibold">Lot {{ $lot->lot_id }}</span>
                            </span>
                            @if ($lot->block)
                                <span class="locator-badge">Block <span class="fw-semibold">{{ $lot->block }}</span></span>
                            @endif
                            @if ($lot->lot_category_label)
                                <span class="locator-badge">{{ $lot->lot_category_label }}</span>
                            @endif
                        </div>

                        <div id="locatorMap"></div>

                        <div class="alert alert-info mt-3 mb-0">
                            <div class="fw-semibold mb-1">Guide</div>
                            <div class="text-muted">
                                Start at the <span class="fw-semibold">{{ $entrance['label'] }}</span> marker (blue),
                                then follow the highlighted path to the <span class="fw-semibold">Lot {{ $lot->lot_id }}</span> marker (red).
                                If you need help, please ask the cemetery office.
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var imageUrl = @json($mapImageUrl);
    var entrance = @json($entrance);
    var lot = {!! json_encode([
        'lot_id' => $lot->lot_id,
        'geometry_type' => $lot->geometry_type,
        'geometry' => $lot->geometry,
        'latitude' => $lot->latitude,
        'longitude' => $lot->longitude,
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!};

    function loadImageDimensions(src, cb) {
        var img = new Image();
        img.onload = function () { cb({ width: img.naturalWidth, height: img.naturalHeight }); };
        img.onerror = function () { cb({ width: 1000, height: 700 }); };
        img.src = src;
    }

    function entranceIcon() {
        return L.divIcon({
            className: '',
            html: '<div class="marker-entrance">' + (entrance.label || 'Entrance') + '</div>',
            iconSize: null,
        });
    }

    function lotIcon() {
        return L.divIcon({
            className: '',
            html: '<div class="marker-lot">Lot ' + lot.lot_id + '</div>',
            iconSize: null,
        });
    }

    loadImageDimensions(imageUrl, function (dim) {
        var map = L.map('locatorMap', {
            crs: L.CRS.Simple,
            minZoom: -2,
            zoomSnap: 0.25,
            zoomDelta: 0.25,
            attributionControl: false,
        });

        var bounds = [[0, 0], [dim.height, dim.width]];
        L.imageOverlay(imageUrl, bounds).addTo(map);
        map.fitBounds(bounds);

        var entranceLatLng = L.latLng(Number(entrance.y || 0), Number(entrance.x || 0));
        L.marker(entranceLatLng, { icon: entranceIcon() }).addTo(map);

        var lotLatLng = L.latLng(Number(lot.latitude || 0), Number(lot.longitude || 0));

        var highlightStyle = {
            color: '#dc3545',
            weight: 3,
            opacity: 0.95,
            fillColor: '#dc3545',
            fillOpacity: 0.20,
        };

        var lotLayer = null;
        if (lot.geometry_type === 'rect' && lot.geometry) {
            var x = Number(lot.geometry.x || 0);
            var y = Number(lot.geometry.y || 0);
            var w = Number(lot.geometry.w || 0);
            var h = Number(lot.geometry.h || 0);
            lotLayer = L.rectangle([[y, x], [y + h, x + w]], highlightStyle).addTo(map);
            lotLatLng = L.latLng(y + h / 2, x + w / 2);
        } else if (lot.geometry_type === 'poly' && lot.geometry && Array.isArray(lot.geometry.points)) {
            var pts = lot.geometry.points
                .map(function (p) { return [Number(p.y || 0), Number(p.x || 0)]; })
                .filter(function (p) { return isFinite(p[0]) && isFinite(p[1]); });
            if (pts.length >= 3) {
                lotLayer = L.polygon(pts, highlightStyle).addTo(map);
                var sumY = 0, sumX = 0;
                pts.forEach(function (p) { sumY += p[0]; sumX += p[1]; });
                lotLatLng = L.latLng(sumY / pts.length, sumX / pts.length);
            }
        }

        L.marker(lotLatLng, { icon: lotIcon() }).addTo(map);
        L.polyline([entranceLatLng, lotLatLng], { color: '#0d6efd', weight: 4, opacity: 0.8, dashArray: '8,10' }).addTo(map);

        var focus = lotLayer && lotLayer.getBounds ? lotLayer.getBounds().pad(0.6) : L.latLngBounds([entranceLatLng, lotLatLng]).pad(0.6);
        map.fitBounds(focus, { animate: true });
    });
});
</script>
@endsection
