@extends('backend.templates.app')

@section('title', 'Reportes')
@section('subtitle', 'Lista de Precios')

@section('content')
	<list-price-form
		:client_sectors = "{{ $client_sectors }}"
		:url = "'{{ route('dashboard.list_price_report.export') }}'"
		:url_validation = "'{{ route('dashboard.list_price_report.validate_form') }}'"
	></list-price-form>

	<loading></loading>
@endsection
