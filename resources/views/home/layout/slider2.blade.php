  <div class="lonyo-section-padding position-relative overflow-hidden">
    <div class="container">
      <div class="lonyo-section-title">
        <div class="row">
          <div class="col-xl-8 col-lg-8">
            <p>Welcome to LiliwMemoria</p>
            <h2 class="lonyo-slide-title">What can we do for you?</h2>
          </div>
          <div class="col-xl-4 col-lg-4 d-flex align-items-center justify-content-end">
            <div class="lonyo-title-btn" data-aos="fade-up" data-aos-duration="900">
              <a class="lonyo-default-btn hero-btn" href="#" data-bs-toggle="modal" data-bs-target="#appointmentModal">Book an Appointment</a>
            </div>

          </div>
        </div>
      </div>
    </div>

<div class="lonyo-testimonial-slider-init">
    @php
      $cards = [
        ['title' => 'BACK OFFICE LOTS', 'img' => 'frontend/assets/images/service/backoffice.jpg'],
        ['title' => 'NARRA', 'img' => 'frontend/assets/images/service/narra.jpg'],
        ['title' => 'GARDEN LOTS', 'img' => 'frontend/assets/images/service/garden-lot.jpg'],
        ['title' => 'MAUSOLEUM', 'img' => 'frontend/assets/images/service/mausoleum.jpg'],
        ['title' => 'PHASES', 'img' => 'frontend/assets/images/service/phase1.jpg'],
        
      ];
    @endphp

    @foreach ($cards as $card)
      <div class="lonyo-t-wrap wrap2 light-bg lonyo-review-card">
        <img class="lonyo-review-card__img" src="{{ asset($card['img']) }}" alt="{{ $card['title'] }}">
        <div class="lonyo-review-card__overlay">
          <h3 class="lonyo-review-card__title">{{ $card['title'] }}</h3>
        </div>
      </div>
    @endforeach
  </div>
  <div class="lonyo-t-overlay2">
    <img src="{{ asset('frontend/assets/images/v2/overlay.png') }}" alt="">
  </div>

  <style>
    /* Scoped styling for square image cards inside the testimonial slider */
    .lonyo-testimonial-slider-init .lonyo-review-card {
      padding: 0;
      margin-bottom: 0;
      animation: none;
      aspect-ratio: 1 / 1;
      width: 100%;
      border-radius: 10px;
      overflow: hidden;
      position: relative;
    }

    .lonyo-testimonial-slider-init .lonyo-review-card__img {
      position: absolute;
      inset: 0;
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .lonyo-testimonial-slider-init .lonyo-review-card__overlay {
      position: absolute;
      inset: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 16px;
      background: rgba(0, 0, 0, 0.38);
    }

    .lonyo-testimonial-slider-init .lonyo-review-card__title {
      margin: 0;
      color: #fff;
      text-align: center;
      font-weight: 700;
      letter-spacing: -0.01em;
      font-size: clamp(14px, 2.2vw, 20px);
      line-height: 1.2;
    }

    @media (max-width: 575.98px) {
      .lonyo-testimonial-slider-init .slick-slide {
        margin: 0 6px;
      }

      .lonyo-testimonial-slider-init .lonyo-review-card {
        max-width: 320px;
        margin-left: auto;
        margin-right: auto;
      }

      .lonyo-testimonial-slider-init .lonyo-review-card__overlay {
        padding: 12px;
      }
    }
  </style>
</div>

    <div class="lonyo-t-overlay2">
      <img src="{{ asset('frontend/assets/images/v2/overlay.png') }}" alt="">
    </div>
  </div>
