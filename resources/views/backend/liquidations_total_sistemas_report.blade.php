@extends('backend.templates.app')

@section('title', 'Reportes')
@section('subtitle', 'Resumen de Liquidación por Canales')

@section('content')
	<liquidation-total-sistemas-report-form
		:companies = "{{ $companies }}"
		:business_units = "{{ $business_units }}"
		:current_date = "'{{ $current_date }}'"
		:url = "'{{ route('dashboard.report.liquidations_total_sistemas.validate_form') }}'"
		:url_get_clients = "'{{ route('dashboard.report.liquidations_total_sistemas.get_clients') }}'"
		:url_export = "'{{ route('dashboard.report.liquidations_total_sistemas.list') }}'"
	></liquidation-total-sistemas-report-form>

	<loading></loading>
@endsection