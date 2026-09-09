@extends('backend.templates.app')

@section('title', 'Reportes')
@section('subtitle', 'Resumen de Venta y Margen')

@section('content')
	<sale-day-margin-form
		:current_date = "'{{ $current_date }}'"
		:url = "'{{ route('dashboard.sale_margin_report.export') }}'"
		:url_validation = "'{{ route('dashboard.sale_margin_report.validate_form') }}'"
	></sale-day-margin-form>

	<loading></loading>
@endsection
