@extends('layouts.app')

@section('content')

@php
    $senaraiNegeri = [
        'Johor',
        'Kedah',
        'Kelantan',
        'Melaka',
        'Negeri Sembilan',
        'Pahang',
        'Perak',
        'Perlis',
        'Pulau Pinang',
        'Sabah',
        'Sarawak',
        'Selangor',
        'Terengganu',
        'W.P. Kuala Lumpur',
        'W.P. Labuan',
        'W.P. Putrajaya',
    ];
@endphp

<style>
    .progress-card {
        background: #fff;
        border-radius: 18px;
        padding: 32px 28px;
        box-shadow: 0 6px 20px rgba(15, 23, 42, 0.06);
        position: relative;
        overflow: hidden;
    }

    .progress-steps {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        position: relative;
        z-index: 2;
    }

    .progress-track {
        position: absolute;
        top: 62px;
        left: calc(12.5% + 31px);
        width: calc(75% - 62px);
        height: 4px;
        background: #dbe3ea;
        z-index: 1;
    }

    .progress-track-fill {
        position: absolute;
        top: 62px;
        left: calc(12.5% + 31px);
        width: 0;
        max-width: calc(75% - 62px);
        height: 4px;
        background: #22c55e;
        z-index: 1;
        transition: width 0.25s ease;
    }

    .progress-step {
        text-align: center;
        position: relative;
    }

    .progress-circle {
        width: 62px;
        height: 62px;
        border-radius: 50%;
        background: #f8fafc;
        border: 4px solid #dbe3ea;
        color: #64748b;
        font-size: 28px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 14px auto;
        transition: all 0.25s ease;
    }

    .progress-title {
        font-size: 16px;
        font-weight: 700;
        color: #64748b;
        line-height: 1.3;
    }

    .progress-subtitle {
        font-size: 14px;
        color: #94a3b8;
        margin-top: 4px;
    }

    .progress-step.active .progress-circle,
    .progress-step.completed .progress-circle {
        background: #22c55e;
        border-color: #d1fae5;
        color: #fff;
    }

    .progress-step.active .progress-title,
    .progress-step.completed .progress-title {
        color: #16a34a;
    }

    .wizard-card {
        border: 0;
        border-radius: 14px;
        box-shadow: 0 4px 16px rgba(15, 23, 42, 0.05);
        overflow: hidden;
    }

    .form-control,
    .form-select {
        border-color: #e2e8f0;
        border-radius: 8px;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #38bdf8;
        box-shadow: 0 0 0 0.2rem rgba(56, 189, 248, 0.15);
    }

    .info-note {
        background: #fff7ed;
        color: #c2410c;
        border: 1px solid #fed7aa;
        border-radius: 10px;
        padding: 14px 16px;
    }

    .readonly-box {
        background: #f8fafc;
        color: #334155;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 0.5rem 0.75rem;
        min-height: 38px;
    }

    @media (max-width: 991px) {
        .progress-steps {
            grid-template-columns: 1fr;
            gap: 24px;
        }

        .progress-track,
        .progress-track-fill {
            display: none;
        }
    }

    .step-nav {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        margin-top: 24px;
        flex-wrap: wrap;
    }

    .step-nav .btn {
        min-width: 140px;
    }

    @media (max-width: 576px) {
        .step-nav {
            flex-direction: column;
            align-items: stretch;
        }

        .step-nav .btn {
            width: 100%;
        }
    }
</style>

<div class="container-fluid">

    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <h1 class="page-title fw-semibold fs-20 mb-1">Lapor Kematian</h1>
            <p class="text-muted mb-0">
                Sila lengkapkan maklumat laporan kematian dengan tepat mengikut langkah yang disediakan.
            </p>
        </div>
    </div>

    @if(session('success'))
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berjaya',
                text: '{{ session('success') }}',
                confirmButtonText: 'OK',
                timer: 2500,
                timerProgressBar: true
            }).then(() => {
                window.location.href = "{{ route('user.dashboard') }}";
            });
        </script>
    @endif

    @if(session('error'))
        <div class="alert alert-danger shadow-sm border-0">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger shadow-sm border-0">
            <div class="fw-semibold mb-2">Sila semak maklumat berikut:</div>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('death-reports.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="progress-card mb-4">
            <div class="progress-track"></div>
            <div class="progress-track-fill" id="progressTrackFill"></div>

            <div class="progress-steps">
                <div class="progress-step active" data-step-indicator="1">
                    <div class="progress-circle">1</div>
                    <div class="progress-label">
                        <div class="progress-title">Maklumat Si Mati</div>
                        <div class="progress-subtitle">Langkah 1</div>
                    </div>
                </div>

                <div class="progress-step" data-step-indicator="2">
                    <div class="progress-circle">2</div>
                    <div class="progress-label">
                        <div class="progress-title">Pengurusan Jenazah</div>
                        <div class="progress-subtitle">Langkah 2</div>
                    </div>
                </div>

                <div class="progress-step" data-step-indicator="3">
                    <div class="progress-circle">3</div>
                    <div class="progress-label">
                        <div class="progress-title">Dokumen Sokongan</div>
                        <div class="progress-subtitle">Langkah 3</div>
                    </div>
                </div>

                <div class="progress-step" data-step-indicator="4">
                    <div class="progress-circle">4</div>
                    <div class="progress-label">
                        <div class="progress-title">Maklumat Pelapor</div>
                        <div class="progress-subtitle">Langkah 4</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">

                {{-- STEP 1 --}}
                <div class="step-section" id="step-1">
                    <div class="card wizard-card mb-4">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0 fw-semibold">Maklumat Si Mati</h5>
                        </div>

                        <div class="card-body">

                            <div class="alert alert-light border mb-4">
                                <div class="fw-semibold mb-1">Panduan</div>
                                <div class="text-muted small">
                                    Pilih kategori laporan dahulu. Sistem akan memaparkan individu yang berkaitan dengan keluarga anda.
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">
                                        Kategori Laporan Kematian <span class="text-danger">*</span>
                                    </label>
                                    <select name="deceased_type" id="deceased_type" class="form-control">
                                        <option value="">-- Pilih Kategori Laporan --</option>

                                        @if(!$isMainMember)
                                            <option value="member" {{ old('deceased_type') == 'member' ? 'selected' : '' }}>
                                                Kematian Ahli Utama
                                            </option>
                                        @endif

                                        <option value="dependent" {{ old('deceased_type') == 'dependent' ? 'selected' : '' }}>
                                            Kematian Tanggungan
                                        </option>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">
                                        Pilih Si Mati <span class="text-danger">*</span>
                                    </label>
                                    <select name="deceased_id" id="deceased_id" class="form-control">
                                        <option value="">-- Pilih Nama --</option>
                                    </select>
                                </div>
                            </div>

                            <div class="border rounded-3 p-3 bg-light-subtle mt-2">
                                <div class="fw-semibold mb-3">Paparan Maklumat Si Mati</div>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label text-muted mb-1">Nama Penuh</label>
                                        <div class="fw-semibold" id="preview_name">-</div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label text-muted mb-1">No. Kad Pengenalan</label>
                                        <div class="fw-semibold" id="preview_nokp">-</div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label text-muted mb-1">Jantina</label>
                                        <div class="fw-semibold" id="preview_jantina">-</div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label text-muted mb-1">Umur</label>
                                        <div class="fw-semibold" id="preview_umur">-</div>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label fw-semibold">
                                        Tempat Kematian <span class="text-danger">*</span>
                                    </label>
                                    <textarea name="alamat_terakhir"
                                              class="form-control"
                                              rows="3"
                                              placeholder="Sila nyatakan tempat kematian si mati">{{ old('alamat_terakhir') }}</textarea>
                                    <small class="text-muted">
                                        Nyatakan tempat kematian seperti rumah, hospital, atau lokasi lain.
                                    </small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">
                                        Tarikh Meninggal <span class="text-danger">*</span>
                                    </label>
                                    <input type="date"
                                           name="tarikh_meninggal"
                                           class="form-control"
                                           value="{{ old('tarikh_meninggal') }}"
                                           max="{{ now()->toDateString() }}">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">
                                        Sebab Kematian <span class="text-danger">*</span>
                                    </label>
                                    <select name="sebab_kematian" id="sebab_kematian" class="form-control">
                                        <option value="">-- Pilih Sebab Kematian --</option>

                                        <option value="Masalah Kesihatan / Sakit" {{ old('sebab_kematian') == 'Masalah Kesihatan / Sakit' ? 'selected' : '' }}>
                                            Masalah Kesihatan / Sakit
                                        </option>

                                        <option value="Penyakit Berjangkit" {{ old('sebab_kematian') == 'Penyakit Berjangkit' ? 'selected' : '' }}>
                                            Penyakit Berjangkit
                                        </option>

                                        <option value="Kemalangan" {{ old('sebab_kematian') == 'Kemalangan' ? 'selected' : '' }}>
                                            Kemalangan
                                        </option>

                                        <option value="Kes Polis / Bedah Siasat" {{ old('sebab_kematian') == 'Kes Polis / Bedah Siasat' ? 'selected' : '' }}>
                                            Kes Polis / Bedah Siasat
                                        </option>

                                        <option value="Lain-lain" {{ old('sebab_kematian') == 'Lain-lain' ? 'selected' : '' }}>
                                            Lain-lain
                                        </option>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3 d-none" id="sebabLainWrapper">
                                    <label class="form-label fw-semibold">
                                        Nyatakan Sebab Kematian <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           name="sebab_kematian_lain"
                                           id="sebab_kematian_lain"
                                           class="form-control"
                                           value="{{ old('sebab_kematian_lain') }}"
                                           placeholder="Sila nyatakan sebab kematian">
                                    <small class="text-muted">
                                        Ruangan ini perlu diisi jika sebab kematian ialah lain-lain.
                                    </small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">No. Permit Kebumi</label>
                                    <input type="text"
                                           name="no_permit_kebumi"
                                           class="form-control"
                                           value="{{ old('no_permit_kebumi') }}"
                                           placeholder="Contoh: PKB-2026-001">
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <button type="button" class="btn btn-info next-step" data-next="2">
                                    Seterusnya
                                </button>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- STEP 2 --}}
                <div class="step-section d-none" id="step-2">
                    <div class="card wizard-card mb-4">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0 fw-semibold">Pengurusan Jenazah</h5>
                        </div>

                        <div class="card-body">

                            <div id="kesKhasAlert" class="alert alert-warning border-0 d-none">
                                <div class="fw-semibold mb-1">Makluman Kes Khas</div>
                                <div class="small">
                                    Bagi kematian melibatkan penyakit berjangkit atau kes polis / bedah siasat,
                                    pengurusan jenazah dan tempat mandi jenazah akan ditetapkan kepada
                                    <strong>Hospital / Pihak Berkuasa</strong>.
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">
                                        Tempat Mandi Jenazah <span class="text-danger">*</span>
                                    </label>

                                    <div id="lokasiMandiWrapper">
                                        <select name="lokasi_mandi_jenazah"
                                                id="lokasi_mandi_jenazah"
                                                class="form-control">
                                            <option value="">-- Pilih Tempat Mandi Jenazah --</option>

                                            <option value="Rumah Keluarga / Waris" {{ old('lokasi_mandi_jenazah') == 'Rumah Keluarga / Waris' ? 'selected' : '' }}>
                                                Rumah Keluarga / Waris
                                            </option>

                                            <option value="Masjid RTB Ar-Rahman" {{ old('lokasi_mandi_jenazah') == 'Masjid RTB Ar-Rahman' ? 'selected' : '' }}>
                                                Masjid RTB Ar-Rahman
                                            </option>

                                            <option value="Hospital" {{ old('lokasi_mandi_jenazah') == 'Hospital' ? 'selected' : '' }}>
                                                Hospital
                                            </option>

                                            <option value="Lain-lain" {{ old('lokasi_mandi_jenazah') == 'Lain-lain' ? 'selected' : '' }}>
                                                Lain-lain
                                            </option>
                                        </select>
                                    </div>

                                    <div id="lokasiMandiReadonly" class="readonly-box d-none">
                                        Hospital
                                    </div>

                                    <input type="hidden" id="lokasi_mandi_jenazah_hidden" value="">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">
                                        Pihak yang Menguruskan Jenazah <span class="text-danger">*</span>
                                    </label>

                                    <div id="pengurusanJenazahWrapper">
                                        <select name="pengurusan_jenazah_oleh"
                                                id="pengurusan_jenazah_oleh"
                                                class="form-control">
                                            <option value="">-- Pilih Pihak Pengurusan --</option>

                                            <option value="Keluarga / Waris" {{ old('pengurusan_jenazah_oleh') == 'Keluarga / Waris' ? 'selected' : '' }}>
                                                Keluarga / Waris
                                            </option>

                                            <option value="Pihak Khairat" {{ old('pengurusan_jenazah_oleh') == 'Pihak Khairat' ? 'selected' : '' }}>
                                                Pihak Khairat
                                            </option>

                                            <option value="Hospital / Pihak Berkuasa" {{ old('pengurusan_jenazah_oleh') == 'Hospital / Pihak Berkuasa' ? 'selected' : '' }}>
                                                Hospital / Pihak Berkuasa
                                            </option>
                                        </select>
                                    </div>

                                    <div id="pengurusanJenazahReadonly" class="readonly-box d-none">
                                        Hospital / Pihak Berkuasa
                                    </div>

                                    <input type="hidden" id="pengurusan_jenazah_oleh_hidden" value="">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">
                                        Lokasi Pengkebumian <span class="text-danger">*</span>
                                    </label>
                                    <select name="lokasi_pengkebumian"
                                            id="lokasi_pengkebumian"
                                            class="form-control">
                                        <option value="">-- Pilih Lokasi --</option>

                                        <option value="rtb" {{ old('lokasi_pengkebumian') == 'rtb' ? 'selected' : '' }}>
                                            Tanah Perkuburan RTB
                                        </option>

                                        <option value="luar_rtb" {{ old('lokasi_pengkebumian') == 'luar_rtb' ? 'selected' : '' }}>
                                            Luar Kawasan / Bukan RTB
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div id="luarRTBSection" class="{{ old('lokasi_pengkebumian') == 'luar_rtb' ? '' : 'd-none' }}">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">
                                            Nama Tanah Perkuburan <span class="text-danger">*</span>
                                        </label>
                                        <input type="text"
                                               name="nama_tanah_perkuburan"
                                               class="form-control"
                                               value="{{ old('nama_tanah_perkuburan') }}"
                                               placeholder="Contoh: Tanah Perkuburan Islam Kampung ...">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">
                                            Negeri <span class="text-danger">*</span>
                                        </label>
                                        <select name="negeri_tanah_perkuburan" class="form-control">
                                            <option value="">-- Pilih Negeri --</option>
                                            @foreach($senaraiNegeri as $negeri)
                                                <option value="{{ $negeri }}" {{ old('negeri_tanah_perkuburan') == $negeri ? 'selected' : '' }}>
                                                    {{ $negeri }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <label class="form-label fw-semibold">
                                            Alamat Penuh Tempat Pengkebumian <span class="text-danger">*</span>
                                        </label>
                                        <textarea name="alamat_tanah_perkuburan"
                                                  class="form-control"
                                                  rows="3"
                                                  placeholder="Sila nyatakan alamat lengkap tempat pengkebumian">{{ old('alamat_tanah_perkuburan') }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-0">
                                <label class="form-label fw-semibold">Catatan Tambahan</label>
                                <textarea name="catatan_pengurusan"
                                          class="form-control"
                                          rows="3"
                                          placeholder="Jika ada maklumat tambahan, nyatakan di sini">{{ old('catatan_pengurusan') }}</textarea>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-outline-secondary prev-step" data-prev="1">
                                    Sebelumnya
                                </button>

                                <button type="button" class="btn btn-info next-step" data-next="3">
                                    Seterusnya
                                </button>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- STEP 3 --}}
                <div class="step-section d-none" id="step-3">
                    <div class="card wizard-card mb-4">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0 fw-semibold">Dokumen Sokongan</h5>
                        </div>

                        <div class="card-body">
                            <div class="text-muted small mb-3">
                                Muat naik dokumen jika ada untuk memudahkan semakan pentadbir.
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Sijil Mati</label>
                                <input type="file" name="sijil_mati" class="form-control">
                                <small class="text-muted">
                                    Format: JPG, JPEG, PNG, PDF. Maksimum 2MB.
                                </small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Permit Kebumi</label>
                                <input type="file" name="permit_kebumi" class="form-control">
                                <small class="text-muted">
                                    Format: JPG, JPEG, PNG, PDF. Maksimum 2MB.
                                </small>
                            </div>

                            <div class="mb-0">
                                <label class="form-label fw-semibold">Dokumen Sokongan Tambahan</label>
                                <input type="file" name="dokumen_sokongan" class="form-control">
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-outline-secondary prev-step" data-prev="2">
                                    Sebelumnya
                                </button>

                                <button type="button" class="btn btn-info next-step" data-next="4">
                                    Seterusnya
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- STEP 4 --}}
                <div class="step-section d-none" id="step-4">
                    <div class="card wizard-card mb-4">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0 fw-semibold">Maklumat Pelapor</h5>
                        </div>

                        <div class="card-body">
                            <div class="info-note small mb-3">
                                Maklumat ini merujuk kepada individu yang membuat laporan kematian.
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    Nama Pelapor <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       name="nama_pelapor"
                                       class="form-control"
                                       value="{{ old('nama_pelapor', $pelaporNama) }}">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    No. Kad Pengenalan <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       name="no_kp_pelapor"
                                       class="form-control"
                                       value="{{ old('no_kp_pelapor', $pelaporNoKp) }}">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    No. Telefon <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       name="no_tel_pelapor"
                                       class="form-control"
                                       value="{{ old('no_tel_pelapor', $pelaporNoTel) }}">
                            </div>

                            <div class="mb-0">
                                <label class="form-label fw-semibold">
                                    Pertalian dengan Si Mati <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       name="pertalian_pelapor"
                                       class="form-control"
                                       value="{{ old('pertalian_pelapor', $pelaporPertalian) }}"
                                       readonly>
                                <small class="text-muted">
                                    Maklumat ini diambil daripada rekod sistem.
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="card custom-card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="fw-semibold mb-2">Peringatan</div>
                            <ul class="text-muted small ps-3 mb-4">
                                <li>Pilih nama si mati yang betul sebelum menghantar laporan.</li>
                                <li>Tempat kematian perlu diisi dengan tepat oleh pelapor.</li>
                                <li>Laporan akan disemak oleh pentadbir terlebih dahulu.</li>
                            </ul>

                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-outline-secondary prev-step" data-prev="3">
                                    Sebelumnya
                                </button>

                                <button type="submit" class="btn btn-info">
                                    <i class="bx bx-send me-1"></i> Hantar Laporan Kematian
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </form>
</div>

<script>
    const memberOptions = @json($memberOptions);
    const dependentOptions = @json($dependentOptions);
    const isMainMember = @json($isMainMember);

    const oldType = @json(old('deceased_type'));
    const oldId = @json(old('deceased_id'));

    const deceasedType = document.getElementById('deceased_type');
    const deceasedId = document.getElementById('deceased_id');

    const previewName = document.getElementById('preview_name');
    const previewNokp = document.getElementById('preview_nokp');
    const previewJantina = document.getElementById('preview_jantina');
    const previewUmur = document.getElementById('preview_umur');

    const sebabKematian = document.getElementById('sebab_kematian');
    const sebabLainWrapper = document.getElementById('sebabLainWrapper');
    const sebabKematianLain = document.getElementById('sebab_kematian_lain');

    const kesKhasAlert = document.getElementById('kesKhasAlert');
    const lokasiMandiJenazah = document.getElementById('lokasi_mandi_jenazah');
    const pengurusanJenazah = document.getElementById('pengurusan_jenazah_oleh');

    const lokasiMandiWrapper = document.getElementById('lokasiMandiWrapper');
    const lokasiMandiReadonly = document.getElementById('lokasiMandiReadonly');
    const lokasiMandiHidden = document.getElementById('lokasi_mandi_jenazah_hidden');

    const pengurusanJenazahWrapper = document.getElementById('pengurusanJenazahWrapper');
    const pengurusanJenazahReadonly = document.getElementById('pengurusanJenazahReadonly');
    const pengurusanJenazahHidden = document.getElementById('pengurusan_jenazah_oleh_hidden');

    const lokasiPengkebumian = document.getElementById('lokasi_pengkebumian');
    const luarRTBSection = document.getElementById('luarRTBSection');

    function getGenderFromIc(ic) {
        if (!ic) return '-';

        const digits = String(ic).replace(/\D/g, '');

        if (!digits.length) return '-';

        const lastDigit = parseInt(digits.slice(-1), 10);

        return lastDigit % 2 === 0 ? 'Perempuan' : 'Lelaki';
    }

    function getAgeFromIc(ic) {
        if (!ic) return '-';

        const digits = String(ic).replace(/\D/g, '');

        if (digits.length < 6) return '-';

        const yy = parseInt(digits.substring(0, 2), 10);
        const mm = parseInt(digits.substring(2, 4), 10);
        const dd = parseInt(digits.substring(4, 6), 10);

        if (mm < 1 || mm > 12 || dd < 1 || dd > 31) return '-';

        const currentYear = new Date().getFullYear() % 100;
        const fullYear = yy <= currentYear ? 2000 + yy : 1900 + yy;

        const birthDate = new Date(fullYear, mm - 1, dd);

        if (isNaN(birthDate.getTime())) return '-';

        const today = new Date();

        let age = today.getFullYear() - birthDate.getFullYear();
        const monthDiff = today.getMonth() - birthDate.getMonth();

        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }

        return age >= 0 ? age : '-';
    }

    function getCurrentDataSet() {
        if (deceasedType.value === 'member') return memberOptions;
        if (deceasedType.value === 'dependent') return dependentOptions;

        return [];
    }

    function populateDeceasedOptions(selectedId = null) {
        const list = getCurrentDataSet();

        deceasedId.innerHTML = '<option value="">-- Pilih Nama --</option>';

        list.forEach(item => {
            const option = document.createElement('option');

            option.value = item.id;
            option.textContent = `${item.name} (${item.label})`;

            if (selectedId && String(selectedId) === String(item.id)) {
                option.selected = true;
            }

            deceasedId.appendChild(option);
        });

        updatePreview();
    }

    function updatePreview() {
        const list = getCurrentDataSet();
        const selected = list.find(item => String(item.id) === String(deceasedId.value));

        if (!selected) {
            previewName.textContent = '-';
            previewNokp.textContent = '-';
            previewJantina.textContent = '-';
            previewUmur.textContent = '-';
            return;
        }

        previewName.textContent = selected.name || '-';
        previewNokp.textContent = selected.no_kp || '-';
        previewJantina.textContent = getGenderFromIc(selected.no_kp);
        previewUmur.textContent = getAgeFromIc(selected.no_kp);
    }

    function toggleSebabLain() {
        if (!sebabKematian || !sebabLainWrapper || !sebabKematianLain) return;

        if (sebabKematian.value === 'Lain-lain') {
            sebabLainWrapper.classList.remove('d-none');
            sebabKematianLain.setAttribute('required', 'required');
        } else {
            sebabLainWrapper.classList.add('d-none');
            sebabKematianLain.removeAttribute('required');
            sebabKematianLain.value = '';
        }
    }

    function toggleKesKhas() {
        if (!sebabKematian || !kesKhasAlert) return;

        const kesKhas = [
            'Penyakit Berjangkit',
            'Kes Polis / Bedah Siasat'
        ];

        const isKesKhas = kesKhas.includes(sebabKematian.value);

        if (isKesKhas) {
            kesKhasAlert.classList.remove('d-none');

            if (lokasiMandiJenazah) {
                lokasiMandiJenazah.value = 'Hospital';
                lokasiMandiJenazah.removeAttribute('name');
            }

            if (pengurusanJenazah) {
                pengurusanJenazah.value = 'Hospital / Pihak Berkuasa';
                pengurusanJenazah.removeAttribute('name');
            }

            if (lokasiMandiWrapper) lokasiMandiWrapper.classList.add('d-none');
            if (lokasiMandiReadonly) lokasiMandiReadonly.classList.remove('d-none');

            if (pengurusanJenazahWrapper) pengurusanJenazahWrapper.classList.add('d-none');
            if (pengurusanJenazahReadonly) pengurusanJenazahReadonly.classList.remove('d-none');

            if (lokasiMandiHidden) {
                lokasiMandiHidden.setAttribute('name', 'lokasi_mandi_jenazah');
                lokasiMandiHidden.value = 'Hospital';
            }

            if (pengurusanJenazahHidden) {
                pengurusanJenazahHidden.setAttribute('name', 'pengurusan_jenazah_oleh');
                pengurusanJenazahHidden.value = 'Hospital / Pihak Berkuasa';
            }
        } else {
            kesKhasAlert.classList.add('d-none');

            if (lokasiMandiJenazah) {
                lokasiMandiJenazah.setAttribute('name', 'lokasi_mandi_jenazah');
            }

            if (pengurusanJenazah) {
                pengurusanJenazah.setAttribute('name', 'pengurusan_jenazah_oleh');
            }

            if (lokasiMandiWrapper) lokasiMandiWrapper.classList.remove('d-none');
            if (lokasiMandiReadonly) lokasiMandiReadonly.classList.add('d-none');

            if (pengurusanJenazahWrapper) pengurusanJenazahWrapper.classList.remove('d-none');
            if (pengurusanJenazahReadonly) pengurusanJenazahReadonly.classList.add('d-none');

            if (lokasiMandiHidden) {
                lokasiMandiHidden.removeAttribute('name');
                lokasiMandiHidden.value = '';
            }

            if (pengurusanJenazahHidden) {
                pengurusanJenazahHidden.removeAttribute('name');
                pengurusanJenazahHidden.value = '';
            }
        }
    }

    function toggleLuarRTB() {
        if (!lokasiPengkebumian || !luarRTBSection) return;

        if (lokasiPengkebumian.value === 'luar_rtb') {
            luarRTBSection.classList.remove('d-none');
        } else {
            luarRTBSection.classList.add('d-none');

            const namaTanahPerkuburan = document.querySelector('input[name="nama_tanah_perkuburan"]');
            const negeriTanahPerkuburan = document.querySelector('select[name="negeri_tanah_perkuburan"]');
            const alamatTanahPerkuburan = document.querySelector('textarea[name="alamat_tanah_perkuburan"]');

            if (namaTanahPerkuburan) namaTanahPerkuburan.value = '';
            if (negeriTanahPerkuburan) negeriTanahPerkuburan.value = '';
            if (alamatTanahPerkuburan) alamatTanahPerkuburan.value = '';
        }
    }

    function showStep(step) {
        const currentStep = parseInt(step, 10);

        document.querySelectorAll('.step-section').forEach(section => {
            section.classList.add('d-none');
        });

        const activeStep = document.getElementById('step-' + step);

        if (activeStep) {
            activeStep.classList.remove('d-none');
        }

        document.querySelectorAll('.progress-step').forEach(item => {
            item.classList.remove('active', 'completed');

            const itemStep = parseInt(item.dataset.stepIndicator, 10);

            if (itemStep < currentStep) {
                item.classList.add('completed');
            } else if (itemStep === currentStep) {
                item.classList.add('active');
            }
        });

        const fill = document.getElementById('progressTrackFill');

        if (fill) {
            let width = '0%';

            if (currentStep === 1) width = '0%';
            if (currentStep === 2) width = 'calc((75% - 62px) / 3)';
            if (currentStep === 3) width = 'calc((75% - 62px) / 3 * 2)';
            if (currentStep === 4) width = 'calc(75% - 62px)';

            fill.style.width = width;
        }

        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }

    deceasedType.addEventListener('change', function () {
        populateDeceasedOptions();
    });

    deceasedId.addEventListener('change', updatePreview);

    document.querySelectorAll('.next-step').forEach(button => {
        button.addEventListener('click', function () {
            showStep(this.dataset.next);
        });
    });

    document.querySelectorAll('.prev-step').forEach(button => {
        button.addEventListener('click', function () {
            showStep(this.dataset.prev);
        });
    });

    if (sebabKematian) {
        sebabKematian.addEventListener('change', function () {
            toggleKesKhas();
            toggleSebabLain();
        });
    }

    if (lokasiPengkebumian) {
        lokasiPengkebumian.addEventListener('change', toggleLuarRTB);
    }

    window.addEventListener('load', function () {
        if (oldType) {
            deceasedType.value = oldType;
            populateDeceasedOptions(oldId);
        } else {
            if (isMainMember) {
                deceasedType.value = 'dependent';
                populateDeceasedOptions();
            }
        }

        toggleKesKhas();
        toggleSebabLain();
        toggleLuarRTB();
        showStep(1);
    });
</script>

@endsection