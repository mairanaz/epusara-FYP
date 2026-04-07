@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <h1 class="page-title fw-semibold fs-20 mb-1">Permohonan Keahlian</h1>
            <p class="text-muted mb-0">Lengkapkan maklumat asas anda untuk memulakan permohonan.</p>
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
                    <span class="badge bg-success rounded-pill px-3 py-2">Step 1</span>
                    <span class="fw-semibold">Maklumat Asas</span>
                </div>
                <small class="text-muted">1 / 4</small>
            </div>

            <div class="progress mt-3" style="height: 8px;">
                <div class="progress-bar bg-success" style="width: 25%"></div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-xl-9">
            <div class="card custom-card border-0 shadow-sm">
                <div class="card-body p-0">
                    <form action="{{ route('user.profile.post.step1') }}" method="POST">
                        @csrf

                        <div class="p-4">
                            <div class="mb-4">
                                <h4 class="fw-semibold mb-1">Maklumat Asas</h4>
                                <p class="text-muted mb-0">Isi maklumat peribadi seperti dalam MyKad.</p>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nama Penuh</label>
                                    <input type="text" name="nama"
                                           class="form-control @error('nama') is-invalid @enderror"
                                           value="{{ old('nama', session('user_profile.step1.nama')) }}"
                                           placeholder="Sila isikan nama penuh">
                                    @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">No. MyKad</label>
                                    <input type="text" name="no_kp"
                                           class="form-control @error('no_kp') is-invalid @enderror"
                                           value="{{ old('no_kp', session('user_profile.step1.no_kp')) }}"
                                           placeholder="Contoh: 010203040506">
                                    @error('no_kp') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    <small class="text-muted">Masukkan 12 digit tanpa sengkang.</small>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Tarikh Lahir</label>
                                    <input type="date" name="tarikh_lahir"
                                           class="form-control @error('tarikh_lahir') is-invalid @enderror"
                                           value="{{ old('tarikh_lahir', session('user_profile.step1.tarikh_lahir')) }}">
                                    @error('tarikh_lahir') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Jantina</label>
                                    <select name="jantina" class="form-select @error('jantina') is-invalid @enderror">
                                        <option value="">-- Sila Pilih --</option>
                                        <option value="lelaki" {{ old('jantina', session('user_profile.step1.jantina')) == 'lelaki' ? 'selected' : '' }}>Lelaki</option>
                                        <option value="perempuan" {{ old('jantina', session('user_profile.step1.jantina')) == 'perempuan' ? 'selected' : '' }}>Perempuan</option>
                                    </select>
                                    @error('jantina') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Agama</label>
                                    <select name="agama" class="form-select @error('agama') is-invalid @enderror">
                                        <option value="">-- Sila Pilih --</option>
                                        <option value="Islam" {{ old('agama', session('user_profile.step1.agama')) == 'Islam' ? 'selected' : '' }}>Islam</option>
                                    </select>
                                    @error('agama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Warganegara</label>
                                    <select name="warganegara" class="form-select @error('warganegara') is-invalid @enderror">
                                        <option value="">-- Sila Pilih --</option>
                                        <option value="Malaysia" {{ old('warganegara', session('user_profile.step1.warganegara')) == 'Malaysia' ? 'selected' : '' }}>Malaysia</option>
                                        <option value="Penduduk Tetap" {{ old('warganegara', session('user_profile.step1.warganegara')) == 'Penduduk Tetap' ? 'selected' : '' }}>Penduduk Tetap</option>
                                    </select>
                                    @error('warganegara') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="px-4 py-3 border-top d-flex justify-content-between">
                            <a href="{{ route('user.dashboard') }}" class="btn btn-light">Batal</a>
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