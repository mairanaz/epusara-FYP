@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <h1 class="page-title fw-semibold fs-18 mb-0">Edit Tanggungan</h1>
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

            <form action="{{ route('user.dependents.update', $dependent->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Nama tanggungan</label>
                        <input type="text" name="name" class="form-control"
                               value="{{ old('name', $dependent->name) }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>No. KP</label>
                        <input type="text" name="no_kp" class="form-control"
                               value="{{ old('no_kp', $dependent->no_kp) }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Pasangan</label>
                        <select name="pasangan" class="form-control" required>
                            <option value="">-- Pilih --</option>
                            <option value="ya" {{ old('pasangan', $dependent->pasangan) == 'ya' ? 'selected' : '' }}>Ya</option>
                            <option value="tidak" {{ old('pasangan', $dependent->pasangan) == 'tidak' ? 'selected' : '' }}>Tidak</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Pertalian</label>
                        <select name="pertalian" class="form-control" required>
                            <option value="">-- Pilih --</option>
                            @foreach(['suami','isteri','anak','bapa kandung','ibu kandung','bapa mertua','ibu mertua'] as $item)
                                <option value="{{ $item }}"
                                    {{ old('pertalian', $dependent->pertalian) == $item ? 'selected' : '' }}>
                                    {{ ucfirst($item) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>No. Tel</label>
                        <input type="text" name="no_tel" class="form-control"
                               value="{{ old('no_tel', $dependent->no_tel) }}">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Kemaskini</button>
                <a href="{{ route('user.dependents.index') }}" class="btn btn-secondary">Kembali</a>
            </form>

        </div>
    </div>
</div>
@endsection