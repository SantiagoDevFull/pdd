@extends('backend.templates.app')

@section('title', 'Reportes')
@section('subtitle', 'Resumen de Venta diaria')

@section('content')
	<sale-day-form
		:current_date = "'{{ $current_date }}'"
		:url = "'{{ route('dashboard.sale_day_report.export') }}'"
		:url_validation = "'{{ route('dashboard.sale_day_report.validate_form') }}'"
	></sale-day-form>

	<loading></loading>
@endsection
