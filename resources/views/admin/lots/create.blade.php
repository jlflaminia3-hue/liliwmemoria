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
                        <label class="form-label">Lot Number</label>
                        <input type="text" name="lot_number" class="form-control" value="{{ $nextLotNumber ?? '' }}" readonly>
                        <div class="form-text">Auto-generated and unique.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Owner</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Phase (Optional)</label>
                        <input type="text" name="section" class="form-control">
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
@endsection
