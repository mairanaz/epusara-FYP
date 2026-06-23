@extends('layouts.app')

@section('content')
<style>
    .grave-report-page .hero-card {
        border: 0;
        border-radius: 22px;
        background: linear-gradient(135deg, #e8f0f8, #f6f9fc);
        color: #1f2937;
        overflow: hidden;
        position: relative;
    }

    .grave-report-page .hero-card::after {
        content: "";
        position: absolute;
        top: -45px;
        right: -45px;
        width: 190px;
        height: 190px;
        background: rgba(255,255,255,0.55);
        border-radius: 50%;
    }

    .grave-report-page .stats-card,
    .grave-report-page .filter-card,
    .grave-report-page .table-card,
    .grave-report-page .chart-card,
    .grave-report-page .analysis-card {
        border: 0;
        border-radius: 18px;
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.06);
    }

    .grave-report-page .stats-card {
        transition: 0.25s ease;
    }

    .grave-report-page .stats-card:hover {
        transform: translateY(-4px);
    }

    .grave-report-page .stats-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        flex-shrink: 0;
    }

    .grave-report-page .summary-label {
        font-size: 13px;
        color: #6b7280;
        margin-bottom: 6px;
    }

    .grave-report-page .summary-value {
        font-size: 24px;
        font-weight: 800;
        color: #111827;
        line-height: 1;
    }

    .grave-report-page .hero-subtitle {
        color: #6b7280;
    }

    .grave-report-page .search-box .form-control,
    .grave-report-page .search-box .form-select {
        border-radius: 12px;
        min-height: 46px;
        border-color: #dbe4ee;
    }

    .grave-report-page .btn {
        border-radius: 12px;
        min-height: 46px;
    }

    .grave-report-page .table-report {
        border: 1px solid #c8d6e5;
    }

    .grave-report-page .table-report thead th {
        background: #f1f5f9;
        border: 1px solid #c8d6e5;
        font-weight: 800;
        color: #14224a;
        vertical-align: middle;
        white-space: nowrap;
        padding: 15px 14px;
    }

    .grave-report-page .table-report tbody td {
        border: 1px solid #c8d6e5;
        vertical-align: middle;
        padding: 15px 14px;
        color: #111827;
    }

    .grave-report-page .table-report tbody tr:hover {
        background-color: #f8fbff;
    }

    .grave-report-page .total-row td {
        background: #f1f5f9;
        font-weight: 800;
        border-top: 2px solid #94a3b8;
    }

    .grave-report-page .report-note {
        background: #f8fafc;
        border-radius: 14px;
        padding: 14px 16px;
        color: #667085;
        font-size: 13px;
    }

    .grave-report-page .report-badge {
        background: #eef6ff;
        color: #2563eb;
        padding: 8px 12px;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .grave-report-page .chart-box {
        min-height: 320px;
    }

    .grave-report-page .person-avatar {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        color: #5b6b78;
        background: linear-gradient(135deg, #edf3f8, #d7e4ef);
        flex-shrink: 0;
    }

    .grave-report-page .person-name {
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 2px;
    }

    .grave-report-page .person-meta {
        font-size: 13px;
        color: #6b7280;
    }

    .grave-report-page .status-badge {
        display: inline-block;
        padding: 8px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
    }

    .grave-report-page .empty-state {
        padding: 40px 20px;
        text-align: center;
        color: #6b7280;
    }

    .grave-report-page .percentage-card {
        border: 1px solid #e5edf5;
        border-radius: 16px;
        background: #ffffff;
        height: 100%;
    }

    .grave-report-page .percentage-value {
        font-size: 30px;
        font-weight: 800;
        color: #111827;
    }

    .grave-report-page .progress {
        height: 9px;
        border-radius: 999px;
        background-color: #eef2f7;
    }

    .grave-report-page .progress-bar {
        border-radius: 999px;
    }

    @media (max-width: 576px) {
        .grave-report-page .summary-value {
            font-size: 20px;
        }

        .grave-report-page .stats-icon {
            width: 46px;
            height: 46px;
            font-size: 20px;
        }
    }
</style>

@php
    $statusMap = [
        'pending' => [
            'label' => 'Menunggu Kelulusan',
            'class' => 'warning text-dark',
        ],
        'approved' => [
            'label' => 'Diluluskan',
            'class' => 'success',
        ],
        'cancelled' => [
            'label' => 'Dibatalkan',
            'class' => 'secondary',
        ],
        'rejected' => [
            'label' => 'Ditolak',
            'class' => 'danger',
        ],
    ];

    $monthOptions = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Mac',
        4 => 'April',
        5 => 'Mei',
        6 => 'Jun',
        7 => 'Julai',
        8 => 'Ogos',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Disember',
    ];

    $selectedMonthName = $month ? ($monthOptions[(int) $month] ?? '-') : 'Semua Bulan';
@endphp

<div class="container-fluid grave-report-page">

    {{-- Hero Header --}}
    <div class="card hero-card shadow-sm mb-4">
        <div class="card-body p-4 p-lg-5">
            <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                <div>
                    <p class="mb-2 small hero-subtitle">Panel Pentadbir / Laporan Sistem</p>
                    <h1 class="fw-bold mb-2">Laporan Kepukan</h1>
                    <p class="mb-0 hero-subtitle">
                        Paparan statistik tempahan kepuk dan nisan berdasarkan tahun, bulan, status, jenis tempahan dan lokasi kubur.
                    </p>
                </div>

                <div class="text-lg-end">
                    <div class="report-badge mb-2">
                        <i class="bx bx-bar-chart-alt-2"></i>
                        Statistik Laporan
                    </div>
                    <div class="small text-muted">Tahun Laporan</div>
                    <div class="fs-3 fw-bold text-dark">{{ $year }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tetapan Laporan --}}
    <div class="card filter-card mb-4">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-3">
                <div>
                    <h5 class="mb-1 fw-bold">Tetapan Laporan</h5>
                    <p class="text-muted mb-0">
                        Pilih tahun atau bulan untuk melihat analisis laporan kepukan.
                    </p>
                </div>

                <div class="report-badge">
                    <i class="bx bx-calendar"></i>
                    {{ $selectedMonthName }} {{ $year }}
                </div>
            </div>

            <form method="GET" action="{{ route('admin.reports.grave-orders.index') }}" class="search-box">
                <div class="row g-3 align-items-end">
                    <div class="col-lg-4 col-md-6">
                        <label class="form-label fw-semibold">Tahun</label>
                        <select name="year" class="form-select">
                            @foreach($years as $availableYear)
                                <option value="{{ $availableYear }}" {{ (int) $year === (int) $availableYear ? 'selected' : '' }}>
                                    {{ $availableYear }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <label class="form-label fw-semibold">Bulan</label>
                        <select name="month" class="form-select">
                            <option value="">Semua Bulan</option>
                            @foreach($monthOptions as $monthNo => $monthName)
                                <option value="{{ $monthNo }}" {{ (string) $month === (string) $monthNo ? 'selected' : '' }}>
                                    {{ $monthName }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-4 col-md-12">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-info btn-wave text-white w-100">
                                <i class="bx bx-filter-alt me-1"></i> Papar Laporan
                            </button>

                            <a href="{{ route('admin.reports.grave-orders.index') }}" class="btn btn-outline-info w-100">
                                Reset
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="card stats-card h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="summary-label">Jumlah Permohonan</div>
                        <div class="summary-value">{{ $summary['total'] ?? 0 }}</div>
                    </div>
                    <div class="stats-icon bg-primary-subtle text-primary">
                        <i class="bx bx-file"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="card stats-card h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="summary-label">Menunggu</div>
                        <div class="summary-value">{{ $summary['pending'] ?? 0 }}</div>
                    </div>
                    <div class="stats-icon bg-warning-subtle text-warning">
                        <i class="bx bx-time-five"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="card stats-card h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="summary-label">Diluluskan</div>
                        <div class="summary-value">{{ $summary['approved'] ?? 0 }}</div>
                    </div>
                    <div class="stats-icon bg-success-subtle text-success">
                        <i class="bx bx-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="card stats-card h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="summary-label">Dibatalkan</div>
                        <div class="summary-value">{{ $summary['cancelled'] ?? 0 }}</div>
                    </div>
                    <div class="stats-icon bg-danger-subtle text-danger">
                        <i class="bx bx-x-circle"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="card stats-card h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="summary-label">Tempahan Kepuk</div>
                        <div class="summary-value">{{ $summary['kepuk'] ?? 0 }}</div>
                    </div>
                    <div class="stats-icon bg-info-subtle text-info">
                        <i class="bx bx-building-house"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="card stats-card h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="summary-label">Tempahan Nisan</div>
                        <div class="summary-value">{{ $summary['nisan'] ?? 0 }}</div>
                    </div>
                    <div class="stats-icon bg-secondary-subtle text-secondary">
                        <i class="bx bx-detail"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Analisis Peratus Status --}}
    <div class="card analysis-card mb-4">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-4">
                <div>
                    <h5 class="mb-1 fw-bold">Analisis Peratus Status Tempahan</h5>
                    <p class="text-muted mb-0">
                        Pecahan peratus status tempahan kepukan berdasarkan jumlah permohonan semasa.
                    </p>
                </div>

                <div class="report-badge">
                    <i class="bx bx-calculator"></i>
                    Bukti Pengiraan
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-xl-4 col-md-6">
                    <div class="percentage-card p-4">
                        <div class="summary-label">Menunggu Kelulusan</div>
                        <div class="d-flex justify-content-between align-items-end">
                            <div>
                                <div class="summary-value">{{ $summary['pending'] ?? 0 }}</div>
                                <div class="person-meta">tempahan</div>
                            </div>
                            <div class="percentage-value text-warning">
                                {{ $statusPercentages['pending'] ?? 0 }}%
                            </div>
                        </div>

                        <div class="progress mt-3">
                            <div class="progress-bar bg-warning"
                                 style="width: {{ $statusPercentages['pending'] ?? 0 }}%">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4 col-md-6">
                    <div class="percentage-card p-4">
                        <div class="summary-label">Diluluskan</div>
                        <div class="d-flex justify-content-between align-items-end">
                            <div>
                                <div class="summary-value">{{ $summary['approved'] ?? 0 }}</div>
                                <div class="person-meta">tempahan</div>
                            </div>
                            <div class="percentage-value text-success">
                                {{ $statusPercentages['approved'] ?? 0 }}%
                            </div>
                        </div>

                        <div class="progress mt-3">
                            <div class="progress-bar bg-success"
                                 style="width: {{ $statusPercentages['approved'] ?? 0 }}%">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4 col-md-6">
                    <div class="percentage-card p-4">
                        <div class="summary-label">Dibatalkan / Ditolak</div>
                        <div class="d-flex justify-content-between align-items-end">
                            <div>
                                <div class="summary-value">{{ $summary['cancelled'] ?? 0 }}</div>
                                <div class="person-meta">tempahan</div>
                            </div>
                            <div class="percentage-value text-danger">
                                {{ $statusPercentages['cancelled'] ?? 0 }}%
                            </div>
                        </div>

                        <div class="progress mt-3">
                            <div class="progress-bar bg-danger"
                                 style="width: {{ $statusPercentages['cancelled'] ?? 0 }}%">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="report-note mb-3">
                <strong>Formula:</strong>
                Peratus Status = (Jumlah Status / Jumlah Permohonan) × 100
            </div>

            <div class="table-responsive">
                <table class="table table-report align-middle text-center mb-0">
                    <thead>
                        <tr>
                            <th class="text-start">Status Tempahan</th>
                            <th>Bilangan</th>
                            <th>Formula Pengiraan</th>
                            <th>Peratus</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($calculationProof as $item)
                            <tr>
                                <td class="text-start fw-semibold">{{ $item['status'] }}</td>
                                <td>{{ $item['count'] }}</td>
                                <td>{{ $item['formula'] }}</td>
                                <td class="fw-bold">{{ $item['percentage'] }}%</td>
                            </tr>
                        @endforeach

                        <tr class="total-row">
                            <td class="text-start">Jumlah</td>
                            <td>{{ $summary['total'] ?? 0 }}</td>
                            <td>-</td>
                            <td>{{ ($summary['total'] ?? 0) > 0 ? '100%' : '0%' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Charts --}}
    <div class="row g-3 mb-4">
        <div class="col-xl-8">
            <div class="card chart-card h-100">
                <div class="card-body p-4">
                    <h5 class="mb-1 fw-bold">Graf Permohonan Mengikut Bulan</h5>
                    <p class="text-muted mb-3">
                        Jumlah permohonan kepukan bagi setiap bulan dalam tahun {{ $year }}.
                    </p>
                    <div id="monthlyOrderChart" class="chart-box"></div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card chart-card h-100">
                <div class="card-body p-4">
                    <h5 class="mb-1 fw-bold">Pecahan Status Tempahan</h5>
                    <p class="text-muted mb-3">
                        Pecahan status berdasarkan tetapan laporan semasa.
                    </p>
                    <div id="statusOrderChart" class="chart-box"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Ringkasan Tempahan Mengikut Status --}}
    <div class="card table-card mb-4">
        <div class="card-body p-4">
            <h5 class="mb-1 fw-bold">Ringkasan Tempahan Kepukan Mengikut Status</h5>
            <p class="text-muted mb-3">
                Ringkasan jumlah tempahan berdasarkan jenis tempahan dan status kelulusan.
            </p>

            <div class="table-responsive">
                <table class="table table-report align-middle text-center mb-0">
                    <thead>
                        <tr>
                            <th class="text-start">Jenis Tempahan</th>
                            <th>Menunggu Kelulusan</th>
                            <th>Diluluskan</th>
                            <th>Dibatalkan / Ditolak</th>
                            <th>Jumlah Tempahan</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($orderTypeSummary as $item)
                            <tr>
                                <td class="text-start fw-semibold">{{ $item->type_label }}</td>
                                <td>{{ $item->pending_total }}</td>
                                <td>{{ $item->approved_total }}</td>
                                <td>{{ $item->cancelled_total }}</td>
                                <td class="fw-bold">{{ $item->grand_total }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    <div class="empty-state">
                                        <i class="bx bx-folder-open fs-1 mb-2 d-block"></i>
                                        Tiada rekod tempahan kepukan dijumpai untuk tetapan ini.
                                    </div>
                                </td>
                            </tr>
                        @endforelse

                        @if($orderTypeSummary->count() > 0)
                            <tr class="total-row">
                                <td class="text-start">Jumlah</td>
                                <td>{{ $orderTypeSummary->sum('pending_total') }}</td>
                                <td>{{ $orderTypeSummary->sum('approved_total') }}</td>
                                <td>{{ $orderTypeSummary->sum('cancelled_total') }}</td>
                                <td>{{ $orderTypeSummary->sum('grand_total') }}</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Ringkasan Zon --}}
    <div class="card table-card mb-4">
        <div class="card-body p-4">
            <h5 class="mb-1 fw-bold">Ringkasan Tempahan Mengikut Zon / Lokasi</h5>
            <p class="text-muted mb-3">
                Ringkasan jumlah tempahan kepukan mengikut zon atau lokasi kubur.
            </p>

            <div class="table-responsive">
                <table class="table table-report align-middle text-center mb-0">
                    <thead>
                        <tr>
                            <th class="text-start">Zon / Lokasi</th>
                            <th>Menunggu</th>
                            <th>Diluluskan</th>
                            <th>Dibatalkan / Ditolak</th>
                            <th>Jumlah Tempahan</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($zoneSummary as $zone)
                            <tr>
                                <td class="text-start fw-semibold">{{ $zone->zone_label }}</td>
                                <td>{{ $zone->pending }}</td>
                                <td>{{ $zone->approved }}</td>
                                <td>{{ $zone->cancelled }}</td>
                                <td class="fw-bold">{{ $zone->total }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    <div class="empty-state">
                                        Tiada rekod zon / lokasi untuk tetapan ini.
                                    </div>
                                </td>
                            </tr>
                        @endforelse

                        @if($zoneSummary->count() > 0)
                            <tr class="total-row">
                                <td class="text-start">Jumlah</td>
                                <td>{{ $zoneSummary->sum('pending') }}</td>
                                <td>{{ $zoneSummary->sum('approved') }}</td>
                                <td>{{ $zoneSummary->sum('cancelled') }}</td>
                                <td>{{ $zoneSummary->sum('total') }}</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Ringkasan Bulanan --}}
    <div class="card table-card mb-4">
        <div class="card-body p-4">
            <h5 class="mb-1 fw-bold">Ringkasan Bulanan Tahun {{ $year }}</h5>
            <p class="text-muted mb-3">
                Jumlah permohonan kepukan mengikut bulan dan status.
            </p>

            <div class="table-responsive">
                <table class="table table-report align-middle text-center mb-0">
                    <thead>
                        <tr>
                            <th class="text-start">Bulan</th>
                            <th>Jumlah Permohonan</th>
                            <th>Menunggu</th>
                            <th>Diluluskan</th>
                            <th>Dibatalkan / Ditolak</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($monthlySummary as $monthItem)
                            <tr>
                                <td class="text-start fw-semibold">{{ $monthItem['month_name'] }}</td>
                                <td class="fw-bold">{{ $monthItem['total'] }}</td>
                                <td>{{ $monthItem['pending'] }}</td>
                                <td>{{ $monthItem['approved'] }}</td>
                                <td>{{ $monthItem['cancelled'] }}</td>
                            </tr>
                        @endforeach

                        <tr class="total-row">
                            <td class="text-start">Jumlah</td>
                            <td>{{ $monthlySummary->sum('total') }}</td>
                            <td>{{ $monthlySummary->sum('pending') }}</td>
                            <td>{{ $monthlySummary->sum('approved') }}</td>
                            <td>{{ $monthlySummary->sum('cancelled') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="report-note mt-4">
                <strong>Nota:</strong>
                Jadual ini tidak memaparkan bayaran kerana tempahan kepukan hanya melibatkan permohonan dan pengesahan admin.
            </div>
        </div>
    </div>

    {{-- Detail Table --}}
    <div class="card table-card">
        <div class="card-body p-4">
            <h5 class="mb-1 fw-bold">Senarai Detail Tempahan Kepukan</h5>
            <p class="text-muted mb-3">
                Jumlah rekod laporan:
                <span class="fw-semibold">
                    {{ method_exists($orders, 'total') ? $orders->total() : $orders->count() }}
                </span>
                tempahan
            </p>

            <div class="table-responsive">
                <table class="table table-report align-middle mb-0">
                    <thead>
                        <tr>
                            <th width="6%">#</th>
                            <th>No Tempahan</th>
                            <th>Pemohon / Waris</th>
                            <th>Si Mati</th>
                            <th>Lot / Lokasi</th>
                            <th>Jenis Tempahan</th>
                            <th>Status</th>
                            <th>Tarikh Mohon</th>
                            <th>Tarikh Lulus</th>
                            <th>Catatan Admin</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($orders as $index => $order)
                            @php
                                $statusData = $statusMap[$order->status] ?? [
                                    'label' => ucfirst(str_replace('_', ' ', $order->status ?? '-')),
                                    'class' => 'secondary',
                                ];

                                $pemohonNama = $order->deathReport->nama_pelapor
                                    ?? $order->user->profile->nama
                                    ?? $order->user->name
                                    ?? '-';

                                $pemohonTel = $order->deathReport->no_tel_pelapor
                                    ?? $order->user->profile->no_tel_bimbit
                                    ?? '-';

                                $namaSiMati = $order->deathReport->nama_si_mati ?? '-';
                                $noKpSiMati = $order->deathReport->no_kp_si_mati ?? '-';

                                $initial = strtoupper(substr($namaSiMati !== '-' ? $namaSiMati : $pemohonNama, 0, 1));
                            @endphp

                            <tr>
                                <td class="fw-semibold text-muted">
                                    {{ method_exists($orders, 'firstItem') ? $orders->firstItem() + $index : $index + 1 }}
                                </td>

                                <td>
                                    <div class="fw-bold">
                                        GO-{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}
                                    </div>
                                    <div class="person-meta">
                                        ID Sistem: {{ $order->id }}
                                    </div>
                                </td>

                                <td>
                                    <div class="fw-semibold">{{ $pemohonNama }}</div>
                                    <div class="person-meta">Tel: {{ $pemohonTel }}</div>
                                </td>

                                <td>
                                    <div class="d-flex align-items-start gap-3">
                                        <div class="person-avatar">{{ $initial }}</div>
                                        <div>
                                            <div class="person-name">{{ $namaSiMati }}</div>
                                            <div class="person-meta">No. KP: {{ $noKpSiMati }}</div>

                                            @if($order->deathReport && $order->deathReport->tarikh_meninggal)
                                                <div class="person-meta">
                                                    Tarikh meninggal:
                                                    {{ optional($order->deathReport->tarikh_meninggal)->format('d/m/Y') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    @if($order->burialPlot)
                                        <div class="fw-semibold">
                                            {{ $order->burialPlot->plot_code ?? '-' }}
                                        </div>

                                        <div class="person-meta">
                                            {{
                                                $order->burialPlot->zone_label
                                                ?? $order->burialPlot->zone
                                                ?? $order->burialPlot->section
                                                ?? $order->burialPlot->blok
                                                ?? '-'
                                            }}
                                        </div>

                                        <div class="person-meta">
                                            Baris {{ $order->burialPlot->row_number ?? '-' }},
                                            Lot {{ $order->burialPlot->lot_number ?? '-' }}
                                        </div>
                                    @else
                                        <span class="text-muted">Tidak ditetapkan</span>
                                    @endif
                                </td>

                                <td>
                                    <div class="fw-semibold">{{ $order->order_label ?? '-' }}</div>
                                    <div class="person-meta">Kategori: {{ $order->category ?? '-' }}</div>
                                    <div class="person-meta">Kod: {{ $order->order_type ?? '-' }}</div>
                                </td>

                                <td>
                                    <span class="badge bg-{{ $statusData['class'] }} status-badge">
                                        {{ $statusData['label'] }}
                                    </span>
                                </td>

                                <td>
                                    <div class="fw-semibold">
                                        {{ optional($order->created_at)->format('d/m/Y') ?? '-' }}
                                    </div>
                                    <div class="person-meta">
                                        {{ optional($order->created_at)->format('h:i A') ?? '-' }}
                                    </div>
                                </td>

                                <td>
                                    @if($order->approved_at)
                                        <div class="fw-semibold">
                                            {{ optional($order->approved_at)->format('d/m/Y') }}
                                        </div>
                                        <div class="person-meta">
                                            {{ optional($order->approved_at)->format('h:i A') }}
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>

                                <td>
                                    <div class="person-meta">
                                        {{ $order->admin_note ?? '-' }}
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10">
                                    <div class="empty-state">
                                        <i class="bx bx-folder-open fs-1 mb-2 d-block"></i>
                                        Tiada rekod tempahan kepukan dijumpai untuk tetapan ini.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($orders, 'hasPages') && $orders->hasPages())
                <div class="mt-4">
                    {{ $orders->links() }}
                </div>
            @endif

            <div class="report-note mt-4">
                <strong>Nota audit:</strong>
                Laporan ini memaparkan maklumat permohonan kepukan, waris, si mati, lot kubur, jenis tempahan, status dan tarikh pengesahan admin.
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const monthlyLabels = @json($chartLabels);
    const monthlyTotals = @json($chartTotals);
    const statusLabels = @json($statusChartLabels);
    const statusTotals = @json($statusChartTotals);

    if (typeof ApexCharts === 'undefined') {
        console.warn('ApexCharts tidak dijumpai. Pastikan library ApexCharts dimuatkan dalam layout.');
        return;
    }

    const monthlyChartElement = document.querySelector("#monthlyOrderChart");
    if (monthlyChartElement) {
        const monthlyOptions = {
            chart: {
                type: 'bar',
                height: 320,
                toolbar: {
                    show: false
                }
            },
            series: [{
                name: 'Jumlah Permohonan',
                data: monthlyTotals
            }],
            xaxis: {
                categories: monthlyLabels
            },
            dataLabels: {
                enabled: false
            },
            plotOptions: {
                bar: {
                    borderRadius: 6,
                    columnWidth: '45%'
                }
            },
            noData: {
                text: 'Tiada data'
            }
        };

        const monthlyChart = new ApexCharts(monthlyChartElement, monthlyOptions);
        monthlyChart.render();
    }

    const statusChartElement = document.querySelector("#statusOrderChart");
    if (statusChartElement) {
        const statusOptions = {
            chart: {
                type: 'donut',
                height: 320
            },
            labels: statusLabels,
            series: statusTotals,
            legend: {
                position: 'bottom'
            },
            dataLabels: {
                enabled: true
            },
            noData: {
                text: 'Tiada data'
            }
        };

        const statusChart = new ApexCharts(statusChartElement, statusOptions);
        statusChart.render();
    }
});
</script>
@endsection