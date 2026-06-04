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
        
        $unpaidDuePeriods = $summary['unpaid_due_periods'] ?? [];
        $remainingCyclePeriods = $summary['remaining_cycle_periods'] ?? [];

        $arrearsAmount = (float) ($summary['arrears_amount'] ?? 0);
        $remainingCycleAmount = (float) ($summary['remaining_cycle_amount'] ?? 0);

        $totalOutstanding = (float) ($summary['total_outstanding'] ?? 0);

        $currentPaymentAmount = $plan === 'monthly'
            ? (
                $isFirstPaymentMonthly
                    ? (float) ($summary['first_monthly_total'] ?? 30)
                    : (
                        count($unpaidDuePeriods) > 1
                            ? $arrearsAmount
                            : 10.00
                    )
            )
            : (
                $isFirstPaymentYearly
                    ? (float) ($summary['first_yearly_total'] ?? 120)
                    : (float) ($summary['annual_balance'] ?? 0)
            );

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
                        @if($plan === 'monthly' && !$isFirstPaymentMonthly)
                            <div class="mb-2">
                                <div class="text-muted small">Pilihan Bayaran Tersedia</div>
                                <div class="fw-semibold">
                                    Sila pilih jenis bayaran di bahagian tindakan bayaran.
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Tunggakan Semasa</span>
                                <span class="fw-bold text-warning">
                                    RM{{ number_format($arrearsAmount, 2) }}
                                </span>
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Baki Kitaran</span>
                                <span class="fw-bold text-success">
                                    RM{{ number_format($remainingCycleAmount, 2) }}
                                </span>
                            </div>

                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Jumlah Baki Keseluruhan</span>
                                <span class="fw-bold text-danger">
                                    RM{{ number_format($totalOutstanding, 2) }}
                                </span>
                            </div>
                        @else
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Perlu Dibayar Sekarang</span>
                                <span class="fw-bold text-info">
                                    RM{{ number_format($currentPaymentAmount, 2) }}
                                </span>
                            </div>

                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Jumlah Baki Keseluruhan</span>
                                <span class="fw-bold text-danger">
                                    RM{{ number_format($totalOutstanding, 2) }}
                                </span>
                            </div>
                        @endif
                    </div>

                    <div class="alert alert-light mb-0">
                        <div class="fw-semibold mb-1">Nota</div>
                        <ul class="mb-0 ps-3">
                            <li>Yuran pendaftaran hanya dikenakan sekali sahaja.</li>
                            @if($plan === 'monthly')
                                <li>Bayaran pertama pelan bulanan ialah RM20 + RM10 = RM30.</li>
                                <li>Selepas itu, bayaran dibuat RM10 mengikut bulan seterusnya sahaja.</li>
                                <li>Ahli boleh membayar untuk satu bulan, beberapa bulan, semua bayaran tertunggak, atau semua baki bayaran.</li>
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
                                            <span class="badge bg-info-transparent text-info">
                                                Jumlah: RM{{ number_format($summary['first_monthly_total'], 2) }}
                                            </span>
                                        </div>
                                    </div>
                                @else
                                    @php
                                        $nextMonthlyLabel = $nextMonthlyPeriod;

                                        try {
                                            $nextMonthlyLabel = \Carbon\Carbon::createFromFormat('Y-m', $nextMonthlyPeriod)->translatedFormat('F Y');
                                        } catch (\Throwable $e) {
                                            $nextMonthlyLabel = $nextMonthlyPeriod;
                                        }

                                        $firstArrearsLabel = count($unpaidDuePeriods) > 0 ? $unpaidDuePeriods[0]['label'] : null;
                                        $lastArrearsLabel = count($unpaidDuePeriods) > 0 ? $unpaidDuePeriods[count($unpaidDuePeriods) - 1]['label'] : null;

                                        $firstBalanceLabel = count($remainingCyclePeriods) > 0 ? $remainingCyclePeriods[0]['label'] : null;
                                        $lastBalanceLabel = count($remainingCyclePeriods) > 0 ? $remainingCyclePeriods[count($remainingCyclePeriods) - 1]['label'] : null;
                                    @endphp

                                    @if(($summary['can_pay_monthly_now'] ?? true) && count($unpaidDuePeriods) <= 1)
                                        <div class="border rounded p-3 mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="payment_action" id="monthly_payment" value="monthly_payment" checked>
                                                <label class="form-check-label fw-semibold" for="monthly_payment">
                                                    Bayaran Bulanan Seterusnya
                                                </label>
                                            </div>
                                            <div class="text-muted small mt-2">
                                                Bayaran untuk {{ $nextMonthlyLabel }}.
                                            </div>
                                            <div class="mt-2">
                                                <span class="badge bg-info-transparent text-info">
                                                    Amaun: RM10.00
                                                </span>
                                            </div>
                                        </div>
                                    @endif

                                    @if(count($unpaidDuePeriods) > 1)
                                        <div class="border rounded p-3 mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="payment_action" id="monthly_arrears" value="monthly_arrears" checked>
                                                <label class="form-check-label fw-semibold" for="monthly_arrears">
                                                    Bayar Tunggakan Bulanan
                                                </label>
                                            </div>

                                            <div class="text-muted small mt-2">
                                                Bayar semua tunggakan dari {{ $firstArrearsLabel }} hingga {{ $lastArrearsLabel }}.
                                            </div>

                                            <div class="mt-2">
                                                <span class="badge bg-warning text-dark">
                                                    Jumlah: RM{{ number_format($arrearsAmount, 2) }}
                                                </span>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Pilihan baharu: Bayar Beberapa Bulan --}}
                                    @if(count($remainingCyclePeriods) > 0)
                                        <div class="border rounded p-3 mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input"
                                                    type="radio"
                                                    name="payment_action"
                                                    id="monthly_custom_amount"
                                                    value="monthly_custom_amount">
                                                <label class="form-check-label fw-semibold" for="monthly_custom_amount">
                                                    Bayar Ikut Jumlah
                                                </label>
                                            </div>

                                            <div class="text-muted small mt-2">
                                                Masukkan jumlah yang ingin dibayar. Sistem akan mengira bulan bayaran anda secara automatik.
                                            </div>

                                            <div class="mt-3" id="customAmountField" style="display: none;">
                                                <label for="custom_amount" class="form-label fw-semibold">
                                                    Jumlah Yang Ingin Dibayar
                                                </label>

                                                <div class="input-group">
                                                    <span class="input-group-text">RM</span>
                                                    <input type="number"
                                                        class="form-control"
                                                        name="custom_amount"
                                                        id="custom_amount"
                                                        value="{{ old('custom_amount') }}"
                                                        min="10"
                                                        step="10"
                                                        placeholder="Contoh: 30.00">
                                                </div>

                                                <small class="text-muted">
                                                    Minimum RM10.00. Bayaran mestilah dalam gandaan RM10.00.
                                                    Baki yang boleh dibayar sekarang ialah RM{{ number_format($remainingCycleAmount, 2) }}.
                                                </small>

                                                <div class="alert alert-info mt-3 mb-0 d-none" id="customAmountPreview">
                                                    <div class="fw-semibold mb-2">Bayaran Anda</div>

                                                    <div class="d-flex justify-content-between mb-1">
                                                        <span>Bilangan Bulan Dibayar</span>
                                                        <span class="fw-semibold" id="previewMonthCount">-</span>
                                                    </div>

                                                    <div class="d-flex justify-content-between mb-1">
                                                        <span>Bayaran Untuk Bulan</span>
                                                        <span class="fw-semibold text-end" id="previewPeriod">-</span>
                                                    </div>

                                                    <div class="d-flex justify-content-between mb-1">
                                                        <span>Jumlah Bayaran</span>
                                                        <span class="fw-semibold text-info" id="previewAmount">-</span>
                                                    </div>

                                                    <div class="d-flex justify-content-between">
                                                        <span>Baki Yang Belum Dibayar</span>
                                                        <span class="fw-semibold text-success" id="previewBalance">-</span>
                                                    </div>
                                                </div>

                                                <div class="text-danger small mt-2 d-none" id="customAmountError"></div>
                                            </div>
                                        </div>
                                    @endif

                                    
                                    @if(count($remainingCyclePeriods) > 0)
                                        <div class="border rounded p-3 mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="payment_action" id="monthly_balance" value="monthly_balance">
                                                <label class="form-check-label fw-semibold" for="monthly_balance">
                                                    Bayar Baki Kitaran
                                                </label>
                                            </div>

                                            <div class="text-muted small mt-2">
                                               Selesaikan semua bayaran yang belum dibayar dari {{ $firstBalanceLabel }} hingga {{ $lastBalanceLabel }}.
                                            </div>

                                            <div class="mt-2">
                                                <span class="badge bg-success-transparent text-success">
                                                    Jumlah: RM{{ number_format($remainingCycleAmount, 2) }}
                                                </span>
                                            </div>
                                        </div>
                                    @endif
                                @endif
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold">Bayaran Untuk Bulan</label>
                                <input type="hidden" name="payment_period" value="{{ $nextMonthlyPeriod }}">

                                <input type="text"
                                    class="form-control"
                                    id="monthlyPeriodDisplay"
                                    value="{{ $nextMonthlyLabel }}"
                                    disabled>

                                <small class="text-muted">
                                    Bulan bayaran akan dikira secara automatik berdasarkan pilihan anda.
                                </small>
                            </div>

                            <div class="card border">
                                <div class="card-body">
                                    <h6 class="mb-3">Ringkasan Bayaran</h6>

                                    <div class="table-responsive">
                                        <table class="table table-sm mb-0">

                                            @if($isFirstPaymentMonthly)
                                                <tbody>
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
                                                        <td class="text-end fw-semibold text-info">
                                                            RM{{ number_format($summary['first_monthly_total'], 2) }}
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            @else

                                                {{-- Ringkasan Bayaran Bulanan Seterusnya --}}
                                                <tbody id="summaryMonthlyPayment"
                                                    class="{{ (($summary['can_pay_monthly_now'] ?? true) && count($unpaidDuePeriods) <= 1) ? '' : 'd-none' }}">
                                                    <tr>
                                                        <td>Jenis Bayaran</td>
                                                        <td class="text-end">Bayaran Bulanan Seterusnya</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Bayaran Untuk Bulan</td>
                                                        <td class="text-end">{{ $nextMonthlyLabel }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-semibold">Jumlah Bayaran</td>
                                                        <td class="text-end fw-semibold text-info">RM10.00</td>
                                                    </tr>
                                                </tbody>

                                                {{-- Ringkasan Bayar Semua Tunggakan --}}
                                                <tbody id="summaryMonthlyArrears"
                                                    class="{{ count($unpaidDuePeriods) > 1 ? '' : 'd-none' }}">
                                                    <tr>
                                                        <td>Jenis Bayaran</td>
                                                        <td class="text-end">Bayar Semua Tunggakan</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Bayaran Untuk Bulan</td>
                                                        <td class="text-end">
                                                            {{ $firstArrearsLabel }} hingga {{ $lastArrearsLabel }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Bilangan Bulan Dibayar</td>
                                                        <td class="text-end">{{ count($unpaidDuePeriods) }} bulan</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-semibold">Jumlah Bayaran</td>
                                                        <td class="text-end fw-semibold text-warning">
                                                            RM{{ number_format($arrearsAmount, 2) }}
                                                        </td>
                                                    </tr>
                                                </tbody>

                                                {{-- Ringkasan Bayar Beberapa Bulan --}}
                                                <tbody id="summaryCustomAmount" class="d-none">
                                                    <tr>
                                                        <td>Jenis Bayaran</td>
                                                        <td class="text-end">Bayar Ikut Jumlah</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Bilangan Bulan Dibayar</td>
                                                        <td class="text-end" id="summaryCustomMonthCount">-</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Bayaran Untuk Bulan</td>
                                                        <td class="text-end" id="summaryCustomPeriod">-</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-semibold">Jumlah Bayaran</td>
                                                        <td class="text-end fw-semibold text-info" id="summaryCustomAmountValue">-</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Baki Yang Belum Dibayar</td>
                                                        <td class="text-end fw-semibold text-success" id="summaryCustomBalance">-</td>
                                                    </tr>
                                                </tbody>

                                                {{-- Ringkasan Bayar Baki Kitaran --}}
                                                <tbody id="summaryMonthlyBalance" class="d-none">
                                                    <tr>
                                                        <td>Jenis Bayaran</td>
                                                        <td class="text-end">Bayar Baki Kitaran</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Bayaran Untuk Bulan</td>
                                                        <td class="text-end">
                                                            {{ $firstBalanceLabel }} hingga {{ $lastBalanceLabel }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Bilangan Bulan Dibayar</td>
                                                        <td class="text-end">{{ count($remainingCyclePeriods) }} bulan</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-semibold">Jumlah Bayaran</td>
                                                        <td class="text-end fw-semibold text-success">
                                                            RM{{ number_format($remainingCycleAmount, 2) }}
                                                        </td>
                                                    </tr>
                                                </tbody>

                                            @endif

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
                                                Bayaran Tahunan
                                            </label>
                                        </div>
                                        <div class="text-muted small mt-2">
                                            Termasuk yuran pendaftaran dan yuran tahunan.
                                        </div>
                                        <div class="mt-2">
                                            <span class="badge bg-info-transparent text-info">
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
                                                            <td class="text-end fw-semibold text-info">RM{{ number_format($summary['first_yearly_total'], 2) }}</td>
                                                        </tr>
                                                    @else
                                                        <tr>
                                                            <td>Yuran Tahunan</td>
                                                            <td class="text-end">RM{{ number_format($summary['annual_balance'], 2) }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="fw-semibold">Jumlah Bayaran</td>
                                                            <td class="text-end fw-semibold text-info">RM{{ number_format($summary['annual_balance'], 2) }}</td>
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
                            ($plan === 'monthly' && (
                                $isFirstPaymentMonthly ||
                                ($summary['can_pay_monthly_now'] ?? true) ||
                                count($unpaidDuePeriods) > 0 ||
                                count($remainingCyclePeriods) > 0
                            )) ||
                            ($plan === 'yearly' && ($isFirstPaymentYearly || !$summary['is_fully_paid_yearly']))
                        )
                            <div class="mt-4 d-flex justify-content-end">
                                <button type="submit" class="btn btn-info">
                                    <i class="bx bx-save me-1"></i> Teruskan Bayaran
                                </button>
                            </div>
                        @endif

                        @if(
                            $plan === 'monthly' &&
                            !$isFirstPaymentMonthly &&
                            !($summary['can_pay_monthly_now'] ?? true) &&
                            count($remainingCyclePeriods) <= 0
                        )
                            <div class="alert alert-success">
                                Semua bayaran dalam kitaran semasa telah dijelaskan.
                            </div>
                        @endif
                    </form>

                </div>
            </div>
        </div>
    </div>

</div>

    @if($plan === 'monthly' && !$isFirstPaymentMonthly)
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const monthlyFee = 10;
            const remainingAmount = {{ $remainingCycleAmount }};
            const remainingPeriods = @json(array_values($remainingCyclePeriods));

            const actionInputs = document.querySelectorAll('input[name="payment_action"]');

            const customRadio = document.getElementById('monthly_custom_amount');
            const customField = document.getElementById('customAmountField');
            const customInput = document.getElementById('custom_amount');

            const previewBox = document.getElementById('customAmountPreview');
            const previewMonthCount = document.getElementById('previewMonthCount');
            const previewPeriod = document.getElementById('previewPeriod');
            const previewAmount = document.getElementById('previewAmount');
            const previewBalance = document.getElementById('previewBalance');
            const errorBox = document.getElementById('customAmountError');

            const monthlyPeriodDisplay = document.getElementById('monthlyPeriodDisplay');

            const summaryMonthlyPayment = document.getElementById('summaryMonthlyPayment');
            const summaryMonthlyArrears = document.getElementById('summaryMonthlyArrears');
            const summaryCustomAmount = document.getElementById('summaryCustomAmount');
            const summaryMonthlyBalance = document.getElementById('summaryMonthlyBalance');

            const summaryCustomMonthCount = document.getElementById('summaryCustomMonthCount');
            const summaryCustomPeriod = document.getElementById('summaryCustomPeriod');
            const summaryCustomAmountValue = document.getElementById('summaryCustomAmountValue');
            const summaryCustomBalance = document.getElementById('summaryCustomBalance');

            const defaultMonthlyPeriod = @json($nextMonthlyLabel);

            const arrearsPeriod = @json(
                count($unpaidDuePeriods) > 1
                    ? $firstArrearsLabel . ' hingga ' . $lastArrearsLabel
                    : $nextMonthlyLabel
            );

            const balancePeriod = @json(
                count($remainingCyclePeriods) > 0
                    ? $firstBalanceLabel . ' hingga ' . $lastBalanceLabel
                    : $nextMonthlyLabel
            );

            function formatMoney(amount) {
                return 'RM' + Number(amount).toFixed(2);
            }

            function hideAllSummaries() {
                if (summaryMonthlyPayment) {
                    summaryMonthlyPayment.classList.add('d-none');
                }

                if (summaryMonthlyArrears) {
                    summaryMonthlyArrears.classList.add('d-none');
                }

                if (summaryCustomAmount) {
                    summaryCustomAmount.classList.add('d-none');
                }

                if (summaryMonthlyBalance) {
                    summaryMonthlyBalance.classList.add('d-none');
                }
            }

            function resetCustomPreview() {
                if (previewBox) {
                    previewBox.classList.add('d-none');
                }

                if (errorBox) {
                    errorBox.classList.add('d-none');
                    errorBox.textContent = '';
                }

                if (summaryCustomMonthCount) {
                    summaryCustomMonthCount.textContent = '-';
                    summaryCustomPeriod.textContent = '-';
                    summaryCustomAmountValue.textContent = '-';
                    summaryCustomBalance.textContent = '-';
                }
            }

            function showCustomError(message) {
                if (errorBox) {
                    errorBox.textContent = message;
                    errorBox.classList.remove('d-none');
                }

                if (monthlyPeriodDisplay) {
                    monthlyPeriodDisplay.value = 'Masukkan jumlah bayaran yang sah';
                }
            }

            function updatePreview() {
                if (!customRadio || !customRadio.checked || !customInput) {
                    return;
                }

                const amount = parseFloat(customInput.value);

                resetCustomPreview();

                if (!amount) {
                    if (monthlyPeriodDisplay) {
                        monthlyPeriodDisplay.value = 'Masukkan jumlah bayaran dahulu';
                    }
                    return;
                }

                if (amount < monthlyFee) {
                    showCustomError('Jumlah minimum yang boleh dibayar ialah RM10.00.');
                    return;
                }

                if (amount % monthlyFee !== 0) {
                    showCustomError('Sila masukkan jumlah seperti RM10, RM20, RM30 atau seterusnya.');
                    return;
                }

                if (amount > remainingAmount) {
                    showCustomError('Jumlah yang dimasukkan melebihi baki yang belum dibayar.');
                    return;
                }

                const monthCount = amount / monthlyFee;
                const selectedPeriods = remainingPeriods.slice(0, monthCount);

                if (selectedPeriods.length !== monthCount) {
                    showCustomError('Bayaran ini melebihi bilangan bulan yang masih belum dibayar.');
                    return;
                }

                const firstLabel = selectedPeriods[0].label;
                const lastLabel = selectedPeriods[selectedPeriods.length - 1].label;

                const coveredPeriod = monthCount === 1
                    ? firstLabel
                    : firstLabel + ' hingga ' + lastLabel;

                const balanceAfterPayment = remainingAmount - amount;

                /*
                |--------------------------------------------------------------------------
                | Paparan dalam card Bayar Beberapa Bulan
                |--------------------------------------------------------------------------
                */
                previewMonthCount.textContent = monthCount + ' bulan';
                previewPeriod.textContent = coveredPeriod;
                previewAmount.textContent = formatMoney(amount);
                previewBalance.textContent = formatMoney(balanceAfterPayment);

                previewBox.classList.remove('d-none');

                /*
                |--------------------------------------------------------------------------
                | Paparan dalam Ringkasan Bayaran di bahagian bawah
                |--------------------------------------------------------------------------
                */
                summaryCustomMonthCount.textContent = monthCount + ' bulan';
                summaryCustomPeriod.textContent = coveredPeriod;
                summaryCustomAmountValue.textContent = formatMoney(amount);
                summaryCustomBalance.textContent = formatMoney(balanceAfterPayment);

                /*
                |--------------------------------------------------------------------------
                | Paparan Bayaran Untuk Bulan
                |--------------------------------------------------------------------------
                */
                if (monthlyPeriodDisplay) {
                    monthlyPeriodDisplay.value = coveredPeriod;
                }
            }

            function updateSelectedPayment() {
                const selectedAction = document.querySelector('input[name="payment_action"]:checked');

                if (!selectedAction) {
                    return;
                }

                hideAllSummaries();

                /*
                |--------------------------------------------------------------------------
                | Bayaran Bulanan Seterusnya
                |--------------------------------------------------------------------------
                */
                if (selectedAction.value === 'monthly_payment') {
                    if (customField) {
                        customField.style.display = 'none';
                    }

                    if (customInput) {
                        customInput.removeAttribute('required');
                    }

                    resetCustomPreview();

                    if (summaryMonthlyPayment) {
                        summaryMonthlyPayment.classList.remove('d-none');
                    }

                    if (monthlyPeriodDisplay) {
                        monthlyPeriodDisplay.value = defaultMonthlyPeriod;
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | Bayar Tunggakan Bulanan
                |--------------------------------------------------------------------------
                */
                if (selectedAction.value === 'monthly_arrears') {
                    if (customField) {
                        customField.style.display = 'none';
                    }

                    if (customInput) {
                        customInput.removeAttribute('required');
                    }

                    resetCustomPreview();

                    if (summaryMonthlyArrears) {
                        summaryMonthlyArrears.classList.remove('d-none');
                    }

                    if (monthlyPeriodDisplay) {
                        monthlyPeriodDisplay.value = arrearsPeriod;
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | Bayar Beberapa Bulan
                |--------------------------------------------------------------------------
                */
                if (selectedAction.value === 'monthly_custom_amount') {
                    if (customField) {
                        customField.style.display = 'block';
                    }

                    if (customInput) {
                        customInput.setAttribute('required', 'required');
                    }

                    if (summaryCustomAmount) {
                        summaryCustomAmount.classList.remove('d-none');
                    }

                    updatePreview();
                }

                /*
                |--------------------------------------------------------------------------
                | Bayar Baki Kitaran
                |--------------------------------------------------------------------------
                */
                if (selectedAction.value === 'monthly_balance') {
                    if (customField) {
                        customField.style.display = 'none';
                    }

                    if (customInput) {
                        customInput.removeAttribute('required');
                    }

                    resetCustomPreview();

                    if (summaryMonthlyBalance) {
                        summaryMonthlyBalance.classList.remove('d-none');
                    }

                    if (monthlyPeriodDisplay) {
                        monthlyPeriodDisplay.value = balancePeriod;
                    }
                }
            }

            actionInputs.forEach(function (input) {
                input.addEventListener('change', updateSelectedPayment);
            });

            if (customInput) {
                customInput.addEventListener('input', updatePreview);
            }

            updateSelectedPayment();
        });
    </script>
@endif
@endsection