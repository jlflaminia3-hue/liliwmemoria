@extends('admin.admin_master')
@section('admin')

<div class="content">
    <div class="container-xxl">
        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">Audit Logs</h4>
                <div class="text-muted mt-1">See created / updated / deleted records with actor + timestamp</div>
            </div>
            <div class="mt-3 mt-sm-0 d-flex gap-2 flex-wrap">
                <a href="{{ route('master.dashboard') }}" class="btn btn-outline-secondary btn-sm">Master Dashboard</a>
                <a href="{{ route('master.users.index') }}" class="btn btn-outline-secondary btn-sm">Users</a>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Search</label>
                        <input name="q" value="{{ request('q') }}" class="form-control" placeholder="User, model, url, id">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Event</label>
                        <select name="event" class="form-select">
                            <option value="">All</option>
                            @foreach (['created', 'updated', 'deleted'] as $event)
                                <option value="{{ $event }}" @selected(request('event') === $event)>{{ ucfirst($event) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Model</label>
                        <select name="model" class="form-select">
                            <option value="">All</option>
                            @foreach ($availableModels as $model)
                                <option value="{{ $model['value'] }}" @selected(request('model') === $model['value'])>{{ $model['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">User ID</label>
                        <input name="user_id" value="{{ request('user_id') }}" class="form-control" placeholder="e.g. 1">
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button class="btn btn-primary w-100" type="submit">Filter</button>
                        <a class="btn btn-outline-secondary w-100" href="{{ route('master.auditLogs.index') }}">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body p-0">
                @include('master.partials.audit-table', ['auditLogs' => $auditLogs])
            </div>
        </div>
    </div>
</div>

@endsection

