@php
    $record = $interment ?? null;
@endphp

<input type="hidden" name="interment_fee" value="{{ $record?->interment_fee ?? 15000 }}">

<div class="row g-3">
    <div class="col-md-6">
        <label for="{{ $idPrefix }}first_name" class="form-label fw-semibold">First Name</label>
        <input
            type="text"
            id="{{ $idPrefix }}first_name"
            name="first_name"
            class="form-control"
            value="{{ old('first_name', $record?->first_name) }}"
            required
        >
    </div>
    <div class="col-md-6">
        <label for="{{ $idPrefix }}last_name" class="form-label fw-semibold">Last Name</label>
        <input
            type="text"
            id="{{ $idPrefix }}last_name"
            name="last_name"
            class="form-control"
            value="{{ old('last_name', $record?->last_name) }}"
            required
        >
    </div>
    <div class="col-md-6">
        <label for="{{ $idPrefix }}client_id" class="form-label fw-semibold">Associated Client</label>
        <div class="js-client-picker position-relative">
            <input
                type="text"
                class="form-control js-client-picker-input"
                placeholder="Search and select a client"
                autocomplete="off"
                role="combobox"
                aria-expanded="false"
                aria-haspopup="listbox"
            >
            <select id="{{ $idPrefix }}client_id" name="client_id" class="form-select js-client-picker-select d-none">
                <option value="">Select a client</option>
                @foreach ($clients as $client)
                    <option value="{{ $client->id }}" @selected((string) old('client_id', $record?->client_id) === (string) $client->id)>
                        {{ $client->full_name }}
                    </option>
                @endforeach
            </select>
            <div class="dropdown-menu w-100 mt-1 js-client-picker-menu" style="max-height: 260px; overflow:auto;"></div>
            <div class="form-text">Click the field to open the list, then type to filter.</div>
        </div>
    </div>
    <div class="col-md-6">
        <label for="{{ $idPrefix }}lot_id" class="form-label fw-semibold">Lot</label>
        <div class="js-lot-picker position-relative">
            <input
                type="text"
                class="form-control js-lot-picker-input"
                placeholder="Search and select a lot"
                autocomplete="off"
                role="combobox"
                aria-expanded="false"
                aria-haspopup="listbox"
            >
            <select id="{{ $idPrefix }}lot_id" name="lot_id" class="form-select js-lot-picker-select d-none" required>
                <option value="">Select a lot</option>
            </select>
            <input type="hidden" class="js-lot-picker-initial-value" value="{{ old('lot_id', $record?->lot_id) }}">
            <div class="dropdown-menu w-100 mt-1 js-lot-picker-menu" style="max-height: 260px; overflow:auto;"></div>
            <div class="form-text">Select a client first, then choose from their lots.</div>
            <div class="invalid-feedback" id="{{ $idPrefix }}lot_eligibility_feedback"></div>
        </div>
    </div>
    <div class="col-12">
        <div class="alert alert-info py-2 px-3 small mb-0" id="{{ $idPrefix }}lot_eligibility_info" style="display: none;">
            <i data-feather="info" class="me-1" style="height: 14px; width: 14px;"></i>
            <span id="{{ $idPrefix }}lot_eligibility_text"></span>
        </div>
    </div>
    <div class="col-md-4">
        <label for="{{ $idPrefix }}status" class="form-label fw-semibold">Status</label>
        <select id="{{ $idPrefix }}status" name="status" class="form-select js-interment-status" required>
            <option value="pending" @selected(old('status', $record?->status ?? 'pending') === 'pending')>Pending</option>
            <option value="confirmed" @selected(old('status', $record?->status) === 'confirmed')>Confirmed</option>
            <option value="exhumed" @selected(old('status', $record?->status) === 'exhumed')>Exhumed</option>
        </select>
    </div>
    <div class="col-md-4">
        <label for="{{ $idPrefix }}burial_date" class="form-label fw-semibold">Interment Date</label>
        <input
            type="date"
            id="{{ $idPrefix }}burial_date"
            name="burial_date"
            class="form-control js-burial-date"
            value="{{ old('burial_date', $record?->burial_date?->format('Y-m-d')) }}"
        >
    </div>
    <div class="col-md-4">
        <label for="{{ $idPrefix }}date_of_death" class="form-label fw-semibold">Date of Death</label>
        <input
            type="date"
            id="{{ $idPrefix }}date_of_death"
            name="date_of_death"
            class="form-control"
            value="{{ old('date_of_death', $record?->date_of_death?->format('Y-m-d')) }}"
        >
    </div>
    <div class="col-md-4">
        <label for="{{ $idPrefix }}date_of_birth" class="form-label fw-semibold">Date of Birth</label>
        <input
            type="date"
            id="{{ $idPrefix }}date_of_birth"
            name="date_of_birth"
            class="form-control"
            value="{{ old('date_of_birth', $record?->date_of_birth?->format('Y-m-d')) }}"
        >
    </div>
    <div class="col-md-4">
        <label for="{{ $idPrefix }}death_certificate" class="form-label fw-semibold">Death Certificate</label>
        <input type="file" id="{{ $idPrefix }}death_certificate" name="death_certificate" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
        @if ($record?->death_certificate_path)
            <div class="form-text">Current file available for download.</div>
        @endif
    </div>
    <div class="col-md-4">
        <label for="{{ $idPrefix }}burial_permit" class="form-label fw-semibold">Burial Permit</label>
        <input
            type="file"
            id="{{ $idPrefix }}burial_permit"
            name="burial_permit"
            class="form-control js-burial-permit"
            accept=".pdf,.jpg,.jpeg,.png"
            data-has-existing="{{ $record?->burial_permit_path ? '1' : '' }}"
        >
        @if ($record?->burial_permit_path)
            <div class="form-text">Current file available for download.</div>
        @endif
    </div>

    <div class="col-md-6">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="{{ $idPrefix }}excavation_scheduled" name="excavation_scheduled" value="1" @checked(old('excavation_scheduled', $record?->excavation_scheduled))>
            <label class="form-check-label" for="{{ $idPrefix }}excavation_scheduled">
                Excavation Scheduled
            </label>
        </div>
    </div>

    <div class="col-md-6">
        <label for="{{ $idPrefix }}excavation_date" class="form-label fw-semibold">Excavation Date</label>
        <input
            type="date"
            id="{{ $idPrefix }}excavation_date"
            name="excavation_date"
            class="form-control"
            value="{{ old('excavation_date', $record?->excavation_date?->format('Y-m-d')) }}"
        >
    </div>

    <div class="col-12">
        <label for="{{ $idPrefix }}interment_form" class="form-label fw-semibold">Other Supporting Document</label>
        <input type="file" id="{{ $idPrefix }}interment_form" name="interment_form" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
        @if ($record?->interment_form_path)
            <div class="form-text">Current file available for download.</div>
        @endif
    </div>
    <div class="col-12">
        <label for="{{ $idPrefix }}notes" class="form-label fw-semibold">Notes</label>
        <textarea id="{{ $idPrefix }}notes" name="notes" class="form-control" rows="3">{{ old('notes', $record?->notes) }}</textarea>
    </div>
</div>
