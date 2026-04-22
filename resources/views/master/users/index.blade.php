@extends('admin.admin_master')
@section('admin')

<div class="content">
    <div class="container-xxl">
        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">Users</h4>
                <div class="text-muted mt-1">Master admin can edit roles and accounts</div>
            </div>
            <div class="mt-3 mt-sm-0 d-flex gap-2 flex-wrap">
                <a href="{{ route('master.dashboard') }}" class="btn btn-outline-secondary btn-sm">Master Dashboard</a>
                <a href="{{ route('master.auditLogs.index') }}" class="btn btn-outline-secondary btn-sm">Audit Logs</a>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" class="row g-2 align-items-end">
                    <div class="col-md-6">
                        <label class="form-label">Search</label>
                        <input name="q" value="{{ request('q') }}" class="form-control" placeholder="Name or email">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-select">
                            <option value="">All</option>
                            @foreach (['user' => 'User', 'admin' => 'Admin', 'master_admin' => 'Master Admin'] as $value => $label)
                                <option value="{{ $value }}" @selected(request('role') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button class="btn btn-primary w-100" type="submit">Filter</button>
                        <a class="btn btn-outline-secondary w-100" href="{{ route('master.users.index') }}">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th style="width: 140px;">Role</th>
                            <th style="width: 160px;">Created</th>
                            <th style="width: 160px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr>
                                <td class="fw-semibold">{{ $user->name }}</td>
                                <td class="text-muted">{{ $user->email }}</td>
                                <td>
                                    <span class="badge bg-{{ $user->role === 'master_admin' ? 'primary' : ($user->role === 'admin' ? 'info' : 'secondary') }}">
                                        {{ strtoupper($user->role) }}
                                    </span>
                                </td>
                                <td class="text-muted">{{ optional($user->created_at)->format('Y-m-d H:i') }}</td>
                                <td class="d-flex gap-2">
                                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('master.users.edit', $user) }}">Edit</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted p-4">No users found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-between align-items-center p-3">
                <div class="text-muted small">
                    Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() }} results
                </div>
                <div class="d-flex align-items-center gap-2">
                    @if ($users->onFirstPage())
                        <span class="btn btn-sm btn-outline-secondary disabled">Previous</span>
                    @else
                        <a class="btn btn-sm btn-outline-secondary" href="{{ $users->previousPageUrl() }}">Previous</a>
                    @endif
                    <span class="text-muted">Page {{ $users->currentPage() }} of {{ $users->lastPage() }}</span>
                    @if ($users->hasMorePages())
                        <a class="btn btn-sm btn-outline-secondary" href="{{ $users->nextPageUrl() }}">Next</a>
                    @else
                        <span class="btn btn-sm btn-outline-secondary disabled">Next</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

