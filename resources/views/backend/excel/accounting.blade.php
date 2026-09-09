<table>
    <thead>
        <tr>
            <th colspan="12" style="text-align:center;font-weight:bold;font-size:14pt;">LISTA DE PRECIOS CONTABILIDAD
                GENERADOS AL {{ $datetime }}</th>
        </tr>
        <tr>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="font-weight:bold;">#</td>
            <td style="font-weight:bold;">Precio</td>
            <td style="font-weight:bold;">Fecha Inicio</td>
            <td style="font-weight:bold;">Fecha Final</td>
            <td style="font-weight:bold;">Estado</td>
        </tr>
        @foreach ($elements as $obj)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $obj->price }}</td>
                <td>{{ $obj->initial_date }}</td>
                <td>{{ $obj->final_date }}</td>
                <td>{{ $obj->state }}</td>

            </tr>
        @endforeach
    </tbody>
</table>
