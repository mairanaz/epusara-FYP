@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <h1 class="page-title fw-semibold fs-20 mb-1">Permohonan Keahlian</h1>
            <p class="text-muted mb-0">Lengkapkan maklumat waris.</p>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger border-0 shadow-sm">
            <div class="fw-semibold mb-2">Sila semak maklumat berikut:</div>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card custom-card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-success rounded-pill px-3 py-2">Step 3</span>
                    <span class="fw-semibold">Maklumat Waris</span>
                </div>
                <small class="text-muted">3 / 4</small>
            </div>

            <div class="progress mt-3" style="height: 8px;">
                <div class="progress-bar bg-success" style="width: 75%"></div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-xl-9">
            <div class="card custom-card border-0 shadow-sm">
                <div class="card-body p-0">
                    <form action="{{ route('user.profile.post.step3') }}" method="POST">
                        @csrf

                        <div class="p-4">
                            <div class="mb-4">
                                <h4 class="fw-semibold mb-1">Maklumat Waris</h4>
                                <p class="text-muted mb-0">Maklumat waris penting untuk urusan khairat dan kecemasan.</p>
                            </div>

                            <div class="row g-3">
                                <div class="col-12">
                                    <h5 class="fw-semibold mb-3">Maklumat Waris</h5>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Nama Waris</label>
                                    <input type="text" name="nama_waris"
                                           class="form-control @error('nama_waris') is-invalid @enderror"
                                           value="{{ old('nama_waris', session('user_profile.step3.nama_waris')) }}"
                                           placeholder="Sila isikan nama waris">
                                    @error('nama_waris') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Hubungan Waris</label>
                                    <select name="hubungan_waris" class="form-select @error('hubungan_waris') is-invalid @enderror">
                                        <option value="">-- Sila Pilih --</option>
                                        @php
                                            $hubunganWaris = old('hubungan_waris', session('user_profile.step3.hubungan_waris'));
                                        @endphp
                                        <option value="Suami" {{ $hubunganWaris == 'Suami' ? 'selected' : '' }}>Suami</option>
                                        <option value="Isteri" {{ $hubunganWaris == 'Isteri' ? 'selected' : '' }}>Isteri</option>
                                        <option value="Anak" {{ $hubunganWaris == 'Anak' ? 'selected' : '' }}>Anak</option>
                                        <option value="Ibu" {{ $hubunganWaris == 'Ibu' ? 'selected' : '' }}>Ibu</option>
                                        <option value="Bapa" {{ $hubunganWaris == 'Bapa' ? 'selected' : '' }}>Bapa</option>
                                        <option value="Ibu Mertua" {{ $hubunganWaris == 'Ibu Mertua' ? 'selected' : '' }}>Ibu Mertua</option>
                                        <option value="Bapa Mertua" {{ $hubunganWaris == 'Bapa Mertua' ? 'selected' : '' }}>Bapa Mertua</option>
                                    </select>
                                    @error('hubungan_waris') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">No. Tel Waris</label>
                                    <input type="text" name="no_tel_waris"
                                           class="form-control @error('no_tel_waris') is-invalid @enderror"
                                           value="{{ old('no_tel_waris', session('user_profile.step3.no_tel_waris')) }}"
                                           placeholder="Contoh: 0123456789">
                                    @error('no_tel_waris') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Alamat Waris</label>
                                    <input type="text" name="alamat_waris"
                                           class="form-control @error('alamat_waris') is-invalid @enderror"
                                           value="{{ old('alamat_waris', session('user_profile.step3.alamat_waris')) }}"
                                           placeholder="Sila isikan alamat waris">
                                    @error('alamat_waris') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="px-4 py-3 border-top d-flex justify-content-between">
                            <a href="{{ route('user.profile.create.step2') }}" class="btn btn-light">
                                Kembali
                            </a>
                            <button type="submit" class="btn btn-success">
                                Seterusnya
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection