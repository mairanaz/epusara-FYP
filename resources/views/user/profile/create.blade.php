@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <!-- Page Header -->
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <h1 class="page-title fw-semibold fs-18 mb-0">Borang Keahlian / Maklumat Diri</h1>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('user.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Maklumat Diri</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- Page Header Close -->

    <!-- Errors -->
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Start::row -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-body add-products p-0">
                    <form method="POST" action="{{ route('user.profile.store') }}">
                        @csrf

                        <div class="p-4">
                            <div class="row gx-5">

                                <!-- LEFT -->
                                <div class="col-xxl-6 col-xl-12 col-lg-12 col-md-6">
                                    <div class="card custom-card shadow-none mb-0 border-0">
                                        <div class="card-body p-0">
                                            <div class="row gy-3">

                                                <div class="col-xl-12">
                                                    <label class="form-label">Nama</label>
                                                    <input type="text"
                                                           class="form-control @error('nama') is-invalid @enderror"
                                                           name="nama"
                                                           value="{{ old('nama') }}"
                                                           placeholder="Sila isikan nama penuh anda">
                                                    @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                </div>

                                                <div class="col-xl-12">
                                                    <label class="form-label">No. Kad Pengenalan</label>
                                                    <input type="text"
                                                           class="form-control @error('no_kp') is-invalid @enderror"
                                                           name="no_kp"
                                                           value="{{ old('no_kp') }}"
                                                           placeholder="Sila isikan no kad pengenalan anda">
                                                    @error('no_kp') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                </div>

                                                <div class="col-xl-12">
                                                    <label class="form-label">Email</label>
                                                    <input type="email"
                                                           class="form-control @error('email') is-invalid @enderror"
                                                           name="email"
                                                           value="{{ old('email', auth()->user()->email) }}"
                                                           placeholder="contoh@email.com">
                                                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                </div>

                                                <div class="col-xl-6">
                                                    <label class="form-label">Tarikh</label>
                                                    <input type="date"
                                                           class="form-control @error('tarikh') is-invalid @enderror"
                                                           name="tarikh"
                                                           value="{{ old('tarikh') }}">
                                                    @error('tarikh') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                </div>

                                                <div class="col-xl-6">
                                                    <label class="form-label">No. Telefon</label>
                                                    <input type="text"
                                                           class="form-control @error('no_tel') is-invalid @enderror"
                                                           name="no_tel"
                                                           value="{{ old('no_tel') }}"
                                                           placeholder="cth: 0123456789">
                                                    @error('no_tel') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                </div>

                                                <div class="col-xl-12">
                                                    <label class="form-label">Alamat Rumah</label>
                                                    <input type="text"
                                                           class="form-control @error('alamat_rumah') is-invalid @enderror"
                                                           name="alamat_rumah"
                                                           value="{{ old('alamat_rumah') }}"
                                                           placeholder="Sila isikan alamat rumah">
                                                    @error('alamat_rumah') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                </div>

                                                <div class="col-xl-12">
                                                    <label class="form-label">No. Tel Rumah</label>
                                                    <input type="text"
                                                           class="form-control @error('no_tel_rumah') is-invalid @enderror"
                                                           name="no_tel_rumah"
                                                           value="{{ old('no_tel_rumah') }}"
                                                           placeholder="cth: 03-xxxx xxxx">
                                                    @error('no_tel_rumah') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- RIGHT -->
                                <div class="col-xxl-6 col-xl-12 col-lg-12 col-md-6">
                                    <div class="card custom-card shadow-none mb-0 border-0">
                                        <div class="card-body p-0">
                                            <div class="row gy-3">

                                                <div class="col-xl-12">
                                                    <label class="form-label">Pekerjaan</label>
                                                    <input type="text"
                                                           class="form-control @error('pekerjaan') is-invalid @enderror"
                                                           name="pekerjaan"
                                                           value="{{ old('pekerjaan') }}"
                                                           placeholder="cth: Pelajar / Swasta / Kerajaan">
                                                    @error('pekerjaan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                </div>

                                                <div class="col-xl-12">
                                                    <label class="form-label">Alamat Kerja</label>
                                                    <input type="text"
                                                           class="form-control @error('alamat_kerja') is-invalid @enderror"
                                                           name="alamat_kerja"
                                                           value="{{ old('alamat_kerja') }}"
                                                           placeholder="Sila isikan alamat tempat kerja">
                                                    @error('alamat_kerja') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                </div>

                                                <!-- Optional note box (kalau nak nampak penuh kolum kanan) -->
                                                <div class="col-xl-12">
                                                    <div class="alert alert-info mb-0">
                                                        <div class="fw-semibold mb-1">Nota</div>
                                                        Pastikan maklumat yang diisi adalah betul sebelum tekan <b>Simpan</b>.
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- Footer Buttons -->
                        <div class="px-4 py-3 border-top border-block-start-dashed d-sm-flex justify-content-end">
                            <a href="{{ route('user.dashboard') }}" class="btn btn-light m-1">Batal</a>
                            <button class="btn btn-success-light m-1" type="submit">
                                Simpan <i class="bi bi-download ms-2"></i>
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--End::row -->

</div>
@endsection