<?php

namespace App\Exports;

use App\Models\Purchase;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PurchaseExport implements
    FromArray,
    WithHeadings,
    WithTitle,
    ShouldAutoSize,
    WithEvents,
    WithColumnFormatting
{
    public function array(): array
    {
        $purchases = Purchase::with(['member', 'user', 'purchaseDetails.product'])->get();

        $rows = [];

        foreach ($purchases as $purchase) {
            foreach ($purchase->purchaseDetails as $detail) {
                $rows[] = [
                    $purchase->id,
                    optional($purchase->member)->name ?? 'NON-MEMBER',
                    optional($purchase->member)->no_phone ?? '-',
                    optional($purchase->member)->poin ?? 0,
                    $purchase->created_at->format('Y-m-d H:i'),
                    $purchase->total_price,
                    optional($detail->product)->name ?? '-',
                    $detail->quantity,
                    $detail->price,
                    $detail->quantity * $detail->price,
                    optional($purchase->user)->name ?? '-',
                ];
            }
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nama Pelanggan',
            'Nomor HP Pelanggan',
            'Poin Pelanggan',
            'Tanggal Penjualan',
            'Total Harga Pembelian',
            'Nama Produk',
            'Qty',
            'Harga Produk',
            'Subtotal',
            'Dibuat Oleh',
        ];
    }

    public function title(): string
    {
        return 'Data Penjualan';
    }

    public function columnFormats(): array
    {
        return [
            'F' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, // Total Harga
            'I' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, // Harga Produk
            'J' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, // Subtotal
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                /** @var Worksheet $sheet */
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                // Tambah judul laporan di baris 1
                $sheet->insertNewRowBefore(1, 1);
                $sheet->setCellValue('A1', 'Laporan Data Penjualan');
                $sheet->mergeCells("A1:{$highestColumn}1");

                // Format judul
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 14,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // Format border dan alignment seluruh data
                $sheet->getStyle("A2:{$highestColumn}" . ($highestRow + 1))->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);
            },
        ];
    }
}
