@extends('layouts.app')

@section('content')
<style>
    .dependent-page .summary-card {
        min-height: 100%;
    }

    .dependent-page .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .dependent-page .table {
        margin-bottom: 0;
    }

    .dependent-page .dependent-name-cell {
        min-width: 220px;
    }

    .dependent-page .action-buttons {
        min-width: 220px;
    }

    @media (max-width: 767.98px) {
        .dependent-page .page-header-breadcrumb {
            gap: 1rem;
        }

        .dependent-page .page-actions {
            width: 100%;
        }

        .dependent-page #tour-add-dependent {
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .dependent-page .card-body {
            padding: 1rem;
        }

        .dependent-page .table {
            min-width: 1050px;
            font-size: 13px;
        }

        .dependent-page .table th,
        .dependent-page .table td {
            white-space: nowrap;
            vertical-align: middle;
        }

        .dependent-page .badge {
            font-size: 11px;
        }

        .dependent-page .btn-sm {
            font-size: 12px;
            padding: 0.35rem 0.55rem;
        }

        .dependent-page .action-buttons {
            flex-wrap: nowrap !important;
        }
    }
</style>

<div class="container-fluid dependent-page">

    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div id="tour-dependent-header">
            <h1 class="page-title fw-semibold fs-18 mb-1">Senarai Tanggungan</h1>
            <p class="text-muted mb-0">Urus maklumat tanggungan ahli khairat dengan lebih teratur</p>
        </div>

        <div class="page-actions">
            <a href="{{ route('user.dependents.create') }}"
               id="tour-add-dependent"
               class="btn btn-info">
                <i class="ri-user-add-line me-1"></i> Tambah Tanggungan
            </a>
        </div>
    </div>

    <div class="row g-3 mb-4" id="tour-dependent-summary">
        <div class="col-xl-4 col-md-6 col-sm-6 col-12">
            <div class="card custom-card summary-card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="mb-1 text-muted">Jumlah Tanggungan</p>
                            <h4 class="fw-semibold mb-0">{{ $dependents->count() }}</h4>
                        </div>
                        <div class="avatar avatar-md bg-info-transparent">
                            <i class="ri-team-line fs-18 text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 col-sm-6 col-12">
            <div class="card custom-card summary-card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="mb-1 text-muted">Masih Hidup</p>
                            <h4 class="fw-semibold mb-0">
                                {{ $dependents->where('status_kehidupan', '!=', 'meninggal_dunia')->count() }}
                            </h4>
                        </div>
                        <div class="avatar avatar-md bg-success-transparent">
                            <i class="ri-user-heart-line fs-18 text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 col-sm-6 col-12">
            <div class="card custom-card summary-card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="mb-1 text-muted">Meninggal Dunia</p>
                            <h4 class="fw-semibold mb-0">
                                {{ $dependents->where('status_kehidupan', 'meninggal_dunia')->count() }}
                            </h4>
                        </div>
                        <div class="avatar avatar-md bg-danger-transparent">
                            <i class="ri-heart-pulse-line fs-18 text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Rekod Tanggungan --}}
    <div class="card custom-card mt-2" id="tour-dependent-records">
        <div class="card-header d-flex align-items-center justify-content-between">
            <div class="card-title mb-0">
                Rekod Tanggungan
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Nama</th>
                            <th>No. KP</th>
                            <th>Pasangan</th>
                            <th>Pertalian</th>
                            <th>No. Tel</th>
                            <th>Status Kehidupan</th>
                            <th>Status Tanggungan</th>
                            <th width="220">Tindakan</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($dependents as $index => $dependent)
                            <tr @if($index === 0) id="tour-first-dependent-record" @endif>
                                <td>{{ $index + 1 }}</td>

                                <td class="dependent-name-cell">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="avatar avatar-sm rounded-circle bg-info text-white d-flex align-items-center justify-content-center">
                                            {{ strtoupper(substr($dependent->name, 0, 1)) }}
                                        </span>
                                        <div>
                                            <div class="fw-semibold">{{ $dependent->name }}</div>
                                        </div>
                                    </div>
                                </td>

                                <td>{{ $dependent->no_kp }}</td>

                                <td>
                                    {{ $dependent->pasangan ? ucfirst($dependent->pasangan) : '-' }}
                                </td>

                                <td>
                                    {{ $dependent->pertalian ? ucwords($dependent->pertalian) : '-' }}
                                </td>

                                <td>{{ $dependent->no_tel ?? '-' }}</td>

                                <td>
                                    @if(($dependent->status_kehidupan ?? 'aktif') === 'meninggal_dunia')
                                        <span class="badge bg-danger">Meninggal Dunia</span>

                                        @if($dependent->tarikh_kematian)
                                            <div class="small text-muted mt-1">
                                                {{ \Carbon\Carbon::parse($dependent->tarikh_kematian)->format('d/m/Y') }}
                                            </div>
                                        @endif
                                    @else
                                        <span class="badge bg-success">Masih Hidup</span>
                                    @endif
                                </td>

                                <td>
                                    @if(($dependent->status_kehidupan ?? 'aktif') === 'meninggal_dunia')
                                        <span class="badge bg-danger">Tidak Aktif</span>

                                    @elseif(($dependent->status_tanggungan ?? 'aktif') === 'aktif')
                                        <span class="badge bg-success">Aktif</span>

                                    @elseif($dependent->status_tanggungan === 'tidak_layak')
                                        <span class="badge bg-danger">Tidak Layak</span>

                                        @if($dependent->sebab_tidak_layak)
                                            <div class="small text-muted mt-1">
                                                {{ $dependent->sebab_tidak_layak }}
                                            </div>
                                        @endif

                                        @if($dependent->tarikh_keluar_tanggungan)
                                            <div class="small text-muted">
                                                Keluar: {{ \Carbon\Carbon::parse($dependent->tarikh_keluar_tanggungan)->format('d/m/Y') }}
                                            </div>
                                        @endif

                                    @elseif($dependent->status_tanggungan === 'meninggal')
                                        <span class="badge bg-dark">Meninggal Dunia</span>

                                    @else
                                        <span class="badge bg-secondary">Tidak Diketahui</span>
                                    @endif
                                </td>

                                <td>
                                    <div class="d-flex flex-wrap gap-1 action-buttons"
                                         @if($index === 0) id="tour-dependent-actions" @endif>

                                        <a href="{{ route('user.dependents.show', $dependent->id) }}"
                                           class="btn btn-info-light btn-sm">
                                            <i class="ri-eye-line me-1"></i> Lihat
                                        </a>

                                        <a href="{{ route('user.dependents.edit', $dependent->id) }}"
                                           class="btn btn-warning-light btn-sm">
                                            <i class="ri-pencil-line me-1"></i> Edit
                                        </a>

                                        <form action="{{ route('user.dependents.destroy', $dependent->id) }}"
                                              method="POST"
                                              class="d-inline"
                                              onsubmit="return confirm('Adakah anda pasti mahu padam data ini?')">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="btn btn-danger-light btn-sm">
                                                <i class="ri-delete-bin-line me-1"></i> Padam
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center">
                                        <div class="avatar avatar-xl bg-light text-muted mb-3">
                                            <i class="ri-team-line fs-2"></i>
                                        </div>

                                        <h6 class="mb-1">Tiada tanggungan direkodkan</h6>
                                        <p class="text-muted mb-3">
                                            Sila tambah tanggungan baharu untuk dipaparkan di sini.
                                        </p>

                                        <a href="{{ route('user.dependents.create') }}" class="btn btn-info btn-sm">
                                            <i class="ri-user-add-line me-1"></i> Tambah Tanggungan
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-block d-md-none mt-2">
                <small class="text-muted">
                    Nota: Sila leret jadual ke kiri atau kanan untuk melihat semua maklumat.
                </small>
            </div>
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
                    element: '#tour-dependent-header',
                    popover: {
                        title: 'Pengurusan Tanggungan',
                        description: 'Halaman ini digunakan untuk mengurus maklumat pasangan atau anak yang berdaftar di bawah keahlian khairat anda.',
                        side: 'bottom',
                        align: 'start'
                    }
                },
                {
                    element: '#tour-add-dependent',
                    popover: {
                        title: 'Tambah Tanggungan Baharu',
                        description: 'Klik di sini untuk mendaftarkan tanggungan baharu. Pastikan maklumat peribadi dan hubungan tanggungan diisi dengan betul.',
                        side: 'bottom',
                        align: 'end'
                    }
                },
                {
                    element: '#tour-dependent-summary',
                    popover: {
                        title: 'Ringkasan Tanggungan',
                        description: 'Bahagian ini menunjukkan jumlah tanggungan, bilangan yang masih aktif dan rekod tanggungan yang telah meninggal dunia.',
                        side: 'bottom',
                        align: 'center'
                    }
                },
                {
                    element: '#tour-dependent-records',
                    popover: {
                        title: 'Rekod Tanggungan Anda',
                        description: 'Semua tanggungan yang telah didaftarkan dipaparkan di sini bersama maklumat pengenalan, pertalian, nombor telefon dan status.',
                        side: 'top',
                        align: 'center'
                    }
                },
                {
                    element: '#tour-first-dependent-record',
                    popover: {
                        title: 'Status Tanggungan',
                        description: 'Semak status kehidupan dan kelayakan tanggungan di sini. Tanggungan yang tidak lagi layak atau telah meninggal dunia akan ditandakan melalui status rekod.',
                        side: 'top',
                        align: 'center'
                    }
                },
                {
                    element: '#tour-dependent-actions',
                    popover: {
                        title: 'Urus Rekod Tanggungan',
                        description: 'Gunakan butang Lihat untuk menyemak maklumat lengkap, Edit untuk mengemas kini rekod, atau Padam jika rekod perlu dibuang.',
                        side: 'left',
                        align: 'center'
                    }
                }
            ];

            const availableSteps = allSteps.filter(function (step) {
                return document.querySelector(step.element);
            });

            if (availableSteps.length === 0) {
                console.warn('Tiada elemen tour ditemui pada halaman ini.');
                return;
            }

            let dependentTour;

            dependentTour = driver({
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
                    const currentIndex = dependentTour.getActiveIndex() ?? 0;

                    if (typeof window.updateEpusaraTourPopover === 'function') {
                        window.updateEpusaraTourPopover(
                            dependentTour,
                            currentIndex,
                            availableSteps.length
                        );
                    }
                },

                steps: availableSteps
            });

            dependentTour.drive();
        });
    });
</script>
@endpush