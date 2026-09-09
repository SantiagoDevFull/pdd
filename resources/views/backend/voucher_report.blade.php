@extends('backend.templates.app')

@section('title', 'Facturación')
@section('subtitle', 'Reporte de Depositos')

@section('content')
	<voucher-report-form
		:current_date = "'{{ $current_date }}'"
		:url = "'{{ route('dashboard.voucher.deposits.validate_form') }}'"
		:url_get_clients = "'{{ route('dashboard.voucher.deposits.get_clients') }}'"
	></voucher-report-form>
	
	<voucher-report-table
		:url = "'{{ route('dashboard.voucher.deposits.list') }}'"
	></voucher-report-table>

	<loading></loading>
@endsection