@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <h1 class="page-title fw-semibold fs-20 mb-1">Permohonan Keahlian</h1>
            <p class="text-muted mb-0">Pilih pelan pembayaran dan sahkan maklumat permohonan anda.</p>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger border-0 shadow-sm">
            <div class="fw-semibold mb-2">Sila semak maklumat berikut:</div>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card custom-card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-success rounded-pill px-3 py-2">Step 4</span>
                    <span class="fw-semibold">Pelan Pembayaran</span>
                </div>
                <small class="text-muted">4 / 4</small>
            </div>

            <div class="progress mt-3" style="height: 8px;">
                <div class="progress-bar bg-success" style="width: 100%"></div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-xl-9">
            <div class="card custom-card border-0 shadow-sm">
                <div class="card-body p-0">
                    <form action="{{ route('user.profile.store.final') }}" method="POST">
                        @csrf

                        <div class="p-4">
                            <div class="mb-4">
                                <h4 class="fw-semibold mb-1">Pelan Pembayaran & Pengesahan</h4>
                                <p class="text-muted mb-0">Semak pilihan pelan dan tandakan pengakuan sebelum menghantar.</p>
                            </div>

                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label">Pilih Pelan Pembayaran</label>
                                    <select name="payment_plan" class="form-select @error('payment_plan') is-invalid @enderror">
                                        <option value="">-- Sila Pilih --</option>
                                        <option value="bulanan" {{ old('payment_plan', session('user_profile.step4.payment_plan')) == 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                                        <option value="tahunan" {{ old('payment_plan', session('user_profile.step4.payment_plan')) == 'tahunan' ? 'selected' : '' }}>Tahunan</option>
                                    </select>
                                    @error('payment_plan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <div class="alert alert-light border h-100 mb-0">
                                        <div class="fw-semibold mb-2">Maklumat Pelan</div>
                                        <ul class="mb-0 ps-3">
                                            <li><b>Bulanan</b>: Bayaran pertama RM30, kemudian RM10 setiap bulan.</li>
                                            <li><b>Tahunan</b>: Bayaran pertama RM120, kemudian tiada bayaran lain untuk tahun semasa.</li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="alert alert-info border-0 shadow-sm mb-0">
                                        <div class="fw-semibold mb-1">Peringatan</div>
                                        <div class="small">
                                            Permohonan keahlian akan dihantar untuk semakan pentadbiran selepas anda menekan butang
                                            <b>Hantar Permohonan</b>.
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-check mt-2">
                                        <input class="form-check-input @error('akuan') is-invalid @enderror"
                                               type="checkbox" name="akuan" value="1" id="akuan"
                                               {{ old('akuan', session('user_profile.step4.akuan')) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="akuan">
                                            Saya mengaku bahawa semua maklumat yang diberikan adalah benar dan saya memahami syarat keahlian.
                                        </label>
                                        @error('akuan') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="px-4 py-3 border-top d-flex justify-content-between">
                            <a href="{{ route('user.profile.create.step3') }}" class="btn btn-light">
                                Kembali
                            </a>
                            <button class="btn btn-success" type="submit">
                                Hantar Permohonan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection