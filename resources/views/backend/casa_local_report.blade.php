@extends('backend.templates.app')

@section('title', 'Reportes')
@section('subtitle', 'Reporte de Cartera Casa Gas')

@section('content')
	<casa-local-form
		:current_date = "'{{ $current_date }}'"
		:client_routes = "{{ $client_routes }}"
		:url = "'{{ route('dashboard.report.casa_local.validate_form') }}'"
		:url_get_clients = "'{{ route('dashboard.report.casa_local.get_clients') }}'"
		:url_export = "'{{ route('dashboard.report.casa_local.list') }}'"
	></casa-local-form>

	<loading></loading>
@endsection
