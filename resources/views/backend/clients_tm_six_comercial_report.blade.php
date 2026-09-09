@extends('backend.templates.app')

@section('title', 'Reportes')
@section('subtitle', 'Reporte Semestral de Ventas Clientes Nuevos')

@section('content')
	<clients-tm-six-comercial-report-form
		:current_date = "'{{ $current_date }}'"
		:client_routes = "{{ $client_routes }}"
		:url = "'{{ route('dashboard.report.clients_tm_six_comercial.validate_form') }}'"
		:url_get_clients = "'{{ route('dashboard.report.clients_tm_six_comercial.get_clients') }}'"
		:url_export = "'{{ route('dashboard.report.clients_tm_six_comercial.list') }}'"
	></clients-tm-six-comercial-report-form>

	<loading></loading>
@endsection