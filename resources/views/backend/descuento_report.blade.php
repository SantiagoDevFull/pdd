@extends('backend.templates.app')

@section('title', 'Creditos')
@section('subtitle', 'Reporte de Descuentos a Personal')

@section('content')
	<descuento-report-form
		:current_date = "'{{ $current_date }}'"
		:url = "'{{ route('dashboard.voucher.descuento.validate_form') }}'"
		:url_get_clients = "'{{ route('dashboard.voucher.descuento.get_clients') }}'"
	></descuento-report-form>

	<descuento-report-table
		:url = "'{{ route('dashboard.voucher.descuento.list') }}'"
	></descuento-report-table>

	<loading></loading>
@endsection
