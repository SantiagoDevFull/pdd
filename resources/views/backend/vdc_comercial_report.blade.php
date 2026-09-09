@extends('backend.templates.app')

@section('title', 'Reportes')
@section('subtitle', 'Reporte de Clientes Perdidos')

@section('content')
	<vdc-comercial-report-form
		:current_date = "'{{ $current_date }}'"
		:client_routes = "{{ $client_routes }}"
		:url = "'{{ route('dashboard.report.vdc_comercial.validate_form') }}'"
		:url_get_clients = "'{{ route('dashboard.report.vdc_comercial.get_clients') }}'"
		:url_export = "'{{ route('dashboard.report.vdc_comercial.list') }}'"
	></vdc-comercial-report-form>
	<loading></loading>
@endsection