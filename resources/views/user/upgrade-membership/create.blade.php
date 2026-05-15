@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <h1 class="page-title fw-semibold fs-20 mb-1">Naik Taraf Ahli Utama</h1>
            <p class="text-muted mb-0">
                Akaun tanggungan anda tidak lagi layak dan perlu memohon sebagai ahli utama.
            </p>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Sila semak maklumat berikut:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="alert alert-warning">
        <strong>Makluman:</strong>
        Anda tidak lagi layak menjadi tanggungan kerana status perkahwinan telah dikemaskini sebagai berkahwin.
        Sila lengkapkan permohonan untuk menjadi ahli utama.
    </div>

    <div class="card custom-card">
        <div class="card-header">
            <h5 class="mb-0">Maklumat Permohonan Ahli Utama</h5>
        </div>

        <div class="card-body">
            <form action="{{ route('user.upgrade-membership.store') }}" method="POST">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nama</label>
                        <input type="text" class="form-control" value="{{ $dependent->name }}" readonly>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">No. KP</label>
                        <input type="text" class="form-control" value="{{ $dependent->no_kp }}" readonly>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">No. Telefon</label>
                        <input type="text" name="no_tel" class="form-control"
                               value="{{ old('no_tel', $dependent->no_tel) }}"
                               placeholder="Contoh: 0123456789" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Jantina</label>
                        <select name="jantina" class="form-select" required>
                            <option value="">-- Pilih Jantina --</option>
                            <option value="lelaki" {{ old('jantina') == 'lelaki' ? 'selected' : '' }}>Lelaki</option>
                            <option value="perempuan" {{ old('jantina') == 'perempuan' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Tarikh Lahir</label>
                        <input type="date" name="tarikh_lahir" class="form-control"
                               value="{{ old('tarikh_lahir') }}">
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-semibold">Alamat Terkini</label>
                        <textarea name="alamat" class="form-control" rows="4" required>{{ old('alamat') }}</textarea>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <button type="submit" class="btn btn-info">
                        Hantar Permohonan Naik Taraf
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection