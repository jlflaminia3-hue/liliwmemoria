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
    <div class="row align-items-center g-5">
      <div class="col-lg-6">
        <div class="lonyo-default-content" data-aos="fade-up" data-aos-duration="700">
          <h2>Transparent and Affordable Memorial Services</h2>
          <p>
            At Liliw Memoria, we believe in providing clear and honest pricing for all our memorial park services. 
            Our packages are designed to accommodate various needs and budgets, ensuring that every family can honor 
            their loved ones with dignity and respect.
          </p>
          <p>
            We offer flexible payment options and customized packages to suit your specific requirements. 
            Contact us today for a personalized quote and consultation.
          </p>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="liliwmemoria-pricing-image" data-aos="fade-up" data-aos-duration="700">
          <img src="{{ asset('frontend/assets/images/plan.jpg') }}" alt="Memorial Park Plan" class="img-fluid rounded-3 shadow">
        </div>
      </div>
    </div>
  </div>
</section>

<section class="lonyo-section-padding pt-0">
  <div class="container">
    <div class="liliwmemoria-section-header text-center" data-aos="fade-up" data-aos-duration="700">
      <h2>Our Packages</h2>
      <p>Choose the package that best fits your family's needs</p>
    </div>
    <div class="row g-4 justify-content-center mt-4">
      <div class="col-lg-4 col-md-6">
        <div class="liliwmemoria-pricing-card" data-aos="fade-up" data-aos-duration="700">
          <div class="liliwmemoria-pricing-card-header">
            <h3>Lot Only</h3>
            <p>Perfect for those who prefer outside services</p>
          </div>
          <div class="liliwmemoria-pricing-card-price">
            <span class="currency">₱</span>
            <span class="amount">60,000</span>
            <span class="period">starting</span>
          </div>
          <ul class="liliwmemoria-pricing-card-features">
            <li class="included"><i class="ri-check-line"></i> Lot space allocation</li>
            <li class="included"><i class="ri-check-line"></i> Basic plot preparation</li>
              {{-- <li class="included"><i class="ri-check-line"></i> 25-year lease agreement</li> --}}
            <li class="included"><i class="ri-check-line"></i> Ground maintenance</li>
            <li class="excluded"><i class="ri-close-line"></i> Interment services</li>
            <li class="excluded"><i class="ri-close-line"></i> Memorial marker</li>
          </ul>
          <a href="#" class="liliwmemoria-pricing-card-btn liliwmemoria-inquiry-trigger" data-bs-toggle="modal" data-bs-target="#inquiryModal">Inquire Now</a>
        </div>
      </div>
      <div class="col-lg-4 col-md-6">
        <div class="liliwmemoria-pricing-card liliwmemoria-pricing-card--featured" data-aos="fade-up" data-aos-duration="700">
          <div class="liliwmemoria-pricing-badge">Most Popular</div>
          <div class="liliwmemoria-pricing-card-header">
            <h3>Standard Package</h3>
            <p>Complete memorial services included</p>
          </div>
          <div class="liliwmemoria-pricing-card-price">
            <span class="currency">₱</span>
            <span class="amount">75,000</span>
            <span class="period">starting</span>
          </div>
          <ul class="liliwmemoria-pricing-card-features">
            <li class="included"><i class="ri-check-line"></i> Lot space allocation</li>
            <li class="included"><i class="ri-check-line"></i> Full plot preparation</li>
            {{-- <li class="included"><i class="ri-check-line"></i> 25-year lease agreement</li> --}}
            <li class="included"><i class="ri-check-line"></i> Ground maintenance</li>
            <li class="included"><i class="ri-check-line"></i> Basic interment services</li>
            <li class="included"><i class="ri-check-line"></i> Standard memorial marker</li>
          </ul>
          <a href="#" class="liliwmemoria-pricing-card-btn liliwmemoria-inquiry-trigger" data-bs-toggle="modal" data-bs-target="#inquiryModal">Inquire Now</a>
        </div>
      </div>

    </div>
  </div>
</section>

<section class="lonyo-section-padding pt-0">
  <div class="container">
    <div class="liliwmemoria-section-header text-center" data-aos="fade-up" data-aos-duration="700">
      <h2>Additional Services</h2>
      <p>Customize your memorial experience with our optional services</p>
    </div>
    <div class="row g-4 mt-4">
      <div class="col-lg-4 col-md-6">
        <div class="liliwmemoria-service-card text-center" data-aos="fade-up" data-aos-duration="700">
          <div class="liliwmemoria-service-icon">
            <i class="ri-cemetery-line"></i>
          </div>
          <h3>Memorial Markers</h3>
          <p>Granite, marble, and bronze markers available in various designs and sizes.</p>
          <span class="liliwmemoria-price-tag">₱5,000 - ₱25,000</span>
        </div>
      </div>
      <div class="col-lg-4 col-md-6">
        <div class="liliwmemoria-service-card text-center" data-aos="fade-up" data-aos-duration="700">
          <div class="liliwmemoria-service-icon">
            <i class="ri-caravan-line"></i>
          </div>
          <h3>Exhumation Services</h3>
          <p>Professional exhumation and re-interment services when needed.</p>
          <span class="liliwmemoria-price-tag">₱8,000 - ₱15,000</span>
        </div>
      </div>
      <div class="col-lg-4 col-md-6">
        <div class="liliwmemoria-service-card text-center" data-aos="fade-up" data-aos-duration="700">
          <div class="liliwmemoria-service-icon">
            <i class="ri-leaf-line"></i>
          </div>
          <h3>Landscaping</h3>
          <p>Floral arrangements and landscaping services for gravesites.</p>
          <span class="liliwmemoria-price-tag">₱2,000 - ₱10,000</span>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="lonyo-section-padding pt-0">
  <div class="container">
    <div class="liliwmemoria-section-header text-center" data-aos="fade-up" data-aos-duration="700">
      <h2>Payment Options</h2>
      <p>Flexible payment plans to suit your family's financial needs</p>
    </div>
    <div class="row g-4 mt-4 justify-content-center">
      <div class="col-lg-5">
        <div class="liliwmemoria-payment-card" data-aos="fade-up" data-aos-duration="700">
          <div class="liliwmemoria-payment-icon">
            <i class="ri-wallet-3-line"></i>
          </div>
          <h3>Full Payment</h3>
          <p>Pay in full and receive a <strong>5% discount</strong> on the total package price.</p>
        </div>
      </div>
      <div class="col-lg-5">
        <div class="liliwmemoria-payment-card" data-aos="fade-up" data-aos-duration="700">
          <div class="liliwmemoria-payment-icon">
            <i class="ri-calendar-check-line"></i>
          </div>
          <h3>Installment Plan</h3>
          <p>Choose from <strong>12, 18, or 24-month</strong> installment plans with competitive interest rates.</p>
        </div>
      </div>
    </div>
    <div class="text-center mt-5" data-aos="fade-up" data-aos-duration="700">
      <a href="#" class="lonyo-default-btn hero-btn" data-bs-toggle="modal" data-bs-target="#inquiryModal">Get a Custom Quote</a>
    </div>
      {{-- <div class="lonyo-title-btn" data-aos="fade-up" data-aos-duration="900">
        <a class="lonyo-default-btn hero-btn" href="#" data-bs-toggle="modal" data-bs-target="#appointmentModal">Get a Custom Quote</a>
      </div> --}}

  </div>
</section>

@include('home.layout.slider2')
@include('home.layout.slider4')
@endsection

@push('styles')
<style>
.liliwmemoria-pricing-image {
    position: relative;
    border-radius: 16px;
    overflow: hidden;
}

.liliwmemoria-pricing-image img {
    width: 100%;
    height: auto;
    display: block;
}

.liliwmemoria-section-header h2 {
    font-size: 2rem;
    font-weight: 700;
    color: #142C14;
    margin-bottom: 0.5rem;
}

.liliwmemoria-section-header p {
    color: #6b7280;
    font-size: 1.1rem;
}

.liliwmemoria-pricing-card {
    background: #fff;
    border-radius: 16px;
    padding: 2rem;
    border: 1px solid #e5e7eb;
    transition: all 0.3s ease;
    position: relative;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.liliwmemoria-pricing-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(20, 44, 20, 0.12);
}

.liliwmemoria-pricing-card--featured {
    background: linear-gradient(135deg, #142C14 0%, #1a3a1a 100%);
    border: none;
    color: #fff;
}

.liliwmemoria-pricing-card--featured .liliwmemoria-pricing-card-header p,
.liliwmemoria-pricing-card--featured .liliwmemoria-pricing-card-features li {
    color: rgba(255, 255, 255, 0.85);
}

.liliwmemoria-pricing-card--featured .liliwmemoria-pricing-card-header h3 {
    color: #fff;
}

.liliwmemoria-pricing-badge {
    position: absolute;
    top: -12px;
    left: 50%;
    transform: translateX(-50%);
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: #fff;
    padding: 0.35rem 1.25rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.liliwmemoria-pricing-card-header {
    text-align: center;
    margin-bottom: 1.5rem;
    padding-top: 0.5rem;
}

.liliwmemoria-pricing-card-header h3 {
    font-size: 1.5rem;
    font-weight: 700;
    color: #142C14;
    margin-bottom: 0.5rem;
}

.liliwmemoria-pricing-card-header p {
    color: #6b7280;
    font-size: 0.9rem;
    margin: 0;
}

.liliwmemoria-pricing-card-price {
    text-align: center;
    margin-bottom: 1.5rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid #e5e7eb;
}

.liliwmemoria-pricing-card--featured .liliwmemoria-pricing-card-price {
    border-bottom-color: rgba(255, 255, 255, 0.2);
}

.liliwmemoria-pricing-card-price .currency {
    font-size: 1.5rem;
    font-weight: 600;
    vertical-align: top;
}

.liliwmemoria-pricing-card-price .amount {
    font-size: 3rem;
    font-weight: 800;
    line-height: 1;
}

.liliwmemoria-pricing-card-price .period {
    display: block;
    font-size: 0.85rem;
    color: #9ca3af;
    margin-top: 0.25rem;
}

.liliwmemoria-pricing-card--featured .liliwmemoria-pricing-card-price .period {
    color: rgba(255, 255, 255, 0.6);
}

.liliwmemoria-pricing-card-features {
    list-style: none;
    padding: 0;
    margin: 0 0 1.5rem 0;
    flex-grow: 1;
}

.liliwmemoria-pricing-card-features li {
    padding: 0.6rem 0;
    font-size: 0.95rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.liliwmemoria-pricing-card-features li.included {
    color: #374151;
}

.liliwmemoria-pricing-card-features li.included i {
    color: #10b981;
    font-size: 1.1rem;
}

.liliwmemoria-pricing-card-features li.excluded {
    color: #9ca3af;
}

.liliwmemoria-pricing-card-features li.excluded i {
    color: #d1d5db;
    font-size: 1.1rem;
}

.liliwmemoria-pricing-card-btn {
    display: block;
    width: 100%;
    padding: 0.875rem 1.5rem;
    text-align: center;
    background: #142C14;
    color: #fff;
    border-radius: 10px;
    font-weight: 600;
    transition: all 0.3s ease;
    text-decoration: none;
    margin-top: auto;
}

.liliwmemoria-pricing-card-btn:hover {
    background: #1a3a1a;
    color: #fff;
}

.liliwmemoria-service-card {
    background: #fff;
    border-radius: 16px;
    padding: 2rem;
    border: 1px solid #e5e7eb;
    transition: all 0.3s ease;
    height: 100%;
}

.liliwmemoria-service-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px rgba(20, 44, 20, 0.08);
}

.liliwmemoria-service-icon {
    width: 70px;
    height: 70px;
    background: linear-gradient(135deg, #142C14 0%, #2d5a27 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.25rem;
}

.liliwmemoria-service-icon i {
    font-size: 2rem;
    color: #fff;
}

.liliwmemoria-service-card h3 {
    font-size: 1.2rem;
    font-weight: 700;
    color: #142C14;
    margin-bottom: 0.75rem;
}

.liliwmemoria-service-card p {
    color: #6b7280;
    font-size: 0.95rem;
    margin-bottom: 1rem;
}

.liliwmemoria-price-tag {
    display: inline-block;
    font-size: 1.1rem;
    font-weight: 700;
    color: #10b981;
    background: rgba(16, 185, 129, 0.1);
    padding: 0.5rem 1rem;
    border-radius: 8px;
}

.liliwmemoria-payment-card {
    background: #fff;
    border-radius: 16px;
    padding: 2rem;
    border: 1px solid #e5e7eb;
    display: flex;
    align-items: flex-start;
    gap: 1.5rem;
    transition: all 0.3s ease;
}

.liliwmemoria-payment-card:hover {
    border-color: #142C14;
    box-shadow: 0 8px 24px rgba(20, 44, 20, 0.1);
}

.liliwmemoria-payment-icon {
    width: 56px;
    height: 56px;
    min-width: 56px;
    background: rgba(20, 44, 20, 0.1);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.liliwmemoria-payment-icon i {
    font-size: 1.75rem;
    color: #142C14;
}

.liliwmemoria-payment-card h3 {
    font-size: 1.2rem;
    font-weight: 700;
    color: #142C14;
    margin-bottom: 0.5rem;
}

.liliwmemoria-payment-card p {
    color: #6b7280;
    font-size: 0.95rem;
    margin: 0;
}
</style>
@endpush
