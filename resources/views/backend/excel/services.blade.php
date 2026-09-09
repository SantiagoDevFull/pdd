<table>
    <thead>
		<tr>
			<th colspan="9" style="text-align:center;font-weight:bold;font-size:14pt;">REPORTE DE SERVICIOS AL {{ $datetime }}</th>
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
		</tr>
    </thead>
    <tbody>
		<tr>
			<td style="font-weight:bold;">#</td>
			<td style="font-weight:bold;">CLAVE</td>
			<td style="font-weight:bold;">COMPAÑÍA</td>
			<td style="font-weight:bold;">SEDE</td>
			<td style="font-weight:bold;">ÁREA</td>
			<td style="font-weight:bold;">CODE</td>
			<td style="font-weight:bold;">SERVICIO</td>
			<td style="font-weight:bold;">COSTO</td>
			<td style="font-weight:bold;">PRECIO</td>
		</tr>
		@foreach ($services as $element)
		<tr>
			<td>{{ $loop->iteration }}</td>
			<td>{{ $element->service_id }}</td>
			<td>{{ $element->company_name }}</td>
			<td>{{ $element->sede_name }}</td>
			<td>{{ $element->area_name }}</td>
			<td>{{ $element->code }}</td>
			<td>{{ $element->name }}</td>
			<td>{{ $element->cost }}</td>
			<td>{{ $element->price }}</td>
		</tr>
		@endforeach
    </tbody>
</table>