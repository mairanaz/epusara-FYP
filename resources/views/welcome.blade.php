<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>E-Pusara</title>

    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Figtree', sans-serif;
            background: #f8fafc;
            color: #1e293b;
        }

        a {
            text-decoration: none;
        }

        .container {
            width: 90%;
            max-width: 1200px;
            margin: auto;
        }

        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
            z-index: 999;
        }

        .navbar-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 18px 0;
        }

        .logo {
            font-size: 28px;
            font-weight: 800;
            color: #0f766e;
        }

        .nav-buttons {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .btn {
            display: inline-block;
            padding: 12px 22px;
            border-radius: 12px;
            font-weight: 600;
            transition: 0.3s ease;
        }

        .btn-login {
            border: 1px solid #0f766e;
            color: #0f766e;
            background: transparent;
        }

        .btn-login:hover {
            background: #0f766e;
            color: white;
        }

        .btn-register {
            background: #0f766e;
            color: white;
            border: 1px solid #0f766e;
        }

        .btn-register:hover {
            background: #0d5f59;
        }

        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            background: linear-gradient(rgba(15, 118, 110, 0.82), rgba(15, 23, 42, 0.82)),
                        url('{{ asset("assets/images/media/bg-img3.jpg") }}') center/cover no-repeat;
            color: white;
            padding-top: 90px;
        }

        .hero-grid {
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            gap: 40px;
            align-items: center;
        }

        .hero-badge {
            display: inline-block;
            background: rgba(255,255,255,0.15);
            padding: 8px 16px;
            border-radius: 999px;
            font-size: 14px;
            margin-bottom: 18px;
        }

        .hero h1 {
            font-size: 52px;
            line-height: 1.15;
            font-weight: 800;
            margin-bottom: 18px;
        }

        .hero p {
            font-size: 18px;
            line-height: 1.8;
            color: #e2e8f0;
            margin-bottom: 28px;
        }

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
        }

        .btn-light {
            background: white;
            color: #0f766e;
        }

        .btn-light:hover {
            background: #f1f5f9;
        }

        .btn-outline-light {
            border: 1px solid white;
            color: white;
        }

        .btn-outline-light:hover {
            background: white;
            color: #0f766e;
        }

        .hero-card {
            background: rgba(255,255,255,0.10);
            border: 1px solid rgba(255,255,255,0.16);
            border-radius: 24px;
            padding: 28px;
            backdrop-filter: blur(10px);
        }

        .hero-card h3 {
            margin-bottom: 18px;
            font-size: 24px;
        }

        .hero-card ul {
            list-style: none;
        }

        .hero-card ul li {
            margin-bottom: 14px;
            padding-left: 24px;
            position: relative;
            line-height: 1.7;
            color: #f8fafc;
        }

        .hero-card ul li::before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #fde68a;
            font-weight: bold;
        }

        .section {
            padding: 90px 0;
        }

        .section-header {
            text-align: center;
            margin-bottom: 50px;
        }

        .section-header h2 {
            font-size: 38px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 12px;
        }

        .section-header p {
            color: #64748b;
            max-width: 760px;
            margin: auto;
            line-height: 1.8;
        }

        .features {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }

        .card {
            background: white;
            border-radius: 22px;
            padding: 28px;
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
            transition: 0.3s ease;
        }

        .card:hover {
            transform: translateY(-6px);
        }

        .icon {
            width: 58px;
            height: 58px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #ccfbf1;
            color: #0f766e;
            font-weight: 800;
            font-size: 22px;
            margin-bottom: 16px;
        }

        .card h3 {
            font-size: 22px;
            margin-bottom: 10px;
        }

        .card p {
            color: #64748b;
            line-height: 1.8;
        }

        .bg-soft {
            background: #f1f5f9;
        }

        .cta-box {
            background: linear-gradient(135deg, #0f766e, #134e4a);
            color: white;
            padding: 55px 35px;
            border-radius: 28px;
            text-align: center;
        }

        .cta-box h2 {
            font-size: 36px;
            margin-bottom: 14px;
        }

        .cta-box p {
            color: #e2e8f0;
            margin-bottom: 24px;
            line-height: 1.8;
        }

        .footer {
            background: #0f172a;
            color: #cbd5e1;
            text-align: center;
            padding: 28px 0;
        }

        @media (max-width: 992px) {
            .hero-grid,
            .features {
                grid-template-columns: 1fr;
            }

            .hero h1 {
                font-size: 38px;
            }

            .navbar-content {
                flex-direction: column;
                gap: 12px;
            }

            .nav-buttons {
                flex-wrap: wrap;
                justify-content: center;
            }
        }
    </style>
</head>
<body>

    <nav class="navbar">
        <div class="container navbar-content">
            <div class="logo">
                <a href="{{ url('/') }}" style="display:flex; align-items:center; gap:14px; text-decoration:none;">
                    <img src="{{ asset('assets/images/logo_rtb.jpg') }}" alt="Logo E-Pusara"
                        style="height:65px; width:auto; display:block;">
                    <span style="font-size:28px; font-weight:800; color:#0f766e;">E-Pusara</span>
                </a>
            </div>

            @if (Route::has('login'))
                <div class="nav-buttons">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn btn-register">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-login">Log Masuk</a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn btn-register">Daftar</a>
                        @endif
                    @endauth
                </div>
            @endif
        </div>
    </nav>

    <section class="hero">
        <div class="container hero-grid">
            <div>
                <span class="hero-badge">Sistem Pengurusan Khairat Kematian</span>
                <h1>Urus Keahlian, Bayaran dan Maklumat Ahli Dengan Lebih Mudah</h1>
                <p>
                    E-Pusara membantu pengurusan khairat kematian menjadi lebih tersusun,
                    sistematik dan mesra pengguna. Pengguna boleh mendaftar akaun,
                    log masuk, mengurus maklumat ahli, tanggungan dan rekod bayaran
                    secara dalam talian.
                </p>

                @guest
                    <div class="hero-actions">
                        <a href="{{ route('whatsapp.lapor-kematian') }}" class="btn btn-light">WhatsApp Pentadbir</a>
                        <a href="tel:0132186469" class="btn btn-outline-light">Call Pentadbir</a>
                    </div>
                @else
                    <div class="hero-actions">
                        <a href="{{ route('dashboard') }}" class="btn btn-light">Pergi ke Dashboard</a>
                        <a href="{{ route('whatsapp.lapor-kematian') }}" class="btn btn-outline-light">WhatsApp Pentadbir</a>
                        <a href="tel:0132186469" class="btn btn-outline-light">Call Pentadbir</a>
                    </div>
                @endguest
            </div>

            <div class="hero-card">
                <h3>Kemudahan Dalam Sistem</h3>
                <ul>
                    <li>Pendaftaran akaun pengguna baharu</li>
                    <li>Log masuk mengikut akaun masing-masing</li>
                    <li>Pengurusan maklumat ahli dan tanggungan</li>
                    <li>Semakan dan rekod bayaran khairat</li>
                    <li>Akses dashboard mengikut peranan pengguna</li>
                </ul>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="section-header">
                <h2>Fungsi Utama Sistem</h2>
                <p>
                    Sistem ini memudahkan pengguna dan pentadbir untuk mengurus maklumat
                    khairat kematian secara lebih cekap dan teratur.
                </p>
            </div>

            <div class="features">
                <div class="card">
                    <div class="icon">1</div>
                    <h3>Register Akaun</h3>
                    <p>Pengguna baharu boleh membuat pendaftaran sebelum menggunakan sistem.</p>
                </div>

                <div class="card">
                    <div class="icon">2</div>
                    <h3>Login Sistem</h3>
                    <p>Pengguna yang berdaftar boleh log masuk untuk mengakses sistem masing-masing.</p>
                </div>

                <div class="card">
                    <div class="icon">3</div>
                    <h3>Dashboard Ikut Role</h3>
                    <p>User dan admin akan dibawa ke dashboard yang berbeza mengikut peranan.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="section bg-soft">
        <div class="container">
            <div class="section-header">
                <h2>Kenapa Pilih E-Pusara?</h2>
                <p>
                    E-Pusara direka untuk menjadikan urusan keahlian dan pengurusan data
                    lebih moden, cepat dan mudah diakses.
                </p>
            </div>

            <div class="features">
                <div class="card">
                    <h3>Mesra Pengguna</h3>
                    <p>Antaramuka yang ringkas dan mudah difahami oleh pengguna biasa.</p>
                </div>

                <div class="card">
                    <h3>Data Lebih Tersusun</h3>
                    <p>Maklumat ahli, tanggungan dan pembayaran disimpan dengan lebih sistematik.</p>
                </div>

                <div class="card">
                    <h3>Akses Lebih Cepat</h3>
                    <p>Semakan rekod boleh dilakukan dengan lebih pantas berbanding cara manual.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="cta-box">
                <h2>Mula Gunakan E-Pusara</h2>
                <p>Daftar akaun sekarang atau log masuk jika anda sudah mempunyai akaun.</p>

                @guest
                    <a href="{{ route('register') }}" class="btn btn-light" style="margin-right:10px;">Daftar</a>
                    <a href="{{ route('login') }}" class="btn btn-outline-light">Log Masuk</a>
                @else
                    <a href="{{ route('dashboard') }}" class="btn btn-light">Dashboard</a>
                @endguest
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            © {{ date('Y') }} E-Pusara. Hak cipta terpelihara.
        </div>
    </footer>
    

</body>
</html>