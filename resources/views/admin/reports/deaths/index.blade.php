@extends('layouts.app')

@section('content')
<style>
    .death-report-page .hero-card {
        border: 0;
        border-radius: 20px;
        background: linear-gradient(135deg, #e8f1f8, #f6f9fc);
        color: #1f2937;
        overflow: hidden;
        position: relative;
    }

    .death-report-page .hero-card::after {
        content: "";
        position: absolute;
        top: -45px;
        right: -45px;
        width: 180px;
        height: 180px;
        background: rgba(255,255,255,0.35);
        border-radius: 50%;
    }

    .death-report-page .stats-card,
    .death-report-page .filter-card,
    .death-report-page .table-card,
    .death-report-page .chart-card {
        border: 0;
        border-radius: 18px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.06);
    }

    .death-report-page .stats-card {
        transition: 0.25s ease;
    }

    .death-report-page .stats-card:hover {
        transform: translateY(-4px);
    }

    .death-report-page .stats-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 23px;
        flex-shrink: 0;
    }

    .death-report-page .summary-label {
        font-size: 13px;
        color: #6b7280;
        margin-bottom: 6px;
    }

    .death-report-page .summary-value {
        font-size: 25px;
        font-weight: 800;
        color: #111827;
        line-height: 1;
    }

    .death-report-page .summary-desc {
        font-size: 12px;
        color: #98a2b3;
        margin-top: 6px;
    }

    .death-report-page .form-control,
    .death-report-page .form-select,
    .death-report-page .btn {
        border-radius: 12px;
        min-height: 46px;
    }

    .death-report-page .table thead th {
        background: #f8fafc;
        border-bottom: 1px solid #e9ecef;
        font-weight: 700;
        color: #344054;
        white-space: nowrap;
    }

    .death-report-page .table tbody tr:hover {
        background-color: #f7fbfe;
    }

    .death-report-page .person-avatar {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        color: #475467;
        background: linear-gradient(135deg, #edf3f8, #d7e4ef);
        flex-shrink: 0;
    }

    .death-report-page .person-name {
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 2px;
    }

    .death-report-page .person-meta {
        font-size: 13px;
        color: #6b7280;
    }

    .death-report-page .status-badge {
        display: inline-block;
        padding: 8px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
        white-space: nowrap;
    }

    .death-report-page .report-badge {
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

    .death-report-page .report-note {
        background: #f8fafc;
        border-radius: 14px;
        padding: 14px 16px;
        color: #667085;
        font-size: 13px;
    }

    .death-report-page .empty-state {
        padding: 40px 20px;
        text-align: center;
        color: #6b7280;
    }

    .death-report-page .chart-box {
        min-height: 320px;
    }

    .death-report-page .total-row td {
        background: #f8fafc;
        font-weight: 800;
    }

    /* Jadual Ringkasan Ahli Mengikut Status - border lebih jelas */
    .death-report-page .summary-status-table {
        border: 1.5px solid #cbd5e1 !important;
        border-collapse: collapse !important;
    }

    .death-report-page .summary-status-table th,
    .death-report-page .summary-status-table td {
        border: 1.3px solid #cbd5e1 !important;
        padding: 16px 14px;
    }

    .death-report-page .summary-status-table thead th {
        background: #f1f5f9 !important;
        color: #172554;
        font-weight: 800;
    }

    .death-report-page .summary-status-table tbody td {
        background: #ffffff;
        color: #111827;
    }

    .death-report-page .summary-status-table tbody tr:hover td {
        background: #eef6ff !important;
    }

    .death-report-page .summary-status-table .total-row td {
        background: #f1f5f9 !important;
        font-weight: 800;
        border-top: 2px solid #94a3b8 !important;
    }

    @media (max-width: 576px) {
        .death-report-page .summary-value {
            font-size: 20px;
        }

        .death-report-page .stats-icon {
            width: 46px;
            height: 46px;
            font-size: 20px;
        }
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

    $selectedMemberTypeName = match ($memberType ?? '') {
        'utama' => 'Ahli Utama',
        'tanggungan' => 'Tanggungan',
        default => 'Semua Jenis Ahli',
    };

    $selectedGenderName = match ($gender ?? '') {
        'lelaki' => 'Lelaki',
        'perempuan' => 'Perempuan',
        default => 'Semua Jantina',
    };

    $statusMap = [
        'pending' => [
            'label' => 'Menunggu Semakan',
            'class' => 'warning text-dark',
        ],
        'approved' => [
            'label' => 'Disahkan',
            'class' => 'success',
        ],
        'rejected' => [
            'label' => 'Ditolak',
            'class' => 'danger',
        ],
    ];
@endphp

<div class="container-fluid death-report-page">

    {{-- Header --}}
    <div class="card hero-card shadow-sm mb-4">
        <div class="card-body p-4 p-lg-5">
            <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                <div>
                    <p class="mb-2 small text-muted">Panel Pentadbir / Laporan Sistem</p>
                    <h1 class="fw-bold mb-2">Laporan Kematian Ahli</h1>
                    <p class="mb-0 text-muted">
                        Paparan statistik ahli yang masih hidup, sudah meninggal dunia dan rekod detail kematian.
                    </p>
                </div>

                <div class="text-lg-end">
                    <div class="report-badge mb-2">
                        <i class="bx bx-bar-chart-alt-2"></i>
                        Death Report
                    </div>
                    <div class="small text-muted">Tahun Laporan</div>
                    <div class="fs-3 fw-bold text-dark">{{ $year }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Summary Utama --}}
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card stats-card h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="summary-label">Jumlah Ahli Berdaftar</div>
                        <div class="summary-value">{{ $summary['total_members'] ?? 0 }}</div>
                        <div class="summary-desc">Ahli utama + tanggungan</div>
                    </div>
                    <div class="stats-icon bg-primary-subtle text-primary">
                        <i class="bx bx-group"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stats-card h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="summary-label">Masih Hidup</div>
                        <div class="summary-value">{{ $summary['alive'] ?? 0 }}</div>
                        <div class="summary-desc">Rekod aktif dalam sistem</div>
                    </div>
                    <div class="stats-icon bg-success-subtle text-success">
                        <i class="bx bx-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stats-card h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="summary-label">Meninggal Dunia</div>
                        <div class="summary-value">{{ $summary['deceased'] ?? 0 }}</div>
                        <div class="summary-desc">Jumlah keseluruhan meninggal</div>
                    </div>
                    <div class="stats-icon bg-dark-subtle text-dark">
                        <i class="bx bx-user-x"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stats-card h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="summary-label">Kematian Tahun Ini</div>
                        <div class="summary-value">{{ $summary['death_this_year'] ?? 0 }}</div>
                        <div class="summary-desc">Berdasarkan tahun dipilih</div>
                    </div>
                    <div class="stats-icon bg-info-subtle text-info">
                        <i class="bx bx-calendar"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Summary Detail --}}
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card stats-card h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="summary-label">Ahli Utama Meninggal</div>
                        <div class="summary-value">{{ $summary['main_deceased'] ?? 0 }}</div>
                    </div>
                    <div class="stats-icon bg-secondary-subtle text-secondary">
                        <i class="bx bx-user"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stats-card h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="summary-label">Tanggungan Meninggal</div>
                        <div class="summary-value">{{ $summary['dependent_deceased'] ?? 0 }}</div>
                    </div>
                    <div class="stats-icon bg-secondary-subtle text-secondary">
                        <i class="bx bx-user-plus"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stats-card h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="summary-label">Lelaki Meninggal</div>
                        <div class="summary-value">{{ $summary['male_deceased'] ?? 0 }}</div>
                    </div>
                    <div class="stats-icon bg-primary-subtle text-primary">
                        <i class="bx bx-male"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stats-card h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="summary-label">Perempuan Meninggal</div>
                        <div class="summary-value">{{ $summary['female_deceased'] ?? 0 }}</div>
                    </div>
                    <div class="stats-icon bg-danger-subtle text-danger">
                        <i class="bx bx-female"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Ringkasan Ahli Mengikut Status --}}
    <div class="card table-card mb-4">
        <div class="card-body p-0">
            <div class="px-4 pt-4 pb-2">
                <h5 class="mb-1 fw-bold">Ringkasan Ahli Mengikut Status</h5>
                <p class="text-muted mb-0">
                    Ringkasan jumlah ahli utama dan tanggungan mengikut status kehidupan serta jantina.
                </p>
            </div>

            <div class="table-responsive px-3 pb-3">
                <table class="table table-bordered align-middle text-center mb-0 summary-status-table">
                    <thead>
                        <tr>
                            <th rowspan="2" class="align-middle text-start">Jenis Ahli</th>
                            <th colspan="3">Masih Hidup</th>
                            <th colspan="3">Meninggal Dunia</th>
                            <th rowspan="2" class="align-middle">Jumlah Ahli</th>
                        </tr>
                        <tr>
                            <th>Lelaki</th>
                            <th>Perempuan</th>
                            <th>Jumlah</th>

                            <th>Lelaki</th>
                            <th>Perempuan</th>
                            <th>Jumlah</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($summaryTable ?? [] as $row)
                            <tr class="{{ ($row['jenis'] ?? '') === 'Jumlah' ? 'total-row' : '' }}">
                                <td class="text-start fw-semibold">
                                    {{ $row['jenis'] ?? '-' }}
                                </td>

                                <td>{{ $row['hidup_lelaki'] ?? 0 }}</td>
                                <td>{{ $row['hidup_perempuan'] ?? 0 }}</td>
                                <td class="fw-semibold">{{ $row['jumlah_hidup'] ?? 0 }}</td>

                                <td>{{ $row['meninggal_lelaki'] ?? 0 }}</td>
                                <td>{{ $row['meninggal_perempuan'] ?? 0 }}</td>
                                <td class="fw-semibold">{{ $row['jumlah_meninggal'] ?? 0 }}</td>

                                <td class="fw-bold">{{ $row['jumlah_ahli'] ?? 0 }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">
                                    <div class="empty-state">
                                        <i class="bx bx-folder-open fs-1 mb-2 d-block"></i>
                                        Tiada data ringkasan ahli dijumpai.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-4 pb-4">
                <div class="report-note">
                    <strong>Nota:</strong>
                    Jadual ini memaparkan jumlah ahli utama dan tanggungan yang masih hidup atau telah meninggal dunia
                    berdasarkan rekod status kehidupan dalam sistem.
                </div>
            </div>
        </div>
    </div>

   


    {{-- Ringkasan Bulanan --}}
    <div class="card table-card mb-4">
        <div class="card-body p-0">
            <div class="px-4 pt-4 pb-2">
                <h5 class="mb-1 fw-bold">Ringkasan Bulanan Tahun {{ $year }}</h5>
                <p class="text-muted mb-0">
                    Ringkasan jumlah kematian mengikut bulan, jenis ahli dan jantina.
                </p>
            </div>

            <div class="table-responsive px-3 pb-3">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Bulan</th>
                            <th class="text-center">Jumlah Kematian</th>
                            <th class="text-center">Ahli Utama</th>
                            <th class="text-center">Tanggungan</th>
                            <th class="text-center">Lelaki</th>
                            <th class="text-center">Perempuan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($monthlySummary as $monthItem)
                            <tr>
                                <td class="fw-bold">{{ $monthItem['month_name'] }}</td>
                                <td class="text-center">{{ $monthItem['total'] }}</td>
                                <td class="text-center">{{ $monthItem['main'] }}</td>
                                <td class="text-center">{{ $monthItem['dependent'] }}</td>
                                <td class="text-center">{{ $monthItem['male'] }}</td>
                                <td class="text-center">{{ $monthItem['female'] }}</td>
                            </tr>
                        @endforeach

                        <tr class="total-row">
                            <td>TOTAL</td>
                            <td class="text-center">{{ $monthlySummary->sum('total') }}</td>
                            <td class="text-center">{{ $monthlySummary->sum('main') }}</td>
                            <td class="text-center">{{ $monthlySummary->sum('dependent') }}</td>
                            <td class="text-center">{{ $monthlySummary->sum('male') }}</td>
                            <td class="text-center">{{ $monthlySummary->sum('female') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Detail Table --}}
    <div class="card table-card">
        <div class="card-body p-0">
            <div class="px-4 pt-4 pb-2">
                <h5 class="mb-1 fw-bold">Senarai Detail Kematian</h5>
                <p class="text-muted mb-0">
                    Jumlah rekod dipaparkan:
                    <span class="fw-semibold">{{ $deathReports->count() }}</span>
                </p>
            </div>

            <div class="table-responsive px-3 pb-3">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th>Maklumat Si Mati</th>
                            <th>Jenis Ahli</th>
                            <th>Tarikh / Umur</th>
                            <th>Pelapor</th>
                            <th>Lokasi Kubur</th>
                            <th>Status Semakan</th>
                            <th class="text-end">Tindakan</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($deathReports as $index => $report)
                            @php
                                $statusData = $statusMap[$report->status ?? 'pending'] ?? [
                                    'label' => ucfirst($report->status ?? 'Menunggu'),
                                    'class' => 'secondary',
                                ];

                                $memberTypeLabel = $report->deceased_type === 'dependent'
                                    ? 'Tanggungan'
                                    : 'Ahli Utama';

                                $initial = strtoupper(substr($report->nama_si_mati ?? 'S', 0, 1));

                                $plot = $report->assignedBurialPlot ?? $report->burialPlot ?? null;
                            @endphp

                            <tr>
                                <td class="fw-semibold text-muted">
                                    {{ $index + 1 }}
                                </td>

                                <td>
                                    <div class="d-flex align-items-start gap-3">
                                        <div class="person-avatar">{{ $initial }}</div>
                                        <div>
                                            <div class="person-name">{{ $report->nama_si_mati ?? '-' }}</div>
                                            <div class="person-meta">No. KP: {{ $report->no_kp_si_mati ?? '-' }}</div>
                                            <div class="person-meta">Jantina: {{ ucfirst($report->jantina ?? '-') }}</div>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <span class="badge bg-secondary-subtle text-secondary status-badge">
                                        {{ $memberTypeLabel }}
                                    </span>
                                </td>

                                <td>
                                    <div class="fw-semibold">
                                        {{ optional($report->tarikh_meninggal)->format('d/m/Y') ?? '-' }}
                                    </div>
                                    <div class="person-meta">
                                        Umur: {{ $report->umur ?? '-' }} tahun
                                    </div>
                                </td>

                                <td>
                                    <div class="fw-semibold">{{ $report->nama_pelapor ?? '-' }}</div>
                                    <div class="person-meta">Tel: {{ $report->no_tel_pelapor ?? '-' }}</div>
                                    <div class="person-meta">Pertalian: {{ $report->pertalian_pelapor ?? '-' }}</div>
                                </td>

                                <td>
                                    @if($plot)
                                        <div class="fw-semibold">{{ $plot->plot_code ?? '-' }}</div>
                                        <div class="person-meta">{{ $plot->zone_label ?? '-' }}</div>
                                        <div class="person-meta">
                                            Baris {{ $plot->row_number ?? '-' }},
                                            Lot {{ $plot->lot_number ?? '-' }}
                                        </div>
                                    @else
                                        <span class="text-muted">Belum ditetapkan</span>
                                    @endif
                                </td>

                                <td>
                                    <span class="badge bg-{{ $statusData['class'] }} status-badge">
                                        {{ $statusData['label'] }}
                                    </span>
                                </td>

                                <td class="text-end">
                                    <a href="{{ route('admin.death-reports.show', $report->id) }}"
                                       class="btn btn-sm btn-outline-info">
                                        <i class="bx bx-show me-1"></i>
                                        Lihat
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">
                                    <div class="empty-state">
                                        <i class="bx bx-folder-open fs-1 mb-2 d-block"></i>
                                        Tiada rekod kematian dijumpai untuk tapisan ini.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-4 pb-4">
                <div class="report-note">
                    <strong>Nota audit:</strong>
                    Laporan ini memaparkan statistik ahli hidup, ahli meninggal dunia, pecahan ahli utama,
                    tanggungan, jantina, tarikh kematian, pelapor dan lokasi kubur.
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const monthlyLabels = @json($chartLabels ?? []);
    const monthlyTotals = @json($chartTotals ?? []);

    const lifeStatusLabels = ['Masih Hidup', 'Meninggal Dunia'];
    const lifeStatusTotals = [
        {{ $summary['alive'] ?? 0 }},
        {{ $summary['deceased'] ?? 0 }}
    ];

    if (typeof ApexCharts === 'undefined') {
        console.warn('ApexCharts tidak dijumpai. Pastikan library ApexCharts dimuatkan dalam layout.');
        return;
    }

    const monthlyChartElement = document.querySelector("#monthlyDeathChart");
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
                name: 'Jumlah Kematian',
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

    const lifeStatusChartElement = document.querySelector("#lifeStatusChart");
    if (lifeStatusChartElement) {
        const lifeStatusOptions = {
            chart: {
                type: 'donut',
                height: 320
            },
            labels: lifeStatusLabels,
            series: lifeStatusTotals,
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

        const lifeStatusChart = new ApexCharts(lifeStatusChartElement, lifeStatusOptions);
        lifeStatusChart.render();
    }
});
</script>
@endsection