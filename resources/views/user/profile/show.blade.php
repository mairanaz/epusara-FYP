@extends('layouts.app')

@section('content')
<div class="container-fluid">

    @php
        $isDependent = auth()->user()->account_type === 'tanggungan';
        $status = $profile->status_permohonan ?? null;

        $statusClass = match($status) {
            'pending' => 'warning',
            'approved' => 'info',
            'rejected' => 'danger',
            'active' => 'success',
            default => 'secondary',
        };

        $statusLabel = match($status) {
            'pending' => 'Menunggu Semakan',
            'approved' => 'Diluluskan',
            'rejected' => 'Ditolak',
            'active' => 'Aktif',
            default => 'Belum Dihantar',
        };

        $paymentPlanLabel = '-';
        if ($profile->payment_plan === 'bulanan') {
            $paymentPlanLabel = 'Bulanan';
        } elseif ($profile->payment_plan === 'tahunan') {
            $paymentPlanLabel = 'Tahunan';
        }
    @endphp

    <style>
        .formal-card {
            border: 1px solid #dee2e6;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            background: #fff;
        }

        .formal-header {
            background: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            font-weight: 700;
            color: #212529;
            padding: 14px 18px;
        }

        .formal-body {
            padding: 18px;
        }

        .info-label {
            font-size: 13px;
            color: #6c757d;
            margin-bottom: 4px;
        }

        .info-value {
            font-size: 15px;
            font-weight: 600;
            color: #212529;
            word-break: break-word;
        }

        .info-row {
            padding: 10px 0;
            border-bottom: 1px solid #f1f3f5;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .profile-summary {
            border: 1px solid #dee2e6;
            border-radius: 10px;
            background: #ffffff;
            padding: 20px;
        }

        .profile-summary h3 {
            margin-bottom: 4px;
            font-weight: 700;
            color: #212529;
        }

        .section-spacing {
            margin-bottom: 1.5rem;
        }

        .status-box {
            border-radius: 10px;
            border: 1px solid #dee2e6;
            padding: 16px 18px;
            background: #fff;
        }

        .table-summary td {
            padding: 10px 12px;
            vertical-align: top;
        }

        .table-summary td:first-child {
            width: 220px;
            font-weight: 600;
            color: #495057;
            background: #f8f9fa;
        }
    </style>

    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <h1 class="page-title fw-semibold fs-20 mb-1">
                {{ $isDependent ? 'Maklumat Tanggungan' : 'Maklumat Ahli' }}
            </h1>
            <p class="text-muted mb-0">
                Paparan maklumat profil dan status permohonan keahlian.
            </p>
        </div>

        <div class="btn-list mt-3 mt-md-0">
            <a class="btn btn-info" href="{{ route('user.profile.edit') }}">
                Edit Maklumat
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="profile-summary section-spacing">
        <div class="row align-items-center g-3">
            <div class="col-md-8">
                <h3>{{ $profile->nama }}</h3>
                <div class="text-muted">{{ $profile->no_kp }}</div>
            </div>
            <div class="col-md-4 text-md-end">
                <div class="mb-2">
                    <small class="text-muted d-block">Status Permohonan</small>
                    <span class="badge bg-{{ $statusClass }} px-3 py-2">
                        {{ $statusLabel }}
                    </span>
                </div>
                <div>
                    <small class="text-muted d-block">Tarikh Permohonan</small>
                    <span class="fw-semibold">
                        {{ $profile->tarikh_permohonan ? \Carbon\Carbon::parse($profile->tarikh_permohonan)->format('d/m/Y') : '-' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    @if($status === 'pending')
        <div class="status-box section-spacing">
            <div class="fw-semibold mb-1">Permohonan Sedang Disemak</div>
            <div class="text-muted">
                Permohonan keahlian anda telah dihantar dan sedang menunggu semakan pihak pentadbiran.
                @unless($isDependent)
                    Bayaran yuran hanya boleh dibuat selepas permohonan diluluskan.
                @endunless
            </div>
        </div>
    @elseif($status === 'approved')
        @if($isDependent)
            <div class="status-box section-spacing">
                <div class="fw-semibold mb-1">Permohonan Diluluskan</div>
                <div class="text-muted">
                    Maklumat anda telah disahkan oleh pihak pentadbiran.
                </div>
            </div>
        @else
            <div class="status-box section-spacing d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <div class="fw-semibold mb-1">Permohonan Diluluskan</div>
                    <div class="text-muted">
                        Permohonan anda telah diluluskan. Sila teruskan dengan bayaran yuran untuk pengaktifan keahlian.
                    </div>
                </div>
                <a href="{{ route('user.payments.create') }}" class="btn btn-success">
                    Bayar Yuran
                </a>
            </div>
        @endif
    @elseif($status === 'rejected')
        <div class="status-box section-spacing">
            <div class="fw-semibold mb-1 text-danger">Permohonan Ditolak</div>
            <div class="text-muted">Permohonan anda telah ditolak oleh pihak pentadbiran.</div>
            @if($profile->catatan_permohonan)
                <div class="mt-2"><b>Catatan:</b> {{ $profile->catatan_permohonan }}</div>
            @endif
        </div>
    @elseif($status === 'active')
        <div class="status-box section-spacing">
            <div class="fw-semibold mb-1 text-success">Keahlian Aktif</div>
            <div class="text-muted">
                Akaun keahlian anda telah aktif dan disahkan.
            </div>
        </div>
    @endif

    <div class="row g-4">

        <div class="col-xl-6">
            <div class="formal-card h-100">
                <div class="formal-header">A. Maklumat Peribadi</div>
                <div class="formal-body">
                    <div class="info-row">
                        <div class="info-label">Nama</div>
                        <div class="info-value">{{ $profile->nama }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">No. MyKad</div>
                        <div class="info-value">{{ $profile->no_kp }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Tarikh Lahir</div>
                        <div class="info-value">{{ optional($profile->tarikh_lahir)->format('d/m/Y') ?? '-' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Jantina</div>
                        <div class="info-value">{{ ucfirst($profile->jantina ?? '-') }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Agama</div>
                        <div class="info-value">{{ $profile->agama ?? '-' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Warganegara</div>
                        <div class="info-value">{{ $profile->warganegara ?? '-' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="formal-card h-100">
                <div class="formal-header">B. Maklumat Perhubungan</div>
                <div class="formal-body">
                    <div class="info-row">
                        <div class="info-label">Alamat Rumah</div>
                        <div class="info-value">{{ $profile->alamat_rumah ?? '-' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">No. Tel Rumah</div>
                        <div class="info-value">{{ $profile->no_tel_rumah ?? '-' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">No. Telefon Bimbit</div>
                        <div class="info-value">{{ $profile->no_tel_bimbit ?? '-' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="formal-card h-100">
                <div class="formal-header">C. Maklumat Kariah</div>
                <div class="formal-body">
                    <div class="info-row">
                        <div class="info-label">Tinggal Dalam Kariah</div>
                        <div class="info-value">
                            <span class="badge bg-{{ $profile->tinggal_dalam_kariah ? 'success' : 'secondary' }}">
                                {{ $profile->tinggal_dalam_kariah ? 'Ya' : 'Tidak' }}
                            </span>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Tempoh Menetap</div>
                        <div class="info-value">{{ $profile->tempoh_menetap ?? '-' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="formal-card h-100">
                <div class="formal-header">D. Maklumat Pekerjaan</div>
                <div class="formal-body">
                    <div class="info-row">
                        <div class="info-label">Pekerjaan</div>
                        <div class="info-value">{{ $profile->pekerjaan ?? '-' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Nama Majikan</div>
                        <div class="info-value">{{ $profile->nama_majikan ?? '-' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Alamat Kerja</div>
                        <div class="info-value">{{ $profile->alamat_kerja ?? '-' }}</div>
                    </div>
                </div>
            </div>
        </div>

        @unless($isDependent)
        <div class="col-12">
            <div class="formal-card">
                <div class="formal-header">E. Maklumat Waris</div>
                <div class="formal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-summary mb-0">
                            <tbody>
                                <tr>
                                    <td>Nama Waris</td>
                                    <td>{{ $profile->nama_waris ?? '-' }}</td>
                                    <td>Hubungan Waris</td>
                                    <td>{{ $profile->hubungan_waris ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td>No. Tel Waris</td>
                                    <td>{{ $profile->no_tel_waris ?? '-' }}</td>
                                    <td>Alamat Waris</td>
                                    <td>{{ $profile->alamat_waris ?? '-' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endunless

        <div class="col-12">
            <div class="formal-card">
                <div class="formal-header">
                    {{ $isDependent ? 'E. Maklumat Permohonan' : 'F. Maklumat Permohonan' }}
                </div>
                <div class="formal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-summary mb-0">
                            <tbody>
                                <tr>
                                    <td>Tarikh Permohonan</td>
                                    <td>{{ $profile->tarikh_permohonan ? \Carbon\Carbon::parse($profile->tarikh_permohonan)->format('d/m/Y') : '-' }}</td>
                                    <td>Status Permohonan</td>
                                    <td>
                                        <span class="badge bg-{{ $statusClass }}">
                                            {{ $statusLabel }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Catatan</td>
                                    <td colspan="3">{{ $profile->catatan_permohonan ?? '-' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        @unless($isDependent)
        <div class="col-12">
            <div class="formal-card">
                <div class="formal-header">G. Maklumat Bayaran</div>
                <div class="formal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-summary mb-0">
                            <tbody>
                                <tr>
                                    <td>Kaedah Bayaran Dipilih</td>
                                    <td>{{ $paymentPlanLabel }}</td>
                                    <td>Yuran Pendaftaran</td>
                                    <td>RM20</td>
                                </tr>
                                <tr>
                                    <td>Jumlah Bayaran Permulaan</td>
                                    <td>
                                        @if($profile->payment_plan === 'tahunan')
                                            RM120
                                        @elseif($profile->payment_plan === 'bulanan')
                                            RM30
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>Keterangan</td>
                                    <td>
                                        @if($profile->payment_plan === 'tahunan')
                                            RM20 pendaftaran + RM100 tahunan
                                        @elseif($profile->payment_plan === 'bulanan')
                                            RM20 pendaftaran + RM10 bulan pertama, kemudian RM10 setiap bulan
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endunless

    </div>
</div>
@endsection