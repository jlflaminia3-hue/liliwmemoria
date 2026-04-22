@extends('admin.admin_master')

@section('admin')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <h4 class="card-title mb-0">Edit Lot: {{ $lot->name }}</h4>
                            <x-status.badge :status="$lot->status ?? 'active'" size="md" />
                        </div>
                        <div class="text-muted">
                            @if($lot->status === 'archived')
                                <i data-feather="archive" class="me-1" style="height: 12px; width: 12px;"></i>
                                This record is archived and hidden from active views.
                            @elseif($lot->status === 'inactive')
                                <i data-feather="x-circle" class="me-1" style="height: 12px; width: 12px;"></i>
                                This record is inactive but remains searchable.
                            @endif
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.lots.index') }}" class="btn btn-light">Back</a>
                    </div>
                </div>

                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if (session('info'))
                    <div class="alert alert-info">{{ session('info') }}</div>
                @endif

                <form method="POST" action="{{ route('admin.lots.update', $lot->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Lot ID</label>
                        <input type="text" class="form-control" value="{{ $lot->lot_id }}" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Owner</label>
                        <input type="text" name="name" class="form-control" value="{{ $lot->name }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Lot Category</label>
                        <select name="section" class="form-select" required>
                            <option value="phase_1" {{ $lot->section === 'phase_1' ? 'selected' : '' }}>Phase 1</option>
                            <option value="phase_2" {{ $lot->section === 'phase_2' ? 'selected' : '' }}>Phase 2</option>
                            <option value="garden_lot" {{ $lot->section === 'garden_lot' ? 'selected' : '' }}>Garden Lot</option>
                            <option value="back_office_lot" {{ $lot->section === 'back_office_lot' ? 'selected' : '' }}>Back Office Lot</option>
                            <option value="narra" {{ $lot->section === 'narra' ? 'selected' : '' }}>Narra</option>
                            <option value="mausoleum" {{ $lot->section === 'mausoleum' ? 'selected' : '' }}>Mausoleum</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Block (Optional)</label>
                        <input type="text" name="block" class="form-control" value="{{ $lot->block }}" placeholder="e.g. A, B, 1, 2">
                    </div>

                    @php
                        $status = $lot->status ?? ($lot->is_occupied ? 'occupied' : 'available');
                        if (!in_array($status, ['available', 'reserved', 'occupied'], true)) {
                            $status = 'available';
                        }
                    @endphp

                    <div class="mb-3">
                        <label class="form-label">Lot Status</label>
                        <div class="d-flex gap-2">
                            <select name="status" id="lot_status" class="form-select" required>
                                <option value="available" @selected($status === 'available')>Available</option>
                                <option value="reserved" @selected($status === 'reserved')>Reserved</option>
                                <option value="occupied" @selected($status === 'occupied')>Occupied</option>
                            </select>
                            <button type="button" class="btn btn-outline-danger" id="btnAddDeceased">Add Deceased</button>
                        </div>
                        <div class="form-text">Adding a deceased will automatically set the lot to Occupied.</div>
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
                    <a href="{{ route('admin.lots.map') }}" class="btn btn-info" target="_blank">View Map</a>
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
        var addDeceasedBtn = document.getElementById('btnAddDeceased');
        if (!statusSelect || !deceasedBlock) return;

        function sync() {
            var status = statusSelect.value;
            deceasedBlock.style.display = status === 'occupied' ? '' : 'none';
        }

        statusSelect.addEventListener('change', sync);
        if (addDeceasedBtn) {
            addDeceasedBtn.addEventListener('click', function () {
                statusSelect.value = 'occupied';
                sync();
                deceasedBlock.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        }
        sync();
    })();
</script>
@endsection
