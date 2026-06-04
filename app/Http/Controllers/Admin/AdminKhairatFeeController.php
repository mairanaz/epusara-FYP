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
        | Query Rekod / Summary
        |--------------------------------------------------------------------------
        | Bahagian ini ikut filter admin pilih:
        | tahun, bulan, search, status, jenis yuran dan kaedah bayaran.
        */
        $query = Payment::with(['user.profile']);

        $this->applyFilters($query, $request, $currentYear, $currentMonth);

        $payments = $query->latest()->get();

        /*
        |--------------------------------------------------------------------------
        | Query Graf
        |--------------------------------------------------------------------------
        | Graf kekal tunjuk trend 12 bulan untuk tahun dipilih.
        | Jadi kalau admin pilih bulan Mei, summary ikut Mei,
        | tetapi graf masih tunjuk trend Jan - Dis tahun tersebut.
        */
        $chartQuery = Payment::with(['user.profile']);

        $this->applyYearFilter($chartQuery, $currentYear);

        $chartPayments = $chartQuery->get();

        /*
        |--------------------------------------------------------------------------
        | Rekod Transaksi
        |--------------------------------------------------------------------------
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
        | Summary Count - Ikut Filter
        |--------------------------------------------------------------------------
        | Kalau pilih Mei, jumlah kutipan akan kira Mei sahaja.
        */
        $summaryPayments = $payments;
        $paidSummaryPayments = $summaryPayments->where('status', 'paid');

        $totalAmount = $paidSummaryPayments->sum('amount');
        $totalCount = $summaryPayments->pluck('user_id')->filter()->unique()->count();

        $paidCount = $summaryPayments->where('status', 'paid')->count();
        $pendingCount = $summaryPayments->where('status', 'pending')->count();
        $failedCount = $summaryPayments->where('status', 'failed')->count();

        /*
        |--------------------------------------------------------------------------
        | Kutipan Bulanan - Untuk Graf 12 Bulan
        |--------------------------------------------------------------------------
        */
        $months = $this->buildMonthlyReport($chartPayments);

        /*
        |--------------------------------------------------------------------------
        | Pecahan Jenis Yuran - Ikut Filter
        |--------------------------------------------------------------------------
        */
        $feeTypes = $this->buildFeeTypeReport($summaryPayments);

        /*
        |--------------------------------------------------------------------------
        | Ahli Belum Bayar - Ikut Filter
        |--------------------------------------------------------------------------
        */
        $unpaidMembers = $this->buildUnpaidMembers($summaryPayments);

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
                    ->orWhere('paid_month', $month)
                    ->orWhereMonth('created_at', $month);
            });
        }

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhereHas('profile', function ($profileQuery) use ($search) {
                            $profileQuery->where('no_kp', 'like', '%' . $search . '%')
                                ->orWhere('no_tel', 'like', '%' . $search . '%');
                        });
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
                $query->whereIn('payment_plan', ['yearly', 'annual'])
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
        $payment = Payment::with(['user.profile'])->findOrFail($payment);

        return view('admin.khairat.fees.show', compact('payment'));
    }

    public function exportExcel(Request $request)
    {
        $currentYear = (int) $request->input('year', now()->year);
        $currentMonth = $request->filled('month') ? (int) $request->month : null;

        $query = Payment::with(['user.profile']);

        $this->applyFilters($query, $request, $currentYear, $currentMonth);

        $payments = $query->latest()->get();

        $fileName = 'laporan-yuran-' . $currentYear;

        if ($currentMonth) {
            $fileName .= '-bulan-' . str_pad($currentMonth, 2, '0', STR_PAD_LEFT);
        }

        $fileName .= '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];

        return response()->stream(function () use ($payments) {
            $file = fopen('php://output', 'w');

            // Bagi Excel baca UTF-8 dengan betul
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, [
                'No',
                'Nama Ahli',
                'No KP',
                'Jenis Yuran',
                'Pelan Bayaran',
                'Amaun',
                'Status',
                'No Resit / Rujukan',
                'Billplz Bill ID',
                'Kaedah Bayaran',
                'Tahun Keahlian',
                'Bulan Bayaran',
                'Tarikh Bayaran',
                'Tarikh Rekod',
            ]);

            foreach ($payments as $index => $payment) {
                $user = $payment->user;

                fputcsv($file, [
                    $index + 1,
                    $user?->name ?? '-',
                    $user?->no_kp ?? data_get($user, 'profile.no_kp') ?? '-',
                    $this->getFeeType($payment),
                    $payment->payment_plan ?? '-',
                    number_format($payment->amount ?? 0, 2, '.', ''),
                    strtoupper($payment->status ?? '-'),
                    $payment->reference_no ?? $payment->receipt_no ?? '-',
                    $payment->billplz_bill_id ?? '-',
                    $payment->payment_method ?? ($payment->billplz_bill_id ? 'Billplz' : 'Manual'),
                    $payment->membership_year ?? '-',
                    $payment->paid_month ?? '-',
                    $payment->paid_at ? \Carbon\Carbon::parse($payment->paid_at)->format('d/m/Y H:i') : '-',
                    $payment->created_at ? \Carbon\Carbon::parse($payment->created_at)->format('d/m/Y H:i') : '-',
                ]);
            }

            fclose($file);
        }, 200, $headers);
    }

    public function previewPdf(Request $request)
    {
        $currentYear = (int) $request->input('year', now()->year);
        $currentMonth = $request->filled('month') ? (int) $request->month : null;

        $query = Payment::with(['user.profile']);

        $this->applyFilters($query, $request, $currentYear, $currentMonth);

        $payments = $query->latest()->get();

        $paidPayments = $payments->where('status', 'paid');

        $totalAmount = $paidPayments->sum('amount');
        $paidCount = $payments->where('status', 'paid')->count();
        $pendingCount = $payments->where('status', 'pending')->count();
        $failedCount = $payments->where('status', 'failed')->count();
        $totalMembers = $payments->pluck('user_id')->filter()->unique()->count();

        $feeTypes = $this->buildFeeTypeReport($payments);

        return view('admin.khairat.fees.pdf-preview', compact(
            'payments',
            'currentYear',
            'currentMonth',
            'totalAmount',
            'paidCount',
            'pendingCount',
            'failedCount',
            'totalMembers',
            'feeTypes'
        ));
    }

    public function getFeeTypeForView($payment): string
    {
        return $this->getFeeType($payment);
    }
    
}