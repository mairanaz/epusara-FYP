@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-md-flex d-block align-items-center justify-content-between my-4">
        <div>
            <h4 class="mb-1">Buat Bayaran</h4>
            <p class="text-muted mb-0">Bayaran adalah berdasarkan pelan keahlian yang telah dipilih semasa pendaftaran.</p>
        </div>
        <div class="mt-3 mt-md-0">
            <a href="{{ route('user.payments.index') }}" class="btn btn-light">
                <i class="bx bx-arrow-back me-1"></i> Kembali
            </a>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <div class="fw-semibold mb-2">Sila semak maklumat berikut:</div>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php
        $plan = $summary['plan'] ?? 'monthly';
        $planLabel = $plan === 'yearly' ? 'Tahunan' : 'Bulanan';
        $currentYear = $summary['current_year'] ?? now()->year;
        $nextMonthlyPeriod = $summary['next_monthly_period'] ?? now()->format('Y-m');

        $registrationPaid = $summary['registration_paid'] ?? 0;
        $monthlyCountThisYear = $summary['monthly_count_this_year'] ?? 0;
        $annualPaidThisYear = $summary['annual_paid_this_year'] ?? 0;

        $isFirstPaymentMonthly = ($summary['registration_paid'] ?? 0) <= 0 && ($summary['monthly_paid_count'] ?? 0) <= 0;
        $isFirstPaymentYearly = $registrationPaid <= 0 && $annualPaidThisYear <= 0;
        
        $totalOutstanding = (float) ($summary['total_outstanding'] ?? 0);
        $currentPaymentAmount = $plan === 'monthly'
            ? ($isFirstPaymentMonthly ? (float) ($summary['first_monthly_total'] ?? 30) : 10.00)
            : ($isFirstPaymentYearly ? (float) ($summary['first_yearly_total'] ?? 120) : (float) ($summary['annual_balance'] ?? 0));

    @endphp

    <div class="row g-4">
        <div class="col-xl-4">
            <div class="card custom-card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Ringkasan Pelan</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Pelan Semasa</span>
                            <span class="fw-semibold">{{ $planLabel }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Yuran Pendaftaran</span>
                            <span class="fw-semibold">RM20.00</span>
                        </div>

                        @if($plan === 'monthly')
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Yuran Bulanan</span>
                                <span class="fw-semibold">RM10.00</span>
                            </div>
                        @endif

                        @if($plan === 'yearly')
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Yuran Tahunan</span>
                                <span class="fw-semibold">RM100.00</span>
                            </div>
                        @endif
                    </div>

                    <div class="border rounded p-3 bg-light mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Perlu Dibayar Sekarang</span>
                            <span class="fw-bold text-primary">RM{{ number_format($currentPaymentAmount, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Jumlah Baki Keseluruhan</span>
                            <span class="fw-bold text-danger">RM{{ number_format($totalOutstanding, 2) }}</span>
                        </div>
                    </div>

                    <div class="alert alert-light mb-0">
                        <div class="fw-semibold mb-1">Nota</div>
                        <ul class="mb-0 ps-3">
                            <li>Yuran pendaftaran hanya dikenakan sekali sahaja.</li>
                            @if($plan === 'monthly')
                                <li>Bayaran pertama pelan bulanan ialah RM20 + RM10 = RM30.</li>
                                <li>Selepas itu, bayaran dibuat RM10 mengikut bulan seterusnya sahaja.</li>
                                <li>Pelan bulanan tidak boleh dibayar sekaligus untuk baki setahun.</li>
                            @else
                                <li>Bayaran pertama pelan tahunan ialah RM20 + RM100 = RM120.</li>
                                <li>Selepas bayaran tahunan dijelaskan, tiada bayaran lain untuk tahun semasa.</li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-8">
            <div class="card custom-card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Tindakan Bayaran</h6>
                </div>
                <div class="card-body">

                    <form method="POST" action="{{ route('user.payments.store') }}">
                        @csrf

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Pelan Keahlian</label>
                            <input type="text" class="form-control" value="{{ $planLabel }}" disabled>
                            <small class="text-muted">
                                Pelan ini diambil daripada maklumat keahlian anda dan tidak boleh diubah di halaman ini.
                            </small>
                        </div>

                        @if($plan === 'monthly')
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Jenis Bayaran</label>

                                @if($isFirstPaymentMonthly)
                                    <div class="border rounded p-3 mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="payment_action" id="first_payment" value="first_payment" checked>
                                            <label class="form-check-label fw-semibold" for="first_payment">
                                                Bayaran Pertama Bulanan
                                            </label>
                                        </div>
                                        <div class="text-muted small mt-2">
                                            Sesuai untuk ahli baru. Termasuk yuran pendaftaran dan bayaran bulan semasa.
                                        </div>
                                        <div class="mt-2">
                                            <span class="badge bg-primary-transparent text-primary">
                                                Jumlah: RM{{ number_format($summary['first_monthly_total'], 2) }}
                                            </span>
                                        </div>
                                    </div>
                                @else
                                    <div class="border rounded p-3 mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="payment_action" id="monthly_payment" value="monthly_payment" checked>
                                            <label class="form-check-label fw-semibold" for="monthly_payment">
                                                Bayaran Bulanan Seterusnya
                                            </label>
                                        </div>
                                        <div class="text-muted small mt-2">
                                            Bayaran hanya dibenarkan untuk tempoh seterusnya dalam kitaran 12 bulan keahlian anda.
                                        </div>
                                        <div class="mt-2">
                                            <span class="badge bg-info-transparent text-info">
                                                Amaun: RM10.00
                                            </span>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold">Tempoh Bayaran Bulanan</label>
                                <input type="hidden" name="payment_period" value="{{ $nextMonthlyPeriod }}">
                                <input type="text" class="form-control" value="{{ $nextMonthlyPeriod }}" disabled>
                                <small class="text-muted">
                                    Tempoh bayaran ditetapkan secara automatik oleh sistem.
                                </small>
                            </div>

                            <div class="card border">
                                <div class="card-body">
                                    <h6 class="mb-3">Ringkasan Bayaran</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm mb-0">
                                            <tbody>
                                                @if($isFirstPaymentMonthly)
                                                    <tr>
                                                        <td>Yuran Pendaftaran</td>
                                                        <td class="text-end">RM20.00</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Yuran Bulan Pertama</td>
                                                        <td class="text-end">RM10.00</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-semibold">Jumlah Bayaran</td>
                                                        <td class="text-end fw-semibold text-primary">RM{{ number_format($summary['first_monthly_total'], 2) }}</td>
                                                    </tr>
                                                @else
                                                    <tr>
                                                        <td>Yuran Bulanan</td>
                                                        <td class="text-end">RM10.00</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-semibold">Jumlah Bayaran</td>
                                                        <td class="text-end fw-semibold text-primary">RM10.00</td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if($plan === 'yearly')
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Jenis Bayaran</label>

                                @if($isFirstPaymentYearly)
                                    <div class="border rounded p-3 mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="payment_action" id="first_payment_yearly" value="first_payment" checked>
                                            <label class="form-check-label fw-semibold" for="first_payment_yearly">
                                                Bayaran Pertama Tahunan
                                            </label>
                                        </div>
                                        <div class="text-muted small mt-2">
                                            Sesuai untuk ahli baru. Termasuk yuran pendaftaran dan yuran tahunan.
                                        </div>
                                        <div class="mt-2">
                                            <span class="badge bg-primary-transparent text-primary">
                                                Jumlah: RM{{ number_format($summary['first_yearly_total'], 2) }}
                                            </span>
                                        </div>
                                    </div>
                                @elseif(!$summary['is_fully_paid_yearly'])
                                    <div class="border rounded p-3 mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="payment_action" id="annual_payment" value="annual_payment" checked>
                                            <label class="form-check-label fw-semibold" for="annual_payment">
                                                Bayaran Tahunan
                                            </label>
                                        </div>
                                        <div class="text-muted small mt-2">
                                            Lengkapkan bayaran tahunan bagi tahun {{ $currentYear }}.
                                        </div>
                                        <div class="mt-2">
                                            <span class="badge bg-success-transparent text-success">
                                                Amaun: RM{{ number_format($summary['annual_balance'], 2) }}
                                            </span>
                                        </div>
                                    </div>
                                @else
                                    <div class="alert alert-success">
                                        Bayaran tahunan bagi tahun {{ $currentYear }} telah dijelaskan sepenuhnya.
                                    </div>
                                @endif
                            </div>

                            @if(!$summary['is_fully_paid_yearly'] || $isFirstPaymentYearly)
                                <div class="mb-4">
                                    <label for="payment_period_year" class="form-label fw-semibold">Tahun Bayaran</label>
                                    <input type="hidden" name="payment_period" value="{{ $currentYear }}">
                                    <input type="text" id="payment_period_year" class="form-control" value="{{ $currentYear }}" disabled>
                                    <small class="text-muted">
                                        Bayaran tahunan adalah untuk tahun semasa sahaja.
                                    </small>
                                </div>

                                <div class="card border">
                                    <div class="card-body">
                                        <h6 class="mb-3">Ringkasan Bayaran</h6>
                                        <div class="table-responsive">
                                            <table class="table table-sm mb-0">
                                                <tbody>
                                                    @if($isFirstPaymentYearly)
                                                        <tr>
                                                            <td>Yuran Pendaftaran</td>
                                                            <td class="text-end">RM20.00</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Yuran Tahunan</td>
                                                            <td class="text-end">RM100.00</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="fw-semibold">Jumlah Bayaran</td>
                                                            <td class="text-end fw-semibold text-primary">RM{{ number_format($summary['first_yearly_total'], 2) }}</td>
                                                        </tr>
                                                    @else
                                                        <tr>
                                                            <td>Yuran Tahunan</td>
                                                            <td class="text-end">RM{{ number_format($summary['annual_balance'], 2) }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="fw-semibold">Jumlah Bayaran</td>
                                                            <td class="text-end fw-semibold text-primary">RM{{ number_format($summary['annual_balance'], 2) }}</td>
                                                        </tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endif

                        @if(
                            ($plan === 'monthly' && ($summary['can_pay_monthly_now'] ?? true)) ||
                            ($plan === 'yearly' && ($isFirstPaymentYearly || !$summary['is_fully_paid_yearly']))
                        )
                            <div class="mt-4 d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bx bx-save me-1"></i> Buat Bayaran
                                </button>
                            </div>
                        @endif

                        @if($plan === 'monthly' && !($summary['can_pay_monthly_now'] ?? true))
                            <div class="alert alert-warning">
                                Bayaran untuk tempoh seterusnya belum dibuka kerana belum masuk bulan tersebut.
                            </div>
                        @endif
                    </form>

                </div>
            </div>
        </div>
    </div>

</div>
@endsection