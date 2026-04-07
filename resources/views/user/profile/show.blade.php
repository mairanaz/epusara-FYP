@extends('layouts.app')

@section('content')
<div class="container-fluid">

    @php
        $isDependent = auth()->user()->account_type === 'tanggungan';
    @endphp

    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <h1 class="page-title fw-semibold fs-20 mb-1">
                {{ $isDependent ? 'Maklumat Tanggungan' : 'Maklumat Ahli' }}
            </h1>
            <p class="text-muted mb-0">
                Semakan maklumat profil dan status permohonan keahlian anda.
            </p>
        </div>

        <div class="btn-list mt-3 mt-md-0">
            <a class="btn btn-primary" href="{{ route('user.profile.edit') }}">
                Edit Maklumat
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h4 class="mb-1 fw-bold">{{ $profile->nama }}</h4>
                <div class="text-muted mb-2">{{ $profile->no_kp }}</div>

                @if($profile->status_permohonan)
                    <span class="badge bg-{{ $statusClass }} px-3 py-2">
                        Status: {{ ucfirst(str_replace('_', ' ', $profile->status_permohonan)) }}
                    </span>
                @else
                    <span class="badge bg-secondary px-3 py-2">
                        Status: Belum Dihantar
                    </span>
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

    @if($profile->status_permohonan === 'pending')
        <div class="alert alert-info border-0 shadow-sm">
            <div class="fw-semibold mb-1">Permohonan Sedang Disemak</div>
            <div>
                Permohonan keahlian anda telah berjaya dihantar dan sedang menunggu semakan pihak pentadbiran.
            </div>

            @unless($isDependent)
                <div class="mt-2">
                    Sila tunggu keputusan pentadbiran sebelum membuat bayaran yuran.
                </div>
            @endunless
        </div>
    @elseif($profile->status_permohonan === 'approved')
        @if($isDependent)
            <div class="alert alert-success border-0 shadow-sm">
                <div class="fw-semibold mb-1">Permohonan Diluluskan</div>
                <div>
                    Maklumat anda telah disahkan oleh pihak pentadbiran.
                </div>
            </div>
        @else
            <div class="alert alert-success border-0 shadow-sm d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <div class="fw-semibold mb-1">Permohonan Diluluskan</div>
                    <div>
                        Permohonan anda telah <b>diluluskan</b>. Sila buat bayaran yuran untuk meneruskan proses pengaktifan keahlian.
                    </div>
                </div>

                <a href="{{ route('user.payments.create') }}" class="btn btn-success">
                    Bayar Yuran
                </a>
            </div>
        @endif
    @elseif($profile->status_permohonan === 'rejected')
        <div class="alert alert-danger border-0 shadow-sm">
            <div class="fw-semibold mb-1">Permohonan Ditolak</div>
            <div>Permohonan anda telah ditolak oleh pihak pentadbiran.</div>

            @if($profile->catatan_permohonan)
                <div class="mt-2">
                    <b>Catatan:</b> {{ $profile->catatan_permohonan }}
                </div>
            @endif
        </div>
    @elseif($profile->status_permohonan === 'active')
        <div class="alert alert-success border-0 shadow-sm">
            <div class="fw-semibold mb-1">Keahlian Aktif</div>
            <div>
                Tahniah. Akaun keahlian anda telah aktif dan maklumat anda telah disahkan.
            </div>
        </div>
    @else
        <div class="alert alert-secondary border-0 shadow-sm">
            Maklumat profil anda telah disimpan, tetapi status permohonan belum tersedia.
        </div>
    @endif

    <div class="row g-4">

        <div class="col-xl-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-light fw-semibold">A. Maklumat Peribadi</div>
                <div class="card-body">
                    <div class="mb-2"><b>Nama:</b> {{ $profile->nama }}</div>
                    <div class="mb-2"><b>No. MyKad:</b> {{ $profile->no_kp }}</div>
                    <div class="mb-2"><b>Tarikh Lahir:</b> {{ optional($profile->tarikh_lahir)->format('d/m/Y') }}</div>
                    <div class="mb-2"><b>Jantina:</b> {{ ucfirst($profile->jantina ?? '-') }}</div>
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
                        <span class="badge bg-success">{{ $profile->tinggal_dalam_kariah ? 'Ya' : 'Tidak' }}</span>
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

        @unless($isDependent)
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light fw-semibold">E. Maklumat Waris</div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <b>Nama Waris:</b><br>
                                {{ $profile->nama_waris }}
                            </div>
                            <div class="col-md-3">
                                <b>Hubungan Waris:</b><br>
                                {{ $profile->hubungan_waris }}
                            </div>
                            <div class="col-md-3">
                                <b>No. Tel Waris:</b><br>
                                {{ $profile->no_tel_waris }}
                            </div>
                            <div class="col-md-3">
                                <b>Alamat Waris:</b><br>
                                {{ $profile->alamat_waris }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endunless

        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light fw-semibold">
                    {{ $isDependent ? 'E. Maklumat Permohonan' : 'F. Maklumat Permohonan' }}
                </div>
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

        @unless($isDependent)
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light fw-semibold">G. Pelan Pembayaran</div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <b>Pelan Dipilih:</b><br>
                                {{ $paymentPlanLabel }}
                            </div>

                            <div class="col-md-6">
                                <b>Keterangan:</b><br>
                                @if($profile->payment_plan === 'tahunan')
                                    RM20 pendaftaran + RM100 tahunan
                                @elseif($profile->payment_plan === 'bulanan')
                                    RM20 pendaftaran + RM10 bulan semasa, kemudian RM10 setiap bulan
                                @else
                                    -
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endunless

    </div>
</div>
@endsection