<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'E-Pusara') }} - Daftar Akaun</title>

    <link id="style" href="{{ asset('assets/libs/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/styles.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet">

    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, rgba(15,118,110,0.08), rgba(15,23,42,0.06));
            font-family: 'Figtree', sans-serif;
        }

        .auth-wrapper {
            min-height: 100vh;
        }

        .brand-box {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .brand-box img {
            height: 72px;
            width: auto;
            display: block;
            margin: 0 auto 12px;
        }

        .brand-title {
            font-size: 2rem;
            font-weight: 800;
            color: #0f766e;
            margin-bottom: 0.25rem;
        }

        .brand-subtitle {
            color: #64748b;
            font-size: 0.95rem;
            margin-bottom: 0;
        }

        .auth-card {
            border: none;
            border-radius: 24px;
            box-shadow: 0 20px 50px rgba(15, 23, 42, 0.08);
            overflow: hidden;
            background: #fff;
        }

        .auth-card .card-body {
            padding: 2.5rem;
        }

        .auth-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 0.35rem;
            text-align: center;
        }

        .auth-text {
            text-align: center;
            color: #64748b;
            margin-bottom: 1.75rem;
        }

        .form-label {
            font-weight: 600;
            color: #334155;
            margin-bottom: 0.5rem;
        }

        .form-control.form-control-lg {
            border-radius: 14px;
            padding: 0.85rem 1rem;
            border: 1px solid #dbe4ee;
        }

        .form-control.form-control-lg:focus {
            border-color: #0f766e;
            box-shadow: 0 0 0 0.15rem rgba(15, 118, 110, 0.12);
        }

        .input-group .form-control {
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
        }

        .toggle-password {
            border: 1px solid #dbe4ee;
            border-left: 0;
            border-top-right-radius: 14px !important;
            border-bottom-right-radius: 14px !important;
            background: #f8fafc;
            min-width: 58px;
        }

        .toggle-password:hover {
            background: #eef2f7;
        }

        .btn-primary {
            background-color: #0f766e;
            border-color: #0f766e;
            border-radius: 14px;
            font-weight: 600;
            padding: 0.9rem 1rem;
        }

        .btn-primary:hover {
            background-color: #0d5f59;
            border-color: #0d5f59;
        }

        .form-check-label {
            color: #64748b;
            font-size: 0.92rem;
            line-height: 1.6;
        }

        .form-check-label a {
            color: #0f766e;
            text-decoration: none;
            font-weight: 600;
        }

        .form-check-label a:hover {
            text-decoration: underline;
        }

        .login-text {
            text-align: center;
            color: #64748b;
            margin-top: 1.25rem;
            margin-bottom: 0;
        }

        .login-text a {
            color: #0f766e;
            font-weight: 600;
            text-decoration: none;
        }

        .login-text a:hover {
            text-decoration: underline;
        }

        .back-home {
            text-align: center;
            margin-top: 1rem;
        }

        .back-home a {
            color: #64748b;
            text-decoration: none;
            font-size: 0.92rem;
        }

        .back-home a:hover {
            color: #0f766e;
        }

        .invalid-feedback {
            display: block;
        }

        @media (max-width: 576px) {
            .auth-card .card-body {
                padding: 1.5rem;
            }

            .brand-title {
                font-size: 1.6rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center align-items-center auth-wrapper">
            <div class="col-xxl-4 col-xl-5 col-lg-5 col-md-7 col-sm-9 col-12">

                <div class="brand-box">
                    <a href="{{ url('/') }}">
                        <img src="{{ asset('assets/images/logo_rtb.jpg') }}" alt="Logo E-Pusara">
                    </a>
                    <div class="brand-title">E-Pusara</div>
                    <p class="brand-subtitle">Sistem Pengurusan Khairat Kematian</p>
                </div>

                <div class="card auth-card">
                    <div class="card-body">
                        <h1 class="auth-title">Daftar Akaun</h1>
                        <p class="auth-text">Cipta akaun baharu untuk mula menggunakan sistem E-Pusara.</p>

                        <form method="POST" action="{{ route('register') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="name" class="form-label">Nama Penuh</label>
                                <input type="text"
                                       id="name"
                                       name="name"
                                       value="{{ old('name') }}"
                                       class="form-control form-control-lg @error('name') is-invalid @enderror"
                                       placeholder="Masukkan nama penuh"
                                       required
                                       autofocus>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email"
                                       id="email"
                                       name="email"
                                       value="{{ old('email') }}"
                                       class="form-control form-control-lg @error('email') is-invalid @enderror"
                                       placeholder="Masukkan email"
                                       required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="signup-password" class="form-label">Kata Laluan</label>
                                <div class="input-group">
                                    <input type="password"
                                           name="password"
                                           id="signup-password"
                                           class="form-control form-control-lg @error('password') is-invalid @enderror"
                                           placeholder="Masukkan kata laluan"
                                           required>
                                    <button class="btn toggle-password" type="button" data-target="signup-password">
                                        <i class="ri-eye-off-line"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-2">
                                <label for="signup-confirmpassword" class="form-label">Sahkan Kata Laluan</label>
                                <div class="input-group">
                                    <input type="password"
                                           name="password_confirmation"
                                           id="signup-confirmpassword"
                                           class="form-control form-control-lg"
                                           placeholder="Masukkan semula kata laluan"
                                           required>
                                    <button class="btn toggle-password" type="button" data-target="signup-confirmpassword">
                                        <i class="ri-eye-off-line"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="form-check mt-3">
                                <input class="form-check-input" type="checkbox" id="agree_terms" required>
                                <label class="form-check-label" for="agree_terms">
                                    Saya bersetuju dengan
                                    <a href="#">Terma &amp; Syarat</a> dan
                                    <a href="#">Dasar Privasi</a>.
                                </label>
                            </div>

                            <div class="d-grid mt-3">
                                <button class="btn btn-lg btn-success" type="submit">Daftar Akaun</button>
                            </div>
                        </form>

                        <p class="login-text">
                            Sudah mempunyai akaun?
                            <a href="{{ route('login') }}">Log masuk</a>
                        </p>

                        <div class="back-home">
                            <a href="{{ url('/') }}">← Kembali ke halaman utama</a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <script>
        document.querySelectorAll('.toggle-password').forEach(button => {
            button.addEventListener('click', function () {
                const targetId = this.getAttribute('data-target');
                const input = document.getElementById(targetId);
                const icon = this.querySelector('i');

                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('ri-eye-off-line');
                    icon.classList.add('ri-eye-line');
                } else {
                    input.type = 'password';
                    icon.classList.remove('ri-eye-line');
                    icon.classList.add('ri-eye-off-line');
                }
            });
        });
    </script>
</body>
</html>