@extends('backend.templates.app')

@section('title', 'Reportes')
@section('subtitle', 'Reporte de Cartera Granel')

@section('content')
	<granel-six-comercial-report-form
		:current_date = "'{{ $current_date }}'"
		:client_routes = "{{ $client_routes }}"
		:managers = "{{ $managers }}"
		:url = "'{{ route('dashboard.report.granel_six_comercial.validate_form') }}'"
		:url_get_clients = "'{{ route('dashboard.report.granel_six_comercial.get_clients') }}'"
		:url_export = "'{{ route('dashboard.report.granel_six_comercial.list') }}'"
	></granel-six-comercial-report-form>

	<loading></loading>
@endsection