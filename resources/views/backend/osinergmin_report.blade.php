@extends('backend.templates.app')

@section('body-class', 'kt-header--fixed kt-header-mobile--fixed')
@section('title', 'Reporte DJ Mensual')
@section('subtitle', 'Osinergmin')

@section('content')
	<osinergmin-report-form
		:current_date = "'{{ $current_date }}'"
        :current_date_1 = "'{{ $current_date_1 }}'"
   	    :current_date_2 = "'{{ $current_date_2 }}'"
		:url = "'{{ route('dashboard.report.osinergmin.validate_form') }}'"
		:url_export = "'{{ route('dashboard.report.osinergmin.list') }}'"
	></osinergmin-report-form>

	<loading></loading>

@endsection