<table>
    <thead>
		<tr>
			<th colspan="3" style="font-size:12pt;">Punto de Distribución S.A.C.</th>
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
		<tr>
			<th colspan="3" style="font-size:12pt;">{{ date('Y/m/d H:i:s') }}</th>
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
		</tr>
		<tr>
			<th colspan="8" style="text-align:center;font-weight:bold;font-size:14pt;">REPORTE DE INVENTARIO FÍSICO AL {{ $creation_date }} {{ ( $state == 1 ? ' - DEFINITIVO' : ' - PREVIO' ) }}</th>
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
		</tr>
    </thead>
    <tbody>
		<tr>
			<td style="font-weight:bold;">Item</td>
			<td style="font-weight:bold;">Almacén</td>
			<td style="font-weight:bold;">Artículo</td>
			<td style="font-weight:bold;">Descripción</td>
			<td style="font-weight:bold;">Unidad Medida</td>
			<td style="font-weight:bold;">Empaque</td>
			<td style="font-weight:bold;">Cantidad Producido</td>
			<td style="font-weight:bold;">Observaciones</td>
		</tr>
		@foreach ($elements as $element)
		<tr>
			<td>{{ $loop->iteration }}</td>
			<td>{{ $element->warehouse_type_name }}</td>
			<td>{{ $element->article_code }}</td>
			<td>{{ $element->article_name }}</td>
			<td>{{ $element->warehouse_unit_short_name }}</td>
			<td>{{ $element->package_warehouse }}</td>
			<td>{{ $element->found_stock_good }}</td>
			<td>{{ $element->observations }}</td>
		</tr>
		@endforeach
    </tbody>
</table>