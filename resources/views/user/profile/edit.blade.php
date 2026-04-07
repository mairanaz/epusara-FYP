@extends('layouts.app')

@section('content')
<div class="container-fluid">

    @php
        $isDependent = auth()->user()->account_type === 'tanggungan';
    @endphp

    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <h1 class="page-title fw-semibold fs-20 mb-1">
                {{ $isDependent ? 'Kemaskini Maklumat Tanggungan' : 'Kemaskini Maklumat Ahli' }}
            </h1>
            <p class="text-muted mb-0">Sila kemaskini maklumat anda dengan tepat sebelum disimpan.</p>
        </div>

        <div class="btn-list mt-3 mt-md-0">
            <a href="{{ route('user.profile.show') }}" class="btn btn-light">
                Kembali
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

    <form action="{{ route('user.profile.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row g-4">

            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                        <div>
                            <h4 class="mb-1 fw-bold">{{ $profile->nama }}</h4>
                            <div class="text-muted">{{ $profile->no_kp }}</div>
                        </div>

                        <div class="text-md-end">
                            <small class="text-muted d-block">Status Semasa</small>
                            <span class="badge bg-secondary px-3 py-2">
                                {{ ucfirst(str_replace('_', ' ', $profile->status_permohonan ?? 'draf')) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light fw-semibold">A. Maklumat Peribadi</div>
                    <div class="card-body">
                        <div class="row g-3">

                            <div class="col-md-6">
                                <label class="form-label">Nama Penuh</label>
                                <input type="text" name="nama"
                                       class="form-control @error('nama') is-invalid @enderror"
                                       value="{{ old('nama', $profile->nama) }}"
                                       placeholder="Sila isikan nama penuh">
                                @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">No. MyKad</label>
                                <input type="text" name="no_kp"
                                       class="form-control @error('no_kp') is-invalid @enderror"
                                       value="{{ old('no_kp', $profile->no_kp) }}"
                                       placeholder="Contoh: 010203040506">
                                @error('no_kp') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <small class="text-muted">Masukkan 12 digit tanpa sengkang.</small>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Tarikh Lahir</label>
                                <input type="date" name="tarikh_lahir"
                                       class="form-control @error('tarikh_lahir') is-invalid @enderror"
                                       value="{{ old('tarikh_lahir', optional($profile->tarikh_lahir)->format('Y-m-d')) }}">
                                @error('tarikh_lahir') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Jantina</label>
                                <select name="jantina" class="form-select @error('jantina') is-invalid @enderror">
                                    <option value="">-- Sila Pilih --</option>
                                    <option value="lelaki" {{ old('jantina', $profile->jantina) == 'lelaki' ? 'selected' : '' }}>Lelaki</option>
                                    <option value="perempuan" {{ old('jantina', $profile->jantina) == 'perempuan' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                                @error('jantina') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Agama</label>
                                <select name="agama" class="form-select @error('agama') is-invalid @enderror">
                                    <option value="">-- Sila Pilih --</option>
                                    <option value="Islam" {{ old('agama', $profile->agama) == 'Islam' ? 'selected' : '' }}>Islam</option>
                                </select>
                                @error('agama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Warganegara</label>
                                <select name="warganegara" class="form-select @error('warganegara') is-invalid @enderror">
                                    <option value="">-- Sila Pilih --</option>
                                    <option value="Malaysia" {{ old('warganegara', $profile->warganegara) == 'Malaysia' ? 'selected' : '' }}>Malaysia</option>
                                    <option value="Penduduk Tetap" {{ old('warganegara', $profile->warganegara) == 'Penduduk Tetap' ? 'selected' : '' }}>Penduduk Tetap</option>
                                </select>
                                @error('warganegara') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-light fw-semibold">B. Maklumat Perhubungan</div>
                    <div class="card-body">
                        <div class="row g-3">

                            <div class="col-12">
                                <label class="form-label">Alamat Rumah</label>
                                <textarea name="alamat_rumah" rows="4"
                                          class="form-control @error('alamat_rumah') is-invalid @enderror"
                                          placeholder="Sila isikan alamat rumah">{{ old('alamat_rumah', $profile->alamat_rumah) }}</textarea>
                                @error('alamat_rumah') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">No. Tel Rumah</label>
                                <input type="text" name="no_tel_rumah"
                                       class="form-control @error('no_tel_rumah') is-invalid @enderror"
                                       value="{{ old('no_tel_rumah', $profile->no_tel_rumah) }}"
                                       placeholder="Contoh: 03-12345678">
                                @error('no_tel_rumah') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">No. Telefon Bimbit</label>
                                <input type="text" name="no_tel_bimbit"
                                       class="form-control @error('no_tel_bimbit') is-invalid @enderror"
                                       value="{{ old('no_tel_bimbit', $profile->no_tel_bimbit) }}"
                                       placeholder="Contoh: 0123456789">
                                @error('no_tel_bimbit') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-light fw-semibold">C. Maklumat Kariah</div>
                    <div class="card-body">
                        <div class="row g-3">

                            <div class="col-12">
                                <label class="form-label">Tinggal Dalam Kariah?</label>
                                <select name="tinggal_dalam_kariah" class="form-select @error('tinggal_dalam_kariah') is-invalid @enderror">
                                    <option value="">-- Sila Pilih --</option>
                                    <option value="1" {{ old('tinggal_dalam_kariah', $profile->tinggal_dalam_kariah) == 1 ? 'selected' : '' }}>Ya</option>
                                </select>
                                @error('tinggal_dalam_kariah') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <small class="text-muted">Permohonan hanya dibuka untuk pemastautin dalam kariah.</small>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Tempoh Menetap</label>
                                <input type="text" name="tempoh_menetap"
                                       class="form-control @error('tempoh_menetap') is-invalid @enderror"
                                       value="{{ old('tempoh_menetap', $profile->tempoh_menetap) }}"
                                       placeholder="Contoh: 5 tahun">
                                @error('tempoh_menetap') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-light fw-semibold">D. Maklumat Pekerjaan</div>
                    <div class="card-body">
                        <div class="row g-3">

                            <div class="col-12">
                                <label class="form-label">Pekerjaan</label>
                                <input type="text" name="pekerjaan"
                                       class="form-control @error('pekerjaan') is-invalid @enderror"
                                       value="{{ old('pekerjaan', $profile->pekerjaan) }}"
                                       placeholder="Contoh: Swasta / Kerajaan / Pelajar">
                                @error('pekerjaan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">Nama Majikan</label>
                                <input type="text" name="nama_majikan"
                                       class="form-control @error('nama_majikan') is-invalid @enderror"
                                       value="{{ old('nama_majikan', $profile->nama_majikan) }}"
                                       placeholder="Sila isikan nama majikan">
                                @error('nama_majikan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">Alamat Kerja</label>
                                <input type="text" name="alamat_kerja"
                                       class="form-control @error('alamat_kerja') is-invalid @enderror"
                                       value="{{ old('alamat_kerja', $profile->alamat_kerja) }}"
                                       placeholder="Sila isikan alamat kerja">
                                @error('alamat_kerja') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            @unless($isDependent)
                <div class="col-xl-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-light fw-semibold">E. Maklumat Waris</div>
                        <div class="card-body">
                            <div class="row g-3">

                                <div class="col-12">
                                    <label class="form-label">Nama Waris</label>
                                    <input type="text" name="nama_waris"
                                           class="form-control @error('nama_waris') is-invalid @enderror"
                                           value="{{ old('nama_waris', $profile->nama_waris) }}"
                                           placeholder="Sila isikan nama waris">
                                    @error('nama_waris') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Hubungan Waris</label>
                                    <select name="hubungan_waris" class="form-select @error('hubungan_waris') is-invalid @enderror">
                                        <option value="">-- Sila Pilih --</option>
                                        <option value="Suami" {{ old('hubungan_waris', $profile->hubungan_waris) == 'Suami' ? 'selected' : '' }}>Suami</option>
                                        <option value="Isteri" {{ old('hubungan_waris', $profile->hubungan_waris) == 'Isteri' ? 'selected' : '' }}>Isteri</option>
                                        <option value="Anak" {{ old('hubungan_waris', $profile->hubungan_waris) == 'Anak' ? 'selected' : '' }}>Anak</option>
                                        <option value="Ibu" {{ old('hubungan_waris', $profile->hubungan_waris) == 'Ibu' ? 'selected' : '' }}>Ibu</option>
                                        <option value="Bapa" {{ old('hubungan_waris', $profile->hubungan_waris) == 'Bapa' ? 'selected' : '' }}>Bapa</option>
                                        <option value="Ibu Mertua" {{ old('hubungan_waris', $profile->hubungan_waris) == 'Ibu Mertua' ? 'selected' : '' }}>Ibu Mertua</option>
                                        <option value="Bapa Mertua" {{ old('hubungan_waris', $profile->hubungan_waris) == 'Bapa Mertua' ? 'selected' : '' }}>Bapa Mertua</option>
                                    </select>
                                    @error('hubungan_waris') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label">No. Tel Waris</label>
                                    <input type="text" name="no_tel_waris"
                                           class="form-control @error('no_tel_waris') is-invalid @enderror"
                                           value="{{ old('no_tel_waris', $profile->no_tel_waris) }}"
                                           placeholder="Contoh: 0123456789">
                                    @error('no_tel_waris') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Alamat Waris</label>
                                    <input type="text" name="alamat_waris"
                                           class="form-control @error('alamat_waris') is-invalid @enderror"
                                           value="{{ old('alamat_waris', $profile->alamat_waris) }}"
                                           placeholder="Sila isikan alamat waris">
                                    @error('alamat_waris') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-light fw-semibold">F. Pelan Pembayaran</div>
                        <div class="card-body">
                            <div class="row g-4 align-items-start">

                                <div class="col-md-6">
                                    <label class="form-label">Pelan Pembayaran</label>

                                    @if($hasPaidPayments)
                                        <input type="hidden" name="payment_plan" value="{{ $profile->payment_plan }}">
                                    @endif

                                    <select name="payment_plan"
                                            class="form-select @error('payment_plan') is-invalid @enderror"
                                            {{ $hasPaidPayments ? 'disabled' : '' }}>
                                        <option value="">-- Sila Pilih --</option>
                                        <option value="bulanan" {{ old('payment_plan', $profile->payment_plan) == 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                                        <option value="tahunan" {{ old('payment_plan', $profile->payment_plan) == 'tahunan' ? 'selected' : '' }}>Tahunan</option>
                                    </select>
                                    @error('payment_plan') <div class="invalid-feedback">{{ $message }}</div> @enderror

                                    @if($hasPaidPayments)
                                        <small class="text-danger d-block mt-2">
                                            Pelan pembayaran tidak boleh diubah kerana anda sudah mempunyai bayaran berstatus paid.
                                        </small>
                                    @else
                                        <small class="text-muted d-block mt-2">
                                            Anda boleh ubah pelan pembayaran selagi belum ada bayaran paid.
                                        </small>
                                    @endif
                                </div>

                                <div class="col-md-6">
                                    <div class="alert alert-light border mb-0 h-100">
                                        <div class="fw-semibold mb-2">Maklumat Pelan</div>
                                        <ul class="mb-0 ps-3">
                                            <li><b>Bulanan</b>: Bayaran pertama RM30, kemudian RM10 setiap bulan.</li>
                                            <li><b>Tahunan</b>: Bayaran pertama RM120, kemudian tiada bayaran lain untuk tahun semasa.</li>
                                        </ul>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            @endunless

        </div>

        <div class="mt-4">
            <div class="form-check mt-2">
                <input class="form-check-input @error('akuan') is-invalid @enderror"
                       type="checkbox"
                       name="akuan"
                       value="1"
                       id="akuan"
                       {{ old('akuan', 1) ? 'checked' : '' }}>
                <label class="form-check-label" for="akuan">
                    Saya mengesahkan bahawa maklumat yang dikemaskini adalah benar.
                </label>
                @error('akuan') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="mt-4 d-flex flex-column flex-sm-row justify-content-end gap-2">
            <a class="btn btn-light" href="{{ route('user.profile.show') }}">
                Batal
            </a>
            <button class="btn btn-primary" type="submit">
                Simpan Kemaskini
            </button>
        </div>
    </form>

</div>
@endsection