{{-- resources/views/auth/register.blade.php --}}
<!DOCTYPE html>
<html lang="en" dir="ltr"
      data-nav-layout="vertical"
      data-vertical-style="overlay"
      data-theme-mode="light"
      data-header-styles="light"
      data-menu-styles="light"
      data-toggled="close">

<head>
    <meta charset="UTF-8">
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Sign Up</title>

    {{-- Main Theme Js --}}
    <script src="{{ asset('assets/js/authentication-main.js') }}"></script>

    {{-- CSS --}}
    <link id="style" href="{{ asset('assets/libs/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/styles.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet">
</head>

<body>

    {{-- (Optional) Switcher: kalau nak, include partial --}}
    {{-- @include('partials.switcher') --}}

    <div class="container-lg">
        <div class="row justify-content-center align-items-center authentication authentication-basic h-100">
            <div class="col-xxl-4 col-xl-5 col-lg-5 col-md-6 col-sm-8 col-12">
                <div class="my-5 d-flex justify-content-center">
                    <a href="{{ url('/') }}">
                        <img src="{{ asset('assets/images/brand-logos/desktop-logo.png') }}" alt="logo" class="desktop-logo">
                        <img src="{{ asset('assets/images/brand-logos/desktop-dark.png') }}" alt="logo" class="desktop-dark">
                    </a>
                </div>

                <div class="card custom-card">
                    <div class="card-body p-5">
                        <p class="h5 fw-semibold mb-2 text-center">Sign Up</p>
                        <p class="mb-4 text-muted op-7 fw-normal text-center">Welcome & Join us by creating a free account !</p>

                        {{-- ✅ Laravel Register Form --}}
                        <form method="POST" action="{{ route('register') }}">
                            @csrf

                            {{-- Name (Laravel default guna 1 field "name") --}}
                            <div class="mb-3">
                                <label class="form-label text-default">Full Name</label>
                                <input type="text"
                                       name="name"
                                       value="{{ old('name') }}"
                                       class="form-control form-control-lg @error('name') is-invalid @enderror"
                                       placeholder="full name" required autofocus>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Email --}}
                            <div class="mb-3">
                                <label class="form-label text-default">Email</label>
                                <input type="email"
                                       name="email"
                                       value="{{ old('email') }}"
                                       class="form-control form-control-lg @error('email') is-invalid @enderror"
                                       placeholder="email" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Password --}}
                            <div class="mb-3">
                                <label class="form-label text-default">Password</label>
                                <div class="input-group">
                                    <input type="password"
                                           name="password"
                                           id="signup-password"
                                           class="form-control form-control-lg @error('password') is-invalid @enderror"
                                           placeholder="password" required>
                                    <button class="btn btn-light" onclick="createpassword('signup-password',this)" type="button">
                                        <i class="ri-eye-off-line align-middle"></i>
                                    </button>
                                    @error('password')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Confirm Password --}}
                            <div class="mb-2">
                                <label class="form-label text-default">Confirm Password</label>
                                <div class="input-group">
                                    <input type="password"
                                           name="password_confirmation"
                                           id="signup-confirmpassword"
                                           class="form-control form-control-lg"
                                           placeholder="confirm password" required>
                                    <button class="btn btn-light" onclick="createpassword('signup-confirmpassword',this)" type="button">
                                        <i class="ri-eye-off-line align-middle"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="form-check mt-3">
                                <input class="form-check-input" type="checkbox" id="defaultCheck1" required>
                                <label class="form-check-label text-muted fw-normal" for="defaultCheck1">
                                    By creating a account you agree to our
                                    <a href="#" class="text-success"><u>Terms & Conditions</u></a> and
                                    <a href="#" class="text-success"><u>Privacy Policy</u></a>
                                </label>
                            </div>

                            <div class="d-grid mt-3">
                                <button class="btn btn-lg btn-primary" type="submit">Create Account</button>
                            </div>
                        </form>

                        <div class="text-center">
                            <p class="fs-12 text-muted mt-3">
                                Already have an account?
                                <a href="{{ route('login') }}" class="text-primary">Sign In</a>
                            </p>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- JS --}}
    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/show-password.js') }}"></script>
</body>
</html>