<?php

namespace App\Exports;

use App\Models\Purchase;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class PurchaseExport implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithColumnFormatting, WithEvents
{
    // Fetching the collection of purchase data along with its related models
    public function collection()
    {
        return Purchase::with(['member', 'user', 'purchaseDetails.product'])->get();
    }

    // Defining the headings for the exported Excel file
    public function headings(): array
    {
        return [
            'ID',                        // Purchase ID
            'Nama Pelanggan',             // Customer's Name
            'Nomor HP Pelanggan',         // Customer's Phone Number
            'Poin Pelanggan',             // Customer's Points
            'Tanggal Penjualan',          // Sale Date
            'Total Harga',                // Total Price
            'Produk',                     // Product Name
            'Dibuat Oleh',                // Created by (User)
        ];
    }

    // Mapping the data to be displayed in the Excel file
    public function map($purchase): array
    {
        $rows = [];

        foreach ($purchase->purchaseDetails as $detail) {
            // Add a new row for each product in the purchase
            $rows[] = [
                $purchase->id, // Purchase ID
                optional($purchase->member)->name ?? 'NON-MEMBER', // Customer Name
                optional($purchase->member)->no_phone ?? '-', // Customer Phone
                optional($purchase->member)->poin ?? 0, // Customer Points
                $purchase->created_at->format('Y-m-d'), // Sale Date
                $purchase->total_price, // Total Price
                $detail->product->name . ' (' . number_format($detail->price) . ' x ' . $detail->quantity . ')', // Product with price and quantity
                optional($purchase->user)->name ?? 'Petugas', // Created by (User)
            ];
        }

        return $rows;
    }

    // Defining the title for the Excel sheet
    public function title(): string
    {
        return 'Data Penjualan'; // Sheet Title
    }

    // Defining the column formatting for specific columns
    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_DATE_YYYYMMDD, // Date format for column E
            'F' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, // Format for total price column (F)
        ];
    }

    // Defining events for after the sheet is created, e.g., for styling and merging cells
   // Defining events for after the sheet is created, e.g., for styling and merging cells
public function registerEvents(): array
{
    return [
        AfterSheet::class => function (AfterSheet $event) {
            $sheet = $event->sheet;

            // Merge cells A1 to H1 for the title
            $sheet->mergeCells('A1:H1');

            // Set the title text for the sheet
            $sheet->setCellValue('A1', 'Laporan Transaksi Penjualan per Produk');

            // Apply styles to the title (bold, font size 16, centered alignment)
            $sheet->getStyle('A1')->applyFromArray([
                'font' => [
                    'bold' => true,
                    'size' => 16,
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);

            // Insert an empty row after the title for spacing
            $sheet->insertNewRowBefore(2, 1);

            // Styling for the heading row (A3:H3)
            $sheet->getStyle('A3:H3')->applyFromArray([
                'font' => ['bold' => true],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
            ]);
        },
    ];
}

}
