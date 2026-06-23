@extends('layouts.app')

@section('content')
<style>
    .member-report-page .hero-card {
        border: 0;
        border-radius: 20px;
        background: linear-gradient(135deg, #a8e1d7, #c9efe8);
        color: #1f2937;
        overflow: hidden;
        position: relative;
    }

    .member-report-page .hero-card::after {
        content: "";
        position: absolute;
        top: -35px;
        right: -35px;
        width: 170px;
        height: 170px;
        background: rgba(255,255,255,0.20);
        border-radius: 50%;
    }

    .member-report-page .stats-card,
    .member-report-page .filter-card,
    .member-report-page .chart-card,
    .member-report-page .table-card {
        border: 0;
        border-radius: 18px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.06);
    }

    .member-report-page .stats-card {
        transition: 0.25s ease;
    }

    .member-report-page .stats-card:hover {
        transform: translateY(-4px);
    }

    .member-report-page .stats-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        flex-shrink: 0;
    }

    .member-report-page .summary-label {
        font-size: 13px;
        color: #6b7280;
        margin-bottom: 6px;
    }

    .member-report-page .summary-value {
        font-size: 24px;
        font-weight: 800;
        color: #111827;
        line-height: 1;
    }

    .member-report-page .summary-desc {
        font-size: 12px;
        color: #98a2b3;
        margin-top: 6px;
    }

    .member-report-page .hero-subtitle {
        color: #5f6f82;
    }

    .member-report-page .form-control,
    .member-report-page .form-select,
    .member-report-page .btn {
        border-radius: 12px;
        min-height: 46px;
    }

    .member-report-page .table thead th {
        background: #f8fafc;
        border-bottom: 1px solid #e9ecef;
        font-weight: 700;
        color: #344054;
        white-space: nowrap;
    }

    .member-report-page .table tbody td {
        vertical-align: middle;
        color: #334155;
    }

    .member-report-page .table tbody tr:hover {
        background-color: #f8fbff;
    }

    .member-report-page .member-avatar {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        color: #2f6f63;
        background: linear-gradient(135deg, #d8f4ee, #bfe9e0);
        flex-shrink: 0;
    }

    .member-report-page .member-name {
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 2px;
    }

    .member-report-page .member-meta {
        font-size: 13px;
        color: #6b7280;
    }

    .member-report-page .custom-badge {
        display: inline-block;
        padding: 8px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
    }

    .member-report-page .chart-box {
        min-height: 320px;
    }

    .member-report-page .empty-state {
        padding: 40px 20px;
        text-align: center;
        color: #6b7280;
    }

    .member-report-page .report-note {
        background: #f8fafc;
        border: 1px solid #e9ecef;
        border-radius: 14px;
        padding: 14px 16px;
        color: #667085;
        font-size: 13px;
    }

    .member-report-page .pagination {
        margin-bottom: 0;
    }

    .member-report-page .pagination svg {
        width: 14px !important;
        height: 14px !important;
    }

    .member-report-page .pagination .page-link {
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 38px;
        height: 38px;
        border-radius: 10px;
        padding: 0 12px;
    }

    .member-report-page nav svg,
    .member-report-page .pagination svg {
        width: 14px !important;
        height: 14px !important;
        max-width: 14px !important;
        max-height: 14px !important;
    }
</style>

@php
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

    $selectedRecordTypeName = match ($recordType ?? '') {
        'utama' => 'Ahli Utama',
        'tanggungan' => 'Tanggungan',
        default => 'Semua Rekod',
    };

    $selectedStatusName = match ($statusKehidupan ?? '') {
        'aktif' => 'Masih Hidup',
        'meninggal' => 'Meninggal Dunia',
        default => 'Semua Status Kehidupan',
    };

    $selectedGenderName = match ($gender ?? '') {
        'lelaki' => 'Lelaki',
        'perempuan' => 'Perempuan',
        default => 'Semua Jantina',
    };
@endphp

<div class="container-fluid member-report-page">

    {{-- Header --}}
    <div class="card hero-card shadow-sm mb-4">
        <div class="card-body p-4 p-lg-5">
            <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                <div>
                    <p class="mb-2 small hero-subtitle">Panel Pentadbir / Laporan Sistem</p>
                    <h1 class="fw-bold mb-2">Laporan Ahli & Tanggungan</h1>
                    <p class="mb-0 hero-subtitle">
                        Analisis jumlah ahli utama, tanggungan, status kehidupan, jantina, umur dan pendaftaran bulanan.
                    </p>
                </div>

                <div class="text-lg-end">
                    <div class="small hero-subtitle">Tahun Laporan</div>
                    <div class="fs-3 fw-bold text-dark">{{ $year }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Summary --}}
    <div class="row g-3 mb-4">
        <div class="col-xl-2 col-md-4">
            <div class="card stats-card h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="summary-label">Ahli Utama</div>
                        <div class="summary-value">{{ $summary['total_main_members'] ?? 0 }}</div>
                    </div>
                    <div class="stats-icon bg-primary-subtle text-primary">
                        <i class="bx bx-user"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4">
            <div class="card stats-card h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="summary-label">Tanggungan</div>
                        <div class="summary-value">{{ $summary['total_dependents'] ?? 0 }}</div>
                    </div>
                    <div class="stats-icon bg-info-subtle text-info">
                        <i class="bx bx-group"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4">
            <div class="card stats-card h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="summary-label">Keseluruhan</div>
                        <div class="summary-value">{{ $summary['total_all_members'] ?? 0 }}</div>
                    </div>
                    <div class="stats-icon bg-secondary-subtle text-secondary">
                        <i class="bx bx-list-ul"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4">
            <div class="card stats-card h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="summary-label">Masih Hidup</div>
                        <div class="summary-value">{{ $summary['alive'] ?? 0 }}</div>
                    </div>
                    <div class="stats-icon bg-success-subtle text-success">
                        <i class="bx bx-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4">
            <div class="card stats-card h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="summary-label">Meninggal</div>
                        <div class="summary-value">{{ $summary['deceased'] ?? 0 }}</div>
                    </div>
                    <div class="stats-icon bg-danger-subtle text-danger">
                        <i class="bx bx-user-x"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4">
            <div class="card stats-card h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="summary-label">Baru Tahun Ini</div>
                        <div class="summary-value">{{ $summary['new_this_year'] ?? 0 }}</div>
                    </div>
                    <div class="stats-icon bg-warning-subtle text-warning">
                        <i class="bx bx-calendar-plus"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter --}}
    <div class="card filter-card mb-4">
        <div class="card-body p-4">
            <div class="mb-3">
                <h5 class="mb-1 fw-bold">Jana Laporan Ahli</h5>
                <p class="text-muted mb-0">
                    Tapis laporan mengikut tahun, bulan, jenis rekod, status kehidupan dan jantina.
                </p>
            </div>

            <form method="GET" action="{{ route('admin.reports.members.index') }}">
                <div class="row g-3 align-items-end">

                    <div class="col-xl-2 col-md-4">
                        <label class="form-label fw-semibold">Tahun</label>
                        <select name="year" class="form-select">
                            @foreach($years as $availableYear)
                                <option value="{{ $availableYear }}" {{ (int) $year === (int) $availableYear ? 'selected' : '' }}>
                                    {{ $availableYear }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-xl-2 col-md-4">
                        <label class="form-label fw-semibold">Bulan Daftar</label>
                        <select name="month" class="form-select">
                            <option value="">Semua Bulan</option>
                            @foreach($monthOptions as $monthNo => $monthName)
                                <option value="{{ $monthNo }}" {{ (string) $month === (string) $monthNo ? 'selected' : '' }}>
                                    {{ $monthName }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-xl-2 col-md-4">
                        <label class="form-label fw-semibold">Jenis Rekod</label>
                        <select name="record_type" class="form-select">
                            <option value="">Semua</option>
                            <option value="utama" {{ ($recordType ?? '') === 'utama' ? 'selected' : '' }}>
                                Ahli Utama
                            </option>
                            <option value="tanggungan" {{ ($recordType ?? '') === 'tanggungan' ? 'selected' : '' }}>
                                Tanggungan
                            </option>
                        </select>
                    </div>

                    <div class="col-xl-2 col-md-4">
                        <label class="form-label fw-semibold">Status Kehidupan</label>
                        <select name="status_kehidupan" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="aktif" {{ ($statusKehidupan ?? '') === 'aktif' ? 'selected' : '' }}>
                                Masih Hidup
                            </option>
                            <option value="meninggal" {{ ($statusKehidupan ?? '') === 'meninggal' ? 'selected' : '' }}>
                                Meninggal Dunia
                            </option>
                        </select>
                    </div>

                    <div class="col-xl-2 col-md-4">
                        <label class="form-label fw-semibold">Jantina</label>
                        <select name="gender" class="form-select">
                            <option value="">Semua</option>
                            <option value="lelaki" {{ ($gender ?? '') === 'lelaki' ? 'selected' : '' }}>
                                Lelaki
                            </option>
                            <option value="perempuan" {{ ($gender ?? '') === 'perempuan' ? 'selected' : '' }}>
                                Perempuan
                            </option>
                        </select>
                    </div>

                    <div class="col-xl-2 col-md-4">
                        <label class="form-label fw-semibold">Kata Kunci</label>
                        <input type="text"
                               name="search"
                               value="{{ $search }}"
                               class="form-control"
                               placeholder="Nama / No KP / Telefon">
                    </div>

                    <div class="col-12">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.reports.members.index') }}" class="btn btn-outline-info px-4">
                                Reset
                            </a>

                            <button type="submit" class="btn btn-info text-white btn-wave px-4">
                                <i class="bx bx-search me-1"></i> Jana Laporan
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            <div class="report-note mt-4">
                <strong>Tapisan semasa:</strong>
                Tahun {{ $year }},
                {{ $selectedMonthName }},
                {{ $selectedRecordTypeName }},
                {{ $selectedStatusName }},
                {{ $selectedGenderName }}.
            </div>
        </div>
    </div>

    {{-- Charts --}}
    <div class="row g-3 mb-4">
        <div class="col-xl-7">
            <div class="card chart-card h-100">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-1">Graf Pendaftaran Ahli Mengikut Bulan</h5>
                    <p class="text-muted mb-3">
                        Jumlah pendaftaran ahli utama dan tanggungan bagi tahun {{ $year }}.
                    </p>
                    <div id="monthlyRegistrationChart" class="chart-box"></div>
                </div>
            </div>
        </div>

        <div class="col-xl-5">
            <div class="card chart-card h-100">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-1">Pecahan Jenis Rekod</h5>
                    <p class="text-muted mb-3">
                        Perbandingan ahli utama dan tanggungan.
                    </p>
                    <div id="memberTypeChart" class="chart-box"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-xl-4">
            <div class="card chart-card h-100">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-1">Status Kehidupan</h5>
                    <p class="text-muted mb-3">Masih hidup dan meninggal dunia.</p>
                    <div id="lifeStatusChart" class="chart-box"></div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card chart-card h-100">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-1">Pecahan Jantina</h5>
                    <p class="text-muted mb-3">Lelaki dan perempuan.</p>
                    <div id="genderChart" class="chart-box"></div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card table-card h-100">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-1">Kumpulan Umur</h5>
                    <p class="text-muted mb-3">Anggaran umur berdasarkan nombor MyKad.</p>

                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Kumpulan Umur</th>
                                    <th class="text-end">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ageGroups as $ageLabel => $ageTotal)
                                    <tr>
                                        <td class="fw-semibold">{{ $ageLabel }}</td>
                                        <td class="text-end">{{ $ageTotal }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- Ringkasan Bulanan --}}
    <div class="card table-card mb-4">
        <div class="card-body p-0">
            <div class="px-4 pt-4 pb-2">
                <h5 class="fw-bold mb-1">Ringkasan Pendaftaran Bulanan Tahun {{ $year }}</h5>
                <p class="text-muted mb-0">
                    Pecahan pendaftaran ahli utama dan tanggungan mengikut bulan.
                </p>
            </div>

            <div class="table-responsive px-3 pb-3">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Bulan</th>
                            <th class="text-center">Jumlah</th>
                            <th class="text-center">Ahli Utama</th>
                            <th class="text-center">Tanggungan</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($monthlySummary as $monthItem)
                            <tr>
                                <td class="fw-bold">{{ $monthItem['month_name'] }}</td>
                                <td class="text-center">{{ $monthItem['total'] }}</td>
                                <td class="text-center">{{ $monthItem['main'] }}</td>
                                <td class="text-center">{{ $monthItem['dependent'] }}</td>
                            </tr>
                        @endforeach

                        <tr>
                            <td class="fw-bold bg-light">TOTAL</td>
                            <td class="text-center fw-bold bg-light">{{ $monthlySummary->sum('total') }}</td>
                            <td class="text-center fw-bold bg-light">{{ $monthlySummary->sum('main') }}</td>
                            <td class="text-center fw-bold bg-light">{{ $monthlySummary->sum('dependent') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Jadual Detail --}}
    <div class="card table-card">
        <div class="card-body p-0">
            <div class="px-4 pt-4 pb-2">
                <h5 class="fw-bold mb-1">Senarai Detail Ahli & Tanggungan</h5>
                <p class="text-muted mb-0">
                    Jumlah rekod dipaparkan:
                    <span class="fw-semibold">{{ $memberRecords->total() }}</span>
                </p>
            </div>

            <div class="table-responsive px-3 pb-3">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th width="6%">#</th>
                            <th>Maklumat</th>
                            <th>Jenis Rekod</th>
                            <th>Jantina</th>
                            <th>Umur</th>
                            <th>Status Kehidupan</th>
                            <th>Pelan Bayaran</th>
                            <th>Tarikh Daftar</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($memberRecords as $index => $record)
                            @php
                                $initial = strtoupper(substr($record['name'] ?? 'A', 0, 1));

                                $genderLabel = match ($record['gender'] ?? '') {
                                    'lelaki' => 'Lelaki',
                                    'perempuan' => 'Perempuan',
                                    default => 'Tidak Diketahui',
                                };

                                $genderClass = match ($record['gender'] ?? '') {
                                    'lelaki' => 'bg-primary-subtle text-primary border',
                                    'perempuan' => 'bg-danger-subtle text-danger border',
                                    default => 'bg-secondary-subtle text-secondary border',
                                };

                                $statusClass = ($record['life_status'] ?? '') === 'meninggal'
                                    ? 'bg-danger-subtle text-danger border'
                                    : 'bg-success-subtle text-success border';
                            @endphp

                            <tr>
                                <td class="fw-semibold text-muted">
                                    {{ $memberRecords->firstItem() + $index }}
                                </td>

                                <td>
                                    <div class="d-flex align-items-start gap-3">
                                        <div class="member-avatar">{{ $initial }}</div>

                                        <div>
                                            <div class="member-name">{{ $record['name'] ?? '-' }}</div>
                                            <div class="member-meta">No. KP: {{ $record['no_kp'] ?? '-' }}</div>
                                            <div class="member-meta">No. Tel: {{ $record['phone'] ?? '-' }}</div>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <span class="custom-badge bg-info-subtle text-info border">
                                        {{ $record['type_label'] ?? '-' }}
                                    </span>
                                    <div class="member-meta mt-1">
                                        {{ $record['relation'] ?? '-' }}
                                    </div>
                                </td>

                                <td>
                                    <span class="custom-badge {{ $genderClass }}">
                                        {{ $genderLabel }}
                                    </span>
                                </td>

                                <td>
                                    {{ $record['age'] !== null ? $record['age'] . ' tahun' : '-' }}
                                </td>

                                <td>
                                    <span class="custom-badge {{ $statusClass }}">
                                        {{ $record['life_status_label'] ?? '-' }}
                                    </span>
                                </td>

                                <td>
                                    @if(!empty($record['payment_plan']) && $record['payment_plan'] !== '-')
                                        <span class="custom-badge bg-light text-dark border">
                                            {{ ucfirst($record['payment_plan']) }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>

                                <td>
                                    {{ !empty($record['registered_at']) ? \Carbon\Carbon::parse($record['registered_at'])->format('d/m/Y') : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">
                                    <div class="empty-state">
                                        <i class="bx bx-folder-open fs-1 mb-2 d-block"></i>
                                        Tiada rekod dijumpai untuk tapisan ini.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($memberRecords->hasPages())
                <div class="card-footer bg-white border-0 pt-0 pb-4 px-4">
                    {{ $memberRecords->links() }}
                </div>
            @endif
        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof ApexCharts === 'undefined') {
        console.warn('ApexCharts tidak dijumpai. Pastikan ApexCharts dimuatkan dalam layout.');
        return;
    }

    const monthlyLabels = @json($monthlySummary->pluck('month_name')->values());
    const monthlyMain = @json($monthlySummary->pluck('main')->values());
    const monthlyDependent = @json($monthlySummary->pluck('dependent')->values());

    const monthlyElement = document.querySelector('#monthlyRegistrationChart');
    if (monthlyElement) {
        const monthlyChart = new ApexCharts(monthlyElement, {
            chart: {
                type: 'bar',
                height: 320,
                toolbar: {
                    show: false
                }
            },
            series: [
                {
                    name: 'Ahli Utama',
                    data: monthlyMain
                },
                {
                    name: 'Tanggungan',
                    data: monthlyDependent
                }
            ],
            xaxis: {
                categories: monthlyLabels
            },
            plotOptions: {
                bar: {
                    borderRadius: 6,
                    columnWidth: '45%'
                }
            },
            dataLabels: {
                enabled: false
            },
            legend: {
                position: 'bottom'
            },
            noData: {
                text: 'Tiada data'
            }
        });

        monthlyChart.render();
    }

    const memberTypeElement = document.querySelector('#memberTypeChart');
    if (memberTypeElement) {
        const memberTypeChart = new ApexCharts(memberTypeElement, {
            chart: {
                type: 'donut',
                height: 320
            },
            labels: ['Ahli Utama', 'Tanggungan'],
            series: [
                {{ $summary['total_main_members'] ?? 0 }},
                {{ $summary['total_dependents'] ?? 0 }}
            ],
            legend: {
                position: 'bottom'
            },
            noData: {
                text: 'Tiada data'
            }
        });

        memberTypeChart.render();
    }

    const lifeStatusElement = document.querySelector('#lifeStatusChart');
    if (lifeStatusElement) {
        const lifeStatusChart = new ApexCharts(lifeStatusElement, {
            chart: {
                type: 'donut',
                height: 300
            },
            labels: ['Masih Hidup', 'Meninggal Dunia'],
            series: [
                {{ $summary['alive'] ?? 0 }},
                {{ $summary['deceased'] ?? 0 }}
            ],
            legend: {
                position: 'bottom'
            },
            noData: {
                text: 'Tiada data'
            }
        });

        lifeStatusChart.render();
    }

    const genderElement = document.querySelector('#genderChart');
    if (genderElement) {
        const genderChart = new ApexCharts(genderElement, {
            chart: {
                type: 'donut',
                height: 300
            },
            labels: ['Lelaki', 'Perempuan'],
            series: [
                {{ $summary['male'] ?? 0 }},
                {{ $summary['female'] ?? 0 }}
            ],
            legend: {
                position: 'bottom'
            },
            noData: {
                text: 'Tiada data'
            }
        });

        genderChart.render();
    }
});
</script>
@endsection