<section class="lonyo-cta-section bg-heading light-bg liliwmemoria-hero-bg">
  <div class="container">
    <div class="row justify-content-center text-center">
      <div class="col-lg-11">
        <p class="text liliwmemoria-hero-contact__subtitle">
          We would be honoured to help you celebrate the life of your loved ones and provide them with a serene final
          resting place.
        </p>
        <p class="text liliwmemoria-hero-contact__subtitle">
          Contact us by calling <a class="liliwmemoria-hero-contact__link" href="tel:+1770124245493">(780) 424-5493</a>
          or by filling out the form below, and let's discuss how we can best honour their wishes.
        </p>
      </div>
    </div>

    <div class="row justify-content-center">
      <div class="col-12">
        @if (session('status'))
          <div class="liliwmemoria-hero-contact__alert alert alert-success" role="alert">
            {{ session('status') }}
          </div>
        @endif

        @if ($errors->any())
          <div class="liliwmemoria-hero-contact__alert alert alert-danger" role="alert">
            Please check the form and try again.
          </div>
        @endif

        <form class="liliwmemoria-hero-contact" method="POST" action="{{ route('contact.submit') }}">
          @csrf
          <div class="row g-3">
            <div class="col-lg-4">
              <div class="liliwmemoria-hero-contact__group">
                <div class="input-group">
                  <span class="input-group-text"><i class="ri-user-line"></i></span>
                  <input class="form-control" type="text" name="first_name" value="{{ old('first_name') }}"
                    placeholder="Name *" required autocomplete="given-name">
                </div>
              </div>

              <div class="liliwmemoria-hero-contact__group">
                <div class="input-group">
                  <span class="input-group-text"><i class="ri-mail-line"></i></span>
                  <input class="form-control" type="email" name="email" value="{{ old('email') }}" placeholder="Email *"
                    required autocomplete="email">
                </div>
              </div>

              <div class="liliwmemoria-hero-contact__group">
                <div class="input-group">
                  <span class="input-group-text"><i class="ri-question-line"></i></span>
                  <select class="form-select" name="reason" required>
                    <option value="" disabled {{ old('reason') ? '' : 'selected' }}>How can we help you?</option>
                    <option value="question" {{ old('reason') === 'question' ? 'selected' : '' }}>Question</option>
                    <option value="burial_plots" {{ old('reason') === 'burial_plots' ? 'selected' : '' }}>Burial Plots
                    </option>
                    <option value="mausoleum" {{ old('reason') === 'mausoleum' ? 'selected' : '' }}>Mausoleum</option>
                  </select>
                </div>
              </div>

                <div class="text-center liliwmemoria-hero-contact__actions">
                    <button class="liliwmemoria-hero-contact__btn" type="submit">SUBMIT MESSAGE</button>
                </div>


            </div>

            <div class="col-lg-8">
              <div class="liliwmemoria-hero-contact__group liliwmemoria-hero-contact__message">
                <div class="input-group">
                  <span class="input-group-text"><i class="ri-pencil-line"></i></span>
                  <textarea class="form-control" name="message" rows="10" placeholder="Message" required>{{ old('message') }}</textarea>
                </div>
              </div>
            </div>
          </div>

        </form>
      </div>
    </div>
  </div>
</section>