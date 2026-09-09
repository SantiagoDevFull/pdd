@extends('backend.templates.app')

@section('body-class', '')
@section('title', 'ADMINISTRACION')
@section('subtitle', 'Registro Liquidación Caja')

@section('content')
	<caja-register-form
		:companies = "{{ $companies }}"
		
		:currencies = "{{ $currencies }}"
		:current_date = "'{{ $current_date }}'"
		{{-- :min_datetime = "'{{ $min_datetime }}'" --}}
		:max_datetime = "'{{ $max_datetime }}'"
		:warehouse_account_types = "{{ $warehouse_account_types }}"
		:warehouse_document_types = "{{ $warehouse_document_types }}"	
		:url = "'{{ route('dashboard.transport.caja_register.list') }}'"
		:url_get_accounts = "'{{ route('dashboard.transport.caja_register.get_accounts') }}'"
		:url_get_articles = "'{{ route('dashboard.transport.caja_register.get_articles') }}'"
		:url_get_perception_percentage = "'{{ route('dashboard.transport.caja_register.get_perception_percentage') }}'"
		:url_get_article = "'{{ route('dashboard.transport.caja_register.get_article') }}'"
		:url_get_areas = "'{{ route('dashboard.transport.caja_register.get_areas') }}'"
		:url_get_costos = "'{{ route('dashboard.transport.caja_register.get_costos') }}'"
		:url_get_costo_secs = "'{{ route('dashboard.transport.caja_register.get_costo_secs') }}'"
		:url_store = "'{{ route('dashboard.transport.caja_register.store') }}'"
	></caja-register-form>

	<caja-register-table
		:igv = "{{ $igv }}"
		:url = "'{{ route('dashboard.transport.caja_register.store') }}'"
		:url_get_article = "'{{ route('dashboard.transport.caja_register.get_article') }}'"
	></caja-register-table>

	<caja-register-modal
		:url_get_article = "'{{ route('dashboard.transport.caja_register.get_article') }}'"
		:cochera_vehicles = "{{ $cochera_vehicles }}"
	></caja-register-modal>

	<loading></loading>
@endsection