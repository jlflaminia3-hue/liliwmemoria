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
                            <i data-feather="plus"></i> Add Lot with Deceased
                        </button>
                    </div>
                </div>

                <div id="map" style="height: 600px; width: 100%;"></div>

                <div class="mt-3">
                    <span class="badge bg-success me-2">Available Lot</span>
                    <span class="badge bg-danger me-2">Occupied Lot</span>
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
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Lot Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Section</label>
                            <input type="text" name="section" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Latitude</label>
                            <input type="text" name="latitude" id="modal_latitude" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Longitude</label>
                            <input type="text" name="longitude" id="modal_longitude" class="form-control" required>
                        </div>
                    </div>

                    <hr>
                    <h6 class="text-muted mb-3">Deceased Information</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">First Name</label>
                            <input type="text" name="first_name" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Last Name</label>
                            <input type="text" name="last_name" class="form-control" required>
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
            <div class="modal-body">
                <p class="text-muted small">Or click on the map to set coordinates automatically.</p>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var map = L.map('map').setView([14.5995, 120.9842], 15);

    // L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    //     attribution: '&copy; OpenStreetMap contributors'
    // }).addTo(map);

    // var imageUrl = '/images/sitemap.jpg';
    // var imageBounds = [[lat1, lng1], [lat2, lng2]];
    // L.imageOverlay(imageUrl, imageBounds).addTo(map);

    var imageUrl = "{{ asset('backend/assets/images/sitemap.png') }}";
   var imageBounds = [[14.5995, 120.9842], [14.6000, 120.9850]];
   var overlay = L.imageOverlay(imageUrl, imageBounds).addTo(map);
   
   // Set map to image bounds
   map.fitBounds(imageBounds);


    var lots = @json($lots);

    lots.forEach(function(lot) {
        var markerColor = lot.is_occupied ? '#dc3545' : '#28a745';
        var icon = L.divIcon({
            className: 'custom-marker',
            html: '<div style="background-color:' + markerColor + '; width:20px; height:20px; border-radius:50%; border:2px solid white; box-shadow:0 2px 5px rgba(0,0,0,0.3);"></div>',
            iconSize: [20, 20],
            iconAnchor: [10, 10]
        });

        var popupContent = '<b>' + lot.name + '</b><br>';
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

        var marker = L.marker([lot.latitude, lot.longitude], {icon: icon}).addTo(map);
        marker.bindPopup(popupContent);
    });

    map.on('click', function(e) {
        document.getElementById('modal_latitude').value = e.latlng.lat.toFixed(6);
        document.getElementById('modal_longitude').value = e.latlng.lng.toFixed(6);
        var modal = new bootstrap.Modal(document.getElementById('addLotModal'));
        modal.show();
    });
});
</script>
@endsection