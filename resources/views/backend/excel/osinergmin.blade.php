<table>
    <thead>
		<tr>
			<th colspan="14">2.- INGRESOS: COMPRAS Y TRANFERENCIAS QUE RECIBE LA PLANTA ENVASADORA DURANTE EL MES DECLARADO</th>
		</tr>
        <tr>
			<th colspan="14">Se debe considerar todas las compras y transferencias durante el mes con estado SCOP "VENDIDO"</th>
		</tr>
		<tr>
			<th colspan="14"></th>
		</tr>
        <tr>
			<th colspan="14" style="color: blue">2.1 - COMPRA DE GLP EFECTUADA A UN AGENTE</th>
		</tr>
    </thead>
    <tbody>
		<tr>
			<td style="font-weight:bold;">N°</td>
			<td style="font-weight:bold;">Numeral</td>
			<td style="font-weight:bold;">Mes</td>
			<td style="font-weight:bold;">Razón Social del Agente</td>
			<td style="font-weight:bold;">RUC</td>
			<td style="font-weight:bold;">Registro de Hidrocarburos</td>
			<td style="font-weight:bold;">Tipo de Agente</td>
			<td style="font-weight:bold;">Fecha Despacho</td>
			<td style="font-weight:bold;">SCOP</td>
			<td style="font-weight:bold;">Placa<br>Principal</td>
			<td style="font-weight:bold;">Fecha Facturación</td>
			<td style="font-weight:bold;">Serie</td>
			<td style="font-weight:bold;">Número</td>
			<td style="font-weight:bold;">GLP-G<br>(Kilogramos)</td>
		</tr>
		@foreach ($abastecimientos_glp as $item)
		<tr>
			<td>{{ $loop->iteration }}</td>
			<td>2.1</td>
			<td>{{ $item->mes }}</td>
			<td>{{ $item->nombre_agente }}</td>
			<td>{{ $item->ruc }}</td>
			<td>{{ $item->registro }}</td>
			<td>{{ $item->tipo_agent }}</td>
			<td>{{ $item->fecha_despacho }}</td>
			<td>{{ $item->scop }}</td>
			<td>{{ $item->placa }}</td>
			<td>{{ $item->fecha_facturacion }}</td>
			<td>{{ $item->serie }}</td>
			<td>{{ $item->numero }}</td>
			<td>{{ $item->total }}</td>
		</tr>
		@endforeach
    </tbody>
</table>

<table>
    <thead>
		<tr>
			<th colspan="14">3.- EGRESOS: VENTAS Y TRANFERENCIAS QUE ENTREGA LA PLANTA ENVASADORA DURANTE EL MES DECLARADO</th>
		</tr>
        <tr>
			<th colspan="14">Se debe considerar todas las ventas y transferencias durante el mes con estado SCOP "VENDIDO"</th>
		</tr>
		<tr>
			<th colspan="14"></th>
		</tr>
        <tr>
			<th colspan="14" style="color: blue">3.1 - VENTA DE GLP A GRANEL EFECTUADA A UN AGENTE</th>
		</tr>
    </thead>
    <tbody>
		<tr>
			<td style="font-weight:bold;">N°</td>
			<td style="font-weight:bold;">Numeral</td>
			<td style="font-weight:bold;">Mes</td>
			<td style="font-weight:bold;">Razón Social del Agente</td>
			<td style="font-weight:bold;">RUC</td>
			<td style="font-weight:bold;">Registro de Hidrocarburos</td>
			<td style="font-weight:bold;">Tipo de Agente</td>
			<td style="font-weight:bold;">Fecha Despacho</td>
			<td style="font-weight:bold;">SCOP</td>
			<td style="font-weight:bold;">Placa<br>Principal</td>
			<td style="font-weight:bold;">Fecha Facturación</td>
			<td style="font-weight:bold;">Serie</td>
			<td style="font-weight:bold;">Número</td>
			<td style="font-weight:bold;">GLP-G<br>(Kilogramos)</td>
		</tr>
		@foreach ($facturas_vd as $item)
		<tr>
			<td>{{ $loop->iteration }}</td>
			<td>3.1</td>
			<td>{{ $item->mes }}</td>
			<td>{{ $item->client_name }}</td>
			<td>{{ $item->ruc }}</td>
			<td>{{ $item->registro }}</td>
			<td>{{ $item->tipo_agent }}</td>
			<td>{{ $item->fecha_despacho }}</td>
			<td>{{ $item->scop }}</td>
			<td>{{ $item->vehiculo }}</td>
			<td>{{ $item->fecha_facturacion }}</td>
			<td>{{ $item->serie }}</td>
			<td>{{ $item->numero }}</td>
			<td>{{ $item->total }}</td>
		</tr>
		@endforeach
    </tbody>
</table>

<table>
    <thead>
        <tr>
			<th colspan="14" style="color: blue">3.2 - TRANSFERENCIA DE GLP A GRANEL HACIA UN AGENTE DE LA MISMA RAZÓN SOCIAL</th>
		</tr>
    </thead>
    <tbody>
		<tr>
			<td style="font-weight:bold;">N°</td>
			<td style="font-weight:bold;">Numeral</td>
			<td style="font-weight:bold;">Mes</td>
			<td style="font-weight:bold; background-color: black"></td>
			<td style="font-weight:bold;">RUC</td>
			<td style="font-weight:bold;">Registro de Hidrocarburos</td>
			<td style="font-weight:bold;">Tipo de Agente</td>
			<td style="font-weight:bold;">Fecha Despacho</td>
			<td style="font-weight:bold;">SCOP</td>
			<td style="font-weight:bold;">Placa<br>Principal</td>
			<td style="font-weight:bold;">Fecha Guía de Remisión</td>
			<td style="font-weight:bold;">Serie</td>
			<td style="font-weight:bold;">Número</td>
			<td style="font-weight:bold;">GLP-G<br>(Kilogramos)</td>
		</tr>
		@foreach ($guias_elect as $item)
		<tr>
			<td>{{ $loop->iteration }}</td>
			<td>3.2</td>
			<td>{{ $item->mes }}</td>
			<td style="background-color: black"></td>
			<td>{{ $item->company_document_number }}</td>
			<td>{{ $item->registro }}</td>
			<td>{{ $item->tipo_agent }}</td>
			<td>{{ $item->fecha_despacho }}</td>
			<td>{{ $item->scop }}</td>
			<td>{{ $item->placa_principal }}</td>
			<td>{{ $item->fecha_remision }}</td>
			<td>{{ $item->serie }}</td>
			<td>{{ $item->numero }}</td>
			<td>{{ $item->total }}</td>
		</tr>
		@endforeach
    </tbody>
</table>

<table>
    <thead>
        <tr>
			<th colspan="14" style="color: blue">3.3 - VENTA DE GLP EN CILINDROS EFECTUADA A UN AGENTE</th>
		</tr>
    </thead>
    <tbody>
		<tr>
			<td style="font-weight:bold;">N°</td>
			<td style="font-weight:bold;">Numeral</td>
			<td style="font-weight:bold;">Mes</td>
			<td style="font-weight:bold;">Razón Social del Agente</td>
			<td style="font-weight:bold;">RUC</td>
			<td style="font-weight:bold;">Registro de Hidrocarburos</td>
			<td style="font-weight:bold;">Tipo de Agente</td>
			<td style="font-weight:bold;">Fecha Despacho</td>
			<td style="font-weight:bold;">SCOP</td>
			<td style="font-weight:bold;">Placa<br>Principal</td>
			<td style="font-weight:bold;">Fecha Facturación</td>
			<td style="font-weight:bold;">Serie</td>
			<td style="font-weight:bold;">Número</td>
			<td style="font-weight:bold;">GLP-G<br>(Kilogramos)</td>
		</tr>
		@foreach ($tres_tres as $item)
		<tr>
			<td>{{ $loop->iteration }}</td>
			<td>3.3</td>
			<td>{{ $item->mes }}</td>
			<td>{{ $item->client_name }}</td>
			<td>{{ $item->ruc }}</td>
			<td>{{ $item->registro }}</td>
			<td>{{ $item->tipo_agent }}</td>
			<td>{{ $item->fecha_despacho }}</td>
			<td>{{ $item->scop }}</td>
			<td>{{ $item->vehiculo }}</td>
			<td>{{ $item->fecha_facturacion }}</td>
			<td>{{ $item->serie }}</td>
			<td>{{ $item->numero }}</td>
			<td>{{ $item->total }}</td>
		</tr>
		@endforeach
    </tbody>
</table>

<table>
    <thead>
        <tr>
			<th colspan="14" style="color: blue">3.4 - TRANSFERENCIA GLP EN CILINDROS HACIA UN AGENTE DE LA MISMA RAZÓN SOCIAL</th>
		</tr>
    </thead>
    <tbody>
		<tr>
			<td style="font-weight:bold;">N°</td>
			<td style="font-weight:bold;">Numeral</td>
			<td style="font-weight:bold;">Mes</td>
			<td style="font-weight:bold;background-color: black"></td>
			<td style="font-weight:bold;">RUC</td>
			<td style="font-weight:bold;">Registro de Hidrocarburos</td>
			<td style="font-weight:bold;">Tipo de Agente</td>
			<td style="font-weight:bold;">Fecha Despacho</td>
			<td style="font-weight:bold;">SCOP</td>
			<td style="font-weight:bold;">Placa<br>Principal</td>
			<td style="font-weight:bold;">Fecha Guía de Remisión</td>
			<td style="font-weight:bold;">Serie</td>
			<td style="font-weight:bold;">Número</td>
			<td style="font-weight:bold;">GLP-G<br>(Kilogramos)</td>
		</tr>
		@foreach ($guias_elect_cilindros as $item)
		<tr>
			<td>{{ $loop->iteration }}</td>
			<td>3.4</td>
			<td>{{ $item->mes }}</td>
			<td style="background-color: black"></td>
			<td>{{ $item->company_document_number }}</td>
			<td>{{ $item->registro }}</td>
			<td>{{ $item->tipo_agent }}</td>
			<td>{{ $item->fecha_despacho }}</td>
			<td>{{ $item->scop }}</td>
			<td>{{ $item->placa_principal }}</td>
			<td>{{ $item->fecha_remision }}</td>
			<td>{{ $item->serie }}</td>
			<td>{{ $item->numero }}</td>
			<td>{{ $item->total }}</td>
		</tr>
		@endforeach
    </tbody>
</table>

<table>
    <thead>
        <tr>
			<th colspan="14" style="color: blue">3.6 - VENTA DE GLP EN CILINDROS A USUARIOS FINALES CON FACTURA</th>
		</tr>
    </thead>
    <tbody>
		<tr>
			<td style="font-weight:bold;">N°</td>
			<td style="font-weight:bold;">Numeral</td>
			<td style="font-weight:bold;">Mes</td>
			<td style="font-weight:bold;">Razón Social del Agente</td>
			<td style="font-weight:bold;">RUC</td>
			<td style="background-color: black"></td>
			<td style="font-weight:bold;">Dirección o Referencia de Ubicación del Usuario</td>
			<td style="font-weight:bold;">Fecha Despacho</td>
			<td style="background-color: black"></td>
			<td style="background-color: black"></td>
			<td style="font-weight:bold;">Fecha Facturación</td>
			<td style="font-weight:bold;">Serie</td>
			<td style="font-weight:bold;">Número</td>
			<td style="font-weight:bold;">GLP-G<br>(Kilogramos)</td>
		</tr>

    </tbody>
</table>

<table>
    <thead>
        <tr>
			<th colspan="14" style="color: blue">3.7 - VENTA DE GLP EN CILINDROS A USUARIOS FINALES CON BOLETA DE VENTA</th>
		</tr>
    </thead>
    <tbody>
		<tr>
			<td style="font-weight:bold;">N°</td>
			<td style="font-weight:bold;">Numeral</td>
			<td style="font-weight:bold;">Mes</td>
			<td style="font-weight:bold; background-color: black"></td>
			<td style="font-weight:bold; background-color: black"></td>
			<td style="font-weight:bold; background-color: black"></td>
			<td style="font-weight:bold; background-color: black"></td>
			<td style="font-weight:bold; background-color: black"></td>
			<td style="font-weight:bold; background-color: black"></td>
			<td style="font-weight:bold;">Tipo</td>
			<td style="font-weight:bold;">Serie</td>
			<td style="font-weight:bold;">Desde</td>
			<td style="font-weight:bold;">Hasta</td>
			<td style="font-weight:bold;">GLP-G<br>(Kilogramos)</td>
		</tr>
		@foreach ($vouchers as $item)
		<tr>
			<td>{{ $loop->iteration }}</td>
			<td>3.7</td>
			<td>{{ $item->mes }}</td>
			<td style="background-color: black"></td>
			<td style="background-color: black"></td>
			<td style="background-color: black"></td>
			<td style="background-color: black"></td>
			<td style="background-color: black"></td>
			<td style="background-color: black"></td>
			<td>{{ $item->tipo }}</td>
			<td>{{ $item->serie }}</td>
			<td>{{ $item->inicio }}</td>
			<td>{{ $item->fin }}</td>
			<td>{{ $item->total }}</td>
		</tr>
		@endforeach
    </tbody>
</table>