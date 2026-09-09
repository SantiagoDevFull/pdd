<table>
    <thead>
		<tr>
			<th colspan="11" style="text-align:center;font-weight:bold;font-size:14pt;">REPORTE DE ORDEN DE TRABAJO DE PUNTO MOTOR AL {{ $datetime }}</th>
		</tr>
		<tr>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
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
			<td style="font-weight:bold;">Estado</td>
			<td style="font-weight:bold;">Compañia</td>
			<td style="font-weight:bold;">Asesor</td>
			<td style="font-weight:bold;">Placa</td>
			<td style="font-weight:bold;">Compañía de Seguro</td>
			<td style="font-weight:bold;">Cliente</td>
			<td style="font-weight:bold;">Serie</td>
			<td style="font-weight:bold;">KM</td>
			<td style="font-weight:bold;">Observaciones</td>
			<td style="font-weight:bold;">Fecha de Emisión</td>

		</tr>
		@foreach ($order_works as $element)
		<tr>
			<td>{{ $loop->iteration }}</td>
			<td>
                @switch ($element->state)
                    @case (0)   Pendiente    @break
                    @case (1)   Aprobado  @break
                    @case (2)   Desaprobado   @break
                    @default    Null      @break
                @endswitch
            </td>
			<td>{{ $element->company_name }}</td>
			<td>{{ $element->asesor_name }}</td>
			<td>{{ $element->vehicle_plate }}</td>
			<td>{{ $element->security_name }}</td>
			<td>{{ $element->client_motor_name }}</td>
			<td>{{ $element->serie }}</td>
			<td>{{ $element->km }}</td>
			<td>{{ $element->observation }}</td>
			<td>{{ $element->issue_date }}</td>

		</tr>
		@endforeach
    </tbody>
</table>