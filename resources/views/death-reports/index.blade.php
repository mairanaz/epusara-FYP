@extends('layouts.app')

@section('content')
<div class="container-fluid">

    @php
        $search = request('search');
        $statusFilter = request('status');
        $categoryFilter = request('category');

        $filteredReports = $reports->filter(function ($report) use ($search, $statusFilter, $categoryFilter) {
            $matchSearch = true;
            $matchStatus = true;
            $matchCategory = true;

            if ($search) {
                $keyword = strtolower($search);
                $matchSearch =
                    str_contains(strtolower($report->nama_si_mati ?? ''), $keyword) ||
                    str_contains(strtolower($report->no_kp_si_mati ?? ''), $keyword) ||
                    str_contains(strtolower($report->nama_pelapor ?? ''), $keyword);
            }

            if ($statusFilter) {
                $matchStatus = $report->status === $statusFilter;
            }

            if ($categoryFilter) {
                $matchCategory = $report->deceased_type === $categoryFilter;
            }

            return $matchSearch && $matchStatus && $matchCategory;
        });

        $totalReports = $reports->count();
        $pendingReports = $reports->where('status', 'menunggu_semakan')->count();
        $approvedReports = $reports->where('status', 'disahkan')->count();
        $actionNeededReports = $reports->whereIn('status', ['ditolak', 'perlukan_dokumen_tambahan'])->count();

                $statusBadgeClass = function ($status) {
            return match($status) {
                'menunggu_semakan' => 'warning',
                'disahkan' => 'success',
                'ditolak' => 'danger',
                'perlukan_dokumen_tambahan' => 'info',
                default => 'secondary',
            };
        };

        $statusLabel = function ($status) {
            return match($status) {
                'menunggu_semakan' => 'Menunggu Semakan',
                'disahkan' => 'Disahkan',
                'ditolak' => 'Ditolak',
                'perlukan_dokumen_tambahan' => 'Perlukan Dokumen Tambahan',
                default => ucfirst(str_replace('_', ' ', $status)),
            };
        };

        $categoryLabel = function ($category) {
            return match($category) {
                'member' => 'Ahli Utama',
                'dependent' => 'Tanggungan',
                default => '-',
            };
        };

        $referenceNo = function ($report) {
            $year = optional($report->created_at)->format('Y') ?? now()->format('Y');
            return 'LRK-' . $year . '-' . str_pad($report->id, 4, '0', STR_PAD_LEFT);
        };
    @endphp

    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div id="tour-death-report-header">
            <h1 class="page-title fw-semibold fs-20 mb-1">Status Laporan Kematian</h1>
            <p class="text-muted mb-0">
                Semak rekod laporan kematian yang telah dihantar dan status semakan oleh pentadbir.
            </p>
        </div>

        <div class="mt-3 mt-md-0">
            <a href="{{ route('death-reports.create') }}"
            id="tour-new-death-report"
            class="btn btn-info">
                <i class="bx bx-plus me-1"></i> Lapor Kematian Baharu
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if(session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berjaya',
                text: @json(session('success')),
                confirmButtonText: 'OK',
                confirmButtonColor: '#8b5cf6'
            });
        </script>
    @endif

    @if(session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Ralat',
                text: @json(session('error')),
                confirmButtonText: 'OK',
                confirmButtonColor: '#8b5cf6'
            });
        </script>
    @endif

    <div class="row g-3 mb-4" id="tour-death-report-summary">
        <div class="col-xl-3 col-md-6">
            <div class="card custom-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted small mb-1">Jumlah Laporan</div>
                            <h3 class="mb-0 fw-bold">{{ $totalReports }}</h3>
                        </div>
                        <div class="avatar avatar-md bg-info-transparent rounded-circle">
                            <i class="bx bx-file text-info fs-20"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card custom-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted small mb-1">Menunggu Semakan</div>
                            <h3 class="mb-0 fw-bold">{{ $pendingReports }}</h3>
                        </div>
                        <div class="avatar avatar-md bg-warning-transparent rounded-circle">
                            <i class="bx bx-time-five text-warning fs-20"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card custom-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted small mb-1">Disahkan</div>
                            <h3 class="mb-0 fw-bold">{{ $approvedReports }}</h3>
                        </div>
                        <div class="avatar avatar-md bg-success-transparent rounded-circle">
                            <i class="bx bx-check-shield text-success fs-20"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card custom-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted small mb-1">Perlu Tindakan</div>
                            <h3 class="mb-0 fw-bold">{{ $actionNeededReports }}</h3>
                        </div>
                        <div class="avatar avatar-md bg-danger-transparent rounded-circle">
                            <i class="bx bx-error-circle text-danger fs-20"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card custom-card border-0 shadow-sm mb-4" id="tour-death-report-filter">
        <div class="card-header bg-white border-bottom">
            <h5 class="mb-0 fw-semibold">Carian dan Tapisan</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('death-reports.index') }}">
                <div class="row g-3 align-items-end">
                    <div class="col-xl-5 col-md-6">
                        <label class="form-label fw-semibold">Carian</label>
                        <input type="text"
                               name="search"
                               class="form-control"
                               value="{{ request('search') }}"
                               placeholder="Cari nama si mati, no. KP atau nama pelapor">
                    </div>

                    <div class="col-xl-3 col-md-3">
                        <label class="form-label fw-semibold">Kategori</label>
                        <select name="category" class="form-select">
                            <option value="">-- Semua Kategori --</option>
                            <option value="member" {{ request('category') === 'member' ? 'selected' : '' }}>Ahli Utama</option>
                            <option value="dependent" {{ request('category') === 'dependent' ? 'selected' : '' }}>Tanggungan</option>
                        </select>
                    </div>

                    <div class="col-xl-2 col-md-3">
                        <label class="form-label fw-semibold">Status</label>
                        <select name="status" class="form-select">
                            <option value="">-- Semua Status --</option>
                            <option value="menunggu_semakan" {{ request('status') === 'menunggu_semakan' ? 'selected' : '' }}>Menunggu Semakan</option>
                            <option value="disahkan" {{ request('status') === 'disahkan' ? 'selected' : '' }}>Disahkan</option>
                            <option value="ditolak" {{ request('status') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                            <option value="perlukan_dokumen_tambahan" {{ request('status') === 'perlukan_dokumen_tambahan' ? 'selected' : '' }}>Perlu Dokumen</option>
                        </select>
                    </div>

                    <div class="col-xl-2 col-md-12">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-info w-100">
                                <i class="bx bx-search me-1"></i> Cari
                            </button>
                            <a href="{{ route('death-reports.index') }}" class="btn btn-light border">
                                Reset
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card custom-card border-0 shadow-sm" id="tour-death-report-table">
        <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between">
            <h5 class="mb-0 fw-semibold">Rekod Laporan Kematian</h5>
            <span class="badge bg-light text-dark border">
                {{ $filteredReports->count() }} rekod
            </span>
        </div>

        <div class="card-body p-0">
            @if($filteredReports->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Bil</th>
                                <th>No. Rujukan</th>
                                <th>Nama Si Mati</th>
                                <th>Kategori</th>
                                <th>Tarikh Meninggal</th>
                                <th>Tarikh Hantar</th>
                                <th>Status</th>
                                <th>Lot Kubur</th>
                                <th class="text-center pe-4">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($filteredReports->values() as $index => $report)
                                <tr @if($index === 0) id="tour-first-death-report-record" @endif>
                                    <td class="ps-4 fw-semibold">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ $referenceNo($report) }}</div>
                                        <div class="text-muted small">
                                            {{ optional($report->created_at)->format('d/m/Y h:i A') ?? '-' }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ $report->nama_si_mati }}</div>
                                        <div class="text-muted small">{{ $report->no_kp_si_mati ?? '-' }}</div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            {{ $categoryLabel($report->deceased_type) }}
                                        </span>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($report->tarikh_meninggal)->format('d/m/Y') }}</td>
                                    <td>{{ optional($report->created_at)->format('d/m/Y') ?? '-' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $statusBadgeClass($report->status) }}">
                                            {{ $statusLabel($report->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($report->burial_plot_code || $report->burial_lot_no)
                                            <div class="fw-semibold">{{ $report->burial_plot_code ?? $report->burial_lot_no }}</div>
                                            <div class="text-muted small">{{ $report->burial_zone ?? '-' }}</div>
                                        @else
                                            <span class="text-muted">Belum ditetapkan</span>
                                        @endif
                                    </td>
                                    <td class="text-center pe-4">
                                        <a href="{{ route('death-reports.show', $report->id) }}"
                                        @if($index === 0) id="tour-view-death-report" @endif
                                        class="btn btn-sm btn-outline-info">
                                            <i class="bx bx-show me-1"></i> Lihat
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-5 text-center">
                    <div class="mb-3">
                        <i class="bx bx-folder-open fs-1 text-muted"></i>
                    </div>
                    <h5 class="fw-semibold mb-1">Tiada rekod laporan dijumpai</h5>
                    <p class="text-muted mb-3">
                        Tiada laporan kematian ditemui bagi carian atau tapisan yang dipilih.
                    </p>
                    <a href="{{ route('death-reports.create') }}" class="btn btn-info">
                        <i class="bx bx-plus me-1"></i> Hantar Laporan Baharu
                    </a>
                </div>
            @endif
        </div>
    </div>
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
                    element: '#tour-death-report-header',
                    popover: {
                        title: 'Laporan Kematian',
                        description: 'Halaman ini digunakan untuk menghantar laporan kematian ahli atau tanggungan serta menyemak status laporan yang telah dihantar.',
                        side: 'bottom',
                        align: 'start'
                    }
                },
                {
                    element: '#tour-new-death-report',
                    popover: {
                        title: 'Buat Laporan Baharu',
                        description: 'Klik di sini untuk melaporkan kematian ahli atau tanggungan. Pastikan maklumat si mati dan dokumen berkaitan disediakan sebelum menghantar laporan.',
                        side: 'bottom',
                        align: 'end'
                    }
                },
                {
                    element: '#tour-death-report-summary',
                    popover: {
                        title: 'Ringkasan Status Laporan',
                        description: 'Bahagian ini menunjukkan jumlah laporan anda yang sedang disemak, telah disahkan atau memerlukan tindakan lanjut.',
                        side: 'bottom',
                        align: 'center'
                    }
                },
                {
                    element: '#tour-death-report-filter',
                    popover: {
                        title: 'Cari dan Tapis Rekod',
                        description: 'Gunakan carian untuk mencari laporan berdasarkan nama si mati, nombor kad pengenalan atau nama pelapor. Anda juga boleh menapis mengikut kategori dan status.',
                        side: 'top',
                        align: 'center'
                    }
                },
                {
                    element: '#tour-death-report-table',
                    popover: {
                        title: 'Rekod Laporan Anda',
                        description: 'Semua laporan yang telah dihantar akan dipaparkan di sini bersama nombor rujukan, nama si mati, tarikh, status dan maklumat lot kubur.',
                        side: 'top',
                        align: 'center'
                    }
                },
                {
                    element: '#tour-first-death-report-record',
                    popover: {
                        title: 'Status dan Lot Kubur',
                        description: 'Status menunjukkan perkembangan laporan anda. Setelah lot kubur ditetapkan, kod lokasi kubur akan dipaparkan untuk rujukan.',
                        side: 'top',
                        align: 'center'
                    }
                },
                {
                    element: '#tour-view-death-report',
                    popover: {
                        title: 'Lihat Maklumat Lengkap',
                        description: 'Klik butang ini untuk melihat butiran penuh laporan kematian dan maklumat pengebumian bagi rekod yang dipilih.',
                        side: 'left',
                        align: 'center'
                    }
                }
            ];

            /*
            |--------------------------------------------------------------------------
            | Abaikan step yang elemennya tidak wujud
            |--------------------------------------------------------------------------
            | Contohnya, jika tiada rekod laporan, step untuk row pertama dan
            | butang Lihat tidak akan dipaparkan.
            */
            const availableSteps = allSteps.filter(function (step) {
                return document.querySelector(step.element);
            });

            if (availableSteps.length === 0) {
                console.warn('Tiada elemen tour ditemui pada halaman ini.');
                return;
            }

            let deathReportTour;

            deathReportTour = driver({
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
                    const currentIndex = deathReportTour.getActiveIndex() ?? 0;
                    window.updateEpusaraTourPopover(
                        deathReportTour,
                        currentIndex,
                        availableSteps.length
                    );
                },

                steps: availableSteps
            });

            deathReportTour.drive();

            deathReportTour.drive();
        });
    });
</script>
@endpush