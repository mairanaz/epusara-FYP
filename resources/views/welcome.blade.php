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
            flex-wrap: wrap;
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

        .btn-danger-soft {
            background: #dc2626;
            color: white;
            border: 1px solid #dc2626;
        }

        .btn-danger-soft:hover {
            background: #b91c1c;
            color: white;
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
            height: 100%;
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

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 24px;
        }

        .info-box {
            background: #ffffff;
            border-radius: 22px;
            padding: 28px;
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.06);
        }

        .info-box h3 {
            font-size: 24px;
            margin-bottom: 14px;
            color: #0f172a;
        }

        .info-box p {
            color: #64748b;
            line-height: 1.8;
            margin-bottom: 14px;
        }

        .info-box ul {
            padding-left: 18px;
            color: #475569;
            line-height: 1.8;
        }

        .steps {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }

        .step-card {
            background: white;
            border-radius: 20px;
            padding: 24px;
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.06);
        }

        .step-number {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: #0f766e;
            color: white;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 14px;
        }

        .step-card h4 {
            font-size: 18px;
            margin-bottom: 8px;
            color: #0f172a;
        }

        .step-card p {
            color: #64748b;
            line-height: 1.7;
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
            .features,
            .info-grid,
            .steps {
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
                <span class="hero-badge">Sistem Pengurusan Khairat Kematian Digital</span>
                <h1>E-Pusara: Platform Pengurusan Khairat, Ahli dan Laporan Kematian</h1>
                <p>
                    E-Pusara merupakan sistem yang dibangunkan untuk membantu pengurusan
                    khairat kematian secara lebih moden, tersusun dan efisien. Sistem ini
                    memudahkan urusan pendaftaran ahli, pengurusan tanggungan, semakan rekod
                    bayaran, serta pelaporan kematian melalui satu platform berpusat.
                </p>

                @guest
                    <div class="hero-actions">
                        <a href="{{ route('whatsapp.lapor-kematian') }}" class="btn btn-outline-light">WhatsApp Pentadbir</a>
                        <a href="tel:0132186469" class="btn btn-outline-light">Call Pentadbir</a>
                    </div>
                @else
                    <div class="hero-actions">
                        <a href="{{ route('dashboard') }}" class="btn btn-light">Pergi ke Dashboard</a>
                       {{--   <a href="{{ route('death-report.create') }}" class="btn btn-danger-soft">Lapor Kematian</a> --}}
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
                    <li>Pelaporan kematian secara dalam talian</li>
                    <li>Akses dashboard mengikut peranan pengguna</li>
                </ul>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="section-header">
                <h2>Tentang E-Pusara</h2>
                <p>
                    E-Pusara dibangunkan bagi memperkenalkan sistem pengurusan khairat kematian
                    yang lebih teratur dan mudah digunakan oleh komuniti. Sistem ini membantu
                    mempercepatkan urusan berkaitan ahli, tanggungan, bayaran serta laporan
                    kematian tanpa bergantung sepenuhnya kepada proses manual.
                </p>
            </div>

            <div class="info-grid">
                <div class="info-box">
                    <h3>Objektif Sistem</h3>
                    <p>
                        Memudahkan pihak pengguna dan pentadbir mengurus data khairat kematian
                        dengan lebih sistematik dalam satu platform.
                    </p>
                    <ul>
                        <li>Menyimpan maklumat ahli dan tanggungan dengan lebih tersusun</li>
                        <li>Memudahkan semakan rekod bayaran khairat</li>
                        <li>Mempercepatkan proses laporan kematian</li>
                        <li>Meningkatkan kecekapan pengurusan oleh pentadbir</li>
                    </ul>
                </div>

                <div class="info-box">
                    <h3>Siapa Yang Guna Sistem Ini?</h3>
                    <p>
                        Sistem ini digunakan oleh dua peranan utama iaitu pengguna dan pentadbir.
                    </p>
                    <ul>
                        <li><strong>Pengguna:</strong> daftar akaun, urus profil, tanggungan dan bayaran</li>
                        <li><strong>Pentadbir:</strong> semak data ahli, sahkan bayaran dan urus laporan kematian</li>
                        <li><strong>Waris/Pelapor:</strong> boleh bertindak lebih cepat melalui WhatsApp, panggilan atau laporan dalam sistem</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <section class="section bg-soft">
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
                    <h3>Daftar Akaun</h3>
                    <p>Pengguna baharu boleh membuat pendaftaran sebelum menggunakan sistem.</p>
                </div>

                <div class="card">
                    <div class="icon">2</div>
                    <h3>Urus Profil & Tanggungan</h3>
                    <p>Maklumat ahli dan tanggungan boleh disimpan, dikemaskini dan disemak dengan mudah.</p>
                </div>

                <div class="card">
                    <div class="icon">3</div>
                    <h3>Rekod Bayaran Khairat</h3>
                    <p>Pengguna boleh melihat status dan rekod bayaran khairat dengan lebih teratur.</p>
                </div>

                <div class="card">
                    <div class="icon">4</div>
                    <h3>Laporan Kematian</h3>
                    <p>Laporan kematian boleh dihantar melalui sistem bagi memudahkan tindakan lanjut oleh pentadbir.</p>
                </div>

                <div class="card">
                    <div class="icon">5</div>
                    <h3>Hubungi Pentadbir</h3>
                    <p>Pengguna atau waris boleh terus menghubungi pentadbir melalui WhatsApp atau panggilan telefon.</p>
                </div>

                <div class="card">
                    <div class="icon">6</div>
                    <h3>Dashboard Ikut Peranan</h3>
                    <p>User dan admin akan dibawa ke dashboard berbeza mengikut peranan masing-masing.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="section-header">
                <h2>Proses Ringkas Laporan Kematian</h2>
                <p>
                    Bahagian ini membantu pelawat memahami bagaimana sistem digunakan
                    apabila berlaku kematian ahli atau tanggungan.
                </p>
            </div>

            <div class="steps">
                <div class="step-card">
                    <div class="step-number">1</div>
                    <h4>Maklumkan Kematian</h4>
                    <p>Waris atau pelapor boleh menghubungi pentadbir dengan segera melalui WhatsApp, panggilan atau sistem.</p>
                </div>

                <div class="step-card">
                    <div class="step-number">2</div>
                    <h4>Isi Borang Laporan</h4>
                    <p>Maklumat kematian boleh diisi melalui borang laporan untuk rekod rasmi dalam sistem.</p>
                </div>

                <div class="step-card">
                    <div class="step-number">3</div>
                    <h4>Semakan Pentadbir</h4>
                    <p>Pentadbir akan menyemak maklumat yang dihantar sebelum tindakan lanjut dibuat.</p>
                </div>

                <div class="step-card">
                    <div class="step-number">4</div>
                    <h4>Tindakan Susulan</h4>
                    <p>Maklumat yang telah disahkan akan membantu proses pengurusan seterusnya dengan lebih cepat dan tersusun.</p>
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
                    <p>Maklumat ahli, tanggungan, bayaran dan laporan kematian disimpan dengan lebih sistematik.</p>
                </div>

                <div class="card">
                    <h3>Tindakan Lebih Cepat</h3>
                    <p>Pelaporan dan semakan boleh dilakukan lebih pantas berbanding kaedah manual.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="cta-box">
                <h2>Mula Gunakan E-Pusara</h2>
                <p>
                    Daftar akaun untuk mengurus maklumat keahlian dan bayaran, atau gunakan
                    butang laporan kematian sekiranya ingin membuat makluman kepada pentadbir.
                </p>

                @guest
                    <a href="{{ route('register') }}" class="btn btn-light" style="margin-right:10px;">Daftar</a>
                    <a href="{{ route('login') }}" class="btn btn-outline-light" style="margin-right:10px;">Log Masuk</a>
                    <a href="{{ route('whatsapp.lapor-kematian') }}" class="btn btn-outline-light">WhatsApp Pentadbir</a>
                @else
                    <a href="{{ route('dashboard') }}" class="btn btn-light" style="margin-right:10px;">Dashboard</a>
                 {{--   <a href="{{ route('death-report.create') }}" class="btn btn-outline-light">Lapor Kematian</a> --}}  
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