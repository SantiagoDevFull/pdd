@extends('backend.templates.app')

@section('body-class', '')
@section('title', 'Operaciones')
@section('subtitle', 'Recepción')

@section('content')
	<reception-register-form
		:client_routes = "{{ $client_routes }}"
		:url = "'{{ route('dashboard.operations.reception_register.store') }}'"
		:url_get_plant_orders = "'{{ route('dashboard.operations.reception_register.get_plan_order') }}'"
		:url_get_plant = "'{{ route('dashboard.operations.reception_register.get_plant') }}'"
	></reception-register-form>

	<loading></loading>
@endsection