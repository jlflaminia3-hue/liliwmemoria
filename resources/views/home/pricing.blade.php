@extends('home.home_master')

@section('home')
  <section class="liliwmemoria-page-hero liliwmemoria-hero-bg">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-10">
          <div class="liliwmemoria-page-hero__content text-center" data-aos="fade-up" data-aos-duration="700">
            <h1 class="liliwmemoria-page-hero__title">Pricing</h1>
          </div>
        </div>
      </div>
  </section>

  <section class="lonyo-section-padding">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-10">
          <div class="lonyo-default-content" data-aos="fade-up" data-aos-duration="700">
            <h2>Honoring Loved Ones with Care</h2>
            <p class="data">
              Liliw Memoria provides a serene, respectful resting place and compassionate support for families in need.
              We are committed to maintaining peaceful grounds, offering attentive service, and helping you plan with clarity.
            </p>
          </div>
        </div>
      </div>
    </div>
  </section>
  @include('home.layout.slider2')
  @include('home.layout.slider4')
@endsection

