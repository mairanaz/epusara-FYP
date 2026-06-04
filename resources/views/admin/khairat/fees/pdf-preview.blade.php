<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Laporan Yuran</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            color: #111827;
            margin: 30px;
            font-size: 13px;
        }

        .header {
            text-align: center;
            margin-bottom: 24px;
            border-bottom: 2px solid #111827;
            padding-bottom: 14px;
        }

        .header h2 {
            margin: 0;
            font-size: 22px;
        }

        .header p {
            margin: 6px 0 0;
            color: #4b5563;
        }

        .summary {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 22px;
        }

        .summary-card {
            border: 1px solid #d1d5db;
            padding: 12px;
            border-radius: 8px;
        }

        .summary-card .label {
            color: #6b7280;
            font-size: 12px;
            margin-bottom: 6px;
        }

        .summary-card .value {
            font-weight: bold;
            font-size: 18px;
        }

        .section-title {
            font-size: 16px;
            font-weight: bold;
            margin: 24px 0 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        th, td {
            border: 1px solid #d1d5db;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background: #f3f4f6;
            font-weight: bold;
        }

        .text-end {
            text-align: right;
        }

        .print-area {
            margin-bottom: 20px;
            text-align: right;
        }

        .btn-print {
            background: #111827;
            color: white;
            padding: 10px 18px;
            border-radius: 8px;
            border: 0;
            cursor: pointer;
            font-weight: bold;
        }

        @media print {
            .print-area {
                display: none;
            }

            body {
                margin: 12px;
            }
        }
    </style>
</head>
<body>

    <div class="print-area">
        <button onclick="window.print()" class="btn-print">Cetak / Save PDF</button>
    </div>

    <div class="header">
        <h2>Laporan Yuran Khairat</h2>
        <p>
            Tahun {{ $currentYear }}
            @if($currentMonth)
                · Bulan {{ str_pad($currentMonth, 2, '0', STR_PAD_LEFT) }}
            @endif
        </p>
        <p>Dijana pada {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <div class="summary">
        <div class="summary-card">
            <div class="label">Jumlah Kutipan</div>
            <div class="value">RM {{ number_format($totalAmount, 2) }}</div>
        </div>

        <div class="summary-card">
            <div class="label">Transaksi Berjaya</div>
            <div class="value">{{ $paidCount }}</div>
        </div>

        <div class="summary-card">
            <div class="label">Pending</div>
            <div class="value">{{ $pendingCount }}</div>
        </div>

        <div class="summary-card">
            <div class="label">Jumlah Ahli</div>
            <div class="value">{{ $totalMembers }}</div>
        </div>
    </div>

    <div class="section-title">Pecahan Jenis Yuran</div>

    <table>
        <thead>
            <tr>
                <th>Jenis Yuran</th>
                <th class="text-end">Bil. Transaksi</th>
                <th class="text-end">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @foreach($feeTypes as $feeType)
                @continue(($feeType['amount'] ?? 0) <= 0 && ($feeType['count'] ?? 0) <= 0)

                <tr>
                    <td>{{ $feeType['name'] }}</td>
                    <td class="text-end">{{ $feeType['count'] }}</td>
                    <td class="text-end">RM {{ number_format($feeType['amount'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="section-title">Senarai Transaksi</div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Ahli</th>
                <th>No KP</th>
                <th>Jenis Yuran</th>
                <th>Status</th>
                <th>No Rujukan</th>
                <th class="text-end">Amaun</th>
            </tr>
        </thead>
        <tbody>
            @forelse($payments as $index => $payment)
                @php
                    $user = $payment->user;
                @endphp

                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $user?->name ?? '-' }}</td>
                    <td>{{ $user?->no_kp ?? data_get($user, 'profile.no_kp') ?? '-' }}</td>
                    <td>{{ app(\App\Http\Controllers\Admin\AdminKhairatFeeController::class)->getFeeTypeForView($payment) }}</td>
                    <td>{{ strtoupper($payment->status ?? '-') }}</td>
                    <td>{{ $payment->reference_no ?? $payment->receipt_no ?? '-' }}</td>
                    <td class="text-end">RM {{ number_format($payment->amount ?? 0, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align:center;">Tiada rekod transaksi dijumpai.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>