@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <style>
        .stepper-wrapper {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 0;
            position: relative;
            flex-wrap: nowrap;
            overflow-x: auto;
            padding: 8px 0 4px;
        }

        .stepper-item {
            position: relative;
            flex: 1;
            min-width: 160px;
            text-align: center;
        }

        .stepper-item:not(:last-child)::after {
            content: "";
            position: absolute;
            top: 24px;
            left: 50%;
            width: 100%;
            height: 4px;
            background: #dfe3e8;
            z-index: 1;
        }

        .stepper-item.completed:not(:last-child)::after,
        .stepper-item.active:not(:last-child)::after {
            background: #22c55e;
        }

        .stepper-circle {
            position: relative;
            z-index: 2;
            width: 48px;
            height: 48px;
            margin: 0 auto;
            border-radius: 50%;
            background: #f1f3f5;
            border: 3px solid #dfe3e8;
            color: #6c757d;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .stepper-title {
            margin-top: 12px;
            font-size: 14px;
            font-weight: 600;
            color: #6c757d;
            line-height: 1.35;
        }

        .stepper-subtitle {
            font-size: 12px;
            color: #9aa1a9;
            margin-top: 2px;
        }

        .stepper-item.active .stepper-circle {
            background: #22c55e;
            border-color: #22c55e;
            color: #fff;
            box-shadow: 0 0 0 6px rgba(34, 197, 94, 0.12);
        }

        .stepper-item.active .stepper-title {
            color: #198754;
        }

        .stepper-item.completed .stepper-circle {
            background: #198754;
            border-color: #198754;
            color: #fff;
        }

        .info-soft-card {
            background: #f8fafc;
            border: 1px solid #edf1f5;
            border-radius: 16px;
        }

        .form-section-card {
            border-radius: 18px;
            overflow: hidden;
        }

        @media (max-width: 768px) {
            .stepper-item {
                min-width: 120px;
            }

            .stepper-circle {
                width: 42px;
                height: 42px;
                font-size: 14px;
            }

            .stepper-item:not(:last-child)::after {
                top: 21px;
            }

            .stepper-title {
                font-size: 12px;
            }

            .stepper-subtitle {
                font-size: 11px;
            }
        }
    </style>

    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <h1 class="page-title fw-semibold fs-20 mb-1">Permohonan Keahlian</h1>
            <p class="text-muted mb-0">Lengkapkan maklumat asas anda untuk memulakan permohonan keahlian.</p>
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

    <div class="card custom-card border-0 shadow-sm mb-4 form-section-card">
        <div class="card-body py-4 px-4 px-md-5">
            <div class="stepper-wrapper">
                <div class="stepper-item active">
                    <div class="stepper-circle">1</div>
                    <div class="stepper-title">Maklumat Asas</div>
                    <div class="stepper-subtitle">Langkah 1</div>
                </div>

                <div class="stepper-item">
                    <div class="stepper-circle">2</div>
                    <div class="stepper-title">Maklumat Peribadi</div>
                    <div class="stepper-subtitle">Langkah 2</div>
                </div>

                <div class="stepper-item">
                    <div class="stepper-circle">3</div>
                    <div class="stepper-title">Maklumat Waris</div>
                    <div class="stepper-subtitle">Langkah 3</div>
                </div>

                <div class="stepper-item">
                    <div class="stepper-circle">4</div>
                    <div class="stepper-title">Bayaran Yuran</div>
                    <div class="stepper-subtitle">Langkah 4</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-xxl-10 col-xl-11">
            <div class="card custom-card border-0 shadow-sm form-section-card">
                <div class="card-body p-0">
                    <form action="{{ route('user.profile.post.step1') }}" method="POST">
                        @csrf

                        <div class="p-4 p-md-5">

                            <div class="text-center mb-4">
                                <h4 class="fw-semibold mb-2">Maklumat Diri Pemohon</h4>
                                <p class="text-muted mb-0">
                                    Sila isi maklumat peribadi seperti dalam MyKad. Tarikh lahir dan jantina akan dibantu secara automatik berdasarkan nombor MyKad yang dimasukkan.
                                </p>
                            </div>

                            <div class="info-soft-card shadow-sm mb-4">
                                <div class="card-body py-3 px-4">
                                    <div class="d-flex align-items-start gap-2">
                                        <i class="bx bx-info-circle fs-5 text-primary mt-1"></i>
                                        <div>
                                            <div class="fw-semibold mb-1">Makluman</div>
                                            <div class="text-muted small mb-0">
                                                Masukkan nombor MyKad tanpa sengkang. Sistem akan membantu mengisi tarikh lahir dan jantina secara automatik jika nombor MyKad lengkap.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Nama Penuh</label>
                                    <input type="text"
                                        name="nama"
                                        class="form-control form-control-lg @error('nama') is-invalid @enderror"
                                        value="{{ old('nama', session('user_profile.step1.nama', auth()->user()->name)) }}"
                                        placeholder="Sila isikan nama penuh seperti dalam MyKad"
                                        readonly>
                                    @error('nama')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">No. MyKad</label>
                                    <input type="text"
                                           id="no_kp"
                                           name="no_kp"
                                           maxlength="12"
                                           class="form-control form-control-lg @error('no_kp') is-invalid @enderror"
                                           value="{{ old('no_kp', session('user_profile.step1.no_kp')) }}"
                                           placeholder="Contoh: 030809160146">
                                    @error('no_kp')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Masukkan 12 digit tanpa sengkang.</small>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Tarikh Lahir</label>
                                    <input type="date"
                                           id="tarikh_lahir"
                                           name="tarikh_lahir"
                                           class="form-control form-control-lg @error('tarikh_lahir') is-invalid @enderror"
                                           value="{{ old('tarikh_lahir', session('user_profile.step1.tarikh_lahir')) }}">
                                    @error('tarikh_lahir')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Diisi secara automatik daripada No. MyKad.</small>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Jantina</label>
                                    <select id="jantina" name="jantina" class="form-select form-select-lg @error('jantina') is-invalid @enderror">
                                        <option value="">-- Sila Pilih --</option>
                                        <option value="lelaki" {{ old('jantina', session('user_profile.step1.jantina')) == 'lelaki' ? 'selected' : '' }}>
                                            Lelaki
                                        </option>
                                        <option value="perempuan" {{ old('jantina', session('user_profile.step1.jantina')) == 'perempuan' ? 'selected' : '' }}>
                                            Perempuan
                                        </option>
                                    </select>
                                    @error('jantina')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Boleh dikesan secara automatik daripada No. MyKad.</small>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Agama</label>
                                    <select name="agama" class="form-select form-select-lg @error('agama') is-invalid @enderror">
                                        <option value="">-- Sila Pilih --</option>
                                        <option value="Islam" {{ old('agama', session('user_profile.step1.agama')) == 'Islam' ? 'selected' : '' }}>
                                            Islam
                                        </option>
                                    </select>
                                    @error('agama')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Warganegara</label>
                                    <select name="warganegara" class="form-select form-select-lg @error('warganegara') is-invalid @enderror">
                                        <option value="">-- Sila Pilih --</option>
                                        <option value="Malaysia" {{ old('warganegara', session('user_profile.step1.warganegara')) == 'Malaysia' ? 'selected' : '' }}>
                                            Malaysia
                                        </option>
                                        <option value="Penduduk Tetap" {{ old('warganegara', session('user_profile.step1.warganegara')) == 'Penduduk Tetap' ? 'selected' : '' }}>
                                            Penduduk Tetap
                                        </option>
                                    </select>
                                    @error('warganegara')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                        </div>

                        <div class="px-4 px-md-5 py-3 border-top d-flex justify-content-between align-items-center">
                            <a href="{{ route('user.dashboard') }}" class="btn btn-light">
                                Batal
                            </a>
                            <button type="submit" class="btn btn-success px-4">
                                Seterusnya
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

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

        let fullYear = yy <= currentYearShort ? 2000 + yy : 1900 + yy;

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

    icInput.addEventListener('input', handleICInput);
    handleICInput();
});
</script>
@endsection