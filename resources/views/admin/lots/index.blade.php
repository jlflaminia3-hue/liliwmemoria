@extends('admin.admin_master')

@section('admin')
@php
    $pageLots = $lots->getCollection();
    $currentSearch = request('search', '');
    $currentSort = request('sort', 'lot_id');
    $currentDirection = request('direction', 'asc');
    $currentPerPage = (int) request('per_page', 20);
@endphp

<style>
    .lots-card,.lots-stat{border:1px solid rgba(15,23,42,.08);border-radius:18px;background:#fff;box-shadow:0 12px 28px rgba(15,23,42,.06)}
    .lots-hero{padding:1.5rem;background:linear-gradient(135deg,#fff 0%,#f8fbff 100%)}
    .lots-title{font-size:1.75rem;font-weight:700;color:#0f172a;margin:0}
    .lots-grid{display:grid;gap:1rem}
    .lots-stats{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:1rem}
    .lots-stat{padding:1rem 1.1rem}
    .lots-label{font-size:.78rem;font-weight:700;color:#475569;margin-bottom:.45rem}
    .lots-value{font-size:1.45rem;font-weight:700;color:#0f172a}
    .lots-body{padding:1.25rem}
    .lots-toolbar{display:grid;grid-template-columns:minmax(0,1.5fr) repeat(3,minmax(140px,.5fr)) auto;gap:1rem;align-items:end}
    .lots-input,.lots-select,.lots-textarea{border-radius:12px;border:1px solid #dbe4f0;box-shadow:none}
    .lots-input,.lots-select{min-height:46px}
    .lots-search-wrap{position:relative}
    .lots-search-icon{position:absolute;top:50%;left:.95rem;transform:translateY(-50%);color:#94a3b8;pointer-events:none}
    .lots-search{padding-left:2.8rem}
    .lots-table-wrap{border:1px solid #edf2f7;border-radius:16px;overflow:hidden}
    .lots-table thead th{background:#f8fafc;color:#475569;font-size:.78rem;font-weight:700;text-transform:uppercase;letter-spacing:.04em;border-bottom:1px solid #edf2f7}
    .lots-table tbody td{border-color:#f1f5f9;vertical-align:middle}
    .lots-row:hover{background:#fafcff}
    .lots-pill{display:inline-flex;border-radius:999px;padding:.35rem .75rem;background:#f1f5f9;color:#334155;font-size:.8rem;font-weight:600}
    .lots-menu-btn{width:38px;height:38px;border-radius:10px;border:1px solid #e2e8f0;background:#fff;display:inline-flex;align-items:center;justify-content:center}
    .lots-selectbar{border:1px solid rgba(13,110,253,.14);border-radius:14px;background:linear-gradient(135deg,rgba(13,110,253,.06),rgba(13,110,253,.02));padding:.9rem 1rem}
    .lots-muted{font-size:.82rem;color:#64748b}
    .lots-card .pagination svg,
    .lots-card nav[role="navigation"] svg{
        width: 16px !important;
        height: 16px !important;
        min-width: 16px !important;
        min-height: 16px !important;
        max-width: 16px !important;
        max-height: 16px !important;
        display: inline-block !important;
        flex: 0 0 16px !important;
    }
    .lots-card .pagination .page-link,
    .lots-card nav[role="navigation"] > div:first-child a,
    .lots-card nav[role="navigation"] > div:first-child span,
    .lots-card nav[role="navigation"] > div:last-child a,
    .lots-card nav[role="navigation"] > div:last-child span{
        display:inline-flex !important;
        align-items:center !important;
        justify-content:center !important;
        min-width:38px;
        min-height:38px;
        overflow:hidden;
    }
    @media (max-width: 991.98px){.lots-stats,.lots-toolbar{grid-template-columns:1fr 1fr}}
    @media (max-width: 575.98px){.lots-stats,.lots-toolbar{grid-template-columns:1fr}}
</style>

<div class="row">
    <div class="col-12">
        <div class="lots-grid">
            <div class="lots-card lots-hero">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <h1 class="lots-title">Lot Management</h1>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('admin.lots.map') }}" class="btn btn-primary"><i data-feather="map"></i> Map View</a>
                        <a href="{{ route('admin.lots.create') }}" class="btn btn-success"><i data-feather="plus"></i> Add Lot</a>
                    </div>
                </div>
            </div>

            <div class="lots-stats">
                <div class="lots-stat"><div class="lots-label">Results</div><div class="lots-value">{{ $lots->total() }}</div></div>
                <div class="lots-stat"><div class="lots-label">Available On Page</div><div class="lots-value">{{ $pageLots->where('status', 'available')->count() + $pageLots->filter(fn($lot) => !$lot->status && !$lot->is_occupied)->count() }}</div></div>
                <div class="lots-stat"><div class="lots-label">Reserved On Page</div><div class="lots-value">{{ $pageLots->where('status', 'reserved')->count() }}</div></div>
                <div class="lots-stat"><div class="lots-label">Occupied On Page</div><div class="lots-value">{{ $pageLots->where('status', 'occupied')->count() + $pageLots->filter(fn($lot) => !$lot->status && $lot->is_occupied)->count() }}</div></div>
            </div>

            <div class="lots-card">
                <div class="lots-body">
                    <form method="GET" action="{{ route('admin.lots.index') }}" class="lots-toolbar mb-3">
                        <div>
                            <label for="lotSearch" class="lots-label">Search</label>
                            <div class="lots-search-wrap">
                                <i data-feather="search" class="lots-search-icon"></i>
                                <input id="lotSearch" name="search" value="{{ $currentSearch }}" class="form-control lots-input lots-search" placeholder="Search by lot, owner, category, status, or deceased">
                            </div>
                        </div>
                        <div>
                            <label for="lotSort" class="lots-label">Sort By</label>
                            <select id="lotSort" name="sort" class="form-select lots-select">
                                <option value="lot_id" @selected($currentSort === 'lot_id')>Lot ID</option>
                                <option value="owner" @selected($currentSort === 'owner')>Owner</option>
                                <option value="category" @selected($currentSort === 'category')>Category</option>
                                <option value="status" @selected($currentSort === 'status')>Status</option>
                                <option value="deceased" @selected($currentSort === 'deceased')>Deceased Count</option>
                            </select>
                        </div>
                        <div>
                            <label for="lotDirection" class="lots-label">Order</label>
                            <select id="lotDirection" name="direction" class="form-select lots-select">
                                <option value="asc" @selected($currentDirection === 'asc')>Ascending</option>
                                <option value="desc" @selected($currentDirection === 'desc')>Descending</option>
                            </select>
                        </div>
                        <div>
                            <label for="lotPerPage" class="lots-label">Rows</label>
                            <select id="lotPerPage" name="per_page" class="form-select lots-select">
                                <option value="10" @selected($currentPerPage === 10)>10</option>
                                <option value="20" @selected($currentPerPage === 20)>20</option>
                                <option value="50" @selected($currentPerPage === 50)>50</option>
                                <option value="100" @selected($currentPerPage === 100)>100</option>
                            </select>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Apply</button>
                            <a href="{{ route('admin.lots.index') }}" class="btn btn-light">Reset</a>
                        </div>
                    </form>

                    <form method="POST" action="{{ route('admin.lots.bulkDestroy') }}" id="bulkDeleteForm" class="mb-3 d-none">
                        @csrf
                        <div class="lots-selectbar d-flex flex-wrap justify-content-between align-items-center gap-3">
                            <div>
                                <div class="fw-semibold"><span id="selectedLotsCount">0</span> lot(s) selected</div>
                                <div class="lots-muted">Choose one lot for map view, or delete multiple lots on this page.</div>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="button" id="mapSelectedBtn" class="btn btn-outline-primary btn-sm">View in Map</button>
                                <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Delete selected lot(s)?')">Delete Selected</button>
                            </div>
                        </div>
                        <div id="bulkDeleteInputs"></div>
                    </form>

                    <div class="lots-table-wrap table-responsive">
                        <table class="table lots-table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th style="width:48px"><input type="checkbox" id="selectAllLots" class="form-check-input" aria-label="Select all lots"></th>
                                    <th>Lot ID</th>
                                    <th>Owner</th>
                                    <th>Lot Category</th>
                                    <th>Status</th>
                                    <th>Deceased</th>
                                    <th class="text-center" style="width:84px">Menu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($lots as $lot)
                                    @php
                                        $status = $lot->status ?? ($lot->is_occupied ? 'occupied' : 'available');
                                        $deceasedNames = $lot->deceased->count() > 0 ? $lot->deceased->map(fn ($d) => trim($d->first_name.' '.$d->last_name))->implode(', ') : '-';
                                    @endphp
                                    <tr class="lots-row">
                                        <td><input type="checkbox" class="form-check-input lot-select" value="{{ $lot->id }}" data-map-url="{{ route('admin.lots.map') }}?lot={{ $lot->id }}"></td>
                                        <td><div class="fw-bold">{{ $lot->lot_id }}</div><div class="lots-muted">Record #{{ $lot->id }}</div></td>
                                        <td>{{ $lot->name }}</td>
                                        <td><span class="lots-pill">{{ $lot->lot_category_label }}</span></td>
                                        <td>
                                            @if($status === 'occupied')
                                                <span class="badge bg-danger">Occupied</span>
                                            @elseif($status === 'reserved')
                                                <span class="badge bg-primary">Reserved</span>
                                            @else
                                                <span class="badge bg-success">Available</span>
                                            @endif
                                        </td>
                                        <td><span class="{{ $deceasedNames === '-' ? 'lots-muted' : '' }}">{{ $deceasedNames }}</span></td>
                                        <td class="text-center">
                                            <div class="dropdown">
                                                <button class="lots-menu-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i data-feather="more-horizontal"></i></button>
                                                <div class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                                    <button type="button" class="dropdown-item js-open-edit-modal"
                                                        data-bs-toggle="modal" data-bs-target="#editLotModal"
                                                        data-id="{{ $lot->id }}"
                                                        data-lot-id="{{ $lot->lot_id }}"
                                                        data-name="{{ $lot->name }}"
                                                        data-section="{{ $lot->section }}"
                                                        data-status="{{ $status }}"
                                                        data-latitude="{{ $lot->latitude }}"
                                                        data-longitude="{{ $lot->longitude }}"
                                                        data-notes="{{ $lot->notes }}">Edit</button>
                                                    <a href="{{ route('admin.lots.map') }}?lot={{ $lot->id }}" class="dropdown-item">View in Map</a>
                                                    <form method="POST" action="{{ route('admin.lots.destroy', $lot->id) }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="text-center py-4 text-muted">No lots found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-end pt-3">
                        <div>{{ $lots->links() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editLotModal" tabindex="-1" aria-labelledby="editLotModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <form method="POST" id="editLotForm">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editLotModalLabel">Edit Lot</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="lots-label">Lot ID</label>
                            <input type="text" id="modalLotIdDisplay" class="form-control lots-select" readonly>
                        </div>
                        <div class="col-md-8">
                            <label class="lots-label">Owner</label>
                            <input type="text" name="name" id="modalLotName" class="form-control lots-input" required>
                        </div>
                        <div class="col-md-4">
                            <label class="lots-label">Lot Category</label>
                            <select name="section" id="modalLotSection" class="form-select lots-select" required>
                                <option value="phase_1">Phase 1</option>
                                <option value="phase_2">Phase 2</option>
                                <option value="garden_lot">Garden Lot</option>
                                <option value="back_office_lot">Back Office Lot</option>
                                <option value="narra">Narra</option>
                                <option value="mausoleum">Mausoleum</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="lots-label">Status</label>
                            <select name="status" id="modalLotStatus" class="form-select lots-select" required>
                                <option value="available">Available</option>
                                <option value="reserved">Reserved</option>
                                <option value="occupied">Occupied</option>
                            </select>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="button" class="btn btn-outline-danger w-100" id="modalAddDeceasedBtn">Add Deceased</button>
                        </div>
                        <div class="col-md-6">
                            <label class="lots-label">Latitude</label>
                            <input type="text" name="latitude" id="modalLotLatitude" class="form-control lots-input" required>
                        </div>
                        <div class="col-md-6">
                            <label class="lots-label">Longitude</label>
                            <input type="text" name="longitude" id="modalLotLongitude" class="form-control lots-input" required>
                        </div>
                        <div class="col-12">
                            <label class="lots-label">Notes</label>
                            <textarea name="notes" id="modalLotNotes" class="form-control lots-textarea" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="mt-4 pt-3 border-top" id="modalDeceasedBlock" style="display:none;">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="lots-label">First Name</label>
                                <input type="text" name="deceased_first_name" id="modalDeceasedFirstName" class="form-control lots-input">
                            </div>
                            <div class="col-md-6">
                                <label class="lots-label">Last Name</label>
                                <input type="text" name="deceased_last_name" id="modalDeceasedLastName" class="form-control lots-input">
                            </div>
                            <div class="col-md-4">
                                <label class="lots-label">Date of Birth</label>
                                <input type="date" name="deceased_date_of_birth" id="modalDeceasedBirth" class="form-control lots-select">
                            </div>
                            <div class="col-md-4">
                                <label class="lots-label">Date of Death</label>
                                <input type="date" name="deceased_date_of_death" id="modalDeceasedDeath" class="form-control lots-select">
                            </div>
                            <div class="col-md-4">
                                <label class="lots-label">Burial Date</label>
                                <input type="date" name="deceased_burial_date" id="modalDeceasedBurial" class="form-control lots-select">
                            </div>
                            <div class="col-12">
                                <label class="lots-label">Deceased Notes</label>
                                <textarea name="deceased_notes" id="modalDeceasedNotes" class="form-control lots-textarea" rows="2"></textarea>
                            </div>
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
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var selectAllInput = document.getElementById('selectAllLots');
    var selectionInputs = Array.from(document.querySelectorAll('.lot-select'));
    var bulkDeleteForm = document.getElementById('bulkDeleteForm');
    var bulkDeleteInputs = document.getElementById('bulkDeleteInputs');
    var selectedLotsCount = document.getElementById('selectedLotsCount');
    var mapSelectedBtn = document.getElementById('mapSelectedBtn');
    var modalTriggers = Array.from(document.querySelectorAll('.js-open-edit-modal'));
    var editLotForm = document.getElementById('editLotForm');
    var modalLotIdDisplay = document.getElementById('modalLotIdDisplay');
    var modalLotName = document.getElementById('modalLotName');
    var modalLotSection = document.getElementById('modalLotSection');
    var modalLotStatus = document.getElementById('modalLotStatus');
    var modalLotLatitude = document.getElementById('modalLotLatitude');
    var modalLotLongitude = document.getElementById('modalLotLongitude');
    var modalLotNotes = document.getElementById('modalLotNotes');
    var modalDeceasedBlock = document.getElementById('modalDeceasedBlock');
    var modalDeceasedFirstName = document.getElementById('modalDeceasedFirstName');
    var modalDeceasedLastName = document.getElementById('modalDeceasedLastName');
    var modalDeceasedBirth = document.getElementById('modalDeceasedBirth');
    var modalDeceasedDeath = document.getElementById('modalDeceasedDeath');
    var modalDeceasedBurial = document.getElementById('modalDeceasedBurial');
    var modalDeceasedNotes = document.getElementById('modalDeceasedNotes');
    var modalAddDeceasedBtn = document.getElementById('modalAddDeceasedBtn');

    function setDateInputValue(input, value) {
        if (!input) return;
        if (window.AdminDatePickers && typeof window.AdminDatePickers.setValue === 'function') {
            window.AdminDatePickers.setValue(input, value);
            return;
        }
        input.value = value || '';
    }

    function selectedCheckboxes() {
        return selectionInputs.filter(function (input) { return input.checked; });
    }

    function syncBulkActions() {
        var selected = selectedCheckboxes();
        var count = selected.length;
        if (selectedLotsCount) selectedLotsCount.textContent = String(count);
        if (bulkDeleteForm) bulkDeleteForm.classList.toggle('d-none', count === 0);
        if (bulkDeleteInputs) {
            bulkDeleteInputs.innerHTML = '';
            selected.forEach(function (input) {
                var hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = 'lot_ids[]';
                hidden.value = input.value;
                bulkDeleteInputs.appendChild(hidden);
            });
        }
        if (mapSelectedBtn) mapSelectedBtn.disabled = count !== 1;
        if (selectAllInput) {
            selectAllInput.checked = selectionInputs.length > 0 && count === selectionInputs.length;
            selectAllInput.indeterminate = count > 0 && count < selectionInputs.length;
        }
    }

    function syncDeceasedBlock() {
        if (!modalLotStatus || !modalDeceasedBlock) return;
        var showBlock = modalLotStatus.value === 'occupied';
        modalDeceasedBlock.style.display = showBlock ? '' : 'none';
        modalDeceasedFirstName.required = showBlock;
        modalDeceasedLastName.required = showBlock;
        if (!showBlock) {
            modalDeceasedFirstName.value = '';
            modalDeceasedLastName.value = '';
            setDateInputValue(modalDeceasedBirth, '');
            setDateInputValue(modalDeceasedDeath, '');
            setDateInputValue(modalDeceasedBurial, '');
            modalDeceasedNotes.value = '';
        }
    }

    if (selectAllInput) {
        selectAllInput.addEventListener('change', function () {
            selectionInputs.forEach(function (input) { input.checked = selectAllInput.checked; });
            syncBulkActions();
        });
    }
    selectionInputs.forEach(function (input) { input.addEventListener('change', syncBulkActions); });
    if (mapSelectedBtn) {
        mapSelectedBtn.addEventListener('click', function () {
            var selected = selectedCheckboxes();
            if (selected.length === 1) window.location.href = selected[0].dataset.mapUrl || '';
        });
    }
    modalTriggers.forEach(function (button) {
        button.addEventListener('click', function () {
            editLotForm.action = "{{ url('/admin/lots') }}/" + button.dataset.id;
            modalLotIdDisplay.value = button.dataset.lotId || '';
            modalLotName.value = button.dataset.name || '';
            modalLotSection.value = button.dataset.section || '';
            modalLotStatus.value = button.dataset.status || 'available';
            modalLotLatitude.value = button.dataset.latitude || '';
            modalLotLongitude.value = button.dataset.longitude || '';
            modalLotNotes.value = button.dataset.notes || '';
            syncDeceasedBlock();
        });
    });
    if (modalLotStatus) modalLotStatus.addEventListener('change', syncDeceasedBlock);
    if (modalAddDeceasedBtn) {
        modalAddDeceasedBtn.addEventListener('click', function () {
            modalLotStatus.value = 'occupied';
            syncDeceasedBlock();
            modalDeceasedFirstName.focus();
        });
    }
    syncBulkActions();
    if (window.feather && typeof window.feather.replace === 'function') window.feather.replace();
});
</script>
@endsection
