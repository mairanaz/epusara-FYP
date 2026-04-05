@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <h1 class="page-title fw-semibold fs-18 mb-0">Borang Tanggungan</h1>
    </div>

    <div class="card custom-card">
        <div class="card-body">

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
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

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Nama tanggungan</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>No. KP</label>
                        <input type="text" name="no_kp" class="form-control" value="{{ old('no_kp') }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Pasangan</label>
                        <select name="pasangan" id="pasangan" class="form-control" required>
                            <option value="">-- Pilih --</option>
                            <option value="ya" {{ old('pasangan') == 'ya' ? 'selected' : '' }}>Ya</option>
                            <option value="tidak" {{ old('pasangan') == 'tidak' ? 'selected' : '' }}>Tidak</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Pertalian</label>
                        <select name="pertalian" id="pertalian" class="form-control" required>
                            <option value="">-- Pilih --</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>No. Tel</label>
                        <input type="text" name="no_tel" class="form-control" value="{{ old('no_tel') }}">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Tambah</button>
                <a href="{{ route('user.dependents.index') }}" class="btn btn-secondary">Kembali</a>
            </form>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const pasanganSelect = document.getElementById('pasangan');
    const pertalianSelect = document.getElementById('pertalian');

    const spouseRelation = @json($spouseRelation);
    const oldPertalian = @json(old('pertalian'));

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

    pasanganSelect.addEventListener('change', renderPertalianOptions);
    renderPertalianOptions();
});
</script>
@endsection