@props([
    'status' => 'active',
    'size' => 'sm',
])

@php
    $badgeClass = match ($status) {
        'active' => 'bg-success',
        'inactive' => 'bg-secondary',
        'archived' => 'bg-warning text-dark',
        'cancelled' => 'bg-danger',
        default => 'bg-secondary',
    };

    $label = match ($status) {
        'active' => 'Active',
        'inactive' => 'Inactive',
        'archived' => 'Archived',
        'cancelled' => 'Cancelled',
        default => ucfirst($status),
    };

    $sizeClass = match ($size) {
        'xs' => 'badge-xs py-1 px-2 fs-10',
        'sm' => 'badge-sm py-1 px-2 fs-11',
        'md' => 'badge-md py-1 px-3 fs-12',
        default => 'badge-sm py-1 px-2 fs-11',
    };
@endphp

<span class="badge {{ $badgeClass }} {{ $sizeClass }}">
    {{ $label }}
</span>