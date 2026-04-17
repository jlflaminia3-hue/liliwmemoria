<header class="site-header lonyo-header-section light-bg" id="sticky-menu">
    <div class="container">
      <div class="row gx-3 align-items-center justify-content-between liliwmemoria-header-row">
        <div class="col-8 col-sm-auto">
          <div class="header-logo1 ">
            @include('home.body.brand')
          </div>
        </div>
        <div class="col-auto d-flex align-items-center liliwmemoria-header-actions">
          <div class="col liliwmemoria-header-nav-col">
            <div class="lonyo-main-menu-item liliwmemoria-header-nav-shell">
              <nav class="main-menu menu-style1 d-none d-lg-block menu-left liliwmemoria-header-nav">
                <ul>
                  <li class="about liliwmemoria-header-link">
                    <a href="{{ route('about.page') }}">About</a>
                  </li>
                  <li class="services liliwmemoria-header-link">
                    <a href="#">Services</a>
                  </li>
                  <li class="galleries liliwmemoria-header-link">
                    <a href="#">Galleries</a>
                  </li>
                  <li class="location liliwmemoria-header-link">
                    <a href="#">Location</a>
                  </li>
                  <li class="liliwmemoria-header-link">
                    <a href="{{ route('contact.page') }}">Contact</a>
                  </li>
                </ul>
              </nav>
            </div>
          </div>
          {{-- <div class="lonyo-header-info-wraper2">
            <a class="lonyo-default-btn lonyo-header-btn liliwmemoria-appointment-trigger" href="#" data-bs-toggle="modal" data-bs-target="#appointmentModal">Book an Appointment</a>
          </div> --}}
          <div class="lonyo-header-info-wraper2">
            <a class="lonyo-default-btn lonyo-header-btn liliwmemoria-inquiry-trigger" href="#" data-bs-toggle="modal" data-bs-target="#inquiryModal">Inquire</a>
          </div>
          <div class="lonyo-header-menu">
            <nav class="navbar site-navbar justify-content-between">
              <!-- Brand Logo-->
              <!-- mobile menu trigger -->
              <button class="lonyo-menu-toggle d-inline-block d-lg-none">
                <span></span>
              </button>
              <!--/.Mobile Menu Hamburger Ends-->
            </nav>
          </div>
        </div>
      </div>
    </div>

  </header>
