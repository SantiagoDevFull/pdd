<table>
    <thead>
		<tr>
			<th colspan="18" style="text-align:center;font-weight:Calibri;font-size:14pt;">REPORTE DE CONDUCTORES BLUE LINE{{ $datetime }}</th>
		</tr>
		<tr>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
		</tr>
    </thead>
    <tbody>
		<tr>
			<td style="font-weight: Calibri; color:whitesmoke; background:black">Estado</td>
			<td style="font-weight: Calibri; color:whitesmoke; background:black">Empresa</td>
			<td style="font-weight: Calibri; color:whitesmoke; background:black">Nombre</td>
			<td style="font-weight: Calibri; color:whitesmoke; background:black">Apellido</td>
			<td style="font-weight: Calibri; color:whitesmoke; background:black">Documento</td>			
			<td style="font-weight: Calibri; color:whitesmoke; background:black">Servicio</td>			
			<td style="font-weight: Calibri; color:whitesmoke; background:#7F27BA">Licencia A3C</td>
			<td style="font-weight: Calibri; color:whitesmoke; background:#7F27BA">Mes</td>
			<td style="font-weight: Calibri; color:whitesmoke; background:#7F27BA">N° Días a vencer Licencia A3C</td>
			<td style="font-weight: Calibri; color:whitesmoke; background:#BA6427">Licencia A4</td>
			<td style="font-weight: Calibri; color:whitesmoke; background:#BA6427">Mes</td>
			<td style="font-weight: Calibri; color:whitesmoke; background:#BA6427">N° Dias a vencer Licencia A4</td>
			<td style="font-weight: Calibri; color:whitesmoke; background:#4DBA27">Emo</td>
			<td style="font-weight: Calibri; color:whitesmoke; background:#4DBA27">Mes</td>
			<td style="font-weight: Calibri; color:whitesmoke; background:#4DBA27">N° Dias a vencer Emo</td>
			<td style="font-weight: Calibri; color:whitesmoke; background:#808080">Carnet Solgas</td>
			<td style="font-weight: Calibri; color:whitesmoke; background:#808080">Mes</td>
			<td style="font-weight: Calibri; color:whitesmoke; background:#808080">Días a vencer Solgas</td>			
			<td style="font-weight: Calibri; color:whitesmoke; background:#032263">Antecedentes penales</td>
			<td style="font-weight: Calibri; color:whitesmoke; background:#032263">Mes</td>
			<td style="font-weight: Calibri; color:whitesmoke; background:#032263">Días a vencer APEN</td>
			<td style="font-weight: Calibri; color:black; background:#FFD700">Antecedentes Policiales</td>
			<td style="font-weight: Calibri; color:black; background:#FFD700">Mes</td>
			<td style="font-weight: Calibri; color:black; background:#FFD700">Días a vencer APOL</td>
			<td style="font-weight: Calibri; color:black; background:#2fa394">Carnet Tripulante</td>
			<td style="font-weight: Calibri; color:black; background:#2fa394">Mes</td>
			<td style="font-weight: Calibri; color:black; background:#2fa394">Días a vencer CT</td>

		</tr>
		@foreach ($drivers as $element)
		<tr>
			<td>{{ $loop->iteration }}</td>
			<td>{{ $element->company_name }}</td>
			<td>{{ $element->first_name }}</td>
			<td>{{ $element->last_name }}</td>
			<td>{{ $element->document_number }}</td>
			<td></td>
			<td>{{ $element->f_license_a3 }}</td>
			<td></td>
			<td></td>
			<td>{{ $element->f_license_a4 }}</td>
			<td></td>
			<td></td>
			<td>{{ $element->f_emo }}</td>
			<td></td>
			<td></td>
			<td>{{ $element->f_carnet_solgas }}</td>
			<td></td>
			<td></td>
			<td>{{ $element->f_ant_pen }}</td>
			<td></td>
			<td></td>
			<td>{{ $element->f_ant_pol }}</td>
			<td></td>
			<td></td>
			<td>{{ $element->f_carnet_trip }}</td>
			<td></td>
			<td></td>
		</tr>
		@endforeach
    </tbody>
</table>