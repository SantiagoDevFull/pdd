@extends('backend.templates.app')

@section('title', 'Reportes')
@section('subtitle', 'Cierre de Ventas')

@section('content')
	<close-sale-report-form
		:companies = "{{ $companies }}"
		:current_date = "'{{ $current_date }}'"
		:url = "'{{ route('dashboard.report.close_sale.validate_form') }}'"
		:url_get_clients = "'{{ route('dashboard.report.close_sale.get_clients') }}'"
	></close-sale-report-form>
	
	<close-sale-report-table
		:url = "'{{ route('dashboard.report.close_sale.list') }}'"
	></close-sale-report-table>

	<loading></loading>
@endsection