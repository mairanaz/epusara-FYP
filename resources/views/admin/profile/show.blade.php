@extends('layouts.app')

@section('content')
<style>
    .application-show-page .hero-card,
    .application-show-page .info-card,
    .application-show-page .action-card {
        border: 0;
        border-radius: 18px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.06);
    }

    .application-show-page .hero-card {
        background: linear-gradient(135deg, #f59e0b, #f97316);
        color: #fff;
        overflow: hidden;
        position: relative;
    }

    .application-show-page .hero-card::after {
        content: "";
        position: absolute;
        top: -35px;
        right: -35px;
        width: 170px;
        height: 170px;
        background: rgba(255,255,255,0.10);
        border-radius: 50%;
    }

    .application-show-page .profile-avatar {
        width: 62px;
        height: 62px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        font-weight: 800;
        color: #fff;
        background: rgba(255,255,255,0.18);
    }

    .application-show-page .status-badge {
        padding: 8px 14px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
    }

    .application-show-page .section-title {
        font-size: 15px;
        font-weight: 700;
        color: #111827;
        margin-bottom: 16px;
    }

    .application-show-page .info-item {
        margin-bottom: 14px;
    }

    .application-show-page .info-label {
        font-size: 12px;
        color: #6b7280;
        margin-bottom: 4px;
    }

    .application-show-page .info-value {
        font-weight: 600;
        color: #111827;
        word-break: break-word;
    }

    .application-show-page .btn {
        border-radius: 12px;
    }

    .application-show-page textarea.form-control,
    .application-show-page .form-control {
        border-radius: 12px;
    }

    .application-show-page .mini-stat {
        background: #fff7ed;
        border-radius: 14px;
        padding: 14px 16px;
        height: 100%;
    }

    .application-show-page .mini-stat .label {
        font-size: 12px;
        color: #6b7280;
        margin-bottom: 4px;
    }

    .application-show-page .mini-stat .value {
        font-size: 15px;
        font-weight: 700;
        color: #111827;
    }
</style>

<div class="container-fluid application-show-page">

    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <h1 class="page-title fw-semibold fs-20 mb-1">Semakan Permohonan Keahlian</h1>
            <p class="text-muted mb-0">Paparan lengkap maklumat permohonan pengguna untuk tindakan pentadbir.</p>
        </div>
        <div class="mt-3 mt-md-0">
            <a href="{{ route('admin.profile.index') }}" class="btn btn-outline-secondary">
                <i class="bx bx-arrow-back me-1"></i> Kembali
            </a>
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

    @if ($errors->any())
        <div class="alert alert-danger border-0 shadow-sm rounded-4">
            <div class="fw-semibold mb-2">Sila semak maklumat berikut:</div>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card hero-card mb-4">
        <div class="card-body p-4 p-lg-5">
            <div class="d-flex flex-column flex-lg-row justify-content-between gap-4">
                <div class="d-flex align-items-start gap-3">
                    <div class="profile-avatar">
                        {{ strtoupper(substr($profile->nama ?? 'A', 0, 1)) }}
                    </div>
                    <div>
                        <div class="small text-white-50 mb-1">Permohonan Keahlian</div>
                        <h2 class="fw-bold mb-1">{{ $profile->nama }}</h2>
                        <div class="text-white-50 mb-3">{{ $profile->no_kp }}</div>

                        @if($profile->status_permohonan)
                            <span class="badge bg-{{ $statusClass }} status-badge">
                                Status: {{ ucfirst(str_replace('_', ' ', $profile->status_permohonan)) }}
                            </span>
                        @else
                            <span class="badge bg-secondary status-badge">
                                Status: Belum Dihantar
                            </span>
                        @endif
                    </div>
                </div>

                <div class="row g-3 flex-grow-1">
                    <div class="col-md-4">
                        <div class="mini-stat">
                            <div class="label">Tarikh Permohonan</div>
                            <div class="value">
                                {{ $profile->tarikh_permohonan ? \Carbon\Carbon::parse($profile->tarikh_permohonan)->format('d/m/Y') : '-' }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mini-stat">
                            <div class="label">Pelan Bayaran</div>
                            <div class="value">{{ ucfirst($profile->payment_plan ?? '-') }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mini-stat">
                            <div class="label">Status Bayaran</div>
                            <div class="value">
                                @if($hasPaidPayment)
                                    Sudah Dibayar
                                @else
                                    Belum Dibayar
                                @endif
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
                    <div class="section-title">A. Maklumat Peribadi</div>

                    <div class="info-item">
                        <div class="info-label">Nama</div>
                        <div class="info-value">{{ $profile->nama }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">No. MyKad</div>
                        <div class="info-value">{{ $profile->no_kp }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Tarikh Lahir</div>
                        <div class="info-value">
                            {{ $profile->tarikh_lahir ? \Carbon\Carbon::parse($profile->tarikh_lahir)->format('d/m/Y') : '-' }}
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Agama</div>
                        <div class="info-value">{{ $profile->agama }}</div>
                    </div>

                    <div class="info-item mb-0">
                        <div class="info-label">Warganegara</div>
                        <div class="info-value">{{ $profile->warganegara }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card info-card h-100">
                <div class="card-body p-4">
                    <div class="section-title">B. Maklumat Perhubungan</div>

                    <div class="info-item">
                        <div class="info-label">Alamat Rumah</div>
                        <div class="info-value">{{ $profile->alamat_rumah }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">No. Tel Rumah</div>
                        <div class="info-value">{{ $profile->no_tel_rumah ?? '-' }}</div>
                    </div>

                    <div class="info-item mb-0">
                        <div class="info-label">No. Telefon Bimbit</div>
                        <div class="info-value">{{ $profile->no_tel_bimbit }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card info-card h-100">
                <div class="card-body p-4">
                    <div class="section-title">C. Maklumat Kariah</div>

                    <div class="info-item">
                        <div class="info-label">Tinggal Dalam Kariah</div>
                        <div class="info-value">{{ $profile->tinggal_dalam_kariah ? 'Ya' : 'Tidak' }}</div>
                    </div>

                    <div class="info-item mb-0">
                        <div class="info-label">Tempoh Menetap</div>
                        <div class="info-value">{{ $profile->tempoh_menetap }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card info-card h-100">
                <div class="card-body p-4">
                    <div class="section-title">D. Maklumat Pekerjaan</div>

                    <div class="info-item">
                        <div class="info-label">Pekerjaan</div>
                        <div class="info-value">{{ $profile->pekerjaan ?? '-' }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Nama Majikan</div>
                        <div class="info-value">{{ $profile->nama_majikan ?? '-' }}</div>
                    </div>

                    <div class="info-item mb-0">
                        <div class="info-label">Alamat Kerja</div>
                        <div class="info-value">{{ $profile->alamat_kerja ?? '-' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card info-card">
                <div class="card-body p-4">
                    <div class="section-title">E. Maklumat Waris</div>

                    <div class="row g-4">
                        <div class="col-md-3">
                            <div class="info-label">Nama Waris</div>
                            <div class="info-value">{{ $profile->nama_waris }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-label">Hubungan Waris</div>
                            <div class="info-value">{{ $profile->hubungan_waris }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-label">No. Tel Waris</div>
                            <div class="info-value">{{ $profile->no_tel_waris }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-label">Alamat Waris</div>
                            <div class="info-value">{{ $profile->alamat_waris }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card info-card">
                <div class="card-body p-4">
                    <div class="section-title">F. Maklumat Permohonan</div>

                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="info-label">Tarikh Permohonan</div>
                            <div class="info-value">
                                {{ $profile->tarikh_permohonan ? \Carbon\Carbon::parse($profile->tarikh_permohonan)->format('d/m/Y') : '-' }}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-label">Status Permohonan</div>
                            <div class="info-value">
                                @if($profile->status_permohonan)
                                    <span class="badge bg-{{ $statusClass }} status-badge">
                                        {{ ucfirst(str_replace('_', ' ', $profile->status_permohonan)) }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary status-badge">Belum Dihantar</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-label">Catatan</div>
                            <div class="info-value">{{ $profile->catatan_permohonan ?? '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card info-card h-100">
                <div class="card-body p-4">
                    <div class="section-title">G. Pelan Pembayaran</div>

                    <div class="info-item">
                        <div class="info-label">Pelan Dipilih</div>
                        <div class="info-value">{{ ucfirst($profile->payment_plan ?? '-') }}</div>
                    </div>

                    <div class="info-item mb-0">
                        <div class="info-label">Keterangan</div>
                        <div class="info-value">
                            @if($profile->payment_plan === 'tahunan')
                                RM20 pendaftaran + RM100 tahunan
                            @elseif($profile->payment_plan === 'bulanan')
                                RM20 pendaftaran + RM10 bulan semasa
                            @else
                                -
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card info-card h-100">
                <div class="card-body p-4">
                    <div class="section-title">H. Status Bayaran</div>

                    @if($hasPaidPayment)
                        <span class="badge bg-success status-badge">Sudah Dibayar</span>
                    @else
                        <span class="badge bg-danger status-badge">Belum Dibayar</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($profile->status_permohonan === 'pending')
        <div class="card action-card mt-4">
            <div class="card-body p-4">
                <div class="section-title mb-3">Tindakan Pentadbir</div>

                <div class="d-flex flex-wrap gap-2 mb-4">
                    <form action="{{ route('admin.profile.approve', $profile) }}" method="POST" onsubmit="return confirm('Adakah anda pasti mahu meluluskan permohonan ini?');">
                        @csrf
                        <button type="submit" class="btn btn-success">
                            <i class="bx bx-check-circle me-1"></i> Luluskan Permohonan
                        </button>
                    </form>
                </div>

                <hr>

                <form action="{{ route('admin.profile.reject', $profile) }}" method="POST" onsubmit="return confirm('Adakah anda pasti mahu menolak permohonan ini?');">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Sebab / Catatan Penolakan</label>
                        <textarea name="catatan_permohonan"
                                  rows="4"
                                  class="form-control @error('catatan_permohonan') is-invalid @enderror"
                                  placeholder="Sila nyatakan sebab permohonan ditolak">{{ old('catatan_permohonan') }}</textarea>
                        @error('catatan_permohonan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-danger">
                        <i class="bx bx-x-circle me-1"></i> Tolak Permohonan
                    </button>
                </form>
            </div>
        </div>
    @endif

</div>
@endsection