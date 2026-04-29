@extends('home.home_master')

@section('home')
  <section class="liliwmemoria-page-hero liliwmemoria-hero-bg">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-10">
          <div class="liliwmemoria-page-hero__content text-center" data-aos="fade-up" data-aos-duration="700">
            <a href="{{ route('services.page') }}" class="liliwmemoria-service-back mb-3">
              <span aria-hidden="true">&larr;</span> Back to Services
            </a>
            <h1 class="liliwmemoria-page-hero__title">MAUSOLEUM</h1>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="lonyo-section-padding">
    <div class="container">
      <div class="row g-5 justify-content-center">
        <div class="col-lg-7" data-aos="fade-up" data-aos-duration="700">
          <figure class="liliwmemoria-service-detail__media">
            <img
              src="{{ asset('frontend/assets/images/service/mausoleum2.jpg') }}"
              alt="Mausoleum - Protected and dignified resting place designed for lasting respect and privacy at Liliw Memoria"
              class="liliwmemoria-service-detail__img"
              loading="lazy"
            >
            <figcaption class="liliwmemoria-service-detail__img-caption">Mausoleum - Dignified resting place with lasting protection and privacy</figcaption>
          </figure>
        </div>

        <div class="col-lg-5" data-aos="fade-up" data-aos-duration="700">
          <div class="liliwmemoria-service-detail__content">
            <div class="liliwmemoria-service-detail__kicker">Service Details</div>
            <h2 class="liliwmemoria-service-detail__title">MAUSOLEUM</h2>
            <p class="liliwmemoria-service-detail__text">Mausoleum Lots offer a distinguished memorial space with contemporary architecture and enduring materials. Featuring clean lines, 
              integrated religious symbols, and serene landscaping, these structures provide families with a prestigious and lasting tribute.</p>

            <p class="liliwmemoria-service-detail__text">Our Mausoleum option provides a protected, dignified resting place designed for lasting respect, security, and family privacy.</p>

            <div class="liliwmemoria-service-detail__note">
              <i class="fas fa-info-circle"></i> For updated availability and current rates, submit an inquiry and we'll respond shortly.
            </div>
          </div>
        </div>
      </div>

      <div class="row mt-5 justify-content-center">
        <div class="col-12" data-aos="fade-up" data-aos-duration="700">
          <div class="liliwmemoria-service-detail__form-wrapper">
            <h3 class="liliwmemoria-service-detail__form-title">Inquire Now</h3>
            <p class="liliwmemoria-service-detail__form-subtitle">Fill out the form below and we will contact you shortly.</p>

            <form method="POST" action="{{ route('contact.inquiry.submit') }}" class="liliwmemoria-service-detail__form-inner">
              @csrf

              <input type="hidden" name="subject" value="MAUSOLEUM">
              <input type="hidden" name="reason" value="reservation_inquiry">

              <div class="row g-3">
                <div class="col-md-6">
                  <input class="form-control" type="text" name="first_name" value="{{ old('first_name') }}" placeholder="First Name" required autocomplete="given-name">
                </div>

                <div class="col-md-6">
                  <input class="form-control" type="text" name="last_name" value="{{ old('last_name') }}" placeholder="Last Name" required autocomplete="family-name">
                </div>

                <div class="col-md-6">
                  <input class="form-control" type="email" name="email" value="{{ old('email') }}" placeholder="Email" required autocomplete="email">
                </div>

                <div class="col-md-6">
                  <input class="form-control" type="tel" name="phone" value="{{ old('phone') }}" placeholder="Contact Number" required autocomplete="tel">
                </div>

                <div class="col-12">
                  <textarea class="form-control" name="message" rows="5" placeholder="Message" required>{{ old('message') }}</textarea>
                </div>

                <div class="col-12">
                  <p class="form-text">Subject will be sent as <strong>MAUSOLEUM</strong>.</p>
                </div>
              </div>

              <div class="liliwmemoria-service-detail__consent">
                <label class="liliwmemoria-inquiry-checkbox" for="service-inquiry-consent">
                  <input
                    id="service-inquiry-consent"
                    type="checkbox"
                    name="consent"
                    value="1"
                    {{ old('consent') ? 'checked' : '' }}
                    required
                  >
                  <span>
                    I agree to the collection and use of my personal information in accordance with the
                    <a href="{{ route('privacy.policy') }}" target="_blank" rel="noopener">privacy policy</a>.
                  </span>
                </label>
              </div>

              <div class="liliwmemoria-service-detail__form-actions">
                <button class="lonyo-default-btn hero-btn liliwmemoria-service-detail__cta" type="submit">Submit Inquiry</button>
              </div>

            </form>
          </div>
        </div>
      </div>
    </div>
  </section>
@endsection

@push('styles')
  <style>
    .liliwmemoria-service-back {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      font-size: 0.9rem;
      font-weight: 700;
      color: rgba(255, 255, 255, 0.9);
      text-decoration: none;
      letter-spacing: 0.02em;
      transition: gap 0.2s ease;
    }

    .liliwmemoria-service-back:hover {
      color: #fff;
      gap: 12px;
    }

    .liliwmemoria-service-detail__media {
      border-radius: 16px;
      overflow: hidden;
      position: relative;
      background: #0f2613;
      box-shadow: 0 16px 40px rgba(17, 24, 39, 0.14);
    }

    .liliwmemoria-service-detail__img {
      width: 100%;
      height: auto;
      display: block;
      object-fit: cover;
      transition: transform 0.3s ease;
    }

    .liliwmemoria-service-detail__media:hover .liliwmemoria-service-detail__img {
      transform: scale(1.02);
    }

    .liliwmemoria-service-detail__img-caption {
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      background: linear-gradient(transparent, rgba(15, 38, 19, 0.8));
      color: #fff;
      padding: 40px 18px 14px;
      font-size: 0.85rem;
      font-weight: 600;
      letter-spacing: 0.02em;
    }

    .liliwmemoria-service-detail__content {
      padding: 20px 0;
    }

    .liliwmemoria-service-detail__kicker {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      font-size: 0.78rem;
      font-weight: 800;
      letter-spacing: 0.08em;
      text-transform: uppercase;
      color: rgba(20, 44, 20, 0.8);
      margin-bottom: 8px;
    }

    .liliwmemoria-service-detail__title {
      font-weight: 900;
      letter-spacing: -0.02em;
      color: #0f2613;
      margin: 0 0 16px;
      font-size: clamp(28px, 3.5vw, 48px);
      line-height: 1.1;
    }

    .liliwmemoria-service-detail__text {
      margin: 0 0 20px;
      color: #374151;
      line-height: 1.8;
      font-size: 1.05rem;
    }

    .liliwmemoria-service-detail__note {
      font-size: 0.95rem;
      color: rgba(55, 65, 81, 0.9);
      line-height: 1.6;
      padding: 12px 16px;
      background: rgba(20, 44, 20, 0.04);
      border-radius: 10px;
      border-left: 3px solid #0f2613;
    }

    .liliwmemoria-service-detail__note i {
      margin-right: 6px;
      color: #0f2613;
    }

    .liliwmemoria-service-detail__form-wrapper {
      border: 1px solid rgba(17, 24, 39, 0.1);
      border-radius: 20px;
      background: #fff;
      padding: 32px;
      box-shadow: 0 16px 40px rgba(17, 24, 39, 0.08);
    }

    .liliwmemoria-service-detail__form-title {
      margin: 0 0 6px;
      font-size: 1.35rem;
      font-weight: 900;
      color: #0f2613;
      letter-spacing: -0.01em;
      text-align: center;
    }

    .liliwmemoria-service-detail__form-subtitle {
      margin: 0 0 20px;
      color: rgba(55, 65, 81, 0.85);
      font-size: 1rem;
      text-align: center;
    }

    .liliwmemoria-service-detail__form-inner .form-control {
      border-radius: 12px;
      padding: 12px 14px;
      border-color: rgba(17, 24, 39, 0.14);
      transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .liliwmemoria-service-detail__form-inner .form-control:focus {
      border-color: rgba(20, 44, 20, 0.55);
      box-shadow: 0 0 0 0.25rem rgba(20, 44, 20, 0.12);
    }

    .liliwmemoria-service-detail__consent {
      margin-top: 14px;
      text-align: center;
    }

    .liliwmemoria-service-detail__form-actions {
      margin-top: 18px;
      text-align: center;
    }

    .liliwmemoria-service-detail__cta {
      background: #142c14;
      border-radius: 12px;
      padding: 14px 28px;
      font-weight: 900;
      letter-spacing: 0.03em;
      font-size: 1rem;
      min-width: 200px;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .liliwmemoria-service-detail__cta:hover {
      transform: translateY(-1px);
      box-shadow: 0 12px 24px rgba(20, 44, 20, 0.2);
    }

    .liliwmemoria-service-detail__form-note {
      margin-top: 12px;
      font-size: 0.9rem;
      color: rgba(55, 65, 81, 0.9);
      text-align: center;
    }

    @media (max-width: 991.98px) {
      .liliwmemoria-service-detail__content {
        padding: 20px 0 0;
      }
    }

    @media (max-width: 575.98px) {
      .liliwmemoria-service-detail__form-wrapper {
        padding: 24px 20px;
      }

      .liliwmemoria-service-detail__cta {
        width: 100%;
        min-width: 0;
      }
    }
  </style>
@endpush
