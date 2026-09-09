@extends('backend.templates.app')

@section('title', 'Reportes')
@section('subtitle', 'Reporte Cartera Clientes Locales')

@section('content')
	<portfolio-local-form
		:current_date = "'{{ $current_date }}'"
		:url = "'{{ route('dashboard.portfolio_local.export') }}'"
		:url_validation = "'{{ route('dashboard.portfolio_local.validate_form') }}'"
	></portfolio-local-form>

	<loading></loading>
@endsection
