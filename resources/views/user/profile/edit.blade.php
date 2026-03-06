@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h5>Kemaskini Maklumat Diri</h5>

    <form method="POST" action="{{ route('user.profile.update') }}">
        @csrf
        @method('PUT')

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Nama</label>
                <input class="form-control" name="nama" value="{{ old('nama', $profile->nama) }}">
            </div>

            <div class="col-md-6">
                <label class="form-label">No. KP</label>
                <input class="form-control" name="no_kp" value="{{ old('no_kp', $profile->no_kp) }}">
            </div>

            <div class="col-md-6">
                <label class="form-label">Email</label>
                <input class="form-control" name="email" value="{{ old('email', $profile->email) }}">
            </div>

            <div class="col-md-6">
                <label class="form-label">Tarikh</label>
                <input type="date" class="form-control" name="tarikh" value="{{ old('tarikh', $profile->tarikh) }}">
            </div>

            <div class="col-md-6">
                <label class="form-label">Alamat Rumah</label>
                <input class="form-control" name="alamat_rumah" value="{{ old('alamat_rumah', $profile->alamat_rumah) }}">
            </div>

            <div class="col-md-3">
                <label class="form-label">No. Tel Rumah</label>
                <input class="form-control" name="no_tel_rumah" value="{{ old('no_tel_rumah', $profile->no_tel_rumah) }}">
            </div>

            <div class="col-md-3">
                <label class="form-label">No. Tel</label>
                <input class="form-control" name="no_tel" value="{{ old('no_tel', $profile->no_tel) }}">
            </div>

            <div class="col-md-6">
                <label class="form-label">Pekerjaan</label>
                <input class="form-control" name="pekerjaan" value="{{ old('pekerjaan', $profile->pekerjaan) }}">
            </div>

            <div class="col-md-6">
                <label class="form-label">Alamat Kerja</label>
                <input class="form-control" name="alamat_kerja" value="{{ old('alamat_kerja', $profile->alamat_kerja) }}">
            </div>
        </div>

        <div class="mt-3">
            <button class="btn btn-primary">Update</button>
            <a class="btn btn-light" href="{{ route('user.profile.show') }}">Batal</a>
        </div>
    </form>
</div>
@endsection