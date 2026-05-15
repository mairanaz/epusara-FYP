@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <h1 class="page-title fw-semibold fs-22 mb-1">Butiran Rekod Kubur</h1>
            <p class="text-muted mb-0">
                Rekod lengkap si mati, lot kubur, status kepuk dan gambar kubur terkini.
            </p>
        </div>

        <div class="mt-3 mt-md-0">
            <a href="{{ route('admin.burial-records.index') }}" class="btn btn-light border rounded-pill px-3">
                <i class="bx bx-arrow-back me-1"></i> Kembali
            </a>
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

    @if ($errors->any())
        <div class="alert alert-danger rounded-4 border-0 shadow-sm">
            <div class="fw-semibold mb-2">Sila semak maklumat berikut:</div>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4">

        {{-- Maklumat Si Mati --}}
        <div class="col-xl-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">Maklumat Si Mati</h5>
                </div>

                <div class="card-body">
                    <div class="row g-3">

                        <div class="col-md-12">
                            <div class="text-muted small">Nama Si Mati</div>
                            <div class="fw-bold">{{ $deathReport->nama_si_mati }}</div>
                        </div>

                        <div class="col-md-6">
                            <div class="text-muted small">No. KP</div>
                            <div class="fw-semibold">{{ $deathReport->no_kp_si_mati }}</div>
                        </div>

                        <div class="col-md-6">
                            <div class="text-muted small">Jenis</div>
                            <div class="fw-semibold">
                                {{ $deathReport->deceased_type === 'dependent' ? 'Tanggungan' : 'Ahli Utama' }}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="text-muted small">Tarikh Meninggal</div>
                            <div class="fw-semibold">
                                {{ $deathReport->tarikh_meninggal ? $deathReport->tarikh_meninggal->format('d/m/Y') : '-' }}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="text-muted small">Tarikh Kebumi</div>
                            <div class="fw-semibold">
                                {{ $deathReport->final_burial_date ? \Carbon\Carbon::parse($deathReport->final_burial_date)->format('d/m/Y') : '-' }}
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="text-muted small">Status Laporan</div>
                            <span class="badge bg-success">
                                {{ ucfirst(str_replace('_', ' ', $deathReport->status)) }}
                            </span>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        {{-- Maklumat Lot --}}
        <div class="col-xl-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">Maklumat Lot Kubur</h5>
                </div>

                <div class="card-body">
                    <div class="row g-3">

                        <div class="col-md-6">
                            <div class="text-muted small">Zon</div>
                            <div class="fw-bold">{{ $plot->zone_label }}</div>
                        </div>

                        <div class="col-md-6">
                            <div class="text-muted small">Kod Lot</div>
                            <span class="badge bg-primary fs-6">
                                {{ $plot->plot_code }}
                            </span>
                        </div>

                        <div class="col-md-6">
                            <div class="text-muted small">Baris</div>
                            <div class="fw-semibold">{{ $plot->row_number }}</div>
                        </div>

                        <div class="col-md-6">
                            <div class="text-muted small">Nombor Lot</div>
                            <div class="fw-semibold">{{ $plot->lot_number }}</div>
                        </div>

                        <div class="col-md-6">
                            <div class="text-muted small">Status Lot</div>
                            <span class="badge bg-success">
                                {{ $plot->status_label }}
                            </span>
                        </div>

                        <div class="col-md-6">
                            <div class="text-muted small">Tarikh Dikemaskini</div>
                            <div class="fw-semibold">
                                {{ $plot->updated_at ? $plot->updated_at->format('d/m/Y h:i A') : '-' }}
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        {{-- Status Tempahan Kepuk --}}
        <div class="col-xl-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">Status Tempahan Kepuk</h5>
                </div>

                <div class="card-body">
                    @if($selectedOrder)
                        <div class="row g-3">

                            <div class="col-md-6">
                                <div class="text-muted small">Jenis Tempahan</div>
                                <div class="fw-bold">{{ $selectedOrder->order_label }}</div>
                            </div>

                            <div class="col-md-6">
                                <div class="text-muted small">Kod Tempahan</div>
                                <div class="fw-semibold">{{ $selectedOrder->order_type ?? '-' }}</div>
                            </div>

                            <div class="col-md-6">
                                <div class="text-muted small">Amaun</div>
                                <div class="fw-bold">RM {{ number_format($selectedOrder->amount, 2) }}</div>
                            </div>

                            <div class="col-md-6">
                                <div class="text-muted small">Status</div>
                                <span class="badge {{ $kepukStatusBadge }}">
                                    {{ $kepukStatusLabel }}
                                </span>
                            </div>

                            <div class="col-md-6">
                                <div class="text-muted small">No Resit</div>
                                <div class="fw-semibold">{{ $selectedOrder->receipt_no ?? '-' }}</div>
                            </div>

                            <div class="col-md-6">
                                <div class="text-muted small">Tarikh Mohon</div>
                                <div class="fw-semibold">
                                    {{ $selectedOrder->created_at ? $selectedOrder->created_at->format('d/m/Y h:i A') : '-' }}
                                </div>
                            </div>

                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bx bx-package fs-1 text-muted"></i>
                            <h6 class="fw-bold mt-2 mb-1">Belum Ada Tempahan Kepuk</h6>
                            <p class="text-muted mb-0">
                                Tiada rekod tempahan kepuk untuk si mati ini.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Gambar Kubur --}}
        <div class="col-xl-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">Gambar Kubur Terkini</h5>
                </div>

                <div class="card-body">

                    @if($plot->grave_image)
                        <div class="mb-3">
                            <img src="{{ asset('storage/' . $plot->grave_image) }}"
                                alt="Gambar Kubur"
                                class="img-fluid rounded-4 border"
                                style="max-height: 280px; width: 100%; object-fit: cover;">
                        </div>

                        <div class="text-muted small">
                            Gambar dikemaskini pada:
                            {{ $plot->grave_image_updated_at ? $plot->grave_image_updated_at->format('d/m/Y h:i A') : '-' }}
                        </div>
                    @else
                        <div class="alert alert-warning rounded-4 border-0">
                            Belum ada gambar kubur dimuat naik.
                        </div>

                        <form action="{{ route('admin.burial-records.update-grave-image', $deathReport->id) }}"
                            method="POST"
                            enctype="multipart/form-data">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Upload Gambar Kubur</label>
                                <input type="file"
                                    name="grave_image"
                                    class="form-control"
                                    accept="image/*"
                                    required>
                                <div class="form-text">
                                    Format dibenarkan: JPG, PNG, WEBP. Saiz maksimum 2MB.
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary rounded-pill">
                                <i class="bx bx-upload me-1"></i> Simpan Gambar Kubur
                            </button>
                        </form>
                    @endif

                </div>
            </div>
        </div>

    </div>

</div>
@endsection