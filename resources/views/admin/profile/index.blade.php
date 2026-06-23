@extends('layouts.app')

@section('content')
<style>
    .application-page .hero-card {
        border: 0;
        border-radius: 20px;
        background: linear-gradient(135deg, #ffd7b8, #ffe7d1);
        color: #1f2937;
        overflow: hidden;
        position: relative;
    }

    .application-page .hero-card::after {
        content: "";
        position: absolute;
        top: -35px;
        right: -35px;
        width: 170px;
        height: 170px;
        background: rgba(255,255,255,0.22);
        border-radius: 50%;
    }

    .application-page .stats-card,
    .application-page .filter-card,
    .application-page .table-card {
        border: 0;
        border-radius: 18px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.06);
    }

    .application-page .stats-card {
        transition: 0.25s ease;
    }

    .application-page .stats-card:hover {
        transform: translateY(-4px);
    }

    .application-page .stats-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
    }

    .application-page .summary-label {
        font-size: 13px;
        color: #6b7280;
        margin-bottom: 6px;
    }

    .application-page .summary-value {
        font-size: 24px;
        font-weight: 800;
        color: #111827;
        line-height: 1;
    }

    .application-page .table thead th {
        background: #f8fafc;
        border-bottom: 1px solid #e9ecef;
        font-weight: 700;
        color: #344054;
        white-space: nowrap;
    }

    .application-page .table tbody tr {
        transition: 0.2s ease;
    }

    .application-page .table tbody tr:hover {
        background-color: #fffaf3;
    }

    .application-page .person-avatar {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        color: #9a5b2e;
        background: linear-gradient(135deg, #ffe6d2, #ffd5b5);
        flex-shrink: 0;
    }

    .application-page .person-name {
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 2px;
    }

    .application-page .person-meta {
        font-size: 13px;
        color: #6b7280;
    }

    .application-page .status-badge {
        padding: 8px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
    }

    .application-page .action-btn {
        width: 38px;
        height: 38px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
    }

    .application-page .search-box .form-control,
    .application-page .search-box .form-select {
        border-radius: 12px;
        min-height: 46px;
    }

    .application-page .btn {
        border-radius: 12px;
    }

    .application-page .empty-state {
        padding: 40px 20px;
        text-align: center;
        color: #6b7280;
    }

    .application-page .hero-subtitle {
        color: #7a6b5f;
    }

    .application-page .applicant-type-text {
        margin-top: 4px;
        margin-bottom: 6px;
        font-size: 13px;
        font-weight: 700;
    }

    .application-page .applicant-type-text.main-text {
        color: #4f46e5;
    }

    .application-page .applicant-type-text.dependent-text {
        color: #d97706;
    }

    .application-page .applicant-type-text.upgrade-text {
        color: #059669;
    }

    .application-page .pagination {
    margin-bottom: 0;
}

.application-page .pagination svg {
    width: 14px !important;
    height: 14px !important;
}

.application-page .pagination .page-link {
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 38px;
    height: 38px;
    border-radius: 10px;
    padding: 0 12px;
}

.application-page .pagination .page-item.active .page-link {
    color: #fff;
}

.application-page nav svg,
.application-page .pagination svg {
    width: 14px !important;
    height: 14px !important;
    max-width: 14px !important;
    max-height: 14px !important;
}

.application-page .card-footer {
    padding-top: 12px !important;
}
</style>

@php
    $getStatusClass = function ($status) {
        return match($status) {
            'pending'  => 'warning text-dark',
            'approved' => 'success',
            'active'   => 'success',
            'rejected' => 'danger',
            default    => 'secondary',
        };
    };

    $getStatusLabel = function ($status) {
        return match($status) {
            'pending'  => 'Menunggu',
            'approved' => 'Diluluskan',
            'active'   => 'Diluluskan',
            'rejected' => 'Ditolak',
            default    => 'Belum Dihantar',
        };
    };
@endphp

<div class="container-fluid application-page">

    <div class="card hero-card shadow-sm mb-4">
        <div class="card-body p-4 p-lg-5">
            <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                <div>
                    <p class="mb-2 small hero-subtitle">Panel Pentadbir</p>
                    <h1 class="fw-bold mb-2">Senarai Permohonan Keahlian</h1>
                    <p class="mb-0 hero-subtitle">
                        Semak, tapis dan urus permohonan keahlian pengguna dengan lebih teratur.
                    </p>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-4 alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm rounded-4 alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card stats-card h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="summary-label">Jumlah Permohonan</div>
                        <div class="summary-value">{{ $totalApplications ?? $profiles->total() }}</div>
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
                        <div class="summary-label">Menunggu</div>
                        <div class="summary-value">{{ $pendingCount ?? 0 }}</div>
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
                        <div class="summary-label">Diluluskan</div>
                        <div class="summary-value">{{ $approvedCount ?? 0 }}</div>
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
                        <div class="summary-label">Ditolak</div>
                        <div class="summary-value">{{ $rejectedCount ?? 0 }}</div>
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
                <h5 class="mb-1 fw-bold">Carian Permohonan</h5>
                <p class="text-muted mb-0">Cari berdasarkan nama, MyKad atau nombor telefon.</p>
            </div>

            <form method="GET" action="{{ route('admin.profile.index') }}" class="search-box">
                <div class="row g-3 align-items-end">
                    <div class="col-lg-7">
                        <label class="form-label fw-semibold">Kata Kunci</label>
                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            placeholder="Contoh: Ali / 990101011234 / 01xxxxxxxx"
                            value="{{ request('search') }}"
                        >
                    </div>

                    <div class="col-lg-3">
                        <label class="form-label fw-semibold">Status</label>
                        <select name="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>
                                Menunggu
                            </option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>
                                Diluluskan
                            </option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>
                                Ditolak
                            </option>
                        </select>
                    </div>

                    <div class="col-lg-2">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-info btn-wave w-100">
                                <i class="bx bx-search me-1"></i> Cari
                            </button>
                            <a href="{{ route('admin.profile.index') }}" class="btn btn-outline-info w-100">
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
                <h5 class="mb-1 fw-bold">Rekod Permohonan</h5>
                <p class="text-muted mb-0">
                    Paparan semasa: <span class="fw-semibold">{{ $profiles->count() }}</span> permohonan
                </p>
            </div>

            <div class="table-responsive px-3 pb-3">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th width="6%">#</th>
                            <th>Pemohon</th>
                            <th>Tarikh Permohonan</th>
                            <th>Pelan</th>
                            <th>Status</th>
                            <th class="text-center" width="10%">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($profiles as $index => $profile)
                            @php
                                $statusClass = $getStatusClass($profile->status_permohonan);
                                $statusLabel = $getStatusLabel($profile->status_permohonan);
                                $initial = strtoupper(substr($profile->nama ?? 'A', 0, 1));

                                /*
                                |--------------------------------------------------------------------------
                                | Semak rekod lama dalam table dependents
                                |--------------------------------------------------------------------------
                                | Jika no_kp pernah wujud sebagai tanggungan dan status_tanggungan
                                | sudah tidak_layak, maka permohonan ini ialah naik taraf ahli utama.
                                |--------------------------------------------------------------------------
                                */
                                $dependentRecord = \App\Models\Dependent::where('no_kp', $profile->no_kp)
                                    ->latest()
                                    ->first();

                                $isUpgradedFromDependent = $dependentRecord
                                    && ($dependentRecord->status_tanggungan ?? null) === 'tidak_layak'
                                    && in_array($profile->status_permohonan, ['approved', 'active']);

                                $isStillActiveDependent = $dependentRecord
                                    && ($dependentRecord->status_tanggungan ?? 'aktif') === 'aktif';
                            @endphp

                            <tr>
                                <td class="fw-semibold text-muted">
                                    {{ $profiles->firstItem() + $index }}
                                </td>

                                <td>
                                    <div class="d-flex align-items-start gap-3">
                                        <div class="person-avatar">{{ $initial }}</div>

                                        <div>
                                            <div class="person-name">
                                                {{ $profile->nama }}
                                            </div>

                                            @if($isUpgradedFromDependent)
                                                <div class="applicant-type-text upgrade-text">
                                                    Naik Taraf Ahli Utama
                                                </div>
                                            @elseif($isStillActiveDependent)
                                                <div class="applicant-type-text dependent-text">
                                                    Tanggungan = {{ ucwords($dependentRecord->pertalian ?? '-') }}
                                                </div>
                                            @else
                                                <div class="applicant-type-text main-text">
                                                    Ahli Utama
                                                </div>
                                            @endif

                                            <div class="person-meta">
                                                No. MyKad: {{ $profile->no_kp }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <div class="fw-semibold">
                                        {{ $profile->tarikh_permohonan ? \Carbon\Carbon::parse($profile->tarikh_permohonan)->format('d/m/Y') : '-' }}
                                    </div>
                                    <div class="person-meta">Tarikh permohonan</div>
                                </td>

                                <td>
                                    @if($profile->payment_plan)
                                        <span class="badge bg-info-subtle text-info border status-badge">
                                            {{ ucfirst($profile->payment_plan) }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>

                                <td>
                                    <span class="badge bg-{{ $statusClass }} status-badge">
                                        {{ $statusLabel }}
                                    </span>
                                </td>

                                <td class="text-center">
                                    <a href="{{ route('admin.profile.show', $profile) }}"
                                       class="btn btn-sm btn-outline-info action-btn"
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
                                        Tiada permohonan keahlian dijumpai.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($profiles->hasPages())
            <div class="card-footer bg-white border-0 pt-0 pb-4 px-4">
                {{ $profiles->withQueryString()->links() }}
            </div>
        @endif
    </div>

</div>
@endsection