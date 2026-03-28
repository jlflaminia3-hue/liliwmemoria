@extends('admin.admin_master')

@section('admin')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title">Clients</h4>
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createClientModal">
                        <i data-feather="plus"></i> Add Client
                    </button>
                </div>

                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Address</th>
                                <th class="text-end" style="width: 60px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($clients as $client)
                                <tr class="client-row" data-href="{{ route('admin.clients.show', $client) }}" tabindex="0" role="button" aria-label="View {{ $client->full_name }}">
                                    <td>{{ $client->full_name }}</td>
                                    <td>{{ $client->email ?? '-' }}</td>
                                    <td>{{ $client->phone ?? '-' }}</td>
                                    <td>
                                        @php
                                            $addressParts = array_filter([
                                                $client->address_line1,
                                                $client->address_line2,
                                                $client->barangay,
                                                $client->city,
                                                $client->province,
                                                $client->postal_code,
                                                $client->country,
                                            ]);
                                        @endphp
                                        {{ !empty($addressParts) ? implode(', ', $addressParts) : '-' }}
                                    </td>
                                    <td class="text-end client-actions">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Client actions">
                                                <i data-feather="more-vertical"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a class="dropdown-item" href="{{ route('admin.payments.index', ['client_id' => $client->id]) }}">View Payments</a>
                                                <a
                                                    class="dropdown-item js-edit-client"
                                                    href="{{ route('admin.clients.edit', $client) }}"
                                                    data-client-id="{{ $client->id }}"
                                                    data-first-name="{{ $client->first_name }}"
                                                    data-last-name="{{ $client->last_name }}"
                                                    data-email="{{ $client->email ?? '' }}"
                                                    data-phone="{{ $client->phone ?? '' }}"
                                                    data-address-line1="{{ $client->address_line1 ?? '' }}"
                                                    data-address-line2="{{ $client->address_line2 ?? '' }}"
                                                    data-barangay="{{ $client->barangay ?? '' }}"
                                                    data-city="{{ $client->city ?? '' }}"
                                                    data-province="{{ $client->province ?? '' }}"
                                                    data-postal-code="{{ $client->postal_code ?? '' }}"
                                                    data-country="{{ $client->country ?? '' }}"
                                                    data-notes="{{ $client->notes ?? '' }}"
                                                >Edit</a>
                                                <form action="{{ route('admin.clients.destroy', $client) }}" method="POST" class="dropdown-item p-0">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-link dropdown-item text-danger m-0" onclick="return confirm('Delete this client?')">
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($clients->isEmpty())
                    <div class="text-center py-4">
                        <p class="text-muted">No clients found. <a href="{{ route('admin.clients.create') }}">Add your first client</a></p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    .client-row { cursor: pointer; }
</style>

<script>
    (function () {
        var rows = document.querySelectorAll('tr.client-row[data-href]');
        rows.forEach(function (row) {
            row.addEventListener('click', function (event) {
                if (event.target.closest('.client-actions')) return;
                window.location.href = row.getAttribute('data-href');
            });

            row.addEventListener('keydown', function (event) {
                if (event.key !== 'Enter' && event.key !== ' ') return;
                if (event.target.closest('.client-actions')) return;
                event.preventDefault();
                window.location.href = row.getAttribute('data-href');
            });
        });

        var editLinks = document.querySelectorAll('.js-edit-client');
        editLinks.forEach(function (link) {
            link.addEventListener('click', function (event) {
                event.preventDefault();

                var clientId = link.getAttribute('data-client-id');
                var form = document.getElementById('editClientForm');
                if (!form || !clientId) return;

                form.setAttribute('action', "{{ url('admin/clients') }}/" + clientId);

                var setValue = function (id, value) {
                    var el = document.getElementById(id);
                    if (!el) return;
                    el.value = value || '';
                };

                setValue('edit_first_name', link.getAttribute('data-first-name'));
                setValue('edit_last_name', link.getAttribute('data-last-name'));
                setValue('edit_email', link.getAttribute('data-email'));
                setValue('edit_phone', link.getAttribute('data-phone'));
                setValue('edit_address_line1', link.getAttribute('data-address-line1'));
                setValue('edit_address_line2', link.getAttribute('data-address-line2'));
                setValue('edit_barangay', link.getAttribute('data-barangay'));
                setValue('edit_city', link.getAttribute('data-city'));
                setValue('edit_province', link.getAttribute('data-province'));
                setValue('edit_postal_code', link.getAttribute('data-postal-code'));
                setValue('edit_country', link.getAttribute('data-country'));
                setValue('edit_notes', link.getAttribute('data-notes'));

                var modalEl = document.getElementById('editClientModal');
                if (!modalEl) return;
                var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                modal.show();
            });
        });
    })();
</script>

<!-- Create Client Modal -->
<div class="modal fade" id="createClientModal" tabindex="-1" aria-labelledby="createClientModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.clients.store') }}">
                @csrf
                <input type="hidden" name="_modal" value="create">
                <div class="modal-header">
                    <h5 class="modal-title" id="createClientModalLabel">Add Client</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @include('admin.clients.partials.form_fields', ['idPrefix' => 'create_'])
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Client</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Client Modal -->
<div class="modal fade" id="editClientModal" tabindex="-1" aria-labelledby="editClientModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="editClientForm" method="POST" action="">
                @csrf
                @method('PUT')
                <input type="hidden" name="_modal" value="edit">
                <div class="modal-header">
                    <h5 class="modal-title" id="editClientModalLabel">Edit Client</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @include('admin.clients.partials.form_fields', ['idPrefix' => 'edit_'])
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

@if ($errors->any() && old('_modal') === 'create')
    <script>
        bootstrap.Modal.getOrCreateInstance(document.getElementById('createClientModal')).show();
    </script>
@endif

@if ($errors->any() && old('_modal') === 'edit')
    <script>
        bootstrap.Modal.getOrCreateInstance(document.getElementById('editClientModal')).show();
    </script>
@endif
@endsection
