@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-md-flex d-block align-items-center justify-content-between my-4">
        <div>
            <h4 class="mb-1">Maklumat Bayaran</h4>
            <p class="text-muted mb-0">
                Semak pelan yuran, status bayaran, baki semasa dan rekod bayaran anda.
            </p>
        </div>
        <div class="mt-3 mt-md-0">
            <a href="{{ route('user.payments.create') }}" class="btn btn-primary">
                <i class="bx bx-plus-circle me-1"></i> Buat Bayaran
            </a>
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
        $nextPaymentLabel = 'Bayaran Tahunan ' . $currentYear;
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
            $noticeMessage = 'Bayaran tahunan anda bagi tahun semasa masih belum lengkap.';
        } else {
            $noticeType = 'success';
            $noticeTitle = 'Bayaran Terkini';
            $noticeMessage = 'Bayaran tahunan bagi tahun semasa telah dijelaskan sepenuhnya.';
        }
    }

    $monthlySchedule = [];
    if ($plan === 'monthly') {
        foreach ($schedulePeriods as $item) {
            $status = 'unpaid';

            if (in_array($item['period'], $paidPeriods)) {
                $status = 'paid';
            } elseif ($item['period'] === $nextMonthlyPeriod && $monthlyRemainingCount > 0) {
                $status = 'current';
            }

            $monthlySchedule[] = [
                'period' => $item['period'],
                'label' => $item['label'],
                'amount' => $item['amount'],
                'status' => $status,
            ];
        }
    }
@endphp

    <div class="alert alert-{{ $noticeType }} d-flex align-items-start justify-content-between gap-3 shadow-sm py-3">
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

    <div class="row g-3 mb-3">
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
                    <div class="fw-bold text-primary">RM{{ number_format($currentPaymentAmount, 2) }}</div>
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
        <div class="card custom-card border-0 shadow-sm h-100">
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
                                    <th class="bg-light">Tahun Semasa</th>
                                    <td>{{ $currentYear }}</td>
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
                            <li>Pelan bulanan dibayar RM10.00 setiap bulan.</li>
                            <li>Bayaran pertama termasuk yuran pendaftaran dan yuran bulan semasa.</li>
                        @else
                            <li>Pelan tahunan dibayar RM100.00 untuk tahun semasa.</li>
                            <li>Bayaran pertama termasuk yuran pendaftaran dan yuran tahunan.</li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card custom-card border-0 shadow-sm mb-3">
            <div class="card-header">
                <h6 class="card-title mb-0">Tindakan</h6>
            </div>
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('user.payments.create') }}" class="btn btn-primary">
                        <i class="bx bx-credit-card me-1"></i> Buat Bayaran
                    </a>
                </div>
            </div>
        </div>

        @if($plan === 'monthly')
            <div class="card custom-card border-0 shadow-sm">
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
        <div class="card custom-card border-0 shadow-sm mb-3">
            <div class="card-header">
            <h6 class="card-title mb-0">
                Jadual Bayaran Bulanan
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
                        <th colspan="2" class="text-end">Baki Bayaran Kitaran Semasa</th>
                        <th colspan="2" class="text-start text-primary">
                            RM{{ number_format($monthlyOutstanding, 2) }}
                        </th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    </div>
    @endif

    <div class="card custom-card border-0 shadow-sm mt-4">
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
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ \Carbon\Carbon::parse($payment->paid_at)->format('d/m/Y H:i') }}</td>
                                <td>
                                    @if($payment->payment_type === 'registration')
                                        Yuran Pendaftaran
                                    @elseif($payment->payment_type === 'monthly')
                                        Yuran Bulanan
                                    @elseif($payment->payment_type === 'yearly')
                                        Yuran Tahunan
                                    @else
                                        {{ ucfirst($payment->payment_type) }}
                                    @endif
                                </td>
                                <td>
                                    @if($payment->payment_type === 'monthly' && $payment->payment_period)
                                        {{ \Carbon\Carbon::createFromFormat('Y-m', $payment->payment_period)->translatedFormat('F Y') }}
                                    @elseif($payment->payment_type === 'yearly' && $payment->payment_period)
                                        {{ $payment->payment_period }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>RM{{ number_format($payment->amount, 2) }}</td>
                                <td>{{ $payment->receipt_no ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-success">{{ ucfirst($payment->status) }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('user.payments.receipt', $payment->id) }}" class="btn btn-sm btn-outline-primary">
                                        Lihat Resit
                                    </a>
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