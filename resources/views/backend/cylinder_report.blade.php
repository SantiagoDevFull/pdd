@extends('backend.templates.app')

@section('body-class', 'kt-header--fixed kt-header-mobile--fixed')
@section('title', 'Activos')
@section('subtitle', 'Reporte de Despacho y Retorno de Cilindros')

@section('content')
	<cylinder-report-form
		:url = "'{{ route('dashboard.report.cylinder.validate_form') }}'"
		:url_export = "'{{ route('dashboard.report.cylinder.list') }}'"
		:current_date = "'{{ $current_date }}'"
	></cylinder-report-form>

	<loading></loading>

@endsection
