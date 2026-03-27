@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <h1 class="page-title fw-semibold fs-20 mb-1">Senarai Permohonan Profil</h1>
            <p class="text-muted mb-0">Semak dan urus permohonan keahlian pengguna.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="5%">#</th>
                        <th>Nama</th>
                        <th>No. MyKad</th>
                        <th>Tarikh Permohonan</th>
                        <th>Pelan</th>
                        <th>Status</th>
                        <th width="12%">Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($profiles as $index => $profile)
                        <tr>
                            <td>{{ $profiles->firstItem() + $index }}</td>
                            <td>{{ $profile->nama }}</td>
                            <td>{{ $profile->no_kp }}</td>
                            <td>
                                {{ $profile->tarikh_permohonan ? \Carbon\Carbon::parse($profile->tarikh_permohonan)->format('d/m/Y') : '-' }}
                            </td>
                            <td>{{ ucfirst($profile->payment_plan ?? '-') }}</td>
                            <td>
                                @php
                                    $badgeClass = match($profile->status_permohonan) {
                                        'pending' => 'warning',
                                        'approved' => 'success',
                                        'rejected' => 'danger',
                                        'active' => 'primary',
                                        default => 'secondary',
                                    };
                                @endphp

                                @if($profile->status_permohonan)
                                    <span class="badge bg-{{ $badgeClass }}">
                                        {{ ucfirst(str_replace('_', ' ', $profile->status_permohonan)) }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary">Belum Dihantar</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.profile.show', $profile) }}" class="btn btn-sm btn-primary">
                                    Lihat
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                Tiada permohonan profil dijumpai.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if($profiles->hasPages())
                <div class="mt-3">
                    {{ $profiles->links() }}
                </div>
            @endif
        </div>
    </div>

</div>
@endsection