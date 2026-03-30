<!DOCTYPE html>
<html lang="en">
    <head>

        <meta charset="utf-8" />
        <title>Forgot Password | LiliwMemoria - Admin Dashboard</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="Reset your LiliwMemoria password">
        <meta name="author" content="LiliwMemoria"/>
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />

        <!-- App favicon -->
        <link rel="shortcut icon" href="{{ asset('backend/assets/images/liliwmemoria-logo.png') }}">

        <!-- App css -->
        <link href="{{ asset('backend/assets/css/app.min.css') }}" rel="stylesheet" type="text/css" id="app-style" />
        <link href="{{ asset('theme-overrides.css') }}" rel="stylesheet" type="text/css" />

        <!-- Icons -->
        <link href="{{ asset('backend/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />

    </head>

    <body class="bg-white">
        <!-- Begin page -->
        <div class="account-page">
            <div class="container-fluid p-0">
                <div class="row align-items-center g-0">
                    <div class="col-xl-5">
                        <div class="row">
                            <div class="col-md-7 mx-auto">
                                <div class="mb-0 border-0 p-md-5 p-lg-0 p-4">
                                    <div class="mb-4 p-0">
                                        <a href="{{ url('/') }}" class="auth-logo">
                                            <img src="{{ asset('backend/assets/images/logo/liliwmemoria-logo.png') }}" alt="LiliwMemoria" class="mx-auto" height="32" />
                                        </a>
                                    </div>

                                    <div class="pt-0">
                                        <p class="text-muted mb-3">
                                            {{ __('Forgot your password? Enter your email and we’ll send you a reset link.') }}
                                        </p>

                                        @if (session('status'))
                                            <div class="alert alert-success" role="alert">
                                                {{ session('status') }}
                                            </div>
                                        @endif

                                        <form method="POST" action="{{ route('password.email') }}" class="my-4">
                                            @csrf

                                            <div class="form-group mb-3">
                                                <label for="email" class="form-label">{{ __('Email address') }}</label>
                                                <input class="form-control @error('email') is-invalid @enderror" type="email" id="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="Enter your email">
                                                @error('email')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="form-group mb-0 row">
                                                <div class="col-12">
                                                    <div class="d-grid">
                                                        <button class="btn btn-primary" type="submit">{{ __('Email Password Reset Link') }}</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>

                                        <div class="text-center text-muted mb-4">
                                            <p class="mb-0">Remembered your password?<a class='text-primary ms-2 fw-medium' href="{{ route('login') }}">Sign in</a></p>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-7">
                        <div class="account-page-bg liliwmemoria-hero-bg p-md-5 p-4">
                            <div class="text-center">
                                <div class="mx-auto" style="max-width: 560px;">
                                    <div class="liliwmemoria-auth-kicker">LiliwMemoria Admin</div>
                                    <h1 class="liliwmemoria-auth-title">Reset your password</h1>
                                    <p class="liliwmemoria-auth-subtitle">We’ll email you a secure link so you can set a new password.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- END wrapper -->

        <!-- Vendor -->
        <script src="{{ asset('backend/assets/libs/jquery/jquery.min.js') }}"></script>
        <script src="{{ asset('backend/assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('backend/assets/libs/simplebar/simplebar.min.js') }}"></script>
        <script src="{{ asset('backend/assets/libs/node-waves/waves.min.js') }}"></script>
        <script src="{{ asset('backend/assets/libs/waypoints/lib/jquery.waypoints.min.js') }}"></script>
        <script src="{{ asset('backend/assets/libs/jquery.counterup/jquery.counterup.min.js') }}"></script>
        <script src="{{ asset('backend/assets/libs/feather-icons/feather.min.js') }}"></script>

        <!-- App js-->
        <script src="{{ asset('backend/assets/js/app.js') }}"></script>

    </body>
</html>
