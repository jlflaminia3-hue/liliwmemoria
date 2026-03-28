<div class="row g-3">
    <div class="col-lg-8">
        <div class="card mb-0">
            <div class="card-body">
                <h5 class="card-title mb-3">Personal Information</h5>

                <div class="row">
                    <div class="col-sm-6 mb-2"><strong>Email:</strong> {{ $client->email ?? '-' }}</div>
                    <div class="col-sm-6 mb-2"><strong>Phone:</strong> {{ $client->phone ?? '-' }}</div>
                    <div class="col-12 mb-2">
                        <strong>Address:</strong>
                        @php
                            $addressParts = array_filter([
                                $client->address_line1,
                                $client->address_line2,
                                $client->barangay,
                                $client->city,
                                $client->province,
                                $client->postal_code,
                                $client->country,
                            ]);
                        @endphp
                        {{ !empty($addressParts) ? implode(', ', $addressParts) : '-' }}
                    </div>
                    <div class="col-12">
                        <strong>Notes:</strong> {{ $client->notes ?? '-' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card mb-0">
            <div class="card-body">
                <h6 class="card-title mb-2">Quick Actions</h6>
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.clients.edit', $client) }}" class="btn btn-outline-primary">Edit Client</a>
                    <a href="{{ route('admin.clients.index') }}" class="btn btn-outline-secondary">All Clients</a>
                </div>
            </div>
        </div>
    </div>
</div>

