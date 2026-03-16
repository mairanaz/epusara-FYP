<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;

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
        $payments = $user->payments()->latest()->get();

        $plan = $this->normalizePlan($profile?->payment_plan);
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

        // PLAN BULANAN
        if ($plan === 'monthly') {
            if ($action === 'annual_payment') {
                return redirect()->route('user.payments.index')
                    ->with('error', 'Pelan bulanan tidak boleh membuat bayaran tahunan.');
            }

            if ($action === 'first_payment') {
                if ($summary['registration_paid'] > 0 || $summary['monthly_count_this_year'] > 0) {
                    return redirect()->route('user.payments.index')
                        ->with('error', 'Bayaran pertama telah pun dibuat.');
                }

                $period = $request->payment_period ?: now()->format('Y-m');
                [$year, $month] = $this->extractYearMonth($period);

                if (!$this->isAllowedMonthlyPeriod($user->id, $year, $month)) {
                    return redirect()->route('user.payments.index')
                        ->with('error', 'Untuk pelan bulanan, bayaran hanya dibenarkan bagi bulan semasa atau bulan seterusnya yang belum dibayar.');
                }

                $this->createPayment([
                    'user_id' => $user->id,
                    'payment_plan' => 'monthly',
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

                $period = $request->payment_period ?: now()->format('Y-m');
                [$year, $month] = $this->extractYearMonth($period);

                if ($this->monthlyPaymentExists($user->id, $year, $month)) {
                    return redirect()->route('user.payments.index')
                        ->with('error', 'Bayaran bagi bulan ini telah direkodkan.');
                }

                if (!$this->isAllowedMonthlyPeriod($user->id, $year, $month)) {
                    return redirect()->route('user.payments.index')
                        ->with('error', 'Untuk pelan bulanan, bayaran hanya dibenarkan bagi bulan semasa atau bulan seterusnya yang belum dibayar.');
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

        // PLAN TAHUNAN
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

    private function getPaymentSummary($userId, $plan)
    {
        $currentYear = now()->year;
        $currentMonth = now()->month;

        $registrationPaid = Payment::where('user_id', $userId)
            ->where('status', 'paid')
            ->where('payment_type', 'registration')
            ->sum('amount');

        $monthlyPaidThisYear = Payment::where('user_id', $userId)
            ->where('status', 'paid')
            ->where('payment_type', 'monthly')
            ->where('membership_year', $currentYear)
            ->sum('amount');

        $monthlyCountThisYear = Payment::where('user_id', $userId)
            ->where('status', 'paid')
            ->where('payment_type', 'monthly')
            ->where('membership_year', $currentYear)
            ->count();

        $annualPaidThisYear = Payment::where('user_id', $userId)
            ->where('status', 'paid')
            ->where('payment_type', 'yearly')
            ->where('membership_year', $currentYear)
            ->sum('amount');

        $registrationBalance = max(0, self::REGISTRATION_FEE - $registrationPaid);
        $annualBalance = max(0, self::YEARLY_FEE - $annualPaidThisYear);

        $latestMonthlyPayment = Payment::where('user_id', $userId)
            ->where('status', 'paid')
            ->where('payment_type', 'monthly')
            ->orderByDesc('membership_year')
            ->orderByDesc('paid_month')
            ->first();

        $nextMonthlyPeriod = null;

        if ($latestMonthlyPayment) {
            $nextMonth = $latestMonthlyPayment->paid_month + 1;
            $nextYear = $latestMonthlyPayment->membership_year;

            if ($nextMonth > 12) {
                $nextMonth = 1;
                $nextYear++;
            }

            $nextMonthlyPeriod = sprintf('%04d-%02d', $nextYear, $nextMonth);
        } else {
            $nextMonthlyPeriod = now()->format('Y-m');
        }

        return [
            'plan' => $plan,
            'registration_paid' => $registrationPaid,
            'registration_balance' => $registrationBalance,
            'monthly_paid_this_year' => $monthlyPaidThisYear,
            'monthly_count_this_year' => $monthlyCountThisYear,
            'annual_paid_this_year' => $annualPaidThisYear,
            'annual_balance' => $annualBalance,
            'first_monthly_total' => self::REGISTRATION_FEE + self::MONTHLY_FEE,
            'first_yearly_total' => self::REGISTRATION_FEE + self::YEARLY_FEE,
            'current_year' => $currentYear,
            'current_month' => $currentMonth,
            'next_monthly_period' => $nextMonthlyPeriod,
            'is_fully_paid_yearly' => $annualPaidThisYear >= self::YEARLY_FEE,
        ];
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
            'paid_at' => now()->toDateString(),
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
        $latestMonthlyPayment = Payment::where('user_id', $userId)
            ->where('status', 'paid')
            ->where('payment_type', 'monthly')
            ->orderByDesc('membership_year')
            ->orderByDesc('paid_month')
            ->first();

        if (!$latestMonthlyPayment) {
            return $year === now()->year && $month === now()->month;
        }

        $expectedMonth = $latestMonthlyPayment->paid_month + 1;
        $expectedYear = $latestMonthlyPayment->membership_year;

        if ($expectedMonth > 12) {
            $expectedMonth = 1;
            $expectedYear++;
        }

        return $year === $expectedYear && $month === $expectedMonth;
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