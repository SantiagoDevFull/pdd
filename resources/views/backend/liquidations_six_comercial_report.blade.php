@extends('backend.templates.app')

@section('title', 'Reportes')
@section('subtitle', 'Reporte de Cartera Envasado')

@section('content')
	<liquidation-six-comercial-report-form
		:current_date = "'{{ $current_date }}'"
		:client_routes = "{{ $client_routes }}"
		:url = "'{{ route('dashboard.report.liquidations_six_comercial.validate_form') }}'"
		:url_get_clients = "'{{ route('dashboard.report.liquidations_six_comercial.get_clients') }}'"
		:url_export = "'{{ route('dashboard.report.liquidations_six_comercial.list') }}'"
	></liquidation-six-comercial-report-form>

	<loading></loading>
@endsection
