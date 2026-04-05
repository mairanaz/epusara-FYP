@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <h1 class="page-title fw-semibold fs-18 mb-1">Senarai Tanggungan</h1>
            <p class="text-muted mb-0">Urus maklumat tanggungan ahli khairat dengan lebih teratur</p>
        </div>
        <div>
            <a href="{{ route('user.dependents.create') }}" class="btn btn-primary">
                <i class="ri-user-add-line me-1"></i> Tambah Tanggungan
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="ri-checkbox-circle-line me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-xl-3 col-md-6 col-sm-6">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="mb-1 text-muted">Jumlah Tanggungan</p>
                            <h4 class="fw-semibold mb-0">{{ $dependents->count() }}</h4>
                        </div>
                        <div class="avatar avatar-md bg-primary-transparent">
                            <i class="ri-team-line fs-18 text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 col-sm-6">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="mb-1 text-muted">Masih Aktif</p>
                            <h4 class="fw-semibold mb-0">
                                {{ $dependents->where('status_kehidupan', '!=', 'meninggal_dunia')->count() }}
                            </h4>
                        </div>
                        <div class="avatar avatar-md bg-success-transparent">
                            <i class="ri-user-heart-line fs-18 text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 col-sm-6">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="mb-1 text-muted">Meninggal Dunia</p>
                            <h4 class="fw-semibold mb-0">
                                {{ $dependents->where('status_kehidupan', 'meninggal_dunia')->count() }}
                            </h4>
                        </div>
                        <div class="avatar avatar-md bg-danger-transparent">
                            <i class="ri-heart-pulse-line fs-18 text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card custom-card">
        <div class="card-header justify-content-between">
            <div class="card-title">
                Rekod Tanggungan
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle text-nowrap">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Nama</th>
                            <th>No. KP</th>
                            <th>Pasangan</th>
                            <th>Pertalian</th>
                            <th>No. Tel</th>
                            <th>Status</th>
                            <th width="220">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dependents as $index => $dependent)
                            <tr>
                                <td>{{ $index + 1 }}</td>

                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="avatar avatar-sm rounded-circle bg-primary text-white d-flex align-items-center justify-content-center">
                                            {{ strtoupper(substr($dependent->name, 0, 1)) }}
                                        </span>
                                        <div>
                                            <div class="fw-semibold">{{ $dependent->name }}</div>
                                        </div>
                                    </div>
                                </td>

                                <td>{{ $dependent->no_kp }}</td>
                                <td>{{ ucfirst($dependent->pasangan) }}</td>
                                <td>{{ ucwords($dependent->pertalian) }}</td>
                                <td>{{ $dependent->no_tel ?? '-' }}</td>

                                <td>
                                    @if(($dependent->status_kehidupan ?? 'aktif') === 'meninggal_dunia')
                                        <span class="badge bg-danger">Meninggal Dunia</span>
                                        @if($dependent->tarikh_kematian)
                                            <div class="small text-muted mt-1">
                                                {{ \Carbon\Carbon::parse($dependent->tarikh_kematian)->format('d/m/Y') }}
                                            </div>
                                        @endif
                                    @else
                                        <span class="badge bg-success">Aktif</span>
                                    @endif
                                </td>

                                <td>
                                    <div class="d-flex flex-wrap gap-1">
                                        <a href="{{ route('user.dependents.show', $dependent->id) }}" class="btn btn-info-light btn-sm">
                                            <i class="ri-eye-line me-1"></i> Lihat
                                        </a>

                                        <a href="{{ route('user.dependents.edit', $dependent->id) }}" class="btn btn-warning-light btn-sm">
                                            <i class="ri-pencil-line me-1"></i> Edit
                                        </a>

                                        <form action="{{ route('user.dependents.destroy', $dependent->id) }}"
                                              method="POST"
                                              class="d-inline"
                                              onsubmit="return confirm('Adakah anda pasti mahu padam data ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger-light btn-sm">
                                                <i class="ri-delete-bin-line me-1"></i> Padam
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center">
                                        <div class="avatar avatar-xl bg-light text-muted mb-3">
                                            <i class="ri-team-line fs-2"></i>
                                        </div>
                                        <h6 class="mb-1">Tiada tanggungan direkodkan</h6>
                                        <p class="text-muted mb-3">Sila tambah tanggungan baharu untuk dipaparkan di sini.</p>
                                        <a href="{{ route('user.dependents.create') }}" class="btn btn-primary btn-sm">
                                            <i class="ri-user-add-line me-1"></i> Tambah Tanggungan
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection