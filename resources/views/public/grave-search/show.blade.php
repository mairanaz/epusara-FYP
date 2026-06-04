<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lokasi Kubur | e-Pusara</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet"
      href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
      crossorigin="">

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

            $rightRoadX = max($mapConfig['right_road_x'], $lastPlotRightX + 80);

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
        | Digunakan untuk memaparkan panduan arah daripada pintu masuk
        | menuju lot pusara yang sedang dicari.
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
        | Maklumat panduan perjalanan
        |--------------------------------------------------------------------------
        */
        $plotParts = explode('-', $selectedPlot->plot_code ?? '');
        $selectedRowLabel = $plotParts[1] ?? ('Baris ' . $selectedPlot->row_number);

        /*
        |--------------------------------------------------------------------------
        | Kedudukan pintu masuk dan laluan panduan
        |--------------------------------------------------------------------------
        | Kedudukan ini menganggap pintu masuk utama berada di bahagian
        | kiri atas pelan dan bersambung dengan Laluan Utama.
        */
        $entranceX = $topRoadStartX + 18;
        $entranceY = 109;

        $guideLaneX = $graveStartX - 55;
        $guideTargetX = $selectedX + ($graveWidth / 2);
        $guideTargetY = $selectedY - 28;

    @endphp

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Figtree', sans-serif;
            background: #f8fafc;
            color: #111827;
        }

        a {
            text-decoration: none;
        }

        .public-topbar {
            background: rgba(255,255,255,0.96);
            backdrop-filter: blur(14px);
            box-shadow: 0 4px 22px rgba(15,23,42,0.08);
            position: sticky;
            top: 0;
            z-index: 99;
        }

        .public-container {
            width: 92%;
            max-width: 1480px;
            margin: auto;
        }

        .public-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            padding: 14px 0;
        }

        .public-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #0f766e;
            font-weight: 800;
            font-size: 26px;
        }

        .public-brand img {
            height: 56px;
            width: auto;
            border-radius: 12px;
        }

        .public-actions {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }

        .btn {
            border: 0;
            outline: 0;
            cursor: pointer;
            font-family: inherit;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            padding: 11px 18px;
            border-radius: 999px;
            font-weight: 700;
            font-size: 14px;
            transition: .2s ease;
        }

        .btn-primary {
            background: #0f766e;
            color: #ffffff;
            box-shadow: 0 10px 24px rgba(15, 118, 110, .22);
        }

        .btn-primary:hover {
            background: #0d5f59;
            transform: translateY(-1px);
        }

        .btn-light {
            background: #ffffff;
            color: #0f766e;
            border: 1px solid #d1d5db;
        }

        .btn-light:hover {
            background: #f8fafc;
            transform: translateY(-1px);
        }

        .btn-success {
            background: #16a34a;
            color: #ffffff;
        }

        .btn-success:hover {
            background: #15803d;
        }

        .page-wrap {
            width: 92%;
            max-width: 1480px;
            margin: auto;
            padding: 34px 0 48px;
        }

        .page-hero {
            background:
                radial-gradient(circle at top left, rgba(45, 212, 191, 0.22), transparent 34%),
                linear-gradient(135deg, #0f766e, #0f172a);
            color: #ffffff;
            border-radius: 32px;
            padding: 34px;
            margin-bottom: 24px;
            overflow: hidden;
            position: relative;
            box-shadow: 0 24px 60px rgba(15, 23, 42, .18);
        }

        .page-hero::after {
            content: "";
            position: absolute;
            width: 260px;
            height: 260px;
            border-radius: 50%;
            background: rgba(255,255,255,.08);
            right: -90px;
            top: -90px;
        }

        .page-hero-content {
            position: relative;
            z-index: 2;
            display: grid;
            grid-template-columns: 1.2fr .8fr;
            gap: 26px;
            align-items: center;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            background: rgba(255,255,255,.14);
            color: #ccfbf1;
            border: 1px solid rgba(255,255,255,.16);
            padding: 8px 15px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 800;
            margin-bottom: 14px;
        }

        .page-hero h1 {
            font-size: 34px;
            line-height: 1.18;
            margin-bottom: 10px;
            letter-spacing: -.5px;
        }

        .page-hero p {
            color: #e2e8f0;
            line-height: 1.8;
            max-width: 780px;
        }

        .hero-lot-card {
            background: rgba(255,255,255,.12);
            border: 1px solid rgba(255,255,255,.18);
            border-radius: 24px;
            padding: 22px;
            backdrop-filter: blur(12px);
        }

        .hero-lot-label {
            color: #cbd5e1;
            font-size: 13px;
            margin-bottom: 8px;
            font-weight: 700;
        }

        .hero-lot-code {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #86efac;
            border-radius: 18px;
            padding: 18px;
            font-size: 26px;
            font-weight: 900;
            text-align: center;
            margin-bottom: 12px;
        }

        .hero-lot-note {
            color: #e2e8f0;
            font-size: 13px;
            line-height: 1.6;
            text-align: center;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 24px;
        }

        .info-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 22px;
            padding: 20px;
            box-shadow: 0 12px 28px rgba(15, 23, 42, .07);
        }

        .info-label {
            color: #6b7280;
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 7px;
        }

        .info-value {
            color: #111827;
            font-size: 16px;
            font-weight: 800;
            line-height: 1.45;
        }

        .zone-chip {
            display: inline-flex;
            align-items: center;
            padding: 7px 13px;
            border-radius: 999px;
            background: #eef2ff;
            color: #4338ca;
            font-size: 13px;
            font-weight: 800;
        }

        .privacy-note {
            background: #fff7ed;
            border: 1px solid #fed7aa;
            color: #9a3412;
            border-radius: 20px;
            padding: 15px 18px;
            margin-bottom: 24px;
            line-height: 1.6;
            font-size: 14px;
            font-weight: 600;
        }

                .gis-section {
            margin-bottom: 24px;
        }

        .gis-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 26px;
            overflow: hidden;
            box-shadow: 0 12px 28px rgba(15, 23, 42, .07);
        }

        .gis-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            gap: 20px;
            flex-wrap: wrap;
            padding: 24px;
            border-bottom: 1px solid #e5e7eb;
        }

        .gis-title {
            font-size: 21px;
            font-weight: 900;
            color: #111827;
            margin-bottom: 7px;
        }

        .gis-subtitle {
            color: #6b7280;
            font-size: 14px;
            line-height: 1.7;
            max-width: 720px;
        }

        .gis-content {
            display: grid;
            grid-template-columns: 1fr 360px;
            gap: 0;
        }

        #cemeteryLocationMap {
            width: 100%;
            min-height: 420px;
            background: #e5e7eb;
        }

        .gis-details {
            padding: 24px;
            border-left: 1px solid #e5e7eb;
            background: #ffffff;
        }

        .gis-location-icon {
            width: 54px;
            height: 54px;
            border-radius: 18px;
            background: #ccfbf1;
            color: #0f766e;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 27px;
            margin-bottom: 16px;
        }

        .gis-location-name {
            font-size: 18px;
            font-weight: 900;
            color: #111827;
            line-height: 1.45;
            margin-bottom: 9px;
        }

        .gis-address {
            color: #6b7280;
            font-size: 14px;
            line-height: 1.7;
            margin-bottom: 16px;
        }

        .gis-note {
            border-radius: 16px;
            background: #f0fdfa;
            border: 1px solid #99f6e4;
            color: #115e59;
            font-size: 13px;
            line-height: 1.65;
            padding: 13px 14px;
            margin-bottom: 18px;
        }

        .gis-button {
            width: 100%;
        }

        .gis-coordinate {
            color: #64748b;
            font-size: 12px;
            margin-top: 14px;
            text-align: center;
        }

        .gis-unavailable {
            min-height: 300px;
            padding: 36px 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            background: #f8fafc;
            color: #64748b;
            line-height: 1.7;
        }

        .gis-popup-title {
            font-weight: 800;
            color: #111827;
            margin-bottom: 4px;
        }

        .gis-popup-address {
            color: #64748b;
            line-height: 1.5;
        }

        @media (max-width: 992px) {
            .gis-content {
                grid-template-columns: 1fr;
            }

            .gis-details {
                border-left: 0;
                border-top: 1px solid #e5e7eb;
            }
        }

        @media (max-width: 767px) {
            #cemeteryLocationMap {
                min-height: 340px;
            }

            .gis-header,
            .gis-details {
                padding: 19px;
            }
        }

        .content-grid {
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 22px;
            align-items: start;
        }

        .side-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 24px;
            padding: 22px;
            box-shadow: 0 12px 28px rgba(15, 23, 42, .07);
        }

        .side-card + .side-card {
            margin-top: 16px;
        }

        .side-title {
            font-size: 16px;
            font-weight: 900;
            color: #111827;
            margin-bottom: 4px;
        }

        .side-subtitle {
            color: #6b7280;
            font-size: 13px;
            line-height: 1.6;
            margin-bottom: 16px;
        }

        .selected-lot-box {
            min-height: 74px;
            border-radius: 18px;
            padding: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            font-size: 20px;
            font-weight: 900;
            color: #166534;
            background: linear-gradient(135deg, #dcfce7 0%, #f0fdf4 100%);
            border: 1px solid #86efac;
            margin-bottom: 12px;
        }

        .selected-note {
            font-size: 13px;
            color: #6b7280;
            line-height: 1.6;
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

        .grave-image-preview {
            width: 100%;
            height: 170px;
            border-radius: 18px;
            object-fit: cover;
            margin-bottom: 14px;
            border: 1px solid #e5e7eb;
        }

        .no-image-box {
            border: 1px dashed #cbd5e1;
            background: #f8fafc;
            color: #64748b;
            border-radius: 18px;
            padding: 22px;
            text-align: center;
            font-size: 14px;
            line-height: 1.6;
        }

        .map-shell {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 24px;
            box-shadow: 0 12px 28px rgba(15, 23, 42, .07);
            overflow: hidden;
        }

        .map-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
            padding: 20px 22px;
            border-bottom: 1px solid #e5e7eb;
            background: #ffffff;
        }

        .toolbar-title {
            font-size: 18px;
            font-weight: 900;
            color: #111827;
            margin-bottom: 4px;
        }

        .toolbar-subtitle {
            font-size: 13px;
            color: #6b7280;
            line-height: 1.5;
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
            font-weight: 900;
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
            font-size: 15px;
            font-weight: 800;
            box-shadow: 0 1px 3px rgba(0,0,0,.06);
            transition: .2s ease;
            cursor: pointer;
            font-family: inherit;
        }

        .tool-btn:hover {
            transform: translateY(-1px);
            background: #f3f4f6;
        }

        .map-body {
            padding: 20px;
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
            font-weight: 700;
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

        .map-footer-note {
            font-size: 13px;
            color: #6b7280;
            margin-top: 14px;
            line-height: 1.6;
        }

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

        .svg-destination-card {
            fill: #ffffff;
            stroke: #86efac;
            stroke-width: 2;
        }

        .svg-destination-title {
            font-size: 14px;
            font-weight: 800;
            fill: #166534;
        }

        .svg-destination-code {
            font-size: 18px;
            font-weight: 900;
            fill: #166534;
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

        .map-shell.fullscreen-mode .map-body {
            height: calc(100vh - 86px);
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

        body.map-fullscreen-active .public-topbar,
        body.map-fullscreen-active .page-hero,
        body.map-fullscreen-active .info-grid,
        body.map-fullscreen-active .privacy-note,
        body.map-fullscreen-active .side-column {
            display: none !important;
        }

        body.map-fullscreen-active .page-wrap {
            width: 100% !important;
            max-width: 100% !important;
            padding: 0 !important;
        }

        body.map-fullscreen-active .content-grid {
            display: block !important;
        }

        .image-modal {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, .76);
            z-index: 10000;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .image-modal.active {
            display: flex;
        }

        .image-modal-card {
            background: #ffffff;
            border-radius: 26px;
            width: min(920px, 100%);
            max-height: 92vh;
            overflow: hidden;
            box-shadow: 0 30px 80px rgba(0,0,0,.35);
        }

        .image-modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 14px;
            padding: 18px 22px;
            border-bottom: 1px solid #e5e7eb;
        }

        .image-modal-title {
            font-size: 18px;
            font-weight: 900;
            color: #111827;
            margin-bottom: 3px;
        }

        .image-modal-subtitle {
            color: #6b7280;
            font-size: 13px;
        }

        .close-modal-btn {
            width: 40px;
            height: 40px;
            border-radius: 999px;
            border: 0;
            background: #f3f4f6;
            color: #111827;
            font-size: 22px;
            cursor: pointer;
        }

        .image-modal-body {
            padding: 20px;
        }

        .image-modal-body img {
            width: 100%;
            max-height: 660px;
            object-fit: contain;
            border-radius: 18px;
            background: #f8fafc;
        }

        .footer {
            background: #0f172a;
            color: #cbd5e1;
            text-align: center;
            padding: 26px 0;
            margin-top: 38px;
        }

        .footer strong {
            color: #ffffff;
        }

        @media (max-width: 1199px) {
            .content-grid {
                grid-template-columns: 1fr;
            }

            .side-column {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 16px;
            }

            .side-card + .side-card {
                margin-top: 0;
            }

            .map-scroll-area {
                height: 660px;
            }
        }

        @media (max-width: 992px) {
            .page-hero-content {
                grid-template-columns: 1fr;
            }

            .info-grid,
            .side-column {
                grid-template-columns: repeat(2, 1fr);
            }

            .map-toolbar {
                align-items: flex-start;
                flex-direction: column;
            }

            .toolbar-right {
                width: 100%;
                justify-content: space-between;
            }
        }

        @media (max-width: 767px) {
            .public-nav {
                flex-direction: column;
                align-items: stretch;
            }

            .public-actions {
                flex-direction: column;
                align-items: stretch;
            }

            .btn {
                width: 100%;
            }

            .page-wrap {
                width: 94%;
                padding-top: 24px;
            }

            .page-hero {
                padding: 26px 22px;
                border-radius: 26px;
            }

            .page-hero h1 {
                font-size: 28px;
            }

            .info-grid,
            .side-column {
                grid-template-columns: 1fr;
            }

            .map-body {
                padding: 14px;
            }

            .map-help-bar {
                flex-direction: column;
            }

            .toolbar-controls {
                width: 100%;
                justify-content: center;
                flex-wrap: wrap;
                border-radius: 18px;
            }

            .map-scroll-area {
                height: 560px;
            }
        }
    </style>

    <style>
        /* =========================================================
           Tema awam e-Pusara - diselaraskan dengan page Ziarah Kubur
           Nota: CSS pelan/SVG/Leaflet asal dikekalkan tanpa perubahan.
        ========================================================= */
        :root {
            --brand-50: #ecfdf8;
            --brand-100: #d1fae9;
            --brand-200: #a7f3d0;
            --brand-500: #159a83;
            --brand-600: #0f7c69;
            --brand-700: #0d6558;
            --brand-800: #114c44;
            --brand-900: #0d332f;
            --sand-50: #fbfaf6;
            --sand-100: #f3efe6;
            --surface: #ffffff;
            --border: #e2e8f0;
            --muted: #64748b;
            --shadow-soft: 0 20px 55px rgba(15, 23, 42, .07);
            --shadow-card: 0 20px 48px rgba(15, 23, 42, .08);
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--sand-50);
            color: #1e293b;
        }

        .font-serif {
            font-family: 'Playfair Display', serif;
        }

        /* Header sama seperti page carian pusara */
        .notice-bar {
            background: var(--brand-900);
            color: var(--brand-100);
            font-size: 13px;
        }

        .notice-inner {
            width: min(1280px, calc(100% - 32px));
            margin: 0 auto;
            min-height: 42px;
            padding: 8px 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
        }

        .notice-text {
            display: inline-flex;
            gap: 9px;
            align-items: center;
            line-height: 1.5;
        }

        .notice-text i {
            color: var(--brand-200);
        }

        .notice-link {
            color: #ffffff;
            font-weight: 700;
            text-decoration: underline;
            text-underline-offset: 4px;
            white-space: nowrap;
        }

        .public-topbar {
            background: rgba(255,255,255,.95);
            backdrop-filter: blur(14px);
            border-bottom: 1px solid #f1f5f9;
            box-shadow: 0 3px 15px rgba(15,23,42,.05);
        }

        .public-container {
            width: min(1280px, calc(100% - 32px));
            max-width: none;
        }

        .public-nav {
            height: 80px;
            padding: 0;
        }

        .public-brand {
            gap: 12px;
            color: var(--brand-800);
            font-size: inherit;
        }

        .public-brand img {
            width: 48px;
            height: 48px;
            object-fit: cover;
            border-radius: 12px;
            border: 1px solid #f1f5f9;
        }

        .brand-name {
            display: block;
            font-size: 20px;
            font-weight: 800;
            line-height: 1;
        }

        .brand-tagline {
            display: block;
            color: #94a3b8;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .1em;
            margin-top: 6px;
            text-transform: uppercase;
        }

        .public-actions {
            gap: 10px;
        }

        .nav-text-link {
            color: #475569;
            font-size: 14px;
            font-weight: 700;
            padding: 10px 13px;
            transition: color .18s ease;
        }

        .nav-text-link:hover {
            color: var(--brand-700);
        }

        .btn {
            font-family: 'Plus Jakarta Sans', sans-serif;
            padding: 11px 18px;
            font-size: 13px;
        }

        .btn-primary {
            background: var(--brand-600);
            box-shadow: 0 10px 24px rgba(15, 124, 105, .2);
        }

        .btn-primary:hover {
            background: var(--brand-700);
        }

        .btn-light {
            background: var(--brand-50);
            color: var(--brand-700);
            border-color: var(--brand-100);
        }

        .btn-light:hover {
            background: var(--brand-100);
        }

        .page-wrap {
            width: min(1280px, calc(100% - 32px));
            max-width: none;
            padding: 34px 0 54px;
        }

        /* Hero lebih tenang dan seragam dengan interface carian */
        .page-hero {
            background:
                radial-gradient(circle at 100% 0%, rgba(15,124,105,.15), transparent 38%),
                linear-gradient(120deg, #ffffff 0%, #ffffff 55%, #ecfdf8 100%);
            color: #0f172a;
            border: 1px solid #f1f5f9;
            border-radius: 30px;
            padding: 34px;
            box-shadow: var(--shadow-soft);
        }

        .page-hero::after {
            background: rgba(15,124,105,.07);
        }

        .hero-badge {
            background: var(--brand-50);
            color: var(--brand-700);
            border-color: var(--brand-100);
            font-size: 11px;
            letter-spacing: .13em;
            text-transform: uppercase;
        }

        .page-hero h1 {
            font-family: 'Playfair Display', serif;
            color: #0f172a;
            font-size: 38px;
        }

        .page-hero p {
            color: #64748b;
            font-size: 14px;
        }

        .hero-lot-card {
            background: #ffffff;
            border: 1px solid #f1f5f9;
            box-shadow: var(--shadow-card);
        }

        .hero-lot-label {
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: .12em;
            font-size: 11px;
        }

        .hero-lot-code {
            background: var(--brand-50);
            color: var(--brand-700);
            border-color: var(--brand-100);
        }

        .hero-lot-note {
            color: #64748b;
        }

        .info-card,
        .gis-card,
        .side-card,
        .map-shell {
            border-color: #f1f5f9;
            box-shadow: var(--shadow-card);
        }

        .info-card {
            border-radius: 22px;
        }

        .info-label {
            color: #64748b;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: .1em;
        }

        .zone-chip {
            background: var(--brand-50);
            color: var(--brand-700);
        }

        .privacy-note {
            background: #fffbeb;
            border-color: #fde68a;
            color: #92400e;
        }

        .gis-title,
        .toolbar-title,
        .side-title {
            color: #0f172a;
        }

        .gis-location-icon {
            background: var(--brand-50);
            color: var(--brand-700);
        }

        .gis-note {
            background: var(--brand-50);
            border-color: var(--brand-100);
            color: var(--brand-700);
        }

        .map-frame {
            background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
        }

        .selected-lot-box {
            color: var(--brand-700);
            background: linear-gradient(135deg, var(--brand-50) 0%, #f0fdf4 100%);
            border-color: var(--brand-100);
        }

        /* Footer sama seperti page carian */
        .footer-main {
            background: var(--brand-900);
            color: var(--brand-100);
            margin-top: 38px;
            padding: 0;
            text-align: left;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 5fr 4fr 3fr;
            gap: 34px;
            padding: 46px 0 38px;
        }

        .footer-brand {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            color: white;
        }

        .footer-brand img {
            height: 48px;
            width: 48px;
            border-radius: 12px;
            object-fit: cover;
            background: white;
        }

        .footer-brand .brand-tagline {
            color: rgba(209,250,233,.66);
        }

        .footer-desc {
            color: rgba(209,250,233,.75);
            font-size: 13px;
            line-height: 1.8;
            margin-top: 17px;
            max-width: 425px;
        }

        .footer-title {
            color: #ffffff;
            font-weight: 800;
            font-size: 14px;
            margin-bottom: 16px;
        }

        .footer-links {
            display: grid;
            gap: 13px;
            font-size: 13px;
        }

        .footer-links a {
            color: rgba(209,250,233,.75);
            display: inline-flex;
            align-items: center;
            gap: 9px;
        }

        .footer-links a:hover {
            color: #ffffff;
        }

        .footer-description {
            color: rgba(209,250,233,.75);
            font-size: 13px;
            line-height: 1.8;
        }

        .privacy-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255,255,255,.08);
            color: var(--brand-100);
            border-radius: 999px;
            padding: 9px 13px;
            font-size: 12px;
            margin-top: 15px;
        }

        .footer-bottom {
            border-top: 1px solid rgba(209,250,233,.15);
            color: rgba(209,250,233,.58);
            font-size: 12px;
            display: flex;
            justify-content: space-between;
            gap: 12px;
            padding: 21px 0 25px;
        }

        @media (max-width: 992px) {
            .footer-grid {
                grid-template-columns: 1fr 1fr;
            }

            .footer-grid > div:first-child {
                grid-column: 1 / -1;
            }
        }

        @media (max-width: 767px) {
            .notice-inner {
                flex-direction: column;
                padding: 10px 0;
                text-align: center;
            }

            .public-nav {
                height: auto;
                padding: 14px 0;
            }

            .public-brand {
                justify-content: center;
            }

            .brand-tagline {
                display: none;
            }

            .nav-text-link {
                display: none;
            }

            .page-hero h1 {
                font-size: 29px;
            }

            .footer-grid {
                display: block;
                padding: 38px 0 28px;
            }

            .footer-grid > div {
                margin-bottom: 29px;
            }

            .footer-bottom {
                flex-direction: column;
                text-align: center;
            }
        }
    </style>

</head>

<body>

    {{-- Bar makluman awam: sama dengan halaman carian pusara --}}
    <div class="notice-bar">
        <div class="notice-inner">
            <p class="notice-text">
                <i class="fa-solid fa-circle-info"></i>
                Carian lokasi pusara disediakan kepada orang awam bagi memudahkan urusan ziarah.
            </p>

            <a href="{{ route('public.grave-search.index') }}" class="notice-link">
                Kembali ke carian pusara
            </a>
        </div>
    </div>

    {{-- Navigation: diselaraskan dengan halaman Ziarah Kubur --}}
    <nav class="public-topbar">
        <div class="public-container">
            <div class="public-nav">
                <a href="{{ url('/') }}" class="public-brand">
                    <img src="{{ asset('assets/images/logo_rtb.jpg') }}" alt="Logo e-Pusara">

                    <span>
                        <span class="brand-name">e-Pusara</span>
                        <span class="brand-tagline">Sistem Pengurusan Perkuburan</span>
                    </span>
                </a>

                <div class="public-actions">
                    <a href="{{ route('public.grave-search.index') }}" class="btn btn-light">
                        <i class="fa-solid fa-arrow-left"></i>
                        Kembali Ke Carian
                    </a>

                    <a href="{{ url('/') }}" class="nav-text-link">Laman Utama</a>

                    @auth
                        <a href="{{ route('dashboard') }}" class="btn btn-primary">
                            <i class="fa-solid fa-gauge-high"></i>
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-light">Log Masuk</a>
                        <a href="{{ route('register') }}" class="btn btn-primary">Daftar</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <main class="page-wrap">

        {{-- Pengenalan lokasi pusara --}}
        <section class="page-hero">
            <div class="page-hero-content">
                <div>
                    <span class="hero-badge">
                        <i class="fa-solid fa-map-location-dot" style="margin-right:8px;"></i>
                        Lokasi Kubur Untuk Tujuan Ziarah
                    </span>

                    <h1>Peta Lokasi Pusara</h1>

                    <p>
                        Lihat kedudukan lot pusara dengan lebih jelas melalui pelan kawasan
                        dan panduan laluan daripada pintu masuk utama. Paparan ini disediakan
                        untuk memudahkan urusan ziarah.
                    </p>
                </div>

                <div class="hero-lot-card">
                    <div class="hero-lot-label">Kod Lot Pusara Dipilih</div>

                    <div class="hero-lot-code">
                        <i class="fa-solid fa-location-dot" style="font-size:20px; margin-right:8px;"></i>
                        {{ $selectedPlot->plot_code ?? '-' }}
                    </div>

                    <div class="hero-lot-note">
                        Lot berwarna hijau pada pelan menunjukkan lokasi pusara si mati.
                    </div>
                </div>
            </div>
        </section>

        <section class="info-grid">
            <div class="info-card">
                <div class="info-label">Nama Si Mati</div>
                <div class="info-value">
                    {{ $deathReport->nama_si_mati ?? '-' }}
                </div>
            </div>

            <div class="info-card">
                <div class="info-label">Tarikh Meninggal</div>
                <div class="info-value">
                    {{ $deathReport->tarikh_meninggal ? $deathReport->tarikh_meninggal->format('d/m/Y') : '-' }}
                </div>
            </div>

            <div class="info-card">
                <div class="info-label">Zon Kubur</div>
                <div class="info-value">
                    <span class="zone-chip">{{ $mapConfig['label'] }}</span>
                </div>
            </div>

            <div class="info-card">
                <div class="info-label">No. Lot Kubur</div>
                <div class="info-value">
                    {{ $selectedPlot->plot_code ?? '-' }}
                </div>
            </div>
        </section>

        <div class="privacy-note">
            Maklumat yang dipaparkan adalah terhad untuk tujuan ziarah sahaja.
            Maklumat peribadi seperti nombor kad pengenalan, alamat, dokumen kematian
            dan maklumat pelapor tidak dipaparkan kepada orang awam.
        </div>

            <section class="gis-section">
                <div class="gis-card">
                    <div class="gis-header">
                        <div>
                            <div class="gis-title">Lokasi Tanah Perkuburan</div>
                            <div class="gis-subtitle">
                                Peta ini menunjukkan lokasi sebenar tanah perkuburan.
                                Selepas tiba di kawasan perkuburan, gunakan pelan lot di bawah
                                untuk mencari pusara
                                <strong>{{ $selectedPlot->plot_code ?? '-' }}</strong>.
                            </div>
                        </div>

                        @if($hasCoordinates)
                            <a href="https://www.google.com/maps/dir/?api=1&destination={{ $cemetery['latitude'] }},{{ $cemetery['longitude'] }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="btn btn-success">
                                🧭 Dapatkan Arah
                            </a>
                        @endif
                    </div>

                    <div class="gis-content">
                        @if($hasCoordinates)
                            <div id="cemeteryLocationMap"
                                aria-label="Peta lokasi sebenar tanah perkuburan"></div>
                        @else
                            <div class="gis-unavailable">
                                <div>
                                    <strong>Koordinat lokasi belum ditetapkan.</strong><br>
                                    Pentadbir perlu memasukkan latitude dan longitude tanah perkuburan
                                    dalam fail konfigurasi sistem.
                                </div>
                            </div>
                        @endif

                        <div class="gis-details">
                            <div class="gis-location-icon">📍</div>

                            <div class="gis-location-name">
                                {{ $cemetery['name'] }}
                            </div>

                            <div class="gis-address">
                                {{ $cemetery['address'] }}
                            </div>

                            <div class="gis-note">
                                {{ $cemetery['entrance_note'] }}
                            </div>

                            @if($hasCoordinates)
                                <a href="https://www.google.com/maps/dir/?api=1&destination={{ $cemetery['latitude'] }},{{ $cemetery['longitude'] }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="btn btn-primary gis-button">
                                    📍 Buka Navigasi Google Maps
                                </a>

                                <div class="gis-coordinate">
                                    Koordinat:
                                    {{ $cemetery['latitude'] }},
                                    {{ $cemetery['longitude'] }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </section>

        <section class="content-grid">
            <aside class="side-column">

                <div class="side-card">
                    <div class="side-title">Panduan Ke Lot Pusara</div>
                    <div class="side-subtitle">
                        Ikuti panduan pada pelan bermula dari pintu masuk utama.
                    </div>

                    <div style="display:flex; flex-direction:column; gap:12px; margin-bottom:16px;">
                        <div style="display:flex; gap:10px; align-items:flex-start;">
                            <div style="
                                width:26px;
                                height:26px;
                                border-radius:50%;
                                background:#dbeafe;
                                color:#2563eb;
                                display:flex;
                                align-items:center;
                                justify-content:center;
                                font-weight:900;
                                flex-shrink:0;">
                                1
                            </div>

                            <div>
                                <strong style="display:block; font-size:13px; color:#111827;">
                                    Pintu Masuk Utama
                                </strong>
                                <span style="font-size:12px; color:#6b7280;">
                                    Anda bermula dari sini
                                </span>
                            </div>
                        </div>

                        <div style="display:flex; gap:10px; align-items:flex-start;">
                            <div style="
                                width:26px;
                                height:26px;
                                border-radius:50%;
                                background:#dbeafe;
                                color:#2563eb;
                                display:flex;
                                align-items:center;
                                justify-content:center;
                                font-weight:900;
                                flex-shrink:0;">
                                2
                            </div>

                            <div>
                                <strong style="display:block; font-size:13px; color:#111827;">
                                    Rujuk Laluan Panduan
                                </strong>
                                <span style="font-size:12px; color:#6b7280;">
                                    Sila ikut garisan biru menuju kawasan lot
                                </span>
                            </div>
                        </div>

                        <div style="display:flex; gap:10px; align-items:flex-start;">
                            <div style="
                                width:26px;
                                height:26px;
                                border-radius:50%;
                                background:#dcfce7;
                                color:#16a34a;
                                display:flex;
                                align-items:center;
                                justify-content:center;
                                font-weight:900;
                                flex-shrink:0;">
                                3
                            </div>

                            <div>
                                <strong style="display:block; font-size:13px; color:#111827;">
                                    Cari Baris {{ $selectedRowLabel }}
                                </strong>
                                <span style="font-size:12px; color:#6b7280;">
                                    Lot berwarna hijau ialah destinasi anda
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="selected-lot-box">
                        📍 {{ $selectedPlot->plot_code ?? '-' }}
                    </div>

                    <div class="selected-note">
                        Garisan panduan pada pelan digunakan sebagai rujukan arah dalam kawasan perkuburan.
                    </div>
                </div>

                <div class="side-card">
                    <div class="side-title">Gambar Kubur</div>
                    <div class="side-subtitle">
                        Gambar dipaparkan jika telah dimuat naik oleh pentadbir.
                    </div>

                    @if(!empty($selectedPlot->grave_image))
                        <img src="{{ asset('storage/' . $selectedPlot->grave_image) }}"
                             alt="Gambar Kubur"
                             class="grave-image-preview">

                        <button type="button" class="btn btn-success" id="openImageModalBtn">
                            Lihat Gambar Kubur
                        </button>
                    @else
                        <div class="no-image-box">
                            Gambar kubur belum dimuat naik oleh pentadbir.
                        </div>
                    @endif
                </div>

                <div class="side-card">
                    <div class="side-title">Petunjuk Pelan</div>
                    <div class="side-subtitle">
                        Maksud warna dan elemen pada peta.
                    </div>

                    <div class="legend-list">
                        <div class="legend-item">
                            <span style="
                                width:18px;
                                height:18px;
                                border-radius:50%;
                                background:#2563eb;
                                display:inline-block;
                                border:2px solid #dbeafe;">
                            </span>
                            <span>Pintu Masuk Utama</span>
                        </div>

                        <div class="legend-item">
                            <span style="
                                width:22px;
                                height:0;
                                border-top:3px dashed #2563eb;
                                display:inline-block;">
                            </span>
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

            </aside>

            <section class="map-shell" id="mapFullContainer">

                <div class="map-toolbar">
                    <div>
                        <div class="toolbar-title">Pelan Kawasan Perkuburan</div>
                        <div class="toolbar-subtitle">
                            Gunakan zoom, drag dan full view untuk melihat kedudukan lot dengan lebih jelas.
                        </div>
                    </div>

                    <div class="toolbar-right">
                        <div class="zoom-indicator" id="zoomLevel">100%</div>

                        <div class="toolbar-controls">
                            <button type="button" class="tool-btn" id="zoomOutBtn" title="Zoom Out">−</button>
                            <button type="button" class="tool-btn" id="zoomInBtn" title="Zoom In">+</button>
                            <button type="button" class="tool-btn" id="resetZoomBtn" title="Reset">Reset</button>
                            <button type="button" class="tool-btn" id="fullViewBtn" title="Full View">Full View</button>
                        </div>
                    </div>
                </div>

                <div class="map-body">

                    <div class="map-help-bar">
                        <div class="help-pill">🖱️ Drag untuk gerakkan pelan</div>
                        <div class="help-pill">🔍 Guna butang untuk zoom</div>
                        <div class="help-pill">↔️ Scroll untuk lihat kawasan lain</div>
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

                                                                                {{-- Panduan arah dari pintu masuk ke lot dipilih --}}
                                        <g class="svg-route-guide">

                                            {{-- Laluan putih sebagai latar supaya garisan jelas --}}
                                            <path d="
                                                M {{ $entranceX }} {{ $entranceY }}
                                                L {{ $guideLaneX }} {{ $entranceY }}
                                                L {{ $guideLaneX }} {{ $guideTargetY }}
                                                L {{ $guideTargetX }} {{ $guideTargetY }}
                                            "
                                            class="svg-guide-route-outline"/>

                                            {{-- Laluan rujukan biru --}}
                                            <path d="
                                                M {{ $entranceX }} {{ $entranceY }}
                                                L {{ $guideLaneX }} {{ $entranceY }}
                                                L {{ $guideLaneX }} {{ $guideTargetY }}
                                                L {{ $guideTargetX }} {{ $guideTargetY }}
                                            "
                                            class="svg-guide-route"/>

                                            {{-- Anak panah berhampiran lot terpilih --}}
                                            <polygon points="
                                                {{ $guideTargetX - 12 }},{{ $guideTargetY - 8 }}
                                                {{ $guideTargetX + 12 }},{{ $guideTargetY - 8 }}
                                                {{ $guideTargetX }},{{ $guideTargetY + 13 }}
                                            "
                                            class="svg-guide-arrow"/>

                                            {{-- Label Laluan Rujukan --}}
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

                                            {{-- Simbol pintu --}}
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

                                        {{-- Label pintu masuk --}}
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
                        Paparan ini adalah untuk semakan lokasi sahaja.
                        Sebarang kemaskini lot kubur hanya boleh dibuat oleh pentadbir.
                    </div>

                </div>
            </section>
        </section>

    </main>

    @if(!empty($selectedPlot->grave_image))
        <div class="image-modal" id="graveImageModal" aria-hidden="true">
            <div class="image-modal-card">
                <div class="image-modal-header">
                    <div>
                        <div class="image-modal-title">Gambar Kubur</div>
                        <div class="image-modal-subtitle">
                            {{ $deathReport->nama_si_mati }} • {{ $selectedPlot->plot_code }}
                        </div>
                    </div>

                    <button type="button" class="close-modal-btn" id="closeImageModalBtn">
                        ×
                    </button>
                </div>

                <div class="image-modal-body">
                    <img src="{{ asset('storage/' . $selectedPlot->grave_image) }}"
                         alt="Gambar Kubur">
                </div>
            </div>
        </div>
    @endif

    {{-- Footer: sama tema dengan halaman carian pusara --}}
    <footer class="footer-main">
        <div class="public-container">
            <div class="footer-grid">

                <div>
                    <a href="{{ url('/') }}" class="footer-brand">
                        <img src="{{ asset('assets/images/logo_rtb.jpg') }}" alt="Logo e-Pusara">

                        <span>
                            <span class="brand-name">e-Pusara</span>
                            <span class="brand-tagline">Sistem Pengurusan Perkuburan</span>
                        </span>
                    </a>

                    <p class="footer-desc">
                        Sistem pengurusan khairat kematian dan lokasi perkuburan yang
                        memudahkan urusan waris serta pengunjung membuat carian pusara.
                    </p>
                </div>

                <div>
                    <h4 class="footer-title">Pautan Pantas</h4>

                    <div class="footer-links">
                        <a href="{{ url('/') }}">
                            <i class="fa-solid fa-angle-right"></i>
                            Laman Utama
                        </a>

                        <a href="{{ route('public.grave-search.index') }}">
                            <i class="fa-solid fa-angle-right"></i>
                            Carian Lokasi Pusara
                        </a>

                        <a href="{{ route('public.grave-search.index') }}#panduan-amalan">
                            <i class="fa-solid fa-angle-right"></i>
                            Panduan Amalan Mulia
                        </a>
                    </div>
                </div>

                <div>
                    <h4 class="footer-title">Modul Ziarah Kubur</h4>

                    <p class="footer-description">
                        Carian lokasi pusara awam disediakan untuk tujuan ziarah
                        dengan paparan maklumat asas sahaja.
                    </p>

                    <div class="privacy-badge">
                        <i class="fa-solid fa-shield-halved"></i>
                        Privasi waris dijaga
                    </div>
                </div>

            </div>

            <div class="footer-bottom">
                <p>&copy; {{ date('Y') }} e-Pusara. Hak cipta terpelihara.</p>
                <p>Sistem Pengurusan Khairat Kematian &amp; Perkuburan</p>
            </div>
        </div>
    </footer>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
        crossorigin=""></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

                        @if($hasCoordinates)
                const cemeteryLatitude = @json($cemetery['latitude']);
                const cemeteryLongitude = @json($cemetery['longitude']);
                const cemeteryName = @json($cemetery['name']);
                const cemeteryAddress = @json($cemetery['address']);

                const cemeteryLocationMap = L.map('cemeteryLocationMap', {
                    scrollWheelZoom: false
                }).setView([cemeteryLatitude, cemeteryLongitude], 16);

                L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(cemeteryLocationMap);

                const cemeteryMarker = L.marker([
                    cemeteryLatitude,
                    cemeteryLongitude
                ]).addTo(cemeteryLocationMap);

                cemeteryMarker.bindPopup(
                    `<div class="gis-popup-title">${cemeteryName}</div>` +
                    `<div class="gis-popup-address">${cemeteryAddress}</div>`
                ).openPopup();
            @endif

            const mapViewer = document.getElementById('mapViewer');
            const cemeteryMap = document.getElementById('cemeteryMap');
            const zoomInBtn = document.getElementById('zoomInBtn');
            const zoomOutBtn = document.getElementById('zoomOutBtn');
            const resetZoomBtn = document.getElementById('resetZoomBtn');
            const zoomLevel = document.getElementById('zoomLevel');
            const fullViewBtn = document.getElementById('fullViewBtn');
            const mapFullContainer = document.getElementById('mapFullContainer');

            const imageModal = document.getElementById('graveImageModal');
            const openImageModalBtn = document.getElementById('openImageModalBtn');
            const closeImageModalBtn = document.getElementById('closeImageModalBtn');

            let scale = 1;
            const minScale = 0.10;
            const maxScale = 2.2;
            const step = 0.15;

            function setFullViewButtonLabel() {
                if (!fullViewBtn || !mapFullContainer) return;

                const isFullscreen =
                    document.fullscreenElement === mapFullContainer ||
                    mapFullContainer.classList.contains('fullscreen-mode');

                fullViewBtn.textContent = isFullscreen ? 'Tutup' : 'Full View';
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
                if (e.key === 'Escape') {
                    if (mapFullContainer) {
                        mapFullContainer.classList.remove('fullscreen-mode');
                        document.body.classList.remove('map-fullscreen-active');
                        setFullViewButtonLabel();
                    }

                    if (imageModal) {
                        imageModal.classList.remove('active');
                        imageModal.setAttribute('aria-hidden', 'true');
                    }
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

            if (openImageModalBtn && imageModal) {
                openImageModalBtn.addEventListener('click', function () {
                    imageModal.classList.add('active');
                    imageModal.setAttribute('aria-hidden', 'false');
                });
            }

            if (closeImageModalBtn && imageModal) {
                closeImageModalBtn.addEventListener('click', function () {
                    imageModal.classList.remove('active');
                    imageModal.setAttribute('aria-hidden', 'true');
                });
            }

            if (imageModal) {
                imageModal.addEventListener('click', function (e) {
                    if (e.target === imageModal) {
                        imageModal.classList.remove('active');
                        imageModal.setAttribute('aria-hidden', 'true');
                    }
                });
            }

            setFullViewButtonLabel();
            applyZoom();
        });
    </script>


</body>
</html>