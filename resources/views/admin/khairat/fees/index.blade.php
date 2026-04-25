@extends('layouts.app')

@section('content')
<style>
    .fee-page .hero-card {
        border: 0;
        border-radius: 22px;
        background: linear-gradient(135deg, #d9ddff, #ecebff);
        color: #1f2937;
        overflow: hidden;
        position: relative;
    }

    .fee-page .hero-card::after {
        content: "";
        position: absolute;
        top: -35px;
        right: -35px;
        width: 170px;
        height: 170px;
        background: rgba(255,255,255,0.22);
        border-radius: 50%;
    }

    .fee-page .stats-card,
    .fee-page .filter-card,
    .fee-page .table-card {
        border: 0;
        border-radius: 18px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.06);
    }

    .fee-page .stats-card {
        transition: 0.25s ease;
    }

    .fee-page .stats-card:hover {
        transform: translateY(-4px);
    }

    .fee-page .stats-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
    }

    .fee-page .summary-label {
        font-size: 13px;
        color: #6b7280;
        margin-bottom: 6px;
    }

    .fee-page .summary-value {
        font-size: 24px;
        font-weight: 800;
        color: #111827;
        line-height: 1;
    }

    .fee-page .search-box .form-control,
    .fee-page .search-box .form-select {
        border-radius: 12px;
        min-height: 48px;
    }

    .fee-page .btn {
        border-radius: 12px;
    }

    .fee-page .table thead th {
        background: #f8fafc;
        border-bottom: 1px solid #e9ecef;
        font-weight: 700;
        color: #344054;
        white-space: nowrap;
        vertical-align: middle;
    }

    .fee-page .table tbody tr:hover {
        background-color: #faf7ff;
    }

    .fee-page .user-name {
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 2px;
    }

    .fee-page .user-avatar {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        color: #5b63a8;
        background: linear-gradient(135deg, #eef0ff, #d9ddff);
        flex-shrink: 0;
    }

    .fee-page .empty-state {
        padding: 40px 20px;
        text-align: center;
        color: #6b7280;
    }

    .fee-page .hero-subtitle {
        color: #6b7280;
    }

    .fee-page .plan-pill {
        padding: 8px 14px;
        border-radius: 999px;
        background: #eef2ff;
        color: #5b63a8;
        font-weight: 700;
        font-size: 12px;
        display: inline-block;
    }

    .fee-page .amount-text {
        font-weight: 700;
        color: #111827;
    }

    .fee-page .status-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 34px;
        height: 34px;
        border-radius: 50%;
        font-size: 17px;
        font-weight: 800;
    }

    .fee-page .status-paid {
        background: #dcfce7;
        color: #15803d;
    }

    .fee-page .status-pending {
        background: #fef3c7;
        color: #b45309;
    }

    .fee-page .status-failed {
        background: #fee2e2;
        color: #b91c1c;
    }

    .fee-page .fee-label {
        font-size: 13px;
        color: #6b7280;
        margin-bottom: 6px;
        font-weight: 600;
    }

    .fee-page .section-title {
        font-size: 15px;
        font-weight: 700;
        color: #111827;
    }

    .fee-page .show-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 8px 14px;
        min-width: unset;
        min-height: unset;
        font-size: 13px;
        font-weight: 600;
        border-radius: 10px;
        line-height: 1.2;
        white-space: nowrap;
    }

    .fee-page .table td,
    .fee-page .table th {
        padding-top: 16px;
        padding-bottom: 16px;
        vertical-align: middle;
    }
</style>

@php
    $currentKeyword = request('search');
    $currentStatus = request('status');

    $getPlanLabel = function ($plan) {
        return $plan === 'yearly' ? 'Tahunan' : 'Bulanan';
    };

    $getStatusIconClass = function ($status) {
        return match(strtolower($status ?? 'pending')) {
            'paid' => 'status-paid',
            'pending' => 'status-pending',
            'failed' => 'status-failed',
            default => 'status-pending',
        };
    };

    $getStatusIcon = function ($status) {
        return match(strtolower($status ?? 'pending')) {
            'paid' => '✓',
            'pending' => '•',
            'failed' => '✕',
            default => '•',
        };
    };
@endphp

<div class="container-fluid fee-page">

    <div class="card hero-card shadow-sm mb-4">
        <div class="card-body p-4 p-lg-5">
            <div>
                <p class="mb-2 small hero-subtitle">Panel Pentadbir</p>
                <h1 class="fw-bold mb-2">Senarai Yuran</h1>
                <p class="mb-0 hero-subtitle">
                    Paparan ringkas pembayaran ahli yang lebih mudah disemak oleh pentadbir.
                </p>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card stats-card h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="summary-label">Jumlah Ahli</div>
                        <div class="summary-value">{{ $totalCount }}</div>
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
                        <div class="summary-label">Lengkap</div>
                        <div class="summary-value">{{ $paidCount }}</div>
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
                        <div class="summary-label">Belum Lengkap</div>
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
                        <div class="summary-label">Jumlah Bayaran</div>
                        <div class="summary-value">RM {{ number_format($totalAmount, 2) }}</div>
                    </div>
                    <div class="stats-icon bg-info-subtle text-info">
                        <i class="bx bx-wallet"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card filter-card mb-4">
        <div class="card-body p-4">
            <div class="mb-3">
                <h5 class="mb-1 fw-bold">Carian Ahli</h5>
                <p class="text-muted mb-0">Cari berdasarkan nama ahli, pelan atau status pembayaran.</p>
            </div>

            <form action="" method="GET" class="search-box">
                <div class="row g-3 align-items-end">
                    <div class="col-lg-7">
                        <label class="form-label fw-semibold">Kata Kunci</label>
                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            placeholder="Contoh: Ali / bulanan / tahunan"
                            value="{{ $currentKeyword }}"
                        >
                    </div>

                    <div class="col-lg-3">
                        <label class="form-label fw-semibold">Status</label>
                        <select name="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="paid" {{ $currentStatus == 'paid' ? 'selected' : '' }}>Lengkap</option>
                            <option value="pending" {{ $currentStatus == 'pending' ? 'selected' : '' }}>Belum Lengkap</option>
                            <option value="failed" {{ $currentStatus == 'failed' ? 'selected' : '' }}>Gagal</option>
                        </select>
                    </div>

                    <div class="col-lg-2">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-info w-100">
                                <i class="bx bx-search me-1"></i> Cari
                            </button>
                            <a href="{{ route('admin.khairat.fees.index') }}" class="btn btn-outline-info w-100">
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
                <h5 class="mb-1 fw-bold">Rekod Bayaran Yuran</h5>
                <p class="text-muted mb-0">
                    Paparan semasa: <span class="fw-semibold">{{ $groupedFees->count() }}</span> ahli
                </p>
            </div>

            <div class="table-responsive px-3 pb-3">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th width="6%">#</th>
                            <th>Nama Ahli</th>
                            <th>Pelan</th>
                            <th>Pendaftaran</th>
                            <th>Yuran</th>
                            <th>Jumlah Dibayar</th>
                            <th>Tarikh Pembayaran</th>
                            <th width="12%">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($groupedFees as $index => $fee)
                            @php
                                $initial = strtoupper(substr($fee->user_name ?? 'U', 0, 1));
                            @endphp

                            <tr>
                                <td class="fw-semibold text-muted">{{ $index + 1 }}</td>

                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="user-avatar">{{ $initial }}</div>
                                        <div class="user-name">{{ $fee->user_name }}</div>
                                    </div>
                                </td>

                                <td>
                                    <span class="plan-pill">{{ $getPlanLabel($fee->plan) }}</span>
                                </td>

                                <td class="text-center">
                                    <span class="status-icon {{ $getStatusIconClass($fee->registration_status) }}">
                                        {{ $getStatusIcon($fee->registration_status) }}
                                    </span>
                                </td>

                                <td>
                                    <div class="fee-label">{{ $fee->fee_label }}</div>
                                    <span class="status-icon {{ $getStatusIconClass($fee->fee_status) }}">
                                        {{ $getStatusIcon($fee->fee_status) }}
                                    </span>
                                </td>

                                <td class="amount-text">
                                    RM {{ number_format($fee->total_paid_amount ?? 0, 2) }}
                                </td>

                                <td>
                                    {{ $fee->latest_paid_at ? \Carbon\Carbon::parse($fee->latest_paid_at)->format('d/m/Y') : '-' }}
                                </td>

                                <td>
                                    <a href="{{ route('admin.khairat.fees.show', $fee->user_id) }}" class="btn btn-info btn-sm show-btn">
                                        <i class="bx bx-show me-1"></i> Show
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">
                                    <div class="empty-state">
                                        <i class="bx bx-folder-open fs-1 mb-2 d-block"></i>
                                        Tiada rekod yuran dijumpai.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection