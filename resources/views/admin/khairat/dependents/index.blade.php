@extends('layouts.app')

@section('content')
<style>
    .dependent-page .hero-card {
        border: 0;
        border-radius: 20px;
        background: linear-gradient(135deg, #198754, #36b37e);
        color: #fff;
        overflow: hidden;
        position: relative;
    }

    .dependent-page .hero-card::after {
        content: "";
        position: absolute;
        top: -35px;
        right: -35px;
        width: 170px;
        height: 170px;
        background: rgba(255,255,255,0.10);
        border-radius: 50%;
    }

    .dependent-page .stats-card,
    .dependent-page .filter-card,
    .dependent-page .table-card {
        border: 0;
        border-radius: 18px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.06);
    }

    .dependent-page .stats-card {
        transition: 0.25s ease;
    }

    .dependent-page .stats-card:hover {
        transform: translateY(-4px);
    }

    .dependent-page .stats-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
    }

    .dependent-page .summary-label {
        font-size: 13px;
        color: #6b7280;
        margin-bottom: 6px;
    }

    .dependent-page .summary-value {
        font-size: 24px;
        font-weight: 800;
        color: #111827;
        line-height: 1;
    }

    .dependent-page .search-box .form-control,
    .dependent-page .search-box .form-select {
        border-radius: 12px;
        min-height: 46px;
    }

    .dependent-page .btn {
        border-radius: 12px;
    }

    .dependent-page .table thead th {
        background: #f8fafc;
        border-bottom: 1px solid #e9ecef;
        font-weight: 700;
        color: #344054;
        white-space: nowrap;
    }

    .dependent-page .table tbody tr {
        transition: 0.2s ease;
    }

    .dependent-page .table tbody tr:hover {
        background-color: #f8fbff;
    }

    .dependent-page .dependent-avatar {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        color: #fff;
        background: linear-gradient(135deg, #198754, #53c68c);
        flex-shrink: 0;
    }

    .dependent-page .dependent-name {
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 2px;
    }

    .dependent-page .dependent-meta {
        font-size: 13px;
        color: #6b7280;
    }

    .dependent-page .custom-badge {
        display: inline-block;
        padding: 8px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
    }

    .dependent-page .empty-state {
        padding: 40px 20px;
        text-align: center;
        color: #6b7280;
    }
</style>

@php
    $anakCount = $dependents->getCollection()->where('pertalian', 'anak')->count();
    $pasanganCount = $dependents->getCollection()->where('pertalian', 'pasangan')->count();
    $lainCount = $dependents->getCollection()->whereNotIn('pertalian', ['anak', 'pasangan'])->count();

    $currentKeyword = request('search');
    $currentRelation = request('pertalian');

    function relationBadgeClass($pertalian) {
        return match(strtolower($pertalian ?? '')) {
            'anak' => 'bg-primary-subtle text-primary border',
            'pasangan' => 'bg-success-subtle text-success border',
            default => 'bg-secondary-subtle text-secondary border',
        };
    }

    function spouseBadgeClass($pasangan) {
        return match(strtolower($pasangan ?? '')) {
            'ya' => 'bg-success-subtle text-success border',
            'tidak' => 'bg-danger-subtle text-danger border',
            default => 'bg-light text-dark border',
        };
    }
@endphp

<div class="container-fluid dependent-page">

    {{-- Header --}}
    <div class="card hero-card shadow-sm mb-4">
        <div class="card-body p-4 p-lg-5">
            <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                <div>
                    <p class="mb-2 small text-white-50">Panel Pentadbir</p>
                    <h1 class="fw-bold mb-2">Senarai Tanggungan</h1>
                    <p class="mb-0 text-white-50">
                        Paparan semua tanggungan yang didaftarkan oleh ahli khairat dalam sistem.
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistik --}}
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card stats-card h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="summary-label">Jumlah Tanggungan</div>
                        <div class="summary-value">{{ $dependents->total() }}</div>
                    </div>
                    <div class="stats-icon bg-success-subtle text-success">
                        <i class="bx bx-group"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stats-card h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="summary-label">Anak</div>
                        <div class="summary-value">{{ $anakCount }}</div>
                    </div>
                    <div class="stats-icon bg-primary-subtle text-primary">
                        <i class="bx bx-child"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stats-card h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="summary-label">Pasangan</div>
                        <div class="summary-value">{{ $pasanganCount }}</div>
                    </div>
                    <div class="stats-icon bg-warning-subtle text-warning">
                        <i class="bx bx-heart"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stats-card h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="summary-label">Lain-lain</div>
                        <div class="summary-value">{{ $lainCount }}</div>
                    </div>
                    <div class="stats-icon bg-secondary-subtle text-secondary">
                        <i class="bx bx-user"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Carian --}}
    <div class="card filter-card mb-4">
        <div class="card-body p-4">
            <div class="mb-3">
                <h5 class="mb-1 fw-bold">Carian Tanggungan</h5>
                <p class="text-muted mb-0">
                    Cari berdasarkan nama tanggungan, nombor KP atau nama ahli utama.
                </p>
            </div>

            <form action="" method="GET" class="search-box">
                <div class="row g-3 align-items-end">
                    <div class="col-lg-6">
                        <label class="form-label fw-semibold">Kata Kunci</label>
                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            placeholder="Contoh: Ali / 010101011234 / nama ahli"
                            value="{{ $currentKeyword }}"
                        >
                    </div>

                    <div class="col-lg-3">
                        <label class="form-label fw-semibold">Pertalian</label>
                        <select name="pertalian" class="form-select">
                            <option value="">Semua Pertalian</option>
                            <option value="anak" {{ $currentRelation == 'anak' ? 'selected' : '' }}>Anak</option>
                            <option value="pasangan" {{ $currentRelation == 'pasangan' ? 'selected' : '' }}>Pasangan</option>
                            <option value="lain-lain" {{ $currentRelation == 'lain-lain' ? 'selected' : '' }}>Lain-lain</option>
                        </select>
                    </div>

                    <div class="col-lg-3">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success w-100">
                                <i class="bx bx-search me-1"></i> Cari
                            </button>
                            <a href="{{ url()->current() }}" class="btn btn-outline-secondary w-100">
                                Reset
                            </a>
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
                <h5 class="mb-1 fw-bold">Rekod Tanggungan</h5>
                <p class="text-muted mb-0">
                    Paparan semasa: <span class="fw-semibold">{{ $dependents->count() }}</span> tanggungan
                </p>
            </div>

            <div class="table-responsive px-3 pb-3">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th width="6%">#</th>
                            <th>Maklumat Tanggungan</th>
                            <th>No. KP</th>
                            <th>Pertalian</th>
                            <th>Pasangan</th>
                            <th>No. Telefon</th>
                            <th>Ahli Utama</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dependents as $index => $dependent)
                            @php
                                $initial = strtoupper(substr($dependent->name ?? 'A', 0, 1));
                            @endphp

                            <tr>
                                <td class="fw-semibold text-muted">
                                    {{ $dependents->firstItem() + $index }}
                                </td>

                                <td>
                                    <div class="d-flex align-items-start gap-3">
                                        <div class="dependent-avatar">{{ $initial }}</div>
                                        <div>
                                            <div class="dependent-name">{{ $dependent->name ?? '-' }}</div>
                                            <div class="dependent-meta">
                                                Tanggungan berdaftar
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td class="fw-semibold">{{ $dependent->no_kp ?? '-' }}</td>

                                <td>
                                    <span class="custom-badge {{ relationBadgeClass($dependent->pertalian) }}">
                                        {{ ucfirst($dependent->pertalian ?? '-') }}
                                    </span>
                                </td>

                                <td>
                                    <span class="custom-badge {{ spouseBadgeClass($dependent->pasangan) }}">
                                        {{ ucfirst($dependent->pasangan ?? '-') }}
                                    </span>
                                </td>

                                <td>{{ $dependent->no_tel ?? '-' }}</td>

                                <td>
                                    <div class="fw-semibold">{{ $dependent->user->name ?? '-' }}</div>
                                    <small class="text-muted">Ahli utama</small>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">
                                        <i class="bx bx-folder-open fs-1 mb-2 d-block"></i>
                                        Tiada tanggungan dijumpai.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($dependents->hasPages())
            <div class="card-footer bg-white border-0 pt-0 pb-4 px-4">
                {{ $dependents->withQueryString()->links() }}
            </div>
        @endif
    </div>

</div>
@endsection