@extends('backend.templates.app')

@section('title', 'Reportes')
@section('subtitle', 'Boleteo')

@section('content')
	<contabilidad-boletas-report-form
		:companies = "{{ $companies }}"
		:current_date = "'{{ $current_date }}'"
		:url = "'{{ route('dashboard.report.contabilidad_boletas.validate_form') }}'"
		:url_get_clients = "'{{ route('dashboard.report.contabilidad_boletas.get_clients') }}'"
	></contabilidad-boletas-report-form>
	
	<contabilidad-boletas-report-table
		:url = "'{{ route('dashboard.report.contabilidad_boletas.list') }}'"
	></contabilidad-boletas-report-table>

	<loading></loading>
@endsection