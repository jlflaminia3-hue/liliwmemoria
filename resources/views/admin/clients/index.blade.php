@extends('admin.admin_master')

@section('admin')
@php
    $currentSearch = request('search', '');
    $currentHasLots = request('has_lots', '');
    $currentPerPage = (int) request('per_page', 20);
@endphp

<div class="content">
    <div class="container-xxl">
        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column gap-3">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">Clients</h4>
                <div class="text-muted mt-1">Manage client profiles, contact details, and linked lot ownerships.</div>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createClientModal">
                    <i data-feather="plus" class="me-1" style="height: 16px; width: 16px;"></i>
                    Add Client
                </button>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row g-3 mb-3">
            <div class="col-md-6 col-xl">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase fw-semibold">All Clients</div>
                        <div class="fs-3 fw-bold">{{ number_format($stats['total'] ?? 0) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase fw-semibold">With Email</div>
                        <div class="fs-3 fw-bold text-primary">{{ number_format($stats['with_email'] ?? 0) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase fw-semibold">With Phone</div>
                        <div class="fs-3 fw-bold text-success">{{ number_format($stats['with_phone'] ?? 0) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase fw-semibold">With Lots</div>
                        <div class="fs-3 fw-bold text-warning">{{ number_format($stats['with_lots'] ?? 0) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.clients.index') }}" class="row g-3 align-items-end mb-4">
                    <div class="col-lg-6">
                        <label for="client_search" class="form-label fw-semibold">Search</label>
                        <input
                            id="client_search"
                            type="text"
                            name="search"
                            class="form-control"
                            value="{{ $currentSearch }}"
                            placeholder="Name, email, phone, or address"
                        >
                    </div>
                    <div class="col-md-4 col-lg-2">
                        <label for="client_has_lots_filter" class="form-label fw-semibold">Lots</label>
                        <select id="client_has_lots_filter" name="has_lots" class="form-select">
                            <option value="">All</option>
                            <option value="yes" @selected($currentHasLots === 'yes')>With lots</option>
                            <option value="no" @selected($currentHasLots === 'no')>No lots</option>
                        </select>
                    </div>
                    <div class="col-md-4 col-lg-2">
                        <label for="client_per_page" class="form-label fw-semibold">Per Page</label>
                        <select id="client_per_page" name="per_page" class="form-select">
                            @foreach ([10, 20, 50, 100] as $size)
                                <option value="{{ $size }}" @selected($currentPerPage === $size)>{{ $size }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 col-lg-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100">Apply</button>
                        <a href="{{ route('admin.clients.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Client</th>
                                <th>Contact</th>
                                <th>Address</th>
                                <th class="text-end" style="width: 70px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($clients as $client)
                                <tr class="client-row" data-href="{{ route('admin.clients.show', $client) }}" tabindex="0" role="button" aria-label="View {{ $client->full_name }}">
                                    <td class="fw-semibold">{{ $client->full_name }}</td>
                                    <td>
                                        <div>{{ $client->email ?: '—' }}</div>
                                        <div class="text-muted small">{{ $client->phone ?: '—' }}</div>
                                    </td>
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
                                        <span class="text-muted">{{ !empty($addressParts) ? implode(', ', $addressParts) : '—' }}</span>
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
                                                    data-notes="{{ e($client->notes ?? '') }}"
                                                >Edit</a>
                                                <form method="POST" action="{{ route('admin.clients.destroy', $client) }}" onsubmit="return confirm('Delete this client?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger">Delete</button>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">No clients found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end pt-3">
                    <div>{{ $clients->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        var rows = document.querySelectorAll('.client-row');
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
                bootstrap.Modal.getOrCreateInstance(modalEl).show();
            });
        });
    })();
</script>

<!-- Create Client Modal -->
<div class="modal fade" id="createClientModal" tabindex="-1" aria-labelledby="createClientModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <form id="createClientForm" method="POST" action="{{ route('admin.clients.store') }}" autocomplete="off" class="modal-content">
            @csrf
            <input type="hidden" name="_modal" value="create">
            <div class="modal-header">
                <h5 class="modal-title" id="createClientModalLabel">Add Client</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-12">
                        @include('admin.clients.partials.form_fields', ['idPrefix' => 'create_'])
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Client</button>
            </div>
        </form>
    </div>
</div>

<script>
    (function () {
        var modalEl = document.getElementById('createClientModal');
        if (!modalEl) return;

        var clearForm = function () {
            var form = document.getElementById('createClientForm');
            if (!form) return;

            form.querySelectorAll('input, textarea, select').forEach(function (el) {
                if (el.tagName === 'SELECT') {
                    el.selectedIndex = 0;
                    return;
                }
                if (el.type === 'hidden' || el.type === 'submit' || el.type === 'button' || el.type === 'reset') return;
                el.value = '';
            });
        };

        modalEl.addEventListener('hidden.bs.modal', function () {
            clearForm();
        });

        window.addEventListener('pageshow', function (event) {
            if (!event.persisted) return;
            clearForm();
        });
    })();
</script>

<!-- Edit Client Modal -->
<div class="modal fade" id="editClientModal" tabindex="-1" aria-labelledby="editClientModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <form id="editClientForm" method="POST" action="" class="modal-content">
            @csrf
            @method('PUT')
            <input type="hidden" name="_modal" value="edit">
            <div class="modal-header">
                <h5 class="modal-title" id="editClientModalLabel">Edit Client</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-12">
                        @include('admin.clients.partials.form_fields', ['idPrefix' => 'edit_'])
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
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

