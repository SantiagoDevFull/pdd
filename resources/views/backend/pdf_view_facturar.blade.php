<!DOCTYPE html>
<html lang="es">
	<!-- begin::Head -->
	<head>
		<meta charset="utf-8" />
		<title>Punto de Distribución</title>
		<meta name="csrf-token" content="{{ csrf_token() }}">
		<meta name="description" content="">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0, shrink-to-fit=no">

		<!--begin::Global Theme Styles -->
		<link href="{{ asset('backend/css/pdf_facturar.css') }}" rel="stylesheet" type="text/css" />
		<!--end::Global Theme Styles -->
	</head>
	<!-- end::Head -->

	<!-- begin::Body -->
	<body>
		<div id="pdf">
			<table class="pdf-head" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td>
						<div class="logo">
							@if ( $obj->company_id == 1 )
								<img src="{{ asset('backend/img/logo-pdf-puntod.png') }}" alt="">
							@elseif ( $obj->company_id == 2 )
								<img src="{{ asset('backend/img/logo-pdf-cordia.png') }}" alt="">
                            @elseif ( $obj->company_id == 3 )
								<img src="{{ asset('backend/img/logo-pdf-global.jpeg') }}" alt="">
                            @elseif ( $obj->company_id == 4 )
								<img src="{{ asset('backend/img/logo-pdf-blueline.jpeg') }}" alt="">
                            @elseif ( $obj->company_id == 5 )
								<img src="{{ asset('backend/img/logo-pdf-puntom.jpeg') }}" alt="">
							@endif
						</div>
						<div class="info">
							<div class="business-name">{{ $obj->company_name }}</div>
							<div class="address">
								<p>{{ $obj->company_address }}</p>
								<p>{{ $obj->company_district }} - {{ $obj->company_province }} - {{ $obj->company_department }}</p>
							</div>
						</div>
					</td>

					<td>
						<div class="voucher-data">
							<p class="ruc">R.U.C. Nº {{ $obj->company_document_number }}</p>
							<p class="voucher-type">{{ $obj->voucher_type_name }}</p>
							<p class="voucher-number">{{ $obj->serie_number }}-{{ $obj->voucher_number }}</p>
                            <p class="referral-guide-title">Cotización</p>
							<p class="referral-guide-number">{{ $voucher_id->referral_guide_series }}-{{ $voucher_id->referral_guide_number }}</p>
						</div>
					</td>
				</tr>
				<tr><td height="20" style="line-height:1;font-size:0;"></td></tr>
			</table>

			<table class="pdf-subhead" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td style="vertical-align:top;">
						<div class="subhead-item">
							<div class="subhead-title">Fecha de Emisión</div>
							<div class="subhead-text">{{ $obj->issue_date }}</div>
						</div>
					</td>
					<td style="vertical-align:top;">
						<div class="subhead-item">
							<div class="subhead-title">Fecha de Vencimiento</div>
                            <div class="subhead-text">{{ $obj->expiry_date }}</div>
						</div>
					</td>
					<td style="vertical-align:top;">
						<div class="subhead-item">
							<div class="subhead-title">Tipo de Moneda</div>
							<div class="subhead-text">{{ $obj->currency_name }}</div>
						</div>
					</td>
				</tr>
				<tr>
					<td style="vertical-align:top;">
						<div class="subhead-item">
							<div class="subhead-title">Nombre/Razón Social</div>
							<div class="subhead-text">{{ $obj->client_name }}</div>
						</div>
					</td>
					<td style="vertical-align:top;">
						<div class="subhead-item">
							<div class="subhead-title">{{ $obj->client_document_name }}</div>
							<div class="subhead-text">{{ $obj->client_document_number }}</div>
						</div>
					</td>
					<td colspan="2" style="vertical-align:top;">
						<div class="subhead-item">
							<div class="subhead-title">Dirección</div>
							<div class="subhead-text">{{ $obj->client_address }}</div>
						</div>
					</td>
				</tr>
			</table>
			<hr>
			Servicios
			<hr>
			<table class="pdf-items" cellspacing="0" cellpadding="0" border="0">
				<thead>
					<tr>
						<td>Descripción</td>
						<td>Cantidad</td>
						<td>V. Unitario</td>
						<td>V. Venta</td>
						<td>% Desc.</td>
						<td>Monto Desc.</td>
						<td>Total</td>
					</tr>
				</thead>
				<tbody>
					@foreach ($services as $item)
						<tr>
							<td>{{ $item->article_name }}</td>
							<td>{{ number_format($item->article_quantity, 0, '.', '') }}</td>
							<td>{{ number_format($item->article_price, 2, '.', '') }}</td>
							<td>{{ number_format($item->subtotal, 2, '.', '') }}</td>
							<td>{{ number_format($item->desc_percentage, 2, '.', '') }}</td>
							<td>{{ number_format($item->desc, 2, '.', '') }}</td>
							<td>{{ number_format($item->total, 2, '.', '') }}</td>
						</tr>
					@endforeach
				</tbody>
			</table>
			<hr>
			Repuestos
			<hr>
			<table class="pdf-items" cellspacing="0" cellpadding="0" border="0">
				<thead>
					<tr>
						<td>Descripción</td>
						<td>Cantidad</td>
						<td>V. Unitario</td>
						<td>V. Venta</td>
						<td>% Desc.</td>
						<td>Monto Desc.</td>
						<td>Total</td>
					</tr>
				</thead>
				<tbody>
					@foreach ($bienes as $item)
						<tr>
							<td>{{ $item->article_name }}</td>
							<td>{{ number_format($item->article_quantity, 0, '.', '') }}</td>
							<td>{{ number_format($item->article_price, 2, '.', '') }}</td>
							<td>{{ number_format($item->subtotal, 2, '.', '') }}</td>
							<td>{{ number_format($item->desc_percentage, 2, '.', '') }}</td>
							<td>{{ number_format($item->desc, 2, '.', '') }}</td>
							<td>{{ number_format($item->total, 2, '.', '') }}</td>
						</tr>
					@endforeach
				</tbody>
			</table>

			<table class="pdf-items-footer" cellspacing="0" cellpadding="0" border="0">
				<tbody>
					<tr>
						<td>
							<table class="pdf-total-text" cellspacing="0" cellpadding="0" border="0">
								<tr>
									<td>
										<div class="total-text-item">
											<div class="total-text-title">SON</div>
											<div class="total-text-text">{{ $total_text }}</div>
										</div>
									</td>
								</tr>
								<tr>
									<td>
										<div class="total-text-item">
											<img src="data:image/png;base64, {{ $document_qrcode }}">
										</div>
									</td>
								</tr>
							</table>
						</td>
						<td align="right">
							<table class="pdf-totals" cellspacing="0" cellpadding="0" border="0" align="right">
								<tbody>
									<tr>
										<td>Op. Gravada</td>
										<td>{{ $obj->currency_symbol }}</td>
										<td>{{ number_format($obj->taxed_operation, 2, '.', '') }}</td>
									</tr>
									<tr>
										<td>IGV (18.00)</td>
										<td>{{ $obj->currency_symbol }}</td>
										<td>{{ number_format($obj->igv, 2, '.', '') }}</td>
									</tr>
									<tr>
										<td>Op. Inafecta</td>
										<td>{{ $obj->currency_symbol }}</td>
										<td>0.00</td>
									</tr>
									<tr>
										<td>Op. Exonerada</td>
										<td>{{ $obj->currency_symbol }}</td>
										<td>0.00</td>
									</tr>
									<tr>
										<td>Importe Total</td>
										<td>{{ $obj->currency_symbol }}</td>
										<td>{{ number_format($obj->total, 2, '.', '') }}</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</body>
	<!-- end::Body -->
</html>
