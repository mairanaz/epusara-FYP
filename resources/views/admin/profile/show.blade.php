@extends('layouts.app')

@section('content')
<style>
    .application-show-page .hero-card,
    .application-show-page .info-card,
    .application-show-page .action-card {
        border: 0;
        border-radius: 18px;
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.06);
    }

    .application-show-page .hero-card {
        background: linear-gradient(135deg, #eaf3ff, #dbeafe);
        color: #1e3a5f;
        overflow: hidden;
        position: relative;
        border: 1px solid #d6e6fb;
    }

    .application-show-page .hero-card::after {
        content: "";
        position: absolute;
        top: -45px;
        right: -45px;
        width: 140px;
        height: 140px;
        background: rgba(255, 255, 255, 0.18);
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
        color: #2563eb;
        background: rgba(255,255,255,0.75);
        border: 1px solid rgba(37, 99, 235, 0.12);
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
        color: #0f172a;
        margin-bottom: 16px;
    }

    .application-show-page .info-item {
        margin-bottom: 14px;
    }

    .application-show-page .info-label {
        font-size: 12px;
        color: #64748b;
        margin-bottom: 4px;
    }

    .application-show-page .info-value {
        font-weight: 600;
        color: #0f172a;
        word-break: break-word;
    }

    .application-show-page .btn {
        border-radius: 12px;
    }

    .application-show-page textarea.form-control,
    .application-show-page .form-control {
        border-radius: 12px;
        border: 1px solid #dbe5f0;
    }

    .application-show-page .mini-stat {
        background: rgba(255, 255, 255, 0.78);
        border: 1px solid #d9e8fb;
        border-radius: 14px;
        padding: 14px 16px;
        height: 100%;
        backdrop-filter: blur(2px);
    }

    .application-show-page .mini-stat .label {
        font-size: 12px;
        color: #64748b;
        margin-bottom: 4px;
    }

    .application-show-page .mini-stat .value {
        font-size: 15px;
        font-weight: 700;
        color: #0f172a;
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
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                icon: 'success',
                title: 'Berjaya',
                text: @json(session('success')),
                confirmButtonText: 'OK',
                confirmButtonColor: '#7c3aed',
                width: '500px'
            });
        });
    </script>
    @endif

    @if(session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                icon: 'error',
                title: 'Ralat',
                text: @json(session('error')),
                confirmButtonText: 'OK',
                confirmButtonColor: '#dc2626',
                width: '500px'
            });
        });
    </script>
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
                        <div class="small text-muted mb-1">Permohonan Keahlian</div>
                        <h2 class="fw-bold mb-1">{{ $profile->nama }}</h2>
                        <div class="text-muted mb-3">{{ $profile->no_kp }}</div>

                        @php
                            $statusLabel = match($profile->status_permohonan) {
                                'pending'  => 'Menunggu',
                                'approved' => 'Diluluskan',
                                'active'   => 'Diluluskan',
                                'rejected' => 'Ditolak',
                                default    => 'Belum Dihantar',
                            };

                            $statusBadgeClass = match($profile->status_permohonan) {
                                'pending'  => 'bg-warning-subtle text-warning',
                                'approved' => 'bg-success-subtle text-success',
                                'active'   => 'bg-success-subtle text-success',
                                'rejected' => 'bg-danger-subtle text-danger',
                                default    => 'bg-secondary-subtle text-secondary',
                            };
                        @endphp

                        <span class="badge {{ $statusBadgeClass }} status-badge">
                            Status: {{ $statusLabel }}
                        </span>
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
                                    <span class="badge {{ $statusBadgeClass }} status-badge">
                                        {{ $statusLabel }}
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
                    <form id="approveForm" action="{{ route('admin.profile.approve', $profile) }}" method="POST">
                        @csrf
                        <button type="button" id="approveBtn" class="btn btn-success">
                            <i class="bx bx-check-circle me-1"></i> Luluskan Permohonan
                        </button>
                    </form>
                </div>

                <hr>

                <form id="rejectForm" action="{{ route('admin.profile.reject', $profile) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Sebab / Catatan Penolakan</label>
                        <textarea name="catatan_permohonan"
                                id="catatan_permohonan"
                                rows="4"
                                class="form-control @error('catatan_permohonan') is-invalid @enderror"
                                placeholder="Sila nyatakan sebab permohonan ditolak">{{ old('catatan_permohonan') }}</textarea>
                        @error('catatan_permohonan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="button" id="rejectBtn" class="btn btn-danger">
                        <i class="bx bx-x-circle me-1"></i> Tolak Permohonan
                    </button>
                </form>
            </div>
        </div>
    @endif

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const approveBtn = document.getElementById('approveBtn');
    const approveForm = document.getElementById('approveForm');

    if (approveBtn && approveForm) {
        approveBtn.addEventListener('click', function () {
            Swal.fire({
                title: 'Adakah anda pasti?',
                text: 'Permohonan ini akan diluluskan.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, luluskan',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                confirmButtonColor: '#16a34a',
                cancelButtonColor: '#6b7280'
            }).then((result) => {
                if (result.isConfirmed) {
                    approveForm.submit();
                }
            });
        });
    }

    const rejectBtn = document.getElementById('rejectBtn');
    const rejectForm = document.getElementById('rejectForm');
    const rejectNote = document.getElementById('catatan_permohonan');

    if (rejectBtn && rejectForm) {
        rejectBtn.addEventListener('click', function () {
            if (!rejectNote || !rejectNote.value.trim()) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Catatan diperlukan',
                    text: 'Sila isi sebab atau catatan penolakan terlebih dahulu.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#7c3aed'
                });
                return;
            }

            Swal.fire({
                title: 'Adakah anda pasti?',
                text: 'Permohonan ini akan ditolak.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, tolak',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280'
            }).then((result) => {
                if (result.isConfirmed) {
                    rejectForm.submit();
                }
            });
        });
    }

});
</script>

@endsection