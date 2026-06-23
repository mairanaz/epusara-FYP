@extends('layouts.app')

@section('content')
<style>
    .grave-order-page .hero-card {
        border: 0;
        border-radius: 20px;
        background: linear-gradient(135deg, #a8e1d7, #c9efe8);
        color: #1f2937;
        overflow: hidden;
        position: relative;
    }

    .grave-order-page .hero-card::after {
        content: "";
        position: absolute;
        top: -35px;
        right: -35px;
        width: 170px;
        height: 170px;
        background: rgba(255,255,255,0.20);
        border-radius: 50%;
    }

    .grave-order-page .stats-card,
    .grave-order-page .filter-card,
    .grave-order-page .table-card,
    .grave-order-page .info-card {
        border: 0;
        border-radius: 18px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.06);
    }

    .grave-order-page .stats-card {
        transition: 0.25s ease;
    }

    .grave-order-page .stats-card:hover {
        transform: translateY(-4px);
    }

    .grave-order-page .stats-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        flex-shrink: 0;
    }

    .grave-order-page .summary-label {
        font-size: 13px;
        color: #6b7280;
        margin-bottom: 6px;
    }

    .grave-order-page .summary-value {
        font-size: 24px;
        font-weight: 800;
        color: #111827;
        line-height: 1;
    }

    .grave-order-page .hero-subtitle {
        color: #5f6f82;
    }

    .grave-order-page .search-box .form-control,
    .grave-order-page .search-box .form-select {
        border-radius: 12px;
        min-height: 46px;
        border-color: #dbe6f3;
    }

    .grave-order-page .btn {
        border-radius: 12px;
    }

    .grave-order-page .table thead th {
        background: #f8fafc;
        border-bottom: 1px solid #e9ecef;
        font-weight: 700;
        color: #344054;
        white-space: nowrap;
    }

    .grave-order-page .table tbody td {
        vertical-align: middle;
        color: #334155;
    }

    .grave-order-page .table tbody tr {
        transition: 0.2s ease;
    }

    .grave-order-page .table tbody tr:hover {
        background-color: #f8fbff;
    }

    .grave-order-page .order-avatar {
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

    .grave-order-page .order-name {
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 2px;
    }

    .grave-order-page .order-meta {
        font-size: 13px;
        color: #6b7280;
    }

    .grave-order-page .custom-badge {
        display: inline-block;
        padding: 8px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
    }

    .grave-order-page .empty-state {
        padding: 40px 20px;
        text-align: center;
        color: #6b7280;
    }

    .grave-order-page .pagination {
        margin-bottom: 0;
    }

    .grave-order-page .pagination svg {
        width: 14px !important;
        height: 14px !important;
    }

    .grave-order-page .pagination .page-link {
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 38px;
        height: 38px;
        border-radius: 10px;
        padding: 0 12px;
    }

    .grave-order-page .pagination .page-item.active .page-link {
        color: #fff;
    }

    .grave-order-page nav svg,
    .grave-order-page .pagination svg {
        width: 14px !important;
        height: 14px !important;
        max-width: 14px !important;
        max-height: 14px !important;
    }

    .grave-order-page .card-footer {
        padding-top: 12px !important;
    }
</style>

@php
    $statusBadgeClass = function ($status) {
        return match(strtolower($status ?? '')) {
            'pending' => 'bg-warning-subtle text-warning border',
            'approved' => 'bg-success-subtle text-success border',
            'cancelled' => 'bg-secondary-subtle text-secondary border',
            'rejected' => 'bg-danger-subtle text-danger border',
            default => 'bg-light text-dark border',
        };
    };
@endphp

<div class="container-fluid grave-order-page">

    {{-- Header --}}
    <div class="card hero-card shadow-sm mb-4">
        <div class="card-body p-4 p-lg-5">
            <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                <div>
                    <p class="mb-2 small hero-subtitle">Panel Pentadbir / Tempahan Kepuk / Nisan</p>
                    <h1 class="fw-bold mb-2">Senarai Tempahan Kepuk / Nisan</h1>
                    <p class="mb-0 hero-subtitle">
                        Semak permohonan tempahan kepuk dan batu nisan yang dihantar oleh waris.
                    </p>
                </div>

                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('admin.grave-orders.export.excel', [
                            'status' => request('status') ?: 'approved',
                            'search' => request('search')
                        ]) }}"
                       class="btn btn-success text-white">
                        <i class="bi bi-file-earmark-excel me-1"></i>
                        Export Excel
                    </a>

                    <a href="{{ route('admin.grave-orders.export.pdf', [
                            'status' => request('status') ?: 'approved',
                            'search' => request('search')
                        ]) }}"
                       target="_blank"
                       class="btn btn-danger text-white">
                        <i class="bi bi-file-earmark-pdf me-1"></i>
                        Surat Arahan Kerja PDF
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Alert --}}
    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-3">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm rounded-3">
            {{ session('error') }}
        </div>
    @endif

    {{-- Info Export --}}
    <div class="card info-card mb-4">
        <div class="card-body p-4">
            <div class="d-flex align-items-start gap-3">
                <div class="stats-icon bg-info-subtle text-info">
                    <i class="bi bi-info-circle"></i>
                </div>

                <div>
                    <h5 class="mb-1 fw-bold">Eksport untuk pihak pembuat kepuk</h5>
                    <p class="text-muted mb-0">
                        Fail Excel akan memaparkan senarai tempahan berdasarkan carian dan status yang dipilih.
                        Untuk pihak pembuat kepuk, disarankan eksport status <strong>Diluluskan</strong> sahaja.
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistik --}}
    <div class="row g-3 mb-4">
        <div class="col-xl-4 col-md-6">
            <div class="card stats-card h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="summary-label">Menunggu Kelulusan</div>
                        <div class="summary-value">{{ $statusCounts['pending'] ?? 0 }}</div>
                    </div>

                    <div class="stats-icon bg-warning-subtle text-warning">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6">
            <div class="card stats-card h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="summary-label">Diluluskan</div>
                        <div class="summary-value">{{ $statusCounts['approved'] ?? 0 }}</div>
                    </div>

                    <div class="stats-icon bg-success-subtle text-success">
                        <i class="bi bi-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6">
            <div class="card stats-card h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="summary-label">Dibatalkan</div>
                        <div class="summary-value">{{ $statusCounts['cancelled'] ?? 0 }}</div>
                    </div>

                    <div class="stats-icon bg-secondary-subtle text-secondary">
                        <i class="bi bi-x-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Carian --}}
    <div class="card filter-card mb-4">
        <div class="card-body p-4">
            <div class="mb-3">
                <h5 class="mb-1 fw-bold">Carian Tempahan</h5>
                <p class="text-muted mb-0">
                    Cari berdasarkan nama si mati, nama waris, lot kubur atau jenis tempahan.
                </p>
            </div>

            <form method="GET" action="{{ route('admin.grave-orders.index') }}" class="search-box">
                <div class="row g-3 align-items-end">
                    <div class="col-lg-5">
                        <label class="form-label fw-semibold">Kata Kunci</label>
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               class="form-control"
                               placeholder="Contoh: nama si mati / waris / lot kubur">
                    </div>

                    <div class="col-lg-4">
                        <label class="form-label fw-semibold">Status</label>
                        <select name="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>
                                Menunggu Kelulusan
                            </option>
                            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>
                                Diluluskan
                            </option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>
                                Dibatalkan
                            </option>
                        </select>
                    </div>

                    <div class="col-lg-3">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-info text-white btn-wave w-100">
                                <i class="bi bi-search me-1"></i> Cari
                            </button>

                            <a href="{{ route('admin.grave-orders.index') }}" class="btn btn-outline-info w-100">
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
                <h5 class="mb-1 fw-bold">Rekod Tempahan</h5>
                <p class="text-muted mb-0">
                    Paparan semasa:
                    <span class="fw-semibold">{{ $orders->count() }}</span>
                    tempahan
                </p>
            </div>

            <div class="table-responsive px-3 pb-3">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th width="6%">#</th>
                            <th>Maklumat Si Mati</th>
                            <th>Maklumat Waris</th>
                            <th>Lot Kubur</th>
                            <th>Jenis Tempahan</th>
                            <th>Jumlah</th>
                            <th>Status</th>
                            <th>Tarikh Mohon</th>
                            <th class="text-end">Tindakan</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($orders as $index => $order)
                            @php
                                $deathReport = $order->deathReport;
                                $plot = $order->burialPlot;

                                $lotNo = $plot->plot_code
                                    ?? $deathReport?->burial_plot_code
                                    ?? $deathReport?->burial_lot_no
                                    ?? '-';

                                $initial = strtoupper(substr($deathReport?->nama_si_mati ?? 'A', 0, 1));
                            @endphp

                            <tr>
                                <td class="fw-semibold text-muted">
                                    {{ $orders->firstItem() + $index }}
                                </td>

                                <td>
                                    <div class="d-flex align-items-start gap-3">
                                        <div class="order-avatar">{{ $initial }}</div>

                                        <div>
                                            <div class="order-name">
                                                {{ $deathReport?->nama_si_mati ?? '-' }}
                                            </div>

                                            <div class="order-meta">
                                                {{ ucfirst($order->category ?? '-') }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <div class="fw-semibold">
                                        {{ $deathReport?->nama_pelapor ?? '-' }}
                                    </div>

                                    <div class="order-meta">
                                        {{ $deathReport?->no_tel_pelapor ?? '-' }}
                                    </div>
                                </td>

                                <td>
                                    <span class="custom-badge bg-light text-dark border">
                                        {{ $lotNo }}
                                    </span>
                                </td>

                                <td>
                                    <div class="fw-semibold">
                                        {{ $order->order_label }}
                                    </div>

                                    <div class="order-meta">
                                        Kod: {{ $order->order_type }}
                                    </div>
                                </td>

                                <td>
                                    <div class="fw-bold text-info">
                                        RM{{ number_format($order->amount, 2) }}
                                    </div>
                                </td>

                                <td>
                                    <span class="custom-badge {{ $statusBadgeClass($order->status ?? null) }}">
                                        {{ $order->statusLabel() }}
                                    </span>
                                </td>

                                <td>
                                    <div class="fw-semibold">
                                        {{ optional($order->created_at)->format('d/m/Y') }}
                                    </div>

                                    <div class="order-meta">
                                        {{ optional($order->created_at)->format('h:i A') }}
                                    </div>
                                </td>

                                <td class="text-end">
                                    <a href="{{ route('admin.grave-orders.show', $order) }}"
                                       class="btn btn-sm btn-info text-white">
                                        <i class="bi bi-eye me-1"></i> Semak
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9">
                                    <div class="empty-state">
                                        <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                                        <h6 class="fw-bold mb-1">Tiada tempahan ditemui</h6>
                                        <div>Belum ada permohonan tempahan kepuk / nisan untuk dipaparkan.</div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($orders->hasPages())
            <div class="card-footer bg-white border-0 pt-0 pb-4 px-4">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

</div>
@endsection