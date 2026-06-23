@extends('layouts.app')

@section('content')
<style>
    .burial-record-page .hero-card {
        border: 0;
        border-radius: 20px;
        background: linear-gradient(135deg, #a8e1d7, #c9efe8);
        color: #1f2937;
        overflow: hidden;
        position: relative;
    }

    .burial-record-page .hero-card::after {
        content: "";
        position: absolute;
        top: -35px;
        right: -35px;
        width: 170px;
        height: 170px;
        background: rgba(255,255,255,0.20);
        border-radius: 50%;
    }

    .burial-record-page .stats-card,
    .burial-record-page .filter-card,
    .burial-record-page .table-card {
        border: 0;
        border-radius: 18px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.06);
    }

    .burial-record-page .stats-card {
        transition: 0.25s ease;
    }

    .burial-record-page .stats-card:hover {
        transform: translateY(-4px);
    }

    .burial-record-page .stats-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        flex-shrink: 0;
    }

    .burial-record-page .summary-label {
        font-size: 13px;
        color: #6b7280;
        margin-bottom: 6px;
    }

    .burial-record-page .summary-value {
        font-size: 24px;
        font-weight: 800;
        color: #111827;
        line-height: 1;
    }

    .burial-record-page .hero-subtitle {
        color: #5f6f82;
    }

    .burial-record-page .search-box .form-control,
    .burial-record-page .search-box .form-select {
        border-radius: 12px;
        min-height: 46px;
        border-color: #dbe6f3;
    }

    .burial-record-page .btn {
        border-radius: 12px;
    }

    .burial-record-page .table thead th {
        background: #f8fafc;
        border-bottom: 1px solid #e9ecef;
        font-weight: 700;
        color: #344054;
        white-space: nowrap;
    }

    .burial-record-page .table tbody td {
        vertical-align: middle;
        color: #334155;
    }

    .burial-record-page .table tbody tr {
        transition: 0.2s ease;
    }

    .burial-record-page .table tbody tr:hover {
        background-color: #f8fbff;
    }

    .burial-record-page .record-avatar {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        color: #2f6f63;
        background: linear-gradient(135deg, #d8f4ee, #bfe9e0);
        flex-shrink: 0;
    }

    .burial-record-page .record-name {
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 2px;
    }

    .burial-record-page .record-meta {
        font-size: 13px;
        color: #6b7280;
    }

    .burial-record-page .custom-badge {
        display: inline-block;
        padding: 8px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
    }

    .burial-record-page .empty-state {
        padding: 45px 20px;
        text-align: center;
        color: #6b7280;
    }

    .burial-record-page .pagination {
        margin-bottom: 0;
    }

    .burial-record-page .pagination svg {
        width: 14px !important;
        height: 14px !important;
    }

    .burial-record-page .pagination .page-link {
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 38px;
        height: 38px;
        border-radius: 10px;
        padding: 0 12px;
    }

    .burial-record-page .pagination .page-item.active .page-link {
        color: #fff;
    }

    .burial-record-page nav svg,
    .burial-record-page .pagination svg {
        width: 14px !important;
        height: 14px !important;
        max-width: 14px !important;
        max-height: 14px !important;
    }

    .burial-record-page .card-footer {
        padding-top: 12px !important;
    }
</style>

@php
    $totalRecords = $totalBurialRecords
        ?? (method_exists($burialRecords, 'total') ? $burialRecords->total() : $burialRecords->count());

    $kepukStatusCounts = $kepukStatusCounts ?? [
        'belum_tempah' => 0,
        'pending' => 0,
        'approved' => 0,
        'cancelled' => 0,
    ];

    $deceasedTypeLabel = function ($type) {
        $type = strtolower($type ?? '');

        return match($type) {
            'dependent' => 'Tanggungan',
            'main_member' => 'Ahli Utama',
            'member' => 'Ahli Utama',
            'user' => 'Ahli Utama',
            'spouse' => 'Pasangan',
            'child' => 'Anak',
            'anak' => 'Anak',
            'isteri' => 'Isteri',
            'suami' => 'Suami',
            default => $type ? ucfirst($type) : '-',
        };
    };
@endphp

<div class="container-fluid burial-record-page">

    {{-- Header --}}
    <div class="card hero-card shadow-sm mb-4">
        <div class="card-body p-4 p-lg-5">
            <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                <div>
                    <p class="mb-2 small hero-subtitle">Panel Pentadbir / Rekod Kubur</p>
                    <h1 class="fw-bold mb-2">Rekod Kubur</h1>
                    <p class="mb-0 hero-subtitle">
                        Senarai rekod si mati yang telah ditetapkan lot kubur.
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Alert --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0 shadow-sm">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Statistik --}}
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card stats-card h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="summary-label">Jumlah Rekod Kubur</div>
                        <div class="summary-value">{{ $totalRecords }}</div>
                    </div>

                    <div class="stats-icon bg-success-subtle text-success">
                        <i class="bx bx-map"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stats-card h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="summary-label">Belum Tempah</div>
                        <div class="summary-value">{{ $kepukStatusCounts['belum_tempah'] ?? 0 }}</div>
                    </div>

                    <div class="stats-icon bg-secondary-subtle text-secondary">
                        <i class="bx bx-time-five"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-6">
            <div class="card stats-card h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="summary-label">Menunggu</div>
                        <div class="summary-value">{{ $kepukStatusCounts['pending'] ?? 0 }}</div>
                    </div>

                    <div class="stats-icon bg-warning-subtle text-warning">
                        <i class="bx bx-hourglass"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-6">
            <div class="card stats-card h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="summary-label">Diluluskan</div>
                        <div class="summary-value">{{ $kepukStatusCounts['approved'] ?? 0 }}</div>
                    </div>

                    <div class="stats-icon bg-primary-subtle text-primary">
                        <i class="bx bx-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-6">
            <div class="card stats-card h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="summary-label">Dibatalkan</div>
                        <div class="summary-value">{{ $kepukStatusCounts['cancelled'] ?? 0 }}</div>
                    </div>

                    <div class="stats-icon bg-danger-subtle text-danger">
                        <i class="bx bx-x-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Carian --}}
    <div class="card filter-card mb-4">
        <div class="card-body p-4">
            <div class="mb-3">
                <h5 class="mb-1 fw-bold">Carian Rekod Kubur</h5>
                <p class="text-muted mb-0">
                    Cari dan tapis rekod mengikut nama si mati, nombor KP, kod lot, zon kubur, status kepuk atau status gambar.
                </p>
            </div>

            <form method="GET" action="{{ route('admin.burial-records.index') }}" class="search-box">
                <div class="row g-3 align-items-end">

                    <div class="col-lg-4">
                        <label class="form-label fw-semibold">Kata Kunci</label>
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               class="form-control"
                               placeholder="Nama si mati / No. KP / Kod lot">
                    </div>

                    <div class="col-lg-2">
                        <label class="form-label fw-semibold">Zon Kubur</label>
                        <select name="zone" class="form-select">
                            <option value="">Semua Zon</option>
                            <option value="L" {{ request('zone') == 'L' ? 'selected' : '' }}>Zon Lelaki</option>
                            <option value="P" {{ request('zone') == 'P' ? 'selected' : '' }}>Zon Perempuan</option>
                            <option value="K" {{ request('zone') == 'K' ? 'selected' : '' }}>Zon Kanak-kanak</option>
                        </select>
                    </div>

                    <div class="col-lg-3">
                        <label class="form-label fw-semibold">Status Kepuk</label>
                        <select name="kepuk_status" class="form-select">
                            <option value="">Semua Status Kepuk</option>

                            <option value="belum_tempah" {{ request('kepuk_status') == 'belum_tempah' ? 'selected' : '' }}>
                                Belum Tempah
                            </option>

                            <option value="pending" {{ request('kepuk_status') == 'pending' ? 'selected' : '' }}>
                                Menunggu Kelulusan
                            </option>

                            <option value="approved" {{ request('kepuk_status') == 'approved' ? 'selected' : '' }}>
                                Diluluskan
                            </option>

                            <option value="cancelled" {{ request('kepuk_status') == 'cancelled' ? 'selected' : '' }}>
                                Dibatalkan
                            </option>
                        </select>
                    </div>

                    <div class="col-lg-3">
                        <label class="form-label fw-semibold">Status Gambar</label>
                        <select name="image_status" class="form-select">
                            <option value="">Semua Rekod</option>
                            <option value="ada" {{ request('image_status') == 'ada' ? 'selected' : '' }}>Ada Gambar</option>
                            <option value="tiada" {{ request('image_status') == 'tiada' ? 'selected' : '' }}>Tiada Gambar</option>
                        </select>
                    </div>

                    <div class="col-lg-12">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.burial-records.index') }}" class="btn btn-outline-info px-4">
                                Reset
                            </a>

                            <button type="submit" class="btn btn-info text-white btn-wave px-4">
                                <i class="bx bx-search me-1"></i> Cari Rekod
                            </button>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>

    {{-- Jadual --}}
    <div class="card table-card">
        <div class="card-body p-0">
            <div class="px-4 pt-4 pb-2">
                <h5 class="mb-1 fw-bold">Senarai Rekod Kubur</h5>
                <p class="text-muted mb-0">
                    Jumlah paparan:
                    <span class="fw-semibold">{{ $burialRecords->total() }}</span>
                    rekod
                </p>
            </div>

            <div class="table-responsive px-3 pb-3">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th width="6%">#</th>
                            <th>Maklumat Si Mati</th>
                            <th>Tarikh Meninggal</th>
                            <th>Tarikh Kebumi</th>
                            <th>Zon</th>
                            <th>Kod Lot</th>
                            <th>Status Kepuk</th>
                            <th>Gambar</th>
                            <th class="text-end">Tindakan</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($burialRecords as $record)
                            @php
                                $plot = $record->final_burial_plot;
                                $selectedOrder = $record->selected_grave_order;
                                $initial = strtoupper(substr($record->nama_si_mati ?? 'A', 0, 1));
                            @endphp

                            <tr>
                                <td class="fw-semibold text-muted">
                                    {{ $burialRecords->firstItem() + $loop->index }}
                                </td>

                                <td>
                                    <div class="d-flex align-items-start gap-3">
                                        <div class="record-avatar">{{ $initial }}</div>

                                        <div>
                                            <div class="record-name">
                                                {{ $record->nama_si_mati ?? '-' }}
                                            </div>

                                            <div class="record-meta">
                                                No. KP: {{ $record->no_kp_si_mati ?? '-' }}
                                            </div>

                                            <div class="record-meta">
                                                Jenis: {{ $deceasedTypeLabel($record->deceased_type) }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <div class="fw-semibold">
                                        {{ $record->tarikh_meninggal ? $record->tarikh_meninggal->format('d/m/Y') : '-' }}
                                    </div>
                                </td>

                                <td>
                                    <div class="fw-semibold">
                                        {{ $record->final_burial_date ? \Carbon\Carbon::parse($record->final_burial_date)->format('d/m/Y') : '-' }}
                                    </div>
                                </td>

                                <td>
                                    @if($plot)
                                        <span class="custom-badge bg-light text-dark border">
                                            {{ $plot->zone_label ?? '-' }}
                                        </span>
                                    @else
                                        <span class="custom-badge bg-secondary-subtle text-secondary border">
                                            -
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    @if($plot)
                                        <span class="custom-badge bg-primary-subtle text-primary border">
                                            {{ $plot->plot_code ?? '-' }}
                                        </span>
                                    @else
                                        <span class="custom-badge bg-secondary-subtle text-secondary border">
                                            Tiada Lot
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    <span class="custom-badge {{ $record->kepuk_status_badge ?? 'bg-light text-dark border' }}">
                                        {{ $record->kepuk_status_label ?? 'Belum Tempah' }}
                                    </span>
                                </td>

                                <td>
                                    @if($plot && $plot->grave_image)
                                        <span class="custom-badge bg-success-subtle text-success border">
                                            Ada Gambar
                                        </span>
                                    @else
                                        <span class="custom-badge bg-warning-subtle text-warning border">
                                            Tiada Gambar
                                        </span>
                                    @endif
                                </td>

                                <td class="text-end">
                                    <a href="{{ route('admin.burial-records.show', $record->id) }}"
                                       class="btn btn-sm btn-info text-white">
                                        <i class="bx bx-show me-1"></i> Lihat
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9">
                                    <div class="empty-state">
                                        <i class="bx bx-map fs-1 mb-2 d-block"></i>
                                        <h6 class="fw-bold mb-1">Tiada rekod kubur dijumpai</h6>
                                        <div>
                                            Rekod akan dipaparkan selepas laporan kematian disahkan dan lot kubur ditetapkan.
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($burialRecords->hasPages())
            <div class="card-footer bg-white border-0 pt-0 pb-4 px-4">
                {{ $burialRecords->links() }}
            </div>
        @endif
    </div>

</div>
@endsection