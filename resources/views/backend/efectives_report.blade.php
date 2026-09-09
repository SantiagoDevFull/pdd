@extends('backend.templates.app')

@section('title', 'Facturación')
@section('subtitle', 'Reporte de Ingreso de Efectivo')

@section('content')
	<efectives-report-form
		:current_date = "'{{ $current_date }}'"
		:url = "'{{ route('dashboard.voucher.efectives.validate_form') }}'"
		:url_get_clients = "'{{ route('dashboard.voucher.efectives.get_clients') }}'"
	></efectives-report-form>

	<efectives-report-table
		:url = "'{{ route('dashboard.voucher.efectives.list') }}'"
	></efectives-report-table>

	<loading></loading>
@endsection
