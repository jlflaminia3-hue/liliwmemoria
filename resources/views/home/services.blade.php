@extends('home.home_master')

@section('home')
  <section class="liliwmemoria-page-hero liliwmemoria-hero-bg">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-10">
          <div class="liliwmemoria-page-hero__content text-center" data-aos="fade-up" data-aos-duration="700">
            <h1 class="liliwmemoria-page-hero__title">Services</h1>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="lonyo-section-padding pt-0">
    <div class="container">
      <div class="liliwmemoria-services-grid__title text-center" data-aos="fade-up" data-aos-duration="700">
        <span class="liliwmemoria-services-grid__rule" aria-hidden="true"></span>
      </div>

      @php
        $services = [
          [
            'id' => 'narra-lots',
            'title' => 'NARRA LOTS',
            'img' => 'frontend/assets/images/service/narra.jpg',
            'text' => 'A serene and well-kept resting place option with a dignified layout for family visits.',
            'details' => 'Our Narra Lots are located in a quiet area of the memorial park and maintained regularly to ensure a clean and dignified setting for remembrance.',
            'price' => 'Price: Please inquire for current rates.',
            'reason' => 'reservation_inquiry',
          ],
          [
            'id' => 'garden-lots',
            'title' => 'GARDEN LOTS',
            'img' => 'frontend/assets/images/service/garden-lot.jpg',
            'text' => 'Peaceful garden settings that support quiet reflection, remembrance, and comfort.',
            'details' => 'Garden Lots offer a calm, landscaped environment ideal for families who value a serene space for visits, reflection, and ongoing care.',
            'price' => 'Price: Please inquire for current rates.',
            'reason' => 'reservation_inquiry',
          ],
          [
            'id' => 'back-office-lots',
            'title' => 'BACK OFFICE LOTS',
            'img' => 'frontend/assets/images/service/backoffice.jpg',
            'text' => 'Convenient lot options close to service areas while maintaining a calm memorial environment.',
            'details' => 'Back Office Lots provide a practical option with convenient access while still offering a peaceful, well-maintained memorial setting.',
            'price' => 'Price: Please inquire for current rates.',
            'reason' => 'reservation_inquiry',
          ],
          [
            'id' => 'mausoleum',
            'title' => 'MAUSOLEUM',
            'img' => 'frontend/assets/images/service/mausoleum.jpg',
            'text' => 'A dignified space for interment designed for lasting protection, privacy, and respect.',
            'details' => 'Our Mausoleum option provides a protected, dignified resting place designed for lasting respect, security, and family privacy.',
            'price' => 'Price: Please inquire for current rates.',
            'reason' => 'reservation_inquiry',
          ],
        ];

        $otherServices = [
          [
            'id' => 'interment',
            'title' => 'INTERMENT',
            'img' => 'frontend/assets/images/service/phases.jpg',
            'text' => 'Professional interment assistance handled with respect, care, and proper coordination.',
            'details' => 'We assist families through the interment process with clear guidance, scheduling support, and on-site coordination to ensure a dignified service.',
            'price' => 'Price: Please inquire for current rates.',
            'reason' => 'interment',
          ],
          [
            'id' => 'exhumation',
            'title' => 'EXHUMATION',
            'img' => 'frontend/assets/images/service/narra2.jpg',
            'text' => 'Careful exhumation services following required procedures and respectful handling.',
            'details' => 'Our team provides support for documentation needs and careful handling throughout the exhumation process, in accordance with applicable requirements.',
            'price' => 'Price: Please inquire for current rates.',
            'reason' => 'reservation_inquiry',
          ],
        ];
      @endphp

      <div class="row g-4 mt-4 justify-content-center">
        @foreach ($services as $service)
          <div class="col-lg-6 col-md-10">
            <article
              id="{{ $service['id'] }}"
              class="liliwmemoria-service-tile"
              style="--service-image: url('{{ asset($service['img']) }}')"
              data-aos="fade-up"
              data-aos-duration="700"
            >
              <div class="liliwmemoria-service-tile__content">
                <h3 class="liliwmemoria-service-tile__title">{{ $service['title'] }}</h3>
                <span class="liliwmemoria-service-tile__accent" aria-hidden="true"></span>
                <p class="liliwmemoria-service-tile__text">{{ $service['text'] }}</p>
                <button
                  type="button"
                  class="liliwmemoria-service-tile__btn"
                  data-bs-toggle="modal"
                  data-bs-target="#serviceModal-{{ $service['id'] }}"
                >
                  VIEW DETAILS
                  <span aria-hidden="true">&rarr;</span>
                </button>
              </div>
            </article>
          </div>
        @endforeach
      </div>
    </div>
  </section>

  <section class="lonyo-section-padding pt-0">
    <div class="container">
      <div class="liliwmemoria-services-grid__title text-center" data-aos="fade-up" data-aos-duration="700">
        <h2 class="liliwmemoria-services-grid__heading">OTHER SERVICES</h2>
        <span class="liliwmemoria-services-grid__rule" aria-hidden="true"></span>
      </div>

      <div class="row g-4 mt-4 justify-content-center">
        @foreach ($otherServices as $service)
          <div class="col-lg-6 col-md-10">
            <article
              id="{{ $service['id'] }}"
              class="liliwmemoria-service-tile"
              style="--service-image: url('{{ asset($service['img']) }}')"
              data-aos="fade-up"
              data-aos-duration="700"
            >
              <div class="liliwmemoria-service-tile__content">
                <h3 class="liliwmemoria-service-tile__title">{{ $service['title'] }}</h3>
                <span class="liliwmemoria-service-tile__accent" aria-hidden="true"></span>
                <p class="liliwmemoria-service-tile__text">{{ $service['text'] }}</p>
                <button
                  type="button"
                  class="liliwmemoria-service-tile__btn"
                  data-bs-toggle="modal"
                  data-bs-target="#serviceModal-{{ $service['id'] }}"
                >
                  VIEW DETAILS
                  <span aria-hidden="true">&rarr;</span>
                </button>
              </div>
            </article>
          </div>
        @endforeach
      </div>
    </div>
  </section>

  @foreach (array_merge($services, $otherServices) as $service)
    <div
      class="modal fade"
      id="serviceModal-{{ $service['id'] }}"
      tabindex="-1"
      aria-labelledby="serviceModalLabel-{{ $service['id'] }}"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content liliwmemoria-service-modal">
          <div class="modal-header">
            <div>
              <div class="liliwmemoria-service-modal__kicker">Service</div>
              <h2 class="modal-title" id="serviceModalLabel-{{ $service['id'] }}">{{ $service['title'] }}</h2>
            </div>
            <button type="button" class="btn-close liliwmemoria-service-modal__close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="liliwmemoria-service-modal__media">
              <img class="liliwmemoria-service-modal__img" src="{{ asset($service['img']) }}" alt="{{ $service['title'] }}">
            </div>

            <div class="liliwmemoria-service-modal__body">
              <p class="liliwmemoria-service-modal__text">{{ $service['details'] }}</p>

              <div class="liliwmemoria-service-modal__meta">
                <div class="liliwmemoria-service-modal__meta-label">Price</div>
                <div class="liliwmemoria-service-modal__meta-value">{{ $service['price'] }}</div>
              </div>

              <div class="liliwmemoria-service-modal__note">
                For updated availability and current rates, tap <strong>Inquire Now</strong> and we'll respond shortly.
              </div>
            </div>
          </div>
          <div class="liliwmemoria-service-modal__divider" aria-hidden="true"></div>
          <div class="liliwmemoria-service-modal__form">
            <h3 class="liliwmemoria-service-modal__form-title">Inquire Now</h3>
            <p class="liliwmemoria-service-modal__form-subtitle">Fill out the form below and we will contact you shortly.</p>

            <form method="POST" action="{{ route('contact.inquiry.submit') }}" class="liliwmemoria-service-modal__form-inner">
              @csrf

              <input type="hidden" name="subject" value="{{ $service['title'] }}">
              <input type="hidden" name="reason" value="{{ $service['reason'] ?? 'reservation_inquiry' }}">

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
              </div>

              <div class="liliwmemoria-service-modal__consent">
                <label class="liliwmemoria-inquiry-checkbox" for="service-inquiry-consent-{{ $service['id'] }}">
                  <input
                    id="service-inquiry-consent-{{ $service['id'] }}"
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

              <div class="liliwmemoria-service-modal__form-actions">
                <button type="button" class="liliwmemoria-service-modal__secondary" data-bs-dismiss="modal">Close</button>
                <button class="lonyo-default-btn hero-btn liliwmemoria-service-modal__cta" type="submit">Submit</button>
              </div>

              <div class="liliwmemoria-service-modal__form-note">
                Subject will be sent as <strong>{{ $service['title'] }}</strong>.
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  @endforeach
@endsection

@push('styles')
  <style>
    .liliwmemoria-services-grid__heading {
      font-size: 2.25rem;
      font-weight: 800;
      letter-spacing: -0.02em;
      color: #142c14;
      margin-bottom: 0.5rem;
    }

    .liliwmemoria-services-grid__rule {
      display: inline-block;
      width: 130px;
      height: 2px;
      background: rgba(160, 230, 170, 0.95);
      border-radius: 999px;
    }

    .liliwmemoria-service-tile {
      position: relative;
      border-radius: 10px;
      overflow: hidden;
      min-height: 320px;
      background-image: var(--service-image);
      background-size: cover;
      background-position: center;
      box-shadow: 0 16px 32px rgba(17, 24, 39, 0.08);
    }

    .liliwmemoria-service-tile::before {
      content: '';
      position: absolute;
      inset: 0;
      background: rgba(20, 44, 20, 0.55);
    }

    .liliwmemoria-service-tile__content {
      position: relative;
      z-index: 1;
      height: 100%;
      padding: 44px 44px 40px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      gap: 14px;
      color: #fff;
    }

    .liliwmemoria-service-tile__title {
      margin: 0;
      font-size: 2.15rem;
      font-weight: 700;
      letter-spacing: 0.02em;
      color: #fff;
    }

    .liliwmemoria-service-tile__accent {
      width: 44px;
      height: 2px;
      background: rgba(160, 230, 170, 0.95);
      border-radius: 999px;
    }

    .liliwmemoria-service-tile__text {
      margin: 0;
      max-width: 44ch;
      font-size: 1rem;
      line-height: 1.6;
      color: rgba(255, 255, 255, 0.9);
    }

    .liliwmemoria-service-tile__btn {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      width: fit-content;
      margin-top: 8px;
      padding: 10px 18px;
      border-radius: 0;
      background: #7fe39c;
      color: #fff;
      text-decoration: none;
      font-weight: 800;
      font-size: 0.85rem;
      letter-spacing: 0.06em;
      transition: transform 0.2s ease, filter 0.2s ease;
      border: 0;
    }

    .liliwmemoria-service-tile__btn:hover {
      color: #fff;
      filter: brightness(0.95);
      transform: translateY(-1px);
    }

    .liliwmemoria-service-modal .modal-body {
      padding: 18px;
    }

    .liliwmemoria-service-modal .modal-header {
      padding: 18px 18px 10px;
      border-bottom: 1px solid rgba(17, 24, 39, 0.08);
      align-items: flex-start;
    }

    .liliwmemoria-service-modal .modal-title {
      font-weight: 900;
      letter-spacing: -0.02em;
      color: #0f2613;
      margin: 0;
      font-size: clamp(26px, 3.2vw, 44px);
      line-height: 1.05;
    }

    .liliwmemoria-service-modal__kicker {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      font-size: 0.78rem;
      font-weight: 800;
      letter-spacing: 0.08em;
      text-transform: uppercase;
      color: rgba(20, 44, 20, 0.8);
      margin-bottom: 6px;
    }

    .liliwmemoria-service-modal__close {
      margin-top: 4px;
    }

    .liliwmemoria-service-modal {
      border: 0;
      border-radius: 16px;
      overflow: hidden;
      box-shadow: 0 22px 60px rgba(17, 24, 39, 0.18);
      background: #fff;
    }

    .liliwmemoria-service-modal__media {
      border-radius: 14px;
      overflow: hidden;
      position: relative;
      aspect-ratio: 16 / 10;
      background: #0f2613;
      box-shadow: 0 12px 26px rgba(17, 24, 39, 0.12);
    }

    .liliwmemoria-service-modal__media::after {
      content: '';
      position: absolute;
      inset: 0;
      background: linear-gradient(180deg, rgba(20, 44, 20, 0.12) 0%, rgba(20, 44, 20, 0.46) 100%);
      pointer-events: none;
    }

    .liliwmemoria-service-modal__img {
      width: 100%;
      height: 100%;
      display: block;
      object-fit: cover;
      transform: scale(1.01);
    }

    .liliwmemoria-service-modal__body {
      padding: 16px 2px 2px;
    }

    .liliwmemoria-service-modal__text {
      margin: 0;
      color: #374151;
      line-height: 1.7;
      font-size: 1rem;
    }

    .liliwmemoria-service-modal__meta {
      margin-top: 14px;
      border: 1px solid rgba(17, 24, 39, 0.1);
      border-radius: 14px;
      background: rgba(20, 44, 20, 0.04);
      padding: 12px 14px;
      display: flex;
      align-items: baseline;
      justify-content: space-between;
      gap: 12px;
    }

    .liliwmemoria-service-modal__meta-label {
      font-size: 0.9rem;
      font-weight: 800;
      color: rgba(20, 44, 20, 0.85);
    }

    .liliwmemoria-service-modal__meta-value {
      font-size: 0.95rem;
      font-weight: 900;
      color: #0f2613;
      text-align: right;
    }

    .liliwmemoria-service-modal__divider {
      height: 1px;
      background: rgba(17, 24, 39, 0.08);
      margin: 16px 18px 0;
    }

    .liliwmemoria-service-modal__form {
      padding: 16px 18px 18px;
    }

    .liliwmemoria-service-modal__form-title {
      margin: 0;
      font-size: 1.1rem;
      font-weight: 900;
      color: #0f2613;
      letter-spacing: -0.01em;
    }

    .liliwmemoria-service-modal__form-subtitle {
      margin: 6px 0 14px;
      color: rgba(55, 65, 81, 0.85);
      font-size: 0.95rem;
    }

    .liliwmemoria-service-modal__form-inner {
      border: 1px solid rgba(17, 24, 39, 0.1);
      border-radius: 16px;
      background: rgba(20, 44, 20, 0.03);
      padding: 14px;
    }

    .liliwmemoria-service-modal__form-inner .form-control {
      border-radius: 12px;
      padding: 12px 12px;
      border-color: rgba(17, 24, 39, 0.14);
    }

    .liliwmemoria-service-modal__form-inner .form-control:focus {
      border-color: rgba(20, 44, 20, 0.55);
      box-shadow: 0 0 0 0.25rem rgba(20, 44, 20, 0.12);
    }

    .liliwmemoria-service-modal__consent {
      margin-top: 12px;
    }

    .liliwmemoria-service-modal__form-actions {
      display: flex;
      flex-wrap: wrap;
      justify-content: flex-end;
      gap: 10px;
      margin-top: 14px;
    }

    .liliwmemoria-service-modal__form-note {
      margin-top: 10px;
      font-size: 0.9rem;
      color: rgba(55, 65, 81, 0.9);
    }

    .liliwmemoria-service-modal__cta {
      background: #142c14;
      border-radius: 12px;
      padding: 12px 16px;
      font-weight: 900;
      letter-spacing: 0.02em;
      min-width: 170px;
    }

    .liliwmemoria-service-modal__secondary {
      border: 1px solid rgba(17, 24, 39, 0.16);
      background: #fff;
      color: #111827;
      border-radius: 12px;
      padding: 12px 16px;
      font-weight: 800;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
      min-width: 130px;
    }

    .liliwmemoria-service-modal__secondary:hover {
      transform: translateY(-1px);
      box-shadow: 0 10px 20px rgba(17, 24, 39, 0.08);
    }

    .liliwmemoria-service-modal__note {
      margin-top: 12px;
      font-size: 0.9rem;
      color: rgba(55, 65, 81, 0.9);
    }

    @media (max-width: 575.98px) {
      .liliwmemoria-service-modal .modal-body {
        padding: 14px;
      }

      .liliwmemoria-service-modal__divider {
        margin: 14px 14px 0;
      }

      .liliwmemoria-service-modal__form {
        padding: 14px 14px 16px;
      }

      .liliwmemoria-service-modal__form-actions {
        justify-content: stretch;
      }

      .liliwmemoria-service-modal__cta,
      .liliwmemoria-service-modal__secondary {
        width: 100%;
        min-width: 0;
      }
    }

    @media (max-width: 575.98px) {
      .liliwmemoria-service-tile__content {
        padding: 32px 24px 28px;
      }

      .liliwmemoria-service-tile__title {
        font-size: 1.75rem;
      }
    }
  </style>
@endpush
