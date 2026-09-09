<table>
    <thead>
		<tr>
			<th colspan="14" style="text-align:center;font-weight:bold;font-size:14pt;">REPORTE DE VEHÍCULOS DE PUNTO MOTOR AL {{ $datetime }}</th>
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
			<td style="font-weight:bold;">Placa</td>
			<td style="font-weight:bold;">Capacidad</td>
			<td style="font-weight:bold;">Chasis</td>
			<td style="font-weight:bold;">Marca</td>
			<td style="font-weight:bold;">Tipo de Combustible</td>
			<td style="font-weight:bold;">Tipo de Carrocería</td>
			<td style="font-weight:bold;">Modelo</td>
			<td style="font-weight:bold;">Color</td>
			<td style="font-weight:bold;">Motor</td>
			<td style="font-weight:bold;">N° llantas</td>
			<td style="font-weight:bold;">Año de modelo</td>

		</tr>
		@foreach ($vehicles as $element)
		<tr>
			<td>{{ $loop->iteration }}</td>
			<td>
                @switch ($element->state)
                    @case (0)   Disponible    @break
                    @case (1)   Asignado  @break
                    @case (2)   Vendido (OT)   @break
                    @case (3)   Cotizado   @break
                    @case (4)   Facturado   @break
                    @default    Null      @break
                @endswitch
            </td>
			<td>{{ $element->company_name }}</td>
			<td>{{ $element->plate }}</td>
			<td>{{ $element->capacity }}</td>
			<td>{{ $element->chasis }}</td>
			<td>{{ $element->marca }}</td>
			<td>{{ $element->tipo_combustible }}</td>
			<td>{{ $element->tipo_carroceria }}</td>
			<td>{{ $element->modelo }}</td>
			<td>{{ $element->color }}</td>
			<td>{{ $element->motor }}</td>
			<td>{{ $element->nro_llantas }}</td>
			<td>{{ $element->year_model }}</td>

		</tr>
		@endforeach
    </tbody>
</table>