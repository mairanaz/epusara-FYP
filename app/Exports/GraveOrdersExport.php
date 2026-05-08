<?php

namespace App\Exports;

use App\Models\GraveOrder;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class GraveOrdersExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithEvents, WithColumnFormatting
{
    protected ?string $status;
    protected ?string $search;

    public function __construct(?string $status = 'approved', ?string $search = null)
    {
        $this->status = $status;
        $this->search = $search;
    }

    public function collection(): Collection
    {
        $query = GraveOrder::with(['user', 'deathReport', 'burialPlot'])
            ->latest();

        if ($this->status) {
            $query->where('status', $this->status);
        }

        if ($this->search) {
            $search = $this->search;

            $query->where(function ($q) use ($search) {
                $q->where('order_label', 'like', "%{$search}%")
                    ->orWhere('order_type', 'like', "%{$search}%")
                    ->orWhereHas('deathReport', function ($deathQuery) use ($search) {
                        $deathQuery->where('nama_si_mati', 'like', "%{$search}%")
                            ->orWhere('no_kp_si_mati', 'like', "%{$search}%")
                            ->orWhere('nama_pelapor', 'like', "%{$search}%")
                            ->orWhere('no_tel_pelapor', 'like', "%{$search}%")
                            ->orWhere('burial_plot_code', 'like', "%{$search}%")
                            ->orWhere('burial_lot_no', 'like', "%{$search}%");
                    })
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Bil',
            'Tarikh Tempahan',
            'Masa Tempahan',
            'Status',
            'Nama Si Mati',
            'No. KP Si Mati',
            'Jantina',
            'Umur',
            'Kategori',
            'No. Lot Kubur',
            'Jenis Tempahan',
            'Kod Tempahan',
            'Jumlah Harga (RM)',
            'Nama Waris',
            'No. KP Waris',
            'No. Telefon Waris',
            'Pertalian Waris',
            'Nama Akaun User',
            'Email User',
            'Tarikh Diluluskan',
            'Catatan Admin',
        ];
    }

    public function map($order): array
    {
        static $bil = 0;
        $bil++;

        $deathReport = $order->deathReport;
        $plot = $order->burialPlot;

        $lotNo = $plot->plot_code
            ?? $deathReport?->burial_plot_code
            ?? $deathReport?->burial_lot_no
            ?? '-';

        return [
            $bil,
            optional($order->created_at)->format('d/m/Y'),
            optional($order->created_at)->format('h:i A'),
            $order->statusLabel(),
            $deathReport->nama_si_mati ?? '-',
            $deathReport->no_kp_si_mati ?? '-',
            $deathReport->jantina ?? '-',
            $deathReport?->umur ? $deathReport->umur . ' tahun' : '-',
            $order->category === 'kanak-kanak' ? 'Kanak-kanak' : 'Dewasa',
            $lotNo,
            $order->order_label ?? '-',
            $order->order_type ?? '-',
            (float) $order->amount,
            $deathReport->nama_pelapor ?? '-',
            $deathReport->no_kp_pelapor ?? '-',
            $deathReport->no_tel_pelapor ?? '-',
            $deathReport->pertalian_pelapor ?? '-',
            $order->user->name ?? '-',
            $order->user->email ?? '-',
            $order->approved_at ? $order->approved_at->format('d/m/Y h:i A') : '-',
            $order->admin_note ?? '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '0D6EFD'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }

    public function columnFormats(): array
    {
        return [
            'M' => '#,##0.00',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                $sheet->freezePane('A2');

                $sheet->getStyle("A1:{$highestColumn}{$highestRow}")
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN)
                    ->getColor()
                    ->setRGB('E5E7EB');

                $sheet->getStyle("A1:{$highestColumn}{$highestRow}")
                    ->getAlignment()
                    ->setVertical(Alignment::VERTICAL_CENTER)
                    ->setWrapText(true);

                $sheet->getRowDimension(1)->setRowHeight(28);

                for ($row = 2; $row <= $highestRow; $row++) {
                    $sheet->getRowDimension($row)->setRowHeight(24);
                }

                $sheet->getStyle("A2:A{$highestRow}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->getStyle("M2:M{$highestRow}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                $sheet->getStyle("A1:{$highestColumn}1")
                    ->getAlignment()
                    ->setWrapText(true);

                $sheet->setAutoFilter("A1:{$highestColumn}{$highestRow}");
            },
        ];
    }
}