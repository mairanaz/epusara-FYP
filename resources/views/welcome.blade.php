<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ePusara | Sistem Pengurusan Perkuburan RTB Bukit Changgang</title>

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">

    {{-- Tailwind CSS --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#ecfdf8',
                            100: '#d1fae9',
                            200: '#a7f3d0',
                            500: '#159a83',
                            600: '#0f7c69',
                            700: '#0d6558',
                            800: '#114c44',
                            900: '#0d332f'
                        },
                        sand: {
                            50: '#fbfaf6',
                            100: '#f3efe6',
                            200: '#e6ddcc'
                        }
                    },
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                        serif: ['Playfair Display', 'serif']
                    },
                    boxShadow: {
                        soft: '0 18px 50px rgba(15, 23, 42, 0.07)',
                        card: '0 16px 40px rgba(15, 23, 42, 0.08)',
                        hero: '0 30px 80px rgba(13, 51, 47, 0.16)'
                    }
                }
            }
        }

        
    </script>

    <style>
        html {
            scroll-behavior: smooth;
        }

        body {
            overflow-x: hidden;
        }

        .fade-up {
            animation: fadeUp .7s ease-out both;
        }

        .fade-up-delay {
            animation: fadeUp .7s .12s ease-out both;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(18px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .hero-overlay {
            background:
                linear-gradient(90deg, rgba(255,255,255,.98) 0%, rgba(255,255,255,.93) 42%, rgba(255,255,255,.40) 100%);
        }

        .photo-overlay {
            background: linear-gradient(180deg, rgba(15,23,42,.02) 10%, rgba(15,23,42,.76) 100%);
        }

        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }

        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

    </style>
</head>

<body class="min-h-screen bg-sand-50 text-slate-800 font-sans antialiased">

    {{-- Bar makluman awam --}}
    <div class="bg-brand-900 text-brand-100 px-4 py-2.5 text-xs md:text-sm">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row justify-between items-center gap-2">
            <p class="flex items-center gap-2 text-center sm:text-left">
                <i class="fa-solid fa-circle-info text-brand-200"></i>
                Sistem pengurusan khairat kematian dan lokasi perkuburan khusus untuk komuniti RTB Bukit Changgang.
            </p>

            <a href="{{ route('public.grave-search.index') }}"
               class="font-semibold underline underline-offset-4 hover:text-white transition">
                Cari lokasi pusara
            </a>
        </div>
    </div>

    {{-- Header / Navigation --}}
    <nav class="sticky top-0 z-50 bg-white/95 backdrop-blur border-b border-slate-100 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">

            {{-- Logo --}}
            <a href="{{ url('/') }}" class="flex items-center gap-3">
                <img src="{{ asset('assets/images/logo_rtb.jpg') }}"
                     alt="Logo e-Pusara"
                     class="h-12 w-12 rounded-xl border border-slate-100 object-cover">

                <div class="hidden sm:block">
                    <p class="font-extrabold text-brand-800 leading-none text-xl">
                        ePusara
                    </p>

                    <p class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider mt-1">
                        Sistem Pengurusan Perkuburan RTB Bukit Changgang
                    </p>
                </div>
            </a>

            {{-- Navigation Links --}}
            <div class="flex items-center gap-2 sm:gap-3">

                <a href="#fungsi"
                   class="hidden lg:inline-flex text-sm font-semibold text-slate-600 px-4 py-2 hover:text-brand-700 transition">
                    Fungsi
                </a>

                <a href="{{ route('public.grave-search.index') }}"
                   class="hidden md:inline-flex text-sm font-semibold text-slate-600 px-4 py-2 hover:text-brand-700 transition">
                    Ziarah Kubur
                </a>

                @auth
                    <a href="{{ route('dashboard') }}"
                       class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white rounded-full px-4 sm:px-5 py-2.5 text-sm font-bold transition">
                        <i class="fa-solid fa-gauge-high"></i>
                        <span class="hidden sm:inline">Dashboard</span>
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       class="inline-flex text-sm font-bold text-brand-700 border border-brand-200 hover:bg-brand-50 rounded-full px-4 sm:px-5 py-2.5 transition">
                        Log Masuk
                    </a>

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                           class="hidden sm:inline-flex text-sm font-bold text-white bg-brand-600 hover:bg-brand-700 rounded-full px-5 py-2.5 transition">
                            Daftar
                        </a>
                    @endif
                @endauth
            </div>
        </div>
    </nav>

    {{-- Hero Section --}}
    <section class="relative overflow-hidden bg-white border-b border-slate-100">

        {{-- Background image --}}
        <div class="absolute inset-0">
            <img src="{{ asset('assets/images/media/bg-img3.jpg') }}"
                 alt="Persekitaran perkuburan RTB Bukit Changgang"
                 class="w-full h-full object-cover object-center">
        </div>

        <div class="absolute inset-0 hero-overlay"></div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16 lg:py-20">
            <div class="grid lg:grid-cols-12 gap-9 lg:gap-12 items-center">

                {{-- Left Content --}}
                <div class="lg:col-span-7 fade-up">

                    <span class="inline-flex items-center gap-2 rounded-full bg-brand-50/95 border border-brand-100 text-brand-700 px-4 py-2 text-xs font-bold uppercase tracking-widest mb-5">
                        <i class="fa-solid fa-moon"></i>
                        Pengurusan Khairat Digital RTB Bukit Changgang
                    </span>

                    <h1 class="font-serif font-bold text-slate-900 text-4xl sm:text-5xl lg:text-[3.55rem] leading-tight max-w-3xl">
                        Pengurusan Khairat &amp; Pusara RTB Bukit Changgang Lebih Tersusun
                    </h1>

                    <p class="text-slate-600 text-sm sm:text-base leading-relaxed max-w-xl mt-5">
                        ePusara memudahkan pengurusan ahli, tanggungan, bayaran khairat,
                        laporan kematian serta carian lokasi pusara bagi kawasan RTB Bukit Changgang
                        dalam satu sistem yang mudah digunakan.
                    </p>

                    {{-- Main Actions --}}
                    <div class="flex flex-col sm:flex-row gap-3 mt-8">

                        @guest
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}"
                                   class="inline-flex items-center justify-center gap-2 rounded-full bg-brand-600 hover:bg-brand-700 text-white px-7 py-4 text-sm font-bold transition shadow-lg shadow-brand-600/20">
                                    <i class="fa-solid fa-user-plus"></i>
                                    Daftar Sebagai Ahli
                                </a>
                            @endif

                            <a href="{{ route('public.grave-search.index') }}"
                               class="inline-flex items-center justify-center gap-2 rounded-full bg-white border border-brand-200 hover:bg-brand-50 text-brand-700 px-7 py-4 text-sm font-bold transition">
                                <i class="fa-solid fa-location-dot"></i>
                                Ziarah Kubur
                            </a>
                        @else
                            <a href="{{ route('dashboard') }}"
                               class="inline-flex items-center justify-center gap-2 rounded-full bg-brand-600 hover:bg-brand-700 text-white px-7 py-4 text-sm font-bold transition shadow-lg shadow-brand-600/20">
                                <i class="fa-solid fa-gauge-high"></i>
                                Pergi ke Dashboard
                            </a>

                            <a href="{{ route('public.grave-search.index') }}"
                               class="inline-flex items-center justify-center gap-2 rounded-full bg-white border border-brand-200 hover:bg-brand-50 text-brand-700 px-7 py-4 text-sm font-bold transition">
                                <i class="fa-solid fa-location-dot"></i>
                                Ziarah Kubur
                            </a>
                        @endguest

                    </div>

                    {{-- Small Highlights --}}
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mt-9 max-w-2xl">

                        <div class="rounded-2xl bg-white/90 border border-white p-4 shadow-sm">
                            <i class="fa-solid fa-users text-brand-600 mb-2"></i>
                            <p class="text-xs font-bold text-slate-800">Pengurusan Ahli</p>
                            <p class="text-[11px] text-slate-500 mt-1">Rekod tersusun</p>
                        </div>

                        <div class="rounded-2xl bg-white/90 border border-white p-4 shadow-sm">
                            <i class="fa-solid fa-receipt text-brand-600 mb-2"></i>
                            <p class="text-xs font-bold text-slate-800">Bayaran Khairat</p>
                            <p class="text-[11px] text-slate-500 mt-1">Semakan mudah</p>
                        </div>

                        <div class="rounded-2xl bg-white/90 border border-white p-4 shadow-sm col-span-2 sm:col-span-1">
                            <i class="fa-solid fa-map-location-dot text-brand-600 mb-2"></i>
                            <p class="text-xs font-bold text-slate-800">Lokasi Pusara</p>
                            <p class="text-[11px] text-slate-500 mt-1">Untuk ziarah</p>
                        </div>

                    </div>
                </div>

                {{-- Right Feature Card --}}
                <div class="lg:col-span-5 fade-up-delay">

                    <div class="bg-white/95 backdrop-blur-md rounded-[30px] border border-white shadow-hero p-6 md:p-7">

                        <div class="relative overflow-hidden rounded-3xl min-h-[215px] mb-6">
                            <img src="{{ asset('assets/images/pusara/persekitaran-kubur.jpg') }}"
                                 alt="Kawasan perkuburan RTB Bukit Changgang"
                                 class="absolute inset-0 w-full h-full object-cover">

                            <div class="photo-overlay absolute inset-0 p-5 flex flex-col justify-end">
                                <span class="inline-flex w-fit bg-brand-600 text-white rounded-full px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider mb-3">
                                    Modul Awam
                                </span>

                                <h2 class="font-serif font-bold text-xl text-white">
                                    Carian Lokasi Pusara
                                </h2>

                                <p class="text-slate-200 text-xs mt-1">
                                    Cari lokasi lot pusara di kawasan RTB Bukit Changgang tanpa perlu log masuk.
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center justify-between gap-3 mb-5">
                            <div>
                                <p class="text-xs font-bold text-brand-700 uppercase tracking-widest">
                                    Ziarah Kubur
                                </p>

                                <h3 class="font-bold text-slate-900 text-lg mt-1">
                                    Mudah dicari, mudah diziarahi
                                </h3>
                            </div>

                            <div class="h-12 w-12 shrink-0 rounded-2xl bg-brand-50 text-brand-700 flex items-center justify-center">
                                <i class="fa-solid fa-location-crosshairs text-xl"></i>
                            </div>
                        </div>

                        <div class="space-y-3 mb-6">

                            <div class="flex items-start gap-3 text-sm text-slate-600">
                                <i class="fa-solid fa-circle-check text-brand-600 mt-1"></i>
                                <span>Carian menggunakan nama si mati atau nombor lot pusara di RTB Bukit Changgang.</span>
                            </div>

                            <div class="flex items-start gap-3 text-sm text-slate-600">
                                <i class="fa-solid fa-circle-check text-brand-600 mt-1"></i>
                                <span>Paparan lot dan peta bagi membantu tujuan ziarah di kawasan RTB Bukit Changgang.</span>
                            </div>

                            <div class="flex items-start gap-3 text-sm text-slate-600">
                                <i class="fa-solid fa-circle-check text-brand-600 mt-1"></i>
                                <span>Maklumat peribadi keluarga kekal dilindungi.</span>
                            </div>

                        </div>

                        <a href="{{ route('public.grave-search.index') }}"
                           class="w-full inline-flex items-center justify-center gap-2 rounded-2xl bg-brand-600 hover:bg-brand-700 text-white py-4 px-5 text-sm font-bold transition">
                            <i class="fa-solid fa-magnifying-glass-location"></i>
                            Cari Lokasi Pusara
                        </a>

                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- Quick Access --}}
    <section id="fungsi" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 md:py-20">

        <div class="max-w-3xl mx-auto text-center mb-9 md:mb-12">
            <span class="inline-flex bg-brand-50 text-brand-700 font-bold text-xs tracking-[.18em] uppercase rounded-full px-4 py-2 mb-4">
                Fungsi Utama
            </span>

            <h2 class="font-serif font-bold text-3xl md:text-4xl text-slate-900">
                Semua Urusan Dalam Satu Sistem
            </h2>

            <p class="text-slate-500 text-sm md:text-base leading-relaxed mt-3">
                ePusara membantu ahli, waris dan pentadbir RTB Bukit Changgang mengurus urusan khairat kematian dengan lebih sistematik.
            </p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">

            <article class="bg-white border border-slate-100 rounded-3xl p-6 shadow-card hover:-translate-y-1 transition duration-300">
                <div class="h-12 w-12 rounded-2xl bg-brand-50 text-brand-700 flex items-center justify-center mb-5">
                    <i class="fa-solid fa-id-card text-xl"></i>
                </div>

                <h3 class="font-bold text-lg text-slate-900">
                    Keahlian
                </h3>

                <p class="text-sm text-slate-500 leading-relaxed mt-2">
                    Pendaftaran serta pengurusan rekod ahli dan tanggungan.
                </p>
            </article>

            <article class="bg-white border border-slate-100 rounded-3xl p-6 shadow-card hover:-translate-y-1 transition duration-300">
                <div class="h-12 w-12 rounded-2xl bg-brand-50 text-brand-700 flex items-center justify-center mb-5">
                    <i class="fa-solid fa-wallet text-xl"></i>
                </div>

                <h3 class="font-bold text-lg text-slate-900">
                    Bayaran Khairat
                </h3>

                <p class="text-sm text-slate-500 leading-relaxed mt-2">
                    Semak bayaran dan rekod transaksi dengan lebih mudah.
                </p>
            </article>

            <article class="bg-white border border-slate-100 rounded-3xl p-6 shadow-card hover:-translate-y-1 transition duration-300">
                <div class="h-12 w-12 rounded-2xl bg-brand-50 text-brand-700 flex items-center justify-center mb-5">
                    <i class="fa-solid fa-file-circle-plus text-xl"></i>
                </div>

                <h3 class="font-bold text-lg text-slate-900">
                    Laporan Kematian
                </h3>

                <p class="text-sm text-slate-500 leading-relaxed mt-2">
                    Penghantaran laporan bagi membantu tindakan pentadbir.
                </p>
            </article>

            <article class="bg-white border border-slate-100 rounded-3xl p-6 shadow-card hover:-translate-y-1 transition duration-300">
                <div class="h-12 w-12 rounded-2xl bg-brand-50 text-brand-700 flex items-center justify-center mb-5">
                    <i class="fa-solid fa-map-location-dot text-xl"></i>
                </div>

                <h3 class="font-bold text-lg text-slate-900">
                    Ziarah Kubur
                </h3>

                <p class="text-sm text-slate-500 leading-relaxed mt-2">
                    Carian lokasi pusara di RTB Bukit Changgang berserta panduan lot.
                </p>
            </article>

        </div>
    </section>

    {{-- About / User Role Section --}}
    <section class="bg-white border-y border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 md:py-20">

            <div class="grid lg:grid-cols-12 gap-8 lg:gap-12 items-center">

                <div class="lg:col-span-5">
                    <span class="inline-flex bg-brand-50 text-brand-700 font-bold text-xs tracking-[.18em] uppercase rounded-full px-4 py-2 mb-4">
                        Mengenai ePusara
                    </span>

                    <h2 class="font-serif font-bold text-3xl md:text-4xl text-slate-900 leading-tight">
                        Sistem Yang Memudahkan Komuniti RTB Bukit Changgang
                    </h2>

                    <p class="text-slate-500 text-sm md:text-base leading-relaxed mt-4">
                        ePusara dibangunkan bagi membantu urusan khairat kematian dan perkuburan di RTB Bukit Changgang secara lebih teratur, pantas dan mudah dirujuk.
                    </p>
                </div>

                <div class="lg:col-span-7">
                    <div class="grid sm:grid-cols-3 gap-4">

                        <div class="rounded-3xl bg-sand-50 border border-sand-100 p-5">
                            <div class="h-10 w-10 rounded-xl bg-white text-brand-700 flex items-center justify-center shadow-sm mb-4">
                                <i class="fa-solid fa-user"></i>
                            </div>

                            <h3 class="font-bold text-slate-900 text-sm">
                                Ahli
                            </h3>

                            <p class="text-xs text-slate-500 leading-relaxed mt-2">
                                Mengurus profil, tanggungan dan rekod bayaran khairat.
                            </p>
                        </div>

                        <div class="rounded-3xl bg-sand-50 border border-sand-100 p-5">
                            <div class="h-10 w-10 rounded-xl bg-white text-brand-700 flex items-center justify-center shadow-sm mb-4">
                                <i class="fa-solid fa-user-gear"></i>
                            </div>

                            <h3 class="font-bold text-slate-900 text-sm">
                                Pentadbir
                            </h3>

                            <p class="text-xs text-slate-500 leading-relaxed mt-2">
                                Menyemak permohonan, bayaran dan laporan kematian.
                            </p>
                        </div>

                        <div class="rounded-3xl bg-sand-50 border border-sand-100 p-5">
                            <div class="h-10 w-10 rounded-xl bg-white text-brand-700 flex items-center justify-center shadow-sm mb-4">
                                <i class="fa-solid fa-people-group"></i>
                            </div>

                            <h3 class="font-bold text-slate-900 text-sm">
                                Pengunjung
                            </h3>

                            <p class="text-xs text-slate-500 leading-relaxed mt-2">
                                Mencari lokasi pusara di RTB Bukit Changgang bagi tujuan ziarah.
                            </p>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- Paparan Sistem / Screenshot Gallery --}}
    <section class="bg-white border-y border-slate-100 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 md:py-20">

            {{-- Heading --}}
            <div class="max-w-3xl mx-auto text-center mb-9 md:mb-12">
                <span class="inline-flex bg-brand-50 text-brand-700 font-bold text-xs tracking-[.18em] uppercase rounded-full px-4 py-2 mb-4">
                    Paparan Sistem
                </span>

                <h2 class="font-serif font-bold text-3xl md:text-4xl text-slate-900">
                    Antara Muka ePusara RTB Bukit Changgang
                </h2>

                <p class="text-slate-500 text-sm md:text-base leading-relaxed mt-3">
                    Paparan sistem direka agar mudah digunakan melalui komputer dan telefon pintar
                    bagi membantu urusan keahlian, bayaran khairat, laporan kematian serta carian pusara RTB Bukit Changgang.
                </p>
            </div>

            {{-- Tab Button --}}
            <div class="flex justify-center mb-10">
                <div class="inline-flex bg-sand-50 border border-sand-100 rounded-full p-1.5 shadow-sm">

                    <button type="button"
                            id="mobileTab"
                            onclick="showSystemGallery('mobile')"
                            class="gallery-tab inline-flex items-center gap-2 rounded-full bg-brand-600 text-white px-5 sm:px-7 py-3 text-sm font-bold transition">
                        <i class="fa-solid fa-mobile-screen-button"></i>
                        Paparan Mobile
                    </button>

                    <button type="button"
                            id="laptopTab"
                            onclick="showSystemGallery('laptop')"
                            class="gallery-tab inline-flex items-center gap-2 rounded-full text-slate-500 hover:text-brand-700 px-5 sm:px-7 py-3 text-sm font-bold transition">
                        <i class="fa-solid fa-laptop"></i>
                        Paparan Laptop
                    </button>

                </div>
            </div>

            {{-- Mobile Gallery --}}
            <div id="mobileGallery" class="gallery-panel">

                <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8">
                    <div>
                        <h3 class="font-bold text-2xl text-slate-900">
                            Paparan Telefon Pintar
                        </h3>

                        <p class="text-sm md:text-base text-slate-500 mt-2">
                            Antara muka responsif untuk kegunaan ahli dan pengunjung RTB Bukit Changgang melalui telefon.
                        </p>
                    </div>

                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center gap-2 bg-brand-50 text-brand-700 rounded-full px-4 py-2 text-xs font-bold">
                            <i class="fa-regular fa-images"></i>
                            5 Paparan
                        </span>

                        <button type="button"
                                onclick="scrollMobileGallery(-1)"
                                class="hidden md:flex h-11 w-11 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-600 hover:bg-brand-600 hover:text-white hover:border-brand-600 transition">
                            <i class="fa-solid fa-arrow-left"></i>
                        </button>

                        <button type="button"
                                onclick="scrollMobileGallery(1)"
                                class="hidden md:flex h-11 w-11 items-center justify-center rounded-full bg-brand-600 text-white hover:bg-brand-700 transition">
                            <i class="fa-solid fa-arrow-right"></i>
                        </button>
                    </div>
                </div>

                <div id="mobileSlider"
                    class="flex gap-5 md:gap-7 overflow-x-auto pb-6 snap-x snap-mandatory scrollbar-hide scroll-smooth">

                    {{-- Mobile 1 --}}
                    <article class="shrink-0 w-[82vw] sm:w-[340px] lg:w-[355px] snap-start group">
                        <div class="relative overflow-hidden rounded-[28px] bg-white border border-slate-100 shadow-card group-hover:-translate-y-1 transition duration-300">
                            <img src="{{ asset('assets/images/screenshots/mobile-1.jpeg') }}"
                                alt="Paparan Log Masuk e-Pusara"
                                class="w-full h-[570px] object-cover object-center">

                            <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-slate-950/85 via-slate-950/40 to-transparent px-6 pt-20 pb-6">
                                <span class="inline-flex bg-white/20 backdrop-blur text-white rounded-full px-3 py-1 text-[10px] font-bold uppercase tracking-wider mb-3">
                                    Mobile
                                </span>

                                <h4 class="font-bold text-white text-lg">
                                    Log Masuk Ahli
                                </h4>

                                <p class="text-xs text-white/75 mt-1">
                                    Akses selamat untuk pengguna berdaftar.
                                </p>
                            </div>
                        </div>
                    </article>

                    {{-- Mobile 2 --}}
                    <article class="shrink-0 w-[82vw] sm:w-[340px] lg:w-[355px] snap-start group">
                        <div class="relative overflow-hidden rounded-[28px] bg-white border border-slate-100 shadow-card group-hover:-translate-y-1 transition duration-300">
                            <img src="{{ asset('assets/images/screenshots/mobile-2.jpeg') }}"
                                alt="Paparan Halaman Utama e-Pusara"
                                class="w-full h-[570px] object-cover object-center">

                            <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-slate-950/85 via-slate-950/40 to-transparent px-6 pt-20 pb-6">
                                <span class="inline-flex bg-white/20 backdrop-blur text-white rounded-full px-3 py-1 text-[10px] font-bold uppercase tracking-wider mb-3">
                                    Mobile
                                </span>

                                <h4 class="font-bold text-white text-lg">
                                    Laman Utama
                                </h4>

                                <p class="text-xs text-white/75 mt-1">
                                    Pengenalan sistem dan capaian fungsi utama.
                                </p>
                            </div>
                        </div>
                    </article>

                    {{-- Mobile 3 --}}
                    <article class="shrink-0 w-[82vw] sm:w-[340px] lg:w-[355px] snap-start group">
                        <div class="relative overflow-hidden rounded-[28px] bg-white border border-slate-100 shadow-card group-hover:-translate-y-1 transition duration-300">
                            <img src="{{ asset('assets/images/screenshots/mobile-3.png') }}"
                                alt="Paparan Bayaran Khairat e-Pusara"
                                class="w-full h-[570px] object-cover object-center">

                            <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-slate-950/85 via-slate-950/40 to-transparent px-6 pt-20 pb-6">
                                <span class="inline-flex bg-white/20 backdrop-blur text-white rounded-full px-3 py-1 text-[10px] font-bold uppercase tracking-wider mb-3">
                                    Mobile
                                </span>

                                <h4 class="font-bold text-white text-lg">
                                    Carian Pusara
                                </h4>

                                <p class="text-xs text-white/75 mt-1">
                                    Melihat lokasi pusara
                                </p>
                            </div>
                        </div>
                    </article>

                    {{-- Mobile 4 --}}
                    <article class="shrink-0 w-[82vw] sm:w-[340px] lg:w-[355px] snap-start group">
                        <div class="relative overflow-hidden rounded-[28px] bg-white border border-slate-100 shadow-card group-hover:-translate-y-1 transition duration-300">
                            <img src="{{ asset('assets/images/screenshots/mobile-4.png') }}"
                                alt="Paparan Laporan Kematian e-Pusara"
                                class="w-full h-[570px] object-cover object-center">

                            <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-slate-950/85 via-slate-950/40 to-transparent px-6 pt-20 pb-6">
                                <span class="inline-flex bg-white/20 backdrop-blur text-white rounded-full px-3 py-1 text-[10px] font-bold uppercase tracking-wider mb-3">
                                    Mobile
                                </span>

                                <h4 class="font-bold text-white text-lg">
                                    Maklumat Ahli
                                </h4>

                                <p class="text-xs text-white/75 mt-1">
                                    Maklumat profil ahli dan status permohonan keahlian.
                                </p>
                            </div>
                        </div>
                    </article>

                    {{-- Mobile 5 --}}
                    <article class="shrink-0 w-[82vw] sm:w-[340px] lg:w-[355px] snap-start group">
                        <div class="relative overflow-hidden rounded-[28px] bg-white border border-slate-100 shadow-card group-hover:-translate-y-1 transition duration-300">
                            <img src="{{ asset('assets/images/screenshots/mobile-5.png') }}"
                                alt="Paparan Ziarah Kubur e-Pusara"
                                class="w-full h-[570px] object-cover object-center">

                            <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-slate-950/85 via-slate-950/40 to-transparent px-6 pt-20 pb-6">
                                <span class="inline-flex bg-white/20 backdrop-blur text-white rounded-full px-3 py-1 text-[10px] font-bold uppercase tracking-wider mb-3">
                                    Mobile
                                </span>

                                <h4 class="font-bold text-white text-lg">
                                    Ziarah Kubur
                                </h4>

                                <p class="text-xs text-white/75 mt-1">
                                    Carian lokasi pusara bagi tujuan ziarah.
                                </p>
                            </div>
                        </div>
                    </article>

                </div>
            </div>

            {{-- Laptop Gallery --}}
            <div id="laptopGallery" class="gallery-panel hidden">

                <div class="flex items-center justify-between gap-4 mb-7">
                    <div>
                        <h3 class="font-bold text-xl text-slate-900">
                            Paparan Komputer / Laptop
                        </h3>

                        <p class="text-sm text-slate-500 mt-1">
                            Paparan desktop yang lebih luas untuk pengurusan sistem RTB Bukit Changgang secara teratur.
                        </p>
                    </div>

                    <span class="hidden sm:inline-flex items-center gap-2 bg-brand-50 text-brand-700 rounded-full px-4 py-2 text-xs font-bold">
                        <i class="fa-solid fa-images"></i>
                        4 Paparan
                    </span>
                </div>

                <div class="grid md:grid-cols-2 gap-6">

                    {{-- Laptop 2 --}}
                    <article class="group">
                        <div class="rounded-[24px] bg-sand-50 border border-sand-100 p-4 shadow-card group-hover:-translate-y-1 transition duration-300">
                            <img src="{{ asset('assets/images/screenshots/laptop-2.jpeg') }}"
                                alt="Paparan laptop Pengurusan Ahli e-Pusara"
                                class="w-full h-auto object-contain rounded-xl">
                        </div>

                        <h4 class="font-bold text-slate-900 mt-4">
                            Bayaran Yuran
                        </h4>

                        <p class="text-xs text-slate-500 mt-1">
                            Pemantauan bayaran dan resit transaksi.
                        </p>
                    </article>

                    {{-- Laptop 3 --}}
                    <article class="group">
                        <div class="rounded-[24px] bg-sand-50 border border-sand-100 p-4 shadow-card group-hover:-translate-y-1 transition duration-300">
                            <img src="{{ asset('assets/images/screenshots/laptop-3.jpeg') }}"
                                alt="Paparan laptop Bayaran Khairat e-Pusara"
                                class="w-full h-auto object-contain rounded-xl">
                        </div>

                        <h4 class="font-bold text-slate-900 mt-4">
                            Laporan Kematian
                        </h4>

                        <p class="text-xs text-slate-500 mt-1">
                            Proses melaporkan kematian si mati.
                        </p>
                    </article>

                    {{-- Laptop 4 --}}
                    <article class="group">
                        <div class="rounded-[24px] bg-sand-50 border border-sand-100 p-4 shadow-card group-hover:-translate-y-1 transition duration-300">
                            <img src="{{ asset('assets/images/screenshots/laptop-4.jpeg') }}"
                                alt="Paparan laptop Laporan Kematian e-Pusara"
                                class="w-full h-auto object-contain rounded-xl">
                        </div>

                        <h4 class="font-bold text-slate-900 mt-4">
                            Lokasi Kubur
                        </h4>

                        <p class="text-xs text-slate-500 mt-1">
                            Paparan pelan kawasan perkuburan dan panduan kedudukan pusara.
                        </p>
                    </article>

                    {{-- Laptop 5 --}}
                    <article class="group">
                        <div class="rounded-[24px] bg-sand-50 border border-sand-100 p-4 shadow-card group-hover:-translate-y-1 transition duration-300">
                            <img src="{{ asset('assets/images/screenshots/laptop-5.jpeg') }}"
                                alt="Paparan laptop Peta Lokasi Pusara e-Pusara"
                                class="w-full h-auto object-contain rounded-xl">
                        </div>

                        <h4 class="font-bold text-slate-900 mt-4">
                            Tempahan Kepukan
                        </h4>

                        <p class="text-xs text-slate-500 mt-1">
                            paparan tempahan kepukan dan batu nisan.
                        </p>
                    </article>

                </div>
            </div>

        </div>
    </section>


    {{-- Ziarah Highlight --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 md:py-20">
        <div class="relative overflow-hidden rounded-[34px] bg-brand-900 shadow-hero">

            <div class="absolute inset-0 opacity-25">
                <img src="{{ asset('assets/images/pusara/hero-ziarah.jpg') }}"
                     alt="Kawasan perkuburan"
                     class="w-full h-full object-cover">
            </div>

            <div class="absolute inset-0 bg-gradient-to-r from-brand-900 via-brand-900/95 to-brand-800/75"></div>

            <div class="relative z-10 grid lg:grid-cols-12 gap-8 items-center p-7 md:p-12">

                <div class="lg:col-span-7">
                    <span class="inline-flex items-center gap-2 bg-white/10 border border-white/10 text-brand-100 rounded-full px-4 py-2 text-xs font-bold uppercase tracking-widest mb-5">
                        <i class="fa-solid fa-location-dot"></i>
                        Modul Ziarah Kubur
                    </span>

                    <h2 class="font-serif text-white font-bold text-3xl md:text-4xl leading-tight">
                        Cari Kedudukan Pusara RTB Bukit Changgang Dengan Lebih Mudah
                    </h2>

                    <p class="text-brand-100/80 text-sm md:text-base leading-relaxed max-w-xl mt-4">
                        Pengunjung boleh mencari lokasi pusara di RTB Bukit Changgang menggunakan nama si mati atau nombor lot, kemudian melihat panduan lokasi untuk tujuan ziarah.
                    </p>

                    <a href="{{ route('public.grave-search.index') }}"
                       class="mt-7 inline-flex items-center justify-center gap-2 rounded-full bg-white hover:bg-brand-50 text-brand-800 px-7 py-4 text-sm font-bold transition">
                        <i class="fa-solid fa-map-location-dot"></i>
                        Buka Carian Ziarah
                    </a>
                </div>

                <div class="lg:col-span-5">
                    <div class="rounded-3xl bg-white/10 backdrop-blur border border-white/10 p-5">

                        <div class="bg-white rounded-2xl p-5">
                            <div class="flex items-center justify-between mb-5">
                                <div class="h-11 w-11 rounded-xl bg-brand-50 text-brand-700 flex items-center justify-center">
                                    <i class="fa-solid fa-location-crosshairs"></i>
                                </div>

                                <span class="inline-flex bg-green-50 text-green-700 rounded-full px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider">
                                    Lokasi Tersedia
                                </span>
                            </div>

                            <h3 class="font-bold text-slate-900 text-lg">
                                Contoh Paparan Pusara
                            </h3>

                            <p class="text-xs text-slate-500 mt-1 mb-5">
                                Maklumat asas pusara untuk kegunaan ziarah sahaja.
                            </p>

                            <div class="grid grid-cols-3 gap-2">
                                <div class="rounded-xl bg-slate-50 border border-slate-100 p-3 text-center">
                                    <p class="text-[10px] uppercase font-bold text-slate-400">
                                        Zon
                                    </p>
                                    <p class="text-xs font-bold text-brand-700 mt-1">
                                        Lelaki
                                    </p>
                                </div>

                                <div class="rounded-xl bg-slate-50 border border-slate-100 p-3 text-center">
                                    <p class="text-[10px] uppercase font-bold text-slate-400">
                                        Lot
                                    </p>
                                    <p class="text-xs font-bold text-slate-800 mt-1">
                                        L-012
                                    </p>
                                </div>

                                <div class="rounded-xl bg-slate-50 border border-slate-100 p-3 text-center">
                                    <p class="text-[10px] uppercase font-bold text-slate-400">
                                        Baris
                                    </p>
                                    <p class="text-xs font-bold text-slate-800 mt-1">
                                        04
                                    </p>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- Laporan Kematian / Emergency Help --}}
    <section class="bg-white border-y border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 md:py-20">

            <div class="grid lg:grid-cols-12 gap-8 lg:gap-12 items-center">

                <div class="lg:col-span-6">
                    <span class="inline-flex bg-amber-50 text-amber-700 font-bold text-xs tracking-[.18em] uppercase rounded-full px-4 py-2 mb-4">
                        Bantuan Waris
                    </span>

                    <h2 class="font-serif font-bold text-3xl md:text-4xl text-slate-900">
                        Perlu Melaporkan Kematian?
                    </h2>

                    <p class="text-slate-500 text-sm md:text-base leading-relaxed mt-4 max-w-xl">
                        Waris atau pelapor bagi kawasan RTB Bukit Changgang boleh menghubungi pentadbir untuk tindakan lanjut berkaitan laporan kematian dan urusan pengurusan yang diperlukan.
                    </p>

                    <div class="flex flex-col sm:flex-row gap-3 mt-7">
                        <a href="{{ route('whatsapp.lapor-kematian') }}"
                           class="inline-flex items-center justify-center gap-2 rounded-full bg-green-600 hover:bg-green-700 text-white px-6 py-3.5 text-sm font-bold transition">
                            <i class="fa-brands fa-whatsapp text-lg"></i>
                            WhatsApp Pentadbir
                        </a>

                        <a href="tel:0132186469"
                           class="inline-flex items-center justify-center gap-2 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 px-6 py-3.5 text-sm font-bold transition">
                            <i class="fa-solid fa-phone"></i>
                            Hubungi Pentadbir
                        </a>
                    </div>
                </div>

                <div class="lg:col-span-6">
                    <div class="grid sm:grid-cols-3 gap-4">

                        <div class="rounded-3xl border border-slate-100 bg-sand-50 p-5">
                            <span class="h-9 w-9 rounded-xl bg-white shadow-sm text-brand-700 flex items-center justify-center text-sm font-bold mb-4">
                                1
                            </span>

                            <h3 class="font-bold text-sm text-slate-900">
                                Maklumkan
                            </h3>

                            <p class="text-xs text-slate-500 leading-relaxed mt-2">
                                Hubungi pentadbir RTB Bukit Changgang berkaitan kematian.
                            </p>
                        </div>

                        <div class="rounded-3xl border border-slate-100 bg-sand-50 p-5">
                            <span class="h-9 w-9 rounded-xl bg-white shadow-sm text-brand-700 flex items-center justify-center text-sm font-bold mb-4">
                                2
                            </span>

                            <h3 class="font-bold text-sm text-slate-900">
                                Semakan
                            </h3>

                            <p class="text-xs text-slate-500 leading-relaxed mt-2">
                                Maklumat disemak oleh pentadbir.
                            </p>
                        </div>

                        <div class="rounded-3xl border border-slate-100 bg-sand-50 p-5">
                            <span class="h-9 w-9 rounded-xl bg-white shadow-sm text-brand-700 flex items-center justify-center text-sm font-bold mb-4">
                                3
                            </span>

                            <h3 class="font-bold text-sm text-slate-900">
                                Tindakan
                            </h3>

                            <p class="text-xs text-slate-500 leading-relaxed mt-2">
                                Urusan seterusnya diproses.
                            </p>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- Final CTA --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 md:py-20">
        <div class="rounded-[32px] bg-brand-50 border border-brand-100 p-7 md:p-12 text-center">

            <h2 class="font-serif font-bold text-3xl md:text-4xl text-slate-900">
                Mulakan Dengan ePusara RTB Bukit Changgang
            </h2>

            <p class="text-slate-500 text-sm md:text-base leading-relaxed max-w-2xl mx-auto mt-4">
                Daftar sebagai ahli RTB Bukit Changgang untuk mengurus rekod khairat, atau gunakan kemudahan carian pusara bagi tujuan ziarah.
            </p>

            <div class="flex flex-col sm:flex-row justify-center gap-3 mt-8">

                @guest
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                           class="inline-flex items-center justify-center gap-2 rounded-full bg-brand-600 hover:bg-brand-700 text-white px-7 py-4 text-sm font-bold transition">
                            <i class="fa-solid fa-user-plus"></i>
                            Daftar Sekarang
                        </a>
                    @endif

                    <a href="{{ route('public.grave-search.index') }}"
                       class="inline-flex items-center justify-center gap-2 rounded-full bg-white border border-brand-200 hover:bg-brand-100 text-brand-700 px-7 py-4 text-sm font-bold transition">
                        <i class="fa-solid fa-location-dot"></i>
                        Cari Lokasi Pusara
                    </a>
                @else
                    <a href="{{ route('dashboard') }}"
                       class="inline-flex items-center justify-center gap-2 rounded-full bg-brand-600 hover:bg-brand-700 text-white px-7 py-4 text-sm font-bold transition">
                        <i class="fa-solid fa-gauge-high"></i>
                        Dashboard
                    </a>

                    <a href="{{ route('public.grave-search.index') }}"
                       class="inline-flex items-center justify-center gap-2 rounded-full bg-white border border-brand-200 hover:bg-brand-100 text-brand-700 px-7 py-4 text-sm font-bold transition">
                        <i class="fa-solid fa-location-dot"></i>
                        Ziarah Kubur
                    </a>
                @endguest

            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="bg-brand-900 text-brand-100 border-t border-brand-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 pb-7">

            <div class="grid md:grid-cols-12 gap-9 pb-10">

                {{-- Brand Summary --}}
                <div class="md:col-span-5">
                    <a href="{{ url('/') }}" class="inline-flex items-center gap-3">
                        <img src="{{ asset('assets/images/logo_rtb.jpg') }}"
                             alt="Logo e-Pusara"
                             class="h-12 w-12 rounded-xl bg-white object-cover border border-white/20">

                        <div>
                            <p class="font-extrabold text-white text-xl leading-none">
                                ePusara
                            </p>

                            <p class="text-[10px] text-brand-100/70 font-semibold uppercase tracking-wider mt-1">
                                Sistem Pengurusan Perkuburan RTB Bukit Changgang
                            </p>
                        </div>
                    </a>

                    <p class="text-sm text-brand-100/75 leading-relaxed max-w-md mt-5">
                        Sistem pengurusan khairat kematian dan lokasi perkuburan bagi memudahkan urusan ahli, waris serta pengunjung di RTB Bukit Changgang.
                    </p>
                </div>

                {{-- Quick Links --}}
                <div class="md:col-span-4">
                    <h4 class="font-bold text-white text-sm mb-4">
                        Pautan Pantas
                    </h4>

                    <ul class="space-y-3 text-sm text-brand-100/75">
                        <li>
                            <a href="{{ url('/') }}"
                               class="hover:text-white transition inline-flex items-center gap-2">
                                <i class="fa-solid fa-angle-right text-brand-200 text-xs"></i>
                                Laman Utama
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('public.grave-search.index') }}"
                               class="hover:text-white transition inline-flex items-center gap-2">
                                <i class="fa-solid fa-angle-right text-brand-200 text-xs"></i>
                                Ziarah Kubur
                            </a>
                        </li>

                        @guest
                            <li>
                                <a href="{{ route('login') }}"
                                   class="hover:text-white transition inline-flex items-center gap-2">
                                    <i class="fa-solid fa-angle-right text-brand-200 text-xs"></i>
                                    Log Masuk
                                </a>
                            </li>
                        @else
                            <li>
                                <a href="{{ route('dashboard') }}"
                                   class="hover:text-white transition inline-flex items-center gap-2">
                                    <i class="fa-solid fa-angle-right text-brand-200 text-xs"></i>
                                    Dashboard
                                </a>
                            </li>
                        @endguest
                    </ul>
                </div>

                {{-- Contact --}}
                <div class="md:col-span-3">
                    <h4 class="font-bold text-white text-sm mb-4">
                        Bantuan &amp; Hubungan
                    </h4>

                    <p class="text-sm text-brand-100/75 leading-relaxed">
                        Untuk laporan kematian atau pertanyaan berkaitan pengurusan khairat RTB Bukit Changgang, sila hubungi pentadbir.
                    </p>

                    <a href="{{ route('whatsapp.lapor-kematian') }}"
                       class="inline-flex items-center gap-2 mt-4 rounded-full bg-white/10 hover:bg-white/15 px-4 py-2.5 text-xs font-bold text-brand-100 transition">
                        <i class="fa-brands fa-whatsapp"></i>
                        WhatsApp Pentadbir
                    </a>
                </div>

            </div>

            <div class="pt-6 border-t border-brand-800 flex flex-col sm:flex-row justify-between items-center gap-3 text-xs text-brand-100/60">
                <p>
                    &copy; {{ date('Y') }} ePusara. Hak cipta terpelihara.
                </p>

                <p>
                    Sistem Pengurusan Khairat Kematian &amp; Perkuburan RTB Bukit Changgang
                </p>
            </div>
        </div>
    </footer>

    <script>
        function showSystemGallery(type) {
            const mobileGallery = document.getElementById('mobileGallery');
            const laptopGallery = document.getElementById('laptopGallery');
            const mobileTab = document.getElementById('mobileTab');
            const laptopTab = document.getElementById('laptopTab');

            if (type === 'mobile') {
                mobileGallery.classList.remove('hidden');
                laptopGallery.classList.add('hidden');

                mobileTab.classList.add('bg-brand-600', 'text-white');
                mobileTab.classList.remove('text-slate-500');

                laptopTab.classList.remove('bg-brand-600', 'text-white');
                laptopTab.classList.add('text-slate-500');
            } else {
                laptopGallery.classList.remove('hidden');
                mobileGallery.classList.add('hidden');

                laptopTab.classList.add('bg-brand-600', 'text-white');
                laptopTab.classList.remove('text-slate-500');

                mobileTab.classList.remove('bg-brand-600', 'text-white');
                mobileTab.classList.add('text-slate-500');
            }
        }

        function scrollMobileGallery(direction) {
            const slider = document.getElementById('mobileSlider');

            slider.scrollBy({
                left: direction * 382,
                behavior: 'smooth'
            });
        }

    </script>

</body>
</html>