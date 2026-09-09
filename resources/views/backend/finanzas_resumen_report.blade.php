@extends('backend.templates.app')

@section('title', 'Reportes')
@section('subtitle', 'Flujo de Caja')

@section('content')
	<finanzas-resumen-report-form
		:companies = "{{ $companies }}"
		:current_date = "'{{ $current_date }}'"
		:url = "'{{ route('dashboard.report.finanzas_resumen.validate_form') }}'"
		:url_get_clients = "'{{ route('dashboard.report.finanzas_resumen.get_clients') }}'"
	></finanzas-resumen-report-form>
	
	<finanzas-resumen-report-table
		:url = "'{{ route('dashboard.report.finanzas_resumen.list') }}'"
	></finanzas-resumen-report-table>

	<loading></loading>
@endsection