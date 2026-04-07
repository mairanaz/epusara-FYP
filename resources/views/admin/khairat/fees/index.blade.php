@extends('layouts.app')

@section('content')
<style>
    .fee-page .hero-card {
        border: 0;
        border-radius: 20px;
        background: linear-gradient(135deg, #7c3aed, #a855f7);
        color: #fff;
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
        background: rgba(255,255,255,0.10);
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
        min-height: 46px;
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
    }

    .fee-page .table tbody tr {
        transition: 0.2s ease;
    }

    .fee-page .table tbody tr:hover {
        background-color: #faf7ff;
    }

    .fee-page .user-name {
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 2px;
    }

    .fee-page .user-meta {
        font-size: 13px;
        color: #6b7280;
    }

    .fee-page .user-avatar {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        color: #fff;
        background: linear-gradient(135deg, #7c3aed, #a855f7);
        flex-shrink: 0;
    }

    .fee-page .status-badge {
        padding: 8px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
    }

    .fee-page .empty-state {
        padding: 40px 20px;
        text-align: center;
        color: #6b7280;
    }
</style>

@php
    $paidCount = $fees->getCollection()->filter(function ($fee) {
        return in_array(strtolower($fee->status ?? ''), ['paid', 'success', 'approved']);
    })->count();

    $pendingCount = $fees->getCollection()->filter(function ($fee) {
        return strtolower($fee->status ?? 'pending') === 'pending';
    })->count();

    $failedCount = $fees->getCollection()->filter(function ($fee) {
        return in_array(strtolower($fee->status ?? ''), ['rejected', 'failed']);
    })->count();

    $totalAmount = $fees->getCollection()->sum(function ($fee) {
        return (float) ($fee->amount ?? 0);
    });

    $currentKeyword = request('search');
    $currentStatus = request('status');

    $getStatusClass = function ($status) {
        $status = strtolower($status ?? 'pending');

        return match($status) {
            'paid', 'success', 'approved' => 'success',
            'pending' => 'warning text-dark',
            'rejected', 'failed' => 'danger',
            default => 'secondary',
        };
    };

    $getStatusLabel = function ($status) {
        $status = strtolower($status ?? 'pending');

        return match($status) {
            'paid' => 'Paid',
            'success' => 'Success',
            'approved' => 'Approved',
            'pending' => 'Pending',
            'rejected' => 'Rejected',
            'failed' => 'Failed',
            default => ucfirst($status),
        };
    };
@endphp

<div class="container-fluid fee-page">

    <div class="card hero-card shadow-sm mb-4">
        <div class="card-body p-4 p-lg-5">
            <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                <div>
                    <p class="mb-2 small text-white-50">Panel Pentadbir</p>
                    <h1 class="fw-bold mb-2">Senarai Yuran</h1>
                    <p class="mb-0 text-white-50">
                        Paparan semua rekod bayaran yuran ahli dalam sistem eKhairat.
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
                        <div class="summary-label">Jumlah Rekod</div>
                        <div class="summary-value">{{ $fees->total() }}</div>
                    </div>
                    <div class="stats-icon bg-primary-subtle text-primary">
                        <i class="bx bx-receipt"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stats-card h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="summary-label">Bayaran Berjaya</div>
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
                        <div class="summary-label">Pending</div>
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
                        <div class="summary-value">RM {{ number_format($totalAmount, 0) }}</div>
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
                <h5 class="mb-1 fw-bold">Carian Rekod Yuran</h5>
                <p class="text-muted mb-0">Cari berdasarkan nama pengguna, pelan atau jenis bayaran.</p>
            </div>

            <form action="" method="GET" class="search-box">
                <div class="row g-3 align-items-end">
                    <div class="col-lg-7">
                        <label class="form-label fw-semibold">Kata Kunci</label>
                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            placeholder="Contoh: Ali / bulanan / pendaftaran"
                            value="{{ $currentKeyword }}"
                        >
                    </div>

                    <div class="col-lg-3">
                        <label class="form-label fw-semibold">Status</label>
                        <select name="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="paid" {{ $currentStatus == 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="pending" {{ $currentStatus == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="rejected" {{ $currentStatus == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            <option value="failed" {{ $currentStatus == 'failed' ? 'selected' : '' }}>Failed</option>
                        </select>
                    </div>

                    <div class="col-lg-2">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary w-100">
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

    <div class="card table-card">
        <div class="card-body p-0">
            <div class="px-4 pt-4 pb-2">
                <h5 class="mb-1 fw-bold">Rekod Bayaran Yuran</h5>
                <p class="text-muted mb-0">
                    Paparan semasa: <span class="fw-semibold">{{ $fees->count() }}</span> rekod
                </p>
            </div>

            <div class="table-responsive px-3 pb-3">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th width="6%">#</th>
                            <th>Nama User</th>
                            <th>Pelan</th>
                            <th>Jenis Bayaran</th>
                            <th>Jumlah</th>
                            <th>Status</th>
                            <th>Tarikh Bayaran</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($fees as $index => $fee)
                            @php
                                $statusClass = $getStatusClass($fee->status);
                                $statusLabel = $getStatusLabel($fee->status);
                                $initial = strtoupper(substr($fee->user->name ?? 'U', 0, 1));
                            @endphp

                            <tr>
                                <td class="fw-semibold text-muted">
                                    {{ $fees->firstItem() + $index }}
                                </td>

                                <td>
                                    <div class="d-flex align-items-start gap-3">
                                        <div class="user-avatar">{{ $initial }}</div>
                                        <div>
                                            <div class="user-name">{{ $fee->user->name ?? '-' }}</div>
                                            <div class="user-meta">Rekod bayaran ahli</div>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    @if($fee->payment_plan)
                                        <span class="badge bg-primary-subtle text-primary border status-badge">
                                            {{ ucfirst($fee->payment_plan) }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>

                                <td class="fw-semibold">
                                    {{ ucfirst(str_replace('_', ' ', $fee->payment_type ?? '-')) }}
                                </td>

                                <td class="fw-semibold">
                                    RM {{ number_format($fee->amount ?? 0, 2) }}
                                </td>

                                <td>
                                    <span class="badge bg-{{ $statusClass }} status-badge">
                                        {{ $statusLabel }}
                                    </span>
                                </td>

                                <td>
                                    {{ $fee->paid_at ? \Carbon\Carbon::parse($fee->paid_at)->format('d/m/Y') : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
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

        @if($fees->hasPages())
            <div class="card-footer bg-white border-0 pt-0 pb-4 px-4">
                {{ $fees->withQueryString()->links() }}
            </div>
        @endif
    </div>

</div>
@endsection