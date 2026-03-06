@extends('layouts.app')

@section('title', 'User Dashboard')

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <p class="fw-semibold fs-18 mb-0">
                Welcome back, {{ auth()->user()->name }}!
            </p>
            <span class="fs-semibold text-muted">Ini dashboard user.</span>
        </div>
    </div>

    {{-- Paste bahagian content dashboard dari template index.html (yang dalam container-fluid) --}}
    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-body">
                    Content dashboard 
                </div>
            </div>
        </div>
    </div>
@endsection