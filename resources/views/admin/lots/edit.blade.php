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
                        <label class="form-label">Lot Name</label>
                        <input type="text" name="name" class="form-control" value="{{ $lot->name }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Section (Optional)</label>
                        <input type="text" name="section" class="form-control" value="{{ $lot->section }}">
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

                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="is_occupied" class="form-check-input" id="is_occupied" {{ $lot->is_occupied ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_occupied">Occupied</label>
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
@endsection