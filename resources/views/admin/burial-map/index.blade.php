@extends('layouts.app')

@section('content')
<div class="container-fluid burial-map-page">

    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <h1 class="page-title fw-semibold fs-22 mb-1">Peta Plot Kubur</h1>
            <p class="text-muted mb-0">
                Paparan lokasi lot kubur, status penggunaan dan maklumat si mati.
            </p>
        </div>

        <div class="mt-3 mt-md-0 d-flex gap-2">
            <a href="{{ route('admin.death-reports.index') }}" class="btn btn-light border rounded-pill px-3">
                <i class="bx bx-arrow-back me-1"></i> Laporan Kematian
            </a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card custom-card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted mb-1 fs-12 fw-semibold">Jumlah Plot</p>
                            <h3 class="fw-bold mb-0">{{ $summary['total'] }}</h3>
                        </div>
                        <div class="stat-icon bg-primary-transparent text-primary">
                            <i class="bx bx-map-alt"></i>
                        </div>
                    </div>
                </div>
                <div class="stat-line primary"></div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card custom-card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted mb-1 fs-12 fw-semibold">Plot Kosong</p>
                            <h3 class="fw-bold mb-0">{{ $summary['available'] }}</h3>
                        </div>
                        <div class="stat-icon bg-success-transparent text-success">
                            <i class="bx bx-check-circle"></i>
                        </div>
                    </div>
                </div>
                <div class="stat-line success"></div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card custom-card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted mb-1 fs-12 fw-semibold">Telah Digunakan</p>
                            <h3 class="fw-bold mb-0">{{ $summary['occupied'] }}</h3>
                        </div>
                        <div class="stat-icon bg-danger-transparent text-danger">
                            <i class="bx bx-lock-alt"></i>
                        </div>
                    </div>
                </div>
                <div class="stat-line danger"></div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card custom-card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted mb-1 fs-12 fw-semibold">Zon Kubur</p>
                            <h3 class="fw-bold mb-0">3</h3>
                        </div>
                        <div class="stat-icon bg-info-transparent text-info">
                            <i class="bx bx-layer"></i>
                        </div>
                    </div>
                </div>
                <div class="stat-line info"></div>
            </div>
        </div>
    </div>

    <div class="card custom-card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.burial-map.index') }}" class="row g-3 align-items-end">

                <div class="col-xl-5 col-md-6">
                    <label class="form-label fw-semibold">Cari Maklumat Kubur</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="bx bx-search text-muted"></i>
                        </span>
                        <input type="text"
                               name="search"
                               value="{{ $search }}"
                               class="form-control border-start-0"
                               placeholder="Cari nama si mati / no IC / kod lot">
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <label class="form-label fw-semibold">Zon Kubur</label>
                    <select name="zone" class="form-select">
                        <option value="all" {{ $selectedZone == 'all' ? 'selected' : '' }}>Semua Zon</option>
                        <option value="L" {{ $selectedZone == 'L' ? 'selected' : '' }}>Zon Lelaki</option>
                        <option value="P" {{ $selectedZone == 'P' ? 'selected' : '' }}>Zon Perempuan</option>
                        <option value="K" {{ $selectedZone == 'K' ? 'selected' : '' }}>Zon Kanak-kanak</option>
                    </select>
                </div>

                <div class="col-xl-4 col-md-12 d-flex flex-wrap gap-2">
                    <button type="submit" class="btn btn-info text-white rounded-pill px-4">
                        <i class="bx bx-search-alt-2 me-1"></i> Cari
                    </button>

                    <a href="{{ route('admin.burial-map.index') }}" class="btn btn-light border rounded-pill px-4">
                        <i class="bx bx-reset me-1"></i> Reset
                    </a>
                </div>

            </form>
        </div>
    </div>

    @if($search && !$matchedPlotId)
        <div class="alert alert-warning border-0 shadow-sm rounded-4 d-flex align-items-center gap-2">
            <i class="bx bx-info-circle fs-5"></i>
            <div>
                Tiada rekod kubur dijumpai untuk carian <strong>{{ $search }}</strong>.
            </div>
        </div>
    @endif

    @if($matchedReport)
        <div class="alert alert-info border-0 shadow-sm rounded-4 d-flex align-items-center gap-2">
            <i class="bx bx-map-pin fs-5"></i>
            <div>
                Rekod dijumpai:
                <strong>{{ $matchedReport->nama_si_mati }}</strong>
                di lot
                <strong>{{ $matchedReport->burial_lot_no ?? $matchedReport->burial_plot_code }}</strong>.
            </div>
        </div>
    @endif

    <div class="card custom-card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-bottom-0 pt-4 px-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h5 class="card-title fw-bold mb-1">Pelan Plot Kubur</h5>
                    <p class="text-muted small mb-0">
                        Klik pada mana-mana plot untuk melihat status dan maklumat si mati.
                    </p>
                </div>

                <div class="map-legend">
                    <span><i class="legend-dot available"></i> Kosong</span>
                    <span><i class="legend-dot occupied"></i> Digunakan</span>
                    <span><i class="legend-dot matched"></i> Hasil Carian</span>
                </div>
            </div>
        </div>

        <div class="card-body p-4">

            @php
                $zoneLabels = [
                    'L' => 'Zon Lelaki',
                    'P' => 'Zon Perempuan',
                    'K' => 'Zon Kanak-kanak',
                ];

                $zoneIcons = [
                    'L' => 'bx-male',
                    'P' => 'bx-female',
                    'K' => 'bx-child',
                ];

                $groupedPlots = $plots->groupBy('zone');
            @endphp

            @forelse($groupedPlots as $zone => $zonePlots)
                <div class="zone-wrapper mb-4">
                    <div class="zone-header">
                        <div class="zone-title-wrap">
                            <div class="zone-icon">
                                <i class="bx {{ $zoneIcons[$zone] ?? 'bx-map-pin' }}"></i>
                            </div>
                            <div>
                                <div class="zone-title">{{ $zoneLabels[$zone] ?? 'Zon ' . $zone }}</div>
                                <div class="zone-subtitle">
                                    {{ $zonePlots->count() }} lot keseluruhan
                                </div>
                            </div>
                        </div>

                        <div class="zone-summary">
                            <span class="summary-pill available">
                                {{ $zonePlots->where('status', 'available')->count() }} Kosong
                            </span>
                            <span class="summary-pill occupied">
                                {{ $zonePlots->where('status', 'occupied')->count() }} Digunakan
                            </span>
                        </div>
                    </div>

                    @php
                        $rows = $zonePlots->groupBy('row_number');
                    @endphp

                    <div class="map-panel">
                        @foreach($rows as $rowNumber => $rowPlots)
                            <div class="plot-row">
                                <div class="row-label">
                                    Baris {{ $rowNumber }}
                                </div>

                                <div class="plot-list">
                                    @foreach($rowPlots as $plot)
                                        @php
                                            $report = $plot->deathReport;
                                            $isMatched = $matchedPlotId == $plot->id;
                                            $isOccupied = $plot->status === 'occupied';
                                        @endphp

                                        <button type="button"
                                                id="plot-{{ $plot->id }}"
                                                class="plot-card {{ $plot->status }} {{ $isMatched ? 'matched' : '' }}"
                                                data-bs-toggle="modal"
                                                data-bs-target="#plotModal{{ $plot->id }}">
                                            <span class="plot-shape">
                                                <i class="bx {{ $isOccupied ? 'bx-lock-alt' : 'bx-map-pin' }}"></i>
                                            </span>
                                            <span class="plot-code">{{ $plot->plot_code }}</span>
                                        </button>

                                        <div class="modal fade" id="plotModal{{ $plot->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                                <div class="modal-content border-0 rounded-4 overflow-hidden">

                                                    <div class="modal-header border-0 plot-modal-header {{ $isOccupied ? 'occupied' : 'available' }}">
                                                        <div>
                                                            <h5 class="modal-title fw-bold mb-1">
                                                                Maklumat Lot {{ $plot->plot_code }}
                                                            </h5>
                                                            <div class="small">
                                                                {{ $zoneLabels[$plot->zone] ?? 'Zon ' . $plot->zone }}
                                                            </div>
                                                        </div>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>

                                                    <div class="modal-body p-4">
                                                        <div class="row g-3 mb-4">
                                                            <div class="col-md-4">
                                                                <div class="info-box">
                                                                    <div class="info-label">Kod Lot</div>
                                                                    <div class="info-value">{{ $plot->plot_code }}</div>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-4">
                                                                <div class="info-box">
                                                                    <div class="info-label">Baris</div>
                                                                    <div class="info-value">{{ $plot->row_number }}</div>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-4">
                                                                <div class="info-box">
                                                                    <div class="info-label">Status</div>
                                                                    <div class="info-value">
                                                                        <span class="status-badge {{ $plot->status }}">
                                                                            {{ $isOccupied ? 'Telah Digunakan' : 'Kosong' }}
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        @if($report)
                                                            <div class="deceased-box">
                                                                <div class="section-title mb-3">
                                                                    <i class="bx bx-user me-1"></i>
                                                                    Maklumat Si Mati
                                                                </div>

                                                                <div class="row g-3">
                                                                    <div class="col-md-6">
                                                                        <div class="detail-label">Nama Si Mati</div>
                                                                        <div class="detail-value">{{ $report->nama_si_mati }}</div>
                                                                    </div>

                                                                    <div class="col-md-6">
                                                                        <div class="detail-label">No Kad Pengenalan</div>
                                                                        <div class="detail-value">{{ $report->no_kp_si_mati }}</div>
                                                                    </div>

                                                                    <div class="col-md-6">
                                                                        <div class="detail-label">Jantina</div>
                                                                        <div class="detail-value">{{ $report->jantina ?? '-' }}</div>
                                                                    </div>

                                                                    <div class="col-md-6">
                                                                        <div class="detail-label">Kategori Semakan</div>
                                                                        <div class="detail-value">
                                                                            {{ ucwords(str_replace('_', ' ', $report->verification_category ?? '-')) }}
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-md-6">
                                                                        <div class="detail-label">Tarikh Meninggal</div>
                                                                        <div class="detail-value">
                                                                            {{ optional($report->tarikh_meninggal)->format('d/m/Y') ?? '-' }}
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-md-6">
                                                                        <div class="detail-label">Tarikh Kebumi</div>
                                                                        <div class="detail-value">
                                                                            {{ optional($report->burial_date)->format('d/m/Y') ?? optional($plot->buried_at)->format('d/m/Y') ?? '-' }}
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-md-6">
                                                                        <div class="detail-label">Nama Pelapor</div>
                                                                        <div class="detail-value">{{ $report->nama_pelapor ?? '-' }}</div>
                                                                    </div>

                                                                    <div class="col-md-6">
                                                                        <div class="detail-label">No Telefon Pelapor</div>
                                                                        <div class="detail-value">{{ $report->no_tel_pelapor ?? '-' }}</div>
                                                                    </div>
                                                                </div>

                                                                <div class="mt-4">
                                                                    <a href="{{ route('admin.death-reports.show', $report->id) }}"
                                                                       class="btn btn-info text-white rounded-pill px-4">
                                                                        <i class="bx bx-show me-1"></i> Lihat Laporan
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        @else
                                                            <div class="empty-box">
                                                                <div class="empty-icon">
                                                                    <i class="bx bx-map-pin"></i>
                                                                </div>
                                                                <div class="fw-bold mb-1">Plot ini masih kosong</div>
                                                                <div class="text-muted">
                                                                    Tiada rekod pengebumian didaftarkan untuk lot ini.
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <div class="modal-footer border-0 pt-0 px-4 pb-4">
                                                        <button type="button" class="btn btn-light border rounded-pill px-4" data-bs-dismiss="modal">
                                                            Tutup
                                                        </button>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <div class="empty-icon mx-auto mb-3">
                        <i class="bx bx-map"></i>
                    </div>
                    <h6 class="fw-bold">Tiada plot dijumpai</h6>
                    <p class="text-muted mb-0">Sila semak semula data plot kubur.</p>
                </div>
            @endforelse

        </div>
    </div>
</div>

<style>
    .burial-map-page {
        --soft-border: #e5e7eb;
        --text-main: #111827;
        --text-muted: #6b7280;
        --soft-bg: #f8fafc;
        --info: #0dcaf0;
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }

    .stat-line {
        height: 4px;
        width: 100%;
    }

    .stat-line.primary {
        background: #cfe2ff;
    }

    .stat-line.success {
        background: #bbf7d0;
    }

    .stat-line.danger {
        background: #fecaca;
    }

    .stat-line.info {
        background: #cff4fc;
    }

    .map-legend {
        display: flex;
        flex-wrap: wrap;
        gap: 14px;
        font-size: 13px;
        color: var(--text-muted);
    }

    .legend-dot {
        width: 11px;
        height: 11px;
        display: inline-block;
        border-radius: 50%;
        margin-right: 5px;
        vertical-align: middle;
    }

    .legend-dot.available {
        background: #86efac;
    }

    .legend-dot.occupied {
        background: #fca5a5;
    }

    .legend-dot.matched {
        background: #38bdf8;
    }

    .zone-wrapper {
        border: 1px solid var(--soft-border);
        border-radius: 22px;
        overflow: hidden;
        background: #ffffff;
    }

    .zone-header {
        padding: 18px 20px;
        background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
        border-bottom: 1px solid var(--soft-border);
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 14px;
        flex-wrap: wrap;
    }

    .zone-title-wrap {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .zone-icon {
        width: 44px;
        height: 44px;
        border-radius: 16px;
        background: #e0f2fe;
        color: #0369a1;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 23px;
    }

    .zone-title {
        font-size: 15px;
        font-weight: 800;
        color: var(--text-main);
    }

    .zone-subtitle {
        font-size: 12px;
        color: var(--text-muted);
        margin-top: 2px;
    }

    .zone-summary {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .summary-pill {
        padding: 7px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
    }

    .summary-pill.available {
        background: #dcfce7;
        color: #166534;
    }

    .summary-pill.occupied {
        background: #fee2e2;
        color: #991b1b;
    }

    .map-panel {
        padding: 22px;
        background:
            radial-gradient(circle at top left, rgba(13,202,240,.08), transparent 28%),
            linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
        max-height: 720px;
        overflow: auto;
    }

    .map-panel::-webkit-scrollbar {
        width: 10px;
        height: 10px;
    }

    .map-panel::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 999px;
    }

    .map-panel::-webkit-scrollbar-track {
        background: #f1f5f9;
    }

    .plot-row {
        display: flex;
        align-items: flex-start;
        gap: 16px;
        margin-bottom: 18px;
        min-width: max-content;
    }

    .plot-row:last-child {
        margin-bottom: 0;
    }

    .row-label {
        min-width: 78px;
        padding-top: 18px;
        font-size: 12px;
        font-weight: 800;
        color: #64748b;
    }

    .plot-list {
        display: flex;
        flex-wrap: nowrap;
        gap: 12px;
    }

    .plot-card {
        width: 82px;
        min-height: 74px;
        border: 1px solid transparent;
        border-radius: 18px;
        padding: 10px 8px;
        background: #ffffff;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 7px;
        transition: .18s ease;
        box-shadow: 0 3px 10px rgba(15,23,42,.06);
    }

    .plot-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 24px rgba(15,23,42,.12);
    }

    .plot-card.available {
        background: #f0fdf4;
        border-color: #bbf7d0;
        color: #166534;
    }

    .plot-card.occupied {
        background: #fff1f2;
        border-color: #fecdd3;
        color: #991b1b;
    }

    .plot-card.matched {
        background: #e0f2fe !important;
        color: #075985 !important;
        border: 2px solid #38bdf8 !important;
        box-shadow: 0 0 0 6px rgba(56,189,248,.18);
        animation: pulsePlot 1.5s infinite;
    }

    .plot-shape {
        width: 34px;
        height: 34px;
        border-radius: 13px;
        background: rgba(255,255,255,.75);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }

    .plot-code {
        font-size: 11px;
        font-weight: 800;
        line-height: 1.2;
    }

    .plot-modal-header {
        padding: 22px 24px;
    }

    .plot-modal-header.available {
        background: linear-gradient(135deg, #dcfce7 0%, #ffffff 100%);
        color: #14532d;
    }

    .plot-modal-header.occupied {
        background: linear-gradient(135deg, #fee2e2 0%, #ffffff 100%);
        color: #7f1d1d;
    }

    .info-box {
        background: #f8fafc;
        border: 1px solid var(--soft-border);
        border-radius: 18px;
        padding: 16px;
        height: 100%;
    }

    .info-label,
    .detail-label {
        font-size: 12px;
        color: #64748b;
        margin-bottom: 4px;
        font-weight: 600;
    }

    .info-value,
    .detail-value {
        font-weight: 800;
        color: #111827;
    }

    .status-badge {
        display: inline-flex;
        padding: 7px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 800;
    }

    .status-badge.available {
        background: #dcfce7;
        color: #166534;
    }

    .status-badge.occupied {
        background: #fee2e2;
        color: #991b1b;
    }

    .deceased-box {
        border: 1px solid var(--soft-border);
        border-radius: 20px;
        padding: 20px;
        background: #ffffff;
    }

    .section-title {
        font-weight: 800;
        color: #111827;
        display: flex;
        align-items: center;
    }

    .empty-box {
        border: 1px dashed #cbd5e1;
        border-radius: 20px;
        padding: 30px;
        text-align: center;
        background: #f8fafc;
    }

    .empty-icon {
        width: 54px;
        height: 54px;
        border-radius: 18px;
        background: #e0f2fe;
        color: #0369a1;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        margin-bottom: 12px;
    }

    @keyframes pulsePlot {
        0% {
            box-shadow: 0 0 0 0 rgba(56,189,248,.35);
        }

        70% {
            box-shadow: 0 0 0 9px rgba(56,189,248,0);
        }

        100% {
            box-shadow: 0 0 0 0 rgba(56,189,248,0);
        }
    }

    @media (max-width: 768px) {
        .map-panel {
            max-height: 620px;
            padding: 16px;
        }

        .plot-card {
            width: 74px;
            min-height: 68px;
        }

        .row-label {
            min-width: 65px;
        }
    }
</style>

@if($matchedPlotId)
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const matchedPlot = document.getElementById('plot-{{ $matchedPlotId }}');

        if (matchedPlot) {
            matchedPlot.scrollIntoView({
                behavior: 'smooth',
                block: 'center',
                inline: 'center'
            });
        }
    });
</script>
@endif

@endsection