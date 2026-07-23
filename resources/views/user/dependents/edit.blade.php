@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <h1 class="page-title fw-semibold fs-18 mb-1">Kemaskini Tanggungan</h1>
            <p class="text-muted mb-0">Sila kemaskini maklumat tanggungan dengan lengkap dan tepat</p>
        </div>
        <div>
            <a href="{{ route('user.dependents.index') }}" class="btn btn-secondary-light">
                <i class="ri-arrow-left-line me-1"></i> Kembali
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Ralat!</strong> Sila semak semula maklumat yang diisi.
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @php
        $userGender = strtolower($gender ?? '');
        $spouseRelation = $userGender === 'lelaki' ? 'isteri' : ($userGender === 'perempuan' ? 'suami' : '');
    @endphp

    <div class="row">
        <div class="col-xl-4 col-lg-5">
            <div class="card custom-card overflow-hidden">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <span class="avatar avatar-xxl rounded-circle bg-info text-white d-flex align-items-center justify-content-center mx-auto fs-2">
                            {{ strtoupper(substr(old('name', $dependent->name), 0, 1)) }}
                        </span>
                    </div>

                    <h5 class="fw-semibold mb-1">{{ old('name', $dependent->name) }}</h5>
                    <p class="text-muted mb-3">{{ ucwords(old('pertalian', $dependent->pertalian)) }}</p>

                    @if(($dependent->status_kehidupan ?? 'aktif') === 'meninggal_dunia')
                        <span class="badge bg-danger fs-12 px-3 py-2">
                            <i class="ri-heart-pulse-fill me-1"></i> Meninggal Dunia
                        </span>
                    @else
                        <span class="badge bg-success fs-12 px-3 py-2">
                            <i class="ri-user-heart-line me-1"></i> Aktif
                        </span>
                    @endif
                </div>
            </div>

            <div class="card custom-card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Maklumat Semasa</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted d-block">Nama</small>
                        <strong>{{ $dependent->name }}</strong>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">No. KP</small>
                        <strong>{{ $dependent->no_kp }}</strong>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Pertalian</small>
                        <strong>{{ ucwords($dependent->pertalian) }}</strong>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Pasangan</small>
                        <strong>{{ ucfirst($dependent->pasangan) }}</strong>
                    </div>

                    <div class="mb-0">
                        <small class="text-muted d-block">No. Telefon</small>
                        <strong>{{ $dependent->no_tel ?? '-' }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-8 col-lg-7">
            <div class="card custom-card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Borang Kemaskini Tanggungan</h6>
                </div>

                <div class="card-body">
                    <form action="{{ route('user.dependents.update', $dependent->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row gy-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Tanggungan</label>
                                <input type="text" name="name" class="form-control"
                                       value="{{ old('name', $dependent->name) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">No. KP</label>
                                <input type="text" name="no_kp" class="form-control"
                                       value="{{ old('no_kp', $dependent->no_kp) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Pasangan</label>
                                <select name="pasangan" id="pasangan" class="form-select" required>
                                    <option value="">-- Pilih --</option>
                                    <option value="ya" {{ old('pasangan', $dependent->pasangan) == 'ya' ? 'selected' : '' }}>Ya</option>
                                    <option value="tidak" {{ old('pasangan', $dependent->pasangan) == 'tidak' ? 'selected' : '' }}>Tidak</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Pertalian</label>
                                <select name="pertalian" id="pertalian" class="form-select" required>
                                    <option value="">-- Pilih --</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Status Perkahwinan</label>
                                <select name="status_perkahwinan" id="status_perkahwinan" class="form-select @error('status_perkahwinan') is-invalid @enderror">
                                    <option value="">-- Pilih Status --</option>
                                    <option value="bujang" {{ old('status_perkahwinan', $dependent->status_perkahwinan) == 'bujang' ? 'selected' : '' }}>Bujang</option>
                                    <option value="berkahwin" {{ old('status_perkahwinan', $dependent->status_perkahwinan) == 'berkahwin' ? 'selected' : '' }}>Berkahwin</option>
                                </select>

                                @error('status_perkahwinan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                <small class="text-muted">
                                    Anak hanya layak sebagai tanggungan jika masih bujang.
                                </small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Tinggal Bersama Ahli Utama?</label>

                                <input type="text"
                                    class="form-control"
                                    value="Ya"
                                    readonly>

                                <input type="hidden"
                                    name="tinggal_bersama"
                                    id="tinggal_bersama"
                                    value="1">

                                @error('tinggal_bersama')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror

                                <small class="text-muted">
                                    Semua tanggungan yang didaftarkan mestilah tinggal bersama atau berada di bawah tanggungan ahli utama.
                                </small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">No. Telefon</label>
                                <input type="text" name="no_tel" class="form-control"
                                       value="{{ old('no_tel', $dependent->no_tel) }}">
                            </div>
                        </div>

                        <div class="mt-4 d-flex flex-wrap gap-2">
                            <button type="submit" class="btn btn-info">
                                <i class="ri-save-line me-1"></i> Simpan Kemaskini
                            </button>

                            <a href="{{ route('user.dependents.index') }}" class="btn btn-secondary-light">
                                <i class="ri-close-line me-1"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            @if(($dependent->status_kehidupan ?? 'aktif') === 'meninggal_dunia')
                <div class="card custom-card border-danger">
                    <div class="card-header">
                        <h6 class="card-title mb-0 text-danger">Maklumat Kematian</h6>
                    </div>
                    <div class="card-body">
                        <div class="row gy-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted">Status Kehidupan</label>
                                <div>
                                    <span class="badge bg-danger">Meninggal Dunia</span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-muted">Tarikh Kematian</label>
                                <div class="fw-semibold">
                                    {{ $dependent->tarikh_kematian ? \Carbon\Carbon::parse($dependent->tarikh_kematian)->format('d/m/Y') : '-' }}
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-warning mt-3 mb-0">
                            Rekod ini telah ditandakan sebagai meninggal dunia oleh pihak pentadbiran.
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const pasanganSelect = document.getElementById('pasangan');
    const pertalianSelect = document.getElementById('pertalian');
    const statusPerkahwinanSelect = document.getElementById('status_perkahwinan');
    const tinggalBersamaSelect = document.getElementById('tinggal_bersama');

    const spouseRelation = @json($spouseRelation);
    const selectedPertalian = @json(old('pertalian', $dependent->pertalian));
    const selectedStatusPerkahwinan = @json(old('status_perkahwinan', $dependent->status_perkahwinan));
    const selectedTinggalBersama = @json(old('tinggal_bersama', $dependent->tinggal_bersama));

    const nonSpouseRelations = [
        'anak',
        'bapa kandung',
        'ibu kandung',
        'bapa mertua',
        'ibu mertua'
    ];

    function formatLabel(text) {
        return text.charAt(0).toUpperCase() + text.slice(1);
    }

    function renderPertalianOptions() {
        const pasangan = pasanganSelect.value;
        pertalianSelect.innerHTML = '<option value="">-- Pilih --</option>';

        let options = [];

        if (pasangan === 'ya') {
            if (spouseRelation !== '') {
                options = [spouseRelation];
            }
        } else if (pasangan === 'tidak') {
            options = nonSpouseRelations;
        }

        options.forEach(function (item) {
            const option = document.createElement('option');
            option.value = item;
            option.textContent = formatLabel(item);

            if (selectedPertalian === item) {
                option.selected = true;
            }

            pertalianSelect.appendChild(option);
        });
    }

    function handleEligibilityFields() {
        const pertalian = pertalianSelect.value;

        statusPerkahwinanSelect.removeAttribute('required');
        tinggalBersamaSelect.removeAttribute('required');

        if (pertalian === 'suami' || pertalian === 'isteri') {
            statusPerkahwinanSelect.value = 'berkahwin';
            tinggalBersamaSelect.value = selectedTinggalBersama ?? '1';
        }

        if (pertalian === 'anak') {
            statusPerkahwinanSelect.setAttribute('required', true);

            if (selectedStatusPerkahwinan) {
                statusPerkahwinanSelect.value = selectedStatusPerkahwinan;
            }
        }

        if (
            pertalian === 'bapa kandung' ||
            pertalian === 'ibu kandung' ||
            pertalian === 'bapa mertua' ||
            pertalian === 'ibu mertua'
        ) {
            tinggalBersamaSelect.setAttribute('required', true);

            if (selectedTinggalBersama !== null && selectedTinggalBersama !== '') {
                tinggalBersamaSelect.value = selectedTinggalBersama;
            }
        }
    }

    pasanganSelect.addEventListener('change', function () {
        renderPertalianOptions();
        handleEligibilityFields();
    });

    pertalianSelect.addEventListener('change', handleEligibilityFields);

    renderPertalianOptions();
    handleEligibilityFields();
});
</script>
@endsection