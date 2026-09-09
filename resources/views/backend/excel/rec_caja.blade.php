<table>
    <thead>
        <tr>
            <th colspan="5" style="font-weight:bold; color: white; text-align: center;background-color: darkorchid">DATOS</th>
            <th colspan="8" style="font-weight:bold; color: white; background-color: darkgreen"></th>
            <th colspan="6" style="font-weight:bold; color: black; text-align: center; background-color: aqua">BILLETES</th>
            <th colspan="7" style="font-weight:bold; color: white; text-align: center; background-color: coral">MONEDAS</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="font-weight:bold;">N°</td>
            <td style="font-weight:bold;">RUTA</td>
            <td style="font-weight:bold;">COD</td>
            <td style="font-weight:bold;">EMPRESA</td>
            <td style="font-weight:bold;">NOMBRE</td>
            <td style="font-weight:bold;">A LIQUIDAR</td>
            <td style="font-weight:bold;">LIQUIDADO</td>
            <td style="font-weight:bold;">DEPÓSITO CHEQUES</td>
            <td style="font-weight:bold;">EFECTIVO</td>
            <td style="font-weight:bold;">FECHA</td>
            <td style="font-weight:bold;">NRO. OPERACIÓN</td>
            <td style="font-weight:bold;">BANCO</td>
            <td style="font-weight:bold;">DIFERENCIA</td>
            <td style="font-weight:bold;">200</td>
            <td style="font-weight:bold;">100</td>
            <td style="font-weight:bold;">50</td>
            <td style="font-weight:bold;">20</td>
            <td style="font-weight:bold;">10</td>
            <td style="font-weight:bold;">TOTAL</td>
            <td style="font-weight:bold;">5.00</td>
            <td style="font-weight:bold;">2.00</td>
            <td style="font-weight:bold;">1.00</td>
            <td style="font-weight:bold;">0.50</td>
            <td style="font-weight:bold;">0.20</td>
            <td style="font-weight:bold;">0.10</td>
            <td style="font-weight:bold;">TOTAL</td>
        </tr>

        @foreach ($lista as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item['route_id'] }}</td><!-- RUTA -->
                <td>{{ $item['route_name'] }}</td><!-- COD -->
                <td>{{ $item['company_short_name'] }}</td><!-- EMPRESA -->
                <td>{{ $item['client_name'] }}</td><!-- NOMBRE -->
                <td>{{ $item['total_liquidation'] }}</td><!-- A LIQUIDAR -->
                <td>{{ $item['total_deposit'] + $item['total_efective'] }}</td><!-- LIQUIDADO -->
                <td>{{ $item['total_deposit'] }}</td><!-- DEPOSITO EN CHEQUES -->
                <td>{{ $item['total_efective'] }}</td><!-- EFECTIVO -->
                <td>{{ $item['bank_date'] }}</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        @endforeach
    </tbody>

</table>
