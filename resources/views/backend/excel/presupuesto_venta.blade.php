<table>
    <thead>
		<tr>
			<th colspan="10" style="text-align:center;font-weight:bold;font-size:14pt;">LISTA DE PRESUPUESTOS</th>
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
		</tr>
    </thead>
    <tbody>
		<tr>
			<td style="font-weight:bold;">#</td>
			<td style="font-weight:bold;">AÑO</td>
			<td style="font-weight:bold;">MES</td>
			<td style="font-weight:bold;">RUTA</td>
			<td style="font-weight:bold;">UNIDAD</td>
			<td style="font-weight:bold;">CANAL</td>
			<td style="font-weight:bold;">SUB CANAL</td>
			<td style="font-weight:bold;">SUPERVISOR</td>
			<td style="font-weight:bold;">PPTO VENTA</td>
			<td style="font-weight:bold;">PPTO MB</td>
		</tr>
		@foreach ($elements as $element)
		<tr>
			<td>{{ $loop->iteration }}</td>
			<td>{{ $element->anio }}</td>
			<td>{{ $element->mes }}</td>
			<td>{{ $element->ruta }}</td>
			<td>{{ $element->business_unit_name }}</td>
			<td>{{ $element->client_sector_name }}</td>
			<td>{{ $element->client_channel_name }}</td>
			<td>{{ $element->manager_name }}</td>
			<td>{{ $element->ppto_venta }}</td>
			<td>{{ $element->ppto_mb }}</td>
		</tr>
		@endforeach
    </tbody>
</table>
