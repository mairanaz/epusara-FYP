@extends('layouts.app')

@section('content')
<style>
    .detail-header {
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

    .detail-icon {
        width: 56px;
        height: 56px;
        border-radius: 16px;
        background: #eaf7ff;
        color: #0d6efd;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 26px;
    }

    .info-row {
        padding: 14px 0;
        border-bottom: 1px solid #eef2f7;
    }

    .info-row:last-child {
        border-bottom: none;
    }

    .timeline-item {
        position: relative;
        padding-left: 34px;
        padding-bottom: 20px;
    }

    .timeline-item::before {
        content: "";
        width: 2px;
        background: #dbe6f3;
        position: absolute;
        top: 10px;
        bottom: -4px;
        left: 10px;
    }

    .timeline-item:last-child::before {
        display: none;
    }

    .timeline-dot {
        width: 22px;
        height: 22px;
        border-radius: 50%;
        background: #0d6efd;
        border: 4px solid #eaf7ff;
        position: absolute;
        left: 0;
        top: 2px;
    }

    .timeline-dot-muted {
        background: #cbd5e1;
    }

    .cancel-box {
        background: #fff8e6;
        border: 1px solid #ffe2a8;
        color: #8a5a00;
        border-radius: 16px;
        padding: 16px 18px;
    }

    .cancel-icon {
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

    .btn-rounded {
        border-radius: 12px;
    }

    .status-badge {
        font-size: 13px;
        padding: 8px 12px;
        border-radius: 999px;
    }
</style>

@php
    $deathReport = $graveOrder->deathReport;
    $plot = $graveOrder->burialPlot;

    $lotNo = $plot->plot_code
        ?? $deathReport?->burial_plot_code
        ?? $deathReport?->burial_lot_no
        ?? '-';

    $isApproved = $graveOrder->status === 'approved';
    $isCancelled = $graveOrder->status === 'cancelled';
@endphp

<div class="container-fluid">

    <div class="detail-header d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <div class="text-muted small mb-1">
                Dashboard / Tempahan Kepuk / Nisan / Butiran
            </div>

            <h3 class="fw-bold mb-1">
                Butiran Tempahan
            </h3>

            <p class="text-muted mb-0">
                Maklumat lengkap permohonan tempahan kepungan dan batu nisan.
            </p>
        </div>

        <a href="{{ route('grave-orders.index') }}" class="btn btn-light border btn-rounded">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    @if($isCancelled)
        <div class="cancel-box d-flex align-items-start gap-3 mb-4">
            <div class="cancel-icon">
                <i class="bi bi-info-circle"></i>
            </div>

            <div class="flex-grow-1">
                <div class="fw-bold mb-1">
                    Tempahan ini telah dibatalkan
                </div>

                <div class="small">
                    Tempahan yang dibatalkan tidak lagi dikira sebagai tempahan aktif.
                    Anda boleh membuat permohonan tempahan baharu untuk si mati ini sekiranya masih diperlukan.
                </div>

                @if($graveOrder->admin_note)
                    <hr>
                    <div class="small">
                        <strong>Catatan pentadbir:</strong> {{ $graveOrder->admin_note }}
                    </div>
                @endif

                <a href="{{ route('grave-orders.create') }}"
                   class="btn btn-warning text-dark btn-sm btn-rounded mt-3">
                    <i class="bi bi-arrow-repeat me-1"></i> Buat Tempahan Baharu
                </a>
            </div>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-xl-8">
            <div class="soft-card p-4 mb-4">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="detail-icon">
                        <i class="bi bi-clipboard-check"></i>
                    </div>

                    <div>
                        <h5 class="fw-bold mb-0">
                            Maklumat Tempahan
                        </h5>

                        <div class="text-muted small">
                            Permohonan dihantar pada {{ optional($graveOrder->created_at)->format('d/m/Y') }}
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="info-row">
                            <div class="text-muted small">Nama Si Mati</div>
                            <div class="fw-bold">{{ $deathReport->nama_si_mati ?? '-' }}</div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="info-row">
                            <div class="text-muted small">No. Lot Kubur</div>
                            <div class="fw-bold">{{ $lotNo }}</div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="info-row">
                            <div class="text-muted small">Jantina</div>
                            <div class="fw-bold">{{ $deathReport->jantina ?? '-' }}</div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="info-row">
                            <div class="text-muted small">Umur</div>
                            <div class="fw-bold">
                                {{ $deathReport?->umur ? $deathReport->umur . ' tahun' : '-' }}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="info-row">
                            <div class="text-muted small">Tarikh Meninggal</div>
                            <div class="fw-bold">
                                {{ optional($deathReport?->tarikh_meninggal)->format('d/m/Y') ?? '-' }}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="info-row">
                            <div class="text-muted small">Kategori Tempahan</div>
                            <div class="fw-bold">
                                {{ $graveOrder->category === 'kanak-kanak' ? 'Kanak-kanak' : 'Dewasa' }}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="info-row">
                            <div class="text-muted small">Status</div>
                            <span class="badge bg-{{ $graveOrder->statusBadge() }} status-badge">
                                {{ $graveOrder->statusLabel() }}
                            </span>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="info-row">
                            <div class="text-muted small">Tarikh Diluluskan</div>
                            <div class="fw-bold">
                                {{ $graveOrder->approved_at ? $graveOrder->approved_at->format('d/m/Y h:i A') : '-' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="soft-card p-4">
                <h5 class="fw-bold mb-3">
                    Pilihan Kepuk / Nisan
                </h5>

                <div class="info-row">
                    <div class="text-muted small">Jenis Tempahan</div>
                    <div class="fw-bold">{{ $graveOrder->order_label }}</div>
                </div>

                <div class="info-row">
                    <div class="text-muted small">Kod Tempahan</div>
                    <div class="fw-bold">{{ $graveOrder->order_type }}</div>
                </div>

                <div class="info-row">
                    <div class="text-muted small">Jumlah Bayaran</div>
                    <div class="display-6 fw-bold text-info">
                        RM{{ number_format($graveOrder->amount, 2) }}
                    </div>
                </div>

                <div class="info-row">
                    <div class="text-muted small">Catatan Pentadbir</div>
                    <div class="fw-semibold">
                        {{ $graveOrder->admin_note ?? 'Tiada catatan.' }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="soft-card p-4 mb-4">
                <h5 class="fw-bold mb-4">
                    Status Permohonan
                </h5>

                <div class="timeline-item">
                    <div class="timeline-dot"></div>
                    <div class="fw-bold">Permohonan Dihantar</div>
                    <div class="text-muted small">
                        {{ optional($graveOrder->created_at)->format('d/m/Y h:i A') }}
                    </div>
                </div>

                @if($isApproved)
                    <div class="timeline-item">
                        <div class="timeline-dot"></div>
                        <div class="fw-bold">Diluluskan Pentadbir</div>
                        <div class="text-muted small">
                            {{ $graveOrder->approved_at ? $graveOrder->approved_at->format('d/m/Y h:i A') : '-' }}
                        </div>
                    </div>
                @elseif($isCancelled)
                    <div class="timeline-item">
                        <div class="timeline-dot" style="background:#64748b;"></div>
                        <div class="fw-bold">Dibatalkan</div>
                        <div class="text-muted small">
                            Sila rujuk catatan pentadbir jika ada.
                        </div>
                    </div>
                @else
                    <div class="timeline-item">
                        <div class="timeline-dot timeline-dot-muted"></div>
                        <div class="fw-bold">Menunggu Kelulusan</div>
                        <div class="text-muted small">
                            Permohonan sedang menunggu tindakan pentadbir.
                        </div>
                    </div>
                @endif
            </div>

            <div class="soft-card p-4">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <i class="bi bi-info-circle text-info fs-4"></i>
                    <h6 class="fw-bold mb-0">
                        Maklumat Penting
                    </h6>
                </div>

                @if($isCancelled)
                    <p class="small text-muted mb-3">
                        Tempahan ini telah dibatalkan. Anda boleh membuat tempahan baharu untuk si mati yang sama jika masih diperlukan.
                    </p>

                    <a href="{{ route('grave-orders.create') }}"
                       class="btn btn-warning text-dark btn-rounded w-100">
                        <i class="bi bi-arrow-repeat me-1"></i> Buat Tempahan Baharu
                    </a>
                @elseif($isApproved)
                    <p class="small text-muted mb-0">
                        Tempahan ini telah diluluskan oleh pihak pentadbiran. Sila hubungi pentadbir jika terdapat perubahan pada maklumat tempahan.
                    </p>
                @else
                    <p class="small text-muted mb-0">
                        Permohonan yang telah dihantar akan disemak oleh pihak pentadbiran khairat.
                        Sila hubungi pentadbir jika terdapat perubahan pada maklumat tempahan.
                    </p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection