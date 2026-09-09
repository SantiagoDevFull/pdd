@extends('backend.templates.app')

@section('title', 'Comercial')
@section('subtitle', 'Rutas')

@section('content')
	<proyection-report-form
		:current_date = "'{{ $current_date }}'"
		:client_routes = "{{ $client_routes }}"
		:url = "'{{ route('dashboard.report.proyection.validate_form') }}'"
		:url_export = "'{{ route('dashboard.report.proyection.list') }}'"
	></proyection-report-form>
	<loading></loading>
@endsection
