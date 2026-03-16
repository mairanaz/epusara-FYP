@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <h1 class="page-title fw-semibold fs-18 mb-0">Kemaskini Maklumat Ahli</h1>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('user.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('user.profile.show') }}">Maklumat Ahli</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Kemaskini</li>
                </ol>
            </nav>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-body p-0">
                    <form method="POST" action="{{ route('user.profile.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="p-4">
                            <div class="row g-4">

                                <div class="col-12">
                                    <h5 class="fw-semibold mb-3">A. Maklumat Ahli</h5>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Nama Penuh</label>
                                    <input type="text"
                                           name="nama"
                                           class="form-control @error('nama') is-invalid @enderror"
                                           value="{{ old('nama', $profile->nama) }}"
                                           placeholder="Sila isikan nama penuh">
                                    @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">No. MyKad</label>
                                    <input type="text"
                                           name="no_kp"
                                           class="form-control @error('no_kp') is-invalid @enderror"
                                           value="{{ old('no_kp', $profile->no_kp) }}"
                                           placeholder="Contoh: 010203040506">
                                    @error('no_kp') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Tarikh Lahir</label>
                                    <input type="date"
                                           name="tarikh_lahir"
                                           class="form-control @error('tarikh_lahir') is-invalid @enderror"
                                           value="{{ old('tarikh_lahir', $profile->tarikh_lahir ? \Carbon\Carbon::parse($profile->tarikh_lahir)->format('Y-m-d') : '') }}">
                                    @error('tarikh_lahir') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Agama</label>
                                    <input type="text"
                                           name="agama"
                                           class="form-control @error('agama') is-invalid @enderror"
                                           value="{{ old('agama', $profile->agama) }}">
                                    @error('agama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Warganegara</label>
                                    <input type="text"
                                           name="warganegara"
                                           class="form-control @error('warganegara') is-invalid @enderror"
                                           value="{{ old('warganegara', $profile->warganegara) }}">
                                    @error('warganegara') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-12">
                                    <h5 class="fw-semibold mt-2 mb-3">B. Maklumat Perhubungan</h5>
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label">Alamat Rumah</label>
                                    <textarea name="alamat_rumah"
                                              rows="3"
                                              class="form-control @error('alamat_rumah') is-invalid @enderror"
                                              placeholder="Sila isikan alamat rumah">{{ old('alamat_rumah', $profile->alamat_rumah) }}</textarea>
                                    @error('alamat_rumah') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">No. Tel Rumah</label>
                                    <input type="text"
                                           name="no_tel_rumah"
                                           class="form-control @error('no_tel_rumah') is-invalid @enderror"
                                           value="{{ old('no_tel_rumah', $profile->no_tel_rumah) }}"
                                           placeholder="Contoh: 03-12345678">
                                    @error('no_tel_rumah') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">No. Telefon Bimbit</label>
                                    <input type="text"
                                           name="no_tel_bimbit"
                                           class="form-control @error('no_tel_bimbit') is-invalid @enderror"
                                           value="{{ old('no_tel_bimbit', $profile->no_tel_bimbit) }}"
                                           placeholder="Contoh: 0123456789">
                                    @error('no_tel_bimbit') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-12">
                                    <h5 class="fw-semibold mt-2 mb-3">C. Maklumat Kariah</h5>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Tinggal Dalam Kariah?</label>
                                    <select name="tinggal_dalam_kariah"
                                            class="form-select @error('tinggal_dalam_kariah') is-invalid @enderror">
                                        <option value="">-- Sila Pilih --</option>
                                        <option value="1" {{ old('tinggal_dalam_kariah', $profile->tinggal_dalam_kariah) == 1 ? 'selected' : '' }}>Ya</option>
                                        <option value="0" {{ old('tinggal_dalam_kariah', $profile->tinggal_dalam_kariah) == 0 ? 'selected' : '' }}>Tidak</option>
                                    </select>
                                    @error('tinggal_dalam_kariah') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Tempoh Menetap</label>
                                    <input type="text"
                                           name="tempoh_menetap"
                                           class="form-control @error('tempoh_menetap') is-invalid @enderror"
                                           value="{{ old('tempoh_menetap', $profile->tempoh_menetap) }}"
                                           placeholder="Contoh: 5 tahun">
                                    @error('tempoh_menetap') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-12">
                                    <h5 class="fw-semibold mt-2 mb-3">D. Maklumat Pekerjaan</h5>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Pekerjaan</label>
                                    <input type="text"
                                           name="pekerjaan"
                                           class="form-control @error('pekerjaan') is-invalid @enderror"
                                           value="{{ old('pekerjaan', $profile->pekerjaan) }}"
                                           placeholder="Contoh: Swasta / Kerajaan / Pelajar">
                                    @error('pekerjaan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Nama Majikan</label>
                                    <input type="text"
                                           name="nama_majikan"
                                           class="form-control @error('nama_majikan') is-invalid @enderror"
                                           value="{{ old('nama_majikan', $profile->nama_majikan) }}"
                                           placeholder="Sila isikan nama majikan">
                                    @error('nama_majikan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Alamat Kerja</label>
                                    <input type="text"
                                           name="alamat_kerja"
                                           class="form-control @error('alamat_kerja') is-invalid @enderror"
                                           value="{{ old('alamat_kerja', $profile->alamat_kerja) }}"
                                           placeholder="Sila isikan alamat tempat kerja">
                                    @error('alamat_kerja') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-12">
                                    <h5 class="fw-semibold mt-2 mb-3">E. Maklumat Waris</h5>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Nama Waris</label>
                                    <input type="text"
                                           name="nama_waris"
                                           class="form-control @error('nama_waris') is-invalid @enderror"
                                           value="{{ old('nama_waris', $profile->nama_waris) }}"
                                           placeholder="Sila isikan nama waris">
                                    @error('nama_waris') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Hubungan Waris</label>
                                    <input type="text"
                                           name="hubungan_waris"
                                           class="form-control @error('hubungan_waris') is-invalid @enderror"
                                           value="{{ old('hubungan_waris', $profile->hubungan_waris) }}"
                                           placeholder="Contoh: Isteri / Suami / Anak">
                                    @error('hubungan_waris') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">No. Tel Waris</label>
                                    <input type="text"
                                           name="no_tel_waris"
                                           class="form-control @error('no_tel_waris') is-invalid @enderror"
                                           value="{{ old('no_tel_waris', $profile->no_tel_waris) }}"
                                           placeholder="Contoh: 0123456789">
                                    @error('no_tel_waris') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Alamat Waris</label>
                                    <input type="text"
                                           name="alamat_waris"
                                           class="form-control @error('alamat_waris') is-invalid @enderror"
                                           value="{{ old('alamat_waris', $profile->alamat_waris) }}"
                                           placeholder="Sila isikan alamat waris">
                                    @error('alamat_waris') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-12">
                                    <h5 class="fw-semibold mt-2 mb-3">F. Pelan Pembayaran</h5>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Pelan Pembayaran</label>

                                    @if($hasPaidPayments)
                                        <input type="hidden" name="payment_plan" value="{{ $profile->payment_plan }}">
                                    @endif

                                    <select name="payment_plan"
                                            class="form-select @error('payment_plan') is-invalid @enderror"
                                            {{ $hasPaidPayments ? 'disabled' : '' }}>
                                        <option value="">-- Sila Pilih --</option>
                                        <option value="bulanan" {{ old('payment_plan', $profile->payment_plan) == 'bulanan' ? 'selected' : '' }}>
                                            Bulanan
                                        </option>
                                        <option value="tahunan" {{ old('payment_plan', $profile->payment_plan) == 'tahunan' ? 'selected' : '' }}>
                                            Tahunan
                                        </option>
                                    </select>
                                    @error('payment_plan') <div class="invalid-feedback">{{ $message }}</div> @enderror

                                    @if($hasPaidPayments)
                                        <small class="text-danger">
                                            Pelan pembayaran tidak boleh diubah kerana anda sudah membuat bayaran.
                                        </small>
                                    @else
                                        <small class="text-muted">
                                            Anda boleh menukar pelan pembayaran selagi belum ada bayaran berstatus paid.
                                        </small>
                                    @endif
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

                            </div>
                        </div>

                        <div class="px-4 py-3 border-top border-block-start-dashed d-sm-flex justify-content-end">
                            <a class="btn btn-light m-1" href="{{ route('user.profile.show') }}">Batal</a>
                            <button class="btn btn-primary m-1" type="submit">Kemaskini</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection