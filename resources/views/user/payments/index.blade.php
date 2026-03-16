@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-md-flex d-block align-items-center justify-content-between my-4">
        <div>
            <h4 class="mb-1">Pembayaran Yuran Keahlian</h4>
            <p class="text-muted mb-0">Semak pelan semasa, status bayaran, dan sejarah transaksi anda.</p>
        </div>
        <div class="mt-3 mt-md-0">
            @if($summary['plan'] === 'yearly' && $summary['is_fully_paid_yearly'])
                <button class="btn btn-success" disabled>
                    <i class="bx bx-check-circle me-1"></i> Bayaran Selesai
                </button>
            @else
                <a href="{{ route('user.payments.create') }}" class="btn btn-primary">
                    <i class="bx bx-credit-card me-1"></i> Buat Bayaran
                </a>
            @endif
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

    @php
        $planLabel = $summary['plan'] === 'yearly' ? 'Tahunan' : 'Bulanan';
        $planBadgeClass = $summary['plan'] === 'yearly' ? 'bg-success-transparent text-success' : 'bg-info-transparent text-info';

        $registrationStatus = $summary['registration_balance'] <= 0 ? 'Selesai' : 'Belum Selesai';
        $registrationBadgeClass = $summary['registration_balance'] <= 0 ? 'bg-success-transparent text-success' : 'bg-warning-transparent text-warning';

        $currentYear = $summary['current_year'] ?? now()->year;
        $isFirstPaymentMonthly = $summary['registration_paid'] <= 0 && $summary['monthly_count_this_year'] <= 0;
        $isFirstPaymentYearly = $summary['registration_paid'] <= 0 && $summary['annual_paid_this_year'] <= 0;
    @endphp

    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card custom-card h-100">
                <div class="card-body">
                    <p class="text-muted mb-2">Pelan Semasa</p>
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">{{ $planLabel }}</h5>
                        <span class="badge {{ $planBadgeClass }}">{{ $planLabel }}</span>
                    </div>
                    <small class="text-muted d-block mt-2">
                        {{ $summary['plan'] === 'yearly' ? 'Bayaran RM100 setahun' : 'Bayaran RM10 sebulan' }}
                    </small>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card custom-card h-100">
                <div class="card-body">
                    <p class="text-muted mb-2">Status Pendaftaran</p>
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">RM{{ number_format($summary['registration_paid'], 2) }}</h5>
                        <span class="badge {{ $registrationBadgeClass }}">{{ $registrationStatus }}</span>
                    </div>
                    <small class="text-muted d-block mt-2">
                        Baki pendaftaran: RM{{ number_format($summary['registration_balance'], 2) }}
                    </small>
                </div>
            </div>
        </div>

        @if($summary['plan'] === 'monthly')
            <div class="col-xl-3 col-md-6">
                <div class="card custom-card h-100">
                    <div class="card-body">
                        <p class="text-muted mb-2">Bayaran Bulanan Tahun {{ $currentYear }}</p>
                        <h5 class="mb-1">RM{{ number_format($summary['monthly_paid_this_year'], 2) }}</h5>
                        <small class="text-muted d-block">
                            Bilangan bulan dibayar: {{ $summary['monthly_count_this_year'] }} bulan
                        </small>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card custom-card h-100">
                    <div class="card-body">
                        <p class="text-muted mb-2">Bayaran Seterusnya</p>
                        <h5 class="mb-1">RM10.00</h5>
                        <small class="text-muted d-block">
                            Tempoh seterusnya: {{ $summary['next_monthly_period'] ?? '-' }}
                        </small>
                    </div>
                </div>
            </div>
        @else
            <div class="col-xl-3 col-md-6">
                <div class="card custom-card h-100">
                    <div class="card-body">
                        <p class="text-muted mb-2">Bayaran Tahunan {{ $currentYear }}</p>
                        <h5 class="mb-1">RM{{ number_format($summary['annual_paid_this_year'], 2) }}</h5>
                        <small class="text-muted d-block">
                            Jumlah bayaran tahunan yang telah direkodkan bagi tahun semasa.
                        </small>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card custom-card h-100">
                    <div class="card-body">
                        <p class="text-muted mb-2">Status Tahun Semasa</p>
                        <h5 class="mb-1">
                            {{ $summary['is_fully_paid_yearly'] ? 'Selesai' : 'Belum Selesai' }}
                        </h5>
                        <small class="text-muted d-block">
                            Baki tahunan: RM{{ number_format($summary['annual_balance'], 2) }}
                        </small>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-5">
            <div class="card custom-card h-100">
                <div class="card-header">
                    <h6 class="card-title mb-0">Ringkasan Akaun</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Pelan Keahlian</span>
                            <span class="fw-semibold">{{ $planLabel }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Yuran Pendaftaran</span>
                            <span class="fw-semibold">RM20.00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Yuran Bulanan</span>
                            <span class="fw-semibold">RM10.00</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Yuran Tahunan</span>
                            <span class="fw-semibold">RM100.00</span>
                        </div>
                    </div>

                    <div class="alert alert-light mb-0">
                        <div class="fw-semibold mb-1">Maklumat Penting</div>
                        <ul class="mb-0 ps-3">
                            <li>Yuran pendaftaran dikenakan sekali sahaja.</li>
                            @if($summary['plan'] === 'monthly')
                                <li>Pelan bulanan dibayar RM10 mengikut bulan.</li>
                                <li>Bayaran pertama ialah pendaftaran + bulan semasa.</li>
                                <li>Pelan bulanan tidak membenarkan bayaran baki setahun sekaligus.</li>
                            @else
                                <li>Pelan tahunan dibayar RM100 setahun.</li>
                                <li>Bayaran pertama ialah pendaftaran + yuran tahunan.</li>
                                <li>Selepas yuran tahunan dijelaskan, tiada bayaran lain bagi tahun semasa.</li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-7">
            <div class="card custom-card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0">Tindakan Disyorkan</h6>
                    <a href="{{ route('user.payments.create') }}" class="btn btn-sm btn-primary">Teruskan</a>
                </div>
                <div class="card-body">
                    @if($summary['plan'] === 'monthly')
                        @if($isFirstPaymentMonthly)
                            <div class="border rounded p-3 mb-3">
                                <h6 class="mb-1">Bayaran Pertama Bulanan</h6>
                                <p class="text-muted mb-2">Bayar yuran pendaftaran dan bayaran bulan semasa.</p>
                                <div class="fw-semibold text-primary">
                                    Jumlah perlu dibayar: RM{{ number_format($summary['first_monthly_total'], 2) }}
                                </div>
                            </div>
                        @else
                            <div class="border rounded p-3">
                                <h6 class="mb-1">Bayaran Bulanan Seterusnya</h6>
                                <p class="text-muted mb-2">Teruskan bayaran untuk bulan seterusnya yang dibenarkan oleh sistem.</p>
                                <div class="fw-semibold text-primary">Amaun bayaran: RM10.00</div>
                                <div class="small text-muted mt-1">
                                    Tempoh seterusnya: {{ $summary['next_monthly_period'] ?? '-' }}
                                </div>
                            </div>
                        @endif
                    @else
                        @if($isFirstPaymentYearly)
                            <div class="border rounded p-3 mb-3">
                                <h6 class="mb-1">Bayaran Pertama Tahunan</h6>
                                <p class="text-muted mb-2">Bayar yuran pendaftaran dan yuran tahunan bagi tahun {{ $currentYear }}.</p>
                                <div class="fw-semibold text-primary">
                                    Jumlah perlu dibayar: RM{{ number_format($summary['first_yearly_total'], 2) }}
                                </div>
                            </div>
                        @elseif(!$summary['is_fully_paid_yearly'])
                            <div class="border rounded p-3 mb-3">
                                <h6 class="mb-1">Bayaran Tahunan</h6>
                                <p class="text-muted mb-2">Lengkapkan bayaran tahunan bagi tahun {{ $currentYear }}.</p>
                                <div class="fw-semibold text-primary">
                                    Jumlah perlu dibayar: RM{{ number_format($summary['annual_balance'], 2) }}
                                </div>
                            </div>
                        @else
                            <div class="border rounded p-3">
                                <h6 class="mb-1">Status Tahunan Semasa</h6>
                                <p class="mb-1">Bayaran tahunan tahun {{ $currentYear }} telah selesai.</p>
                                <p class="mb-0">Baki tahunan: <span class="fw-semibold text-success">RM0.00</span></p>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card custom-card">
        <div class="card-header">
            <h6 class="card-title mb-0">Sejarah Bayaran</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Tarikh</th>
                            <th>Pelan</th>
                            <th>Jenis Bayaran</th>
                            <th>Tempoh</th>
                            <th>Amaun</th>
                            <th>Status</th>
                            <th>No. Resit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                            @php
                                $displayPlan = match($payment->payment_plan) {
                                    'monthly', 'bulanan' => 'Bulanan',
                                    'yearly', 'tahunan' => 'Tahunan',
                                    default => ucfirst($payment->payment_plan),
                                };

                                $displayType = match($payment->payment_type) {
                                    'registration', 'pendaftaran' => 'Pendaftaran',
                                    'monthly', 'bulanan' => 'Bulanan',
                                    'yearly', 'tahunan' => 'Tahunan',
                                    default => ucwords(str_replace('_', ' ', $payment->payment_type)),
                                };

                                $statusClass = match($payment->status) {
                                    'paid' => 'bg-success-transparent text-success',
                                    'pending' => 'bg-warning-transparent text-warning',
                                    'failed', 'cancelled' => 'bg-danger-transparent text-danger',
                                    default => 'bg-light text-dark',
                                };
                            @endphp
                            <tr>
                                <td>{{ $payment->paid_at ? \Carbon\Carbon::parse($payment->paid_at)->format('d/m/Y') : '-' }}</td>
                                <td>{{ $displayPlan }}</td>
                                <td>{{ $displayType }}</td>
                                <td>{{ $payment->payment_period ?? '-' }}</td>
                                <td class="fw-semibold">RM{{ number_format($payment->amount, 2) }}</td>
                                <td>
                                    <span class="badge {{ $statusClass }}">
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                </td>
                                <td>{{ $payment->receipt_no ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">Tiada rekod bayaran setakat ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection