@extends('backend.templates.app')

@section('title', 'Reportes')
@section('subtitle', 'Reporte de Clientes Perdidos Granel')

@section('content')
	<granel-comercial-report-form
		:current_date = "'{{ $current_date }}'"
		:client_routes = "{{ $client_routes }}"
		:url = "'{{ route('dashboard.report.granel_comercial.validate_form') }}'"
		:url_get_clients = "'{{ route('dashboard.report.granel_comercial.get_clients') }}'"
		:url_export = "'{{ route('dashboard.report.granel_comercial.list') }}'"
	></granel-comercial-report-form>
	<loading></loading>
@endsection