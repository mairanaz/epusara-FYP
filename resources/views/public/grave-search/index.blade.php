<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ziarah Kubur | e-Pusara</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>

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
                        soft: '0 20px 55px rgba(15, 23, 42, 0.07)',
                        card: '0 20px 48px rgba(15, 23, 42, 0.08)'
                    }
                }
            }
        }
    </script>

    <style>
        html {
            scroll-behavior: smooth;
        }

        .fade-up {
            animation: fadeUp .65s ease-out both;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(15px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .photo-overlay {
            background: linear-gradient(
                180deg,
                rgba(15, 23, 42, 0.04) 20%,
                rgba(15, 23, 42, 0.84) 100%
            );
        }
    </style>
</head>

<body class="min-h-screen bg-sand-50 text-slate-800 font-sans antialiased">

    {{-- Bar makluman awam --}}
    <div class="bg-brand-900 text-brand-100 px-4 py-2.5 text-xs md:text-sm">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row justify-between items-center gap-2">
            <p class="flex items-center gap-2 text-center sm:text-left">
                <i class="fa-solid fa-circle-info text-brand-200"></i>
                Carian lokasi pusara disediakan kepada orang awam bagi memudahkan urusan ziarah.
            </p>

            <button type="button"
                    onclick="openModal('adab-modal')"
                    class="font-semibold underline underline-offset-4 hover:text-white">
                Panduan adab ziarah
            </button>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="sticky top-0 z-50 bg-white/95 backdrop-blur border-b border-slate-100 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">

            <a href="{{ url('/') }}" class="flex items-center gap-3">
                <img src="{{ asset('assets/images/logo_rtb.jpg') }}"
                     alt="Logo e-Pusara"
                     class="h-12 w-12 rounded-xl border border-slate-100 object-cover">

                <div class="hidden sm:block">
                    <p class="font-extrabold text-brand-800 leading-none text-xl">
                        e-Pusara
                    </p>
                    <p class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider mt-1">
                        Sistem Pengurusan Perkuburan
                    </p>
                </div>
            </a>

            <div class="flex items-center gap-2 sm:gap-3">
                <a href="{{ url('/') }}"
                   class="hidden md:inline-flex text-sm font-semibold text-slate-600 px-4 py-2 hover:text-brand-700">
                    Laman Utama
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

                    <a href="{{ route('register') }}"
                       class="hidden sm:inline-flex text-sm font-bold text-white bg-brand-600 hover:bg-brand-700 rounded-full px-5 py-2.5 transition">
                        Daftar
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    {{-- Hero dan carian utama --}}
    <section class="relative overflow-hidden border-b border-slate-100 bg-white">

        {{-- Gambar latar belakang kawasan perkuburan --}}
        <div class="absolute inset-0">
            <img src="{{ asset('assets/images/pusara/hero-ziarah.jpg') }}"
                 alt="Persekitaran kawasan perkuburan e-Pusara"
                 class="w-full h-full object-cover object-center">
        </div>

        {{-- Overlay supaya tulisan mudah dibaca --}}
        <div class="absolute inset-0 bg-white/55"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-white/95 via-white/72 to-brand-900/20"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-16 relative z-10">
            <div class="grid lg:grid-cols-12 gap-8 lg:gap-12 items-center">

                {{-- Kiri: Pengenalan Modul Ziarah --}}
                <div class="lg:col-span-7 fade-up">

                    <span class="inline-flex items-center gap-2 bg-brand-50/95 text-brand-700 rounded-full px-4 py-2 text-xs font-bold uppercase tracking-widest mb-5 border border-brand-100">
                        <i class="fa-solid fa-location-dot"></i>
                        Ziarah Kubur
                    </span>

                    <h1 class="font-serif text-4xl sm:text-5xl lg:text-[3.4rem] leading-tight text-slate-900 font-bold">
                        Cari Lokasi Pusara Dengan Lebih Mudah
                    </h1>

                    <p class="text-slate-600 text-sm sm:text-base leading-relaxed max-w-xl mt-4">
                        e-Pusara membantu waris dan pengunjung mendapatkan kedudukan lot
                        pusara si mati secara pantas melalui carian nama atau nombor lot
                        yang didaftarkan.
                    </p>

                    <div class="grid sm:grid-cols-3 gap-3 mt-8 max-w-2xl">

                        <div class="rounded-2xl bg-white/95 backdrop-blur-md border border-white p-4 shadow-sm">
                            <i class="fa-solid fa-magnifying-glass text-brand-600 mb-2"></i>
                            <p class="text-xs font-bold text-slate-800">Carian Mudah</p>
                            <p class="text-[11px] text-slate-500 mt-1">Nama atau no. lot</p>
                        </div>

                        <div class="rounded-2xl bg-white/95 backdrop-blur-md border border-white p-4 shadow-sm">
                            <i class="fa-solid fa-map-location-dot text-brand-600 mb-2"></i>
                            <p class="text-xs font-bold text-slate-800">Lokasi Lot</p>
                            <p class="text-[11px] text-slate-500 mt-1">Panduan kedudukan</p>
                        </div>

                        <div class="rounded-2xl bg-white/95 backdrop-blur-md border border-white p-4 shadow-sm">
                            <i class="fa-solid fa-shield-halved text-brand-600 mb-2"></i>
                            <p class="text-xs font-bold text-slate-800">Privasi Dijaga</p>
                            <p class="text-[11px] text-slate-500 mt-1">Maklumat asas sahaja</p>
                        </div>

                    </div>
                </div>

                {{-- Kanan: Borang Carian --}}
                <div class="lg:col-span-5 fade-up" style="animation-delay:.08s">
                    <div class="bg-white/95 backdrop-blur-md rounded-[28px] border border-white shadow-soft p-6 md:p-8">

                        <div class="flex items-center gap-3 mb-5">
                            <div class="h-11 w-11 rounded-2xl bg-brand-50 text-brand-700 flex items-center justify-center">
                                <i class="fa-solid fa-magnifying-glass-location text-lg"></i>
                            </div>

                            <div>
                                <h2 class="font-bold text-lg text-slate-900">
                                    Carian Pusara
                                </h2>
                                <p class="text-xs text-slate-500">
                                    Semak lokasi berdaftar
                                </p>
                            </div>
                        </div>

                        <form action="{{ route('public.grave-search.index') }}#keputusan-carian"
                              method="GET"
                              class="space-y-4">

                            <label for="search" class="text-xs font-bold text-slate-600 block">
                                Nama si mati atau nombor lot
                            </label>

                            <div class="relative">
                                <i class="fa-regular fa-user absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>

                                <input type="text"
                                       id="search"
                                       name="search"
                                       value="{{ $search ?? '' }}"
                                       placeholder="Contoh: Ahmad bin Ali / L-012"
                                       autocomplete="off"
                                       class="w-full rounded-2xl border border-slate-200 bg-slate-50 pl-11 pr-4 py-4 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition">
                            </div>

                            <button type="submit"
                                    class="w-full rounded-2xl bg-brand-600 hover:bg-brand-700 text-white py-4 px-5 font-bold text-sm flex justify-center items-center gap-2 transition shadow-lg shadow-brand-600/20">
                                <i class="fa-solid fa-location-crosshairs"></i>
                                Cari Lokasi Pusara
                            </button>
                        </form>

                        <p class="border-t border-slate-100 mt-5 pt-4 text-[11px] text-slate-500 leading-relaxed">
                            <i class="fa-solid fa-lock text-brand-600 mr-1"></i>
                            Paparan awam hanya menunjukkan nama, tarikh meninggal dan kedudukan lot pusara.
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-14">

        {{-- Paparan ralat --}}
        @if(session('error'))
            <div class="mb-8 rounded-2xl border border-red-200 bg-red-50 p-4 flex items-start gap-3 text-red-700">
                <i class="fa-solid fa-circle-exclamation mt-0.5"></i>
                <p class="text-sm">{{ session('error') }}</p>
            </div>
        @endif

        {{-- Keputusan carian --}}
        @if(!empty($search))
            <section id="keputusan-carian" class="scroll-mt-28 mb-12 md:mb-16 fade-up">

                <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
                    <div>
                        <span class="text-brand-700 font-bold text-xs tracking-widest uppercase">
                            Carian Lokasi Lot
                        </span>

                        <h2 class="font-serif text-3xl md:text-4xl font-bold text-slate-900 mt-2">
                            Keputusan Carian Pusara
                        </h2>

                        <p class="text-sm text-slate-500 mt-2">
                            Lokasi pusara yang telah direkodkan dalam sistem e-Pusara.
                        </p>
                    </div>

                    <a href="{{ route('public.grave-search.index') }}"
                       class="inline-flex items-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-full px-5 py-2.5 text-xs transition">
                        <i class="fa-solid fa-rotate-left"></i>
                        Set Semula
                    </a>
                </div>

                @if(isset($deathReports) && $deathReports->count() > 0)

                    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($deathReports as $report)
                            @php
                                $plot = $report->final_burial_plot;
                            @endphp

                            <article class="bg-white border border-slate-100 rounded-3xl p-6 shadow-card flex flex-col justify-between hover:-translate-y-1 transition duration-300">
                                <div>

                                    <div class="flex items-center justify-between gap-2 mb-5">
                                        <span class="inline-flex items-center gap-1.5 bg-brand-50 text-brand-700 rounded-full px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider">
                                            <span class="w-1.5 h-1.5 bg-brand-500 rounded-full"></span>
                                            Lokasi Tersedia
                                        </span>

                                        <i class="fa-solid fa-location-dot text-slate-300"></i>
                                    </div>

                                    <h3 class="font-bold text-xl text-slate-900">
                                        {{ $report->nama_si_mati }}
                                    </h3>

                                    <p class="text-xs text-slate-500 mt-2 mb-5">
                                        <i class="fa-regular fa-calendar mr-1"></i>
                                        Tarikh meninggal:
                                        <span class="font-semibold text-slate-700">
                                            {{ $report->tarikh_meninggal ? $report->tarikh_meninggal->format('d/m/Y') : '-' }}
                                        </span>
                                    </p>

                                    <div class="grid grid-cols-3 gap-2 mb-6">

                                        <div class="bg-slate-50 rounded-xl p-3 text-center border border-slate-100">
                                            <span class="block text-[10px] uppercase font-bold tracking-wider text-slate-400 mb-1">
                                                Zon
                                            </span>
                                            <strong class="text-xs text-brand-700">
                                                {{ $plot->zone_label ?? '-' }}
                                            </strong>
                                        </div>

                                        <div class="bg-slate-50 rounded-xl p-3 text-center border border-slate-100">
                                            <span class="block text-[10px] uppercase font-bold tracking-wider text-slate-400 mb-1">
                                                Lot
                                            </span>
                                            <strong class="text-xs text-slate-800">
                                                {{ $plot->plot_code ?? '-' }}
                                            </strong>
                                        </div>

                                        <div class="bg-slate-50 rounded-xl p-3 text-center border border-slate-100">
                                            <span class="block text-[10px] uppercase font-bold tracking-wider text-slate-400 mb-1">
                                                Baris
                                            </span>
                                            <strong class="text-xs text-slate-800">
                                                {{ $plot->row_number ?? '-' }}
                                            </strong>
                                        </div>

                                    </div>
                                </div>

                                <a href="{{ route('public.grave-search.show', $report->id) }}"
                                   class="w-full rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-sm py-3.5 flex items-center justify-center gap-2 transition">
                                    <i class="fa-solid fa-map-location-dot"></i>
                                    Lihat Lokasi Kubur
                                </a>
                            </article>
                        @endforeach
                    </div>

                    @if(method_exists($deathReports, 'links'))
                        <div class="mt-8">
                            {{ $deathReports->appends(['search' => $search])->fragment('keputusan-carian')->links() }}
                        </div>
                    @endif

                @else

                    <div class="max-w-xl text-center rounded-3xl border-2 border-dashed border-slate-200 bg-white p-9 md:p-12 shadow-soft">
                        <div class="h-16 w-16 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center mx-auto mb-5 text-xl">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </div>

                        <h3 class="font-bold text-xl text-slate-900">
                            Tiada Rekod Dijumpai
                        </h3>

                        <p class="text-sm text-slate-500 mt-2 leading-relaxed">
                            Tiada lokasi pusara ditemui bagi carian
                            <span class="font-bold text-slate-700">“{{ $search }}”</span>.
                            Sila semak ejaan atau cuba nombor lot.
                        </p>
                    </div>

                @endif
            </section>
        @endif

        {{-- Paparan sebelum user membuat carian --}}
        @if(empty($search))
            <section class="mb-10 md:mb-14">
                <div class="rounded-2xl border border-brand-100 bg-brand-50/60 py-4 px-5 text-center text-sm text-brand-800">
                    <i class="fa-solid fa-circle-info mr-2"></i>
                    Masukkan nama si mati atau nombor lot pada ruangan carian di atas untuk melihat lokasi pusara.
                </div>
            </section>
        @endif

        {{-- Notis privasi --}}
        <div class="mb-12 md:mb-16 rounded-2xl border border-amber-200 bg-amber-50 p-5 flex items-start gap-4">
            <div class="shrink-0 h-10 w-10 rounded-xl bg-amber-500 text-white flex items-center justify-center">
                <i class="fa-solid fa-circle-info"></i>
            </div>

            <div>
                <h3 class="font-bold text-sm md:text-base text-slate-900">
                    Maklumat Untuk Tujuan Ziarah Sahaja
                </h3>

                <p class="text-xs md:text-sm text-slate-600 leading-relaxed mt-1">
                    Bagi menjaga privasi keluarga, nombor kad pengenalan, alamat dan dokumen kematian tidak dipaparkan kepada orang awam.
                </p>
            </div>
        </div>

        {{-- Persekitaran & Landskap Perkuburan --}}
        <section class="mb-14 md:mb-20">

            <div class="max-w-3xl mx-auto text-center mb-8 md:mb-10">
                <span class="inline-flex bg-brand-50 text-brand-700 font-bold text-xs tracking-[.18em] uppercase rounded-full px-4 py-2 mb-4">
                    Galeri Kawasan e-Pusara
                </span>

                <h2 class="font-serif font-bold text-3xl md:text-4xl text-slate-900">
                    Persekitaran &amp; Landskap Perkuburan
                </h2>

                <p class="text-slate-500 text-sm md:text-base leading-relaxed mt-3">
                    Paparan keadaan kawasan perkuburan, laluan ziarah dan kemudahan awam yang disediakan bagi keselesaan pengunjung.
                </p>
            </div>

            <div class="grid md:grid-cols-12 gap-5 md:gap-6">

                <article class="md:col-span-7 relative min-h-[370px] md:min-h-[470px] overflow-hidden rounded-[28px] group shadow-soft bg-slate-100">
                    <img src="{{ asset('assets/images/pusara/persekitaran-kubur.jpg') }}"
                         alt="Persekitaran tanah perkuburan e-Pusara"
                         class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition duration-700">

                    <div class="photo-overlay absolute inset-0 p-6 md:p-8 flex flex-col justify-end">
                        <span class="inline-flex w-fit bg-brand-600 text-white rounded-full px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider mb-3">
                            Kawasan Perkuburan
                        </span>

                        <h3 class="font-serif font-bold text-2xl text-white">
                            Kawasan Ziarah Yang Terpelihara
                        </h3>

                        <p class="text-slate-200 text-xs md:text-sm max-w-lg mt-2 leading-relaxed">
                            Persekitaran yang bersih dan tersusun membantu pengunjung melakukan ziarah dengan lebih selesa dan tenang.
                        </p>
                    </div>
                </article>

                <div class="md:col-span-5 grid gap-5 md:gap-6">

                    <article class="relative min-h-[220px] overflow-hidden rounded-[28px] group shadow-soft bg-slate-100">
                        <img src="{{ asset('assets/images/pusara/laluan-ziarah.jpg') }}"
                             alt="Laluan pejalan kaki ke kawasan lot pusara"
                             class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition duration-700">

                        <div class="photo-overlay absolute inset-0 p-5 md:p-6 flex flex-col justify-end">
                            <h3 class="font-bold text-white text-lg">
                                Laluan Pejalan Kaki
                            </h3>

                            <p class="text-slate-200 text-xs mt-1">
                                Laluan yang memudahkan pengunjung menuju ke plot pusara.
                            </p>
                        </div>
                    </article>

                    <article class="relative min-h-[220px] overflow-hidden rounded-[28px] group shadow-soft bg-slate-100">
                        <img src="{{ asset('assets/images/pusara/kemudahan-awam.jpg') }}"
                             alt="Kemudahan awam di kawasan perkuburan"
                             class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition duration-700">

                        <div class="photo-overlay absolute inset-0 p-5 md:p-6 flex flex-col justify-end">
                            <h3 class="font-bold text-white text-lg">
                                Kemudahan Pengunjung
                            </h3>

                            <p class="text-slate-200 text-xs mt-1">
                                Maklumat parkir, pintu masuk dan kemudahan berkaitan ziarah.
                            </p>
                        </div>
                    </article>

                </div>
            </div>
        </section>

        {{-- Panduan Amalan Mulia --}}
        <section class="mb-14 md:mb-20 border-t border-slate-100 pt-14 md:pt-16">

            <div class="max-w-3xl mx-auto text-center mb-8 md:mb-10">
                <span class="inline-flex bg-brand-50 text-brand-700 font-bold text-xs tracking-[.18em] uppercase rounded-full px-4 py-2 mb-4">
                    Panduan Amalan Mulia
                </span>

                <h2 class="font-serif font-bold text-3xl md:text-4xl text-slate-900">
                    Panduan Untuk Ziarah Kubur
                </h2>

                <p class="text-slate-500 text-sm md:text-base leading-relaxed mt-3">
                    Rujukan ringkas untuk membantu pengunjung menjaga adab, membaca doa dan melakukan amalan ziarah dengan tertib.
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-5 md:gap-6">

                {{-- Kad Doa --}}
                <article class="bg-white border border-slate-100 rounded-3xl p-6 shadow-card flex flex-col justify-between hover:-translate-y-1 transition duration-300">
                    <div>
                        <div class="h-12 w-12 rounded-2xl bg-brand-50 text-brand-700 flex items-center justify-center text-lg mb-5">
                            <i class="fa-solid fa-book-open-reader"></i>
                        </div>

                        <h3 class="font-bold text-lg text-slate-900">
                            Doa Ringkas Ziarah
                        </h3>

                        <p class="text-sm text-slate-500 leading-relaxed mt-2 mb-6">
                            Bacaan salam ketika menziarahi perkuburan disertakan teks Arab, rumi dan maksud ringkas.
                        </p>
                    </div>

                    <button type="button"
                            onclick="openModal('doa-modal')"
                            class="w-full rounded-xl bg-brand-50 hover:bg-brand-100 text-brand-700 font-bold text-sm py-3 flex items-center justify-center gap-2 transition">
                        <i class="fa-solid fa-book"></i>
                        Baca Doa
                    </button>
                </article>

                {{-- Kad Tahlil --}}
                <article class="bg-white border border-slate-100 rounded-3xl p-6 shadow-card flex flex-col justify-between hover:-translate-y-1 transition duration-300">
                    <div>
                        <div class="h-12 w-12 rounded-2xl bg-brand-50 text-brand-700 flex items-center justify-center text-lg mb-5">
                            <i class="fa-solid fa-file-lines"></i>
                        </div>

                        <h3 class="font-bold text-lg text-slate-900">
                            Tahlil &amp; Yasin Ringkas
                        </h3>

                        <p class="text-sm text-slate-500 leading-relaxed mt-2 mb-6">
                            Panduan susunan bacaan ringkas sebagai rujukan pengunjung semasa berziarah.
                        </p>
                    </div>

                    <button type="button"
                            onclick="openModal('tahlil-modal')"
                            class="w-full rounded-xl bg-brand-50 hover:bg-brand-100 text-brand-700 font-bold text-sm py-3 flex items-center justify-center gap-2 transition">
                        <i class="fa-solid fa-file-lines"></i>
                        Lihat Panduan
                    </button>
                </article>

                {{-- Kad Adab --}}
                <article class="bg-white border border-slate-100 rounded-3xl p-6 shadow-card flex flex-col justify-between hover:-translate-y-1 transition duration-300">
                    <div>
                        <div class="h-12 w-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg mb-5">
                            <i class="fa-solid fa-hand-holding-heart"></i>
                        </div>

                        <h3 class="font-bold text-lg text-slate-900">
                            Adab Ziarah Kubur
                        </h3>

                        <p class="text-sm text-slate-500 leading-relaxed mt-2 mb-6">
                            Tatatertib asas bagi menjaga kehormatan kawasan perkuburan dan keselesaan pengunjung lain.
                        </p>
                    </div>

                    <button type="button"
                            onclick="openModal('adab-modal')"
                            class="w-full rounded-xl bg-brand-50 hover:bg-brand-100 text-brand-700 font-bold text-sm py-3 flex items-center justify-center gap-2 transition">
                        <i class="fa-solid fa-list-check"></i>
                        Senarai Adab
                    </button>
                </article>

            </div>
        </section>

    </main>

    {{-- Footer --}}
    <footer class="bg-brand-900 text-brand-100 border-t border-brand-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 pb-7">

            <div class="grid md:grid-cols-12 gap-9 pb-10">

                {{-- Ringkasan sistem --}}
                <div class="md:col-span-5">
                    <a href="{{ url('/') }}" class="inline-flex items-center gap-3">
                        <img src="{{ asset('assets/images/logo_rtb.jpg') }}"
                             alt="Logo e-Pusara"
                             class="h-12 w-12 rounded-xl bg-white object-cover border border-white/20">

                        <div>
                            <p class="font-extrabold text-white text-xl leading-none">
                                e-Pusara
                            </p>

                            <p class="text-[10px] text-brand-100/70 font-semibold uppercase tracking-wider mt-1">
                                Sistem Pengurusan Perkuburan
                            </p>
                        </div>
                    </a>

                    <p class="text-sm text-brand-100/75 leading-relaxed max-w-md mt-5">
                        Sistem pengurusan khairat kematian dan lokasi perkuburan yang memudahkan urusan waris serta pengunjung membuat carian pusara.
                    </p>
                </div>

                {{-- Pautan pantas --}}
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
                                Carian Lokasi Pusara
                            </a>
                        </li>

                        <li>
                            <button type="button"
                                    onclick="openModal('adab-modal')"
                                    class="hover:text-white transition inline-flex items-center gap-2">
                                <i class="fa-solid fa-angle-right text-brand-200 text-xs"></i>
                                Panduan Adab Ziarah
                            </button>
                        </li>
                    </ul>
                </div>

                {{-- Maklumat modul --}}
                <div class="md:col-span-3">
                    <h4 class="font-bold text-white text-sm mb-4">
                        Modul Ziarah Kubur
                    </h4>

                    <p class="text-sm text-brand-100/75 leading-relaxed">
                        Carian lokasi pusara awam disediakan untuk tujuan ziarah dengan paparan maklumat asas sahaja.
                    </p>

                    <div class="inline-flex items-center gap-2 mt-4 rounded-full bg-white/10 px-3 py-2 text-xs text-brand-100">
                        <i class="fa-solid fa-shield-halved"></i>
                        Privasi waris dijaga
                    </div>
                </div>

            </div>

            <div class="pt-6 border-t border-brand-800 flex flex-col sm:flex-row justify-between items-center gap-3 text-xs text-brand-100/60">
                <p>
                    &copy; {{ date('Y') }} e-Pusara. Hak cipta terpelihara.
                </p>

                <p>
                    Sistem Pengurusan Khairat Kematian &amp; Perkuburan
                </p>
            </div>
        </div>
    </footer>

    {{-- Modal Doa Ziarah --}}
    <div id="doa-modal"
         class="fixed inset-0 z-[999] hidden items-center justify-center bg-slate-900/75 p-4">

        <div class="relative bg-white rounded-3xl w-full max-w-2xl max-h-[88vh] overflow-y-auto p-6 md:p-8 shadow-2xl">

            <button type="button"
                    onclick="closeModal('doa-modal')"
                    class="absolute top-5 right-5 h-9 w-9 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600">
                <i class="fa-solid fa-xmark"></i>
            </button>

            <span class="inline-flex bg-brand-50 text-brand-700 rounded-full px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider mb-4">
                Doa Ziarah
            </span>

            <h2 class="font-serif font-bold text-2xl text-slate-900 mb-6">
                Salam Kepada Penghuni Kubur
            </h2>

            <div class="rounded-2xl bg-slate-50 border border-slate-100 p-5 md:p-6 text-center">
                <p class="font-serif text-2xl md:text-3xl leading-loose text-slate-900" dir="rtl">
                    السَّلَامُ عَلَيْكُمْ دَارَ قَوْمٍ مُؤْمِنِينَ وَإِنَّا إِنْ شَاءَ اللَّهُ بِكُمْ لَاحِقُونَ
                </p>
            </div>

            <div class="mt-5 space-y-4 text-sm">

                <div class="rounded-xl bg-brand-50/60 p-4">
                    <p class="font-bold text-xs uppercase tracking-wider text-brand-700 mb-2">
                        Bacaan Rumi
                    </p>

                    <p class="text-slate-700 italic leading-relaxed">
                        Assalamu 'alaikum dara qoumin mu'minin, wa inna in sya Allahu bikum lahiqun.
                    </p>
                </div>

                <div class="rounded-xl bg-slate-50 p-4">
                    <p class="font-bold text-xs uppercase tracking-wider text-slate-500 mb-2">
                        Maksud
                    </p>

                    <p class="text-slate-600 leading-relaxed">
                        Sejahtera ke atas kamu wahai penghuni tempat kediaman golongan mukmin. Sesungguhnya kami, dengan izin Allah, akan menyusul kamu.
                    </p>
                </div>

            </div>
        </div>
    </div>

    {{-- Modal Tahlil & Yasin Ringkas --}}
    <div id="tahlil-modal"
         class="fixed inset-0 z-[999] hidden items-center justify-center bg-slate-900/75 p-4">

        <div class="relative bg-white rounded-3xl w-full max-w-2xl max-h-[88vh] overflow-y-auto p-6 md:p-8 shadow-2xl">

            <button type="button"
                    onclick="closeModal('tahlil-modal')"
                    class="absolute top-5 right-5 h-9 w-9 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600">
                <i class="fa-solid fa-xmark"></i>
            </button>

            <span class="inline-flex bg-brand-50 text-brand-700 rounded-full px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider mb-4">
                Rujukan Bacaan
            </span>

            <h2 class="font-serif font-bold text-2xl text-slate-900 mb-6">
                Tahlil &amp; Yasin Ringkas
            </h2>

            <div class="space-y-3 text-sm text-slate-600">

                <div class="rounded-2xl border border-brand-100 bg-brand-50/50 p-4 flex gap-3">
                    <span class="h-7 w-7 shrink-0 rounded-full bg-brand-600 text-white text-xs font-bold flex items-center justify-center">
                        1
                    </span>

                    <div>
                        <p class="font-bold text-slate-900">
                            Al-Fatihah
                        </p>

                        <p class="mt-1 leading-relaxed">
                            Mulakan dengan menghadiahkan bacaan Surah Al-Fatihah kepada arwah.
                        </p>
                    </div>
                </div>

                <div class="rounded-2xl border border-brand-100 bg-brand-50/50 p-4 flex gap-3">
                    <span class="h-7 w-7 shrink-0 rounded-full bg-brand-600 text-white text-xs font-bold flex items-center justify-center">
                        2
                    </span>

                    <div>
                        <p class="font-bold text-slate-900">
                            Surah Yasin atau Surah-surah Ringkas
                        </p>

                        <p class="mt-1 leading-relaxed">
                            Baca Surah Yasin, atau bacaan ringkas seperti Al-Ikhlas, Al-Falaq dan An-Nas mengikut kesesuaian.
                        </p>
                    </div>
                </div>

                <div class="rounded-2xl border border-brand-100 bg-brand-50/50 p-4 flex gap-3">
                    <span class="h-7 w-7 shrink-0 rounded-full bg-brand-600 text-white text-xs font-bold flex items-center justify-center">
                        3
                    </span>

                    <div>
                        <p class="font-bold text-slate-900">
                            Tahlil dan Doa
                        </p>

                        <p class="mt-1 leading-relaxed">
                            Akhiri bacaan dengan tahlil serta doa memohon keampunan dan rahmat untuk si mati.
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Modal Adab Ziarah --}}
    <div id="adab-modal"
         class="fixed inset-0 z-[999] hidden items-center justify-center bg-slate-900/75 p-4">

        <div class="relative bg-white rounded-3xl w-full max-w-2xl max-h-[88vh] overflow-y-auto p-6 md:p-8 shadow-2xl">

            <button type="button"
                    onclick="closeModal('adab-modal')"
                    class="absolute top-5 right-5 h-9 w-9 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600">
                <i class="fa-solid fa-xmark"></i>
            </button>

            <span class="inline-flex bg-brand-50 text-brand-700 rounded-full px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider mb-4">
                Adab Ziarah
            </span>

            <h2 class="font-serif font-bold text-2xl text-slate-900 mb-6">
                Panduan Tatatertib Ziarah
            </h2>

            <div class="space-y-3 text-sm text-slate-600">

                <p class="flex gap-3 rounded-xl bg-slate-50 p-4">
                    <span class="font-bold text-brand-700">01</span>
                    Memberi salam dan mendoakan ahli kubur ketika berziarah.
                </p>

                <p class="flex gap-3 rounded-xl bg-slate-50 p-4">
                    <span class="font-bold text-brand-700">02</span>
                    Menjaga kebersihan, ketenangan dan kesopanan sepanjang berada di kawasan perkuburan.
                </p>

                <p class="flex gap-3 rounded-xl bg-slate-50 p-4">
                    <span class="font-bold text-brand-700">03</span>
                    Tidak memijak, duduk atau melangkah di atas lot pusara.
                </p>

                <p class="flex gap-3 rounded-xl bg-slate-50 p-4">
                    <span class="font-bold text-brand-700">04</span>
                    Mengelakkan perbuatan yang mengganggu pengunjung lain atau merosakkan landskap kawasan.
                </p>

            </div>
        </div>
    </div>

    {{-- Script Modal --}}
    <script>
        function openModal(id) {
            const modal = document.getElementById(id);

            if (!modal) {
                return;
            }

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function closeModal(id) {
            const modal = document.getElementById(id);

            if (!modal) {
                return;
            }

            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
        }

        window.addEventListener('click', function (event) {
            ['doa-modal', 'tahlil-modal', 'adab-modal'].forEach(function (id) {
                const modal = document.getElementById(id);

                if (event.target === modal) {
                    closeModal(id);
                }
            });
        });

        window.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                ['doa-modal', 'tahlil-modal', 'adab-modal'].forEach(function (id) {
                    closeModal(id);
                });
            }
        });
    </script>

</body>
</html>