@extends('admin.admin_master')
@section('admin')

<div class="content">
    <div class="container-xxl">
        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column gap-3">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">Dashboard</h4>
                <div class="text-muted mt-1">KPIs → Charts → Tables → Logs</div>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('admin.clients.create') }}" class="btn btn-success btn-sm">
                    <i data-feather="user-plus" class="me-1" style="height: 16px; width: 16px;"></i>
                    Add Client
                </a>
                <a href="{{ route('admin.lots.map') }}" class="btn btn-outline-secondary btn-sm">
                    <i data-feather="map" class="me-1" style="height: 16px; width: 16px;"></i>
                    Map View
                </a>
                <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i data-feather="download" class="me-1" style="height: 16px; width: 16px;"></i>
                    Reports
                </a>
            </div>
        </div>

        <div class="dashboard-kpi-bar position-sticky mb-3">
            <div class="row g-3">
                <div class="col-12 col-sm-6 col-xl-4 col-xxl-2">
                    <div class="card kpi-card border-0 shadow-sm h-100">
                        <div class="card-body py-2">
                            <div class="text-muted small text-uppercase fw-semibold">Total Clients</div>
                            <div class="fs-4 fw-bold">{{ number_format($clientsTotal) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-xl-4 col-xxl-2">
                    <div class="card kpi-card border-0 shadow-sm h-100">
                        <div class="card-body py-2">
                            <div class="text-muted small text-uppercase fw-semibold">Active vs Inactive</div>
                            <div class="fw-bold">
                                <span class="text-primary">{{ number_format($clientsActive) }}</span>
                                <span class="text-muted">/</span>
                                <span class="text-danger">{{ number_format($clientsInactive) }}</span>
                            </div>
                            <div class="text-muted small">Last 30 days / 6+ months</div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-xl-4 col-xxl-2">
                    <div class="card kpi-card border-0 shadow-sm h-100">
                        <div class="card-body py-2">
                            <div class="text-muted small text-uppercase fw-semibold">New Clients This Month</div>
                            <div class="fs-4 fw-bold text-success">{{ number_format($clientsNewThisMonth) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-xl-4 col-xxl-2">
                    <div class="card kpi-card border-0 shadow-sm h-100">
                        <div class="card-body py-2">
                            <div class="text-muted small text-uppercase fw-semibold">Total Plots Reserved</div>
                            <div class="fs-4 fw-bold text-warning">{{ number_format($lotsReserved) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-xl-4 col-xxl-2">
                    <div class="card kpi-card border-0 shadow-sm h-100">
                        <div class="card-body py-2">
                            <div class="text-muted small text-uppercase fw-semibold">Pending Payments</div>
                            <div class="fw-bold">
                                <span class="text-warning">{{ number_format($contractsPastDue) }}</span>
                                <span class="text-muted small ms-1">contracts</span>
                            </div>
                            <div class="text-muted small">Outstanding: ₱{{ number_format($outstandingBalance, 2) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-xl-4 col-xxl-2">
                    <div class="card kpi-card border-0 shadow-sm h-100">
                        <div class="card-body py-2">
                            <div class="text-muted small text-uppercase fw-semibold">Upcoming Interments</div>
                            <div class="fs-4 fw-bold">{{ number_format($upcomingIntermentsCount) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="accordion" id="dashboardSections">
            <div class="accordion-item border-0 shadow-sm mb-3" id="clientsSection">
                <h2 class="accordion-header">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseClients" aria-expanded="true">
                        Clients
                    </button>
                </h2>
                <div id="collapseClients" class="accordion-collapse collapse show" data-bs-parent="#dashboardSections">
                    <div class="accordion-body">
                        <div class="row g-3">
                            <div class="col-xl-8">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <div class="fw-semibold">Monthly Growth Trend</div>
                                            <a href="{{ route('admin.clients.index') }}" class="btn btn-outline-secondary btn-sm">View Clients</a>
                                        </div>
                                        <div id="dash_clients_growth" class="dashboard-chart" style="height: 280px;"><canvas></canvas></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <div class="fw-semibold">Retention Rate</div>
                                            <div class="text-muted small">% with 2+ interactions</div>
                                        </div>
                                        <div class="row align-items-center g-2">
                                            <div class="col-6">
                                                <div id="dash_clients_retention" class="dashboard-chart-sm" style="height: 220px;"><canvas></canvas></div>
                                            </div>
                                            <div class="col-6">
                                                <div class="display-6 fw-bold">{{ $retentionRate }}%</div>
                                                <div class="text-muted small">Reservations, communications, maintenance</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <div class="fw-semibold">Top 10 Most Active Clients</div>
                                            <a href="{{ route('admin.clients.index') }}" class="btn btn-outline-secondary btn-sm">Open Clients</a>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-hover align-middle mb-0">
                                                <thead>
                                                    <tr>
                                                        <th>Client</th>
                                                        <th class="text-end">Activity Score</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse ($topActiveClients as $c)
                                                        <tr>
                                                            <td>
                                                                <a href="{{ route('admin.clients.show', $c->id) }}">{{ $c->first_name }} {{ $c->last_name }}</a>
                                                            </td>
                                                            <td class="text-end">{{ $c->activity_score }}</td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="2" class="text-muted text-center py-3">No activity data yet.</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="accordion-item border-0 shadow-sm mb-3" id="plotsSection">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePlots" aria-expanded="false">
                        Plots
                    </button>
                </h2>
                <div id="collapsePlots" class="accordion-collapse collapse" data-bs-parent="#dashboardSections">
                    <div class="accordion-body">
                        <div class="row g-3">
                            <div class="col-xl-4">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <div class="fw-semibold">Occupied vs Available</div>
                                            <a href="{{ route('admin.lots.map') }}" class="btn btn-outline-secondary btn-sm">Open Map</a>
                                        </div>
                                        <div id="dash_plots_status" class="dashboard-chart" style="height: 280px;"><canvas></canvas></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-8">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="card border-0 shadow-sm h-100">
                                            <div class="card-body">
                                                <div class="text-muted small text-uppercase fw-semibold">% Occupancy</div>
                                                @php($occupancy = $lotsTotal > 0 ? round(($lotsOccupied / $lotsTotal) * 100, 1) : 0)
                                                <div class="display-6 fw-bold">{{ $occupancy }}%</div>
                                                <div class="text-muted small">{{ number_format($lotsOccupied) }} occupied out of {{ number_format($lotsTotal) }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card border-0 shadow-sm h-100">
                                            <div class="card-body">
                                                <div class="text-muted small text-uppercase fw-semibold">Avg Reservation Time</div>
                                                <div class="display-6 fw-bold">{{ number_format($avgReservationDays, 1) }}</div>
                                                <div class="text-muted small">Days until expiry (active reservations)</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="card border-0 shadow-sm">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center justify-content-between mb-2">
                                                    <div class="fw-semibold">Plot Reservations</div>
                                                    <a href="{{ route('admin.reservations.index') }}" class="btn btn-outline-secondary btn-sm">Open Reservations</a>
                                                </div>
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
                                </div>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm mt-3">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <div class="fw-semibold">Lots by Section</div>
                                    <a href="{{ route('admin.lots.index') }}" class="btn btn-outline-secondary btn-sm">Open Lots</a>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th>Section</th>
                                                <th class="text-end">Total</th>
                                                <th class="text-end">Available</th>
                                                <th class="text-end">Reserved</th>
                                                <th class="text-end">Occupied</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($lotsBySection as $row)
                                                <tr>
                                                    <td>{{ $row->section }}</td>
                                                    <td class="text-end">{{ number_format($row->total) }}</td>
                                                    <td class="text-end text-success">{{ number_format($row->available) }}</td>
                                                    <td class="text-end text-warning">{{ number_format($row->reserved) }}</td>
                                                    <td class="text-end text-danger">{{ number_format($row->occupied) }}</td>
                                                </tr>
                                            @endforeach
                                            @if ($lotsBySection->isEmpty())
                                                <tr>
                                                    <td colspan="5" class="text-muted text-center py-3">No lots found.</td>
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

            <div class="accordion-item border-0 shadow-sm mb-3" id="paymentsSection">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePayments" aria-expanded="false">
                        Payments
                    </button>
                </h2>
                <div id="collapsePayments" class="accordion-collapse collapse" data-bs-parent="#dashboardSections">
                    <div class="accordion-body">
                        <div class="row g-3">
                            <div class="col-xl-8">
                                    <div class="card border-0 shadow-sm h-100">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                <div class="fw-semibold">Monthly Revenue</div>
                                                <a href="{{ route('admin.reports.payments') }}" class="btn btn-outline-secondary btn-sm">View Report</a>
                                            </div>
                                        <div id="dash_payments_revenue" class="dashboard-chart" style="height: 340px;"><canvas></canvas></div>
                                        </div>
                                    </div>
                                </div>
                            <div class="col-xl-4">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="card border-0 shadow-sm h-100">
                                            <div class="card-body">
                                                <div class="text-muted small text-uppercase fw-semibold">Paid vs Pending</div>
                                                <div class="d-flex justify-content-between mt-2">
                                                    <div>
                                                        <div class="text-muted small">Paid</div>
                                                        <div class="fw-bold text-success">₱{{ number_format($contractsPaid, 2) }}</div>
                                                    </div>
                                                    <div class="text-end">
                                                        <div class="text-muted small">Pending</div>
                                                        <div class="fw-bold text-warning">₱{{ number_format($outstandingBalance, 2) }}</div>
                                                    </div>
                                                </div>
                                                <div class="text-muted small mt-1">Collectible: ₱{{ number_format($contractsCollectible, 2) }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="card border-0 shadow-sm h-100">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center justify-content-between mb-2">
                                                    <div class="fw-semibold">Pending Contracts</div>
                                                    <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary btn-sm">Open Payments</a>
                                                </div>
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-hover align-middle mb-0">
                                                        <thead>
                                                            <tr>
                                                                <th>Client</th>
                                                                <th>Due</th>
                                                                <th class="text-end">Balance</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($pastDueContracts as $contract)
                                                                @php($balance = max(0, (float) ($contract->total_amount ?? 0) - (float) ($contract->amount_paid ?? 0)))
                                                                <tr>
                                                                    <td>
                                                                        <div class="fw-semibold">{{ $contract->client?->full_name ?? 'Unknown' }}</div>
                                                                        <div class="text-muted small">
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
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <div class="fw-semibold">Payment History</div>
                                            <div class="d-flex gap-2 flex-wrap">
                                                <a href="{{ route('admin.reports.payments') }}" class="btn btn-outline-secondary btn-sm">Export (Reports)</a>
                                                <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary btn-sm">Open List</a>
                                            </div>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-hover align-middle mb-0">
                                                <thead>
                                                    <tr>
                                                        <th>Date</th>
                                                        <th>Client</th>
                                                        <th>Method</th>
                                                        <th class="text-end">Amount</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse ($recentPaymentTransactions as $tx)
                                                        <tr>
                                                            <td>{{ $tx->transaction_date?->format('Y-m-d') ?? ($tx->created_at?->format('Y-m-d') ?? '-') }}</td>
                                                            <td>{{ $tx->client?->full_name ?? 'Unknown' }}</td>
                                                            <td class="text-muted">{{ $tx->method ?? '-' }}</td>
                                                            <td class="text-end fw-semibold">₱{{ number_format((float) ($tx->amount ?? 0), 2) }}</td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="4" class="text-muted text-center py-3">No payments yet.</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="accordion-item border-0 shadow-sm mb-3" id="intermentsSection">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseInterments" aria-expanded="false">
                        Interments
                    </button>
                </h2>
                <div id="collapseInterments" class="accordion-collapse collapse" data-bs-parent="#dashboardSections">
                    <div class="accordion-body">
                        <div class="row g-3">
                            <div class="col-xl-4">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <div class="fw-semibold">Upcoming Interments</div>
                                            <a href="{{ route('admin.interments.index') }}" class="btn btn-outline-secondary btn-sm">Open Interments</a>
                                        </div>
                                        <div class="text-muted small mb-2">Timeline (next 8)</div>
                                        <div class="list-group list-group-flush">
                                            @forelse ($upcomingBurials as $record)
                                                <div class="list-group-item px-0">
                                                    <div class="d-flex justify-content-between">
                                                        <div class="fw-semibold">{{ trim($record->first_name.' '.$record->last_name) }}</div>
                                                        <div class="text-muted">{{ $record->burial_date?->format('Y-m-d') ?? '-' }}</div>
                                                    </div>
                                                    <div class="text-muted small">{{ $record->lot ? 'Lot ID '.$record->lot->lot_id : '-' }}</div>
                                                </div>
                                            @empty
                                                <div class="text-muted text-center py-3">No upcoming interments.</div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-8">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="card border-0 shadow-sm h-100">
                                            <div class="card-body">
                                                <div class="text-muted small text-uppercase fw-semibold">Total Interments This Month</div>
                                                <div class="display-6 fw-bold">{{ number_format($intermentsThisMonth) }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card border-0 shadow-sm h-100">
                                            <div class="card-body">
                                                <div class="text-muted small text-uppercase fw-semibold">Total Interments (All Time)</div>
                                                <div class="display-6 fw-bold">{{ number_format($deceasedTotal) }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="card border-0 shadow-sm">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center justify-content-between mb-2">
                                                    <div class="fw-semibold">Interment Records</div>
                                                    <a href="{{ route('admin.interments.index') }}" class="btn btn-outline-secondary btn-sm">Open List</a>
                                                </div>
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
                    </div>
                </div>
            </div>

            <div class="accordion-item border-0 shadow-sm mb-3" id="auditLogs">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseReports" aria-expanded="false">
                        Reports & Audit Logs
                    </button>
                </h2>
                <div id="collapseReports" class="accordion-collapse collapse" data-bs-parent="#dashboardSections">
                    <div class="accordion-body">
                        <div class="d-flex gap-2 flex-wrap mb-3">
                            <a href="{{ route('admin.reports.clients') }}" class="btn btn-outline-secondary btn-sm">Clients Report</a>
                            <a href="{{ route('admin.reports.plots') }}" class="btn btn-outline-secondary btn-sm">Plots Report</a>
                            <a href="{{ route('admin.reports.payments') }}" class="btn btn-outline-secondary btn-sm">Payments Report</a>
                            <a href="{{ route('admin.clients.export.pdf') }}" class="btn btn-outline-secondary btn-sm">Export Clients (PDF)</a>
                            <a href="{{ route('admin.clients.export.csv') }}" class="btn btn-outline-secondary btn-sm">Export Clients (CSV)</a>
                            @if (auth()->check() && auth()->user()->role === 'master_admin')
                                <a href="{{ route('master.auditLogs.index') }}" class="btn btn-outline-secondary btn-sm">Open Full Audit Logs</a>
                            @endif
                        </div>

                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <div class="fw-semibold">Audit Log Summary (Last 10)</div>
                                    <div class="text-muted small">For compliance</div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>User</th>
                                                <th>Event</th>
                                                <th class="text-end">Target</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($recentAuditLogs as $log)
                                                <tr>
                                                    <td>{{ $log->created_at?->format('Y-m-d H:i') ?? '-' }}</td>
                                                    <td>{{ $log->user?->name ?? 'System' }}</td>
                                                    <td class="text-muted">{{ $log->event ?? '-' }}</td>
                                                    <td class="text-end text-muted small">
                                                        {{ class_basename($log->auditable_type ?? '') }}
                                                        {{ $log->auditable_id ? '#'.$log->auditable_id : '' }}
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-muted text-center py-3">No audit logs yet.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .dashboard-kpi-bar { top: 72px; z-index: 9; background: #f5f6f8; padding-top: .25rem; padding-bottom: .25rem; }
    .dashboard-kpi-bar .kpi-card { min-height: 84px; }
    .dashboard-chart { min-height: 320px; position: relative; }
    .dashboard-chart canvas { max-height: 320px; }
    .dashboard-chart-sm { min-height: 200px; position: relative; }
    .dashboard-chart-sm canvas { max-height: 200px; }
    .chart-container { position: relative; width: 100%; height: 100%; }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var chartInstances = [];

    Chart.defaults.color = '#6b7280';
    Chart.defaults.borderColor = '#e5e7eb';
    Chart.defaults.font.family = 'system-ui, -apple-system, sans-serif';

    var growthEl = document.getElementById('dash_clients_growth');
    if (growthEl) {
        var growthCtx = growthEl.querySelector('canvas').getContext('2d');
        var growthChart = new Chart(growthCtx, {
            type: 'line',
            data: {
                labels: @json($growthMonths),
                datasets: [{
                    label: 'New Clients',
                    data: @json($growthCounts),
                    borderColor: '#374151',
                    backgroundColor: 'rgba(55, 65, 81, 0.1)',
                    fill: true,
                    tension: 0.3,
                    pointRadius: 4,
                    pointBackgroundColor: '#374151',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
        chartInstances.push(growthChart);
    }

    var retentionEl = document.getElementById('dash_clients_retention');
    if (retentionEl) {
        var rate = Number(@json($retentionRate));
        rate = isNaN(rate) ? 0 : Math.max(0, Math.min(100, rate));
        var retentionCtx = retentionEl.querySelector('canvas').getContext('2d');
        var retentionChart = new Chart(retentionCtx, {
            type: 'doughnut',
            data: {
                labels: ['Retained', 'Other'],
                datasets: [{
                    data: [rate, 100 - rate],
                    backgroundColor: ['#374151', '#e5e7eb'],
                    borderWidth: 0,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                cutout: '65%',
            }
        });
        chartInstances.push(retentionChart);
    }

    var plotsEl = document.getElementById('dash_plots_status');
    if (plotsEl) {
        var plotsCtx = plotsEl.querySelector('canvas').getContext('2d');
        var plotsChart = new Chart(plotsCtx, {
            type: 'doughnut',
            data: {
                labels: ['Available', 'Reserved', 'Occupied'],
                datasets: [{
                    data: [@json((int) $lotsAvailable), @json((int) $lotsReserved), @json((int) $lotsOccupied)],
                    backgroundColor: ['#22c55e', '#f59e0b', '#374151'],
                    borderWidth: 0,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                },
                cutout: '55%',
            }
        });
        chartInstances.push(plotsChart);
    }

    var revenueEl = document.getElementById('dash_payments_revenue');
    if (revenueEl) {
        var revenueCtx = revenueEl.querySelector('canvas').getContext('2d');
        var revenueChart = new Chart(revenueCtx, {
            type: 'bar',
            data: {
                labels: @json($paymentMonths),
                datasets: [
                    {
                        label: 'Installment Payments',
                        data: @json($paymentRevenue),
                        backgroundColor: '#374151',
                    },
                    {
                        label: 'Interment Payments',
                        data: @json($intermentRevenue),
                        backgroundColor: '#9ca3af',
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                },
                scales: {
                    y: { 
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '₱' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
        chartInstances.push(revenueChart);
    }
});
</script>
@endpush
