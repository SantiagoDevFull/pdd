<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CostGlpReportExport implements FromCollection, WithTitle, WithHeadings, ShouldAutoSize, WithMapping, WithColumnFormatting, WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */

    protected $collection;
    

    public function __construct($collection)
    {
        $this->collection = $collection;
    
    }

    public function collection()
    {
        return $this->collection;
    }

    public function title(): string
    {
        return 'Reporte de Costo de GLP 2024';
    }

    public function headings(): array
    {       
            $array = [
                'Mes',
                'Compañía',
                'Fecha de Emisión',
                'N° de Factura',
                'Proveedor',
                'Producto',
                'Nro de Guia',
                'Cisterna',
                'Tracto',
                'Cantidad',
                'Precio Kg soles',
                'Soles',
                'Carga Terminal',
                'SCOP',
                'Conductor',
                'TM',
                'N° de Pedido',
                'Precio Dolares',
                'N° Orden de Pedido',
                'Costo de Aprov.',               
                'Valor Venta',
                'I.G.V',
                'Precio Ponderado',
                'Destino',
                'Mezcla',             
                'Part',
            ];
        
        return $array;
    }

    public function CostGlp(): float
    {
        return $this->Soles() / $this->Quantity() * 10;
    }

    public function Quantity(): float
    {
        $quantity = $this->collection->sum('quantity');
        return $quantity;
    }
    public function Soles(): float
    {
        $soles = $this->collection->sum('soles');
        return $soles;
    }

    public function map($collection): array
    {

            $array = [
                $collection->price_mes,
                $collection->company_short_name,
                $collection->issue_date,
                $collection->factura,                
                $collection->warehouse_name,
                $collection->article_name,
                $collection->referral_guide,
                $collection->license_plate,
                $collection->license_plate_2,
                $collection->quantity,
                $collection->price_kg,
                $collection->soles,
                $collection->warehouse_short_name,
                $collection->scop_number,
                $collection->account_name,
                $collection->tm,
                $collection->pedido_fact,
                $collection->price_dol,
                $collection->pedido_m,
                $collection->aprov,
                $collection->sub_total,
                $collection->igv,
                $collection->pond,
                $collection->destiny,
                $collection->mezcla,
                $collection->partition,
            ];
        
        
        return $array;
    }

    public function columnFormats(): array
    {
        
            return [
                'C' => NumberFormat::FORMAT_DATE_DDMMYYYY,
                'J' => NumberFormat::FORMAT_NUMBER,
            ];
        
    }

    public function styles(Worksheet $sheet)
    {
        $styleArray = [
            'font' => [
                'bold' => true,
                'size'  => 13,
                'color' => array('rgb' => '5F3BEE'),
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'BCB1EE'],
            ],
        ];

        $sheet->getStyle('A1:Z1')->applyFromArray($styleArray);
        $sheet->setAutoFilter('A1:Z1');

        $col = $sheet->getHighestRow('Z');
        for ($row = 2; $row <= $col; $row++) {
            $cellValue = $sheet->getCell("Z{$row}")->getValue();

            if ($cellValue == 'SI') {
                $cellStyle = [
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => '8BEDF3'],
                    ],
                ];
            } else if($cellValue == 'NO') {
                $cellStyle = [
                    'fill' => [
                        //'fillType' => Fill::FILL_SOLID,
                        //'startColor' => ['argb' => 'BCB1EE'],
                    ],
                ];
            }
            $sheet->getStyle("A{$row}:Z{$row}")->applyFromArray($cellStyle);
        }

        $sheet->getRowDimension(1)->setRowHeight(30);
        $rowQuantity = $sheet->getHighestRow('J') + 2;

        $cellStyle = [
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => '8BEDF3'],
            ],
        ];

        $sheet->getStyle("H{$rowQuantity}")->applyFromArray($cellStyle);
        $sheet->getStyle("J{$rowQuantity}")->applyFromArray($cellStyle);
        $sheet->getStyle("L{$rowQuantity}")->applyFromArray($cellStyle);

        $sheet->setCellValue("H{$rowQuantity}","Avance");
        $sheet->setCellValue("J{$rowQuantity}",$this->Quantity());
        $sheet->setCellValue("L{$rowQuantity}",$this->Soles());

        $rowQuantity=$rowQuantity+2;
        $sheet->setCellValue("H{$rowQuantity}","Costo GLP");
        $sheet->setCellValue("K{$rowQuantity}",$this->CostGlp());

        $sheet->getStyle("K{$rowQuantity}")->applyFromArray($cellStyle);
        $sheet->getStyle("H{$rowQuantity}")->applyFromArray($cellStyle);

    }

}
