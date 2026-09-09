@extends('backend.templates.app')

@section('body-class', '')
@section('title', 'Control GLP')
@section('subtitle', 'Registro Abastecimientos PLANTA')

@section('content')
	<stock-plant-register-form
		:companies = "{{ $companies }}"
		:currencies = "{{ $currencies }}"
		:current_date = "'{{ $current_date }}'"
		{{-- :min_datetime = "'{{ $min_datetime }}'" --}}
		:max_datetime = "'{{ $max_datetime }}'"
		:warehouse_account_types = "{{ $warehouse_account_types }}"
		:warehouse_document_types = "{{ $warehouse_document_types }}"	
		:url = "'{{ route('dashboard.operations.stock_plant_register.list') }}'"
		:url_get_accounts = "'{{ route('dashboard.operations.stock_plant_register.get_accounts') }}'"
		:url_get_articles = "'{{ route('dashboard.operations.stock_plant_register.get_articles') }}'"
		:url_get_perception_percentage = "'{{ route('dashboard.operations.stock_plant_register.get_perception_percentage') }}'"
		:url_get_article = "'{{ route('dashboard.operations.stock_plant_register.get_article') }}'"
		:url_store = "'{{ route('dashboard.operations.stock_plant_register.store') }}'"
	></stock-plant-register-form>

	<stock-plant-register-table
		:igv = "{{ $igv }}"
		:url = "'{{ route('dashboard.operations.stock_plant_register.store') }}'"
		:url_get_article = "'{{ route('dashboard.operations.stock_plant_register.get_article') }}'"
	></stock-plant-register-table>

	<stock-plant-register-modal
		:url_get_article = "'{{ route('dashboard.operations.stock_plant_register.get_article') }}'"
	></stock-plant-register-modal>

	<loading></loading>
@endsection