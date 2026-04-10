@extends('admin.admin_master')
@section('admin')

<div class="content">
    <div class="container-xxl">
        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">Cemetery Dashboard</h4>
                <div class="text-muted mt-1">Lots, clients, interments, and contract overview</div>
            </div>
            <div class="mt-3 mt-sm-0 d-flex gap-2 flex-wrap">
                <a href="{{ route('admin.clients.create') }}" class="btn btn-success btn-sm">
                    <i data-feather="user-plus" class="me-1" style="height: 16px; width: 16px;"></i>
                    Add Client
                </a>
                <a href="{{ route('admin.analytics.clients') }}" class="btn btn-outline-secondary btn-sm">
                    <i data-feather="bar-chart-2" class="me-1" style="height: 16px; width: 16px;"></i>
                    Client Analytics
                </a>
                <a href="{{ route('admin.lots.create') }}" class="btn btn-primary btn-sm">
                    <i data-feather="plus-square" class="me-1" style="height: 16px; width: 16px;"></i>
                    Add Lot
                </a>
                <a href="{{ route('admin.lots.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i data-feather="grid" class="me-1" style="height: 16px; width: 16px;"></i>
                    View Lots
                </a>
                <a href="{{ route('admin.lots.map') }}" class="btn btn-outline-secondary btn-sm">
                    <i data-feather="map-pin" class="me-1" style="height: 16px; width: 16px;"></i>
                    Map View
                </a>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-6 col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-muted">Total Lots</div>
                                <div class="fs-22 fw-semibold text-black">{{ number_format($lotsTotal) }}</div>
                            </div>
                            <div class="border rounded-2 p-2">
                                <i data-feather="layers" style="height: 18px; width: 18px;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-muted">Available</div>
                                <div class="fs-22 fw-semibold text-success">{{ number_format($lotsAvailable) }}</div>
                            </div>
                            <div class="border rounded-2 p-2">
                                <i data-feather="check-circle" style="height: 18px; width: 18px;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-muted">Reserved</div>
                                <div class="fs-22 fw-semibold text-warning">{{ number_format($lotsReserved) }}</div>
                            </div>
                            <div class="border rounded-2 p-2">
                                <i data-feather="bookmark" style="height: 18px; width: 18px;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-muted">Occupied</div>
                                <div class="fs-22 fw-semibold text-danger">{{ number_format($lotsOccupied) }}</div>
                            </div>
                            <div class="border rounded-2 p-2">
                                <i data-feather="x-circle" style="height: 18px; width: 18px;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-muted">Clients</div>
                                <div class="fs-22 fw-semibold text-black">{{ number_format($clientsTotal) }}</div>
                            </div>
                            <div class="border rounded-2 p-2">
                                <i data-feather="users" style="height: 18px; width: 18px;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-muted">Deceased Records</div>
                                <div class="fs-22 fw-semibold text-black">{{ number_format($deceasedTotal) }}</div>
                            </div>
                            <div class="border rounded-2 p-2">
                                <i data-feather="file-text" style="height: 18px; width: 18px;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-muted">Active Contracts</div>
                                <div class="fs-22 fw-semibold text-primary">{{ number_format($contractsActive) }}</div>
                            </div>
                            <div class="border rounded-2 p-2">
                                <i data-feather="clipboard" style="height: 18px; width: 18px;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-muted">Pending / Balance</div>
                                <div class="fs-22 fw-semibold text-danger">{{ number_format($contractsPastDue) }}</div>
                                <div class="text-muted mt-1">₱{{ number_format($outstandingBalance, 2) }}</div>
                            </div>
                            <div class="border rounded-2 p-2">
                                <i data-feather="alert-circle" style="height: 18px; width: 18px;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mt-0">
            <div class="col-lg-8">
                <div class="card h-100">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="card-title mb-0">Upcoming Burials</h5>
                        <a href="{{ route('admin.lots.map') }}" class="btn btn-outline-secondary btn-sm">Open Map</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Deceased</th>
                                        <th>Lot</th>
                                        <th>Section</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($upcomingBurials as $record)
                                        <tr>
                                            <td>{{ $record->burial_date?->format('Y-m-d') ?? '-' }}</td>
                                            <td>{{ trim($record->first_name.' '.$record->last_name) }}</td>
                                            <td>{{ $record->lot ? 'Lot ID '.$record->lot->lot_id : '-' }}</td>
                                            <td>{{ $record->lot?->section ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                    @if ($upcomingBurials->isEmpty())
                                        <tr>
                                            <td colspan="4" class="text-muted text-center py-3">No upcoming burials found.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Availability by Section</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Section</th>
                                        <th class="text-end">Avail</th>
                                        <th class="text-end">Res</th>
                                        <th class="text-end">Occ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($lotsBySection as $row)
                                        <tr>
                                            <td>{{ $row->section }}</td>
                                            <td class="text-end text-success">{{ number_format((int) $row->available) }}</td>
                                            <td class="text-end text-warning">{{ number_format((int) $row->reserved) }}</td>
                                            <td class="text-end text-danger">{{ number_format((int) $row->occupied) }}</td>
                                        </tr>
                                    @endforeach
                                    @if ($lotsBySection->isEmpty())
                                        <tr>
                                            <td colspan="4" class="text-muted text-center py-3">No lots yet.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mt-0">
            <div class="col-lg-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Pending Contracts</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Client</th>
                                        <th>Completion</th>
                                        <th class="text-end">Balance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pastDueContracts as $contract)
                                        @php($total = (float) ($contract->total_amount ?? 0))
                                        @php($paid = (float) ($contract->amount_paid ?? 0))
                                        @php($balance = max(0, $total - $paid))
                                        <tr>
                                            <td>
                                                <div class="fw-semibold">{{ $contract->client?->full_name ?? 'Unknown' }}</div>
                                                <div class="text-muted">
                                                    {{ $contract->contract_number ? 'Contract #'.$contract->contract_number : 'No contract #' }}
                                                    {{ $contract->lot ? ' • Lot ID '.$contract->lot->lot_id : '' }}
                                                </div>
                                            </td>
                                            <td>{{ $contract->due_date?->format('Y-m-d') ?? '-' }}</td>
                                            <td class="text-end">₱{{ number_format($balance, 2) }}</td>
                                        </tr>
                                    @endforeach
                                    @if ($pastDueContracts->isEmpty())
                                        <tr>
                                            <td colspan="3" class="text-muted text-center py-3">No pending contracts.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Recent Reservations</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Client</th>
                                        <th>Lot</th>
                                        <th class="text-end">Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($recentReservations as $ownership)
                                        <tr>
                                            <td>{{ $ownership->client?->full_name ?? 'Unknown' }}</td>
                                            <td>{{ $ownership->lot ? 'Lot ID '.$ownership->lot->lot_id : '-' }}</td>
                                            <td class="text-end">{{ $ownership->created_at?->format('Y-m-d') ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                    @if ($recentReservations->isEmpty())
                                        <tr>
                                            <td colspan="3" class="text-muted text-center py-3">No recent reservations.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Recent Interments</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Deceased</th>
                                        <th>Lot</th>
                                        <th class="text-end">Burial</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($recentInterments as $record)
                                        <tr>
                                            <td>{{ trim($record->first_name.' '.$record->last_name) }}</td>
                                            <td>{{ $record->lot ? 'Lot ID '.$record->lot->lot_id : '-' }}</td>
                                            <td class="text-end">{{ $record->burial_date?->format('Y-m-d') ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                    @if ($recentInterments->isEmpty())
                                        <tr>
                                            <td colspan="3" class="text-muted text-center py-3">No interment records yet.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
