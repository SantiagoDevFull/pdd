<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;

class OtherCashReportExport implements FromArray, WithTitle, WithHeadings, ShouldAutoSize, WithMapping
{
    protected $collection;
    protected $type;

    public function __construct(array $collection, $type)
    {
        $this->collection = $collection;
        $this->type = $type;
    }

    public function array(): array
    {
        return $this->collection;
    }

    public function title(): string
    {
        return 'Reporte de Ingresos de Caja';
    }

    public function headings(): array
    {
        if ($this->type == 1) {
            return [
                'Compañía',
                'ID Cliente',
                'Doc. Cliente',
                'Ruta',
                'N° Doc.',
                'Razón Social',
                'Origen',
                'Serie',
                'Fecha de Cobranza',
                'Fecha de Emisión',
                'Total Venta',
                '#',
                'Concepto',
                'Cantidad',
                'Precio',
                'SubTotal',

            ];
        } else {
            return [
                'Compañía',
                'ID Cliente',
                'Doc. Cliente',
                'Ruta',
                'N° Doc.',
                'Razón Social',
                'Origen',
                'Serie',
                'Fecha de Cobranza',
                'Fecha de Emisión',
                'Total Venta',
                'Condición de Pago',
                'Forma de Pago',
                'Moneda',
                'Banco',
                'N° Operación',
                'Monto',

            ];
        }
    }

    public function map($collection): array
    {
        if ($this->type == 1) {
            return [
                $collection['company_short_name'],
                $collection['sale_client_id'],
                $collection['document_type_name'],
                $collection['client_route_name'],
                $collection['client_document_number'],
                $collection['client_name'],
                $collection['warehouse_document_type_name'],
                $collection['referral_serie_number'] . '-' . $collection['referral_voucher_number'],
                $collection['issue_date'],
                $collection['issue_date'],
                $collection['sale_total_perception'],
                $collection['item_number'],
                $collection['concept'],
                $collection['quantity'],
                $collection['price_igv'],
                $collection['subtotal'],
            ];
        } else {
            return [
                $collection['company_short_name'],
                $collection['sale_client_id'],
                $collection['document_type_name'],
                $collection['client_route_name'],
                $collection['client_document_number'],
                $collection['client_name'],
                $collection['warehouse_document_type_name'],
                $collection['referral_serie_number'] . '-' . $collection['referral_voucher_number'],
                $collection['issue_date'],
                $collection['issue_date'],
                $collection['sale_total_perception'],
                $collection['payment_name'],
                $collection['payment_mathod_name'],
                $collection['currency_name'],
                $collection['bank_short_name'] . ' - ' . $collection['bank_account_number'],
                $collection['liquidation_operation_number'],
                $collection['liquidation_amount']
            ];
        }
    }
}
