@extends('backend.templates.app')

@section('title', 'Reportes')
@section('subtitle', 'Margen por Ruta')

@section('content')
	<route-report-form
		:companies = "{{ $companies }}"
		:client_routes = "{{ $client_routes }}"
		:current_date = "'{{ $current_date }}'"
		:url = "'{{ route('dashboard.report.route.validate_form') }}'"
		:url_get_clients = "'{{ route('dashboard.report.route.get_clients') }}'"
		:url_export = "'{{ route('dashboard.report.route.list') }}'"
	></route-report-form>

	<loading></loading>
@endsection