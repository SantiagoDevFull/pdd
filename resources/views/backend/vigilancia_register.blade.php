@extends('backend.templates.app')

@section('body-class', '')
@section('title', 'VIGILANCIA')
@section('subtitle', 'Vigilancia')

@section('content')
	<vigilancia-register-form
		:max_datetime = "'{{ $max_datetime }}'"
		:current_date = "'{{ $current_date }}'"
		:client_routes = "{{ $client_routes }}"
		:movement_classes = "{{ $movement_classes }}"
		:vehicles = "{{ $vehicles }}"
		:url_get_plant_orders = "'{{ route('dashboard.operations.vigilancia_register.get_plan_order') }}'"
		:url_get_movement_class = "'{{ route('dashboard.operations.vigilancia_register.get_movement_class') }}'"
		:url = "'{{ route('dashboard.operations.vigilancia_register.store') }}'"
		:url_get_employees = "'{{ route('dashboard.operations.vigilancia_register.get_employees') }}'"
		:url_get_clients = "'{{ route('dashboard.operations.vigilancia_register.get_clients') }}'"
	></vigilancia-register-form>

	<loading></loading>
@endsection