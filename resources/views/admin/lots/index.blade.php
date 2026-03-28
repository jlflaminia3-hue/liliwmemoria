@extends('admin.admin_master')

@section('admin')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title">All Lots</h4>
                    <div>
                        <a href="{{ route('admin.lots.map') }}" class="btn btn-primary me-2">
                            <i data-feather="map"></i> Map View
                        </a>
                        <a href="{{ route('admin.lots.create') }}" class="btn btn-success">
                            <i data-feather="plus"></i> Add Lot
                        </a>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Lot #</th>
                                <th>Owner</th>
                                <th>Phase</th>
                                <th>Status</th>
                                <th>Deceased</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lots as $lot)
                            <tr>
                                <td>{{ $lot->lot_number }}</td>
                                <td>{{ $lot->name }}</td>
                                <td>{{ $lot->section ?? '-' }}</td>
                                <td>
                                    @php
                                        $status = $lot->status ?? ($lot->is_occupied ? 'occupied' : 'available');
                                    @endphp

                                    @if($status === 'occupied')
                                        <span class="badge bg-danger">Occupied</span>
                                    @elseif($status === 'reserved')
                                        <span class="badge bg-primary">Reserved</span>
                                    @else
                                        <span class="badge bg-success">Available</span>
                                    @endif
                                </td>
                                <td>
                                    @if($lot->deceased->count() > 0)
                                    {{ $lot->deceased->pluck('first_name')->implode(', ') }}
                                    @else
                                    -
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.lots.edit', $lot->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                    <form action="{{ route('admin.lots.destroy', $lot->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($lots->isEmpty())
                <div class="text-center py-4">
                    <p class="text-muted">No lots found. <a href="{{ route('admin.lots.create') }}">Add your first lot</a></p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
