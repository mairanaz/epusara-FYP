@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <h1 class="page-title fw-semibold fs-20 mb-1">Borang Permohonan Keahlian</h1>
            <p class="text-muted mb-0">Sila lengkapkan maklumat dengan tepat mengikut syarat keahlian khairat.</p>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <div class="fw-semibold mb-2">Sila semak maklumat berikut:</div>
            <ul class="mb-0">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="alert alert-info border-0 shadow-sm">
        <div class="fw-semibold mb-2">Ringkasan Syarat Keahlian</div>
        <ul class="mb-0 ps-3">
            <li>Pemohon mestilah beragama Islam.</li>
            <li>Pemohon mestilah tinggal dalam kariah Masjid RTB Bukit Changgang.</li>
            <li>Keahlian tertakluk kepada semakan dan kelulusan pentadbiran.</li>
            <li>Keahlian hanya sah selepas syarat bayaran dan proses pentadbiran dipenuhi.</li>
        </ul>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card border-0 shadow-sm">
                <div class="card-body p-0">
                    <form action="{{ route('user.profile.store') }}" method="POST">
                        @csrf

                        <div class="p-4">
                            <div class="row g-3">

                                <div class="col-12">
                                    <h5 class="fw-semibold mb-3">A. Maklumat Peribadi</h5>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Nama Penuh</label>
                                    <input type="text" name="nama"
                                           class="form-control @error('nama') is-invalid @enderror"
                                           value="{{ old('nama') }}"
                                           placeholder="Sila isikan nama penuh">
                                    @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">No. MyKad</label>
                                    <input type="text" name="no_kp"
                                           class="form-control @error('no_kp') is-invalid @enderror"
                                           value="{{ old('no_kp') }}"
                                           placeholder="Contoh: 010203040506">
                                    @error('no_kp') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    <small class="text-muted">Masukkan 12 digit tanpa sengkang.</small>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Tarikh Lahir</label>
                                    <input type="date" name="tarikh_lahir"
                                           class="form-control @error('tarikh_lahir') is-invalid @enderror"
                                           value="{{ old('tarikh_lahir') }}">
                                    @error('tarikh_lahir') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Agama</label>
                                    <select name="agama" class="form-select @error('agama') is-invalid @enderror">
                                        <option value="">-- Sila Pilih --</option>
                                        <option value="Islam" {{ old('agama') == 'Islam' ? 'selected' : '' }}>Islam</option>
                                    </select>
                                    @error('agama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Warganegara</label>
                                    <select name="warganegara" class="form-select @error('warganegara') is-invalid @enderror">
                                        <option value="">-- Sila Pilih --</option>
                                        <option value="Malaysia" {{ old('warganegara') == 'Malaysia' ? 'selected' : '' }}>Malaysia</option>
                                        <option value="Penduduk Tetap" {{ old('warganegara') == 'Penduduk Tetap' ? 'selected' : '' }}>Penduduk Tetap</option>
                                    </select>
                                    @error('warganegara') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-12">
                                    <h5 class="fw-semibold mt-2 mb-3">B. Maklumat Perhubungan</h5>
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label">Alamat Rumah</label>
                                    <textarea name="alamat_rumah" rows="3"
                                              class="form-control @error('alamat_rumah') is-invalid @enderror"
                                              placeholder="Sila isikan alamat rumah">{{ old('alamat_rumah') }}</textarea>
                                    @error('alamat_rumah') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">No. Tel Rumah</label>
                                    <input type="text" name="no_tel_rumah"
                                           class="form-control @error('no_tel_rumah') is-invalid @enderror"
                                           value="{{ old('no_tel_rumah') }}"
                                           placeholder="Contoh: 03-12345678">
                                    @error('no_tel_rumah') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">No. Telefon Bimbit</label>
                                    <input type="text" name="no_tel_bimbit"
                                           class="form-control @error('no_tel_bimbit') is-invalid @enderror"
                                           value="{{ old('no_tel_bimbit') }}"
                                           placeholder="Contoh: 0123456789">
                                    @error('no_tel_bimbit') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-12">
                                    <h5 class="fw-semibold mt-2 mb-3">C. Maklumat Kariah</h5>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Tinggal Dalam Kariah?</label>
                                    <select name="tinggal_dalam_kariah" class="form-select @error('tinggal_dalam_kariah') is-invalid @enderror">
                                        <option value="">-- Sila Pilih --</option>
                                        <option value="1" {{ old('tinggal_dalam_kariah') == '1' ? 'selected' : '' }}>Ya</option>
                                    </select>
                                    @error('tinggal_dalam_kariah') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    <small class="text-muted">Permohonan hanya dibuka untuk pemastautin dalam kariah.</small>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Tempoh Menetap</label>
                                    <input type="text" name="tempoh_menetap"
                                           class="form-control @error('tempoh_menetap') is-invalid @enderror"
                                           value="{{ old('tempoh_menetap') }}"
                                           placeholder="Contoh: 5 tahun">
                                    @error('tempoh_menetap') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-12">
                                    <h5 class="fw-semibold mt-2 mb-3">D. Maklumat Pekerjaan</h5>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Pekerjaan</label>
                                    <input type="text" name="pekerjaan"
                                           class="form-control @error('pekerjaan') is-invalid @enderror"
                                           value="{{ old('pekerjaan') }}"
                                           placeholder="Contoh: Swasta / Kerajaan / Pelajar">
                                    @error('pekerjaan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Nama Majikan</label>
                                    <input type="text" name="nama_majikan"
                                           class="form-control @error('nama_majikan') is-invalid @enderror"
                                           value="{{ old('nama_majikan') }}"
                                           placeholder="Sila isikan nama majikan">
                                    @error('nama_majikan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Alamat Kerja</label>
                                    <input type="text" name="alamat_kerja"
                                           class="form-control @error('alamat_kerja') is-invalid @enderror"
                                           value="{{ old('alamat_kerja') }}"
                                           placeholder="Sila isikan alamat tempat kerja">
                                    @error('alamat_kerja') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-12">
                                    <h5 class="fw-semibold mt-2 mb-3">E. Maklumat Waris</h5>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Nama Waris</label>
                                    <input type="text" name="nama_waris"
                                           class="form-control @error('nama_waris') is-invalid @enderror"
                                           value="{{ old('nama_waris') }}"
                                           placeholder="Sila isikan nama waris">
                                    @error('nama_waris') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Hubungan Waris</label>
                                    <select name="hubungan_waris" class="form-select @error('hubungan_waris') is-invalid @enderror">
                                        <option value="">-- Sila Pilih --</option>
                                        <option value="Suami" {{ old('hubungan_waris') == 'Suami' ? 'selected' : '' }}>Suami</option>
                                        <option value="Isteri" {{ old('hubungan_waris') == 'Isteri' ? 'selected' : '' }}>Isteri</option>
                                        <option value="Anak" {{ old('hubungan_waris') == 'Anak' ? 'selected' : '' }}>Anak</option>
                                        <option value="Ibu" {{ old('hubungan_waris') == 'Ibu' ? 'selected' : '' }}>Ibu</option>
                                        <option value="Bapa" {{ old('hubungan_waris') == 'Bapa' ? 'selected' : '' }}>Bapa</option>
                                        <option value="Ibu Mertua" {{ old('hubungan_waris') == 'Ibu Mertua' ? 'selected' : '' }}>Ibu Mertua</option>
                                        <option value="Bapa Mertua" {{ old('hubungan_waris') == 'Bapa Mertua' ? 'selected' : '' }}>Bapa Mertua</option>
                                    </select>
                                    @error('hubungan_waris') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">No. Tel Waris</label>
                                    <input type="text" name="no_tel_waris"
                                           class="form-control @error('no_tel_waris') is-invalid @enderror"
                                           value="{{ old('no_tel_waris') }}"
                                           placeholder="Contoh: 0123456789">
                                    @error('no_tel_waris') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Alamat Waris</label>
                                    <input type="text" name="alamat_waris"
                                           class="form-control @error('alamat_waris') is-invalid @enderror"
                                           value="{{ old('alamat_waris') }}"
                                           placeholder="Sila isikan alamat waris">
                                    @error('alamat_waris') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-12">
                                    <h5 class="fw-semibold mt-2 mb-3">F. Pelan Pembayaran</h5>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Pilih Pelan Pembayaran</label>
                                    <select name="payment_plan" class="form-select @error('payment_plan') is-invalid @enderror">
                                        <option value="">-- Sila Pilih --</option>
                                        <option value="bulanan" {{ old('payment_plan') == 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                                        <option value="tahunan" {{ old('payment_plan') == 'tahunan' ? 'selected' : '' }}>Tahunan</option>
                                    </select>
                                    @error('payment_plan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <div class="alert alert-light border mb-0">
                                        <div class="fw-semibold mb-2">Maklumat Pelan</div>
                                        <ul class="mb-0 ps-3">
                                            <li><b>Bulanan</b>: Bayaran pertama RM30, kemudian RM10 setiap bulan.</li>
                                            <li><b>Tahunan</b>: Bayaran pertama RM120, kemudian tiada bayaran lain untuk tahun semasa.</li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-check mt-2">
                                        <input class="form-check-input @error('akuan') is-invalid @enderror" type="checkbox" name="akuan" value="1" id="akuan" {{ old('akuan') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="akuan">
                                            Saya mengaku bahawa semua maklumat yang diberikan adalah benar dan saya memahami syarat keahlian.
                                        </label>
                                        @error('akuan') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="px-4 py-3 border-top d-sm-flex justify-content-end">
                            <a href="{{ route('user.dashboard') }}" class="btn btn-light m-1">Batal</a>
                            <button class="btn btn-success m-1" type="submit">
                                Simpan Maklumat
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection