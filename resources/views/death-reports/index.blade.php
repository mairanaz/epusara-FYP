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

        function statusBadgeClass($status) {
            return match($status) {
                'menunggu_semakan' => 'warning',
                'disahkan' => 'success',
                'ditolak' => 'danger',
                'perlukan_dokumen_tambahan' => 'info',
                default => 'secondary',
            };
        }

        function statusLabel($status) {
            return match($status) {
                'menunggu_semakan' => 'Menunggu Semakan',
                'disahkan' => 'Disahkan',
                'ditolak' => 'Ditolak',
                'perlukan_dokumen_tambahan' => 'Perlukan Dokumen Tambahan',
                default => ucfirst(str_replace('_', ' ', $status)),
            };
        }

        function categoryLabel($category) {
            return match($category) {
                'member' => 'Ahli Utama',
                'dependent' => 'Tanggungan',
                default => '-',
            };
        }

        function referenceNo($report) {
            $year = optional($report->created_at)->format('Y') ?? now()->format('Y');
            return 'LRK-' . $year . '-' . str_pad($report->id, 4, '0', STR_PAD_LEFT);
        }
    @endphp

    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <h1 class="page-title fw-semibold fs-20 mb-1">Status Laporan Kematian</h1>
            <p class="text-muted mb-0">
                Semak rekod laporan kematian yang telah dihantar dan status semakan oleh pentadbir.
            </p>
        </div>

        <div class="mt-3 mt-md-0">
            <a href="{{ route('death-reports.create') }}" class="btn btn-info">
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

    <div class="row g-3 mb-4">
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

    <div class="card custom-card border-0 shadow-sm mb-4">
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

    <div class="card custom-card border-0 shadow-sm">
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
                                <tr>
                                    <td class="ps-4 fw-semibold">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ referenceNo($report) }}</div>
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
                                            {{ categoryLabel($report->deceased_type) }}
                                        </span>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($report->tarikh_meninggal)->format('d/m/Y') }}</td>
                                    <td>{{ optional($report->created_at)->format('d/m/Y') ?? '-' }}</td>
                                    <td>
                                        <span class="badge bg-{{ statusBadgeClass($report->status) }}">
                                            {{ statusLabel($report->status) }}
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