@extends('backend.templates.app')

@section('body-class', '')
@section('title', 'Operaciones')
@section('subtitle', 'Registro de Pedidos')

@section('content')
	<plant-order-register-form
		:url = "'{{ route('dashboard.operations.plant_order_register.store') }}'"
		:url_get_plant_orders = "'{{ route('dashboard.operations.plant_order_register.get_plan_order') }}'"
		:url_get_plant_order = "'{{ route('dashboard.operations.plant_order_register.get_plan') }}'"
		:client_routes = "{{ $client_routes }}"
	></plant-order-register-form>

	<plant-order-modal></plant-order-modal>

	<loading></loading>
@endsection