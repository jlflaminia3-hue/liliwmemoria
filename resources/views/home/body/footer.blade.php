<footer class="lonyo-footer-section light-bg">
  <div class="container">
    <div class="lonyo-footer-one">
      <div class="row liliw-footer-grid">
        <div class="col-lg-4 col-md-6">
          <div class="lonyo-footer-textarea liliw-footer-brand">
            @include('home.body.brand')
            <p class="mb-3">Providing compassionate cemetery management services to help families honor and remember their loved ones with dignity.</p>
            <div class="liliw-footer-contact-info">
              <div class="liliw-footer-contact-item">
                <i class="ri-map-pin-line"></i>
                <span>Liliw, Laguna, Philippines</span>
              </div>
              <div class="liliw-footer-contact-item">
                <i class="ri-phone-line"></i>
                <span>(+63) 995 360 1357</span>
              </div>
              <div class="liliw-footer-contact-item">
                <i class="ri-mail-line"></i>
                <span>liliwmemoria@gmail.com</span>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-2 col-md-6">
          <div class="lonyo-footer-menu">
            <h4>Quick Links</h4>
            <ul>
              <li><a href="{{ route('about.page') }}">About Us</a></li>
              <li><a href="{{ route('pricing.page') }}">Pricing</a></li>
              <li><a href="{{ route('location.page') }}">Location</a></li>
              <li><a href="{{ url('/') }}#contact">Contact</a></li>
            </ul>
          </div>
        </div>

        {{-- <div class="col-lg-3 col-md-6">
          <div class="lonyo-footer-menu">
            <h4>Services</h4>
            <ul>
              <li><a href="{{ url('/') }}#contact">Burial Plots</a></li>
              <li><a href="{{ url('/') }}#contact">Memorial Services</a></li>
              <li><a href="{{ url('/') }}#contact">Interment</a></li>
              <li><a href="{{ url('/') }}#contact">Landscaping</a></li>
            </ul>
          </div>
        </div> --}}

        <div class="col-lg-3 col-md-6">
          <div class="lonyo-footer-menu">
            <h4>Connect With Us</h4>
            <p class="mb-3">Follow us on social media for updates and announcements.</p>
            <div class="lonyo-social-wrap">
              <ul class="liliw-footer-social">
                <li>
                  <a href="https://www.facebook.com/memorialparkofsansebastian" target="_blank" rel="noopener noreferrer">
                    <svg width="18" height="18" viewBox="0 0 9 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M2.61987 16.7464V9.37041H0.137695V6.49583H2.61987V4.37591C2.61987 1.91577 4.12245 0.576172 6.31707 0.576172C7.36832 0.576172 8.27181 0.654439 8.53511 0.689422V3.26042L7.01302 3.26111C5.81946 3.26111 5.58836 3.82827 5.58836 4.66054V6.49583H8.43488L8.06426 9.37041H5.58836V16.7464H2.61987Z" fill="currentColor" />
                    </svg>
                  </a>
                </li>
                    </svg>
                  </a>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
      <div class="lonyo-footer-shape"></div>
    </div>
    <div class="lonyo-footer-bottom-text liliw-footer-bottom-text">
      <p>&copy; <span id="current-year"></span> LiliwMemoria. All rights reserved.</p>
      <p>Compassionate memorial care for every family.</p>
    </div>
  </div>
</footer>

<style>
  .liliw-footer-grid {
    row-gap: 40px;
  }

  .liliw-footer-brand {
    max-width: 100%;
  }

  .liliw-footer-brand .liliwmemoria-brand {
    display: inline-flex;
    align-items: center;
    margin-bottom: 16px;
  }

  .liliw-footer-brand .liliwmemoria-brand img {
    height: 48px !important;
    width: auto;
  }

  .liliw-footer-contact-info {
    margin-top: 20px;
  }

  .liliw-footer-contact-item {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 12px;
    color: #4b5563;
    font-size: 0.95rem;
  }

  .liliw-footer-contact-item i {
    color: #142C14;
    font-size: 1.1rem;
    width: 20px;
    text-align: center;
  }

  .liliw-footer-menu h4 {
    font-size: 1.1rem;
    font-weight: 700;
    color: #142C14;
    margin-bottom: 20px;
  }

  .liliw-footer-menu ul {
    list-style: none;
    padding: 0;
    margin: 0;
  }

  .liliw-footer-menu ul li {
    margin-bottom: 12px;
  }

  .liliw-footer-menu ul li a {
    color: #4b5563;
    text-decoration: none;
    font-size: 0.95rem;
    transition: color 0.2s ease;
  }

  .liliw-footer-menu ul li a:hover {
    color: #142C14;
  }

  .liliw-footer-social {
    display: flex;
    gap: 12px;
    list-style: none;
    padding: 0;
    margin: 0;
  }

  .liliw-footer-social li {
    margin: 0;
  }

  .liliw-footer-social a {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    background: rgba(20, 44, 20, 0.08);
    border-radius: 10px;
    color: #142C14;
    transition: all 0.3s ease;
  }

  .liliw-footer-social a:hover {
    background: #142C14;
    color: #fff;
  }

  .liliw-footer-bottom-text {
    margin-top: 48px;
    padding-top: 24px;
    border-top: 1px solid #e5e7eb;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    text-align: left;
  }

  .liliw-footer-bottom-text p {
    margin: 0;
    color: #6b7280;
    font-size: 0.9rem;
  }

  @media (max-width: 991.98px) {
    .liliw-footer-grid > div {
      margin-bottom: 24px;
    }
  }

  @media (max-width: 767.98px) {
    .liliw-footer-brand .liliwmemoria-brand img {
      height: 40px !important;
    }

    .liliw-footer-bottom-text {
      flex-direction: column;
      text-align: center;
    }
  }
</style>
