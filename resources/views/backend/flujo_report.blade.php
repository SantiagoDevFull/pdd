@extends('backend.templates.app')

@section('title', 'Reportes')
@section('subtitle', 'Flujo de Finanzas')

@section('content')
	<flujo-report-form
		:companies = "{{ $companies }}"
		:current_date = "'{{ $current_date }}'"
		:url = "'{{ route('dashboard.report.flujo.validate_form') }}'"
		:url_get_clients = "'{{ route('dashboard.report.flujo.get_clients') }}'"
	></flujo-report-form>
	
	<flujo-report-table
		:url = "'{{ route('dashboard.report.flujo.list') }}'"
	></flujo-report-table>

	<loading></loading>
@endsection