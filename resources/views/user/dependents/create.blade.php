@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <h1 class="page-title fw-semibold fs-20 mb-1">Borang Tanggungan</h1>
            <p class="text-muted mb-0">Sila lengkapkan maklumat tanggungan dengan tepat.</p>
        </div>
    </div>

    <div class="card custom-card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom">
            <h5 class="mb-0 fw-semibold">Maklumat Tanggungan</h5>
        </div>

        <div class="card-body p-4">

            @if ($errors->any())
                <div class="alert alert-danger border-0">
                    <div class="fw-semibold mb-2">Sila semak maklumat berikut:</div>
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @php
                $userGender = strtolower($gender ?? '');
                $spouseRelation = $userGender === 'lelaki' ? 'isteri' : ($userGender === 'perempuan' ? 'suami' : '');
            @endphp

            <form action="{{ route('user.dependents.store') }}" method="POST">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nama Tanggungan</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="Masukkan nama tanggungan" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">No. KP</label>
                        <input type="text" name="no_kp" class="form-control" value="{{ old('no_kp') }}" placeholder="Contoh: 010203101234" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Pasangan</label>
                        <select name="pasangan" id="pasangan" class="form-select" required>
                            <option value="">-- Pilih --</option>
                            <option value="ya" {{ old('pasangan') == 'ya' ? 'selected' : '' }}>Ya</option>
                            <option value="tidak" {{ old('pasangan') == 'tidak' ? 'selected' : '' }}>Tidak</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Pertalian</label>
                        <select name="pertalian" id="pertalian" class="form-select" required>
                            <option value="">-- Pilih --</option>
                        </select>
                    </div>

                    <div class="col-md-6" id="statusPerkahwinanWrapper">
                        <label class="form-label fw-semibold">Status Perkahwinan</label>
                        <select name="status_perkahwinan" id="status_perkahwinan" class="form-select @error('status_perkahwinan') is-invalid @enderror">
                            <option value="">-- Pilih Status --</option>
                            <option value="bujang" {{ old('status_perkahwinan') == 'bujang' ? 'selected' : '' }}>Bujang</option>
                            <option value="berkahwin" {{ old('status_perkahwinan') == 'berkahwin' ? 'selected' : '' }}>Berkahwin</option>
                        </select>

                        @error('status_perkahwinan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        <small class="text-muted">
                            Anak hanya layak sebagai tanggungan sekiranya masih bujang.
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
                        <label class="form-label fw-semibold">No. Tel</label>
                        <input type="text" name="no_tel" class="form-control" value="{{ old('no_tel') }}" placeholder="Masukkan nombor telefon">
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('user.dependents.index') }}" class="btn btn-light border">
                        Kembali
                    </a>
                    <button type="submit" class="btn btn-info">
                        Simpan Tanggungan
                    </button>
                </div>
            </form>

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
    const oldPertalian = @json(old('pertalian'));
    const oldStatusPerkahwinan = @json(old('status_perkahwinan'));
    const oldTinggalBersama = @json(old('tinggal_bersama'));

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

            if (oldPertalian === item) {
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
            tinggalBersamaSelect.value = '1';
        }

        if (pertalian === 'anak') {
            statusPerkahwinanSelect.setAttribute('required', true);

            if (!statusPerkahwinanSelect.value && oldStatusPerkahwinan) {
                statusPerkahwinanSelect.value = oldStatusPerkahwinan;
            }
        }

        if (
            pertalian === 'bapa kandung' ||
            pertalian === 'ibu kandung' ||
            pertalian === 'bapa mertua' ||
            pertalian === 'ibu mertua'
        ) {
            tinggalBersamaSelect.setAttribute('required', true);

            if (oldTinggalBersama !== null && oldTinggalBersama !== '') {
                tinggalBersamaSelect.value = oldTinggalBersama;
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