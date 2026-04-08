@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <style>
        .stepper-wrapper {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 0;
            position: relative;
            flex-wrap: nowrap;
            overflow-x: auto;
            padding: 8px 0 4px;
        }

        .stepper-item {
            position: relative;
            flex: 1;
            min-width: 160px;
            text-align: center;
        }

        .stepper-item:not(:last-child)::after {
            content: "";
            position: absolute;
            top: 24px;
            left: 50%;
            width: 100%;
            height: 4px;
            background: #dfe3e8;
            z-index: 1;
        }

        .stepper-item.completed:not(:last-child)::after,
        .stepper-item.active:not(:last-child)::after {
            background: #22c55e;
        }

        .stepper-circle {
            position: relative;
            z-index: 2;
            width: 48px;
            height: 48px;
            margin: 0 auto;
            border-radius: 50%;
            background: #f1f3f5;
            border: 3px solid #dfe3e8;
            color: #6c757d;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .stepper-title {
            margin-top: 12px;
            font-size: 14px;
            font-weight: 600;
            color: #6c757d;
            line-height: 1.35;
        }

        .stepper-subtitle {
            font-size: 12px;
            color: #9aa1a9;
            margin-top: 2px;
        }

        .stepper-item.active .stepper-circle {
            background: #22c55e;
            border-color: #22c55e;
            color: #fff;
            box-shadow: 0 0 0 6px rgba(34, 197, 94, 0.12);
        }

        .stepper-item.active .stepper-title {
            color: #198754;
        }

        .stepper-item.completed .stepper-circle {
            background: #198754;
            border-color: #198754;
            color: #fff;
        }

        .info-soft-card {
            background: #f8fafc;
            border: 1px solid #edf1f5;
            border-radius: 16px;
        }

        .form-section-card {
            border-radius: 18px;
            overflow: hidden;
        }

        .section-box {
            border: 1px solid #edf1f5;
            border-radius: 16px;
            padding: 24px;
            background: #ffffff;
            height: 100%;
        }

        .section-heading {
            font-size: 16px;
            font-weight: 700;
            color: #198754;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-heading .badge-circle {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: rgba(25, 135, 84, 0.12);
            color: #198754;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
        }

        @media (max-width: 768px) {
            .stepper-item {
                min-width: 120px;
            }

            .stepper-circle {
                width: 42px;
                height: 42px;
                font-size: 14px;
            }

            .stepper-item:not(:last-child)::after {
                top: 21px;
            }

            .stepper-title {
                font-size: 12px;
            }

            .stepper-subtitle {
                font-size: 11px;
            }

            .section-box {
                padding: 18px;
            }
        }
    </style>

    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <h1 class="page-title fw-semibold fs-20 mb-1">Permohonan Keahlian</h1>
            <p class="text-muted mb-0">Lengkapkan maklumat perhubungan, kariah dan pekerjaan.</p>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger border-0 shadow-sm">
            <div class="fw-semibold mb-2">Sila semak maklumat berikut:</div>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card custom-card border-0 shadow-sm mb-4 form-section-card">
        <div class="card-body py-4 px-4 px-md-5">
            <div class="stepper-wrapper">
                <div class="stepper-item completed">
                    <div class="stepper-circle">
                        <i class="bx bx-check"></i>
                    </div>
                    <div class="stepper-title">Maklumat Asas</div>
                    <div class="stepper-subtitle">Langkah 1</div>
                </div>

                <div class="stepper-item active">
                    <div class="stepper-circle">2</div>
                    <div class="stepper-title">Maklumat Perhubungan</div>
                    <div class="stepper-subtitle">Langkah 2</div>
                </div>

                <div class="stepper-item">
                    <div class="stepper-circle">3</div>
                    <div class="stepper-title">Maklumat Waris</div>
                    <div class="stepper-subtitle">Langkah 3</div>
                </div>

                <div class="stepper-item">
                    <div class="stepper-circle">4</div>
                    <div class="stepper-title">Bayaran Yuran</div>
                    <div class="stepper-subtitle">Langkah 4</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-xxl-10 col-xl-11">
            <div class="card custom-card border-0 shadow-sm form-section-card">
                <div class="card-body p-0">
                    <form action="{{ route('user.profile.post.step2') }}" method="POST">
                        @csrf

                        <div class="p-4 p-md-5">

                            <div class="text-center mb-4">
                                <h4 class="fw-semibold mb-2">Maklumat Perhubungan, Kariah & Pekerjaan</h4>
                                <p class="text-muted mb-0">
                                    Sila pastikan alamat, nombor telefon, maklumat tempat tinggal dan pekerjaan diisi dengan tepat.
                                </p>
                            </div>

                            <div class="info-soft-card shadow-sm mb-4">
                                <div class="card-body py-3 px-4">
                                    <div class="d-flex align-items-start gap-2">
                                        <i class="bx bx-info-circle fs-5 text-primary mt-1"></i>
                                        <div>
                                            <div class="fw-semibold mb-1">Makluman</div>
                                            <div class="text-muted small mb-0">
                                                Bahagian ini digunakan untuk semakan kelayakan keahlian dan memudahkan urusan perhubungan dengan pihak pentadbiran.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-4">
                                <div class="col-12">
                                    <div class="section-box shadow-sm">
                                        <div class="section-heading">
                                            <span class="badge-circle">A</span>
                                            <span>Maklumat Perhubungan & Kariah</span>
                                        </div>

                                        <div class="row g-4">
                                            <div class="col-12">
                                                <label class="form-label fw-semibold">Alamat Rumah</label>
                                                <textarea name="alamat_rumah"
                                                          rows="4"
                                                          class="form-control form-control-lg @error('alamat_rumah') is-invalid @enderror"
                                                          placeholder="Sila isikan alamat rumah dengan lengkap">{{ old('alamat_rumah', session('user_profile.step2.alamat_rumah')) }}</textarea>
                                                @error('alamat_rumah')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">No. Tel Rumah</label>
                                                <input type="text"
                                                       name="no_tel_rumah"
                                                       class="form-control form-control-lg @error('no_tel_rumah') is-invalid @enderror"
                                                       value="{{ old('no_tel_rumah', session('user_profile.step2.no_tel_rumah')) }}"
                                                       placeholder="Contoh: 03-12345678">
                                                @error('no_tel_rumah')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">No. Telefon Bimbit</label>
                                                <input type="text"
                                                       name="no_tel_bimbit"
                                                       class="form-control form-control-lg @error('no_tel_bimbit') is-invalid @enderror"
                                                       value="{{ old('no_tel_bimbit', session('user_profile.step2.no_tel_bimbit')) }}"
                                                       placeholder="Contoh: 0123456789">
                                                @error('no_tel_bimbit')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Tinggal Dalam Kariah?</label>
                                                <select name="tinggal_dalam_kariah" class="form-select form-select-lg @error('tinggal_dalam_kariah') is-invalid @enderror">
                                                    <option value="">-- Sila Pilih --</option>
                                                    <option value="1" {{ old('tinggal_dalam_kariah', session('user_profile.step2.tinggal_dalam_kariah')) == '1' ? 'selected' : '' }}>
                                                        Ya
                                                    </option>
                                                </select>
                                                @error('tinggal_dalam_kariah')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="text-muted">Permohonan hanya dibuka untuk pemastautin dalam kariah.</small>
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Tempoh Menetap</label>
                                                <input type="text"
                                                       name="tempoh_menetap"
                                                       class="form-control form-control-lg @error('tempoh_menetap') is-invalid @enderror"
                                                       value="{{ old('tempoh_menetap', session('user_profile.step2.tempoh_menetap')) }}"
                                                       placeholder="Contoh: 5 tahun">
                                                @error('tempoh_menetap')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="section-box shadow-sm">
                                        <div class="section-heading">
                                            <span class="badge-circle">B</span>
                                            <span>Maklumat Pekerjaan</span>
                                        </div>

                                        <div class="row g-4">
                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold">Pekerjaan</label>
                                                <input type="text"
                                                       name="pekerjaan"
                                                       class="form-control form-control-lg @error('pekerjaan') is-invalid @enderror"
                                                       value="{{ old('pekerjaan', session('user_profile.step2.pekerjaan')) }}"
                                                       placeholder="Contoh: Swasta / Kerajaan / Pelajar">
                                                @error('pekerjaan')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold">Nama Majikan</label>
                                                <input type="text"
                                                       name="nama_majikan"
                                                       class="form-control form-control-lg @error('nama_majikan') is-invalid @enderror"
                                                       value="{{ old('nama_majikan', session('user_profile.step2.nama_majikan')) }}"
                                                       placeholder="Sila isikan nama majikan">
                                                @error('nama_majikan')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold">Alamat Kerja</label>
                                                <input type="text"
                                                       name="alamat_kerja"
                                                       class="form-control form-control-lg @error('alamat_kerja') is-invalid @enderror"
                                                       value="{{ old('alamat_kerja', session('user_profile.step2.alamat_kerja')) }}"
                                                       placeholder="Sila isikan alamat tempat kerja">
                                                @error('alamat_kerja')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="px-4 px-md-5 py-3 border-top d-flex justify-content-between align-items-center">
                            <a href="{{ route('user.profile.create.step1') }}" class="btn btn-light">
                                Kembali
                            </a>
                            <button type="submit" class="btn btn-success px-4">
                                Seterusnya
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection