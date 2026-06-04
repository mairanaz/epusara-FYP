@extends('layouts.app')

@section('content')
<style>
    .grave-page-header {
        background: linear-gradient(135deg, #0f766e 0%, #115e59 100%);
        border-radius: 22px;
        padding: 28px 30px;
        color: #fff;
        position: relative;
        overflow: hidden;
    }

    .grave-page-header::after {
        content: '';
        position: absolute;
        width: 180px;
        height: 180px;
        border-radius: 50%;
        background: rgba(255,255,255,.08);
        right: -45px;
        top: -70px;
    }

    .grave-page-header::before {
        content: '';
        position: absolute;
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: rgba(255,255,255,.06);
        right: 100px;
        bottom: -70px;
    }

    .header-icon-box {
        width: 58px;
        height: 58px;
        border-radius: 18px;
        background: rgba(255,255,255,.16);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 29px;
        flex-shrink: 0;
    }

    .summary-card {
        border: 0;
        border-radius: 18px;
        box-shadow: 0 3px 16px rgba(15, 23, 42, .06);
        height: 100%;
    }

    .summary-icon {
        width: 46px;
        height: 46px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 23px;
    }

    .icon-teal {
        background: #e6fffb;
        color: #0f766e;
    }

    .icon-blue {
        background: #eff6ff;
        color: #2563eb;
    }

    .icon-amber {
        background: #fff7ed;
        color: #ea580c;
    }

    .record-section {
        border: 0;
        border-radius: 22px;
        box-shadow: 0 4px 20px rgba(15, 23, 42, .06);
        overflow: hidden;
    }

    .grave-record-card {
        border: 1px solid #edf2f7;
        border-radius: 20px;
        padding: 20px;
        background: #fff;
        height: 100%;
        transition: .18s ease-in-out;
        position: relative;
    }

    .grave-record-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 26px rgba(15, 23, 42, .08);
        border-color: #d7e8e7;
    }

    .deceased-avatar {
        width: 54px;
        height: 54px;
        border-radius: 17px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #edfdfb;
        color: #0f766e;
        font-size: 26px;
        flex-shrink: 0;
    }

    .status-ready {
        color: #15803d;
        background: #dcfce7;
        padding: 6px 11px;
        border-radius: 50rem;
        font-size: 11px;
        font-weight: 700;
    }

    .status-pending {
        color: #92400e;
        background: #fef3c7;
        padding: 6px 11px;
        border-radius: 50rem;
        font-size: 11px;
        font-weight: 700;
    }

    .lot-highlight {
        margin-top: 18px;
        background: #f5fbfa;
        border: 1px dashed #b7dfdb;
        border-radius: 16px;
        padding: 14px 16px;
    }

    .lot-code {
        color: #0f766e;
        font-weight: 800;
        font-size: 22px;
        letter-spacing: .4px;
    }

    .info-row {
        display: flex;
        align-items: center;
        gap: 9px;
        font-size: 13px;
        color: #64748b;
        margin-top: 12px;
    }

    .info-row i {
        font-size: 18px;
        color: #0f766e;
    }

    .btn-map {
        background: #0f766e;
        border-color: #0f766e;
        color: #fff;
        border-radius: 12px;
        padding: 11px 16px;
        font-weight: 600;
        transition: .18s ease;
    }

    .btn-map:hover {
        background: #115e59;
        border-color: #115e59;
        color: #fff;
    }

    .btn-unavailable {
        border-radius: 12px;
        padding: 11px 16px;
        background: #f1f5f9;
        color: #94a3b8;
        font-weight: 600;
        border: 0;
    }

    .grave-image-label {
        border-radius: 10px;
        padding: 6px 10px;
        font-size: 12px;
        font-weight: 600;
    }

    .empty-location {
        padding: 65px 20px;
        text-align: center;
    }

    .empty-icon {
        width: 78px;
        height: 78px;
        border-radius: 24px;
        background: #effaf8;
        color: #0f766e;
        margin: 0 auto 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 39px;
    }

    @media (max-width: 576px) {
        .grave-page-header {
            padding: 22px 20px;
        }

        .grave-record-card {
            padding: 17px;
        }
    }
</style>

<div class="container-fluid">

    @php
        $totalRecord = $deathReports->total();
        $totalWithPlot = $deathReports->getCollection()
            ->filter(fn($report) => $report->final_burial_plot)
            ->count();

        $totalWithImage = $deathReports->getCollection()
            ->filter(fn($report) => $report->final_burial_plot && $report->final_burial_plot->grave_image)
            ->count();
    @endphp

    {{-- Header --}}
    <div class="grave-page-header my-4" id="tour-grave-header">
        <div class="d-flex align-items-center gap-3 position-relative" style="z-index: 1;">
            <div class="header-icon-box">
                <i class="bx bx-map-alt"></i>
            </div>
            <div>
                <h1 class="fs-22 fw-bold mb-1 text-white">Lokasi Kubur Keluarga</h1>
                <p class="mb-0 text-white-50">
                    Semak lokasi lot kubur dan panduan ziarah ahli keluarga anda.
                </p>
            </div>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4">
            <i class="bx bx-error-circle me-1"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Ringkasan --}}
    <div class="row g-3 mb-4" id="tour-grave-summary">
        <div class="col-md-4">
            <div class="card summary-card">
                <div class="card-body d-flex align-items-center gap-3 p-3">
                    <div class="summary-icon icon-teal">
                        <i class="bx bx-user"></i>
                    </div>
                    <div>
                        <div class="text-muted small mb-1">Jumlah Rekod Keluarga</div>
                        <div class="fw-bold fs-22">{{ $totalRecord }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card summary-card">
                <div class="card-body d-flex align-items-center gap-3 p-3">
                    <div class="summary-icon icon-blue">
                        <i class="bx bx-map-pin"></i>
                    </div>
                    <div>
                        <div class="text-muted small mb-1">Lokasi Tersedia</div>
                        <div class="fw-bold fs-22">{{ $totalWithPlot }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card summary-card">
                <div class="card-body d-flex align-items-center gap-3 p-3">
                    <div class="summary-icon icon-amber">
                        <i class="bx bx-image"></i>
                    </div>
                    <div>
                        <div class="text-muted small mb-1">Gambar Kubur Tersedia</div>
                        <div class="fw-bold fs-22">{{ $totalWithImage }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Senarai Lokasi --}}
    <div class="card record-section" id="tour-grave-record-section">
        <div class="card-header bg-white border-0 px-4 pt-4 pb-2">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                    <h5 class="fw-bold mb-1">Rekod Lokasi Kubur</h5>
                    <p class="text-muted small mb-0">
                        Klik lihat lokasi untuk paparan peta dan panduan perjalanan ke kubur.
                    </p>
                </div>
            </div>
        </div>

                <div class="card-body p-4">
            @if($deathReports->count() > 0)

                @php
                    $tourAvailableRecordAssigned = false;
                    $tourPendingRecordAssigned = false;
                @endphp

                <div class="row g-3">
                    @foreach($deathReports as $report)
                        @php
                            $plot = $report->final_burial_plot;

                            $isTourAvailableRecord = $plot && !$tourAvailableRecordAssigned;
                            $isTourPendingRecord = !$plot && !$tourPendingRecordAssigned;

                            if ($isTourAvailableRecord) {
                                $tourAvailableRecordAssigned = true;
                            }

                            if ($isTourPendingRecord) {
                                $tourPendingRecordAssigned = true;
                            }
                        @endphp

                        <div class="col-xl-4 col-md-6">
                            <div class="grave-record-card"
                                 @if($isTourAvailableRecord)
                                     id="tour-grave-available-card"
                                 @elseif($isTourPendingRecord)
                                     id="tour-grave-pending-card"
                                 @endif>

                                <div class="d-flex align-items-start justify-content-between gap-2">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="deceased-avatar">
                                            <i class="bx bx-user"></i>
                                        </div>

                                        <div>
                                            <h6 class="fw-bold mb-1 text-dark">
                                                {{ $report->nama_si_mati }}
                                            </h6>
                                            <span class="text-muted small">
                                                {{ $report->deceased_type === 'dependent' ? 'Ahli Tanggungan' : 'Ahli Utama' }}
                                            </span>
                                        </div>
                                    </div>

                                    @if($plot)
                                        <span class="status-ready">TERSEDIA</span>
                                    @else
                                        <span class="status-pending">MENUNGGU</span>
                                    @endif
                                </div>

                                @if($plot)
                                    <div class="lot-highlight"
                                         @if($isTourAvailableRecord) id="tour-grave-lot" @endif>
                                        <div class="text-muted small mb-1">Kod Lot Kubur</div>

                                        <div class="lot-code">
                                            {{ $plot->plot_code }}
                                        </div>

                                        <div class="small text-muted mt-1">
                                            <i class="bx bx-current-location me-1"></i>
                                            {{ $plot->zone_label }}
                                        </div>
                                    </div>
                                @else
                                    <div class="lot-highlight">
                                        <div class="text-muted small mb-1">Kod Lot Kubur</div>

                                        <div class="fw-semibold text-muted">
                                            Belum ditetapkan
                                        </div>

                                        <div class="small text-muted mt-1">
                                            Lokasi akan dipaparkan selepas pengesahan admin.
                                        </div>
                                    </div>
                                @endif

                                <div class="info-row">
                                    <i class="bx bx-calendar"></i>
                                    <span>
                                        Tarikh meninggal:
                                        <strong class="text-dark">
                                            {{ $report->tarikh_meninggal ? $report->tarikh_meninggal->format('d/m/Y') : '-' }}
                                        </strong>
                                    </span>
                                </div>

                                <div class="info-row">
                                    <i class="bx bx-image-alt"></i>
                                    <span>Gambar kubur:</span>

                                    @if($plot && $plot->grave_image)
                                        <span class="grave-image-label bg-success-transparent text-success">
                                            Tersedia
                                        </span>
                                    @else
                                        <span class="grave-image-label bg-light text-muted">
                                            Tiada gambar
                                        </span>
                                    @endif
                                </div>

                                <div class="mt-4">
                                    @if($plot)
                                        <a href="{{ route('user.grave-locations.show', $report->id) }}"
                                           @if($isTourAvailableRecord) id="tour-grave-open-map" @endif
                                           class="btn btn-map w-100">
                                            <i class="bx bx-map me-1"></i>
                                            Lihat Lokasi Kubur
                                        </a>
                                    @else
                                        <button type="button"
                                                class="btn btn-unavailable w-100"
                                                disabled>
                                            <i class="bx bx-time-five me-1"></i>
                                            Lokasi Belum Tersedia
                                        </button>
                                    @endif
                                </div>

                            </div>
                        </div>
                    @endforeach
                </div>

                @if($deathReports->hasPages())
                    <div class="mt-4 d-flex justify-content-center">
                        {{ $deathReports->links() }}
                    </div>
                @endif

            @else

                <div class="empty-location" id="tour-grave-empty-state">
                    <div class="empty-icon">
                        <i class="bx bx-map"></i>
                    </div>

                    <h5 class="fw-bold mb-2">Tiada Lokasi Kubur Ditemui</h5>

                    <p class="text-muted mb-0">
                        Lokasi kubur ahli keluarga akan dipaparkan selepas lot ditetapkan oleh pentadbir.
                    </p>
                </div>

            @endif
        </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tourButton = document.getElementById('btnPageTour');

        if (!tourButton) {
            return;
        }

        tourButton.addEventListener('click', function () {
            if (!window.driver || !window.driver.js) {
                console.error('Driver.js tidak berjaya dimuatkan.');
                return;
            }

            const driver = window.driver.js.driver;

            const allSteps = [
                {
                    element: '#tour-grave-header',
                    popover: {
                        title: 'Lokasi Kubur Keluarga',
                        description: 'Halaman ini membantu anda menyemak lokasi lot kubur ahli keluarga serta mendapatkan akses kepada panduan ziarah.',
                        side: 'bottom',
                        align: 'start'
                    }
                },
                {
                    element: '#tour-grave-summary',
                    popover: {
                        title: 'Ringkasan Lokasi Kubur',
                        description: 'Bahagian ini menunjukkan jumlah rekod keluarga, bilangan lokasi kubur yang sudah tersedia dan rekod yang mempunyai gambar kubur.',
                        side: 'bottom',
                        align: 'center'
                    }
                },
                {
                    element: '#tour-grave-record-section',
                    popover: {
                        title: 'Rekod Lokasi Kubur',
                        description: 'Semua rekod kematian keluarga anda dipaparkan di sini. Lokasi hanya tersedia selepas lot kubur ditetapkan oleh pentadbir.',
                        side: 'top',
                        align: 'center'
                    }
                },
                {
                    element: '#tour-grave-empty-state',
                    popover: {
                        title: 'Belum Ada Lokasi Kubur',
                        description: 'Buat masa ini, tiada lokasi kubur dipaparkan. Lokasi akan tersedia selepas laporan kematian disahkan dan lot kubur ditetapkan oleh pentadbir.',
                        side: 'top',
                        align: 'center'
                    }
                },
                {
                    element: '#tour-grave-available-card',
                    popover: {
                        title: 'Lokasi Sudah Tersedia',
                        description: 'Rekod bertanda Tersedia bermaksud lot kubur telah ditetapkan. Anda juga boleh menyemak tarikh kematian dan status gambar kubur di kad ini.',
                        side: 'top',
                        align: 'center'
                    }
                },
                {
                    element: '#tour-grave-lot',
                    popover: {
                        title: 'Kod Lot dan Zon Kubur',
                        description: 'Kod lot digunakan untuk mengenal pasti kedudukan kubur. Maklumat zon membantu anda mengetahui bahagian perkuburan yang perlu dituju.',
                        side: 'top',
                        align: 'center'
                    }
                },
                {
                    element: '#tour-grave-open-map',
                    popover: {
                        title: 'Buka Peta Lokasi Kubur',
                        description: 'Klik butang ini untuk melihat kedudukan kubur pada peta serta panduan perjalanan ke lot kubur yang dipilih.',
                        side: 'top',
                        align: 'center'
                    }
                },
                {
                    element: '#tour-grave-pending-card',
                    popover: {
                        title: 'Lokasi Masih Menunggu',
                        description: 'Rekod ini sudah tersedia dalam sistem, tetapi lokasi kubur masih belum ditetapkan. Kod lot dan peta akan dipaparkan selepas proses pentadbir selesai.',
                        side: 'top',
                        align: 'center'
                    }
                }
            ];

            /*
            |--------------------------------------------------------------------------
            | Hanya paparkan step yang tersedia
            |--------------------------------------------------------------------------
            | Jika belum ada lot kubur yang ditetapkan, step kad lokasi,
            | kod lot dan butang peta akan dilangkau secara automatik.
            */
            const availableSteps = allSteps.filter(function (step) {
                return document.querySelector(step.element);
            });

            if (availableSteps.length === 0) {
                console.warn('Tiada elemen tour ditemui pada halaman ini.');
                return;
            }

            let graveLocationTour;

            graveLocationTour = driver({
                animate: true,
                smoothScroll: true,
                popoverClass: 'epusara-tour-popover',

                allowClose: true,
                overlayColor: '#0f172a',
                overlayOpacity: 0.58,
                stagePadding: 10,
                stageRadius: 10,
                popoverOffset: 14,
                disableActiveInteraction: true,

                showProgress: false,

                nextBtnText: 'Seterusnya →',
                prevBtnText: '← Sebelumnya',
                doneBtnText: 'Selesai',

                onPopoverRender: function () {
                    const currentIndex = graveLocationTour.getActiveIndex() ?? 0;

                    window.updateEpusaraTourPopover(
                        graveLocationTour,
                        currentIndex,
                        availableSteps.length
                    );
                },

                steps: availableSteps
            });

            graveLocationTour.drive();
        });
    });
</script>
@endpush