@extends('backend.templates.app')

@section('body-class', 'kt-header--fixed kt-header-mobile--fixed')
@section('title', 'Blue Line - Clientes')
@section('subtitle', 'Reporte de Clientes')

@section('content')
	<blue-clients-report-form
		:business_units = "{{ $business_units }}"
		:companies = "{{ $companies }}"
		:url = "'{{ route('dashboard.report.blue_list_clients.validate_form') }}'"
	></blue-clients-report-form>

	<blue-clients-report-table
		:url = "'{{ route('dashboard.report.blue_list_clients.list') }}'"
	></blue-clients-report-table>

	<loading></loading>
@endsection