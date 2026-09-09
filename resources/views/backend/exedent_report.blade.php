@extends('backend.templates.app')

@section('title', 'Reportes')
@section('subtitle', 'Reporte de Excedente')

@section('content')
	<exedent-report-form
		:current_date = "'{{ $current_date }}'"
		:url = "'{{ route('dashboard.report.exedent.validate_form') }}'"
		:url_export = "'{{ route('dashboard.report.exedent.list') }}'"
	></exedent-report-form>|

	<loading></loading>
@endsection