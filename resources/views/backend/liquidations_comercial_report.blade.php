@extends('backend.templates.app')

@section('title', 'Reportes')
@section('subtitle', 'Reporte de Clientes Perdidos')

@section('content')
	<liquidation-comercial-report-form
		:current_date = "'{{ $current_date }}'"
		:client_routes = "{{ $client_routes }}"
		:url = "'{{ route('dashboard.report.liquidations_comercial.validate_form') }}'"
		:url_get_clients = "'{{ route('dashboard.report.liquidations_comercial.get_clients') }}'"
		:url_export = "'{{ route('dashboard.report.liquidations_comercial.list') }}'"
	></liquidation-comercial-report-form>

	<loading></loading>
@endsection