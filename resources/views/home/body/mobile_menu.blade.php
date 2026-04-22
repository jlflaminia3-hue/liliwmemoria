  <div class="lonyo-menu-wrapper">
    <div class="lonyo-menu-area text-center">
      <div class="lonyo-menu-mobile-top">
        <div class="mobile-logo">
          @include('home.body.brand')
        </div>
        <button class="lonyo-menu-toggle mobile">
          <i class="ri-close-line"></i>
        </button>
      </div>
      <div class="lonyo-mobile-menu">
        <ul>
          <li class="menu-item-has-children">
            <a href="{{ route('about.page') }}">About</a>
          </li>
          <li class="menu-item-has-children">
            <a href="{{ route('services.page') }}">Services</a>
          </li>
          <li class="menu-item-has-children">
            <a href="#">Galleries</a>
          </li>
          <li class="menu-item-has-children">
            <a href="{{ route('location.page') }}">Location</a>
          </li>
          <li>
            <a href="{{ url('/') }}#contact">Contact</a>
          </li>
        </ul>
      </div>
      <div class="lonyo-mobile-menu-btn">
        <a class="lonyo-default-btn sm-size liliwmemoria-inquiry-trigger" href="#" data-bs-toggle="modal" data-bs-target="#inquiryModal" data-text="Get in Touch"><span class="btn-wraper">Contact</span></a>
        <a class="lonyo-default-btn sm-size video-btn liliwmemoria-appointment-trigger" href="#" data-bs-toggle="modal" data-bs-target="#appointmentModal" data-text="Book an Appointment"><span class="btn-wraper">Book an Appointment</span></a>
      </div>
    </div>
  </div>
