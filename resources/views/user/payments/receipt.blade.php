@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <style>
        .receipt-card {
            border: 0;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
            background: #fff;
        }

        .receipt-header {
            background: linear-gradient(135deg, #0d6efd, #0b5ed7);
            color: #fff;
            padding: 28px;
        }

        .receipt-header h3,
        .receipt-header h5,
        .receipt-header p {
            color: #fff;
        }

        .receipt-badge {
            font-size: 13px;
            padding: 8px 14px;
            border-radius: 999px;
            font-weight: 600;
        }

        .receipt-section-title {
            font-size: 15px;
            font-weight: 700;
            color: #344054;
            margin-bottom: 14px;
        }

        .receipt-info-box {
            background: #f8fafc;
            border: 1px solid #e9ecef;
            border-radius: 14px;
            padding: 18px;
            height: 100%;
        }

        .receipt-label {
            font-size: 13px;
            color: #6c757d;
            margin-bottom: 4px;
        }

        .receipt-value {
            font-size: 15px;
            font-weight: 600;
            color: #212529;
        }

        .receipt-total-box {
            background: linear-gradient(135deg, #f8f9fa, #eef4ff);
            border: 1px solid #dbe7ff;
            border-radius: 16px;
            padding: 24px;
            text-align: center;
            height: 100%;
        }

        .receipt-total-box .amount {
            font-size: 32px;
            font-weight: 800;
            color: #0d6efd;
            line-height: 1.1;
        }

        .receipt-table th {
            width: 35%;
            background: #f8f9fa;
            color: #495057;
        }

        .receipt-footer-note {
            font-size: 13px;
            color: #6c757d;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            .container-fluid {
                padding: 0 !important;
            }

            .receipt-card {
                box-shadow: none !important;
                border: 1px solid #dee2e6;
            }

            body {
                background: #fff !important;
            }
        }
    </style>

    <div class="d-md-flex d-block align-items-center justify-content-between my-4 no-print">
        <div>
            <h4 class="mb-1">Resit Bayaran</h4>
            <p class="text-muted mb-0">Butiran lengkap bayaran ahli.</p>
        </div>
        <div class="mt-3 mt-md-0 d-flex gap-2">
            <a href="{{ route('user.payments.index') }}" class="btn btn-light">
                <i class="bx bx-arrow-back me-1"></i> Kembali
            </a>
            <button onclick="window.print()" class="btn btn-info">
                <i class="bx bx-printer me-1"></i> Cetak Resit
            </button>
        </div>
    </div>

    @php

        $paymentTypeLabel = match($payment->payment_type) {
            'first_monthly' => 'Bayaran Pertama Bulanan',
            'monthly' => 'Bayaran Bulanan',
            'monthly_arrears' => 'Bayaran Tunggakan Bulanan',
            'monthly_balance' => 'Bayaran Baki Kitaran',
            'first_yearly' => 'Bayaran Pertama Tahunan',
            'yearly' => 'Bayaran Tahunan',
            default => ucfirst(str_replace('_', ' ', $payment->payment_type)),
        };

        $paymentPlanLabel = match($payment->payment_plan) {
            'monthly' => 'Pelan Bulanan',
            'yearly' => 'Pelan Tahunan',
            default => ucfirst($payment->payment_plan),
        };

        $statusClass = match(strtolower($payment->status)) {
            'paid' => 'bg-success',
            'pending' => 'bg-warning text-dark',
            'failed' => 'bg-danger',
            'cancelled' => 'bg-secondary',
            default => 'bg-secondary',
        };

        $statusLabel = match(strtolower($payment->status)) {
            'paid' => 'Berjaya',
            'pending' => 'Menunggu Bayaran',
            'failed' => 'Gagal',
            'cancelled' => 'Dibatalkan',
            default => ucfirst($payment->status),
        };

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

        $periodLabel = $periodLabels->isNotEmpty()
            ? $periodLabels->implode(', ')
            : '-';

        $memberName = auth()->user()->name ?? '-';
    @endphp

    <div class="receipt-card">
        <div class="receipt-header">
            <div class="row align-items-center g-3">
                <div class="col-md-8">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width:56px;height:56px;border-radius:14px;background:rgba(255,255,255,.18);display:flex;align-items:center;justify-content:center;">
                            <i class="bx bx-receipt fs-2"></i>
                        </div>
                        <div>
                            <h3 class="mb-1 fw-bold">Resit Bayaran</h3>
                            <p class="mb-0 opacity-75">Sistem Pengurusan Khairat Kematian e-Pusara</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-md-end">
                    <div class="mb-2">
                        <span class="badge {{ $statusClass }} receipt-badge">
                            {{ strtoupper($statusLabel) }}
                        </span>
                    </div>
                    <div class="small opacity-75">No. Resit</div>
                    <h5 class="fw-bold mb-0">{{ $payment->receipt_no ?? '-' }}</h5>
                </div>
            </div>
        </div>

        <div class="p-4 p-md-5">
            <div class="row g-4 mb-4">
                <div class="col-lg-8">
                    <div class="receipt-info-box">
                        <div class="receipt-section-title">Maklumat Transaksi</div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="receipt-label">Nama Ahli</div>
                                <div class="receipt-value">{{ $memberName }}</div>
                            </div>

                            <div class="col-md-6">
                                <div class="receipt-label">Pelan Bayaran</div>
                                <div class="receipt-value">{{ $paymentPlanLabel }}</div>
                            </div>

                            <div class="col-md-6">
                                <div class="receipt-label">Jenis Bayaran</div>
                                <div class="receipt-value">{{ $paymentTypeLabel }}</div>
                            </div>

                            <div class="col-md-6">
                                <div class="receipt-label">Tempoh Bayaran</div>
                                <div class="receipt-value">{{ $periodLabel }}</div>
                            </div>

                            <div class="col-md-6">
                                <div class="receipt-label">Tarikh Bayaran</div>
                                <div class="receipt-value">
                                    {{ $payment->paid_at ? \Carbon\Carbon::parse($payment->paid_at)->format('d/m/Y h:i A') : '-' }}
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="receipt-label">Kaedah Bayaran</div>
                                <div class="receipt-value">{{ ucfirst($payment->payment_method ?? 'manual') }}</div>
                            </div>

                            <div class="col-md-12">
                                <div class="receipt-label">No. Rujukan</div>
                                <div class="receipt-value">{{ $payment->reference_no ?? '-' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="receipt-total-box">
                        <div class="text-muted small mb-2">Jumlah Bayaran</div>
                        <div class="amount">RM{{ number_format($payment->amount, 2) }}</div>
                        <div class="mt-3">
                            <span class="badge bg-info-subtle text-info px-3 py-2">
                                {{ $paymentTypeLabel }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="receipt-info-box mb-4">
                <div class="receipt-section-title">Pecahan Bayaran</div>

                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Jenis Yuran</th>
                                <th>Tempoh</th>
                                <th class="text-end">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payment->items as $item)
                                @php
                                    $itemTypeLabel = match($item->payment_type) {
                                        'registration' => 'Yuran Pendaftaran',
                                        'monthly' => 'Yuran Bulanan',
                                        'yearly' => 'Yuran Tahunan',
                                        default => ucfirst(str_replace('_', ' ', $item->payment_type)),
                                    };

                                    $itemPeriodLabel = '-';

                                    if ($item->payment_type === 'monthly' && $item->payment_period) {
                                        try {
                                            $itemPeriodLabel = \Carbon\Carbon::createFromFormat('Y-m', $item->payment_period)->translatedFormat('F Y');
                                        } catch (\Throwable $e) {
                                            $itemPeriodLabel = $item->payment_period;
                                        }
                                    } elseif ($item->payment_type === 'yearly' && $item->payment_period) {
                                            try {
                                                if (str_contains($item->payment_period, '_to_')) {
                                                    [$startPeriod, $endPeriod] = explode('_to_', $item->payment_period);

                                                    $startLabel = \Carbon\Carbon::createFromFormat('Y-m', $startPeriod)->translatedFormat('F Y');
                                                    $endLabel = \Carbon\Carbon::createFromFormat('Y-m', $endPeriod)->translatedFormat('F Y');

                                                    $itemPeriodLabel = $startLabel . ' - ' . $endLabel;
                                                } else {
                                                    $itemPeriodLabel = $item->payment_period;
                                                }
                                            } catch (\Throwable $e) {
                                                $itemPeriodLabel = $item->payment_period;
                                            }
                                        }
                                @endphp

                                <tr>
                                    <td>{{ $itemTypeLabel }}</td>
                                    <td>{{ $itemPeriodLabel }}</td>
                                    <td class="text-end">RM{{ number_format($item->amount, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="2" class="text-end">Jumlah Keseluruhan</th>
                                <th class="text-end text-info">
                                    RM{{ number_format($payment->amount, 2) }}
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="receipt-info-box mb-4">
                <div class="receipt-section-title">Butiran Resit</div>

                <div class="table-responsive">
                    <table class="table table-bordered align-middle receipt-table mb-0">
                        <tbody>
                            <tr>
                                <th>No. Resit</th>
                                <td>{{ $payment->receipt_no ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>No. Rujukan</th>
                                <td>{{ $payment->reference_no ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Jenis Bayaran</th>
                                <td>{{ $paymentTypeLabel }}</td>
                            </tr>
                            <tr>
                                <th>Pelan</th>
                                <td>{{ $paymentPlanLabel }}</td>
                            </tr>
                            <tr>
                                <th>Tempoh</th>
                                <td>{{ $periodLabel }}</td>
                            </tr>
                            <tr>
                                <th>Jumlah</th>
                                <td class="fw-bold text-info">RM{{ number_format($payment->amount, 2) }}</td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>
                                    <span class="badge {{ $statusClass }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Catatan</th>
                                <td>{{ $payment->notes ?? 'Tiada catatan.' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div class="receipt-footer-note">
                    Resit ini dijana oleh sistem dan sah sebagai bukti rekod bayaran.
                </div>

                <div class="text-md-end">
                    <div class="fw-semibold">Terima kasih</div>
                    <div class="text-muted small">Sila simpan resit ini untuk rujukan anda.</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection