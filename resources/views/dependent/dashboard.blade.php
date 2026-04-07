@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <h1 class="page-title fw-semibold fs-18 mb-1">Dashboard Tanggungan</h1>
            <p class="text-muted mb-0">Paparan khas untuk pengguna tanggungan</p>
        </div>
    </div>

    <div class="card custom-card">
        <div class="card-body">
            <div class="alert alert-success">
                Akaun anda telah dikenalpasti sebagai <strong>tanggungan</strong>.
            </div>

            <div class="mb-3">
                <strong>Nama Akaun:</strong> {{ auth()->user()->name }}
            </div>
            <div class="mb-3">
                <strong>Jenis Akaun:</strong> {{ auth()->user()->account_type }}
            </div>
            <div class="mb-3">
                <strong>Linked Dependent ID:</strong> {{ auth()->user()->linked_dependent_id }}
            </div>
        </div>
    </div>
</div>
@endsection