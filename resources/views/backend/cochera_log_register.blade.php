@extends('backend.templates.app')

@section('body-class', '')
@section('title', 'Transportes')
@section('subtitle', 'Registro Facturas Transportes')

@section('content')
	<cochera-log-register-form
		:companies = "{{ $companies }}"
		
		:currencies = "{{ $currencies }}"
		:current_date = "'{{ $current_date }}'"
		{{-- :min_datetime = "'{{ $min_datetime }}'" --}}
		:max_datetime = "'{{ $max_datetime }}'"
		:warehouse_account_types = "{{ $warehouse_account_types }}"
		:warehouse_document_types = "{{ $warehouse_document_types }}"	
		:url = "'{{ route('dashboard.transport.cochera_log_register.list') }}'"
		:url_get_accounts = "'{{ route('dashboard.transport.cochera_log_register.get_accounts') }}'"
		:url_get_articles = "'{{ route('dashboard.transport.cochera_log_register.get_articles') }}'"
		:url_get_perception_percentage = "'{{ route('dashboard.transport.cochera_log_register.get_perception_percentage') }}'"
		:url_get_article = "'{{ route('dashboard.transport.cochera_log_register.get_article') }}'"
		:url_get_areas = "'{{ route('dashboard.transport.cochera_log_register.get_areas') }}'"
		:url_get_costos = "'{{ route('dashboard.transport.cochera_log_register.get_costos') }}'"
		:url_get_costo_secs = "'{{ route('dashboard.transport.cochera_log_register.get_costo_secs') }}'"
		:url_store = "'{{ route('dashboard.transport.cochera_log_register.store') }}'"
	></cochera-log-register-form>

	<cochera-log-register-table
		:igv = "{{ $igv }}"
		:url = "'{{ route('dashboard.transport.cochera_log_register.store') }}'"
		:url_get_article = "'{{ route('dashboard.transport.cochera_log_register.get_article') }}'"
	></cochera-log-register-table>

	<cochera-log-register-modal
		:url_get_article = "'{{ route('dashboard.transport.cochera_log_register.get_article') }}'"
		:cochera_vehicles = "{{ $cochera_vehicles }}"
	></cochera-log-register-modal>

	<loading></loading>
@endsection