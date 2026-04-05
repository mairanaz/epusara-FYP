@extends('layouts.app')

@section('content')
<div class="container-fluid">

    @php
        $statusMap = [
            'menunggu_semakan' => ['label' => 'Menunggu Semakan', 'class' => 'warning'],
            'disahkan' => ['label' => 'Disahkan', 'class' => 'success'],
            'perlukan_dokumen_tambahan' => ['label' => 'Perlukan Dokumen Tambahan', 'class' => 'info'],
            'ditolak' => ['label' => 'Ditolak', 'class' => 'danger'],
        ];

        $categoryMap = [
            'ahli_khairat' => ['label' => 'Ahli Khairat', 'class' => 'success'],
            'tanggungan' => ['label' => 'Tanggungan', 'class' => 'primary'],
            'bukan_ahli' => ['label' => 'Bukan Ahli', 'class' => 'secondary'],
            'warga_asing' => ['label' => 'Warga Asing', 'class' => 'dark'],
        ];
    @endphp

    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <h1 class="page-title fw-semibold fs-20 mb-1">Senarai Laporan Kematian</h1>
            <div class="text-muted">
                Pengurusan dan semakan semua laporan kematian oleh pentadbir
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- Summary Cards --}}
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card custom-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted fs-12 mb-1">Jumlah Laporan</div>
                    <h3 class="fw-semibold mb-0">{{ $summary['total'] }}</h3>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card custom-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted fs-12 mb-1">Menunggu Semakan</div>
                    <h3 class="fw-semibold mb-0 text-warning">{{ $summary['pending'] }}</h3>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card custom-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted fs-12 mb-1">Disahkan</div>
                    <h3 class="fw-semibold mb-0 text-success">{{ $summary['approved'] }}</h3>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card custom-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted fs-12 mb-1">Perlukan Dokumen Tambahan</div>
                    <h3 class="fw-semibold mb-0 text-info">{{ $summary['need_docs'] }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Card --}}
    <div class="card custom-card border-0 shadow-sm mb-4">
        <div class="card-header">
            <h5 class="mb-0">Carian & Penapisan</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.death-reports.index') }}">
                <div class="row g-3">
                    <div class="col-xl-5">
                        <label class="form-label">Carian</label>
                        <input type="text"
                               name="search"
                               class="form-control"
                               value="{{ request('search') }}"
                               placeholder="Cari nama si mati, no KP, nama pelapor atau no telefon">
                    </div>

                    <div class="col-xl-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control">
                            <option value="">-- Semua Status --</option>
                            <option value="menunggu_semakan" {{ request('status') == 'menunggu_semakan' ? 'selected' : '' }}>
                                Menunggu Semakan
                            </option>
                            <option value="disahkan" {{ request('status') == 'disahkan' ? 'selected' : '' }}>
                                Disahkan
                            </option>
                            <option value="perlukan_dokumen_tambahan" {{ request('status') == 'perlukan_dokumen_tambahan' ? 'selected' : '' }}>
                                Perlukan Dokumen Tambahan
                            </option>
                            <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>
                                Ditolak
                            </option>
                        </select>
                    </div>

                    <div class="col-xl-2">
                        <label class="form-label">Kategori Rekod</label>
                        <select name="verification_category" class="form-control">
                            <option value="">-- Semua Kategori --</option>
                            <option value="ahli_khairat" {{ request('verification_category') == 'ahli_khairat' ? 'selected' : '' }}>
                                Ahli Khairat
                            </option>
                            <option value="tanggungan" {{ request('verification_category') == 'tanggungan' ? 'selected' : '' }}>
                                Tanggungan
                            </option>
                            <option value="bukan_ahli" {{ request('verification_category') == 'bukan_ahli' ? 'selected' : '' }}>
                                Bukan Ahli
                            </option>
                            <option value="warga_asing" {{ request('verification_category') == 'warga_asing' ? 'selected' : '' }}>
                                Warga Asing
                            </option>
                        </select>
                    </div>

                    <div class="col-xl-2 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary w-100">
                            Cari
                        </button>
                        <a href="{{ route('admin.death-reports.index') }}" class="btn btn-light w-100 border">
                            Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="card custom-card border-0 shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="mb-0">Rekod Laporan Kematian</h5>
                <small class="text-muted">Klik butang lihat untuk semakan penuh dan tindakan pentadbir</small>
            </div>

            <div class="text-muted fs-13">
                Jumlah dipaparkan: <strong>{{ $deathReports->total() }}</strong>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="min-width: 220px;">Si Mati</th>
                            <th style="min-width: 190px;">Pelapor</th>
                            <th style="min-width: 140px;">Jenis Laporan</th>
                            <th style="min-width: 170px;">Kategori Rekod</th>
                            <th style="min-width: 170px;">Status</th>
                            <th style="min-width: 170px;">Tarikh Lapor</th>
                            <th class="text-center" style="min-width: 120px;">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($deathReports as $report)
                            @php
                                $status = $statusMap[$report->status] ?? [
                                    'label' => ucfirst(str_replace('_', ' ', $report->status ?? 'Belum Disemak')),
                                    'class' => 'warning'
                                ];

                                $category = $categoryMap[$report->verification_category] ?? null;
                            @endphp

                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $report->nama_si_mati }}</div>
                                    <div class="text-muted fs-12">No KP: {{ $report->no_kp_si_mati }}</div>
                                    <div class="text-muted fs-12">
                                        Tarikh Meninggal:
                                        {{ optional($report->tarikh_meninggal)->format('d/m/Y') ?: '-' }}
                                    </div>
                                </td>

                                <td>
                                    <div class="fw-semibold">{{ $report->nama_pelapor }}</div>
                                    <div class="text-muted fs-12">{{ $report->no_tel_pelapor ?: '-' }}</div>
                                    <div class="text-muted fs-12">{{ $report->pertalian_pelapor ?: '-' }}</div>
                                </td>

                                <td>
                                    @if($report->deceased_type === 'member')
                                        <span class="badge bg-success-transparent text-success px-3 py-2">
                                            Ahli Utama
                                        </span>
                                    @elseif($report->deceased_type === 'dependent')
                                        <span class="badge bg-primary-transparent text-primary px-3 py-2">
                                            Tanggungan
                                        </span>
                                    @else
                                        <span class="badge bg-light text-dark px-3 py-2">
                                            -
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    @if($category)
                                        <span class="badge bg-{{ $category['class'] }} px-3 py-2">
                                            {{ $category['label'] }}
                                        </span>
                                    @else
                                        <span class="badge bg-light text-dark px-3 py-2">
                                            Belum Ditentukan
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    <span class="badge bg-{{ $status['class'] }} px-3 py-2">
                                        {{ $status['label'] }}
                                    </span>
                                </td>

                                <td>
                                    <div class="fw-semibold">
                                        {{ optional($report->created_at)->format('d/m/Y') }}
                                    </div>
                                    <div class="text-muted fs-12">
                                        {{ optional($report->created_at)->format('h:i A') }}
                                    </div>
                                </td>

                                <td class="text-center">
                                    <a href="{{ route('admin.death-reports.show', $report) }}"
                                       class="btn btn-sm btn-primary">
                                        Lihat
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="text-muted">Tiada laporan kematian dijumpai.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $deathReports->links() }}
            </div>
        </div>
    </div>
</div>
@endsection