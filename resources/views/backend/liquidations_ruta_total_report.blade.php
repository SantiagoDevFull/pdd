@extends('backend.templates.app')

@section('title', 'Reportes')
@section('subtitle', 'Venta por Rutas Resumido')

@section('content')
	<liquidation-ruta-total-report-form
		:client_routes = "{{ $client_routes }}"
		:current_date = "'{{ $current_date }}'"
		:url = "'{{ route('dashboard.report.liquidations_ruta_total.validate_form') }}'"
		:url_get_clients = "'{{ route('dashboard.report.liquidations_ruta_total.get_clients') }}'"
	></liquidation-ruta-total-report-form>
	
	<liquidation-ruta-total-report-table
		:url = "'{{ route('dashboard.report.liquidations_ruta_total.list') }}'"
	></liquidation-ruta-total-report-table>

	<loading></loading>
@endsection