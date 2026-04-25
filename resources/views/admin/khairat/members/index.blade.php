@extends('layouts.app')

@section('content')
<style>
    .member-page .hero-card {
        border: 0;
        border-radius: 20px;
        background: linear-gradient(135deg, #8ec5ff, #b8dcff);
        color: #1f2937;
        overflow: hidden;
        position: relative;
    }

    .member-page .hero-card::after {
        content: "";
        position: absolute;
        top: -30px;
        right: -30px;
        width: 160px;
        height: 160px;
        background: rgba(255,255,255,0.22);
        border-radius: 50%;
    }

    .member-page .stats-card,
    .member-page .filter-card,
    .member-page .table-card {
        border: 0;
        border-radius: 18px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.06);
    }

    .member-page .stats-card {
        transition: 0.25s ease;
    }

    .member-page .stats-card:hover {
        transform: translateY(-4px);
    }

    .member-page .stats-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
    }

    .member-page .search-box .form-control,
    .member-page .search-box .form-select {
        border-radius: 12px;
        min-height: 46px;
    }

    .member-page .btn {
        border-radius: 12px;
    }

    .member-page .table thead th {
        background: #f8fafc;
        border-bottom: 1px solid #e9ecef;
        font-weight: 700;
        color: #344054;
        white-space: nowrap;
    }

    .member-page .table tbody tr {
        transition: 0.2s ease;
    }

    .member-page .table tbody tr:hover {
        background-color: #f8fbff;
    }

    .member-page .member-name {
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 2px;
    }

    .member-page .member-meta {
        font-size: 13px;
        color: #6b7280;
    }

    .member-page .status-badge {
        padding: 8px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
    }

    .member-page .member-avatar {
         width: 42px;
        height: 42px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        color: #2b4c7e;
        background: linear-gradient(135deg, #d9ecff, #bfe0ff);
        flex-shrink: 0;
    }

    .member-page .summary-label {
        font-size: 13px;
        color: #6b7280;
        margin-bottom: 6px;
    }

    .member-page .summary-value {
        font-size: 24px;
        font-weight: 800;
        color: #111827;
        line-height: 1;
    }

    .member-page .empty-state {
        padding: 40px 20px;
        text-align: center;
        color: #6b7280;
    }

    .member-page .action-btn {
        width: 38px;
        height: 38px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
    }

    .member-page .hero-subtitle {
        color: #5b6b7f;
    }
</style>

@php
    $approvedCount = $members->getCollection()->where('status_permohonan', 'approved')->count();
    $pendingCount  = $members->getCollection()->where('status_permohonan', 'pending')->count();
    $rejectedCount = $members->getCollection()->where('status_permohonan', 'rejected')->count();
    $activeCount   = $members->getCollection()->where('status_permohonan', 'active')->count();

    $currentKeyword = request('search');
    $currentStatus  = request('status');

    $getStatusClass = function ($status) {
        return match($status) {
            'pending'  => 'warning text-dark',
            'approved' => 'success',
            'rejected' => 'danger',
            'active'   => 'primary',
            default    => 'secondary',
        };
    };

    $getStatusLabel = function ($status) {
        return match($status) {
            'pending'  => 'Menunggu',
            'approved' => 'Diluluskan',
            'rejected' => 'Ditolak',
            'active'   => 'Aktif',
            default    => 'Tiada Status',
        };
    };
@endphp

<div class="container-fluid member-page">

    <div class="card hero-card shadow-sm mb-4">
        <div class="card-body p-4 p-lg-5">
            <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                <div>
                    <p class="mb-2 small hero-subtitle">Panel Pentadbir</p>
                    <h1 class="fw-bold mb-2">Senarai Ahli Khairat</h1>
                    <p class="mb-0 hero-subtitle">
                        Paparan semua ahli yang telah berdaftar dalam sistem eKhairat.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card stats-card h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="summary-label">Jumlah Ahli</div>
                        <div class="summary-value">{{ $members->total() }}</div>
                    </div>
                    <div class="stats-icon bg-primary-subtle text-primary">
                        <i class="bx bx-group"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stats-card h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="summary-label">Diluluskan / Aktif</div>
                        <div class="summary-value">{{ $approvedCount + $activeCount }}</div>
                    </div>
                    <div class="stats-icon bg-success-subtle text-success">
                        <i class="bx bx-check-shield"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stats-card h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="summary-label">Menunggu</div>
                        <div class="summary-value">{{ $pendingCount }}</div>
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
                        <div class="summary-label">Ditolak</div>
                        <div class="summary-value">{{ $rejectedCount }}</div>
                    </div>
                    <div class="stats-icon bg-danger-subtle text-danger">
                        <i class="bx bx-x-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card filter-card mb-4">
        <div class="card-body p-4">
            <div class="mb-3">
                <h5 class="mb-1 fw-bold">Carian Ahli</h5>
                <p class="text-muted mb-0">Cari ahli dengan lebih cepat berdasarkan nama, MyKad atau nombor telefon.</p>
            </div>

            <form action="" method="GET" class="search-box">
                <div class="row g-3 align-items-end">
                    <div class="col-lg-6">
                        <label class="form-label fw-semibold">Kata Kunci</label>
                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            placeholder="Contoh: Ali / 0101234567 / 990101011234"
                            value="{{ $currentKeyword }}"
                        >
                    </div>

                    <div class="col-lg-3">
                        <label class="form-label fw-semibold">Status</label>
                        <select name="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="pending" {{ $currentStatus == 'pending' ? 'selected' : '' }}>Menunggu</option>
                            <option value="approved" {{ $currentStatus == 'approved' ? 'selected' : '' }}>Diluluskan</option>
                            <option value="active" {{ $currentStatus == 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="rejected" {{ $currentStatus == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                    </div>

                    <div class="col-lg-3">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-info btn-wave w-100">
                                <i class="bx bx-search me-1"></i> Cari
                            </button>
                            <a href="{{ url()->current() }}" class="btn btn-outline-info w-100">
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
                <h5 class="mb-1 fw-bold">Rekod Ahli</h5>
                <p class="text-muted mb-0">
                    Paparan semasa: <span class="fw-semibold">{{ $members->count() }}</span> ahli
                </p>
            </div>

            <div class="table-responsive px-3 pb-3">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th width="6%">#</th>
                            <th>Maklumat Ahli</th>
                            <th>No. MyKad</th>
                            <th>No. Telefon</th>
                            <th>Pelan</th>
                            <th>Status</th>
                            <th class="text-center" width="10%">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($members as $index => $member)
                            @php
                                $statusClass = $getStatusClass($member->status_permohonan);
                                $statusLabel = $getStatusLabel($member->status_permohonan);
                                $initial = strtoupper(substr($member->nama ?? 'A', 0, 1));
                            @endphp

                            <tr>
                                <td class="fw-semibold text-muted">
                                    {{ $members->firstItem() + $index }}
                                </td>

                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="member-avatar">{{ $initial }}</div>
                                        <div class="member-name">{{ $member->nama }}</div>    
                                    </div>
                                </td>

                                <td class="fw-semibold">{{ $member->no_kp }}</td>
                                <td>{{ $member->no_tel_bimbit ?? '-' }}</td>

                                <td>
                                    @if($member->payment_plan)
                                        <span class="badge bg-info-subtle text-info border">
                                            {{ ucfirst($member->payment_plan) }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>

                                <td>
                                    @if($member->status_permohonan)
                                        <span class="badge bg-{{ $statusClass }} status-badge">
                                            {{ $statusLabel }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary status-badge">Tiada Status</span>
                                    @endif
                                </td>

                                <td class="text-center">
                                    <a href="{{ route('admin.khairat.members.show', $member) }}"
                                       class="btn btn-sm btn-outline-info action-btn"
                                       title="Lihat">
                                        <i class="bx bx-show"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">
                                        <i class="bx bx-folder-open fs-1 mb-2 d-block"></i>
                                        Tiada ahli dijumpai.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($members->hasPages())
            <div class="card-footer bg-white border-0 pt-0 pb-4 px-4">
                {{ $members->withQueryString()->links() }}
            </div>
        @endif
    </div>

</div>
@endsection