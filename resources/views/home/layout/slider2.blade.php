<section class="liliwmemoria-slider2-cards position-relative overflow-hidden">
  <div class="container">
    <div class="liliwmemoria-services-section__title text-center">
      <h2 class="liliwmemoria-services-section__heading">Services</h2>
      <span class="liliwmemoria-services-section__rule" aria-hidden="true"></span>
    </div>

    <div class="liliwmemoria-services-card shadow-sm">
      <div class="row g-0 align-items-stretch">
        <div class="col-lg-6">
          <div class="liliwmemoria-services-card__media">
            <img
              src="{{ asset('frontend/assets/images/service/narra2.jpg') }}"
              alt="Narra lots"
              class="liliwmemoria-services-card__img"
            >
          </div>
        </div>
        <div class="col-lg-6">
          <div class="liliwmemoria-services-card__body">
            <h3 class="liliwmemoria-services-card__title">Narra Lots</h3>
            <p class="liliwmemoria-services-card__text">
              Our Narra Lots offer a peaceful, well-kept resting place designed for families who want a clean and serene
              setting for remembrance. Located in a quiet area of the memorial park, Narra Lots provide easy access
              paths, consistent maintenance, and a dignified layout that makes visits calm and comfortable.
            </p>
            <p class="liliwmemoria-services-card__text">
              Whether you are planning ahead or arranging for immediate needs, Narra Lots give you a thoughtful option
              that balances value, beauty, and long-term care, so your loved one's memory can be honored with respect
              for years to come.
            </p>
            <a href="{{ route('services.page') }}" class="liliwmemoria-services-card__btn">
              Read More
              <span aria-hidden="true">&rarr;</span>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</br>
  <div class="container position-relative">
    <div class="lonyo-testimonial-slider-init">
      @php
        $cards = [
          ['title' => 'BACK OFFICE LOTS', 'img' => 'frontend/assets/images/service/backoffice.jpg'],
          ['title' => 'NARRA LOTS', 'img' => 'frontend/assets/images/service/narra.jpg'],
          ['title' => 'GARDEN LOTS', 'img' => 'frontend/assets/images/service/garden-lot.jpg'],
          ['title' => 'MAUSOLEUM', 'img' => 'frontend/assets/images/service/mausoleum.jpg'],
          ['title' => 'LOT PHASES', 'img' => 'frontend/assets/images/service/phases.jpg'],
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
  </div>

  <div class="lonyo-t-overlay2" aria-hidden="true">
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
      background: rgba(20, 44, 20, 0.55);
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
</section>
