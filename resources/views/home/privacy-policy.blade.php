@extends('home.home_master')

@section('home')
<section class="liliwmemoria-policy-page">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-9">
        <div class="liliwmemoria-policy-card">
          <h1>Privacy Policy</h1>
          <p>
            Liliw Memoria respects your privacy and is committed to protecting the personal information you share with us.
            This page explains what information we collect, how we use it, and how we safeguard it.
          </p>

          <h2>Information We Collect</h2>
          <p>
            We may collect the information you provide through our inquiry forms, including your name, email address,
            contact number, subject, inquiry type, and message details.
          </p>

          <h2>How We Use Your Information</h2>
          <p>
            We use your information to respond to your inquiries, provide assistance regarding our memorial park services,
            process requests, and improve our customer support experience.
          </p>

          <h2>Protection of Information</h2>
          <p>
            We take reasonable administrative and technical measures to protect your information from unauthorized access,
            misuse, disclosure, or loss.
          </p>

          <h2>Sharing of Information</h2>
          <p>
            Your personal information will only be shared when necessary for legitimate business operations, legal compliance,
            or when required to respond to your request.
          </p>

          <h2>Your Consent</h2>
          <p>
            By submitting your information through our website, you acknowledge that you have read and understood this
            privacy policy and consent to the collection and use of your information for inquiry handling.
          </p>

          <h2>Contact Us</h2>
          <p>
            If you have questions about this privacy policy, you may reach us through our <a href="{{ route('contact.page') }}">inquiry page</a>.
          </p>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection
