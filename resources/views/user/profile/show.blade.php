@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center">
        <h5>Maklumat Diri</h5>
        <a class="btn btn-outline-primary" href="{{ route('user.profile.edit') }}">Edit</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success mt-2">{{ session('success') }}</div>
    @endif

    <div class="card mt-3">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6"><b>Nama:</b> {{ $profile->nama }}</div>
                <div class="col-md-6"><b>No. KP:</b> {{ $profile->no_kp }}</div>
                <div class="col-md-6"><b>Email:</b> {{ $profile->email }}</div>
                <div class="col-md-6"><b>Tarikh:</b> {{ $profile->tarikh }}</div>

                <div class="col-md-6"><b>Alamat Rumah:</b> {{ $profile->alamat_rumah }}</div>
                <div class="col-md-3"><b>No. Tel Rumah:</b> {{ $profile->no_tel_rumah }}</div>
                <div class="col-md-3"><b>No. Tel:</b> {{ $profile->no_tel }}</div>

                <div class="col-md-6"><b>Pekerjaan:</b> {{ $profile->pekerjaan }}</div>
                <div class="col-md-6"><b>Alamat Kerja:</b> {{ $profile->alamat_kerja }}</div>
            </div>
        </div>
    </div>
</div>
@endsection