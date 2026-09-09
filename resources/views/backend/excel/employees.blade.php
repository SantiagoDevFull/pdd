<table>
    <thead>
		<tr>
			<th colspan="6" style="text-align:center;font-weight:bold;font-size:14pt;">REPORTE DE CHOFERES AL {{ $datetime }}</th>
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
			<td style="font-weight:bold;">Tipo de Doc.</td>
			<td style="font-weight:bold;">N° de Doc.</td>
			<td style="font-weight:bold;">Chofer</td>
			<td style="font-weight:bold;">Licencia</td>
			<td style="font-weight:bold;">Tipo</td>
		</tr>
		@foreach ($employees as $element)
		<tr>
			<td>{{ $loop->iteration }}</td>
			<td>{{ $element->document_type_name }}</td>
			<td>{{ $element->document_number }}</td>
			<td>{{ $element->name_complete }}</td>
			<td>{{ $element->license }}</td>
			<td>{{ $element->employee_type_name }}</td>
		</tr>
		@endforeach
    </tbody>
</table>
