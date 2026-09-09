@extends('backend.templates.app')

@section('title', 'Reportes')
@section('subtitle', 'Venta por Canales')

@section('content')
	<liquidation-sistemas-report-form
		:companies = "{{ $companies }}"
		:business_units = "{{ $business_units }}"
		:current_date = "'{{ $current_date }}'"
		:url = "'{{ route('dashboard.report.liquidations_sistemas.validate_form') }}'"
		:url_get_clients = "'{{ route('dashboard.report.liquidations_sistemas.get_clients') }}'"
		:url_export = "'{{ route('dashboard.report.liquidations_sistemas.list') }}'"
	></liquidation-sistemas-report-form>

	<loading></loading>
@endsection