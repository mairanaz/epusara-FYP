@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <h1 class="page-title fw-semibold fs-22 mb-1">Rekod Kubur</h1>
            <p class="text-muted mb-0">
                Senarai rekod si mati yang telah ditetapkan lot kubur.
            </p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Filter --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.burial-records.index') }}">
                <div class="row g-3 align-items-end">

                    <div class="col-md-5">
                        <label class="form-label fw-semibold">Carian</label>
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               class="form-control"
                               placeholder="Cari nama si mati / no KP / kod lot">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Zon</label>
                        <select name="zone" class="form-select">
                            <option value="">Semua Zon</option>
                            <option value="L" {{ request('zone') == 'L' ? 'selected' : '' }}>Zon Lelaki</option>
                            <option value="P" {{ request('zone') == 'P' ? 'selected' : '' }}>Zon Perempuan</option>
                            <option value="K" {{ request('zone') == 'K' ? 'selected' : '' }}>Zon Kanak-kanak</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Gambar</label>
                        <select name="image_status" class="form-select">
                            <option value="">Semua</option>
                            <option value="ada" {{ request('image_status') == 'ada' ? 'selected' : '' }}>Ada Gambar</option>
                            <option value="tiada" {{ request('image_status') == 'tiada' ? 'selected' : '' }}>Tiada Gambar</option>
                        </select>
                    </div>

                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bx bx-search me-1"></i> Cari
                        </button>

                        <a href="{{ route('admin.burial-records.index') }}" class="btn btn-light border">
                            Reset
                        </a>
                    </div>

                </div>
            </form>
        </div>
    </div>

    {{-- Table --}}
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="mb-0 fw-bold">Senarai Rekod Kubur</h5>
            <div class="text-muted small">
                Jumlah paparan: {{ $burialRecords->total() }} rekod
            </div>
        </div>

        <div class="card-body p-0">
            @if($burialRecords->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Si Mati</th>
                                <th>Tarikh Meninggal</th>
                                <th>Tarikh Kebumi</th>
                                <th>Zon</th>
                                <th>Kod Lot</th>
                                <th>Status Kepuk</th>
                                <th>Gambar</th>
                                <th class="text-end">Tindakan</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($burialRecords as $record)
                                @php
                                    $plot = $record->final_burial_plot;
                                    $graveOrder = $record->selected_grave_order;
                                @endphp

                                <tr>
                                    <td>{{ $burialRecords->firstItem() + $loop->index }}</td>

                                    <td>
                                        <div class="fw-bold">{{ $record->nama_si_mati }}</div>
                                        <div class="text-muted small">
                                            No. KP: {{ $record->no_kp_si_mati }}
                                        </div>
                                        <div class="text-muted small">
                                            Jenis: {{ ucfirst($record->deceased_type) }}
                                        </div>
                                    </td>

                                    <td>
                                        {{ $record->tarikh_meninggal ? $record->tarikh_meninggal->format('d/m/Y') : '-' }}
                                    </td>

                                    <td>
                                        {{ $record->final_burial_date ? \Carbon\Carbon::parse($record->final_burial_date)->format('d/m/Y') : '-' }}
                                    </td>

                                    <td>
                                        @if($plot)
                                            <span class="badge bg-light text-dark border">
                                                {{ $plot->zone_label }}
                                            </span>
                                        @else
                                            -
                                        @endif
                                    </td>

                                    <td>
                                        @if($plot)
                                            <span class="badge bg-primary">
                                                {{ $plot->plot_code }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">Tiada Lot</span>
                                        @endif
                                    </td>

                                   <td>
                                        <span class="badge {{ $record->kepuk_status_badge }}">
                                            {{ $record->kepuk_status_label }}
                                        </span>
                                    </td>

                                    <td>
                                        @if($plot && $plot->grave_image)
                                            <span class="badge bg-success">Ada Gambar</span>
                                        @else
                                            <span class="badge bg-warning text-dark">Tiada Gambar</span>
                                        @endif
                                    </td>

                                    <td class="text-end">
                                        <a href="{{ route('admin.burial-records.show', $record->id) }}"
                                           class="btn btn-sm btn-outline-primary rounded-pill">
                                            <i class="bx bx-show me-1"></i> Lihat
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-3">
                    {{ $burialRecords->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bx bx-map fs-1 text-muted"></i>
                    <h6 class="fw-bold mt-2 mb-1">Tiada rekod kubur dijumpai</h6>
                    <p class="text-muted mb-0">
                        Rekod akan dipaparkan selepas laporan kematian disahkan dan lot kubur ditetapkan.
                    </p>
                </div>
            @endif
        </div>
    </div>

</div>
@endsection