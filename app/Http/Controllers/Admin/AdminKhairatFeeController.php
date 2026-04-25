<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class AdminKhairatFeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with('user');

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', '%' . $search . '%');
                })
                ->orWhere('payment_plan', 'like', '%' . $search . '%')
                ->orWhere('payment_period', 'like', '%' . $search . '%')
                ->orWhere('status', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'paid') {
                $query->where('status', 'paid');
            } elseif ($request->status === 'pending') {
                $query->where('status', 'pending');
            } elseif ($request->status === 'failed') {
                $query->where('status', 'failed');
            }
        }

        $payments = $query->latest()->get();

        $groupedFees = $payments->groupBy('user_id')->map(function ($userPayments) {
            $firstPayment = $userPayments->first();
            $user = $firstPayment->user;

            $registrationPayment = $userPayments->first(function ($payment) {
                return in_array(strtoupper(substr($payment->reference_no ?? '', -3)), ['REG'])
                    || strtolower($payment->payment_type ?? '') === 'registration';
            });

            $monthlyPayment = $userPayments
                ->filter(function ($payment) {
                    return $payment->payment_plan === 'monthly';
                })
                ->sortByDesc(function ($payment) {
                    return sprintf(
                        '%04d%02d',
                        (int) ($payment->membership_year ?? 0),
                        (int) ($payment->paid_month ?? 0)
                    );
                })
                ->first(function ($payment) {
                    return !in_array(strtoupper(substr($payment->reference_no ?? '', -3)), ['REG']);
                });

            $yearlyPayment = $userPayments
                ->filter(function ($payment) {
                    return $payment->payment_plan === 'yearly';
                })
                ->sortByDesc('membership_year')
                ->first(function ($payment) {
                    return !in_array(strtoupper(substr($payment->reference_no ?? '', -3)), ['REG']);
                });

            $plan = $userPayments->contains('payment_plan', 'yearly') ? 'yearly' : 'monthly';
            $mainPayment = $plan === 'yearly' ? $yearlyPayment : $monthlyPayment;

            $totalPaidAmount = $userPayments
                ->where('status', 'paid')
                ->sum('amount');

            $registrationStatus = $registrationPayment?->status ?? 'pending';
            $feeStatus = $mainPayment?->status ?? 'pending';

            $overallStatus = 'pending';
            if ($registrationStatus === 'paid' && $feeStatus === 'paid') {
                $overallStatus = 'paid';
            } elseif ($registrationStatus === 'failed' || $feeStatus === 'failed') {
                $overallStatus = 'failed';
            }

            return (object) [
                'user_id' => $user?->id,
                'user_name' => $user?->name ?? '-',
                'plan' => $plan,
                'registration_status' => $registrationStatus,
                'fee_status' => $feeStatus,
                'fee_label' => $this->buildFeeLabel($mainPayment, $plan),
                'total_paid_amount' => $totalPaidAmount,
                'latest_paid_at' => $userPayments->where('status', 'paid')->sortByDesc('paid_at')->first()?->paid_at,
                'overall_status' => $overallStatus,
            ];
        })->values();

        $totalCount = $groupedFees->count();
        $paidCount = $groupedFees->where('overall_status', 'paid')->count();
        $pendingCount = $groupedFees->where('overall_status', 'pending')->count();
        $failedCount = $groupedFees->where('overall_status', 'failed')->count();
        $totalAmount = $payments->where('status', 'paid')->sum('amount');

        return view('admin.khairat.fees.index', compact(
            'groupedFees',
            'totalCount',
            'paidCount',
            'pendingCount',
            'failedCount',
            'totalAmount'
        ));
    }

    private function buildFeeLabel($payment, string $plan): string
    {
        if (!$payment) {
            return $plan === 'yearly' ? 'Tahunan' : 'Bulanan';
        }

        if ($plan === 'monthly') {
            if (!empty($payment->paid_month) && !empty($payment->membership_year)) {
                return \Carbon\Carbon::createFromDate(
                    $payment->membership_year,
                    $payment->paid_month,
                    1
                )->translatedFormat('F Y');
            }

            return 'Bulanan';
        }

        return !empty($payment->membership_year)
            ? 'Tahun ' . $payment->membership_year
            : 'Tahunan';
    }

    public function show($payment)
    {
        $payment = Payment::with('user')->findOrFail($payment);

        return view('admin.khairat.fees.show', compact('payment'));
    }
}