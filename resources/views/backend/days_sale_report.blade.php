@extends('backend.templates.app')

@section('title', 'Reportes')
@section('subtitle', 'Resumen de Venta diaria')

@section('content')
	<days-sale-report-form
		:current_date = "'{{ $current_date }}'"
		:url_export = "'{{ route('dashboard.report.days_sale.list') }}'"
		:url = "'{{ route('dashboard.report.days_sale.validate_form') }}'"
	></days-sale-report-form>

	<loading></loading>
@endsection
