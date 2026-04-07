@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <h1 class="page-title fw-semibold fs-20 mb-1">Permohonan Keahlian</h1>
            <p class="text-muted mb-0">Lengkapkan maklumat perhubungan, kariah dan pekerjaan.</p>
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
                    <span class="badge bg-success rounded-pill px-3 py-2">Step 2</span>
                    <span class="fw-semibold">Maklumat Perhubungan & Pekerjaan</span>
                </div>
                <small class="text-muted">2 / 4</small>
            </div>

            <div class="progress mt-3" style="height: 8px;">
                <div class="progress-bar bg-success" style="width: 50%"></div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-xl-9">
            <div class="card custom-card border-0 shadow-sm">
                <div class="card-body p-0">
                    <form action="{{ route('user.profile.post.step2') }}" method="POST">
                        @csrf

                        <div class="p-4">
                            <div class="mb-4">
                                <h4 class="fw-semibold mb-1">Maklumat Perhubungan, Kariah & Pekerjaan</h4>
                                <p class="text-muted mb-0">Pastikan alamat, nombor telefon dan maklumat pekerjaan diisi dengan betul.</p>
                            </div>

                            <div class="row g-3">
                                <div class="col-12">
                                    <h5 class="fw-semibold mb-3">A. Maklumat Perhubungan & Kariah</h5>
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Alamat Rumah</label>
                                    <textarea name="alamat_rumah" rows="3"
                                              class="form-control @error('alamat_rumah') is-invalid @enderror"
                                              placeholder="Sila isikan alamat rumah">{{ old('alamat_rumah', session('user_profile.step2.alamat_rumah')) }}</textarea>
                                    @error('alamat_rumah') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">No. Tel Rumah</label>
                                    <input type="text" name="no_tel_rumah"
                                           class="form-control @error('no_tel_rumah') is-invalid @enderror"
                                           value="{{ old('no_tel_rumah', session('user_profile.step2.no_tel_rumah')) }}"
                                           placeholder="Contoh: 03-12345678">
                                    @error('no_tel_rumah') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">No. Telefon Bimbit</label>
                                    <input type="text" name="no_tel_bimbit"
                                           class="form-control @error('no_tel_bimbit') is-invalid @enderror"
                                           value="{{ old('no_tel_bimbit', session('user_profile.step2.no_tel_bimbit')) }}"
                                           placeholder="Contoh: 0123456789">
                                    @error('no_tel_bimbit') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Tinggal Dalam Kariah?</label>
                                    <select name="tinggal_dalam_kariah" class="form-select @error('tinggal_dalam_kariah') is-invalid @enderror">
                                        <option value="">-- Sila Pilih --</option>
                                        <option value="1" {{ old('tinggal_dalam_kariah', session('user_profile.step2.tinggal_dalam_kariah')) == '1' ? 'selected' : '' }}>Ya</option>
                                    </select>
                                    @error('tinggal_dalam_kariah') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    <small class="text-muted">Permohonan hanya dibuka untuk pemastautin dalam kariah.</small>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Tempoh Menetap</label>
                                    <input type="text" name="tempoh_menetap"
                                           class="form-control @error('tempoh_menetap') is-invalid @enderror"
                                           value="{{ old('tempoh_menetap', session('user_profile.step2.tempoh_menetap')) }}"
                                           placeholder="Contoh: 5 tahun">
                                    @error('tempoh_menetap') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-12 mt-4">
                                    <h5 class="fw-semibold mb-3">B. Maklumat Pekerjaan</h5>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Pekerjaan</label>
                                    <input type="text" name="pekerjaan"
                                           class="form-control @error('pekerjaan') is-invalid @enderror"
                                           value="{{ old('pekerjaan', session('user_profile.step2.pekerjaan')) }}"
                                           placeholder="Contoh: Swasta / Kerajaan / Pelajar">
                                    @error('pekerjaan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Nama Majikan</label>
                                    <input type="text" name="nama_majikan"
                                           class="form-control @error('nama_majikan') is-invalid @enderror"
                                           value="{{ old('nama_majikan', session('user_profile.step2.nama_majikan')) }}"
                                           placeholder="Sila isikan nama majikan">
                                    @error('nama_majikan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Alamat Kerja</label>
                                    <input type="text" name="alamat_kerja"
                                           class="form-control @error('alamat_kerja') is-invalid @enderror"
                                           value="{{ old('alamat_kerja', session('user_profile.step2.alamat_kerja')) }}"
                                           placeholder="Sila isikan alamat tempat kerja">
                                    @error('alamat_kerja') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="px-4 py-3 border-top d-flex justify-content-between">
                            <a href="{{ route('user.profile.create.step1') }}" class="btn btn-light">
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