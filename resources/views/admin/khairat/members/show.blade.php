@extends('layouts.app')

@section('content')
<style>
    .member-show-page {
        --soft-primary: #8ec5ff;
        --soft-primary-2: #d8ecff;
        --soft-primary-3: #eef7ff;
        --soft-border: #d9e8f7;
        --soft-text: #1f2937;
        --soft-muted: #6b7280;
    }

    .member-show-page .hero-card,
    .member-show-page .info-card {
        border: 0;
        border-radius: 22px;
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.06);
    }

    .member-show-page .hero-card {
        background: linear-gradient(135deg, #cfe7ff 0%, #9fcdff 45%, #7fb8ff 100%);
        color: var(--soft-text);
        overflow: hidden;
        position: relative;
    }

    .member-show-page .hero-card::before {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(to right, rgba(255,255,255,0.18), rgba(255,255,255,0.02));
        pointer-events: none;
    }

    .member-show-page .hero-card::after {
        content: "";
        position: absolute;
        top: -35px;
        right: -35px;
        width: 190px;
        height: 190px;
        background: rgba(255,255,255,0.18);
        border-radius: 50%;
    }

    .member-show-page .hero-card .card-body {
        position: relative;
        z-index: 1;
    }

    .member-show-page .profile-avatar {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        font-weight: 800;
        color: #2d5b8f;
        background: rgba(255,255,255,0.28);
        backdrop-filter: blur(4px);
        box-shadow: inset 0 1px 0 rgba(255,255,255,0.35);
    }

    .member-show-page .hero-eyebrow,
    .member-show-page .hero-meta {
        color: #5b6b7f;
    }

    .member-show-page .hero-name {
        color: #1e293b;
        font-size: clamp(22px, 2.2vw, 34px);
        line-height: 1.2;
    }

    .member-show-page .status-badge {
        padding: 9px 15px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.2px;
        border: 0;
        box-shadow: none;
    }

    .member-show-page .section-title {
        font-size: 16px;
        font-weight: 700;
        color: #111827;
        margin-bottom: 18px;
    }

    .member-show-page .info-card {
        background: #ffffff;
    }

    .member-show-page .info-item {
        margin-bottom: 16px;
    }

    .member-show-page .info-label {
        font-size: 13px;
        color: #7b8794;
        margin-bottom: 4px;
        text-transform: none;
        letter-spacing: 0;
        font-weight: 600;
    }

    .member-show-page .info-value {
        font-weight: 600;
        color: #111827;
        word-break: break-word;
        font-size: 14px;
        line-height: 1.5;
    }

    .member-show-page .mini-stat {
        background: rgba(255,255,255,0.72);
        border: 1px solid rgba(255,255,255,0.32);
        border-radius: 18px;
        padding: 18px 18px;
        height: 100%;
        box-shadow: 0 8px 20px rgba(30, 41, 59, 0.05);
        backdrop-filter: blur(6px);
    }

    .member-show-page .mini-stat .label {
        font-size: 13px;
        color: #5f6f82;
        margin-bottom: 6px;
        font-weight: 500;
    }

    .member-show-page .mini-stat .value {
        font-size: 17px;
        font-weight: 800;
        color: #111827;
    }

    .member-show-page .table thead th {
        background: #f8fbff;
        font-weight: 800;
        color: #334155;
        border-bottom: 1px solid #e5eef8;
        white-space: nowrap;
    }

    .member-show-page .table tbody td {
        vertical-align: middle;
        color: #1f2937;
    }

    .member-show-page .table tbody tr:hover {
        background: #fbfdff;
    }

    .member-show-page .btn {
        border-radius: 14px;
    }

    .member-show-page .btn-outline-secondary {
        border-color: #b7d8f7;
        color: #4b6b88;
        background: #f8fbff;
    }

    .member-show-page .btn-outline-secondary:hover {
        border-color: #8ec5ff;
        background: #eaf5ff;
        color: #2b567e;
    }

    .member-show-page .empty-text {
        color: #7b8794;
        font-weight: 500;
    }

    @media (max-width: 991.98px) {
        .member-show-page .hero-name {
            font-size: 30px;
        }
    }
</style>

<div class="container-fluid member-show-page">

    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <h1 class="page-title fw-semibold fs-20 mb-1">Maklumat Ahli</h1>
            <p class="text-muted mb-0">Paparan lengkap maklumat ahli khairat.</p>
        </div>
        <div class="mt-3 mt-md-0">
            <a href="{{ route('admin.khairat.members.index') }}" class="btn btn-outline-secondary">
                <i class="bx bx-arrow-back me-1"></i> Kembali
            </a>
        </div>
    </div>

    <div class="card hero-card mb-4">
        <div class="card-body p-4 p-lg-5">
            <div class="d-flex flex-column flex-lg-row justify-content-between gap-4">
                <div class="d-flex align-items-start gap-3">
                    <div class="profile-avatar">
                        {{ strtoupper(substr($member->nama ?? 'A', 0, 1)) }}
                    </div>
                    <div>
                        <div class="small hero-eyebrow mb-1">Ahli Khairat</div>
                        <h2 class="fw-bold mb-1 hero-name">{{ $member->nama }}</h2>
                        <div class="hero-meta mb-3">{{ $member->no_kp }}</div>

                        <span class="badge bg-{{ $statusClass }} status-badge">
                            {{ ucfirst(str_replace('_', ' ', $member->status_permohonan ?? 'tiada status')) }}
                        </span>
                    </div>
                </div>

                <div class="row g-3 flex-grow-1">
                    <div class="col-md-4">
                        <div class="mini-stat">
                            <div class="label">No. Telefon</div>
                            <div class="value">{{ $member->no_tel_bimbit ?? '-' }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mini-stat">
                            <div class="label">Pelan Bayaran</div>
                            <div class="value">{{ ucfirst($member->payment_plan ?? '-') }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mini-stat">
                            <div class="label">Tarikh Permohonan</div>
                            <div class="value">
                                {{ $member->tarikh_permohonan ? \Carbon\Carbon::parse($member->tarikh_permohonan)->format('d/m/Y') : '-' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-6">
            <div class="card info-card h-100">
                <div class="card-body p-4">
                    <div class="section-title">Maklumat Peribadi</div>

                    <div class="info-item">
                        <div class="info-label">Nama</div>
                        <div class="info-value">{{ $member->nama }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">No. MyKad</div>
                        <div class="info-value">{{ $member->no_kp }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Tarikh Lahir</div>
                        <div class="info-value">
                            {{ $member->tarikh_lahir ? \Carbon\Carbon::parse($member->tarikh_lahir)->format('d/m/Y') : '-' }}
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Agama</div>
                        <div class="info-value">{{ $member->agama ?? '-' }}</div>
                    </div>

                    <div class="info-item mb-0">
                        <div class="info-label">Warganegara</div>
                        <div class="info-value">{{ $member->warganegara ?? '-' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card info-card h-100">
                <div class="card-body p-4">
                    <div class="section-title">Maklumat Perhubungan</div>

                    <div class="info-item">
                        <div class="info-label">Alamat Rumah</div>
                        <div class="info-value">{{ $member->alamat_rumah ?? '-' }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">No. Tel Rumah</div>
                        <div class="info-value">{{ $member->no_tel_rumah ?? '-' }}</div>
                    </div>

                    <div class="info-item mb-0">
                        <div class="info-label">No. Telefon Bimbit</div>
                        <div class="info-value">{{ $member->no_tel_bimbit ?? '-' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card info-card h-100">
                <div class="card-body p-4">
                    <div class="section-title">Maklumat Waris</div>

                    <div class="info-item">
                        <div class="info-label">Nama Waris</div>
                        <div class="info-value">{{ $member->nama_waris ?? '-' }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Hubungan Waris</div>
                        <div class="info-value">{{ $member->hubungan_waris ?? '-' }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">No. Tel Waris</div>
                        <div class="info-value">{{ $member->no_tel_waris ?? '-' }}</div>
                    </div>

                    <div class="info-item mb-0">
                        <div class="info-label">Alamat Waris</div>
                        <div class="info-value">{{ $member->alamat_waris ?? '-' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card info-card h-100">
                <div class="card-body p-4">
                    <div class="section-title">Maklumat Tambahan</div>

                    <div class="info-item">
                        <div class="info-label">Tinggal Dalam Kariah</div>
                        <div class="info-value">{{ $member->tinggal_dalam_kariah ? 'Ya' : 'Tidak' }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Tempoh Menetap</div>
                        <div class="info-value">{{ $member->tempoh_menetap ?? '-' }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Pekerjaan</div>
                        <div class="info-value">{{ $member->pekerjaan ?? '-' }}</div>
                    </div>

                    <div class="info-item mb-0">
                        <div class="info-label">Catatan Permohonan</div>
                        <div class="info-value">{{ $member->catatan_permohonan ?? '-' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card info-card">
                <div class="card-body p-4">
                    <div class="section-title">Senarai Tanggungan</div>

                    @if($dependents->count())
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Nama</th>
                                        <th>No. KP</th>
                                        <th>Pertalian</th>
                                        <th>No. Telefon</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($dependents as $dependent)
                                        <tr>
                                            <td>{{ $dependent->name ?? '-' }}</td>
                                            <td>{{ $dependent->no_kp ?? '-' }}</td>
                                            <td>{{ ucfirst($dependent->pertalian ?? '-') }}</td>
                                            <td>{{ $dependent->no_tel ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-text">Tiada tanggungan direkodkan.</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card info-card">
                <div class="card-body p-4">
                    <div class="section-title">Rekod Bayaran</div>

                    @if($payments->count())
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Tarikh</th>
                                        <th>Jumlah</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($payments as $payment)
                                        <tr>
                                            <td>{{ $payment->created_at ? $payment->created_at->format('d/m/Y') : '-' }}</td>
                                            <td>RM {{ number_format($payment->amount ?? 0, 2) }}</td>
                                            <td>{{ ucfirst($payment->status ?? '-') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-text">Tiada rekod bayaran.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>
@endsection