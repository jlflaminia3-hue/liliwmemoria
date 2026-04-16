<div class="modal fade liliwmemoria-inquiry-modal" id="inquiryModal" tabindex="-1" aria-labelledby="inquiryModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <div>
          <h2 class="modal-title" id="inquiryModalLabel">Inquire</h2>
          <p>Fill out the form below, and we will be in touch shortly.</p>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        @if (session('inquiry_status'))
          <div class="alert alert-success" role="alert">
            {{ session('inquiry_status') }}
          </div>
        @endif

        @if ($errors->inquiry->any())
          <div class="alert alert-danger" role="alert">
            Please check the form and try again.
          </div>
        @endif

        <form method="POST" action="{{ route('contact.inquiry.submit') }}" class="liliwmemoria-inquiry-form">
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

            <div class="col-md-12 col-lg-4">
              <div class="liliwmemoria-inquiry-select-wrap">
                <select class="form-select liliwmemoria-inquiry-select" name="reason" required aria-label="Inquiry Type">
                  <option value="" disabled {{ old('reason') ? '' : 'selected' }}>Select Inquiry Type</option>
                  <option value="reservation_inquiry" {{ old('reason') === 'reservation_inquiry' ? 'selected' : '' }}>Reservation Inquiry</option>
                  <option value="contract_agreement" {{ old('reason') === 'contract_agreement' ? 'selected' : '' }}>Contract Aggreement</option>
                  <option value="proof_of_payment" {{ old('reason') === 'proof_of_payment' ? 'selected' : '' }}>Proof of Payment</option>
                  <option value="burial_permit" {{ old('reason') === 'burial_permit' ? 'selected' : '' }}>Burial Permit</option>
                  <option value="maintenance_aggreement" {{ old('reason') === 'maintenance_aggreement' ? 'selected' : '' }}>Maintenance Aggreement</option>
                  <option value="interment" {{ old('reason') === 'interment' ? 'selected' : '' }}>Interment</option>
                  <option value="billing_inquiry" {{ old('reason') === 'billing_inquiry' ? 'selected' : '' }}>Billing Inquiry</option>
                </select>
                <span class="liliwmemoria-inquiry-select-icon" aria-hidden="true">
                  <i class="ri-arrow-down-s-line"></i>
                </span>
              </div>
            </div>

            <div class="col-12">
              <input class="form-control" type="text" name="subject" value="{{ old('subject') }}" placeholder="Subject" required>
            </div>

            <div class="col-12">
              <textarea class="form-control" name="message" rows="8" placeholder="Message" required>{{ old('message') }}</textarea>
            </div>
          </div>

          <p class="liliwmemoria-inquiry-note">
            Note: For billing inquiries, please indicate your CONTRACT NUMBER in your message.
          </p>

          <div class="liliwmemoria-inquiry-consent">
            {{-- <p><strong>Consent</strong> <span>(Required)</span></p> --}}
            <label class="liliwmemoria-inquiry-checkbox" for="inquiry-consent">
              <input id="inquiry-consent" type="checkbox" name="consent" value="1" {{ old('consent') ? 'checked' : '' }} required>
              <span>I agree to the collection and use of my personal information in accordance with the <a href="{{ route('privacy.policy') }}" target="_blank" rel="noopener">privacy policy</a>.</span>
            </label>
          </div>

          <div class="liliwmemoria-inquiry-actions">
            <button class="liliwmemoria-btn-solid" type="submit">SUBMIT</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
