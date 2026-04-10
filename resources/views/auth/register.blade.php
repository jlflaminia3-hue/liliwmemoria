<!DOCTYPE html>
<html lang="en">
    <head>

        <meta charset="utf-8" />
        <title>Register | LiliwMemoria - Admin Dashboard</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="Create your LiliwMemoria admin account">
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
                                        <form method="POST" action="{{ route('register') }}" class="my-4">
                                            @csrf

                                            <div class="form-group mb-3">
                                                <label for="name" class="form-label">{{ __('Name') }}</label>
                                                <input class="form-control @error('name') is-invalid @enderror" type="text" id="name" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="Enter your name">
                                                @error('name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="email" class="form-label">{{ __('Email address') }}</label>
                                                <input class="form-control @error('email') is-invalid @enderror" type="email" id="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="Enter your email">
                                                @error('email')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="password" class="form-label">{{ __('Password') }}</label>
                                                <input class="form-control @error('password') is-invalid @enderror" type="password" id="password" name="password" required autocomplete="new-password" placeholder="Create a password">
                                                @error('password')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="password_confirmation" class="form-label">{{ __('Confirm password') }}</label>
                                                <input class="form-control" type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password" placeholder="Confirm your password">
                                            </div>

                                            <div class="form-group mb-0 row">
                                                <div class="col-12">
                                                    <div class="d-grid">
                                                        <button class="btn btn-primary" type="submit">{{ __('Create account') }}</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>

                                        <div class="text-center text-muted mb-4">
                                            <p class="mb-0">Already have an account?<a class='text-primary ms-2 fw-medium' href="{{ route('login') }}">Sign in</a></p>
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
                                    <h1 class="liliwmemoria-auth-title">Create your account</h1>
                                    <p class="liliwmemoria-auth-subtitle">Join the team and start managing the cemetery dashboard</p>
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
