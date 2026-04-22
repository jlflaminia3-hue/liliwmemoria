<div class="row">
    <div class="col-12">
        <div class="card mb-0">
            <div class="card-body">
                <h5 class="card-title mb-3">Communication Logs</h5>

                <form method="POST" action="{{ route('admin.clients.communications.store', $client) }}" class="row g-2 mb-3">
                    @csrf
                    <div class="col-md-2">
                        <select name="channel" class="form-select" required>
                            <option value="phone" @selected(old('channel') === 'phone')>Phone</option>
                            <option value="email" @selected(old('channel') === 'email')>Email</option>
                            <option value="sms" @selected(old('channel') === 'sms')>SMS</option>
                            <option value="in_person" @selected(old('channel') === 'in_person')>In Person</option>
                            <option value="other" @selected(old('channel', 'other') === 'other')>Other</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="subject" class="form-control" value="{{ old('subject') }}" placeholder="Subject (optional)">
                    </div>
                    <div class="col-md-3">
                        <input type="datetime-local" name="occurred_at" class="form-control" value="{{ old('occurred_at') }}">
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-success w-100">Add Log</button>
                    </div>
                    <div class="col-12">
                        <textarea name="message" class="form-control" rows="2" placeholder="Message / notes" required>{{ old('message') }}</textarea>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th style="width: 180px;">When</th>
                                <th style="width: 120px;">Channel</th>
                                <th style="width: 200px;">Subject</th>
                                <th>Message</th>
                                <th style="width: 140px;">By</th>
                                <th style="width: 80px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($client->communications->sortByDesc('occurred_at') as $comm)
                                <tr>
                                    <td>{{ $comm->occurred_at?->format('Y-m-d H:i') ?? $comm->created_at?->format('Y-m-d H:i') }}</td>
                                    <td>{{ $comm->channel }}</td>
                                    <td>{{ $comm->subject ?? '-' }}</td>
                                    <td>{{ \Illuminate\Support\Str::limit($comm->message, 180) }}</td>
                                    <td>{{ $comm->creator?->name ?? '-' }}</td>
                                </tr>
                            @endforeach
                            @if ($client->communications->isEmpty())
                                <tr>
                                    <td colspan="6" class="text-muted text-center py-3">No communication logs yet.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

