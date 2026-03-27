@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <h1 class="page-title fw-semibold fs-20 mb-1">Semakan Permohonan Profil</h1>
            <p class="text-muted mb-0">Paparan lengkap maklumat permohonan pengguna.</p>
        </div>
        <div class="btn-list mt-3 mt-md-0">
            <a href="{{ route('admin.profile.index') }}" class="btn btn-light">
                Kembali
            </a>
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

    @if ($errors->any())
        <div class="alert alert-danger">
            <div class="fw-semibold mb-2">Sila semak maklumat berikut:</div>
            <ul class="mb-0">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h4 class="fw-bold mb-1">{{ $profile->nama }}</h4>
                <div class="text-muted mb-2">{{ $profile->no_kp }}</div>
                @if($profile->status_permohonan)
                    <span class="badge bg-{{ $statusClass }}">
                        Status: {{ ucfirst(str_replace('_', ' ', $profile->status_permohonan)) }}
                    </span>
                @else
                    <span class="badge bg-secondary">Status: Belum Dihantar</span>
                @endif
            </div>
            <div class="text-md-end">
                <small class="text-muted d-block">Tarikh Permohonan</small>
                <span class="fw-semibold">
                    {{ $profile->tarikh_permohonan ? \Carbon\Carbon::parse($profile->tarikh_permohonan)->format('d/m/Y') : '-' }}
                </span>
            </div>
        </div>
    </div>

    <div class="row g-4">

        <div class="col-xl-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-light fw-semibold">A. Maklumat Peribadi</div>
                <div class="card-body">
                    <div class="mb-2"><b>Nama:</b> {{ $profile->nama }}</div>
                    <div class="mb-2"><b>No. MyKad:</b> {{ $profile->no_kp }}</div>
                    <div class="mb-2"><b>Tarikh Lahir:</b> {{ $profile->tarikh_lahir ? \Carbon\Carbon::parse($profile->tarikh_lahir)->format('d/m/Y') : '-' }}</div>
                    <div class="mb-2"><b>Agama:</b> {{ $profile->agama }}</div>
                    <div><b>Warganegara:</b> {{ $profile->warganegara }}</div>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-light fw-semibold">B. Maklumat Perhubungan</div>
                <div class="card-body">
                    <div class="mb-2"><b>Alamat Rumah:</b> {{ $profile->alamat_rumah }}</div>
                    <div class="mb-2"><b>No. Tel Rumah:</b> {{ $profile->no_tel_rumah ?? '-' }}</div>
                    <div><b>No. Telefon Bimbit:</b> {{ $profile->no_tel_bimbit }}</div>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-light fw-semibold">C. Maklumat Kariah</div>
                <div class="card-body">
                    <div class="mb-2">
                        <b>Tinggal Dalam Kariah:</b>
                        {{ $profile->tinggal_dalam_kariah ? 'Ya' : 'Tidak' }}
                    </div>
                    <div><b>Tempoh Menetap:</b> {{ $profile->tempoh_menetap }}</div>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-light fw-semibold">D. Maklumat Pekerjaan</div>
                <div class="card-body">
                    <div class="mb-2"><b>Pekerjaan:</b> {{ $profile->pekerjaan ?? '-' }}</div>
                    <div class="mb-2"><b>Nama Majikan:</b> {{ $profile->nama_majikan ?? '-' }}</div>
                    <div><b>Alamat Kerja:</b> {{ $profile->alamat_kerja ?? '-' }}</div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light fw-semibold">E. Maklumat Waris</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3"><b>Nama Waris:</b><br>{{ $profile->nama_waris }}</div>
                        <div class="col-md-3"><b>Hubungan Waris:</b><br>{{ $profile->hubungan_waris }}</div>
                        <div class="col-md-3"><b>No. Tel Waris:</b><br>{{ $profile->no_tel_waris }}</div>
                        <div class="col-md-3"><b>Alamat Waris:</b><br>{{ $profile->alamat_waris }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light fw-semibold">F. Maklumat Permohonan</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <b>Tarikh Permohonan:</b><br>
                            {{ $profile->tarikh_permohonan ? \Carbon\Carbon::parse($profile->tarikh_permohonan)->format('d/m/Y') : '-' }}
                        </div>
                        <div class="col-md-4">
                            <b>Status Permohonan:</b><br>
                            @if($profile->status_permohonan)
                                <span class="badge bg-{{ $statusClass }}">
                                    {{ ucfirst(str_replace('_', ' ', $profile->status_permohonan)) }}
                                </span>
                            @else
                                <span class="badge bg-secondary">Belum Dihantar</span>
                            @endif
                        </div>
                        <div class="col-md-4">
                            <b>Catatan:</b><br>
                            {{ $profile->catatan_permohonan ?? '-' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light fw-semibold">G. Pelan Pembayaran</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <b>Pelan Dipilih:</b><br>
                            {{ ucfirst($profile->payment_plan ?? '-') }}
                        </div>
                        <div class="col-md-6">
                            <b>Keterangan:</b><br>
                            @if($profile->payment_plan === 'tahunan')
                                RM20 pendaftaran + RM100 tahunan
                            @elseif($profile->payment_plan === 'bulanan')
                                RM20 pendaftaran + RM10 bulan semasa
                            @else
                                -
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light fw-semibold">H. Status Bayaran</div>
                <div class="card-body">
                    @if($hasPaidPayment)
                        <span class="badge bg-success">Sudah Dibayar</span>
                    @else
                        <span class="badge bg-danger">Belum Dibayar</span>
                    @endif
                </div>
            </div>
        </div>

    </div>

    @if($profile->status_permohonan === 'pending')
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-light fw-semibold">Tindakan Pentadbir</div>
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2 mb-4">
                    <form action="{{ route('admin.profile.approve', $profile) }}" method="POST" onsubmit="return confirm('Adakah anda pasti mahu meluluskan permohonan ini?');">
                        @csrf
                        <button type="submit" class="btn btn-success">
                            Luluskan Permohonan
                        </button>
                    </form>
                </div>

                <hr>

                <form action="{{ route('admin.profile.reject', $profile) }}" method="POST" onsubmit="return confirm('Adakah anda pasti mahu menolak permohonan ini?');">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Sebab / Catatan Penolakan</label>
                        <textarea name="catatan_permohonan"
                                  rows="4"
                                  class="form-control @error('catatan_permohonan') is-invalid @enderror"
                                  placeholder="Sila nyatakan sebab permohonan ditolak">{{ old('catatan_permohonan') }}</textarea>
                        @error('catatan_permohonan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-danger">
                        Tolak Permohonan
                    </button>
                </form>
            </div>
        </div>
    @endif

</div>
@endsection