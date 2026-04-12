<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
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
            return redirect()->route('user.profile.create')
                ->with('error', 'Sila lengkapkan maklumat ahli terlebih dahulu.');
        }

        $payments = $user->payments()->latest()->get();

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
            return redirect()->route('user.profile.create')
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
            return redirect()->route('user.profile.create')
                ->with('error', 'Sila lengkapkan maklumat ahli terlebih dahulu.');
        }

        $plan = $this->normalizePlan($profile->payment_plan);

        $request->validate([
            'payment_action' => 'required|in:first_payment,monthly_payment,annual_payment',
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

                $this->createPayment([
                    'user_id' => $user->id,
                    'payment_plan' => 'monthly',
                    'payment_type' => 'registration',
                    'amount' => self::REGISTRATION_FEE,
                    'payment_period' => null,
                    'membership_year' => $year,
                    'paid_month' => null,
                    'notes' => 'Bayaran pendaftaran',
                    'suffix' => 'REG',
                ]);

                $this->createPayment([
                    'user_id' => $user->id,
                    'payment_plan' => 'monthly',
                    'payment_type' => 'monthly',
                    'amount' => self::MONTHLY_FEE,
                    'payment_period' => sprintf('%04d-%02d', $year, $month),
                    'membership_year' => $year,
                    'paid_month' => $month,
                    'notes' => 'Bayaran bulan pertama',
                    'suffix' => 'MON',
                ]);

                return redirect()->route('user.payments.index')
                    ->with('success', 'Bayaran pertama bulanan berjaya direkodkan.');
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

                $this->createPayment([
                    'user_id' => $user->id,
                    'payment_plan' => 'monthly',
                    'payment_type' => 'monthly',
                    'amount' => self::MONTHLY_FEE,
                    'payment_period' => sprintf('%04d-%02d', $year, $month),
                    'membership_year' => $year,
                    'paid_month' => $month,
                    'notes' => 'Bayaran bulanan',
                    'suffix' => 'MON',
                ]);

                return redirect()->route('user.payments.index')
                    ->with('success', 'Bayaran bulanan berjaya direkodkan.');
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

                $this->createPayment([
                    'user_id' => $user->id,
                    'payment_plan' => 'yearly',
                    'payment_type' => 'registration',
                    'amount' => self::REGISTRATION_FEE,
                    'payment_period' => null,
                    'membership_year' => $currentYear,
                    'paid_month' => null,
                    'notes' => 'Bayaran pendaftaran',
                    'suffix' => 'REG',
                ]);

                $this->createPayment([
                    'user_id' => $user->id,
                    'payment_plan' => 'yearly',
                    'payment_type' => 'yearly',
                    'amount' => self::YEARLY_FEE,
                    'payment_period' => (string) $currentYear,
                    'membership_year' => $currentYear,
                    'paid_month' => null,
                    'notes' => 'Bayaran tahunan',
                    'suffix' => 'ANN',
                ]);

                return redirect()->route('user.payments.index')
                    ->with('success', 'Bayaran pertama tahunan berjaya direkodkan.');
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

                $this->createPayment([
                    'user_id' => $user->id,
                    'payment_plan' => 'yearly',
                    'payment_type' => 'yearly',
                    'amount' => $summary['annual_balance'],
                    'payment_period' => (string) $currentYear,
                    'membership_year' => $currentYear,
                    'paid_month' => null,
                    'notes' => 'Bayaran tahunan',
                    'suffix' => 'ANN',
                ]);

                return redirect()->route('user.payments.index')
                    ->with('success', 'Bayaran tahunan berjaya direkodkan.');
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

        return view('user.payments.receipt', compact('payment'));
    }

    private function getPaymentSummary($userId, $plan)
    {
        $currentYear = now()->year;
        $currentMonth = now()->month;

        $registrationPaid = Payment::where('user_id', $userId)
            ->where('status', 'paid')
            ->where('payment_type', 'registration')
            ->sum('amount');

        $monthlyPayments = Payment::where('user_id', $userId)
            ->where('status', 'paid')
            ->where('payment_type', 'monthly')
            ->orderBy('membership_year')
            ->orderBy('paid_month')
            ->get();

        $monthlyPaidCount = $monthlyPayments->count();

        $annualPaidThisYear = Payment::where('user_id', $userId)
            ->where('status', 'paid')
            ->where('payment_type', 'yearly')
            ->where('membership_year', $currentYear)
            ->sum('amount');

        $registrationBalance = max(0, self::REGISTRATION_FEE - $registrationPaid);
        $annualBalance = max(0, self::YEARLY_FEE - $annualPaidThisYear);

        $firstMonthlyPayment = $monthlyPayments->first();
        $latestMonthlyPayment = $monthlyPayments->last();

        $membershipStartPeriod = $firstMonthlyPayment
            ? sprintf('%04d-%02d', $firstMonthlyPayment->membership_year, $firstMonthlyPayment->paid_month)
            : now()->format('Y-m');

        $schedulePeriods = $this->buildRollingMonthlySchedule($membershipStartPeriod, 12);

        $paidPeriods = $monthlyPayments
            ->map(fn ($payment) => sprintf('%04d-%02d', $payment->membership_year, $payment->paid_month))
            ->values()
            ->toArray();

        $currentDate = now()->startOfMonth();

        if ($latestMonthlyPayment) {
            $expectedDate = Carbon::createFromDate(
                $latestMonthlyPayment->membership_year,
                $latestMonthlyPayment->paid_month,
                1
            )->addMonth()->startOfMonth();

            $nextMonthlyPeriod = $expectedDate->format('Y-m');
            $canPayMonthlyNow = $expectedDate->lessThanOrEqualTo($currentDate);
        } else {
            $nextMonthlyPeriod = $currentDate->format('Y-m');
            $canPayMonthlyNow = true;
        }

        $monthlyRemainingCount = max(0, 12 - $monthlyPaidCount);
        $monthlyOutstanding = $monthlyRemainingCount * self::MONTHLY_FEE;

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
            'schedule_periods' => $schedulePeriods,
            'paid_periods' => $paidPeriods,
            'monthly_paid_count' => $monthlyPaidCount,
            'monthly_remaining_count' => $monthlyRemainingCount,
            'monthly_outstanding' => $monthlyOutstanding,
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

    private function createPayment(array $data): void
    {
        $timestamp = now()->format('YmdHis');

        Payment::create([
            'user_id' => $data['user_id'],
            'payment_plan' => $data['payment_plan'],
            'payment_type' => $data['payment_type'],
            'amount' => $data['amount'],
            'payment_period' => $data['payment_period'],
            'membership_year' => $data['membership_year'],
            'paid_month' => $data['paid_month'],
            'status' => 'paid',
            'paid_at' => now(),
            'payment_method' => 'manual',
            'reference_no' => 'MANUAL-' . $timestamp . '-' . $data['suffix'],
            'receipt_no' => 'RCP-' . $timestamp . '-' . $data['suffix'],
            'notes' => $data['notes'],
        ]);
    }

    private function monthlyPaymentExists($userId, $year, $month): bool
    {
        return Payment::where('user_id', $userId)
            ->where('payment_type', 'monthly')
            ->where('membership_year', $year)
            ->where('paid_month', $month)
            ->where('status', 'paid')
            ->exists();
    }

    private function isAllowedMonthlyPeriod($userId, $year, $month): bool
    {
        $currentDate = now()->startOfMonth();

        $requestedDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();

        // Tak boleh bayar bulan masa depan
        if ($requestedDate->gt($currentDate)) {
            return false;
        }

        $latestMonthlyPayment = Payment::where('user_id', $userId)
            ->where('status', 'paid')
            ->where('payment_type', 'monthly')
            ->orderByDesc('membership_year')
            ->orderByDesc('paid_month')
            ->first();

        // Kalau belum pernah bayar bulanan, hanya bulan semasa dibenarkan
        if (!$latestMonthlyPayment) {
            return $requestedDate->equalTo($currentDate);
        }

        $expectedDate = Carbon::createFromDate(
            $latestMonthlyPayment->membership_year,
            $latestMonthlyPayment->paid_month,
            1
        )->addMonth()->startOfMonth();

        // Hanya boleh bayar bulan seterusnya DAN bulan itu mesti dah tiba
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