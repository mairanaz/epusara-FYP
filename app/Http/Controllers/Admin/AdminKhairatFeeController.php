<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminKhairatFeeController extends Controller
{
    public function index(Request $request)
    {
        $currentYear = (int) $request->input('year', now()->year);
        $currentMonth = $request->filled('month') ? (int) $request->month : null;

        /*
        |--------------------------------------------------------------------------
        | Query Untuk Rekod Yang Dipaparkan
        |--------------------------------------------------------------------------
        | Query ini ikut filter carian, tahun, bulan dan status.
        */
        $query = Payment::with('user');

        $this->applyFilters($query, $request, $currentYear, $currentMonth);

        $payments = $query->latest()->get();

        /*
        |--------------------------------------------------------------------------
        | Query Untuk Laporan Tahun Semasa
        |--------------------------------------------------------------------------
        | Digunakan untuk kira summary, bulan, jenis yuran, tunggakan.
        */
        $yearQuery = Payment::with('user');
        $this->applyYearFilter($yearQuery, $currentYear);

        $yearPayments = $yearQuery->get();

        /*
        |--------------------------------------------------------------------------
        | Rekod Transaksi
        |--------------------------------------------------------------------------
        | Variable ini digunakan oleh tab "Rekod Transaksi".
        */
        $groupedFees = $payments->map(function ($payment) {
            $user = $payment->user;

            return (object) [
                'payment_id' => $payment->id,
                'user_id' => $user?->id,
                'user_name' => $user?->name ?? '-',
                'no_kp' => $user?->no_kp ?? data_get($user, 'profile.no_kp') ?? '-',

                'plan' => $payment->payment_plan,
                'payment_type' => $this->getFeeType($payment),
                'fee_label' => $this->buildFeeLabel($payment, $payment->payment_plan ?? 'monthly'),

                'amount' => $payment->amount ?? 0,
                'total_paid_amount' => $payment->amount ?? 0,

                'status' => $payment->status ?? 'pending',
                'fee_status' => $payment->status ?? 'pending',
                'overall_status' => $payment->status ?? 'pending',

                'receipt_no' => $payment->reference_no ?? $payment->receipt_no ?? '-',
                'reference_no' => $payment->reference_no ?? '-',
                'payment_reference' => $payment->billplz_bill_id
                    ?? $payment->payment_reference
                    ?? $payment->reference_no
                    ?? '-',

                'payment_method' => $payment->payment_method
                    ?? ($payment->billplz_bill_id ? 'Billplz' : 'Manual'),

                'membership_year' => $payment->membership_year,
                'paid_month' => $payment->paid_month,
                'paid_at' => $payment->paid_at,
                'latest_paid_at' => $payment->paid_at,
                'created_at' => $payment->created_at,
            ];
        })->values();

        /*
        |--------------------------------------------------------------------------
        | Summary Count
        |--------------------------------------------------------------------------
        */
        $paidYearPayments = $yearPayments->where('status', 'paid');

        $totalAmount = $paidYearPayments->sum('amount');
        $totalCount = $yearPayments->pluck('user_id')->filter()->unique()->count();

        $paidCount = $yearPayments->where('status', 'paid')->count();
        $pendingCount = $yearPayments->where('status', 'pending')->count();
        $failedCount = $yearPayments->where('status', 'failed')->count();

        /*
        |--------------------------------------------------------------------------
        | Kutipan Bulanan
        |--------------------------------------------------------------------------
        */
        $months = $this->buildMonthlyReport($yearPayments);

        /*
        |--------------------------------------------------------------------------
        | Pecahan Jenis Yuran
        |--------------------------------------------------------------------------
        */
        $feeTypes = $this->buildFeeTypeReport($yearPayments);

        /*
        |--------------------------------------------------------------------------
        | Ahli Belum Bayar / Pending / Failed
        |--------------------------------------------------------------------------
        */
        $unpaidMembers = $this->buildUnpaidMembers($yearPayments);

        return view('admin.khairat.fees.index', compact(
            'groupedFees',
            'totalCount',
            'paidCount',
            'pendingCount',
            'failedCount',
            'totalAmount',
            'months',
            'feeTypes',
            'unpaidMembers',
            'currentYear',
            'currentMonth'
        ));
    }

    private function applyFilters($query, Request $request, int $year, ?int $month = null): void
    {
        $this->applyYearFilter($query, $year);

        if ($month) {
            $query->where(function ($q) use ($month) {
                $q->whereMonth('paid_at', $month)
                    ->orWhere('paid_month', $month);
            });
        }

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', '%' . $search . '%');
                })
                ->orWhere('reference_no', 'like', '%' . $search . '%')
                ->orWhere('payment_plan', 'like', '%' . $search . '%')
                ->orWhere('payment_period', 'like', '%' . $search . '%')
                ->orWhere('payment_type', 'like', '%' . $search . '%')
                ->orWhere('status', 'like', '%' . $search . '%')
                ->orWhere('billplz_bill_id', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('fee_type')) {
            if ($request->fee_type === 'registration') {
                $query->where(function ($q) {
                    $q->where('payment_type', 'registration')
                        ->orWhere('reference_no', 'like', '%REG');
                });
            }

            if ($request->fee_type === 'monthly') {
                $query->where('payment_plan', 'monthly')
                    ->where(function ($q) {
                        $q->whereNull('payment_type')
                            ->orWhere('payment_type', '!=', 'registration');
                    });
            }

            if (in_array($request->fee_type, ['annual', 'yearly'])) {
                $query->where('payment_plan', 'yearly')
                    ->where(function ($q) {
                        $q->whereNull('payment_type')
                            ->orWhere('payment_type', '!=', 'registration');
                    });
            }
        }

        if ($request->filled('payment_method')) {
            if ($request->payment_method === 'billplz') {
                $query->whereNotNull('billplz_bill_id');
            }

            if ($request->payment_method === 'manual') {
                $query->whereNull('billplz_bill_id');
            }
        }
    }

    private function applyYearFilter($query, int $year): void
    {
        $query->where(function ($q) use ($year) {
            $q->where('membership_year', $year)
                ->orWhereYear('paid_at', $year)
                ->orWhereYear('created_at', $year);
        });
    }

    private function buildMonthlyReport($payments): array
    {
        $monthNames = [
            1 => ['short' => 'JAN', 'name' => 'Januari'],
            2 => ['short' => 'FEB', 'name' => 'Februari'],
            3 => ['short' => 'MAC', 'name' => 'Mac'],
            4 => ['short' => 'APR', 'name' => 'April'],
            5 => ['short' => 'MEI', 'name' => 'Mei'],
            6 => ['short' => 'JUN', 'name' => 'Jun'],
            7 => ['short' => 'JUL', 'name' => 'Julai'],
            8 => ['short' => 'OGO', 'name' => 'Ogos'],
            9 => ['short' => 'SEP', 'name' => 'September'],
            10 => ['short' => 'OKT', 'name' => 'Oktober'],
            11 => ['short' => 'NOV', 'name' => 'November'],
            12 => ['short' => 'DIS', 'name' => 'Disember'],
        ];

        $months = [];

        foreach ($monthNames as $monthNumber => $monthInfo) {
            $monthPayments = $payments->filter(function ($payment) use ($monthNumber) {
                return $this->getPaymentMonth($payment) === $monthNumber;
            });

            $paidPayments = $monthPayments->where('status', 'paid');

            $registrationAmount = $paidPayments
                ->filter(fn ($payment) => $this->isRegistrationPayment($payment))
                ->sum('amount');

            $monthlyAmount = $paidPayments
                ->filter(fn ($payment) => $this->isMonthlyPayment($payment))
                ->sum('amount');

            $annualAmount = $paidPayments
                ->filter(fn ($payment) => $this->isAnnualPayment($payment))
                ->sum('amount');

            $months[$monthNumber] = [
                'short' => $monthInfo['short'],
                'name' => $monthInfo['name'],
                'amount' => $paidPayments->sum('amount'),
                'registration' => $registrationAmount,
                'monthly' => $monthlyAmount,
                'annual' => $annualAmount,
                'paid' => $monthPayments->where('status', 'paid')->count(),
                'pending' => $monthPayments->where('status', 'pending')->count(),
                'failed' => $monthPayments->where('status', 'failed')->count(),
            ];
        }

        return $months;
    }

    private function buildFeeTypeReport($payments): array
    {
        $paidPayments = $payments->where('status', 'paid');

        $registrationPayments = $paidPayments->filter(fn ($payment) => $this->isRegistrationPayment($payment));
        $monthlyPayments = $paidPayments->filter(fn ($payment) => $this->isMonthlyPayment($payment));
        $annualPayments = $paidPayments->filter(fn ($payment) => $this->isAnnualPayment($payment));

        return [
            [
                'name' => 'Yuran Pendaftaran',
                'amount' => $registrationPayments->sum('amount'),
                'count' => $registrationPayments->count(),
                'icon' => 'ri-user-add-line',
                'status' => 'Aktif',
            ],
            [
                'name' => 'Yuran Bulanan',
                'amount' => $monthlyPayments->sum('amount'),
                'count' => $monthlyPayments->count(),
                'icon' => 'ri-calendar-check-line',
                'status' => 'Utama',
            ],
            [
                'name' => 'Yuran Tahunan',
                'amount' => $annualPayments->sum('amount'),
                'count' => $annualPayments->count(),
                'icon' => 'ri-calendar-todo-line',
                'status' => 'Aktif',
            ],
        ];
    }

    private function buildUnpaidMembers($payments): array
    {
        return $payments
            ->whereIn('status', ['pending', 'failed'])
            ->groupBy('user_id')
            ->map(function ($userPayments) {
                $firstPayment = $userPayments->first();
                $user = $firstPayment->user;

                $months = $userPayments
                    ->map(fn ($payment) => $this->getPaymentMonthName($payment))
                    ->filter()
                    ->unique()
                    ->implode(', ');

                return [
                    'name' => $user?->name ?? '-',
                    'no_kp' => $user?->no_kp ?? data_get($user, 'profile.no_kp') ?? '-',
                    'phone' => $user?->phone ?? data_get($user, 'profile.no_tel') ?? '-',
                    'arrears' => $userPayments->sum('amount'),
                    'month' => $months ?: '-',
                    'status' => $userPayments->contains('status', 'failed') ? 'Tertunggak' : 'Belum Bayar',
                ];
            })
            ->values()
            ->toArray();
    }

    private function getPaymentMonth($payment): ?int
    {
        if (!empty($payment->paid_month)) {
            return (int) $payment->paid_month;
        }

        if (!empty($payment->paid_at)) {
            return Carbon::parse($payment->paid_at)->month;
        }

        if (!empty($payment->created_at)) {
            return Carbon::parse($payment->created_at)->month;
        }

        return null;
    }

    private function getPaymentMonthName($payment): ?string
    {
        $month = $this->getPaymentMonth($payment);

        $monthNames = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Mac',
            4 => 'April',
            5 => 'Mei',
            6 => 'Jun',
            7 => 'Julai',
            8 => 'Ogos',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Disember',
        ];

        return $monthNames[$month] ?? null;
    }

    private function isRegistrationPayment($payment): bool
    {
        return strtolower($payment->payment_type ?? '') === 'registration'
            || strtoupper(substr($payment->reference_no ?? '', -3)) === 'REG';
    }

    private function isMonthlyPayment($payment): bool
    {
        return !$this->isRegistrationPayment($payment)
            && strtolower($payment->payment_plan ?? '') === 'monthly';
    }

    private function isAnnualPayment($payment): bool
    {
        return !$this->isRegistrationPayment($payment)
            && in_array(strtolower($payment->payment_plan ?? ''), ['yearly', 'annual']);
    }

    private function getFeeType($payment): string
    {
        if ($this->isRegistrationPayment($payment)) {
            return 'Yuran Pendaftaran';
        }

        if ($this->isMonthlyPayment($payment)) {
            return 'Yuran Bulanan';
        }

        if ($this->isAnnualPayment($payment)) {
            return 'Yuran Tahunan';
        }

        return $payment->payment_type ?? '-';
    }

    private function buildFeeLabel($payment, string $plan): string
    {
        if (!$payment) {
            return $plan === 'yearly' ? 'Tahunan' : 'Bulanan';
        }

        if ($this->isRegistrationPayment($payment)) {
            return 'Yuran Pendaftaran';
        }

        if ($plan === 'monthly') {
            if (!empty($payment->paid_month) && !empty($payment->membership_year)) {
                return Carbon::createFromDate(
                    $payment->membership_year,
                    $payment->paid_month,
                    1
                )->translatedFormat('F Y');
            }

            return 'Yuran Bulanan';
        }

        return !empty($payment->membership_year)
            ? 'Yuran Tahunan ' . $payment->membership_year
            : 'Yuran Tahunan';
    }

    public function show($payment)
    {
        $payment = Payment::with('user')->findOrFail($payment);

        return view('admin.khairat.fees.show', compact('payment'));
    }
}