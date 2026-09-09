@extends('backend.templates.app')

@section('body-class', 'kt-header--fixed kt-header-mobile--fixed')
@section('title', 'CASA DEL GAS')
@section('subtitle', 'Reporte de Pedidos')

@section('content')
	<order-house-report-form
		:current_date = "'{{ $current_date }}'"
		:first_date   = "'{{ $first_date }}'"
		:client_routes   = "{{ $client_routes }}"
		:url = "'{{ route('dashboard.report.order_house.validate_form') }}'"
	></order-house-report-form>

	<order-house-report-table
		:url = "'{{ route('dashboard.report.order_house.list') }}'"
	></order-house-report-table>

	<loading></loading>
@endsection
