@extends('admin.admin_master')

@section('admin')
@php
    $deceased = $exhumation->deceased;
    $lot = $deceased?->lot;
    $client = $deceased?->client;

    $workflow = [
        ['key' => 'draft', 'label' => 'Draft', 'at' => null],
        ['key' => 'submitted', 'label' => 'Submitted', 'at' => $exhumation->requested_at],
        ['key' => 'approved', 'label' => 'Approved', 'at' => $exhumation->approved_at],
        ['key' => 'scheduled', 'label' => 'Scheduled', 'at' => $exhumation->exhumed_at],
        ['key' => 'completed', 'label' => 'Completed', 'at' => null],
        ['key' => 'archived', 'label' => 'Archived', 'at' => null],
    ];
    $workflowKeys = array_map(fn ($s) => $s['key'], $workflow);
    $currentKey = (string) ($exhumation->workflow_status ?? 'draft');
    $currentIdx = array_search($currentKey, $workflowKeys, true);
    if ($currentIdx === false) {
        $currentIdx = 0;
    }

    $warnings = [];
    $hasTransferPermit = ! empty($exhumation->transfer_permit_path);
    $hasDestination = ! empty($exhumation->destination_cemetery_name);

    if ($currentKey === 'completed') {
        if (! $hasTransferPermit) $warnings[] = 'Missing transfer permit (required for completed transfers).';
        if (! $hasDestination) $warnings[] = 'Missing destination cemetery (required for transfer).';
    }
@endphp

<style>
    .exh-card{border:1px solid rgba(15,23,42,.08);border-radius:16px;box-shadow:0 12px 28px rgba(15,23,42,.06)}
    .exh-step{display:grid;grid-template-columns:16px 1fr auto;gap:10px;align-items:center;padding:10px 0}
    .exh-dot{width:12px;height:12px;border-radius:999px;background:#cbd5e1;margin-left:2px}
    .exh-step--done .exh-dot{background:#22c55e}
    .exh-step--current .exh-dot{background:#2563eb;box-shadow:0 0 0 4px rgba(37,99,235,.14)}
    .exh-step__label{font-weight:700;color:#0f172a}
    .exh-step__date{font-size:.82rem;color:#64748b}
    .exh-doc{display:flex;align-items:center;justify-content:space-between;gap:10px;padding:10px 0;border-top:1px dashed rgba(148,163,184,.5)}
    .exh-doc:first-child{border-top:0}
    .exh-doc__left{min-width:0}
    .exh-doc__name{font-weight:700;color:#0f172a;margin:0}
    .exh-doc__meta{font-size:.82rem;color:#64748b;margin:2px 0 0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
    .exh-badge{display:inline-flex;align-items:center;gap:6px;border-radius:999px;padding:.22rem .6rem;font-size:.75rem;font-weight:700;border:1px solid transparent}
    .exh-badge--ok{background:rgba(34,197,94,.12);color:#166534;border-color:rgba(34,197,94,.22)}
    .exh-badge--warn{background:rgba(245,158,11,.12);color:#92400e;border-color:rgba(245,158,11,.22)}
    .exh-badge--miss{background:rgba(239,68,68,.10);color:#991b1b;border-color:rgba(239,68,68,.22)}
    .exh-tabs .nav-link{border-radius:999px}
    .exh-tabs .nav-link.active{background:#0f172a;color:#fff;border-color:#0f172a}
</style>

<div class="content">
    <div class="container-xxl">
        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column gap-3">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">Exhumation Case</h4>
                <div class="text-muted mt-1">
                    {{ $deceased?->full_name ?? 'Unknown deceased' }}
                    @if ($lot) - Lot {{ $lot->lot_id }} @endif
                </div>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('admin.exhumations.index') }}" class="btn btn-outline-secondary btn-sm">All Exhumations</a>
                <a href="{{ route('admin.interments.index') }}" class="btn btn-outline-secondary btn-sm">Interments</a>
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

        <div class="row g-3">
            <div class="col-lg-4">
                <div class="card exh-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="text-muted small text-uppercase fw-semibold">Workflow Status</div>
                                <div class="fs-4 fw-bold">{{ $workflow[$currentIdx]['label'] ?? 'Requested' }}</div>
                            </div>
                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">
                                #{{ $exhumation->id }}
                            </span>
                        </div>

                        <hr class="my-3">

                        <div class="text-muted small text-uppercase fw-semibold mb-2">Checkpoints</div>
                        <div>
                            @foreach ($workflow as $idx => $step)
                                @php
                                    $cls = $idx < $currentIdx ? 'exh-step--done' : ($idx === $currentIdx ? 'exh-step--current' : '');
                                @endphp
                                <div class="exh-step {{ $cls }}">
                                    <div class="exh-dot"></div>
                                    <div class="exh-step__label">{{ $step['label'] }}</div>
                                    <div class="exh-step__date">{{ $step['at']?->format('Y-m-d H:i') ?? '-' }}</div>
                                </div>
                            @endforeach
                        </div>

                        @if (! empty($warnings))
                            <div class="alert alert-warning mt-3 mb-0">
                                <div class="fw-semibold mb-1">Action Needed</div>
                                <ul class="mb-0 ps-3">
                                    @foreach ($warnings as $w)
                                        <li>{{ $w }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <hr class="my-3">

                        <div class="text-muted small text-uppercase fw-semibold mb-2">Documents</div>
                        {{-- Exhumation permit section removed - to be edited later --}}
                        <div class="exh-doc">
                            <div class="exh-doc__left">
                                <p class="exh-doc__name mb-0">Transfer Permit</p>
                                <p class="exh-doc__meta mb-0">{{ $exhumation->transfer_permit_path ? basename($exhumation->transfer_permit_path) : 'No file uploaded yet' }}</p>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="exh-badge {{ $exhumation->transfer_permit_path ? 'exh-badge--ok' : 'exh-badge--warn' }}">
                                    {{ $exhumation->transfer_permit_path ? 'UPLOADED' : 'OPTIONAL' }}
                                </span>
                                @if ($exhumation->transfer_permit_path)
                                    <a class="btn btn-sm btn-outline-success" href="{{ route('admin.exhumations.documents.download', [$exhumation, 'transfer_permit']) }}">Download</a>
                                    <button type="submit" form="exh-delete-transfer-permit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete the transfer permit?')">Delete</button>
                                @else
                                    <button type="button" class="btn btn-sm btn-light" data-bs-toggle="pill" data-bs-target="#tab-docs">Upload</button>
                                @endif
                            </div>
                        </div>
                        <div class="exh-doc">
                            <div class="exh-doc__left">
                                <p class="exh-doc__name mb-0">Transfer Certificate (PDF)</p>
                                <p class="exh-doc__meta mb-0">{{ $exhumation->transfer_certificate_path ? basename($exhumation->transfer_certificate_path) : 'Generate from this case' }}</p>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="exh-badge {{ $exhumation->transfer_certificate_path ? 'exh-badge--ok' : 'exh-badge--warn' }}">
                                    {{ $exhumation->transfer_certificate_path ? 'READY' : 'NOT GENERATED' }}
                                </span>
                                <button type="submit" form="exh-generate-certificate" class="btn btn-sm btn-primary">Generate</button>
                                @if ($exhumation->transfer_certificate_path)
                                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.exhumations.transferCertificate.download', $exhumation) }}">Download</a>
                                    <button type="submit" form="exh-delete-transfer-certificate" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete the transfer certificate PDF?')">Delete</button>
                                @endif
                            </div>
                        </div>
                        @if ($exhumation->transfer_certificate_generated_at)
                            <div class="text-muted small mt-2">Certificate generated {{ $exhumation->transfer_certificate_generated_at->format('Y-m-d H:i') }}</div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card exh-card">
                    <div class="card-body">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                            <h5 class="card-title mb-0">Case Details</h5>
                            <ul class="nav nav-pills exh-tabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#tab-workflow" type="button" role="tab">Workflow</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-docs" type="button" role="tab">Documents</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-destination" type="button" role="tab">Destination</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-transport" type="button" role="tab">Transport</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-notes" type="button" role="tab">Notes</button>
                                </li>
                            </ul>
                        </div>

                        <form id="exh-update-form" method="POST" action="{{ route('admin.exhumations.update', $exhumation) }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="tab-workflow" role="tabpanel">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Status</label>
                                            <select name="workflow_status" class="form-select" required>
                                                @foreach ($workflow as $idx => $step)
                                                    @if ($idx < $currentIdx)
                                                        @continue
                                                    @endif
                                                    <option value="{{ $step['key'] }}" @selected($exhumation->workflow_status === $step['key'])>{{ $step['label'] }}</option>
                                                @endforeach
                                            </select>
                                            <div class="form-text">Forward-only workflow. Required uploads are enforced per status.</div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Requested By</label>
                                            <input type="text" name="requested_by_name" class="form-control" value="{{ old('requested_by_name', $exhumation->requested_by_name ?: $deceased?->client?->full_name) }}" placeholder="Auto-populated from lot client">
                                            @if ($deceased?->client)
                                                <div class="form-text">From client: <strong>{{ $deceased->client->full_name }}</strong></div>
                                            @endif
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Relationship to Deceased</label>
                                            <select name="requested_by_relationship" class="form-select" required>
                                                <option value="">-- Select Relationship --</option>
                                                <option value="Spouse" @selected(old('requested_by_relationship', $exhumation->requested_by_relationship) === 'Spouse')>Spouse</option>
                                                <option value="Parent" @selected(old('requested_by_relationship', $exhumation->requested_by_relationship) === 'Parent')>Parent</option>
                                                <option value="Child" @selected(old('requested_by_relationship', $exhumation->requested_by_relationship) === 'Child')>Child</option>
                                                <option value="Son" @selected(old('requested_by_relationship', $exhumation->requested_by_relationship) === 'Son')>Son</option>
                                                <option value="Daughter" @selected(old('requested_by_relationship', $exhumation->requested_by_relationship) === 'Daughter')>Daughter</option>
                                                <option value="Sibling" @selected(old('requested_by_relationship', $exhumation->requested_by_relationship) === 'Sibling')>Sibling</option>
                                                <option value="Grandchild" @selected(old('requested_by_relationship', $exhumation->requested_by_relationship) === 'Grandchild')>Grandchild</option>
                                                <option value="Grandparent" @selected(old('requested_by_relationship', $exhumation->requested_by_relationship) === 'Grandparent')>Grandparent</option>
                                                <option value="Nephew/Niece" @selected(old('requested_by_relationship', $exhumation->requested_by_relationship) === 'Nephew/Niece')>Nephew/Niece</option>
                                                <option value="Aunt/Uncle" @selected(old('requested_by_relationship', $exhumation->requested_by_relationship) === 'Aunt/Uncle')>Aunt/Uncle</option>
                                                <option value="Cousin" @selected(old('requested_by_relationship', $exhumation->requested_by_relationship) === 'Cousin')>Cousin</option>
                                                <option value="In-Law" @selected(old('requested_by_relationship', $exhumation->requested_by_relationship) === 'In-Law')>In-Law</option>
                                                <option value="Other" @selected(old('requested_by_relationship', $exhumation->requested_by_relationship) === 'Other')>Other</option>
                                            </select>
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Requested At</label>
                                            <input type="datetime-local" name="requested_at" class="form-control" value="{{ old('requested_at', $exhumation->requested_at?->format('Y-m-d\\TH:i')) }}">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Approved At</label>
                                            <input type="datetime-local" name="approved_at" class="form-control" value="{{ old('approved_at', $exhumation->approved_at?->format('Y-m-d\\TH:i')) }}">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Exhumed At</label>
                                            <input type="datetime-local" name="exhumed_at" class="form-control" value="{{ old('exhumed_at', $exhumation->exhumed_at?->format('Y-m-d\\TH:i')) }}">
                                        </div>

                                        <div class="col-12">
                                            <div class="alert alert-info mb-0">
                                                Once the status reaches <strong>Scheduled</strong> or later, the interment is marked <strong>exhumed</strong> and the lot becomes <strong>vacant</strong> (or <strong>reserved</strong> if there is an active owner).
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="tab-docs" role="tabpanel">
                                    <div class="row g-3">
                                        {{-- Exhumation permit upload removed - to be edited later --}}
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Exhumation Permit Upload</label>
                                            <input type="file" name="exhumation_permit" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                                            @if ($exhumation->exhumation_permit_path)
                                                <div class="form-text">
                                                    Current: {{ basename($exhumation->exhumation_permit_path) }}
                                                    &middot; <a href="{{ route('admin.exhumations.documents.download', [$exhumation, 'exhumation_permit']) }}">Download</a>
                                                    &middot; <button type="submit" form="exh-delete-exhumation-permit" class="btn btn-link p-0 align-baseline" onclick="return confirm('Delete the exhumation permit?')">Delete</button>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Transfer Permit Upload</label>
                                            <input type="file" name="transfer_permit" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                                            @if ($exhumation->transfer_permit_path)
                                                <div class="form-text">
                                                    Current: {{ basename($exhumation->transfer_permit_path) }}
                                                    &middot; <a href="{{ route('admin.exhumations.documents.download', [$exhumation, 'transfer_permit']) }}">Download</a>
                                                    &middot; <button type="submit" form="exh-delete-transfer-permit" class="btn btn-link p-0 align-baseline" onclick="return confirm('Delete the transfer permit?')">Delete</button>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-12">
                                            <div class="d-flex flex-wrap gap-2 align-items-center">
                                                <button type="submit" form="exh-generate-certificate" class="btn btn-sm btn-primary">Generate Transfer Certificate PDF</button>
                                                @if ($exhumation->transfer_certificate_path)
                                                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.exhumations.transferCertificate.download', $exhumation) }}">Download Current PDF</a>
                                                    <button type="submit" form="exh-delete-transfer-certificate" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete the transfer certificate PDF?')">Delete PDF</button>
                                                @endif
                                            </div>
                                            <div class="text-muted small mt-2">Tip: Generate the certificate after destination and transport details are filled.</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="tab-destination" role="tabpanel">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Cemetery Name</label>
                                            <input type="text" name="destination_cemetery_name" class="form-control" value="{{ old('destination_cemetery_name', $exhumation->destination_cemetery_name) }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Contact Person</label>
                                            <input type="text" name="destination_contact_person" class="form-control" value="{{ old('destination_contact_person', $exhumation->destination_contact_person) }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Contact Phone</label>
                                            <input type="text" name="destination_contact_phone" class="form-control" value="{{ old('destination_contact_phone', $exhumation->destination_contact_phone) }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Contact Email</label>
                                            <input type="email" name="destination_contact_email" class="form-control" value="{{ old('destination_contact_email', $exhumation->destination_contact_email) }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Address</label>
                                            <input type="text" name="destination_address" class="form-control" value="{{ old('destination_address', $exhumation->destination_address) }}">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold">City</label>
                                            <input type="text" name="destination_city" class="form-control" value="{{ old('destination_city', $exhumation->destination_city) }}">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold">Province</label>
                                            <input type="text" name="destination_province" class="form-control" value="{{ old('destination_province', $exhumation->destination_province) }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="tab-transport" role="tabpanel">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Transport Company</label>
                                            <input type="text" name="transport_company" class="form-control" value="{{ old('transport_company', $exhumation->transport_company) }}">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Vehicle Plate</label>
                                            <input type="text" name="transport_vehicle_plate" class="form-control" value="{{ old('transport_vehicle_plate', $exhumation->transport_vehicle_plate) }}">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Driver Name</label>
                                            <input type="text" name="transport_driver_name" class="form-control" value="{{ old('transport_driver_name', $exhumation->transport_driver_name) }}">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label fw-semibold">Transport Notes</label>
                                            <textarea name="transport_log" class="form-control" rows="4">{{ old('transport_log', $exhumation->transport_log) }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="tab-notes" role="tabpanel">
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label class="form-label fw-semibold">Internal Notes</label>
                                            <textarea name="notes" class="form-control" rows="6">{{ old('notes', $exhumation->notes) }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <a href="{{ route('admin.exhumations.index') }}" class="btn btn-light">Back</a>
                                <button id="exh-save-btn" type="submit" class="btn btn-primary">Save Updates</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<form id="exh-generate-certificate" method="POST" action="{{ route('admin.exhumations.transferCertificate.generate', $exhumation) }}">
    @csrf
</form>

{{-- Exhumation permit delete form removed - to be edited later --}}

<form id="exh-delete-transfer-permit" method="POST" action="{{ route('admin.exhumations.documents.destroy', [$exhumation, 'transfer_permit']) }}">
    @csrf
    @method('DELETE')
</form>

<form id="exh-delete-transfer-certificate" method="POST" action="{{ route('admin.exhumations.documents.destroy', [$exhumation, 'transfer_certificate']) }}">
    @csrf
    @method('DELETE')
</form>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const updateForm = document.getElementById('exh-update-form');
        const saveButton = document.getElementById('exh-save-btn');

        if (!updateForm || !saveButton) return;

        updateForm.addEventListener('submit', function () {
            saveButton.disabled = true;
            saveButton.innerText = 'Saving...';
        });
    });
</script>
@endsection
