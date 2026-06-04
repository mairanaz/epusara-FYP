<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\PaymentItem;
use App\Models\User;
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

    
    public function receipt(Payment $payment)
    {
        if ($payment->user_id !== auth()->id()) {
            abort(403);
        }

        $payment->load('items');

        return view('user.payments.receipt', compact('payment'));
    }

    private function getMembershipStartDate(User $user): Carbon
    {
        $profile = $user->profile;

        $startDate = $profile?->tarikh_permohonan
            ?? $profile?->created_at
            ?? $user->created_at
            ?? now();

        return Carbon::parse($startDate)->startOfMonth();
    }

    private function getCurrentMembershipCycle(User $user): array
    {
        $cycleStart = $this->getMembershipStartDate($user);
        $today = now()->startOfMonth();

        while ($cycleStart->copy()->addYear()->lessThanOrEqualTo($today)) {
            $cycleStart->addYear();
        }

        $cycleEnd = $cycleStart->copy()->addYear()->subDay();

        return [
            'start' => $cycleStart,
            'end' => $cycleEnd,
        ];
    }

    private function generateCycleMonths(Carbon $cycleStart, Carbon $cycleEnd): array
    {
        $months = [];
        $cursor = $cycleStart->copy()->startOfMonth();

        while ($cursor->lessThanOrEqualTo($cycleEnd)) {
            $months[] = [
                'period' => $cursor->format('Y-m'),
                'label' => $cursor->translatedFormat('F Y'),
                'year' => (int) $cursor->format('Y'),
                'month' => (int) $cursor->format('m'),
                'amount' => self::MONTHLY_FEE,
                'billing_month' => $cursor->copy()->startOfMonth()->toDateString(),
            ];

            $cursor->addMonth();
        }

        return $months;
    }

    private function getPaymentSummary($userId, $plan)
    {
        $user = User::with('profile')->findOrFail($userId);

        $cycle = $this->getCurrentMembershipCycle($user);
        $cycleStart = $cycle['start'];
        $cycleEnd = $cycle['end'];

        $currentYear = now()->year;
        $currentMonth = now()->month;
        $currentDate = now()->startOfMonth();

        $paidPaymentIds = Payment::where('user_id', $userId)
            ->where('status', 'paid')
            ->pluck('id');

        /*
        |--------------------------------------------------------------------------
        | Yuran Pendaftaran
        |--------------------------------------------------------------------------
        */
        $registrationPaid = PaymentItem::whereIn('payment_id', $paidPaymentIds)
            ->where('payment_type', 'registration')
            ->sum('amount');

        $registrationBalance = max(0, self::REGISTRATION_FEE - $registrationPaid);

        /*
        |--------------------------------------------------------------------------
        | Kitaran Semasa 12 Bulan Berdasarkan Tarikh Permohonan
        |--------------------------------------------------------------------------
        */
        $allCyclePeriods = $this->generateCycleMonths($cycleStart, $cycleEnd);

        $cyclePeriodKeys = collect($allCyclePeriods)
            ->pluck('period')
            ->toArray();

        /*
        |--------------------------------------------------------------------------
        | Bayaran Bulanan Dalam Kitaran Semasa
        |--------------------------------------------------------------------------
        */
        $monthlyPayments = PaymentItem::whereIn('payment_id', $paidPaymentIds)
            ->where('payment_type', 'monthly')
            ->whereIn('payment_period', $cyclePeriodKeys)
            ->orderBy('billing_month')
            ->orderBy('membership_year')
            ->orderBy('paid_month')
            ->get();

        $paidPeriods = $monthlyPayments
            ->pluck('payment_period')
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        $monthlyPaidCount = count($paidPeriods);

        /*
        |--------------------------------------------------------------------------
        | Bulan Yang Sepatutnya Sudah Dibayar Sampai Bulan Semasa
        |--------------------------------------------------------------------------
        */
        $duePeriods = collect($allCyclePeriods)
            ->filter(function ($item) use ($currentDate) {
                return Carbon::createFromFormat('Y-m', $item['period'])
                    ->startOfMonth()
                    ->lessThanOrEqualTo($currentDate);
            })
            ->values()
            ->toArray();

        $unpaidDuePeriods = collect($duePeriods)
            ->reject(fn ($item) => in_array($item['period'], $paidPeriods))
            ->values()
            ->toArray();

        /*
        |--------------------------------------------------------------------------
        | Semua Baki Dalam Kitaran Semasa
        |--------------------------------------------------------------------------
        */
        $remainingCyclePeriods = collect($allCyclePeriods)
            ->reject(fn ($item) => in_array($item['period'], $paidPeriods))
            ->values()
            ->toArray();

        $monthlyRemainingCount = count($remainingCyclePeriods);
        $monthlyOutstanding = $monthlyRemainingCount * self::MONTHLY_FEE;

        $arrearsAmount = count($unpaidDuePeriods) * self::MONTHLY_FEE;
        $remainingCycleAmount = count($remainingCyclePeriods) * self::MONTHLY_FEE;

        /*
        |--------------------------------------------------------------------------
        | Bayaran Tahunan Dalam Kitaran Semasa
        |--------------------------------------------------------------------------
        */
        $yearlyPeriod = $cycleStart->format('Y-m') . '_to_' . $cycleEnd->format('Y-m');

        $annualPaidThisYear = PaymentItem::whereIn('payment_id', $paidPaymentIds)
            ->where('payment_type', 'yearly')
            ->where('payment_period', $yearlyPeriod)
            ->sum('amount');

        $annualBalance = max(0, self::YEARLY_FEE - $annualPaidThisYear);

        /*
        |--------------------------------------------------------------------------
        | Total Outstanding
        |--------------------------------------------------------------------------
        */
        $totalOutstanding = $plan === 'monthly'
            ? ($registrationBalance + $monthlyOutstanding)
            : ($registrationBalance + $annualBalance);

        /*
        |--------------------------------------------------------------------------
        | Bayaran Bulanan Seterusnya
        |--------------------------------------------------------------------------
        */
        $nextMonthlyPeriod = null;
        $canPayMonthlyNow = false;

        if (count($remainingCyclePeriods) > 0) {
            $nextMonthlyPeriod = $remainingCyclePeriods[0]['period'];

            $nextMonthlyDate = Carbon::createFromFormat('Y-m', $nextMonthlyPeriod)
                ->startOfMonth();

            $canPayMonthlyNow = $nextMonthlyDate->lessThanOrEqualTo($currentDate);
        }

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

            'membership_start_period' => $cycleStart->format('Y-m'),
            'membership_end_period' => $cycleEnd->format('Y-m'),

            'cycle_start_period' => $cycleStart->format('Y-m'),
            'cycle_end_period' => $cycleEnd->format('Y-m'),

            'cycle_start_date' => $cycleStart->toDateString(),
            'cycle_end_date' => $cycleEnd->toDateString(),

            'yearly_period' => $yearlyPeriod,

            'schedule_periods' => $allCyclePeriods,
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
                    'membership_year' => $item['membership_year'] ?? null,
                    'paid_month' => $item['paid_month'] ?? null,
                    'billing_month' => $item['billing_month'] ?? null,
                    'cycle_start' => $item['cycle_start'] ?? null,
                    'cycle_end' => $item['cycle_end'] ?? null,
                    'payment_period' => $item['payment_period'] ?? null,
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            return $payment;
        });
    }

    private function monthlyPaymentExists($userId, $year, $month): bool
    {
        $period = sprintf('%04d-%02d', $year, $month);

        $paidPaymentIds = Payment::where('user_id', $userId)
            ->where('status', 'paid')
            ->pluck('id');

        return PaymentItem::whereIn('payment_id', $paidPaymentIds)
            ->where('payment_type', 'monthly')
            ->where('payment_period', $period)
            ->exists();
    }

    private function isAllowedMonthlyPeriod($userId, $year, $month): bool
    {
        $user = User::with('profile')->findOrFail($userId);

        $plan = $this->normalizePlan($user->profile?->payment_plan);
        $summary = $this->getPaymentSummary($userId, $plan);

        $requestedPeriod = sprintf('%04d-%02d', $year, $month);

        $remainingPeriods = collect($summary['remaining_cycle_periods'] ?? [])
            ->pluck('period')
            ->toArray();

        if (!in_array($requestedPeriod, $remainingPeriods)) {
            return false;
        }

        $nextMonthlyPeriod = $summary['next_monthly_period'] ?? null;

        return $requestedPeriod === $nextMonthlyPeriod;
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

    public function store(Request $request)
    {
        $request->validate([
            'payment_action' => 'required|string',
        ]);

        $user = auth()->user()->load('profile');

        if (!$user->profile) {
            return redirect()
                ->route('user.payments.index')
                ->with('error', 'Profil pengguna tidak dijumpai.');
        }

        $plan = $this->normalizePlan($user->profile->payment_plan);
        $action = $request->payment_action;

        $summary = $this->getPaymentSummary($user->id, $plan);

        $cycleStartDate = Carbon::parse($summary['cycle_start_date']);
        $cycleEndDate = Carbon::parse($summary['cycle_end_date']);
        $yearlyPeriod = $summary['yearly_period'];

        if ($plan === 'monthly' && $action === 'monthly_custom_amount') {
            $request->validate([
                'custom_amount' => [
                    'required',
                    'numeric',
                    'min:' . self::MONTHLY_FEE,
                ],
            ], [
                'custom_amount.required' => 'Sila masukkan jumlah yang ingin dibayar.',
                'custom_amount.numeric' => 'Jumlah bayaran mestilah dalam bentuk nombor.',
                'custom_amount.min' => 'Jumlah minimum yang boleh dibayar ialah RM10.00.',
            ]);
        }


        try {
            /*
            |--------------------------------------------------------------------------
            | PELAN BULANAN
            |--------------------------------------------------------------------------
            */
            if ($plan === 'monthly') {

                /*
                |--------------------------------------------------------------------------
                | Bayaran Pertama Bulanan
                |--------------------------------------------------------------------------
                */
                if ($action === 'first_payment') {

                    if (($summary['registration_balance'] ?? 0) <= 0) {
                        return redirect()
                            ->route('user.payments.index')
                            ->with('error', 'Yuran pendaftaran telah dijelaskan.');
                    }

                    $nextMonthlyPeriod = $summary['next_monthly_period'] ?? null;

                    if (!$nextMonthlyPeriod) {
                        return redirect()
                            ->route('user.payments.index')
                            ->with('error', 'Tiada bulan bayaran yang perlu dijelaskan.');
                    }

                    [$year, $month] = $this->extractYearMonth($nextMonthlyPeriod);

                    if (!$this->isAllowedMonthlyPeriod($user->id, $year, $month)) {
                        return redirect()
                            ->route('user.payments.index')
                            ->with('error', 'Tempoh bayaran bulanan tidak sah.');
                    }

                    if ($this->monthlyPaymentExists($user->id, $year, $month)) {
                        return redirect()
                            ->route('user.payments.index')
                            ->with('error', 'Bayaran untuk bulan ini telah dijelaskan.');
                    }

                    $paymentPeriod = sprintf('%04d-%02d', $year, $month);
                    $billingMonth = Carbon::createFromDate($year, $month, 1)->startOfMonth();

                    $payment = $this->createPaymentWithItems([
                        'user_id' => $user->id,
                        'payment_plan' => 'monthly',
                        'payment_type' => 'first_monthly',
                        'amount' => self::REGISTRATION_FEE + self::MONTHLY_FEE,
                        'payment_period' => $paymentPeriod,
                        'membership_year' => $year,
                        'paid_month' => $month,
                        'notes' => 'Bayaran pertama bulanan',
                        'items' => [
                            [
                                'payment_type' => 'registration',
                                'amount' => self::REGISTRATION_FEE,
                                'payment_period' => null,
                                'membership_year' => null,
                                'paid_month' => null,
                                'billing_month' => null,
                                'cycle_start' => $cycleStartDate->toDateString(),
                                'cycle_end' => $cycleEndDate->toDateString(),
                                'notes' => 'Yuran pendaftaran',
                            ],
                            [
                                'payment_type' => 'monthly',
                                'amount' => self::MONTHLY_FEE,
                                'payment_period' => $paymentPeriod,
                                'membership_year' => $year,
                                'paid_month' => $month,
                                'billing_month' => $billingMonth->toDateString(),
                                'cycle_start' => $cycleStartDate->toDateString(),
                                'cycle_end' => $cycleEndDate->toDateString(),
                                'notes' => 'Yuran bulan pertama',
                            ],
                        ],
                    ]);

                    return redirect()->route('payment.billplz', $payment->id);
                }

                /*
                |--------------------------------------------------------------------------
                | Bayaran Bulanan Biasa
                |--------------------------------------------------------------------------
                */
                if ($action === 'monthly_payment') {

                    if (($summary['registration_balance'] ?? 0) > 0) {
                        return redirect()
                            ->route('user.payments.index')
                            ->with('error', 'Sila jelaskan yuran pendaftaran terlebih dahulu.');
                    }

                    $nextMonthlyPeriod = $summary['next_monthly_period'] ?? null;

                    if (!$nextMonthlyPeriod) {
                        return redirect()
                            ->route('user.payments.index')
                            ->with('error', 'Semua bayaran bulanan untuk kitaran semasa telah dijelaskan.');
                    }

                    [$year, $month] = $this->extractYearMonth($nextMonthlyPeriod);

                    if (!$this->isAllowedMonthlyPeriod($user->id, $year, $month)) {
                        return redirect()
                            ->route('user.payments.index')
                            ->with('error', 'Tempoh bayaran bulanan tidak sah.');
                    }

                    if ($this->monthlyPaymentExists($user->id, $year, $month)) {
                        return redirect()
                            ->route('user.payments.index')
                            ->with('error', 'Bayaran untuk bulan ini telah dijelaskan.');
                    }

                    $paymentPeriod = sprintf('%04d-%02d', $year, $month);
                    $billingMonth = Carbon::createFromDate($year, $month, 1)->startOfMonth();

                    $payment = $this->createPaymentWithItems([
                        'user_id' => $user->id,
                        'payment_plan' => 'monthly',
                        'payment_type' => 'monthly',
                        'amount' => self::MONTHLY_FEE,
                        'payment_period' => $paymentPeriod,
                        'membership_year' => $year,
                        'paid_month' => $month,
                        'notes' => 'Bayaran bulanan',
                        'items' => [
                            [
                                'payment_type' => 'monthly',
                                'amount' => self::MONTHLY_FEE,
                                'payment_period' => $paymentPeriod,
                                'membership_year' => $year,
                                'paid_month' => $month,
                                'billing_month' => $billingMonth->toDateString(),
                                'cycle_start' => $cycleStartDate->toDateString(),
                                'cycle_end' => $cycleEndDate->toDateString(),
                                'notes' => 'Yuran bulanan',
                            ],
                        ],
                    ]);

                    return redirect()->route('payment.billplz', $payment->id);
                }

                /*
                |--------------------------------------------------------------------------
                | Bayar Beberapa Bulan
                |--------------------------------------------------------------------------
                */
                if ($action === 'monthly_custom_amount') {

                    if (($summary['registration_balance'] ?? 0) > 0) {
                        return redirect()
                            ->route('user.payments.index')
                            ->with('error', 'Sila jelaskan yuran pendaftaran terlebih dahulu.');
                    }

                    

                    /*
                    |--------------------------------------------------------------------------
                    | Tukarkan amaun kepada sen untuk semakan yang lebih tepat
                    |--------------------------------------------------------------------------
                    | Contoh:
                    | RM20.00 = 2000 sen
                    | RM10.00 = 1000 sen
                    */
                    $customAmount = (float) $request->custom_amount;
                    $customAmountInCents = (int) round($customAmount * 100);
                    $monthlyFeeInCents = (int) round(self::MONTHLY_FEE * 100);

                    /*
                    |--------------------------------------------------------------------------
                    | Amaun mesti gandaan RM10
                    |--------------------------------------------------------------------------
                    */
                    if ($customAmountInCents % $monthlyFeeInCents !== 0) {
                        return redirect()
                            ->back()
                            ->withInput()
                            ->with('error', 'Sila masukkan jumlah seperti RM10, RM20, RM30 atau seterusnya.');
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Dapatkan bulan yang masih belum dibayar
                    |--------------------------------------------------------------------------
                    */
                    $remainingPeriods = $summary['remaining_cycle_periods'] ?? [];
                    $remainingAmount = (float) ($summary['remaining_cycle_amount'] ?? 0);

                    if (count($remainingPeriods) <= 0) {
                        return redirect()
                            ->route('user.payments.index')
                            ->with('error', 'Semua bayaran bulanan untuk kitaran semasa telah dijelaskan.');
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Amaun tidak boleh melebihi semua baki yang belum dibayar
                    |--------------------------------------------------------------------------
                    */
                    if ($customAmount > $remainingAmount) {
                        return redirect()
                            ->back()
                            ->withInput()
                            ->with('error', 'Jumlah yang dimasukkan melebihi baki yang belum dibayar.');
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Kira bilangan bulan berdasarkan amaun
                    |--------------------------------------------------------------------------
                    | Contoh:
                    | RM20 / RM10 = 2 bulan
                    | RM30 / RM10 = 3 bulan
                    */
                    $numberOfMonths = intdiv($customAmountInCents, $monthlyFeeInCents);

                    /*
                    |--------------------------------------------------------------------------
                    | Ambil bulan terawal yang masih belum dibayar
                    |--------------------------------------------------------------------------
                    | Contoh RM20:
                    | June 2026 dan July 2026
                    */
                    $periods = array_slice($remainingPeriods, 0, $numberOfMonths);

                    if (count($periods) !== $numberOfMonths) {
                        return redirect()
                            ->back()
                            ->withInput()
                            ->with('error', 'Bayaran ini melebihi bilangan bulan yang masih belum dibayar.');
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Sediakan rekod pecahan bayaran mengikut bulan
                    |--------------------------------------------------------------------------
                    */
                    $items = collect($periods)->map(function ($period) use ($cycleStartDate, $cycleEndDate) {
                        $billingMonth = Carbon::createFromDate(
                            $period['year'],
                            $period['month'],
                            1
                        )->startOfMonth();

                        return [
                            'payment_type' => 'monthly',
                            'amount' => self::MONTHLY_FEE,
                            'payment_period' => $period['period'],
                            'membership_year' => $period['year'],
                            'paid_month' => $period['month'],
                            'billing_month' => $billingMonth->toDateString(),
                            'cycle_start' => $cycleStartDate->toDateString(),
                            'cycle_end' => $cycleEndDate->toDateString(),
                            'notes' => 'Bayaran ikut jumlah',
                        ];
                    })->toArray();

                    $firstPeriod = $periods[0]['period'];
                    $lastPeriod = $periods[count($periods) - 1]['period'];

                    [$firstYear, $firstMonth] = $this->extractYearMonth($firstPeriod);

                    /*
                    |--------------------------------------------------------------------------
                    | Cipta bayaran utama untuk dihantar ke Billplz
                    |--------------------------------------------------------------------------
                    | payment_type menggunakan "monthly" supaya tidak perlu tambah
                    | jenis baharu dalam database.
                    |--------------------------------------------------------------------------
                    */
                    $payment = $this->createPaymentWithItems([
                        'user_id' => $user->id,
                        'payment_plan' => 'monthly',
                        'payment_type' => 'monthly',
                        'amount' => $customAmount,
                        'payment_period' => $firstPeriod . '_to_' . $lastPeriod,
                        'membership_year' => $firstYear,
                        'paid_month' => $firstMonth,
                        'notes' => 'Bayaran ikut jumlah sebanyak RM' . number_format($customAmount, 2),
                        'items' => $items,
                    ]);

                    return redirect()->route('payment.billplz', $payment->id);
                }

                /*
                |--------------------------------------------------------------------------
                | Bayaran Tunggakan Bulanan
                |--------------------------------------------------------------------------
                */
                if ($action === 'monthly_arrears') {

                    if (($summary['registration_balance'] ?? 0) > 0) {
                        return redirect()
                            ->route('user.payments.index')
                            ->with('error', 'Sila jelaskan yuran pendaftaran terlebih dahulu.');
                    }

                    $periods = $summary['unpaid_due_periods'] ?? [];

                    if (count($periods) <= 0) {
                        return redirect()
                            ->route('user.payments.index')
                            ->with('error', 'Tiada tunggakan bulanan untuk dijelaskan.');
                    }

                    $items = collect($periods)->map(function ($period) use ($cycleStartDate, $cycleEndDate) {
                        $billingMonth = Carbon::createFromDate($period['year'], $period['month'], 1)->startOfMonth();

                        return [
                            'payment_type' => 'monthly',
                            'amount' => self::MONTHLY_FEE,
                            'payment_period' => $period['period'],
                            'membership_year' => $period['year'],
                            'paid_month' => $period['month'],
                            'billing_month' => $billingMonth->toDateString(),
                            'cycle_start' => $cycleStartDate->toDateString(),
                            'cycle_end' => $cycleEndDate->toDateString(),
                            'notes' => 'Bayaran tunggakan bulanan',
                        ];
                    })->toArray();

                    $firstPeriod = $periods[0]['period'];
                    $lastPeriod = $periods[count($periods) - 1]['period'];
                    $amount = count($items) * self::MONTHLY_FEE;

                    [$firstYear, $firstMonth] = $this->extractYearMonth($firstPeriod);

                    $payment = $this->createPaymentWithItems([
                        'user_id' => $user->id,
                        'payment_plan' => 'monthly',
                        'payment_type' => 'monthly_arrears',
                        'amount' => $amount,
                        'payment_period' => $firstPeriod . '_to_' . $lastPeriod,
                        'membership_year' => $firstYear,
                        'paid_month' => $firstMonth,
                        'notes' => 'Bayaran tunggakan bulanan',
                        'items' => $items,
                    ]);

                    return redirect()->route('payment.billplz', $payment->id);
                }

                /*
                |--------------------------------------------------------------------------
                | Bayaran Baki Kitaran Bulanan
                |--------------------------------------------------------------------------
                */
                if ($action === 'monthly_balance') {

                    if (($summary['registration_balance'] ?? 0) > 0) {
                        return redirect()
                            ->route('user.payments.index')
                            ->with('error', 'Sila jelaskan yuran pendaftaran terlebih dahulu.');
                    }

                    $periods = $summary['remaining_cycle_periods'] ?? [];

                    if (count($periods) <= 0) {
                        return redirect()
                            ->route('user.payments.index')
                            ->with('error', 'Semua bayaran bulanan untuk kitaran semasa telah dijelaskan.');
                    }

                    $items = collect($periods)->map(function ($period) use ($cycleStartDate, $cycleEndDate) {
                        $billingMonth = Carbon::createFromDate($period['year'], $period['month'], 1)->startOfMonth();

                        return [
                            'payment_type' => 'monthly',
                            'amount' => self::MONTHLY_FEE,
                            'payment_period' => $period['period'],
                            'membership_year' => $period['year'],
                            'paid_month' => $period['month'],
                            'billing_month' => $billingMonth->toDateString(),
                            'cycle_start' => $cycleStartDate->toDateString(),
                            'cycle_end' => $cycleEndDate->toDateString(),
                            'notes' => 'Bayaran baki kitaran bulanan',
                        ];
                    })->toArray();

                    $firstPeriod = $periods[0]['period'];
                    $lastPeriod = $periods[count($periods) - 1]['period'];
                    $amount = count($items) * self::MONTHLY_FEE;

                    [$firstYear, $firstMonth] = $this->extractYearMonth($firstPeriod);

                    $payment = $this->createPaymentWithItems([
                        'user_id' => $user->id,
                        'payment_plan' => 'monthly',
                        'payment_type' => 'monthly_balance',
                        'amount' => $amount,
                        'payment_period' => $firstPeriod . '_to_' . $lastPeriod,
                        'membership_year' => $firstYear,
                        'paid_month' => $firstMonth,
                        'notes' => 'Bayaran baki kitaran bulanan',
                        'items' => $items,
                    ]);

                    return redirect()->route('payment.billplz', $payment->id);
                }
            }

            /*
            |--------------------------------------------------------------------------
            | PELAN TAHUNAN
            |--------------------------------------------------------------------------
            */
            if ($plan === 'yearly') {

                /*
                |--------------------------------------------------------------------------
                | Bayaran Pertama Tahunan
                |--------------------------------------------------------------------------
                */
                if ($action === 'first_payment') {

                    if (($summary['registration_balance'] ?? 0) <= 0 && ($summary['annual_balance'] ?? 0) <= 0) {
                        return redirect()
                            ->route('user.payments.index')
                            ->with('error', 'Bayaran tahunan telah dijelaskan.');
                    }

                    $amount = ($summary['registration_balance'] ?? 0) + ($summary['annual_balance'] ?? 0);

                    $items = [];

                    if (($summary['registration_balance'] ?? 0) > 0) {
                        $items[] = [
                            'payment_type' => 'registration',
                            'amount' => self::REGISTRATION_FEE,
                            'payment_period' => null,
                            'membership_year' => null,
                            'paid_month' => null,
                            'billing_month' => null,
                            'cycle_start' => $cycleStartDate->toDateString(),
                            'cycle_end' => $cycleEndDate->toDateString(),
                            'notes' => 'Yuran pendaftaran',
                        ];
                    }

                    if (($summary['annual_balance'] ?? 0) > 0) {
                        $items[] = [
                            'payment_type' => 'yearly',
                            'amount' => $summary['annual_balance'],
                            'payment_period' => $yearlyPeriod,
                            'membership_year' => (int) $cycleStartDate->format('Y'),
                            'paid_month' => (int) $cycleStartDate->format('m'),
                            'billing_month' => $cycleStartDate->toDateString(),
                            'cycle_start' => $cycleStartDate->toDateString(),
                            'cycle_end' => $cycleEndDate->toDateString(),
                            'notes' => 'Yuran tahunan sesi ' . $summary['cycle_start_period'] . ' hingga ' . $summary['cycle_end_period'],
                        ];
                    }

                    $payment = $this->createPaymentWithItems([
                        'user_id' => $user->id,
                        'payment_plan' => 'yearly',
                        'payment_type' => 'first_yearly',
                        'amount' => $amount,
                        'payment_period' => $yearlyPeriod,
                        'membership_year' => (int) $cycleStartDate->format('Y'),
                        'paid_month' => (int) $cycleStartDate->format('m'),
                        'notes' => 'Bayaran pertama tahunan',
                        'items' => $items,
                    ]);

                    return redirect()->route('payment.billplz', $payment->id);
                }

                /*
                |--------------------------------------------------------------------------
                | Bayaran Tahunan Biasa
                |--------------------------------------------------------------------------
                */
               if ($action === 'annual_payment') {

                    if (($summary['registration_balance'] ?? 0) > 0) {
                        return redirect()
                            ->route('user.payments.index')
                            ->with('error', 'Sila jelaskan yuran pendaftaran terlebih dahulu.');
                    }

                    if (($summary['annual_balance'] ?? 0) <= 0) {
                        return redirect()
                            ->route('user.payments.index')
                            ->with('error', 'Bayaran tahunan bagi sesi semasa telah dijelaskan.');
                    }

                    $payment = $this->createPaymentWithItems([
                        'user_id' => $user->id,
                        'payment_plan' => 'yearly',
                        'payment_type' => 'yearly',
                        'amount' => $summary['annual_balance'],
                        'payment_period' => $yearlyPeriod,
                        'membership_year' => (int) $cycleStartDate->format('Y'),
                        'paid_month' => (int) $cycleStartDate->format('m'),
                        'notes' => 'Bayaran tahunan',
                        'items' => [
                            [
                                'payment_type' => 'yearly',
                                'amount' => $summary['annual_balance'],
                                'payment_period' => $yearlyPeriod,
                                'membership_year' => (int) $cycleStartDate->format('Y'),
                                'paid_month' => (int) $cycleStartDate->format('m'),
                                'billing_month' => $cycleStartDate->toDateString(),
                                'cycle_start' => $cycleStartDate->toDateString(),
                                'cycle_end' => $cycleEndDate->toDateString(),
                                'notes' => 'Yuran tahunan sesi ' . $summary['cycle_start_period'] . ' hingga ' . $summary['cycle_end_period'],
                            ],
                        ],
                    ]);

                    return redirect()->route('payment.billplz', $payment->id);
                }
            }

            return redirect()
                ->route('user.payments.index')
                ->with('error', 'Tindakan bayaran tidak sah.');

        } catch (\Throwable $e) {
            return redirect()
                ->route('user.payments.index')
                ->with('error', 'Ralat berlaku semasa menjana bayaran: ' . $e->getMessage());
        }
    }

}