<table>
    <thead>
		<tr>
			<th colspan="17" style="text-align:center;font-weight:bold;font-size:14pt;">REPORTE DE FACTURAS POR PAGAR AL {{ $datetime }}</th>
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
			<th></th>
			<th></th>
			<th></th>
		</tr>
    </thead>
    <tbody>
		<tr>
			<td style="font-weight:bold;">#</td>
			<td style="font-weight:bold;">MES</td>
			<td style="font-weight:bold;">PLACA</td>
			<td style="font-weight:bold;">CENTRO DE COSTOS PRIMARIO - SECUNDARIO</td>
			<td style="font-weight:bold;">FECHA DE REGISTRO</td>
			<td style="font-weight:bold;">DESCRIPCIÓN</td>
			<td style="font-weight:bold;">RUC</td>
			<td style="font-weight:bold;">PROVEEDOR</td>
			<td style="font-weight:bold;">DOCUMENTO</td>
			<td style="font-weight:bold;">FEC. DOC.</td>
			<td style="font-weight:bold;">TOTAL</td>
			<td style="font-weight:bold;">ÁREA</td>
			<td style="font-weight:bold;">EMPRESA</td>
			<td style="font-weight:bold;">TIPO</td>
			<td style="font-weight:bold;">FECHA DE PAGO</td>
			<td style="font-weight:bold;">ESTADO</td>
			<td style="font-weight:bold;">PRESUPUESTADO</td>
			<td style="font-weight:bold;">COMENTARIO</td>
		</tr>
		@foreach ($movements as $element)
		<tr>
			
			<td>{{ $loop->iteration }}</td>
			<td>{{ $element->mes }}</td>
			<td>{{ $element->plate }}</td>
			<td>{{ $element->centro_costos }}</td>
			<td>{{ $element->date}}</td>
			<td>{{ $element->article_name}}</td>
			<td>{{ $element->provider_ruc }}</td>
			<td>{{ $element->provider_name }}</td>
			<td>{{ $element->factura }}</td>
			<td>{{ $element->movement_date }}</td>
			<td>{{ $element->total}}</td>
			<td>{{ $element->area_name }}</td>
			<td>{{ $element->company_name }}</td>
			<td>{{ $element->tipo }}</td>
			<td>{{ $element->fecha_pago }}</td>
			<td>
                @switch ($element->state)
                    @case (0)   Por Aprobar    @break
                    @case (1)   Aprobado  @break
                    @case (2)   Desaprobado   @break
                    @default    Null      @break
                @endswitch
            </td>
			<td>{{ $element->ppto }}</td>
			<td>{{ $element->observation }}</td>
		</tr>
		@endforeach
    </tbody>
</table>