<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>REPORTE DE VENTA DIARIA</title>
    <style>
        body {
            font-family: "Inter","Segoe UI",Arial,sans-serif;
            color: #2c3e50;
            background: #fff;
            font-size: 11px;
            margin: 10px;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
        }
        .header h1 {
            font-size: 18px;
            margin: 0;
            font-weight: 700;
            color: #003366;
            text-transform: uppercase;
            letter-spacing: .5px;
        }
        .header .info {
            margin-top: 3px;
            font-size: 11px;
            color: #555;
            line-height: 1.2;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            line-height: 1.2;
        }

        thead tr {
            background-color: #333333; 
            color: #ffffff;           
        }

        th {
            padding: 6px 4px;
            text-align: left;
            font-weight: 700;
            font-size: 10px;
            text-transform: uppercase;
            border-bottom: 1px solid #222222; 
            white-space: normal;
            word-break: break-word;
        }

        tbody tr {
            border-bottom: 0.5px solid #eaeaea;
        }

        td {
            padding: 4px 3px;
        }

        tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        tbody tr:hover {
            background-color: #eef6ff;
        }


        .totales td {
            font-weight: 700;
            background-color: #e2e3e5; 
            border-top: 1px solid #333333;
            padding-top: 4px;
            padding-bottom: 4px;
            font-size: 11px;
        }

        .text-right { text-align: right; }
    </style>
</head>
<body>

    <div class="header">
        <h1>REPORTE DE VENTA DIARIA</h1>
        <div class="info">
            <strong>Fecha:</strong> {{ \Carbon\Carbon::now()->format('Y-m-d') }} <br>
            <strong>Vendedor: ______________________________</strong> <br>
            <strong>Zona: __________________________________</strong> <br>
            <strong>#{{ $warehouse_movement->movement_number }}</strong> 
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>CODIGO CLIENTE</th>
                <th>TIPO COMPROBANTE</th>
                <th>CLIENTE</th>
                <th>PEDIDO</th>
                <th class="text-right">TOTAL VENTA</th>
                <th class="text-right">EFECTIVO</th>
                <th class="text-right">DEPOSITO</th>
                <th class="text-right">DESCUENTO</th>
                <th class="text-right">CREDITO</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sales as $sale)
            <tr>
                <td>{{ $sale->client_id }}</td>
                <td>{{ $sale->warehouse_document_type_short_name }}</td>
                <td>{{ $sale->client_full_name }}</td>
                <td>{{ $sale->guia_electronica }}</td>
                <td class="text-right">{{ number_format($sale->total_perception,2) }}</td>
                <td class="text-right">{{ number_format($sale->total_efectivo,2) }}</td>
                <td class="text-right">{{ number_format($sale->total_deposito,2) }}</td>
                <td class="text-right">{{ number_format($sale->total_descuento,2) }}</td>
                <td class="text-right">{{ number_format($sale->total_credito,2) }}</td>
            </tr>
            @endforeach
            <tr class="totales">
                <td colspan="4" class="text-right">Totales:</td>
                <td class="text-right">{{ number_format($totalVenta,2) }}</td>
                <td class="text-right">{{ number_format($totalEfectivo,2) }}</td>
                <td class="text-right">{{ number_format($totalDeposito,2) }}</td>
                <td class="text-right">{{ number_format($totalDescuento,2) }}</td>
                <td class="text-right">{{ number_format($totalCredito,2) }}</td>
            </tr>
        </tbody>
    </table>

    <hr>
    <br><br>
    <!-- Cobranzas -->
    <div class="header">
        <h1>REPORTE DE COBRANZAS</h1>
    </div>

    <table>
        <thead>
            <tr>
                <th>CODIGO CLIENTE</th>
                <th>TIPO COMPROBANTE</th>
                <th>CLIENTE</th>
                <th>FECHA EMISIÓN</th>
                <th>TOTAL VENTA</th>
                <th>MÉTODO DE PAGO</th>
                <th>BANCO</th>
                <th>N° OPERACIÓN</th>
                <th>MONTO LIQUIDADO</th>
                
            </tr>
        </thead>
        <tbody>
            @foreach ($cobranzas as $cobranza)
            <tr>
                <td>{{ $cobranza->client_id }}</td>
                <td>{{ $cobranza->warehouse_document_type_name }}</td>
                <td>{{ $cobranza->client_name }}</td>
                <td>{{ $cobranza->sale_date }}</td>
                <td class="text-right">{{ number_format($cobranza->total_perception,2) }}</td>
                <td>{{ $cobranza->payment_method_name }}</td>
                <td>{{ $cobranza->bank_account_name }}</td>
                <td>{{ $cobranza->operation_number }}</td>
                <td class="text-right">{{ number_format($cobranza->liquidation_amount,2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <hr>
    <br><br>
    <!-- Depositos registrados -->
    <div class="header">
        <h1>REPORTE DE DEPOSITOS REGISTRADOS</h1>
    </div>

    <table>
        <thead>
            <tr>
                <th>COMPAÑÍA</th>
                <th>FECHA VOUCHER</th>
                <th>BANCO</th>
                <th>N° OPERACIÓN</th>
                <th>MONTO DEPOSITADO</th>
                <th>SALDO PENDIENTE</th>
                <th>MONTO USADO</th>
                
            </tr>
        </thead>
        <tbody>
            @foreach ($deposits as $deposit)
            <tr>
                <td>{{ $deposit->company_name }}</td>
                <td>{{ $deposit->bank_date }}</td>
                <td>{{ $deposit->bank_account_name }}</td>
                <td>{{ $deposit->operation_number }}</td>
                <td class="text-right">{{ number_format($deposit->total,2) }}</td>
                <td class="text-right">{{ number_format($deposit->total_pend,2) }}</td>
                <td class="text-right">{{ number_format($deposit->paid,2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <hr>
    <br><br>
    <!-- Resumen -->
    <div class="header">
        <h1>RESUMEN</h1>
    </div>

    <table>
        <tbody>
            <tr>
                <td>TOTAL EFECTIVO</td>
                <td class="text-right">{{ number_format($totalEfectivo,2) }}</td>
            </tr>
            <tr>
                <td>TOTAL DEPOSITO</td>
                <td class="text-right">{{ number_format($totalDeposito,2) }}</td>
            </tr>
        </tbody>
    </table>

</body>
</html>
