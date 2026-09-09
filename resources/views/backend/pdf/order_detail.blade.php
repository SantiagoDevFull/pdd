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
    <link href="{{ asset('backend/css/order_detail.css') }}" rel="stylesheet" type="text/css" />
    <!--end::Global Theme Styles -->
</head>
<!-- end::Head -->

<!-- begin::Body -->

<body class="">
    @switch($orders->company_id)
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
        {{ $orders->orders_type }}
    </div>
    <hr>

    <div>
        <div>
            <table>
                <tr>
                    <td>{{ $orders->company_name }}</td>       
                </tr>
                <tr>
                    <td>{{ $orders->company_document_type }} {{ $orders->company_document_number }}</td>
                </tr>
                <tr>
                    <td>{{ $orders->company_address }}</td>
                </tr>
            </table>
        </div>
    </div>
    <hr>

    <div>
        <table class="provider">
            <thead>
                <tr>
                    <td style="font-weight: bold">FECHA DE ORDEN</td>
                    <td>{{ $orders->orders_order_date }}</td>
                    <td style="font-weight: bold">N°. ORDEN</td>
                    <td>{{ $orders->orders_id }}</td>
                    <td style="font-weight: bold">MONEDA</td>
                    <td>{{ $orders->currency_symbol }} {{ $orders->currency_name }}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold">PROVEEDOR</td>
                    <td>{{ $orders->provider_name }}</td>
                    <td style="font-weight: bold">{{ $orders->provider_document_type }}</td>
                    <td>{{ $orders->provider_document_number }}</td>
                    <td style="font-weight: bold">Tipo de Cambio</td>
                    <td>{{ $orders->orders_tc }}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold">DIRECCIÓN</td>
                    <td>{{ $orders->provider_address }}</td>
                    <td style="font-weight: bold">CÓDIGO PROV.</td>
                    <td>{{ $orders->provider_document_number }}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold">CONTACTO</td>
                    <td>{{ $orders->provider_email }}</td>
                    <td style="font-weight: bold">TELÉFONO</td>
                    <td>{{ $orders->provider_number_1 }}</td>
                </tr>
            </thead>
        </table>
    </div>
    <br>
    <div>
        <table class="detail">
            <thead style="background-color: darkgray">
                <tr>
                    <th>#</th>
                    <th>DESCRIPCIÓN</th>
                    <th>CANTIDAD</th>
                    <th>MEDIDA</th>
                    <th>PRECIO UNIT.</th>
                    <th>IMPORTE</th>
                </tr>
            </thead>
            <tbody>
               @foreach($detail as $obj)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $obj->article_name }}</td>
                    <td>{{ $obj->detail_quantity }}</td>
                    <td>{{ $obj->detail_unit }}</td>
                    <td>{{ $orders->currency_symbol }} {{ $obj->detail_price }}</td>
                    <td>{{ $orders->currency_symbol }} {{ $obj->detail_import }}</td>
                </tr>
               @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th>SUBTOTAL</th>
                    <th>{{ $orders->currency_symbol }} {{ $orders->orders_subtotal }}</th>
                </tr>
                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th>IGV 18%</th>
                    <th>{{ $orders->currency_symbol }} {{ $orders->orders_igv }}</th>
                </tr>
                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th>NETO</th>
                    <th>{{ $orders->currency_symbol }} {{ $orders->orders_import }}</th>
                </tr>
                <tr>
                    <td colspan="6">{{ $orders->total_text }}</td>
                </tr>
                <tr>
                    <td colspan="3"></td>
                    <td style="font-weight: bold;background-color: yellow" colspan="3" style="">{{ $orders->orders_reference_id == 1 ? 'PROFORMA' : 'COTIZACIÓN' }}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold">MÉTODO DE PAGO</td>
                    <td>{{ $orders->orders_type_pay }}</td>
                    <td>-</td>
                    <td rowspan="2" colspan="3">{{ $orders->description }}</td>
                </tr>
                <tr>
                    <td>-</td>
                    <td></td>
                    <td>-</td>
                </tr>
            </tfoot>
        </table>
    </div>
    <br>

    <div class="footer">
        <div>
            <table class="aprobacion">
                <thead>
                    <tr>
                        <th colspan="2" style="text-align: left">APROBRACIÓN</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                    <td>
                        ÁREA SOLICITANTE {{ $orders->area_name }}<br>
                        FECHA {{ $orders->orders_order_date }}<br>
                        ENCARGADO (A) {{ $orders->area_short_name }}
                    </td>
                    <td>
                        ÁREA DE ADMINISTRACIÓN Y FINANZAS<br>
                        FECHA<br>
                        ENCARGADO (A)<br><br>
                        <img src="{{ asset('backend/img/firma-orden-pedido.png') }}" alt="" style><br>
                        __________________________________<br>
                        FIRMA
                    </td>
                    </tr>
                </tbody>
                
            </table>
        </div>
        <br>

        <div>
            <table class="observa">
                <thead>
                    <tr>
                        <th style="text-align: left">OBSERVA</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                    <td>
                    Somos agentes de retención<br>
                    Para facturas de servicio indicar número de cuenta de detracción en la factura emitida<br>
                    El único correo donde se recibirán facturas electrónicas es <a href="">contabilidad@puntodedistribucion.com</a><br>
                    <span style="color: red">#N/D</span>
                    </td>
                    </tr>
                </tbody>
                
            </table>
        </div>
    </div>

    <script type="text/php">
        if ( isset($pdf) ) {
            $pdf->page_script('
                $font = $fontMetrics->get_font("Arial, Helvetica, sans-serif", "normal");
                $pdf->text(488, 63, "Página $PAGE_NUM/$PAGE_COUNT", $font, 9, array(0.349, 0.3647, 0.4314));
            ');
        }
    </script>
</body>
</html>
