@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <h1 class="page-title fw-semibold fs-18 mb-1">Butiran Tanggungan</h1>
            <p class="text-muted mb-0">Maklumat lengkap tanggungan ahli khairat</p>
        </div>
        <div>
            <a href="{{ route('user.dependents.index') }}" class="btn btn-secondary-light">
                <i class="ri-arrow-left-line me-1"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Ringkasan Tanggungan -->
        <div class="col-xl-4 col-lg-5 col-md-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <div class="avatar avatar-xxl mx-auto">
                            <span class="avatar avatar-xxl rounded-circle bg-primary text-white fs-2 d-flex align-items-center justify-content-center">
                                {{ strtoupper(substr($dependent->name, 0, 1)) }}
                            </span>
                        </div>
                    </div>

                    <h5 class="fw-semibold mb-1">{{ $dependent->name }}</h5>
                    <p class="text-muted mb-3">{{ ucwords($dependent->pertalian) }}</p>

                    @if(($dependent->status_kehidupan ?? 'aktif') === 'meninggal_dunia')
                        <span class="badge bg-danger fs-12 px-3 py-2">
                            <i class="ri-heart-pulse-fill me-1"></i> Telah Meninggal Dunia
                        </span>
                    @else
                        <span class="badge bg-success fs-12 px-3 py-2">
                            <i class="ri-user-heart-line me-1"></i> Aktif
                        </span>
                    @endif
                </div>
            </div>

            @if(($dependent->status_kehidupan ?? 'aktif') === 'meninggal_dunia')
                <div class="card custom-card border-danger">
                    <div class="card-header">
                        <h6 class="card-title mb-0 text-danger">Maklumat Kematian</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <small class="text-muted d-block">Status Kehidupan</small>
                            <strong class="text-danger">Meninggal Dunia</strong>
                        </div>

                        <div class="mb-0">
                            <small class="text-muted d-block">Tarikh Kematian</small>
                            <strong>
                                {{ $dependent->tarikh_kematian ? \Carbon\Carbon::parse($dependent->tarikh_kematian)->format('d/m/Y') : '-' }}
                            </strong>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Maklumat Lengkap -->
        <div class="col-xl-8 col-lg-7 col-md-12">
            <div class="card custom-card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Maklumat Peribadi Tanggungan</h6>
                </div>
                <div class="card-body">

                    @if(($dependent->status_kehidupan ?? 'aktif') === 'meninggal_dunia')
                        <div class="alert alert-danger">
                            <strong>Perhatian:</strong> Tanggungan ini telah direkodkan sebagai meninggal dunia.
                        </div>
                    @endif

                    <div class="row gy-4">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Nama</label>
                            <div class="fw-semibold fs-14">{{ $dependent->name }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-muted">No. KP</label>
                            <div class="fw-semibold fs-14">{{ $dependent->no_kp }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-muted">Pasangan</label>
                            <div class="fw-semibold fs-14">{{ ucfirst($dependent->pasangan) }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-muted">Pertalian</label>
                            <div class="fw-semibold fs-14">{{ ucwords($dependent->pertalian) }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-muted">Status Perkahwinan</label>
                            <div class="fw-semibold fs-14">
                                {{ $dependent->status_perkahwinan ? ucfirst($dependent->status_perkahwinan) : '-' }}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-muted">Tinggal Bersama Ahli Utama</label>
                            <div class="fw-semibold fs-14">
                                {{ $dependent->tinggal_bersama ? 'Ya' : 'Tidak' }}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-muted">Status Tanggungan</label>
                            <div>
                                @if(($dependent->status_tanggungan ?? 'aktif') === 'aktif')
                                    <span class="badge bg-success">Layak / Aktif</span>

                                @elseif($dependent->status_tanggungan === 'tidak_layak')
                                    <span class="badge bg-danger">Tidak Layak</span>
                                    <div class="small text-muted mt-1">
                                        {{ $dependent->sebab_tidak_layak ?? '-' }}
                                    </div>

                                    @if($dependent->tarikh_keluar_tanggungan)
                                        <div class="small text-muted">
                                            Tarikh keluar: {{ \Carbon\Carbon::parse($dependent->tarikh_keluar_tanggungan)->format('d/m/Y') }}
                                        </div>
                                    @endif

                                @elseif($dependent->status_tanggungan === 'meninggal')
                                    <span class="badge bg-dark">Meninggal Dunia</span>

                                @else
                                    <span class="badge bg-secondary">Tidak Diketahui</span>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-muted">No. Telefon</label>
                            <div class="fw-semibold fs-14">{{ $dependent->no_tel ?? '-' }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-muted">Status Kehidupan</label>
                            <div>
                                @if(($dependent->status_kehidupan ?? 'aktif') === 'meninggal_dunia')
                                    <span class="badge bg-danger">Meninggal Dunia</span>
                                @else
                                    <span class="badge bg-success">Aktif</span>
                                @endif
                            </div>
                        </div>

                        @if(($dependent->status_kehidupan ?? 'aktif') === 'meninggal_dunia')
                            <div class="col-md-6">
                                <label class="form-label text-muted">Tarikh Kematian</label>
                                <div class="fw-semibold fs-14">
                                    {{ $dependent->tarikh_kematian ? \Carbon\Carbon::parse($dependent->tarikh_kematian)->format('d/m/Y') : '-' }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="card-footer text-end">
                    <a href="{{ route('user.dependents.index') }}" class="btn btn-secondary-light">
                        <i class="ri-arrow-left-line me-1"></i> Kembali
                    </a>
                    <a href="{{ route('user.dependents.edit', $dependent->id) }}" class="btn btn-primary">
                        <i class="ri-pencil-line me-1"></i> Kemaskini
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection