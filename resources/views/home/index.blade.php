@extends('home.home_master')
@section('home')

@include('home.layout.slider')



<div class="lonyo-content-shape1">
    <img src="{{ asset('frontend/assets/images/shape/shape1.svg') }}" alt="">
  </div>
  <div class="lonyo-section-padding2 position-relative">
    <div class="container">
      <div class="lonyo-section-title center">
        <p>Welcome to LiliwMemoria</p>
        <h2 class="lonyo-slide-title">Memorial Park of San Sebastian</h2>
      </div>
      <div class="row">
        <div class="col-xl-4 col-lg-6 col-md-6">
          <div class="lonyo-service-wrap light-bg" data-aos="fade-up" data-aos-duration="500">
            <div class="lonyo-service-title">
              <h4>BURIAL PLOTS</h4>
              {{-- <img src="{{ asset('frontend/assets/images/v1/feature1.svg') }}" alt=""> --}}
            </div>
            <div class="lonyo-service-data">
              <p>Find the perfect resting place for your loved ones in our serene memorial park.</p>
            </div>
          </div>
        </div>
        <div class="col-xl-4 col-lg-6 col-md-6">
          <div class="lonyo-service-wrap light-bg" data-aos="fade-up" data-aos-duration="700">
            <div class="lonyo-service-title">
              <h4>MAUSOLEUM</h4>
              {{-- <img src="{{ asset('frontend/assets/images/v1/feature2.svg') }}" alt=""> --}}
            </div>
            <div class="lonyo-service-data">
              <p>Provides a dignified and protected space for the remains of your loved ones.</p>
            </div>
          </div>
        </div>
        <div class="col-xl-4 col-lg-6 col-md-6">
          <div class="lonyo-service-wrap light-bg" data-aos="fade-up" data-aos-duration="900">
            <div class="lonyo-service-title">
              <h4>GARDEN LOTS</h4>
              {{-- <img src="{{ asset('frontend/assets/images/v1/feature3.svg') }}" alt=""> --}}
            </div>
            <div class="lonyo-service-data">
              <p>Enjoy a peaceful retreat in our beautifully landscaped garden plots.</p>
            </div>
          </div>
        </div>
        <div class="col-xl-4 col-lg-6 col-md-6">
          <div class="lonyo-service-wrap light-bg" data-aos="fade-up" data-aos-duration="500">
            <div class="lonyo-service-title">
              <h4>BACK OFFICE LOTS</h4>
              {{-- <img src="{{ asset('frontend/assets/images/v1/feature4.svg') }}" alt=""> --}}
            </div>
            <div class="lonyo-service-data">
              <p>Provides convenient storage and management solutions for back office needs.</p>
            </div>
          </div>
        </div>
        <div class="col-xl-4 col-lg-6 col-md-6">
          <div class="lonyo-service-wrap light-bg" data-aos="fade-up" data-aos-duration="700">
            <div class="lonyo-service-title">
              <h4>PHASES</h4>
              {{-- <img src="{{ asset('frontend/assets/images/v1/feature5.svg') }}" alt=""> --}}
            </div>
            <div class="lonyo-service-data">
              <p>Explore different phases of our memorial park development and plan for the future.</p>
            </div>
          </div>
        </div>
        <div class="col-xl-4 col-lg-6 col-md-6">
          <div class="lonyo-service-wrap light-bg" data-aos="fade-up" data-aos-duration="900">
            <div class="lonyo-service-title">
              <h4>LAWN LOTS</h4>
              {{-- <img src="{{ asset('frontend/assets/images/v1/feature6.svg') }}" alt=""> --}}
            </div>
            <div class="lonyo-service-data">
              <p>Provides a peaceful and well-maintained space for the final resting place of your loved ones.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="lonyo-feature-shape"></div>
  </div>
  <!-- end content -->

  <section class="lonyo-section-padding6">
    <div class="container">
      <div class="row">
        <div class="col-lg-5">
          <div class="lonyo-content-thumb" data-aos="fade-up" data-aos-duration="700">
            <img src="{{ asset('frontend/assets/images/v1/content-thumb.png') }}" alt="">
          </div>
        </div>
        <div class="col-lg-7 d-flex align-items-center">
          <div class="lonyo-default-content pl-50" data-aos="fade-up" data-aos-duration="700">
            <h2>It clarifies all strategic financial decisions</h2>
            <p class="data">With this tool, you can say goodbye to overspending, stay on track with your savings goals, and say goodbye to financial worries. Get ready for a clearer view of your finances like never before!</p>
            <div class="lonyo-faq-wrap1 mt-50">
              <div class="lonyo-faq-item open" data-aos="fade-up" data-aos-duration="500">
                <div class="lonyo-faq-header">
                  <h4>Real-Time Expense Tracking:</h4>
                  <div class="lonyo-active-icon">
                    <img class="plasicon" src="{{ asset('frontend/assets/images/v1/mynus.svg') }}" alt="">
                    <img class="mynusicon" src="{{ asset('frontend/assets/images/v1/plas.svg') }}" alt="">
                  </div>
                </div>
                <div class="lonyo-faq-body">
                  <p>Automatically and syncs with bank accounts and credit cards to provide instant updates on spending, helping users stay aware of their all daily transactions.</p>
                </div>
              </div>
              <div class="lonyo-faq-item" data-aos="fade-up" data-aos-duration="700">
                <div class="lonyo-faq-header">
                  <h4>Comprehensive Financial Overview:</h4>
                  <div class="lonyo-active-icon">
                    <img class="plasicon" src="{{ asset('frontend/assets/images/v1/mynus.svg') }}" alt="">
                    <img class="mynusicon" src="{{ asset('frontend/assets/images/v1/plas.svg') }}" alt="">
                  </div>
                </div>
                <div class="lonyo-faq-body">
                  <p>Automatically and syncs with bank accounts and credit cards to provide instant updates on spending, helping users stay aware of their all daily transactions.</p>
                </div>
              </div>
              <div class="lonyo-faq-item" data-aos="fade-up" data-aos-duration="900">
                <div class="lonyo-faq-header">
                  <h4>Stress-Reducing Automation:</h4>
                  <div class="lonyo-active-icon">
                    <img class="plasicon" src="{{ asset('frontend/assets/images/v1/mynus.svg') }}" alt="">
                    <img class="mynusicon" src="{{ asset('frontend/assets/images/v1/plas.svg') }}" alt="">
                  </div>
                </div>
                <div class="lonyo-faq-body">
                  <p>Automatically and syncs with bank accounts and credit cards to provide instant updates on spending, helping users stay aware of their all daily transactions.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- end content -->

  <div class="lonyo-section-padding4 position-relative">
    <div class="container">
      <div class="row">
        <div class="col-lg-5 order-lg-2">
          <div class="lonyo-content-thumb" data-aos="fade-up" data-aos-duration="700">
            <img src="{{ asset('frontend/assets/images/v1/content-thumb2.png') }}" alt="">
          </div>
        </div>
        <div class="col-lg-7 d-flex align-items-center">
          <div class="lonyo-default-content pr-50" data-aos="fade-right" data-aos-duration="700">
            <h2>Get all your financial updates in one place</h2>
            <p class="data">This feature ensures you can easily stay on top of your finances by consolidating all updates into a single dashboard.</p>
            <div class="mt-50">
              <ul class="tabs">
                <li class="active-tab">
                  <img src="{{ asset('frontend/assets/images/v1/tv.svg') }}" alt="">
                  <h4>Unified Dashboard</h4>
                </li>
                <li>
                  <img src="{{ asset('frontend/assets/images/v1/alerm.svg') }}" alt="">
                  <h4>Real-Time Updates</h4>
                </li>
              </ul>
              <ul class="tabs-content">
                <li>
                  View all your accounts, transactions & investments in one central location. See every credit & debit transaction as it happens across all your accounts. Get a complete view of your expenses with expense categories.
                </li>
                <li>
                  This feature ensures you can easily stay on top of your finances by consolidating all updates into a single dashboard.View all your accounts, transactions iew of your expenses with expense categories.
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="lonyo-content-shape2"></div>
  </div>
  <!-- end content -->

  <div class="lonyo-section-padding bg-heading position-relative sectionn">
    <div class="container">
      <div class="row">
        <div class="col-lg-5">
          <div class="lonyo-video-thumb">
            <img src="{{ asset('frontend/assets/images/v1/video-thumb.png') }}" alt="">
            <a class="play-btn video-init" href="https://www.youtube.com/watch?v=fgZc7mAYIY8">
              <img src="{{ asset('frontend/assets/images/v1/play-icon.svg') }}" alt="">
              <div class="waves wave-1"></div>
              <div class="waves wave-2"></div>
              <div class="waves wave-3"></div>
            </a>
          </div>
        </div>
        <div class="col-lg-7 d-flex align-items-center">
          <div class="lonyo-default-content lonyo-video-section pl-50" data-aos="fade-up" data-aos-duration="500">
            <h2>Its usability is simple and intuitive for users</h2>
            <p>It's a cloud-based accounting tool ideal for individuals & businesses to easily manage finances, invoices & payroll. Unlock the 3-step path to enhanced financial control. </p>
            <div class="mt-50" data-aos="fade-up" data-aos-duration="700">
              <a class="lonyo-default-btn video-btn" href="contact-us.html">Download the app</a>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-xl-4 col-md-6">
          <div class="lonyo-process-wrap" data-aos="fade-up" data-aos-duration="500">
            <div class="lonyo-process-number">
              <img src="{{ asset('frontend/assets/images/v1/n1.svg') }}" alt="">
            </div>
            <div class="lonyo-process-title">
              <h4>Connect Your Accounts</h4>
            </div>
            <div class="lonyo-process-data">
              <p>Link your bank, credit card or investment accounts to automatically track transactions and get a complete financial overview</p>
            </div>
          </div>
        </div>
        <div class="col-xl-4 col-md-6">
          <div class="lonyo-process-wrap" data-aos="fade-up" data-aos-duration="700">
            <div class="lonyo-process-number">
              <img src="{{ asset('frontend/assets/images/v1/n2.svg') }}" alt="">
            </div>
            <div class="lonyo-process-title">
              <h4>Set Budgets & Goals</h4>
            </div>
            <div class="lonyo-process-data">
              <p>Define your spending limits and savings goals for categories like groceries, bills or future investments to stay on track.</p>
            </div>
          </div>
        </div>
        <div class="col-xl-4 col-md-6">
          <div class="lonyo-process-wrap" data-aos="fade-up" data-aos-duration="900">
            <div class="lonyo-process-number">
              <img src="{{ asset('frontend/assets/images/v1/n3.svg') }}" alt="">
            </div>
            <div class="lonyo-process-title">
              <h4>Monitor & Automate</h4>
            </div>
            <div class="lonyo-process-data">
              <p>Check your financial dashboard for regular updates and set up automatic payments or savings to simplify management.</p>
            </div>
          </div>
        </div>
        <div class="border-bottom" data-aos="fade-up" data-aos-duration="500"></div>
      </div>
    </div>
  </div>
  <!-- end video -->

  <div class="lonyo-section-padding position-relative overflow-hidden">
    <div class="container">
      <div class="lonyo-section-title">
        <div class="row">
          <div class="col-xl-8 col-lg-8">
            <h2>Don't take our word for it, check user reviews</h2>
          </div>
          <div class="col-xl-4 col-lg-4 d-flex align-items-center justify-content-end">
            <div class="lonyo-title-btn">
              <a class="lonyo-default-btn t-btn" href="contact-us.html">Read Customer Stories</a>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="lonyo-testimonial-slider-init">
      <div class="lonyo-t-wrap wrap2 light-bg">
        <div class="lonyo-t-ratting">
          <img src="{{ asset('frontend/assets/images/shape/star.svg') }}" alt="">
        </div>
        <div class="lonyo-t-text">
          <p>"This app transformed my budgeting! It has been a clear view longer have to worry of my It has been success expenses and savings goals."</p>
        </div>
        <div class="lonyo-t-author">
          <div class="lonyo-t-author-thumb">
            <img src="{{ asset('frontend/assets/images/v1/img7.png') }}" alt="">
          </div>
          <div class="lonyo-t-author-data">
            <p>Liam Gallagher</p>
            <span>Teacher of Luxe Escapes Hotels</span>
          </div>
        </div>
      </div>
      <div class="lonyo-t-wrap wrap2 light-bg">
        <div class="lonyo-t-ratting">
          <img src="{{ asset('frontend/assets/images/shape/star.svg') }}" alt="">
        </div>
        <div class="lonyo-t-text">
          <p>“The interface is intuitive, and I love how syncs with my bank accounts. I no longer have to worry about manual tracking. Highly recommend!”</p>
        </div>
        <div class="lonyo-t-author">
          <div class="lonyo-t-author-thumb">
            <img src="{{ asset('frontend/assets/images/v1/img2.png') }}" alt="">
          </div>
          <div class="lonyo-t-author-data">
            <p>Michael Chen</p>
            <span>Founder of EcoChic Apparel</span>
          </div>
        </div>
      </div>
      <div class="lonyo-t-wrap wrap2 light-bg">
        <div class="lonyo-t-ratting">
          <img src="{{ asset('frontend/assets/images/shape/star.svg') }}" alt="">
        </div>
        <div class="lonyo-t-text">
          <p>“With this app, I’ve been able to stick to my budget and even save for a vacation.The budget alerts are a game changer!”</p>
        </div>
        <div class="lonyo-t-author">
          <div class="lonyo-t-author-thumb">
            <img src="{{ asset('frontend/assets/images/v1/img3.png') }}" alt="">
          </div>
          <div class="lonyo-t-author-data">
            <p>David Nguyen</p>
            <span>COO of Luxe Escapes Hotels</span>
          </div>
        </div>
      </div>
      <div class="lonyo-t-wrap wrap2 light-bg">
        <div class="lonyo-t-ratting">
          <img src="{{ asset('frontend/assets/images/shape/star.svg') }}" alt="">
        </div>
        <div class="lonyo-t-text">
          <p>"Having all my accounts in one place gives me complete overspending accounts control over my money. Highly recommend!"</p>
        </div>
        <div class="lonyo-t-author">
          <div class="lonyo-t-author-thumb">
            <img src="{{ asset('frontend/assets/images/v1/img5.png') }}" alt="">
          </div>
          <div class="lonyo-t-author-data">
            <p>Rachel Kim</p>
            <span>CEO of GreenLeaf Organics</span>
          </div>
        </div>
      </div>
      <div class="lonyo-t-wrap wrap2 light-bg">
        <div class="lonyo-t-ratting">
          <img src="{{ asset('frontend/assets/images/shape/star.svg') }}" alt="">
        </div>
        <div class="lonyo-t-text">
          <p>"Having all my accounts in one place gives me complete control over my money. So user-friendly and helpful! Highly recommend!"</p>
        </div>
        <div class="lonyo-t-author">
          <div class="lonyo-t-author-thumb">
            <img src="{{ asset('frontend/assets/images/v1/img6.png') }}" alt="">
          </div>
          <div class="lonyo-t-author-data">
            <p>Aisha Hassan</p>
            <span>CEO of RoyexLeaf Organics</span>
          </div>
        </div>
      </div>
    
    </div>
    <div class="lonyo-t-overlay2">
      <img src="{{ asset('frontend/assets/images/v2/overlay.png') }}" alt="">
    </div>
  </div>
  <!-- end testimonial -->

  <div class="lonyo-section-padding4">
    <div class="container">
      <div class="lonyo-section-title center">
        <h2>Find answers to all questions below</h2>
      </div>
      <div class="lonyo-faq-shape"></div>
      <div class="lonyo-faq-wrap1">
        <div class="lonyo-faq-item item2 open" data-aos="fade-up" data-aos-duration="500">
          <div class="lonyo-faq-header">
            <h4>Is my financial data safe and secure?</h4>
            <div class="lonyo-active-icon">
              <img class="plasicon" src="{{ asset('frontend/assets/images/v1/mynus.svg') }}" alt="">
              <img class="mynusicon" src="{{ asset('frontend/assets/images/v1/plas.svg') }}" alt="">
            </div>
          </div>
          <div class="lonyo-faq-body body2">
            <p>Yes, this finance apps use bank-level encryption, multi-factor authentication, and other security measures to protect your sensitive information.</p>
          </div>
        </div>
        <div class="lonyo-faq-item item2" data-aos="fade-up" data-aos-duration="700">
          <div class="lonyo-faq-header">
            <h4>Can I link multiple bank accounts and credit cards?</h4>
            <div class="lonyo-active-icon">
              <img class="plasicon" src="{{ asset('frontend/assets/images/v1/mynus.svg') }}" alt="">
              <img class="mynusicon" src="{{ asset('frontend/assets/images/v1/plas.svg') }}" alt="">
            </div>
          </div>
          <div class="lonyo-faq-body body2">
            <p>Yes, this finance apps use bank-level encryption, multi-factor authentication, and other security measures to protect your sensitive information.</p>
          </div>
        </div>
        <div class="lonyo-faq-item item2" data-aos="fade-up" data-aos-duration="900">
          <div class="lonyo-faq-header">
            <h4>How does the app help me stick to my budget?</h4>
            <div class="lonyo-active-icon">
              <img class="plasicon" src="{{ asset('frontend/assets/images/v1/mynus.svg') }}" alt="">
              <img class="mynusicon" src="{{ asset('frontend/assets/images/v1/plas.svg') }}" alt="">
            </div>
          </div>
          <div class="lonyo-faq-body body2">
            <p>Yes, this finance apps use bank-level encryption, multi-factor authentication, and other security measures to protect your sensitive information.</p>
          </div>
        </div>
        <div class="lonyo-faq-item item2" data-aos="fade-up" data-aos-duration="1100">
          <div class="lonyo-faq-header">
            <h4>Can I track my investments with the app?</h4>
            <div class="lonyo-active-icon">
              <img class="plasicon" src="{{ asset('frontend/assets/images/v1/mynus.svg') }}" alt="">
              <img class="mynusicon" src="{{ asset('frontend/assets/images/v1/plas.svg') }}" alt="">
            </div>
          </div>
          <div class="lonyo-faq-body body2">
            <p>Yes, this finance apps use bank-level encryption, multi-factor authentication, and other security measures to protect your sensitive information.</p>
          </div>
        </div>
        <div class="lonyo-faq-item item2" data-aos="fade-up" data-aos-duration="1300">
          <div class="lonyo-faq-header">
            <h4>Is the app free, or are there subscription fees?</h4>
            <div class="lonyo-active-icon">
              <img class="plasicon" src="{{ asset('frontend/assets/images/v1/mynus.svg') }}" alt="">
              <img class="mynusicon" src="{{ asset('frontend/assets/images/v1/plas.svg') }}" alt="">
            </div>
          </div>
          <div class="lonyo-faq-body body2">
            <p>Yes, this finance apps use bank-level encryption, multi-factor authentication, and other security measures to protect your sensitive information.</p>
          </div>
        </div>
      </div>
      <div class="faq-btn" data-aos="fade-up" data-aos-duration="700">
        <a class="lonyo-default-btn faq-btn2" href="faq.html">Can't find your answer</a>
      </div>
    </div>
  </div>

  <!-- end faq -->

  <section class="lonyo-cta-section bg-heading">
    <div class="container">
      <div class="row">
        <div class="col-lg-6">
          <div class="lonyo-cta-thumb" data-aos="fade-up" data-aos-duration="500">
            <img src="{{ asset('frontend/assets/images/v1/cta-thumb.png') }}" alt="">
          </div>
        </div>
        <div class="col-lg-6">
          <div class="lonyo-default-content lonyo-cta-wrap" data-aos="fade-up" data-aos-duration="700">
            <h2>Start a new level of money management</h2>
            <p>Our finance apps and software are powerful tools for managing personal or business finances, helping users stay organized, track financial health, and make informed decisions.</p>
            <div class="lonyo-cta-info mt-50" data-aos="fade-up" data-aos-duration="900">
              <ul>
                <li>
                  <a href="https://www.apple.com/app-store/"><img src="{{ asset('frontend/assets/images/v1/app-store.svg') }}" alt=""></a>
                </li>
                <li>
                  <a href="https://playstore.com/"><img src="{{ asset('frontend/assets/images/v1/play-store.svg') }}" alt=""></a>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>



@endsection
