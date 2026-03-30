@extends('admin.admin_master')

@section('admin')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Add New Lot</h4>

                <form method="POST" action="{{ route('admin.lots.store') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Lot ID</label>
                        <input type="text" id="lot_id_display" class="form-control" value="{{ $nextLotId ?? '' }}" readonly>
                        <input type="hidden" name="lot_number" id="lot_number" value="{{ $nextLotNumber ?? '' }}">
                        <div class="form-text">Auto-generated and unique.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Owner</label>
                        <input type="text" name="name" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Lot Category</label>
                        <select name="section" id="lot_category" class="form-select" required>
                            <option value="phase_1" {{ ($defaultCategory ?? '') === 'phase_1' ? 'selected' : '' }}>Phase 1</option>
                            <option value="phase_2" {{ ($defaultCategory ?? '') === 'phase_2' ? 'selected' : '' }}>Phase 2</option>
                            <option value="garden_lot" {{ ($defaultCategory ?? '') === 'garden_lot' ? 'selected' : '' }}>Garden Lot</option>
                            <option value="back_office_lot" {{ ($defaultCategory ?? '') === 'back_office_lot' ? 'selected' : '' }}>Back Office Lot</option>
                            <option value="mausoleum" {{ ($defaultCategory ?? '') === 'mausoleum' ? 'selected' : '' }}>Mausoleum</option>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Latitude</label>
                            <input type="text" name="latitude" class="form-control" placeholder="e.g. 14.5995" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Longitude</label>
                            <input type="text" name="longitude" class="form-control" placeholder="e.g. 120.9842" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Notes (Optional)</label>
                        <textarea name="notes" class="form-control" rows="3"></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Save Lot</button>
                    <a href="{{ route('admin.lots.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-3">How to get coordinates:</h5>
                <ol>
                    <li>Go to <a href="https://www.openstreetmap.org" target="_blank">OpenStreetMap</a></li>
                    <li>Navigate to your cemetery location</li>
                    <li>Right-click on the lot position</li>
                    <li>Copy the numbers from the popup</li>
                    <li>Paste them in the fields above</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var categorySelect = document.getElementById('lot_category');
    var lotIdDisplay = document.getElementById('lot_id_display');
    var lotNumberInput = document.getElementById('lot_number');
    if (!categorySelect || !lotIdDisplay || !lotNumberInput) return;

    var nextLotNumberUrl = @json(
        \Illuminate\Support\Facades\Route::has('admin.lots.nextLotNumber')
            ? route('admin.lots.nextLotNumber')
            : url('/admin/lots/next-lot-number')
    );

    function refreshLotId() {
        var category = String(categorySelect.value || '');
        lotIdDisplay.value = '';
        lotNumberInput.value = '';

        fetch(nextLotNumberUrl + '?category=' + encodeURIComponent(category), {
            headers: { 'Accept': 'application/json' }
        })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (!data) return;
                if (data.lot_number) lotNumberInput.value = String(data.lot_number);
                if (data.lot_id) lotIdDisplay.value = String(data.lot_id);
            })
            .catch(function () {});
    }

    categorySelect.addEventListener('change', refreshLotId);
});
</script>
@endsection
