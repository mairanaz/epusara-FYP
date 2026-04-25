@extends('layouts.app')

@section('content')
<div class="container-fluid cemetery-page">

    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <h1 class="page-title fw-semibold fs-22 mb-1">Pelan Lot Kubur</h1>
            <p class="text-muted mb-0">
                Taman Zaiirah • Tanah Perkuburan RTB Bukit Changgang
            </p>
        </div>

        <div class="mt-3 mt-md-0">
            <a href="{{ route('admin.death-reports.show', $deathReport->id) }}" class="btn btn-light border rounded-pill px-3">
                <i class="bx bx-arrow-back me-1"></i> Kembali
            </a>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger rounded-4 border-0 shadow-sm">
            <div class="fw-semibold mb-2">Sila semak maklumat berikut:</div>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.death-reports.store-plot', $deathReport->id) }}" method="POST">
        @csrf

        <div class="row g-4">

            <div class="col-xl-3">
                <div class="sidebar-stack">

                    <div class="card border-0 shadow-sm cemetery-card">
                        <div class="card-body">
                            <div class="panel-title-wrap">
                                <span class="panel-icon"><i class="bx bx-user"></i></span>
                                <div>
                                    <div class="panel-title">Maklumat Si Mati</div>
                                    <div class="panel-subtitle">Butiran asas untuk penetapan lot</div>
                                </div>
                            </div>

                            <div class="info-group">
                                <div class="info-label">Nama</div>
                                <div class="info-value">{{ $deathReport->nama_si_mati }}</div>
                            </div>

                            <div class="info-group">
                                <div class="info-label">Zon Dipaparkan</div>
                                <div class="info-value">
                                    <span class="zone-chip">Zon {{ $zone }}</span>
                                </div>
                            </div>

                            <div class="info-group">
                                <label class="info-label mb-2">Tarikh Kebumi</label>
                                <input
                                    type="date"
                                    name="burial_date"
                                    class="form-control custom-input"
                                    value="{{ old('burial_date', optional($deathReport->burial_date)->format('Y-m-d')) }}"
                                    required
                                >
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm cemetery-card">
                        <div class="card-body">
                            <div class="panel-title-wrap">
                                <span class="panel-icon blue"><i class="bx bx-map-pin"></i></span>
                                <div>
                                    <div class="panel-title">Lot Dipilih</div>
                                    <div class="panel-subtitle">Satu laporan kematian hanya dibenarkan satu lot kubur</div>
                                </div>
                            </div>

                            <div class="selected-lot-box" id="selectedLotText">
                                {{ $deathReport->burial_lot_no ?: 'Belum memilih lot kubur' }}
                            </div>

                            <div class="selected-note">
                                Pilih satu lot yang masih kosong untuk jenazah ini. Lot yang telah digunakan tidak boleh dipilih.
                            </div>

                            <button type="submit" class="btn btn-info w-100 rounded-pill py-2 mt-3 save-btn">
                                <i class="bx bx-save me-1"></i> Simpan Lot Kubur
                            </button>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm cemetery-card">
                        <div class="card-body">
                            <div class="panel-title-wrap mb-3">
                                <span class="panel-icon green"><i class="bx bx-layer"></i></span>
                                <div>
                                    <div class="panel-title">Petunjuk Pelan</div>
                                    <div class="panel-subtitle">Maksud warna dan elemen</div>
                                </div>
                            </div>

                            <div class="legend-list">
                                <div class="legend-item">
                                    <span class="legend-box available-box"></span>
                                    <span>Lot Kosong</span>
                                </div>
                                <div class="legend-item">
                                    <span class="legend-box occupied-box"></span>
                                    <span>Telah Digunakan</span>
                                </div>
                                <div class="legend-item">
                                    <span class="legend-box selected-box"></span>
                                    <span>Lot Dipilih</span>
                                </div>
                                <div class="legend-item">
                                    <span class="legend-line road-box"></span>
                                    <span>Jalan Laluan</span>
                                </div>
                                <div class="legend-item">
                                    <span class="legend-circle tree-box"></span>
                                    <span>Kawasan Pokok</span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="col-xl-9">
                <div class="card border-0 shadow-sm cemetery-card map-shell" id="mapFullContainer">

                    <div class="card-header map-toolbar border-0 bg-white">
                        <div class="toolbar-left">
                            <div class="toolbar-title">Pelan Kawasan Perkuburan</div>
                            <div class="toolbar-subtitle">
                                 {{-- Susun atur lot, jalan, pondok, pokok dan arah kiblat dipaparkan dalam satu pelan interaktif. --}}
                            </div>
                        </div>

                        <div class="toolbar-right">
                            <div class="zoom-indicator" id="zoomLevel">100%</div>

                            <div class="toolbar-controls">
                                <button type="button" class="tool-btn" id="zoomOutBtn" title="Zoom Out">
                                    <i class="bx bx-minus"></i>
                                </button>

                                <button type="button" class="tool-btn" id="zoomInBtn" title="Zoom In">
                                    <i class="bx bx-plus"></i>
                                </button>

                                <button type="button" class="tool-btn reset-btn" id="resetZoomBtn" title="Reset">
                                    <i class="bx bx-reset"></i>
                                    <span>Reset</span>
                                </button>

                                <button type="button" class="tool-btn fullview-btn" id="fullViewBtn" title="Full View">
                                    <i class="bx bx-fullscreen"></i>
                                    <span>Full View</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-4">

                        <div class="map-help-bar">
                            <div class="help-pill">
                                <i class="bx bx-mouse"></i>
                                Drag untuk gerakkan pelan
                            </div>
                            <div class="help-pill">
                                <i class="bx bx-zoom-in"></i>
                                Guna butang untuk zoom
                            </div>
                            <div class="help-pill">
                                <i class="bx bx-move"></i>
                                Scroll untuk lihat kawasan lain
                            </div>
                        </div>

                        <div class="map-frame">
                            <div class="map-scroll-area" id="mapViewer">
                                <div class="map-stage">
                                    <div class="cemetery-map" id="cemeteryMap">

                                        <div class="qiblat-panel">
                                            <div class="qiblat-label">KIBLAT</div>
                                            <div class="qiblat-arrow">⬅</div>
                                        </div>

                                        <div class="compass-box">
                                            <div class="compass-circle">
                                                <span class="north">N</span>
                                                <span class="south">S</span>
                                                <span class="west">W</span>
                                                <span class="east">E</span>
                                                <div class="compass-needle"></div>
                                                <div class="compass-center"></div>
                                            </div>
                                        </div>

                                        <div class="road road-top"></div>
                                        <div class="road road-right"></div>
                                        <div class="road road-bottom"></div>

                                        <div class="feature-label label-road-top">Laluan Utama</div>

                                        <div class="gazebo-card">
                                            <div class="gazebo-icon">
                                                <div class="roof"></div>
                                                <div class="base"></div>
                                                <span class="pillar p1"></span>
                                                <span class="pillar p2"></span>
                                                <span class="pillar p3"></span>
                                                <span class="pillar p4"></span>
                                            </div>
                                            <div class="gazebo-label">Pondok Rehat</div>
                                        </div>

                                        <div class="tree-cluster cluster-top-left">
                                            <span class="tree"></span>
                                            <span class="tree"></span>
                                        </div>

                                        <div class="tree-cluster cluster-top-right">
                                            <span class="tree"></span>
                                            <span class="tree"></span>
                                        </div>

                                        <div class="tree-cluster cluster-bottom-left">
                                            <span class="tree"></span>
                                            <span class="tree"></span>
                                        </div>

                                        <div class="tree-cluster cluster-bottom-right">
                                            <span class="tree"></span>
                                            <span class="tree"></span>
                                        </div>

                                        <div class="grave-zone-heading">
                                            <div class="grave-zone-title">Kawasan Lot Perkuburan</div>
                                            <div class="grave-zone-subtitle">Klik pada lot kosong untuk membuat pilihan</div>
                                        </div>

                                        <div class="grave-zone">
                                            @foreach($plots as $row => $rowPlots)
                                                <div class="grave-row">
                                                    @foreach($rowPlots as $plot)
                                                        <label class="grave-label">
                                                            <input
                                                                type="radio"
                                                                name="burial_plot_id"
                                                                value="{{ $plot->id }}"
                                                                class="grave-radio"
                                                                data-code="{{ $plot->plot_code }}"
                                                                {{ $plot->status === 'occupied' ? 'disabled' : '' }}
                                                                {{ old('burial_plot_id') == $plot->id ? 'checked' : '' }}
                                                            >

                                                            <div class="grave-item {{ $plot->status === 'occupied' ? 'occupied' : 'available' }}">
                                                                <div class="grave-shape"></div>
                                                                <div class="grave-code">{{ $plot->plot_code }}</div>
                                                            </div>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            @endforeach
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="map-footer-note">
                            Paparan ini direka supaya admin boleh fokus pada lot, jalan dan susun atur kawasan dengan lebih jelas.
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
    .cemetery-page {
        --text-main: #111827;
        --text-soft: #6b7280;
        --card-radius: 22px;
    }

    .cemetery-card {
        border-radius: var(--card-radius);
        overflow: hidden;
    }

    .sidebar-stack {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    .panel-title-wrap {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 20px;
    }

    .panel-icon {
        width: 42px;
        height: 42px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #f3f4f6;
        color: #111827;
        font-size: 20px;
        flex-shrink: 0;
    }

    .panel-icon.blue {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .panel-icon.green {
        background: #dcfce7;
        color: #166534;
    }

    .panel-title {
        font-size: 15px;
        font-weight: 800;
        color: var(--text-main);
        line-height: 1.2;
    }

    .panel-subtitle {
        font-size: 12px;
        color: var(--text-soft);
        margin-top: 2px;
    }

    .info-group {
        margin-bottom: 18px;
    }

    .info-label {
        font-size: 12px;
        font-weight: 600;
        color: var(--text-soft);
        display: block;
        margin-bottom: 6px;
    }

    .info-value {
        font-size: 15px;
        font-weight: 700;
        color: var(--text-main);
    }

    .zone-chip {
        display: inline-flex;
        align-items: center;
        padding: 7px 14px;
        border-radius: 999px;
        background: #eef2ff;
        color: #4338ca;
        font-size: 13px;
        font-weight: 800;
    }

    .custom-input {
        min-height: 48px;
        border-radius: 14px;
        border: 1px solid #dbe1e8;
        box-shadow: none;
    }

    .selected-lot-box {
        min-height: 78px;
        border-radius: 18px;
        padding: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        font-size: 18px;
        font-weight: 800;
        color: #1d4ed8;
        background: linear-gradient(135deg, #eff6ff 0%, #f8fbff 100%);
        border: 1px solid #cfe0ff;
    }

    .selected-note {
        font-size: 13px;
        color: var(--text-soft);
        line-height: 1.6;
        margin-top: 12px;
    }

    .save-btn {
        font-weight: 700;
    }

    .legend-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 14px;
        color: #374151;
    }

    .legend-box {
        width: 18px;
        height: 18px;
        border-radius: 5px;
        display: inline-block;
        flex-shrink: 0;
    }

    .available-box {
        background: #e9fbe9;
        border: 1px solid #9ad59a;
    }

    .occupied-box {
        background: #fde8e8;
        border: 1px solid #efb2b2;
    }

    .selected-box {
        background: #dbeafe;
        border: 1px solid #7aa8ff;
    }

    .legend-line {
        width: 20px;
        height: 8px;
        border-radius: 999px;
        display: inline-block;
        flex-shrink: 0;
    }

    .road-box {
        background: #9a5a25;
    }

    .legend-circle {
        width: 18px;
        height: 18px;
        border-radius: 50%;
        display: inline-block;
        flex-shrink: 0;
    }

    .tree-box {
        background: #65a30d;
    }

    .map-shell .card-header {
        padding: 22px 24px 18px;
    }

    .map-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
    }

    .toolbar-title {
        font-size: 18px;
        font-weight: 800;
        color: #111827;
        margin-bottom: 4px;
    }

    .toolbar-subtitle {
        font-size: 13px;
        color: var(--text-soft);
    }

    .toolbar-right {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .zoom-indicator {
        min-width: 76px;
        height: 38px;
        padding: 0 14px;
        border-radius: 999px;
        background: #eef2ff;
        color: #4338ca;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        font-weight: 800;
        border: 1px solid #d8dfff;
    }

    .toolbar-controls {
        display: flex;
        align-items: center;
        gap: 8px;
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        border-radius: 999px;
        padding: 6px;
    }

    .tool-btn {
        height: 40px;
        min-width: 40px;
        border: 0;
        border-radius: 999px;
        background: #ffffff;
        color: #111827;
        padding: 0 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        font-size: 18px;
        box-shadow: 0 1px 3px rgba(0,0,0,.06);
        transition: .2s ease;
    }

    .tool-btn:hover {
        transform: translateY(-1px);
        background: #f3f4f6;
    }

    .reset-btn {
        font-size: 13px;
        font-weight: 700;
    }

    .map-help-bar {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 16px;
    }

    .help-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 14px;
        border-radius: 999px;
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        color: #4b5563;
        font-size: 13px;
        font-weight: 600;
    }

    .map-frame {
        border-radius: 24px;
        background: linear-gradient(180deg, #f8fafc 0%, #eef2f7 100%);
        border: 1px solid #e5e7eb;
        padding: 14px;
    }

    .map-scroll-area {
        width: 100%;
        height: 760px;
        overflow: auto;
        border-radius: 18px;
        background: #edf3e7;
        cursor: grab;
        position: relative;
        scroll-behavior: smooth;
        border: 1px solid #d9e1d1;
    }

    .map-scroll-area.dragging {
        cursor: grabbing;
        user-select: none;
    }

    .map-scroll-area::-webkit-scrollbar {
        width: 12px;
        height: 12px;
    }

    .map-scroll-area::-webkit-scrollbar-thumb {
        background: #b9c2cc;
        border-radius: 999px;
        border: 2px solid #edf3e7;
    }

    .map-scroll-area::-webkit-scrollbar-track {
        background: #e6ece0;
        border-radius: 999px;
    }

    .map-stage {
        width: max-content;
        min-width: 1680px;
        min-height: 2250px;
        padding: 24px;
    }

    .cemetery-map {
        position: relative;
        width: 1600px;
        min-height: 2180px;
        border-radius: 30px;
        overflow: hidden;
        transform-origin: top left;
        transition: transform .18s ease;
        border: 1px solid #cdd9c5;
        background:
            radial-gradient(circle at 20% 20%, rgba(255,255,255,.18), transparent 18%),
            radial-gradient(circle at 80% 80%, rgba(255,255,255,.12), transparent 16%),
            linear-gradient(180deg, #87b561 0%, #79a853 100%);
        box-shadow:
            inset 0 0 0 1px rgba(255,255,255,.12),
            0 10px 24px rgba(0,0,0,.08);
    }

    .qiblat-panel {
        position: absolute;
        top: 24px;
        left: 24px;
        display: flex;
        align-items: center;
        gap: 12px;
        z-index: 4;
    }

    .qiblat-label {
        writing-mode: vertical-rl;
        transform: rotate(180deg);
        background: #ffedd5;
        color: #7c2d12;
        padding: 14px 10px;
        border-radius: 14px;
        font-size: 12px;
        font-weight: 800;
        letter-spacing: 1px;
        box-shadow: 0 8px 18px rgba(0,0,0,.12);
    }

    .qiblat-arrow {
        font-size: 44px;
        font-weight: 900;
        color: #111827;
    }

    .compass-box {
        position: absolute;
        top: 22px;
        right: 22px;
        width: 96px;
        height: 96px;
        z-index: 4;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .compass-circle {
        position: relative;
        width: 86px;
        height: 86px;
        border-radius: 50%;
        border: 3px solid #334155;
        background: rgba(255,255,255,.35);
    }

    .north, .south, .west, .east {
        position: absolute;
        font-size: 11px;
        font-weight: 800;
        color: #111827;
    }

    .north { top: -8px; left: 37px; }
    .south { bottom: -8px; left: 36px; }
    .west  { top: 35px; left: -9px; }
    .east  { top: 35px; right: -9px; }

    .compass-needle {
        position: absolute;
        top: 10px;
        left: 39px;
        width: 6px;
        height: 28px;
        background: #dc2626;
        clip-path: polygon(50% 0%, 100% 100%, 0% 100%);
    }

    .compass-center {
        position: absolute;
        top: 35px;
        left: 35px;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        background: #cbd5e1;
        border: 2px solid #475569;
    }

    .road {
        position: absolute;
        z-index: 2;
        background: linear-gradient(180deg, #a9662e 0%, #925420 100%);
        box-shadow: inset 0 0 0 1px rgba(255,255,255,.08), 0 4px 10px rgba(0,0,0,.08);
    }

    .road::after {
        content: "";
        position: absolute;
        inset: 0;
        background: repeating-linear-gradient(
            90deg,
            transparent 0px,
            transparent 30px,
            rgba(255,255,255,.15) 30px,
            rgba(255,255,255,.15) 36px
        );
        opacity: .35;
    }

    .road-top {
        top: 74px;
        left: 220px;
        width: 58%;
        height: 56px;
        border-radius: 16px;
    }

    .road-right {
        top: 0;
        right: 120px;
        width: 58px;
        height: 100%;
    }

    .road-bottom {
        bottom: 85px;
        left: 0;
        width: 70%;
        height: 44px;
        border-radius: 0 18px 18px 0;
    }

    .feature-label {
        position: absolute;
        z-index: 3;
        padding: 8px 14px;
        background: rgba(255,255,255,.85);
        color: #374151;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
        box-shadow: 0 4px 10px rgba(0,0,0,.08);
    }

    .label-road-top {
        top: 140px;
        left: 430px;
    }

    .gazebo-card {
        position: absolute;
        top: 190px;
        right: 24px;
        width: 180px;
        padding: 14px 10px;
        border-radius: 20px;
        background: rgba(255,255,255,.22);
        backdrop-filter: blur(4px);
        text-align: center;
        z-index: 2;
        box-shadow: 0 10px 22px rgba(0,0,0,.08);
    }

    .gazebo-icon {
        position: relative;
        width: 140px;
        height: 92px;
        margin: 0 auto;
    }

    .gazebo-icon .roof {
        position: absolute;
        top: 0;
        left: 0;
        width: 140px;
        height: 42px;
        background: #111827;
        clip-path: polygon(50% 0%, 100% 100%, 0% 100%);
    }

    .gazebo-icon .base {
        position: absolute;
        bottom: 0;
        left: 16px;
        width: 108px;
        height: 8px;
        background: #111827;
    }

    .gazebo-icon .pillar {
        position: absolute;
        bottom: 8px;
        width: 8px;
        height: 48px;
        background: #111827;
    }

    .gazebo-icon .p1 { left: 26px; }
    .gazebo-icon .p2 { left: 42px; }
    .gazebo-icon .p3 { right: 42px; }
    .gazebo-icon .p4 { right: 26px; }

    .gazebo-label {
        margin-top: 10px;
        font-size: 12px;
        font-weight: 800;
        color: #111827;
    }

    .tree-cluster {
        position: absolute;
        z-index: 2;
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        width: 90px;
    }

    .cluster-top-left {
        top: 210px;
        left: 26px;
    }

    .cluster-top-right {
        top: 160px;
        right: 26px;
    }

    .cluster-bottom-left {
        bottom: 120px;
        left: 26px;
    }

    .cluster-bottom-right {
        bottom: 120px;
        right: 26px;
    }

    .tree {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: radial-gradient(circle at 35% 35%, #d9f99d 0%, #84cc16 35%, #4d7c0f 70%, #365314 100%);
        box-shadow: 0 4px 10px rgba(0,0,0,.18);
    }

    .grave-zone-heading {
        position: absolute;
        top: 230px;
        left: 170px;
        z-index: 4;
        padding: 14px 18px;
        border-radius: 18px;
        background: rgba(255,255,255,.16);
        border: 1px solid rgba(255,255,255,.18);
        box-shadow: 0 8px 20px rgba(0,0,0,.08);
    }

    .grave-zone-title {
        font-size: 17px;
        font-weight: 800;
        color: #ffffff;
        text-shadow: 0 1px 4px rgba(0,0,0,.18);
    }

    .grave-zone-subtitle {
        font-size: 13px;
        color: rgba(255,255,255,.9);
        margin-top: 2px;
    }

    .grave-zone {
        position: absolute;
        top: 330px;
        left: 170px;
        width: 1180px;
        z-index: 4;
        display: flex;
        flex-direction: column;
        gap: 34px;
    }

    .grave-row {
        display: grid;
        grid-template-columns: repeat(10, minmax(58px, 1fr));
        gap: 18px 16px;
    }

    .grave-label {
        display: block;
        margin: 0;
        cursor: pointer;
    }

    .grave-radio {
        display: none;
    }

    .grave-item {
        text-align: center;
        transition: transform .18s ease, opacity .18s ease;
        padding-bottom: 2px;
    }

    .grave-label:hover .grave-item.available {
        transform: translateY(-3px);
    }

    .grave-shape {
        width: 44px;
        height: 90px;
        margin: 0 auto 10px;
        border-radius: 10px;
        border: 1px solid #8fa4b8;
        background:
            linear-gradient(180deg, rgba(255,255,255,.88), rgba(226,232,240,.98)),
            repeating-linear-gradient(
                90deg,
                #dce6ef 0px,
                #dce6ef 5px,
                #b9c6d2 5px,
                #b9c6d2 6px
            );
        box-shadow:
            0 4px 10px rgba(0,0,0,.14),
            inset 0 0 0 1px rgba(255,255,255,.5);
        position: relative;
    }

    .grave-shape::after {
        content: "";
        position: absolute;
        top: 10px;
        bottom: 10px;
        left: 16px;
        width: 11px;
        border-radius: 18px;
        transform: rotate(8deg);
        background: repeating-linear-gradient(
            180deg,
            rgba(71,85,105,.38) 0px,
            rgba(71,85,105,.38) 1px,
            transparent 1px,
            transparent 7px
        );
    }

    .grave-code {
        display: inline-block;
        min-width: 56px;
        padding: 4px 9px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 800;
        color: #111827;
        background: rgba(255,255,255,.94);
        box-shadow: 0 2px 6px rgba(0,0,0,.08);
        line-height: 1.2;
    }

    .grave-item.occupied {
        opacity: .5;
        cursor: not-allowed;
    }

    .grave-item.occupied .grave-shape {
        background:
            linear-gradient(180deg, #efe2e2, #f6d4d4),
            repeating-linear-gradient(
                90deg,
                #eccaca 0px,
                #eccaca 5px,
                #dcaaaa 5px,
                #dcaaaa 6px
            );
    }

    .grave-item.occupied .grave-code {
        background: #fee2e2;
        color: #b91c1c;
    }

    .grave-radio:checked + .grave-item .grave-shape {
        outline: 4px solid #2563eb;
        outline-offset: 3px;
        box-shadow: 0 0 0 8px rgba(37,99,235,.18), 0 4px 10px rgba(0,0,0,.14);
    }

    .grave-radio:checked + .grave-item .grave-code {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .map-footer-note {
        font-size: 13px;
        color: #6b7280;
        margin-top: 14px;
    }

    @media (max-width: 1399px) {
        .map-scroll-area {
            height: 680px;
        }
    }

    @media (max-width: 1199px) {
        .sidebar-stack {
            gap: 16px;
        }

        .map-scroll-area {
            height: 620px;
        }
    }

    @media (max-width: 767px) {
        .map-toolbar {
            flex-direction: column;
            align-items: stretch;
        }

        .toolbar-right {
            justify-content: space-between;
        }

        .map-help-bar {
            flex-direction: column;
        }
    }

    .fullview-btn {
    font-size: 13px;
    font-weight: 700;
}

.map-shell.fullscreen-mode {
    position: fixed !important;
    inset: 0;
    width: 100vw !important;
    height: 100vh !important;
    z-index: 9999;
    margin: 0 !important;
    border-radius: 0 !important;
    background: #ffffff;
}

.map-shell.fullscreen-mode .card-header {
    padding: 16px 20px;
}

.map-shell.fullscreen-mode .card-body {
    height: calc(100vh - 88px);
    display: flex;
    flex-direction: column;
    padding: 16px 20px 20px;
}

.map-shell.fullscreen-mode .map-frame {
    flex: 1;
    height: 100%;
    padding: 10px;
    border-radius: 18px;
}

.map-shell.fullscreen-mode .map-scroll-area {
    height: 100% !important;
    min-height: 100% !important;
}

body.map-fullscreen-active {
    overflow: hidden;
}

body.map-fullscreen-active .page-header-breadcrumb,
body.map-fullscreen-active .col-xl-3 {
    display: none !important;
}

body.map-fullscreen-active .col-xl-9 {
    width: 100% !important;
}

body.map-fullscreen-active .container-fluid.cemetery-page {
    max-width: 100% !important;
    padding: 0 !important;
}

@media (max-width: 767px) {
    .map-shell.fullscreen-mode .toolbar-right {
        justify-content: flex-start;
    }

    .map-shell.fullscreen-mode .toolbar-controls {
        flex-wrap: wrap;
        border-radius: 18px;
    }
}
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const radios = document.querySelectorAll('.grave-radio');
        const selectedLotText = document.getElementById('selectedLotText');

        radios.forEach(radio => {
            radio.addEventListener('change', function () {
                const plotCode = this.dataset.code;
                if (selectedLotText && plotCode) {
                    selectedLotText.textContent = plotCode;
                }
            });
        });

        const checked = document.querySelector('.grave-radio:checked');
        if (checked && selectedLotText) {
            selectedLotText.textContent = checked.dataset.code;
        }

        const mapViewer = document.getElementById('mapViewer');
        const cemeteryMap = document.getElementById('cemeteryMap');
        const zoomInBtn = document.getElementById('zoomInBtn');
        const zoomOutBtn = document.getElementById('zoomOutBtn');
        const resetZoomBtn = document.getElementById('resetZoomBtn');
        const zoomLevel = document.getElementById('zoomLevel');
        const fullViewBtn = document.getElementById('fullViewBtn');
        const mapFullContainer = document.getElementById('mapFullContainer');

        let scale = 1;
        const minScale = 0.65;
        const maxScale = 2.2;
        const step = 0.15;

        function setFullViewButtonLabel() {
            if (!fullViewBtn || !mapFullContainer) return;

            const isFullscreen =
                document.fullscreenElement === mapFullContainer ||
                mapFullContainer.classList.contains('fullscreen-mode');

            fullViewBtn.innerHTML = isFullscreen
                ? '<i class="bx bx-exit-fullscreen"></i><span>Tutup</span>'
                : '<i class="bx bx-fullscreen"></i><span>Full View</span>';
        }

        async function enterFullView() {
            if (!mapFullContainer) return;

            mapFullContainer.classList.add('fullscreen-mode');
            document.body.classList.add('map-fullscreen-active');

            try {
                if (mapFullContainer.requestFullscreen) {
                    await mapFullContainer.requestFullscreen();
                }
            } catch (error) {
                console.log('Fullscreen API tidak disokong, guna CSS fullscreen sahaja.');
            }

            setFullViewButtonLabel();
        }

        async function exitFullView() {
            if (!mapFullContainer) return;

            try {
                if (document.fullscreenElement) {
                    await document.exitFullscreen();
                }
            } catch (error) {
                console.log('Keluar fullscreen biasa.');
            }

            mapFullContainer.classList.remove('fullscreen-mode');
            document.body.classList.remove('map-fullscreen-active');

            setFullViewButtonLabel();
        }

        async function toggleFullView() {
            if (!mapFullContainer) return;

            const isFullscreen =
                document.fullscreenElement === mapFullContainer ||
                mapFullContainer.classList.contains('fullscreen-mode');

            if (isFullscreen) {
                await exitFullView();
            } else {
                await enterFullView();
            }
        }

        function applyZoom() {
            if (!cemeteryMap) return;

            cemeteryMap.style.transform = `scale(${scale})`;

            if (zoomLevel) {
                zoomLevel.textContent = `${Math.round(scale * 100)}%`;
            }
        }

        function zoomIn() {
            scale = Math.min(scale + step, maxScale);
            applyZoom();
        }

        function zoomOut() {
            scale = Math.max(scale - step, minScale);
            applyZoom();
        }

        function resetZoom() {
            scale = 1;
            applyZoom();

            if (mapViewer) {
                mapViewer.scrollTo({
                    top: 0,
                    left: 0,
                    behavior: 'smooth'
                });
            }
        }

        if (zoomInBtn) zoomInBtn.addEventListener('click', zoomIn);
        if (zoomOutBtn) zoomOutBtn.addEventListener('click', zoomOut);
        if (resetZoomBtn) resetZoomBtn.addEventListener('click', resetZoom);
        if (fullViewBtn) fullViewBtn.addEventListener('click', toggleFullView);

        document.addEventListener('fullscreenchange', function () {
            if (!mapFullContainer) return;

            const active = document.fullscreenElement === mapFullContainer;

            if (active) {
                mapFullContainer.classList.add('fullscreen-mode');
                document.body.classList.add('map-fullscreen-active');
            } else {
                mapFullContainer.classList.remove('fullscreen-mode');
                document.body.classList.remove('map-fullscreen-active');
            }

            setFullViewButtonLabel();
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && mapFullContainer) {
                mapFullContainer.classList.remove('fullscreen-mode');
                document.body.classList.remove('map-fullscreen-active');
                setFullViewButtonLabel();
            }
        });

        let isDown = false;
        let startX = 0;
        let startY = 0;
        let scrollLeft = 0;
        let scrollTop = 0;

        if (mapViewer) {
            mapViewer.addEventListener('mousedown', function (e) {
                isDown = true;
                mapViewer.classList.add('dragging');
                startX = e.pageX - mapViewer.offsetLeft;
                startY = e.pageY - mapViewer.offsetTop;
                scrollLeft = mapViewer.scrollLeft;
                scrollTop = mapViewer.scrollTop;
            });

            mapViewer.addEventListener('mouseleave', function () {
                isDown = false;
                mapViewer.classList.remove('dragging');
            });

            mapViewer.addEventListener('mouseup', function () {
                isDown = false;
                mapViewer.classList.remove('dragging');
            });

            mapViewer.addEventListener('mousemove', function (e) {
                if (!isDown) return;
                e.preventDefault();

                const x = e.pageX - mapViewer.offsetLeft;
                const y = e.pageY - mapViewer.offsetTop;
                const walkX = (x - startX) * 1.2;
                const walkY = (y - startY) * 1.2;

                mapViewer.scrollLeft = scrollLeft - walkX;
                mapViewer.scrollTop = scrollTop - walkY;
            });

            mapViewer.addEventListener('wheel', function (e) {
                if (!e.ctrlKey) return;
                e.preventDefault();

                if (e.deltaY < 0) {
                    zoomIn();
                } else {
                    zoomOut();
                }
            }, { passive: false });
        }

        setFullViewButtonLabel();
        applyZoom();
    });
</script>
@endsection