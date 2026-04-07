@extends('layouts.app')

@section('title', 'Maklumat Ahli Utama')

@section('content')
<div class="container-fluid">

    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <h1 class="page-title fw-semibold fs-18 mb-1">Maklumat Ahli Utama</h1>
            <p class="text-muted mb-0">Maklumat ahli utama yang berkaitan dengan akaun tanggungan anda</p>
        </div>
    </div>

    <div class="card custom-card">
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

</div>
@endsection