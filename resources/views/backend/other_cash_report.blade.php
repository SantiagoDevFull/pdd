@extends('backend.templates.app')

@section('body-class', 'kt-header--fixed kt-header-mobile--fixed')
@section('title', 'Reporte')
@section('subtitle', 'Ingresos de Caja')

@section('content')
	<other-cash-report-form
		:companies = "{{ $companies }}"
		:current_date = "'{{ $current_date }}'"
		:url = "'{{ route('dashboard.report.register_other_cash.validate_form') }}'"
		:url_export = "'{{ route('dashboard.report.other_cash.list') }}'"
        :url_get_accounts = "'{{ route('dashboard.report.register_other_cash.get_accounts') }}'"
	></other-cash-report-form>

	<loading></loading>

@endsection