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

        .form-section-card {
            border-radius: 18px;
            overflow: hidden;
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
        }
    </style>

    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <h1 class="page-title fw-semibold fs-20 mb-1">Permohonan Keahlian</h1>
            <p class="text-muted mb-0">Sila pilih kaedah bayaran yuran dan semak maklumat bayaran sebelum menghantar permohonan.</p>
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

                <div class="stepper-item completed">
                    <div class="stepper-circle">
                        <i class="bx bx-check"></i>
                    </div>
                    <div class="stepper-title">Maklumat Perhubungan</div>
                    <div class="stepper-subtitle">Langkah 2</div>
                </div>

                <div class="stepper-item completed">
                    <div class="stepper-circle">
                        <i class="bx bx-check"></i>
                    </div>
                    <div class="stepper-title">Maklumat Waris</div>
                    <div class="stepper-subtitle">Langkah 3</div>
                </div>

                <div class="stepper-item active">
                    <div class="stepper-circle">4</div>
                    <div class="stepper-title">Bayaran Yuran</div>
                    <div class="stepper-subtitle">Langkah 4</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-xxl-10 col-xl-11">
            <div class="card custom-card border-0 shadow-sm">
                <div class="card-body p-0">
                    <form action="{{ route('user.profile.store.final') }}" method="POST">
                        @csrf

                        <div class="p-4 p-md-5">

                            <div class="text-center mb-4">
                                <h4 class="fw-semibold mb-2">Maklumat Bayaran & Pengesahan</h4>
                                <p class="text-muted mb-0">
                                    Sila pilih kaedah bayaran yang dikehendaki berdasarkan jadual bayaran di bawah.
                                </p>
                            </div>

                            <div class="row justify-content-center mb-4">
                                <div class="col-lg-6 col-md-8">
                                    <label class="form-label fw-semibold">Pilih Kaedah Bayaran</label>
                                    <select name="payment_plan" class="form-select form-select-lg @error('payment_plan') is-invalid @enderror">
                                        <option value="">-- Sila Pilih --</option>
                                        <option value="bulanan" {{ old('payment_plan', session('user_profile.step4.payment_plan')) == 'bulanan' ? 'selected' : '' }}>
                                            Bulanan
                                        </option>
                                        <option value="tahunan" {{ old('payment_plan', session('user_profile.step4.payment_plan')) == 'tahunan' ? 'selected' : '' }}>
                                            Tahunan
                                        </option>
                                    </select>
                                    @error('payment_plan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="card border-0 bg-light-subtle shadow-sm mb-4">
                                <div class="card-body py-3 px-4">
                                    <div class="d-flex align-items-start gap-2">
                                        <i class="bx bx-info-circle fs-5 text-primary mt-1"></i>
                                        <div>
                                            <div class="fw-semibold mb-1">Makluman Bayaran</div>
                                            <div class="text-muted small mb-0">
                                                Yuran pendaftaran dikenakan sekali sahaja bagi permohonan baharu. Sila rujuk jadual bayaran di bawah untuk melihat jumlah bayaran permulaan bagi setiap kaedah bayaran.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-header bg-white border-0 pb-0">
                                    <h5 class="fw-semibold mb-3">Jadual Bayaran Yuran</h5>
                                </div>
                                <div class="card-body pt-0">
                                    <div class="table-responsive">
                                        <table class="table table-bordered align-middle text-center mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th style="min-width: 150px;">Kaedah Bayaran</th>
                                                    <th style="min-width: 140px;">Yuran Pendaftaran</th>
                                                    <th style="min-width: 140px;">Yuran Pelan</th>
                                                    <th style="min-width: 170px;">Jumlah Bayaran Permulaan</th>
                                                    <th style="min-width: 280px;">Keterangan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="fw-semibold">Bulanan</td>
                                                    <td>RM20</td>
                                                    <td>RM10 sebulan</td>
                                                    <td class="fw-bold text-success">RM30</td>
                                                    <td class="text-start">
                                                        Bayaran permulaan terdiri daripada yuran pendaftaran sebanyak RM20 dan bayaran bulan pertama sebanyak RM10.
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-semibold">Tahunan</td>
                                                    <td>RM20</td>
                                                    <td>RM100 setahun</td>
                                                    <td class="fw-bold text-primary">RM120</td>
                                                    <td class="text-start">
                                                        Bayaran permulaan terdiri daripada yuran pendaftaran sebanyak RM20 dan bayaran tahunan sebanyak RM100 untuk tahun semasa.
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <div class="card border-0 bg-warning-subtle h-100 shadow-sm">
                                        <div class="card-body">
                                            <h6 class="fw-semibold mb-2">Perhatian</h6>
                                            <p class="small text-muted mb-0">
                                                Sila pastikan kaedah bayaran yang dipilih adalah tepat kerana jumlah bayaran permulaan adalah berbeza mengikut pilihan bayaran.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card border-0 bg-info-subtle h-100 shadow-sm">
                                        <div class="card-body">
                                            <h6 class="fw-semibold mb-2">Semakan Pentadbiran</h6>
                                            <p class="small text-muted mb-0">
                                                Selepas permohonan dihantar, permohonan anda akan disemak terlebih dahulu oleh pihak pentadbiran.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-check mt-2">
                                <input class="form-check-input @error('akuan') is-invalid @enderror"
                                       type="checkbox"
                                       name="akuan"
                                       value="1"
                                       id="akuan"
                                       {{ old('akuan', session('user_profile.step4.akuan')) ? 'checked' : '' }}>

                                <label class="form-check-label" for="akuan">
                                    Saya mengaku bahawa semua maklumat yang diberikan adalah benar dan saya telah memahami maklumat bayaran yang dinyatakan.
                                </label>

                                @error('akuan')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>

                        <div class="px-4 px-md-5 py-3 border-top d-flex justify-content-between align-items-center">
                            <a href="{{ route('user.profile.create.step3') }}" class="btn btn-light">
                                Kembali
                            </a>
                            <button class="btn btn-success px-4" type="submit">
                                Hantar Permohonan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection