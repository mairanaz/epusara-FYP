@extends('layouts.app')

@section('content')
<div class="container-fluid">

    @php
        $isDependent = auth()->user()->account_type === 'tanggungan';
        $status = $profile->status_permohonan ?? null;

        $statusClass = match($status) {
            'pending' => 'warning',
            'approved' => 'info',
            'rejected' => 'danger',
            'active' => 'success',
            default => 'secondary',
        };

        $statusLabel = match($status) {
            'pending' => 'Menunggu Semakan',
            'approved' => 'Diluluskan',
            'rejected' => 'Ditolak',
            'active' => 'Aktif',
            default => 'Belum Dihantar',
        };

        $paymentPlanLabel = '-';
        if ($profile->payment_plan === 'bulanan') {
            $paymentPlanLabel = 'Bulanan';
        } elseif ($profile->payment_plan === 'tahunan') {
            $paymentPlanLabel = 'Tahunan';
        }
    @endphp

    <style>
        .formal-card {
            border: 1px solid #dee2e6;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            background: #fff;
        }

        .formal-header {
            background: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            font-weight: 700;
            color: #212529;
            padding: 14px 18px;
        }

        .formal-body {
            padding: 18px;
        }

        .info-label {
            font-size: 13px;
            color: #6c757d;
            margin-bottom: 4px;
        }

        .info-value {
            font-size: 15px;
            font-weight: 600;
            color: #212529;
            word-break: break-word;
        }

        .info-row {
            padding: 10px 0;
            border-bottom: 1px solid #f1f3f5;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .profile-summary {
            border: 1px solid #dee2e6;
            border-radius: 10px;
            background: #ffffff;
            padding: 20px;
        }

        .profile-summary h3 {
            margin-bottom: 4px;
            font-weight: 700;
            color: #212529;
        }

        .section-spacing {
            margin-bottom: 1.5rem;
        }

        .status-box {
            border-radius: 10px;
            border: 1px solid #dee2e6;
            padding: 16px 18px;
            background: #fff;
        }

        .table-summary td {
            padding: 10px 12px;
            vertical-align: top;
        }

        .table-summary td:first-child {
            width: 220px;
            font-weight: 600;
            color: #495057;
            background: #f8f9fa;
        }

        .status-hero {
            position: relative;
            overflow: hidden;
            border-radius: 18px;
            padding: 22px;
            border: 1px solid #f6c343;
            background: linear-gradient(135deg, #fff7df 0%, #ffffff 65%);
            box-shadow: 0 10px 28px rgba(245, 158, 11, 0.18);
        }

        .status-hero::before {
            content: "";
            position: absolute;
            top: -45px;
            right: -45px;
            width: 150px;
            height: 150px;
            background: rgba(245, 158, 11, 0.15);
            border-radius: 50%;
        }

        .status-icon {
            width: 54px;
            height: 54px;
            border-radius: 16px;
            background: #f59e0b;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            flex-shrink: 0;
            box-shadow: 0 8px 18px rgba(245, 158, 11, 0.35);
        }

        .status-title {
            font-size: 18px;
            font-weight: 800;
            color: #92400e;
            margin-bottom: 4px;
        }

        .status-desc {
            color: #6b7280;
            font-size: 14px;
            line-height: 1.6;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            background: #fffbeb;
            color: #92400e;
            border: 1px solid #fcd34d;
            border-radius: 999px;
            padding: 7px 13px;
            font-size: 13px;
            font-weight: 700;
        }

        .status-dot {
            width: 9px;
            height: 9px;
            background: #f59e0b;
            border-radius: 50%;
            box-shadow: 0 0 0 5px rgba(245, 158, 11, 0.18);
        }

        .review-steps {
            display: flex;
            gap: 12px;
            margin-top: 18px;
            flex-wrap: wrap;
        }

        .review-step {
            flex: 1;
            min-width: 160px;
            background: rgba(255,255,255,0.75);
            border: 1px solid #fde68a;
            border-radius: 14px;
            padding: 12px 14px;
        }

        .review-step .step-no {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            background: #f59e0b;
            color: #fff;
            font-size: 13px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 8px;
        }

        .review-step .step-title {
            font-weight: 700;
            color: #78350f;
            font-size: 14px;
        }

        .review-step .step-text {
            font-size: 13px;
            color: #6b7280;
            margin-top: 3px;
        }

        .application-status-card {
            border: 1px solid #e5e7eb;
            border-left: 5px solid #0d6efd;
            border-radius: 14px;
            background: #ffffff;
            padding: 20px;
            box-shadow: 0 4px 14px rgba(15, 23, 42, 0.06);
        }

        .status-main {
            display: flex;
            align-items: flex-start;
            gap: 14px;
        }

        .status-main-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: #eef4ff;
            color: #0d6efd;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            flex-shrink: 0;
        }

        .status-main-title {
            font-size: 17px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 4px;
        }

        .status-main-desc {
            font-size: 14px;
            color: #6b7280;
            line-height: 1.6;
        }

        .status-badge-soft {
            background: #eef4ff;
            color: #0d6efd;
            border: 1px solid #cfe2ff;
            padding: 7px 12px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 600;
            white-space: nowrap;
        }

        .status-progress-mini {
            margin-top: 18px;
            padding-top: 15px;
            border-top: 1px solid #f1f3f5;
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .progress-item {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #6b7280;
            font-size: 13px;
            font-weight: 500;
        }

        .progress-circle {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: #0d6efd;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
        }

        .progress-line {
            width: 38px;
            height: 1px;
            background: #d1d5db;
        }

        .application-status-card.is-approved {
            border-left-color: #10b981;
        }

        .application-status-card.is-approved .status-main-icon {
            background: #ecfdf5;
            color: #10b981;
        }

        .application-status-card.is-approved .status-badge-soft {
            background: #ecfdf5;
            color: #047857;
            border-color: #a7f3d0;
        }

        .application-status-card.is-approved .progress-circle {
            background: #10b981;
        }

        .status-action-box {
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 12px 14px;
            min-width: 210px;
        }

        .status-action-title {
            font-size: 13px;
            color: #6b7280;
            margin-bottom: 8px;
        }

    </style>

    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div id="tour-profile-header">
            <h1 class="page-title fw-semibold fs-20 mb-1">
                {{ $isDependent ? 'Maklumat Tanggungan' : 'Maklumat Ahli' }}
            </h1>
            <p class="text-muted mb-0">
                Paparan maklumat profil dan status permohonan keahlian.
            </p>
        </div>

        <div class="btn-list mt-3 mt-md-0">
            <a class="btn btn-info"
            id="tour-profile-edit"
            href="{{ route('user.profile.edit') }}">
                Edit Maklumat
            </a>
        </div>
    </div>



    <div class="profile-summary section-spacing" id="tour-profile-summary">
        <div class="row align-items-center g-3">
            <div class="col-md-8">
                <h3>{{ $profile->nama }}</h3>
                <div class="text-muted">{{ $profile->no_kp }}</div>
            </div>
            <div class="col-md-4 text-md-end">
                <div class="mb-2">
                    <small class="text-muted d-block">Status Permohonan</small>
                    <span class="badge bg-{{ $statusClass }} px-3 py-2">
                        {{ $statusLabel }}
                    </span>
                </div>
                <div>
                    <small class="text-muted d-block">Tarikh Permohonan</small>
                    <span class="fw-semibold">
                        {{ $profile->tarikh_permohonan ? \Carbon\Carbon::parse($profile->tarikh_permohonan)->format('d/m/Y') : '-' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    @if($status === 'pending')
    <div class="application-status-card section-spacing" id="tour-profile-status">
        <div class="d-flex flex-column flex-md-row justify-content-between gap-3">
            <div class="status-main">
                <div class="status-main-icon">
                    <i class="ri-time-line"></i>
                </div>

                <div>
                    <div class="status-main-title">
                        Permohonan Sedang Disemak
                    </div>

                    <div class="status-main-desc">
                        Permohonan keahlian anda telah dihantar dan sedang menunggu semakan pihak pentadbiran.
                        @unless($isDependent)
                            Bayaran yuran hanya boleh dibuat selepas permohonan diluluskan.
                        @endunless
                    </div>
                </div>
            </div>

            <div class="text-md-end">
                <div class="status-badge-soft">
                    Menunggu Semakan
                </div>

                <div class="small text-muted mt-2">
                    Tarikh permohonan:
                    <strong>
                        {{ $profile->tarikh_permohonan ? \Carbon\Carbon::parse($profile->tarikh_permohonan)->format('d/m/Y') : '-' }}
                    </strong>
                </div>
            </div>
        </div>

        <div class="status-progress-mini">
            <div class="progress-item">
                <span class="progress-circle">1</span>
                Dihantar
            </div>

            <div class="progress-line"></div>

            <div class="progress-item">
                <span class="progress-circle">2</span>
                Dalam Semakan
            </div>

            <div class="progress-line"></div>

            <div class="progress-item">
                <span class="progress-circle" style="background:#d1d5db;">3</span>
                Keputusan
            </div>
        </div>
    </div>
    @elseif($status === 'approved')
    @if($isDependent)
        <div class="application-status-card is-approved section-spacing" id="tour-profile-status">
            <div class="d-flex flex-column flex-md-row justify-content-between gap-3">
                <div class="status-main">
                    <div class="status-main-icon">
                        <i class="ri-checkbox-circle-line"></i>
                    </div>

                    <div>
                        <div class="status-main-title">
                            Permohonan Diluluskan
                        </div>

                        <div class="status-main-desc">
                            Maklumat anda telah disahkan oleh pihak pentadbiran. Akaun tanggungan anda kini telah direkodkan dalam sistem.
                        </div>
                    </div>
                </div>

                <div class="text-md-end">
                    <div class="status-badge-soft">
                        Diluluskan
                    </div>

                    <div class="small text-muted mt-2">
                        Tarikh permohonan:
                        <strong>
                            {{ $profile->tarikh_permohonan ? \Carbon\Carbon::parse($profile->tarikh_permohonan)->format('d/m/Y') : '-' }}
                        </strong>
                    </div>
                </div>
            </div>

            <div class="status-progress-mini">
                <div class="progress-item">
                    <span class="progress-circle">1</span>
                    Dihantar
                </div>

                <div class="progress-line"></div>

                <div class="progress-item">
                    <span class="progress-circle">2</span>
                    Disemak
                </div>

                <div class="progress-line"></div>

                <div class="progress-item">
                    <span class="progress-circle">3</span>
                    Diluluskan
                </div>
            </div>
        </div>
    @else
        <div class="application-status-card is-approved section-spacing" id="tour-profile-status">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div class="status-main">
                    <div class="status-main-icon">
                        <i class="ri-checkbox-circle-line"></i>
                    </div>

                    <div>
                        <div class="status-main-title">
                            Permohonan Diluluskan
                        </div>

                        <div class="status-main-desc">
                            Permohonan anda telah diluluskan. Sila teruskan bayaran yuran untuk mengaktifkan keahlian.
                        </div>
                    </div>
                </div>

                <div class="status-action-box text-md-end">
                    <div class="status-action-title">
                        Tindakan diperlukan
                    </div>

                    <a href="{{ route('user.payments.create') }}" class="btn btn-success">
                        Bayar Yuran
                    </a>
                </div>
            </div>

            <div class="status-progress-mini">
                <div class="progress-item">
                    <span class="progress-circle">1</span>
                    Dihantar
                </div>

                <div class="progress-line"></div>

                <div class="progress-item">
                    <span class="progress-circle">2</span>
                    Disemak
                </div>

                <div class="progress-line"></div>

                <div class="progress-item">
                    <span class="progress-circle">3</span>
                    Diluluskan
                </div>
            </div>
        </div>
    @endif
    @elseif($status === 'rejected')
        <div class="status-box section-spacing" id="tour-profile-status">
            <div class="fw-semibold mb-1 text-danger">Permohonan Ditolak</div>
            <div class="text-muted">Permohonan anda telah ditolak oleh pihak pentadbiran.</div>
            @if($profile->catatan_permohonan)
                <div class="mt-2"><b>Catatan:</b> {{ $profile->catatan_permohonan }}</div>
            @endif
        </div>
    @elseif($status === 'active')
        <div class="status-box section-spacing" id="tour-profile-status">
            <div class="fw-semibold mb-1 text-success">Keahlian Aktif</div>
            <div class="text-muted">
                Akaun keahlian anda telah aktif dan disahkan.
            </div>
        </div>
    @endif

    <div class="row g-4">

        <div class="col-xl-6">
            <div class="formal-card h-100" id="tour-profile-personal">
                <div class="formal-header">A. Maklumat Peribadi</div>
                <div class="formal-body">
                    <div class="info-row">
                        <div class="info-label">Nama</div>
                        <div class="info-value">{{ $profile->nama }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">No. MyKad</div>
                        <div class="info-value">{{ $profile->no_kp }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Tarikh Lahir</div>
                        <div class="info-value">{{ optional($profile->tarikh_lahir)->format('d/m/Y') ?? '-' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Jantina</div>
                        <div class="info-value">{{ ucfirst($profile->jantina ?? '-') }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Agama</div>
                        <div class="info-value">{{ $profile->agama ?? '-' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Warganegara</div>
                        <div class="info-value">{{ $profile->warganegara ?? '-' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="formal-card h-100" id="tour-profile-contact">
                <div class="formal-header">B. Maklumat Perhubungan</div>
                <div class="formal-body">
                    <div class="info-row">
                        <div class="info-label">Alamat Rumah</div>
                        <div class="info-value">{{ $profile->alamat_rumah ?? '-' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">No. Tel Rumah</div>
                        <div class="info-value">{{ $profile->no_tel_rumah ?? '-' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">No. Telefon Bimbit</div>
                        <div class="info-value">{{ $profile->no_tel_bimbit ?? '-' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="formal-card h-100" id="tour-profile-community">
                <div class="formal-header">C. Maklumat Kariah</div>
                <div class="formal-body">
                    <div class="info-row">
                        <div class="info-label">Tinggal Dalam Kariah</div>
                        <div class="info-value">
                            <span class="badge bg-{{ $profile->tinggal_dalam_kariah ? 'success' : 'secondary' }}">
                                {{ $profile->tinggal_dalam_kariah ? 'Ya' : 'Tidak' }}
                            </span>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Tempoh Menetap</div>
                        <div class="info-value">{{ $profile->tempoh_menetap ?? '-' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="formal-card h-100" id="tour-profile-employment">
                <div class="formal-header">D. Maklumat Pekerjaan</div>
                <div class="formal-body">
                    <div class="info-row">
                        <div class="info-label">Pekerjaan</div>
                        <div class="info-value">{{ $profile->pekerjaan ?? '-' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Nama Majikan</div>
                        <div class="info-value">{{ $profile->nama_majikan ?? '-' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Alamat Kerja</div>
                        <div class="info-value">{{ $profile->alamat_kerja ?? '-' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="formal-card" id="tour-profile-application">
                <div class="formal-header">
                    E. Maklumat Permohonan
                </div>
                <div class="formal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-summary mb-0">
                            <tbody>
                                <tr>
                                    <td>Tarikh Permohonan</td>
                                    <td>{{ $profile->tarikh_permohonan ? \Carbon\Carbon::parse($profile->tarikh_permohonan)->format('d/m/Y') : '-' }}</td>
                                    <td>Status Permohonan</td>
                                    <td>
                                        <span class="badge bg-{{ $statusClass }}">
                                            {{ $statusLabel }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Catatan</td>
                                    <td colspan="3">{{ $profile->catatan_permohonan ?? '-' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        @unless($isDependent)
        <div class="col-12">
            <div class="formal-card" id="tour-profile-payment">
                <div class="formal-header">F. Maklumat Bayaran</div>
                <div class="formal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-summary mb-0">
                            <tbody>
                                <tr>
                                    <td>Kaedah Bayaran Dipilih</td>
                                    <td>{{ $paymentPlanLabel }}</td>
                                    <td>Yuran Pendaftaran</td>
                                    <td>RM20</td>
                                </tr>
                                <tr>
                                    <td>Jumlah Bayaran Permulaan</td>
                                    <td>
                                        @if($profile->payment_plan === 'tahunan')
                                            RM120
                                        @elseif($profile->payment_plan === 'bulanan')
                                            RM30
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>Keterangan</td>
                                    <td>
                                        @if($profile->payment_plan === 'tahunan')
                                            RM20 pendaftaran + RM100 tahunan
                                        @elseif($profile->payment_plan === 'bulanan')
                                            RM20 pendaftaran + RM10 bulan pertama, kemudian RM10 setiap bulan
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endunless

    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tourButton = document.getElementById('btnPageTour');

        if (!tourButton) {
            return;
        }

        tourButton.addEventListener('click', function () {
            if (!window.driver || !window.driver.js) {
                console.error('Driver.js tidak berjaya dimuatkan.');
                return;
            }

            const driver = window.driver.js.driver;

            const allSteps = [
                {
                    element: '#tour-profile-header',
                    popover: {
                        title: 'Maklumat Profil',
                        description: 'Halaman ini memaparkan maklumat peribadi, status permohonan dan butiran keahlian anda dalam sistem e-Pusara.',
                        side: 'bottom',
                        align: 'start'
                    }
                },
                {
                    element: '#tour-profile-edit',
                    popover: {
                        title: 'Kemas Kini Maklumat',
                        description: 'Klik butang ini untuk mengemas kini maklumat profil anda sekiranya terdapat perubahan pada data peribadi atau perhubungan.',
                        side: 'bottom',
                        align: 'end'
                    }
                },
                {
                    element: '#tour-profile-summary',
                    popover: {
                        title: 'Ringkasan Profil dan Status',
                        description: 'Bahagian ini menunjukkan nama, nombor MyKad, status permohonan dan tarikh permohonan keahlian anda.',
                        side: 'bottom',
                        align: 'center'
                    }
                },
                {
                    element: '#tour-profile-status',
                    popover: {
                        title: 'Status Keahlian',
                        description: 'Semak perkembangan permohonan atau status keahlian anda di sini. Arahan tambahan akan dipaparkan jika tindakan diperlukan.',
                        side: 'bottom',
                        align: 'center'
                    }
                },
                {
                    element: '#tour-profile-personal',
                    popover: {
                        title: 'Maklumat Peribadi',
                        description: 'Semak maklumat asas anda seperti nama, nombor MyKad, tarikh lahir, jantina dan agama.',
                        side: 'right',
                        align: 'start'
                    }
                },
                {
                    element: '#tour-profile-contact',
                    popover: {
                        title: 'Maklumat Perhubungan',
                        description: 'Pastikan alamat rumah dan nombor telefon sentiasa tepat supaya pihak pentadbiran dapat menghubungi anda jika diperlukan.',
                        side: 'left',
                        align: 'start'
                    }
                },
                {
                    element: '#tour-profile-community',
                    popover: {
                        title: 'Maklumat Kariah',
                        description: 'Bahagian ini menunjukkan maklumat tempat tinggal dalam kariah dan tempoh menetap yang berkaitan dengan kelayakan keahlian.',
                        side: 'right',
                        align: 'start'
                    }
                },
                {
                    element: '#tour-profile-employment',
                    popover: {
                        title: 'Maklumat Pekerjaan',
                        description: 'Semak maklumat pekerjaan dan majikan yang telah didaftarkan dalam permohonan anda.',
                        side: 'left',
                        align: 'start'
                    }
                },
            
                {
                    element: '#tour-profile-application',
                    popover: {
                        title: 'Maklumat Permohonan',
                        description: 'Lihat tarikh penghantaran, status semasa dan catatan pentadbir bagi permohonan keahlian anda.',
                        side: 'top',
                        align: 'center'
                    }
                },
                {
                    element: '#tour-profile-payment',
                    popover: {
                        title: 'Maklumat Pelan Bayaran',
                        description: 'Bahagian ini memaparkan kaedah bayaran yang dipilih serta jumlah bayaran permulaan berkaitan keahlian anda.',
                        side: 'top',
                        align: 'center'
                    }
                }
            ];

            /*
            |--------------------------------------------------------------------------
            | Hanya paparkan bahagian yang wujud
            |--------------------------------------------------------------------------
            | Maklumat Waris dan Maklumat Bayaran tidak dipaparkan untuk
            | akaun tanggungan, jadi step tersebut akan dilangkau automatik.
            */
            const availableSteps = allSteps.filter(function (step) {
                return document.querySelector(step.element);
            });

            if (availableSteps.length === 0) {
                console.warn('Tiada elemen tour ditemui pada halaman ini.');
                return;
            }

           let profileTour;

            profileTour = driver({
                animate: true,
                smoothScroll: true,
                popoverClass: 'epusara-tour-popover',

                allowClose: true,
                overlayColor: '#0f172a',
                overlayOpacity: 0.58,
                stagePadding: 10,
                stageRadius: 10,
                popoverOffset: 14,
                disableActiveInteraction: true,

                showProgress: false,

                nextBtnText: 'Seterusnya →',
                prevBtnText: '← Sebelumnya',
                doneBtnText: 'Selesai',

                onPopoverRender: function () {
                    const currentIndex = profileTour.getActiveIndex() ?? 0;
                    window.updateEpusaraTourPopover(
                        profileTour,
                        currentIndex,
                        availableSteps.length
                    );
                },

                steps: availableSteps
            });

            profileTour.drive();
        });
    });
</script>
@endpush