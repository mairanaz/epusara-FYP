@extends('layouts.app')

@section('content')
<div class="container-fluid">

    @php
        $isDependent = auth()->user()->account_type === 'tanggungan';

        $status = $profile->status_permohonan ?? null;

        $statusClass = match($status) {
            'pending' => 'warning',
            'approved' => 'info',
            'rejected' => 'danger',
            'active' => 'success',
            default => 'secondary',
        };

        $statusLabel = match($status) {
            'pending' => 'Menunggu Semakan',
            'approved' => 'Diluluskan',
            'rejected' => 'Ditolak',
            'active' => 'Aktif',
            default => 'Draf',
        };
    @endphp

    <style>
        .formal-card {
            border: 1px solid #dee2e6;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            background: #fff;
        }

        .formal-header {
            background: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            font-weight: 700;
            color: #212529;
            padding: 14px 18px;
        }

        .formal-body {
            padding: 18px;
        }

        .summary-box {
            border: 1px solid #dee2e6;
            border-radius: 10px;
            background: #ffffff;
            padding: 20px;
            margin-bottom: 1.5rem;
        }

        .summary-name {
            font-weight: 700;
            color: #212529;
            margin-bottom: 4px;
        }

        .form-label {
            font-weight: 600;
            color: #212529;
            margin-bottom: 6px;
        }

        .form-control,
        .form-select {
            border-radius: 8px;
        }

        .section-spacing {
            margin-bottom: 1.5rem;
        }

        .info-note {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 14px 16px;
            font-size: 14px;
            color: #495057;
        }

        .bottom-action-wrap {
            border-top: 1px solid #dee2e6;
            padding-top: 1rem;
            margin-top: 1.5rem;
        }

        .page-top-actions {
            display: flex;
            justify-content: flex-end;
            align-items: center;
        }

    .page-back-btn {
        background: #ffffff;
        border: 1px solid #dbe3f0;
        color: #334155;
        border-radius: 12px;
        padding: 10px 18px;
        font-weight: 600;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.05);
        transition: all 0.2s ease;
    }

    .page-back-btn:hover {
        background: #f8fbff;
        border-color: #cbd5e1;
        color: #0f172a;
    }

    .bottom-action-wrap {
        border-top: 1px solid #dee2e6;
        padding-top: 1rem;
        margin-top: 1.5rem;
        padding-bottom: 2.5rem;
    }

    .form-action-buttons {
        margin-top: 1.25rem;
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        flex-wrap: wrap;
    }

    .form-action-buttons .btn {
        min-width: 150px;
        height: 46px;
        border-radius: 12px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .form-action-buttons .btn-light {
        background: #ffffff;
        border: 1px solid #dbe3f0;
        color: #334155;
    }

    .form-action-buttons .btn-light:hover {
        background: #f8fbff;
        border-color: #cbd5e1;
        color: #0f172a;
    }

    .form-action-buttons .btn-info {
        background: #4db3eb;
        border-color: #4db3eb;
        color: #fff;
        box-shadow: 0 8px 18px rgba(77, 179, 235, 0.22);
    }

    .form-action-buttons .btn-info:hover {
        background: #38a7e3;
        border-color: #38a7e3;
        color: #fff;
    }

    @media (max-width: 576px) {
        .page-top-actions {
            justify-content: flex-start;
        }

        .page-back-btn {
            width: 100%;
            justify-content: center;
            display: inline-flex;
            align-items: center;
        }

        .form-action-buttons {
            flex-direction: column;
        }

        .form-action-buttons .btn {
            width: 100%;
            min-width: unset;
        }

        .form-check-input {
            border-color: #b6dff5;
            box-shadow: none;
        }

        .form-check-input:checked {
            background-color: #0dcaf0 !important;
            border-color: #0dcaf0 !important;
        }

        .form-check-input:focus {
            border-color: #0dcaf0 !important;
            box-shadow: 0 0 0 0.15rem rgba(13, 202, 240, 0.2) !important;
        }

    }

    </style>

    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <h1 class="page-title fw-semibold fs-20 mb-1">
                {{ $isDependent ? 'Kemaskini Maklumat Tanggungan' : 'Kemaskini Maklumat Ahli' }}
            </h1>
            <p class="text-muted mb-0">Sila kemaskini maklumat anda dengan tepat sebelum disimpan.</p>
        </div>

        <div class="btn-list mt-3 mt-md-0">
            <a href="{{ route('user.profile.show') }}" class="btn page-back-btn">
                <i class="bx bx-arrow-back me-1"></i> Kembali
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

        <div class="summary-box">
            <div class="row align-items-center g-3">
                <div class="col-md-8">
                    <div class="summary-name fs-4">{{ $profile->nama }}</div>
                    <div class="text-muted">{{ $profile->no_kp }}</div>
                </div>

                <div class="col-md-4 text-md-end">
                    <small class="text-muted d-block">Status Semasa</small>
                    <span class="badge bg-{{ $statusClass }} px-3 py-2">
                        {{ $statusLabel }}
                    </span>
                </div>
            </div>
        </div>

        <div class="row g-4">

            <div class="col-12">
                <div class="formal-card">
                    <div class="formal-header">A. Maklumat Peribadi</div>
                    <div class="formal-body">
                        <div class="row g-3">

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
                                       id="no_kp"
                                       name="no_kp"
                                       maxlength="12"
                                       class="form-control @error('no_kp') is-invalid @enderror"
                                       value="{{ old('no_kp', $profile->no_kp) }}"
                                       placeholder="Contoh: 010203040506">
                                @error('no_kp') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <small class="text-muted">Masukkan 12 digit tanpa sengkang.</small>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Tarikh Lahir</label>
                                <input type="date"
                                       id="tarikh_lahir"
                                       name="tarikh_lahir"
                                       class="form-control @error('tarikh_lahir') is-invalid @enderror"
                                       value="{{ old('tarikh_lahir', optional($profile->tarikh_lahir)->format('Y-m-d')) }}">
                                @error('tarikh_lahir') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Jantina</label>
                                <select id="jantina" name="jantina" class="form-select @error('jantina') is-invalid @enderror">
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
                <div class="formal-card h-100">
                    <div class="formal-header">B. Maklumat Perhubungan</div>
                    <div class="formal-body">
                        <div class="row g-3">

                            <div class="col-12">
                                <label class="form-label">Alamat Rumah</label>
                                <textarea name="alamat_rumah"
                                          rows="4"
                                          class="form-control @error('alamat_rumah') is-invalid @enderror"
                                          placeholder="Sila isikan alamat rumah">{{ old('alamat_rumah', $profile->alamat_rumah) }}</textarea>
                                @error('alamat_rumah') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">No. Tel Rumah</label>
                                <input type="text"
                                       name="no_tel_rumah"
                                       class="form-control @error('no_tel_rumah') is-invalid @enderror"
                                       value="{{ old('no_tel_rumah', $profile->no_tel_rumah) }}"
                                       placeholder="Contoh: 03-12345678">
                                @error('no_tel_rumah') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">No. Telefon Bimbit</label>
                                <input type="text"
                                       name="no_tel_bimbit"
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
                <div class="formal-card h-100">
                    <div class="formal-header">C. Maklumat Kariah</div>
                    <div class="formal-body">
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
                                <input type="text"
                                       name="tempoh_menetap"
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
                <div class="formal-card h-100">
                    <div class="formal-header">D. Maklumat Pekerjaan</div>
                    <div class="formal-body">
                        <div class="row g-3">

                            <div class="col-12">
                                <label class="form-label">Pekerjaan</label>
                                <input type="text"
                                       name="pekerjaan"
                                       class="form-control @error('pekerjaan') is-invalid @enderror"
                                       value="{{ old('pekerjaan', $profile->pekerjaan) }}"
                                       placeholder="Contoh: Swasta / Kerajaan / Pelajar">
                                @error('pekerjaan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">Nama Majikan</label>
                                <input type="text"
                                       name="nama_majikan"
                                       class="form-control @error('nama_majikan') is-invalid @enderror"
                                       value="{{ old('nama_majikan', $profile->nama_majikan) }}"
                                       placeholder="Sila isikan nama majikan">
                                @error('nama_majikan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">Alamat Kerja</label>
                                <input type="text"
                                       name="alamat_kerja"
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
                    <div class="formal-card h-100">
                        <div class="formal-header">E. Maklumat Waris</div>
                        <div class="formal-body">
                            <div class="row g-3">

                                <div class="col-12">
                                    <label class="form-label">Nama Waris</label>
                                    <input type="text"
                                           name="nama_waris"
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
                                    <input type="text"
                                           name="no_tel_waris"
                                           class="form-control @error('no_tel_waris') is-invalid @enderror"
                                           value="{{ old('no_tel_waris', $profile->no_tel_waris) }}"
                                           placeholder="Contoh: 0123456789">
                                    @error('no_tel_waris') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Alamat Waris</label>
                                    <input type="text"
                                           name="alamat_waris"
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
                    <div class="formal-card">
                        <div class="formal-header">F. Maklumat Bayaran</div>
                        <div class="formal-body">
                            <div class="row g-4 align-items-start">

                                <div class="col-md-6">
                                    <label class="form-label">Kaedah Bayaran</label>

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
                                            Kaedah bayaran tidak boleh diubah kerana anda sudah mempunyai bayaran berstatus paid.
                                        </small>
                                    @else
                                        <small class="text-muted d-block mt-2">
                                            Kaedah bayaran boleh diubah selagi belum ada bayaran berstatus paid.
                                        </small>
                                    @endif
                                </div>

                                <div class="col-md-6">
                                    <div class="info-note">
                                        <div class="fw-semibold mb-2">Maklumat Bayaran</div>
                                        <div class="mb-2"><b>Bulanan:</b> RM20 yuran pendaftaran + RM10 bulan pertama. Bayaran seterusnya RM10 setiap bulan.</div>
                                        <div><b>Tahunan:</b> RM20 yuran pendaftaran + RM100 yuran tahunan.</div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            @endunless

        </div>

        <div class="bottom-action-wrap">
            <div class="form-check mt-2">
                <input class="form-check-input @error('akuan') is-invalid @enderror"
                       type="checkbox"
                       name="akuan"
                       value="1"
                       id="akuan"
                       {{ old('akuan', 1) ? 'checked' : '' }}>
                <label class="form-check-label" for="akuan">
                    Saya mengesahkan bahawa semua maklumat yang dikemaskini adalah benar.
                </label>
                @error('akuan') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <div class="form-action-buttons">
                <a class="btn btn-light" href="{{ route('user.profile.show') }}">
                    Batal
                </a>
                <button class="btn btn-info" type="submit">
                    Simpan Kemaskini
                </button>
            </div>
        </div>
    </form>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const icInput = document.getElementById('no_kp');
    const birthDateInput = document.getElementById('tarikh_lahir');
    const genderSelect = document.getElementById('jantina');

    function parseBirthDateFromIC(ic) {
        if (!/^\d{12}$/.test(ic)) {
            return null;
        }

        const yy = parseInt(ic.substring(0, 2), 10);
        const mm = parseInt(ic.substring(2, 4), 10);
        const dd = parseInt(ic.substring(4, 6), 10);

        if (mm < 1 || mm > 12 || dd < 1 || dd > 31) {
            return null;
        }

        const currentYear = new Date().getFullYear();
        const currentYearShort = currentYear % 100;
        const fullYear = yy <= currentYearShort ? 2000 + yy : 1900 + yy;

        const formatted = `${fullYear}-${String(mm).padStart(2, '0')}-${String(dd).padStart(2, '0')}`;
        const testDate = new Date(formatted);

        if (isNaN(testDate.getTime())) {
            return null;
        }

        if (
            testDate.getFullYear() !== fullYear ||
            (testDate.getMonth() + 1) !== mm ||
            testDate.getDate() !== dd
        ) {
            return null;
        }

        return formatted;
    }

    function parseGenderFromIC(ic) {
        if (!/^\d{12}$/.test(ic)) {
            return '';
        }

        const lastDigit = parseInt(ic.charAt(11), 10);
        return lastDigit % 2 === 0 ? 'perempuan' : 'lelaki';
    }

    function handleICInput() {
        let ic = icInput.value.replace(/\D/g, '');
        icInput.value = ic;

        if (ic.length === 12) {
            const birthDate = parseBirthDateFromIC(ic);
            const gender = parseGenderFromIC(ic);

            if (birthDate) {
                birthDateInput.value = birthDate;
            }

            if (gender) {
                genderSelect.value = gender;
            }
        }
    }

    if (icInput) {
        icInput.addEventListener('input', handleICInput);
        handleICInput();
    }
});
</script>
@endsection