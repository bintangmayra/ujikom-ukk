<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductExport implements
    FromCollection,
    WithHeadings,
    WithTitle,
    ShouldAutoSize,
    WithStyles,
    WithMapping,
    WithDrawings,
    WithCustomStartCell,
    WithEvents
{
    private $products;

    public function __construct()
    {
        $this->products = Product::select('id', 'name', 'price', 'stock', 'image')->get();
    }

    public function collection()
    {
        return $this->products;
    }

    public function map($product): array
    {
        return [
            $product->id,
            $product->name,
            $product->price,
            $product->stock,
            '', // Placeholder untuk gambar
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nama Produk',
            'Harga',
            'Stok',
            'Gambar',
        ];
    }

    public function title(): string
    {
        return 'Daftar Produk';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            2 => [ // Styling header (karena baris 1 dipakai untuk judul)
                'font' => ['bold' => true],
            ],
            1 => [
                'font' => ['bold' => true, 'size' => 14],
            ],
        ];
    }

    public function drawings()
    {
        $drawings = [];
        foreach ($this->products as $index => $product) {
            if ($product->image && file_exists(public_path($product->image))) {
                $drawing = new Drawing();
                $drawing->setName('Product Image');
                $drawing->setDescription($product->name);
                $drawing->setPath(public_path($product->image));
                $drawing->setHeight(60);
                $drawing->setCoordinates('E' . ($index + 3)); // Baris ke-3 dan seterusnya (karena header di baris 2)
                $drawings[] = $drawing;
            }
        }

        return $drawings;
    }

    public function startCell(): string
    {
        return 'A2'; // Mulai data dari A2, karena A1 dipakai untuk judul
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->mergeCells('A1:E1');
                $event->sheet->setCellValue('A1', 'Laporan Data Produk');

                $event->sheet->getStyle('A1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 14,
                        'color' => ['rgb' => '000000'],
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                ]);
            },
        ];
    }
}
