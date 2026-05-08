@extends('layouts.app')

@section('content')
<style>
    .page-header-box {
        background: linear-gradient(135deg, #f8fbff 0%, #eef7ff 100%);
        border: 1px solid #e5eef9;
        border-radius: 18px;
        padding: 24px;
        margin-bottom: 24px;
    }

    .stat-card {
        border: 1px solid #e9eef5;
        border-radius: 16px;
        background: #fff;
        padding: 18px;
        box-shadow: 0 8px 22px rgba(15, 23, 42, 0.04);
        height: 100%;
    }

    .stat-icon {
        width: 46px;
        height: 46px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
    }

    .soft-card {
        background: #fff;
        border: 1px solid #e9eef5;
        border-radius: 18px;
        box-shadow: 0 8px 22px rgba(15, 23, 42, 0.04);
    }

    .table thead th {
        background: #f8fafc;
        color: #475569;
        font-size: 13px;
        font-weight: 700;
        border-bottom: 1px solid #e5e7eb;
        white-space: nowrap;
    }

    .table tbody td {
        vertical-align: middle;
        color: #334155;
    }

    .form-control,
    .form-select {
        border-radius: 12px;
        border-color: #dbe6f3;
        padding: 10px 14px;
    }

    .btn-rounded {
        border-radius: 12px;
    }

    .empty-box {
        padding: 60px 20px;
        text-align: center;
        color: #64748b;
    }

    .grave-badge {
        font-size: 12px;
        padding: 7px 10px;
        border-radius: 999px;
    }
</style>

<div class="container-fluid">

    <div class="page-header-box">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <div class="text-muted small mb-1">
                    Admin / Tempahan Kepuk / Nisan
                </div>

                <h3 class="fw-bold mb-1">
                    Senarai Tempahan Kepuk / Nisan
                </h3>

                <p class="text-muted mb-0">
                    Semak permohonan tempahan kepuk dan batu nisan yang dihantar oleh waris.
                </p>
            </div>

            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('admin.grave-orders.export.excel', [
                        'status' => request('status') ?: 'approved',
                        'search' => request('search')
                    ]) }}"
                class="btn btn-success text-white btn-rounded">
                    <i class="bi bi-file-earmark-excel me-1"></i>
                    Export Excel
                </a>

                <a href="{{ route('admin.grave-orders.export.pdf', [
                        'status' => request('status') ?: 'approved',
                        'search' => request('search')
                    ]) }}"
                target="_blank"
                class="btn btn-danger text-white btn-rounded">
                    <i class="bi bi-file-earmark-pdf me-1"></i>
                    Surat Arahan Kerja PDF
                </a>
            </div>
        </div>
    </div>

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

    <div class="alert alert-info border-0 shadow-sm rounded-3 mb-4">
        <div class="fw-bold mb-1">
            Eksport untuk pihak pembuat kepuk
        </div>
        <div class="small">
            Fail Excel akan memaparkan senarai tempahan berdasarkan carian dan status yang dipilih.
            Untuk pihak pembuat kepuk, disarankan eksport status <strong>Diluluskan</strong> sahaja.
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="stat-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon bg-warning-subtle text-warning">
                        <i class="bi bi-hourglass-split"></i>
                    </div>

                    <div>
                        <div class="text-muted small">Menunggu Kelulusan</div>
                        <h4 class="fw-bold mb-0">{{ $statusCounts['pending'] ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="stat-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon bg-success-subtle text-success">
                        <i class="bi bi-check-circle"></i>
                    </div>

                    <div>
                        <div class="text-muted small">Diluluskan</div>
                        <h4 class="fw-bold mb-0">{{ $statusCounts['approved'] ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="stat-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon bg-secondary-subtle text-secondary">
                        <i class="bi bi-x-circle"></i>
                    </div>

                    <div>
                        <div class="text-muted small">Dibatalkan</div>
                        <h4 class="fw-bold mb-0">{{ $statusCounts['cancelled'] ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="soft-card p-4 mb-4">
        <form method="GET" action="{{ route('admin.grave-orders.index') }}">
            <div class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label fw-semibold">
                        Carian
                    </label>
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           class="form-control"
                           placeholder="Cari nama si mati, waris, lot kubur atau jenis tempahan">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        Status
                    </label>
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

                <div class="col-md-3">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-info text-white btn-rounded flex-fill">
                            <i class="bi bi-search me-1"></i> Cari
                        </button>

                        <a href="{{ route('admin.grave-orders.index') }}" class="btn btn-light border btn-rounded">
                            Reset
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="soft-card">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead>
                    <tr>
                        <th style="width: 60px;">No</th>
                        <th>Nama Si Mati</th>
                        <th>Nama Waris</th>
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
                        @endphp

                        <tr>
                            <td>
                                {{ $orders->firstItem() + $index }}
                            </td>

                            <td>
                                <div class="fw-bold">
                                    {{ $deathReport->nama_si_mati ?? '-' }}
                                </div>
                                <div class="text-muted small">
                                    {{ ucfirst($order->category) }}
                                </div>
                            </td>

                            <td>
                                <div class="fw-semibold">
                                    {{ $deathReport->nama_pelapor ?? '-' }}
                                </div>
                                <div class="text-muted small">
                                    {{ $deathReport->no_tel_pelapor ?? '-' }}
                                </div>
                            </td>

                            <td>
                                <span class="badge bg-light text-dark border grave-badge">
                                    {{ $lotNo }}
                                </span>
                            </td>

                            <td>
                                <div class="fw-semibold">
                                    {{ $order->order_label }}
                                </div>
                                <div class="text-muted small">
                                    Kod: {{ $order->order_type }}
                                </div>
                            </td>

                            <td>
                                <div class="fw-bold text-info">
                                    RM{{ number_format($order->amount, 2) }}
                                </div>
                            </td>

                            <td>
                                <span class="badge bg-{{ $order->statusBadge() }} grave-badge">
                                    {{ $order->statusLabel() }}
                                </span>
                            </td>

                            <td>
                                <div class="fw-semibold">
                                    {{ optional($order->created_at)->format('d/m/Y') }}
                                </div>
                                <div class="text-muted small">
                                    {{ optional($order->created_at)->format('h:i A') }}
                                </div>
                            </td>

                            <td class="text-end">
                                <a href="{{ route('admin.grave-orders.show', $order) }}"
                                   class="btn btn-sm btn-info text-white btn-rounded">
                                    <i class="bi bi-eye me-1"></i> Semak
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9">
                                <div class="empty-box">
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

        @if($orders->hasPages())
            <div class="p-3 border-top">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>
@endsection