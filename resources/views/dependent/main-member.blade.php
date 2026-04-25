@extends('layouts.app')

@section('title', 'Maklumat Keluarga')

@section('content')
<div class="container-fluid">

    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <h1 class="page-title fw-semibold fs-18 mb-1">Maklumat Keluarga</h1>
            <p class="text-muted mb-0">Maklumat ahli utama, ahli tanggungan lain dan rekod kematian keluarga anda</p>
        </div>
    </div>

    {{-- Ringkasan --}}
    @php
        $totalFamily = isset($familyMembers) ? $familyMembers->count() : 0;
        $totalAlive = isset($familyMembers) ? $familyMembers->where('status_kehidupan', '!=', 'meninggal_dunia')->count() : 0;
        $totalDeath = isset($familyMembers) ? $familyMembers->where('status_kehidupan', 'meninggal_dunia')->count() : 0;
    @endphp

    <div class="row mb-4">
        <div class="col-xl-4 col-md-4 col-sm-6">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="mb-1 text-muted">Jumlah Ahli Keluarga</p>
                            <h4 class="fw-semibold mb-0">{{ $totalFamily }}</h4>
                        </div>
                        <div class="avatar avatar-md bg-primary-transparent">
                            <i class="bx bx-group fs-20"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-4 col-sm-6">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="mb-1 text-muted">Masih Hidup</p>
                            <h4 class="fw-semibold mb-0">{{ $totalAlive }}</h4>
                        </div>
                        <div class="avatar avatar-md bg-success-transparent">
                            <i class="bx bx-check-circle fs-20"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-4 col-sm-6">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="mb-1 text-muted">Meninggal Dunia</p>
                            <h4 class="fw-semibold mb-0">{{ $totalDeath }}</h4>
                        </div>
                        <div class="avatar avatar-md bg-danger-transparent">
                            <i class="bx bx-heart-circle fs-20"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Maklumat Ahli Utama --}}
    <div class="card custom-card mb-4">
        <div class="card-header">
            <div class="card-title">Maklumat Ahli Utama</div>
        </div>
        <div class="card-body">

            <div class="alert alert-info">
                Anda ialah <strong>{{ ucwords($dependent->pertalian) }}</strong> kepada ahli utama ini.
            </div>

            @if($principalProfile)
                <div class="row gy-4">
                    <div class="col-md-6">
                        <label class="form-label text-muted">Nama Ahli Utama</label>
                        <div class="fw-semibold fs-14">{{ $principalProfile->nama }}</div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label text-muted">No. KP</label>
                        <div class="fw-semibold fs-14">{{ $principalProfile->no_kp }}</div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label text-muted">No. Telefon Bimbit</label>
                        <div class="fw-semibold fs-14">{{ $principalProfile->no_tel_bimbit ?? '-' }}</div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label text-muted">Alamat Rumah</label>
                        <div class="fw-semibold fs-14">{{ $principalProfile->alamat_rumah ?? '-' }}</div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label text-muted">Status Kehidupan</label>
                        <div>
                            @if(($principalProfile->status_kehidupan ?? 'aktif') === 'meninggal_dunia')
                                <span class="badge bg-danger">Meninggal Dunia</span>
                            @else
                                <span class="badge bg-success">Aktif</span>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label text-muted">Hubungan Anda</label>
                        <div class="fw-semibold fs-14">{{ ucwords($dependent->pertalian) }}</div>
                    </div>
                </div>
            @else
                <div class="alert alert-warning mb-0">
                    Maklumat ahli utama tidak dijumpai.
                </div>
            @endif

        </div>
    </div>

    {{-- Senarai Ahli Keluarga --}}
    <div class="card custom-card mb-4">
        <div class="card-header">
            <div class="card-title">Senarai Ahli Keluarga</div>
        </div>
        <div class="card-body">
            @if(isset($familyMembers) && $familyMembers->count())
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle text-nowrap mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="60">#</th>
                                <th>Nama</th>
                                <th>No. KP</th>
                                <th>Peranan</th>
                                <th>Pertalian</th>
                                <th>Status Kehidupan</th>
                                <th>Tarikh Kematian</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($familyMembers as $index => $member)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td class="fw-semibold">{{ $member['nama'] ?? '-' }}</td>
                                    <td>{{ $member['no_kp'] ?? '-' }}</td>
                                    <td>
                                        @if(($member['peranan'] ?? '') === 'ahli_utama')
                                            <span class="badge bg-primary">Ahli Utama</span>
                                        @else
                                            <span class="badge bg-secondary">Tanggungan</span>
                                        @endif
                                    </td>
                                    <td>{{ $member['pertalian'] ?? '-' }}</td>
                                    <td>
                                        @if(($member['status_kehidupan'] ?? 'aktif') === 'meninggal_dunia')
                                            <span class="badge bg-danger">Meninggal Dunia</span>
                                        @else
                                            <span class="badge bg-success">Aktif</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(!empty($member['tarikh_kematian']))
                                            {{ \Carbon\Carbon::parse($member['tarikh_kematian'])->format('d/m/Y') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-light mb-0">
                    Tiada maklumat ahli keluarga dijumpai.
                </div>
            @endif
        </div>
    </div>

    {{-- Rekod Kematian Keluarga --}}
    <div class="card custom-card">
        <div class="card-header">
            <div class="card-title">Rekod Kematian Keluarga</div>
        </div>
        <div class="card-body">
            @if(isset($familyDeathReports) && $familyDeathReports->count())
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle text-nowrap mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="60">#</th>
                                <th>Nama Si Mati</th>
                                <th>Hubungan</th>
                                <th>Tarikh Meninggal</th>
                                <th>Status Laporan</th>
                                <th>No. Lot Kubur</th>
                                <th>Tarikh Kebumi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($familyDeathReports as $index => $report)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td class="fw-semibold">{{ $report->nama_si_mati ?? '-' }}</td>
                                    <td>{{ $report->hubungan_keluarga ?? '-' }}</td>
                                    <td>
                                        @if(!empty($report->tarikh_meninggal))
                                            {{ \Carbon\Carbon::parse($report->tarikh_meninggal)->format('d/m/Y') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if(($report->status ?? '') === 'disahkan')
                                            <span class="badge bg-success">Disahkan</span>
                                        @elseif(($report->status ?? '') === 'ditolak')
                                            <span class="badge bg-danger">Ditolak</span>
                                        @elseif(($report->status ?? '') === 'perlukan_dokumen_tambahan')
                                            <span class="badge bg-warning text-dark">Dokumen Tambahan</span>
                                        @else
                                            <span class="badge bg-info">Dalam Semakan</span>
                                        @endif
                                    </td>
                                    <td>{{ $report->burial_lot_no ?? '-' }}</td>
                                    <td>
                                        @if(!empty($report->burial_date))
                                            {{ \Carbon\Carbon::parse($report->burial_date)->format('d/m/Y') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-light mb-0">
                    Tiada rekod kematian keluarga.
                </div>
            @endif
        </div>
    </div>

</div>
@endsection