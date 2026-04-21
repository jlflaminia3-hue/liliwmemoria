@extends('admin.admin_master')

@section('admin')
@php
    $deceased = $exhumation->deceased;
    $lot = $deceased?->lot;
    $client = $deceased?->client;
    
    $statusColors = [
        'pending' => 'warning',
        'approved' => 'info',
        'completed' => 'success',
    ];
    $currentStatus = $exhumation->workflow_status ?? 'pending';
@endphp

<div class="content">
    <div class="container-xxl">
        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column gap-3">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">Exhumation Case</h4>
                <div class="text-muted mt-1">
                    {{ $deceased?->full_name ?? 'Unknown' }} · Lot {{ $lot?->lot_id ?? '-' }}
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.exhumations.index') }}" class="btn btn-outline-secondary btn-sm">All Exhumations</a>
                <a href="{{ route('admin.interments.show', $deceased) }}" class="btn btn-outline-secondary btn-sm">View Deceased</a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="row g-3">
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted small text-uppercase fw-semibold">Status</span>
                            <span class="badge bg-{{ $statusColors[$currentStatus] ?? 'secondary' }}">
                                {{ ucfirst($currentStatus) }}
                            </span>
                        </div>

                        <form method="POST" action="{{ route('admin.exhumations.update', $exhumation) }}">
                            @csrf
                            @method('PUT')
                            
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Change Status</label>
                                <select name="workflow_status" class="form-select" onchange="this.form.submit()">
                                    <option value="pending" @selected($currentStatus === 'pending')>Pending</option>
                                    <option value="approved" @selected($currentStatus === 'approved')>Approved</option>
                                    <option value="completed" @selected($currentStatus === 'completed')>Completed</option>
                                </select>
                            </div>
                        </form>

                        <hr>

                        <div class="mb-3">
                            <div class="text-muted small">Requested By</div>
                            <div class="fw-semibold">{{ $exhumation->requested_by_name ?? '-' }}</div>
                            <div class="small text-muted">{{ $exhumation->requested_by_relationship ?? '-' }}</div>
                        </div>

                        <div class="mb-3">
                            <div class="text-muted small">Requested Date</div>
                            <div class="fw-semibold">{{ $exhumation->requested_at?->format('M d, Y') ?? '-' }}</div>
                        </div>

                        <div class="mb-3">
                            <div class="text-muted small">Destination Cemetery</div>
                            <div class="fw-semibold">{{ $exhumation->destination_cemetery_name ?? 'Not specified' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Details</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.exhumations.update', $exhumation) }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Requested By</label>
                                    <input type="text" name="requested_by_name" class="form-control" value="{{ $exhumation->requested_by_name }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Relationship</label>
                                    <select name="requested_by_relationship" class="form-select">
                                        <option value="">Select...</option>
                                        <option value="Spouse" @selected($exhumation->requested_by_relationship === 'Spouse')>Spouse</option>
                                        <option value="Parent" @selected($exhumation->requested_by_relationship === 'Parent')>Parent</option>
                                        <option value="Child" @selected($exhumation->requested_by_relationship === 'Child')>Child</option>
                                        <option value="Sibling" @selected($exhumation->requested_by_relationship === 'Sibling')>Sibling</option>
                                        <option value="Other" @selected($exhumation->requested_by_relationship === 'Other')>Other</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Destination Cemetery</label>
                                    <input type="text" name="destination_cemetery_name" class="form-control" value="{{ $exhumation->destination_cemetery_name }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Transfer Date</label>
                                    <input type="date" name="exhumed_at" class="form-control" value="{{ $exhumation->exhumed_at?->format('Y-m-d') }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Notes</label>
                                    <textarea name="notes" class="form-control" rows="3">{{ $exhumation->notes }}</textarea>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Documents</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.exhumations.update', $exhumation) }}" enctype="multipart/form-data" class="mb-3">
                            @csrf
                            @method('PUT')
                            
                            <div class="row g-3 align-items-end">
                                <div class="col-md-8">
                                    <label class="form-label fw-semibold">Upload Transfer Permit</label>
                                    <input type="file" name="transfer_permit" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-outline-secondary w-100">Upload</button>
                                </div>
                            </div>
                        </form>

                        @if ($exhumation->transfer_permit_path)
                            <div class="d-flex align-items-center justify-content-between p-3 border rounded">
                                <div>
                                    <div class="fw-semibold">Transfer Permit</div>
                                    <div class="small text-muted">{{ basename($exhumation->transfer_permit_path) }}</div>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.exhumations.documents.download', [$exhumation, 'transfer_permit']) }}" class="btn btn-sm btn-outline-primary">Download</a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
