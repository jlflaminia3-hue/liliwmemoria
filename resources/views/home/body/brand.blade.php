@php($logoPath = 'frontend/assets/images/logo/liliwmemoria-logo.png')
@php($logoExists = file_exists(public_path($logoPath)))

<a href="{{ url('/') }}" class="liliwmemoria-brand">
    @if ($logoExists)
        <img
            src="{{ asset($logoPath) }}"
            alt="LiliwMemoria logo"
            width="28"
            height="28"
            style="object-fit: contain;"
        >
    @else
        <span class="text-primary d-inline-flex align-items-center" aria-hidden="true">
            <svg viewBox="0 0 64 64" width="28" height="28" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 56H52" stroke="currentColor" stroke-width="4" stroke-linecap="round" />
                <path d="M22 56V29C22 20.163 29.163 13 38 13C46.837 13 54 20.163 54 29V56" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                <path d="M38 22V40" stroke="currentColor" stroke-width="4" stroke-linecap="round" />
                <path d="M30 30H46" stroke="currentColor" stroke-width="4" stroke-linecap="round" />
            </svg>
        </span>
    @endif

    <span class="liliwmemoria-brand__text text-primary">
        LiliwMemoria
    </span>
</a>
