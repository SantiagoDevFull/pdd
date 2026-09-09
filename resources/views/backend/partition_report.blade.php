@extends('backend.templates.app')

@section('body-class', 'kt-header--fixed kt-header-mobile--fixed')
@section('title', 'Abastecimientos')
@section('subtitle', 'Reporte de Particiones')

@section('content')
	<partition-report-form
		:url = "'{{ route('dashboard.report.partition.validate_form') }}'"
		:url_export = "'{{ route('dashboard.report.partition.list') }}'"
		:current_date = "'{{ $current_date }}'"
	></partition-report-form>

	<loading></loading>

@endsection
