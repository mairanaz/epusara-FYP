@extends('layouts.app')

@section('content')

<style>
    .admin-dashboard-page {
        padding-bottom: 30px;
    }

    .admin-dashboard-page .welcome-card {
        border: 0;
        border-radius: 20px;
        background: linear-gradient(135deg, #0f766e, #0d9488);
        color: #ffffff;
        overflow: hidden;
        position: relative;
    }

    .admin-dashboard-page .welcome-card::after {
        content: "";
        position: absolute;
        top: -60px;
        right: -40px;
        width: 220px;
        height: 220px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.13);
    }

    .admin-dashboard-page .welcome-card h3 {
        color: #ffffff;
        font-weight: 800;
        margin-bottom: 8px;
    }

    .admin-dashboard-page .welcome-card p {
        color: rgba(255, 255, 255, 0.95);
        font-size: 15px;
        max-width: 850px;
    }

    .admin-dashboard-page .summary-card {
        border: 0;
        border-radius: 18px;
        background: #ffffff;
        box-shadow: 0 10px 26px rgba(15, 23, 42, 0.07);
        height: 100%;
    }

    .admin-dashboard-page .summary-label {
        color: #64748b;
        font-weight: 700;
        font-size: 14px;
        margin-bottom: 8px;
    }

    .admin-dashboard-page .summary-value {
        color: #020617;
        font-weight: 800;
        font-size: 34px;
        line-height: 1;
        margin-bottom: 0;
    }

    .admin-dashboard-page .summary-icon {
        width: 58px;
        height: 58px;
        border-radius: 18px;
        background: #e0f2f1;
        color: #0f766e;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 30px;
        flex-shrink: 0;
    }

    .admin-dashboard-page .summary-icon.warning {
        background: #fff7ed;
        color: #f59e0b;
    }

    .admin-dashboard-page .summary-icon.primary {
        background: #eff6ff;
        color: #2563eb;
    }

    .admin-dashboard-page .action-card {
        border: 0;
        border-radius: 18px;
        background: #ffffff;
        box-shadow: 0 10px 26px rgba(15, 23, 42, 0.07);
    }

    .admin-dashboard-page .action-title {
        color: #020617;
        font-weight: 800;
        margin-bottom: 4px;
    }

    .admin-dashboard-page .action-subtitle {
        color: #64748b;
        margin-bottom: 22px;
    }

    .admin-dashboard-page .action-box {
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        padding: 18px;
        height: 100%;
        background: #ffffff;
    }

    .admin-dashboard-page .action-count {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        background: #f8fafc;
        color: #0f766e;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        font-weight: 800;
        flex-shrink: 0;
    }

    .admin-dashboard-page .action-name {
        color: #020617;
        font-weight: 800;
        margin-bottom: 3px;
    }

    .admin-dashboard-page .action-desc {
        color: #64748b;
        margin-bottom: 0;
        font-size: 14px;
    }

    .admin-dashboard-page .btn-semak {
        border-radius: 12px;
        padding: 9px 18px;
        font-weight: 700;
        background: #0f766e;
        border-color: #0f766e;
        color: #ffffff;
        white-space: nowrap;
    }

    .admin-dashboard-page .btn-semak:hover {
        background: #115e59;
        border-color: #115e59;
        color: #ffffff;
    }

    .admin-dashboard-page .note-card {
        border: 1px solid #dbeafe;
        border-radius: 16px;
        background: #eff6ff;
        color: #1e3a8a;
        padding: 16px 18px;
        margin-top: 22px;
    }

    @media (max-width: 767px) {
        .admin-dashboard-page .welcome-card .card-body {
            padding: 24px !important;
        }

        .admin-dashboard-page .summary-value {
            font-size: 30px;
        }

        .admin-dashboard-page .action-box .d-flex {
            align-items: flex-start !important;
            gap: 14px;
        }

        .admin-dashboard-page .btn-semak {
            padding: 8px 14px;
        }
    }
</style>

@php
    /*
    |--------------------------------------------------------------------------
    | Link Selamat
    |--------------------------------------------------------------------------
    | Guna Route::has supaya dashboard tidak crash kalau nama route berbeza.
    */

    $deathReportUrl = \Illuminate\Support\Facades\Route::has('admin.death-reports.index')
        ? route('admin.death-reports.index')
        : url('/admin/death-reports');

    $graveOrderUrl = \Illuminate\Support\Facades\Route::has('admin.grave-orders.index')
        ? route('admin.grave-orders.index')
        : url('/admin/grave-orders');
@endphp

<div class="container-fluid admin-dashboard-page">

    {{-- Welcome Section --}}
    <div class="card welcome-card mb-4">
        <div class="card-body p-4 p-lg-5">
            <h3>Selamat Datang, Admin</h3>
            <p class="mb-0">
                Ringkasan pengurusan e-Pusara RTB Bukit Changgang untuk memudahkan admin
                menyemak ahli, laporan kematian dan tempahan kepukan.
            </p>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="row g-4 mb-4">

        {{-- Jumlah Ahli Utama --}}
        <div class="col-xl-3 col-md-6">
            <div class="summary-card">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="summary-label">Jumlah Ahli Utama</div>
                            <h2 class="summary-value">{{ $totalAhliUtama ?? 0 }}</h2>
                        </div>
                        <div class="summary-icon">
                            <i class="bx bx-user"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Jumlah Tanggungan --}}
        <div class="col-xl-3 col-md-6">
            <div class="summary-card">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="summary-label">Jumlah Tanggungan</div>
                            <h2 class="summary-value">{{ $totalTanggungan ?? 0 }}</h2>
                        </div>
                        <div class="summary-icon">
                            <i class="bx bx-group"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Laporan Kematian Menunggu --}}
        <div class="col-xl-3 col-md-6">
            <div class="summary-card">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="summary-label">Laporan Kematian Menunggu</div>
                            <h2 class="summary-value">{{ $laporanKematianMenunggu ?? 0 }}</h2>
                        </div>
                        <div class="summary-icon warning">
                            <i class="bx bx-file"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tempahan Kepukan Menunggu --}}
        <div class="col-xl-3 col-md-6">
            <div class="summary-card">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="summary-label">Tempahan Kepukan Menunggu</div>
                            <h2 class="summary-value">{{ $tempahanKepukanMenunggu ?? 0 }}</h2>
                        </div>
                        <div class="summary-icon primary">
                            <i class="bx bx-calendar-check"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Tindakan Segera --}}
    <div class="card action-card">
        <div class="card-body p-4">

            <h4 class="action-title">Tindakan Segera</h4>
            <p class="action-subtitle">
                Senarai tugasan utama yang perlu disemak oleh admin.
            </p>

            <div class="row g-3">

                {{-- Laporan Kematian --}}
                <div class="col-lg-6">
                    <div class="action-box">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="action-count me-3">
                                    {{ $laporanKematianMenunggu ?? 0 }}
                                </div>

                                <div>
                                    <h6 class="action-name">Laporan Kematian</h6>
                                    <p class="action-desc">Belum disahkan oleh admin</p>
                                </div>
                            </div>

                            <a href="{{ $deathReportUrl }}" class="btn btn-semak">
                                Semak
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Tempahan Kepukan --}}
                <div class="col-lg-6">
                    <div class="action-box">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="action-count me-3">
                                    {{ $tempahanKepukanMenunggu ?? 0 }}
                                </div>

                                <div>
                                    <h6 class="action-name">Tempahan Kepukan</h6>
                                    <p class="action-desc">Menunggu kelulusan admin</p>
                                </div>
                            </div>

                            <a href="{{ $graveOrderUrl }}" class="btn btn-semak">
                                Semak
                            </a>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Nota --}}
            <div class="note-card">
                <strong>Nota:</strong>
                Dashboard ini hanya memaparkan ringkasan penting untuk membantu admin membuat semakan dengan lebih cepat.
            </div>

        </div>
    </div>

</div>

@endsection