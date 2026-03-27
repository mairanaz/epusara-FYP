@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <p class="fw-semibold fs-18 mb-0">
                Welcome admin, {{ auth()->user()->name }}!
            </p>
            <span class="fs-semibold text-muted">Ini dashboard admin.</span>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-body">
                    Content dashboard admin.
                </div>
            </div>
        </div>
    </div>
@endsection