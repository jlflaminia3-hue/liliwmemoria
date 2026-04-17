@extends('home.home_master')

@section('home')
<section class="liliwmemoria-page-hero liliwmemoria-hero-bg">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-10">
        <div class="liliwmemoria-page-hero__content text-center" data-aos="fade-up" data-aos-duration="700">
          <h1 class="liliwmemoria-page-hero__title">Our Location</h1>
        </div>
      </div>
    </div>
</section>

<section class="lonyo-section-padding">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-10">
        <div class="lonyo-default-content" data-aos="fade-up" data-aos-duration="700">
          <h2>Visit Us at Liliw Memoria</h2>
          <p class="data">
            Our memorial park is nestled in the heart of Liliw, Laguna, offering a peaceful and serene environment for honoring your loved ones. 
            We welcome visitors daily and are here to assist you with any inquiries about our services, plots, and memorial options.
          </p>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="lonyo-section-padding pt-0">
  <div class="container">
    <div class="row g-4 justify-content-center">
      <div class="col-lg-4 col-md-6">
        <div class="lonyo-default-content text-center" data-aos="fade-up" data-aos-duration="700">
          <div class="liliwmemoria-icon-box">
            <i class="ri-map-pin-2-line"></i>
          </div>
          <h3>Address</h3>
          <p class="data">
            Liliw Memoria<br>
            Liliw, Laguna<br>
            Philippines
          </p>
        </div>
      </div>
      <div class="col-lg-4 col-md-6">
        <div class="lonyo-default-content text-center" data-aos="fade-up" data-aos-duration="700">
          <div class="liliwmemoria-icon-box">
            <i class="ri-time-line"></i>
          </div>
          <h3>Office Hours</h3>
          <p class="data">
            Monday - Saturday<br>
            8:00 AM - 5:00 PM<br>
            Sunday: By Appointment
          </p>
        </div>
      </div>
      <div class="col-lg-4 col-md-6">
        <div class="lonyo-default-content text-center" data-aos="fade-up" data-aos-duration="700">
          <div class="liliwmemoria-icon-box">
            <i class="ri-phone-line"></i>
          </div>
          <h3>Contact Us</h3>
          <p class="data">
            Phone: (049) 123-4567<br>
            Mobile: 0917-123-4567<br>
            Email: info@liliwmemoria.com
          </p>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="lonyo-section-padding pt-0">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-10">
        <div class="lonyo-default-content" data-aos="fade-up" data-aos-duration="700">
          <h2>Find Us on the Map</h2>
          <p class="data">
            Located in the scenic town of Liliw, our memorial park is easily accessible and situated in a tranquil setting. 
            Follow the directions below or contact us for assistance in finding our location.
          </p>
        </div>
        <div class="liliwmemoria-map-container mt-4" data-aos="fade-up" data-aos-duration="700">
          <iframe 
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d30924.89679847!2d121.4351!3d14.1325!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x33bd5c9c6b4f0c0d%3A0x5c8e0d0b4f0e0d0d!2sLiliw%2C%20Laguna!5e0!3m2!1sen!2sph!4v1700000000000!5m2!1sen!2sph"
            width="100%" 
            height="450" 
            style="border:0; border-radius: 12px;" 
            allowfullscreen="" 
            loading="lazy" 
            referrerpolicy="no-referrer-when-downgrade">
          </iframe>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="lonyo-section-padding">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-10">
        <div class="lonyo-default-content text-center" data-aos="fade-up" data-aos-duration="700">
          <h2>How to Get Here</h2>
          <p class="data">
            Liliw Memoria is located in the municipality of Liliw, Laguna. From Manila, you can take a bus bound for Laguna (e.g., Lucena or San Pablo) and get off at Liliw town proper. From there, our memorial park is a short tricycle ride away.
          </p>
        </div>
      </div>
    </div>
  </div>
</section>

@include('home.layout.slider2')
@include('home.layout.slider4')
@endsection
