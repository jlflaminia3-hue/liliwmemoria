@props([
    'id' => 'panel',
    'title' => 'Panel',
    'icon' => 'box',
    'expanded' => true,
    'badge' => null,
    'badgeClass' => 'bg-secondary',
])

<div class="accordion-item border-0 shadow-sm mb-3">
    <h2 class="accordion-header" id="heading{{ Str::studly($id) }}">
        <button class="accordion-button {{ !$expanded ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ Str::studly($id) }}" aria-expanded="{{ $expanded ? 'true' : 'false' }}">
            <i data-feather="{{ $icon }}" class="me-2" style="height: 16px; width: 16px;"></i>
            <span class="me-auto">{{ $title }}</span>
            @if($badge)
                <span class="badge {{ $badgeClass }} ms-2">{{ $badge }}</span>
            @endif
        </button>
    </h2>
    <div id="collapse{{ Str::studly($id) }}" class="accordion-collapse collapse {{ $expanded ? 'show' : '' }}" data-bs-parent="#{{ $id }}Parent">
        <div class="accordion-body">
            {{ $slot }}
        </div>
    </div>
</div>