<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Surat Arahan Kerja Tempahan Kepuk / Nisan</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #111827;
            line-height: 1.4;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #111827;
            padding-bottom: 12px;
            margin-bottom: 18px;
        }

        .system-name {
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .subtitle {
            font-size: 12px;
            color: #374151;
            margin-top: 4px;
        }

        .document-title {
            margin-top: 12px;
            font-size: 15px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .meta-table,
        .summary-table,
        .work-table {
            width: 100%;
            border-collapse: collapse;
        }

        .meta-table td {
            padding: 4px 0;
            vertical-align: top;
        }

        .meta-label {
            width: 120px;
            font-weight: bold;
        }

        .section-title {
            font-size: 12px;
            font-weight: bold;
            margin-top: 18px;
            margin-bottom: 8px;
            text-transform: uppercase;
            background: #f3f4f6;
            padding: 7px;
            border: 1px solid #d1d5db;
        }

        .paragraph {
            text-align: justify;
            margin-bottom: 12px;
        }

        .summary-table th,
        .summary-table td {
            border: 1px solid #d1d5db;
            padding: 7px;
            text-align: left;
        }

        .summary-table th {
            background: #f9fafb;
            font-weight: bold;
            width: 35%;
        }

        .work-table th,
        .work-table td {
            border: 1px solid #d1d5db;
            padding: 6px;
            vertical-align: top;
        }

        .work-table th {
            background: #111827;
            color: #ffffff;
            font-weight: bold;
            text-align: center;
            font-size: 10px;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .small {
            font-size: 10px;
            color: #4b5563;
        }

        .note-box {
            border: 1px solid #d1d5db;
            background: #f9fafb;
            padding: 10px;
            margin-top: 10px;
        }

        .signature-section {
            margin-top: 35px;
            width: 100%;
        }

        .signature-box {
            width: 45%;
            display: inline-block;
            vertical-align: top;
        }

        .signature-line {
            margin-top: 55px;
            border-top: 1px solid #111827;
            padding-top: 6px;
            font-weight: bold;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            font-size: 9px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
            padding-top: 6px;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>

    <div class="header">
        <div class="system-name">E-Pusara</div>
        <div class="subtitle">Sistem Pengurusan Khairat Kematian & Tempahan Kepuk</div>
        <div class="document-title">Surat Arahan Kerja Tempahan Kepuk / Nisan</div>
    </div>

    <table class="meta-table">
        <tr>
            <td class="meta-label">No. Rujukan</td>
            <td>: {{ $summary['reference_no'] }}</td>
        </tr>
        <tr>
            <td class="meta-label">Tarikh Jana</td>
            <td>: {{ $summary['generated_at']->format('d/m/Y h:i A') }}</td>
        </tr>
        <tr>
            <td class="meta-label">Status Tempahan</td>
            <td>: {{ $summary['status_label'] }}</td>
        </tr>
    </table>

    <div class="section-title">Tujuan Surat</div>

    <p class="paragraph">
        Surat ini dijana sebagai arahan kerja kepada pihak pembuat kepuk / batu nisan bagi
        tempahan yang telah diluluskan oleh pihak pentadbiran. Pihak pembuat kepuk diminta
        merujuk senarai tempahan di bawah sebelum melaksanakan kerja berkaitan.
    </p>

    <div class="section-title">Ringkasan Tempahan</div>

    <table class="summary-table">
        <tr>
            <th>Jumlah Tempahan Diluluskan</th>
            <td>{{ $summary['total_orders'] }}</td>
        </tr>
        <tr>
            <th>Jumlah Nilai Tempahan</th>
            <td>RM{{ number_format($summary['total_amount'], 2) }}</td>
        </tr>
        <tr>
            <th>Kategori Dewasa</th>
            <td>{{ $summary['adult_count'] }}</td>
        </tr>
        <tr>
            <th>Kategori Kanak-kanak</th>
            <td>{{ $summary['child_count'] }}</td>
        </tr>
    </table>

    <div class="section-title">Senarai Kerja Tempahan Kepuk / Nisan</div>

    <table class="work-table">
        <thead>
            <tr>
                <th style="width: 4%;">Bil</th>
                <th style="width: 18%;">Nama Si Mati</th>
                <th style="width: 11%;">Lot Kubur</th>
                <th style="width: 10%;">Kategori</th>
                <th style="width: 24%;">Jenis Kepuk / Nisan</th>
                <th style="width: 17%;">Nama Waris</th>
                <th style="width: 12%;">No. Telefon</th>
            </tr>
        </thead>

        <tbody>
            @forelse($orders as $index => $order)
                @php
                    $deathReport = $order->deathReport;
                    $plot = $order->burialPlot;

                    $lotNo = $plot->plot_code
                        ?? $deathReport?->burial_plot_code
                        ?? $deathReport?->burial_lot_no
                        ?? '-';
                @endphp

                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $deathReport->nama_si_mati ?? '-' }}</td>
                    <td class="text-center">{{ $lotNo }}</td>
                    <td class="text-center">
                        {{ $order->category === 'kanak-kanak' ? 'Kanak-kanak' : 'Dewasa' }}
                    </td>
                    <td>{{ $order->order_label ?? '-' }}</td>
                    <td>{{ $deathReport->nama_pelapor ?? '-' }}</td>
                    <td>{{ $deathReport->no_tel_pelapor ?? '-' }}</td>
                </tr>

                @if(($index + 1) % 18 === 0 && !$loop->last)
                    </tbody>
                    </table>

                    <div class="page-break"></div>

                    <table class="work-table">
                        <thead>
                            <tr>
                                <th style="width: 4%;">Bil</th>
                                <th style="width: 18%;">Nama Si Mati</th>
                                <th style="width: 11%;">Lot Kubur</th>
                                <th style="width: 10%;">Kategori</th>
                                <th style="width: 24%;">Jenis Kepuk / Nisan</th>
                                <th style="width: 17%;">Nama Waris</th>
                                <th style="width: 12%;">No. Telefon</th>
                            </tr>
                        </thead>
                        <tbody>
                @endif
            @empty
                <tr>
                    <td colspan="7" class="text-center">
                        Tiada tempahan diluluskan untuk dipaparkan.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">Nota Arahan Kerja</div>

    <div class="note-box">
        <ol style="margin: 0; padding-left: 18px;">
            <li>Pihak pembuat kepuk / batu nisan diminta merujuk nombor lot kubur sebelum melaksanakan kerja.</li>
            <li>Sebarang perubahan jenis tempahan atau maklumat waris hendaklah dirujuk kepada pihak pentadbiran.</li>
            <li>Dokumen Excel yang disertakan boleh digunakan sebagai senarai kerja terperinci.</li>
        </ol>
    </div>

    <div class="signature-section">
        <div class="signature-box">
            <div>Disediakan oleh,</div>
            <div class="signature-line">Pentadbir E-Pusara</div>
            <div class="small">Tarikh: ____________________</div>
        </div>

        <div class="signature-box" style="margin-left: 8%;">
            <div>Diterima oleh,</div>
            <div class="signature-line">Pihak Pembuat Kepuk / Batu Nisan</div>
            <div class="small">Tarikh: ____________________</div>
        </div>
    </div>

    <div class="footer">
        Dokumen ini dijana secara automatik melalui Sistem E-Pusara.
    </div>

</body>
</html>