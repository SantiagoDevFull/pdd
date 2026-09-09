<table>
    <thead>
		<tr>
			<th colspan="15" style="text-align:center;font-weight:bold;font-size:14pt;">REPORTE DE VEHÍCULOS AL {{ $datetime }}</th>
		</tr>
		<tr>
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
			<td style="font-weight:bold;">Compañía</td>
			<td style="font-weight:bold;">Placa</td>
			<td style="font-weight:bold;">Capacidad</td>
			<td style="font-weight:bold;">Licencia</td>
			<td style="font-weight:bold;">Transportista</td>
			<td style="font-weight:bold;">Chasis</td>
			<td style="font-weight:bold;">Marca</td>
			<td style="font-weight:bold;">Tipo de Combustible</td>
			<td style="font-weight:bold;">Tipo de Carrocería</td>
			<td style="font-weight:bold;">Modelo</td>
			<td style="font-weight:bold;">Color</td>
			<td style="font-weight:bold;">Motor</td>
			<td style="font-weight:bold;">N° de Llantas</td>
			<td style="font-weight:bold;">Año de Modelo</td>
		</tr>
		@foreach ($vehicles as $element)
		<tr>
			<td>{{ $loop->iteration }}</td>
			<td>{{ $element->company_name }}</td>
			<td>{{ $element->plate }}</td>
			<td>{{ $element->capacity }}</td>
			<td>{{ $element->license }}</td>
			<td>{{ $element->transportist_name }}</td>
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