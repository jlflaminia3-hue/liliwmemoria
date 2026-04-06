<section class="lonyo-cta-section bg-heading light-bg liliwmemoria-hero-bg">
  <div class="container">
    <div class="row justify-content-center text-center">
      <div class="col-lg-11">
        <p class="text liliwmemoria-hero-contact__subtitle liliwmemoria-hero-contact__intro">
          <span class="liliwmemoria-hero-contact__intro-icon" aria-hidden="true">
            <i class="ri-heart-line"></i>
          </span>
          <span>
            We’re honored to help you remember your loved ones and provide a peaceful resting place. 
            {{-- It would be our privilege to help you honor the memory of your loved ones and offer them a peaceful place to rest. --}}
          </span>
        </p>
        <p class="text liliwmemoria-hero-contact__subtitle liliwmemoria-hero-contact__intro">
          <i class="ri-customer-service-2-line"></i>
          <span class="liliwmemoria-hero-contact__intro-icon" aria-hidden="true">
          </span>
          <span>
            Simply fill out the form below, and we’ll discuss how best to honor their wishes.
            {{-- You can reach us
            <a class="liliwmemoria-hero-contact__link" href="tel:+1770124245493">(780) 424-5493</a>
            by simply fill out the form below, and together we'll talk about how to best honor your loved one's wishes. --}}
          </span>
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
                  <span class="input-group-text"><i class="fas fa-user"></i></span>
                  <input class="form-control" type="text" name="first_name" value="{{ old('first_name') }}"
                    placeholder="Name" required autocomplete="given-name">
                </div>
              </div>

              <div class="liliwmemoria-hero-contact__group">
                <div class="input-group">
                  <span class="input-group-text"><i class="ri-mail-fill"></i></span>
                  <input class="form-control" type="email" name="email" value="{{ old('email') }}" placeholder="Email"
                    required autocomplete="email">
                </div>
              </div>

              <div class="liliwmemoria-hero-contact__group">
                <div class="input-group">
                  <span class="input-group-text"><i class="ri-phone-line"></i></span>
                  <input class="form-control" type="tel" name="phone" value="{{ old('phone') }}" placeholder="Phone"
                    required autocomplete="tel">
                </div>
              </div>

              <div class="liliwmemoria-hero-contact__group">
                <div class="input-group liliwmemoria-hero-contact__select-group">
                  <span class="input-group-text"><i class="fas fa-question-circle"></i></span>
                  <select class="form-select" id="hero-contact-reason" name="reason" required aria-label="How can we help you?">
                    <option value="" disabled hidden {{ old('reason') ? '' : 'selected' }}>How can we help you?</option>
                    <option value="question" {{ old('reason') === 'question' ? 'selected' : '' }}>Question</option>
                    <option value="burial_plots" {{ old('reason') === 'burial_plots' ? 'selected' : '' }}>Burial Plots</option>
                    <option value="mausoleum" {{ old('reason') === 'mausoleum' ? 'selected' : '' }}>Mausoleum</option>
                  </select>
                  <span class="liliwmemoria-hero-contact__select-icon" aria-hidden="true">
                  </span>
                </div>
              </div>

              <div class="text-center liliwmemoria-hero-contact__actions">
                <button class="liliwmemoria-hero-contact__btn" type="submit">SUBMIT MESSAGE</button>
              </div>
            </div>

            <div class="col-lg-8">
              <div class="liliwmemoria-hero-contact__group liliwmemoria-hero-contact__message">
                <div class="input-group">
                  <span class="input-group-text"><i class="fas fa-comment-alt"></i></span>
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
