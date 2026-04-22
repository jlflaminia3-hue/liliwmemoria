  <div class="lonyo-section-padding position-relative section liliwmemoria-slider3">
    <div class="container">
      <div class="row">
        <div class="col-lg-5">
          <div class="lonyo-video-thumb" style="border-radius: 16px; box-shadow: 0 8px 32px rgba(20, 44, 20, 0.38);">
            <video class="video-init" width="100%" controls autoplay muted style="display: block; border-radius: 12px;">
              <source src="{{ asset('frontend/assets/liliw_vid.mp4') }}" type="video/mp4">
              Your browser does not support the video tag.
            </video>
          </div>
        </div>
        <div class="col-lg-7 d-flex align-items-center">
          <div class="lonyo-default-content lonyo-video-section pl-50" data-aos="fade-up" data-aos-duration="500">
            <h2>Memorial Park of San Sebastian</h2>
            <p>
              Originally known as Liliw Garden of Memories, is a serene and peaceful resting place located in Brgy
              Ilayang Palina of Liliw, Laguna. Established in 1985 on a 1.2-hectare rice field, the park was the vision of Mr. Bernardo “Sosoy” P.
              Rolova Jr., who saw the land’s potential to be transformed into a peaceful memorial site.
            </p>
            <div class="mt-30 liliwmemoria-slider3-actions" data-aos="fade-up" data-aos-duration="700">
              <a class="lonyo-default-btn video-btn liliwmemoria-inquiry-trigger" href="#" data-bs-toggle="modal" data-bs-target="#inquiryModal">Schedule a Visit</a>
              <a class="lonyo-default-btn video-btn liliwmemoria-slider3-actions__secondary" href="#" data-bs-toggle="modal" data-bs-target="#directionsModal">Get Directions</a>
              <a class="lonyo-default-btn video-btn liliwmemoria-slider3-actions__secondary" href="{{ url('/') }}#contact">Contact Us</a>
            </div>
          </div>
        </div>
      </div>
      {{-- <div class="row">
        <div class="col-xl-4 col-md-6">
          <div class="lonyo-process-wrap" data-aos="fade-up" data-aos-duration="500">
            <div class="lonyo-process-title">
              <h4>  </h4>
            </div>
            <div class="lonyo-process-data">
              <p></p>
            </div>
          </div>
        </div>
        <div class="col-xl-4 col-md-6">
          <div class="lonyo-process-wrap" data-aos="fade-up" data-aos-duration="700">
            <div class="lonyo-process-title">
              <h4></h4>
            </div>
            <div class="lonyo-process-data">
              <p></p>
            </div>
          </div>
        </div>
        <div class="col-xl-4 col-md-6">
          <div class="lonyo-process-wrap" data-aos="fade-up" data-aos-duration="900">
            <div class="lonyo-process-title">
              <h4></h4>
            </div>
            <div class="lonyo-process-data">
              <p></p>
            </div>
          </div>
        </div>
        <div class="border-bottom" data-aos="fade-up" data-aos-duration="500"></div>
      </div> --}}
    </div>
  </div>

  <div class="modal fade" id="directionsModal" tabindex="-1" aria-labelledby="directionsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="directionsModalLabel">Get Directions</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-0">
          <div style="position: relative; width: 100%; padding-top: 56.25%;">
            <iframe
              src="https://www.google.com/maps/embed?pb=!1m17!1m12!1m3!1d823.6770420820307!2d121.43133517759503!3d14.135209795209938!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m2!1m1!2zMTTCsDA4JzA0LjkiTiAxMjHCsDI1JzUyLjkiRQ!5e1!3m2!1sen!2sph!4v1776868344851!5m2!1sen!2sph"
              style="border: 0; position: absolute; inset: 0; width: 100%; height: 100%;"
              allowfullscreen=""
              loading="lazy"
              referrerpolicy="no-referrer-when-downgrade"
              title="Memorial Park of San Sebastian map"
            ></iframe>
          </div>
        </div>
      </div>
    </div>
  </div>
