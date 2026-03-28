@extends('admin.admin_master')

@section('admin')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Edit Lot: {{ $lot->name }}</h4>

                <form method="POST" action="{{ route('admin.lots.update', $lot->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Lot Number</label>
                        <input type="text" class="form-control" value="{{ $lot->lot_number }}" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Owner</label>
                        <input type="text" name="name" class="form-control" value="{{ $lot->name }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Phase (Optional)</label>
                        <input type="text" name="section" class="form-control" value="{{ $lot->section }}">
                    </div>

                    @php
                        $status = $lot->status ?? ($lot->is_occupied ? 'occupied' : 'available');
                        if (!in_array($status, ['available', 'reserved', 'occupied'], true)) {
                            $status = 'available';
                        }
                    @endphp

                    <div class="mb-3">
                        <label class="form-label">Lot Status</label>
                        <select name="status" id="lot_status" class="form-select" required>
                            <option value="available" @selected($status === 'available')>Available</option>
                            <option value="reserved" @selected($status === 'reserved')>Reserved</option>
                            <option value="occupied" @selected($status === 'occupied')>Occupied</option>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Latitude</label>
                            <input type="text" name="latitude" class="form-control" value="{{ $lot->latitude }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Longitude</label>
                            <input type="text" name="longitude" class="form-control" value="{{ $lot->longitude }}" required>
                        </div>
                    </div>

                    <div id="deceased_block" style="display: none;">
                        <hr>
                        <h5 class="card-title mb-3">Add Deceased (Optional)</h5>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">First Name</label>
                                <input type="text" name="deceased_first_name" class="form-control" value="{{ old('deceased_first_name') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Last Name</label>
                                <input type="text" name="deceased_last_name" class="form-control" value="{{ old('deceased_last_name') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Date of Birth</label>
                                <input type="date" name="deceased_date_of_birth" class="form-control" value="{{ old('deceased_date_of_birth') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Date of Death</label>
                                <input type="date" name="deceased_date_of_death" class="form-control" value="{{ old('deceased_date_of_death') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Burial Date</label>
                                <input type="date" name="deceased_burial_date" class="form-control" value="{{ old('deceased_burial_date') }}">
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Notes</label>
                                <textarea name="deceased_notes" class="form-control" rows="2">{{ old('deceased_notes') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Notes (Optional)</label>
                        <textarea name="notes" class="form-control" rows="3">{{ $lot->notes }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Update Lot</button>
                    <a href="{{ route('admin.lots.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        var statusSelect = document.getElementById('lot_status');
        var deceasedBlock = document.getElementById('deceased_block');
        if (!statusSelect || !deceasedBlock) return;

        function sync() {
            var status = statusSelect.value;
            deceasedBlock.style.display = status === 'occupied' ? '' : 'none';
        }

        statusSelect.addEventListener('change', sync);
        sync();
    })();
</script>
@endsection
