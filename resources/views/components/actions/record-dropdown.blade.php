@props([
    'record' => null,
    'type' => 'client',
    'showArchive' => true,
    'showDeactivate' => true,
    'showCancel' => false,
    'showRestore' => false,
])

@php
    $canArchive = $showArchive && $record && $record->status !== 'archived';
    $canDeactivate = $showDeactivate && $record && $record->status === 'active';
    $canCancel = $showCancel && $record && in_array($record->status, ['active', 'inactive']);
    $canRestore = $showRestore && $record && in_array($record->status, ['inactive', 'archived', 'cancelled']);
    $hasActions = $canArchive || $canDeactivate || $canCancel || $canRestore;
@endphp

@if ($hasActions)
    @if ($canArchive)
        <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('archive-form-{{ $record->id }}').submit();">
            <i data-feather="archive" class="me-2" style="height: 14px; width: 14px;"></i>
            Archive
        </a>
        <form id="archive-form-{{ $record->id }}" action="{{ route('admin.' . $type . '.archive', $record) }}" method="POST" class="d-none">
            @csrf
            @method('PATCH')
        </form>
    @endif

    @if ($canDeactivate)
        <a class="dropdown-item text-secondary" href="#" onclick="event.preventDefault(); document.getElementById('deactivate-form-{{ $record->id }}').submit();">
            <i data-feather="x-circle" class="me-2" style="height: 14px; width: 14px;"></i>
            Deactivate
        </a>
        <form id="deactivate-form-{{ $record->id }}" action="{{ route('admin.' . $type . '.deactivate', $record) }}" method="POST" class="d-none">
            @csrf
            @method('PATCH')
        </form>
    @endif

    @if ($canCancel)
        <a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); if(confirm('Cancel this record? This action will be logged for audit purposes.')) { document.getElementById('cancel-form-{{ $record->id }}').submit(); }">
            <i data-feather="slash" class="me-2" style="height: 14px; width: 14px;"></i>
            Cancel
        </a>
        <form id="cancel-form-{{ $record->id }}" action="{{ route('admin.' . $type . '.cancel', $record) }}" method="POST" class="d-none">
            @csrf
            @method('PATCH')
        </form>
    @endif

    @if ($canRestore)
        <a class="dropdown-item text-success" href="#" onclick="event.preventDefault(); document.getElementById('restore-form-{{ $record->id }}').submit();">
            <i data-feather="refresh-cw" class="me-2" style="height: 14px; width: 14px;"></i>
            Restore
        </a>
        <form id="restore-form-{{ $record->id }}" action="{{ route('admin.' . $type . '.restore', $record) }}" method="POST" class="d-none">
            @csrf
            @method('PATCH')
        </form>
    @endif
@endif