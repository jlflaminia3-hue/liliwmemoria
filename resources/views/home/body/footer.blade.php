<footer class="lonyo-footer-section liliw-footer liliw-footer--dark">
  <div class="container">
    <div class="liliw-footer__top">
      <div class="row liliw-footer-grid">
        <div class="col-lg-3 col-md-6">
          <div class="liliw-footer-brand">
            <a href="{{ url('/') }}" class="liliw-footer-logo">
              <img src="{{ asset('frontend/assets/images/logo/liliw-square.png') }}" alt="LiliwMemoria logo">
            </a>
          </div>
        </div>

        <div class="col-lg-3 col-md-6">
          <div class="liliw-footer-block">
            <h4 class="liliw-footer-title">Contact Us</h4>
            <div class="liliw-footer-list">
              <div class="liliw-footer-item">Memorial Park of San Sebastian</div>
              <div class="liliw-footer-item">(+63) 995 360 1357</div>
            </div>

            <div class="liliw-footer-social-row">
              <a href="https://www.facebook.com/memorialparkofsansebastian" target="_blank" rel="noopener noreferrer" class="liliw-footer-social">
                <svg width="18" height="18" viewBox="0 0 9 17" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                  <path d="M2.61987 16.7464V9.37041H0.137695V6.49583H2.61987V4.37591C2.61987 1.91577 4.12245 0.576172 6.31707 0.576172C7.36832 0.576172 8.27181 0.654439 8.53511 0.689422V3.26042L7.01302 3.26111C5.81946 3.26111 5.58836 3.82827 5.58836 4.66054V6.49583H8.43488L8.06426 9.37041H5.58836V16.7464H2.61987Z" fill="currentColor" />
                </svg>
                <span class="visuallyhidden">Facebook</span>
              </a>
            </div>
          </div>
        </div>

        <div class="col-lg-3 col-md-6">
          <div class="liliw-footer-block">
            <h4 class="liliw-footer-title">Office Schedule</h4>
            <div class="liliw-footer-list">
              <div class="liliw-footer-item">7:30am – 5:00pm (Mon–Fri)</div>
              <div class="liliw-footer-item liliw-footer-muted">Liliw, Laguna, Philippines</div>
            </div>

            <a
              href="#"
              class="liliw-footer-btn liliw-footer-btn--primary liliwmemoria-inquiry-trigger"
              data-bs-toggle="modal"
              data-bs-target="#inquiryModal"
            >Schedule a Visit</a>
          </div>
        </div>

        <div class="col-lg-3 col-md-6">
          <div class="liliw-footer-block">
            <h4 class="liliw-footer-title">Email Us</h4>
            <div class="liliw-footer-list">
              <div class="liliw-footer-item">memorialparkofsansebastian@gmail.com</div>
              <div class="liliw-footer-item"><span class="liliw-footer-muted">Inquiries:</span> liliwmemoria@gmail.com</div>
            </div>

            <a href="{{ route('services.page') }}" class="liliw-footer-btn liliw-footer-btn--accent">Inquire Now</a>
          </div>
        </div>
      </div>
    </div>

    <div class="liliw-footer__bottom">
      <p>&copy; <span id="current-year"></span> LiliwMemoria. All rights reserved.</p>
      <p>Compassionate memorial care for every family.</p>
    </div>
  </div>
</footer>

<style>
  .liliw-footer {
    background: #142c14;
    color: rgba(255, 255, 255, 0.92);
  }

  .liliw-footer__top {
    padding: 0 0 34px;
  }

  .liliw-footer-grid {
    row-gap: 34px;
  }

  .liliw-footer-logo {
    display: inline-flex;
    align-items: center;
    margin-bottom: 18px;
    text-decoration: none;
  }

  .liliw-footer-logo img {
    height: 158px !important;
    width: auto;
    object-fit: contain;
  }

  .liliw-footer-muted {
    color: rgba(255, 255, 255, 0.72);
  }

  .liliw-footer-title {
    font-size: 1.1rem;
    font-weight: 800;
    color: rgba(255, 255, 255, 0.96);
    margin-bottom: 18px;
    letter-spacing: 0.02em;
  }

  .liliw-footer-list {
    display: grid;
    gap: 12px;
  }

  .liliw-footer-item {
    font-size: 0.95rem;
    line-height: 1.55;
  }

  .liliw-footer-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-top: 18px;
    padding: 12px 18px;
    border-radius: 4px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    font-size: 12px;
    text-decoration: none;
    border: 1px solid transparent;
    transition: all 0.2s ease;
  }

  .liliw-footer-btn--primary {
    background: rgba(255, 255, 255, 0.16);
    border-color: rgba(255, 255, 255, 0.26);
    color: rgba(255, 255, 255, 0.95) !important;
  }

  .liliw-footer-btn--primary:hover,
  .liliw-footer-btn--primary:focus {
    background: rgba(255, 255, 255, 0.24);
    border-color: rgba(255, 255, 255, 0.36);
    color: #ffffff !important;
  }

  .liliw-footer-btn--accent {
    background: #7fe39c;
    color: #0a180a !important;
  }

  .liliw-footer-btn--accent:hover,
  .liliw-footer-btn--accent:focus {
    background: #5fd883;
    color: #061006 !important;
  }

  .liliw-footer-btn--ghost {
    background: transparent;
    border-color: rgba(255, 255, 255, 0.28);
    color: rgba(255, 255, 255, 0.9) !important;
  }

  .liliw-footer-btn--ghost:hover,
  .liliw-footer-btn--ghost:focus {
    background: rgba(255, 255, 255, 0.12);
    border-color: rgba(255, 255, 255, 0.36);
    color: #ffffff !important;
  }

  .liliw-footer-social-row {
    display: flex;
    gap: 12px;
    margin-top: 16px;
  }

  .liliw-footer-social {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: rgba(255, 255, 255, 0.12);
    color: rgba(255, 255, 255, 0.92);
    text-decoration: none;
    transition: all 0.2s ease;
  }

  .liliw-footer-social:hover,
  .liliw-footer-social:focus {
    background: rgba(255, 255, 255, 0.22);
    color: #ffffff;
  }

  .liliw-footer__bottom {
    padding: 18px 0;
    border-top: 1px solid rgba(255, 255, 255, 0.14);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    font-size: 0.9rem;
  }

  .liliw-footer__bottom p {
    margin: 0;
    color: rgba(255, 255, 255, 0.72);
  }

  @media (max-width: 767.98px) {
    .liliw-footer__top {
      padding: 46px 0 26px;
    }

    .liliw-footer-logo img {
      height: 40px !important;
    }

    .liliw-footer__bottom {
      flex-direction: column;
      text-align: center;
    }
  }
</style>
