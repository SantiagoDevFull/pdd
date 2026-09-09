@extends('backend.templates.app')

@section('body-class', 'kt-header--fixed kt-header-mobile--fixed')
@section('title', 'Reporte')
@section('subtitle', 'Volumen de Ventas')

@section('content')
	<sales-volume-report-form
		:companies = "{{ $companies }}"
		:current_date = "'{{ $current_date }}'"
		:url = "'{{ route('dashboard.report.sales_volume.validate_form') }}'"
		:url_export = "'{{ route('dashboard.report.sales_volume.export') }}'"
	></sales-volume-report-form>

	<loading></loading>

@endsection
