@extends('backend.templates.app')

@section('body-class', 'kt-header--fixed kt-header-mobile--fixed')
@section('title', 'Reportes')
@section('subtitle', 'Relación de Clientes')

@section('content')
	<list-clients-report-form
		:business_units = "{{ $business_units }}"
		:companies = "{{ $companies }}"
		:url = "'{{ route('dashboard.report.list_clients.validate_form') }}'"
		:url_get_clients = "'{{ route('dashboard.report.price_list.get_clients') }}'"
	></list-clients-report-form>

	<list-clients-report-table
		:url = "'{{ route('dashboard.report.list_clients.list') }}'"
	></list-clients-report-table>

	<loading></loading>
@endsection