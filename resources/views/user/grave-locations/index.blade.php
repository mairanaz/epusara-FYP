@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <h1 class="page-title fw-semibold fs-22 mb-1">Lokasi Kubur Keluarga</h1>
            <p class="text-muted mb-0">
                Senarai lokasi kubur ahli keluarga yang telah meninggal dunia.
            </p>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="mb-0 fw-bold">Senarai Rekod Kubur</h5>
            <div class="text-muted small">
                Rekod hanya memaparkan ahli keluarga yang berkaitan dengan akaun anda.
            </div>
        </div>

        <div class="card-body p-0">
            @if($deathReports->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nama Si Mati</th>
                                <th>Tarikh Meninggal</th>
                                <th>Zon</th>
                                <th>Kod Lot</th>
                                <th>Gambar</th>
                                <th>Status</th>
                                <th class="text-end">Tindakan</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($deathReports as $report)
                                @php
                                    $plot = $report->final_burial_plot;
                                @endphp

                                <tr>
                                    <td>
                                        <div class="fw-bold">
                                            {{ $report->nama_si_mati }}
                                        </div>

                                        <div class="text-muted small">
                                            {{ $report->deceased_type === 'dependent' ? 'Tanggungan' : 'Ahli Utama' }}
                                        </div>
                                    </td>

                                    <td>
                                        {{ $report->tarikh_meninggal ? $report->tarikh_meninggal->format('d/m/Y') : '-' }}
                                    </td>

                                    <td>
                                        @if($plot)
                                            <span class="badge bg-light text-dark border">
                                                {{ $plot->zone_label }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>

                                    <td>
                                        @if($plot)
                                            <span class="badge bg-primary">
                                                {{ $plot->plot_code }}
                                            </span>
                                        @else
                                            <span class="badge bg-warning text-dark">
                                                Belum Ditetapkan
                                            </span>
                                        @endif
                                    </td>

                                    <td>
                                        @if($plot && $plot->grave_image)
                                            <span class="badge bg-success">
                                                Ada Gambar
                                            </span>
                                        @else
                                            <span class="badge bg-light text-dark border">
                                                Tiada Gambar
                                            </span>
                                        @endif
                                    </td>

                                    <td>
                                        @if($plot)
                                            <span class="badge bg-success">
                                                Lokasi Ditetapkan
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                Tiada Lokasi
                                            </span>
                                        @endif
                                    </td>

                                    <td class="text-end">
                                        @if($plot)
                                            <a href="{{ route('user.grave-locations.show', $report->id) }}"
                                               class="btn btn-sm btn-outline-primary rounded-pill">
                                                <i class="bx bx-map me-1"></i> Lihat Lokasi
                                            </a>
                                        @else
                                            <button class="btn btn-sm btn-light border rounded-pill" disabled>
                                                Belum Tersedia
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-3">
                    {{ $deathReports->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bx bx-map fs-1 text-muted"></i>

                    <h6 class="fw-bold mt-2 mb-1">
                        Tiada rekod lokasi kubur dijumpai
                    </h6>

                    <p class="text-muted mb-0">
                        Rekod akan dipaparkan selepas pentadbir menetapkan lot kubur.
                    </p>
                </div>
            @endif
        </div>
    </div>

</div>
@endsection