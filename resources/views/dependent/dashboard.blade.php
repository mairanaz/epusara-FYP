@extends('layouts.app')

@section('title', 'Dashboard Tanggungan')

@section('content')
@php
    $currentUser = auth()->user();

    $dependentData = $dependentProfile ?? null;
    $mainMemberData = $mainMember ?? null;

    /*
    |--------------------------------------------------------------------------
    | Data Paparan Dashboard Tanggungan
    |--------------------------------------------------------------------------
    */
    $displayName = $dependentData?->name ?? $currentUser->name ?? 'Tanggungan';
    $mainMemberName = $mainMemberData?->name ?? 'Belum Dipautkan';

    $displayDependentStatus = $dependentData
        ? ($dependentStatus ?? 'Aktif')
        : 'Belum Dipautkan';

    $displayLifeStatus = $dependentData
        ? ($lifeStatus ?? 'Hidup')
        : 'Belum Dipautkan';

    $displayRelationship = $dependentData?->pertalian ?? ($relationship ?? '-');

    $displayIneligibilityReason = $ineligibilityReason ?? null;
    $displayDeathDate = $deathDate ?? null;

    /*
    |--------------------------------------------------------------------------
    | Warna Badge Status
    |--------------------------------------------------------------------------
    */
    $statusClass = function ($status) {
        $normalized = strtolower(str_replace([' ', '-'], '_', trim((string) $status)));

        return match ($normalized) {
            'aktif', 'hidup' => 'success',
            'tidak_aktif', 'meninggal_dunia' => 'danger',
            'belum_dipautkan' => 'warning',
            'belum_ditentukan' => 'secondary',
            default => 'secondary',
        };
    };

    /*
    |--------------------------------------------------------------------------
    | Route Selamat
    |--------------------------------------------------------------------------
    */
    $safeRoute = function (array $names) {
        foreach ($names as $name) {
            if (\Illuminate\Support\Facades\Route::has($name)) {
                return route($name);
            }
        }

        return '#';
    };

    $profileUrl = $safeRoute([
        'user.profile.index',
        'user.profile.show',
        'user.profile.edit',
    ]);

    $familyUrl = $safeRoute([
        'dependent.main-member',
        'user.profile.show',
        'user.profile.index',
    ]);

    $deathReportUrl = $safeRoute([
        'death-reports.create',
        'death-reports.index',
        'user.death-reports.index',
    ]);
@endphp

<style>
    .member-dashboard .hero-card {
        background: linear-gradient(135deg, #0c7359 0%, #15906f 100%);
        color: #fff;
        border: 0;
        overflow: hidden;
        position: relative;
    }

    .member-dashboard .hero-card::after {
        content: '';
        position: absolute;
        right: -55px;
        top: -70px;
        width: 205px;
        height: 205px;
        border-radius: 50%;
        background: rgba(255, 255, 255, .10);
    }

    .member-dashboard .hero-card::before {
        content: '';
        position: absolute;
        right: 75px;
        bottom: -80px;
        width: 150px;
        height: 150px;
        border-radius: 50%;
        background: rgba(255, 255, 255, .08);
    }

    .member-dashboard .hero-card .card-body {
        position: relative;
        z-index: 1;
    }

    .member-dashboard .hero-subtitle {
        color: rgba(255, 255, 255, .82);
    }

    .member-dashboard .hero-account {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border-radius: 50rem;
        padding: .38rem .72rem;
        margin-bottom: 12px;
        font-size: .75rem;
        font-weight: 600;
        color: #ffffff;
        background: rgba(255, 255, 255, .15);
    }

    .member-dashboard .btn-hero {
        background: #fff;
        color: #087052;
        border: 0;
        font-weight: 600;
    }

    .member-dashboard .btn-hero:hover {
        background: #eefaf5;
        color: #075f47;
    }

    .member-dashboard .family-panel {
        border: 1px solid #e8efe9;
        background: #fbfefd;
    }

    .member-dashboard .member-profile {
        border-bottom: 1px solid #e8efe9;
        padding-bottom: 18px;
        margin-bottom: 6px;
    }

    .member-dashboard .member-icon {
        width: 50px;
        height: 50px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 25px;
        color: #0c7359;
        background: #e6f5f0;
        flex-shrink: 0;
    }

    .member-dashboard .detail-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        padding: 13px 0;
        border-bottom: 1px solid #f0f2f5;
    }

    .member-dashboard .detail-row:last-child {
        border-bottom: 0;
        padding-bottom: 0;
    }

    .member-dashboard .detail-label {
        color: #6c757d;
        font-size: 13px;
    }

    .member-dashboard .detail-value {
        color: #263238;
        font-size: 14px;
        font-weight: 600;
        text-align: right;
    }

    .member-dashboard .dashboard-badge {
        display: inline-flex;
        align-items: center;
        border-radius: 50rem;
        padding: .38rem .7rem;
        font-size: .75rem;
        font-weight: 600;
    }

    .member-dashboard .badge-soft-success {
        color: #14804a;
        background: #e6f6ec;
    }

    .member-dashboard .badge-soft-danger {
        color: #bd3434;
        background: #fde8e8;
    }

    .member-dashboard .badge-soft-warning {
        color: #a66700;
        background: #fff3d6;
    }

    .member-dashboard .badge-soft-secondary {
        color: #59636f;
        background: #edf0f3;
    }

    .member-dashboard .quick-action {
        display: block;
        height: 100%;
        color: #263238;
        text-decoration: none;
        border: 1px solid #edf0f3;
        border-radius: 12px;
        padding: 14px 10px;
        text-align: center;
        transition: all .18s ease;
        background: #fff;
    }

    .member-dashboard .quick-action:hover {
        border-color: #b9ddcf;
        background: #f4fbf8;
        transform: translateY(-1px);
        color: #087052;
    }

    .member-dashboard .quick-action i {
        display: block;
        color: #0c7359;
        font-size: 25px;
        margin-bottom: 7px;
    }

    .member-dashboard .access-note {
        padding: 13px;
        margin-top: 14px;
        border: 1px solid #edf2ef;
        border-radius: 10px;
        background: #f7faf9;
    }

    .member-dashboard .access-note i {
        color: #0c7359;
        font-size: 20px;
    }

    .member-dashboard .inactive-box {
        padding: 13px;
        margin-top: 16px;
        border: 1px solid #f4d0a3;
        border-radius: 10px;
        color: #9a3412;
        background: #fff7ed;
        font-size: 13px;
    }

    @media (max-width: 576px) {
        .member-dashboard .detail-row {
            align-items: flex-start;
            flex-direction: column;
            gap: 5px;
        }

        .member-dashboard .detail-value {
            text-align: left;
        }
    }
</style>

<div class="member-dashboard">

    {{-- Tajuk Halaman --}}
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <p class="fw-semibold fs-18 mb-1">Dashboard Tanggungan</p>
            <span class="text-muted">
                Ringkasan maklumat tanggungan dan hubungan dengan ahli utama.
            </span>
        </div>
    </div>

    {{-- Welcome / Hero --}}
    <div class="card custom-card hero-card mb-4" id="tour-dependent-hero">
        <div class="card-body p-4">
            <div class="row align-items-center g-3">
                <div class="col-lg-8">
                    <div class="hero-account">
                        <i class="bx bx-check-shield"></i>
                        Akaun Tanggungan
                    </div>

                    <p class="hero-subtitle mb-1">Selamat datang kembali,</p>

                    <h3 class="text-white fw-semibold mb-2">
                        {{ $displayName }}
                    </h3>

                    <p class="hero-subtitle mb-0">
                        Semak maklumat diri sebagai tanggungan, ahli utama berkaitan,
                        pertalian keluarga dan fungsi laporan kematian jika diperlukan.
                    </p>
                </div>

                <div class="col-lg-4 text-lg-end">
                    <a href="{{ $familyUrl }}" class="btn btn-hero px-4 py-2">
                        <i class="bx bx-group me-1"></i>
                        Semak Maklumat Tanggungan
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">

        {{-- Maklumat Keahlian Keluarga --}}
        <div class="col-xl-7">
            <div class="card custom-card h-100 mb-0" id="tour-dependent-family">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="card-title mb-1">Maklumat Keahlian Keluarga</h5>
                        <p class="text-muted fs-12 mb-0">
                            Butiran ringkas ahli utama berkaitan dan status tanggungan anda
                        </p>
                    </div>

                    <a href="{{ $familyUrl }}" class="btn btn-sm btn-outline-info">
                        Lihat Maklumat
                    </a>
                </div>

                <div class="card-body">
                    <div class="family-panel rounded-3 p-4">

                        <div class="member-profile d-flex align-items-center gap-3">
                            <div class="member-icon">
                                <i class="bx bx-user"></i>
                            </div>

                            <div>
                                <p class="text-muted fs-12 mb-1">Ahli Utama</p>
                                <h6 class="fw-semibold mb-0">{{ $mainMemberName }}</h6>
                            </div>
                        </div>

                        <div class="detail-row">
                            <span class="detail-label">Nama Tanggungan</span>
                            <span class="detail-value">{{ $displayName }}</span>
                        </div>

                        <div class="detail-row">
                            <span class="detail-label">Pertalian</span>
                            <span class="detail-value">{{ $displayRelationship }}</span>
                        </div>

                        <div class="detail-row">
                            <span class="detail-label">Status Akaun Tanggungan</span>
                            <span class="detail-value">
                                <span class="dashboard-badge badge-soft-{{ $statusClass($displayDependentStatus) }}">
                                    {{ $displayDependentStatus }}
                                </span>
                            </span>
                        </div>

                        <div class="detail-row">
                            <span class="detail-label">Status Rekod</span>
                            <span class="detail-value">
                                <span class="dashboard-badge badge-soft-{{ $statusClass($displayLifeStatus) }}">
                                    {{ $displayLifeStatus }}
                                </span>
                            </span>
                        </div>

                        @if($displayDeathDate)
                            <div class="detail-row">
                                <span class="detail-label">Tarikh Kematian</span>
                                <span class="detail-value">{{ $displayDeathDate }}</span>
                            </div>
                        @endif

                        @if($displayIneligibilityReason)
                            <div class="inactive-box">
                                <strong>Sebab Tidak Aktif:</strong>
                                {{ $displayIneligibilityReason }}
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>

        {{-- Tindakan Pantas --}}
        <div class="col-xl-5">
            <div class="card custom-card h-100 mb-0" id="tour-dependent-quick-actions">
                <div class="card-header">
                    <h5 class="card-title mb-1">Tindakan Pantas</h5>
                    <p class="text-muted fs-12 mb-0">Akses fungsi yang sesuai untuk akaun tanggungan</p>
                </div>

                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-4">
                            <a class="quick-action" href="{{ $profileUrl }}">
                                <i class="bx bx-id-card"></i>
                                <span class="fs-12">Maklumat Diri</span>
                            </a>
                        </div>

                        <div class="col-4">
                            <a class="quick-action" href="{{ $familyUrl }}">
                                <i class="bx bx-group"></i>
                                <span class="fs-12">Keluarga</span>
                            </a>
                        </div>

                        <div class="col-4">
                            <a class="quick-action" href="{{ $deathReportUrl }}">
                                <i class="bx bx-file"></i>
                                <span class="fs-12">Lapor Kematian</span>
                            </a>
                        </div>
                    </div>

                    <div class="access-note">
                        <div class="d-flex gap-2 align-items-start">
                            <i class="bx bx-info-circle"></i>

                            <p class="mb-0 text-muted fs-12">
                                Akaun tanggungan tidak perlu membuat bayaran yuran khairat sendiri.
                                Bayaran diuruskan melalui ahli utama yang berkaitan.
                            </p>
                        </div>
                    </div>

                    @if(!$dependentData)
                        <div class="inactive-box mt-3">
                            <strong>Perhatian:</strong>
                            Rekod tanggungan belum dipautkan. Pastikan nombor kad pengenalan dalam profil sama
                            dengan nombor kad pengenalan yang didaftarkan oleh ahli utama.
                        </div>
                    @endif
                </div>
            </div>
        </div>

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
                    element: '#tour-dependent-hero',
                    popover: {
                        title: 'Dashboard Tanggungan',
                        description: 'Selamat datang ke e-Pusara. Dashboard ini ialah halaman utama bagi pengguna yang berdaftar sebagai tanggungan.',
                        side: 'bottom',
                        align: 'start'
                    }
                },
                {
                    element: '#tour-dependent-family',
                    popover: {
                        title: 'Maklumat Keahlian Keluarga',
                        description: 'Bahagian ini memaparkan ahli utama berkaitan, pertalian serta status rekod tanggungan anda.',
                        side: 'right',
                        align: 'start'
                    }
                },
                {
                    element: '#tour-dependent-quick-actions',
                    popover: {
                        title: 'Tindakan Pantas',
                        description: 'Gunakan menu ini untuk mengakses maklumat diri, keluarga dan laporan kematian.',
                        side: 'left',
                        align: 'start'
                    }
                },
                {
                    element: '#btnPageTour',
                    popover: {
                        title: 'Buka Semula Panduan',
                        description: 'Klik icon bantuan pada navbar pada bila-bila masa untuk melihat panduan halaman ini semula.',
                        side: 'bottom',
                        align: 'end'
                    }
                }
            ];

            const availableSteps = allSteps.filter(function (step) {
                return document.querySelector(step.element);
            });

            if (availableSteps.length === 0) {
                return;
            }

            let dependentDashboardTour;

            dependentDashboardTour = driver({
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
                    const currentIndex = dependentDashboardTour.getActiveIndex() ?? 0;

                    if (typeof window.updateEpusaraTourPopover === 'function') {
                        window.updateEpusaraTourPopover(
                            dependentDashboardTour,
                            currentIndex,
                            availableSteps.length
                        );
                    }
                },

                steps: availableSteps
            });

            dependentDashboardTour.drive();
        });
    });
</script>
@endpush
