@extends('layouts.app')

@section('content')

@php
    $zoneMapConfig = [
        'K' => [
            'class' => 'zone-kanak',
            'label' => 'Zon Kanak-kanak',
            'col' => 94,
            'svg_width' => 1750,
            'svg_height' => 1850,
            'grave_start_x' => 150,
            'grave_start_y' => 430,
            'row_gap' => 160,
            'right_road_x' => 1580,
            'main_road_width' => 1280,
            'road_center_x' => null,
        ],
        'P' => [
            'class' => 'zone-perempuan',
            'label' => 'Zon Perempuan',
            'col' => 88,
            'svg_width' => 2250,
            'svg_height' => 2650,
            'grave_start_x' => 150,
            'grave_start_y' => 440,
            'row_gap' => 160,
            'right_road_x' => 2050,
            'main_road_width' => 1750,
            'road_center_x' => null,
        ],
        'W' => [
            'class' => 'zone-perempuan',
            'label' => 'Zon Perempuan',
            'col' => 88,
            'svg_width' => 2250,
            'svg_height' => 2650,
            'grave_start_x' => 150,
            'grave_start_y' => 440,
            'row_gap' => 160,
            'right_road_x' => 2050,
            'main_road_width' => 1750,
            'road_center_x' => null,
        ],
        'L' => [
            'class' => 'zone-lelaki',
            'label' => 'Zon Lelaki',
            'col' => 80,
            'svg_width' => 5100,
            'svg_height' => 2300,
            'grave_start_x' => 180,
            'grave_start_y' => 440,
            'row_gap' => 160,
            'right_road_x' => null,
            'main_road_width' => 4550,
            'road_center_x' => 2550,
        ],
    ];

    $mapConfig = $zoneMapConfig[$zone] ?? $zoneMapConfig['L'];

    $graveWidth = 46;
    $graveHeight = 88;
    $graveTextOffsetY = 116;

    $graveStartX = $mapConfig['grave_start_x'];
    $graveStartY = $mapConfig['grave_start_y'];
    $graveCol = $mapConfig['col'];
    $graveRowGap = $mapConfig['row_gap'];
    $mainRoadWidth = $mapConfig['main_road_width'];

    /*
    |--------------------------------------------------------------------------
    | Kiraan Dinamik Pelan
    |--------------------------------------------------------------------------
    | Tujuan:
    | - Elak jalan kanan bertindih dengan lot paling kanan.
    | - Elak jalan bawah bertindih dengan row kubur terakhir.
    | - Tinggi dan lebar SVG ikut jumlah lot sebenar.
    */
    $totalRows = $plots->count();
    $maxRowPlots = 0;

    foreach ($plots as $rowPlots) {
        if ($zone === 'L') {
            $leftCount = $rowPlots->where('lot_number', '<=', 28)->count();
            $rightCount = $rowPlots->where('lot_number', '>', 28)->count();
            $maxRowPlots = max($maxRowPlots, $leftCount, $rightCount);
        } else {
            $maxRowPlots = max($maxRowPlots, $rowPlots->count());
        }
    }

    $lastRowY = $graveStartY + (max($totalRows - 1, 0) * $graveRowGap);
    $lastGraveLabelY = $lastRowY + $graveTextOffsetY;

    $bottomNoteY = $lastGraveLabelY + 95;
    $bottomRoadY = $bottomNoteY + 75;
    $topRoadStartX = 220;
    $bottomRoadStartX = 120;   
    $roadThickness = 62;
    $roadHalf = 31;

    $svgHeight = max($mapConfig['svg_height'], $bottomRoadY + 130);

    if ($zone !== 'L') {
        $lastPlotRightX = $graveStartX + (max($maxRowPlots - 1, 0) * $graveCol) + $graveWidth;

        /*
        | Jalan kanan sekurang-kurangnya 130px selepas lot terakhir.
        */
        $rightRoadX = max($mapConfig['right_road_x'], $lastPlotRightX + 80);

        /*
        | Tambah ruang kanan supaya kompas, pokok dan pondok tidak melekat pada jalan.
        */
        $rightFeatureX = $rightRoadX + 105;
        $compassX = $rightRoadX + 115;
        $pondokX = $rightRoadX + 105;
        $rightTreeX = $rightRoadX + 135;

        $svgWidth = max($mapConfig['svg_width'], $rightRoadX + 430);
} else {
    $rightRoadX = null;
    $rightFeatureX = null;
    $compassX = $mapConfig['svg_width'] - 235;
    $pondokX = null;
    $rightTreeX = null;
    $svgWidth = $mapConfig['svg_width'];
}

/*
|--------------------------------------------------------------------------
| Lokasi SVG bagi lot yang dipilih
|--------------------------------------------------------------------------
| Digunakan untuk menunjukkan laluan rujukan daripada pintu masuk
| menuju lot pusara yang dipilih.
*/
$selectedX = $graveStartX;
$selectedY = $graveStartY;

$selectedRowIndex = $plots->keys()->search($selectedPlot->row_number);
$selectedRowIndex = $selectedRowIndex === false ? 0 : $selectedRowIndex;

$selectedY = $graveStartY + ($selectedRowIndex * $graveRowGap);

$selectedRowPlots = $plots->get($selectedPlot->row_number, collect())->values();

if ($zone === 'L') {
    if ($selectedPlot->lot_number <= 28) {
        $selectedPosition = $selectedRowPlots
            ->where('lot_number', '<=', 28)
            ->values()
            ->search(fn ($plot) => $plot->id === $selectedPlot->id);

        $selectedPosition = $selectedPosition === false ? 0 : $selectedPosition;
        $selectedX = $graveStartX + ($selectedPosition * $graveCol);
    } else {
        $selectedPosition = $selectedRowPlots
            ->where('lot_number', '>', 28)
            ->values()
            ->search(fn ($plot) => $plot->id === $selectedPlot->id);

        $selectedPosition = $selectedPosition === false ? 0 : $selectedPosition;
        $selectedX = $mapConfig['road_center_x'] + 220 + ($selectedPosition * $graveCol);
    }
} else {
        $selectedPosition = $selectedRowPlots
            ->search(fn ($plot) => $plot->id === $selectedPlot->id);

        $selectedPosition = $selectedPosition === false ? 0 : $selectedPosition;
        $selectedX = $graveStartX + ($selectedPosition * $graveCol);
    }

    /*
    |--------------------------------------------------------------------------
    | Maklumat baris lot
    |--------------------------------------------------------------------------
    */
    $plotParts = explode('-', $selectedPlot->plot_code ?? '');
    $selectedRowLabel = $plotParts[1] ?? ('Baris ' . $selectedPlot->row_number);

    /*
    |--------------------------------------------------------------------------
    | Kedudukan pintu masuk dan laluan panduan
    |--------------------------------------------------------------------------
    | Pintu masuk diletakkan pada bahagian kiri atas pelan.
    | Pastikan kedudukan ini bersesuaian dengan lokasi sebenar perkuburan.
    */
    $entranceX = $topRoadStartX + 18;
    $entranceY = 109;

    $guideLaneX = $graveStartX - 55;
    $guideTargetX = $selectedX + ($graveWidth / 2);
    $guideTargetY = $selectedY - 28;
    @endphp

<div class="container-fluid cemetery-page">

    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <h1 class="page-title fw-semibold fs-22 mb-1">Peta Lot Kubur</h1>
            <p class="text-muted mb-0">
                Lot kubur si mati ditandakan pada peta kawasan perkuburan.
            </p>
        </div>

        <div class="mt-3 mt-md-0">
            <a href="{{ route('user.grave-locations.index') }}" class="btn btn-light border rounded-pill px-3">
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

    <div class="row g-4">

        <div class="col-xl-3">
            <div class="sidebar-stack">

                {{-- Maklumat Si Mati --}}
                <div class="card border-0 shadow-sm cemetery-card">
                    <div class="card-body">
                        <div class="panel-title-wrap">
                            <span class="panel-icon"><i class="bx bx-user"></i></span>
                            <div>
                                <div class="panel-title">Maklumat Si Mati</div>
                                <div class="panel-subtitle">Butiran ringkas lokasi kubur</div>
                            </div>
                        </div>

                        <div class="info-group">
                            <div class="info-label">Nama Si Mati</div>
                            <div class="info-value">{{ $deathReport->nama_si_mati }}</div>
                        </div>

                        <div class="info-group">
                            <div class="info-label">Tarikh Meninggal</div>
                            <div class="info-value">
                                {{ $deathReport->tarikh_meninggal ? $deathReport->tarikh_meninggal->format('d/m/Y') : '-' }}
                            </div>
                        </div>

                        <div class="info-group">
                            <div class="info-label">Tarikh Kebumi</div>
                            <div class="info-value">
                                {{ $deathReport->final_burial_date ? \Carbon\Carbon::parse($deathReport->final_burial_date)->format('d/m/Y') : '-' }}
                            </div>
                        </div>

                        <div class="info-group">
                            <div class="info-label">Zon Dipaparkan</div>
                            <div class="info-value">
                                <span class="zone-chip">{{ $mapConfig['label'] }}</span>
                            </div>
                        </div>

                        <div class="info-group mb-0">
                            <div class="info-label">Kod Lot Kubur</div>
                            <div class="selected-lot-box user-selected-lot">
                                {{ $selectedPlot->plot_code ?? '-' }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Panduan Ke Lot Pusara --}}
<div class="card border-0 shadow-sm cemetery-card">
    <div class="card-body">
        <div class="panel-title-wrap">
            <span class="panel-icon blue"><i class="bx bx-walk"></i></span>
            <div>
                <div class="panel-title">Panduan Ke Lot Pusara</div>
                <div class="panel-subtitle">Bermula dari pintu masuk utama</div>
            </div>
        </div>

        <div class="route-step-list">
            <div class="route-step-item">
                <span class="route-step-number blue">1</span>
                <div>
                    <div class="route-step-title">Pintu Masuk Utama</div>
                    <div class="route-step-note">Anda bermula dari sini</div>
                </div>
            </div>

            <div class="route-step-item">
                <span class="route-step-number blue">2</span>
                <div>
                    <div class="route-step-title">Rujuk Laluan Panduan</div>
                    <div class="route-step-note">Ikuti garisan biru menuju kawasan lot</div>
                </div>
            </div>

            <div class="route-step-item">
                <span class="route-step-number green">3</span>
                <div>
                    <div class="route-step-title">Cari Baris {{ $selectedRowLabel }}</div>
                    <div class="route-step-note">Lot hijau ialah destinasi anda</div>
                </div>
            </div>
        </div>

        <div class="selected-lot-box user-selected-lot mt-3">
            <i class="bx bx-map-pin me-1"></i>
            {{ $selectedPlot->plot_code ?? '-' }}
        </div>

        <div class="selected-note">
            Garisan biru pada pelan digunakan sebagai panduan arah dalam kawasan perkuburan.
        </div>

        @if(!empty($selectedPlot->grave_image))
            <button type="button"
                    class="btn btn-success w-100 rounded-pill py-2 mt-3"
                    data-bs-toggle="modal"
                    data-bs-target="#graveImageModal">
                <i class="bx bx-image me-1"></i> Lihat Gambar Kubur
            </button>
        @endif
    </div>
</div>

                {{-- Petunjuk --}}
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
                                <span class="entrance-legend"></span>
                                <span>Pintu Masuk Utama</span>
                            </div>

                            <div class="legend-item">
                                <span class="route-legend"></span>
                                <span>Laluan Rujukan</span>
                            </div>

                            <div class="legend-item">
                                <span class="legend-box selected-box-user"></span>
                                <span>Lokasi Kubur Si Mati</span>
                            </div>

                            <div class="legend-item">
                                <span class="legend-box disabled-box-user"></span>
                                <span>Lot Lain / Rujukan</span>
                            </div>

                            <div class="legend-item">
                                <span class="legend-line road-box"></span>
                                <span>Jalan Laluan</span>
                            </div>

                            <div class="legend-item">
                                <span class="legend-circle tree-box"></span>
                                <span>Kawasan Pokok</span>
                            </div>

                            <div class="legend-item">
                                <span class="legend-box pondok-box"></span>
                                <span>Pondok Rehat</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- Peta --}}
        <div class="col-xl-9">
            <div class="card border-0 shadow-sm cemetery-card map-shell" id="mapFullContainer">

                <div class="card-header map-toolbar border-0 bg-white">
                    <div class="toolbar-left">
                        <div class="toolbar-title">Pelan Kawasan Perkuburan</div>
                        <div class="toolbar-subtitle">
                            Gunakan zoom, drag dan full view untuk melihat kedudukan lot dengan lebih jelas.
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
                            <div class="map-stage map-stage-{{ $zone }}">
                                <div class="cemetery-map {{ $mapConfig['class'] }}" id="cemeteryMap">

                                    <svg class="cemetery-svg"
                                         xmlns="http://www.w3.org/2000/svg"
                                         viewBox="0 0 {{ $svgWidth }} {{ $svgHeight }}"
                                         width="{{ $svgWidth }}"
                                         height="{{ $svgHeight }}"
                                         role="img"
                                         aria-label="Pelan lot kubur {{ $mapConfig['label'] }}">

                                        <defs>
                                            <linearGradient id="grassGradient" x1="0" y1="0" x2="0" y2="1">
                                                <stop offset="0%" stop-color="#8fbd68"/>
                                                <stop offset="45%" stop-color="#78aa55"/>
                                                <stop offset="100%" stop-color="#679a48"/>
                                            </linearGradient>

                                            <linearGradient id="roadGradient" x1="0" y1="0" x2="0" y2="1">
                                                <stop offset="0%" stop-color="#b87943"/>
                                                <stop offset="55%" stop-color="#955a28"/>
                                                <stop offset="100%" stop-color="#7c461d"/>
                                            </linearGradient>

                                            <linearGradient id="graveGrey" x1="0" y1="0" x2="0" y2="1">
                                                <stop offset="0%" stop-color="#f1f5f9"/>
                                                <stop offset="100%" stop-color="#cbd5e1"/>
                                            </linearGradient>

                                            <linearGradient id="graveGreen" x1="0" y1="0" x2="0" y2="1">
                                                <stop offset="0%" stop-color="#dcfce7"/>
                                                <stop offset="100%" stop-color="#86efac"/>
                                            </linearGradient>

                                            <linearGradient id="pondokRoof" x1="0" y1="0" x2="0" y2="1">
                                                <stop offset="0%" stop-color="#1f2937"/>
                                                <stop offset="100%" stop-color="#020617"/>
                                            </linearGradient>

                                            <filter id="softShadow" x="-30%" y="-30%" width="160%" height="160%">
                                                <feDropShadow dx="0" dy="8" stdDeviation="8" flood-color="#000000" flood-opacity="0.16"/>
                                            </filter>

                                            <filter id="selectedGlow" x="-70%" y="-70%" width="240%" height="240%">
                                                <feDropShadow dx="0" dy="0" stdDeviation="10" flood-color="#16a34a" flood-opacity="0.75"/>
                                                <feDropShadow dx="0" dy="8" stdDeviation="8" flood-color="#000000" flood-opacity="0.20"/>
                                            </filter>

                                            <pattern id="grassTexture" width="80" height="80" patternUnits="userSpaceOnUse">
                                                <circle cx="12" cy="14" r="2" fill="#ffffff" opacity="0.09"/>
                                                <circle cx="52" cy="26" r="2.5" fill="#ffffff" opacity="0.08"/>
                                                <circle cx="38" cy="62" r="1.8" fill="#245c22" opacity="0.12"/>
                                                <path d="M4 72 C18 60, 32 80, 48 66 S76 68, 80 58"
                                                      fill="none"
                                                      stroke="#ffffff"
                                                      stroke-width="1"
                                                      opacity="0.06"/>
                                            </pattern>

                                            <pattern id="roadMarkingHorizontal" width="80" height="56" patternUnits="userSpaceOnUse">
                                                <rect x="0" y="0" width="80" height="56" fill="transparent"/>
                                                <rect x="22" y="25" width="24" height="5" rx="2" fill="#ffffff" opacity="0.22"/>
                                            </pattern>

                                            <pattern id="roadMarkingVertical" width="58" height="90" patternUnits="userSpaceOnUse">
                                                <rect x="0" y="0" width="58" height="90" fill="transparent"/>
                                                <rect x="26" y="24" width="5" height="28" rx="2" fill="#ffffff" opacity="0.22"/>
                                            </pattern>
                                        </defs>

                                        {{-- Background --}}
                                        <rect x="0"
                                              y="0"
                                              width="{{ $svgWidth }}"
                                              height="{{ $svgHeight }}"
                                              rx="34"
                                              fill="url(#grassGradient)"/>

                                        <rect x="0"
                                              y="0"
                                              width="{{ $svgWidth }}"
                                              height="{{ $svgHeight }}"
                                              rx="34"
                                              fill="url(#grassTexture)"/>

                                        {{-- Border kawasan --}}
                                        <rect x="10"
                                              y="10"
                                              width="{{ $svgWidth - 20 }}"
                                              height="{{ $svgHeight - 20 }}"
                                              rx="28"
                                              fill="none"
                                              stroke="#dce9d2"
                                              stroke-width="4"
                                              opacity="0.55"/>

                                        {{-- Jalan --}}
                                        <g class="svg-roads" filter="url(#softShadow)">

                                            @if($zone !== 'L')
                                                {{-- Jalan atas --}}
                                                <rect x="{{ $topRoadStartX }}"
                                                    y="78"
                                                    width="{{ ($rightRoadX + $roadHalf) - $topRoadStartX }}"
                                                    height="62"
                                                    rx="20"
                                                    fill="url(#roadGradient)"/>

                                                <rect x="{{ $topRoadStartX }}"
                                                    y="78"
                                                    width="{{ ($rightRoadX + $roadHalf) - $topRoadStartX }}"
                                                    height="62"
                                                    rx="20"
                                                    fill="url(#roadMarkingHorizontal)"
                                                    opacity="0.8"/>

                                                {{-- Jalan kanan --}}
                                                <rect x="{{ $rightRoadX }}"
                                                    y="78"
                                                    width="62"
                                                    height="{{ ($bottomRoadY + $roadHalf) - 78 }}"
                                                    rx="20"
                                                    fill="url(#roadGradient)"/>

                                                <rect x="{{ $rightRoadX }}"
                                                    y="78"
                                                    width="62"
                                                    height="{{ ($bottomRoadY + $roadHalf) - 78 }}"
                                                    rx="20"
                                                    fill="url(#roadMarkingVertical)"
                                                    opacity="0.8"/>

                                                {{-- Jalan bawah (jalan mati) --}}
                                                <rect x="{{ $bottomRoadStartX }}"
                                                    y="{{ $bottomRoadY }}"
                                                    width="{{ ($rightRoadX + $roadHalf) - $bottomRoadStartX }}"
                                                    height="52"
                                                    rx="20"
                                                    fill="url(#roadGradient)"/>

                                                <rect x="{{ $bottomRoadStartX }}"
                                                    y="{{ $bottomRoadY }}"
                                                    width="{{ ($rightRoadX + $roadHalf) - $bottomRoadStartX }}"
                                                    height="52"
                                                    rx="20"
                                                    fill="url(#roadMarkingHorizontal)"
                                                    opacity="0.8"/>

                                            @else
                                                {{-- Zon lelaki kekalkan asal --}}
                                                <rect x="220"
                                                    y="78"
                                                    width="{{ $mainRoadWidth }}"
                                                    height="58"
                                                    rx="18"
                                                    fill="url(#roadGradient)"/>

                                                <rect x="220"
                                                    y="78"
                                                    width="{{ $mainRoadWidth }}"
                                                    height="58"
                                                    rx="18"
                                                    fill="url(#roadMarkingHorizontal)"
                                                    opacity="0.8"/>

                                                <rect x="{{ $mapConfig['road_center_x'] }}"
                                                    y="350"
                                                    width="96"
                                                    height="{{ $svgHeight - 500 }}"
                                                    rx="22"
                                                    fill="url(#roadGradient)"/>

                                                <rect x="{{ $mapConfig['road_center_x'] }}"
                                                    y="350"
                                                    width="96"
                                                    height="{{ $svgHeight - 500 }}"
                                                    rx="22"
                                                    fill="url(#roadMarkingVertical)"
                                                    opacity="0.8"/>
                                            @endif

                                        </g>

                                        {{-- Laluan rujukan dari pintu masuk ke lot dipilih --}}
                                        <g class="svg-route-guide">

                                            <path d="
                                                M {{ $entranceX }} {{ $entranceY }}
                                                L {{ $guideLaneX }} {{ $entranceY }}
                                                L {{ $guideLaneX }} {{ $guideTargetY }}
                                                L {{ $guideTargetX }} {{ $guideTargetY }}
                                            "
                                            class="svg-guide-route-outline"/>

                                            <path d="
                                                M {{ $entranceX }} {{ $entranceY }}
                                                L {{ $guideLaneX }} {{ $entranceY }}
                                                L {{ $guideLaneX }} {{ $guideTargetY }}
                                                L {{ $guideTargetX }} {{ $guideTargetY }}
                                            "
                                            class="svg-guide-route"/>

                                            <polygon points="
                                                {{ $guideTargetX - 12 }},{{ $guideTargetY - 8 }}
                                                {{ $guideTargetX + 12 }},{{ $guideTargetY - 8 }}
                                                {{ $guideTargetX }},{{ $guideTargetY + 13 }}
                                            "
                                            class="svg-guide-arrow"/>

                                            <g transform="translate({{ $guideLaneX + 18 }}, {{ max($guideTargetY - 95, 205) }})">
                                                <rect x="0"
                                                    y="0"
                                                    width="170"
                                                    height="40"
                                                    rx="20"
                                                    class="svg-guide-label-card"/>

                                                <text x="85"
                                                    y="26"
                                                    text-anchor="middle"
                                                    class="svg-guide-label">
                                                    Laluan Rujukan
                                                </text>
                                            </g>
                                        </g>

                                        {{-- Pintu Masuk Utama --}}
                                        <g class="svg-entrance"
                                        transform="translate({{ $entranceX - 28 }}, {{ $entranceY - 28 }})"
                                        filter="url(#softShadow)">

                                            <circle cx="28"
                                                    cy="28"
                                                    r="27"
                                                    class="svg-entrance-marker"/>

                                            <rect x="19"
                                                y="15"
                                                width="18"
                                                height="25"
                                                rx="2"
                                                class="svg-entrance-door"/>

                                            <circle cx="32"
                                                    cy="28"
                                                    r="2"
                                                    fill="#2563eb"/>
                                        </g>

                                        {{-- Label Pintu Masuk --}}
                                        <g transform="translate({{ $entranceX + 48 }}, {{ $entranceY - 35 }})"
                                        filter="url(#softShadow)">
                                            <rect x="0"
                                                y="0"
                                                width="245"
                                                height="68"
                                                rx="16"
                                                class="svg-entrance-card"/>

                                            <text x="16"
                                                y="28"
                                                class="svg-entrance-title">
                                                PINTU MASUK UTAMA
                                            </text>

                                            <text x="16"
                                                y="51"
                                                class="svg-entrance-subtitle">
                                                Anda bermula dari sini
                                            </text>
                                        </g>

                                        {{-- Label jalan --}}
                                        <g class="svg-map-label">
                                            <rect x="520"
                                                  y="150"
                                                  width="150"
                                                  height="38"
                                                  rx="19"
                                                  fill="#ffffff"
                                                  opacity="0.88"/>
                                            <text x="595"
                                                  y="174"
                                                  text-anchor="middle"
                                                  class="svg-small-label">
                                                Laluan Utama
                                            </text>
                                        </g>

                                        @if($zone === 'L')
                                            <g class="svg-map-label">
                                                <rect x="{{ $mapConfig['road_center_x'] - 26 }}"
                                                      y="275"
                                                      width="150"
                                                      height="38"
                                                      rx="19"
                                                      fill="#ffffff"
                                                      opacity="0.88"/>
                                                <text x="{{ $mapConfig['road_center_x'] + 49 }}"
                                                      y="299"
                                                      text-anchor="middle"
                                                      class="svg-small-label">
                                                    Laluan Tengah
                                                </text>
                                            </g>
                                        @endif

                                        {{-- Kiblat --}}
                                        <g class="svg-qiblat" transform="translate(34, 34)" filter="url(#softShadow)">
                                            <rect x="0" y="0" width="72" height="146" rx="18" fill="#ffedd5"/>
                                            <text x="36"
                                                  y="78"
                                                  text-anchor="middle"
                                                  transform="rotate(-90 36 78)"
                                                  class="svg-qiblat-text">
                                                KIBLAT
                                            </text>

                                            <path d="M132 72 H92"
                                                  stroke="#111827"
                                                  stroke-width="8"
                                                  stroke-linecap="round"/>
                                            <path d="M91 72 L116 50 M91 72 L116 94"
                                                  fill="none"
                                                  stroke="#111827"
                                                  stroke-width="8"
                                                  stroke-linecap="round"
                                                  stroke-linejoin="round"/>
                                        </g>

                                        {{-- Kompas --}}
                                        <g class="svg-compass"
                                            transform="translate({{ $zone !== 'L' ? $compassX : $svgWidth - 235 }}, 38)"
                                            filter="url(#softShadow)">
                                            <circle cx="70" cy="70" r="56" fill="rgba(255,255,255,0.42)" stroke="#334155" stroke-width="4"/>
                                            <circle cx="70" cy="70" r="5" fill="#334155"/>

                                            <text x="70" y="8" text-anchor="middle" class="svg-compass-text">N</text>
                                            <text x="70" y="143" text-anchor="middle" class="svg-compass-text">S</text>
                                            <text x="2" y="76" text-anchor="middle" class="svg-compass-text">W</text>
                                            <text x="138" y="76" text-anchor="middle" class="svg-compass-text">E</text>

                                            <path d="M70 22 L82 70 L70 62 L58 70 Z" fill="#dc2626"/>
                                            <path d="M70 118 L58 70 L70 78 L82 70 Z" fill="#475569"/>
                                        </g>

                                        {{-- Pokok --}}
                                        <g class="svg-trees" filter="url(#softShadow)">
                                            <g transform="translate(55, 230)">
                                                <circle cx="30" cy="24" r="24" fill="#65a30d"/>
                                                <circle cx="55" cy="18" r="22" fill="#84cc16"/>
                                                <circle cx="72" cy="40" r="25" fill="#4d7c0f"/>
                                                <rect x="49" y="48" width="14" height="36" rx="5" fill="#7c4a22"/>
                                            </g>

                                            <g transform="translate(52, {{ $bottomRoadY - 220 }})">
                                                <circle cx="30" cy="24" r="24" fill="#65a30d"/>
                                                <circle cx="55" cy="18" r="22" fill="#84cc16"/>
                                                <circle cx="72" cy="40" r="25" fill="#4d7c0f"/>
                                                <rect x="49" y="48" width="14" height="36" rx="5" fill="#7c4a22"/>
                                            </g>

                                            @if($zone !== 'L')
                                                <g transform="translate({{ $rightTreeX }}, 245)">
                                                    <circle cx="30" cy="24" r="24" fill="#65a30d"/>
                                                    <circle cx="55" cy="18" r="22" fill="#84cc16"/>
                                                    <circle cx="72" cy="40" r="25" fill="#4d7c0f"/>
                                                    <rect x="49" y="48" width="14" height="36" rx="5" fill="#7c4a22"/>
                                                </g>

                                                <g transform="translate({{ $rightTreeX }}, {{ $bottomRoadY - 245 }})">
                                                    <circle cx="30" cy="24" r="24" fill="#65a30d"/>
                                                    <circle cx="55" cy="18" r="22" fill="#84cc16"/>
                                                    <circle cx="72" cy="40" r="25" fill="#4d7c0f"/>
                                                    <rect x="49" y="48" width="14" height="36" rx="5" fill="#7c4a22"/>
                                                </g>
                                            @endif
                                        </g>

                                        {{-- Pondok --}}
                                        @if($zone !== 'L')
                                            <g class="svg-pondok"
                                                transform="translate({{ $pondokX }}, 390)"
                                                filter="url(#softShadow)">
                                                <rect x="0" y="0" width="165" height="138" rx="24" fill="rgba(255,255,255,0.22)"/>
                                                <polygon points="82,18 25,70 140,70" fill="url(#pondokRoof)"/>
                                                <rect x="40" y="70" width="85" height="48" rx="8" fill="#f8fafc" opacity="0.88"/>
                                                <rect x="50" y="74" width="9" height="44" rx="3" fill="#111827"/>
                                                <rect x="72" y="74" width="9" height="44" rx="3" fill="#111827"/>
                                                <rect x="94" y="74" width="9" height="44" rx="3" fill="#111827"/>
                                                <rect x="116" y="74" width="9" height="44" rx="3" fill="#111827"/>
                                                <rect x="35" y="118" width="96" height="9" rx="4" fill="#111827"/>
                                                <text x="82"
                                                      y="158"
                                                      text-anchor="middle"
                                                      class="svg-pondok-text">
                                                    Pondok Rehat
                                                </text>
                                            </g>
                                        @endif

                                        {{-- Tajuk kawasan --}}
                                        <g class="svg-zone-title" transform="translate(170, 250)">
                                            <rect x="0" y="0" width="395" height="82" rx="20" fill="rgba(255,255,255,0.18)" stroke="rgba(255,255,255,0.25)"/>
                                            <text x="24" y="34" class="svg-zone-title-text">
                                                Kawasan Lot Perkuburan
                                            </text>
                                            <text x="24" y="60" class="svg-zone-subtitle-text">
                                                Lot hijau menunjukkan lokasi kubur si mati
                                            </text>
                                        </g>

                                        {{-- Lot Kubur --}}
                                        <g class="svg-grave-zone">
                                            @foreach($plots as $row => $rowPlots)
                                                @php
                                                    $rowIndex = $loop->index;
                                                    $y = $graveStartY + ($rowIndex * $graveRowGap);
                                                @endphp

                                                @if($zone === 'L')
                                                    @php
                                                        $leftPlots = $rowPlots->where('lot_number', '<=', 28)->values();
                                                        $rightPlots = $rowPlots->where('lot_number', '>', 28)->values();

                                                        $graveSections = [
                                                            [
                                                                'plots' => $leftPlots,
                                                                'start_x' => $graveStartX,
                                                            ],
                                                            [
                                                                'plots' => $rightPlots,
                                                                'start_x' => $mapConfig['road_center_x'] + 220,
                                                            ],
                                                        ];
                                                    @endphp

                                                    @foreach($graveSections as $section)
                                                        @foreach($section['plots'] as $plot)
                                                            @php
                                                                $isSelected = $selectedPlot && $plot->id === $selectedPlot->id;
                                                                $x = $section['start_x'] + ($loop->index * $graveCol);
                                                            @endphp

                                                            <g class="svg-grave-item {{ $isSelected ? 'selected-user' : 'disabled-user' }}"
                                                               transform="translate({{ $x }}, {{ $y }})">

                                                                @if($isSelected)
                                                                    <circle cx="{{ $graveWidth / 2 }}"
                                                                            cy="{{ $graveHeight / 2 }}"
                                                                            r="58"
                                                                            class="svg-selected-ring"/>

                                                                    <g transform="translate({{ ($graveWidth / 2) - 18 }}, -74)" class="svg-location-pin">
                                                                        <path d="M18 0
                                                                                C8 0 0 8 0 18
                                                                                C0 32 18 48 18 48
                                                                                C18 48 36 32 36 18
                                                                                C36 8 28 0 18 0Z"
                                                                            class="svg-pin-body"/>
                                                                        <circle cx="18" cy="18" r="7" class="svg-pin-hole"/>
                                                                    </g>
                                                                @endif

                                                                <rect x="0"
                                                                      y="0"
                                                                      width="{{ $graveWidth }}"
                                                                      height="{{ $graveHeight }}"
                                                                      rx="13"
                                                                      class="svg-grave-body"
                                                                      filter="{{ $isSelected ? 'url(#selectedGlow)' : 'url(#softShadow)' }}"/>

                                                                <path d="M10 18 Q23 8 36 18"
                                                                      class="svg-grave-headline"/>

                                                                <path d="M22 22
                                                                         C14 34 14 58 22 76
                                                                         M29 22
                                                                         C37 34 37 58 29 76"
                                                                      class="svg-grave-line"/>

                                                                <rect x="-12"
                                                                      y="{{ $graveTextOffsetY - 18 }}"
                                                                      width="70"
                                                                      height="28"
                                                                      rx="14"
                                                                      class="svg-grave-code-bg"/>

                                                                <text x="{{ $graveWidth / 2 }}"
                                                                      y="{{ $graveTextOffsetY }}"
                                                                      text-anchor="middle"
                                                                      class="svg-grave-code">
                                                                    {{ $plot->plot_code }}
                                                                </text>
                                                            </g>
                                                        @endforeach
                                                    @endforeach
                                                @else
                                                    @foreach($rowPlots as $plot)
                                                        @php
                                                            $isSelected = $selectedPlot && $plot->id === $selectedPlot->id;
                                                            $x = $graveStartX + ($loop->index * $graveCol);
                                                        @endphp

                                                        <g class="svg-grave-item {{ $isSelected ? 'selected-user' : 'disabled-user' }}"
                                                           transform="translate({{ $x }}, {{ $y }})">

                                                            @if($isSelected)
                                                                <circle cx="{{ $graveWidth / 2 }}"
                                                                        cy="{{ $graveHeight / 2 }}"
                                                                        r="58"
                                                                        class="svg-selected-ring"/>

                                                                <g transform="translate({{ ($graveWidth / 2) - 18 }}, -58)" class="svg-location-pin">
                                                                    <path d="M18 0
                                                                            C8 0 0 8 0 18
                                                                            C0 32 18 48 18 48
                                                                            C18 48 36 32 36 18
                                                                            C36 8 28 0 18 0Z"
                                                                        class="svg-pin-body"/>
                                                                    <circle cx="18" cy="18" r="7" class="svg-pin-hole"/>
                                                                </g>
                                                            @endif

                                                            <rect x="0"
                                                                  y="0"
                                                                  width="{{ $graveWidth }}"
                                                                  height="{{ $graveHeight }}"
                                                                  rx="13"
                                                                  class="svg-grave-body"
                                                                  filter="{{ $isSelected ? 'url(#selectedGlow)' : 'url(#softShadow)' }}"/>

                                                            <path d="M10 18 Q23 8 36 18"
                                                                  class="svg-grave-headline"/>

                                                            <path d="M22 22
                                                                     C14 34 14 58 22 76
                                                                     M29 22
                                                                     C37 34 37 58 29 76"
                                                                  class="svg-grave-line"/>

                                                            <rect x="-12"
                                                                  y="{{ $graveTextOffsetY - 18 }}"
                                                                  width="70"
                                                                  height="28"
                                                                  rx="14"
                                                                  class="svg-grave-code-bg"/>

                                                            <text x="{{ $graveWidth / 2 }}"
                                                                  y="{{ $graveTextOffsetY }}"
                                                                  text-anchor="middle"
                                                                  class="svg-grave-code">
                                                                {{ $plot->plot_code }}
                                                            </text>
                                                        </g>
                                                    @endforeach
                                                @endif
                                            @endforeach
                                        </g>

                                        {{-- Nota bawah map --}}
                                        <g transform="translate(40, {{ $bottomNoteY }})">
                                            <rect x="0" y="-26" width="560" height="42" rx="21" fill="rgba(255,255,255,0.30)"/>
                                            <text x="24" y="0" class="svg-bottom-note">
                                                Paparan lokasi adalah untuk rujukan kedudukan lot kubur sahaja.
                                            </text>
                                        </g>

                                    </svg>

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="map-footer-note">
                        Paparan ini adalah untuk semakan lokasi sahaja. Sebarang kemaskini lot kubur hanya boleh dibuat oleh pentadbir.
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

@if(!empty($selectedPlot->grave_image))
    <div class="modal fade" id="graveImageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content rounded-4 border-0">
                <div class="modal-header border-0">
                    <div>
                        <h5 class="modal-title fw-bold">Gambar Kubur</h5>
                        <div class="text-muted small">
                            {{ $deathReport->nama_si_mati }} • {{ $selectedPlot->plot_code }}
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <img src="{{ asset('storage/' . $selectedPlot->grave_image) }}"
                         alt="Gambar Kubur"
                         class="img-fluid rounded-4 w-100"
                         style="max-height: 600px; object-fit: cover;">

                    @if(!empty($selectedPlot->grave_image_updated_at))
                        <div class="text-muted small mt-3">
                            Gambar dikemaskini pada:
                            {{ \Carbon\Carbon::parse($selectedPlot->grave_image_updated_at)->format('d/m/Y h:i A') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endif

<style>
    .svg-location-pin {
        filter: url(#softShadow);
    }

    .svg-pin-body {
        fill: #dc2626;
        stroke: #ffffff;
        stroke-width: 3;
    }

    .svg-pin-hole {
        fill: #ffffff;
    }

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

    .user-selected-lot {
        color: #166534;
        background: linear-gradient(135deg, #dcfce7 0%, #f0fdf4 100%);
        border: 1px solid #86efac;
    }

    .selected-note {
        font-size: 13px;
        color: var(--text-soft);
        line-height: 1.6;
        margin-top: 12px;
    }

    .route-step-list {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .route-step-item {
        display: flex;
        align-items: flex-start;
        gap: 10px;
    }

    .route-step-number {
        width: 27px;
        height: 27px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 12px;
        font-weight: 800;
    }

    .route-step-number.blue {
        background: #dbeafe;
        color: #2563eb;
    }

    .route-step-number.green {
        background: #dcfce7;
        color: #16a34a;
    }

    .route-step-title {
        font-size: 13px;
        font-weight: 800;
        color: #111827;
    }

    .route-step-note {
        font-size: 12px;
        color: #6b7280;
        line-height: 1.45;
        margin-top: 2px;
    }

    .entrance-legend {
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: #2563eb;
        border: 2px solid #dbeafe;
        display: inline-block;
        flex-shrink: 0;
    }

    .route-legend {
        width: 22px;
        height: 0;
        border-top: 3px dashed #2563eb;
        display: inline-block;
        flex-shrink: 0;
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

    .selected-box-user {
        background: #dcfce7;
        border: 1px solid #16a34a;
    }

    .disabled-box-user {
        background: #e5e7eb;
        border: 1px solid #9ca3af;
    }

    .pondok-box {
        background: #111827;
        border: 1px solid #020617;
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

    .reset-btn,
    .fullview-btn {
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
        padding: 24px;
    }

    .map-stage-K {
        min-width: 1850px;
        min-height: 1950px;
    }

    .map-stage-P {
        min-width: 2350px;
        min-height: 2750px;
    }

    .map-stage-L {
        min-width: 5200px;
        min-height: 2400px;
    }

    .cemetery-map {
        width: max-content;
        height: max-content;
        transform-origin: top left;
        transition: transform .18s ease;
        border-radius: 34px;
        overflow: hidden;
        box-shadow:
            0 14px 30px rgba(15, 23, 42, .14),
            inset 0 0 0 1px rgba(255,255,255,.14);
    }

    .cemetery-svg {
        display: block;
        border-radius: 34px;
    }

    .svg-small-label {
        font-size: 14px;
        font-weight: 800;
        fill: #374151;
    }

    .svg-qiblat-text {
        font-size: 16px;
        font-weight: 900;
        letter-spacing: 2px;
        fill: #7c2d12;
    }

    .svg-compass-text {
        font-size: 17px;
        font-weight: 900;
        fill: #111827;
    }

    .svg-zone-title-text {
        font-size: 22px;
        font-weight: 900;
        fill: #ffffff;
    }

    .svg-zone-subtitle-text {
        font-size: 15px;
        font-weight: 700;
        fill: rgba(255,255,255,.9);
    }

    .svg-pondok-text {
        font-size: 15px;
        font-weight: 900;
        fill: #111827;
    }

    .svg-bottom-note {
        font-size: 15px;
        font-weight: 800;
        fill: #ffffff;
    }

    .svg-grave-item {
        transition: opacity .2s ease, transform .2s ease;
    }

    .svg-grave-item.disabled-user {
        opacity: .32;
    }

    .svg-grave-item.selected-user {
        opacity: 1;
    }

    .svg-grave-body {
        fill: url(#graveGrey);
        stroke: #64748b;
        stroke-width: 1.4;
    }

    .svg-grave-item.selected-user .svg-grave-body {
        fill: url(#graveGreen);
        stroke: #16a34a;
        stroke-width: 4;
    }

    .svg-grave-headline {
        fill: none;
        stroke: #64748b;
        stroke-width: 2;
        opacity: .65;
    }

    .svg-grave-line {
        fill: none;
        stroke: #475569;
        stroke-width: 1.7;
        stroke-linecap: round;
        opacity: .45;
    }

    .svg-grave-item.selected-user .svg-grave-headline,
    .svg-grave-item.selected-user .svg-grave-line {
        stroke: #166534;
        opacity: .8;
    }

    .svg-grave-code-bg {
        fill: #ffffff;
        opacity: .96;
        stroke: rgba(15, 23, 42, .08);
    }

    .svg-grave-code {
        font-size: 13px;
        font-weight: 900;
        fill: #111827;
        dominant-baseline: middle;
    }

    .svg-grave-item.disabled-user .svg-grave-code {
        fill: #64748b;
    }

    .svg-grave-item.selected-user .svg-grave-code-bg {
        fill: #16a34a;
        stroke: #15803d;
    }

    .svg-grave-item.selected-user .svg-grave-code {
        fill: #ffffff;
    }

    .svg-selected-ring {
        fill: rgba(22, 163, 74, .16);
        stroke: #22c55e;
        stroke-width: 5;
        stroke-dasharray: 12 8;
    }

    .svg-entrance-marker {
        fill: #2563eb;
        stroke: #ffffff;
        stroke-width: 4;
    }

    .svg-entrance-door {
        fill: #ffffff;
    }

    .svg-entrance-card {
        fill: rgba(255, 255, 255, .96);
        stroke: #bfdbfe;
        stroke-width: 2;
    }

    .svg-entrance-title {
        font-size: 17px;
        font-weight: 900;
        fill: #1e3a8a;
    }

    .svg-entrance-subtitle {
        font-size: 13px;
        font-weight: 700;
        fill: #475569;
    }

    .svg-guide-route {
        fill: none;
        stroke: #2563eb;
        stroke-width: 7;
        stroke-dasharray: 16 12;
        stroke-linecap: round;
        stroke-linejoin: round;
        opacity: .95;
    }

    .svg-guide-route-outline {
        fill: none;
        stroke: rgba(255, 255, 255, .8);
        stroke-width: 13;
        stroke-linecap: round;
        stroke-linejoin: round;
        opacity: .65;
    }

    .svg-guide-arrow {
        fill: #2563eb;
    }

    .svg-guide-label-card {
        fill: #eff6ff;
        stroke: #bfdbfe;
        stroke-width: 2;
    }

    .svg-guide-label {
        font-size: 14px;
        font-weight: 900;
        fill: #1d4ed8;
    }

    .svg-pin {
        fill: #16a34a;
        stroke: #ffffff;
        stroke-width: 4;
        filter: url(#softShadow);
    }

    .map-footer-note {
        font-size: 13px;
        color: #6b7280;
        margin-top: 14px;
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
        const mapViewer = document.getElementById('mapViewer');
        const cemeteryMap = document.getElementById('cemeteryMap');
        const zoomInBtn = document.getElementById('zoomInBtn');
        const zoomOutBtn = document.getElementById('zoomOutBtn');
        const resetZoomBtn = document.getElementById('resetZoomBtn');
        const zoomLevel = document.getElementById('zoomLevel');
        const fullViewBtn = document.getElementById('fullViewBtn');
        const mapFullContainer = document.getElementById('mapFullContainer');

        let scale = 1;
        const minScale = 0.10;
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