@extends('home.home_master')

@section('home')
<section class="liliwmemoria-page-hero liliwmemoria-hero-bg" style="padding: 60px 0;">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-10">
        <div class="liliwmemoria-page-hero__content text-center" data-aos="fade-up">
          <h1 class="liliwmemoria-page-hero__title">Our Location</h1>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="lonyo-section-padding">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-10">
        <div class="liliwmemoria-about-panel" style="padding: 16px;">
          <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
            <div>
              <h3 style="color: #142c14; font-weight: 800;">Find Us on the Map</h3>
              <p class="text-muted mb-0" style="font-size: 0.9rem;">Liliw Memoria, Liliw, Laguna</p>
            </div>
            <a href="https://maps.google.com/?q=Liliw+Memoria+Liliw+Laguna" target="_blank" class="btn btn-outline-secondary btn-sm">
              <i class="ri-external-link-line"></i> Open in Google Maps
            </a>
          </div>

          <div class="liliwmemoria-map-container" data-aos="fade-up" data-aos-duration="700">
            <iframe
              src="https://www.google.com/maps/embed?pb=!1m13!1m8!1m3!1d823.6778595364278!2d121.43159!3d14.134984!3m2!1i1024!2i768!4f13.1!3m2!1m1!2zMTTCsDA4JzA0LjkuTiAxMjHCsDI1JzUyLjkiRQ!5e1!3m2!1sen!2sph!4v1777645911615!5m2!1sen!2sph"
              width="100%"
              height="450"
              style="border:0; border-radius: 12px;"
              allowfullscreen=""
              loading="lazy"
              referrerpolicy="no-referrer-when-downgrade">
            </iframe>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="pt-0 liliwmemoria-slider2-cards2">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-11">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
          <div>
          </br>
            <h3 class="mb-1">Cemetery Map</h3>
            <p class="text-muted">Click a lot to view its number and status.</p>
          </div>
        </div>

        <div class="card border-0 shadow-sm">
          <div class="card-body p-3 p-md-4">
            <div id="cemeteryMap" style="height: 400px; width: 100%; border-radius: 12px; overflow: hidden;"></div>
            <div class="d-flex flex-wrap gap-3 mt-3 justify-content-center">
              <div class="d-flex align-items-center gap-1">
                <span style="display: inline-block; width: 12px; height: 12px; background-color: #198754; border-radius: 2px;"></span>
                <span class="small text-muted">Available</span>
              </div>
              <div class="d-flex align-items-center gap-1">
                <span style="display: inline-block; width: 12px; height: 12px; background-color: #0d6efd; border-radius: 2px;"></span>
                <span class="small text-muted">Reserved</span>
              </div>
              <div class="d-flex align-items-center gap-1">
                <span style="display: inline-block; width: 12px; height: 12px; background-color: #dc3545; border-radius: 2px;"></span>
                <span class="small text-muted">Occupied</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- <section class="lonyo-section-padding pt-0">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-10 text-center">
        <h2 style="color: #142c14; font-weight: 800;">How to Get Here</h2>
        <p style="color: rgba(20,44,20,0.72); max-width: 700px; margin: 12px auto 0; line-height: 1.65;">
          From Manila, take a bus bound for Laguna (e.g., Lucena or San Pablo) and get off at Liliw town proper. Our memorial park is a short tricycle ride away.
        </p>
      </div>
    </div>
  </div>
</section> --}}

@include('home.layout.slider4')
@endsection

@push('styles')
<style>
  .liliwmemoria-map-container { margin-top: 16px; }
  .liliwmemoria-map-container iframe { border-radius: 12px; }
</style>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@push('scripts')
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
        var lotNumber = lot.lot_id || ('L-' + (lot.lot_number || lot.id));
        var status = lot.status || (lot.is_occupied ? 'occupied' : 'available');
        var statusLabel = status.charAt(0).toUpperCase() + status.slice(1);
        return '<div style="min-width:180px"><strong>Lot ' + lotNumber + '</strong><br>Status: ' + statusLabel + '</div>';
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
        var map = L.map('cemeteryMap', { crs: L.CRS.Simple, minZoom: -2, zoomSnap: 0.25, zoomDelta: 0.25, attributionControl: false });
        var bounds = [[0, 0], [dim.height, dim.width]];
        L.imageOverlay(imageUrl, bounds).addTo(map);
        map.fitBounds(bounds);

        lots.forEach(function (lot) {
            var layer = makeLayerForLot(lot).addTo(map);
            layer.bindPopup(lotPopupHtml(lot));
        });
    });
});
</script>
@endpush
