@extends('layouts.app')

@section('content')
<style>
    .booking-header {
        background: linear-gradient(135deg, #f8fbff 0%, #eef7ff 100%);
        border: 1px solid #e5eef9;
        border-radius: 18px;
        padding: 24px;
        margin-bottom: 24px;
    }

    .step-wrap {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .step-item {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #64748b;
        font-size: 13px;
        font-weight: 600;
    }

    .step-circle {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #dbe6f3;
        background: #fff;
        color: #64748b;
    }

    .step-item.active .step-circle {
        background: #0d6efd;
        color: #fff;
        border-color: #0d6efd;
    }

    .step-line {
        width: 45px;
        height: 2px;
        background: #dbe6f3;
    }

    .soft-card {
        background: #fff;
        border: 1px solid #e9eef5;
        border-radius: 18px;
        box-shadow: 0 8px 22px rgba(15, 23, 42, 0.04);
    }

    .deceased-icon {
        width: 76px;
        height: 76px;
        border-radius: 50%;
        background: #eaf4ff;
        color: #7fa6d6;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .deceased-icon svg {
        width: 42px;
        height: 42px;
    }

    .deceased-icon-memorial {
        background: #eef6ff;
        color: #8aa9cf;
    }

    .category-pill {
        border: 1px solid #dbe6f3;
        background: #fff;
        color: #334155;
        border-radius: 999px;
        padding: 9px 18px;
        font-weight: 600;
        cursor: pointer;
        transition: .2s;
    }

    .category-pill.active {
        background: #0d6efd;
        color: #fff;
        border-color: #0d6efd;
        box-shadow: 0 8px 18px rgba(13, 110, 253, 0.22);
    }

    .option-card {
        border: 1px solid #e3eaf3;
        border-radius: 16px;
        overflow: hidden;
        background: #fff;
        transition: .2s;
        height: 100%;
        cursor: pointer;
        position: relative;
    }

    .option-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.09);
    }

    .option-card.active {
        border: 2px solid #0d6efd;
        box-shadow: 0 12px 28px rgba(13, 110, 253, 0.15);
    }

    .option-image {
        width: 100%;
        height: 145px;
        object-fit: cover;
        background: #f1f5f9;
    }

    .option-placeholder {
        width: 100%;
        height: 145px;
        background: linear-gradient(135deg, #eaf7ff, #f8fafc);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #0d6efd;
        font-size: 38px;
    }

    .price-text {
        color: #0d6efd;
        font-size: 18px;
        font-weight: 800;
    }

    .summary-card {
        position: sticky;
        top: 90px;
    }

    .summary-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        background: #eaf7ff;
        color: #0d6efd;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
    }

    .btn-submit-main {
        background: linear-gradient(135deg, #0d6efd, #0aa2c0);
        border: none;
        color: #fff;
        font-weight: 700;
        border-radius: 12px;
        padding: 12px 16px;
    }

    .btn-submit-main:hover {
        color: #fff;
        opacity: .95;
    }

    .info-list li {
        margin-bottom: 10px;
        color: #475569;
    }

    .form-select,
    .form-control {
        border-radius: 12px;
        border-color: #dbe6f3;
        padding: 11px 14px;
    }

    .section-label {
        font-size: 13px;
        color: #64748b;
        font-weight: 600;
        margin-bottom: 4px;
    }

    .section-value {
        color: #0f172a;
        font-weight: 700;
    }

    .waris-box {
        background: #f8fbff;
        border: 1px solid #e5eef9;
        border-radius: 16px;
        padding: 18px;
    }

    @media (max-width: 768px) {
        .booking-header {
            padding: 18px;
        }

        .step-line {
            display: none;
        }

        .summary-card {
            position: static;
        }
    }

    .option-card.disabled-option {
        opacity: .55;
        cursor: not-allowed;
        filter: grayscale(30%);
    }

    .option-card.disabled-option:hover {
        transform: none;
        box-shadow: none;
    }

    .option-card.disabled-option .choose-option-btn {
        pointer-events: none;
    }

    .category-note {
        background: #fff8e6;
        border: 1px solid #ffe2a8;
        color: #8a5a00;
        border-radius: 12px;
        padding: 10px 14px;
        font-size: 13px;
        font-weight: 600;
    }

    .left-order-col {
        padding-bottom: 25px;
    }

    .detail-modal-image {
        width: 100%;
        height: 360px;
        object-fit: cover;
        border-radius: 18px;
        background: #f1f5f9;
    }

    .detail-thumb.active {
        border-color: #0d6efd;
    }

    .detail-price {
        font-size: 36px;
        font-weight: 800;
        color: #0d6efd;
    }

    .detail-info-box {
        background: #f8fbff;
        border: 1px solid #e5eef9;
        border-radius: 14px;
        padding: 14px;
    }

    .detail-feature-list li {
        margin-bottom: 8px;
        color: #475569;
    }

    .option-action-wrap {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .gallery-nav {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 42px;
        height: 42px;
        border: none;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.9);
        color: #334155;
        box-shadow: 0 6px 14px rgba(15, 23, 42, 0.12);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }

    .gallery-prev {
        left: 14px;
    }

    .gallery-next {
        right: 14px;
    }

    .gallery-nav:hover {
        background: #fff;
    }

    .detail-thumb {
        width: 82px;
        height: 70px;
        object-fit: cover;
        border-radius: 12px;
        border: 2px solid #e5eef9;
        cursor: pointer;
    }
</style>

@php
    $pemohonTempahan = Auth::user()->name;
    $deathReportsData = $deathReports->map(function ($report) {
        $plot = $report->final_burial_plot;

        $umur = is_numeric($report->umur) ? (int) $report->umur : null;
        $detectedCategory = (!is_null($umur) && $umur < 12) ? 'kanak-kanak' : 'dewasa';

        return [
            'id' => $report->id,
            'nama_si_mati' => $report->nama_si_mati,
            'jantina' => $report->jantina ?? '-',
            'umur' => $umur,
            'detected_category' => $detectedCategory,
            'detected_category_label' => $detectedCategory === 'kanak-kanak' ? 'Kanak-kanak' : 'Dewasa',
            'tarikh_meninggal' => optional($report->tarikh_meninggal)->format('d/m/Y'),
            'plot_code' => $plot->plot_code ?? $report->burial_plot_code ?? $report->burial_lot_no ?? '-',

            'nama_waris' => $report->nama_pelapor ?? '-',
            'no_tel_waris' => $report->no_tel_pelapor ?? '-',
            'pertalian_waris' => $report->pertalian_pelapor ?? '-',
            'alamat_terakhir' => $report->alamat_terakhir ?? '-',
        ];
    })->values();

    $oldDeathReportId = old('death_report_id');
    $oldCategory = old('category', 'dewasa');
    $oldOrderType = old('order_type');
@endphp

<div class="container-fluid">

    <div class="booking-header">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <div class="text-muted small mb-1">
                    Dashboard / Tempahan Kepuk / Nisan
                </div>

                <h3 class="fw-bold mb-1">
                    Tempahan Kepuk / Nisan
                </h3>

                <p class="text-muted mb-0">
                    Pilih waris / si mati dahulu, kemudian pilih jenis kepungan dan batu nisan yang dikehendaki.
                </p>
            </div>

            <div class="step-wrap">
                <div class="step-item active" id="stepOne">
                    <span class="step-circle">1</span>
                    <span>Pilih Si Mati</span>
                </div>

                <div class="step-line"></div>

                <div class="step-item" id="stepTwo">
                    <span class="step-circle">2</span>
                    <span>Pilih Tempahan</span>
                </div>

                <div class="step-line"></div>

                <div class="step-item" id="stepThree">
                    <span class="step-circle">3</span>
                    <span>Semak Harga</span>
                </div>

                <div class="step-line"></div>

                <div class="step-item" id="stepFour">
                    <span class="step-circle">4</span>
                    <span>Hantar</span>
                </div>
            </div>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm rounded-3">
            <div class="fw-semibold mb-1">Sila semak semula maklumat berikut:</div>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm rounded-3">
            {{ session('error') }}
        </div>
    @endif

    @if($deathReports->isEmpty())
        <div class="soft-card p-5 text-center">
            <i class="bi bi-info-circle fs-1 text-info"></i>

            <h5 class="fw-bold mt-3">
                Tiada laporan kematian yang layak
            </h5>

            <p class="text-muted mb-3">
                Permohonan tempahan hanya boleh dibuat untuk laporan kematian yang telah diluluskan
                dan belum mempunyai tempahan kepuk / nisan.
            </p>

            <a href="{{ route('death-reports.index') }}" class="btn btn-info text-white">
                Semak Status Laporan
            </a>
        </div>
    @else

        <form action="{{ route('grave-orders.store') }}" method="POST" id="graveOrderForm">
            @csrf

            <input type="hidden" name="category" id="categoryInput" value="{{ $oldCategory }}">
            <input type="hidden" name="order_type" id="orderTypeInput" value="{{ $oldOrderType }}">

            <div class="row g-4">
                <div class="col-xl-8 left-order-col">

                    <div class="soft-card p-4 mb-4">
                        <label class="form-label fw-semibold">
                            Pilih Waris / Si Mati
                        </label>

                        <select name="death_report_id" id="deathReportSelect" class="form-select">
                            <option value="">-- Pilih waris / si mati --</option>

                            @foreach($deathReports as $report)
                                @php
                                    $plot = $report->final_burial_plot;
                                    $plotCode = $plot->plot_code ?? $report->burial_plot_code ?? $report->burial_lot_no ?? null;
                                @endphp

                                <option value="{{ $report->id }}" {{ $oldDeathReportId == $report->id ? 'selected' : '' }}>
                                    {{ $report->nama_si_mati }}
                                    
                                    @if($plotCode)
                                        - Lot {{ $plotCode }}
                                    @endif
                                </option>
                            @endforeach
                        </select>

                        <div class="small text-muted mt-2">
                            Hanya laporan kematian yang telah disahkan dan belum mempunyai tempahan aktif akan dipaparkan.
                        </div>
                    </div>

                    <div class="soft-card p-4 mb-4" id="deceasedInfoCard">
                        <div class="d-flex align-items-start gap-4 flex-wrap">
                            <div class="deceased-icon deceased-icon-memorial">
                                <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <!-- Batu nisan -->
                                    <path d="M32 12C27.5 12 24 15.5 24 20V28H40V20C40 15.5 36.5 12 32 12Z"
                                        stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M24 28H40V50H24V28Z"
                                        stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>

                                    <!-- Bulan sabit kecil -->
                                    <path d="M34.5 19.5C33.2 17.8 31 17 29 17.6C31.6 18.7 33 21.5 32.2 24.2C31.8 25.8 30.7 27.1 29.3 27.9C31.1 28.3 33.1 27.8 34.5 26.5C37 24.4 37 21.6 34.5 19.5Z"
                                        fill="currentColor"/>

                                    <!-- Tapak -->
                                    <path d="M18 54H46"
                                        stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>

                                    <!-- Daun / rumput kiri -->
                                    <path d="M18 54C18 49.5 19.5 46.5 22.5 44"
                                        stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                                    <path d="M21 53C21 49.5 22 47.5 24.5 45.5"
                                        stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>

                                    <!-- Daun / rumput kanan -->
                                    <path d="M46 54C46 49.5 44.5 46.5 41.5 44"
                                        stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                                    <path d="M43 53C43 49.5 42 47.5 39.5 45.5"
                                        stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                                </svg>
                            </div>

                            <div class="flex-grow-1">
                                <h5 class="fw-bold text-info mb-3">
                                    Maklumat Si Mati
                                </h5>

                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <div class="section-label">Nama Si Mati</div>
                                        <div class="section-value" id="infoName">-</div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="section-label">Tarikh Meninggal</div>
                                        <div class="section-value" id="infoDeathDate">-</div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="section-label">Jantina</div>
                                        <div class="section-value" id="infoGender">-</div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="section-label">No. Lot Kubur</div>
                                        <div class="section-value" id="infoLot">-</div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="section-label">Umur</div>
                                        <div class="section-value" id="infoAge">-</div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="section-label">Tempat Kematian</div>
                                        <div class="section-value" id="infoLastAddress">-</div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="section-label">Kategori Tempahan</div>
                                        <div class="section-value text-info" id="infoDetectedCategory">-</div>
                                    </div>
                                </div>

                                <div class="waris-box mt-4">
                                    <h6 class="fw-bold text-info mb-3">
                                        <i class="bi bi-person-lines-fill me-1"></i>
                                        Maklumat Waris
                                    </h6>

                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <div class="section-label">Nama Waris</div>
                                            <div class="section-value" id="infoWarisName">-</div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="section-label">No. Telefon Waris</div>
                                            <div class="section-value" id="infoWarisPhone">-</div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="section-label">Pertalian</div>
                                            <div class="section-value" id="infoWarisRelation">-</div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="soft-card p-4">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
                            <div>
                                <h5 class="fw-bold mb-1">
                                    Pilih Jenis Kepungan / Batu Nisan
                                </h5>

                                <p class="text-muted mb-0 small">
                                    Pilih satu tempahan yang dikehendaki.
                                </p>
                            </div>

                            <div class="d-flex gap-2 flex-wrap">
                                <button type="button"
                                        class="category-pill {{ $oldCategory === 'dewasa' ? 'active' : '' }}"
                                        data-category="dewasa">
                                    <i class="bi bi-person me-1"></i> Dewasa
                                </button>

                                <button type="button"
                                        class="category-pill {{ $oldCategory === 'kanak-kanak' ? 'active' : '' }}"
                                        data-category="kanak-kanak">
                                    <i class="bi bi-person-heart me-1"></i> Kanak-kanak
                                </button>
                            </div>

                            <div class="category-note mt-2 w-100" id="categoryNote" style="display:none;">
                                Sila pilih si mati terlebih dahulu.
                            </div>
                        </div>

                        @foreach($orderOptions as $category => $options)
                            <div class="option-group"
                                 data-group="{{ $category }}"
                                 style="{{ $category === $oldCategory ? '' : 'display:none;' }}">

                                <div class="row g-3">
                                    @foreach($options as $key => $option)
                                        @php
                                            $optionImages = [];

                                            if (!empty($option['images']) && is_array($option['images'])) {
                                                $optionImages = $option['images'];
                                            } elseif (!empty($option['image']) && is_array($option['image'])) {
                                                $optionImages = $option['image'];
                                            } elseif (!empty($option['image']) && is_string($option['image'])) {
                                                $optionImages = [$option['image']];
                                            }

                                            $optionImages = array_values(array_filter($optionImages, function ($image) {
                                                return is_string($image) && trim($image) !== '';
                                            }));

                                            $previewImage = $optionImages[0] ?? null;
                                        @endphp

                                        <div class="col-md-6 col-lg-4">
                                            <div class="option-card {{ $oldCategory === $category && $oldOrderType === $key ? 'active' : '' }}"
                                                data-type="{{ $key }}"
                                                data-category="{{ $category }}"
                                                data-label="{{ $option['label'] }}"
                                                data-description="{{ $option['description'] ?? '' }}"
                                                data-amount="{{ $option['amount'] }}"
                                                data-images='@json($optionImages)'>

                                                @if($previewImage && file_exists(public_path($previewImage)))
                                                    <img src="{{ asset($previewImage) }}"
                                                        class="option-image"
                                                        alt="{{ $option['label'] }}">
                                                @else
                                                    <div class="option-placeholder">
                                                        <i class="bi bi-image"></i>
                                                    </div>
                                                @endif

                                                <div class="p-3">
                                                    <h6 class="fw-bold mb-2">
                                                        {{ $option['label'] }}
                                                    </h6>

                                                    <p class="text-muted small mb-3">
                                                        {{ $option['description'] ?? '-' }}
                                                    </p>

                                                    <div class="d-flex align-items-center justify-content-between gap-2">
                                                        <div class="price-text">
                                                            @if($option['amount'] > 0)
                                                                RM{{ number_format($option['amount'], 0) }}
                                                            @else
                                                                Harga rujuk pentadbir
                                                            @endif
                                                        </div>

                                                        <div class="option-action-wrap">
                                                            <button type="button" class="btn btn-sm btn-light border view-detail-btn">
                                                                Detail
                                                            </button>

                                                            <button type="button" class="btn btn-sm btn-outline-info choose-option-btn">
                                                                Pilih
                                                            </button>
                                                        </div>
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

                <div class="col-xl-4">
                    <div class="soft-card p-4 summary-card mb-4">
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <div class="summary-icon">
                                <i class="bi bi-clipboard-check"></i>
                            </div>

                            <div>
                                <h5 class="fw-bold mb-0">
                                    Ringkasan Tempahan
                                </h5>

                                <div class="text-muted small">
                                    Semak sebelum hantar
                                </div>
                            </div>
                        </div>

                        <div class="border-bottom pb-3 mb-3">
                            <div class="text-muted small">Nama Si Mati</div>
                            <div class="fw-bold" id="summaryName">Belum dipilih</div>
                        </div>

                        <div class="border-bottom pb-3 mb-3">
                            <div class="text-muted small">Pemohon Tempahan</div>
                            <div class="fw-bold" id="summaryPemohon">{{ $pemohonTempahan }}</div>
                        </div>

                        <div class="border-bottom pb-3 mb-3">
                            <div class="text-muted small">No. Lot Kubur</div>
                            <div class="fw-bold" id="summaryLot">-</div>
                        </div>

                        <div class="border-bottom pb-3 mb-3">
                            <div class="text-muted small">Pilihan Dipilih</div>
                            <div class="fw-bold" id="summaryOption">Belum dipilih</div>
                        </div>

                        <div class="border-bottom pb-3 mb-3">
                            <div class="text-muted small">Jumlah Bayaran</div>
                            <div class="display-6 fw-bold text-info mb-0" id="summaryAmount">RM0</div>
                        </div>

                        <div class="border-bottom pb-3 mb-3">
                            <div class="text-muted small">Status</div>
                            <span class="badge bg-warning text-dark">
                                Belum dihantar
                            </span>
                        </div>

                        <div class="form-check mb-4">
                            <input class="form-check-input"
                                   type="checkbox"
                                   name="declaration"
                                   value="1"
                                   id="declaration"
                                   {{ old('declaration') ? 'checked' : '' }}>

                            <label class="form-check-label small" for="declaration">
                                Saya mengaku maklumat yang diberikan adalah benar dan bersetuju mematuhi
                                peraturan tempahan kepuk dan batu nisan.
                            </label>
                        </div>

                        <button type="submit" class="btn btn-submit-main w-100 mb-2">
                            <i class="bi bi-send me-1"></i> Hantar Permohonan
                        </button>

                        <a href="{{ route('grave-orders.index') }}" class="btn btn-light border w-100">
                            <i class="bi bi-x-lg me-1"></i> Batal
                        </a>
                    </div>

                    <div class="soft-card p-4">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <i class="bi bi-info-circle text-info fs-4"></i>
                            <h6 class="fw-bold mb-0">
                                Panduan Ringkas
                            </h6>
                        </div>

                        <ul class="info-list ps-3 mb-0 small">
                            <li>Pastikan maklumat si mati dan waris adalah tepat sebelum membuat tempahan.</li>
                            <li>Harga yang dipaparkan adalah tertakluk kepada perubahan dari semasa ke semasa.</li>
                            <li>Permohonan akan disemak oleh pihak pentadbiran khairat.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </form>
    @endif
</div>

<div class="modal fade" id="orderDetailModal" tabindex="-1" aria-labelledby="orderDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 rounded-4 overflow-hidden">
            <div class="modal-header bg-white border-bottom">
                <div>
                    <h5 class="modal-title fw-bold" id="detailModalTitle">Butiran Tempahan</h5>
                    <div class="text-muted small" id="detailModalCategory">Kategori tempahan</div>
                </div>

                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>

            <div class="modal-body p-4">
                <div class="row g-4">
                    <div class="col-xl-5">
                        <div class="position-relative">
                            <img src="" alt="Gambar tempahan" class="detail-modal-image" id="detailModalImage">

                            <button type="button" class="gallery-nav gallery-prev" id="detailPrevBtn">
                                <i class="bi bi-chevron-left"></i>
                            </button>

                            <button type="button" class="gallery-nav gallery-next" id="detailNextBtn">
                                <i class="bi bi-chevron-right"></i>
                            </button>
                        </div>

                        <div class="d-flex gap-2 mt-3 flex-wrap" id="detailThumbContainer"></div>
                    </div>

                    <div class="col-xl-7">
                        <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-3">
                            <div>
                                <h4 class="fw-bold mb-2" id="detailModalName">Nama Tempahan</h4>
                                <p class="text-muted mb-0" id="detailModalDescription">
                                    Penerangan tempahan.
                                </p>
                            </div>

                            <span class="badge bg-info-transparent text-info px-3 py-2" id="detailModalBadge">
                                Dewasa
                            </span>
                        </div>

                        <div class="detail-price mb-3" id="detailModalAmount">
                            RM0
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <div class="detail-info-box">
                                    <div class="text-muted small">Kategori</div>
                                    <div class="fw-bold" id="detailInfoCategory">-</div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="detail-info-box">
                                    <div class="text-muted small">Jenis Tempahan</div>
                                    <div class="fw-bold" id="detailInfoType">-</div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="detail-info-box">
                                    <div class="text-muted small">Status Pilihan</div>
                                    <div class="fw-bold" id="detailInfoAvailability">-</div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h6 class="fw-bold mb-2">Maklumat Ringkas</h6>
                            <ul class="detail-feature-list ps-3 mb-0">
                                <li>Tempahan akan disemak oleh pihak pentadbiran khairat.</li>
                                <li>Harga adalah tertakluk kepada pengesahan pihak pentadbiran.</li>
                                <li>Pemasangan tertakluk kepada peraturan tanah perkuburan.</li>
                            </ul>
                        </div>

                        <div class="alert alert-light border rounded-3 small mb-4">
                            <i class="bi bi-info-circle text-info me-1"></i>
                            Sila pastikan maklumat si mati dan pilihan tempahan adalah tepat sebelum menghantar permohonan.
                        </div>

                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-submit-main" id="detailChooseBtn">
                                <i class="bi bi-cart-check me-1"></i> Pilih Tempahan
                            </button>

                            <button type="button" class="btn btn-light border" data-bs-dismiss="modal">
                                Kembali
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const deathReports = @json($deathReportsData);

    const deathReportSelect = document.getElementById('deathReportSelect');

    const infoName = document.getElementById('infoName');
    const infoDeathDate = document.getElementById('infoDeathDate');
    const infoGender = document.getElementById('infoGender');
    const infoLot = document.getElementById('infoLot');
    const infoAge = document.getElementById('infoAge');
    const infoDetectedCategory = document.getElementById('infoDetectedCategory');

    const infoWarisName = document.getElementById('infoWarisName');
    const infoWarisPhone = document.getElementById('infoWarisPhone');
    const infoWarisRelation = document.getElementById('infoWarisRelation');
    const infoLastAddress = document.getElementById('infoLastAddress');

    const summaryName = document.getElementById('summaryName');
    const summaryPemohon = document.getElementById('summaryPemohon');
    const summaryLot = document.getElementById('summaryLot');

    const categoryInput = document.getElementById('categoryInput');
    const orderTypeInput = document.getElementById('orderTypeInput');
    const summaryOption = document.getElementById('summaryOption');
    const summaryAmount = document.getElementById('summaryAmount');

    const declarationInput = document.getElementById('declaration');
    const categoryNote = document.getElementById('categoryNote');

    const stepOne = document.getElementById('stepOne');
    const stepTwo = document.getElementById('stepTwo');
    const stepThree = document.getElementById('stepThree');
    const stepFour = document.getElementById('stepFour');

    let selectedDetectedCategory = null;
    let currentModalImages = [];
    let currentModalIndex = 0;

    function formatAmount(amount) {
        amount = parseFloat(amount);

        if (amount > 0) {
            return 'RM' + amount.toLocaleString('ms-MY', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            });
        }

        return 'Rujuk pentadbir';
    }

    function getSelectedDeathReport() {
        if (!deathReportSelect || !deathReportSelect.value) {
            return null;
        }

        const selectedId = parseInt(deathReportSelect.value);
        return deathReports.find(item => item.id === selectedId) || null;
    }

    function showCategoryGroup(category) {
        document.querySelectorAll('.category-pill').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.category === category);
        });

        document.querySelectorAll('.option-group').forEach(group => {
            group.style.display = group.dataset.group === category ? '' : 'none';
        });
    }

    function resetSelectedOption() {
        document.querySelectorAll('.option-card').forEach(card => {
            card.classList.remove('active');
        });

        orderTypeInput.value = '';
        summaryOption.textContent = 'Belum dipilih';
        summaryAmount.textContent = 'RM0';
    }

    function updateCategoryLock() {
        const selected = getSelectedDeathReport();

        if (!selected) {
            selectedDetectedCategory = null;

            document.querySelectorAll('.option-card').forEach(card => {
                card.classList.add('disabled-option');
                const btn = card.querySelector('.choose-option-btn');
                if (btn) {
                    btn.disabled = true;
                    btn.textContent = 'Pilih si mati dahulu';
                }
            });

            if (categoryNote) {
                categoryNote.style.display = '';
                categoryNote.textContent = 'Sila pilih waris / si mati dahulu sebelum memilih tempahan.';
            }

            return;
        }

        selectedDetectedCategory = selected.detected_category;

        document.querySelectorAll('.option-card').forEach(card => {
            const cardCategory = card.dataset.category;
            const btn = card.querySelector('.choose-option-btn');

            if (cardCategory === selectedDetectedCategory) {
                card.classList.remove('disabled-option');

                if (btn) {
                    btn.disabled = false;
                    btn.textContent = 'Pilih';
                    btn.classList.remove('btn-outline-secondary');
                    btn.classList.add('btn-outline-info');
                }
            } else {
                card.classList.add('disabled-option');
                card.classList.remove('active');

                if (btn) {
                    btn.disabled = true;
                    btn.textContent = 'Tidak tersedia';
                    btn.classList.remove('btn-outline-info');
                    btn.classList.add('btn-outline-secondary');
                }
            }
        });

        if (categoryNote) {
            categoryNote.style.display = '';
            categoryNote.textContent = selectedDetectedCategory === 'kanak-kanak'
                ? 'Kategori tempahan telah ditetapkan kepada Kanak-kanak berdasarkan maklumat si mati.'
                : 'Kategori tempahan telah ditetapkan kepada Dewasa berdasarkan maklumat si mati.';
        }
    }

    function updateSteps() {
        const hasDeathReport = deathReportSelect && deathReportSelect.value !== '';
        const hasOrderType = orderTypeInput && orderTypeInput.value !== '';
        const hasDeclaration = declarationInput && declarationInput.checked;

        stepOne.classList.add('active');

        if (hasDeathReport) {
            stepTwo.classList.add('active');
        } else {
            stepTwo.classList.remove('active');
        }

        if (hasDeathReport && hasOrderType) {
            stepThree.classList.add('active');
        } else {
            stepThree.classList.remove('active');
        }

        if (hasDeathReport && hasOrderType && hasDeclaration) {
            stepFour.classList.add('active');
        } else {
            stepFour.classList.remove('active');
        }
    }

    function resetDeathInfo() {
        infoName.textContent = '-';
        infoDeathDate.textContent = '-';
        infoGender.textContent = '-';
        infoLot.textContent = '-';

        if (infoAge) {
            infoAge.textContent = '-';
        }

        if (infoDetectedCategory) {
            infoDetectedCategory.textContent = '-';
        }

        infoWarisName.textContent = '-';
        infoWarisPhone.textContent = '-';
        infoWarisRelation.textContent = '-';
        infoLastAddress.textContent = '-';

        summaryName.textContent = 'Belum dipilih';
        summaryPemohon.textContent = @json($pemohonTempahan);
        summaryLot.textContent = '-';

        selectedDetectedCategory = null;
        resetSelectedOption();
        updateCategoryLock();
        updateSteps();
    }

    function updateDeathInfo() {
        const selected = getSelectedDeathReport();

        if (!selected) {
            resetDeathInfo();
            return;
        }

        infoName.textContent = selected.nama_si_mati || '-';
        infoDeathDate.textContent = selected.tarikh_meninggal || '-';
        infoGender.textContent = selected.jantina || '-';
        infoLot.textContent = selected.plot_code || '-';

        if (infoAge) {
            infoAge.textContent = selected.umur !== null ? selected.umur + ' tahun' : '-';
        }

        if (infoDetectedCategory) {
            infoDetectedCategory.textContent = selected.detected_category_label || '-';
        }

        infoWarisName.textContent = selected.nama_waris || '-';
        infoWarisPhone.textContent = selected.no_tel_waris || '-';
        infoWarisRelation.textContent = selected.pertalian_waris || '-';
        infoLastAddress.textContent = selected.alamat_terakhir || '-';

        summaryName.textContent = selected.nama_si_mati || '-';
        summaryPemohon.textContent = @json($pemohonTempahan);
        summaryLot.textContent = selected.plot_code || '-';

        selectedDetectedCategory = selected.detected_category;

        categoryInput.value = selectedDetectedCategory;
        showCategoryGroup(selectedDetectedCategory);
        resetSelectedOption();
        updateCategoryLock();
        updateSteps();
    }

    if (deathReportSelect) {
        deathReportSelect.addEventListener('change', updateDeathInfo);
    }

    document.querySelectorAll('.category-pill').forEach(button => {
        button.addEventListener('click', function () {
            const category = this.dataset.category;

            showCategoryGroup(category);

            if (categoryInput) {
                categoryInput.value = category;
            }

            resetSelectedOption();
            updateCategoryLock();
            updateSteps();
        });
    });

    function getCardImages(card) {
        try {
            const images = JSON.parse(card.dataset.images || '[]');

            if (Array.isArray(images) && images.length > 0) {
                return images.map(path => {
                    if (!path) return null;
                    return "{{ asset('') }}" + path;
                }).filter(Boolean);
            }
        } catch (e) {
            console.error('Gagal baca data-images', e);
        }

        const img = card.querySelector('.option-image');

        if (img && img.getAttribute('src')) {
            return [img.getAttribute('src')];
        }

        return [];
    }

    function renderModalGallery() {
        const modalImage = document.getElementById('detailModalImage');
        const thumbContainer = document.getElementById('detailThumbContainer');
        const prevBtn = document.getElementById('detailPrevBtn');
        const nextBtn = document.getElementById('detailNextBtn');

        if (!currentModalImages.length) {
            modalImage.src = '';
            modalImage.style.display = 'none';
            thumbContainer.innerHTML = '';
            prevBtn.style.display = 'none';
            nextBtn.style.display = 'none';
            return;
        }

        modalImage.style.display = '';
        modalImage.src = currentModalImages[currentModalIndex];

        thumbContainer.innerHTML = '';

        currentModalImages.forEach((image, index) => {
            const thumb = document.createElement('img');
            thumb.src = image;
            thumb.alt = 'Thumbnail ' + (index + 1);
            thumb.className = 'detail-thumb' + (index === currentModalIndex ? ' active' : '');
            thumb.addEventListener('click', function () {
                currentModalIndex = index;
                renderModalGallery();
            });

            thumbContainer.appendChild(thumb);
        });

        if (currentModalImages.length > 1) {
            prevBtn.style.display = '';
            nextBtn.style.display = '';
        } else {
            prevBtn.style.display = 'none';
            nextBtn.style.display = 'none';
        }
    }

function chooseOptionCard(card) {
    const selected = getSelectedDeathReport();

    if (!selected) {
        alert('Sila pilih waris / si mati terlebih dahulu.');
        return;
    }

    const category = card.dataset.category;

    if (category !== selected.detected_category) {
        alert(
            selected.detected_category === 'kanak-kanak'
                ? 'Sila pilih tempahan kategori kanak-kanak sahaja.'
                : 'Sila pilih tempahan kategori dewasa sahaja.'
        );
        return;
    }

    const type = card.dataset.type;
    const label = card.dataset.label;
    const amount = card.dataset.amount;

    document.querySelectorAll('.option-card').forEach(item => {
        item.classList.remove('active');
    });

    card.classList.add('active');

    categoryInput.value = category;
    orderTypeInput.value = type;
    summaryOption.textContent = label || 'Belum dipilih';
    summaryAmount.textContent = formatAmount(amount);

    updateSteps();
}

function openDetailModal(card) {
    const selected = getSelectedDeathReport();

    if (!selected) {
        alert('Sila pilih waris / si mati terlebih dahulu.');
        return;
    }

    const category = card.dataset.category;
    const type = card.dataset.type;
    const label = card.dataset.label;
    const description = card.dataset.description || '-';
    const amount = card.dataset.amount;
    const images = getCardImages(card);

    const isAvailable = category === selected.detected_category;

    document.getElementById('detailModalTitle').textContent = 'Butiran Tempahan Kepuk / Nisan';
    document.getElementById('detailModalCategory').textContent = category === 'kanak-kanak'
        ? 'Kategori Kanak-kanak'
        : 'Kategori Dewasa';

    document.getElementById('detailModalName').textContent = label;
    document.getElementById('detailModalDescription').textContent = description;
    document.getElementById('detailModalAmount').textContent = formatAmount(amount);
    document.getElementById('detailModalBadge').textContent = category === 'kanak-kanak'
        ? 'Kanak-kanak'
        : 'Dewasa';

    document.getElementById('detailInfoCategory').textContent = category === 'kanak-kanak'
        ? 'Kanak-kanak'
        : 'Dewasa';

    document.getElementById('detailInfoType').textContent = type;
    document.getElementById('detailInfoAvailability').textContent = isAvailable
        ? 'Boleh dipilih'
        : 'Tidak sesuai dengan kategori tempahan';

    currentModalImages = images;
    currentModalIndex = 0;
    renderModalGallery();

    const detailChooseBtn = document.getElementById('detailChooseBtn');

    detailChooseBtn.disabled = !isAvailable;
    detailChooseBtn.innerHTML = isAvailable
        ? '<i class="bi bi-cart-check me-1"></i> Pilih Tempahan'
        : 'Tidak Tersedia';

    detailChooseBtn.onclick = function () {
        if (!isAvailable) {
            return;
        }

        chooseOptionCard(card);

        const modalElement = document.getElementById('orderDetailModal');
        const modalInstance = bootstrap.Modal.getInstance(modalElement);

        if (modalInstance) {
            modalInstance.hide();
        }
    };

    const modal = new bootstrap.Modal(document.getElementById('orderDetailModal'));
    modal.show();
}

document.querySelectorAll('.option-card').forEach(card => {
    card.addEventListener('click', function (event) {
        if (event.target.closest('.choose-option-btn')) {
            chooseOptionCard(this);
            return;
        }

        if (event.target.closest('.view-detail-btn')) {
            openDetailModal(this);
            return;
        }

        openDetailModal(this);
    });
});

    if (declarationInput) {
        declarationInput.addEventListener('change', updateSteps);
    }

    updateDeathInfo();
    updateCategoryLock();
    updateSteps();


    const detailPrevBtn = document.getElementById('detailPrevBtn');
    const detailNextBtn = document.getElementById('detailNextBtn');

    if (detailPrevBtn) {
        detailPrevBtn.addEventListener('click', function () {
            if (!currentModalImages.length) return;

            currentModalIndex = (currentModalIndex - 1 + currentModalImages.length) % currentModalImages.length;
            renderModalGallery();
        });
    }

    if (detailNextBtn) {
        detailNextBtn.addEventListener('click', function () {
            if (!currentModalImages.length) return;

            currentModalIndex = (currentModalIndex + 1) % currentModalImages.length;
            renderModalGallery();
        });
    }

</script>
@endsection