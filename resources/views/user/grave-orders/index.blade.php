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

    .soft-card {
        background: #fff;
        border: 1px solid #e9eef5;
        border-radius: 18px;
        box-shadow: 0 8px 22px rgba(15, 23, 42, 0.04);
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
        background: #eaf7ff;
        color: #0d6efd;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
    }

    .table thead th {
        background: #f8fafc;
        color: #475569;
        font-size: 13px;
        font-weight: 800;
        border-bottom: 1px solid #e5e7eb;
        white-space: nowrap;
        text-transform: uppercase;
    }

    .table tbody td {
        vertical-align: middle;
        color: #334155;
    }

    .grave-badge {
        font-size: 12px;
        padding: 7px 10px;
        border-radius: 999px;
    }

    .btn-rounded {
        border-radius: 12px;
    }

    .empty-box {
        padding: 60px 20px;
        text-align: center;
        color: #64748b;
    }

    .cancel-guide {
        background: #fff8e6;
        border: 1px solid #ffe2a8;
        color: #8a5a00;
        border-radius: 16px;
        padding: 16px 18px;
    }

    .cancel-guide-icon {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        background: #fff0c2;
        color: #b36b00;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 20px;
    }

    .action-wrap {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
        flex-wrap: wrap;
    }

    @media (max-width: 768px) {
        .page-header-box {
            padding: 18px;
        }

        .action-wrap {
            justify-content: flex-start;
        }
    }
</style>

@php
    $orderCollection = $orders instanceof \Illuminate\Pagination\AbstractPaginator
        ? $orders->getCollection()
        : collect($orders);

    $totalOrders = $orders instanceof \Illuminate\Pagination\AbstractPaginator
        ? $orders->total()
        : $orderCollection->count();

    $activeDeathReportIds = $orderCollection
        ->whereIn('status', ['pending', 'approved'])
        ->pluck('death_report_id')
        ->filter()
        ->unique()
        ->values()
        ->toArray();

    $hasCancelledOrders = $orderCollection
        ->where('status', 'cancelled')
        ->filter(function ($order) use ($activeDeathReportIds) {
            return !in_array($order->death_report_id, $activeDeathReportIds);
        })
        ->count() > 0;
@endphp

<div class="container-fluid">

    <div class="page-header-box">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <div class="text-muted small mb-1">
                    Dashboard / Tempahan Kepuk / Nisan
                </div>

                <h3 class="fw-bold mb-1">
                    Tempahan Kepuk / Nisan
                </h3>

                <p class="text-muted mb-0">
                    Semak rekod permohonan tempahan kepungan dan batu nisan yang telah dihantar.
                </p>
            </div>

            <div>
                <a href="{{ route('grave-orders.create') }}" class="btn btn-info text-white btn-rounded px-4">
                    <i class="bi bi-plus-circle me-1"></i> Permohonan Baru
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

    @if(session('info'))
        <div class="alert alert-info border-0 shadow-sm rounded-3">
            {{ session('info') }}
        </div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="stat-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon">
                        <i class="bi bi-clipboard-check"></i>
                    </div>

                    <div>
                        <div class="text-muted small">Jumlah Permohonan</div>
                        <h4 class="fw-bold mb-0">{{ $totalOrders }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($hasCancelledOrders)
        <div class="cancel-guide d-flex align-items-start gap-3 mb-4">
            <div class="cancel-guide-icon">
                <i class="bi bi-info-circle"></i>
            </div>

            <div>
                <div class="fw-bold mb-1">
                    Terdapat tempahan yang telah dibatalkan
                </div>

                <div class="small">
                    Tempahan yang dibatalkan tidak lagi dikira sebagai tempahan aktif.
                    Anda boleh membuat permohonan tempahan baharu untuk si mati yang sama jika masih diperlukan.
                </div>
            </div>
        </div>
    @endif

    <div class="soft-card">
        <div class="p-4 border-bottom">
            <h5 class="fw-bold mb-0">
                Senarai Tempahan
            </h5>
        </div>

        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead>
                    <tr>
                        <th style="width: 60px;">Bil</th>
                        <th>Nama Si Mati</th>
                        <th>No. Lot Kubur</th>
                        <th>Jenis Tempahan</th>
                        <th>Jumlah Bayaran</th>
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

                            $bil = $orders instanceof \Illuminate\Pagination\AbstractPaginator
                                ? $orders->firstItem() + $index
                                : $index + 1;

                            $hasActiveReplacement = $order->status === 'cancelled'
                                && in_array($order->death_report_id, $activeDeathReportIds);
                        @endphp

                        <tr>
                            <td>{{ $bil }}</td>

                            <td>
                                <div class="fw-bold">
                                    {{ $deathReport->nama_si_mati ?? '-' }}
                                </div>

                                <div class="text-muted small text-uppercase">
                                    {{ $order->category === 'kanak-kanak' ? 'Kanak-kanak' : 'Dewasa' }}
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

                                @if($order->status === 'cancelled' && $order->admin_note)
                                    <div class="small text-muted mt-1">
                                        <i class="bi bi-chat-left-text me-1"></i>
                                        {{ \Illuminate\Support\Str::limit($order->admin_note, 70) }}
                                    </div>
                                @endif
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

                                @if($order->status === 'cancelled')
                                    <div class="small text-muted mt-1">
                                        {{ $hasActiveReplacement ? 'Tempahan baharu telah dibuat.' : 'Boleh buat tempahan baharu.' }}
                                    </div>
                                @endif
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
                                <div class="action-wrap">
                                    <a href="{{ route('grave-orders.show', $order) }}"
                                       class="btn btn-sm btn-info text-white btn-rounded">
                                        <i class="bi bi-eye me-1"></i> Lihat
                                    </a>

                                    @if($order->status === 'cancelled')
                                        @if($hasActiveReplacement)
                                            <span class="badge bg-light text-muted border grave-badge">
                                                Tempahan baharu telah dibuat
                                            </span>
                                        @else
                                            <a href="{{ route('grave-orders.create') }}"
                                            class="btn btn-sm btn-warning text-dark btn-rounded">
                                                <i class="bi bi-arrow-repeat me-1"></i> Tempah Semula
                                            </a>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="empty-box">
                                    <i class="bi bi-inbox fs-1 d-block mb-3"></i>

                                    <h6 class="fw-bold mb-1">
                                        Tiada tempahan ditemui
                                    </h6>

                                    <div>
                                        Belum ada permohonan tempahan kepuk / nisan dihantar.
                                    </div>

                                    <a href="{{ route('grave-orders.create') }}"
                                       class="btn btn-info text-white btn-rounded mt-3">
                                        <i class="bi bi-plus-circle me-1"></i> Buat Permohonan Baru
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders instanceof \Illuminate\Pagination\AbstractPaginator && $orders->hasPages())
            <div class="p-3 border-top">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>
@endsection