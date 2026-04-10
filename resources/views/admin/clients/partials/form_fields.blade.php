@php($idPrefix = $idPrefix ?? '')
@php($client = $client ?? null)

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label" for="{{ $idPrefix }}first_name">First Name</label>
        <input id="{{ $idPrefix }}first_name" type="text" name="first_name" class="form-control" value="{{ old('first_name', $client->first_name ?? '') }}" autocomplete="off" required>
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label" for="{{ $idPrefix }}last_name">Last Name</label>
        <input id="{{ $idPrefix }}last_name" type="text" name="last_name" class="form-control" value="{{ old('last_name', $client->last_name ?? '') }}" autocomplete="off" required>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label" for="{{ $idPrefix }}email">Email</label>
        <input id="{{ $idPrefix }}email" type="email" name="email" class="form-control" value="{{ old('email', $client->email ?? '') }}" autocomplete="off">
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label" for="{{ $idPrefix }}phone">Phone</label>
        <input id="{{ $idPrefix }}phone" type="text" name="phone" class="form-control" value="{{ old('phone', $client->phone ?? '') }}" autocomplete="off">
    </div>
</div>

<div class="mb-3">
    <label class="form-label" for="{{ $idPrefix }}address_line1">Address Line 1</label>
    <input id="{{ $idPrefix }}address_line1" type="text" name="address_line1" class="form-control" value="{{ old('address_line1', $client->address_line1 ?? '') }}" autocomplete="off">
</div>
<div class="mb-3">
    <label class="form-label" for="{{ $idPrefix }}address_line2">Address Line 2</label>
    <input id="{{ $idPrefix }}address_line2" type="text" name="address_line2" class="form-control" value="{{ old('address_line2', $client->address_line2 ?? '') }}" autocomplete="off">
</div>

<div class="row">
    <div class="col-md-4 mb-3">
        <label class="form-label" for="{{ $idPrefix }}barangay">Barangay</label>
        <input id="{{ $idPrefix }}barangay" type="text" name="barangay" class="form-control" value="{{ old('barangay', $client->barangay ?? '') }}" autocomplete="off">
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label" for="{{ $idPrefix }}city">City</label>
        <input id="{{ $idPrefix }}city" type="text" name="city" class="form-control" value="{{ old('city', $client->city ?? '') }}" autocomplete="off">
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label" for="{{ $idPrefix }}province">Province</label>
        <input id="{{ $idPrefix }}province" type="text" name="province" class="form-control" value="{{ old('province', $client->province ?? '') }}" autocomplete="off">
    </div>
</div>

<div class="row">
    <div class="col-md-4 mb-3">
        <label class="form-label" for="{{ $idPrefix }}postal_code">Postal Code</label>
        <input id="{{ $idPrefix }}postal_code" type="text" name="postal_code" class="form-control" value="{{ old('postal_code', $client->postal_code ?? '') }}" autocomplete="off">
    </div>
    <div class="col-md-8 mb-3">
        <label class="form-label" for="{{ $idPrefix }}country">Country</label>
        <input id="{{ $idPrefix }}country" type="text" name="country" class="form-control" value="{{ old('country', $client->country ?? '') }}" autocomplete="off">
    </div>
</div>

<div class="mb-3">
    <label class="form-label" for="{{ $idPrefix }}notes">Notes</label>
    <textarea id="{{ $idPrefix }}notes" name="notes" class="form-control" rows="3" autocomplete="off">{{ old('notes', $client->notes ?? '') }}</textarea>
</div>
