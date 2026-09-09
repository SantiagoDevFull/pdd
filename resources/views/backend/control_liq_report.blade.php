@extends('backend.templates.app')

@section('title', 'Reportes')
@section('subtitle', 'Control de Liquidación')

@section('content')
	<control-liq-report-form
		:companies = "{{ $companies }}"
		:current_date = "'{{ $current_date }}'"
		:url = "'{{ route('dashboard.report.control_liq.validate_form') }}'"
		:url_get_clients = "'{{ route('dashboard.report.control_liq.get_clients') }}'"
	></control-liq-report-form>
	
	<control-liq-report-table
		:url = "'{{ route('dashboard.report.control_liq.list') }}'"
	></control-liq-report-table>

	<loading></loading>
@endsection