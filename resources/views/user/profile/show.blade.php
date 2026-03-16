@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <h1 class="page-title fw-semibold fs-20 mb-1">Maklumat Ahli</h1>
            <p class="text-muted mb-0">Paparan lengkap maklumat keahlian anda.</p>
        </div>
        <div class="btn-list mt-3 mt-md-0">
            <a class="btn btn-primary" href="{{ route('user.profile.edit') }}">
                <i class="bi bi-pencil-square me-1"></i> Edit Maklumat
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @php
        $status = strtolower($profile->status_permohonan ?? '');
        $statusClass = match($status) {
            'lulus' => 'success',
            'diterima' => 'success',
            'pending' => 'warning',
            'dalam proses' => 'warning',
            'ditolak' => 'danger',
            default => 'secondary',
        };

        $paymentPlanLabel = match($profile->payment_plan) {
            'tahunan' => 'Tahunan',
            'bulanan' => 'Bulanan',
            default => '-',
        };
    @endphp

    <div class="card custom-card border-0 shadow-sm overflow-hidden mb-4 profile-summary-card">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="profile-avatar">
                        <i class="bi bi-person-vcard"></i>
                    </div>
                    <div>
                        <h4 class="mb-1 fw-bold">{{ $profile->nama ?? 'Nama belum diisi' }}</h4>
                        <div class="text-muted mb-2">
                            <i class="bi bi-credit-card-2-front me-1"></i>
                            {{ $profile->no_kp ?? '-' }}
                        </div>
                        <span class="badge rounded-pill bg-{{ $statusClass }}">
                            Status: {{ ucfirst($profile->status_permohonan ?? '-') }}
                        </span>
                    </div>
                </div>

                <div class="text-md-end">
                    <small class="text-muted d-block">Tarikh Permohonan</small>
                    <span class="fw-semibold">
                        {{ $profile->tarikh_permohonan ? $profile->tarikh_permohonan->format('d/m/Y') : '-' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">

        <div class="col-xl-6">
            <div class="card custom-card border-0 shadow-sm h-100 section-card">
                <div class="card-header section-header">
                    <h5 class="card-title mb-0 fw-semibold">
                        <i class="bi bi-person-lines-fill text-primary me-2"></i>
                        A. Maklumat Ahli
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="info-box">
                                <label>Nama Penuh</label>
                                <div>{{ $profile->nama ?? '-' }}</div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="info-box">
                                <label>No. MyKad</label>
                                <div>{{ $profile->no_kp ?? '-' }}</div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="info-box">
                                <label>Tarikh Lahir</label>
                                <div>{{ $profile->tarikh_lahir ? $profile->tarikh_lahir->format('d/m/Y') : '-' }}</div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="info-box">
                                <label>Agama</label>
                                <div>{{ $profile->agama ?? '-' }}</div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="info-box">
                                <label>Warganegara</label>
                                <div>{{ $profile->warganegara ?? '-' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card custom-card border-0 shadow-sm h-100 section-card">
                <div class="card-header section-header">
                    <h5 class="card-title mb-0 fw-semibold">
                        <i class="bi bi-telephone-fill text-success me-2"></i>
                        B. Maklumat Perhubungan
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="info-box">
                                <label>Alamat Rumah</label>
                                <div>{{ $profile->alamat_rumah ?? '-' }}</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-box">
                                <label>No. Tel Rumah</label>
                                <div>{{ $profile->no_tel_rumah ?? '-' }}</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-box">
                                <label>No. Telefon Bimbit</label>
                                <div>{{ $profile->no_tel_bimbit ?? '-' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card custom-card border-0 shadow-sm h-100 section-card">
                <div class="card-header section-header">
                    <h5 class="card-title mb-0 fw-semibold">
                        <i class="bi bi-geo-alt-fill text-danger me-2"></i>
                        C. Maklumat Kariah
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="info-box">
                                <label>Tinggal Dalam Kariah</label>
                                <div>
                                    <span class="badge bg-{{ $profile->tinggal_dalam_kariah ? 'success' : 'secondary' }}">
                                        {{ $profile->tinggal_dalam_kariah ? 'Ya' : 'Tidak' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-box">
                                <label>Tempoh Menetap</label>
                                <div>{{ $profile->tempoh_menetap ?? '-' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card custom-card border-0 shadow-sm h-100 section-card">
                <div class="card-header section-header">
                    <h5 class="card-title mb-0 fw-semibold">
                        <i class="bi bi-briefcase-fill text-warning me-2"></i>
                        D. Maklumat Pekerjaan
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="info-box">
                                <label>Pekerjaan</label>
                                <div>{{ $profile->pekerjaan ?? '-' }}</div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="info-box">
                                <label>Nama Majikan</label>
                                <div>{{ $profile->nama_majikan ?? '-' }}</div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="info-box">
                                <label>Alamat Kerja</label>
                                <div>{{ $profile->alamat_kerja ?? '-' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card custom-card border-0 shadow-sm section-card">
                <div class="card-header section-header">
                    <h5 class="card-title mb-0 fw-semibold">
                        <i class="bi bi-people-fill text-info me-2"></i>
                        E. Maklumat Waris
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="info-box">
                                <label>Nama Waris</label>
                                <div>{{ $profile->nama_waris ?? '-' }}</div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="info-box">
                                <label>Hubungan Waris</label>
                                <div>{{ $profile->hubungan_waris ?? '-' }}</div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="info-box">
                                <label>No. Tel Waris</label>
                                <div>{{ $profile->no_tel_waris ?? '-' }}</div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="info-box">
                                <label>Alamat Waris</label>
                                <div>{{ $profile->alamat_waris ?? '-' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card custom-card border-0 shadow-sm section-card">
                <div class="card-header section-header">
                    <h5 class="card-title mb-0 fw-semibold">
                        <i class="bi bi-file-earmark-text-fill text-secondary me-2"></i>
                        F. Maklumat Permohonan
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="info-box">
                                <label>Tarikh Permohonan</label>
                                <div>{{ $profile->tarikh_permohonan ? $profile->tarikh_permohonan->format('d/m/Y') : '-' }}</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-box">
                                <label>Status Permohonan</label>
                                <div>
                                    <span class="badge bg-{{ $statusClass }}">
                                        {{ ucfirst($profile->status_permohonan ?? '-') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card custom-card border-0 shadow-sm section-card">
                <div class="card-header section-header">
                    <h5 class="card-title mb-0 fw-semibold">
                        <i class="bi bi-credit-card-2-front-fill text-primary me-2"></i>
                        G. Pelan Pembayaran
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="info-box">
                                <label>Pelan Dipilih</label>
                                <div>{{ $paymentPlanLabel }}</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-box">
                                <label>Keterangan</label>
                                <div>
                                    @if($profile->payment_plan === 'tahunan')
                                        RM20 pendaftaran + RM100 tahunan
                                    @elseif($profile->payment_plan === 'bulanan')
                                        RM20 pendaftaran + RM10 bulan semasa, kemudian RM10 setiap bulan
                                    @else
                                        -
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<style>
    .profile-summary-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%);
        border-radius: 18px;
    }

    .profile-avatar {
        width: 72px;
        height: 72px;
        min-width: 72px;
        border-radius: 50%;
        background: rgba(99, 102, 241, 0.12);
        color: #4f46e5;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 30px;
    }

    .section-card {
        border-radius: 18px;
        overflow: hidden;
    }

    .section-header {
        background: #f8f9fb !important;
        border-bottom: 1px solid #edf0f5 !important;
        padding: 16px 20px;
    }

    .section-header .card-title {
        font-size: 1.1rem;
        color: #1f2937;
    }

    .info-box {
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        padding: 18px 20px;
        min-height: 95px;
        transition: all 0.2s ease-in-out;
    }

    .info-box:hover {
        background: #ffffff;
        border-color: #dbe2ea;
        box-shadow: 0 6px 18px rgba(15, 23, 42, 0.06);
        transform: translateY(-2px);
    }

    .info-box label {
        display: block;
        margin-bottom: 8px;
        font-size: 12px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.6px;
    }

    .info-box div {
        font-size: 15px;
        font-weight: 600;
        color: #111827;
        line-height: 1.5;
        word-break: break-word;
    }

    .badge {
        font-size: 12px;
        padding: 7px 12px;
    }

    @media (max-width: 767.98px) {
        .profile-avatar {
            width: 60px;
            height: 60px;
            min-width: 60px;
            font-size: 24px;
        }

        .info-box {
            min-height: auto;
        }
    }
</style>
@endsection