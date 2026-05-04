<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\PaymentItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PaymentController extends Controller
{
    private const REGISTRATION_FEE = 20.00;
    private const MONTHLY_FEE = 10.00;
    private const YEARLY_FEE = 100.00;

    public function index()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $profile = $user->profile;

        if (!$profile) {
            return redirect()->route('user.profile.create.step1')
                ->with('error', 'Sila lengkapkan maklumat ahli terlebih dahulu.');
        }

        $payments = $user->payments()->with('items')->latest()->get();

        $plan = $this->normalizePlan($profile->payment_plan);
        $summary = $this->getPaymentSummary($user->id, $plan);

        return view('user.payments.index', compact('payments', 'summary', 'profile'));
    }

    public function create()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $profile = $user->profile;

        if (!$profile) {
            return redirect()->route('user.profile.create.step1')
                ->with('error', 'Sila lengkapkan maklumat ahli terlebih dahulu.');
        }

        $plan = $this->normalizePlan($profile->payment_plan);
        $summary = $this->getPaymentSummary($user->id, $plan);

        return view('user.payments.create', compact('profile', 'summary'));
    }

    public function store(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $profile = $user->profile;

        if (!$profile) {
            return redirect()->route('user.profile.create.step1')
                ->with('error', 'Sila lengkapkan maklumat ahli terlebih dahulu.');
        }

        $plan = $this->normalizePlan($profile->payment_plan);

        $request->validate([
            'payment_action' => 'required|in:first_payment,monthly_payment,monthly_arrears,monthly_balance,annual_payment',
            'payment_period' => 'nullable|string|max:20',
        ]);

        $action = $request->payment_action;
        $summary = $this->getPaymentSummary($user->id, $plan);
        $currentYear = now()->year;

        if ($plan === 'monthly') {
            if ($action === 'annual_payment') {
                return redirect()->route('user.payments.index')
                    ->with('error', 'Pelan bulanan tidak boleh membuat bayaran tahunan.');
            }

            if ($action === 'first_payment') {
                if ($summary['registration_paid'] > 0 || $summary['monthly_paid_count'] > 0) {
                    return redirect()->route('user.payments.index')
                        ->with('error', 'Bayaran pertama telah pun dibuat.');
                }

                $period = $request->payment_period ?: now()->format('Y-m');
                [$year, $month] = $this->extractYearMonth($period);

                if (!$this->isAllowedMonthlyPeriod($user->id, $year, $month)) {
                    return redirect()->route('user.payments.index')
                        ->with('error', 'Bayaran pertama pelan bulanan hanya dibenarkan untuk bulan semasa.');
                }

                $payment = $this->createPaymentWithItems([
                    'user_id' => $user->id,
                    'payment_plan' => 'monthly',
                    'payment_type' => 'first_monthly',
                    'amount' => self::REGISTRATION_FEE + self::MONTHLY_FEE,
                    'payment_period' => sprintf('%04d-%02d', $year, $month),
                    'membership_year' => $year,
                    'paid_month' => $month,
                    'notes' => 'Bayaran pertama bulanan',
                    'items' => [
                        [
                            'payment_type' => 'registration',
                            'amount' => self::REGISTRATION_FEE,
                            'payment_period' => null,
                            'membership_year' => $year,
                            'paid_month' => null,
                            'notes' => 'Yuran pendaftaran',
                        ],
                        [
                            'payment_type' => 'monthly',
                            'amount' => self::MONTHLY_FEE,
                            'payment_period' => sprintf('%04d-%02d', $year, $month),
                            'membership_year' => $year,
                            'paid_month' => $month,
                            'notes' => 'Yuran bulan pertama',
                        ],
                    ],
                ]);

                return redirect()->route('payment.billplz', $payment->id);
            }

            if ($action === 'monthly_payment') {
                if ($summary['registration_balance'] > 0) {
                    return redirect()->route('user.payments.index')
                        ->with('error', 'Sila buat bayaran pertama dahulu (pendaftaran + bulan semasa).');
                }

                if ($summary['monthly_remaining_count'] <= 0) {
                    return redirect()->route('user.payments.index')
                        ->with('error', 'Semua 12 bulan bayaran untuk kitaran semasa telah lengkap.');
                }

                $period = $request->payment_period ?: now()->format('Y-m');
                [$year, $month] = $this->extractYearMonth($period);

                if ($this->monthlyPaymentExists($user->id, $year, $month)) {
                    return redirect()->route('user.payments.index')
                        ->with('error', 'Bayaran bagi bulan ini telah direkodkan.');
                }

                if (!$this->isAllowedMonthlyPeriod($user->id, $year, $month)) {
                    return redirect()->route('user.payments.index')
                        ->with('error', 'Bayaran bulanan hanya dibenarkan untuk bulan seterusnya yang belum dibayar.');
                }

                $payment = $this->createPaymentWithItems([
                    'user_id' => $user->id,
                    'payment_plan' => 'monthly',
                    'payment_type' => 'monthly',
                    'amount' => self::MONTHLY_FEE,
                    'payment_period' => sprintf('%04d-%02d', $year, $month),
                    'membership_year' => $year,
                    'paid_month' => $month,
                    'notes' => 'Bayaran bulanan',
                    'items' => [
                        [
                            'payment_type' => 'monthly',
                            'amount' => self::MONTHLY_FEE,
                            'payment_period' => sprintf('%04d-%02d', $year, $month),
                            'membership_year' => $year,
                            'paid_month' => $month,
                            'notes' => 'Yuran bulanan',
                        ],
                    ],
                ]);

                return redirect()->route('payment.billplz', $payment->id);
            }

            if ($action === 'monthly_arrears') {
                if ($summary['registration_balance'] > 0) {
                    return redirect()->route('user.payments.index')
                        ->with('error', 'Sila buat bayaran pertama dahulu sebelum membayar tunggakan.');
                }

                $periods = $summary['unpaid_due_periods'] ?? [];

                if (count($periods) <= 0) {
                    return redirect()->route('user.payments.index')
                        ->with('error', 'Tiada tunggakan sehingga bulan semasa.');
                }

                $items = collect($periods)->map(function ($period) {
                    return [
                        'payment_type' => 'monthly',
                        'amount' => self::MONTHLY_FEE,
                        'payment_period' => $period['period'],
                        'membership_year' => $period['year'],
                        'paid_month' => $period['month'],
                        'notes' => 'Bayaran tunggakan bulanan',
                    ];
                })->toArray();

                $firstPeriod = $periods[0]['period'];
                $lastPeriod = $periods[count($periods) - 1]['period'];

                $payment = $this->createPaymentWithItems([
                    'user_id' => $user->id,
                    'payment_plan' => 'monthly',
                    'payment_type' => 'monthly_arrears',
                    'amount' => count($periods) * self::MONTHLY_FEE,
                    'payment_period' => $firstPeriod . '_to_' . $lastPeriod,
                    'membership_year' => now()->year,
                    'paid_month' => null,
                    'notes' => 'Bayaran tunggakan bulanan',
                    'items' => $items,
                ]);

                return redirect()->route('payment.billplz', $payment->id);
            }

            if ($action === 'monthly_balance') {
                if ($summary['registration_balance'] > 0) {
                    return redirect()->route('user.payments.index')
                        ->with('error', 'Sila buat bayaran pertama dahulu sebelum membayar baki kitaran.');
                }

                $periods = $summary['remaining_cycle_periods'] ?? [];

                if (count($periods) <= 0) {
                    return redirect()->route('user.payments.index')
                        ->with('error', 'Semua bayaran dalam kitaran semasa telah lengkap.');
                }

                $items = collect($periods)->map(function ($period) {
                    return [
                        'payment_type' => 'monthly',
                        'amount' => self::MONTHLY_FEE,
                        'payment_period' => $period['period'],
                        'membership_year' => $period['year'],
                        'paid_month' => $period['month'],
                        'notes' => 'Bayaran baki kitaran bulanan',
                    ];
                })->toArray();

                $firstPeriod = $periods[0]['period'];
                $lastPeriod = $periods[count($periods) - 1]['period'];

                $payment = $this->createPaymentWithItems([
                    'user_id' => $user->id,
                    'payment_plan' => 'monthly',
                    'payment_type' => 'monthly_balance',
                    'amount' => count($periods) * self::MONTHLY_FEE,
                    'payment_period' => $firstPeriod . '_to_' . $lastPeriod,
                    'membership_year' => now()->year,
                    'paid_month' => null,
                    'notes' => 'Bayaran baki kitaran bulanan',
                    'items' => $items,
                ]);

                return redirect()->route('payment.billplz', $payment->id);
            }


            return redirect()->route('user.payments.index')
                ->with('error', 'Tindakan bayaran tidak sah untuk pelan bulanan.');
        }

        if ($plan === 'yearly') {
            if ($action === 'monthly_payment') {
                return redirect()->route('user.payments.index')
                    ->with('error', 'Pelan tahunan tidak menggunakan bayaran bulanan.');
            }

            if ($action === 'first_payment') {
                if ($summary['registration_paid'] > 0 || $summary['annual_paid_this_year'] > 0) {
                    return redirect()->route('user.payments.index')
                        ->with('error', 'Bayaran pertama tahunan telah pun dibuat.');
                }

                $payment = $this->createPaymentWithItems([
                    'user_id' => $user->id,
                    'payment_plan' => 'yearly',
                    'payment_type' => 'first_yearly',
                    'amount' => self::REGISTRATION_FEE + self::YEARLY_FEE,
                    'payment_period' => (string) $currentYear,
                    'membership_year' => $currentYear,
                    'paid_month' => null,
                    'notes' => 'Bayaran pertama tahunan',
                    'items' => [
                        [
                            'payment_type' => 'registration',
                            'amount' => self::REGISTRATION_FEE,
                            'payment_period' => null,
                            'membership_year' => $currentYear,
                            'paid_month' => null,
                            'notes' => 'Yuran pendaftaran',
                        ],
                        [
                            'payment_type' => 'yearly',
                            'amount' => self::YEARLY_FEE,
                            'payment_period' => (string) $currentYear,
                            'membership_year' => $currentYear,
                            'paid_month' => null,
                            'notes' => 'Yuran tahunan',
                        ],
                    ],
                ]);

                return redirect()->route('payment.billplz', $payment->id);
            }

            if ($action === 'annual_payment') {
                if ($summary['registration_balance'] > 0) {
                    return redirect()->route('user.payments.index')
                        ->with('error', 'Sila buat bayaran pertama dahulu (pendaftaran + tahunan).');
                }

                if ($summary['annual_balance'] <= 0) {
                    return redirect()->route('user.payments.index')
                        ->with('error', 'Bayaran tahunan untuk tahun ini telah dijelaskan.');
                }

                $payment = $this->createPaymentWithItems([
                    'user_id' => $user->id,
                    'payment_plan' => 'yearly',
                    'payment_type' => 'yearly',
                    'amount' => $summary['annual_balance'],
                    'payment_period' => (string) $currentYear,
                    'membership_year' => $currentYear,
                    'paid_month' => null,
                    'notes' => 'Bayaran tahunan',
                    'items' => [
                        [
                            'payment_type' => 'yearly',
                            'amount' => $summary['annual_balance'],
                            'payment_period' => (string) $currentYear,
                            'membership_year' => $currentYear,
                            'paid_month' => null,
                            'notes' => 'Yuran tahunan',
                        ],
                    ],
                ]);

                return redirect()->route('payment.billplz', $payment->id);
            }

            return redirect()->route('user.payments.index')
                ->with('error', 'Tindakan bayaran tidak sah untuk pelan tahunan.');
        }

        return redirect()->route('user.payments.index')
            ->with('error', 'Pelan bayaran tidak sah.');
    }

    public function receipt(Payment $payment)
    {
        if ($payment->user_id !== auth()->id()) {
            abort(403);
        }

        $payment->load('items');

        return view('user.payments.receipt', compact('payment'));
    }

    private function getPaymentSummary($userId, $plan)
    {
        $currentYear = now()->year;
        $currentMonth = now()->month;

        $paidPaymentIds = Payment::where('user_id', $userId)
            ->where('status', 'paid')
            ->pluck('id');

        $registrationPaid = PaymentItem::whereIn('payment_id', $paidPaymentIds)
            ->where('payment_type', 'registration')
            ->sum('amount');

        $monthlyPayments = PaymentItem::whereIn('payment_id', $paidPaymentIds)
            ->where('payment_type', 'monthly')
            ->orderBy('membership_year')
            ->orderBy('paid_month')
            ->get();

        $monthlyPaidCount = $monthlyPayments->count();

        $annualPaidThisYear = PaymentItem::whereIn('payment_id', $paidPaymentIds)
            ->where('payment_type', 'yearly')
            ->where('membership_year', $currentYear)
            ->sum('amount');

        $registrationBalance = max(0, self::REGISTRATION_FEE - $registrationPaid);
        $annualBalance = max(0, self::YEARLY_FEE - $annualPaidThisYear);

        $firstMonthlyPayment = $monthlyPayments->first();
        $latestMonthlyPayment = $monthlyPayments->last();

        /*
        |--------------------------------------------------------------------------
        | Kitaran Bulanan 12 Bulan
        |--------------------------------------------------------------------------
        | Contoh:
        | Bayaran pertama April 2026
        | Maka kitaran = April 2026 hingga Mac 2027
        */
        $cycleStart = $firstMonthlyPayment
            ? Carbon::createFromDate(
                $firstMonthlyPayment->membership_year,
                $firstMonthlyPayment->paid_month,
                1
            )->startOfMonth()
            : now()->startOfMonth();

        $cycleEnd = $cycleStart->copy()->addMonths(11)->startOfMonth();

        $membershipStartPeriod = $cycleStart->format('Y-m');
        $membershipEndPeriod = $cycleEnd->format('Y-m');

        $currentDate = now()->startOfMonth();

        /*
        |--------------------------------------------------------------------------
        | Senarai bulan yang sudah dibayar
        |--------------------------------------------------------------------------
        */
        $paidPeriods = $monthlyPayments
            ->map(fn ($payment) => sprintf('%04d-%02d', $payment->membership_year, $payment->paid_month))
            ->values()
            ->toArray();

        /*
        |--------------------------------------------------------------------------
        | Semua bulan dalam kitaran 12 bulan
        |--------------------------------------------------------------------------
        */
        $allCyclePeriods = [];
        $cursor = $cycleStart->copy();

        while ($cursor->lessThanOrEqualTo($cycleEnd)) {
            $allCyclePeriods[] = [
                'period' => $cursor->format('Y-m'),
                'label' => $cursor->translatedFormat('F Y'),
                'year' => (int) $cursor->format('Y'),
                'month' => (int) $cursor->format('m'),
                'amount' => self::MONTHLY_FEE,
            ];

            $cursor->addMonth();
        }

        /*
        |--------------------------------------------------------------------------
        | Bulan yang sudah patut dibayar sampai bulan semasa
        |--------------------------------------------------------------------------
        | Kalau sekarang Oktober, due periods ialah dari bulan mula sampai Oktober.
        | Tapi kalau kitaran tamat sebelum Oktober, berhenti pada cycleEnd.
        */
        $duePeriods = collect($allCyclePeriods)
            ->filter(function ($item) use ($currentDate) {
                return Carbon::createFromFormat('Y-m', $item['period'])
                    ->startOfMonth()
                    ->lessThanOrEqualTo($currentDate);
            })
            ->values()
            ->toArray();

        /*
        |--------------------------------------------------------------------------
        | Tunggakan sampai bulan semasa
        |--------------------------------------------------------------------------
        | Contoh:
        | Sudah bayar April dan Mei.
        | Sekarang Oktober.
        | Maka unpaid_due_periods = Jun, Julai, Ogos, September, Oktober.
        */
        $unpaidDuePeriods = collect($duePeriods)
            ->reject(fn ($item) => in_array($item['period'], $paidPeriods))
            ->values()
            ->toArray();

        /*
        |--------------------------------------------------------------------------
        | Semua baki kitaran sampai cukup 12 bulan
        |--------------------------------------------------------------------------
        | Contoh:
        | Kitaran April 2026 - Mac 2027.
        | Sudah bayar April.
        | Maka remaining_cycle_periods = Mei 2026 - Mac 2027.
        */
        $remainingCyclePeriods = collect($allCyclePeriods)
            ->reject(fn ($item) => in_array($item['period'], $paidPeriods))
            ->values()
            ->toArray();

        $monthlyRemainingCount = count($remainingCyclePeriods);
        $monthlyOutstanding = $monthlyRemainingCount * self::MONTHLY_FEE;

        $arrearsAmount = count($unpaidDuePeriods) * self::MONTHLY_FEE;
        $remainingCycleAmount = count($remainingCyclePeriods) * self::MONTHLY_FEE;

        $totalOutstanding = $plan === 'monthly'
            ? ($registrationBalance + $monthlyOutstanding)
            : ($registrationBalance + $annualBalance);

        /*
        |--------------------------------------------------------------------------
        | Bayaran bulan seterusnya
        |--------------------------------------------------------------------------
        | Untuk pilihan "Bayar Bulan Seterusnya" sahaja.
        | Dia ambil baki pertama yang belum dibayar dalam kitaran.
        */
        $nextMonthlyPeriod = null;
        $canPayMonthlyNow = false;

        if (count($remainingCyclePeriods) > 0) {
            $nextMonthlyPeriod = $remainingCyclePeriods[0]['period'];

            $nextMonthlyDate = Carbon::createFromFormat('Y-m', $nextMonthlyPeriod)->startOfMonth();

            // Boleh bayar jika bulan itu sudah sampai atau bulan semasa.
            // Kalau belum sampai, dia masih boleh dibayar melalui "Bayar Baki Kitaran",
            // bukan melalui "Bayar Bulan Seterusnya".
            $canPayMonthlyNow = $nextMonthlyDate->lessThanOrEqualTo($currentDate);
        } else {
            $nextMonthlyPeriod = $currentDate->format('Y-m');
            $canPayMonthlyNow = false;
        }

        /*
        |--------------------------------------------------------------------------
        | Jadual paparan bulanan
        |--------------------------------------------------------------------------
        */
        $schedulePeriods = $allCyclePeriods;

        return [
            'plan' => $plan,
            'registration_paid' => $registrationPaid,
            'registration_balance' => $registrationBalance,

            'annual_paid_this_year' => $annualPaidThisYear,
            'annual_balance' => $annualBalance,
            'is_fully_paid_yearly' => $annualPaidThisYear >= self::YEARLY_FEE,

            'first_monthly_total' => self::REGISTRATION_FEE + self::MONTHLY_FEE,
            'first_yearly_total' => self::REGISTRATION_FEE + self::YEARLY_FEE,

            'current_year' => $currentYear,
            'current_month' => $currentMonth,

            'membership_start_period' => $membershipStartPeriod,
            'membership_end_period' => $membershipEndPeriod,

            'cycle_start_period' => $membershipStartPeriod,
            'cycle_end_period' => $membershipEndPeriod,

            'schedule_periods' => $schedulePeriods,
            'all_cycle_periods' => $allCyclePeriods,

            'paid_periods' => $paidPeriods,

            'due_periods' => $duePeriods,
            'unpaid_due_periods' => $unpaidDuePeriods,
            'remaining_cycle_periods' => $remainingCyclePeriods,

            'arrears_amount' => $arrearsAmount,
            'remaining_cycle_amount' => $remainingCycleAmount,

            'monthly_paid_count' => $monthlyPaidCount,
            'monthly_remaining_count' => $monthlyRemainingCount,
            'monthly_outstanding' => $monthlyOutstanding,

            'total_outstanding' => $totalOutstanding,

            'next_monthly_period' => $nextMonthlyPeriod,
            'can_pay_monthly_now' => $canPayMonthlyNow,
        ];
    }

    private function buildRollingMonthlySchedule(string $startPeriod, int $months = 12): array
    {
        $startDate = Carbon::createFromFormat('Y-m', $startPeriod)->startOfMonth();
        $periods = [];

        for ($i = 0; $i < $months; $i++) {
            $date = $startDate->copy()->addMonths($i);

            $periods[] = [
                'period' => $date->format('Y-m'),
                'year' => (int) $date->format('Y'),
                'month' => (int) $date->format('m'),
                'label' => $date->translatedFormat('F Y'),
                'amount' => self::MONTHLY_FEE,
            ];
        }

        return $periods;
    }

    private function createPaymentWithItems(array $data): Payment
    {
        return DB::transaction(function () use ($data) {
            $timestamp = now()->format('YmdHis');

            $payment = Payment::create([
                'user_id' => $data['user_id'],
                'payment_plan' => $data['payment_plan'],
                'payment_type' => $data['payment_type'],
                'amount' => $data['amount'],
                'payment_period' => $data['payment_period'],
                'membership_year' => $data['membership_year'],
                'paid_month' => $data['paid_month'],
                'status' => 'pending',
                'paid_at' => null,
                'payment_method' => 'Billplz',
                'reference_no' => 'BILLPLZ-' . $timestamp,
                'receipt_no' => null,
                'notes' => $data['notes'],
            ]);

            foreach ($data['items'] as $item) {
                $payment->items()->create([
                    'payment_type' => $item['payment_type'],
                    'amount' => $item['amount'],
                    'membership_year' => $item['membership_year'],
                    'paid_month' => $item['paid_month'],
                    'payment_period' => $item['payment_period'],
                    'notes' => $item['notes'],
                ]);
            }

            return $payment;
        });
    }

    private function monthlyPaymentExists($userId, $year, $month): bool
    {
        $paidPaymentIds = Payment::where('user_id', $userId)
            ->where('status', 'paid')
            ->pluck('id');

        return PaymentItem::whereIn('payment_id', $paidPaymentIds)
            ->where('payment_type', 'monthly')
            ->where('membership_year', $year)
            ->where('paid_month', $month)
            ->exists();
    }

    private function isAllowedMonthlyPeriod($userId, $year, $month): bool
    {
        $currentDate = now()->startOfMonth();

        $requestedDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();

        if ($requestedDate->gt($currentDate)) {
            return false;
        }

        $paidPaymentIds = Payment::where('user_id', $userId)
            ->where('status', 'paid')
            ->pluck('id');

        $latestMonthlyPayment = PaymentItem::whereIn('payment_id', $paidPaymentIds)
            ->where('payment_type', 'monthly')
            ->orderByDesc('membership_year')
            ->orderByDesc('paid_month')
            ->first();

        if (!$latestMonthlyPayment) {
            return $requestedDate->equalTo($currentDate);
        }

        $expectedDate = Carbon::createFromDate(
            $latestMonthlyPayment->membership_year,
            $latestMonthlyPayment->paid_month,
            1
        )->addMonth()->startOfMonth();

        return $requestedDate->equalTo($expectedDate)
            && $requestedDate->lessThanOrEqualTo($currentDate);
    }

    private function extractYearMonth(string $period): array
    {
        if (!preg_match('/^\d{4}-\d{2}$/', $period)) {
            abort(422, 'Format payment period bulanan mesti YYYY-MM.');
        }

        [$year, $month] = explode('-', $period);

        $year = (int) $year;
        $month = (int) $month;

        if ($month < 1 || $month > 12) {
            abort(422, 'Bulan tidak sah.');
        }

        return [$year, $month];
    }

    private function normalizePlan($plan): string
    {
        return match ($plan) {
            'bulanan', 'monthly' => 'monthly',
            'tahunan', 'yearly', 'annual' => 'yearly',
            default => 'monthly',
        };
    }
}