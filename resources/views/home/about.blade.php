@extends('home.home_master')

@section('home')
  <section class="liliwmemoria-page-hero liliwmemoria-hero-bg liliwmemoria-about-hero">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-10">
          <div class="liliwmemoria-page-hero__content text-center" data-aos="fade-up" data-aos-duration="700">
            <h1 class="liliwmemoria-page-hero__title">About Us</h1>
          </div>
        </div>
      </div>
    </div>
  </section>

<section class="liliwmemoria-slider2-cards1 position-relative overflow-hidden">
  <div class="container">
    {{-- <div class="liliwmemoria-services-section__title text-center">
      <span class="liliwmemoria-services-section__rule" aria-hidden="true"></span>
    </div> --}}

    <div class="liliwmemoria-services-card shadow-sm">
      <div class="row g-0 align-items-stretch">
         <div class="col-lg-6">
          <div class="liliwmemoria-services-card__media">
            <img
              src="{{ asset('frontend/assets/images/about/liliw2.jpg') }}"
              alt="Narra lots"
              class="liliwmemoria-services-card__img"
            >
          </div>
        </div>
       <div class="col-lg-6">
          <div class="liliwmemoria-services-card__body">
            <h3 class="liliwmemoria-services-card__title">Built with care for families</h3>
            <p class="liliwmemoria-services-card__text">
              Founded in Liliw, Laguna, our work brings together local tradition and modern support. We help families
              move through memorial arrangements with confidence — with clear steps, reliable assistance, and a calm
              space for remembrance.
            </p>
            <p class="liliwmemoria-services-card__text">
              We are more than a system — we are a commitment to families who entrust us with their most meaningful
              memories. Our goal is simple: provide peace of mind through compassionate service and transparent
              communication.
            </p>

              <a
                class="lonyo-default-btn liliwmemoria-inquiry-trigger"
                href="#"
                data-bs-toggle="modal"
                data-bs-target="#inquiryModal"
              >
                Inquire Now
              </a>

            {{-- <a href="{{ route('services.page') }}" class="liliwmemoria-services-card__btn">
              Read More
              <span aria-hidden="true">&rarr;</span>
            </a> --}}
          </div>
        </div>
      </div>
      
    </div>
  </div>
</section>
  
            {{-- <div class="liliwmemoria-about-hero__actions">
              <a
                class="lonyo-default-btn liliwmemoria-inquiry-trigger"
                href="#"
                data-bs-toggle="modal"
                data-bs-target="#inquiryModal"
              >
                Inquire Now
              </a>
              <a class="lonyo-default-btn liliwmemoria-about-hero__secondary" href="{{ route('services.page') }}">
                View Services
              </a>
            </div> --}}

  {{-- <section class="lonyo-section-padding liliwmemoria-about-intro">
    <div class="container">
      <div class="row align-items-center">
          <div class="liliwmemoria-about-intro__card" data-aos="fade-up" data-aos-duration="700">
            <div class="liliwmemoria-about-intro__head">
              <h3 class="liliwmemoria-about-intro__title">Built with care for families in Liliw, Laguna</h3>
            </div>
            <p>
              Founded in Liliw, Laguna, our work brings together local tradition and modern support. We help families
              move through memorial arrangements with confidence — with clear steps, reliable assistance, and a calm
              space for remembrance.
            </p>
            <p>
              We are more than a system — we are a commitment to families who entrust us with their most meaningful
              memories. Our goal is simple: provide peace of mind through compassionate service and transparent
              communication.
            </p>
          </div>
      </div>
    </div>
  </section> --}}
</br>
  <section>
    <div class="liliwmemoria-about-banner__media" data-aos="fade-up" data-aos-duration="700">
      <img
        src="{{ asset('frontend/assets/images/about/banner.png') }}"
        alt="LiliwMemoria banner"
        class="img-fluid"
        loading="lazy"
      >
    </div>
  </section>
</br></br>



  {{-- include in services
  
  <section class="lonyo-section-padding pt-0 liliwmemoria-about-values">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-10">
          <div class="liliwmemoria-about-section-title text-center" data-aos="fade-up" data-aos-duration="700">
            <h2>What We Value</h2>
            <p>
              The principles that guide how we care for families with empathy, clarity, and respect.
            </p>
          </div>
        </div>
      </div>

      <div class="row g-4 justify-content-center mt-3">
        <div class="col-lg-5">
          <div class="liliwmemoria-about-feature" data-aos="fade-up" data-aos-duration="650">
            <div class="liliwmemoria-about-feature__head">
              <span class="liliwmemoria-about-feature__icon" aria-hidden="true"><i class="ri-heart-2-line"></i></span>
              <div>
                <h3 class="liliwmemoria-about-feature__title">Compassionate & Community-Centered</h3>
                <p class="liliwmemoria-about-feature__subtitle">We treat every family with care.</p>
              </div>
            </div>
            <ul class="liliwmemoria-about-bullets">
              <li><strong>Compassionate Service</strong>guiding families with empathy at every step.</li>
              <li><strong>Transparency & Accountability</strong>clear workflows and easy-to-follow updates.</li>
              <li><strong>Community Connection</strong>honoring heritage while embracing modern solutions.</li>
              <li><strong>Innovation</strong>improving convenience with organized, reliable tools.</li>
            </ul>
          </div>
        </div>

        <div class="col-lg-5">
          <div class="liliwmemoria-about-feature liliwmemoria-about-feature--accent" data-aos="fade-up" data-aos-duration="750">
            <div class="liliwmemoria-about-feature__head">
              <span class="liliwmemoria-about-feature__icon" aria-hidden="true"><i class="ri-service-line"></i></span>
              <div>
                <h3 class="liliwmemoria-about-feature__title">What We Offer</h3>
                <p class="liliwmemoria-about-feature__subtitle">Clear options and helpful guidance.</p>
              </div>
            </div>
            <ul class="liliwmemoria-about-bullets">
              <li>Lot reservations with clear payment options.</li>
              <li>Guidance for requirements, contracts, and records.</li>
              <li>Well-maintained grounds and peaceful visiting areas.</li>
              <li>Helpful support for inquiries, scheduling, and directions.</li>
            </ul>
            <div class="liliwmemoria-about-feature__actions">
              <a
                href="#"
                class="lonyo-default-btn liliwmemoria-inquiry-trigger"
                data-bs-toggle="modal"
                data-bs-target="#inquiryModal"
              >
                Inquire Now
              </a>
              <a class="lonyo-default-btn liliwmemoria-about-panel__secondary" href="{{ route('services.page') }}">
                See Services
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section> --}}

  <section class="lonyo-section-padding pt-0 liliwmemoria-about-why">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-10">
          <div class="liliwmemoria-about-section-title text-center" data-aos="fade-up" data-aos-duration="700">
            <h2>Why Choose Us</h2>
            <p>
              We focus on what families need most: a peaceful place, dependable care, and respectful service with
              clear guidance and fair options.
            </p>
          </div>
        </div>
      </div>

      <div class="row g-4 justify-content-center mt-3">
        <div class="col-lg-3 col-md-6">
          <div class="liliwmemoria-about-card" data-aos="fade-up" data-aos-duration="600">
            <h3>Peaceful for Families</h3>
            <p>A quiet, well-kept space to remember, reflect, and visit in comfort.</p>
          </div>
        </div>
        <div class="col-lg-3 col-md-6">
          <div class="liliwmemoria-about-card" data-aos="fade-up" data-aos-duration="700">
            <h3>Affordable Options</h3>
            <p>Respectful memorial services, including burial plots and mausoleums, priced with families in mind.</p>
          </div>
        </div>
        <div class="col-lg-3 col-md-6">
          <div class="liliwmemoria-about-card" data-aos="fade-up" data-aos-duration="800">
            <h3>Eco-Friendly Grounds</h3>
            <p>We plant trees, manage waste responsibly, and preserve the natural beauty of the park.</p>
          </div>
        </div>
        <div class="col-lg-3 col-md-6">
          <div class="liliwmemoria-about-card" data-aos="fade-up" data-aos-duration="900">
            <h3>Community Support</h3>
            <p>We help create local opportunities and provide services that meet the community’s needs.</p>
          </div>
        </div>
      </div>
    </div>
  </section>


<section class="liliwmemoria-slider2-cards position-relative overflow-hidden">
  <div class="container">
    <div class="liliwmemoria-services-section__title text-center">
      <span class="liliwmemoria-services-section__rule" aria-hidden="true"></span>
    </div>

    <div class="liliwmemoria-services-card shadow-sm">
      <div class="row g-0 align-items-stretch">
        <div class="col-lg-6">
          <div class="liliwmemoria-services-card__media">
            <img
              src="{{ asset('frontend/assets/images/about/liliw1.jpg') }}"
              alt="Narra lots"
              class="liliwmemoria-services-card__img"
            >
          </div>
        </div>
        <div class="col-lg-6">
          <div class="liliwmemoria-services-card__body">
            <h3 class="liliwmemoria-services-card__title">Services</h3>
            <p class="liliwmemoria-services-card__text">
              At LiliwMemoria, we believe every life deserves to be remembered with dignity, compassion, and clarity.
              We’re here to guide families and keep memorial processes simple, respectful, and transparent.
            </p>
            <p class="liliwmemoria-services-card__text">
              Whether you are planning ahead or arranging for immediate needs, we’ll guide you with clarity and care —
              from choosing a lot to completing requirements.
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
</section>

  
  {{-- <section class="lonyo-section-padding pt-0 liliwmemoria-about-services">
    <div class="container">
      <div class="row align-items-center g-5">
        <div class="col-lg-6 order-lg-2">
          <div class="lonyo-default-content" data-aos="fade-up" data-aos-duration="700">
            <div class="liliwmemoria-about-pill">Featured Service</div>
            <h2>Garden Lots</h2>
            <p>
              At LiliwMemoria, we believe every life deserves to be remembered with dignity, compassion, and clarity.
              We’re here to guide families and keep memorial processes simple, respectful, and transparent.
            </p>
            <p>
              Whether you are planning ahead or arranging for immediate needs, we’ll guide you with clarity and care —
              from choosing a lot to completing requirements.
            </p>
            <div class="liliwmemoria-about-services__actions">
              <a
                href="#"
                class="lonyo-default-btn liliwmemoria-inquiry-trigger"
                data-bs-toggle="modal"
                data-bs-target="#inquiryModal"
              >
                Inquire About Garden Lots
              </a>
              <a class="lonyo-default-btn liliwmemoria-about-hero__secondary" href="{{ route('services.page') }}">
                Explore More Services
              </a>
            </div>
          </div>
        </div>
        <div class="col-lg-6 order-lg-1">
          <div class="liliwmemoria-about-services__media" data-aos="fade-up" data-aos-duration="700">
            <img
              src="{{ asset('frontend/assets/images/service/garden-lot.jpg') }}"
              alt="Garden lots"
              class="img-fluid"
              loading="lazy"
            >
          </div>
        </div>
      </div>
    </div>
  </section> --}}

  @include('home.layout.slider4')

  <style>
    .liliwmemoria-about-hero__eyebrow{
      margin: 0 0 8px;
      letter-spacing: 0.12em;
      text-transform: uppercase;
      font-weight: 700;
      color: rgba(255, 255, 255, 0.82);
      font-size: 0.88rem;
    }

    .liliwmemoria-about-hero__lead{
      max-width: 820px;
      margin: 14px auto 0;
      color: rgba(255, 255, 255, 0.86);
      font-size: 1.05rem;
      line-height: 1.65;
    }

    .liliwmemoria-about-hero__actions{
      margin-top: 22px;
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      justify-content: center;
      gap: 12px;
    }

    .liliwmemoria-about-hero__secondary{
      background: rgba(255, 255, 255, 0.14);
      border: 1px solid rgba(255, 255, 255, 0.22);
      color: #fff !important;
    }

    .liliwmemoria-about-hero__secondary:hover,
    .liliwmemoria-about-hero__secondary:focus{
      background: rgba(255, 255, 255, 0.22);
      border-color: rgba(255, 255, 255, 0.3);
      color: #fff !important;
    }

    .liliwmemoria-about-media,
    .liliwmemoria-about-services__media{
      position: relative;
      border-radius: 18px;
      overflow: hidden;
      box-shadow: 0 18px 45px rgba(20, 44, 20, 0.18);
      border: 1px solid rgba(20, 44, 20, 0.1);
      background: #fff;
    }

    .liliwmemoria-about-media img,
    .liliwmemoria-about-services__media img{
      width: 100%;
      height: auto;
      display: block;
      object-fit: cover;
      aspect-ratio: 4 / 3;
    }

    .liliwmemoria-about-banner__media{
      max-width: 1040px;
      margin-left: auto;
      margin-right: auto;
      display: flex;
      justify-content: center;
      align-items: center;
      overflow: hidden;
    }


    .liliwmemoria-about-media__badge{
      position: absolute;
      left: 16px;
      bottom: 16px;
      display: inline-flex;
      align-items: center;
      gap: 10px;
      padding: 10px 12px;
      border-radius: 999px;
      background: rgba(255, 255, 255, 0.92);
      color: #142c14;
      font-weight: 700;
      font-size: 0.92rem;
      box-shadow: 0 12px 28px rgba(20, 44, 20, 0.16);
    }

    .liliwmemoria-about-media__badge-icon{
      width: 34px;
      height: 34px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      border-radius: 999px;
      background: rgba(20, 44, 20, 0.08);
      color: #142c14;
      font-size: 18px;
    }

    .liliwmemoria-about-panel{
      background: #ffffff;
      border: 1px solid rgba(20, 44, 20, 0.12);
      border-radius: 18px;
      padding: 26px;
      box-shadow: 0 14px 34px rgba(20, 44, 20, 0.08);
    }

    .liliwmemoria-about-panel--soft{
      background: linear-gradient(180deg, rgba(20, 44, 20, 0.04), rgba(20, 44, 20, 0.015));
    }

    .liliwmemoria-about-panel__header h2{
      margin-bottom: 10px;
    }

    .liliwmemoria-about-panel__grid{
      margin-top: 18px;
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 14px;
    }

    .liliwmemoria-about-stat{
      display: flex;
      gap: 12px;
      align-items: flex-start;
      padding: 14px 14px;
      border-radius: 14px;
      background: rgba(20, 44, 20, 0.03);
      border: 1px solid rgba(20, 44, 20, 0.08);
    }

    .liliwmemoria-about-stat__icon{
      width: 42px;
      height: 42px;
      border-radius: 12px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      background: rgba(20, 44, 20, 0.08);
      color: #142c14;
      font-size: 20px;
      flex: 0 0 42px;
    }

    .liliwmemoria-about-stat__label{
      margin: 0;
      font-size: 0.85rem;
      color: rgba(20, 44, 20, 0.68);
      font-weight: 700;
      letter-spacing: 0.02em;
      text-transform: uppercase;
    }

    .liliwmemoria-about-stat__value{
      margin: 2px 0 0;
      font-weight: 700;
      color: #142c14;
      line-height: 1.35;
    }

    .liliwmemoria-about-panel__footer{
      margin-top: 18px;
      display: flex;
      flex-wrap: wrap;
      gap: 12px;
      align-items: center;
    }

    .liliwmemoria-about-panel__secondary{
      background: transparent;
      border: 1px solid rgba(20, 44, 20, 0.22);
      color: #142c14 !important;
    }

    .liliwmemoria-about-panel__secondary:hover,
    .liliwmemoria-about-panel__secondary:focus{
      border-color: rgba(20, 44, 20, 0.4);
      color: #142c14 !important;
    }

    .liliwmemoria-about-section-title h2{
      margin-bottom: 10px;
    }

    .liliwmemoria-about-section-title p{
      max-width: 860px;
      margin-left: auto;
      margin-right: auto;
    }

    .liliwmemoria-about-card{
      height: 100%;
      background: #ffffff;
      border: 1px solid rgba(20, 44, 20, 0.12);
      border-radius: 18px;
      padding: 22px 18px;
      box-shadow: 0 12px 30px rgba(20, 44, 20, 0.06);
      transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
    }

    .liliwmemoria-about-card:hover{
      transform: translateY(-4px);
      border-color: rgba(20, 44, 20, 0.24);
      box-shadow: 0 18px 40px rgba(20, 44, 20, 0.1);
    }

    .liliwmemoria-about-card__icon{
      width: 54px;
      height: 54px;
      border-radius: 16px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      background: rgba(20, 44, 20, 0.08);
      color: #142c14;
      font-size: 26px;
      margin-bottom: 14px;
    }

    .liliwmemoria-about-card h3{
      font-size: 1.12rem;
      font-weight: 800;
      letter-spacing: -0.02em;
      margin-bottom: 8px;
      color: #142c14;
    }

    .liliwmemoria-about-card p{
      margin: 0;
      color: rgba(20, 44, 20, 0.72);
      line-height: 1.65;
      font-size: 0.97rem;
    }

    .liliwmemoria-about-intro .liliwmemoria-about-section-title{
      margin-bottom: 26px;
    }

    .liliwmemoria-about-intro__card{
      background: #ffffff;
      border: 1px solid rgba(20, 44, 20, 0.12);
      border-radius: 18px;
      padding: 22px 20px;
      box-shadow: 0 14px 34px rgba(20, 44, 20, 0.08);
    }

    .liliwmemoria-about-intro__head{
      padding-bottom: 14px;
      margin-bottom: 14px;
      border-bottom: 1px solid rgba(20, 44, 20, 0.09);
    }

    .liliwmemoria-about-intro__kicker{
      margin: 0 0 6px;
      font-weight: 800;
      letter-spacing: 0.14em;
      text-transform: uppercase;
      color: rgba(20, 44, 20, 0.72);
      font-size: 0.78rem;
    }

    .liliwmemoria-about-intro__title{
      margin: 0;
      font-size: 1.35rem;
      font-weight: 900;
      letter-spacing: -0.03em;
      color: #142c14;
      line-height: 1.2;
    }

    .liliwmemoria-about-copy-split{
      margin-top: 18px;
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 14px;
    }

    /* Hide the old inline value/offer block (moved to its own section). */
    .liliwmemoria-about-copy-split--moved{
      display: none;
    }

    .liliwmemoria-about-feature{
      height: 100%;
      background: #ffffff;
      border: 1px solid rgba(20, 44, 20, 0.12);
      border-radius: 18px;
      padding: 20px 18px;
      box-shadow: 0 14px 34px rgba(20, 44, 20, 0.08);
    }

    .liliwmemoria-about-feature--accent{
      background: linear-gradient(180deg, rgba(20, 44, 20, 0.045), rgba(20, 44, 20, 0.012));
    }

    .liliwmemoria-about-feature__head{
      display: flex;
      gap: 12px;
      align-items: center;
      padding-bottom: 14px;
      margin-bottom: 14px;
      border-bottom: 1px solid rgba(20, 44, 20, 0.09);
    }

    .liliwmemoria-about-feature__icon{
      width: 48px;
      height: 48px;
      border-radius: 16px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      background: rgba(20, 44, 20, 0.08);
      color: #142c14;
      font-size: 22px;
      flex: 0 0 48px;
    }

    .liliwmemoria-about-feature__title{
      margin: 0;
      font-size: 1.12rem;
      font-weight: 900;
      letter-spacing: -0.02em;
      color: #142c14;
      line-height: 1.2;
    }

    .liliwmemoria-about-feature__subtitle{
      margin: 4px 0 0;
      color: rgba(20, 44, 20, 0.68);
      font-weight: 650;
      font-size: 0.95rem;
      line-height: 1.3;
    }

    .liliwmemoria-about-feature__actions{
      margin-top: 16px;
      display: flex;
      flex-wrap: wrap;
      gap: 12px;
      align-items: center;
    }

    .liliwmemoria-about-copy-split__card{
      background: rgba(20, 44, 20, 0.02);
      border: 1px solid rgba(20, 44, 20, 0.09);
      border-radius: 16px;
      padding: 16px 16px;
    }

    .liliwmemoria-about-copy-split__card h3{
      margin: 0 0 10px;
      font-size: 1.05rem;
      font-weight: 900;
      letter-spacing: -0.02em;
      color: #142c14;
    }

    .liliwmemoria-about-bullets{
      margin: 0;
      padding-left: 0;
      list-style: none;
      color: rgba(20, 44, 20, 0.74);
      display: grid;
      gap: 8px;
      line-height: 1.6;
      font-size: 0.97rem;
    }

    .liliwmemoria-about-bullets li{
      position: relative;
      padding-left: 28px;
    }

    .liliwmemoria-about-bullets li::before{
      content: "✓";
      position: absolute;
      left: 0;
      top: 0.1rem;
      width: 20px;
      height: 20px;
      border-radius: 999px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      background: rgba(16, 185, 129, 0.14);
      color: #0a7a50;
      font-weight: 900;
      font-size: 0.85rem;
      line-height: 1;
    }

    .liliwmemoria-about-bullets strong{
      color: #142c14;
    }

    .liliwmemoria-about-pill{
      display: inline-flex;
      align-items: center;
      padding: 6px 12px;
      border-radius: 999px;
      background: rgba(20, 44, 20, 0.08);
      color: #142c14;
      font-weight: 800;
      letter-spacing: 0.12em;
      text-transform: uppercase;
      font-size: 0.78rem;
      margin-bottom: 12px;
    }

    .liliwmemoria-about-services__actions{
      margin-top: 18px;
      display: flex;
      flex-wrap: wrap;
      gap: 12px;
      align-items: center;
    }

    .liliwmemoria-about-timeline{
      display: grid;
      gap: 12px;
    }

    .liliwmemoria-about-timeline__item{
      display: grid;
      grid-template-columns: 110px 1fr;
      gap: 12px;
      padding: 14px;
      border-radius: 16px;
      background: rgba(255, 255, 255, 0.65);
      border: 1px solid rgba(20, 44, 20, 0.1);
    }

    .liliwmemoria-about-timeline__year{
      margin: 0;
      font-weight: 900;
      color: #142c14;
      letter-spacing: -0.02em;
    }

    .liliwmemoria-about-timeline__text{
      margin: 0;
      color: rgba(20, 44, 20, 0.74);
      line-height: 1.65;
    }

    @media (max-width: 991.98px){
      .liliwmemoria-about-banner__media{
        max-width: 82vw;
        padding: 10px;
      }

      .liliwmemoria-about-banner__media img{
        max-width: 82vw;
      }

      .liliwmemoria-about-panel{
        padding: 20px;
      }

      .liliwmemoria-about-panel__grid{
        grid-template-columns: 1fr;
      }

      .liliwmemoria-about-intro__card{
        padding: 18px 16px;
      }

      .liliwmemoria-about-copy-split{
        grid-template-columns: 1fr;
      }

      .liliwmemoria-about-timeline__item{
        grid-template-columns: 1fr;
      }
    }
  </style>
@endsection
