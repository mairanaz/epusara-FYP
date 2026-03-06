@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <h1 class="page-title fw-semibold fs-18 mb-0">Maklumat Tanggungan</h1>
    </div>

    <div class="card custom-card">
        <div class="card-body">
            <div class="mb-3"><strong>Nama:</strong> {{ $dependent->name }}</div>
            <div class="mb-3"><strong>No. KP:</strong> {{ $dependent->no_kp }}</div>
            <div class="mb-3"><strong>Pasangan:</strong> {{ ucfirst($dependent->pasangan) }}</div>
            <div class="mb-3"><strong>Pertalian:</strong> {{ ucfirst($dependent->pertalian) }}</div>
            <div class="mb-3"><strong>No. Tel:</strong> {{ $dependent->no_tel }}</div>

            <a href="{{ route('user.dependents.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </div>

</div>
@endsection