<header class="site-header lonyo-header-section light-bg" id="sticky-menu">
    <div class="container">
      <div class="row gx-3 align-items-center justify-content-between">
        <div class="col-8 col-sm-auto ">
          <div class="header-logo1 ">
            @include('home.body.brand')
          </div>
        </div>
        <div class="col">
          <div class="lonyo-main-menu-item">
            <nav class="main-menu menu-style1 d-none d-lg-block menu-left">
              <ul>
                {{-- <li class="menu-item-has-children"> --}}
                <li class="about">
                  <a href="#">About</a>           
                </li>
                <li class="services">
                  <a href="#">Services</a>
                </li>
                <li class="galleries">
                  <a href="#">Galleries</a>
                </li>
                <li class="location">
                  <a href="#">Location</a>
                </li>
                <li>
                  <a href="contact-us.html">Contact</a>
                </li>
              </ul>
            </nav>
          </div>
        </div>
        <div class="col-auto d-flex align-items-center">
          <div class="lonyo-header-info-wraper2">
            <div class="lonyo-header-info-content">
              <ul>
                <li><a href="sign-in.html">Log in</a></li>
              </ul>
            </div>
            <a class="lonyo-default-btn lonyo-header-btn" href="conact-us.html">Inquire</a>
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
