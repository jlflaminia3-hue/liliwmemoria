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


      <div class="row g-4 mt-4 justify-content-center">
        <div class="liliwmemoria-services-grid__title text-center" data-aos="fade-up" data-aos-duration="700">
          <h2 class="liliwmemoria-services-grid__heading">PRODUCTS</h2>
          <span class="liliwmemoria-services-grid__rule" aria-hidden="true"></span>
        </div>
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
                <a
                  href="{{ route('services.show', $service['id']) }}"
                  class="liliwmemoria-service-tile__btn"
                >
                  VIEW DETAILS
                  <span aria-hidden="true">&rarr;</span>
                </a>
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
                <a
                  href="{{ route('services.show', $service['id']) }}"
                  class="liliwmemoria-service-tile__btn"
                >
                  VIEW DETAILS
                  <span aria-hidden="true">&rarr;</span>
                </a>
              </div>
            </article>
          </div>
        @endforeach
      </div>
    </div>
  </section>

  <section class="pt-0 liliwmemoria-slider2-cards2" id="pricing">
    <div class="container">
      <div class="text-center mb-4" data-aos="fade-up" data-aos-duration="700">
      </br></br>
        <h2 class="liliwmemoria-services-grid__heading" style="font-size: 1.75rem;">Flexible Payment Plans</h2>
        <span class="liliwmemoria-services-grid__rule" aria-hidden="true"></span>
        <p class="mt-2 mx-auto" style="max-width: 680px; color: #4a5a4a; line-height: 1.5; font-size: 0.9rem;">
          Transparent pricing designed to make memorial services accessible and affordable. Choose a flexible payment schedule that fits your budget.
        </p>
      </div>

      <div class="row g-3 justify-content-center">
        <!-- Phase 1 & 2 -->
        <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-duration="700">
          <div class="card border-0 shadow-sm h-100 pricing-card" style="border-radius: 8px; overflow: hidden;">
            <div class="card-body p-3">
              <h3 class="mb-3" style="color: #142c14; font-weight: 700; font-size: 1.1rem;">Phase 1 & 2</h3>
              <div class="d-flex justify-content-between mb-3" style="font-size: 0.85rem;">
                <div>
                  <p class="mb-0 text-muted" style="font-size: 0.75rem;">Contract</p>
                  <strong style="color: #142c14;">₱70,000</strong>
                </div>
                <div>
                  <p class="mb-0 text-muted" style="font-size: 0.75rem;">Down</p>
                  <strong style="color: #142c14;">₱10,000</strong>
                </div>
                <div>
                  <p class="mb-0 text-muted" style="font-size: 0.75rem;">Balance</p>
                  <strong style="color: #142c14;">₱60,000</strong>
                </div>
              </div>
              <div class="table-responsive">
                <table class="table table-sm table-bordered mb-0" style="border-color: #e5e7eb; font-size: 0.8rem;">
                  <thead style="background-color: #f0fdf4;">
                    <tr>
                      <th class="py-2 px-2" style="color: #142c14; font-size: 0.75rem;">Duration</th>
                      <th class="py-2 px-2" style="color: #142c14; font-size: 0.75rem;">Total</th>
                      <th class="py-2 px-2" style="color: #142c14; font-size: 0.75rem;">Monthly</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td class="py-1 px-2">12m (10%)</td>
                      <td class="py-1 px-2">₱66,000</td>
                      <td class="py-1 px-2">₱5,500</td>
                    </tr>
                    <tr>
                      <td class="py-1 px-2">18m (15%)</td>
                      <td class="py-1 px-2">₱69,000</td>
                      <td class="py-1 px-2">₱3,833</td>
                    </tr>
                    <tr>
                      <td class="py-1 px-2">24m (20%)</td>
                      <td class="py-1 px-2">₱72,000</td>
                      <td class="py-1 px-2">₱3,000</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <!-- Garden Lot -->
        <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-duration="700">
          <div class="card border-0 shadow-sm h-100 pricing-card" style="border-radius: 8px; overflow: hidden;">
            <div class="card-body p-3">
              <h3 class="mb-3" style="color: #142c14; font-weight: 700; font-size: 1.1rem;">Garden Lot</h3>
              <div class="d-flex justify-content-between mb-3" style="font-size: 0.85rem;">
                <div>
                  <p class="mb-0 text-muted" style="font-size: 0.75rem;">Contract</p>
                  <strong style="color: #142c14;">₱90,000</strong>
                </div>
                <div>
                  <p class="mb-0 text-muted" style="font-size: 0.75rem;">Down</p>
                  <strong style="color: #142c14;">₱20,000</strong>
                </div>
                <div>
                  <p class="mb-0 text-muted" style="font-size: 0.75rem;">Balance</p>
                  <strong style="color: #142c14;">₱70,000</strong>
                </div>
              </div>
              <div class="table-responsive">
                <table class="table table-sm table-bordered mb-0" style="border-color: #e5e7eb; font-size: 0.8rem;">
                  <thead style="background-color: #f0fdf4;">
                    <tr>
                      <th class="py-2 px-2" style="color: #142c14; font-size: 0.75rem;">Duration</th>
                      <th class="py-2 px-2" style="color: #142c14; font-size: 0.75rem;">Total</th>
                      <th class="py-2 px-2" style="color: #142c14; font-size: 0.75rem;">Monthly</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td class="py-1 px-2">12m (10%)</td>
                      <td class="py-1 px-2">₱77,000</td>
                      <td class="py-1 px-2">₱6,417</td>
                    </tr>
                    <tr>
                      <td class="py-1 px-2">18m (15%)</td>
                      <td class="py-1 px-2">₱80,500</td>
                      <td class="py-1 px-2">₱4,472</td>
                    </tr>
                    <tr>
                      <td class="py-1 px-2">24m (20%)</td>
                      <td class="py-1 px-2">₱84,000</td>
                      <td class="py-1 px-2">₱3,500</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <!-- Back Office Lot -->
        <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-duration="700">
          <div class="card border-0 shadow-sm h-100 pricing-card" style="border-radius: 8px; overflow: hidden;">
            <div class="card-body p-3">
              <h3 class="mb-3" style="color: #142c14; font-weight: 700; font-size: 1.1rem;">Back Office Lot</h3>
              <div class="d-flex justify-content-between mb-3" style="font-size: 0.85rem;">
                <div>
                  <p class="mb-0 text-muted" style="font-size: 0.75rem;">Contract</p>
                  <strong style="color: #142c14;">₱60,000</strong>
                </div>
                <div>
                  <p class="mb-0 text-muted" style="font-size: 0.75rem;">Down</p>
                  <strong style="color: #142c14;">₱10,000</strong>
                </div>
                <div>
                  <p class="mb-0 text-muted" style="font-size: 0.75rem;">Balance</p>
                  <strong style="color: #142c14;">₱50,000</strong>
                </div>
              </div>
              <div class="table-responsive">
                <table class="table table-sm table-bordered mb-0" style="border-color: #e5e7eb; font-size: 0.8rem;">
                  <thead style="background-color: #f0fdf4;">
                    <tr>
                      <th class="py-2 px-2" style="color: #142c14; font-size: 0.75rem;">Duration</th>
                      <th class="py-2 px-2" style="color: #142c14; font-size: 0.75rem;">Total</th>
                      <th class="py-2 px-2" style="color: #142c14; font-size: 0.75rem;">Monthly</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td class="py-1 px-2">12m (10%)</td>
                      <td class="py-1 px-2">₱55,000</td>
                      <td class="py-1 px-2">₱4,583</td>
                    </tr>
                    <tr>
                      <td class="py-1 px-2">18m (15%)</td>
                      <td class="py-1 px-2">₱57,500</td>
                      <td class="py-1 px-2">₱3,194</td>
                    </tr>
                    <tr>
                      <td class="py-1 px-2">24m (20%)</td>
                      <td class="py-1 px-2">₱60,000</td>
                      <td class="py-1 px-2">₱2,500</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  @include('home.layout.slider4')
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

    .pricing-card .table th {
      font-weight: 600;
      font-size: 0.75rem;
      text-transform: uppercase;
      letter-spacing: 0.03em;
    }

    .pricing-card .table td {
      font-size: 0.8rem;
      color: #374151;
    }

    .pricing-card .table tbody tr:hover {
      background-color: #f0fdf4;
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
