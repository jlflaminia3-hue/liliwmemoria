@extends('admin.admin_master')

@section('admin')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title mb-1">{{ $client->full_name }}</h4>
                        <div class="text-muted">Client Profile</div>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.payments.index', ['client_id' => $client->id]) }}" class="btn btn-primary">View Payments</a>
                        <a href="{{ route('admin.clients.index') }}" class="btn btn-light">Back</a>
                        <div class="dropdown">
                            <button class="btn btn-light" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Client actions">
                                <i data-feather="more-vertical"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#editClientModal">Edit</button>
                                <form action="{{ route('admin.clients.destroy', $client) }}" method="POST" class="dropdown-item p-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-link dropdown-item text-danger m-0" onclick="return confirm('Delete this client?')">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                @include('admin.clients.partials.alerts')

                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-personal" type="button" role="tab">Personal</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-ownerships" type="button" role="tab">Ownership</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-contracts" type="button" role="tab">Contracts</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-maintenance" type="button" role="tab">Maintenance</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-family" type="button" role="tab">Family</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-comms" type="button" role="tab">Communication</button>
                    </li>
                </ul>

                <div class="tab-content pt-3">
                    <div class="tab-pane fade show active" id="tab-personal" role="tabpanel">
                        @include('admin.clients.partials.personal')
                    </div>
                    <div class="tab-pane fade" id="tab-ownerships" role="tabpanel">
                        @include('admin.clients.partials.ownerships')
                    </div>
                    <div class="tab-pane fade" id="tab-contracts" role="tabpanel">
                        @include('admin.clients.partials.contracts')
                    </div>
                    <div class="tab-pane fade" id="tab-maintenance" role="tabpanel">
                        @include('admin.clients.partials.maintenance')
                    </div>
                    <div class="tab-pane fade" id="tab-family" role="tabpanel">
                        @include('admin.clients.partials.family')
                    </div>
                    <div class="tab-pane fade" id="tab-comms" role="tabpanel">
                        @include('admin.clients.partials.communications')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Client Modal -->
<div class="modal fade" id="editClientModal" tabindex="-1" aria-labelledby="editClientModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.clients.update', $client) }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="_modal" value="edit">
                <div class="modal-header">
                    <h5 class="modal-title" id="editClientModalLabel">Edit Client</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @include('admin.clients.partials.form_fields', ['idPrefix' => 'show_edit_', 'client' => $client])
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

@if ($errors->any() && old('_modal') === 'edit')
    <script>
        bootstrap.Modal.getOrCreateInstance(document.getElementById('editClientModal')).show();
    </script>
@endif
@endsection
