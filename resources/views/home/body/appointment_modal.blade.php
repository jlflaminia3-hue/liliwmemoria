<div class="modal fade liliwmemoria-inquiry-modal" id="appointmentModal" tabindex="-1" aria-labelledby="appointmentModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <div>
          <h2 class="modal-title" id="appointmentModalLabel">Book an Appointment</h2>
          <p>Fill out the form below to schedule your visit.</p>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        @if (session('appointment_status'))
          <div class="alert alert-success" role="alert">
            {{ session('appointment_status') }}
          </div>
        @endif

        @if ($errors->appointment->any())
          <div class="alert alert-danger" role="alert">
            Please check the form and try again.
          </div>
        @endif

        <form method="POST" action="{{ route('appointment.store') }}" class="liliwmemoria-inquiry-form">
          @csrf

          <div class="row g-3 liliwmemoria-inquiry-grid">
            <div class="col-md-6">
              <input class="form-control" type="text" name="first_name" value="{{ old('first_name') }}" placeholder="First Name" required autocomplete="given-name">
            </div>

            <div class="col-md-6">
              <input class="form-control" type="text" name="last_name" value="{{ old('last_name') }}" placeholder="Last Name" required autocomplete="family-name">
            </div>

            <div class="col-md-6 col-lg-4">
              <input class="form-control" type="email" name="email" value="{{ old('email') }}" placeholder="Email" required autocomplete="email">
            </div>

            <div class="col-md-6 col-lg-4">
              <input class="form-control" type="tel" name="phone" value="{{ old('phone') }}" placeholder="Contact Number" required autocomplete="tel">
            </div>

            <div class="col-md-6 col-lg-4">
              <div class="liliwmemoria-inquiry-select-wrap">
                <select class="form-select liliwmemoria-inquiry-select" name="appointment_type" required aria-label="Appointment Type">
                  <option value="" disabled {{ old('appointment_type') ? '' : 'selected' }}>Select Appointment Type</option>
                  <option value="reservation_inquiry" {{ old('appointment_type') === 'reservation_inquiry' ? 'selected' : '' }}>Reservation Inquiry</option>
                  <option value="lot_viewing" {{ old('appointment_type') === 'lot_viewing' ? 'selected' : '' }}>Lot Viewing</option>
                  <option value="contract_signing" {{ old('appointment_type') === 'contract_signing' ? 'selected' : '' }}>Contract Signing</option>
                  <option value="interment_consultation" {{ old('appointment_type') === 'interment_consultation' ? 'selected' : '' }}>Interment Consultation</option>
                  <option value="payment_arrangement" {{ old('appointment_type') === 'payment_arrangement' ? 'selected' : '' }}>Payment Arrangement</option>
                  <option value="document_submission" {{ old('appointment_type') === 'document_submission' ? 'selected' : '' }}>Document Submission</option>
                  <option value="maintenance_service" {{ old('appointment_type') === 'maintenance_service' ? 'selected' : '' }}>Maintenance Service</option>
                  <option value="other" {{ old('appointment_type') === 'other' ? 'selected' : '' }}>Other</option>
                </select>
                <span class="liliwmemoria-inquiry-select-icon" aria-hidden="true">
                  <i class="ri-arrow-down-s-line"></i>
                </span>
              </div>
            </div>

            <div class="col-md-6 col-lg-4">
              <input class="form-control" type="date" name="appointment_date" value="{{ old('appointment_date') }}" placeholder="Select Date" required min="{{ date('Y-m-d') }}">
            </div>

            <div class="col-md-6 col-lg-4">
              <input class="form-control" type="time" name="appointment_time" value="{{ old('appointment_time') }}" placeholder="Select Time" required>
            </div>

            <div class="col-12">
              <input class="form-control" type="text" name="subject" value="{{ old('subject') }}" placeholder="Subject (Optional)">
            </div>

            <div class="col-12">
              <textarea class="form-control" name="message" rows="5" placeholder="Message (Optional)">{{ old('message') }}</textarea>
            </div>
          </div>

          <p class="liliwmemoria-inquiry-note">
            Our office hours are Monday to Saturday, 8:00 AM to 5:00 PM. We will confirm your appointment via email or phone.
          </p>

          <div class="liliwmemoria-inquiry-consent">
            <label class="liliwmemoria-inquiry-checkbox" for="appointment-consent">
              <input id="appointment-consent" type="checkbox" name="consent" value="1" {{ old('consent') ? 'checked' : '' }} required>
              <span>I agree to the collection and use of my personal information in accordance with the <a href="{{ route('privacy.policy') }}" target="_blank" rel="noopener">privacy policy</a>.</span>
            </label>
          </div>

          <div class="liliwmemoria-inquiry-actions">
            <button class="liliwmemoria-btn-solid" type="submit">SUBMIT APPOINTMENT</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
