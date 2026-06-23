@extends('layouts.app')

@section('title', 'Dashboard Ahli Utama')

@section('content')
@php
    $currentUser = auth()->user();

    $displayName = $currentUser->name ?? 'Ahli Utama';

    $totalDependents = $dependentCount ?? 0;

    $displayPaymentStatus = $paymentStatus ?? 'Belum Ada Rekod';
    $displayPaymentPeriod = $paymentPeriod ?? '-';
    $displayPaymentAmount = isset($paymentAmount) ? 'RM ' . number_format($paymentAmount, 2) : '-';
    $displayLastPaymentDate = $lastPaymentDate ?? '-';

    $displayDeathReportStatus = $deathReportStatus ?? 'Tiada Laporan';
    $displayKhairatStatus = $khairatStatus ?? 'Belum Disambungkan';

    $safeRoute = function (array $names) {
        foreach ($names as $name) {
            if (\Illuminate\Support\Facades\Route::has($name)) {
                return route($name);
            }
        }

        return '#';
    };

    $profileUrl = $safeRoute([
        'user.profile.show',
        'user.profile.index',
        'user.profile.edit',
        'user.profile.create.step1',
    ]);

    $dependentUrl = $safeRoute([
        'user.dependents.index',
        'dependents.index',
    ]);

    $paymentUrl = $safeRoute([
        'payments.index',
        'user.payments.index',
        'payments.create',
    ]);

    $deathReportUrl = $safeRoute([
        'death-reports.create',
        'user.death-reports.create',
        'death-reports.index',
    ]);

    $statusClass = function ($status) {
        $status = strtolower(trim((string) $status));

        return match ($status) {
            'telah dibayar', 'disahkan', 'aktif' => 'success',
            'dalam proses', 'dalam semakan' => 'warning',
            'gagal', 'ditolak', 'dibatalkan' => 'danger',
            default => 'secondary',
        };
    };
@endphp

<style>
    .main-dashboard .hero-card {
        background: linear-gradient(135deg, #0c7359 0%, #15906f 100%);
        color: #fff;
        border: 0;
        overflow: hidden;
        position: relative;
    }

    .main-dashboard .hero-card::after {
        content: '';
        position: absolute;
        right: -60px;
        top: -70px;
        width: 215px;
        height: 215px;
        border-radius: 50%;
        background: rgba(255,255,255,.10);
    }

    .main-dashboard .hero-card::before {
        content: '';
        position: absolute;
        right: 95px;
        bottom: -90px;
        width: 165px;
        height: 165px;
        border-radius: 50%;
        background: rgba(255,255,255,.08);
    }

    .main-dashboard .hero-card .card-body {
        position: relative;
        z-index: 1;
    }

    .main-dashboard .hero-account {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border-radius: 50rem;
        padding: .38rem .72rem;
        margin-bottom: 12px;
        font-size: .75rem;
        font-weight: 600;
        color: #fff;
        background: rgba(255,255,255,.15);
    }

    .main-dashboard .hero-subtitle {
        color: rgba(255,255,255,.84);
    }

    .main-dashboard .btn-hero {
        background: #fff;
        color: #087052;
        border: 0;
        font-weight: 600;
    }

    .main-dashboard .btn-hero:hover {
        background: #eefaf5;
        color: #075f47;
    }

    .main-dashboard .stat-card {
        border: 1px solid #eef1f5;
    }

    .main-dashboard .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 25px;
        flex-shrink: 0;
    }

    .main-dashboard .soft-primary {
        color: #0c7359;
        background: #e6f5f0;
    }

    .main-dashboard .soft-success {
        color: #168754;
        background: #e8f7ef;
    }

    .main-dashboard .soft-warning {
        color: #a66700;
        background: #fff3d6;
    }

    .main-dashboard .soft-info {
        color: #116b96;
        background: #e7f4fb;
    }

    .main-dashboard .dashboard-badge {
        display: inline-flex;
        align-items: center;
        border-radius: 50rem;
        padding: .38rem .7rem;
        font-size: .75rem;
        font-weight: 600;
    }

    .main-dashboard .badge-soft-success {
        color: #14804a;
        background: #e6f6ec;
    }

    .main-dashboard .badge-soft-warning {
        color: #a66700;
        background: #fff3d6;
    }

    .main-dashboard .badge-soft-danger {
        color: #bd3434;
        background: #fde8e8;
    }

    .main-dashboard .badge-soft-secondary {
        color: #59636f;
        background: #edf0f3;
    }

    .main-dashboard .quick-action {
        display: block;
        height: 100%;
        color: #263238;
        text-decoration: none;
        border: 1px solid #edf0f3;
        border-radius: 12px;
        padding: 15px 10px;
        text-align: center;
        transition: all .18s ease;
        background: #fff;
    }

    .main-dashboard .quick-action:hover {
        border-color: #b9ddcf;
        background: #f4fbf8;
        transform: translateY(-1px);
        color: #087052;
    }

    .main-dashboard .quick-action i {
        display: block;
        color: #0c7359;
        font-size: 26px;
        margin-bottom: 8px;
    }

    .main-dashboard .dependent-item,
    .main-dashboard .activity-item,
    .main-dashboard .announcement-item {
        padding: 13px 0;
        border-bottom: 1px solid #f0f2f5;
    }

    .main-dashboard .dependent-item:last-child,
    .main-dashboard .activity-item:last-child,
    .main-dashboard .announcement-item:last-child {
        border-bottom: 0;
        padding-bottom: 0;
    }

    .main-dashboard .empty-box {
        padding: 20px;
        border: 1px dashed #d9e2dc;
        border-radius: 12px;
        background: #f8fbfa;
        text-align: center;
    }

    @media (max-width: 576px) {
        .main-dashboard .hero-card .text-lg-end {
            text-align: left !important;
        }
    }
</style>

<div class="main-dashboard">

    {{-- Tajuk Halaman --}}
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <p class="fw-semibold fs-18 mb-1">Dashboard Ahli Utama</p>
            <span class="text-muted">
                Ringkasan keahlian khairat, bayaran, tanggungan dan laporan kematian anda.
            </span>
        </div>
    </div>

    {{-- Hero --}}
    <div class="card custom-card hero-card mb-4">
        <div class="card-body p-4">
            <div class="row align-items-center g-3">
                <div class="col-lg-8">
                    <div class="hero-account">
                        <i class="bx bx-check-shield"></i>
                        Akaun Ahli Utama
                    </div>

                    <p class="hero-subtitle mb-1">Selamat datang kembali,</p>

                    <h3 class="text-white fw-semibold mb-2">
                        {{ $displayName }}
                    </h3>

                    <p class="hero-subtitle mb-0">
                        Urus profil keahlian, tanggungan, bayaran khairat dan laporan kematian melalui sistem e-Pusara.
                    </p>
                </div>

                <div class="col-lg-4 text-lg-end">
                    <a href="{{ $profileUrl }}" class="btn btn-hero px-4 py-2">
                        <i class="bx bx-user me-1"></i>
                        Lihat Profil
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card custom-card stat-card h-100 mb-0">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted mb-1">Jumlah Tanggungan</p>
                            <h4 class="fw-semibold mb-2">{{ $totalDependents }}</h4>
                            <small class="text-muted">Rekod keluarga didaftarkan</small>
                        </div>

                        <span class="stat-icon soft-primary">
                            <i class="bx bx-group"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card custom-card stat-card h-100 mb-0">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted mb-1">Status Bayaran</p>
                            <h5 class="fw-semibold mb-2">{{ $displayPaymentStatus }}</h5>
                            <small class="text-muted">Tempoh: {{ $displayPaymentPeriod }}</small>
                        </div>

                        <span class="stat-icon soft-success">
                            <i class="bx bx-receipt"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card custom-card stat-card h-100 mb-0">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted mb-1">Bayaran Terkini</p>
                            <h5 class="fw-semibold mb-2">{{ $displayPaymentAmount }}</h5>
                            <small class="text-muted">{{ $displayLastPaymentDate }}</small>
                        </div>

                        <span class="stat-icon soft-warning">
                            <i class="bx bx-wallet"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card custom-card stat-card h-100 mb-0">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted mb-1">Laporan Kematian</p>
                            <h5 class="fw-semibold mb-2">{{ $displayDeathReportStatus }}</h5>
                            <small class="text-muted">Status laporan terkini</small>
                        </div>

                        <span class="stat-icon soft-info">
                            <i class="bx bx-file"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <div class="row g-3">

        {{-- Tindakan Pantas --}}
        <div class="col-xl-5">
            <div class="card custom-card h-100 mb-0">
                <div class="card-header">
                    <h5 class="card-title mb-1">Tindakan Pantas</h5>
                    <p class="text-muted fs-12 mb-0">Akses fungsi utama ahli dengan segera</p>
                </div>

                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-6">
                            <a class="quick-action" href="{{ $profileUrl }}">
                                <i class="bx bx-id-card"></i>
                                <span class="fs-12">Profil Ahli</span>
                            </a>
                        </div>

                        <div class="col-6">
                            <a class="quick-action" href="{{ $dependentUrl }}">
                                <i class="bx bx-group"></i>
                                <span class="fs-12">Tanggungan</span>
                            </a>
                        </div>

                        <div class="col-6">
                            <a class="quick-action" href="{{ $paymentUrl }}">
                                <i class="bx bx-wallet"></i>
                                <span class="fs-12">Bayaran</span>
                            </a>
                        </div>

                        <div class="col-6">
                            <a class="quick-action" href="{{ $deathReportUrl }}">
                                <i class="bx bx-file"></i>
                                <span class="fs-12">Lapor Kematian</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Senarai Tanggungan --}}
        <div class="col-xl-7">
            <div class="card custom-card h-100 mb-0">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="card-title mb-1">Tanggungan Aktif & Layak</h5>
                        <p class="text-muted fs-12 mb-0">
                            Ringkasan tanggungan yang masih layak di bawah keahlian anda
                        </p>
                    </div>

                    <a href="{{ $dependentUrl }}" class="btn btn-sm btn-outline-info">
                        Lihat Semua
                    </a>
                </div>

                <div class="card-body">
                    @if(isset($dependents) && $dependents->count() > 0)
                        @foreach($dependents as $dependent)
                            <div class="dependent-item">
                                <div class="d-flex align-items-center justify-content-between gap-3">
                                    <div>
                                        <h6 class="fw-semibold mb-1">
                                            {{ $dependent->name ?? '-' }}
                                        </h6>

                                        <p class="text-muted fs-12 mb-0">
                                            Pertalian: {{ $dependent->pertalian ?? '-' }}
                                            @if(!empty($dependent->no_kp))
                                                · No. KP: {{ $dependent->no_kp }}
                                            @endif
                                        </p>
                                    </div>

                                    @php
                                        $lifeStatus = strtolower((string) ($dependent->status_kehidupan ?? 'aktif'));

                                        $lifeStatusLabel = match ($lifeStatus) {
                                            'aktif', 'hidup', 'active' => 'Aktif',
                                            'meninggal', 'meninggal_dunia', 'deceased' => 'Meninggal Dunia',
                                            default => ucfirst(str_replace('_', ' ', $lifeStatus)),
                                        };
                                    @endphp

                                    <span class="dashboard-badge badge-soft-success">
                                        {{ $lifeStatusLabel }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="empty-box">
                            <i class="bx bx-group fs-2 text-muted"></i>
                            <h6 class="fw-semibold mt-2 mb-1">Belum Ada Tanggungan</h6>
                            <p class="text-muted fs-12 mb-3">
                                Anda belum merekodkan maklumat tanggungan.
                            </p>
                            <a href="{{ $dependentUrl }}" class="btn btn-sm btn-primary">
                                Tambah Tanggungan
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    

        {{-- Aktiviti --}}
        <div class="col-xl-12">
            <div class="card custom-card h-100 mb-0">
                <div class="card-header">
                    <h5 class="card-title mb-1">Aktiviti Terkini</h5>
                    <p class="text-muted fs-12 mb-0">Ringkasan aktiviti terkini akaun anda</p>
                </div>

                <div class="card-body">
                    @if(isset($activities) && $activities->count() > 0)
                        @foreach($activities as $activity)
                            <div class="activity-item">
                                <div class="d-flex align-items-start gap-3">
                                    <span class="stat-icon soft-primary">
                                        <i class="bx bx-history"></i>
                                    </span>

                                    <div>
                                        <h6 class="fw-semibold mb-1">
                                            {{ $activity['title'] ?? '-' }}
                                        </h6>
                                        <p class="text-muted fs-12 mb-0">
                                            {{ $activity['date'] ?? '-' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="empty-box">
                            <i class="bx bx-history fs-2 text-muted"></i>
                            <h6 class="fw-semibold mt-2 mb-1">Belum Ada Aktiviti</h6>
                            <p class="text-muted fs-12 mb-0">
                                Aktiviti berkaitan bayaran, laporan dan tanggungan akan dipaparkan di sini.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>
@endsection