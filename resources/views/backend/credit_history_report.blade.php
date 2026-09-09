@extends('backend.templates.app')

@section('body-class', 'kt-header--fixed kt-header-mobile--fixed')
@section('title', 'Reportes')
@section('subtitle', 'Credito histórico')

@section('content')
	<credit-history-report-form
		:companies = "{{ $companies }}"
		:current_date = "'{{ $current_date }}'"
		:url = "'{{ route('dashboard.report.credit_history.validate_form') }}'"
	></credit-history-report-form>

	<credit-history-report-table
		:url = "'{{ route('dashboard.report.credit_history.list') }}'"
	></credit-history-report-table>

	<loading></loading>
@endsection