@extends('layouts.app')

@section('content')
<style>
    .death-page .hero-card {
        border: 0;
        border-radius: 20px;
        background: linear-gradient(135deg, #6f42c1, #8b5cf6);
        color: #fff;
        overflow: hidden;
        position: relative;
    }

    .death-page .hero-card::after {
        content: "";
        position: absolute;
        top: -35px;
        right: -35px;
        width: 170px;
        height: 170px;
        background: rgba(255,255,255,0.10);
        border-radius: 50%;
    }

    .death-page .stats-card,
    .death-page .filter-card,
    .death-page .table-card {
        border: 0;
        border-radius: 18px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.06);
    }

    .death-page .stats-card {
        transition: 0.25s ease;
    }

    .death-page .stats-card:hover {
        transform: translateY(-4px);
    }

    .death-page .stats-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
    }

    .death-page .summary-label {
        font-size: 13px;
        color: #6b7280;
        margin-bottom: 6px;
    }

    .death-page .summary-value {
        font-size: 24px;
        font-weight: 800;
        color: #111827;
        line-height: 1;
    }

    .death-page .search-box .form-control,
    .death-page .search-box .form-select {
        border-radius: 12px;
        min-height: 46px;
    }

    .death-page .btn {
        border-radius: 12px;
    }

    .death-page .table thead th {
        background: #f8fafc;
        border-bottom: 1px solid #e9ecef;
        font-weight: 700;
        color: #344054;
        white-space: nowrap;
    }

    .death-page .table tbody tr {
        transition: 0.2s ease;
    }

    .death-page .table tbody tr:hover {
        background-color: #faf8ff;
    }

    .death-page .person-avatar {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        color: #fff;
        background: linear-gradient(135deg, #6f42c1, #9b6dff);
        flex-shrink: 0;
    }

    .death-page .person-name {
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 2px;
    }

    .death-page .person-meta {
        font-size: 13px;
        color: #6b7280;
    }

    .death-page .status-badge {
        display: inline-block;
        padding: 8px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
    }

    .death-page .action-btn {
        width: 38px;
        height: 38px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
    }

    .death-page .empty-state {
        padding: 40px 20px;
        text-align: center;
        color: #6b7280;
    }
</style>

@php
    $statusMap = [
        'menunggu_semakan' => ['label' => 'Menunggu Semakan', 'class' => 'warning text-dark'],
        'disahkan' => ['label' => 'Disahkan', 'class' => 'success'],
        'perlukan_dokumen_tambahan' => ['label' => 'Perlu Dokumen', 'class' => 'info'],
        'ditolak' => ['label' => 'Ditolak', 'class' => 'danger'],
    ];

    $currentSearch = request('search');
    $currentStatus = request('status');
@endphp

<div class="container-fluid death-page">

    <div class="card hero-card shadow-sm mb-4">
        <div class="card-body p-4 p-lg-5">
            <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                <div>
                    <p class="mb-2 small text-white-50">Panel Pentadbir</p>
                    <h1 class="fw-bold mb-2">Senarai Laporan Kematian</h1>
                    <p class="mb-0 text-white-50">
                        Paparan semua laporan kematian untuk semakan dan tindakan pentadbir.
                    </p>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card stats-card h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="summary-label">Jumlah Laporan</div>
                        <div class="summary-value">{{ $summary['total'] ?? 0 }}</div>
                    </div>
                    <div class="stats-icon bg-primary-subtle text-primary">
                        <i class="bx bx-file"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stats-card h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="summary-label">Menunggu Semakan</div>
                        <div class="summary-value">{{ $summary['pending'] ?? 0 }}</div>
                    </div>
                    <div class="stats-icon bg-warning-subtle text-warning">
                        <i class="bx bx-time-five"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stats-card h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="summary-label">Disahkan</div>
                        <div class="summary-value">{{ $summary['approved'] ?? 0 }}</div>
                    </div>
                    <div class="stats-icon bg-success-subtle text-success">
                        <i class="bx bx-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stats-card h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="summary-label">Perlu Dokumen</div>
                        <div class="summary-value">{{ $summary['need_docs'] ?? 0 }}</div>
                    </div>
                    <div class="stats-icon bg-info-subtle text-info">
                        <i class="bx bx-file-find"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card filter-card mb-4">
        <div class="card-body p-4">
            <div class="mb-3">
                <h5 class="mb-1 fw-bold">Carian Laporan</h5>
                <p class="text-muted mb-0">
                    Cari berdasarkan nama si mati, nombor KP, nama pelapor atau nombor telefon.
                </p>
            </div>

            <form method="GET" action="{{ route('admin.death-reports.index') }}" class="search-box">
                <div class="row g-3 align-items-end">
                    <div class="col-lg-8">
                        <label class="form-label fw-semibold">Kata Kunci</label>
                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            value="{{ $currentSearch }}"
                            placeholder="Contoh: Ali / 990101011234 / nama pelapor / 01xxxxxxxx"
                        >
                    </div>

                    <div class="col-lg-2">
                        <label class="form-label fw-semibold">Status</label>
                        <select name="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="menunggu_semakan" {{ $currentStatus == 'menunggu_semakan' ? 'selected' : '' }}>Menunggu Semakan</option>
                            <option value="disahkan" {{ $currentStatus == 'disahkan' ? 'selected' : '' }}>Disahkan</option>
                            <option value="perlukan_dokumen_tambahan" {{ $currentStatus == 'perlukan_dokumen_tambahan' ? 'selected' : '' }}>Perlu Dokumen</option>
                            <option value="ditolak" {{ $currentStatus == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                    </div>

                    <div class="col-lg-2">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bx bx-search me-1"></i> Cari
                            </button>
                            <a href="{{ route('admin.death-reports.index') }}" class="btn btn-outline-secondary w-100">
                                Reset
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card table-card">
        <div class="card-body p-0">
            <div class="px-4 pt-4 pb-2">
                <h5 class="mb-1 fw-bold">Rekod Laporan Kematian</h5>
                <p class="text-muted mb-0">
                    Jumlah paparan: <span class="fw-semibold">{{ $deathReports->total() }}</span> laporan
                </p>
            </div>

            <div class="table-responsive px-3 pb-3">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th width="6%">#</th>
                            <th>Si Mati</th>
                            <th>Pelapor</th>
                            <th>Status</th>
                            <th>Tarikh Laporan</th>
                            <th>No Lot Kubur</th>
                            <th>Tarikh Kebumi</th>
                            <th class="text-center" width="10%">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($deathReports as $index => $report)
                            @php
                                $status = $statusMap[$report->status] ?? [
                                    'label' => ucfirst(str_replace('_', ' ', $report->status ?? 'Belum Disemak')),
                                    'class' => 'secondary'
                                ];

                                $initial = strtoupper(substr($report->nama_si_mati ?? 'A', 0, 1));
                            @endphp

                            <tr>
                                <td class="fw-semibold text-muted">
                                    {{ $deathReports->firstItem() + $index }}
                                </td>

                                <td>
                                    <div class="d-flex align-items-start gap-3">
                                        <div class="person-avatar">{{ $initial }}</div>
                                        <div>
                                            <div class="person-name">{{ $report->nama_si_mati ?? '-' }}</div>
                                            <div class="person-meta">No. KP: {{ $report->no_kp_si_mati ?? '-' }}</div>
                                            <div class="person-meta">
                                                Jenis: {{ $report->deceased_type === 'member' ? 'Ahli Utama' : 'Tanggungan' }}
                                            </div>
                                            <div class="person-meta">
                                                Tarikh meninggal:
                                                {{ optional($report->tarikh_meninggal)->format('d/m/Y') ?: '-' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <div class="fw-semibold">{{ $report->nama_pelapor ?? '-' }}</div>
                                    <div class="person-meta">{{ $report->no_tel_pelapor ?? '-' }}</div>
                                    <div class="person-meta">{{ $report->pertalian_pelapor ?? '-' }}</div>
                                </td>

                                <td>
                                    <span class="badge bg-{{ $status['class'] }} status-badge">
                                        {{ $status['label'] }}
                                    </span>
                                </td>

                                <td>
                                    <div class="fw-semibold">
                                        {{ optional($report->created_at)->format('d/m/Y') ?: '-' }}
                                    </div>
                                    <div class="person-meta">
                                        {{ optional($report->created_at)->format('h:i A') ?: '-' }}
                                    </div>
                                </td>

                                <td>{{ $report->burial_lot_no ?: '-' }}</td>
                                <td>{{ $report->burial_date ? \Carbon\Carbon::parse($report->burial_date)->format('d/m/Y') : '-' }}</td>

                                <td class="text-center">
                                    <a href="{{ route('admin.death-reports.show', $report) }}"
                                       class="btn btn-sm btn-outline-primary action-btn"
                                       title="Lihat">
                                        <i class="bx bx-show"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="empty-state">
                                        <i class="bx bx-folder-open fs-1 mb-2 d-block"></i>
                                        Tiada laporan kematian dijumpai.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($deathReports->hasPages())
            <div class="card-footer bg-white border-0 pt-0 pb-4 px-4">
                {{ $deathReports->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection