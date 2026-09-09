@php
    $subtotal_1=0;
    $subtotal_2=0;
@endphp

<!DOCTYPE html>
<html lang="es">
<!-- begin::Head -->

<head>
    <meta charset="utf-8" />
    <title>Punto de Distribución</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="">
    <meta name="viewport"
        content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0, shrink-to-fit=no">

    <!--begin::Global Theme Styles -->
    <link href="{{ asset('backend/css/cotizacion.css') }}" rel="stylesheet" type="text/css" />
    <!--end::Global Theme Styles -->
</head>
<!-- end::Head -->

<!-- begin::Body -->

<body class="">

    @switch($cotizacion->company_id)
        @case(1)
            <img src="{{ asset('backend/img/logo-pdf-puntod.png') }}" alt="">
            @break
        @case(2)
            <img src="{{ asset('backend/img/logo-pdf-cordia.png') }}" alt="">
            @break
        @case(3)
            <img src="{{ asset('backend/img/logo-pdf-global.jpeg') }}" alt="">
            @break
        @case(4)
            <img src="{{ asset('backend/img/logo-pdf-blueline.jpeg') }}" alt="">
            @break
        @case(5)
            <img src="{{ asset('backend/img/logo-pdf-puntom.jpeg') }}" alt="">
            @break
        @default
            <img src="#" alt="">
            @break
    @endswitch
    <hr>

    <div class="title">
        COTIZACION  <br> {{ $cotizacion->serie_cot }}
    </div>
    <hr>

    <div>
        <div>
            <table>
                <tr>
                    <td>{{ $cotizacion->company_name }}</td>       
                </tr>
                <tr>
                    <td>{{ $cotizacion->company_document_type }} {{ $cotizacion->company_document_number }}</td>
                </tr>
                <tr>
                    <td>{{ $cotizacion->company_address }}</td>
                </tr>
                <tr>
                    <td>ORDEN DE TRABAJO : {{ $cotizacion->serie_ot }}</td>
                </tr>
            </table>
        </div>
    </div>
    <hr>

    <div>
        <table class="provider">
            <thead>
                <tr>
                    <td style="font-weight: bold">Cliente:</td>
                    <td>{{ $cotizacion->client_name }}</td>
                    <td style="font-weight: bold">F. Cotización:</td>
                    <td>{{ $cotizacion->cot_issue_date }}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold">Dirección:</td>
                    <td>{{ $cotizacion->client_adress }}</td>
                    <td style="font-weight: bold">F. Vigencia:</td>
                    <td>{{ $cotizacion->cot_expiry_date }}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold">Teléfono:</td>
                    <td>{{ $cotizacion->client_phone_1 ? $cotizacion->client_phone_1 : $cotizacion->client_phone_2 }}</td>
                    <td style="font-weight: bold">Moneda:</td>
                    <td>{{ $cotizacion->currency_name }}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold">Correo:</td>
                    <td>{{ $cotizacion->client_email }}</td>
                    <td style="font-weight: bold">Cambio:</td>
                    <td>{{ $cotizacion->tc }}</td>
                </tr>
            </thead>
        </table>
    </div>
    <br>
    <div>
        <div style="padding: 2% 2% 2% 2%;"><b>Mano de Obra / Servicio</b></div>
        <table class="detail">
            <thead style="background-color: black">
                <tr>
                    <th style="width: 10%; color: white">#</th>
                    <th style="width: 20%; color: white">DESCRIPCIÓN</th>
                    <th style="width: 10%; color: white">UNIDAD</th>
                    <th style="width: 10%; color: white">CANTIDAD</th>
                    <th style="width: 10%; color: white">PRECIO</th>
                    <th style="width: 10%; color: white">Importe</th>
                    <th style="width: 10%; color: white">DESC %</th>
                    <th style="width: 10%; color: white">DESC</th>
                    <th style="width: 10%; color: white">VALOR VENTA</th>
                </tr>
            </thead>
            <tbody>
                @foreach($services as $obj)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $obj->name }}</td>
                    <td>UNID</td>
                    <td>{{ number_format($obj->quantity, 0, '.', '') }}</td>
                    <td>{{ number_format($obj->price, 2, '.', '') }}</td>
                    <td>{{ number_format($obj->import, 2, '.', '') }}</td>
                    <td>{{ number_format($obj->desc_percentage, 2, '.', '') }}</td>
                    <td>{{ number_format($obj->desc, 2, '.', '') }}</td>
                    <td>{{ number_format($obj->neto, 2, '.', '') }}</td>
                </tr>
                    @php
                        $subtotal_1+=$obj->neto;
                    @endphp
                @endforeach
            </tbody>
            
            <tfoot>
                <tr>
                    <th colspan="8">Subtotal</th>
                    <th>{{ number_format($subtotal_1, 2, '.', '') }}</th>
                </tr>
            </tfoot>
            
        </table>
    </div>
    <br>
    <div>
        <div style="padding: 2% 2% 2% 2%;"><b>Repuesto</b></div>
        <table class="detail">
            <thead style="background-color: black">
                <tr>
                    <th style="width: 10%; color: white">#</th>
                    <th style="width: 20%; color: white">DESCRIPCIÓN</th>
                    <th style="width: 10%; color: white">UNIDAD</th>
                    <th style="width: 10%; color: white">CANTIDAD</th>
                    <th style="width: 10%; color: white">PRECIO</th>
                    <th style="width: 10%; color: white">Importe</th>
                    <th style="width: 10%; color: white">DESC %</th>
                    <th style="width: 10%; color: white">DESC</th>
                    <th style="width: 10%; color: white">VALOR VENTA</th>
                </tr>
            </thead>
            <tbody>
                @foreach($articles as $obj)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $obj->name }}</td>
                    <td>UNID</td>
                    <td>{{ number_format($obj->quantity, 0, '.', '') }}</td>
                    <td>{{ number_format($obj->price, 2, '.', '') }}</td>
                    <td>{{ number_format($obj->import, 2, '.', '') }}</td>
                    <td>{{ number_format($obj->desc_percentage, 2, '.', '') }}</td>
                    <td>{{ number_format($obj->desc, 2, '.', '') }}</td>
                    <td>{{ number_format($obj->neto, 2, '.', '') }}</td>
                </tr>
                @php
                        $subtotal_2+=$obj->neto;
                    @endphp
                @endforeach
            </tbody>
            
            <tfoot>
                <tr>
                    <th colspan="8">Subtotal</th>
                    <th>{{ number_format($subtotal_2, 2, '.', '') }}</th>
                </tr>
            </tfoot>
            
        </table>
    </div>
    
    <br>
    <div>
        <div style="padding: 2% 2% 2% 2%;"><b>Pago Total</b></div>
        <table class="detail">
            <thead style="background-color: black">
                <tr>
                    <th style="width: 10%; border-color: black"></th>
                    <th style="width: 20%; border-color: black"></th>
                    <th style="width: 10%; border-color: black"></th>
                    <th style="width: 10%; border-color: black"></th>
                    <th style="width: 10%; border-color: black"></th>
                    <th style="width: 10%; border-color: black"></th>
                    <th style="width: 10%; border-color: black"></th>
                    <th style="width: 10%; border-color: black"></th>
                    <th style="width: 10%; border-color: black"></th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th colspan="6"></th>
                    <th colspan="2">Total Valor de Venta</th>
                    <th>{{ number_format($cotizacion->cot_subtotal, 2, '.', '') }}</th>
                </tr>
                <tr>
                    <th colspan="6"></th>
                    <th colspan="2">IGV</th>
                    <th>{{ number_format($cotizacion->cot_igv, 2, '.', '') }}</th>
                </tr>
                <tr>
                    <th colspan="6"></th>
                    <th colspan="2">Importe Total</th>
                    <th>{{ number_format($cotizacion->cot_total, 2, '.', '') }}</th>
                </tr>
                <tr>
                    <th colspan="9">{{ $total_text }}</th>
                </tr>
            </tfoot>
            
        </table>
    </div>

    <script type="text/php">
        if ( isset($pdf) ) {
            $pdf->page_script('
                $font = $fontMetrics->get_font("Arial, Helvetica, sans-serif", "normal");
                $pdf->text(400, 700, "______________________", $font, 9, array(0.349, 0.3647, 0.4314));
                $pdf->text(400, 715, "         V° B°", $font, 9, array(0.349, 0.3647, 0.4314));
            ');
        }
        if ( isset($pdf) ) {
            $pdf->page_script('
                $font = $fontMetrics->get_font("Arial, Helvetica, sans-serif", "normal");
                $pdf->text(488, 63, "Página $PAGE_NUM/$PAGE_COUNT", $font, 9, array(0.349, 0.3647, 0.4314));
            ');
        }
    </script>
</body>
</html>
