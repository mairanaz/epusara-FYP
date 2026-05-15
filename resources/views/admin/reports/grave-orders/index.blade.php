@extends('layouts.app')

@section('content')
<style>
    .death-page .hero-card {
        border: 0;
        border-radius: 20px;
        background: linear-gradient(135deg, #dde7f2, #eef4f9);
        color: #1f2937;
        overflow: hidden;
        position: relative;
    }

    .death-page .hero-card::after {
        content: "";
        position: absolute;
        top: -35px;
        right: -35px;
        width: 170px;
        height: 170px;
        background: rgba(255,255,255,0.22);
        border-radius: 50%;
    }

    .death-page .stats-card,
    .death-page .filter-card,
    .death-page .table-card,
    .death-page .chart-card {
        border: 0;
        border-radius: 18px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.06);
    }

    .death-page .stats-card {
        transition: 0.25s ease;
    }

    .death-page .stats-card:hover {
        transform: translateY(-4px);
    }

    .death-page .stats-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        flex-shrink: 0;
    }

    .death-page .summary-label {
        font-size: 13px;
        color: #6b7280;
        margin-bottom: 6px;
    }

    .death-page .summary-value {
        font-size: 24px;
        font-weight: 800;
        color: #111827;
        line-height: 1;
    }

    .death-page .search-box .form-control,
    .death-page .search-box .form-select {
        border-radius: 12px;
        min-height: 46px;
    }

    .death-page .btn {
        border-radius: 12px;
        min-height: 46px;
    }

    .death-page .table thead th {
        background: #f8fafc;
        border-bottom: 1px solid #e9ecef;
        font-weight: 700;
        color: #344054;
        white-space: nowrap;
    }

    .death-page .table tbody tr {
        transition: 0.2s ease;
    }

    .death-page .table tbody tr:hover {
        background-color: #f7fbfe;
    }

    .death-page .person-avatar {
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

    .death-page .person-name {
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 2px;
    }

    .death-page .person-meta {
        font-size: 13px;
        color: #6b7280;
    }

    .death-page .status-badge {
        display: inline-block;
        padding: 8px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
    }

    .death-page .action-btn {
        width: 38px;
        height: 38px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
    }

    .death-page .empty-state {
        padding: 40px 20px;
        text-align: center;
        color: #6b7280;
    }

    .death-page .hero-subtitle {
        color: #6b7280;
    }

    .death-page .total-row td {
        background: #f8fafc;
        font-weight: 800;
    }

    .death-page .amount-text {
        font-weight: 800;
        color: #111827;
        white-space: nowrap;
    }

    .death-page .report-note {
        background: #f8fafc;
        border-radius: 14px;
        padding: 14px 16px;
        color: #667085;
        font-size: 13px;
    }

    .death-page .report-badge {
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

    .death-page .table-responsive {
        border-radius: 14px;
    }

    .death-page .chart-box {
        min-height: 320px;
    }

    @media (max-width: 576px) {
        .death-page .summary-value {
            font-size: 20px;
        }

        .death-page .stats-icon {
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

    $selectedStatusName = match ($status) {
        'pending' => 'Menunggu Kelulusan',
        'approved' => 'Diluluskan',
        'cancelled' => 'Dibatalkan',
        default => 'Semua Status',
    };
@endphp

<div class="container-fluid death-page">

    {{-- Hero Header --}}
    <div class="card hero-card shadow-sm mb-4">
        <div class="card-body p-4 p-lg-5">
            <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                <div>
                    <p class="mb-2 small hero-subtitle">Panel Pentadbir / Laporan Sistem</p>
                    <h1 class="fw-bold mb-2">Laporan Tempahan Kepukan</h1>
                    <p class="mb-0 hero-subtitle">
                        Paparan laporan tempahan kepukan mengikut tahun, bulan dan status untuk tujuan semakan serta audit.
                    </p>
                </div>

                <div class="text-lg-end">
                    <div class="report-badge mb-2">
                        <i class="bx bx-bar-chart-alt-2"></i>
                        Audit Report
                    </div>
                    <div class="small text-muted">Tahun Laporan</div>
                    <div class="fs-3 fw-bold text-dark">{{ $year }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card stats-card h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="summary-label">Jumlah Tempahan</div>
                        <div class="summary-value">{{ $summary['total'] ?? 0 }}</div>
                    </div>
                    <div class="stats-icon bg-primary-subtle text-primary">
                        <i class="bx bx-file"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stats-card h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="summary-label">Menunggu Kelulusan</div>
                        <div class="summary-value">{{ $summary['pending'] ?? 0 }}</div>
                    </div>
                    <div class="stats-icon bg-warning-subtle text-warning">
                        <i class="bx bx-time-five"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
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

        <div class="col-xl-3 col-md-6">
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
    </div>

    {{-- Filter --}}
    <div class="card filter-card mb-4">
        <div class="card-body p-4">
            <div class="mb-3">
                <h5 class="mb-1 fw-bold">Jana Laporan</h5>
                <p class="text-muted mb-0">
                    Pilih tahun, bulan dan status untuk menjana laporan tempahan kepukan.
                </p>
            </div>

            <form method="GET" action="{{ route('admin.reports.grave-orders.index') }}" class="search-box">
                <div class="row g-3 align-items-end">
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label fw-semibold">Tahun</label>
                        <select name="year" class="form-select">
                            @foreach($years as $availableYear)
                                <option value="{{ $availableYear }}" {{ (int) $year === (int) $availableYear ? 'selected' : '' }}>
                                    {{ $availableYear }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-3 col-md-6">
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

                    <div class="col-lg-3 col-md-6">
                        <label class="form-label fw-semibold">Status</label>
                        <select name="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Menunggu Kelulusan</option>
                            <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>Diluluskan</option>
                            <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                        </select>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-info btn-wave w-100">
                                <i class="bx bx-search me-1"></i> Jana
                            </button>

                            <a href="{{ route('admin.reports.grave-orders.index') }}" class="btn btn-outline-info w-100">
                                Reset
                            </a>
                        </div>
                    </div>
                </div>
            </form>

            <div class="report-note mt-4">
                <strong>Tapisan semasa:</strong>
                Tahun {{ $year }},
                {{ $selectedMonthName }},
                {{ $selectedStatusName }}.
            </div>
        </div>
    </div>

    {{-- Financial Summary --}}
    <div class="row g-3 mb-4">
        <div class="col-xl-6 col-md-6">
            <div class="card stats-card h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="summary-label">Jumlah Bayaran</div>
                        <div class="summary-value fs-4">
                            RM {{ number_format($summary['amount'] ?? 0, 2) }}
                        </div>
                    </div>
                    <div class="stats-icon bg-secondary-subtle text-secondary">
                        <i class="bx bx-money"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-md-6">
            <div class="card stats-card h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="summary-label">Jumlah Rekod Dipaparkan</div>
                        <div class="summary-value fs-4">{{ $orders->count() }}</div>
                    </div>
                    <div class="stats-icon bg-info-subtle text-info">
                        <i class="bx bx-list-ul"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts --}}
    <div class="row g-3 mb-4">
        <div class="col-xl-8">
            <div class="card chart-card h-100">
                <div class="card-body p-4">
                    <h5 class="mb-1 fw-bold">Graf Tempahan Mengikut Bulan</h5>
                    <p class="text-muted mb-3">
                        Jumlah tempahan kepukan bagi setiap bulan dalam tahun {{ $year }}.
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
                        Pecahan status berdasarkan tapisan semasa.
                    </p>
                    <div id="statusOrderChart" class="chart-box"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Monthly Summary --}}
    <div class="card table-card mb-4">
        <div class="card-body p-0">
            <div class="px-4 pt-4 pb-2">
                <h5 class="mb-1 fw-bold">Ringkasan Bulanan Tahun {{ $year }}</h5>
                <p class="text-muted mb-0">
                    Jumlah tempahan, status dan jumlah bayaran mengikut bulan.
                </p>
            </div>

            <div class="table-responsive px-3 pb-3">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Bulan</th>
                            <th class="text-center">Jumlah Tempahan</th>
                            <th class="text-center">Menunggu</th>
                            <th class="text-center">Diluluskan</th>
                            <th class="text-center">Dibatalkan</th>
                            <th class="text-end">Jumlah Bayaran</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($monthlySummary as $monthItem)
                            <tr>
                                <td class="fw-bold">{{ $monthItem['month_name'] }}</td>
                                <td class="text-center">{{ $monthItem['total'] }}</td>
                                <td class="text-center">{{ $monthItem['pending'] }}</td>
                                <td class="text-center">{{ $monthItem['approved'] }}</td>
                                <td class="text-center">{{ $monthItem['cancelled'] }}</td>
                                <td class="text-end amount-text">
                                    RM {{ number_format($monthItem['amount'], 2) }}
                                </td>
                            </tr>
                        @endforeach

                        <tr class="total-row">
                            <td>TOTAL</td>
                            <td class="text-center">{{ $monthlySummary->sum('total') }}</td>
                            <td class="text-center">{{ $monthlySummary->sum('pending') }}</td>
                            <td class="text-center">{{ $monthlySummary->sum('approved') }}</td>
                            <td class="text-center">{{ $monthlySummary->sum('cancelled') }}</td>
                            <td class="text-end amount-text">
                                RM {{ number_format($monthlySummary->sum('amount'), 2) }}
                            </td>
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
                <h5 class="mb-1 fw-bold">Senarai Detail Tempahan</h5>
                <p class="text-muted mb-0">
                    Jumlah paparan: <span class="fw-semibold">{{ $orders->count() }}</span> tempahan
                </p>
            </div>

            <div class="table-responsive px-3 pb-3">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th width="6%">#</th>
                            <th>No Tempahan</th>
                            <th>Pemohon / Waris</th>
                            <th>Si Mati</th>
                            <th>Plot / Lokasi</th>
                            <th>Jenis Kepukan</th>
                            <th>Jumlah</th>
                            <th>Status</th>
                            <th>Tarikh Tempahan</th>
                            <th>Resit / Catatan</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($orders as $index => $order)
                            @php
                                $statusData = $statusMap[$order->status] ?? [
                                    'label' => ucfirst(str_replace('_', ' ', $order->status ?? '-')),
                                    'class' => 'secondary',
                                ];

                                $pemohonNama = $order->user->profile->nama
                                    ?? $order->user->name
                                    ?? '-';

                                $pemohonKp = $order->user->profile->no_kp ?? '-';
                                $pemohonTel = $order->user->profile->no_tel_bimbit ?? '-';

                                $namaSiMati = $order->deathReport->nama_si_mati ?? '-';
                                $noKpSiMati = $order->deathReport->no_kp_si_mati ?? '-';

                                $initial = strtoupper(substr($namaSiMati !== '-' ? $namaSiMati : $pemohonNama, 0, 1));
                            @endphp

                            <tr>
                                <td class="fw-semibold text-muted">
                                    {{ $index + 1 }}
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
                                    <div class="person-meta">No. KP: {{ $pemohonKp }}</div>
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
                                        <div class="fw-semibold">{{ $order->burialPlot->plot_code }}</div>
                                        <div class="person-meta">{{ $order->burialPlot->zone_label }}</div>
                                        <div class="person-meta">
                                            Baris {{ $order->burialPlot->row_number }},
                                            Lot {{ $order->burialPlot->lot_number }}
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>

                                <td>
                                    <div class="fw-semibold">{{ $order->order_label ?? '-' }}</div>
                                    <div class="person-meta">Kategori: {{ $order->category ?? '-' }}</div>
                                    <div class="person-meta">Jenis: {{ $order->order_type ?? '-' }}</div>
                                </td>

                                <td>
                                    <span class="amount-text">
                                        RM {{ number_format($order->amount ?? 0, 2) }}
                                    </span>
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
                                    @if($order->approved_at)
                                        <div class="person-meta">
                                            Lulus: {{ optional($order->approved_at)->format('d/m/Y') }}
                                        </div>
                                    @endif
                                </td>

                                <td>
                                    <div class="person-meta">
                                        Resit: {{ $order->receipt_no ?? '-' }}
                                    </div>
                                    <div class="person-meta">
                                        Catatan: {{ $order->admin_note ?? '-' }}
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10">
                                    <div class="empty-state">
                                        <i class="bx bx-folder-open fs-1 mb-2 d-block"></i>
                                        Tiada rekod tempahan kepukan dijumpai untuk tapisan ini.
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
                    Laporan ini memaparkan maklumat tempahan, pemohon, si mati, plot kubur, jenis kepukan,
                    jumlah bayaran, status, tarikh tempahan, nombor resit dan catatan admin.
                </div>
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
                name: 'Jumlah Tempahan',
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