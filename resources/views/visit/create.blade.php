@extends('home.home_master')

@section('home')
<style>
    .visit-page { padding-top: 80px !important; }
    .visit-container { max-width: 860px; margin: 0 auto; }
    .visit-card { border-radius: 16px; border: 1px solid #eef0f3; }
    .visit-title { font-weight: 700; letter-spacing: -.02em; }
    .visit-subtitle { font-size: .95rem; color: #6c757d; }
    .visit-step { display: inline-flex; align-items: center; gap: .45rem; padding: .3rem .6rem; border-radius: 999px; background: #f8f9fa; border: 1px solid #e9ecef; font-size: .85rem; color: #495057; }
    .visit-step .dot { width: 8px; height: 8px; border-radius: 999px; background: #0d6efd; display: inline-block; }
    .visit-field { background: #fff; border-radius: 12px; border: 1px solid #eef0f3; padding: 10px; }
    .visit-field .form-label { font-weight: 600; margin-bottom: .3rem; font-size: .92rem; color: #212529; }
    .visit-field .form-control,
    .visit-field .form-select { font-size: .95rem; padding: .4rem .65rem; border-radius: 10px; }
    .visit-help { font-size: .85rem; color: #6c757d; }
    .deceased-search-wrap { position: relative; }
    .deceased-results { position: absolute; top: calc(100% + 8px); left: 0; right: 0; z-index: 1000; max-height: 220px; overflow: auto; border-radius: 12px; }
    .deceased-results .list-group-item { cursor: pointer; }
    .selected-chip { display: inline-flex; align-items: center; gap: .5rem; border-radius: 999px; padding: .35rem .65rem; background: #eef6ff; border: 1px solid #cfe5ff; }
    .selected-chip .x { border: 0; background: transparent; font-weight: 700; color: #0d6efd; padding: 0 2px; }

    #visitSubmitBtn { color: #fff !important; background: #0f2f16 !important; border-color: #0f2f16 !important; }
    #visitSubmitBtn:hover, #visitSubmitBtn:focus { color: #0f2f16 !important; background: #0a1f0f; border-color: #0a1f0f; }
    #visitSubmitBtn { font-size: .95rem; padding: .55rem 1rem; border-radius: 12px; }

    @media (min-width: 992px) {
        .visit-page { padding-top: 70px !important; }
        .visit-field { padding: 12px; }
        .visit-field .form-control, .visit-field .form-select { padding-top: .45rem; padding-bottom: .45rem; }
    }
</style>

<section class="visit-page lonyo-hero-section light-bg liliwmemoria-hero-bg">
    <div class="container-fluid px-3 px-md-4 px-lg-3">
        <div class="visit-container">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10">
                <div class="card shadow-sm visit-card">
                    <div class="card-body p-3 p-md-4 p-lg-4">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                            <div>
                                <h2 class="mb-1 visit-title">Visitor Log</h2>
                                <div class="visit-subtitle">Scan QR &rarr; fill up &rarr; get a tomb locator guide.</div>
                            </div>
                            <div class="d-flex flex-wrap gap-2">
                                <span class="visit-step"><span class="dot"></span>Step 1: Details</span>
                                <span class="visit-step"><span class="dot"></span>Step 2: Select Deceased</span>
                                <span class="visit-step"><span class="dot"></span>Step 3: Locator</span>
                            </div>
                        </div>

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <div class="fw-semibold mb-1">Please fix the following:</div>
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('public.visit.store') }}">
                            @csrf

                            <div class="row g-3">
                                <div class="col-12 col-md-6 col-lg-3">
                                    <div class="visit-field h-100">
                                        <label class="form-label">Your Name</label>
                                        <input type="text" name="visitor_name" class="form-control" value="{{ old('visitor_name') }}" required autocomplete="name" placeholder="Full name">
                                        {{-- <div class="visit-help mt-1">Please use your real name for the visitor record.</div> --}}
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-lg-3">
                                    <div class="visit-field h-100">
                                        <label class="form-label">Contact Number</label>
                                        <input type="text" name="contact_number" class="form-control" value="{{ old('contact_number') }}" autocomplete="tel" placeholder="09xx xxx xxxx">
                                        {{-- <div class="visit-help mt-1">For safety and office assistance if needed.</div> --}}
                                    </div>
                                </div>
                                <div class="col-12 col-md-7 col-lg-3">
                                    <div class="visit-field h-100">
                                        <label class="form-label">Address</label>
                                        <input type="text" name="address" class="form-control" value="{{ old('address') }}" autocomplete="street-address" placeholder="Brgy. / City">
                                    </div>
                                </div>
                                <div class="col-12 col-md-5 col-lg-3">
                                    <div class="visit-field h-100">
                                        <label class="form-label">Purpose</label>
                                        <input type="text" name="purpose" class="form-control" placeholder="Visit" value="{{ old('purpose') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="visit-field mt-3 mb-3">
                                <label class="form-label">Who are you visiting?</label>

                                <input type="hidden" name="deceased_id" id="deceased_id" value="{{ old('deceased_id') }}">

                                <div class="deceased-search-wrap">
                                    <input type="text" id="deceased_search" class="form-control" placeholder="Type last name / first name / lot (e.g. Dela Cruz, Juan or P1-12)">
                                    <div id="deceased_results" class="deceased-results list-group shadow-sm d-none"></div>
                                </div>

                                <div id="deceased_selected" class="mt-2 d-none"></div>

                                {{-- <div class="visit-help mt-1">
                                    If you can&rsquo;t find the name, please ask the admin office for assistance.
                                </div> --}}

                                <noscript>
                                    <div class="mt-3">
                                        <select name="deceased_id" class="form-select" required>
                                            <option value="" disabled selected>Select...</option>
                                            @foreach ($deceased as $person)
                                                <option value="{{ $person->id }}">
                                                    {{ $person->last_name }}, {{ $person->first_name }}
                                                    @if ($person->lot)
                                                        (Lot: {{ $person->lot->lot_id }})
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </noscript>
                            </div>

                            <div class="d-flex gap-2 flex-wrap mt-2">
                                <button type="submit" class="lonyo-default-btn" id="visitSubmitBtn">CONTINUE TO LOCATOR</button>
                                <a href="{{ route('public.map') }}" class="btn btn-sm btn-outline-secondary">Open Map</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var deceased = {!! json_encode($deceasedIndex ?? [], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!};

    var searchInput = document.getElementById('deceased_search');
    var resultsEl = document.getElementById('deceased_results');
    var hiddenId = document.getElementById('deceased_id');
    var selectedEl = document.getElementById('deceased_selected');
    var submitBtn = document.getElementById('visitSubmitBtn');

    function normalize(str) {
        return String(str || '').toLowerCase().replace(/\s+/g, ' ').trim();
    }

    function hideResults() {
        resultsEl.classList.add('d-none');
        resultsEl.innerHTML = '';
    }

    function setSelected(item) {
        hiddenId.value = String(item.id);
        searchInput.value = item.name + (item.lot ? (' (Lot ' + item.lot + ')') : '');

        selectedEl.classList.remove('d-none');
        selectedEl.innerHTML = '';

        var chip = document.createElement('div');
        chip.className = 'selected-chip';
        chip.innerHTML = '<span class="fw-semibold">Selected:</span><span>' + item.name + (item.lot ? (' • Lot ' + item.lot) : '') + '</span>';

        var clearBtn = document.createElement('button');
        clearBtn.type = 'button';
        clearBtn.className = 'x';
        clearBtn.setAttribute('aria-label', 'Clear selection');
        clearBtn.textContent = '×';
        clearBtn.addEventListener('click', function () {
            hiddenId.value = '';
            searchInput.value = '';
            selectedEl.classList.add('d-none');
            selectedEl.innerHTML = '';
            searchInput.focus();
        });

        chip.appendChild(clearBtn);
        selectedEl.appendChild(chip);
    }

    function renderResults(items) {
        resultsEl.innerHTML = '';
        if (!items.length) return hideResults();
        resultsEl.classList.remove('d-none');

        items.slice(0, 12).forEach(function (item) {
            var a = document.createElement('a');
            a.className = 'list-group-item list-group-item-action';
            a.innerHTML = '<div class="fw-semibold">' + item.name + '</div>'
                + '<div class="text-muted small">' + (item.lot ? ('Lot ' + item.lot) : 'Lot not set') + '</div>';
            a.addEventListener('click', function () {
                setSelected(item);
                hideResults();
            });
            resultsEl.appendChild(a);
        });
    }

    // Restore selected (after validation errors)
    if (hiddenId.value) {
        var existing = deceased.find(function (d) { return String(d.id) === String(hiddenId.value); });
        if (existing) setSelected(existing);
    }

    searchInput.addEventListener('input', function () {
        var q = normalize(searchInput.value);
        if (q.length < 2) return hideResults();
        var matches = deceased.filter(function (d) {
            return normalize(d.name).includes(q) || normalize(d.lot).includes(q);
        });
        renderResults(matches);
    });

    searchInput.addEventListener('focus', function () {
        var q = normalize(searchInput.value);
        if (q.length < 2) return;
        var matches = deceased.filter(function (d) {
            return normalize(d.name).includes(q) || normalize(d.lot).includes(q);
        });
        renderResults(matches);
    });

    document.addEventListener('click', function (e) {
        if (!resultsEl.contains(e.target) && e.target !== searchInput) hideResults();
    });

    // Prevent submit without a selection (since hidden input isn't "required")
    submitBtn.closest('form').addEventListener('submit', function (e) {
        if (!hiddenId.value) {
            e.preventDefault();
            searchInput.focus();
            renderResults(deceased.slice(0, 12));
        }
    });
});
</script>
@endsection
