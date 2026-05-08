@extends('layouts.app')

@section('content')
<style>
    .admin-order-header {
        background: linear-gradient(135deg, #f8fbff 0%, #eef7ff 100%);
        border: 1px solid #e5eef9;
        border-radius: 18px;
        padding: 24px;
        margin-bottom: 24px;
    }

    .custom-card {
        background: #fff;
        border: 1px solid #e9eef5;
        border-radius: 18px;
        box-shadow: 0 8px 22px rgba(15, 23, 42, 0.04);
        overflow: hidden;
    }

    .custom-card .card-header {
        background: #fff;
        border-bottom: 1px solid #eef2f7;
        padding: 18px 22px;
    }

    .custom-card .card-title {
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 0;
    }

    .info-label {
        font-size: 12px;
        color: #64748b;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .02em;
        margin-bottom: 4px;
    }

    .info-value {
        color: #0f172a;
        font-weight: 700;
    }

    .info-row {
        padding: 14px 0;
        border-bottom: 1px dashed #e5eaf2;
    }

    .info-row:last-child {
        border-bottom: none;
    }

    .order-avatar {
        width: 74px;
        height: 74px;
        border-radius: 18px;
        background: #eaf7ff;
        color: #0d6efd;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 34px;
        flex-shrink: 0;
    }

    .status-badge {
        font-size: 12px;
        padding: 8px 12px;
        border-radius: 999px;
    }

    .price-summary-box {
        background: linear-gradient(135deg, #f8fbff, #eef7ff);
        border: 1px solid #e5eef9;
        border-radius: 16px;
        padding: 20px;
    }

    .price-text {
        font-size: 36px;
        font-weight: 900;
        color: #0d6efd;
        line-height: 1;
    }

    .detail-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 11px;
        border-radius: 999px;
        background: #f1f5f9;
        color: #475569;
        font-size: 12px;
        font-weight: 700;
    }

    .user-avatar {
        width: 54px;
        height: 54px;
        border-radius: 50%;
        background: #eaf7ff;
        color: #0d6efd;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        flex-shrink: 0;
    }

    .right-card-sticky {
        position: sticky;
        top: 90px;
    }

    .timeline {
        position: relative;
        padding-left: 4px;
    }

    .track-item {
        position: relative;
        padding-left: 44px;
        padding-bottom: 24px;
    }

    .track-item::before {
        content: "";
        position: absolute;
        left: 17px;
        top: 34px;
        bottom: -4px;
        width: 2px;
        background: #e5eaf2;
    }

    .track-item:last-child {
        padding-bottom: 0;
    }

    .track-item:last-child::before {
        display: none;
    }

    .track-icon {
        position: absolute;
        left: 0;
        top: 0;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: #eaf7ff;
        color: #0d6efd;
        border: 4px solid #fff;
        box-shadow: 0 0 0 1px #dbeafe;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .track-icon.success {
        background: #dcfce7;
        color: #16a34a;
        box-shadow: 0 0 0 1px #bbf7d0;
    }

    .track-icon.warning {
        background: #fef3c7;
        color: #d97706;
        box-shadow: 0 0 0 1px #fde68a;
    }

    .track-icon.cancel {
        background: #f1f5f9;
        color: #64748b;
        box-shadow: 0 0 0 1px #cbd5e1;
    }

    .track-icon.muted {
        background: #f8fafc;
        color: #94a3b8;
        box-shadow: 0 0 0 1px #e2e8f0;
    }

    .status-panel {
        background: #f8fbff;
        border: 1px solid #e5eef9;
        border-radius: 16px;
        padding: 16px;
    }

    .note-box {
        background: #fff8e6;
        border: 1px solid #ffe2a8;
        color: #8a5a00;
        border-radius: 14px;
        padding: 14px;
        font-size: 13px;
        font-weight: 600;
    }

    .form-select,
    .form-control,
    textarea {
        border-radius: 14px !important;
        border-color: #dbe6f3 !important;
        padding: 12px 15px;
    }

    .form-select:focus,
    .form-control:focus,
    textarea:focus {
        border-color: #86c8ff !important;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, .10) !important;
    }

    .btn-rounded {
        border-radius: 12px;
    }

    .btn-submit-main {
        background: linear-gradient(135deg, #0d6efd, #0aa2c0);
        border: none;
        color: #fff;
        font-weight: 700;
        border-radius: 14px;
        padding: 13px 16px;
        min-height: 52px;
    }

    .btn-submit-main:hover {
        color: #fff;
        opacity: .95;
    }

    .section-mini-title {
        font-size: 14px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 12px;
    }

    .status-update-icon {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        background: #eaf7ff;
        color: #0d6efd;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
    }

    .status-textarea {
        min-height: 145px;
        resize: vertical;
        line-height: 1.6;
    }

    .summary-mini-box {
        background: #f8fbff;
        border: 1px solid #e5eef9;
        border-radius: 16px;
        padding: 16px;
    }

    @media (max-width: 1199px) {
        .right-card-sticky {
            position: static;
        }
    }

    @media (max-width: 768px) {
        .admin-order-header {
            padding: 18px;
        }

        .price-text {
            font-size: 28px;
        }

        .order-avatar {
            width: 62px;
            height: 62px;
            font-size: 28px;
        }
    }
</style>

@php
    $deathReport = $graveOrder->deathReport;
    $plot = $graveOrder->burialPlot;

    $lotNo = $plot->plot_code
        ?? $deathReport?->burial_plot_code
        ?? $deathReport?->burial_lot_no
        ?? '-';

    $burialDate = $deathReport?->burial_date
        ?? $deathReport?->tarikh_kebumi
        ?? null;

    $isPending = $graveOrder->status === 'pending';
    $isApproved = $graveOrder->status === 'approved';
    $isCancelled = $graveOrder->status === 'cancelled';
@endphp

<div class="container-fluid">

    <div class="admin-order-header">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <div class="text-muted small mb-1">
                    Admin / Tempahan Kepuk / Nisan / Semak Tempahan
                </div>

                <h3 class="fw-bold mb-1">
                    Semakan Tempahan Kepuk / Nisan
                </h3>

                <p class="text-muted mb-0">
                    Semak maklumat tempahan, waris, si mati dan kemaskini status permohonan.
                </p>
            </div>

            <a href="{{ route('admin.grave-orders.index') }}" class="btn btn-light border btn-rounded">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
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

    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm rounded-3">
            <div class="fw-semibold mb-1">Sila semak maklumat berikut:</div>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4">

        <div class="col-xl-8">

            <div class="custom-card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="card-title">
                        Tempahan No - <span class="text-primary">#KPN-{{ str_pad($graveOrder->id, 5, '0', STR_PAD_LEFT) }}</span>
                    </div>

                    <span class="badge bg-{{ $graveOrder->statusBadge() }} status-badge">
                        {{ $graveOrder->statusLabel() }}
                    </span>
                </div>

                <div class="card-body p-4">
                    <div class="d-flex align-items-start gap-3 mb-4 flex-wrap">
                        <div class="order-avatar">
                            <i class="bi bi-flower1"></i>
                        </div>

                        <div class="flex-grow-1">
                            <h4 class="fw-bold mb-1">
                                {{ $graveOrder->order_label }}
                            </h4>

                            <div class="text-muted small mb-3">
                                Permohonan dihantar pada {{ optional($graveOrder->created_at)->format('d/m/Y h:i A') }}
                            </div>

                            <div class="d-flex flex-wrap gap-2">
                                <span class="detail-chip">
                                    <i class="bi bi-tag"></i>
                                    {{ $graveOrder->category === 'kanak-kanak' ? 'Kanak-kanak' : 'Dewasa' }}
                                </span>

                                <span class="detail-chip">
                                    <i class="bi bi-upc-scan"></i>
                                    {{ $graveOrder->order_type }}
                                </span>

                                <span class="detail-chip">
                                    <i class="bi bi-check2-circle"></i>
                                    {{ $graveOrder->declaration ? 'Perakuan disahkan' : 'Perakuan belum disahkan' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="price-summary-box mb-4">
                        <div class="text-muted small mb-1">Jumlah Tempahan</div>
                        <div class="price-text">
                            RM{{ number_format($graveOrder->amount, 2) }}
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-row">
                                <div class="info-label">Status Semasa</div>
                                <span class="badge bg-{{ $graveOrder->statusBadge() }} status-badge">
                                    {{ $graveOrder->statusLabel() }}
                                </span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-row">
                                <div class="info-label">Tarikh Diluluskan</div>
                                <div class="info-value">
                                    {{ $graveOrder->approved_at ? $graveOrder->approved_at->format('d/m/Y h:i A') : '-' }}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-row">
                                <div class="info-label">No. Lot Kubur</div>
                                <div class="info-value">{{ $lotNo }}</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-row">
                                <div class="info-label">No. Resit</div>
                                <div class="info-value">
                                    {{ $graveOrder->receipt_no ?? 'Belum direkodkan' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($graveOrder->admin_note)
                        <div class="note-box mt-3">
                            <div class="fw-bold mb-1">
                                <i class="bi bi-chat-left-text me-1"></i> Catatan Pentadbir
                            </div>
                            {{ $graveOrder->admin_note }}
                        </div>
                    @endif
                </div>
            </div>

            <div class="custom-card mb-4">
                <div class="card-header">
                    <div class="card-title">
                        Maklumat Si Mati
                    </div>
                </div>

                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-row">
                                <div class="info-label">Nama Si Mati</div>
                                <div class="info-value">{{ $deathReport->nama_si_mati ?? '-' }}</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-row">
                                <div class="info-label">No. Kad Pengenalan</div>
                                <div class="info-value">{{ $deathReport->no_kp_si_mati ?? '-' }}</div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="info-row">
                                <div class="info-label">Jantina</div>
                                <div class="info-value">{{ $deathReport->jantina ?? '-' }}</div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="info-row">
                                <div class="info-label">Umur</div>
                                <div class="info-value">
                                    {{ $deathReport?->umur ? $deathReport->umur . ' tahun' : '-' }}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="info-row">
                                <div class="info-label">Tarikh Meninggal</div>
                                <div class="info-value">
                                    {{ $deathReport?->tarikh_meninggal ? $deathReport->tarikh_meninggal->format('d/m/Y') : '-' }}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-row">
                                <div class="info-label">Tarikh Kebumi</div>
                                <div class="info-value">
                                    {{ $burialDate ? \Carbon\Carbon::parse($burialDate)->format('d/m/Y') : '-' }}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-row">
                                <div class="info-label">Lot Kubur</div>
                                <div class="info-value">{{ $lotNo }}</div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="info-row">
                                <div class="info-label">Alamat Terakhir</div>
                                <div class="info-value">{{ $deathReport->alamat_terakhir ?? '-' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="custom-card">
                <div class="card-header">
                    <div class="card-title">
                        Maklumat Waris / Pelapor
                    </div>
                </div>

                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3 flex-wrap mb-4">
                        <div class="user-avatar">
                            <i class="bi bi-person"></i>
                        </div>

                        <div class="flex-fill">
                            <div class="fw-bold">
                                {{ $deathReport->nama_pelapor ?? '-' }}
                            </div>

                            <div class="text-muted small">
                                {{ $deathReport->pertalian_pelapor ?? 'Waris / Pelapor' }}
                            </div>
                        </div>

                        <span class="badge bg-primary-transparent text-primary">
                            Waris
                        </span>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="info-row">
                                <div class="info-label">No. Kad Pengenalan</div>
                                <div class="info-value">{{ $deathReport->no_kp_pelapor ?? '-' }}</div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="info-row">
                                <div class="info-label">No. Telefon</div>
                                <div class="info-value">{{ $deathReport->no_tel_pelapor ?? '-' }}</div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="info-row">
                                <div class="info-label">Pertalian</div>
                                <div class="info-value">{{ $deathReport->pertalian_pelapor ?? '-' }}</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-row">
                                <div class="info-label">Nama Akaun</div>
                                <div class="info-value">{{ $graveOrder->user->name ?? '-' }}</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-row">
                                <div class="info-label">Email Akaun</div>
                                <div class="info-value">{{ $graveOrder->user->email ?? '-' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="col-xl-4">
            <div class="right-card-sticky">

                <div class="custom-card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="card-title">
                            Status Tempahan
                        </div>
                        <div class="text-success small fw-bold">
                            #KPN-{{ str_pad($graveOrder->id, 5, '0', STR_PAD_LEFT) }}
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <div class="timeline">

                            <div class="track-item">
                                <div class="track-icon success">
                                    <i class="bi bi-send-check"></i>
                                </div>

                                <div class="fw-bold mb-1">
                                    Permohonan Dihantar
                                </div>

                                <div class="text-muted small">
                                    {{ optional($graveOrder->created_at)->format('d/m/Y h:i A') }}
                                </div>

                                <div class="text-muted small mt-1">
                                    Waris telah menghantar permohonan tempahan.
                                </div>
                            </div>

                            <div class="track-item">
                                <div class="track-icon {{ $isApproved ? 'success' : ($isPending ? 'warning' : 'cancel') }}">
                                    @if($isApproved)
                                        <i class="bi bi-check2-circle"></i>
                                    @elseif($isCancelled)
                                        <i class="bi bi-x-circle"></i>
                                    @else
                                        <i class="bi bi-hourglass-split"></i>
                                    @endif
                                </div>

                                <div class="fw-bold mb-1">
                                    @if($isApproved)
                                        Diluluskan Pentadbir
                                    @elseif($isCancelled)
                                        Tempahan Dibatalkan
                                    @else
                                        Menunggu Kelulusan
                                    @endif
                                </div>

                                <div class="text-muted small">
                                    @if($isApproved)
                                        {{ $graveOrder->approved_at ? $graveOrder->approved_at->format('d/m/Y h:i A') : '-' }}
                                    @elseif($isCancelled)
                                        Sila rujuk catatan pentadbir.
                                    @else
                                        Permohonan sedang menunggu tindakan pentadbir.
                                    @endif
                                </div>
                            </div>

                            <div class="track-item">
                                <div class="track-icon {{ $isApproved ? 'success' : 'muted' }}">
                                    <i class="bi bi-clipboard2-check"></i>
                                </div>

                                <div class="fw-bold mb-1">
                                    Urusan Pentadbiran
                                </div>

                                <div class="text-muted small">
                                    @if($isApproved)
                                        Tempahan telah diterima dan boleh diteruskan oleh pihak pentadbiran.
                                    @elseif($isCancelled)
                                        Tidak diteruskan kerana tempahan telah dibatalkan.
                                    @else
                                        Akan diteruskan selepas tempahan diluluskan.
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="custom-card mb-4">
                    <div class="card-header">
                        <div class="card-title d-flex align-items-center gap-2">
                            <span class="status-update-icon">
                                <i class="bi bi-pencil-square"></i>
                            </span>
                            Kemaskini Status
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <form action="{{ route('admin.grave-orders.update', $graveOrder) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-4">
                                <label class="form-label fw-semibold mb-2">
                                    Status Tempahan
                                </label>

                                <select name="status" class="form-select" required>
                                    <option value="pending" {{ old('status', $graveOrder->status) === 'pending' ? 'selected' : '' }}>
                                        Menunggu Kelulusan
                                    </option>

                                    <option value="approved" {{ old('status', $graveOrder->status) === 'approved' ? 'selected' : '' }}>
                                        Diluluskan
                                    </option>

                                    <option value="cancelled" {{ old('status', $graveOrder->status) === 'cancelled' ? 'selected' : '' }}>
                                        Dibatalkan
                                    </option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold mb-2">
                                    Catatan Admin
                                </label>

                                <textarea name="admin_note"
                                          rows="5"
                                          class="form-control status-textarea"
                                          placeholder="Contoh: Tempahan diluluskan dan akan diuruskan oleh pihak pentadbiran.">{{ old('admin_note', $graveOrder->admin_note) }}</textarea>

                                <div class="small text-muted mt-2 lh-sm">
                                    Catatan boleh digunakan untuk maklumat tambahan atau sebab pembatalan.
                                </div>
                            </div>

                            <button type="submit" class="btn btn-submit-main w-100">
                                <i class="bi bi-save me-1"></i> Simpan Kemaskini
                            </button>
                        </form>
                    </div>
                </div>

                <div class="custom-card">
                    <div class="card-header">
                        <div class="card-title">
                            Panduan Status
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <div class="summary-mini-box">
                            <div class="small text-muted">
                                <div class="mb-3">
                                    <strong class="text-dark">Menunggu Kelulusan:</strong>
                                    Tempahan baru dihantar oleh waris.
                                </div>

                                <div class="mb-3">
                                    <strong class="text-dark">Diluluskan:</strong>
                                    Tempahan diterima oleh pihak pentadbiran.
                                </div>

                                <div>
                                    <strong class="text-dark">Dibatalkan:</strong>
                                    Tempahan tidak diteruskan dan waris boleh membuat tempahan baharu jika perlu.
                                </div>
                            </div>
                        </div>

                        @if($isCancelled && $graveOrder->admin_note)
                            <div class="note-box mt-3">
                                <div class="fw-bold mb-1">
                                    Sebab Pembatalan
                                </div>
                                {{ $graveOrder->admin_note }}
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
@endsection