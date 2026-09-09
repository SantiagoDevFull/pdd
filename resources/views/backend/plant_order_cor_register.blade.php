@extends('backend.templates.app')

@section('body-class', '')
@section('title', 'Operaciones')
@section('subtitle', 'Coordinador')

@section('content')
	<plant-order-cor-register-form
		:max_datetime = "'{{ $max_datetime }}'"
		:current_date = "'{{ $current_date }}'"
		:vehicles = "{{ $vehicles }}"
		:client_routes = "{{ $client_routes }}"
		:url_get_employees = "'{{ route('dashboard.operations.plant_order_cor_register.get_employees') }}'"
		:url = "'{{ route('dashboard.operations.plant_order_cor_register.store') }}'"
	></plant-order-cor-register-form>

	<plant-order-cor-register-table
		:url_list = "'{{ route('dashboard.operations.plant_order_cor_register.list') }}'"
		:url_detail = "'{{ route('dashboard.operations.plant_order_cor_register.detail') }}'"
	></plant-order-cor-register-table>

	<plant-order-cor-register-modal
		:vehicles = "{{ $vehicles }}"
		:url_get_employees = "'{{ route('dashboard.operations.plant_order_cor_register.get_employees') }}'"
		:url_update = "'{{ route('dashboard.operations.plant_order_cor_register.update') }}'"
		:url_get_select2 = "'{{ route('dashboard.operations.plant_order_cor_register.get_select2') }}'"
	></plant-order-cor-register-modal>

	<loading></loading>
@endsection