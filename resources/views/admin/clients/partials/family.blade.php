<div class="row">
    <div class="col-12">
        <div class="card mb-0">
            <div class="card-body">
                <h5 class="card-title mb-3">Family Links</h5>

                <form method="POST" action="{{ route('admin.clients.familyLinks.store', $client) }}" class="row g-2 mb-3">
                    @csrf
                    <div class="col-md-5">
                        <select name="related_client_id" class="form-select" required>
                            <option value="">Select client...</option>
                            @foreach ($otherClients as $other)
                                <option value="{{ $other->id }}" @selected(old('related_client_id') == $other->id)>
                                    {{ trim($other->first_name.' '.$other->last_name) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="relationship" class="form-control" value="{{ old('relationship') }}" placeholder="Relationship (e.g. spouse)">
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="notes" class="form-control" value="{{ old('notes') }}" placeholder="Notes (optional)">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-success">Add Link</button>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Related Client</th>
                                <th>Relationship</th>
                                <th>Notes</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($familyLinks as $link)
                                @php
                                    $other = $link->client_id === $client->id ? $link->relatedClient : $link->client;
                                @endphp
                                <tr>
                                    <td>{{ $other?->full_name ?? '-' }}</td>
                                    <td>{{ $link->relationship ?? '-' }}</td>
                                    <td>{{ $link->notes ?? '-' }}</td>
                                    <td class="text-end">
                                        <form method="POST" action="{{ route('admin.clients.familyLinks.destroy', [$client, $link]) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Remove this family link?')">Remove</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            @if ($familyLinks->isEmpty())
                                <tr>
                                    <td colspan="4" class="text-muted text-center py-3">No family links yet.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

