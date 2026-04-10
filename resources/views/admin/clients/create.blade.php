@extends('admin.admin_master')

@section('admin')
<div class="row">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Add Client</h4>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @php($repopulate = $errors->any())

                <form method="POST" action="{{ route('admin.clients.store') }}" autocomplete="off">
                    @csrf

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">First Name</label>
                            <input type="text" name="first_name" class="form-control" value="{{ $repopulate ? old('first_name') : '' }}" autocomplete="off" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Last Name</label>
                            <input type="text" name="last_name" class="form-control" value="{{ $repopulate ? old('last_name') : '' }}" autocomplete="off" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ $repopulate ? old('email') : '' }}" autocomplete="off">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control" value="{{ $repopulate ? old('phone') : '' }}" autocomplete="off">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Address Line 1</label>
                        <input type="text" name="address_line1" class="form-control" value="{{ $repopulate ? old('address_line1') : '' }}" autocomplete="off">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address Line 2</label>
                        <input type="text" name="address_line2" class="form-control" value="{{ $repopulate ? old('address_line2') : '' }}" autocomplete="off">
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Barangay</label>
                            <input type="text" name="barangay" class="form-control" value="{{ $repopulate ? old('barangay') : '' }}" autocomplete="off">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">City</label>
                            <input type="text" name="city" class="form-control" value="{{ $repopulate ? old('city') : '' }}" autocomplete="off">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Province</label>
                            <input type="text" name="province" class="form-control" value="{{ $repopulate ? old('province') : '' }}" autocomplete="off">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Postal Code</label>
                            <input type="text" name="postal_code" class="form-control" value="{{ $repopulate ? old('postal_code') : '' }}" autocomplete="off">
                        </div>
                        <div class="col-md-8 mb-3">
                            <label class="form-label">Country</label>
                            <input type="text" name="country" class="form-control" value="{{ $repopulate ? old('country') : '' }}" autocomplete="off">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="3" autocomplete="off">{{ $repopulate ? old('notes') : '' }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Save Client</button>
                    <a href="{{ route('admin.clients.index') }}" class="btn btn-secondary">Cancel</a>
                </form>

                @if (!$errors->any())
                    <script>
                        (function () {
                            window.addEventListener('pageshow', function (event) {
                                if (!event.persisted) return;
                                var form = document.querySelector('form[action="{{ route('admin.clients.store') }}"]');
                                if (!form) return;
                                form.reset();
                            });
                        })();
                    </script>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
