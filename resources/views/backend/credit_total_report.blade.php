@extends('backend.templates.app')

@section('title', 'Reportes')
@section('subtitle', 'Reporte Consolidado de Creditos')

@section('content')
	<credit-total-report-form
		:companies = "{{ $companies }}"
		:current_date = "'{{ $current_date }}'"
		:url = "'{{ route('dashboard.report.credit_total.validate_form') }}'"
		:url_list = "'{{ route('dashboard.report.credit_total.list') }}'"
		:url_get_clients = "'{{ route('dashboard.report.credit_total.get_clients') }}'"
	></credit-total-report-form>
	
	<credit-total-report-table
		:url = "'{{ route('dashboard.report.credit_total.list') }}'"
	></credit-total-report-table>

	<loading></loading>
@endsection