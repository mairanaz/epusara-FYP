@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-md-flex d-block align-items-center justify-content-between my-4">
        <div id="tour-fee-header">
            <h4 class="mb-1">Maklumat Bayaran</h4>
            <p class="text-muted mb-0">
                Semak pelan yuran, status bayaran, baki semasa dan rekod bayaran anda.
            </p>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @php
    use Carbon\Carbon;

    $plan = $summary['plan'] ?? 'monthly';
    $planLabel = $plan === 'yearly' ? 'Pelan Tahunan' : 'Pelan Bulanan';

    $registrationFee = 20.00;
    $monthlyFee = 10.00;
    $yearlyFee = 100.00;

    $firstPaymentTotal = $plan === 'monthly'
        ? ($registrationFee + $monthlyFee)
        : ($registrationFee + $yearlyFee);


    $currentYear = $summary['current_year'] ?? now()->year;
    $registrationPaid = (float) ($summary['registration_paid'] ?? 0);

    $annualPaidThisYear = (float) ($summary['annual_paid_this_year'] ?? 0);
    $annualBalance = (float) ($summary['annual_balance'] ?? 0);
    $isFullyPaidYearly = (bool) ($summary['is_fully_paid_yearly'] ?? false);

    $monthlyPaidCount = (int) ($summary['monthly_paid_count'] ?? 0);
    $monthlyRemainingCount = (int) ($summary['monthly_remaining_count'] ?? 12);
    $monthlyOutstanding = (float) ($summary['monthly_outstanding'] ?? 0);
    $nextMonthlyPeriod = $summary['next_monthly_period'] ?? now()->format('Y-m');
    $schedulePeriods = $summary['schedule_periods'] ?? [];
    $paidPeriods = $summary['paid_periods'] ?? [];
    $membershipStartPeriod = $summary['membership_start_period'] ?? now()->format('Y-m');

    $membershipEndPeriod = $summary['membership_end_period']
        ?? Carbon::createFromFormat('Y-m', $membershipStartPeriod)->addMonths(11)->format('Y-m');

    try {
        $membershipStartLabel = Carbon::createFromFormat('Y-m', $membershipStartPeriod)->translatedFormat('F Y');
    } catch (\Throwable $e) {
        $membershipStartLabel = $membershipStartPeriod;
    }

    try {
        $membershipEndLabel = Carbon::createFromFormat('Y-m', $membershipEndPeriod)->translatedFormat('F Y');
    } catch (\Throwable $e) {
        $membershipEndLabel = $membershipEndPeriod;
    }

    $membershipSessionLabel = $membershipStartLabel . ' - ' . $membershipEndLabel;

    $registrationBalance = max(0, $registrationFee - $registrationPaid);

    $totalOutstanding = $plan === 'monthly'
        ? $registrationBalance + $monthlyOutstanding
        : $registrationBalance + $annualBalance;

    $paymentStatus = 'Belum Mula Bayar';
    if ($plan === 'monthly') {
        if ($monthlyPaidCount <= 0 && $registrationPaid <= 0) {
            $paymentStatus = 'Belum Mula Bayar';
        } elseif ($monthlyRemainingCount <= 0 && $registrationBalance <= 0) {
            $paymentStatus = 'Lengkap';
        } else {
            $paymentStatus = 'Belum Lengkap';
        }
    } else {
        if ($isFullyPaidYearly && $registrationBalance <= 0) {
            $paymentStatus = 'Lengkap';
        } elseif ($annualPaidThisYear > 0 || $registrationPaid > 0) {
            $paymentStatus = 'Belum Lengkap';
        } else {
            $paymentStatus = 'Belum Mula Bayar';
        }
    }

    $statusBadgeClass = match($paymentStatus) {
        'Lengkap' => 'success',
        'Belum Lengkap' => 'warning',
        default => 'secondary',
    };

    $monthlyProgress = min(100, ($monthlyPaidCount / 12) * 100);

    $currentPaymentAmount = 0;
    if ($plan === 'monthly') {
        $currentPaymentAmount = $registrationPaid <= 0 && $monthlyPaidCount <= 0
            ? ($registrationFee + $monthlyFee)
            : ($monthlyRemainingCount > 0 ? $monthlyFee : 0);
    } else {
        $currentPaymentAmount = ($registrationPaid <= 0 && $annualPaidThisYear <= 0)
            ? ($registrationFee + $yearlyFee)
            : max(0, $annualBalance);
    }

    $nextPaymentLabel = '-';
    if ($plan === 'monthly') {
        if ($monthlyRemainingCount <= 0) {
            $nextPaymentLabel = 'Semua 12 bulan telah dibayar';
        } else {
            try {
                $nextPaymentLabel = Carbon::createFromFormat('Y-m', $nextMonthlyPeriod)->translatedFormat('F Y');
            } catch (\Throwable $e) {
                $nextPaymentLabel = $nextMonthlyPeriod;
            }
        }
    } else {
        $nextPaymentLabel = 'Bayaran Tahunan ' . $membershipSessionLabel;
    }

    $noticeType = 'info';
    $noticeTitle = 'Makluman';
    $noticeMessage = 'Sila semak status bayaran anda.';

    if ($plan === 'monthly') {
        if ($monthlyPaidCount <= 0 && $registrationPaid <= 0) {
            $noticeType = 'warning';
            $noticeTitle = 'Peringatan Bayaran';
            $noticeMessage = 'Anda masih belum membuat bayaran pertama. Sila jelaskan yuran pendaftaran dan yuran bulan semasa.';
        } elseif ($monthlyRemainingCount > 0) {
            $noticeType = 'warning';
            $noticeTitle = 'Peringatan Bayaran';
            $noticeMessage = 'Anda masih mempunyai baki bayaran untuk melengkapkan 12 bulan kitaran semasa.';
        } else {
            $noticeType = 'success';
            $noticeTitle = 'Bayaran Terkini';
            $noticeMessage = 'Semua 12 bulan bayaran bagi kitaran semasa telah dijelaskan.';
        }
    } else {
        if ($registrationPaid <= 0 && $annualPaidThisYear <= 0) {
            $noticeType = 'warning';
            $noticeTitle = 'Peringatan Bayaran';
            $noticeMessage = 'Anda masih belum membuat bayaran pertama tahunan.';
        } elseif (!$isFullyPaidYearly) {
            $noticeType = 'warning';
            $noticeTitle = 'Peringatan Bayaran';
            $noticeMessage = 'Bayaran tahunan anda masih belum lengkap.';
        } else {
            $noticeType = 'success';
            $noticeTitle = 'Bayaran Terkini';
            $noticeMessage = 'Bayaran tahunan anda telah dijelaskan sepenuhnya.';
        }
    }

    $monthlySchedule = [];
    if ($plan === 'monthly') {
        foreach ($schedulePeriods as $index => $item) {
            $status = 'unpaid';
            $amount = (float) $item['amount'];

            if (in_array($item['period'], $paidPeriods)) {
                $status = 'paid';
            } elseif ($item['period'] === $nextMonthlyPeriod && $monthlyRemainingCount > 0) {
                $status = 'current';

                // Jika bayaran pertama masih belum dibuat,
                // bulan semasa perlu campur yuran pendaftaran
                if ($registrationPaid <= 0 && $monthlyPaidCount <= 0) {
                    $amount += $registrationFee;
                }
            }

            $monthlySchedule[] = [
                'period' => $item['period'],
                'label' => $item['label'],
                'amount' => $amount,
                'status' => $status,
            ];
        }
    }
@endphp

    <div class="alert alert-{{ $noticeType }} d-flex align-items-start justify-content-between gap-3 shadow-sm py-3"
     id="tour-fee-status-notice">
        <div class="d-flex align-items-start gap-3">
            <div class="fs-4">
                @if($noticeType === 'success')
                    <i class="bx bx-check-circle"></i>
                @elseif($noticeType === 'warning')
                    <i class="bx bx-error-circle"></i>
                @else
                    <i class="bx bx-info-circle"></i>
                @endif
            </div>
            <div>
                <div class="fw-bold mb-1">{{ $noticeTitle }}</div>
                <div>{{ $noticeMessage }}</div>
            </div>
        </div>
        <div class="text-md-end">
            <span class="badge bg-{{ $statusBadgeClass }}">{{ $paymentStatus }}</span>
        </div>
    </div>

    <div class="row g-3 mb-3" id="tour-fee-summary">
        <div class="col-md-4">
            <div class="card custom-card border-0 shadow-sm h-100">
                <div class="card-body py-3">
                    <div class="text-muted small mb-1">Pelan Keahlian</div>
                    <div class="fw-bold">{{ $planLabel }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card custom-card border-0 shadow-sm h-100">
                <div class="card-body py-3">
                    <div class="text-muted small mb-1">Perlu Dibayar Sekarang</div>
                    <div class="fw-bold text-info">RM{{ number_format($currentPaymentAmount, 2) }}</div>
                    <small class="text-muted">{{ $nextPaymentLabel }}</small>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card custom-card border-0 shadow-sm h-100">
                <div class="card-body py-3">
                    <div class="text-muted small mb-1">Jumlah Baki</div>
                    <div class="fw-bold {{ $totalOutstanding > 0 ? 'text-danger' : 'text-success' }}">
                        RM{{ number_format($totalOutstanding, 2) }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
    <div class="col-lg-5">
        <div class="card custom-card border-0 shadow-sm h-100" id="tour-fee-plan">
            <div class="card-header">
                <h6 class="card-title mb-0">Maklumat Pelan Yuran</h6>
            </div>
            
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-3">
                        <tbody>
                            <tr>
                                <th width="45%" class="bg-light">Pelan Dipilih</th>
                                <td>{{ $planLabel }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light">Yuran Pendaftaran</th>
                                <td>RM{{ number_format($registrationFee, 2) }}</td>
                            </tr>

                            @if($plan === 'monthly')
                                <tr>
                                    <th class="bg-light">Yuran Bulanan</th>
                                    <td>RM{{ number_format($monthlyFee, 2) }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Jumlah Bayaran Pertama</th>
                                    <td class="fw-bold text-info">RM{{ number_format($firstPaymentTotal, 2) }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Bayaran Seterusnya</th>
                                    <td>{{ $nextPaymentLabel }}</td>
                                </tr>
                            @endif

                            @if($plan === 'yearly')
                                <tr>
                                    <th class="bg-light">Yuran Tahunan</th>
                                    <td>RM{{ number_format($yearlyFee, 2) }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Jumlah Bayaran</th>
                                    <td class="fw-bold text-info">RM{{ number_format($firstPaymentTotal, 2) }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Tempoh Yuran</th>
                                    <td>{{ $membershipSessionLabel }}</td>
                                </tr>
                            @endif

                            <tr>
                                <th class="bg-light">Status Bayaran</th>
                                <td>
                                    <span class="badge bg-{{ $statusBadgeClass }}">
                                        {{ $paymentStatus }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                    <div class="alert alert-light border mb-0 py-2">
                        <div class="fw-semibold mb-2">Penerangan</div>
                        <ul class="mb-0 ps-3">
                            <li>Yuran pendaftaran dikenakan sekali sahaja.</li>
                            @if($plan === 'monthly')
                                <li>Pelan bulanan dibayar RM{{ number_format($monthlyFee, 2) }} setiap bulan.</li>
                                <li>Bayaran pertama ialah <strong>RM{{ number_format($firstPaymentTotal, 2) }}</strong> iaitu yuran pendaftaran dan yuran bulan pertama.</li>
                            @else
                                <li>Pelan tahunan dibayar RM{{ number_format($yearlyFee, 2) }} untuk tempoh 12 bulan.</li>
                                <li>Bayaran pertama ialah <strong>RM{{ number_format($firstPaymentTotal, 2) }}</strong> iaitu yuran pendaftaran dan yuran tahunan.</li>
                            @endif
                        </ul>
                    </div>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card custom-card border-0 shadow-sm mb-3" id="tour-fee-action">
            <div class="card-header">
                <h6 class="card-title mb-0">Tindakan</h6>
            </div>
            <div class="card-body">
                @if($paymentStatus === 'Belum Mula Bayar')
                    <div class="alert alert-warning mb-3 py-2">
                        <i class="bx bx-error-circle me-1"></i>
                        Anda belum membuat bayaran pertama.
                        Jumlah perlu dibayar sekarang ialah
                        <strong>RM{{ number_format($currentPaymentAmount, 2) }}</strong>.
                    </div>

                    <a href="{{ route('user.payments.create') }}" class="btn btn-info">
                        <i class="bx bx-credit-card me-1"></i> Teruskan Bayaran
                    </a>

                @elseif($totalOutstanding > 0)
                    <div class="alert alert-info mb-3 py-2">
                        <i class="bx bx-time-five me-1"></i>
                        Bayaran anda masih belum lengkap.
                        Baki semasa ialah
                        <strong>RM{{ number_format($totalOutstanding, 2) }}</strong>.
                    </div>

                    <a href="{{ route('user.payments.create') }}" class="btn btn-info">
                        <i class="bx bx-credit-card me-1"></i> Teruskan Bayaran
                    </a>

                @else
                    <div class="alert alert-success mb-0 py-2">
                        <i class="bx bx-check-circle me-1"></i> Bayaran telah selesai.
                    </div>
                @endif
            </div>
        </div>

        @if($plan === 'monthly')
            <div class="card custom-card border-0 shadow-sm" id="tour-fee-progress">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0">Kemajuan Bayaran Bulanan</h6>
                        <span class="fw-semibold">{{ $monthlyPaidCount }}/12 bulan</span>
                    </div>
                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar" role="progressbar"
                            style="width: {{ $monthlyProgress }}%;"
                            aria-valuenow="{{ $monthlyProgress }}"
                            aria-valuemin="0"
                            aria-valuemax="100">
                        </div>
                    </div>
                    <div class="small text-muted mt-2">
                        {{ $monthlyRemainingCount }} bulan lagi belum dibayar untuk melengkapkan kitaran 12 bulan semasa.
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

    @if($plan === 'monthly')
        <div class="card custom-card border-0 shadow-sm mb-3" id="tour-fee-schedule">
            <div class="card-header">
            <h6 class="card-title mb-0">
                Jadual Kitaran Bayaran Semasa
                <span class="text-muted fw-normal">
                    (Kitaran {{ \Carbon\Carbon::createFromFormat('Y-m', $membershipStartPeriod)->translatedFormat('F Y') }} - 
                    {{ \Carbon\Carbon::createFromFormat('Y-m', $membershipStartPeriod)->addMonths(11)->translatedFormat('F Y') }})
                </span>
            </h6>
        </div>
            <div class="card-body py-3">
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="8%">#</th>
                        <th>Tempoh</th>
                        <th width="18%">Jumlah</th>
                        <th width="22%">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($monthlySchedule as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item['label'] }}</td>
                            <td>RM{{ number_format($item['amount'], 2) }}</td>
                            <td>
                                @if($item['status'] === 'paid')
                                    <span class="badge bg-success">Sudah Bayar</span>
                                @elseif($item['status'] === 'current')
                                    <span class="badge bg-warning text-dark">Perlu Dibayar</span>
                                @else
                                    <span class="badge bg-danger">Belum Bayar</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
               <tfoot>
                    <tr>
                        <th colspan="2" class="text-end">Jumlah Baki Keseluruhan</th>
                        <th colspan="2" class="text-start text-danger fw-bold">
                            RM{{ number_format($totalOutstanding, 2) }}
                        </th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    </div>
    @endif

    <div class="card custom-card border-0 shadow-sm mt-4" id="tour-fee-history">
        <div class="card-header">
            <h6 class="card-title mb-0">Sejarah Bayaran</h6>
        </div>
        <div class="card-body py-3">
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Tarikh Bayar</th>
                            <th>Jenis Bayaran</th>
                            <th>Tempoh</th>
                            <th>Jumlah</th>
                            <th>No. Resit</th>
                            <th>Status</th>
                            <th>Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $index => $payment)
                            @php
                                $itemLabels = $payment->items->map(function ($item) {
                                    return match($item->payment_type) {
                                        'registration' => 'Yuran Pendaftaran',
                                        'monthly' => 'Yuran Bulanan',
                                        'yearly' => 'Yuran Tahunan',
                                        default => ucfirst(str_replace('_', ' ', $item->payment_type)),
                                    };
                                })->unique()->values();

                                $periodLabels = $payment->items->map(function ($item) {
                                    if ($item->payment_type === 'monthly' && $item->payment_period) {
                                        try {
                                            return \Carbon\Carbon::createFromFormat('Y-m', $item->payment_period)->translatedFormat('F Y');
                                        } catch (\Throwable $e) {
                                            return $item->payment_period;
                                        }
                                    }

                                    if ($item->payment_type === 'yearly' && $item->payment_period) {
                                        try {
                                            if (str_contains($item->payment_period, '_to_')) {
                                                [$startPeriod, $endPeriod] = explode('_to_', $item->payment_period);

                                                $startLabel = \Carbon\Carbon::createFromFormat('Y-m', $startPeriod)->translatedFormat('F Y');
                                                $endLabel = \Carbon\Carbon::createFromFormat('Y-m', $endPeriod)->translatedFormat('F Y');

                                                return $startLabel . ' - ' . $endLabel;
                                            }

                                            return $item->payment_period;
                                        } catch (\Throwable $e) {
                                            return $item->payment_period;
                                        }
                                    }

                                    return null;
                                })->filter()->unique()->values();

                                $statusClass = match($payment->status) {
                                    'paid' => 'success',
                                    'pending' => 'warning',
                                    'failed' => 'danger',
                                    'cancelled' => 'secondary',
                                    default => 'secondary',
                                };

                                $statusLabel = match($payment->status) {
                                    'paid' => 'Berjaya',
                                    'pending' => 'Menunggu Bayaran',
                                    'failed' => 'Gagal',
                                    'cancelled' => 'Dibatalkan',
                                    default => ucfirst($payment->status),
                                };
                            @endphp

                            <tr @if($index === 0) id="tour-first-payment-record" @endif>
                                <td>{{ $index + 1 }}</td>

                                <td>
                                    {{ $payment->paid_at ? \Carbon\Carbon::parse($payment->paid_at)->format('d/m/Y H:i') : '-' }}
                                </td>

                                <td>
                                    @php
                                        $mainPaymentLabel = match($payment->payment_type) {
                                            'first_monthly' => 'Bayaran Pertama Bulanan',
                                            'monthly' => 'Bayaran Bulanan',
                                            'monthly_arrears' => 'Bayaran Tunggakan Bulanan',
                                            'monthly_balance' => 'Bayaran Baki Kitaran',
                                            'first_yearly' => 'Bayaran Pertama Tahunan',
                                            'yearly' => 'Bayaran Tahunan',
                                            default => ucfirst(str_replace('_', ' ', $payment->payment_type)),
                                        };
                                    @endphp

                                    <div class="fw-semibold">{{ $mainPaymentLabel }}</div>

                                    <small class="text-muted">
                                        @forelse($itemLabels as $label)
                                            {{ $label }}@if(!$loop->last), @endif
                                        @empty
                                            -
                                        @endforelse
                                    </small>
                                </td>

                                <td>
                                    @if($periodLabels->count() > 1)
                                        <div class="fw-semibold">
                                            {{ $periodLabels->first() }} - {{ $periodLabels->last() }}
                                        </div>
                                        <small class="text-muted">
                                            {{ $periodLabels->count() }} bulan
                                        </small>
                                    @elseif($periodLabels->count() === 1)
                                        <div>{{ $periodLabels->first() }}</div>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>RM{{ number_format($payment->amount, 2) }}</td>

                                <td>{{ $payment->receipt_no ?? '-' }}</td>

                                <td>
                                    <span class="badge bg-{{ $statusClass }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>

                                <td>
                                    @if($payment->status === 'pending')
                                        <a href="{{ route('payment.billplz', $payment->id) }}" class="btn btn-sm btn-info mb-1">
                                            <i class="bx bx-credit-card me-1"></i> Bayar Billplz
                                        </a>
                                    @endif

                                    @if($payment->status === 'paid')
                                        <a href="{{ route('user.payments.receipt', $payment->id) }}" class="btn btn-sm btn-outline-info">
                                            Lihat Resit
                                        </a>
                                    @else
                                        <a href="{{ route('user.payments.receipt', $payment->id) }}" class="btn btn-sm btn-outline-secondary">
                                            Semak
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">Tiada rekod bayaran.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tourButton = document.getElementById('btnPageTour');

        if (!tourButton) {
            return;
        }

        tourButton.addEventListener('click', function () {
            if (!window.driver || !window.driver.js) {
                console.error('Driver.js tidak berjaya dimuatkan.');
                return;
            }

            const driver = window.driver.js.driver;

            const allSteps = [
                {
                    element: '#tour-fee-header',
                    popover: {
                        title: 'Maklumat Bayaran',
                        description: 'Halaman ini digunakan untuk menyemak pelan yuran khairat, status bayaran, baki semasa dan rekod pembayaran anda.',
                        side: 'bottom',
                        align: 'start'
                    }
                },
                {
                    element: '#tour-fee-status-notice',
                    popover: {
                        title: 'Status Bayaran Semasa',
                        description: 'Makluman ini menunjukkan keadaan bayaran anda sama ada belum dimulakan, belum lengkap atau telah selesai dibayar.',
                        side: 'bottom',
                        align: 'center'
                    }
                },
                {
                    element: '#tour-fee-summary',
                    popover: {
                        title: 'Ringkasan Bayaran',
                        description: 'Semak jenis pelan keahlian, jumlah yang perlu dibayar sekarang dan baki keseluruhan yang masih belum dijelaskan.',
                        side: 'bottom',
                        align: 'center'
                    }
                },
                {
                    element: '#tour-fee-plan',
                    popover: {
                        title: 'Maklumat Pelan Yuran',
                        description: 'Bahagian ini menerangkan pelan yang dipilih, yuran pendaftaran, kadar bayaran dan status pembayaran anda.',
                        side: 'right',
                        align: 'start'
                    }
                },
                {
                    element: '#tour-fee-action',
                    popover: {
                        title: 'Tindakan Pembayaran',
                        description: 'Jika masih terdapat baki bayaran, klik butang Teruskan Bayaran untuk membuat transaksi. Jika bayaran lengkap, status selesai akan dipaparkan di sini.',
                        side: 'left',
                        align: 'start'
                    }
                },
                {
                    element: '#tour-fee-progress',
                    popover: {
                        title: 'Kemajuan Bayaran Bulanan',
                        description: 'Bagi pelan bulanan, bar kemajuan ini menunjukkan bilangan bulan yang telah dibayar daripada keseluruhan kitaran 12 bulan.',
                        side: 'left',
                        align: 'center'
                    }
                },
                {
                    element: '#tour-fee-schedule',
                    popover: {
                        title: 'Jadual Kitaran Bayaran',
                        description: 'Semak status setiap bulan dalam kitaran semasa. Status menunjukkan bulan yang sudah dibayar, perlu dibayar sekarang atau masih belum dibayar.',
                        side: 'top',
                        align: 'center'
                    }
                },
                {
                    element: '#tour-fee-history',
                    popover: {
                        title: 'Sejarah Bayaran',
                        description: 'Semua transaksi bayaran anda direkodkan di sini bersama tarikh, tempoh bayaran, jumlah, nombor resit dan status transaksi.',
                        side: 'top',
                        align: 'center'
                    }
                },
                {
                    element: '#tour-first-payment-record',
                    popover: {
                        title: 'Status Transaksi dan Resit',
                        description: 'Lihat status transaksi anda pada rekod ini. Bagi bayaran yang berjaya, anda boleh melihat resit sebagai bukti pembayaran.',
                        side: 'top',
                        align: 'center'
                    }
                }
            ];

            /*
            |--------------------------------------------------------------------------
            | Paparkan step yang tersedia sahaja
            |--------------------------------------------------------------------------
            | Pelan tahunan tidak mempunyai bahagian kemajuan dan jadual bulanan.
            | Pengguna tanpa sejarah bayaran juga tidak mempunyai rekod pertama.
            */
            const availableSteps = allSteps.filter(function (step) {
                return document.querySelector(step.element);
            });

            if (availableSteps.length === 0) {
                console.warn('Tiada elemen tour ditemui pada halaman ini.');
                return;
            }

           let feeTour;

feeTour = driver({
    animate: true,
    smoothScroll: true,
    popoverClass: 'epusara-tour-popover',

    allowClose: true,
    overlayColor: '#0f172a',
    overlayOpacity: 0.58,
    stagePadding: 10,
    stageRadius: 10,
    popoverOffset: 14,
    disableActiveInteraction: true,

    showProgress: false,

    nextBtnText: 'Seterusnya →',
    prevBtnText: '← Sebelumnya',
    doneBtnText: 'Selesai',

    onPopoverRender: function () {
        const currentIndex = feeTour.getActiveIndex() ?? 0;
        window.updateEpusaraTourPopover(
            feeTour,
            currentIndex,
            availableSteps.length
        );
    },

    steps: availableSteps
});

feeTour.drive();

            feeTour.drive();
        });
    });
</script>
@endpush